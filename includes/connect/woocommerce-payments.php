<?php
/**
 * Register the WooCommerce Payments top level pages with the Calypso bridge.
 *
 * @package WC_Calypso_Bridge
 * @since 1.0.18
 * @version 1.0.0
 */

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'woocommerce-payments',
		'menu'      => 'wc-admin&path=/payments/deposits',
		'submenu'   => 'wc-admin&path=/payments/deposits',
	)
);

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'woocommerce-payments-transactions',
		'menu'      => 'wc-admin&path=/payments/deposits',
		'submenu'   => 'wc-admin&path=/payments/transactions',
	)
);

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'woocommerce-payments-connect',
		'menu'      => 'wc-admin&path=/payments/connect',
		'submenu'   => 'wc-admin&path=/payments/connect',
	)
);
