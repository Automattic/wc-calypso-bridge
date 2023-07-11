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
	PLUGINS_STORE_NAME,
	TaskType,
} from '@woocommerce/data';
import { queueRecordEvent, recordEvent } from '@woocommerce/tracks';
import { updateQueryString } from '@woocommerce/navigation';
import {
	useCallback,
	useEffect,
	useState,
	createElement,
} from '@wordpress/element';
import { Link } from '@woocommerce/components';
import interpolateComponents from '@automattic/interpolate-components';

/**
 * Internal dependencies
 */
import { redirectToTaxSettings, supportsAvalara } from './utils';
import { Card as WooCommerceTaxCard } from './woocommerce-tax/card';
import {
	getCountryCode,
	createNoticesFromResponse,
	getUpgradePlanLink,
} from '../../utils';
import { ManualConfiguration } from './manual-configuration';
import { WooCommerceTax } from './woocommerce-tax';
import Notice from '../../notice';
import { Card as AvalaraCard } from './avalara/card';
import { Partners } from './components/partners';

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

const trackUpgradeClick = () => {
	recordEvent( 'free_trial_upgrade_now', {
		source: 'tax_task',
	} );
};

const UpgradeNotice = () => (
	<div
		className="woocommerce-task-notice-container"
		style={ { maxWidth: '680px', margin: 'auto' } }
	>
		<Notice
			text={ interpolateComponents( {
				mixedString: __(
					'During the free trial period you can configure your sales tax settings, but not collect it. {{br/}}To start selling products, {{link}}upgrade now{{/link}}.',
					'wc-calypso-bridge'
				),
				components: {
					br: <br />,
					link: (
						<Link
							href={ getUpgradePlanLink() }
							type="external"
							target="_blank"
							onClick={ trackUpgradeClick }
						>
							<></>
						</Link>
					),
				},
			} ) }
		/>
	</div>
);

export const Tax: React.FC< TaxProps > = ( { onComplete, query, task } ) => {
	const [ isPending, setIsPending ] = useState( false );
	const { updateOptions } = useDispatch( OPTIONS_STORE_NAME );
	const { createNotice } = useDispatch( 'core/notices' );
	const { updateAndPersistSettingsForGroup } =
		useDispatch( SETTINGS_STORE_NAME );
	const { generalSettings, isResolving, taxSettings, avalaraInstallState } =
		useSelect( ( select ) => {
			const { getSettings, hasFinishedResolution } =
				select( SETTINGS_STORE_NAME );

			const { getPluginInstallState } = select( PLUGINS_STORE_NAME );

			return {
				generalSettings: getSettings( 'general' ).general,
				isResolving: ! hasFinishedResolution( 'getSettings', [
					'general',
				] ),
				taxSettings: getSettings( 'tax' ).tax || {},
				avalaraInstallState:
					getPluginInstallState( 'woocommerce-avatax' ),
			};
		} );

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

		const countryCode = getCountryCode(
			generalSettings?.woocommerce_default_country
		);

		const partners = [
			{
				id: 'woocommerce-tax',
				card: WooCommerceTaxCard,
				component: WooCommerceTax,
				isVisible:
					! taxJarActivated && // WCS integration doesn't work with the official TaxJar plugin.
					woocommerceTaxCountries.includes( countryCode ),
			},
			{
				id: 'avalara',
				card: AvalaraCard,
				component: null,
				isVisible:
					supportsAvalara( countryCode ) &&
					[ 'installed', 'activated' ].includes(
						avalaraInstallState
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

		if ( partners.length === 1 && partners[ 0 ].component ) {
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
			<>
				<UpgradeNotice />
				<TaskCard>
					<ManualConfiguration { ...childProps } />
				</TaskCard>
			</>
		);
	}

	if ( currentPartner ) {
		return (
			<>
				<UpgradeNotice />
				<TaskCard>
					{ currentPartner.component &&
						createElement( currentPartner.component, childProps ) }
				</TaskCard>
			</>
		);
	}

	return (
		<>
			<UpgradeNotice />
			<Partners { ...childProps }>
				{ partners.map(
					( partner ) =>
						partner.card &&
						createElement( partner.card, {
							key: partner.id,
							...childProps,
						} )
				) }
			</Partners>
		</>
	);
};
