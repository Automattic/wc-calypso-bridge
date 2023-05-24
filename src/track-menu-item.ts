/**
 * External dependencies
 */
import { recordTracksEvent } from '@automattic/calypso-analytics';

document.addEventListener( 'DOMContentLoaded', function () {
	// Track clicks on the "Upgrades > Plans" menu item in the sidebar
	const el = document.querySelector(
		'#toplevel_page_paid-upgrades > ul > li.wp-first-item'
	);
	if ( el ) {
		el.addEventListener( 'click', function () {
			recordTracksEvent( 'calypso_sidebar_item_click', {
				path: '/plans',
			} );
		} );
	}
} );
