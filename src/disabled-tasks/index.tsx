/**
 * External dependencies
 */

import { Fill } from '@wordpress/components';
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './style.scss';
import { ExperimentalCollapsibleList } from './collapsible-list';

export const DisabledTasks = () => {
	const notice = __(
		"You're currently using a free trial! To get access to the full range of features, please sign up for a plan.",
		'wc-calypso-bridge'
	);
	const signupUrl =
		'https://wordpress.com/plans/' + window.wcCalypsoBridge.siteSlug;
	return (
		<ExperimentalCollapsibleList
			collapseLabel="Upgrade to paid plan to unlock the next tasks"
			expandLabel="Upgrade to paid plan to unlock the next tasks"
			show={ 0 }
			collapsed={ true }
			onCollapse={ () => {
				// eslint-disable-next-line no-console
				console.log( 'collapsed' );
			} }
			onExpand={ () => {
				// eslint-disable-next-line no-console
				console.log( 'expanded' );
			} }
			direction="bottom"
			className="free-trial-disabled-tasks"
		>
			<div className="free-trial-disabled-tasks-content">
				<p>{ notice }</p>
				<Button href={ signupUrl } variant="primary">
					{ __( 'Sign up for a plan', 'wc-calypso-bridge' ) }
				</Button>
				<p className="disabled-task">
					{ __( 'Grow your business', 'wc-calypso-bridge' ) }
				</p>
				<p className="disabled-task">
					{ __(
						'Launch your store and start selling!',
						'wc-calypso-bridge'
					) }
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
