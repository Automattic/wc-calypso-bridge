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
import { useAppearanceClick } from '../utils/hooks/use-appearance-click';

const AppearanceHeader = () => {
	const { onClick } = useAppearanceClick();
	return (
		<WooOnboardingTaskListHeader id="appearance">
			{ ( { task, goToTask } ) => {
				const taskTitle = task.title;
				const taskDescription = task.content;
				const taskCta = task.actionLabel;
				const taskTime = task.time;

				return (
					<div className="woocommerce-task-header__contents-container">
						<img
							alt={ __(
								'Appearance illustration',
								'woocommerce'
							) }
							src={
								WC_ASSET_URL +
								'images/task_list/expand-section-illustration.png'
							}
							className="svg-background"
						/>
						<div className="woocommerce-task-header__contents">
							<h1>{ taskTitle }</h1>
							<p>{ taskDescription }</p>
							<Button
								isSecondary={ task.isComplete }
								isPrimary={ ! task.isComplete }
								onClick={ onClick }
							>
								{ taskCta }
							</Button>
							<p className="woocommerce-task-header__timer">
								<img src={ TimerImage } alt="Timer" />{ ' ' }
								<span>{ taskTime }</span>
							</p>
						</div>
					</div>
				);
			} }
		</WooOnboardingTaskListHeader>
	);
};

registerPlugin( 'wc-calypso-bridge-appearance-task-header', {
	render: AppearanceHeader,
	scope: 'woocommerce-tasks',
} );
