<?php
wc_calypso_bridge_connect_page( array(
	'screen_id' => 'edit-wc_pickup_location',
	'menu'      => 'edit.php?post_type=wc_pickup_location',
	'submenu'   => 'admin.php?page=wc-settings&tab=shipping&section=local_pickup_plus',
) );

wc_calypso_bridge_connect_page( array(
	'screen_id' => 'add-wc_pickup_location',
	'menu'      => 'edit.php?post_type=wc_pickup_location',
	'submenu'   => 'admin.php?page=wc-settings&tab=shipping&section=local_pickup_plus',
) );

wc_calypso_bridge_connect_page( array(
	'screen_id' => 'wc_pickup_location',
	'menu'      => 'edit.php?post_type=wc_pickup_location',
	'submenu'   => 'admin.php?page=wc-settings&tab=shipping&section=local_pickup_plus',
) );

wc_calypso_bridge_connect_page( array(
	'screen_id' => 'admin_page_wc_local_pickup_plus_import',
	'menu'      => '',
	'submenu'   => 'admin.php?page=wc-settings&tab=shipping&section=local_pickup_plus',
) );

wc_calypso_bridge_connect_page( array(
	'screen_id' => 'admin_page_wc_local_pickup_plus_export',
	'menu'      => '',
	'submenu'   => 'admin.php?page=wc-settings&tab=shipping&section=local_pickup_plus',
) );
