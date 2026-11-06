<?php
/**
 * Site footer: brand + socials, NAP from Site Settings, then the legal row. Reuses the
 * secondary ground's measured family (.mbf-section--secondary) rather than its own colours.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_ground  = 'secondary';
$mbf_name    = mbf_company_name();
$mbf_phone   = (string) mbf_setting( 'phone' );
$mbf_email   = (string) mbf_setting( 'email' );
$mbf_address = (string) mbf_setting( 'address' );
$mbf_legal   = (string) mbf_setting( 'legal' );
?>
</main>

<footer class="mbf-footer mbf-section mbf-section--<?php echo esc_attr( $mbf_ground ); ?>">
	<div class="mbf-section__inner">
		<div class="mbf-footer__grid">
			<div class="mbf-footer__brand">
				<?php
				$mbf_logo = mbf_logo( $mbf_ground, 'mbf-footer__logo' );
				echo $mbf_logo ?: '<p class="mbf-footer__brand-name">' . esc_html( $mbf_name ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image()

				get_template_part( 'template-parts/social-links', null, [ 'links' => mbf_setting( 'socials' ), 'icons' => mbf_has_icons() ] );
				?>
			</div>

			<?php if ( $mbf_phone || $mbf_email || $mbf_address ) : ?>
				<address class="mbf-footer__contact">
					<?php if ( $mbf_address ) : ?>
						<span class="mbf-footer__address"><?php echo esc_html( $mbf_address ); ?></span>
					<?php endif; ?>
					<?php if ( $mbf_phone ) : ?>
						<a class="mbf-footer__link" href="<?php echo esc_url( mbf_tel_href( $mbf_phone ) ); ?>"><?php echo esc_html( $mbf_phone ); ?></a>
					<?php endif; ?>
					<?php if ( $mbf_email ) : ?>
						<a class="mbf-footer__link" href="<?php echo esc_url( 'mailto:' . $mbf_email ); ?>"><?php echo esc_html( $mbf_email ); ?></a>
					<?php endif; ?>
				</address>
			<?php endif; ?>
		</div>

		<div class="mbf-footer__legal">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) . ' ' . $mbf_name ); ?></p>
			<?php if ( $mbf_legal ) : ?>
				<p><?php echo esc_html( $mbf_legal ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
