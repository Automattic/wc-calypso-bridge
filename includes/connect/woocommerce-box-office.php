<?php
wc_calypso_bridge_connect_page( array(
	'screen_id' => 'event_ticket_page_ticket_tools',
	'menu'      => 'edit.php?post_type=event_ticket',
) );

wc_calypso_bridge_connect_page( array(
	'screen_id' => 'event_ticket_page_create_ticket',
	'menu'      => 'edit.php?post_type=event_ticket',
) );

wc_calypso_bridge_connect_page( array(
	'screen_id' => 'edit-event_ticket',
	'menu'      => 'edit.php?post_type=event_ticket',
	'submenu'   => 'edit.php?post_type=event_ticket',
) );

wc_calypso_bridge_connect_page( array(
	'screen_id' => 'event_ticket',
	'menu'      => 'edit.php?post_type=event_ticket',
	'submenu'   => 'edit.php?post_type=event_ticket',
) );
