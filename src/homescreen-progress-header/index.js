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
import './style.scss';

export const ProgressHeaderFill = () => (
	<Fill name="woocommerce_tasklist_experimental_progress_header_item">
		{ ( { taskListId } ) => {
			return <ProgressHeader taskListId={ taskListId } />;
		} }
	</Fill>
);

export const ProgressTitleFill = () => (
	<Fill name="woocommerce_tasklist_experimental_progress_title_item">
		{ ( { taskListId } ) => {
			return <ProgressTitle taskListId={ taskListId } />;
		} }
	</Fill>
);
