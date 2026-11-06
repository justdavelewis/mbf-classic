<?php
/**
 * FAQ: questions as native <details> disclosures (03: not a card). Several may be open.
 *
 * $args: heading, items (rows: question, answer) + section controls.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a     = wp_parse_args( $args, [ 'heading' => '', 'items' => [] ] );
$mbf_items = array_filter( mbf_rows( $mbf_a['items'] ), static fn( $item ) => '' !== (string) ( $item['question'] ?? '' ) );

if ( ! $mbf_items ) {
	return;
}

$mbf_id = '' !== (string) $mbf_a['heading'] ? mbf_section_id( $mbf_a['heading'] ) : '';
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-faq' ] ); ?>>
	<div class="mbf-section__inner">
		<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_a['heading'], 'id' => $mbf_id ] ); ?>
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
	</div>
</section>
