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
	<article>
		<?php
		get_template_part(
			'template-parts/page-hero',
			null,
			[
				'title'    => get_the_title(),
				'meta'     => get_the_date(),
				'datetime' => get_the_date( 'c' ),
				'crumbs'   => mbf_crumbs(),
			]
		);
		?>
		<div class="mbf-section">
			<div class="mbf-section__inner">
				<?php the_post_thumbnail( 'large' ); ?>
				<div class="prose"><?php the_content(); ?></div>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
