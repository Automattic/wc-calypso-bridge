/**
 * External dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { WooOnboardingTask } from '@woocommerce/onboarding';
import { registerPlugin } from '@wordpress/plugins';
import { render } from '@wordpress/element';

/**
 * Internal dependencies
 */
import wcNavFilterRootUrl from './wc-navigation-root-url';
import LaunchStorePage from './launch-store';
import WelcomeModal from './welcome-modal';
import { TaskListCompletedHeaderFill } from './task-completion/fill';
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
