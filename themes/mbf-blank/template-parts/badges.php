<?php
/**
 * Badges: proof marks such as a standard or an accreditation. With a logo the name becomes
 * its alt text, so the name is always what's announced.
 *
 * $args: items (rows: name, logo attachment ID).
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_items = array_filter( mbf_rows( $args['items'] ?? [] ), static fn( $row ) => '' !== (string) ( $row['name'] ?? '' ) );

if ( ! $mbf_items ) {
	return;
}
?>
<ul class="mbf-badges" role="list">
	<?php foreach ( $mbf_items as $mbf_item ) : ?>
		<?php if ( ! empty( $mbf_item['logo'] ) ) : ?>
			<li class="mbf-badge mbf-badge--logo"><?php echo wp_get_attachment_image( (int) $mbf_item['logo'], 'medium', false, [ 'alt' => (string) $mbf_item['name'], 'class' => 'mbf-badge__logo' ] ); ?></li>
		<?php else : ?>
			<li class="mbf-badge"><?php echo esc_html( $mbf_item['name'] ); ?></li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>
