/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { recordEvent } from '@woocommerce/tracks';

/**
 * Internal dependencies
 */
import { default as ConnectForm } from './connect-form';

type ConnectProps = {
	onConnect: () => void;
};

export const Connect: React.FC< ConnectProps > = ( { onConnect } ) => {
	return (
		<ConnectForm
			// @ts-expect-error ConnectForm is pure JS component
			onConnect={ () => {
				recordEvent( 'tasklist_shipping_recommendation_connect_store', {
					connect: true,
				} );
				onConnect?.();
			} }
		/>
	);
};
