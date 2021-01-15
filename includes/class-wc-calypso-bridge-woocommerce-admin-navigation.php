<?php
/**
 * Modify the navigation to fit the bridge needs.
 *
 * @package WC_Calypso_Bridge/Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_WooCommerce_Admin_Navigation
 */
class WC_Calypso_Bridge_WooCommerce_Admin_Navigation {

	/**
	 * Class Instance.
	 *
	 * @var WC_Calypso_Bridge_WooCommerce_Admin_Navigation instance
	 */
	protected static $instance = null;

	/**
	 * Get class instance
	 */
	public static function get_instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'woocommerce_navigation_root_back_url', array( $this, 'add_calypso_url' ) );
	}

	/**
	 * Add calypso URL.
	 */
	public static function add_calypso_url() {
		$strip_http = '/.*?:\/\//i';
		$site_slug  = preg_replace( $strip_http, '', get_home_url() );
		$site_slug  = str_replace( '/', '::', $site_slug );

		$store_url = 'https://wordpress.com/home/' . $site_slug;
		return $store_url;
	}
}

WC_Calypso_Bridge_WooCommerce_Admin_Navigation::get_instance();
