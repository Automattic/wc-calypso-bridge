/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Card, CardBody, Spinner } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { getAdminLink } from '@woocommerce/settings';
import {
	OPTIONS_STORE_NAME,
	SETTINGS_STORE_NAME,
	TaskType,
} from '@woocommerce/data';
import { queueRecordEvent, recordEvent } from '@woocommerce/tracks';
import { registerPlugin } from '@wordpress/plugins';
import { updateQueryString } from '@woocommerce/navigation';
import {
	useCallback,
	useEffect,
	useState,
	createElement,
} from '@wordpress/element';
import { WooOnboardingTask } from '@woocommerce/onboarding';

/**
 * Internal dependencies
 */
import { redirectToTaxSettings } from './utils';
import { Card as WooCommerceTaxCard } from './woocommerce-tax/card';
import { getCountryCode, createNoticesFromResponse } from '../../utils';
import { ManualConfiguration } from './manual-configuration';
import { WooCommerceTax } from './woocommerce-tax';

const TaskCard: React.FC = ( { children } ) => {
	return (
		<Card className="woocommerce-task-card">
			<CardBody>{ children }</CardBody>
		</Card>
	);
};

export type TaxProps = {
	onComplete: () => void;
	query: Record< string, string >;
	task: TaskType;
};

export const Tax: React.FC< TaxProps > = ( { onComplete, query, task } ) => {
	const [ isPending, setIsPending ] = useState( false );
	const { updateOptions } = useDispatch( OPTIONS_STORE_NAME );
	const { createNotice } = useDispatch( 'core/notices' );
	const { updateAndPersistSettingsForGroup } =
		useDispatch( SETTINGS_STORE_NAME );
	const { generalSettings, isResolving, taxSettings } = useSelect(
		( select ) => {
			const { getSettings, hasFinishedResolution } =
				select( SETTINGS_STORE_NAME );
			return {
				generalSettings: getSettings( 'general' ).general,
				isResolving: ! hasFinishedResolution( 'getSettings', [
					'general',
				] ),
				taxSettings: getSettings( 'tax' ).tax || {},
			};
		}
	);

	const onManual = useCallback( async () => {
		setIsPending( true );
		if ( generalSettings?.woocommerce_calc_taxes !== 'yes' ) {
			updateAndPersistSettingsForGroup( 'tax', {
				tax: {
					...taxSettings,
					wc_connect_taxes_enabled: 'no',
				},
			} );
			updateAndPersistSettingsForGroup( 'general', {
				general: {
					...generalSettings,
					woocommerce_calc_taxes: 'yes',
				},
			} )
				.then( () => redirectToTaxSettings() )
				.catch( ( error: unknown ) => {
					setIsPending( false );
					createNoticesFromResponse( error );
				} );
		} else {
			redirectToTaxSettings();
		}
	}, [] );

	const onAutomate = useCallback( async () => {
		setIsPending( true );
		try {
			await Promise.all( [
				updateAndPersistSettingsForGroup( 'tax', {
					tax: {
						...taxSettings,
						wc_connect_taxes_enabled: 'yes',
					},
				} ),
				updateAndPersistSettingsForGroup( 'general', {
					general: {
						...generalSettings,
						woocommerce_calc_taxes: 'yes',
					},
				} ),
			] );
		} catch ( error: unknown ) {
			setIsPending( false );
			createNotice(
				'error',
				__(
					'There was a problem setting up automated taxes. Please try again.',
					'woocommerce'
				)
			);
			return;
		}

		createNotice(
			'success',
			__(
				"You're awesome! One less item on your to-do list ✅",
				'woocommerce'
			)
		);
		onComplete();
	}, [] );

	const onDisable = useCallback( () => {
		setIsPending( true );
		queueRecordEvent( 'tasklist_tax_connect_store', {
			connect: false,
			no_tax: true,
		} );

		updateOptions( {
			woocommerce_no_sales_tax: true,
			woocommerce_calc_taxes: 'no',
		} ).then( () => {
			window.location.href = getAdminLink( 'admin.php?page=wc-admin' );
		} );
	}, [] );

	const getVisiblePartners = () => {
		const {
			additionalData: {
				woocommerceTaxCountries = [],
				taxJarActivated,
			} = {},
		} = task;

		const partners = [
			{
				id: 'woocommerce-tax',
				card: WooCommerceTaxCard,
				component: WooCommerceTax,
				isVisible:
					! taxJarActivated && // WCS integration doesn't work with the official TaxJar plugin.
					woocommerceTaxCountries.includes(
						getCountryCode(
							generalSettings?.woocommerce_default_country
						)
					),
			},
		];

		return partners.filter( ( partner ) => partner.isVisible );
	};

	const partners = getVisiblePartners();

	useEffect( () => {
		const { auto } = query;

		if ( auto === 'true' ) {
			onAutomate();
			return;
		}

		if ( query.partner ) {
			return;
		}

		recordEvent( 'tasklist_tax_view_options', {
			options: partners.map( ( partner ) => partner.id ),
		} );
	}, [] );

	const getCurrentPartner = () => {
		if ( ! query.partner ) {
			return null;
		}

		return (
			partners.find( ( partner ) => partner.id === query.partner ) || null
		);
	};

	useEffect( () => {
		if ( partners.length > 1 || query.partner ) {
			return;
		}

		if ( partners.length === 1 ) {
			updateQueryString( {
				partner: partners[ 0 ].id,
			} );
		}
	}, [ partners ] );

	const childProps = {
		isPending,
		onAutomate,
		onManual,
		onDisable,
		task,
	};

	if ( isResolving ) {
		return <Spinner />;
	}

	const currentPartner = getCurrentPartner();

	if ( ! partners.length ) {
		return (
			<TaskCard>
				<ManualConfiguration { ...childProps } />
			</TaskCard>
		);
	}

	if ( currentPartner ) {
		return (
			<TaskCard>
				{ currentPartner.component &&
					createElement( currentPartner.component, childProps ) }
			</TaskCard>
		);
	}
	return <p>Nothing here!</p>;
};
