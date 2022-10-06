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
import './index.scss';

wcNavFilterRootUrl();


addFilter( 'woocommerce_admin_pages_list', 'wc-calypso-bridge', ( pages ) => {

	pages.push( {
		container: PaymentsWelcomePage,
		path: '/payments-welcome',
		breadcrumbs: [ __( 'WooCommerce Payments', 'wc-calypso-bridge' ) ],
		navArgs: {
			id: 'wc-calypso-bridge-payments-welcome-page',
		},
	} );

	/**
	 * Ensure that WooCommerce Home page will not highlight the WooCommerce parent menu item.
	 */
	pages = pages.map( page => page.path === '/' ? {...page, wpOpenMenu: ''} : page );

	return pages;
} );
