<?php
/**
 * Page: the default page header (unless the first section is a hero, which owns the h1)
 * → page_layouts → the_content() as the fallback when a page has no sections (docs/12).
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$mbf_layouts = mbf_layout_names( get_the_ID() );

	if ( 'hero' !== ( $mbf_layouts[0] ?? '' ) ) {
		get_template_part( 'template-parts/page-hero', null, [ 'title' => get_the_title(), 'crumbs' => mbf_crumbs() ] );
	}

	if ( $mbf_layouts ) {
		mbf_render_layouts();
	} else {
		?>
		<div class="mbf-section">
			<div class="mbf-section__inner">
				<div class="prose"><?php the_content(); ?></div>
				<?php wp_link_pages(); ?>
			</div>
		</div>
		<?php
	}
endwhile;

get_footer();
