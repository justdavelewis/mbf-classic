<?php
/**
 * The one card (03, standards §4). Every linked unit uses it.
 *
 * One <a> wraps only the title text and is stretched over the card with ::before, so the
 * card is one tab stop and its link name is exactly the title. The "Learn more" is an
 * aria-hidden <span>, never a second link. The image is decorative (the title names the
 * card). With no URL there's no link, no ::before and no lift.
 *
 * $args: title, text, url, image (attachment ID), level (heading level, by outline: 3 under
 * a section h2, 2 under an archive h1), cta (visible CTA text), tag (small label above the
 * title, read before it), icon (Font Awesome name, shown only when there's no image).
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a = wp_parse_args(
	$args,
	[ 'title' => '', 'text' => '', 'url' => '', 'image' => 0, 'level' => 3, 'cta' => __( 'Learn more', 'mbf-blank' ), 'tag' => '', 'icon' => '' ]
);

if ( '' === (string) $mbf_a['title'] ) {
	return;
}

$mbf_tag = 'h' . max( 2, min( 6, (int) $mbf_a['level'] ) );
$mbf_url = (string) $mbf_a['url'];
$mbf_icon = $mbf_a['image'] ? '' : mbf_icon( (string) $mbf_a['icon'], 'mbf-card__icon' );
$mbf_tag_text = (string) $mbf_a['tag'];
?>
<article class="mbf-card">
	<?php
	if ( $mbf_a['image'] ) {
		// alt="" + aria-hidden: decorative. Built as a full array: 'aria-hidden' => null would print aria-hidden="" (03).
		echo wp_get_attachment_image( (int) $mbf_a['image'], 'card', false, [ 'alt' => '', 'aria-hidden' => 'true', 'class' => 'mbf-card__image' ] );
	}
	?>
	<div class="mbf-card__body">
		<?php if ( $mbf_icon || '' !== $mbf_tag_text ) : ?>
			<?php // One subgrid row with the title, so tagged and plain cards still align (card.css). ?>
			<div class="mbf-card__head">
				<?php echo $mbf_icon; // phpcs:ignore WordPress.Security.EscapeOutput -- built by mbf_icon() ?>
				<?php if ( '' !== $mbf_tag_text ) : ?>
					<span class="mbf-card__tag"><?php echo esc_html( $mbf_tag_text ); ?></span>
				<?php endif; ?>
		<?php endif; ?>
		<<?php echo $mbf_tag; // phpcs:ignore WordPress.Security.EscapeOutput -- h2–h6 built above ?> class="mbf-card__title">
			<?php if ( '' !== $mbf_url ) : ?>
				<a class="mbf-card__hit" href="<?php echo esc_url( $mbf_url ); ?>"><?php echo esc_html( $mbf_a['title'] ); ?></a>
			<?php else : ?>
				<?php echo esc_html( $mbf_a['title'] ); ?>
			<?php endif; ?>
		</<?php echo $mbf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<?php if ( $mbf_icon || '' !== $mbf_tag_text ) : ?>
			</div>
		<?php endif; ?>
		<?php if ( '' !== (string) $mbf_a['text'] ) : ?>
			<p class="mbf-card__text"><?php echo esc_html( $mbf_a['text'] ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== $mbf_url ) : ?>
			<span class="mbf-card__cta" aria-hidden="true"><?php echo esc_html( $mbf_a['cta'] ); ?> &rarr;</span>
		<?php endif; ?>
	</div>
</article>
