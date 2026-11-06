<?php
/**
 * Site Settings (ACF options page `mbf-site-settings`, defined in acf-json/). Read through
 * mbf_setting() everywhere, so the header, footer and schema can't disagree (05).
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * One Site Settings value. Null when ACF is inactive, so callers degrade to the site title.
 */
function mbf_setting( string $name ): mixed {
	return function_exists( 'get_field' ) ? get_field( $name, 'option' ) : null;
}

function mbf_company_name(): string {
	$name = (string) mbf_setting( 'company_name' );

	return '' !== $name ? $name : get_bloginfo( 'name' );
}

/**
 * The logo for a ground: the white logo on dark grounds (falling back to the main logo),
 * the main logo elsewhere. Empty when neither is set; callers print the name instead.
 */
function mbf_logo( string $ground, string $class ): string {
	$id = (int) mbf_setting( 'logo' );

	if ( mbf_is_dark_ground( $ground ) && (int) mbf_setting( 'logo_white' ) ) {
		$id = (int) mbf_setting( 'logo_white' );
	}

	if ( ! $id ) {
		return '';
	}

	// alt = the company name: the logo is the link's only content, so it names the link.
	return (string) wp_get_attachment_image( $id, 'medium', false, [ 'class' => $class, 'alt' => mbf_company_name() ] );
}

/**
 * tel: href from a display phone number ("01234 567 890" → "tel:01234567890").
 */
function mbf_tel_href( string $phone ): string {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $phone );
}
