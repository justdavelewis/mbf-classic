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
	'photo'    => [ 1200, 900, [ 176, 184, 196 ], 'Placeholder image' ],
	'portrait' => [ 900, 1200, [ 160, 170, 186 ], 'Placeholder portrait image' ],   // the split shows portraits whole
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

// A placeholder PDF for the download layouts: the smallest valid file, written here so the repo carries no binaries.
$mbf_pdf = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_mbf_sample_asset', 'meta_value' => 'file', 'fields' => 'ids', 'posts_per_page' => 1 ] ); // phpcs:ignore WordPress.DB.SlowDBQuery
$mbf_ids['file'] = $mbf_pdf ? (int) $mbf_pdf[0] : 0;

if ( ! $mbf_ids['file'] ) {
	$mbf_log( 'import placeholder PDF' );

	if ( $mbf_force ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$mbf_tmp = wp_tempnam( 'mbf-sample-file.pdf' );
		file_put_contents( $mbf_tmp, "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 595 842]>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF\n" );

		$mbf_id = media_handle_sideload( [ 'name' => 'mbf-sample-file.pdf', 'tmp_name' => $mbf_tmp ], 0, 'Placeholder document' );

		if ( is_wp_error( $mbf_id ) ) {
			WP_CLI::error( 'PDF: ' . $mbf_id->get_error_message() );
		}

		update_post_meta( $mbf_id, '_mbf_sample_asset', 'file' );
		$mbf_ids['file'] = (int) $mbf_id;
	}
}

// --- 2. The enquiry form: Mantech's structure, with nothing client-specific (scripts/data/) ---
global $wpdb;

$mbf_form_label = 'MBF enquiry';
$mbf_form_id    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}wsf_form WHERE label = %s AND status != 'trash' ORDER BY id LIMIT 1", $mbf_form_label ) );

if ( ! $mbf_form_id ) {
	$mbf_log( "create WS Form \"{$mbf_form_label}\" from scripts/data/enquiry-form.json" );

	if ( $mbf_force ) {
		$mbf_template = __DIR__ . '/data/enquiry-form.json';
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

		// An import gives fields new IDs but leaves the old ones in action text, so the file names them {{Label}}.
		$mbf_tree   = $mbf_form->db_read( true, true );
		$mbf_action = (string) wp_json_encode( $mbf_tree->meta->action );

		foreach ( $mbf_tree->groups as $mbf_group ) {
			foreach ( $mbf_group->sections as $mbf_section ) {
				foreach ( $mbf_section->fields as $mbf_field ) {
					$mbf_action = str_replace( '{{' . $mbf_field->label . '}}', (string) $mbf_field->id, $mbf_action );
				}
			}
		}

		$mbf_meta            = new WS_Form_Meta();
		$mbf_meta->object    = 'form';
		$mbf_meta->parent_id = $mbf_form_id;
		$mbf_meta->db_update_from_array( [ 'action' => json_decode( $mbf_action ) ], false, true );

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
$mbf_page_id_of = static fn( string $path ) => (int) url_to_postid( home_url( $path ) );
$mbf_card    = static fn( string $title, string $path, bool $image = true, string $text = 'A short placeholder description.', string $tag = '', string $icon = '' ) => [
	'tag'   => $tag,
	'icon'  => $icon,
	'title' => $title,
	'text'  => $text,
	'url'   => '' !== $path ? (int) url_to_postid( home_url( $path ) ) : '',
	'image' => $image ? $mbf_photo : '',
];

// Card counts per ground exercise the count-aware grid: 3 (auto-fill), 2 and 4 (pairs), 1 (lone).
$mbf_cards = [
	'base'      => [ $mbf_card( 'Inspection', '/services/inspection/' ), $mbf_card( 'Maintenance', '/services/maintenance/', true, 'A longer placeholder description that wraps onto more lines, so the subgrid rows can be seen aligning across the row.' ), $mbf_card( 'About us', '/about/', false, 'No image, so its icon shows.', '6 systems', 'fa-helmet-safety' ) ],
	'tertiary'  => [ $mbf_card( 'Services', '/services/', true, 'A short placeholder description.', 'EN 795' ), $mbf_card( 'Contact', '/contact/' ) ],
	'secondary' => [ $mbf_card( 'Inspection', '/services/inspection/' ), $mbf_card( 'Maintenance', '/services/maintenance/' ), $mbf_card( 'About us', '/about/', false ), $mbf_card( 'Not linked', '', false, 'No URL: no link, no CTA, no lift.' ) ],
	'primary'   => [ $mbf_card( 'A single card', '/about/' ) ],
];

$mbf_rows = [];

// Grounds exercise the optional fields: some rows carry them, some don't, so both shapes render.
$mbf_ticks   = [ [ 'text' => 'A first ticked point' ], [ 'text' => 'A second, longer ticked point that wraps onto another line at narrow widths' ] ];
$mbf_spots   = [
	[ 'title' => 'A linked spot', 'text' => 'Short text about this spot.', 'url' => $mbf_page_id_of( '/services/inspection/' ), 'x' => 22, 'y' => 30 ],
	[ 'title' => 'A spot with no link', 'text' => 'Another short description.', 'url' => '', 'x' => 64, 'y' => 48 ],
	[ 'title' => 'At the very edge', 'text' => 'Placed at 100% so the clamp can be seen.', 'url' => '', 'x' => 100, 'y' => 100 ],
];
$mbf_files   = [ [ 'title' => 'Placeholder data sheet', 'file' => $mbf_ids['file'], 'note' => 'Updated this year', 'cover' => '' ], [ 'title' => 'Placeholder guide with a cover', 'file' => $mbf_ids['file'], 'note' => '', 'cover' => $mbf_photo ] ];
$mbf_tables  = [
	[ 'heading' => 'Dimensions', 'rows' => [ [ 'label' => 'Height', 'value' => '1,100 mm' ], [ 'label' => 'Bay width', 'value' => '2,500 mm' ] ] ],
	[ 'heading' => 'Compliance', 'rows' => [ [ 'label' => 'Standard', 'value' => 'Placeholder standard' ], [ 'label' => 'Finish', 'value' => 'Galvanised' ] ] ],
];
$mbf_is      = static fn( string $g, string ...$grounds ) => in_array( $g, $grounds, true );

// A real scene shows far more than a flat fill. Only used if a site has one tagged; a fork gets the placeholder.
$mbf_scene = static function ( string $key ) use ( $mbf_photo ): int {
	$found = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_mbf_sample_asset', 'meta_value' => $key, 'fields' => 'ids', 'posts_per_page' => 1 ] ); // phpcs:ignore WordPress.DB.SlowDBQuery

	return (int) ( $found[0] ?? 0 ) ?: $mbf_photo;
};
$mbf_rooftop   = $mbf_scene( 'scene-rooftop' );
$mbf_warehouse = $mbf_scene( 'scene-warehouse' );

$mbf_layouts = [
	'hero'       => static fn( string $g ) => [ 'eyebrow' => $mbf_is( $g, 'base', 'secondary' ) ? 'A short eyebrow' : '', 'heading' => "Hero on {$g}", 'lead' => 'One lead sentence under the hero heading.', 'button_1' => $mbf_link( 'Get in touch', '/contact/' ), 'button_2' => $mbf_link( 'Our services', '/services/' ), 'badges' => $mbf_is( $g, 'base', 'tertiary' ) ? [ [ 'text' => 'EN 795' ], [ 'text' => 'ISO 9001' ] ] : [], 'panel' => $mbf_is( $g, 'base', 'secondary' ) ? 'gallery' : 'finder', 'image' => $mbf_photo, 'gallery' => array_values( array_unique( [ $mbf_rooftop, $mbf_warehouse, $mbf_photo, $mbf_ids['portrait'] ] ) ), 'glance' => $mbf_is( $g, 'base' ) ? [ [ 'label' => 'Fixing', 'value' => 'A short placeholder fact' ], [ 'label' => 'A longer label that wraps', 'value' => 'A longer placeholder value that wraps at narrow widths' ] ] : [ [ 'label' => 'Height', 'value' => '1,100 mm' ] ], 'background_image' => 'primary' === $g ? $mbf_rooftop : 0 ],
	'text'       => static fn( string $g ) => [ 'heading' => "Text on {$g}", 'lead' => 'base' === $g ? 'An optional lead under the heading.' : '', 'body' => '<p>Rich text with <a href="' . esc_url( home_url( '/about/' ) ) . '">a link in running text</a> and <strong>bold text</strong>.</p><ul><li>A list item</li><li>Another list item</li></ul><blockquote><p>A short quotation.</p></blockquote>' ],
	'split'      => static fn( string $g ) => [ 'eyebrow' => $mbf_is( $g, 'base', 'secondary' ) ? 'A short eyebrow' : '', 'heading' => "Split on {$g}", 'body' => '<p>Text beside an image. Alternate rows put the image first.</p>', 'points' => $mbf_is( $g, 'base', 'secondary' ) ? $mbf_ticks : [], 'image' => 'secondary' === $g ? $mbf_ids['portrait'] : $mbf_photo, 'image_side' => $mbf_is( $g, 'tertiary', 'primary' ) ? 'start' : 'end', 'button_1' => $mbf_link( 'Read more', '/about/' ), 'button_2' => $mbf_is( $g, 'secondary' ) ? $mbf_link( 'Contact us', '/contact/' ) : null ],
	'cards'      => static fn( string $g ) => [ 'heading' => "Cards on {$g}", 'lead' => count( $mbf_cards[ $g ] ) . ' cards.', 'link' => $mbf_is( $g, 'base', 'secondary' ) ? $mbf_link( 'All services', '/services/' ) : null, 'cards' => $mbf_cards[ $g ] ],
	'cta_band'   => static fn( string $g ) => [ 'eyebrow' => 'primary' === $g ? 'A short eyebrow' : '', 'heading' => "CTA band on {$g}", 'text' => 'No second button set, so the Site Settings phone stands in.', 'button_1' => $mbf_link( 'Contact us', '/contact/' ), 'button_2' => null ],
	'faq'        => static fn( string $g ) => [ 'heading' => "FAQ on {$g}", 'lead' => $mbf_is( $g, 'tertiary', 'primary' ) ? 'An optional lead.' : '', 'items' => [ [ 'question' => 'A first placeholder question?', 'answer' => '<p>A placeholder answer.</p>' ], [ 'question' => 'A second, longer placeholder question that wraps at narrow widths?', 'answer' => '<p>Another answer, with <a href="' . esc_url( home_url( '/contact/' ) ) . '">a link</a>.</p>' ] ], 'aside_heading' => $mbf_is( $g, 'tertiary', 'primary' ) ? 'Still have a question?' : '', 'aside_text' => $mbf_is( $g, 'tertiary', 'primary' ) ? 'A short line of text in the side card.' : '', 'aside_button' => $mbf_is( $g, 'tertiary', 'primary' ) ? $mbf_link( 'Contact us', '/contact/' ) : null ],
	'stats'      => static fn( string $g ) => [ 'heading' => "Stats on {$g}", 'items' => [ [ 'value' => '25+', 'label' => 'Years' ], [ 'value' => '1,200', 'label' => 'Projects' ], [ 'value' => '98%', 'label' => 'Repeat clients' ], [ 'value' => '24/7', 'label' => 'Support' ] ], 'badges' => $mbf_is( $g, 'tertiary', 'secondary' ) ? [ [ 'name' => 'Placeholder accreditation', 'logo' => '' ], [ 'name' => 'Placeholder Organisation One', 'logo' => $mbf_ids['logo-1'] ] ] : [] ],
	'quote'      => static fn( string $g ) => [ 'quote' => "A short placeholder testimonial, on {$g}.", 'name' => 'A. Person', 'role' => 'Placeholder role' ],
	'steps'      => static fn( string $g ) => [ 'heading' => "Steps on {$g}", 'lead' => 'How it works.', 'items' => [ [ 'title' => 'Call', 'text' => 'Tell us what you need.', 'tag' => '' ], [ 'title' => 'Survey', 'text' => 'We visit the site.', 'tag' => $mbf_is( $g, 'base', 'secondary' ) ? 'Free' : '' ], [ 'title' => 'Deliver', 'text' => 'We do the work.', 'tag' => '' ] ], 'button_1' => $mbf_is( $g, 'base', 'secondary' ) ? $mbf_link( 'Book a survey', '/contact/' ) : null ],
	'logos'      => static fn( string $g ) => [ 'heading' => "Logos on {$g}", 'logos' => [ $mbf_ids['logo-1'], $mbf_ids['logo-2'], $mbf_ids['logo-3'] ] ],
	'form'       => static fn( string $g ) => [ 'heading' => "Enquiry form on {$g}", 'text' => 'The placeholder WS Form. Email and webhook actions are off outside production.', 'points' => $mbf_is( $g, 'base', 'secondary' ) ? [ [ 'text' => 'We reply within one working day' ], [ 'text' => 'No obligation' ] ] : [], 'show_contact' => $mbf_is( $g, 'base', 'primary' ) ],
	'finder'     => static fn( string $g ) => [ 'heading' => $mbf_is( $g, 'tertiary', 'primary' ) ? "Finder on {$g}" : '', 'lead' => 'Pick the closest match.' ],
	'case_study' => static fn( string $g ) => [ 'eyebrow' => 'Case study', 'heading' => "Case study on {$g}", 'text' => 'A short summary of the job and how it went.', 'metrics' => [ [ 'value' => '37 km', 'label' => 'Of edge protection' ], [ 'value' => '12 wks', 'label' => 'Start to handover' ] ], 'quote' => $mbf_is( $g, 'base', 'secondary' ) ? 'A short quote from the client about the job.' : '', 'name' => 'A. Person', 'role' => 'Placeholder role', 'image' => $mbf_photo, 'image_side' => $mbf_is( $g, 'tertiary', 'primary' ) ? 'start' : 'end', 'button_1' => $mbf_link( 'Read the case study', '/about/' ) ],
	'hotspots'   => static fn( string $g ) => [ 'heading' => "Hotspots on {$g}", 'lead' => 'Tap a number, or read the list.', 'scenes' => $mbf_is( $g, 'base', 'secondary' ) ? [ [ 'label' => 'Rooftop', 'image' => $mbf_rooftop, 'spots' => $mbf_spots ], [ 'label' => 'Warehouse', 'image' => $mbf_warehouse, 'spots' => array_slice( $mbf_spots, 0, 2 ) ] ] : [ [ 'label' => '', 'image' => $mbf_rooftop, 'spots' => $mbf_spots ] ] ],
	'specs'      => static fn( string $g ) => [ 'heading' => "Specifications on {$g}", 'lead' => '', 'groups' => $mbf_is( $g, 'primary' ) ? array_slice( $mbf_tables, 0, 1 ) : $mbf_tables, 'files' => $mbf_is( $g, 'base', 'secondary' ) ? array_slice( $mbf_files, 0, 1 ) : [] ],
	'downloads'  => static fn( string $g ) => [ 'heading' => "Downloads on {$g}", 'lead' => 'Brochures and guides.', 'files' => $mbf_files ],
	'posts'      => static fn( string $g ) => [ 'heading' => "Posts on {$g}", 'lead' => '', 'source' => 'latest', 'category' => '', 'count' => $mbf_is( $g, 'tertiary' ) ? 2 : 3, 'chosen' => [], 'link' => $mbf_link( 'All articles', '/blog/' ) ],
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
	$mbf_page_id = $mbf_force ? wp_insert_post(
		[ 'post_type' => 'page', 'post_name' => '_sample', 'post_title' => 'Sample', 'post_status' => 'publish' ],
		true
	) : 0;

	// Stop rather than carry on: (int) of a WP_Error is 1, so the rows would land on post 1.
	if ( is_wp_error( $mbf_page_id ) ) {
		WP_CLI::error( 'Could not create /_sample/: ' . $mbf_page_id->get_error_message() );
	}

	$mbf_page_id = (int) $mbf_page_id;
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
