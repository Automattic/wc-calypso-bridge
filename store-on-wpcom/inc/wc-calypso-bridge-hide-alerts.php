<?php
/**
 * Logic to hide various alerts in wp-admin
 *
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Hide setup store notice.
add_filter( 'woocommerce_show_admin_notice', 'wc_calypso_bridge_hide_admin_notice', 10, 2 );

function wc_calypso_bridge_hide_admin_notice( $bool, $notice ) {
	if ( 'install' === $notice ) {
		return false;
	}

	return $bool;
}
