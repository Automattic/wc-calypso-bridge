<?php
/**
 * Plugin Name: WooCommerce Calypso Bridge
 * Plugin URI: https://wordpress.com/
 * Description: A feature plugin to provide ux enhancments for users of Store on WordPress.com.
 * Version: 0.2.3
 * Author: Automattic
 * Author URI: https://wordpress.com/
 * Requires at least: 4.4
 * Tested up to: 4.8.2
 */

if ( ! file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) {
	// No WooCommerce installed, we don't need this.
	return;
}

// Allow for wc-calypso-bridge to be installed as a traditional plugin
if ( file_exists( WP_PLUGIN_DIR . '/wc-calypso-bridge/wc-calypso-bridge.php' ) ) {
	if ( WP_PLUGIN_DIR . '/wc-calypso-bridge/' !== plugin_dir_path( __FILE__ ) ) {
		// wc-calypso-bridge is already installed conventionally, exiting to avoid conflict.
		return;
	}
}

define( 'WC_CALYPSO_BRIDGE_CURRENT_VERSION', '0.2.4' );
define( 'WC_MIN_VERSION', '3.0.0' );

// TODO Pick a better option name.
// We can set this during store setup/provisioning so they get the right code loaded.
//update_option( 'is_atomic_ecommerce_plan', true );
$is_atomic_ecommerce_plan = get_option( 'is_atomic_ecommerce_plan', false );

if ( ! $is_atomic_ecommerce_plan ) {
	include_once( dirname( __FILE__ ) . '/store-on-wpcom/wc-calypso-bridge-class.php' );
	return;
}

include_once( dirname( __FILE__ ) . '/wc-calypso-bridge-class.php' );

