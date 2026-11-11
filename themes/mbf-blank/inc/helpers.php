<?php
/**
 * Shared helpers. Menus first: wp_nav_menu() with one walker, used by the inline nav, the
 * drawer and the footer, so core owns what a menu contains and which item is current.
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/* --- Menus ------------------------------------------------------------------------- */

/**
 * A menu location as BEM markup under $block (`mbf-nav`, `mbf-drawer`, `mbf-footer`), or ''
 * when the location is unassigned or its menu is empty.
 *
 * wp_nav_menu() does the work (standards 05, core first): core picks the items, marks the
 * current one, and runs every menu filter (`wp_nav_menu_objects`, `nav_menu_link_attributes`,
 * `nav_menu_item_title`…), so a plugin that adds or changes items is honoured. Until
 * 2026-11-11 a hand-built tree from wp_get_nav_menu_items() did this and re-derived
 * "current" itself, which skipped all of them and ignored an item's "open in a new tab".
 *
 * @param array{group?: string, depth?: int, list_class?: string, link_class?: string} $o
 *        group: shared `name` on the <details>, so only one opens at a time. Empty in the
 *        drawer, where several open at once is useful. depth: 2 at most; a third level
 *        would be a dropdown inside a dropdown, which the nav doesn't support.
 */
function mbf_menu( string $location, string $block, array $o = [] ): string {
	$o += [ 'group' => '', 'depth' => 2, 'list_class' => "{$block}__list", 'link_class' => "{$block}__link" ];

	return (string) wp_nav_menu(
		[
			'theme_location' => $location,
			'container'      => false,
			'menu_class'     => $o['list_class'],
			// No id (core's default wrap has one): the primary menu prints twice, in the nav and the drawer.
			'items_wrap'     => '<ul class="%2$s" role="list">%3$s</ul>',
			'depth'          => min( 2, (int) $o['depth'] ),
			'fallback_cb'    => false,   // unassigned = not rendered (05), never a list of every page
			'echo'           => false,
			'walker'         => new Mbf_Nav_Walker( $block, (string) $o['group'], (string) $o['link_class'] ),
		]
	);
}

/**
 * The markup wp_nav_menu() can't produce with filters alone. A walker, because a parent
 * with children becomes <details>/<summary>: keyboard-operable and announced as
 * expanded/collapsed natively, with no ARIA to maintain. The summary isn't a link, so a
 * parent with its own URL is repeated as the first sub-item ("Services overview").
 */
class Mbf_Nav_Walker extends Walker_Nav_Menu {

	/** The "overview" <li> for the parent being opened, printed first in its sub-list. */
	private string $overview = '';

	public function __construct( private string $block, private string $group = '', private string $link_class = '' ) {}

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= sprintf( '<ul class="%s" role="list">%s', esc_attr( "{$this->block}__sublist" ), $this->overview );

		$this->overview = '';
	}

	// Closes the <details> start_el() opened: a parent with children always gets a sub-list at depth 2.
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</ul></details>';
	}

	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$item  = $data_object;
		$title = (string) apply_filters( 'nav_menu_item_title', $item->title, $item, $args, $depth );
		$url   = (string) $item->url;

		$output .= 0 === $depth ? sprintf( '<li class="%s">', esc_attr( "{$this->block}__item" ) ) : '<li>';

		if ( 0 === $depth && $this->has_children && 1 !== (int) ( $args->depth ?? 0 ) ) {
			$output .= sprintf(
				'<details class="%1$s"%2$s><summary class="%3$s">%4$s<span class="mbf-chevron" aria-hidden="true"></span></summary>',
				esc_attr( "{$this->block}__group" ),
				'' !== $this->group ? ' name="' . esc_attr( $this->group ) . '"' : '',
				esc_attr( "{$this->block}__link {$this->block}__summary" ),
				esc_html( $title )
			);

			if ( '' !== $url && '#' !== $url ) {
				/* translators: %s: menu item label, e.g. "Services". */
				$this->overview = '<li>' . $this->link( $item, sprintf( __( '%s overview', 'mbf-blank' ), $title ), $args, 1 ) . '</li>';
			}

			return;
		}

		$output .= $this->link( $item, $title, $args, $depth );
	}

	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		$output .= '</li>';
	}

	/**
	 * One <a>, with the attributes core's own walker gives a menu link, through the same filter.
	 */
	private function link( WP_Post $item, string $label, mixed $args, int $depth ): string {
		$atts = [
			'class'        => 0 === $depth ? $this->link_class : "{$this->block}__sublink",
			'href'         => (string) $item->url,
			'aria-current' => $item->current ? 'page' : '',
			'target'       => (string) $item->target,
			'rel'          => '_blank' === $item->target && '' === (string) $item->xfn ? 'noopener' : (string) $item->xfn,
			'title'        => (string) $item->attr_title,
		];

		return '<a' . $this->build_atts( apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth ) ) . '>' . esc_html( $label ) . '</a>';
	}
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

/**
 * A decorative Font Awesome icon from a stored name ("fa-helmet-safety"), or '' when icons
 * are off or the name isn't one. Filtered, because the name comes from a text field. Always
 * aria-hidden: the label beside it carries the meaning.
 *
 * One style for every icon: Duotone Regular (David, 2026-11-10), whose rounded corners match
 * the starter's and whose second layer takes the section accent (src/components/icon.css).
 * A square-cornered brand switches in one line, e.g. S&A's:
 *   add_filter( 'mbf_icon_style', fn() => 'fa-sharp fa-solid' );
 * The kit must serve that style (Font Awesome › Kits › Settings), or icons render blank.
 */
function mbf_icon( string $name, string $class = '' ): string {
	if ( ! mbf_has_icons() || ! preg_match( '/^fa-[a-z0-9-]+$/', $name ) ) {
		return '';
	}

	$style = (string) apply_filters( 'mbf_icon_style', 'fa-duotone fa-regular' );

	return sprintf( '<i class="%s" aria-hidden="true"></i>', esc_attr( trim( "{$style} {$name} {$class}" ) ) );
}

/* --- Files ------------------------------------------------------------------------ */

/**
 * "PDF, 1.2 MB" for an attachment: said out loud before the visitor commits to a download.
 */
function mbf_file_meta( int $id ): string {
	$path = get_attached_file( $id );

	if ( ! $path ) {
		return '';
	}

	$type = strtoupper( (string) pathinfo( $path, PATHINFO_EXTENSION ) );
	$size = file_exists( $path ) ? size_format( (int) filesize( $path ), 1 ) : '';

	return implode( ', ', array_filter( [ $type, (string) $size ] ) );
}
