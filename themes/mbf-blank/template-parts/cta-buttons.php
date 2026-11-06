<?php
/**
 * Buttons. The only way buttons are emitted (03): the first is the filled button, any
 * second is the outline variant. Both read the section's guarded accent, so they pass on
 * whatever ground they sit on.
 *
 * $args: buttons (ACF link arrays), phone_fallback (bool: with fewer than two buttons, add
 * "Call {phone}" from Site Settings, as the CTA band's second button does).
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a       = wp_parse_args( $args, [ 'buttons' => [], 'phone_fallback' => false ] );
$mbf_buttons = array_values( array_filter( array_map( 'mbf_link', mbf_rows( $mbf_a['buttons'] ) ) ) );
$mbf_phone   = $mbf_a['phone_fallback'] ? (string) mbf_setting( 'phone' ) : '';

if ( count( $mbf_buttons ) < 2 && '' !== $mbf_phone ) {
	$mbf_buttons[] = [
		'url'    => mbf_tel_href( $mbf_phone ),
		/* translators: %s: phone number. */
		'label'  => sprintf( __( 'Call %s', 'mbf-blank' ), $mbf_phone ),
		'target' => '',
	];
}

if ( ! $mbf_buttons ) {
	return;
}
?>
<div class="mbf-buttons">
	<?php foreach ( $mbf_buttons as $mbf_i => $mbf_button ) : ?>
		<a
			class="mbf-button<?php echo $mbf_i ? ' mbf-button--secondary' : ''; ?>"
			href="<?php echo esc_url( $mbf_button['url'] ); ?>"
			<?php if ( $mbf_button['target'] ) : ?>target="_blank" rel="noopener"<?php endif; ?>
		>
			<?php echo esc_html( $mbf_button['label'] ); ?>
			<?php if ( $mbf_button['target'] ) : ?>
				<span class="sr-only"><?php esc_html_e( '(opens in a new tab)', 'mbf-blank' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endforeach; ?>
</div>
