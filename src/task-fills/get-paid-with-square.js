/**
 * External dependencies
 */
import React from 'react';
import { WooOnboardingTaskListItem } from '@woocommerce/onboarding';

export const GetPaidWithSquareFill = () => {
	return (
		<WooOnboardingTaskListItem id="get-paid-with-square">
			{ ( { defaultTaskItem: DefaultTaskItem } ) => (
				<DefaultTaskItem
					onClick={ () => {
						window.location.href =
							window.wcCalypsoBridge.square_connect_url;
					} }
				/>
			) }
		</WooOnboardingTaskListItem>
	);
};
