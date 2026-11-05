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
