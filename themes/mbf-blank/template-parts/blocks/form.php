<?php
/**
 * Enquiry form section: heading + text beside the Site Settings enquiry form.
 *
 * $args: heading, text + section controls. The form ID is site-wide (Site Settings).
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a  = wp_parse_args( $args, [ 'heading' => '', 'text' => '' ] );
$mbf_id = '' !== (string) $mbf_a['heading'] ? mbf_section_id( $mbf_a['heading'] ) : '';
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-enquiry' ] ); ?>>
	<div class="mbf-section__inner">
		<div class="mbf-enquiry__grid">
			<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_a['heading'], 'lead' => $mbf_a['text'], 'id' => $mbf_id ] ); ?>
			<?php get_template_part( 'template-parts/enquiry-form', null, [ 'form_id' => (int) mbf_setting( 'form_enquiry' ) ] ); ?>
		</div>
	</div>
</section>
