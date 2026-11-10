<?php
/**
 * The page builder: one `page_layouts` Flexible Content field, each layout rendered by
 * template-parts/blocks/{layout}.php with $args only (05). Partials never call
 * get_field()/get_sub_field(), so the same file can become an ACF Block render template.
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Renders every row of a Flexible Content field. A layout with no template prints an
 * admin-only comment instead of failing silently (get_template_part() returns nothing).
 */
function mbf_render_layouts( string $field = 'page_layouts', int|false $post_id = false ): void {
	if ( ! function_exists( 'have_rows' ) || ! have_rows( $field, $post_id ) ) {
		return;
	}

	mbf_section_id_registry( true );

	while ( have_rows( $field, $post_id ) ) {
		the_row();

		$layout = (string) get_row_layout();
		$slug   = str_replace( '_', '-', $layout );

		if ( ! locate_template( "template-parts/blocks/{$slug}.php" ) ) {
			if ( current_user_can( 'edit_posts' ) ) {
				printf( '<!-- mbf: no template for "%s" -->', esc_html( $layout ) );
			}
			continue;
		}

		get_template_part( "template-parts/blocks/{$slug}", null, mbf_row_args( $layout ) );
	}
}

/**
 * The current row as $args: [ 'layout' => …, field name => formatted value ].
 *
 * Verified 2026-11-05 (ACF Pro 6.8.10): get_row( true ) keys by field NAME and returns
 * the seamless section-controls clone flattened (ground/accent/spacing at the top level).
 */
function mbf_row_args( string $layout ): array {
	$row = get_row( true ) ?: [];
	unset( $row['acf_fc_layout'] );

	// position: 1-based row index, so a hero knows whether it owns the page's h1.
	return [ 'layout' => $layout, 'position' => (int) get_row_index() ] + $row;
}

/**
 * Layout names on a post, in order, without formatting any values (FC stores them as
 * the raw meta value). Used to ask "does this page start with a hero?".
 *
 * @return string[]
 */
function mbf_layout_names( int $post_id, string $field = 'page_layouts' ): array {
	// Without ACF the rows can't render, so report none: page.php then falls back to the_content()
	// instead of printing nothing but the page header.
	if ( ! function_exists( 'get_field' ) ) {
		return [];
	}

	$names = get_post_meta( $post_id, $field, true );

	return is_array( $names ) ? array_values( array_map( 'strval', $names ) ) : [];
}
