<?php
/**
 * The enquiry form: a WS Form, styled through the plugin's own variables (02 §Plugin CSS,
 * src/vendor/wsform.css). Outbound actions are gated off outside production (inc/forms.php).
 *
 * $args: form_id (WS Form ID; Site Settings `form_enquiry`).
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_form_id = (int) ( $args['form_id'] ?? 0 );

if ( ! $mbf_form_id || ! shortcode_exists( 'ws_form' ) ) {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<!-- mbf: enquiry form needs WS Form active and Site Settings › Forms › Enquiry form ID -->';
	}
	return;
}
?>
<div class="mbf-form">
	<?php echo do_shortcode( sprintf( '[ws_form id="%d"]', $mbf_form_id ) ); ?>
</div>
