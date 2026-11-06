<?php
/**
 * The default page header: crumbs, the page's h1, optional meta and lead. Used wherever a
 * page doesn't start with a `hero` section. flush-btm: the section after it supplies the gap.
 *
 * $args: title, lead, meta (plain text, e.g. a date), datetime (ISO, wraps meta in <time>),
 *        crumbs (from mbf_crumbs()).
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a = wp_parse_args( $args, [ 'title' => '', 'lead' => '', 'meta' => '', 'datetime' => '', 'crumbs' => [] ] );
?>
<div class="mbf-section mbf-section--flush-btm mbf-page-hero">
	<div class="mbf-section__inner">
		<?php get_template_part( 'template-parts/crumbs', null, [ 'items' => $mbf_a['crumbs'] ] ); ?>
		<div class="mbf-page-hero__text">
			<h1 class="h1"><?php echo esc_html( $mbf_a['title'] ); ?></h1>
			<?php if ( '' !== $mbf_a['meta'] ) : ?>
				<p class="mbf-meta">
					<?php if ( '' !== $mbf_a['datetime'] ) : ?>
						<time datetime="<?php echo esc_attr( $mbf_a['datetime'] ); ?>"><?php echo esc_html( $mbf_a['meta'] ); ?></time>
					<?php else : ?>
						<?php echo esc_html( $mbf_a['meta'] ); ?>
					<?php endif; ?>
				</p>
			<?php endif; ?>
			<?php if ( '' !== $mbf_a['lead'] ) : ?>
				<p class="lead"><?php echo esc_html( $mbf_a['lead'] ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</div>
