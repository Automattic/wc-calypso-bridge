/**
 * External dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { WooOnboardingTask } from '@woocommerce/onboarding';
import { registerPlugin } from '@wordpress/plugins';

/**
 * Internal dependencies
 */
import wcNavFilterRootUrl from './wc-navigation-root-url';
// import PaymentsWelcomePage from './payments-welcome';
import LaunchStorePage from './launch-store';
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
