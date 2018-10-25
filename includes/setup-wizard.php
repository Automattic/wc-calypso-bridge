<?php
/**
 * Setup Wizard Class
 *
 * Extends the original WC setup wizard so we can add in new templates to the view.
 */

if ( ! defined( 'ABSPATH' ) || ! class_exists( 'WC_Admin_Setup_Wizard' ) ) {
	exit;
}

/**
 * WC_Admin_Setup_Wizard class.
 */
class WC_Calypso_Bridge_Admin_Setup_Wizard extends WC_Admin_Setup_Wizard {

    /**
	 * Hook in tabs.
	 */
	public function __construct() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			add_action( 'admin_init', array( $this, 'setup_wizard' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

    /**
	 * Setup Wizard Header.
	 */
	public function setup_wizard_header() {
		set_current_screen();
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php esc_html_e( 'WooCommerce &rsaquo; Setup Wizard', 'woocommerce' ); ?></title>
			<?php do_action( 'admin_head' ); ?>
			<?php do_action( 'admin_enqueue_scripts' ); ?>
			<?php wp_print_scripts( 'wc-setup' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_print_scripts' ); ?>
		</head>
		<body class="wc-setup wp-core-ui">
			<?php wp_admin_bar_render(); ?>
			<h1 id="wc-logo"><a href="https://woocommerce.com/"><img src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/woocommerce_logo.png" alt="WooCommerce" /></a></h1>
		<?php
    }
    
}

new WC_Calypso_Bridge_Admin_Setup_Wizard();
