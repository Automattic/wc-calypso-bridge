/**
 * External dependencies
 */
import React from 'react';
import { WooOnboardingTaskListItem } from '@woocommerce/onboarding';

export const GetPaidWithPayPalFill = () => {
	console.log(
		'hola en el getpaid fill',
		window.wcCalypsoBridge.paypal_connect_url
	);
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
