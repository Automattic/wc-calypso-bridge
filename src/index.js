/**
 * External dependencies
 */
import { addFilter, addAction } from '@wordpress/hooks';
import { WooOnboardingTask } from '@woocommerce/onboarding';
import {
	registerPlugin,
	unregisterPlugin,
	getPlugins,
} from '@wordpress/plugins';
import { render, lazy } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import wcNavFilterRootUrl from './wc-navigation-root-url';
import LaunchStorePage from './launch-store';
import WelcomeModal from './welcome-modal';
import { DisabledTasksFill } from './disabled-tasks';
import { Tax } from './free-trial/tax';
import { WoocommercePaymentsTaskPage } from './free-trial/fills/woocommerce-payments';
import { TaskListCompletedHeaderFill } from './task-completion/fill.tsx';
import {
	ProgressHeaderFill,
	ProgressTitleFill,
} from './homescreen-progress-header';
import { CalypsoBridgeHomescreenBanner } from './homescreen-banner';
import { AppearanceFill, GetPaidWithSquareFill, GetPaidWithStripeFill, GetPaidWithPayPalFill } from './task-fills';
import './task-headers';
import './track-menu-item';
import { CalypsoBridgeIntroductoryOfferBanner } from './introductory-offer-banner';
import { loadTranslations } from './i18n-loader';

// A workaround for Webpack's tree-shaking to ensure `loadTranslations` is included in the production bundle.
( function () {} )( loadTranslations );

// Modify webpack to append the ?ver parameter to JS chunk
// https://webpack.js.org/api/module-variables/#__webpack_get_script_filename__-webpack-specific
// eslint-disable-next-line no-undef,camelcase
const oldGetScriptFileNameFn = __webpack_get_script_filename__;
// eslint-disable-next-line no-undef,camelcase
__webpack_get_script_filename__ = ( chunk ) => {
	const filename = oldGetScriptFileNameFn( chunk );
	return `${ filename }?ver=${ window.wcCalypsoBridge.version }`;
};

const Marketing = lazy( () =>
	import( /* webpackChunkName: "marketing" */ './marketing' )
);

const PaymentGatewaySuggestions = lazy( () =>
	import(
		/* webpackChunkName: "payment-gateway-suggestions" */ './payment-gateway-suggestions'
	)
);

const Plugins = lazy( () =>
	import( /* webpackChunkName: "plugins" */ './plugins' )
);

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

// Unregister task fills from WooCommerce Core
// Otherwise we'll have both the original and new fills rendered.
const pluginsToRemove = [ 'wc-admin-onboarding-task-appearance' ];

// Appearance task fill.
registerPlugin( 'wc-calypso-bridge-task-appearance', {
	scope: 'woocommerce-tasks',
	render: AppearanceFill,
} );

if ( !! window.wcCalypsoBridge.isWooExpress ) {
	registerPlugin( 'wc-calypso-bridge-homescreen-progress-header', {
		render: ProgressHeaderFill,
		scope: 'woocommerce-admin',
	} );

	registerPlugin( 'wc-calypso-bridge-homescreen-progress-title', {
		render: ProgressTitleFill,
		scope: 'woocommerce-admin',
	} );
}

if ( !! window.wcCalypsoBridge.isEcommercePlanTrial ) {
	import( './free-trial/fills' );

	registerPlugin( 'my-tasklist-footer-extension', {
		render: DisabledTasksFill,
		scope: 'woocommerce-admin',
	} );

	pluginsToRemove.push(
		'wc-admin-onboarding-task-payments',
		'woocommerce-admin-task-wcpay', // WCPay task item which handles direct click on the task. (Not needed in free trial)
		'woocommerce-admin-task-wcpay-page', // WCPay task page which handles URL navigation to the task.
		'wc-admin-onboarding-task-tax'
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

	if ( window.wcCalypsoBridge.wooExpressIntroductoryOffer ) {
		registerPlugin( 'wc-calypso-bridge-homescreen-slotfill-banner', {
			render: CalypsoBridgeIntroductoryOfferBanner,
			scope: 'woocommerce-admin',
		} );
	} else {
		registerPlugin( 'wc-calypso-bridge-homescreen-slotfill-banner', {
			render: CalypsoBridgeHomescreenBanner,
			scope: 'woocommerce-admin',
		} );
	}

	if ( window?.wcCalypsoBridge?.square_connect_url ) {
		// Setup Square task fill (Partner Aware Onboarding).
		registerPlugin( 'wc-calypso-bridge-task-setup-woocommerce-square', {
			scope: 'woocommerce-tasks',
			render: GetPaidWithSquareFill,
		} );
	}

	if ( window?.wcCalypsoBridge?.stripe_connect_url ) {
		// Setup Stripe task fill (Partner Aware Onboarding).
		registerPlugin( 'wc-calypso-bridge-task-setup-woocommerce-stripe', {
			scope: 'woocommerce-tasks',
			render: GetPaidWithStripeFill,
		} );
  }

	if ( window?.wcCalypsoBridge?.paypal_connect_url ) {
		// Setup PayPal task fill (Partner Aware Onboarding).
		registerPlugin( 'wc-calypso-bridge-task-setup-woocommerce-paypal', {
			scope: 'woocommerce-tasks',
			render: GetPaidWithPayPalFill,
		} );
	}
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

			if ( !! window.wcCalypsoBridge.isEcommercePlanTrial ) {
				// Remove the marketplace page from the list.
				for ( let i = 0; i < pages.length; i++ ) {
					if ( pages[ i ].path === '/extensions' ) {
						pages.splice( i, 1 );
						break;
					}
				}

				// Override marketing page.
				pages = pages.map( ( page ) => {
					if ( page.path === '/marketing' ) {
						page.container = Marketing;
					}
					return page;
				} );

				// Add the Plugins landing page.
				pages.push( {
					container: Plugins,
					path: '/plugins-upgrade',
					breadcrumbs: [ __( 'Plugins' ), __( 'Plugins' ) ],
					navArgs: {
						id: 'plugins-upgrade',
					},
					capability: 'manage_woocommerce',
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

// Remove plugins that had already been added.
const taskPlugins = getPlugins( 'woocommerce-tasks' );
taskPlugins.forEach( ( plugin ) => {
	if ( pluginsToRemove.includes( plugin.name ) ) {
		unregisterPlugin( plugin.name );
	}
} );

addAction(
	'plugins.pluginRegistered',
	'wc-calypso-bridge',
	function ( _settings, name ) {
		if ( pluginsToRemove.includes( name ) ) {
			unregisterPlugin( name );
		}
	}
);
