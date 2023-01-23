<?php
/**
 * Plugin Name: WooCommerce Calypso Bridge
 * Plugin URI: https://wordpress.com/
 * Description: A feature plugin to provide ux enhancments for users of Store on WordPress.com.
 * Version: 1.9.17
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

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$wc_plugin_path = 'woocommerce/woocommerce.php';

if ( ! file_exists( WP_PLUGIN_DIR . '/' . $wc_plugin_path ) || ! is_plugin_active( $wc_plugin_path ) ) {
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

define( 'WC_CALYSPO_BRIDGE_PLUGIN_FILE', __FILE__ );
define( 'WC_CALYPSO_BRIDGE_CURRENT_VERSION', '1.9.17' );
define( 'WC_MIN_VERSION', '3.0.0' );

if ( ! function_exists( 'wc_calypso_bridge_is_ecommerce_plan' ) ) {
	/**
	 * Returns if a site is an eCommerce plan site or not.
	 *
	 * @return bool True if the site is an ecommerce site.
	 */
	function wc_calypso_bridge_is_ecommerce_plan() {
		if ( function_exists( 'wpcom_site_has_feature' ) ) {
			return wpcom_site_has_feature( \WPCOM_Features::ECOMMERCE_MANAGED_PLUGINS );
		}

		return false;
	}
}

// Filters we want to add for ecommerce plan.
require_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-filters.php';
add_action( 'init', array( 'WC_Calypso_Bridge_Filters', 'get_instance' ) );

// We want to adjust tracks settings for business, ecomm, in calypsoified and wp-admin views.
require_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-tracks.php';
add_filter( 'pre_option_woocommerce_allow_tracking', array( 'WC_Calypso_Bridge_Tracks', 'always_enable_tracking' ) );
add_action( 'init', array( 'WC_Calypso_Bridge_Tracks', 'get_instance' ) );

// Load cron events.
require_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-events.php';

// Also prevent Crowdsignal from redirecting during onboarding in all both wp-admin and calypsoified ecommerce plan.
require_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-crowdsignal-redirect.php';

// Load shared stuff for both ecommerce and business plan.
require_once dirname( __FILE__ ) . '/class-wc-calypso-bridge-shared.php';

// Load WCPay in core experiment.
require_once dirname( __FILE__ ) . '/includes/class-wc-calypso-bridge-payments.php';

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
