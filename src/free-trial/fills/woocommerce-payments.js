/**
 * External dependencies
 */
import React, { useEffect } from 'react';
import { WooOnboardingTask } from '@woocommerce/onboarding';
import { useDispatch } from '@wordpress/data';
import { Spinner } from '@woocommerce/components';

/**
 * Internal dependencies
 */
import { connectWcpay } from '../../payment-gateway-suggestions/components/WCPay/utils';

const ReadyWcPay = () => {
	const { createNotice } = useDispatch( 'core/notices' );

	useEffect( () => {
		// Attempt to connect. Catch is not provided since notice is handled in the util.
		connectWcpay( createNotice, () => {} );
	}, [ createNotice ] );

	return (
		<div
			style={ {
				height: '70vh',
				display: 'flex',
				flexDirection: 'column',
				justifyContent: 'center',
				alignItems: 'center',
			} }
		>
			<Spinner />
			<div style={ { marginTop: '1rem' } }>
				Preparing payment settings...
			</div>
		</div>
	);
};

// shows up at http://host/wp-admin/admin.php?page=wc-admin&task=woocommerce-payments which is the default url for woocommerce-payments task
export const WoocommercePaymentsTaskPage = () => (
	<WooOnboardingTask id="woocommerce-payments">
		<ReadyWcPay />
	</WooOnboardingTask>
);
