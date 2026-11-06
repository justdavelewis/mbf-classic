<?php
/**
 * Breadcrumbs. Hidden when the trail is only "Home".
 *
 * $args: items ([ [ label, url ], … ] from mbf_crumbs(); the last is the current page).
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_items = mbf_rows( $args['items'] ?? [] );

if ( count( $mbf_items ) < 2 ) {
	return;
}

$mbf_last = count( $mbf_items ) - 1;
?>
<nav class="mbf-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'mbf-blank' ); ?>">
	<ol class="mbf-crumbs__list" role="list">
		<?php foreach ( $mbf_items as $mbf_i => $mbf_item ) : ?>
			<li class="mbf-crumbs__item">
				<?php if ( $mbf_i === $mbf_last || '' === $mbf_item['url'] ) : ?>
					<span aria-current="page"><?php echo esc_html( $mbf_item['label'] ); ?></span>
				<?php else : ?>
					<a class="mbf-crumbs__link" href="<?php echo esc_url( $mbf_item['url'] ); ?>"><?php echo esc_html( $mbf_item['label'] ); ?></a>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
</nav>
