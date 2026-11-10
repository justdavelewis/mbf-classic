<?php
/**
 * Hotspots data: one set of scenes per site (Site Settings › Hotspots), added to a page by the
 * Hotspots section, which only chooses which scenes to show (David, 2026-11-10: "this block
 * should not be customisable in ACF, its just one thing to add in on a page").
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * The site's scenes, each spot resolved: its own title, text and link win; otherwise its
 * solution's (mbf_finder_solution()). `image` is the linked page's featured image, for the
 * popup. Scenes without an image, and spots still without a title, are dropped.
 *
 * @return array{heading: string, lead: string, scenes: list<array{key: string, label: string, image: int, spots: list<array{title: string, text: string, url: string, image: int, x: float, y: float}>}>}
 */
function mbf_hotspots_config(): array {
	static $config = null;

	if ( null !== $config ) {
		return $config;
	}

	$scenes = [];

	foreach ( mbf_rows( mbf_setting( 'hotspot_scenes' ) ) as $i => $scene ) {
		if ( empty( $scene['image'] ) ) {
			continue;
		}

		$spots = [];

		foreach ( mbf_rows( $scene['spots'] ?? [] ) as $spot ) {
			$solution = mbf_finder_solution( (string) ( $spot['solution'] ?? '' ) );
			$title    = (string) ( ( $spot['title'] ?? '' ) ?: ( $solution['title'] ?? '' ) );
			$url      = (string) ( ( $spot['url'] ?? '' ) ?: ( $solution['page'] ?? '' ) );

			if ( '' === $title ) {
				continue;
			}

			$spots[] = [
				'title' => $title,
				'text'  => (string) ( ( $spot['text'] ?? '' ) ?: ( $solution['label'] ?? '' ) ),
				'url'   => $url,
				'image' => '' !== $url ? (int) get_post_thumbnail_id( url_to_postid( $url ) ) : 0,
				'x'     => max( 0, min( 100, (float) ( $spot['x'] ?? 0 ) ) ),
				'y'     => max( 0, min( 100, (float) ( $spot['y'] ?? 0 ) ) ),
			];
		}

		$label    = (string) ( $scene['label'] ?? '' );
		$scenes[] = [
			'key'   => sanitize_title( $label ) ?: "scene-{$i}",
			'label' => $label,
			'image' => (int) $scene['image'],
			'spots' => $spots,
		];
	}

	$config = [
		'heading' => (string) mbf_setting( 'hotspot_heading' ),
		'lead'    => (string) mbf_setting( 'hotspot_lead' ),
		'scenes'  => $scenes,
	];

	return $config;
}

// The section's "Scenes to show" lists the site's scenes, live.
add_filter(
	'acf/load_field/key=field_mbf_hotspots_show_scenes',
	static function ( array $field ): array {
		$field['choices'] = [];

		foreach ( mbf_hotspots_config()['scenes'] as $scene ) {
			$field['choices'][ $scene['key'] ] = '' !== $scene['label'] ? $scene['label'] : $scene['key'];
		}

		return $field;
	}
);
