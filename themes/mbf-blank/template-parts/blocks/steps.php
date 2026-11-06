<?php
/**
 * Steps: an ordered process. The numbers are the <ol>'s own, drawn by a CSS counter, so
 * they're announced once as list positions, not twice as text.
 *
 * $args: heading, lead, items (rows: title, text) + section controls.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a     = wp_parse_args( $args, [ 'heading' => '', 'lead' => '', 'items' => [], 'button_1' => null ] );
$mbf_items = array_filter( mbf_rows( $mbf_a['items'] ), static fn( $item ) => '' !== (string) ( $item['title'] ?? '' ) );

if ( ! $mbf_items ) {
	return;
}

$mbf_id    = '' !== (string) $mbf_a['heading'] ? mbf_section_id( $mbf_a['heading'] ) : '';
$mbf_level = '' !== (string) $mbf_a['heading'] ? 'h3' : 'h2';
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-steps' ] ); ?>>
	<div class="mbf-section__inner">
		<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_a['heading'], 'lead' => $mbf_a['lead'], 'id' => $mbf_id ] ); ?>
		<ol class="mbf-steps__list" role="list">
			<?php foreach ( $mbf_items as $mbf_item ) : ?>
				<li class="mbf-steps__item">
					<<?php echo $mbf_level; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="h4 mbf-steps__title"><?php echo esc_html( $mbf_item['title'] ); ?></<?php echo $mbf_level; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<?php if ( '' !== (string) ( $mbf_item['text'] ?? '' ) ) : ?>
						<p><?php echo esc_html( $mbf_item['text'] ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== (string) ( $mbf_item['tag'] ?? '' ) ) : ?>
						<span class="mbf-steps__tag"><?php echo esc_html( $mbf_item['tag'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
		<?php get_template_part( 'template-parts/cta-buttons', null, [ 'buttons' => [ $mbf_a['button_1'] ] ] ); ?>
	</div>
</section>
