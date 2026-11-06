<?php
/**
 * Social profile links. With Font Awesome loaded (Site Settings › Icons), each is its
 * Brands mark plus an sr-only name; without it, or for an unmapped network, the name is
 * visible text. Either way the target is 44px (04: 24px minimum).
 *
 * $args: links (Site Settings `socials` rows: network, url), icons (bool: FA is loaded).
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_links = array_filter( mbf_rows( $args['links'] ?? [] ), static fn( $row ) => ! empty( $row['url'] ) );

if ( ! $mbf_links ) {
	return;
}

$mbf_names = [
	'facebook'  => 'Facebook',
	'instagram' => 'Instagram',
	'linkedin'  => 'LinkedIn',
	'x'         => 'X',
	'youtube'   => 'YouTube',
	'tiktok'    => 'TikTok',
];
?>
<ul class="mbf-social" role="list">
	<?php foreach ( $mbf_links as $mbf_link ) : ?>
		<?php
		$mbf_network = (string) ( $mbf_link['network'] ?? '' );
		$mbf_name    = $mbf_names[ $mbf_network ] ?? ucfirst( $mbf_network );
		$mbf_icon    = ! empty( $args['icons'] ) ? mbf_social_icon( $mbf_network ) : '';
		?>
		<li>
			<a class="mbf-social__link<?php echo $mbf_icon ? ' mbf-social__link--icon' : ''; ?>" href="<?php echo esc_url( $mbf_link['url'] ); ?>" rel="me noopener">
				<?php if ( $mbf_icon ) : ?>
					<i class="<?php echo esc_attr( $mbf_icon ); ?> mbf-social__icon" aria-hidden="true"></i>
					<span class="sr-only"><?php echo esc_html( $mbf_name ); ?></span>
				<?php else : ?>
					<?php echo esc_html( $mbf_name ); ?>
				<?php endif; ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
