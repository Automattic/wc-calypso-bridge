<?php
/**
 * Control wc-admin features in the eCommerce Plan.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.3.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC EComm Bridge
 */
class WC_Calypso_Bridge_WooCommerce_Admin_Features {

	/**
	 * Class Instance.
	 *
	 * @var WC_Calypso_Bridge_WooCommerce_Admin_Features instance
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'initialize' ), 2 );
	}

	/**
	 * Add hooks and filters if WooCommerce is active.
	 */
	public function initialize() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		add_filter( 'woocommerce_admin_features', array( $this, 'filter_wc_admin_enabled_features' ) );
	}

	/**
	 * Set feature flags for WooCommerce Admin front end at run time.
	 *
	 * @param array $features Array of woocommerce-admin features that are enabled by default for the current env.
	 * @return array
	 */
	public function filter_wc_admin_enabled_features( $features ) {
		if ( ! array_key_exists( 'remote-inbox-notifications', $features ) ) {
			$features[] = 'remote-inbox-notifications';
		}

		if ( ! array_key_exists( 'navigation', $features ) && 'yes' === get_option( 'woocommerce_navigation_enabled', 'yes' ) ) {
			$features[] = 'navigation';
		}

		return $features;
	}

	/**
	 * Get class instance
	 */
	public static function get_instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}
}

WC_Calypso_Bridge_WooCommerce_Admin_Features::get_instance();
