<?php
/**
 * Style Settings (ACF options page `mbf-style-settings`, admins only): colours, fonts,
 * content spacing, section spacing and corners, without touching code.
 *
 * ONE mechanism: saved choices override theme.json through `wp_theme_json_data_theme`, so
 * WordPress prints them as the same --wp--preset--* / --wp--custom--* variables that
 * src/tokens.css already reads, on the front end and in the editor. Defaults stay in
 * theme.json (the fork test still holds); an empty field means "theme default".
 *
 * Colours are checked before they're saved: every text, link, button and border pair from
 * theme.json.md must still meet WCAG AA on every ground, or nothing is saved and the failing
 * pair is named (standards §0: accessibility wins).
 *
 * The values live in the database, not git: developer defaults in theme.json, client choices
 * on live. The database only comes down (§8), so a local copy picks them up on a pull.
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/* --- Choices ---------------------------------------------------------------------------- */

const MBF_STYLE_SLOTS = [ 'base', 'contrast', 'primary', 'secondary', 'tertiary', 'accent' ];

/** Section spacing → --section-y multiplier. */
const MBF_SECTION_SCALES = [ 'compact' => '0.75', 'standard' => '1', 'roomy' => '1.25' ];

/** Corners → [ surface radius, control radius ]. Soft = the theme.json default. */
const MBF_CORNERS = [
	'square' => [ '0', '0' ],
	'soft'   => [ '12px', '6px' ],
	'round'  => [ '24px', '999px' ],   // 999px: buttons become pills
];

const MBF_CONTENT_GAPS = [ 'fluid-s', 'fluid-m', 'fluid-l', 'fluid-xl' ];

/**
 * theme.json as shipped (read from the file, not through WordPress, so the filter below
 * can't feed back into it).
 */
function mbf_theme_json_file(): array {
	static $data = null;

	if ( null === $data ) {
		$data = json_decode( (string) file_get_contents( get_theme_file_path( 'theme.json' ) ), true ) ?: [];
	}

	return $data;
}

/** The theme's default palette: slug => hex. */
function mbf_default_palette(): array {
	return array_column( mbf_theme_json_file()['settings']['color']['palette'] ?? [], 'color', 'slug' );
}

/** Installed font families: slug => name (theme.json fontFamilies). */
function mbf_font_choices(): array {
	return array_column( mbf_theme_json_file()['settings']['typography']['fontFamilies'] ?? [], 'name', 'slug' );
}

/** Normalise a picker value to #rrggbb, or '' when empty or invalid. */
function mbf_hex( mixed $value ): string {
	$value = strtolower( trim( (string) $value ) );

	if ( preg_match( '/^#([0-9a-f]{3})$/', $value, $m ) ) {
		$value = '#' . $m[1][0] . $m[1][0] . $m[1][1] . $m[1][1] . $m[1][2] . $m[1][2];
	}

	return preg_match( '/^#[0-9a-f]{6}$/', $value ) ? $value : '';
}

/**
 * The palette in use: theme defaults with any saved (or submitted) slots swapped in.
 *
 * @param array<string, string> $chosen slot => hex ('' = default).
 */
function mbf_resolved_palette( array $chosen ): array {
	$palette = mbf_default_palette();

	foreach ( MBF_STYLE_SLOTS as $slot ) {
		$hex = mbf_hex( $chosen[ $slot ] ?? '' );

		if ( '' !== $hex ) {
			$palette[ $slot ] = $hex;
		}
	}

	return $palette;
}

/** Saved colour choices: slot => hex or ''. */
function mbf_saved_colours(): array {
	$out = [];

	foreach ( MBF_STYLE_SLOTS as $slot ) {
		$out[ $slot ] = mbf_hex( mbf_setting( "colour_{$slot}" ) );
	}

	return $out;
}

/* --- Apply: Style Settings → theme.json --------------------------------------------------- */

/**
 * The theme.json `settings` overrides for the saved choices ([] when nothing is set).
 */
function mbf_style_overrides(): array {
	if ( ! function_exists( 'get_field' ) ) {
		return [];
	}

	$settings = [];
	$saved    = mbf_saved_colours();

	if ( array_filter( $saved ) ) {
		// WordPress replaces a preset list whole, so send the full palette, not just the changes.
		$palette = mbf_resolved_palette( $saved );
		$names   = array_column( mbf_theme_json_file()['settings']['color']['palette'] ?? [], 'name', 'slug' );

		$settings['color']['palette'] = array_map(
			static fn( string $slug ) => [ 'slug' => $slug, 'name' => $names[ $slug ] ?? ucfirst( $slug ), 'color' => $palette[ $slug ] ],
			array_keys( $palette )
		);
	}

	$fonts = mbf_font_choices();

	foreach ( [ 'body' => 'font_body', 'display' => 'font_display' ] as $role => $field ) {
		$slug = (string) mbf_setting( $field );

		if ( isset( $fonts[ $slug ] ) ) {
			$settings['custom']['font'][ $role ] = "var(--wp--preset--font-family--{$slug})";
		}
	}

	$gap = (string) mbf_setting( 'content_gap' );

	if ( in_array( $gap, MBF_CONTENT_GAPS, true ) ) {
		$settings['custom']['contentGap'] = "var(--wp--preset--spacing--{$gap})";
	}

	$scale = MBF_SECTION_SCALES[ (string) mbf_setting( 'section_spacing' ) ] ?? null;

	if ( null !== $scale ) {
		$settings['custom']['sectionScale'] = $scale;
	}

	$corners = MBF_CORNERS[ (string) mbf_setting( 'corners' ) ] ?? null;

	if ( null !== $corners ) {
		$settings['custom']['radius'] = [ 'surface' => $corners[0], 'control' => $corners[1] ];
	}

	return $settings;
}

add_filter(
	'wp_theme_json_data_theme',
	static function ( $theme_json ) {
		$settings = mbf_style_overrides();

		return $settings ? $theme_json->update_with( [ 'version' => 3, 'settings' => $settings ] ) : $theme_json;
	}
);

/**
 * Font selects: "Theme default (…)" first, then the fonts installed in the theme. Every
 * select defaults to `theme`, which overrides nothing, so pressing Update never pins a
 * value and a later theme.json change still reaches the site. (Without it, the first save
 * stored `font_body = sans` and froze it.)
 */
function mbf_font_select( array $field, string $role ): array {
	$fonts   = mbf_font_choices();
	$current = (string) ( mbf_theme_json_file()['settings']['custom']['font'][ $role ] ?? '' );
	$slug    = preg_match( '/font-family--([a-z0-9-]+)\)/', $current, $m ) ? $m[1] : '';

	/* translators: %s: the theme's default font name. */
	$field['choices'] = [ 'theme' => sprintf( __( 'Theme default (%s)', 'mbf-blank' ), $fonts[ $slug ] ?? $slug ) ] + $fonts;

	return $field;
}
add_filter( 'acf/load_field/key=field_mbf_style_font_body', static fn( array $field ) => mbf_font_select( $field, 'body' ) );
add_filter( 'acf/load_field/key=field_mbf_style_font_display', static fn( array $field ) => mbf_font_select( $field, 'display' ) );

/** After a save, drop WordPress's cached theme.json so the next request prints the new values. */
add_action(
	'acf/save_post',
	static function ( $post_id ): void {
		if ( 'options' === $post_id && isset( $_POST['acf']['field_mbf_style_colour_base'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- ACF verified the nonce
			wp_clean_theme_json_cache();
		}
	},
	20
);

/* --- Colour maths: what the browser does for color-mix(in oklch, …) ------------------------- */

/** #rrggbb → [ r, g, b ] in 0–1 (gamma-encoded sRGB). */
function mbf_srgb( string $hex ): array {
	return [ hexdec( substr( $hex, 1, 2 ) ) / 255, hexdec( substr( $hex, 3, 2 ) ) / 255, hexdec( substr( $hex, 5, 2 ) ) / 255 ];
}

function mbf_srgb_to_linear( float $c ): float {
	return $c <= 0.04045 ? $c / 12.92 : ( ( $c + 0.055 ) / 1.055 ) ** 2.4;
}

function mbf_linear_to_srgb( float $c ): float {
	$c = max( 0.0, min( 1.0, $c ) );

	return $c <= 0.0031308 ? 12.92 * $c : 1.055 * ( $c ** ( 1 / 2.4 ) ) - 0.055;
}

/** sRGB (0–1) → OKLCH [ L, C, H° ] (Björn Ottosson's OKLab). */
function mbf_oklch( array $rgb ): array {
	[ $r, $g, $b ] = array_map( 'mbf_srgb_to_linear', $rgb );

	$l = ( 0.4122214708 * $r + 0.5363325363 * $g + 0.0514459929 * $b ) ** ( 1 / 3 );
	$m = ( 0.2119034982 * $r + 0.6806995451 * $g + 0.1073969566 * $b ) ** ( 1 / 3 );
	$s = ( 0.0883024619 * $r + 0.2817188376 * $g + 0.6299787005 * $b ) ** ( 1 / 3 );

	$ok_l = 0.2104542553 * $l + 0.7936177850 * $m - 0.0040720468 * $s;
	$ok_a = 1.9779984951 * $l - 2.4285922050 * $m + 0.4505937099 * $s;
	$ok_b = 0.0259040371 * $l + 0.7827717662 * $m - 0.8086757660 * $s;

	$chroma = sqrt( $ok_a ** 2 + $ok_b ** 2 );

	return [ $ok_l, $chroma, fmod( rad2deg( atan2( $ok_b, $ok_a ) ) + 360, 360 ) ];
}

/** OKLCH → sRGB (0–1), clipped to the gamut. */
function mbf_oklch_to_srgb( array $lch ): array {
	[ $ok_l, $chroma, $hue ] = $lch;

	$ok_a = $chroma * cos( deg2rad( $hue ) );
	$ok_b = $chroma * sin( deg2rad( $hue ) );

	$l = ( $ok_l + 0.3963377774 * $ok_a + 0.2158037573 * $ok_b ) ** 3;
	$m = ( $ok_l - 0.1055613458 * $ok_a - 0.0638541728 * $ok_b ) ** 3;
	$s = ( $ok_l - 0.0894841775 * $ok_a - 1.2914855480 * $ok_b ) ** 3;

	return array_map(
		'mbf_linear_to_srgb',
		[
			4.0767416621 * $l - 3.3077115913 * $m + 0.2309699292 * $s,
			-1.2684380046 * $l + 2.6097574011 * $m - 0.3413193965 * $s,
			-0.0041960863 * $l - 0.7034186147 * $m + 1.7076147010 * $s,
		]
	);
}

/**
 * color-mix( in oklch, $a $pct%, $b ): shorter-arc hue; an achromatic colour's hue is
 * powerless, so it takes the other colour's (CSS Color 4).
 */
function mbf_mix( string $a, float $pct, string $b ): string {
	$x = mbf_oklch( mbf_srgb( $a ) );
	$y = mbf_oklch( mbf_srgb( $b ) );
	$w = $pct / 100;

	$powerless = 0.0004;   // below this chroma the hue is noise
	if ( $x[1] < $powerless ) {
		$x[2] = $y[2];
	}
	if ( $y[1] < $powerless ) {
		$y[2] = $x[2];
	}

	$delta = $y[2] - $x[2];
	if ( $delta > 180 ) {
		$delta -= 360;
	} elseif ( $delta < -180 ) {
		$delta += 360;
	}

	$rgb = mbf_oklch_to_srgb(
		[
			$x[0] * $w + $y[0] * ( 1 - $w ),
			$x[1] * $w + $y[1] * ( 1 - $w ),
			fmod( $x[2] + $delta * ( 1 - $w ) + 360, 360 ),
		]
	);

	return sprintf( '#%02x%02x%02x', (int) round( $rgb[0] * 255 ), (int) round( $rgb[1] * 255 ), (int) round( $rgb[2] * 255 ) );
}

/** WCAG 2.x contrast ratio of two #rrggbb colours. */
function mbf_contrast( string $a, string $b ): float {
	$lum = static function ( string $hex ): float {
		[ $r, $g, $bl ] = array_map( 'mbf_srgb_to_linear', mbf_srgb( $hex ) );

		return 0.2126 * $r + 0.7152 * $g + 0.0722 * $bl;
	};

	$x = $lum( $a );
	$y = $lum( $b );

	return ( max( $x, $y ) + 0.05 ) / ( min( $x, $y ) + 0.05 );
}

/* --- The contrast check ---------------------------------------------------------------------- */

/**
 * Every pair the components draw, from the palette + the same mixes src/tokens.css and
 * src/components/section.css use. Mirrors the table in theme.json.md.
 *
 * @return array<int, array{0: string, 1: string, 2: string, 3: float, 4: string}>
 *         [ label, foreground, background, minimum ratio, slot to blame ]
 */
function mbf_contrast_pairs( array $p ): array {
	$error  = '#b42318';                                   // --color-error (tokens.css; not an editor choice)
	$text   = mbf_mix( $p['contrast'], 75, $p['base'] );   // --color-text
	$muted  = mbf_mix( $p['contrast'], 62, $p['base'] );   // --color-text-muted
	$border = mbf_mix( $p['contrast'], 45, $p['base'] );   // --color-border
	$pairs  = [];

	// Ground names as Style Settings shows them, so each message is one whole translatable string.
	$grounds = [
		'base'     => __( 'Base', 'mbf-blank' ),
		'tertiary' => __( 'Tertiary', 'mbf-blank' ),
	];

	foreach ( $grounds as $ground => $label ) {
		$bg = $p[ $ground ];
		array_push(
			$pairs,
			/* translators: %s: a light background colour, e.g. "Base". */
			[ sprintf( __( 'Headings on %s', 'mbf-blank' ), $label ), $p['contrast'], $bg, 4.5, 'contrast' ],
			/* translators: %s: a light background colour, e.g. "Base". */
			[ sprintf( __( 'Body text on %s', 'mbf-blank' ), $label ), $text, $bg, 4.5, 'contrast' ],
			/* translators: %s: a light background colour, e.g. "Base". */
			[ sprintf( __( 'Muted text on %s', 'mbf-blank' ), $label ), $muted, $bg, 4.5, 'contrast' ],
			/* translators: %s: a light background colour, e.g. "Base". */
			[ sprintf( __( 'Field and toggle borders on %s', 'mbf-blank' ), $label ), $border, $bg, 3.0, 'contrast' ],
			/* translators: %s: a light background colour, e.g. "Base". */
			[ sprintf( __( 'Primary links and accents on %s', 'mbf-blank' ), $label ), $p['primary'], $bg, 4.5, 'primary' ],
			/* translators: %s: a light background colour, e.g. "Base". */
			[ sprintf( __( 'Form errors on %s', 'mbf-blank' ), $label ), $error, $bg, 4.5, $ground ]
		);
	}

	$hover = static fn( string $fill ) => mbf_mix( $fill, 85, $p['contrast'] );   // button hover: 15% contrast

	// A colour at this opacity over white. Plain alpha blending, as the browser does it, so not mbf_mix().
	$veil = static function ( string $hex, int $pct ): string {
		$out = '#';

		foreach ( str_split( ltrim( $hex, '#' ), 2 ) as $channel ) {
			$out .= sprintf( '%02x', (int) round( hexdec( $channel ) * $pct / 100 + 255 * ( 100 - $pct ) / 100 ) );
		}

		return $out;
	};

	array_push(
		$pairs,
		[ __( 'Button text on a Primary button', 'mbf-blank' ), $p['base'], $p['primary'], 4.5, 'primary' ],
		[ __( 'Button text on a hovered Primary button', 'mbf-blank' ), $p['base'], $hover( $p['primary'] ), 4.5, 'primary' ],
		// Secondary (dark) ground.
		[ __( 'Text on Secondary', 'mbf-blank' ), $p['base'], $p['secondary'], 4.5, 'secondary' ],
		[ __( 'Muted text on Secondary', 'mbf-blank' ), mbf_mix( $p['base'], 80, $p['secondary'] ), $p['secondary'], 4.5, 'secondary' ],
		[ __( 'Accent links and buttons on Secondary', 'mbf-blank' ), $p['accent'], $p['secondary'], 4.5, 'accent' ],
		[ __( 'Button text on an Accent button', 'mbf-blank' ), $p['secondary'], $p['accent'], 4.5, 'accent' ],
		[ __( 'Button text on a hovered Accent button', 'mbf-blank' ), $p['secondary'], $hover( $p['accent'] ), 4.5, 'accent' ],
		[ __( 'Form errors on Secondary', 'mbf-blank' ), mbf_mix( $error, 20, $p['base'] ), $p['secondary'], 4.5, 'secondary' ],
		// Primary ground.
		[ __( 'Text on Primary', 'mbf-blank' ), $p['base'], $p['primary'], 4.5, 'primary' ],
		[ __( 'Muted text on Primary', 'mbf-blank' ), mbf_mix( $p['base'], 90, $p['primary'] ), $p['primary'], 4.5, 'primary' ],
		[ __( 'Button text on a hovered button on Primary', 'mbf-blank' ), $p['primary'], $hover( $p['base'] ), 4.5, 'primary' ],
		[ __( 'Form errors on Primary', 'mbf-blank' ), mbf_mix( $error, 20, $p['base'] ), $p['primary'], 4.5, 'primary' ],
		// Hero photo, worst case: a pure white photo under the veil (--hero-veil, hero.css). The finder's glass only darkens this further.
		[ __( 'Accent text over a hero photo on Secondary', 'mbf-blank' ), $p['accent'], $veil( $p['secondary'], 80 ), 4.5, 'secondary' ],
		[ __( 'Text over a hero photo on Primary', 'mbf-blank' ), $p['base'], $veil( $p['primary'], 88 ), 4.5, 'primary' ]
	);

	return $pairs;
}

/**
 * Failing pairs for a palette: [ slot => [ messages ] ].
 */
function mbf_contrast_failures( array $palette ): array {
	$fails = [];

	foreach ( mbf_contrast_pairs( $palette ) as [ $label, $fg, $bg, $min, $slot ] ) {
		$ratio = mbf_contrast( $fg, $bg );

		if ( $ratio < $min ) {
			$fails[ $slot ][] = [ $ratio / $min, sprintf(
				/* translators: 1: pair, 2: measured ratio, 3: required ratio. */
				__( '%1$s: %2$s:1, needs %3$s:1.', 'mbf-blank' ),
				$label,
				number_format( floor( $ratio * 100 ) / 100, 2 ),
				rtrim( rtrim( number_format( $min, 1 ), '0' ), '.' )
			) ];
		}
	}

	// Worst first, three per field: enough to act on without a wall of text.
	foreach ( $fails as $slot => $list ) {
		usort( $list, static fn( $a, $b ) => $a[0] <=> $b[0] );
		$messages = array_column( array_slice( $list, 0, 3 ), 1 );

		if ( count( $list ) > 3 ) {
			/* translators: %d: number of further failing pairs. */
			$messages[] = sprintf( _n( 'And %d more pair.', 'And %d more pairs.', count( $list ) - 3, 'mbf-blank' ), count( $list ) - 3 );
		}

		$fails[ $slot ] = $messages;
	}

	return $fails;
}

/** Refuse to save a palette that fails AA anywhere. */
add_action(
	'acf/validate_save_post',
	static function (): void {
		$posted = $_POST['acf'] ?? []; // phpcs:ignore WordPress.Security.NonceVerification -- ACF verifies; values only measured

		if ( ! is_array( $posted ) || ! array_key_exists( 'field_mbf_style_colour_base', $posted ) ) {
			return;   // not the Style page
		}

		$chosen = [];
		foreach ( MBF_STYLE_SLOTS as $slot ) {
			$chosen[ $slot ] = mbf_hex( wp_unslash( $posted[ "field_mbf_style_colour_{$slot}" ] ?? '' ) );
		}

		foreach ( mbf_contrast_failures( mbf_resolved_palette( $chosen ) ) as $slot => $messages ) {
			acf_add_validation_error( "acf[field_mbf_style_colour_{$slot}]", implode( ' ', $messages ) );
		}
	}
);
