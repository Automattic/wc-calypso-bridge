<?php
/**
 * Adds a Store link to the masterbar
 *
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wc_calypso_bridge_masterbar_css() {
	$asset_path = WC_Calypso_Bridge_Deprecated::$plugin_asset_path ? WC_Calypso_Bridge_Deprecated::$plugin_asset_path : WC_Calypso_Bridge_Deprecated::MU_PLUGIN_ASSET_PATH;
	wp_enqueue_style( 'wp-calypso-bridge-masterbar', $asset_path . 'assets/css/masterbar.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );
}

if ( ! function_exists( 'wc_api_dev_masterbar_css' ) ) {
	add_action( 'wp_enqueue_scripts', 'wc_calypso_bridge_masterbar_css' );
	add_action( 'admin_enqueue_scripts', 'wc_calypso_bridge_masterbar_css' );

	add_action( 'jetpack_masterbar', function() {
		global $wp_admin_bar;

		if ( isset( $wp_admin_bar ) && current_user_can( 'manage_options' ) ) {
			$strip_http = '/.*?:\/\//i';
			$site_slug  = preg_replace( $strip_http, '', get_home_url() );
			$site_slug  = str_replace( '/', '::', $site_slug );

			$store_url = 'https://wordpress.com/store/' . $site_slug;

			$wp_admin_bar->add_menu( array(
				'parent' => 'blog',
				'id'     => 'store',
				'title'  => esc_html__( 'Store', 'wc_calypso_bridge' ),
				'href'   => $store_url,
				'meta'   => array(
					'class' => 'mb-icon-spacer',
				)
			) );
		}
	} );
}
