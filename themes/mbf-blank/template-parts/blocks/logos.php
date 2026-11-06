<?php
/**
 * Logos: client or accreditation marks. Each image keeps its Media Library alt, which
 * should name the organisation (the field's instructions say so).
 *
 * $args: heading, logos (attachment IDs) + section controls.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a     = wp_parse_args( $args, [ 'heading' => '', 'logos' => [] ] );
$mbf_logos = array_filter( array_map( 'intval', mbf_rows( $mbf_a['logos'] ) ) );

if ( ! $mbf_logos ) {
	return;
}

$mbf_id = '' !== (string) $mbf_a['heading'] ? mbf_section_id( $mbf_a['heading'] ) : '';
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-logos' ] ); ?>>
	<div class="mbf-section__inner">
		<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_a['heading'], 'id' => $mbf_id ] ); ?>
		<ul class="mbf-logos__list" role="list">
			<?php foreach ( $mbf_logos as $mbf_logo ) : ?>
				<li class="mbf-logos__item"><?php echo wp_get_attachment_image( $mbf_logo, 'medium', false, [ 'class' => 'mbf-logos__image' ] ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
