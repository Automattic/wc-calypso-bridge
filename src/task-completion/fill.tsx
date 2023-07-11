// eslint-disable-next-line @typescript-eslint/ban-ts-comment
// @ts-nocheck
/**
 * External dependencies
 */
import { Fill } from '@wordpress/components';
/**
 * Internal dependencies
 */
import { TaskListCompletedHeader } from './index';

export const TaskListCompletedHeaderFill = () => (
	<Fill name="woocommerce_experimental_task_list_completion">
		{ ( { hideTasks, keepTasks, customerEffortScore } ) => {
			return (
				<TaskListCompletedHeader
					hideTasks={ hideTasks }
					keepTasks={ keepTasks }
					customerEffortScore={ customerEffortScore }
				></TaskListCompletedHeader>
			);
		} }
	</Fill>
);
