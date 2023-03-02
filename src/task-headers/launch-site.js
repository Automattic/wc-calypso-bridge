/**
 * External dependencies
 */
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { WooOnboardingTaskListHeader } from '@woocommerce/onboarding';
import { registerPlugin } from '@wordpress/plugins';

/**
 * Internal dependencies
 */
import TimerImage from './assets/images/timer.svg';
import LaunchSiteImage from './assets/images/store-launch-illustration.png';

const LaunchSiteHeader = () => {
	return (
		<WooOnboardingTaskListHeader id="launch_site">
			{ ( { task, goToTask } ) => {
				return (
					<div className="woocommerce-task-header__contents-container">
						<img
							alt={ __(
								'Launch your store illustration',
								'wc-calypso-bridge'
							) }
							src={ LaunchSiteImage }
							className="svg-background"
						/>
						<div className="woocommerce-task-header__contents">
							<h1>
								{ __(
									'Your store is ready for launch!',
									'wc-calypso-bridge'
								) }
							</h1>
							<p>
								{ __(
									"It's time to celebrate – you're ready to launch your store! Woo!",
									'wc-calypso-bridge'
								) }
							</p>
							<Button
								isSecondary={ task.isComplete }
								isPrimary={ ! task.isComplete }
								onClick={ goToTask }
							>
								{ __(
									'Launch your store',
									'wc-calypso-bridge'
								) }
							</Button>
							<p className="woocommerce-task-header__timer">
								<img src={ TimerImage } alt="Timer" />{ ' ' }
								<span>{ task.time }</span>
							</p>
						</div>
					</div>
				);
			} }
		</WooOnboardingTaskListHeader>
	);
};

registerPlugin( 'wc-calypso-bridge-launch-site-task-header', {
	render: LaunchSiteHeader,
	scope: 'woocommerce-tasks',
} );
