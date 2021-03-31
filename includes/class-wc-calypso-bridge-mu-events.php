<?php
/**
 * Events that must be registered even without WooCommerce detected.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Mu Events
 */
class WC_Calypso_Bridge_Mu_Events {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Bridge_Themes_Setup instance
	 */
	protected static $instance = false;

	/**
	 * Get class instance
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
		add_action( 'woocommerce_admin_newly_installed', array( $this, 'maybe_create_wc_pages' ), 10, 2 );
	}

	/**
	 * Check WooCommerce pages (shop, cart, my-account, checkout) and create them if they don't exist.
	 */
	public function maybe_create_wc_pages() {
		global $wpdb;

		if ( ! class_exists( 'WC_Install' ) ) {
			return;
		}

		$post_count = $wpdb->get_var( "select count(*) from $wpdb->posts where post_name in ('shop', 'cart', 'my-account', 'checkout')" );

		if ( 4 !== (int) $post_count ) {
			// reset the woocommerce_*_page_id options.
			foreach ( [ 'shop', 'cart', 'myaccount', 'checkout' ] as $page ) {
				delete_option( "woocommerce_{$page}_page_id" );
			}
			WC_Install::create_pages();
		}
	}
}

$wc_calypso_bridge_must_use_events = WC_Calypso_Bridge_Mu_Events::get_instance();
