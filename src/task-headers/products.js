/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { registerPlugin } from '@wordpress/plugins';
import { WooOnboardingTaskListHeader } from '@woocommerce/onboarding';

/**
 * Internal dependencies
 */
import TimerImage from './assets/images/timer.svg';
import { WC_ASSET_URL } from '../utils/admin-settings';

const ProductsHeader = () => {
	return (
		<WooOnboardingTaskListHeader id="products">
			{ ( { task, goToTask } ) => {
				const taskTitle = task?.title;
				const taskDescription = task?.content;
				const taskCta = task?.actionLabel;
				const taskIsComplete = task?.isComplete;

				return (
					<div className="woocommerce-task-header__contents-container">
						<img
							alt={ __( 'Products illustration', 'woocommerce' ) }
							src={
								WC_ASSET_URL +
								'images/task_list/sales-section-illustration.svg'
							}
							className="svg-background"
						/>
						<div className="woocommerce-task-header__contents">
							<h1>{ taskTitle }</h1>
							<p>{ taskDescription }</p>
							<Button
								variant={
									taskIsComplete ? 'secondary' : 'primary'
								}
								onClick={ goToTask }
							>
								{ taskCta }
							</Button>
							<p className="woocommerce-task-header__timer">
								<img src={ TimerImage } alt="Timer" />{ ' ' }
								<span>
									{ __( '2 minutes', 'woocommerce' ) }
								</span>
							</p>
						</div>
					</div>
				);
			} }
		</WooOnboardingTaskListHeader>
	);
};

registerPlugin( 'wc-calypso-bridge-products-task-header', {
	render: ProductsHeader,
	scope: 'woocommerce-tasks',
} );
