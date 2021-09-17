<?php
/**
 * Hide onboarding flows.
 *
 * @since 1.8.1
 * @package WC_Calypso_Bridge
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hide the 'WooCommerce Setup' card from wp-admin.
 */
function wc_calypso_bridge_hide_task_list() {
	return 'yes';
}
add_filter( 'pre_option_woocommerce_task_list_hidden', 'wc_calypso_bridge_hide_task_list' );

// Disable the setup wizard redirect on plugin activation.
add_filter( 'woocommerce_enable_setup_wizard', '__return_false' );

/**
 * Skip the onboarding profile setup wizard when navigating to wc-admin for the first time.
 *
 * Preset the onboarding profile data to skipped. This is the same as clicking "Skip" in the first step of the setup-wizard.
 *
 * @return array
 */
function wc_calypso_bridge_skip_onboarding() {
	return array( 'skipped' => 1 );
}
add_filter( 'pre_option_woocommerce_onboarding_profile', 'wc_calypso_bridge_skip_onboarding' );
