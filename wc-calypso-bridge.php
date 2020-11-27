<?php
/**
 * Plugin Name: WooCommerce Calypso Bridge
 * Plugin URI: https://wordpress.com/
 * Description: A feature plugin to provide ux enhancments for users of Store on WordPress.com.
 * Version: 1.5.0
 * Author: Automattic
 * Author URI: https://wordpress.com/
 * Requires at least: 4.4
 * Tested up to: 5.4.2
 *
 * @package WC_Calypso_bridge
 */

// Return instead of exit to prevent phpcs errors.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if ( ! file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) {
	// No WooCommerce installed, we don't need this.
	return;
}

// Allow for wc-calypso-bridge to be installed as a traditional plugin.
if ( file_exists( WP_PLUGIN_DIR . '/wc-calypso-bridge/wc-calypso-bridge.php' ) ) {
	if ( WP_PLUGIN_DIR . '/wc-calypso-bridge/' !== plugin_dir_path( __FILE__ ) ) {
		// wc-calypso-bridge is already installed conventionally, exiting to avoid conflict.
		return;
	}
}

define( 'WC_CALYPSO_BRIDGE_CURRENT_VERSION', '1.5.0' );
define( 'WC_MIN_VERSION', '3.0.0' );

if ( ! function_exists( 'wc_calypso_bridge_is_ecommerce_plan' ) ) {
	/**
	 * Returns if a site is an eCommerce plan site or not.
	 * The `at_options` array is created during provisioning. Usually it is 'business' or 'ecommerce'
	 * To Test: update_option( 'at_options', array( 'plan_slug' => 'ecommerce' ) );
	 *
	 * @return bool True if the site is an ecommerce site.
	 */
	function wc_calypso_bridge_is_ecommerce_plan() {
		if ( class_exists( 'Atomic_Plan_Manager' ) && method_exists( 'Atomic_Plan_Manager', 'current_plan_slug' ) ) {
			return Atomic_Plan_Manager::current_plan_slug() === Atomic_Plan_Manager::ECOMMERCE_PLAN_SLUG;
		}

		return false;
	}
}

// Filters we want to add for ecommerce plan.
require_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-filters.php';
add_action( 'init', array( 'WC_Calypso_Bridge_Filters', 'get_instance' ) );

// We want to adjust tracks settings for business, ecomm, in calypsoified and wp-admin views.
require_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-tracks.php';
add_action( 'init', array( 'WC_Calypso_Bridge_Tracks', 'get_instance' ) );

// Also prevent Crowdsignal from redirecting during onboarding in all both wp-admin and calypsoified ecommerce plan.
require_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-crowdsignal-redirect.php';

if ( ! wc_calypso_bridge_is_ecommerce_plan() ) {
	include_once dirname( __FILE__ ) . '/store-on-wpcom/class-wc-calypso-bridge.php';
	return;
}

if ( ! function_exists( 'wc_calypso_bridge_init' ) ) {
	/**
	 * Loads language files for the plugin
	 */
	function wc_calypso_bridge_init() {
		$plugin_path = dirname( __FILE__ ) . '/languages';
		$locale      = apply_filters( 'plugin_locale', determine_locale(), 'wc-calypso-bridge' );
		$mofile      = $plugin_path . '/wc-calypso-bridge-' . $locale . '.mo';

		load_textdomain( 'wc-calypso-bridge', $mofile );
	}
}
add_action( 'plugins_loaded', 'wc_calypso_bridge_init' );

require_once dirname( __FILE__ ) . '/class-wc-calypso-bridge.php';

require_once dirname( __FILE__ ) . '/class-wc-calypso-bridge-frontend.php';

require_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-woocommerce-admin-features.php';
