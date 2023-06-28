/**
 * External dependencies
 */
import React from 'react';
import { WooOnboardingTaskListItem } from '@woocommerce/onboarding';
import { useAppearanceClick } from '../utils/hooks/use-appearance-click';

export const AppearanceFill = () => {
	const { onClick } = useAppearanceClick();
	return (
		<WooOnboardingTaskListItem id="appearance">
			{ ( { defaultTaskItem: DefaultTaskItem } ) => (
				<DefaultTaskItem
					// Override task click so it doesn't navigate to a task component.
					onClick={ onClick }
				/>
			) }
		</WooOnboardingTaskListItem>
	);
};
