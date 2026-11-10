<?php
/**
 * Enquiry form section: heading + text beside the Site Settings enquiry form.
 *
 * $args: heading, text + section controls. The form ID is site-wide (Site Settings).
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a  = wp_parse_args( $args, [ 'heading' => '', 'text' => '', 'points' => [], 'show_contact' => false ] );
$mbf_id    = '' !== (string) $mbf_a['heading'] ? mbf_section_id( $mbf_a['heading'] ) : '';
$mbf_phone = (string) mbf_setting( 'phone' );
$mbf_email = (string) mbf_setting( 'email' );
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-enquiry' ] ); ?>>
	<div class="mbf-section__inner">
		<div class="mbf-enquiry__grid">
			<div class="mbf-enquiry__intro">
				<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_a['heading'], 'lead' => $mbf_a['text'], 'id' => $mbf_id ] ); ?>
				<?php get_template_part( 'template-parts/ticks', null, [ 'items' => $mbf_a['points'] ] ); ?>
				<?php if ( $mbf_a['show_contact'] && ( $mbf_phone || $mbf_email ) ) : ?>
					<ul class="mbf-enquiry__contact" role="list">
						<?php if ( $mbf_phone ) : ?>
							<li><a href="<?php echo esc_url( mbf_tel_href( $mbf_phone ) ); ?>"><?php echo esc_html( $mbf_phone ); ?></a></li>
						<?php endif; ?>
						<?php if ( $mbf_email ) : ?>
							<li><a href="<?php echo esc_url( 'mailto:' . antispambot( $mbf_email ) ); ?>"><?php echo esc_html( antispambot( $mbf_email ) ); ?></a></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>
			<?php get_template_part( 'template-parts/enquiry-form', null, [ 'form_id' => (int) mbf_setting( 'form_enquiry' ) ] ); ?>
		</div>
	</div>
</section>
