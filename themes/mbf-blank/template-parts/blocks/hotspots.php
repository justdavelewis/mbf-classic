<?php
/**
 * Hotspots: images with numbered markers, each one paired with an entry in the list beside
 * it. One scene for a product, several for "rooftop / warehouse". Our Image Map Pro.
 *
 * ACCESSIBILITY (from Fisher Alvin's showcase, which got this right):
 *  - The numbered LIST is the content. It's the keyboard and screen-reader path, not a
 *    fallback, and nothing is hidden behind a hover, so touch sees what a mouse sees.
 *  - Markers repeat the list, so they're aria-hidden and not focusable: focusable too, they
 *    would double every tab stop.
 *  - Without JS every scene shows, under its own heading. assets/js/hotspots.js adds the
 *    tabs (it builds them itself, so there's never a tablist that doesn't work) and the
 *    marker ↔ list highlight.
 *
 * $args: heading, lead, scenes (rows: label, image, spots (rows: title, text, url, x, y)).
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a      = wp_parse_args( $args, [ 'heading' => '', 'lead' => '', 'scenes' => [] ] );
$mbf_scenes = array_values( array_filter( mbf_rows( $mbf_a['scenes'] ), static fn( $scene ) => ! empty( $scene['image'] ) ) );

if ( ! $mbf_scenes ) {
	return;
}

mbf_enqueue_script( 'mbf-hotspots', 'assets/js/hotspots.js' );

$mbf_id    = '' !== (string) $mbf_a['heading'] ? mbf_section_id( $mbf_a['heading'] ) : '';
$mbf_level = '' !== $mbf_id ? 'h3' : 'h2';
$mbf_many  = count( $mbf_scenes ) > 1;
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-hotspots' ] ); ?>>
	<div class="mbf-section__inner">
		<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_a['heading'], 'lead' => $mbf_a['lead'], 'id' => $mbf_id ] ); ?>
		<div class="mbf-hotspots__scenes" data-mbf-hotspots>
			<?php foreach ( $mbf_scenes as $mbf_scene ) : ?>
				<?php
				$mbf_spots = array_values( array_filter( mbf_rows( $mbf_scene['spots'] ?? [] ), static fn( $spot ) => '' !== (string) ( $spot['title'] ?? '' ) ) );
				$mbf_label = (string) ( $mbf_scene['label'] ?? '' );
				?>
				<div class="mbf-hotspots__scene" data-mbf-hotspots-scene="<?php echo esc_attr( $mbf_label ); ?>">
					<?php if ( $mbf_many && '' !== $mbf_label ) : ?>
						<<?php echo $mbf_level; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="h4 mbf-hotspots__label"><?php echo esc_html( $mbf_label ); ?></<?php echo $mbf_level; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<?php endif; ?>
					<div class="mbf-hotspots__media">
						<?php echo wp_get_attachment_image( (int) $mbf_scene['image'], 'large', false, [ 'class' => 'mbf-hotspots__image' ] ); ?>
						<?php foreach ( $mbf_spots as $mbf_n => $mbf_spot ) : ?>
							<?php
							// Numbers only reach the style attribute, clamped to the image.
							$mbf_x = max( 0, min( 100, (float) ( $mbf_spot['x'] ?? 0 ) ) );
							$mbf_y = max( 0, min( 100, (float) ( $mbf_spot['y'] ?? 0 ) ) );
							?>
							<span class="mbf-hotspots__marker" aria-hidden="true" data-mbf-spot="<?php echo (int) $mbf_n; ?>" style="--x:<?php echo esc_attr( (string) $mbf_x ); ?>%;--y:<?php echo esc_attr( (string) $mbf_y ); ?>%"><?php echo (int) $mbf_n + 1; ?></span>
						<?php endforeach; ?>
					</div>
					<?php if ( $mbf_spots ) : ?>
						<ol class="mbf-hotspots__list">
							<?php foreach ( $mbf_spots as $mbf_n => $mbf_spot ) : ?>
								<li class="mbf-hotspots__item" data-mbf-spot="<?php echo (int) $mbf_n; ?>">
									<?php if ( '' !== (string) ( $mbf_spot['url'] ?? '' ) ) : ?>
										<a class="mbf-hotspots__title" href="<?php echo esc_url( $mbf_spot['url'] ); ?>"><?php echo esc_html( $mbf_spot['title'] ); ?></a>
									<?php else : ?>
										<span class="mbf-hotspots__title"><?php echo esc_html( $mbf_spot['title'] ); ?></span>
									<?php endif; ?>
									<?php if ( '' !== (string) ( $mbf_spot['text'] ?? '' ) ) : ?>
										<p class="mbf-hotspots__text"><?php echo esc_html( $mbf_spot['text'] ); ?></p>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ol>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
