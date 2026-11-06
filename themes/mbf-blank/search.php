<?php
/**
 * Search results: each result is the one card, at h2 under the results h1.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part(
	'template-parts/page-hero',
	null,
	[
		/* translators: %s: the search query. */
		'title'  => sprintf( __( 'Search results for “%s”', 'mbf-blank' ), get_search_query() ),
		'crumbs' => mbf_crumbs(),
	]
);
?>
<div class="mbf-section">
	<div class="mbf-section__inner">
		<?php get_search_form(); ?>

		<?php if ( have_posts() ) : ?>
			<div class="<?php echo esc_attr( mbf_grid_class( (int) $GLOBALS['wp_query']->post_count ) ); ?>">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part(
						'template-parts/card',
						null,
						[
							'title' => get_the_title(),
							'text'  => get_the_excerpt(),
							'url'   => get_permalink(),
							'level' => 2,
							'cta'   => __( 'Read more', 'mbf-blank' ),
						]
					);
				endwhile;
				?>
			</div>

			<?php the_posts_pagination( [ 'class' => 'mbf-pagination' ] ); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing matched that search. Try different words.', 'mbf-blank' ); ?></p>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
