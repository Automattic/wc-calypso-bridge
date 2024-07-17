/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { Spinner } from '@woocommerce/components';
import { PLUGINS_STORE_NAME, SETTINGS_STORE_NAME } from '@woocommerce/data';

/**
 * Internal dependencies
 */
import { TaxChildProps } from '../utils';
import { Setup } from './setup';

export const WooCommerceTax: React.FC< TaxChildProps > = ( {
	isPending,
	onAutomate,
	onManual,
	onDisable,
} ) => {
	const { isResolving } = useSelect( ( select ) => {
		const { getSettings } = select( SETTINGS_STORE_NAME );
		const { getActivePlugins, hasFinishedResolution } =
			select( PLUGINS_STORE_NAME );
		getActivePlugins();

		return {
			generalSettings: getSettings( 'general' ).general,
			isResolving:
				! select( SETTINGS_STORE_NAME ).hasFinishedResolution(
					'getSettings',
					[ 'general' ]
				) ||
				! hasFinishedResolution( 'getActivePlugins' ),
		};
	} );

	if ( isResolving ) {
		return <Spinner />;
	}

	const childProps = {
		isPending,
		onAutomate,
		onManual,
		onDisable,
	};

	return <Setup { ...childProps } />;
};
