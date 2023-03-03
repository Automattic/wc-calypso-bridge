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
import DomainImage from './assets/images/add-domain-illustration.png';

const AddDomainHeader = () => {
	return (
		<WooOnboardingTaskListHeader id="add_domain">
			{ ( { task, goToTask } ) => {
				return (
					<div className="woocommerce-task-header__contents-container">
						<img
							alt={ __(
								'Add a domain illustration',
								'wc-calypso-bridge'
							) }
							src={ DomainImage }
							className="svg-background"
						/>
						<div className="woocommerce-task-header__contents">
							<h1>
								{ __( 'Add a domain', 'wc-calypso-bridge' ) }
							</h1>
							<p>
								{ __(
									'Choose a new website address for your store or transfer one you already own.',
									'wc-calypso-bridge'
								) }
							</p>
							<Button
								isSecondary={ task.isComplete }
								isPrimary={ ! task.isComplete }
								onClick={ goToTask }
							>
								{ __( 'Add a domain', 'wc-calypso-bridge' ) }
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

registerPlugin( 'wc-calypso-bridge-add-domain-task-header', {
	render: AddDomainHeader,
	scope: 'woocommerce-tasks',
} );
