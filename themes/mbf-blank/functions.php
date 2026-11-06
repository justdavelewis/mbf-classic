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
require_once get_theme_file_path( 'inc/options.php' );
require_once get_theme_file_path( 'inc/builder.php' );
require_once get_theme_file_path( 'inc/finder.php' );
require_once get_theme_file_path( 'inc/forms.php' );
require_once get_theme_file_path( 'inc/schema.php' );
require_once get_theme_file_path( 'inc/style-settings.php' );
require_once get_theme_file_path( 'inc/enqueue.php' );
