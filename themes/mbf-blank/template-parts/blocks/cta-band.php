<?php
/**
 * Call-to-action band: a heading, a line of text and two buttons. With no second button,
 * the Site Settings phone number stands in (03).
 *
 * $args: heading, text, button_1, button_2 + section controls.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a = wp_parse_args( $args, [ 'eyebrow' => '', 'heading' => '', 'text' => '', 'button_1' => null, 'button_2' => null ] );

if ( '' === (string) $mbf_a['heading'] ) {
	return;
}

$mbf_id = mbf_section_id( $mbf_a['heading'] );
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-cta-band' ] ); ?>>
	<div class="mbf-section__inner">
		<div class="mbf-cta-band__row">
			<div class="mbf-cta-band__text">
				<?php if ( '' !== (string) $mbf_a['eyebrow'] ) : ?>
					<p class="mbf-eyebrow"><?php echo esc_html( $mbf_a['eyebrow'] ); ?></p>
				<?php endif; ?>
				<h2 class="h2" id="<?php echo esc_attr( $mbf_id ); ?>"><?php echo esc_html( $mbf_a['heading'] ); ?></h2>
				<?php if ( '' !== (string) $mbf_a['text'] ) : ?>
					<p class="lead"><?php echo esc_html( $mbf_a['text'] ); ?></p>
				<?php endif; ?>
			</div>
			<?php get_template_part( 'template-parts/cta-buttons', null, [ 'buttons' => [ $mbf_a['button_1'], $mbf_a['button_2'] ], 'phone_fallback' => true ] ); ?>
		</div>
	</div>
</section>
