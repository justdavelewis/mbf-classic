<?php
/**
 * MBF MCP server: read-only abilities for an AI agent, local development only.
 *
 * A proof of concept (David, 2026-11-10: "This is just to make sure it works"). Needs the
 * MCP Adapter plugin (WordPress/mcp-adapter, installed per machine: see docs/STATUS.md) and
 * the Abilities API in core (WordPress 6.9+). Start it with scripts/mcp-serve.sh.
 *
 * Why a must-use plugin: deploys copy the theme only, so this file can't reach a live site,
 * and it also does nothing outside a local or development environment (unset means
 * production). Agent tooling never reaches live (David).
 *
 * Only the abilities named in the server below are exposed. The adapter's default server
 * is switched off: it serves every ability marked mcp.public, and WS Form marks 53 of its own
 * public, including reading submissions and deleting forms (measured 2026-11-10, 1.12.19).
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! in_array( wp_get_environment_type(), [ 'local', 'development' ], true ) ) {
	return;
}

add_filter( 'mcp_adapter_create_default_server', '__return_false' );

// Belt and braces: if the default server is ever switched back on, WS Form's abilities stay off it.
add_filter(
	'wsf_config_abilities',
	static function ( $abilities ) {
		foreach ( $abilities as $name => $ability ) {
			if ( isset( $ability['meta']['mcp'] ) ) {
				$abilities[ $name ]['meta']['mcp']['public'] = false;
			}
		}

		return $abilities;
	}
);

add_action(
	'wp_abilities_api_categories_init',
	static function (): void {
		wp_register_ability_category( 'mbf', [ 'label' => 'Made by Factory', 'description' => 'Read-only views of an MBF theme build.' ] );
	}
);

add_action(
	'wp_abilities_api_init',
	static function (): void {
		$read_only = [ 'annotations' => [ 'readonly' => true, 'destructive' => false, 'idempotent' => true ] ];

		wp_register_ability(
			'mbf/get-site-settings',
			[
				'label'               => 'Get Site Settings',
				'description'         => 'Every field in Site Settings (NAP, header button, forms, solution finder), by field name.',
				'category'            => 'mbf',
				'output_schema'       => [ 'type' => 'object' ],
				'permission_callback' => static fn() => current_user_can( 'manage_options' ),
				'execute_callback'    => static function () {
					if ( ! function_exists( 'get_field' ) ) {
						return new WP_Error( 'mbf_no_acf', 'ACF Pro is not active.' );
					}

					$group  = json_decode( (string) file_get_contents( get_theme_file_path( 'acf-json/group_mbf_site_settings.json' ) ), true );
					$values = [];

					foreach ( $group['fields'] ?? [] as $field ) {
						if ( '' !== ( $field['name'] ?? '' ) ) {
							$values[ $field['name'] ] = get_field( $field['name'], 'option' );
						}
					}

					return $values;
				},
				'meta'                => $read_only,
			]
		);

		wp_register_ability(
			'mbf/get-page-sections',
			[
				'label'               => 'Get page sections',
				'description'         => 'The sections on a page, in order: layout, ground, accent, spacing and heading.',
				'category'            => 'mbf',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [ 'path' => [ 'type' => 'string', 'description' => 'The page path, e.g. "products/placeholder-product". Empty for the home page.' ] ],
					'required'             => [ 'path' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [ 'type' => 'object' ],
				'permission_callback' => static fn() => current_user_can( 'edit_pages' ),
				'execute_callback'    => static function ( array $input ) {
					$path = trim( (string) $input['path'], '/' );
					$id   = '' === $path ? (int) get_option( 'page_on_front' ) : (int) ( get_page_by_path( $path )->ID ?? 0 );

					if ( ! $id ) {
						return new WP_Error( 'mbf_no_page', "No page at \"{$path}\"." );
					}

					$sections = [];

					if ( ! function_exists( 'get_field' ) ) {
						return new WP_Error( 'mbf_no_acf', 'ACF Pro is not active.' );
					}

					// get_field() returns false for a page with no sections, and (array) false is [ false ].
					$rows = get_field( 'page_layouts', $id );

					foreach ( is_array( $rows ) ? $rows : [] as $row ) {
						$sections[] = [
							'layout'  => (string) ( $row['acf_fc_layout'] ?? '' ),
							'ground'  => (string) ( $row['ground'] ?? 'base' ),
							'accent'  => (string) ( $row['accent'] ?? 'default' ),
							'spacing' => (string) ( $row['spacing'] ?? 'default' ),
							'heading' => (string) ( $row['heading'] ?? '' ),
						];
					}

					return [ 'id' => $id, 'title' => get_the_title( $id ), 'url' => get_permalink( $id ), 'sections' => $sections ];
				},
				'meta'                => $read_only,
			]
		);

		wp_register_ability(
			'mbf/check-contrast',
			[
				'label'               => 'Check palette contrast',
				'description'         => 'Every colour pair the theme measures (mbf_contrast_pairs) against the current palette, with its ratio and whether it passes.',
				'category'            => 'mbf',
				'output_schema'       => [ 'type' => 'object' ],
				'permission_callback' => static fn() => current_user_can( 'manage_options' ),
				'execute_callback'    => static function () {
					if ( ! function_exists( 'mbf_contrast_pairs' ) ) {
						return new WP_Error( 'mbf_no_theme', 'The mbf-blank theme (or a fork) is not active.' );
					}

					$palette = mbf_resolved_palette( mbf_saved_colours() );
					$pairs   = [];

					foreach ( mbf_contrast_pairs( $palette ) as [ $label, $fg, $bg, $min ] ) {
						$ratio   = round( mbf_contrast( $fg, $bg ), 2 );
						$pairs[] = [ 'pair' => $label, 'foreground' => $fg, 'background' => $bg, 'ratio' => $ratio, 'needs' => $min, 'passes' => $ratio >= $min ];
					}

					return [ 'palette' => $palette, 'failures' => count( array_filter( $pairs, static fn( $p ) => ! $p['passes'] ) ), 'pairs' => $pairs ];
				},
				'meta'                => $read_only,
			]
		);
	}
);

add_action(
	'mcp_adapter_init',
	static function ( $adapter ): void {
		$adapter->create_server(
			'mbf',                 // Server ID, for `wp mcp-adapter serve --server=mbf`.
			'mbf',                 // REST namespace (unused: no HTTP transport).
			'mcp',                 // REST route (unused).
			'MBF local',
			'Read-only views of this MBF build. Local development only.',
			'0.1.0',
			[],                    // No HTTP transport: STDIO over WP-CLI only, so nothing is reachable over the web.
			\WP\MCP\Infrastructure\ErrorHandling\ErrorLogMcpErrorHandler::class,
			\WP\MCP\Infrastructure\Observability\NullMcpObservabilityHandler::class,
			[ 'mbf/get-site-settings', 'mbf/get-page-sections', 'mbf/check-contrast' ]
		);
	}
);
