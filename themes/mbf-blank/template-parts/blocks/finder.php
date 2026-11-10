<?php
/**
 * Solution finder section: the finder on the section's ground. With no heading of its own,
 * the first question is the section heading, so it isn't asked twice.
 *
 * $args: heading, lead + section controls. Options: Site Settings › Solution finder.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a      = wp_parse_args( $args, [ 'heading' => '', 'lead' => '' ] );
$mbf_config = mbf_finder_config();

if ( ! $mbf_config ) {
	return;
}

$mbf_own     = '' !== (string) $mbf_a['heading'];
$mbf_heading = $mbf_own ? (string) $mbf_a['heading'] : $mbf_config['question'];
$mbf_id      = mbf_section_id( $mbf_heading );
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-finder-section' ] ); ?>>
	<div class="mbf-section__inner">
		<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_heading, 'lead' => $mbf_a['lead'], 'id' => $mbf_id ] ); ?>
		<?php get_template_part( 'template-parts/finder', null, [ 'context' => 'section', 'level' => 3, 'show_question' => $mbf_own ] ); ?>
	</div>
</section>
