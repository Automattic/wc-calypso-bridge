/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { getAdminLink } from '@woocommerce/settings';
import interpolateComponents from '@automattic/interpolate-components';
import { recordEvent } from '@woocommerce/tracks';
import { useDispatch } from '@wordpress/data';
import { PLUGINS_STORE_NAME } from '@woocommerce/data';

/**
 * Internal dependencies
 */
import { PartnerCard } from '../components/partner-card';
import { TaxChildProps } from '../utils';
import logo from './logo.png';

export const Card: React.FC< TaxChildProps > = ( { task } ) => {
	const { additionalData: { avalaraActivated } = {} } = task;
	const { activatePlugins } = useDispatch( PLUGINS_STORE_NAME );
	const { createNotice } = useDispatch( 'core/notices' );
	const [ isActivating, setIsActivating ] = useState( false );

	return (
		<PartnerCard
			name={ __( 'Avalara', 'woocommerce' ) }
			logo={ logo }
			description={ __( 'Powerful all-in-one tax tool', 'woocommerce' ) }
			benefits={ [
				__( 'Real-time sales tax calculation', 'woocommerce' ),
				interpolateComponents( {
					mixedString: __(
						'{{strong}}Multi{{/strong}}-economic nexus compliance',
						'woocommerce'
					),
					components: {
						strong: <strong />,
					},
				} ),
				__(
					'Cross-border and multi-channel compliance',
					'woocommerce'
				),
				__( 'Automate filing & remittance', 'woocommerce' ),
				__(
					'Return-ready, jurisdiction-level reporting.',
					'woocommerce'
				),
			] }
			terms={ '' }
			actionText={
				avalaraActivated
					? __( 'Continue setup', 'woocommerce' )
					: __( 'Enable & set up', 'woocommerce' )
			}
			isBusy={ isActivating }
			onClick={ async () => {
				recordEvent( 'tasklist_tax_select_option', {
					selected_option: 'avalara',
				} );

				if ( ! avalaraActivated ) {
					try {
						setIsActivating( true );
						await activatePlugins( [ 'woocommerce-avatax' ] );
						setIsActivating( false );
					} catch ( error ) {
						createNotice(
							'error',
							__(
								'There was a problem activating the plugin. Please try again.',
								'woocommerce'
							)
						);
						setIsActivating( false );
						return;
					}
				}

				window.location.href = getAdminLink(
					'admin.php?page=wc-settings&tab=tax&section=avatax'
				);
			} }
		/>
	);
};
