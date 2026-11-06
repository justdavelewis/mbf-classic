<?php
/**
 * The solution finder's data: one set of answers per site (Site Settings › Solution finder),
 * shown in a hero panel and in the Solution finder section (docs/05). Generalised from
 * Mantech's hero wizard.
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * The finder as plain data, or [] when no option can lead anywhere (the callers then print
 * nothing). Keys go into the form page's query string, so they're limited to slugs.
 *
 * Every option carries `href`, the no-JS link: its recommended page, or the form page when
 * it has none ("Not sure yet"). An option with neither is dropped: it would be a dead end.
 *
 * @return array{}|array{eyebrow: string, question: string, question2: string, button: string, target: string, options: array, sites: array}
 */
function mbf_finder_config(): array {
	$target  = (string) mbf_setting( 'finder_target' );
	$options = [];

	foreach ( mbf_rows( mbf_setting( 'finder_options' ) ) as $row ) {
		$key   = sanitize_title( (string) ( $row['key'] ?? '' ) );
		$label = (string) ( $row['label'] ?? '' );
		$page  = (string) ( $row['page'] ?? '' );

		if ( '' === $key || '' === $label || ( '' === $page && '' === $target ) ) {
			continue;
		}

		$options[] = [
			'key'    => $key,
			'label'  => $label,
			'icon'   => mbf_icon( (string) ( $row['icon'] ?? '' ), 'mbf-finder__icon' ),
			'page'   => $page,
			'href'   => '' !== $page ? $page : add_query_arg( 'need', $key, $target ),
			'title'  => (string) ( ( $row['title'] ?? '' ) ?: $label ),
			'points' => array_values( array_filter( array_map( static fn( $point ) => (string) ( $point['text'] ?? '' ), mbf_rows( $row['points'] ?? [] ) ), 'strlen' ) ),
		];
	}

	if ( ! $options ) {
		return [];
	}

	$sites = [];

	foreach ( mbf_rows( mbf_setting( 'finder_sites' ) ) as $row ) {
		$key   = sanitize_title( (string) ( $row['key'] ?? '' ) );
		$label = (string) ( $row['label'] ?? '' );

		if ( '' !== $key && '' !== $label ) {
			$sites[] = [ 'key' => $key, 'label' => $label, 'phrase' => (string) ( $row['phrase'] ?? '' ) ];
		}
	}

	return [
		'eyebrow'   => (string) mbf_setting( 'finder_eyebrow' ),
		'question'  => (string) ( mbf_setting( 'finder_question' ) ?: __( 'What do you need help with?', 'mbf-blank' ) ),
		'question2' => $sites ? (string) mbf_setting( 'finder_question_2' ) : '',
		'button'    => (string) ( mbf_setting( 'finder_button' ) ?: __( 'Get in touch', 'mbf-blank' ) ),
		'target'    => $target,
		'options'   => $options,
		'sites'     => $sites,
	];
}
