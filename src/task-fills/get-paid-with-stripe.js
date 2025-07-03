/**
 * External dependencies
 */
import React from 'react';
import { WooOnboardingTaskListItem } from '@woocommerce/onboarding';

export const GetPaidWithStripeFill = () => {
	return (
		<WooOnboardingTaskListItem id="get-paid-with-stripe">
			{ ( { defaultTaskItem: DefaultTaskItem } ) => (
				<DefaultTaskItem
					onClick={ () => {
						window.location.href =
							window.wcCalypsoBridge.stripe_connect_url;
					} }
				/>
			) }
		</WooOnboardingTaskListItem>
	);
};
