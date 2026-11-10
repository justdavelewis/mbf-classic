<?php
/**
 * Stats: big values with labels. Value then label in the DOM, so it reads as it looks
 * ("25+ projects"), with no visual reordering (04).
 *
 * $args: heading, items (rows: value, label) + section controls.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a     = wp_parse_args( $args, [ 'heading' => '', 'items' => [], 'badges' => [] ] );
$mbf_items = array_filter( mbf_rows( $mbf_a['items'] ), static fn( $item ) => '' !== (string) ( $item['value'] ?? '' ) );

if ( ! $mbf_items ) {
	return;
}

$mbf_id = '' !== (string) $mbf_a['heading'] ? mbf_section_id( $mbf_a['heading'] ) : '';
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-stats' ] ); ?>>
	<div class="mbf-section__inner">
		<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_a['heading'], 'id' => $mbf_id ] ); ?>
		<ul class="mbf-stats__list" role="list">
			<?php foreach ( $mbf_items as $mbf_item ) : ?>
				<li class="mbf-stat">
					<span class="mbf-stat__value"><?php echo esc_html( $mbf_item['value'] ); ?></span>
					<span class="mbf-stat__label"><?php echo esc_html( $mbf_item['label'] ?? '' ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php get_template_part( 'template-parts/badges', null, [ 'items' => $mbf_a['badges'] ] ); ?>
	</div>
</section>
