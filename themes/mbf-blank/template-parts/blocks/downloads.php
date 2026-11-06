<?php
/**
 * Downloads: brochures, guides, certificates.
 *
 * $args: heading, lead, files (rows: title, file, note, cover) + section controls.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a = wp_parse_args( $args, [ 'heading' => '', 'lead' => '', 'files' => [] ] );

if ( ! mbf_rows( $mbf_a['files'] ) ) {
	return;
}

$mbf_id = '' !== (string) $mbf_a['heading'] ? mbf_section_id( $mbf_a['heading'] ) : '';
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-downloads' ] ); ?>>
	<div class="mbf-section__inner">
		<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_a['heading'], 'lead' => $mbf_a['lead'], 'id' => $mbf_id ] ); ?>
		<?php get_template_part( 'template-parts/file-list', null, [ 'files' => $mbf_a['files'] ] ); ?>
	</div>
</section>
