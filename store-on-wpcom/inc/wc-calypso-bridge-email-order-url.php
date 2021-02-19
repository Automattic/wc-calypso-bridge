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
    return get_home_url() . '/wp-admin/post.php?post=' . $order->get_id() . '&action=edit';
}

add_filter( 'woocommerce_get_edit_order_url', 'wc_calypso_bridge_email_get_wpcom_order_link', 10, 2 );
