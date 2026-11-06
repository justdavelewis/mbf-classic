<?php
/**
 * Placeholder pages, posts, the Primary menu and Site Settings NAP for the mbf-blank
 * starter, so the header, drawer, dropdowns, footer, prose, blog index and pagination have
 * something real to render. A fork deletes or replaces this content; it isn't a client
 * site's content. Site Settings fills empty fields only, so it never overwrites real ones.
 *
 * Idempotent: pages and posts match by slug, the menu by name and its items by target.
 * A second run is a no-op.
 *
 *   wp eval-file scripts/build-placeholder-content.php          # dry run (default)
 *   wp eval-file scripts/build-placeholder-content.php force    # write
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$mbf_force = in_array( 'force', $args ?? [], true );
$mbf_log   = static fn( string $msg ) => WP_CLI::log( ( $mbf_force ? '' : '[dry] ' ) . $msg );

$mbf_prose = <<<HTML
<p>This is placeholder copy for the starter theme. It exists so every text style can be checked on a real page, including <a href="/">a link in running text</a> and <strong>bold text</strong>.</p>
<h2>A second-level heading</h2>
<p>Paragraphs, lists and quotes all read the section's colour family, so the same content works on any ground.</p>
<ul><li>A first list item</li><li>A second list item, long enough to wrap onto another line at narrow widths so the marker alignment shows</li><li>A third list item</li></ul>
<h3>A third-level heading</h3>
<ol><li>Step one</li><li>Step two</li></ol>
<blockquote><p>A short quotation, to check the quote border and text colour.</p></blockquote>
HTML;

/**
 * Ensure a post exists by slug (and parent). Returns its ID, or 0 in a dry run when it
 * would be created.
 */
$mbf_ensure = static function ( string $type, string $slug, string $title, string $content, int $parent = 0 ) use ( $mbf_force, $mbf_log ): int {
	$existing = get_posts(
		[
			'post_type'      => $type,
			'name'           => $slug,
			'post_parent'    => $parent,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		]
	);

	if ( $existing ) {
		return (int) $existing[0];
	}

	$mbf_log( "create {$type} \"{$title}\" ({$slug})" );

	if ( ! $mbf_force ) {
		return 0;
	}

	return (int) wp_insert_post(
		[
			'post_type'    => $type,
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_content' => $content,
			'post_parent'  => $parent,
			'post_status'  => 'publish',
		],
		true
	);
};

// --- Pages ---------------------------------------------------------------------------
$mbf_about    = $mbf_ensure( 'page', 'about', 'About', $mbf_prose );
$mbf_services = $mbf_ensure( 'page', 'services', 'Services', '<p>Placeholder overview of the services.</p>' );
$mbf_svc_a    = $mbf_services ? $mbf_ensure( 'page', 'inspection', 'Inspection', '<p>Placeholder service page.</p>', $mbf_services ) : 0;
$mbf_svc_b    = $mbf_services ? $mbf_ensure( 'page', 'maintenance', 'Maintenance', '<p>Placeholder service page.</p>', $mbf_services ) : 0;
$mbf_contact  = $mbf_ensure( 'page', 'contact', 'Contact', '<p>Placeholder contact page.</p>' );

if ( ! $mbf_services ) {
	$mbf_log( 'create the two Services child pages once Services exists' );
}

// --- Posts: 12, so the blog index paginates at the default 10 per page ----------------
for ( $mbf_i = 1; $mbf_i <= 12; $mbf_i++ ) {
	$mbf_ensure( 'post', "placeholder-post-{$mbf_i}", "Placeholder post {$mbf_i}", '<p>Placeholder post content.</p>' );
}

// --- Primary menu --------------------------------------------------------------------
$mbf_menu = wp_get_nav_menu_object( 'Primary' );

if ( ! $mbf_menu ) {
	$mbf_log( 'create menu "Primary"' );
	$mbf_menu_id = $mbf_force ? (int) wp_create_nav_menu( 'Primary' ) : 0;
} else {
	$mbf_menu_id = (int) $mbf_menu->term_id;
}

$mbf_existing = $mbf_menu_id ? ( wp_get_nav_menu_items( $mbf_menu_id ) ?: [] ) : [];

$mbf_item = static function ( int $page_id, int $parent_item = 0 ) use ( $mbf_menu_id, $mbf_existing, $mbf_force, $mbf_log ): int {
	if ( ! $page_id ) {
		return 0;
	}

	foreach ( $mbf_existing as $item ) {
		if ( 'post_type' === $item->type && (int) $item->object_id === $page_id ) {
			return (int) $item->ID;
		}
	}

	$mbf_log( 'add menu item: ' . get_the_title( $page_id ) );

	if ( ! $mbf_force || ! $mbf_menu_id ) {
		return 0;
	}

	return (int) wp_update_nav_menu_item(
		$mbf_menu_id,
		0,
		[
			'menu-item-object-id' => $page_id,
			'menu-item-object'    => 'page',
			'menu-item-type'      => 'post_type',
			'menu-item-parent-id' => $parent_item,
			'menu-item-status'    => 'publish',
		]
	);
};

$mbf_item( $mbf_about );
$mbf_services_item = $mbf_item( $mbf_services );
$mbf_item( $mbf_svc_a, $mbf_services_item );
$mbf_item( $mbf_svc_b, $mbf_services_item );
$mbf_item( $mbf_contact );

$mbf_locations = get_nav_menu_locations();

if ( $mbf_menu_id && ( $mbf_locations['primary'] ?? 0 ) !== $mbf_menu_id ) {
	$mbf_log( 'assign "Primary" to the primary location' );

	if ( $mbf_force ) {
		$mbf_locations['primary'] = $mbf_menu_id;
		set_theme_mod( 'nav_menu_locations', $mbf_locations );
	}
} elseif ( ! $mbf_menu_id ) {
	$mbf_log( 'assign "Primary" to the primary location' );
}

// --- Site Settings: placeholder NAP (never a real business). Fills empty fields only. ---
if ( ! function_exists( 'update_field' ) ) {
	WP_CLI::error( 'ACF Pro is not active: Site Settings need it.' );
}

// Keys come from acf-json, never typed by hand: a wrong key writes an orphan row and "succeeds" (05).
$mbf_group = json_decode( (string) file_get_contents( get_theme_file_path( 'acf-json/group_mbf_site_settings.json' ) ), true );
$mbf_keys  = array_column( $mbf_group['fields'] ?? [], 'key', 'name' );

$mbf_settings = [
	'company_name' => 'Placeholder Company Ltd',
	'phone'        => '01234 567890',
	'email'        => 'hello@example.com',
	'address'      => "1 Placeholder Street\nPlaceholder Town\nAB1 2CD",
	'legal'        => 'Registered in England and Wales. Company no. 00000000.',
];

foreach ( $mbf_settings as $mbf_name => $mbf_value ) {
	if ( empty( $mbf_keys[ $mbf_name ] ) ) {
		WP_CLI::error( "No field named \"{$mbf_name}\" in acf-json/group_mbf_site_settings.json." );
	}

	if ( '' !== (string) get_field( $mbf_keys[ $mbf_name ], 'option' ) ) {
		continue;
	}

	$mbf_log( "set Site Settings {$mbf_name}" );

	if ( $mbf_force ) {
		update_field( $mbf_keys[ $mbf_name ], $mbf_value, 'option' );
	}
}

WP_CLI::success( $mbf_force ? 'Placeholder content in place.' : 'Dry run complete. Re-run with "force" to write.' );
