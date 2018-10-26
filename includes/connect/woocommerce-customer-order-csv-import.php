<?php
wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'woocommerce_page_csv_import_suite',
		'menu'      => 'woocommerce',
	)
);

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'importer_woocommerce_coupon_csv',
		'menu'      => 'woocommerce',
	)
);

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'importer_woocommerce_customer_csv',
		'menu'      => 'woocommerce',
	)
);

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'importer_woocommerce_order_csv',
		'menu'      => 'tools.php',
		'submenu'   => 'import.php',
	)
);
