<?php
/**
 * Functions for determining dotCom features in Calypso Bridge.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.0.0
 * @version 2.1.3
 */

defined( 'ABSPATH' ) || exit;

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

if ( ! function_exists( 'wc_calypso_bridge_has_ecommerce_features' ) ) {
	/**
	 * Returns if a site is an eCommerce plan site or not.
	 *
	 * @return bool True if the site is an ecommerce site.
	 */
	function wc_calypso_bridge_has_ecommerce_features() {
		return WC_Calypso_Bridge_DotCom_Features::has_ecommerce_features();
	}
}

if ( ! function_exists( 'wc_calypso_bridge_is_ecommerce_plan' ) ) {
	/**
	 * Returns if a site is an eCommerce paid plan site or not.
	 *
	 * @return bool True if the site is an paid ecommerce site.
	 */
	function wc_calypso_bridge_is_ecommerce_plan() {
		return WC_Calypso_Bridge_DotCom_Features::is_ecommerce_plan();
	}
}

if ( ! function_exists( 'wc_calypso_bridge_is_wpcom_ecommerce_plan' ) ) {
	/**
	 * Returns if a site is an eCommerce plan site from dotCom or not.
	 *
	 * @since 2.1.3
	 *
	 * @return bool True if the site is an ecommerce from dotCom site.
	 */
	function wc_calypso_bridge_is_wpcom_ecommerce_plan() {
		return WC_Calypso_Bridge_DotCom_Features::is_wpcom_ecommerce_plan();
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

if ( ! function_exists( 'wc_calypso_bridge_is_ecommerce_small_plan' ) ) {
	/**
	 * Returns if a site is an Small eCommerce plan (Woo Express Essential) site or not.
	 *
	 * @since 2.1.3
	 *
	 * @return bool True if the site is an small ecommerce site.
	 */
	function wc_calypso_bridge_is_ecommerce_small_plan() {
		return WC_Calypso_Bridge_DotCom_Features::is_ecommerce_small_plan();
	}
}

if ( ! function_exists( 'wc_calypso_bridge_is_ecommerce_medium_plan' ) ) {
	/**
	 * Returns if a site is an Medium eCommerce plan (Woo Express Performance) site or not.
	 *
	 * @since 2.1.3
	 *
	 * @return bool True if the site is an medium ecommerce site.
	 */
	function wc_calypso_bridge_is_ecommerce_medium_plan() {
		return WC_Calypso_Bridge_DotCom_Features::is_ecommerce_medium_plan();
	}
}

/**
 * WC Calypso Bridge DotCom Features class.
 */
class WC_Calypso_Bridge_DotCom_Features {

	/**
	 * Has Ecommerce features.
	 *
	 * This boolean will be true for all ecommerce plans.
	 *
	 * @var bool
	 */
	protected static $has_ecommerce_features = null;

	/**
	 * Is a paid Ecommerce plan.
	 *
	 * @var bool
	 */
	protected static $is_ecommerce_plan = null;

	/**
	 * Is an Ecommerce plan from dotCom.
	 *
	 * @since 2.1.3
	 *
	 * @var bool
	 */
	protected static $is_wpcom_ecommerce_plan = null;

	/**
	 * Is Ecommerce Trial plan.
	 *
	 * @var bool
	 */
	protected static $is_ecommerce_trial_plan = null;

	/**
	 * Is Ecommerce Small (Woo Express Essential) plan.
	 *
	 * @since 2.1.3
	 *
	 * @var bool
	 */
	protected static $is_ecommerce_small_plan = null;

	/**
	 * Is Ecommerce Medium (Woo Express Performance) plan.
	 *
	 * @since 2.1.3
	 *
	 * @var bool
	 */
	protected static $is_ecommerce_medium_plan = null;

	/**
	 * Is Business plan.
	 *
	 * @var bool
	 */
	protected static $is_business_plan = null;

	/**
	 * Determine if site is Ecommerce and cache it.
	 *
	 * @var bool
	 */
	public static function has_ecommerce_features() {
		if ( is_null( self::$has_ecommerce_features ) ) {
			self::$has_ecommerce_features = wpcom_site_has_feature( \WPCOM_Features::ECOMMERCE_MANAGED_PLUGINS );
		}

		return self::$has_ecommerce_features;
	}

	/**
	 * Determine if site is a paid Ecommerce plan and cache it (refers all ecommerce plans from dotCom or WCCOM).
	 *
	 * @var bool
	 */
	public static function is_ecommerce_plan() {
		if ( is_null( self::$is_ecommerce_plan ) ) {
			self::$is_ecommerce_plan = self::has_ecommerce_features() && (
				wpcom_site_has_feature( \WPCOM_Features::ECOMMERCE_MANAGED_PLUGINS_SMALL ) || wpcom_site_has_feature( \WPCOM_Features::ECOMMERCE_MANAGED_PLUGINS_MEDIUM )
			);
		}

		return self::$is_ecommerce_plan;
	}

	/**
	 * Determine if site is Ecommerce from dotCom and cache it.
	 *
	 * @since 2.1.3
	 *
	 * @var bool
	 */
	public static function is_wpcom_ecommerce_plan() {
		if ( is_null( self::$is_wpcom_ecommerce_plan ) ) {

			self::$is_wpcom_ecommerce_plan = false;
			$all_site_purchases      = wpcom_get_site_purchases();
			$plan_purchases          = array_filter(
				$all_site_purchases,
				function ( $purchase ) {
					return 'bundle' === $purchase->product_type;
				}
			);

			if ( 1 === count( $plan_purchases ) ) {
				// We have exactly one plan
				$plan_purchase = reset( $plan_purchases );
				if ( 'wp-bundle-ecommerce' === $plan_purchase->billing_product_slug ) {
					self::$is_wpcom_ecommerce_plan = self::has_ecommerce_features();
				}
			}
		}

		return self::$is_wpcom_ecommerce_plan;
	}

	/**
	 * Determine if site is Small Ecommerce (Woo Express Essential) and cache it.
	 *
	 * @since 2.1.3
	 *
	 * @var bool
	 */
	public static function is_ecommerce_small_plan() {
		if ( is_null( self::$is_ecommerce_small_plan ) ) {

			self::$is_ecommerce_small_plan = false;
			if ( ! function_exists( 'wpcom_get_site_purchases' ) ) {
				return self::$is_ecommerce_small_plan;
			}

			$all_site_purchases = wpcom_get_site_purchases();
			$plan_purchases     = array_filter(
				$all_site_purchases,
				function ( $purchase ) {
					return 'bundle' === $purchase->product_type;
				}
			);

			if ( 1 === count( $plan_purchases ) ) {
				// We have exactly one plan
				$plan_purchase = reset( $plan_purchases );
				if ( 'wp-bundle-wooexpress-small' === $plan_purchase->billing_product_slug ) {
					self::$is_ecommerce_small_plan = self::has_ecommerce_features();
				}
			}
		}

		return self::$is_ecommerce_small_plan;
	}

	/**
	 * Determine if site is Medium Ecommerce (Woo Express Performance) and cache it.
	 *
	 * @since 2.1.3
	 *
	 * @var bool
	 */
	public static function is_ecommerce_medium_plan() {
		if ( is_null( self::$is_ecommerce_medium_plan ) ) {

			self::$is_ecommerce_medium_plan = false;
			if ( ! function_exists( 'wpcom_get_site_purchases' ) ) {
				return self::$is_ecommerce_medium_plan;
			}

			$all_site_purchases = wpcom_get_site_purchases();
			$plan_purchases     = array_filter(
				$all_site_purchases,
				function ( $purchase ) {
					return 'bundle' === $purchase->product_type;
				}
			);

			if ( 1 === count( $plan_purchases ) ) {
				// We have exactly one plan
				$plan_purchase = reset( $plan_purchases );
				if ( in_array( $plan_purchase->billing_product_slug, array( 'wp-bundle-wooexpress-medium', 'wp-bundle-wooexpress-medium-yearly' ) ) ) { // This was a bug. We include both to be on the safe side.
					self::$is_ecommerce_medium_plan = self::has_ecommerce_features();
				}
			}
		}

		return self::$is_ecommerce_medium_plan;
	}

	/**
	 * Determine if site is Ecommerce Trial and cache it.
	 *
	 * @var bool
	 */
	public static function is_ecommerce_trial_plan() {
		if ( is_null( self::$is_ecommerce_trial_plan ) ) {
			self::$is_ecommerce_trial_plan = self::has_ecommerce_features() && wpcom_site_has_feature( \WPCOM_Features::ECOMMERCE_MANAGED_PLUGINS_TRIAL );
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
			self::$is_business_plan = wpcom_site_has_feature( \WPCOM_Features::CONCIERGE_BUSINESS );
		}

		return self::$is_business_plan;
	}
}
