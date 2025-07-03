/**
 * External dependencies
 */
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { WooOnboardingTaskListHeader } from '@woocommerce/onboarding';
import { registerPlugin } from '@wordpress/plugins';
import interpolateComponents from '@automattic/interpolate-components';
import { Link } from '@woocommerce/components';

/**
 * Internal dependencies
 */
import TimerImage from '../../../task-headers/assets/images/timer.svg';
import { WC_ASSET_URL } from '../../../utils/admin-settings';
import { sanitizeHTML } from '../../../payment-gateway-suggestions/utils';

const WoocommercePaymentsHeader = () => {
	const incentive =
		window.wcSettings?.admin?.wcpayWelcomePageIncentive ||
		window.wcpaySettings?.connectIncentive;

	return (
		<WooOnboardingTaskListHeader id="woocommerce-payments">
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
									'It’s time to test payments',
									'wc-calypso-bridge'
								) }
							</h1>
							{ incentive?.task_header_content ? (
								<p
									dangerouslySetInnerHTML={ sanitizeHTML(
										incentive.task_header_content
									) }
								/>
							) : (
								<p>
									{ __(
										'Power your payments with a simple, all-in-one option. Verify your business details to start testing transactions with WooCommerce Payments.',
										'wc-calypso-bridge'
									) }
								</p>
							) }
							<p>
								{ interpolateComponents( {
									mixedString: __(
										'By clicking "Test payments", you agree to the {{tosLink}}Terms of Service{{/tosLink}}',
										'wc-calypso-bridge'
									),
									components: {
										tosLink: (
											<Link
												href="https://wordpress.com/tos/"
												type="external"
												target="_blank"
											>
												<></>
											</Link>
										),
									},
								} ) }
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

registerPlugin( 'wc-calypso-bridge-woocommerce-payments-task-header', {
	render: WoocommercePaymentsHeader,
	scope: 'woocommerce-tasks',
} );
