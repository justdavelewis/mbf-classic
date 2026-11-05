<?php
/**
 * Search form (used by get_search_form() on 404 and search).
 *
 * Ours rather than core's so the submit is an .mbf-button and the label is visible
 * text tied to the field, not a screen-reader-only span inside a wrapping label.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_search_id = wp_unique_id( 'mbf-search-' );
?>
<form role="search" method="get" class="mbf-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="mbf-search__label" for="<?php echo esc_attr( $mbf_search_id ); ?>"><?php esc_html_e( 'Search this site', 'mbf-blank' ); ?></label>
	<div class="mbf-search__row">
		<input class="mbf-search__field" type="search" id="<?php echo esc_attr( $mbf_search_id ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>">
		<button class="mbf-button" type="submit"><?php esc_html_e( 'Search', 'mbf-blank' ); ?></button>
	</div>
</form>
