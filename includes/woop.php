<?php
/**
 * Woo on Plans.
 *
 * @since 1.8.1
 * @package WC_Calypso_Bridge
 */

defined( 'ABSPATH' ) || exit;

/**
 * Init woop.
 *
 * @return void
 */
function wc_calypso_bridge_woop_init() {
	// Gate Woop behind feature flag.
	if ( ! defined( 'WPCOM_ENABLE_WOOP' ) ) {
		return;
	}

	// Don't load mods in cronjobs.
	if ( defined( 'DOING_CRON' ) ) {
		return;
	}

	// Ensure a valid Woo install is installed.
	if ( ! class_exists( 'woocommerce' ) || ! version_compare( get_option( 'woocommerce_db_version' ), WC_MIN_VERSION, '>=' ) ) {
		return;
	}

	if ( is_admin() ) {
		require_once dirname( __FILE__ ) . '/woop/hide-onboarding.php';
	}
}
add_action( 'plugins_loaded', 'wc_calypso_bridge_woop_init' );
