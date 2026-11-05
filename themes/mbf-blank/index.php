<?php
/**
 * Fallback: the posts page (/blog via page_for_posts) and archives.
 * Phase 3 swaps each post for the card partial; phase 2 reads page_for_posts rows.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

get_header();

$mbf_posts_page = (int) get_option( 'page_for_posts' );

if ( is_home() ) {
	$mbf_title = $mbf_posts_page ? get_the_title( $mbf_posts_page ) : __( 'Latest posts', 'mbf-blank' );
} elseif ( is_archive() ) {
	$mbf_title = get_the_archive_title();
} else {
	$mbf_title = get_bloginfo( 'name' );
}
?>
<div class="mbf-section">
	<div class="mbf-section__inner">
		<h1 class="h1"><?php echo wp_kses_post( $mbf_title ); ?></h1>

		<?php if ( have_posts() ) : ?>
			<div class="mbf-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article class="mbf-stack mbf-stack--tight">
						<h2 class="h4"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="mbf-meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
						<?php the_excerpt(); ?>
					</article>
				<?php endwhile; ?>
			</div>

			<?php the_posts_pagination( [ 'class' => 'mbf-pagination' ] ); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing published here yet.', 'mbf-blank' ); ?></p>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
