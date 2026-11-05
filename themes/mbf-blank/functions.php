<?php
/**
 * MBF Blank: loads inc/ only. Logic lives in the inc/ files.
 *
 * @package mbf-blank
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

require_once get_theme_file_path( 'inc/setup.php' );
require_once get_theme_file_path( 'inc/helpers.php' );
require_once get_theme_file_path( 'inc/enqueue.php' );
