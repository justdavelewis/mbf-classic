<?php
/**
 * The solution finder (inc/finder.php holds its data).
 *
 * The server prints real links to every recommendation: that IS the no-JS finder, and it's
 * what makes each one crawlable. assets/js/finder.js replaces it with the steps only once
 * it has parsed its data, so a script error leaves working links, never an empty panel.
 *
 * $args: context ('panel': a light card beside a hero. 'glass': frosted, beside a hero on a
 * dark ground. 'section': on the section's ground), level (heading level of the questions), show_question (false when the section heading
 * already is the first question).
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a      = wp_parse_args( $args, [ 'context' => 'section', 'level' => 3, 'show_question' => true ] );
$mbf_config = mbf_finder_config();

if ( ! $mbf_config ) {
	return;
}

mbf_enqueue_script( 'mbf-finder', 'assets/js/finder.js' );

$mbf_level   = max( 2, min( 6, (int) $mbf_a['level'] ) );
$mbf_tag     = "h{$mbf_level}";
$mbf_context = in_array( $mbf_a['context'], [ 'panel', 'glass' ], true ) ? $mbf_a['context'] : 'section';

// What the script needs, and every string it prints, so a fork translates in one place.
$mbf_data = $mbf_config + [
	'level'        => $mbf_level,
	'showQuestion' => (bool) $mbf_a['show_question'],
	'strings'      => [
		/* translators: 1: current step, 2: number of steps. */
		'step'     => __( 'Step %1$s of %2$s', 'mbf-blank' ),
		/* translators: %s: site type, e.g. "a warehouse". */
		'for'      => __( 'Our recommendation for %s', 'mbf-blank' ),
		'forAny'   => __( 'Our recommendation', 'mbf-blank' ),
		'readMore' => __( 'Read more', 'mbf-blank' ),
		'back'     => __( 'Back', 'mbf-blank' ),
		'restart'  => __( 'Start again', 'mbf-blank' ),
	],
];
?>
<div class="mbf-finder mbf-finder--<?php echo esc_attr( $mbf_context ); ?>" data-mbf-finder="<?php echo esc_attr( (string) wp_json_encode( $mbf_data ) ); ?>">
	<div class="mbf-finder__head">
		<?php if ( 'section' !== $mbf_context && '' !== $mbf_config['eyebrow'] ) : ?>
			<p class="mbf-eyebrow"><?php echo esc_html( $mbf_config['eyebrow'] ); ?></p>
		<?php endif; ?>

		<?php // Polite, and only this line: focus moves to each new question, which announces the rest. ?>
		<p class="mbf-finder__step" data-mbf-finder-step aria-live="polite"></p>
	</div>

	<?php // Filled by finder.js. Presentational: the step line says the same thing in words. ?>
	<div class="mbf-finder__bars" data-mbf-finder-bars aria-hidden="true"></div>

	<div class="mbf-finder__stage" data-mbf-finder-stage>
		<?php if ( $mbf_a['show_question'] ) : ?>
			<<?php echo $mbf_tag; // phpcs:ignore WordPress.Security.EscapeOutput -- h2–h6 built above ?> class="h4 mbf-finder__question"><?php echo esc_html( $mbf_config['question'] ); ?></<?php echo $mbf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<?php endif; ?>
		<ul class="mbf-finder__options" role="list">
			<?php foreach ( $mbf_config['options'] as $mbf_option ) : ?>
				<li>
					<a class="mbf-finder__option" href="<?php echo esc_url( $mbf_option['href'] ); ?>">
						<?php echo $mbf_option['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- built by mbf_icon() ?>
						<?php echo esc_html( $mbf_option['label'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
