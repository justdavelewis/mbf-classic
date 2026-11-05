<?php
/**
 * Search results. Phase 3 swaps each result for the card partial.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="mbf-section">
	<div class="mbf-section__inner">
		<h1 class="h1">
			<?php
			/* translators: %s: the search query. */
			printf( esc_html__( 'Search results for “%s”', 'mbf-blank' ), esc_html( get_search_query() ) );
			?>
		</h1>

		<?php get_search_form(); ?>

		<?php if ( have_posts() ) : ?>
			<div class="mbf-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article class="mbf-stack mbf-stack--tight">
						<h2 class="h4"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<?php the_excerpt(); ?>
					</article>
				<?php endwhile; ?>
			</div>

			<?php the_posts_pagination( [ 'class' => 'mbf-pagination' ] ); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing matched that search. Try different words.', 'mbf-blank' ); ?></p>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
