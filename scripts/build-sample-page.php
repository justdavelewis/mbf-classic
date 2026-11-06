<?php
/**
 * /_sample/: every starter layout on every ground (docs/08: keep it during the build,
 * delete it at launch). Also provisions what the layouts need to render: neutral
 * placeholder images and a placeholder WS Form (its ID goes to Site Settings › Forms).
 *
 * Idempotent: images match by `_mbf_sample_asset` meta, the form by label, the page by
 * slug, and the page's rows by a hash of what they should be. A second run is a no-op.
 *
 *   wp eval-file scripts/build-sample-page.php          # dry run (default)
 *   wp eval-file scripts/build-sample-page.php force    # write
 *
 * Needs ACF Pro, WS Form Pro and PHP's GD extension. Run build-placeholder-content.php
 * first: the CTA band's phone fallback reads the Site Settings phone it sets.
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$mbf_force = in_array( 'force', $args ?? [], true );
$mbf_log   = static fn( string $msg ) => WP_CLI::log( ( $mbf_force ? '' : '[dry] ' ) . $msg );

if ( ! function_exists( 'update_field' ) || ! class_exists( 'WS_Form_Form' ) || ! function_exists( 'imagecreatetruecolor' ) ) {
	WP_CLI::error( 'Needs ACF Pro, WS Form Pro and the GD extension.' );
}

wp_set_current_user( 1 );

// --- 1. Placeholder images: flat neutral PNGs drawn here, so the repo carries no binaries.
$mbf_assets = [
	'photo'  => [ 1200, 900, [ 176, 184, 196 ], 'Placeholder image' ],
	'logo-1' => [ 320, 120, [ 92, 100, 122 ], 'Placeholder Organisation One' ],
	'logo-2' => [ 320, 120, [ 120, 128, 148 ], 'Placeholder Organisation Two' ],
	'logo-3' => [ 320, 120, [ 70, 78, 98 ], 'Placeholder Organisation Three' ],
];
$mbf_ids    = [];

foreach ( $mbf_assets as $mbf_key => [ $mbf_w, $mbf_h, $mbf_rgb, $mbf_alt ] ) {
	$mbf_found = get_posts(
		[
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_key'       => '_mbf_sample_asset', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $mbf_key, // phpcs:ignore WordPress.DB.SlowDBQuery
			'fields'         => 'ids',
			'posts_per_page' => 1,
		]
	);

	if ( $mbf_found ) {
		$mbf_ids[ $mbf_key ] = (int) $mbf_found[0];
		continue;
	}

	$mbf_log( "import placeholder image {$mbf_key} ({$mbf_w}×{$mbf_h})" );

	if ( ! $mbf_force ) {
		$mbf_ids[ $mbf_key ] = 0;
		continue;
	}

	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$mbf_img = imagecreatetruecolor( $mbf_w, $mbf_h );
	imagefill( $mbf_img, 0, 0, imagecolorallocate( $mbf_img, ...$mbf_rgb ) );
	$mbf_tmp = wp_tempnam( "mbf-sample-{$mbf_key}.png" );
	imagepng( $mbf_img, $mbf_tmp );

	$mbf_id = media_handle_sideload( [ 'name' => "mbf-sample-{$mbf_key}.png", 'tmp_name' => $mbf_tmp ], 0, $mbf_alt );

	if ( is_wp_error( $mbf_id ) ) {
		WP_CLI::error( "Image {$mbf_key}: " . $mbf_id->get_error_message() );
	}

	update_post_meta( $mbf_id, '_mbf_sample_asset', $mbf_key );
	update_post_meta( $mbf_id, '_wp_attachment_image_alt', $mbf_alt );
	$mbf_ids[ $mbf_key ] = (int) $mbf_id;
}

// --- 2. Placeholder enquiry form, from WS Form's own "Contact Us" template --------------
global $wpdb;

$mbf_form_label = 'MBF placeholder enquiry';
$mbf_form_id    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}wsf_form WHERE label = %s AND status != 'trash' ORDER BY id LIMIT 1", $mbf_form_label ) );

if ( ! $mbf_form_id ) {
	$mbf_log( "create WS Form \"{$mbf_form_label}\" from the plugin's Contact Us template" );

	if ( $mbf_force ) {
		$mbf_template = WS_FORM_PLUGIN_DIR_PATH . 'includes/templates/form/contact/wsf-form-contact-us.json';
		$mbf_obj      = json_decode( (string) file_get_contents( $mbf_template ) );

		if ( ! $mbf_obj ) {
			WP_CLI::error( "Could not read {$mbf_template}" );
		}

		$mbf_obj->label = $mbf_form_label;

		// WS Form has no public import API; this is its own restore path (Mantech's import script).
		$mbf_form        = new WS_Form_Form();
		$mbf_form->label = $mbf_form_label;
		$mbf_form_id     = (int) $mbf_form->db_create();
		$mbf_form->id    = $mbf_form_id;
		$mbf_form->db_update_from_object( $mbf_obj, true, true, true );

		// The front end renders the PUBLISHED snapshot, so publish after the writes (05).
		$mbf_form     = new WS_Form_Form();
		$mbf_form->id = $mbf_form_id;
		$mbf_form->db_checksum();
		$mbf_form->db_publish();
	}
}

if ( ! $mbf_form_id || (int) get_field( 'field_mbf_site_form_enquiry', 'option' ) !== $mbf_form_id ) {
	$mbf_log( "set Site Settings › Forms › Enquiry form ID = {$mbf_form_id}" );

	if ( $mbf_force && $mbf_form_id ) {
		update_field( 'field_mbf_site_form_enquiry', $mbf_form_id, 'option' );
	}
}

// --- 3. The rows: every layout × every ground --------------------------------------------
// Grounds cycle base → tertiary → secondary → primary within each layout, so no two
// adjacent sections share a ground (08 sweep).
$mbf_grounds = [ 'base', 'tertiary', 'secondary', 'primary' ];
$mbf_photo   = $mbf_ids['photo'];
$mbf_link    = static fn( string $title, string $path ) => [ 'title' => $title, 'url' => home_url( $path ), 'target' => '' ];
$mbf_card    = static fn( string $title, string $path, bool $image = true, string $text = 'A short placeholder description.' ) => [
	'title' => $title,
	'text'  => $text,
	'url'   => '' !== $path ? (int) url_to_postid( home_url( $path ) ) : '',
	'image' => $image ? $mbf_photo : '',
];

// Card counts per ground exercise the count-aware grid: 3 (auto-fill), 2 and 4 (pairs), 1 (lone).
$mbf_cards = [
	'base'      => [ $mbf_card( 'Inspection', '/services/inspection/' ), $mbf_card( 'Maintenance', '/services/maintenance/', true, 'A longer placeholder description that wraps onto more lines, so the subgrid rows can be seen aligning across the row.' ), $mbf_card( 'About us', '/about/', false ) ],
	'tertiary'  => [ $mbf_card( 'Services', '/services/' ), $mbf_card( 'Contact', '/contact/' ) ],
	'secondary' => [ $mbf_card( 'Inspection', '/services/inspection/' ), $mbf_card( 'Maintenance', '/services/maintenance/' ), $mbf_card( 'About us', '/about/', false ), $mbf_card( 'Not linked', '', false, 'No URL: no link, no CTA, no lift.' ) ],
	'primary'   => [ $mbf_card( 'A single card', '/about/' ) ],
];

$mbf_rows = [];

$mbf_layouts = [
	'hero'     => static fn( string $g ) => [ 'heading' => "Hero on {$g}", 'lead' => 'One lead sentence under the hero heading.', 'button_1' => $mbf_link( 'Get in touch', '/contact/' ), 'button_2' => $mbf_link( 'Our services', '/services/' ), 'image' => $mbf_photo ],
	'text'     => static fn( string $g ) => [ 'heading' => "Text on {$g}", 'body' => '<p>Rich text with <a href="' . esc_url( home_url( '/about/' ) ) . '">a link in running text</a> and <strong>bold text</strong>.</p><ul><li>A list item</li><li>Another list item</li></ul><blockquote><p>A short quotation.</p></blockquote>' ],
	'split'    => static fn( string $g ) => [ 'heading' => "Split on {$g}", 'body' => '<p>Text beside an image. Alternate rows put the image first.</p>', 'image' => $mbf_photo, 'image_side' => in_array( $g, [ 'tertiary', 'primary' ], true ) ? 'start' : 'end', 'button_1' => $mbf_link( 'Read more', '/about/' ) ],
	'cards'    => static fn( string $g ) => [ 'heading' => "Cards on {$g}", 'lead' => count( $mbf_cards[ $g ] ) . ' cards.', 'cards' => $mbf_cards[ $g ] ],
	'cta_band' => static fn( string $g ) => [ 'heading' => "CTA band on {$g}", 'text' => 'No second button set, so the Site Settings phone stands in.', 'button_1' => $mbf_link( 'Contact us', '/contact/' ), 'button_2' => null ],
	'faq'      => static fn( string $g ) => [ 'heading' => "FAQ on {$g}", 'items' => [ [ 'question' => 'A first placeholder question?', 'answer' => '<p>A placeholder answer.</p>' ], [ 'question' => 'A second, longer placeholder question that wraps at narrow widths?', 'answer' => '<p>Another answer, with <a href="' . esc_url( home_url( '/contact/' ) ) . '">a link</a>.</p>' ] ] ],
	'stats'    => static fn( string $g ) => [ 'heading' => "Stats on {$g}", 'items' => [ [ 'value' => '25+', 'label' => 'Years' ], [ 'value' => '1,200', 'label' => 'Projects' ], [ 'value' => '98%', 'label' => 'Repeat clients' ], [ 'value' => '24/7', 'label' => 'Support' ] ] ],
	'quote'    => static fn( string $g ) => [ 'quote' => "A short placeholder testimonial, on {$g}.", 'name' => 'A. Person', 'role' => 'Placeholder role' ],
	'steps'    => static fn( string $g ) => [ 'heading' => "Steps on {$g}", 'lead' => 'How it works.', 'items' => [ [ 'title' => 'Call', 'text' => 'Tell us what you need.' ], [ 'title' => 'Survey', 'text' => 'We visit the site.' ], [ 'title' => 'Deliver', 'text' => 'We do the work.' ] ] ],
	'logos'    => static fn( string $g ) => [ 'heading' => "Logos on {$g}", 'logos' => [ $mbf_ids['logo-1'], $mbf_ids['logo-2'], $mbf_ids['logo-3'] ] ],
	'form'     => static fn( string $g ) => [ 'heading' => "Enquiry form on {$g}", 'text' => 'The placeholder WS Form. Email and webhook actions are off outside production.' ],
];

foreach ( $mbf_layouts as $mbf_layout => $mbf_content ) {
	foreach ( $mbf_grounds as $mbf_ground ) {
		$mbf_rows[] = [ 'acf_fc_layout' => $mbf_layout ] + $mbf_content( $mbf_ground ) + [ 'ground' => $mbf_ground, 'accent' => 'default', 'spacing' => 'default' ];
	}
}

// --- 4. The page --------------------------------------------------------------------------
$mbf_hash = md5( (string) wp_json_encode( $mbf_rows ) );
$mbf_page = get_page_by_path( '_sample' );

if ( ! $mbf_page ) {
	$mbf_log( 'create page /_sample/' );
	$mbf_page_id = $mbf_force ? (int) wp_insert_post(
		[ 'post_type' => 'page', 'post_name' => '_sample', 'post_title' => 'Sample', 'post_status' => 'publish' ],
		true
	) : 0;
} else {
	$mbf_page_id = (int) $mbf_page->ID;
}

if ( ! $mbf_page_id || get_post_meta( $mbf_page_id, '_mbf_sample_hash', true ) !== $mbf_hash ) {
	$mbf_log( 'write ' . count( $mbf_rows ) . ' sections (' . count( $mbf_layouts ) . ' layouts × ' . count( $mbf_grounds ) . ' grounds)' );

	if ( $mbf_force && $mbf_page_id ) {
		update_field( 'page_layouts', $mbf_rows, $mbf_page_id );
		update_post_meta( $mbf_page_id, '_mbf_sample_hash', $mbf_hash );
	}
}

WP_CLI::success( $mbf_force ? 'Sample page in place: ' . home_url( '/_sample/' ) : 'Dry run complete. Re-run with "force" to write.' );
