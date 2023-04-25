<?php
/**
 * Plugin Name: WooCommerce Calypso Bridge
 * Plugin URI: https://wordpress.com/
 * Description: A feature plugin to provide ux enhancements for users of Store on WordPress.com.
 * Version: 2.1.0
 * Author: Automattic
 * Author URI: https://wordpress.com/
 * Requires at least: 4.4
 * Tested up to: 5.4.2
 *
 * @package WC_Calypso_bridge
 */

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

if ( ! function_exists( 'wpcom_site_has_feature' ) ) {
	// Bail early, if we cannot determine the site plan.
	return;
}

if ( ! defined( 'WC_CALYPSO_BRIDGE_PLUGIN_FILE' ) ) {
	define( 'WC_CALYPSO_BRIDGE_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'WC_CALYPSO_BRIDGE_PLUGIN_PATH' ) ) {
	define( 'WC_CALYPSO_BRIDGE_PLUGIN_PATH', dirname( __FILE__ ) );
}
if ( ! defined( 'WC_CALYPSO_BRIDGE_CURRENT_VERSION' ) ) {
	define( 'WC_CALYPSO_BRIDGE_CURRENT_VERSION', '2.1.0' );
}
if ( ! defined( 'WC_MIN_VERSION' ) ) {
	define( 'WC_MIN_VERSION', '7.3' );
}

/**
 * Always make the tracks setting be yes. Users can opt via WordPress.com privacy settings.
 */
add_filter(
	'pre_option_woocommerce_allow_tracking',
	function() {
		return 'yes';
	}
);

// The Bridge Main Controller.
require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/class-wc-calypso-bridge-dotcom-features.php';
require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/class-wc-calypso-bridge.php';

if ( ! wc_calypso_bridge_has_ecommerce_features() ) {
	require_once WC_CALYPSO_BRIDGE_PLUGIN_PATH . '/store-on-wpcom/class-wc-calypso-bridge.php';
	return;
}
