<?php
wc_calypso_bridge_connect_page( array(
	'screen_id' => 'woocommerce_page_wc-settings-wc_wishlists',
	'menu'      => 'woocommerce',
) );

wc_calypso_bridge_connect_page( array(
	'screen_id' => 'edit-wishlist',
	'menu'      => 'woocommerce',
	'submenu'   => 'edit.php?post_type=wishlist',
) );

wc_calypso_bridge_connect_page( array(
	'screen_id' => 'wishlist',
	'menu'      => 'woocommerce',
	'submenu'   => 'edit.php?post_type=wishlist',
) );
