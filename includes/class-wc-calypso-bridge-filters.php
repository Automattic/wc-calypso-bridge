<?php
/**
 * Filters for the ecommerce plan.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.1.6
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Filters
 */
class WC_Calypso_Bridge_Filters {

	/**
	 * Class instance.
	 *
	 * @var WC_Calypso_Filters instance
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
		add_action( 'woocommerce_admin_onboarding_industries', array( $this, 'remove_not_allowed_industries' ), 10, 1 );

		// Turn off email notifications.
		//add_filter( 'pre_option_woocommerce_merchant_email_notifications', array( $this, 'disable_email_notes' ) );
	}

	/**
	 * Remove `CBD and other hemp-derived products` option from industries list
	 *
	 * @param  array $industries Array of industries.
	 * @return array
	 */
	public function remove_not_allowed_industries( $industries ) {
		if ( isset( $industries['cbd-other-hemp-derived-products'] ) ) {
			unset( $industries['cbd-other-hemp-derived-products'] );
		} else {
			$industries = array_filter( $industries, array( $this, 'filter_industries' ) );
		}
		return $industries;
	}

	/**
	 * Filter method for industries to remove `CBD and other hemp-derived products` option.
	 *
	 * @param  array $industry Array of industries.
	 * @return boolean
	 */
	public function filter_industries( $industry ) {
		return 'cbd-other-hemp-derived-products' !== $industry['slug'];
	}

	/**
	 * Disable email based notifications.
	 */
	function disable_email_notes(){
		return 'no';
	}
}

$wc_calypso_bridge_filters = WC_Calypso_Bridge_Filters::get_instance();
