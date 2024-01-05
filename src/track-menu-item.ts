/**
 * External dependencies
 */
import { recordTracksEvent } from '@automattic/calypso-analytics';

const trackUpgradePlansClick = () => {
	// Track clicks on the "Upgrades > Plans" menu item in the sidebar
	// Find all links under Upgrades that point to /plans/* paths
	const possiblePlansLinks = document.querySelectorAll(
		'#toplevel_page_paid-upgrades a[href^="https://wordpress.com/plans/"]'
	);
	possiblePlansLinks.forEach( ( possiblePlanLink ) => {
		const href = possiblePlanLink.getAttribute( 'href' );
		if (
			! href ||
			typeof href !== 'string' ||
			! href.startsWith( 'https://wordpress.com/plans/' )
		) {
			return;
		}

		// Check if we're navigating to /plans/:siteSlug or some deeper path below /plans/[something]/siteSlug
		// The implementation can be changed to be simpler or different, but the check is needed.
		const hasDeeperPath = href.substring( 28 ).includes( '/' );
		if ( hasDeeperPath ) {
			return;
		}

		possiblePlanLink.addEventListener( 'click', function () {
			// Note that we also track this event in Calypso Screen via wp-calypso. If you change this event, please update it there as well. See: https://github.com/Automattic/wp-calypso/pull/77303.
			recordTracksEvent( 'calypso_sidebar_item_click', {
				path: '/plans',
			} );
		} );
	} );
};

// Tracks when a user clicks on the "My Home" menu item in the sidebar
// And the user has an active intro offer.
const trackMyHomeClickWithIntroOffer = () => {
	const myHomeLink = document.querySelectorAll(
		'#adminmenu li#menu-dashboard a[href^="admin.php?page=wc-admin"]'
	);

	if (
		myHomeLink.length &&
		window.wcCalypsoBridge.wooExpressIntroductoryOffer
	) {
		myHomeLink[ 0 ].addEventListener( 'click', function () {
			recordTracksEvent( 'calypso_wooexpress_one_dollar_offer', {
				location: 'homescreen',
			} );
		} );
	}
};

document.addEventListener( 'DOMContentLoaded', () => {
	trackUpgraePlansClick();
	trackMyHomeClickWithIntroOffer();
} );
