<?php
/**
 * Fixes for nav unification issues.
 *
 * @since 1.7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once dirname( __FILE__ ) . '/../../includes/class-wc-calypso-bridge-page-controller.php';

// @todo This should rely on the navigation screens instead.
$connect_files = glob( dirname( __FILE__ ) . '/../../includes/connect/*.php' );
foreach ( $connect_files as $connect_file ) {
  include_once $connect_file;
}

add_action( 'current_screen', 'load_ui_elements' );

/**
 * Updates required UI elements for calypso bridge pages only.
 */
function load_ui_elements() {
  if ( is_wc_calypso_bridge_page() ) {
    if ( function_exists( 'wpcomsh_activate_nav_unification' ) && wpcomsh_activate_nav_unification( false ) ) {
      add_action( 'admin_enqueue_scripts', 'add_nav_unification_styles' );
    }
  }
}

/**
 * Add styles for nav unification fixes.
 */
function add_nav_unification_styles() {
  $asset_path = WC_Calypso_Bridge::$plugin_asset_path ? WC_Calypso_Bridge::$plugin_asset_path : WC_Calypso_Bridge::MU_PLUGIN_ASSET_PATH;
  wp_enqueue_style( 'wp-calypso-bridge-nav-unification', $asset_path . 'assets/css/admin/nav-unification.css', array(), WC_CALYPSO_BRIDGE_CURRENT_VERSION );
}