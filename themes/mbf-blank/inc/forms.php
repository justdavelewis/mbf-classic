<?php
/**
 * WS Form. Outside production, only on-site actions run, so a local or staging submission
 * can never email a client or fire a webhook (05). Unset WP_ENVIRONMENT_TYPE means
 * production: set it on every non-production server.
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Filter for each WS Form action before it runs (ws-form-pro: class-ws-form-action.php).
 * An allow-list, not a block-list: a newly added action type is off outside production
 * until it's listed here.
 *
 * @param bool  $do     Whether the action runs.
 * @param mixed $form   Unused.
 * @param mixed $submit Unused.
 * @param mixed $row_id Unused.
 * @param mixed $db     Unused.
 * @param array $config The action; `id` is its type (database, email, webhook…).
 */
function mbf_gate_form_actions( bool $do, mixed $form, mixed $submit, mixed $row_id, mixed $db, mixed $config ): bool {
	if ( 'production' === wp_get_environment_type() ) {
		return $do;
	}

	return $do && in_array( $config['id'] ?? '', [ 'database', 'message', 'redirect' ], true );
}
add_filter( 'wsf_action_post_do', 'mbf_gate_form_actions', 10, 6 );
