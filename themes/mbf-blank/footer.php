<?php
/**
 * Site footer. Reuses the secondary ground's measured family (.mbf-section--secondary)
 * rather than defining its own colours. NAP, logo and socials arrive from Site
 * Settings in phase 2.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<footer class="mbf-footer mbf-section mbf-section--secondary">
	<div class="mbf-section__inner">
		<p class="mbf-footer__legal">
			&copy; <?php echo esc_html( wp_date( 'Y' ) . ' ' . get_bloginfo( 'name' ) ); ?>
		</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
