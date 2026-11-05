<?php
/**
 * Page. Phase 2 adds page-hero → page_layouts, keeping the_content() as the fallback.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="mbf-section">
		<div class="mbf-section__inner">
			<h1 class="h1"><?php the_title(); ?></h1>
			<div class="prose"><?php the_content(); ?></div>
		</div>
	</div>
	<?php
endwhile;

get_footer();
