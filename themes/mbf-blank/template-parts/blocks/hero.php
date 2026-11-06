<?php
/**
 * Hero: the page's opening statement. It owns the page's h1 only when it's the first row
 * (page.php then skips the default page header). Anywhere else it's an h2, so a page never
 * gets two h1s.
 *
 * $args: heading, lead, button_1, button_2, image + section controls, position.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a = wp_parse_args( $args, [ 'heading' => '', 'lead' => '', 'button_1' => null, 'button_2' => null, 'image' => 0, 'position' => 1 ] );

if ( '' === (string) $mbf_a['heading'] ) {
	return;
}

$mbf_id    = mbf_section_id( $mbf_a['heading'] );
$mbf_first = 1 === (int) $mbf_a['position'];
$mbf_tag   = $mbf_first ? 'h1' : 'h2';
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, array_filter( [ 'mbf-hero', $mbf_a['image'] ? 'mbf-hero--media' : '' ] ) ); ?>>
	<div class="mbf-section__inner">
		<div class="mbf-hero__grid">
			<div class="mbf-hero__text">
				<<?php echo $mbf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="display" id="<?php echo esc_attr( $mbf_id ); ?>"><?php echo esc_html( $mbf_a['heading'] ); ?></<?php echo $mbf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php if ( '' !== (string) $mbf_a['lead'] ) : ?>
					<p class="lead"><?php echo esc_html( $mbf_a['lead'] ); ?></p>
				<?php endif; ?>
				<?php get_template_part( 'template-parts/cta-buttons', null, [ 'buttons' => [ $mbf_a['button_1'], $mbf_a['button_2'] ] ] ); ?>
			</div>
			<?php
			if ( $mbf_a['image'] ) {
				// Alt text comes from the Media Library (human-written, 04). The first row is the LCP image.
				echo wp_get_attachment_image(
					(int) $mbf_a['image'],
					'large',
					false,
					$mbf_first ? [ 'class' => 'mbf-hero__image', 'loading' => 'eager', 'fetchpriority' => 'high' ] : [ 'class' => 'mbf-hero__image' ]
				);
			}
			?>
		</div>
	</div>
</section>
