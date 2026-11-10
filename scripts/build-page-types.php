<?php
/**
 * One placeholder page of each page type, the home page and the blog page, as the baseline
 * for the render fingerprint. /_sample/ tests every layout on every ground; these test the
 * order sections really come in. A fork deletes or replaces them.
 *
 * Idempotent: pages match by path and their rows by a hash of what they should be. A second
 * run is a no-op.
 *
 *   wp eval-file scripts/build-page-types.php          # dry run (default)
 *   wp eval-file scripts/build-page-types.php force    # write
 *
 * Run build-placeholder-content.php and build-sample-page.php first: they make the pages,
 * images and form this reads. Images tagged `_mbf_sample_asset` = scene-rooftop or
 * scene-warehouse are used if a site has them; otherwise the flat placeholder stands in.
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$mbf_force = in_array( 'force', $args ?? [], true );
$mbf_log   = static fn( string $msg ) => WP_CLI::log( ( $mbf_force ? '' : '[dry] ' ) . $msg );

if ( ! function_exists( 'update_field' ) ) {
	WP_CLI::error( 'ACF Pro is not active.' );
}

wp_set_current_user( 1 );

// --- Assets --------------------------------------------------------------------------------
$mbf_asset = static function ( string $key ): int {
	$found = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_mbf_sample_asset', 'meta_value' => $key, 'fields' => 'ids', 'posts_per_page' => 1 ] ); // phpcs:ignore WordPress.DB.SlowDBQuery

	return (int) ( $found[0] ?? 0 );
};

$mbf_photo = $mbf_asset( 'photo' );

if ( ! $mbf_photo ) {
	WP_CLI::error( 'No placeholder images yet: run build-sample-page.php force first.' );
}

$mbf_rooftop   = $mbf_asset( 'scene-rooftop' ) ?: $mbf_photo;
$mbf_warehouse = $mbf_asset( 'scene-warehouse' ) ?: $mbf_photo;
$mbf_file      = $mbf_asset( 'file' );
$mbf_logos     = [ $mbf_asset( 'logo-1' ), $mbf_asset( 'logo-2' ), $mbf_asset( 'logo-3' ) ];

// --- Helpers -------------------------------------------------------------------------------
$mbf_id   = static fn( string $path ): int => (int) ( get_page_by_path( $path )->ID ?? 0 );
$mbf_link = static fn( string $title, string $path ) => [ 'title' => $title, 'url' => home_url( $path ), 'target' => '' ];
$mbf_row  = static fn( string $layout, string $ground, array $fields ) => [ 'acf_fc_layout' => $layout ] + $fields + [ 'ground' => $ground, 'accent' => 'default', 'spacing' => 'default' ];
$mbf_card = static fn( string $title, string $path, string $text = 'A short placeholder description.', int $image = 0 ) => [ 'tag' => '', 'icon' => '', 'title' => $title, 'text' => $text, 'url' => $mbf_id( $path ), 'image' => $image ];

$mbf_contact_link = $mbf_link( 'Get in touch', '/contact/' );
$mbf_spot         = static fn( string $solution, float $x, float $y ) => [ 'solution' => $solution, 'title' => '', 'text' => '', 'url' => '', 'x' => $x, 'y' => $y ];
$mbf_own          = static fn( string $title, string $path, float $x, float $y ) => [ 'solution' => '', 'title' => $title, 'text' => 'A short line about this solution.', 'url' => $mbf_id( $path ), 'x' => $x, 'y' => $y ];
$mbf_ticks        = [ [ 'text' => 'A first reason to choose it' ], [ 'text' => 'A second reason to choose it' ], [ 'text' => 'A third reason to choose it' ] ];
$mbf_standards    = [ [ 'text' => 'EN 795' ], [ 'text' => 'ISO 9001' ] ];
$mbf_glance       = [ [ 'label' => 'Fixing', 'value' => 'A short placeholder fact' ], [ 'label' => 'Roof types', 'value' => 'Placeholder, placeholder' ], [ 'label' => 'Height', 'value' => '1,100 mm' ], [ 'label' => 'Finish', 'value' => 'Galvanised' ] ];

// Shared sections: each one is used by more than one page type.
$mbf_enquiry = static fn( string $ground ) => $mbf_row( 'form', $ground, [ 'heading' => 'Talk to us about your site', 'text' => 'Tell us what you need and we will come back to you.', 'points' => [ [ 'text' => 'We reply within one working day' ], [ 'text' => 'No obligation' ] ], 'show_contact' => false ] );
$mbf_faq     = static fn( string $ground ) => $mbf_row( 'faq', $ground, [ 'heading' => 'Common questions', 'lead' => '', 'items' => [ [ 'question' => 'A first placeholder question?', 'answer' => '<p>A placeholder answer.</p>' ], [ 'question' => 'A second, longer placeholder question that wraps at narrow widths?', 'answer' => '<p>Another placeholder answer.</p>' ], [ 'question' => 'A third placeholder question?', 'answer' => '<p>A third placeholder answer.</p>' ] ], 'aside_heading' => '', 'aside_text' => '', 'aside_button' => null ] );
$mbf_band    = static fn( string $ground ) => $mbf_row( 'cta_band', $ground, [ 'eyebrow' => '', 'heading' => 'Not sure what you need?', 'text' => 'Book a free site survey and we will tell you.', 'button_1' => $mbf_contact_link, 'button_2' => null ] );
$mbf_steps   = static fn( string $ground ) => $mbf_row( 'steps', $ground, [ 'heading' => 'How it works', 'lead' => 'From first call to handover.', 'items' => [ [ 'title' => 'Survey', 'text' => 'We visit the site.', 'tag' => 'Free' ], [ 'title' => 'Design', 'text' => 'We plan the system.', 'tag' => '' ], [ 'title' => 'Install', 'text' => 'We fit and certify it.', 'tag' => '' ] ], 'button_1' => $mbf_link( 'Book a survey', '/contact/' ) ] );
$mbf_study   = static fn( string $ground, string $side = 'end' ) => $mbf_row( 'case_study', $ground, [ 'eyebrow' => 'Case study', 'heading' => 'A placeholder project', 'text' => 'A short summary of the job and how it went.', 'metrics' => [ [ 'value' => '37 km', 'label' => 'Of edge protection' ], [ 'value' => '12 wks', 'label' => 'Start to handover' ] ], 'quote' => 'A short quote from the client about the job.', 'name' => 'A. Person', 'role' => 'Placeholder role', 'image' => $mbf_rooftop, 'image_side' => $side, 'button_1' => $mbf_link( 'See all case studies', '/case-studies/' ) ] );
$mbf_split   = static fn( string $ground, string $heading, string $side, int $image ) => $mbf_row( 'split', $ground, [ 'eyebrow' => '', 'heading' => $heading, 'body' => '<p>Placeholder text beside an image, long enough to show how the two columns balance.</p>', 'points' => $mbf_ticks, 'image' => $image, 'image_side' => $side, 'button_1' => null, 'button_2' => null ] );
$mbf_hero    = static fn( string $ground, string $heading, array $extra = [] ) => $mbf_row( 'hero', $ground, $extra + [ 'eyebrow' => '', 'heading' => $heading, 'lead' => 'One lead sentence under the page heading.', 'button_1' => $mbf_contact_link, 'button_2' => null, 'badges' => [], 'panel' => 'image', 'image' => $mbf_rooftop ] );

$mbf_product_cards = [ $mbf_card( 'Placeholder product', 'products/placeholder-product', 'A short placeholder description.', $mbf_rooftop ), $mbf_card( 'Inspection', 'services/inspection', 'A short placeholder description.', $mbf_warehouse ), $mbf_card( 'Maintenance', 'services/maintenance', 'A short placeholder description.', $mbf_rooftop ) ];
$mbf_cards         = static fn( string $ground, string $heading, array $cards, ?array $link = null ) => $mbf_row( 'cards', $ground, [ 'heading' => $heading, 'lead' => '', 'link' => $link, 'cards' => $cards ] );

$mbf_service = [
	$mbf_hero( 'secondary', 'A placeholder service' ),
	$mbf_split( 'base', 'What the service covers', 'end', $mbf_warehouse ),
	$mbf_steps( 'tertiary' ),
	$mbf_row( 'stats', 'secondary', [ 'heading' => 'In numbers', 'items' => [ [ 'value' => '25+', 'label' => 'Years' ], [ 'value' => '1,200', 'label' => 'Projects' ], [ 'value' => '98%', 'label' => 'Repeat clients' ], [ 'value' => '24/7', 'label' => 'Support' ] ], 'badges' => [] ] ),
	$mbf_faq( 'base' ),
	$mbf_enquiry( 'tertiary' ),
];

// --- The page types ---------------------------------------------------------------------------
// Section order follows what the client sites' pages of the same type actually use.
$mbf_pages = [
	'home'                       => [
		$mbf_hero( 'secondary', 'A placeholder headline for the home page', [ 'eyebrow' => 'A short eyebrow', 'badges' => $mbf_standards, 'panel' => 'finder', 'background_image' => $mbf_rooftop ] ),
		$mbf_row( 'logos', 'base', [ 'heading' => 'Trusted by', 'logos' => $mbf_logos ] ),
		$mbf_cards( 'tertiary', 'What we do', $mbf_product_cards, $mbf_link( 'All products', '/products/' ) ),
		$mbf_steps( 'base' ),
		$mbf_band( 'primary' ),
		$mbf_study( 'base' ),
		// The site's scenes come from Site Settings › Hotspots (written below); the section only adds them.
		$mbf_row( 'hotspots', 'tertiary', [ 'show_scenes' => [] ] ),
		$mbf_faq( 'base' ),
		$mbf_row( 'posts', 'tertiary', [ 'heading' => 'Latest articles', 'lead' => '', 'source' => 'latest', 'category' => '', 'count' => 3, 'chosen' => [], 'link' => $mbf_link( 'All articles', '/blog/' ) ] ),
		$mbf_enquiry( 'secondary' ),
	],

	'about'                      => [
		$mbf_hero( 'secondary', 'About the company' ),
		$mbf_split( 'base', 'Who we are', 'end', $mbf_warehouse ),
		$mbf_split( 'tertiary', 'How we work', 'start', $mbf_rooftop ),
		$mbf_row( 'text', 'base', [ 'heading' => 'Our accreditations', 'lead' => '', 'body' => '<p>Placeholder text, with <a href="' . esc_url( home_url( '/contact/' ) ) . '">a link in running text</a>.</p><ul><li>A list item</li><li>Another list item</li></ul>' ] ),
		$mbf_row( 'quote', 'primary', [ 'quote' => 'A short placeholder testimonial.', 'name' => 'A. Person', 'role' => 'Placeholder role' ] ),
		$mbf_enquiry( 'base' ),
	],

	// Hub: an overview, then cards to the pages below it.
	'products'                   => [
		$mbf_hero( 'secondary', 'Products' ),
		$mbf_cards( 'base', 'The range', $mbf_product_cards ),
		$mbf_faq( 'tertiary' ),
		$mbf_enquiry( 'secondary' ),
	],

	'products/placeholder-product' => [
		// The solution hero (panel 'gallery'): the default product header, from Mantech and FA.
		$mbf_hero( 'base', 'A placeholder product', [ 'eyebrow' => 'A product range', 'badges' => $mbf_standards, 'panel' => 'gallery', 'gallery' => array_values( array_unique( [ $mbf_rooftop, $mbf_warehouse, $mbf_photo ] ) ), 'glance' => $mbf_glance, 'button_2' => $mbf_link( 'Specifications', '/products/placeholder-product/#specifications' ) ] ),
		$mbf_split( 'tertiary', 'What it does', 'end', $mbf_rooftop ),
		$mbf_split( 'base', 'Where it fits', 'start', $mbf_warehouse ),
		$mbf_band( 'primary' ),
		$mbf_row( 'specs', 'base', [ 'heading' => 'Specifications', 'lead' => '', 'groups' => [ [ 'heading' => 'Dimensions', 'rows' => [ [ 'label' => 'Height', 'value' => '1,100 mm' ], [ 'label' => 'Bay width', 'value' => '2,500 mm' ] ] ], [ 'heading' => 'Compliance', 'rows' => [ [ 'label' => 'Standard', 'value' => 'Placeholder standard' ], [ 'label' => 'Finish', 'value' => 'Galvanised' ] ] ] ], 'files' => [] ] ),
		$mbf_row( 'downloads', 'tertiary', [ 'heading' => 'Downloads', 'lead' => '', 'files' => [ [ 'title' => 'Placeholder data sheet', 'file' => $mbf_file, 'note' => 'Updated this year', 'cover' => '' ], [ 'title' => 'Placeholder installation guide', 'file' => $mbf_file, 'note' => '', 'cover' => '' ] ] ] ),
		$mbf_faq( 'base' ),
		$mbf_cards( 'tertiary', 'Related', array_slice( $mbf_product_cards, 1 ) ),
		$mbf_enquiry( 'secondary' ),
	],

	'services'                   => [
		$mbf_hero( 'secondary', 'Services' ),
		$mbf_cards( 'base', 'What we offer', array_slice( $mbf_product_cards, 1 ) ),
		$mbf_steps( 'tertiary' ),
		$mbf_enquiry( 'secondary' ),
	],
	'services/inspection'        => $mbf_service,
	'services/maintenance'       => $mbf_service,

	// No hero on the listing pages: the default page header owns the h1, so that shape is covered too.
	'industries'                 => [
		$mbf_cards( 'base', 'Who we work with', [ $mbf_card( 'A placeholder sector', 'industries/placeholder-sector', 'A short placeholder description.', $mbf_warehouse ) ] ),
		$mbf_enquiry( 'tertiary' ),
	],
	'industries/placeholder-sector' => [
		$mbf_hero( 'secondary', 'A placeholder sector' ),
		$mbf_split( 'base', 'The risks in this sector', 'end', $mbf_warehouse ),
		$mbf_split( 'tertiary', 'How we help', 'start', $mbf_rooftop ),
		$mbf_cards( 'base', 'What suits it', array_slice( $mbf_product_cards, 0, 2 ) ),
		$mbf_enquiry( 'secondary' ),
	],

	'case-studies'               => [
		$mbf_study( 'base' ),
		$mbf_study( 'tertiary', 'start' ),
		$mbf_study( 'base' ),
		$mbf_band( 'primary' ),
	],

	'faqs'                       => [
		$mbf_faq( 'base' ),
		$mbf_enquiry( 'tertiary' ),
	],

	'contact'                    => [
		$mbf_row( 'form', 'base', [ 'heading' => 'Send us a message', 'text' => 'Tell us what you need and we will come back to you.', 'points' => [], 'show_contact' => true ] ),
	],

	// /blog renders through index.php, which prints these after the post list.
	'blog'                       => [
		$mbf_band( 'primary' ),
	],
];

foreach ( $mbf_pages as $mbf_path => $mbf_rows ) {
	$mbf_page_id = $mbf_id( $mbf_path );

	if ( ! $mbf_page_id ) {
		$mbf_log( "skip {$mbf_path}: run build-placeholder-content.php force first" );
		continue;
	}

	$mbf_hash = md5( (string) wp_json_encode( $mbf_rows ) );

	if ( get_post_meta( $mbf_page_id, '_mbf_page_type_hash', true ) === $mbf_hash ) {
		continue;
	}

	$mbf_log( "write {$mbf_path}: " . count( $mbf_rows ) . ' sections' );

	if ( $mbf_force ) {
		update_field( 'page_layouts', $mbf_rows, $mbf_page_id );
		update_post_meta( $mbf_page_id, '_mbf_page_type_hash', $mbf_hash );
	}
}

// --- Site Settings › Hotspots: the site's scenes, set once (written only when unset) -------
$mbf_hotspots = [
	'hotspot_heading' => 'See a typical site',
	'hotspot_lead'    => 'Hover a number to see what it is, or choose from the list.',
	'hotspot_scenes'  => [
		[
			'label' => 'Rooftop',
			'image' => $mbf_rooftop,
			// FA's 11 rooftop positions on the same render (2026-11-10), with generic names. Spots that
			// match a finder answer take it; the rest set their own name and link.
			'spots' => [
				$mbf_spot( 'roof-edge', 14.15, 11.81 ),
				$mbf_spot( 'fragile-roof', 38.05, 18.85 ),
				$mbf_spot( 'routes', 51.2, 26.83 ),
				$mbf_own( 'Mini step-over', 'products/placeholder-product', 67.04, 42.23 ),
				$mbf_own( 'Rooflight cover', 'products', 8.17, 46.37 ),
				$mbf_own( 'Ramp handrail', 'services/inspection', 50.61, 80.49 ),
				$mbf_own( 'Tube and fittings', 'products/placeholder-product', 4.4, 84 ),
				$mbf_own( 'Access platform', 'services/maintenance', 62, 27.86 ),
				$mbf_own( 'Self-closing gate', 'products', 27.93, 81.37 ),
				$mbf_own( 'Handrail kit', 'products/placeholder-product', 66.63, 88.87 ),
				$mbf_own( 'Ground-level barrier', 'industries/placeholder-sector', 15.5, 69.5 ),
			],
		],
		[
			'label' => 'Warehouse',
			'image' => $mbf_warehouse,
			// FA's 7 warehouse positions.
			'spots' => [
				$mbf_own( 'Mobile access platform', 'services/maintenance', 61.58, 60.6 ),
				$mbf_own( 'Tube and fittings', 'products/placeholder-product', 72.32, 34.7 ),
				$mbf_spot( 'loading', 40.61, 25.24 ),
				$mbf_own( 'Floor demarcation', 'industries/placeholder-sector', 48.17, 76.31 ),
				$mbf_own( 'Impact barrier', 'products', 9.51, 44.39 ),
				$mbf_own( 'Self-closing gate', 'products', 26.22, 50.33 ),
				$mbf_own( 'Handrail kit', 'products/placeholder-product', 85.49, 53.85 ),
			],
		],
	],
];

foreach ( $mbf_hotspots as $mbf_name => $mbf_value ) {
	if ( ! empty( get_option( "options_{$mbf_name}" ) ) ) {
		continue;
	}

	$mbf_log( "set Site Settings {$mbf_name}" );

	if ( $mbf_force ) {
		update_field( $mbf_name, $mbf_value, 'option' );
	}
}

// --- Featured images: the hotspot popup shows the linked page's, so the placeholders get one --
foreach ( [ 'products' => $mbf_warehouse, 'products/placeholder-product' => $mbf_rooftop, 'services/inspection' => $mbf_warehouse, 'services/maintenance' => $mbf_rooftop, 'industries/placeholder-sector' => $mbf_warehouse ] as $mbf_path => $mbf_image ) {
	$mbf_page_id = $mbf_id( $mbf_path );

	if ( ! $mbf_page_id || has_post_thumbnail( $mbf_page_id ) ) {
		continue;
	}

	$mbf_log( "set featured image on {$mbf_path}" );

	if ( $mbf_force ) {
		set_post_thumbnail( $mbf_page_id, $mbf_image );
	}
}

// --- Reading settings: a real home page, and /blog/ as the posts page -------------------------
foreach ( [ 'show_on_front' => 'page', 'page_on_front' => $mbf_id( 'home' ), 'page_for_posts' => $mbf_id( 'blog' ) ] as $mbf_option => $mbf_value ) {
	if ( (string) get_option( $mbf_option ) === (string) $mbf_value || ! $mbf_value ) {
		continue;
	}

	$mbf_log( "set {$mbf_option} = {$mbf_value}" );

	if ( $mbf_force ) {
		update_option( $mbf_option, $mbf_value );
	}
}

WP_CLI::success( $mbf_force ? 'Page types in place.' : 'Dry run complete. Re-run with "force" to write.' );
