<?php
/**
 * Specifications: real tables (a <th> names each row), then the downloads that go with them.
 *
 * $args: heading, lead, groups (rows: heading, rows (rows: label, value)), files (rows: title,
 * file) + section controls.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_a      = wp_parse_args( $args, [ 'heading' => '', 'lead' => '', 'groups' => [], 'files' => [] ] );
$mbf_groups = array_filter( mbf_rows( $mbf_a['groups'] ), static fn( $group ) => (bool) mbf_rows( $group['rows'] ?? [] ) );

if ( ! $mbf_groups ) {
	return;
}

$mbf_id = '' !== (string) $mbf_a['heading'] ? mbf_section_id( $mbf_a['heading'] ) : '';
?>
<section <?php mbf_section_attrs( $mbf_a, $mbf_id, [ 'mbf-specs' ] ); ?>>
	<div class="mbf-section__inner">
		<?php get_template_part( 'template-parts/section-heading', null, [ 'heading' => $mbf_a['heading'], 'lead' => $mbf_a['lead'], 'id' => $mbf_id ] ); ?>
		<div class="mbf-specs__tables">
			<?php foreach ( $mbf_groups as $mbf_group ) : ?>
				<table class="mbf-specs__table">
					<?php if ( '' !== (string) ( $mbf_group['heading'] ?? '' ) ) : ?>
						<caption class="mbf-specs__caption"><?php echo esc_html( $mbf_group['heading'] ); ?></caption>
					<?php endif; ?>
					<tbody>
						<?php foreach ( mbf_rows( $mbf_group['rows'] ) as $mbf_row ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( $mbf_row['label'] ?? '' ); ?></th>
								<td><?php echo esc_html( $mbf_row['value'] ?? '' ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endforeach; ?>
		</div>
		<?php get_template_part( 'template-parts/file-list', null, [ 'files' => $mbf_a['files'] ] ); ?>
	</div>
</section>
