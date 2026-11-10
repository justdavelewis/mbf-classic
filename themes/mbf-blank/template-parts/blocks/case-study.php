<?php
/**
 * Case study feature: the story, its results, the client's words and a photo.
 *
 * $args: eyebrow, heading (required), text, metrics (rows: value, label), quote, name, role,
 * image (required), image_side, button_1 + section controls.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a = wp_parse_args( $args, [ 'eyebrow' => '', 'heading' => '', 'text' => '', 'metrics' => [], 'quote' => '', 'name' => '', 'role' => '', 'image' => 0, 'image_side' => 'end', 'button_1' => null ] );

if ( '' === (string) $mbf_a['heading'] || ! $mbf_a['image'] ) {
	return;
}

$mbf_id      = mbf_section_id( $mbf_a['heading'] );
$mbf_side    = 'start' === $mbf_a['image_side'] ? 'start' : 'end';
$mbf_metrics = array_filter( mbf_rows( $mbf_a['metrics'] ), static fn( $row ) => '' !== (string) ( $row['value'] ?? '' ) );
$mbf_cite    = implode( ', ', array_filter( [ (string) $mbf_a['name'], (string) $mbf_a['role'] ], 'strlen' ) );
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-case-study', "mbf-case-study--image-{$mbf_side}" ] ); ?>>
	<div class="mbf-section__inner">
		<div class="mbf-case-study__grid">
			<div class="mbf-case-study__text">
				<?php get_template_part( 'template-parts/section-heading', null, [ 'eyebrow' => $mbf_a['eyebrow'], 'heading' => $mbf_a['heading'], 'lead' => $mbf_a['text'], 'id' => $mbf_id ] ); ?>
				<?php if ( $mbf_metrics ) : ?>
					<ul class="mbf-case-study__metrics" role="list">
						<?php foreach ( $mbf_metrics as $mbf_metric ) : ?>
							<li class="mbf-case-study__metric">
								<span class="mbf-case-study__value"><?php echo esc_html( $mbf_metric['value'] ); ?></span>
								<span class="mbf-case-study__label"><?php echo esc_html( $mbf_metric['label'] ?? '' ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( '' !== (string) $mbf_a['quote'] ) : ?>
					<figure class="mbf-case-study__quote">
						<blockquote><p><?php echo esc_html( $mbf_a['quote'] ); ?></p></blockquote>
						<?php if ( '' !== $mbf_cite ) : ?>
							<figcaption class="mbf-meta"><?php echo esc_html( $mbf_cite ); ?></figcaption>
						<?php endif; ?>
					</figure>
				<?php endif; ?>
				<?php get_template_part( 'template-parts/cta-buttons', null, [ 'buttons' => [ $mbf_a['button_1'] ] ] ); ?>
			</div>
			<?php echo wp_get_attachment_image( (int) $mbf_a['image'], 'large', false, [ 'class' => 'mbf-case-study__image' ] ); ?>
		</div>
	</div>
</section>
