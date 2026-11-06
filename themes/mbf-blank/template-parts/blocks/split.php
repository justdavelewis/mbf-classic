<?php
/**
 * Split: text beside an image. The DOM is always text → image (reading order); "image
 * before the text" moves it with grid-column, never `order` (03, 04).
 *
 * $args: heading, body, image, image_side (end|start), button_1 + section controls.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a = wp_parse_args( $args, [ 'heading' => '', 'body' => '', 'image' => 0, 'image_side' => 'end', 'button_1' => null ] );

if ( ! $mbf_a['image'] ) {
	return;
}

$mbf_id   = '' !== (string) $mbf_a['heading'] ? mbf_section_id( $mbf_a['heading'] ) : '';
$mbf_side = 'start' === $mbf_a['image_side'] ? 'start' : 'end';
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-split', "mbf-split--image-{$mbf_side}" ] ); ?>>
	<div class="mbf-section__inner">
		<div class="mbf-split__grid">
			<div class="mbf-split__text">
				<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_a['heading'], 'id' => $mbf_id ] ); ?>
				<?php if ( '' !== (string) $mbf_a['body'] ) : ?>
					<div class="prose"><?php echo wp_kses_post( $mbf_a['body'] ); ?></div>
				<?php endif; ?>
				<?php get_template_part( 'template-parts/cta-buttons', null, [ 'buttons' => [ $mbf_a['button_1'] ] ] ); ?>
			</div>
			<?php echo wp_get_attachment_image( (int) $mbf_a['image'], 'large', false, [ 'class' => 'mbf-split__image' ] ); ?>
		</div>
	</div>
</section>
