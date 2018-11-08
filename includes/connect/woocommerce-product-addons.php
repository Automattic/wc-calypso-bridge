<?php
/**
 * Register Product Add-on Pages
 *
 * @package WC_Calypso_Bridge/Classes
 */

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'product_page_global_addons',
		'menu'      => 'edit.php?post_type=product',
	)
);

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'product_page_addons',
		'menu'      => 'edit.php?post_type=product',
	)
);
