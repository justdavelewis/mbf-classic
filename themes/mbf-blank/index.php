<?php
/**
 * Fallback: the posts page (/blog via page_for_posts) and archives. Each post is the one
 * card, at h2 under the archive's h1 (03 rule 8).
 *
 * /blog renders here, not through page.php, so its page's sections are read from
 * page_for_posts explicitly (05) and follow the post list.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

get_header();

$mbf_posts_page = (int) get_option( 'page_for_posts' );

if ( is_home() ) {
	$mbf_title = $mbf_posts_page ? get_the_title( $mbf_posts_page ) : __( 'Latest posts', 'mbf-blank' );
} elseif ( is_archive() ) {
	$mbf_title = wp_strip_all_tags( get_the_archive_title() );
} else {
	$mbf_title = get_bloginfo( 'name' );
}

get_template_part( 'template-parts/page-hero', null, [ 'title' => $mbf_title, 'crumbs' => mbf_crumbs() ] );
?>
<div class="mbf-section">
	<div class="mbf-section__inner">
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
							'image' => (int) get_post_thumbnail_id(),
							'level' => 2,
							'cta'   => __( 'Read more', 'mbf-blank' ),
						]
					);
				endwhile;
				?>
			</div>

			<?php the_posts_pagination( [ 'class' => 'mbf-pagination' ] ); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing published here yet.', 'mbf-blank' ); ?></p>
		<?php endif; ?>
	</div>
</div>
<?php
if ( is_home() && $mbf_posts_page ) {
	mbf_render_layouts( 'page_layouts', $mbf_posts_page );
}

get_footer();
