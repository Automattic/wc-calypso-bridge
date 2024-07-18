/**
 * External dependencies
 */
import React from 'react';
import { WooOnboardingTaskListItem } from '@woocommerce/onboarding';

export const GetPaidWithPayPalFill = () => {
	return (
		<WooOnboardingTaskListItem id="get-paid-with-paypal">
			{ ( { defaultTaskItem: DefaultTaskItem } ) => (
				<DefaultTaskItem
					onClick={ () => {
						window.location.href =
							window.wcCalypsoBridge.paypal_connect_url;
					} }
				/>
			) }
		</WooOnboardingTaskListItem>
	);
};
