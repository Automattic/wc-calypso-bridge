<?php
/**
 * Plugin Name: WooCommerce Calypso Bridge
 * Plugin URI: https://wordpress.com/
 * Description: A feature plugin to provide ux enhancments for users of Store on WordPress.com.
 * Version: 1.0.1
 * Author: Automattic
 * Author URI: https://wordpress.com/
 * Requires at least: 4.4
 * Tested up to: 4.9.8
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

define( 'WC_CALYPSO_BRIDGE_CURRENT_VERSION', '1.0.1' );
define( 'WC_MIN_VERSION', '3.0.0' );

/**
 * Returns if a site is an eCommerce plan site or not.
 * The `at_options` array is created during provisioning. Usually it is 'business' or 'ecommerce'
 * To Test: update_option( 'at_options', array( 'plan_slug' => 'ecommerce' ) );
 *
 * @return bool True if the site is an ecommerce site.
 */
function wc_calypso_bridge_is_ecommerce_plan() {
	$at_options = get_option( 'at_options', array() );

	if ( array_key_exists( 'plan_slug', $at_options ) && 'ecommerce' === $at_options['plan_slug'] ) {
		return true;
	}

	return false;
}

if ( ! wc_calypso_bridge_is_ecommerce_plan() ) {
	include_once dirname( __FILE__ ) . '/store-on-wpcom/wc-calypso-bridge-class.php';
	return;
}

require_once dirname( __FILE__ ) . '/class-wc-calypso-bridge.php';

require_once dirname( __FILE__ ) . '/class-wc-calypso-bridge-frontend.php';
