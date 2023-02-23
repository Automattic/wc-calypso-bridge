/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { getVisibleTasks, ONBOARDING_STORE_NAME } from '@woocommerce/data';

/**
 * Internal dependencies
 */
import { TaskListMenu } from './task-list-menu';

export type ProgressHeaderProps = {
	taskListId: string;
};

export const ProgressHeader: React.FC< ProgressHeaderProps > = ( {
	taskListId,
} ) => {
	const { loading, tasksCount, completedCount } = useSelect( ( select ) => {
		const taskList = select( ONBOARDING_STORE_NAME ).getTaskList(
			taskListId
		);
		const finishedResolution = select(
			ONBOARDING_STORE_NAME
		).hasFinishedResolution( 'getTaskList', [ taskListId ] );
		const visibleTasks = getVisibleTasks( taskList?.tasks || [] );

		return {
			loading: ! finishedResolution,
			tasksCount: visibleTasks?.length,
			completedCount: visibleTasks?.filter( ( task ) => task.isComplete )
				.length,
		};
	}, [] );

	if ( loading ) {
		return null;
	}

	return (
		<div className="woocommerce-task-progress-header">
			<TaskListMenu
				id={ taskListId }
				hideTaskListText={ __( 'Hide setup list', 'woocommerce' ) }
			/>
			<div className="woocommerce-task-progress-header__contents">
				{ completedCount !== tasksCount ? (
					<>
						<p>
							{ sprintf(
								/* translators: 1: completed tasks, 2: total tasks */
								__(
									'Follow these steps to start selling quickly. %1$d out of %2$d complete.',
									'woocommerce'
								),
								completedCount,
								tasksCount
							) }
						</p>
						<progress
							className="woocommerce-task-progress-header__progress-bar"
							max={ tasksCount }
							value={ completedCount || 0 }
						/>
					</>
				) : null }
			</div>
		</div>
	);
};
