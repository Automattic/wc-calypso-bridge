<?php
wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'toplevel_page_wcpv-commissions',
		'menu'      => 'wcpv-commissions',
	)
);

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'woocommerce_page_wc-reports-vendors',
		'menu'      => 'woocommerce',
	)
);

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'product_page_product_attributes',
		'menu'      => 'edit.php?post_type=product',
		'submenu'   => 'edit-tags.php?taxonomy=wcpv_product_vendors&post_type=product',
	)
);
