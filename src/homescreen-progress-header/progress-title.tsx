/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { useMemo } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { getVisibleTasks, ONBOARDING_STORE_NAME } from '@woocommerce/data';
import { getSetting } from '@woocommerce/settings';

/**
 * Internal dependencies
 */
import { sanitizeHTML } from '../payment-gateway-suggestions/utils';

export type ProgressTitleProps = {
	taskListId: string;
};

export const ProgressTitle: React.FC< ProgressTitleProps > = ( {
	taskListId,
} ) => {
	const { loading, tasksCount, completedCount, hasVisitedTasks } = useSelect(
		( select ) => {
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
				completedCount: visibleTasks?.filter(
					( task ) => task.isComplete
				).length,
				hasVisitedTasks:
					visibleTasks?.filter(
						( task ) =>
							task.isVisited && task.id !== 'store_details'
					).length > 0,
			};
		},
		[ taskListId ]
	);

	const title = useMemo( () => {
		if (
			! hasVisitedTasks ||
			completedCount === tasksCount ||
			completedCount === 0
		) {
			const siteTitle = getSetting( 'siteTitle' );
			return siteTitle
				? sprintf(
						/* translators: %s = site title */
						__( 'Welcome to %s', 'woocommerce' ),
						siteTitle
				  )
				: __( 'Welcome to your Woo Express store', 'woocommerce' );
		}
		switch ( completedCount ) {
			case 1:
			case 2:
				return __(
					'Only a few more tasks to tick off!',
					'wc-calypso-bridge'
				);
			case 3:
			case 4:
			case tasksCount - 1:
				return __(
					'Woo! We’ve made it to the last step! Great job',
					'wc-calypso-bridge'
				);
			default:
				return __(
					'Everything is looking great, keep it up!',
					'wc-calypso-bridge'
				);
		}
	}, [ completedCount, hasVisitedTasks, tasksCount ] );

	if ( loading ) {
		return null;
	}

	return (
		<h1
			id="woocommerce-task-progress-header__title"
			className="woocommerce-task-progress-header__title"
			dangerouslySetInnerHTML={ sanitizeHTML( title ) }
		/>
	);
};
