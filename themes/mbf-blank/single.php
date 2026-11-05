<?php
/**
 * Single post.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article class="mbf-section">
		<div class="mbf-section__inner">
			<header class="mbf-stack mbf-stack--tight">
				<h1 class="h1"><?php the_title(); ?></h1>
				<p class="mbf-meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
			</header>
			<?php the_post_thumbnail( 'large' ); ?>
			<div class="prose"><?php the_content(); ?></div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
