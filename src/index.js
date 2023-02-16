/**
 * External dependencies
 */
import { addFilter, addAction } from '@wordpress/hooks';
import { WooOnboardingTask } from '@woocommerce/onboarding';
import { registerPlugin, unregisterPlugin } from '@wordpress/plugins';
import { render } from '@wordpress/element';

/**
 * Internal dependencies
 */
import wcNavFilterRootUrl from './wc-navigation-root-url';
import LaunchStorePage from './launch-store';
import WelcomeModal from './welcome-modal';
import { PaymentGatewaySuggestions } from './payment-gateway-suggestions';
import './index.scss';

wcNavFilterRootUrl();

// Add slot fill for launch-your-store task.
registerPlugin( 'wc-calypso-bridge', {
	scope: 'woocommerce-tasks',
	render: () => (
		<WooOnboardingTask id="launch_site">
			{ ( { onComplete, query, task } ) => (
				<LaunchStorePage
					onComplete={ onComplete }
					query={ query }
					task={ task }
				/>
			) }
		</WooOnboardingTask>
	),
} );


if ( !! window.wcCalypsoBridge.isEcommercePlanTrial ) {
	// Unregister 'wc-admin-onboarding-task-payments'' task from WooCommerce Core
	// Otherwise we'll have both the original payments and trial payments rendered.
	addAction(
		'plugins.pluginRegistered',
		'wc-calypso-bridge',
		function ( _settings, name ) {
			if ( name === 'wc-admin-onboarding-task-payments' ) {
				unregisterPlugin( 'wc-admin-onboarding-task-payments' );
			}
		}
	);

	// Add slot fill for payments task.
	registerPlugin( 'wc-calypso-bridge-payments', {
		scope: 'woocommerce-tasks',
		render: () => (
			<WooOnboardingTask id="payments">
				{ ( { onComplete, query } ) => (
					<PaymentGatewaySuggestions
						onComplete={ onComplete }
						query={ query }
					/>
				) }
			</WooOnboardingTask>
		),
	} );
}

if ( !! window.wcCalypsoBridge.isEcommercePlan ) {

	// Filter wc admin pages.
	addFilter( 'woocommerce_admin_pages_list', 'wc-calypso-bridge', ( pages ) => {

		if ( !! window.wcCalypsoBridge.isWooNavigationEnabled ) {
			/**
			 * Ensure that WooCommerce Home page will not highlight the WooCommerce parent menu item.
			 */
			pages = pages.map( page => page.path === '/' ? {...page, wpOpenMenu: 'menu-dashboard' } : page );
			pages = pages.map( page => page.path === '/customers' ? {...page, wpOpenMenu: ''} : page );
		}

		return pages;
	} );

	// Embed code on woo pages.
	if ( !! window.wcCalypsoBridge.isWooNavigationEnabled && !! window.wcCalypsoBridge.showEcommerceNavigationModal && !! window.wcCalypsoBridge.isWooPage ) {
		const wpBody = document.getElementById( 'wpbody-content' );
		const wrap =
			wpBody.querySelector( '.wrap.woocommerce' ) ||
			document.querySelector( '#wpbody-content > .woocommerce' ) ||
			wpBody.querySelector( '.wrap' );
		const embeddedBodyContainer = document.createElement( 'div' );

		render(
			<WelcomeModal />,
			wpBody.insertBefore( embeddedBodyContainer, wrap )
		);
	}
}
