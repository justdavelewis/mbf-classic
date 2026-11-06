<?php
/**
 * Text: a heading and rich text at reading width.
 *
 * $args: heading, body (WYSIWYG HTML) + section controls.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a = wp_parse_args( $args, [ 'heading' => '', 'body' => '' ] );

if ( '' === (string) $mbf_a['heading'] && '' === (string) $mbf_a['body'] ) {
	return;
}

$mbf_id = '' !== (string) $mbf_a['heading'] ? mbf_section_id( $mbf_a['heading'] ) : '';
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-text' ] ); ?>>
	<div class="mbf-section__inner">
		<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_a['heading'], 'id' => $mbf_id ] ); ?>
		<?php if ( '' !== (string) $mbf_a['body'] ) : ?>
			<div class="prose"><?php echo wp_kses_post( $mbf_a['body'] ); ?></div>
		<?php endif; ?>
	</div>
</section>
