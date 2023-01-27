<?php
/**
 * Functions for determining dotCom features in Calypso Bridge.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_calypso_bridge_is_ecommerce_plan' ) ) {
	/**
	 * Returns if a site is an eCommerce plan site or not.
	 *
	 * @return bool True if the site is an ecommerce site.
	 */
	function wc_calypso_bridge_is_ecommerce_plan() {
		return WC_Calypso_Bridge_DotCom_Features::is_ecommerce_plan();
	}
}

if ( ! function_exists( 'wc_calypso_bridge_is_ecommerce_trial_plan' ) ) {
	/**
	 * Returns if a site is an eCommerce trial plan site or not.
	 *
	 * @return bool True if the site is an ecommerce trial site.
	 */
	function wc_calypso_bridge_is_ecommerce_trial_plan() {
		return WC_Calypso_Bridge_DotCom_Features::is_ecommerce_trial_plan();
	}
}

if ( ! function_exists( 'wc_calypso_bridge_is_business_plan' ) ) {

	/**
	 * Returns if a site is a Business plan site or not.
	 *
	 * @return bool True if the site is a business site.
	 */
	function wc_calypso_bridge_is_business_plan() {
		return WC_Calypso_Bridge_DotCom_Features::is_business_plan();
	}
}

/**
 * WC Calypso Bridge DotCom Features class.
 */
class WC_Calypso_Bridge_DotCom_Features {

	/**
	 * Is Ecommerce plan.
	 *
	 * @var bool
	 */
	protected static $is_ecommerce_plan = null;

	/**
	 * Is Business plan.
	 *
	 * @var bool
	 */
	protected static $is_business_plan = null;

	/**
	 * Is Ecommerce Trial plan.
	 *
	 * @var bool
	 */
	protected static $is_ecommerce_trial_plan = null;

	/**
	 * Determine if site is Ecommerce and cache it.
	 *
	 * @var bool
	 */
	public static function is_ecommerce_plan() {
		if ( is_null( self::$is_ecommerce_plan ) ) {
			self::$is_ecommerce_plan = (bool) function_exists( 'wpcom_site_has_feature' ) ? wpcom_site_has_feature( \WPCOM_Features::ECOMMERCE_MANAGED_PLUGINS ) : false;
		}

		return self::$is_ecommerce_plan;
	}

	/**
	 * Determine if site is Ecommerce Trial and cache it.
	 *
	 * @var bool
	 */
	public static function is_ecommerce_trial_plan() {
		if ( is_null( self::$is_ecommerce_trial_plan ) ) {
			self::$is_ecommerce_trial_plan = self::is_ecommerce_plan() && (bool) function_exists( 'wpcom_site_has_feature' ) ? wpcom_site_has_feature( \WPCOM_Features::INSTALL_PLUGINS ) : false;
		}

		return self::$is_ecommerce_trial_plan;
	}

	/**
	 * Determine if site is Business and cache it.
	 *
	 * @var bool
	 */
	public static function is_business_plan() {
		if ( is_null( self::$is_business_plan ) ) {
			self::$is_business_plan = (bool) function_exists( 'wpcom_site_has_feature' ) ? wpcom_site_has_feature( \WPCOM_Features::CONCIERGE_BUSINESS ) : false;
		}

		return self::$is_business_plan;
	}
}
