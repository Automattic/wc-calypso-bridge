/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';

/**
 * Internal dependencies
 */
import wcNavFilterRootUrl from './wc-navigation-root-url';
import PaymentsWelcomePage from './payments-welcome';

wcNavFilterRootUrl();

addFilter('woocommerce_admin_pages_list', 'wc-calypso-bridge', (pages) => {
	pages.push({
		container: PaymentsWelcomePage,
		path: '/payments-welcome',
		breadcrumbs: [__('WooCommerce Payments', 'wc-calypso-bridge')],
		navArgs: {
			id: 'wc-calypso-bridge-payments-welcome-page',
		},
	});

	return pages;
});

// Remove theme selection step from onboarding per https://wp.me/pNEWy-e9X#comment-53446
addFilter( 'woocommerce_admin_profile_wizard_steps', 'woocommerce-admin', ( steps ) => {
	return steps.filter( ( step ) => step.key !== 'theme' );
} );
