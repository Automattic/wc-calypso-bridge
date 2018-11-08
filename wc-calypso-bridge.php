<?php
/**
 * Plugin Name: WooCommerce Calypso Bridge
 * Plugin URI: https://wordpress.com/
 * Description: A feature plugin to provide ux enhancments for users of Store on WordPress.com.
 * Version: 1.0.0
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

define( 'WC_CALYPSO_BRIDGE_CURRENT_VERSION', '1.0.0' );
define( 'WC_MIN_VERSION', '3.0.0' );

// TODO Pick a better option name.
// We can set this during store setup/provisioning so they get the right code loaded.
// @codingStandardsIgnoreLine
// update_option( 'is-atomic-ecommerce', true );
$is_atomic_ecommerce = get_option( 'is-atomic-ecommerce', false );

if ( ! $is_atomic_ecommerce ) {
	include_once dirname( __FILE__ ) . '/store-on-wpcom/wc-calypso-bridge-class.php';
	return;
}

require_once dirname( __FILE__ ) . '/class-wc-calypso-bridge.php';
