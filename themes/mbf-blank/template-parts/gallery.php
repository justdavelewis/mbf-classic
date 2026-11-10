<?php
/**
 * Photo gallery: one large photo, a counter, prev/next and a thumbnail strip. From the
 * solution hero on Mantech and Fisher Alvin (2026-11-10).
 *
 * Every photo is in the markup and the script only changes which one shows, so without JS
 * the first photo shows and nothing breaks. The thumbnails are the keyboard and screen-reader
 * path (buttons with aria-pressed, as on FA). The arrows over the photo repeat them for a
 * pointer, so they stay out of the tab order: focusable, they doubled every stop.
 *
 * $args: ids (attachment IDs, first shown first), eager (true when this is the LCP image),
 *        label (names the thumbnail strip).
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a   = wp_parse_args( $args, [ 'ids' => [], 'eager' => false, 'label' => __( 'Photos', 'mbf-blank' ) ] );
$mbf_ids = array_values( array_filter( array_map( 'intval', mbf_rows( $mbf_a['ids'] ) ), static fn( int $id ) => (bool) wp_get_attachment_image_url( $id ) ) );
$mbf_n   = count( $mbf_ids );

if ( ! $mbf_n ) {
	return;
}

if ( $mbf_n > 1 ) {
	mbf_enqueue_script( 'mbf-gallery', 'assets/js/gallery.js' );
}

// Captions are optional per photo. The overlay prints only when one has text, and the script swaps it.
$mbf_captions = array_map( static fn( int $id ) => trim( (string) wp_get_attachment_caption( $id ) ), $mbf_ids );
$mbf_any_cap  = (bool) array_filter( $mbf_captions, 'strlen' );
?>
<div class="mbf-gallery"<?php echo $mbf_n > 1 ? ' data-mbf-gallery' : ''; ?>>
	<figure class="mbf-gallery__main">
		<?php
		foreach ( $mbf_ids as $mbf_i => $mbf_id ) {
			// Alt text comes from the Media Library (04). A hidden photo is hidden from AT too.
			echo wp_get_attachment_image(
				$mbf_id,
				'large',
				false,
				array_filter(
					[
						'class'         => 'mbf-gallery__image' . ( 0 === $mbf_i ? ' is-on' : '' ),
						'data-caption'  => $mbf_any_cap ? $mbf_captions[ $mbf_i ] : null,
						'aria-hidden'   => 0 === $mbf_i ? null : 'true',
						'loading'       => 0 === $mbf_i && $mbf_a['eager'] ? 'eager' : 'lazy',
						'fetchpriority' => 0 === $mbf_i && $mbf_a['eager'] ? 'high' : null,
					],
					static fn( $value ) => null !== $value
				)
			);
		}
		?>

		<?php if ( $mbf_n > 1 ) : ?>
			<p class="mbf-gallery__count" data-mbf-gallery-count aria-live="polite">
				<?php
				/* translators: 1: number of the photo shown, 2: number of photos. */
				printf( esc_html__( 'Photo %1$s of %2$s', 'mbf-blank' ), '<span data-mbf-gallery-current>1</span>', esc_html( (string) $mbf_n ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed markup, number escaped
				?>
			</p>
			<?php foreach ( [ -1 => 'prev', 1 => 'next' ] as $mbf_step => $mbf_dir ) : ?>
				<button type="button" class="mbf-gallery__nav mbf-gallery__nav--<?php echo esc_attr( $mbf_dir ); ?>" data-mbf-gallery-step="<?php echo esc_attr( (string) $mbf_step ); ?>" tabindex="-1" aria-hidden="true">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="<?php echo 'prev' === $mbf_dir ? 'M15 18l-6-6 6-6' : 'M9 18l6-6-6-6'; ?>"/></svg>
				</button>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php if ( $mbf_any_cap ) : ?>
			<figcaption class="mbf-gallery__caption" data-mbf-gallery-caption<?php echo '' === $mbf_captions[0] ? ' hidden' : ''; ?>><?php echo esc_html( $mbf_captions[0] ); ?></figcaption>
		<?php endif; ?>
	</figure>

	<?php if ( $mbf_n > 1 ) : ?>
		<ul class="mbf-gallery__thumbs" role="list" aria-label="<?php echo esc_attr( $mbf_a['label'] ); ?>">
			<?php foreach ( $mbf_ids as $mbf_i => $mbf_id ) : ?>
				<li>
					<button type="button" class="mbf-gallery__thumb" aria-pressed="<?php echo 0 === $mbf_i ? 'true' : 'false'; ?>" data-mbf-gallery-index="<?php echo esc_attr( (string) $mbf_i ); ?>">
						<?php echo wp_get_attachment_image( $mbf_id, 'medium', false, [ 'alt' => '', 'loading' => 'lazy' ] ); ?>
						<span class="screen-reader-text">
							<?php
							/* translators: %d: photo number. */
							printf( esc_html__( 'Show photo %d', 'mbf-blank' ), (int) $mbf_i + 1 );
							?>
						</span>
					</button>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
