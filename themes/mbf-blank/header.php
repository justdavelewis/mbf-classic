<?php
/**
 * Site header: brand | inline nav (wide) | drawer toggle (narrow).
 *
 * The nav ↔ drawer switch is one min-width breakpoint, stated in
 * src/components/site-header.css and copied once in assets/js/drawer.js.
 *
 * The menu renders twice, inline and in the drawer, from the same mbf_menu_tree(), so
 * the two can differ in presentation but never in content. Only one copy is ever
 * visible: the inline nav is display:none below the breakpoint, and the drawer is a
 * closed <dialog> above it.
 *
 * @package mbf-blank
 */

defined( 'ABSPATH' ) || exit;

$mbf_menu = mbf_menu_tree( 'primary' );
$mbf_name = get_bloginfo( 'name' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="mbf-skip" href="#main"><?php esc_html_e( 'Skip to content', 'mbf-blank' ); ?></a>

<header class="mbf-site-header">
	<div class="mbf-site-header__inner">

		<?php // A <p>, never an <h1>: each page's own heading is its h1. ?>
		<p class="mbf-site-header__brand">
			<a class="mbf-site-header__brand-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo esc_html( $mbf_name ); ?></a>
		</p>

		<?php if ( $mbf_menu ) : ?>
			<nav class="mbf-nav" aria-label="<?php esc_attr_e( 'Primary', 'mbf-blank' ); ?>">
				<?php mbf_the_menu( $mbf_menu, 'mbf-nav', 'mbf-nav' ); ?>
			</nav>

			<?php
			/*
			 * commandfor/command open the dialog with no JS where supported (docs/13 tier 2);
			 * drawer.js adds the fallback. A real <button>: showModal() gives the focus trap,
			 * Esc, the inert page and focus return for free.
			 */
			?>
			<button type="button" class="mbf-site-header__toggle" commandfor="mbf-drawer" command="show-modal" aria-haspopup="dialog">
				<span class="mbf-site-header__toggle-bars" aria-hidden="true"></span>
				<span class="sr-only"><?php esc_html_e( 'Menu', 'mbf-blank' ); ?></span>
			</button>
		<?php endif; ?>

	</div>

	<?php if ( $mbf_menu ) : ?>
		<?php // Dark ground (secondary): the white logo goes here once Site Settings exists (phase 2). ?>
		<dialog id="mbf-drawer" class="mbf-drawer mbf-section--secondary" closedby="any" aria-label="<?php esc_attr_e( 'Menu', 'mbf-blank' ); ?>">
			<div class="mbf-drawer__panel">
				<div class="mbf-drawer__head">
					<a class="mbf-drawer__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo esc_html( $mbf_name ); ?></a>
					<button type="button" class="mbf-drawer__close" commandfor="mbf-drawer" command="close">
						<span aria-hidden="true">&times;</span>
						<span class="sr-only"><?php esc_html_e( 'Close menu', 'mbf-blank' ); ?></span>
					</button>
				</div>
				<nav class="mbf-drawer__nav" aria-label="<?php esc_attr_e( 'Primary', 'mbf-blank' ); ?>">
					<?php mbf_the_menu( $mbf_menu, 'mbf-drawer' ); ?>
				</nav>
			</div>
		</dialog>
	<?php endif; ?>
</header>

<main id="main">
