<?php
/**
 * Filters woocommerce_get_edit_order_url to return wpcom edit order URL
 *
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wc_calypso_bridge_email_get_wpcom_order_link( $url, $order ) {
	$strip_http = '/.*?:\/\//i';
	$site_slug  = preg_replace( $strip_http, '', get_home_url() );
	$site_slug  = str_replace( '/', '::', $site_slug );
	$order_id = $order->get_id();

	return 'https://wordpress.com/store/order/' . $site_slug . '/' . $order_id;
}

add_filter( 'woocommerce_get_edit_order_url', 'wc_calypso_bridge_email_get_wpcom_order_link', 10, 2 );
