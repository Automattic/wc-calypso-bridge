<?php
/**
 * Adds the functionality needed to bridge WooCommerce.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.1.7
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC EComm Bridge
 */
class WC_EComm_Bridge {

	/**
	 * Class Instance.
	 *
	 * @var WC_EComm_Bridge instance
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

		add_filter( 'woocommerce_admin_features_to_enable_disable', array( $this, 'filter_wc_admin_enabled_features' ) );
	}

	/**
	 * Set feature flags for WooCommerce Admin front end at run time.
	 *
	 * @param array $features List of features 'feature' => bool indicating whether the feature is enabled.
	 * @return array
	 */
	public function filter_wc_admin_enabled_features( $features ) {
		$features['homepage']  = false;

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

WC_EComm_Bridge::get_instance();
