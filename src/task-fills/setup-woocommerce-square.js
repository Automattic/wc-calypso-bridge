/**
 * External dependencies
 */
import React from 'react';
import { WooOnboardingTaskListItem } from '@woocommerce/onboarding';

export const SetupWooCommerceSquareFill = () => {
	return (
		<WooOnboardingTaskListItem id="setup-woocommerce-square">
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
