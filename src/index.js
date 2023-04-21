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
import './use-slot-patch';
import wcNavFilterRootUrl from './wc-navigation-root-url';
import LaunchStorePage from './launch-store';
import WelcomeModal from './welcome-modal';
import { DisabledTasksFill } from './disabled-tasks';
import { PaymentGatewaySuggestions } from './payment-gateway-suggestions';
import { Tax } from './free-trial/tax';
import { WoocommercePaymentsTaskPage } from './free-trial/fills/woocommerce-payments';
import { TaskListCompletedHeaderFill } from './task-completion/fill.tsx';
import {
	ProgressHeaderFill,
	ProgressTitleFill,
} from './homescreen-progress-header';
import { Marketing } from './marketing';
import './index.scss';
import { CalypsoBridgeHomescreenBanner } from './homescreen-banner';
import './task-headers';

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
		'woocommerce-admin-task-wcpay', // WCPay task item which handles direct click on the task. (Not needed in free trial)
		'woocommerce-admin-task-wcpay-page', // WCPay task page which handles URL navigation to the task.
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

	registerPlugin( 'wc-calypso-bridge-task-woocommerce-payments-page', {
		scope: 'woocommerce-tasks',
		render: WoocommercePaymentsTaskPage,
	} );

	registerPlugin( 'wc-calypso-bridge-homescreen-slotfill-banner', {
		render: CalypsoBridgeHomescreenBanner,
		scope: 'woocommerce-admin',
	} );

	registerPlugin( 'wc-calypso-bridge-homescreen-progress-header', {
		render: ProgressHeaderFill,
		scope: 'woocommerce-admin',
	} );

	registerPlugin( 'wc-calypso-bridge-homescreen-progress-title', {
		render: ProgressTitleFill,
		scope: 'woocommerce-admin',
	} );
}

if ( !! window.wcCalypsoBridge.isEcommercePlan ) {
	// Filter wc admin pages.
	addFilter(
		'woocommerce_admin_pages_list',
		'wc-calypso-bridge',
		( pages ) => {
			if ( !! window.wcCalypsoBridge.isWooNavigationEnabled ) {
				/**
				 * Ensure that WooCommerce Home page will not highlight the WooCommerce parent menu item.
				 */
				pages = pages.map( ( page ) =>
					page.path === '/'
						? { ...page, wpOpenMenu: 'menu-dashboard' }
						: page
				);
				pages = pages.map( ( page ) =>
					page.path === '/customers'
						? { ...page, wpOpenMenu: '' }
						: page
				);
			}

			// Override marketing page.
			if ( !! window.wcCalypsoBridge.isEcommercePlanTrial ) {
				pages = pages.map( ( page ) => {
					if ( page.path === '/marketing' ) {
						page.container = Marketing;
					}
					return page;
				} );
			}

			return pages;
		}
	);

	// Embed code on woo pages.
	if (
		!! window.wcCalypsoBridge.isWooNavigationEnabled &&
		!! window.wcCalypsoBridge.showEcommerceNavigationModal &&
		!! window.wcCalypsoBridge.isWooPage
	) {
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
