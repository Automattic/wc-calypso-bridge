<?php
/**
 * Control wc-admin features in the eCommerce Plan.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.9.4
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
		add_filter( 'woocommerce_admin_get_feature_config', array( $this, 'filter_woocommerce_admin_features' ), PHP_INT_MAX );
	}

	/**
	 * Set feature flags for WooCommerce Admin front end at run time.
	 *
	 * @param array $features Array of wc-calypso-bridge features that are enabled by default for the current env.
	 *
	 * @return array
	 */
	public function filter_wc_admin_enabled_features( $features ) {
		if ( ! in_array( 'remote-inbox-notifications', $features, true ) ) {
			$features[] = 'remote-inbox-notifications';
		}

		return $features;
	}

	/**
	 * Enable/disable features for WooCommerce Admin .
	 *
	 * @param array $features Array containing all wc-calypso-bridge features (enabled and disabled).
	 *
	 * @return array
	 */
	public function filter_woocommerce_admin_features( $features ) {

		// Disable and revert the navigation experiment.
		if ( array_key_exists( 'navigation', $features ) ) {
			$features[ 'navigation' ] = false;
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
