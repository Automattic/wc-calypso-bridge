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
import { DisabledTasksFill } from './disabled-tasks';
import { PaymentGatewaySuggestions } from './payment-gateway-suggestions';
import { Tax } from './free-trial/tax';
import { TaskListCompletedHeaderFill } from './task-completion/fill.tsx';
import './index.scss';
import { CalypsoBridgeHomescreenBanner } from './homescreen-banner';

wcNavFilterRootUrl();

if ( !! window.wcCalypsoBridge.isEcommercePlanTrial ) {
	registerPlugin( 'free-trial-tasklist-completion', {
		render: TaskListCompletedHeaderFill,
		scope: 'woocommerce-admin',
	} );
}

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
	import( './free-trial/fills' );

	registerPlugin( 'my-tasklist-footer-extension', {
		render: DisabledTasksFill,
		scope: 'woocommerce-admin',
	} );

	// Unregister task fills from WooCommerce Core
	// Otherwise we'll have both the original and new fills rendered.
	const oldTaskNames = [
		'wc-admin-onboarding-task-payments',
		'wc-admin-onboarding-task-tax',
	];
	addAction(
		'plugins.pluginRegistered',
		'wc-calypso-bridge',
		function ( _settings, name ) {
			if ( oldTaskNames.includes( name ) ) {
				unregisterPlugin( name );
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

	registerPlugin( 'wc-calypso-bridge-task-tax', {
		scope: 'woocommerce-tasks',
		render: () => (
			<WooOnboardingTask id="tax">
				{ ( { onComplete, query, task } ) => (
					<Tax
						onComplete={ onComplete }
						query={ query }
						task={ task }
					/>
				) }
			</WooOnboardingTask>
		),
	} );

	registerPlugin( 'wc-calypso-bridge-homescreen-slotfill-banner', {
		render: CalypsoBridgeHomescreenBanner,
		scope: 'woocommerce-admin',
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
