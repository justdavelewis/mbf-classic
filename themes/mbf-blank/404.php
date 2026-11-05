<?php
/**
 * Not found.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="mbf-section">
	<div class="mbf-section__inner">
		<div class="mbf-stack">
			<h1 class="h1"><?php esc_html_e( 'Page not found', 'mbf-blank' ); ?></h1>
			<p class="lead"><?php esc_html_e( 'That page has moved or no longer exists. Try a search, or start again from the home page.', 'mbf-blank' ); ?></p>
			<?php get_search_form(); ?>
			<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go to the home page', 'mbf-blank' ); ?></a></p>
		</div>
	</div>
</div>
<?php
get_footer();
