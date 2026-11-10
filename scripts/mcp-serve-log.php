<?php
/**
 * Lifecycle log for scripts/mcp-serve.sh, loaded with WP-CLI's --require.
 *
 * Why: on 2026-11-10 Claude Code sessions lost the mbf-local server with no close recorded on
 * either side, although the server survives 6 minutes idle. This records each start, and how it
 * ended: "stopped" (the client closed the connection), a fatal error, or nothing after "start",
 * which means the process was killed.
 *
 * No signal handlers, deliberately. A shell wrapper around a background `wp` hung on SIGINT
 * (how Claude Code stops a server), and pcntl handlers made PHP ignore SIGINT and SIGTERM
 * altogether while waiting for input (measured, with and without restarting the read).
 *
 * Log: $TMPDIR/mbf-mcp-serve.log (times in UTC). Local development only, like the server.
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

$mbf_mcp_note = static function ( string $message ): void {
	$file = rtrim( (string) ( getenv( 'TMPDIR' ) ?: sys_get_temp_dir() ), '/' ) . '/mbf-mcp-serve.log';
	file_put_contents( $file, gmdate( 'Y-m-d H:i:s' ) . ' [' . getmypid() . "] {$message}\n", FILE_APPEND ); // phpcs:ignore WordPress.WP.AlternativeFunctions -- WordPress isn't loaded yet.
};

$mbf_mcp_parent = function_exists( 'posix_getppid' ) ? posix_getppid() : 0;
$mbf_mcp_note( 'start, parent ' . $mbf_mcp_parent . ': ' . trim( (string) shell_exec( 'ps -o command= -p ' . (int) $mbf_mcp_parent ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions -- local diagnostics.

register_shutdown_function(
	static function () use ( $mbf_mcp_note ): void {
		$error = error_get_last();
		$fatal = $error && in_array( $error['type'], [ E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR ], true );

		$mbf_mcp_note( $fatal ? "stopped: fatal error {$error['message']} ({$error['file']}:{$error['line']})" : 'stopped' );
	}
);

