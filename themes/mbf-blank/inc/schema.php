<?php
/**
 * The site's business node: ONE per page (05), built from Site Settings, so the header,
 * footer and schema read the same NAP and can't disagree.
 *
 * Off when an SEO plugin is active, because those output their own Organization node and
 * two would contradict each other (10: "Two business nodes in JSON-LD"). A fork whose SEO
 * plugin covers only some pages turns ours back on for the rest with the
 * `mbf_print_business_schema` filter.
 *
 * `Organization`, not `LocalBusiness`: the starter has no opening hours or geo. A fork with
 * a physical premises switches @type to the right LocalBusiness subtype and adds them.
 * BreadcrumbList / FAQPage / Article are per fork; the starter emits none.
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Whether a known SEO plugin is active (each outputs its own Organization node).
 */
function mbf_seo_plugin_active(): bool {
	return defined( 'WPSEO_VERSION' )          // Yoast SEO
		|| class_exists( 'RankMath' )          // Rank Math
		|| defined( 'SEOPRESS_VERSION' )       // SEOPress
		|| defined( 'AIOSEO_VERSION' );        // All in One SEO
}

/**
 * The Organization node from Site Settings. Empty values are left out, not sent blank.
 *
 * @return array<string, mixed>
 */
function mbf_business_schema(): array {
	$logo    = (int) mbf_setting( 'logo' );
	$address = trim( (string) mbf_setting( 'address' ) );
	$same_as = array_values(
		array_filter( array_map( static fn( $row ) => (string) ( $row['url'] ?? '' ), mbf_rows( mbf_setting( 'socials' ) ) ) )
	);

	$node = [
		'@context'  => 'https://schema.org',
		'@type'     => 'Organization',
		'@id'       => home_url( '/#organization' ),
		'name'      => mbf_company_name(),
		'url'       => home_url( '/' ),
		'logo'      => $logo ? (string) wp_get_attachment_image_url( $logo, 'full' ) : '',
		'telephone' => (string) mbf_setting( 'phone' ),
		'email'     => (string) mbf_setting( 'email' ),
		// One textarea in Site Settings, so one line of text (schema.org allows Text here).
		'address'   => '' !== $address ? (string) preg_replace( '/\s*\R\s*/', ', ', $address ) : '',
		'sameAs'    => $same_as,
	];

	return array_filter( $node, static fn( $value ) => '' !== $value && [] !== $value );
}

function mbf_print_business_schema(): void {
	if ( ! apply_filters( 'mbf_print_business_schema', ! mbf_seo_plugin_active() ) ) {
		return;
	}

	// Core's script tag printer, so `wp_inline_script_attributes` (a CSP nonce) applies here too.
	// JSON_HEX_TAG: a "</script>" inside any value can't close the tag early.
	wp_print_inline_script_tag(
		(string) wp_json_encode( mbf_business_schema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ),
		[ 'type' => 'application/ld+json' ]
	);
}
add_action( 'wp_head', 'mbf_print_business_schema' );
