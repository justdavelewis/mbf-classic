<?php
/**
 * A list of downloads. The link names the file and states its type and size up front.
 *
 * $args: files (rows: title, file attachment ID, note, cover attachment ID).
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_files = array_filter( mbf_rows( $args['files'] ?? [] ), static fn( $row ) => '' !== (string) ( $row['title'] ?? '' ) && ! empty( $row['file'] ) && wp_get_attachment_url( (int) $row['file'] ) );

if ( ! $mbf_files ) {
	return;
}
?>
<ul class="mbf-files" role="list">
	<?php foreach ( $mbf_files as $mbf_file ) : ?>
		<li class="mbf-file">
			<?php
			if ( ! empty( $mbf_file['cover'] ) ) {
				echo wp_get_attachment_image( (int) $mbf_file['cover'], 'medium', false, [ 'alt' => '', 'aria-hidden' => 'true', 'class' => 'mbf-file__cover' ] );
			}
			?>
			<div class="mbf-file__body">
				<a class="mbf-file__link" href="<?php echo esc_url( (string) wp_get_attachment_url( (int) $mbf_file['file'] ) ); ?>" download>
					<?php echo esc_html( $mbf_file['title'] ); ?>
					<span class="mbf-file__meta">(<?php echo esc_html( mbf_file_meta( (int) $mbf_file['file'] ) ); ?>)</span>
				</a>
				<?php if ( '' !== (string) ( $mbf_file['note'] ?? '' ) ) : ?>
					<p class="mbf-file__note"><?php echo esc_html( $mbf_file['note'] ); ?></p>
				<?php endif; ?>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
