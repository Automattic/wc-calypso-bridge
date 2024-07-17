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
import { WC_ASSET_URL } from '../utils/admin-settings';

const GetPaidWithStripeHeader = () => {
	return (
		<WooOnboardingTaskListHeader id="get-paid-with-stripe">
			{ ( { task, goToTask } ) => {
				return (
					<div className="woocommerce-task-header__contents-container">
						<img
							alt={ __(
								'Payment illustration',
								'wc-calypso-bridge'
							) }
							src={
								WC_ASSET_URL +
								'images/task_list/payment-illustration.svg'
							}
							className="svg-background"
						/>
						<div className="woocommerce-task-header__contents">
							<h1>
								{ __(
									"It's time to get paid",
									'wc-calypso-bridge'
								) }
							</h1>
							<p>
								{ __(
									'Accepting payments is easy with Stripe. Sell online and in person, and sync all payments, customers, items, and inventory.',
									'wc-calypso-bridge'
								) }
							</p>
							<Button
								isSecondary={ task.isComplete }
								isPrimary={ ! task.isComplete }
								onClick={ () => {
									window.location.href =
										window.wcCalypsoBridge.stripe_connect_url;
								} }
							>
								{ __( 'Set up Stripe', 'wc-calypso-bridge' ) }
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

registerPlugin( 'wc-calypso-bridge-get-paid-with-stripe-task-header', {
	render: GetPaidWithStripeHeader,
	scope: 'woocommerce-tasks',
} );

export default GetPaidWithStripeHeader;
