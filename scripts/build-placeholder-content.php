<?php
/**
 * Placeholder pages, posts, the Primary and Footer menus and Site Settings (NAP, header
 * button, solution finder) for the mbf-blank starter, so the header, drawer, dropdowns, footer, prose, blog
 * index and pagination have something real to render. A fork deletes or replaces this content; it isn't a client
 * site's content. Site Settings fills empty fields only, so it never overwrites real ones.
 *
 * Idempotent: pages and posts match by slug, menus by name and their items by target.
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

// One of each page type; build-page-types.php writes their sections.
$mbf_ensure( 'page', 'home', 'Home', '' );
$mbf_products   = $mbf_ensure( 'page', 'products', 'Products', '' );
$mbf_product    = $mbf_products ? $mbf_ensure( 'page', 'placeholder-product', 'Placeholder product', '', $mbf_products ) : 0;
$mbf_industries = $mbf_ensure( 'page', 'industries', 'Industries', '' );
$mbf_sector     = $mbf_industries ? $mbf_ensure( 'page', 'placeholder-sector', 'Placeholder sector', '', $mbf_industries ) : 0;
$mbf_ensure( 'page', 'case-studies', 'Case studies', '' );
$mbf_ensure( 'page', 'faqs', 'FAQs', '' );
$mbf_ensure( 'page', 'blog', 'Blog', '' );

if ( ! $mbf_services ) {
	$mbf_log( 'create the two Services child pages once Services exists' );
}

// --- Posts: 12, so the blog index paginates at the default 10 per page ----------------
for ( $mbf_i = 1; $mbf_i <= 12; $mbf_i++ ) {
	$mbf_ensure( 'post', "placeholder-post-{$mbf_i}", "Placeholder post {$mbf_i}", '<p>Placeholder post content.</p>' );
}

// --- Legal pages for the Footer menu ------------------------------------------------------
// WordPress ships "Privacy Policy" as a draft; a menu item to a draft never renders, so publish it.
$mbf_privacy = $mbf_ensure( 'page', 'privacy-policy', 'Privacy Policy', '<p>Placeholder privacy policy.</p>' );
$mbf_terms   = $mbf_ensure( 'page', 'terms', 'Terms', '<p>Placeholder terms.</p>' );

if ( $mbf_privacy && 'publish' !== get_post_status( $mbf_privacy ) ) {
	$mbf_log( 'publish page "Privacy Policy"' );

	if ( $mbf_force ) {
		wp_update_post( [ 'ID' => $mbf_privacy, 'post_status' => 'publish' ] );
	}
}

/**
 * Ensure a named menu holds these pages (matched by target, so re-runs add nothing) and is
 * assigned to its location. Items: [ page_id, parent_page_id ].
 */
$mbf_menu = static function ( string $name, string $location, array $items ) use ( $mbf_force, $mbf_log ): void {
	$menu    = wp_get_nav_menu_object( $name );
	$menu_id = $menu ? (int) $menu->term_id : 0;

	if ( ! $menu ) {
		$mbf_log( "create menu \"{$name}\"" );
		$menu_id = $mbf_force ? (int) wp_create_nav_menu( $name ) : 0;
	}

	// Raw term objects, not wp_get_nav_menu_items(): that can drop items whose page isn't
	// published, and a re-run would then add them again (no longer idempotent).
	$existing = $menu_id ? get_objects_in_term( $menu_id, 'nav_menu' ) : [];
	$by_page  = [];

	foreach ( is_array( $existing ) ? $existing : [] as $item_id ) {
		if ( 'post_type' === get_post_meta( (int) $item_id, '_menu_item_type', true ) ) {
			$by_page[ (int) get_post_meta( (int) $item_id, '_menu_item_object_id', true ) ] = (int) $item_id;
		}
	}

	foreach ( $items as [ $page_id, $parent_page ] ) {
		if ( ! $page_id || isset( $by_page[ $page_id ] ) ) {
			continue;
		}

		$mbf_log( "add \"{$name}\" menu item: " . get_the_title( $page_id ) );

		if ( $mbf_force && $menu_id ) {
			$by_page[ $page_id ] = (int) wp_update_nav_menu_item(
				$menu_id,
				0,
				[
					'menu-item-object-id' => $page_id,
					'menu-item-object'    => 'page',
					'menu-item-type'      => 'post_type',
					'menu-item-parent-id' => $parent_page ? ( $by_page[ $parent_page ] ?? 0 ) : 0,
					'menu-item-status'    => 'publish',
				]
			);
		}
	}

	// The order given here, not the order items happened to be added across runs.
	$position = 0;

	foreach ( $items as [ $page_id ] ) {
		++$position;
		$item_id = $by_page[ $page_id ] ?? 0;

		if ( $item_id && (int) get_post_field( 'menu_order', $item_id ) !== $position ) {
			$mbf_log( "move \"{$name}\" menu item to position {$position}: " . get_the_title( $page_id ) );

			if ( $mbf_force ) {
				wp_update_post( [ 'ID' => $item_id, 'menu_order' => $position ] );
			}
		}
	}

	$locations = get_nav_menu_locations();

	if ( ! $menu_id || ( $locations[ $location ] ?? 0 ) !== $menu_id ) {
		$mbf_log( "assign \"{$name}\" to the {$location} location" );

		if ( $mbf_force && $menu_id ) {
			$locations[ $location ] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}
};

$mbf_menu(
	'Primary',
	'primary',
	[ [ $mbf_about, 0 ], [ $mbf_products, 0 ], [ $mbf_product, $mbf_products ], [ $mbf_services, 0 ], [ $mbf_svc_a, $mbf_services ], [ $mbf_svc_b, $mbf_services ], [ $mbf_industries, 0 ], [ $mbf_sector, $mbf_industries ], [ $mbf_contact, 0 ] ]
);
$mbf_menu( 'Footer', 'footer', [ [ $mbf_privacy, 0 ], [ $mbf_terms, 0 ] ] );

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
	'header_cta'   => $mbf_contact ? [ 'title' => 'Get in touch', 'url' => (string) get_permalink( $mbf_contact ), 'target' => '' ] : null,

	// The finder: sector wording, because every client so far works at height, but no client's.
	'finder_eyebrow'    => 'Find your solution',
	'finder_question'   => "Where's the risk on your site?",
	'finder_options'    => $mbf_contact ? [
		[ 'key' => 'roof-edge', 'label' => 'Roof edge or perimeter', 'icon' => 'fa-house-chimney', 'page' => $mbf_svc_a, 'title' => 'Placeholder roof edge system', 'points' => [ [ 'text' => 'A first reason it suits' ], [ 'text' => 'A second reason it suits' ] ] ],
		[ 'key' => 'fragile-roof', 'label' => 'Fragile roof or skylights', 'icon' => 'fa-table-cells-large', 'page' => $mbf_svc_b, 'title' => 'Placeholder fragile roof system', 'points' => [ [ 'text' => 'A first reason it suits' ] ] ],
		[ 'key' => 'access', 'label' => 'Ladders and access points', 'icon' => 'fa-stairs', 'page' => $mbf_services, 'title' => '', 'points' => [] ],
		[ 'key' => 'not-sure', 'label' => 'Not sure yet', 'icon' => 'fa-circle-question', 'page' => '', 'title' => '', 'points' => [] ],
	] : null,
	'finder_question_2' => 'What kind of site is it?',
	'finder_sites'      => [
		[ 'key' => 'commercial', 'label' => 'Commercial or office', 'phrase' => 'a commercial building' ],
		[ 'key' => 'industrial', 'label' => 'Industrial or warehouse', 'phrase' => 'a warehouse' ],
		[ 'key' => 'public', 'label' => 'School or public building', 'phrase' => 'a public building' ],
	],
	'finder_button'     => 'Book a free site survey',
	'finder_target'     => $mbf_contact ?: null,
];

foreach ( $mbf_settings as $mbf_name => $mbf_value ) {
	if ( empty( $mbf_keys[ $mbf_name ] ) ) {
		WP_CLI::error( "No field named \"{$mbf_name}\" in acf-json/group_mbf_site_settings.json." );
	}

	// What's stored, not get_field() by key: that hands back the field's default, which reads as
	// already set, while mbf_setting() by name gets nothing until the value is really saved.
	if ( null === $mbf_value || ! empty( get_option( "options_{$mbf_name}" ) ) ) {
		continue;
	}

	$mbf_log( "set Site Settings {$mbf_name}" );

	if ( $mbf_force ) {
		update_field( $mbf_keys[ $mbf_name ], $mbf_value, 'option' );
	}
}

WP_CLI::success( $mbf_force ? 'Placeholder content in place.' : 'Dry run complete. Re-run with "force" to write.' );
