/**
 * External dependencies
 */
import { recordTracksEvent } from '@automattic/calypso-analytics';

document.addEventListener( 'DOMContentLoaded', function () {
	// Track clicks on the "Upgrades > Plans" menu item in the sidebar
	// Find all links under Upgrades that point to /plans/* paths
	const possiblePlansLinks = document.querySelectorAll(
		'#toplevel_page_paid-upgrades a[href^="https://wordpress.com/plans/"]'
	);
	possiblePlansLinks.forEach( ( possiblePlanLink ) => {
		possiblePlanLink.addEventListener( 'click', function ( evt ) {
			const href = evt && evt.target ? evt.target.getAttribute( 'href' ) : null;
			if ( ! href || typeof href !== 'string' || ! href.startsWith( 'https://wordpress.com/plans/' )  ) {
				return;
			}
			// Check if we're navigating to /plans/:siteSlug or some deeper path below /plans/[something]/siteSlug
			// The implementation can be changed to be simpler or different, but the check is needed.
			const hasDeeperPath = href.substring( 28 ).includes( '/' );
			if ( hasDeeperPath ) {
				return;
			}
			recordTracksEvent( 'calypso_sidebar_item_click', {
				path: '/plans',
			} );
		} );
	}
} );
