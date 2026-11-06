<?php
/**
 * A ticked list. The tick is CSS with empty alt text: the list itself is what's announced.
 *
 * $args: items (rows: text).
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_items = array_filter( array_map( static fn( $row ) => (string) ( $row['text'] ?? '' ), mbf_rows( $args['items'] ?? [] ) ), 'strlen' );

if ( ! $mbf_items ) {
	return;
}
?>
<ul class="mbf-ticks" role="list">
	<?php foreach ( $mbf_items as $mbf_item ) : ?>
		<li class="mbf-ticks__item"><?php echo esc_html( $mbf_item ); ?></li>
	<?php endforeach; ?>
</ul>
