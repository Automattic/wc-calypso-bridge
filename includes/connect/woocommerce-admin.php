<?php
/**
 * Register the WooCommerce Admin top level pages with the Calypso bridge.
 *
 * @package WC_Calypso_Bridge
 * @since 1.0.18
 * @version 1.0.0
 */

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'woocommerce_page_wc-admin',
		'menu'      => 'woocommerce',
		'submenu'   => 'page=wc-admin',
	)
);

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'toplevel_page_wc-admin',
		'menu'      => 'woocommerce',
		'submenu'   => 'page=wc-admin',
	)
);

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'woocommerce-revenue',
		'menu'      => 'wc-admin&path=/analytics/overview',
		'submenu'   => 'wc-admin&path=/analytics/overview',
	)
);

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'woocommerce-revenue',
		'menu'      => 'wc-admin&path=/analytics/revenue',
		'submenu'   => 'wc-admin&path=/analytics/revenue',
	)
);

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'woocommerce-marketing',
		'menu'      => 'woocommerce-marketing',
		'submenu'   => 'wc-admin&path=/marketing',
	)
);
