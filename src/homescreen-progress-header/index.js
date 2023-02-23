/**
 * External dependencies
 */
import { registerPlugin } from '@wordpress/plugins';
import { Fill } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { ProgressHeader } from './progress-header';
import { ProgressTitle } from './progress-title';

export const ProgressHeaderFill = () => (
	<Fill name="woocommerce_tasklist_experimental_progress_header_item">
		{ ( { taskListId } ) => <ProgressHeader taskListId={ taskListId } /> }
	</Fill>
);

export const ProgressTitleFill = () => (
	<Fill name="woocommerce_tasklist_experimental_progress_title_item">
		<ProgressTitle />
	</Fill>
);

// registerPlugin( 'my-extension', {
// 	render: () => (
// 		<Fill name="woocommerce_tasklist_experimental_progress_header_item">
// 			{ ( { taskListId } ) => (
// 				<ProgressHeader taskListId={ taskListId } />
// 			) }
// 		</Fill>
// 	),
// 	scope: 'woocommerce-admin',
// } );

// registerPlugin( 'wc-calypso-bridge-progress-title', {
// 	render: () => (
// 		<Fill name="woocommerce_tasklist_experimental_progress_title_item">
// 			<ProgressTitle />
// 		</Fill>
// 	),
// 	scope: 'woocommerce-admin',
// } );
