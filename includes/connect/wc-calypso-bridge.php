<?php
/**
 * WC Calypso Bridge pages
 *
 * @package WC_Calypso_Bridge/Connect
 * @since   1.0.0
 * @version 1.0.0
 */

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'toplevel_page_wc-setup-checklist',
		'menu'      => 'wc-setup-checklist',
	)
);

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'toplevel_page_wc-wp-manage-site',
		'menu'      => 'wc-wp-manage-site',
	)
);

wc_calypso_bridge_connect_page(
	array(
		'screen_id' => 'toplevel_page_wc-support',
		'menu'      => 'wc-support',
	)
);
