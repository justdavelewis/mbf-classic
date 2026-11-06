<?php
/**
 * Cards: a heading over a grid of the one card (template-parts/card.php).
 *
 * $args: heading, lead, cards (rows: title, text, url, image) + section controls.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a     = wp_parse_args( $args, [ 'heading' => '', 'lead' => '', 'cards' => [] ] );
$mbf_cards = array_filter( mbf_rows( $mbf_a['cards'] ), static fn( $card ) => '' !== (string) ( $card['title'] ?? '' ) );

if ( ! $mbf_cards ) {
	return;
}

$mbf_id = '' !== (string) $mbf_a['heading'] ? mbf_section_id( $mbf_a['heading'] ) : '';
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-cards' ] ); ?>>
	<div class="mbf-section__inner">
		<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_a['heading'], 'lead' => $mbf_a['lead'], 'id' => $mbf_id ] ); ?>
		<div class="<?php echo esc_attr( mbf_grid_class( count( $mbf_cards ) ) ); ?>">
			<?php
			foreach ( $mbf_cards as $mbf_card ) {
				get_template_part(
					'template-parts/card',
					null,
					[
						'title' => $mbf_card['title'],
						'text'  => $mbf_card['text'] ?? '',
						'url'   => $mbf_card['url'] ?? '',
						'image' => $mbf_card['image'] ?? 0,
						'level' => '' !== (string) $mbf_a['heading'] ? 3 : 2,
					]
				);
			}
			?>
		</div>
	</div>
</section>
