<?php
/**
 * Hotspots: the site's scenes (Site Settings › Hotspots, inc/hotspots.php), each an image with
 * numbered markers beside a numbered list of links. Our Image Map Pro.
 *
 * Nothing to fill in on the page (David, 2026-11-10): the section adds the site's scenes and
 * can only choose which to show, plus the usual section controls. Each spot points at a
 * solution (a finder answer) or sets its own title, text and link.
 *
 * LAYOUT (David, 2026-11-10): the image at 2:1 beside a compact list of numbered text links,
 * title only. The information lives in a popup card at the marker (title, the info line, the
 * linked page's featured image), shown on hovering the marker or its link: under a big image
 * the list item can be off-screen, and full cards under the image made the list redundant.
 *
 * ACCESSIBILITY (from Fisher Alvin's showcase, which got this right):
 *  - The numbered LIST is the content, and the keyboard and screen-reader path. Nothing is
 *    hidden behind a hover: the popup's info line is each link's accessible description
 *    (aria-describedby reads it, though the marker is aria-hidden).
 *  - Markers and popups repeat the list, so they're aria-hidden and not focusable: focusable
 *    too, they would double every tab stop. A marker tap follows its link.
 *  - The popup can be hovered and is dismissed with Escape (1.4.13).
 *  - Without JS every scene shows, under its own heading. assets/js/hotspots.js adds the tabs
 *    and the marker ↔ list pairing.
 *
 * $args: show_scenes (scene keys; empty = all) + section controls.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a      = wp_parse_args( $args, [ 'show_scenes' => [] ] );
$mbf_config = mbf_hotspots_config();
$mbf_show   = array_filter( (array) $mbf_a['show_scenes'], 'is_string' );
$mbf_scenes = array_values( array_filter( $mbf_config['scenes'], static fn( array $scene ) => ! $mbf_show || in_array( $scene['key'], $mbf_show, true ) ) );

if ( ! $mbf_scenes ) {
	return;
}

mbf_enqueue_script( 'mbf-hotspots', 'assets/js/hotspots.js' );

$mbf_id    = '' !== $mbf_config['heading'] ? mbf_section_id( $mbf_config['heading'] ) : '';
$mbf_level = '' !== $mbf_id ? 'h3' : 'h2';
$mbf_many  = count( $mbf_scenes ) > 1;
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-hotspots' ] ); ?>>
	<div class="mbf-section__inner">
		<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_config['heading'], 'lead' => $mbf_config['lead'], 'id' => $mbf_id ] ); ?>
		<div class="mbf-hotspots__scenes" data-mbf-hotspots>
			<?php foreach ( $mbf_scenes as $mbf_scene ) : ?>
				<div class="mbf-hotspots__scene" data-mbf-hotspots-scene="<?php echo esc_attr( $mbf_scene['label'] ); ?>">
					<?php if ( $mbf_many && '' !== $mbf_scene['label'] ) : ?>
						<<?php echo $mbf_level; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="h4 mbf-hotspots__label"><?php echo esc_html( $mbf_scene['label'] ); ?></<?php echo $mbf_level; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<?php endif; ?>
					<div class="mbf-hotspots__media">
						<?php echo wp_get_attachment_image( $mbf_scene['image'], 'large', false, [ 'class' => 'mbf-hotspots__image' ] ); ?>
						<?php foreach ( $mbf_scene['spots'] as $mbf_n => $mbf_spot ) : ?>
							<?php
							$mbf_uid = wp_unique_id( 'mbf-spot-' );

							// The popup opens away from the nearest edges: right of a spot in the left half, below one in the top half.
							$mbf_pop = 'mbf-hotspots__pop mbf-hotspots__pop--' . ( $mbf_spot['x'] < 50 ? 'start' : 'end' ) . ( $mbf_spot['y'] < 50 ? ' mbf-hotspots__pop--below' : '' );
							?>
							<span class="mbf-hotspots__marker" aria-hidden="true" data-mbf-spot="<?php echo (int) $mbf_n; ?>" style="--x:<?php echo esc_attr( (string) $mbf_spot['x'] ); ?>%;--y:<?php echo esc_attr( (string) $mbf_spot['y'] ); ?>%">
								<?php echo (int) $mbf_n + 1; ?>
								<span class="<?php echo esc_attr( $mbf_pop ); ?>">
									<?php
									if ( $mbf_spot['image'] ) {
										// Decorative: the title says what it is. Lazy: only seen on hover.
										echo wp_get_attachment_image( $mbf_spot['image'], 'medium', false, [ 'class' => 'mbf-hotspots__pop-image', 'alt' => '', 'loading' => 'lazy' ] );
									}
									?>
									<span class="mbf-hotspots__pop-body">
										<span class="mbf-hotspots__pop-title"><?php echo esc_html( $mbf_spot['title'] ); ?></span>
										<?php if ( '' !== $mbf_spot['text'] ) : ?>
											<span class="mbf-hotspots__pop-text" id="<?php echo esc_attr( $mbf_uid ); ?>"><?php echo esc_html( $mbf_spot['text'] ); ?></span>
										<?php endif; ?>
									</span>
								</span>
							</span>
							<?php
							// Kept for the list below: its link is described by this spot's info line.
							$mbf_scene['spots'][ $mbf_n ]['uid'] = '' !== $mbf_spot['text'] ? $mbf_uid : '';
							?>
						<?php endforeach; ?>
					</div>
					<?php if ( $mbf_scene['spots'] ) : ?>
						<ol class="mbf-hotspots__list" role="list">
							<?php foreach ( $mbf_scene['spots'] as $mbf_n => $mbf_spot ) : ?>
								<li class="mbf-hotspots__item" data-mbf-spot="<?php echo (int) $mbf_n; ?>">
									<?php if ( '' !== $mbf_spot['url'] ) : ?>
										<a class="mbf-hotspots__link" href="<?php echo esc_url( $mbf_spot['url'] ); ?>"<?php echo '' !== $mbf_spot['uid'] ? ' aria-describedby="' . esc_attr( $mbf_spot['uid'] ) . '"' : ''; ?>>
									<?php else : ?>
										<span class="mbf-hotspots__link">
									<?php endif; ?>
										<span class="mbf-hotspots__n" aria-hidden="true"><?php echo (int) $mbf_n + 1; ?></span>
										<span class="mbf-hotspots__title"><?php echo esc_html( $mbf_spot['title'] ); ?></span>
									<?php echo '' !== $mbf_spot['url'] ? '</a>' : '</span>'; ?>
								</li>
							<?php endforeach; ?>
						</ol>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
