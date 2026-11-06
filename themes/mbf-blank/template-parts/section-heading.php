<?php
/**
 * Section heading: optional heading + lead. The only way a section heading is emitted (03).
 *
 * $args: heading, lead, id (the section's aria-labelledby target), level (2), size ('h2').
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a = wp_parse_args( $args, [ 'heading' => '', 'lead' => '', 'id' => '', 'level' => 2, 'size' => 'h2' ] );

if ( '' === $mbf_a['heading'] && '' === $mbf_a['lead'] ) {
	return;
}

$mbf_tag = 'h' . max( 1, min( 6, (int) $mbf_a['level'] ) );
?>
<header class="mbf-section-heading">
	<?php if ( '' !== $mbf_a['heading'] ) : ?>
		<<?php echo $mbf_tag; // phpcs:ignore WordPress.Security.EscapeOutput -- h1–h6 built above ?> class="<?php echo esc_attr( $mbf_a['size'] ); ?>"<?php echo $mbf_a['id'] ? ' id="' . esc_attr( $mbf_a['id'] ) . '"' : ''; ?>><?php echo esc_html( $mbf_a['heading'] ); ?></<?php echo $mbf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<?php endif; ?>
	<?php if ( '' !== $mbf_a['lead'] ) : ?>
		<p class="lead mbf-section-heading__lead"><?php echo esc_html( $mbf_a['lead'] ); ?></p>
	<?php endif; ?>
</header>
