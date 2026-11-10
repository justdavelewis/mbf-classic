<?php
/**
 * Theme supports and menus. Exactly the docs/12 set: nothing "just in case".
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

function mbf_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	// No comment-form / comment-list: the starter has no comments template (STATUS D8).
	add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	// Editor-only build: tokens + prose on the canvas, no Preflight (src/editor.css).
	add_editor_style( 'dist/editor.css' );

	register_nav_menus(
		[
			'primary' => __( 'Primary', 'mbf-blank' ),
			'footer'  => __( 'Footer (legal links)', 'mbf-blank' ),   // top level only: a flat row in the footer
		]
	);

	// Card images: 16:10, cropped, so every card in a row has the same image box (03).
	add_image_size( 'card', 800, 500, true );
}
add_action( 'after_setup_theme', 'mbf_setup' );
