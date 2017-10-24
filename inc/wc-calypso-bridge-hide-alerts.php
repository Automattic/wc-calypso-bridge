<?php
/**
 * Logic to hide various alerts in wp-admin
 *
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Hide Apple Pay and Google Payment notices
add_filter( 'pre_option_wc_stripe_show_apple_pay_notice', '__return_true' );
add_filter( 'pre_option_wc_stripe_show_request_api_notice', '__return_true' );

// Hide setup store notice.
add_filter( 'woocommerce_show_admin_notice', 'wc_calypso_bridge_hide_admin_notice', 10, 2 );

function wc_calypso_bridge_hide_admin_notice( $bool, $notice ) {
	if ( 'install' === $notice ) {
		return false;
	}

	return $bool;
}
