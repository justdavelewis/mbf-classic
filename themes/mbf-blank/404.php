<?php
/**
 * Not found.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part(
	'template-parts/page-hero',
	null,
	[
		'title'  => __( 'Page not found', 'mbf-blank' ),
		'lead'   => __( 'That page has moved or no longer exists. Try a search, or start again from the home page.', 'mbf-blank' ),
		'crumbs' => mbf_crumbs(),
	]
);
?>
<div class="mbf-section">
	<div class="mbf-section__inner">
		<?php get_search_form(); ?>
		<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go to the home page', 'mbf-blank' ); ?></a></p>
	</div>
</div>
<?php
get_footer();
