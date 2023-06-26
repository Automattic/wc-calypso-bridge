/**
 * External dependencies
 */

import { Fill, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { recordEvent } from '@woocommerce/tracks';

/**
 * Internal dependencies
 */
import './style.scss';
import { ExperimentalCollapsibleList } from './collapsible-list';

export const DisabledTasks = () => {
	const notice = __(
		"You're currently using a free trial! To get access to the full range of features, please upgrade to a paid plan.",
		'wc-calypso-bridge'
	);
	const listLabel = __(
		'Upgrade to a paid plan to unlock more features and start selling',
		'wc-calypso-bridge'
	);
	const signupUrl =
		'https://wordpress.com/plans/' + window.wcCalypsoBridge.siteSlug;
	return (
		<ExperimentalCollapsibleList
			collapseLabel={ listLabel }
			expandLabel={ listLabel }
			show={ 0 }
			collapsed={ true }
			direction="bottom"
			className="free-trial-disabled-tasks"
		>
			<div className="free-trial-disabled-tasks-content">
				<p>{ notice }</p>
				<Button
					href={ signupUrl }
					variant="secondary"
					onClick={ () => {
						recordEvent( 'free_trial_upgrade_now', {
							source: 'task_list',
						} );
					} }
				>
					{ __( 'Upgrade now', 'wc-calypso-bridge' ) }
				</Button>
				<p className="disabled-task">
					{ __( 'Get more sales', 'wc-calypso-bridge' ) }
				</p>
				<p className="disabled-task">
					{ __( 'Launch your store', 'wc-calypso-bridge' ) }
				</p>
			</div>
		</ExperimentalCollapsibleList>
	);
};

export const DisabledTasksFill = () => (
	<Fill name="experimental_woocommerce_tasklist_footer_item">
		<DisabledTasks />
	</Fill>
);
