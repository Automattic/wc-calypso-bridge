<?php
function wc_calypso_bridge_disable_gutenberg_for_post_type( $current_value, $post_type ) {
	$wc_post_types = array(
		'shop_coupon',
		'shop_order',
		'product',
		'bookable_resource',
		'wc_booking',
		'event_ticket',
		'wc_membership_plan',
		'wc_user_membership',
		'wc_voucher',
		'wc_pickup_location',
		'shop_subscription',
		'wc_product_tab',
		'wishlist',
		'wc_zapier_feed',
	);
	if ( in_array( $post_type, $wc_post_types ) ) {
		return false;
	}
	return $current_value;
}
add_filter( 'use_block_editor_for_post_type', 'wc_calypso_bridge_disable_gutenberg_for_post_type', 10, 2 );
