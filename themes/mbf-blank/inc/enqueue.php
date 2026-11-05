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
