/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { difference } from 'lodash';
import { useEffect, useState } from '@wordpress/element';
import { Stepper } from '@woocommerce/components';
import {
	OPTIONS_STORE_NAME,
	PLUGINS_STORE_NAME,
	SETTINGS_STORE_NAME,
} from '@woocommerce/data';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { AUTOMATION_PLUGINS } from '../utils';
import { StoreLocation } from '../components/store-location';
import './setup.scss';
import { AutomatedTaxes } from './automated-taxes';

export type SetupProps = {
	isPending: boolean;
	onDisable: () => void;
	onAutomate: () => void;
	onManual: () => void;
};

export type SetupStepProps = {
	isPending: boolean;
	isResolving: boolean;
	nextStep: () => void;
	onDisable: () => void;
	onAutomate: () => void;
	onManual: () => void;
	pluginsToActivate: string[];
};

export const Setup: React.FC< SetupProps > = ( {
	isPending,
	onDisable,
	onAutomate,
	onManual,
} ) => {
	const [ pluginsToActivate, setPluginsToActivate ] = useState< string[] >(
		[]
	);
	const { activePlugins, isResolving } = useSelect( ( select ) => {
		const { getSettings } = select( SETTINGS_STORE_NAME );
		const { hasFinishedResolution } = select( OPTIONS_STORE_NAME );
		const { getActivePlugins } = select( PLUGINS_STORE_NAME );

		return {
			activePlugins: getActivePlugins(),
			generalSettings: getSettings( 'general' )?.general,
			isResolving:
				! hasFinishedResolution( 'getOption', [
					'woocommerce_setup_jetpack_opted_in',
				] ) ||
				! hasFinishedResolution( 'getOption', [
					'wc_connect_options',
				] ),
		};
	} );
	const [ stepIndex, setStepIndex ] = useState( 0 );

	useEffect( () => {
		const remainingPlugins = difference(
			AUTOMATION_PLUGINS,
			activePlugins
		);
		if ( remainingPlugins.length <= pluginsToActivate.length ) {
			return;
		}
		setPluginsToActivate( remainingPlugins );
	}, [ activePlugins ] );

	const nextStep = () => {
		setStepIndex( stepIndex + 1 );
	};

	const stepProps = {
		isPending,
		isResolving,
		onAutomate,
		onDisable,
		nextStep,
		onManual,
		pluginsToActivate,
	};

	const steps = [
		{
			key: 'store_location',
			label: __( 'Set store location', 'woocommerce' ),
			description: __(
				'The address from which your business operates',
				'woocommerce'
			),
			content: <StoreLocation { ...stepProps } />,
		},
		{
			key: 'connect',
			label: __( 'Automate Taxes', 'woocommerce' ),
			description: __(
				'WooCommerce Tax can automate your sales tax calculations for you.',
				'woocommerce'
			),
			content: <AutomatedTaxes { ...stepProps } />,
		},
	];

	const step = steps[ stepIndex ];

	return (
		<Stepper
			isPending={ isResolving }
			isVertical={ true }
			currentStep={ step.key }
			steps={ steps }
		/>
	);
};
