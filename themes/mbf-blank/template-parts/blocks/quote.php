<?php
/**
 * Quote: one testimonial as <figure> + <blockquote> + <figcaption>.
 *
 * $args: quote, name, role + section controls.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a = wp_parse_args( $args, [ 'quote' => '', 'name' => '', 'role' => '' ] );

if ( '' === (string) $mbf_a['quote'] ) {
	return;
}

$mbf_cite = implode( ', ', array_filter( [ (string) $mbf_a['name'], (string) $mbf_a['role'] ] ) );
?>
<section <?php mbf_section_attrs( $mbf_a, '', [ 'mbf-quote' ] ); ?>>
	<div class="mbf-section__inner">
		<figure class="mbf-quote__figure">
			<blockquote class="mbf-quote__text">
				<p><?php echo esc_html( $mbf_a['quote'] ); ?></p>
			</blockquote>
			<?php if ( '' !== $mbf_cite ) : ?>
				<figcaption class="mbf-quote__cite"><?php echo esc_html( $mbf_cite ); ?></figcaption>
			<?php endif; ?>
		</figure>
	</div>
</section>
