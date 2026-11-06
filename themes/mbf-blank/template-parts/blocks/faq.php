<?php
/**
 * FAQ: questions as native <details> disclosures (03: not a card). Several may be open.
 *
 * $args: heading, items (rows: question, answer) + section controls.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a     = wp_parse_args( $args, [ 'heading' => '', 'lead' => '', 'items' => [], 'aside_heading' => '', 'aside_text' => '', 'aside_button' => null ] );
$mbf_items = array_filter( mbf_rows( $mbf_a['items'] ), static fn( $item ) => '' !== (string) ( $item['question'] ?? '' ) );

if ( ! $mbf_items ) {
	return;
}

$mbf_id    = '' !== (string) $mbf_a['heading'] ? mbf_section_id( $mbf_a['heading'] ) : '';
$mbf_aside = '' !== (string) $mbf_a['aside_heading'];
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-faq' ] ); ?>>
	<div class="mbf-section__inner">
		<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_a['heading'], 'lead' => $mbf_a['lead'], 'id' => $mbf_id ] ); ?>
		<?php if ( $mbf_aside ) : ?>
			<div class="mbf-faq__grid">
		<?php endif; ?>
		<div class="mbf-faq__list">
			<?php foreach ( $mbf_items as $mbf_item ) : ?>
				<details class="mbf-faq__item">
					<summary class="mbf-faq__question">
						<?php echo esc_html( $mbf_item['question'] ); ?>
						<span class="mbf-chevron" aria-hidden="true"></span>
					</summary>
					<div class="mbf-faq__answer prose"><?php echo wp_kses_post( $mbf_item['answer'] ?? '' ); ?></div>
				</details>
			<?php endforeach; ?>
		</div>
		<?php if ( $mbf_aside ) : ?>
				<aside class="mbf-faq__aside">
					<h3 class="h4"><?php echo esc_html( $mbf_a['aside_heading'] ); ?></h3>
					<?php if ( '' !== (string) $mbf_a['aside_text'] ) : ?>
						<p><?php echo esc_html( $mbf_a['aside_text'] ); ?></p>
					<?php endif; ?>
					<?php get_template_part( 'template-parts/cta-buttons', null, [ 'buttons' => [ $mbf_a['aside_button'] ] ] ); ?>
				</aside>
			</div>
		<?php endif; ?>
	</div>
</section>
