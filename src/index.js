/**
 * External dependencies
 */
import { render, useEffect, useState, useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { getQuery } from '@woocommerce/navigation';

/**
 * Internal dependencies
 */
import wcNavFilterRootUrl from './wc-navigation-root-url';
// import PaymentsWelcomePage from './payments-welcome';
import LaunchStorePage from './launch-store';
import './index.scss';
import { WooOnboardingTask, WooOnboardingTaskListItem } from '@woocommerce/onboarding';
import { registerPlugin } from '@wordpress/plugins';

wcNavFilterRootUrl();

// registerPlugin( 'wc-calypso-bridge', {
// 	scope: 'woocommerce-tasks',
// 	render: () => (
// 	    <WooOnboardingTask id="launch_site">
// 	      { ( { onComplete, query, task } ) => (
// 	        <MyTask onComplete={ onComplete } task={ task } />
// 	      ) }
// 	    </WooOnboardingTask>
// 	),
// } );

// const MyTask = ( { onComplete, task } ) => {
// 	return (
// 		<h1>Test</h1>
// 	);
// }

registerPlugin( 'wc-calypso-bridge-2', {
	scope: 'woocommerce-tasks',
	render: () => (
    <WooOnboardingTaskListItem id="launch_site">
      { ( { defaultTaskItem: DefaultTaskItem } ) => (
			<DefaultTaskItem
				// intercept the click on the task list item so that we don't have to see a intermediate page before installing woocommerce payments
				onClick={ () => {
					console.log( 'HEY THERE' );
				} }
			/>
		) }
    </WooOnboardingTaskListItem>
	),
} );

// const MyTaskListItem = ( { onComplete } ) => {
// 	return (
// 		<h1>Test2</h1>
// 	);
// }

addFilter( 'woocommerce_admin_pages_list', 'wc-calypso-bridge', ( pages ) => {
	// pages.push( {
	// 	container: PaymentsWelcomePage,
	// 	path: '/payments-welcome',
	// 	breadcrumbs: [ __( 'WooCommerce Payments', 'wc-calypso-bridge' ) ],
	// 	navArgs: {
	// 		id: 'wc-calypso-bridge-payments-welcome-page',
	// 	},
	// } );

	pages.push( {
		container: LaunchStorePage,
		path: '/launch-store',
		breadcrumbs: [ __( 'Launch your store', 'wc-calypso-bridge' ) ],
		navArgs: {
			id: 'wc-calypso-bridge-launch-store',
		},
		wpOpenMenu: 'menu-dashboard',
	} );

	/**
	 * Ensure that WooCommerce Home page will not highlight the WooCommerce parent menu item.
	 */
	pages = pages.map( page => page.path === '/' ? {...page, wpOpenMenu: 'toplevel_page_admin-page-wc-admin'} : page );
	pages = pages.map( page => page.path === '/customers' ? {...page, wpOpenMenu: ''} : page );

	return pages;
} );
