<?php
/**
 * Replaces the "Extensions" screen with one where
 * installed extensions don't show in search results.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.6
 * @version 1.0.6
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Addons
 */
class WC_Calypso_Bridge_Addons {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Addons instance
	 */
	protected static $instance = false;

	/**
	 * Get class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Hook into WordPress's menu system.
	 */
	public function __construct() {
		add_filter( 'woocommerce_show_addons_page', '__return_false' );
		add_action( 'admin_menu', array( $this, 'addons_menu' ), 70 );
		add_action( 'woocommerce_loaded', array( $this, 'load_modified_addons_menu' ) );
	}

	/**
	 * Load the class for the modified Addons menu.
	 */
	public function load_modified_addons_menu() {
		require 'class-wc-modified-admin-addons.php';
	}

	/**
	 * Addons menu item.
	 */
	public function addons_menu() {
		$count_html = WC_Helper_Updater::get_updates_count_html();
		/* translators: %s: extensions count */
		$menu_title = sprintf( __( 'Extensions %s', 'wc-calypso-bridge' ), $count_html );
		add_submenu_page( 'woocommerce', __( 'WooCommerce extensions', 'wc-calypso-bridge' ), $menu_title, 'manage_woocommerce', 'wc-addons', array( $this, 'addons_page' ) );
	}

	/**
	 * Init the addons page.
	 */
	public function addons_page() {
		WC_Calypso_Bridge_Addons_Screen::output();
	}
}
$wc_calypso_bridge_addons = WC_Calypso_Bridge_Addons::get_instance();
