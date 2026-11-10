<?php
/**
 * Hero: the page's opening statement. It owns the page's h1 only when it's the first row
 * (page.php then skips the default page header). Anywhere else it's an h2, so a page never
 * gets two h1s.
 *
 * Panel 'gallery' is the solution hero (Mantech's and FA's product header, 2026-11-10): photos
 * on one side; eyebrow, title, lead, badges, facts at a glance and buttons on the other. As
 * the first row it prints the page's breadcrumb too, since page.php then skips the page header.
 *
 * $args: eyebrow, heading, lead, button_1, button_2, badges, panel (image|finder|gallery),
 *        image, gallery, glance, background_image + section controls, position.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a = wp_parse_args( $args, [ 'eyebrow' => '', 'heading' => '', 'lead' => '', 'button_1' => null, 'button_2' => null, 'badges' => [], 'panel' => 'image', 'image' => 0, 'gallery' => [], 'glance' => [], 'background_image' => 0, 'position' => 1 ] );

if ( '' === (string) $mbf_a['heading'] ) {
	return;
}

$mbf_id    = mbf_section_id( $mbf_a['heading'] );
$mbf_first = 1 === (int) $mbf_a['position'];
$mbf_tag   = $mbf_first ? 'h1' : 'h2';

// The finder needs its options set; without them the hero falls back to its plain text column.
$mbf_finder = 'finder' === $mbf_a['panel'] && mbf_finder_config();
$mbf_image  = $mbf_finder ? 0 : (int) $mbf_a['image'];
$mbf_photos = 'gallery' === $mbf_a['panel'] ? array_filter( array_map( 'intval', mbf_rows( $mbf_a['gallery'] ) ) ) : [];
$mbf_glance = array_filter( mbf_rows( $mbf_a['glance'] ), static fn( $row ) => '' !== (string) ( $row['label'] ?? '' ) && '' !== (string) ( $row['value'] ?? '' ) );
$mbf_badges = array_filter( array_column( mbf_rows( $mbf_a['badges'] ), 'text' ), 'strlen' );

// The veil's contrast is only measured for light text, so a photo and glass need a dark ground.
$mbf_dark  = mbf_is_dark_ground( (string) ( $mbf_a['ground'] ?? '' ) );
$mbf_photo = $mbf_dark ? (int) $mbf_a['background_image'] : 0;
?>
<?php if ( $mbf_photos ) : ?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-hero', 'mbf-hero--solution' ] ); ?>>
	<div class="mbf-section__inner">
		<?php
		if ( $mbf_first ) {
			get_template_part( 'template-parts/crumbs', null, [ 'items' => mbf_crumbs() ] );
		}
		?>
		<div class="mbf-hero__grid">
			<?php get_template_part( 'template-parts/gallery', null, [ 'ids' => $mbf_photos, 'eager' => $mbf_first, 'label' => sprintf( /* translators: %s: hero heading. */ __( 'Photos of %s', 'mbf-blank' ), $mbf_a['heading'] ) ] ); ?>
			<div class="mbf-hero__text">
				<?php if ( '' !== (string) $mbf_a['eyebrow'] ) : ?>
					<p class="mbf-eyebrow"><?php echo esc_html( $mbf_a['eyebrow'] ); ?></p>
				<?php endif; ?>
				<<?php echo $mbf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="h1 mbf-hero__title" id="<?php echo esc_attr( $mbf_id ); ?>"><?php echo esc_html( $mbf_a['heading'] ); ?></<?php echo $mbf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php if ( '' !== (string) $mbf_a['lead'] ) : ?>
					<p class="lead"><?php echo esc_html( $mbf_a['lead'] ); ?></p>
				<?php endif; ?>
				<?php get_template_part( 'template-parts/badges', null, [ 'items' => array_map( static fn( $text ) => [ 'name' => $text ], $mbf_badges ) ] ); ?>
				<?php if ( $mbf_glance ) : ?>
					<dl class="mbf-glance">
						<?php foreach ( $mbf_glance as $mbf_row ) : ?>
							<div class="mbf-glance__row">
								<dt class="mbf-glance__label"><?php echo esc_html( $mbf_row['label'] ); ?></dt>
								<dd class="mbf-glance__value"><?php echo esc_html( $mbf_row['value'] ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
				<?php get_template_part( 'template-parts/cta-buttons', null, [ 'buttons' => [ $mbf_a['button_1'], $mbf_a['button_2'] ] ] ); ?>
			</div>
		</div>
	</div>
</section>
<?php else : ?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, array_filter( [ 'mbf-hero', ( $mbf_image || $mbf_finder ) ? 'mbf-hero--media' : '', $mbf_photo ? 'mbf-hero--photo' : '' ] ) ); ?>>
	<?php
	if ( $mbf_photo ) {
		// Decorative: the heading says what the section is. The first row is the LCP image.
		echo wp_get_attachment_image(
			$mbf_photo,
			'full',
			false,
			[ 'class' => 'mbf-hero__photo', 'alt' => '', 'sizes' => '100vw' ] + ( $mbf_first ? [ 'loading' => 'eager', 'fetchpriority' => 'high' ] : [] )
		);
	}
	?>
	<div class="mbf-section__inner">
		<div class="mbf-hero__grid">
			<div class="mbf-hero__text">
				<?php if ( '' !== (string) $mbf_a['eyebrow'] ) : ?>
					<p class="mbf-eyebrow"><?php echo esc_html( $mbf_a['eyebrow'] ); ?></p>
				<?php endif; ?>
				<<?php echo $mbf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="display" id="<?php echo esc_attr( $mbf_id ); ?>"><?php echo esc_html( $mbf_a['heading'] ); ?></<?php echo $mbf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php if ( '' !== (string) $mbf_a['lead'] ) : ?>
					<p class="lead"><?php echo esc_html( $mbf_a['lead'] ); ?></p>
				<?php endif; ?>
				<?php get_template_part( 'template-parts/cta-buttons', null, [ 'buttons' => [ $mbf_a['button_1'], $mbf_a['button_2'] ] ] ); ?>
				<?php get_template_part( 'template-parts/badges', null, [ 'items' => array_map( static fn( $text ) => [ 'name' => $text ], $mbf_badges ) ] ); ?>
			</div>
			<?php
			if ( $mbf_finder ) {
				get_template_part( 'template-parts/finder', null, [ 'context' => $mbf_dark ? 'glass' : 'panel', 'level' => $mbf_first ? 2 : 3 ] );
			} elseif ( $mbf_image ) {
				// Alt text comes from the Media Library (human-written, 04). The first row is the LCP image.
				echo wp_get_attachment_image(
					$mbf_image,
					'large',
					false,
					$mbf_first ? [ 'class' => 'mbf-hero__image', 'loading' => 'eager', 'fetchpriority' => 'high' ] : [ 'class' => 'mbf-hero__image' ]
				);
			}
			?>
		</div>
	</div>
</section>
<?php endif; ?>
