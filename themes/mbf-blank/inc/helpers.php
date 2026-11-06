<?php
/**
 * Shared helpers. Phase 1: the menu tree and its one renderer, used by both the inline
 * nav and the drawer so the two can never disagree about what the menu contains.
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * A menu location as a two-level tree: [ label, url, current, children[] ].
 *
 * Two levels only. A third level would be a dropdown inside a dropdown, which the
 * nav doesn't support, so grandchildren are dropped rather than rendered badly.
 *
 * @return array<int, array{label: string, url: string, current: bool, children: array}>
 */
function mbf_menu_tree( string $location ): array {
	$locations = get_nav_menu_locations();

	if ( empty( $locations[ $location ] ) ) {
		return [];
	}

	$items    = wp_get_nav_menu_items( $locations[ $location ] ) ?: [];
	$tree     = [];
	$children = [];

	foreach ( $items as $item ) {
		$node = [
			'label'    => (string) $item->title,
			'url'      => (string) $item->url,
			'current'  => mbf_menu_item_is_current( $item ),
			'children' => [],
		];

		$parent = (int) $item->menu_item_parent;

		if ( $parent ) {
			$children[ $parent ][] = $node;
		} else {
			$tree[ (int) $item->ID ] = $node;
		}
	}

	foreach ( $children as $parent => $nodes ) {
		if ( isset( $tree[ $parent ] ) ) {
			$tree[ $parent ]['children'] = $nodes;
		}
	}

	return array_values( $tree );
}

/**
 * Whether a menu item points at the page being viewed (for aria-current).
 */
function mbf_menu_item_is_current( WP_Post $item ): bool {
	$object_id = (int) $item->object_id;

	return match ( $item->type ) {
		// is_home(): the posts page (page_for_posts) is a page item but not is_singular().
		'post_type'         => ( is_singular() || is_home() ) && $object_id === get_queried_object_id(),
		'taxonomy'          => ( is_category() || is_tag() || is_tax() ) && $object_id === get_queried_object_id(),
		'post_type_archive' => is_post_type_archive( $item->object ),
		default             => untrailingslashit( (string) $item->url ) === untrailingslashit( home_url( $GLOBALS['wp']->request ?? '' ) ),
	};
}

/**
 * Prints a menu tree as BEM markup under $block (`mbf-nav` or `mbf-drawer`).
 *
 * A parent with children becomes <details>/<summary>: keyboard-operable and announced
 * as expanded/collapsed natively, with no ARIA to maintain. The summary isn't a link,
 * so a parent with its own URL is repeated as the first sub-item.
 *
 * @param string $group_name Shared `name` on the <details>, so only one opens at a time.
 *                           Empty in the drawer, where several open at once is useful.
 */
function mbf_the_menu( array $tree, string $block, string $group_name = '' ): void {
	if ( ! $tree ) {
		return;
	}
	?>
	<ul class="<?php echo esc_attr( "{$block}__list" ); ?>" role="list">
		<?php foreach ( $tree as $item ) : ?>
			<li class="<?php echo esc_attr( "{$block}__item" ); ?>">
				<?php if ( $item['children'] ) : ?>
					<details class="<?php echo esc_attr( "{$block}__group" ); ?>"<?php echo $group_name ? ' name="' . esc_attr( $group_name ) . '"' : ''; ?>>
						<summary class="<?php echo esc_attr( "{$block}__link {$block}__summary" ); ?>">
							<?php echo esc_html( $item['label'] ); ?>
							<span class="mbf-chevron" aria-hidden="true"></span>
						</summary>
						<ul class="<?php echo esc_attr( "{$block}__sublist" ); ?>" role="list">
							<?php if ( '' !== $item['url'] && '#' !== $item['url'] ) : ?>
								<li>
									<?php
									mbf_the_menu_link(
										[
											'url'     => $item['url'],
											'current' => $item['current'],
											/* translators: %s: menu item label, e.g. "Services". */
											'label'   => sprintf( __( '%s overview', 'mbf-blank' ), $item['label'] ),
										],
										"{$block}__sublink"
									);
									?>
								</li>
							<?php endif; ?>
							<?php foreach ( $item['children'] as $child ) : ?>
								<li><?php mbf_the_menu_link( $child, "{$block}__sublink" ); ?></li>
							<?php endforeach; ?>
						</ul>
					</details>
				<?php else : ?>
					<?php mbf_the_menu_link( $item, "{$block}__link" ); ?>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}

function mbf_the_menu_link( array $item, string $class ): void {
	printf(
		'<a class="%1$s" href="%2$s"%3$s>%4$s</a>',
		esc_attr( $class ),
		esc_url( $item['url'] ),
		$item['current'] ? ' aria-current="page"' : '',
		esc_html( $item['label'] )
	);
}

/* --- Sections ---------------------------------------------------------------------- */

/** Grounds a section can sit on (ACF `ground` choices). */
const MBF_GROUNDS = [ 'base', 'tertiary', 'secondary', 'primary' ];

/**
 * THE list of dark grounds (05): logo choice, prose, anything that flips on dark.
 * One place, so a fork that adds a ground changes it once.
 */
function mbf_is_dark_ground( string $ground ): bool {
	return in_array( $ground, [ 'secondary', 'primary' ], true );
}

/**
 * The accent a ground uses when the editor picks "Ground default". Matches the
 * --section-accent each .mbf-section--{ground} sets in src/components/section.css.
 */
function mbf_ground_accent( string $ground ): string {
	return [
		'base'      => 'primary',
		'tertiary'  => 'primary',
		'secondary' => 'accent',
		'primary'   => 'base',
	][ $ground ] ?? 'primary';
}

/**
 * The contrast guard (05): the accent a section actually uses. The editor's choice if it
 * passes on this ground, otherwise the ground's default.
 *
 * Accent as text on the ground, measured 2026-11-05 on the theme.json placeholders
 * (needs ≥4.5:1, since accents colour titles and links):
 *
 *              base   tertiary  secondary  primary
 *   primary    6.62   6.01      2.55 ✗     1.00 ✗
 *   accent     1.88✗  1.70 ✗    9.01       3.53 ✗
 *
 * $passes lists the passing pairs among the ACF `accent` choices (primary, accent). With
 * the placeholder palette no ground passes anything but its own default (primary's
 * default is `base`, not a choice), so every other choice is re-mapped. A fork that adds
 * a passing accent re-measures, edits $passes, and adds its .mbf-section--accent-* rule.
 */
function mbf_accent_colour( string $accent, string $ground ): string {
	$passes = [
		'base'      => [ 'primary' ],
		'tertiary'  => [ 'primary' ],
		'secondary' => [ 'accent' ],
		'primary'   => [],
	];

	return in_array( $accent, $passes[ $ground ] ?? [], true ) ? $accent : mbf_ground_accent( $ground );
}

/**
 * Prints a section's class list (BEM modifiers from its controls, never utility strings)
 * and aria-labelledby. $a is the row's $args; $classes are the layout's own block and
 * modifier classes (`mbf-split`, `mbf-split--image-start`).
 */
function mbf_section_attrs( array $a, string $labelledby = '', array $classes = [] ): void {
	$ground  = in_array( $a['ground'] ?? '', MBF_GROUNDS, true ) ? $a['ground'] : 'base';
	$accent  = mbf_accent_colour( (string) ( $a['accent'] ?? 'default' ), $ground );
	$spacing = (string) ( $a['spacing'] ?? 'default' );
	$classes = array_merge( [ 'mbf-section' ], $classes );

	if ( 'base' !== $ground ) {
		$classes[] = "mbf-section--{$ground}";
	}

	if ( mbf_ground_accent( $ground ) !== $accent ) {
		$classes[] = "mbf-section--accent-{$accent}";
	}

	if ( in_array( $spacing, [ 'flush-top', 'flush-btm', 'flush' ], true ) ) {
		$classes[] = "mbf-section--{$spacing}";
	}

	printf( 'class="%s"', esc_attr( implode( ' ', $classes ) ) );

	if ( '' !== $labelledby ) {
		printf( ' aria-labelledby="%s"', esc_attr( $labelledby ) );
	}
}

/**
 * The ids handed out on this page. mbf_render_layouts() resets it at the start of each
 * pass, so a second pass (a preview, a REST render) doesn't number from where the first
 * stopped.
 *
 * @return array<string, true>
 */
function &mbf_section_id_registry( bool $reset = false ): array {
	static $ids = [];

	if ( $reset ) {
		$ids = [];
	}

	return $ids;
}

/**
 * A unique, readable id for a section heading ("our-services", "our-services-2").
 */
function mbf_section_id( string $text ): string {
	$ids  = &mbf_section_id_registry();
	$base = sanitize_title( $text ) ?: 'section';
	$id   = $base;

	for ( $n = 2; isset( $ids[ $id ] ); $n++ ) {
		$id = "{$base}-{$n}";
	}

	$ids[ $id ] = true;

	return $id;
}

/**
 * Repeater and gallery values as an array. ACF returns false or '' when empty, and
 * (array) false is [false], which loops once over nothing (10).
 */
function mbf_rows( mixed $value ): array {
	return is_array( $value ) ? $value : [];
}

/* --- Links, grids, crumbs --------------------------------------------------------- */

/**
 * An ACF link field as [ url, label, target ], or [] when it can't be a link.
 */
function mbf_link( mixed $link ): array {
	if ( ! is_array( $link ) || empty( $link['url'] ) || empty( $link['title'] ) ) {
		return [];
	}

	return [
		'url'    => (string) $link['url'],
		'label'  => (string) $link['title'],
		'target' => '_blank' === ( $link['target'] ?? '' ) ? '_blank' : '',
	];
}

/**
 * Card grid classes. Exactly 2 or 4 items read best as two columns (03: count-aware
 * grid). The count comes from the server, not :has(), because it changes layout (§2).
 */
function mbf_grid_class( int $count ): string {
	return in_array( $count, [ 2, 4 ], true ) ? 'mbf-grid mbf-grid--pairs' : 'mbf-grid';
}

/**
 * Breadcrumb trail for the current request: [ [ label, url ], … ], the last item being the
 * current page (url ''). Empty on the front page.
 *
 * @return array<int, array{label: string, url: string}>
 */
function mbf_crumbs(): array {
	if ( is_front_page() ) {
		return [];
	}

	$trail      = [ [ 'label' => __( 'Home', 'mbf-blank' ), 'url' => home_url( '/' ) ] ];
	$posts_page = (int) get_option( 'page_for_posts' );

	if ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_queried_object_id() ) ) as $ancestor ) {
			$trail[] = [ 'label' => get_the_title( $ancestor ), 'url' => (string) get_permalink( $ancestor ) ];
		}
		$trail[] = [ 'label' => get_the_title( get_queried_object_id() ), 'url' => '' ];
	} elseif ( is_singular( 'post' ) ) {
		if ( $posts_page ) {
			$trail[] = [ 'label' => get_the_title( $posts_page ), 'url' => (string) get_permalink( $posts_page ) ];
		}
		$trail[] = [ 'label' => get_the_title( get_queried_object_id() ), 'url' => '' ];
	} elseif ( is_home() ) {
		$trail[] = [ 'label' => $posts_page ? get_the_title( $posts_page ) : __( 'Latest posts', 'mbf-blank' ), 'url' => '' ];
	} elseif ( is_search() ) {
		$trail[] = [ 'label' => __( 'Search results', 'mbf-blank' ), 'url' => '' ];
	} elseif ( is_404() ) {
		$trail[] = [ 'label' => __( 'Page not found', 'mbf-blank' ), 'url' => '' ];
	} elseif ( is_archive() ) {
		$trail[] = [ 'label' => wp_strip_all_tags( get_the_archive_title() ), 'url' => '' ];
	}

	return $trail;
}

/* --- Icons ------------------------------------------------------------------------ */

/**
 * Whether Font Awesome is loaded (Site Settings › Icons has a kit token). Icon spots fall
 * back to visible text when it isn't.
 */
function mbf_has_icons(): bool {
	return '' !== (string) mbf_setting( 'fontawesome_token' );
}

/**
 * Font Awesome classes for a social network (Site Settings `socials` choices), or '' when
 * there's no mark, so the caller keeps the visible name.
 *
 * BRANDS, not Sharp: brand marks only exist in `fa-brands`; `fa-sharp fa-regular
 * fa-linkedin` renders nothing, silently (Mantech). `fa-x-twitter`, not the retired bird.
 */
function mbf_social_icon( string $network ): string {
	$icons = [
		'facebook'  => 'fa-facebook-f',
		'instagram' => 'fa-instagram',
		'linkedin'  => 'fa-linkedin-in',
		'x'         => 'fa-x-twitter',
		'youtube'   => 'fa-youtube',
		'tiktok'    => 'fa-tiktok',
	];

	return isset( $icons[ $network ] ) ? 'fa-brands ' . $icons[ $network ] : '';
}
