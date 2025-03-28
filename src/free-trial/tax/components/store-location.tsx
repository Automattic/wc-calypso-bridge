/**
 * External dependencies
 */
import { SETTINGS_STORE_NAME } from '@woocommerce/data';
import { recordEvent } from '@woocommerce/tracks';
import { useEffect } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { getCountryCode } from '../../../utils';
import { hasCompleteAddress } from '../utils';
import { default as StoreLocationForm } from './location';

export const StoreLocation: React.FC< {
	nextStep: () => void;
} > = ( { nextStep } ) => {
	const { createNotice } = useDispatch( 'core/notices' );
	const { updateAndPersistSettingsForGroup } =
		useDispatch( SETTINGS_STORE_NAME );
	const { generalSettings, isResolving } = useSelect( ( select ) => {
		const { getSettings, hasFinishedResolution } =
			select( SETTINGS_STORE_NAME );

		return {
			generalSettings: getSettings( 'general' )?.general,
			isResolving: ! hasFinishedResolution( 'getSettings', [
				'general',
			] ),
		};
	} );

	useEffect( () => {
		if ( isResolving || ! hasCompleteAddress( generalSettings || {} ) ) {
			return;
		}
		nextStep();
	}, [ isResolving ] );

	if ( isResolving ) {
		return null;
	}

	return (
		<StoreLocationForm
			onComplete={ ( values: { [ key: string ]: string } ) => {
				const country = getCountryCode( values.countryState );
				recordEvent( 'tasklist_tax_set_location', {
					country,
				} );
				nextStep();
			} }
			isSettingsRequesting={ false }
			settings={ generalSettings }
			updateAndPersistSettingsForGroup={
				updateAndPersistSettingsForGroup
			}
			createNotice={ createNotice }
			isSettingsError={ false }
		/>
	);
};
