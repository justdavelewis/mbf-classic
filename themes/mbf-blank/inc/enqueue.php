<?php
/**
 * Front-end assets. Versions are file mtimes, so a rebuild busts the cache.
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

function mbf_enqueue(): void {
	$css = get_theme_file_path( 'dist/style.css' );

	if ( file_exists( $css ) ) {
		wp_enqueue_style( 'mbf', get_theme_file_uri( 'dist/style.css' ), [], (string) filemtime( $css ) );
	}

	/*
	 * Font Awesome Pro: the CSS build, NOT the JS kit, which rewrites <i> into inline SVG
	 * at runtime, so the markup you author isn't the markup that ships (05; a wrong family
	 * silently rendered nothing on SafetyAccess). Token in Site Settings so it rotates
	 * without a deploy; blank = no icons, and icon spots fall back to text.
	 * Version pinned as on Mantech (2026-11-05).
	 */
	if ( mbf_has_icons() ) {
		$fa = 'https://ka-p.fontawesome.com/releases/v7.3.1/css/pro.min.css?token=' . rawurlencode( (string) mbf_setting( 'fontawesome_token' ) );
		wp_enqueue_style( 'mbf-fontawesome', $fa, [], null );
	}

	// The header only renders the nav and drawer when a Primary menu is assigned.
	if ( has_nav_menu( 'primary' ) ) {
		mbf_enqueue_script( 'mbf-nav', 'assets/js/nav.js' );
		mbf_enqueue_script( 'mbf-drawer', 'assets/js/drawer.js' );
	}

	// Block-library CSS only where the content actually has blocks (docs/05).
	if ( is_singular() && ! has_blocks() ) {
		wp_dequeue_style( 'wp-block-library' );
	}
}
add_action( 'wp_enqueue_scripts', 'mbf_enqueue' );

/**
 * Preload the body font, so text doesn't reflow when it arrives. theme.json writes the
 * @font-face rules; this only adds the hint, and only while the body font is still Archivo
 * (Style Settings can switch it, and a preload nothing uses is a console warning).
 */
function mbf_preload_fonts(): void {
	if ( ! str_contains( (string) wp_get_global_settings( [ 'custom', 'font', 'body' ] ), 'font-family--sans' ) ) {
		return;
	}

	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( get_theme_file_uri( 'assets/fonts/archivo-latin-wdth-normal.woff2' ) )
	);
}
add_action( 'wp_head', 'mbf_preload_fonts', 1 );

/**
 * The hotspot placer: click an image in the editor to set a spot's position. ACF admin
 * screens only. The front end never loads it.
 */
function mbf_enqueue_acf_admin(): void {
	mbf_enqueue_script( 'mbf-hotspots-admin', 'assets/js/hotspots-admin.js' );
}
add_action( 'acf/input/admin_enqueue_scripts', 'mbf_enqueue_acf_admin' );

function mbf_enqueue_script( string $handle, string $path ): void {
	wp_enqueue_script(
		$handle,
		get_theme_file_uri( $path ),
		[],
		(string) filemtime( get_theme_file_path( $path ) ),
		[ 'strategy' => 'defer', 'in_footer' => true ]
	);
}

// Emoji detection script and styles: every MBF site removes them (docs/05).
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
