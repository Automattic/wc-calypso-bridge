<?php
/**
 * Replaces the "Extensions" screen with one where
 * installed extensions don't show in search results.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.6
 * @version 2.0.0
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
	 * Constructor.
	 */
	private function __construct() {

		// Only in Ecommerce.
		if ( ! wc_calypso_bridge_has_ecommerce_features() ) {
			return;
		}

		if ( ! is_admin() ) {
			return;
		}

		add_action( 'init', array( $this, 'init' ), 2 );
	}

	/**
	 * Initialize.
	 */
	public function init() {
		add_filter( 'woocommerce_show_addons_page', '__return_false' );
		add_action( 'admin_menu', array( $this, 'addons_menu' ), 70 );
		add_action( 'woocommerce_loaded', array( $this, 'load_modified_addons_menu' ) );

		// Add admin body class for the free trial landing page.
		if ( wc_calypso_bridge_is_ecommerce_trial_plan() ) {

			add_filter( 'admin_body_class', function( $classes ) {
				$screen = get_current_screen();
				if ( $screen && 'woocommerce_page_wc-addons' === $screen->id ) {
					$classes .= 'woocommerce_page_wc-addons-landing-page';
				}

				return $classes;
			} );
		}
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
		require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/includes/class-wc-calypso-bridge-addons-screen.php';
		WC_Calypso_Bridge_Addons_Screen::output();
	}
}

WC_Calypso_Bridge_Addons::get_instance();
