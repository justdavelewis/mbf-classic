<?php
/**
 * Posts: the latest from a category, or hand-picked, as the one card.
 *
 * $args: heading, lead, source (latest|chosen), category, count, chosen (post IDs), link +
 * section controls.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a     = wp_parse_args( $args, [ 'heading' => '', 'lead' => '', 'source' => 'latest', 'category' => 0, 'count' => 3, 'chosen' => [], 'link' => null ] );
$mbf_query = [ 'post_type' => 'post', 'post_status' => 'publish', 'no_found_rows' => true, 'ignore_sticky_posts' => true, 'fields' => 'ids' ];

if ( 'chosen' === $mbf_a['source'] ) {
	$mbf_chosen = array_map( 'intval', mbf_rows( $mbf_a['chosen'] ) );

	if ( ! $mbf_chosen ) {
		return;
	}

	$mbf_query += [ 'post__in' => $mbf_chosen, 'orderby' => 'post__in', 'posts_per_page' => count( $mbf_chosen ) ];
} else {
	$mbf_query += [ 'posts_per_page' => max( 1, min( 12, (int) $mbf_a['count'] ) ), 'cat' => (int) $mbf_a['category'] ];

	// Not the post being read: a feed on a post shouldn't offer that same post.
	if ( is_singular( 'post' ) ) {
		$mbf_query['post__not_in'] = [ get_queried_object_id() ];
	}
}

$mbf_posts = get_posts( $mbf_query );

if ( ! $mbf_posts ) {
	return;
}

$mbf_id = '' !== (string) $mbf_a['heading'] ? mbf_section_id( $mbf_a['heading'] ) : '';
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-posts' ] ); ?>>
	<div class="mbf-section__inner">
		<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_a['heading'], 'lead' => $mbf_a['lead'], 'link' => $mbf_a['link'], 'id' => $mbf_id ] ); ?>
		<div class="<?php echo esc_attr( mbf_grid_class( count( $mbf_posts ) ) ); ?>">
			<?php
			foreach ( $mbf_posts as $mbf_post_id ) {
				get_template_part(
					'template-parts/card',
					null,
					[
						'title' => get_the_title( $mbf_post_id ),
						'text'  => get_the_excerpt( $mbf_post_id ),
						'url'   => (string) get_permalink( $mbf_post_id ),
						'image' => (int) get_post_thumbnail_id( $mbf_post_id ),
						'level' => '' !== (string) $mbf_a['heading'] ? 3 : 2,
						'cta'   => __( 'Read more', 'mbf-blank' ),
					]
				);
			}
			?>
		</div>
	</div>
</section>
