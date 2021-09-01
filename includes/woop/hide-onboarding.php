<?php
/**
 * Hide onboarding flows.
 *
 * @since 1.8.1
 * @package WC_Calypso_Bridge
 */

defined( 'ABSPATH' ) || exit;

// Hide the 'WooCommerce Setup' card from wp-admin.
add_filter( 'pre_option_woocommerce_task_list_hidden', 'wc_calypso_bridge_return_yes' );

// Disable the setup wizard redirect on plugin activation
add_filter( 'woocommerce_enable_setup_wizard', '__return_false' );
