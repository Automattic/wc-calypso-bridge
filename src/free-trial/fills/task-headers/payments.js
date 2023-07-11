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
import TimerImage from '../../../task-headers/assets/images/timer.svg';
import { WC_ASSET_URL } from '../../../utils/admin-settings';

const PaymentsHeader = () => {
	return (
		<WooOnboardingTaskListHeader id="payments">
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
								'images/task_list/payment-illustration.png'
							}
							className="svg-background"
						/>
						<div className="woocommerce-task-header__contents">
							<h1>
								{ __(
									'It’s time to test payments',
									'wc-calypso-bridge'
								) }
							</h1>
							<p>
								{ __(
									'Give your customers an easy and convenient way to pay! Set up and test some of our fast and secure online or in person payment methods.',
									'wc-calypso-bridge'
								) }
							</p>
							<Button
								isSecondary={ task.isComplete }
								isPrimary={ ! task.isComplete }
								onClick={ goToTask }
							>
								{ __( 'Test payments', 'wc-calypso-bridge' ) }
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

registerPlugin( 'wc-calypso-bridge-payments-task-header', {
	render: PaymentsHeader,
	scope: 'woocommerce-tasks',
} );
