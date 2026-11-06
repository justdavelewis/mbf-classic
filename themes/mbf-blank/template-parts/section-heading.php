<?php
/**
 * Section heading: optional heading + lead. The only way a section heading is emitted (03).
 *
 * $args: eyebrow, heading, lead, link (ACF link), id (the section's aria-labelledby target),
 * level (2), size ('h2').
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a = wp_parse_args( $args, [ 'eyebrow' => '', 'heading' => '', 'lead' => '', 'link' => null, 'id' => '', 'level' => 2, 'size' => 'h2' ] );
$mbf_link = mbf_link( $mbf_a['link'] );

if ( '' === (string) $mbf_a['heading'] && '' === (string) $mbf_a['lead'] && '' === (string) $mbf_a['eyebrow'] && ! $mbf_link ) {
	return;
}

$mbf_tag = 'h' . max( 1, min( 6, (int) $mbf_a['level'] ) );
?>
<header class="mbf-section-heading">
	<?php if ( '' !== (string) $mbf_a['eyebrow'] ) : ?>
		<p class="mbf-eyebrow"><?php echo esc_html( $mbf_a['eyebrow'] ); ?></p>
	<?php endif; ?>
	<?php if ( '' !== (string) $mbf_a['heading'] ) : ?>
		<<?php echo $mbf_tag; // phpcs:ignore WordPress.Security.EscapeOutput -- h1–h6 built above ?> class="<?php echo esc_attr( $mbf_a['size'] ); ?>"<?php echo $mbf_a['id'] ? ' id="' . esc_attr( $mbf_a['id'] ) . '"' : ''; ?>><?php echo esc_html( $mbf_a['heading'] ); ?></<?php echo $mbf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<?php endif; ?>
	<?php if ( '' !== (string) $mbf_a['lead'] ) : ?>
		<p class="lead mbf-section-heading__lead"><?php echo esc_html( $mbf_a['lead'] ); ?></p>
	<?php endif; ?>
	<?php if ( $mbf_link ) : ?>
		<a class="mbf-section-heading__link" href="<?php echo esc_url( $mbf_link['url'] ); ?>"<?php echo $mbf_link['target'] ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( $mbf_link['label'] ); ?> <span aria-hidden="true">&rarr;</span></a>
	<?php endif; ?>
</header>
