/**
 * External dependencies
 */
import { Fill, Card, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { OPTIONS_STORE_NAME } from '@woocommerce/data';
import { useSelect, useDispatch } from '@wordpress/data';
import { recordEvent } from '@woocommerce/tracks';
import { recordTracksEvent as calypsoRecordTracksEvent } from '@automattic/calypso-analytics'
import { useEffect } from '@wordpress/element';
import { getScreenFromPath, parseAdminUrl } from '@woocommerce/navigation';

/**
 * Internal dependencies
 */
import './style.scss';
import { getOfferMessage } from './get-offer-message';

export const WC_CALYPSO_BRIDGE_INTRODUCTORY_OFFER_BANNER_HIDDEN =
	'wc_calypso_bridge_introductory_offer_banner_hidden';

export const CalypsoBridgeIntroductoryOfferBanner = () => {
	const { updateOptions } = useDispatch( OPTIONS_STORE_NAME );
	const { isModalHidden } = useSelect( ( select ) => {
		const { getOption, hasFinishedResolution } =
			select( OPTIONS_STORE_NAME );

		return {
			isModalHidden:
				getOption(
					WC_CALYPSO_BRIDGE_INTRODUCTORY_OFFER_BANNER_HIDDEN
				) === 'yes' ||
				! hasFinishedResolution( 'getOption', [
					WC_CALYPSO_BRIDGE_INTRODUCTORY_OFFER_BANNER_HIDDEN,
				] ),
		};
	} );

	const dismissModal = () => {
		updateOptions( {
			[ WC_CALYPSO_BRIDGE_INTRODUCTORY_OFFER_BANNER_HIDDEN ]: 'yes',
		} );

		recordEvent( 'free_trial_homescreen_offer_banner_dismiss' );
	};

	const screenPath = getScreenFromPath();
	const taskIsNull =
		parseAdminUrl( window.location.href ).searchParams.get( 'task' ) ===
		null;

	useEffect( () => {
		if ( screenPath === 'homescreen' && taskIsNull ) {
			calypsoRecordTracksEvent( 'calypso_wooexpress_one_dollar_offer', {
				location: 'homescreen',
			} );
		}
	}, [ screenPath, taskIsNull ] );

	const offer = window.wcCalypsoBridge.wooExpressIntroductoryOffer;

	return (
		! isModalHidden && (
			<Fill name="woocommerce_homescreen_experimental_header_banner_item">
				<Card className="wc-calypso-bridge-woocommerce-admin-introductory-offer-banner__container">
					<div className="wc-calypso-bridge-woocommerce-admin-introductory-offer-banner">
						<div className="wc-calypso-bridge-woocommerce-admin-introductory-offer-banner__text">
							<p>
								{ __(
									'Start selling — for less!',
									'wc-calypso-bridge'
								) }
							</p>
							<p>
								{ getOfferMessage( {
									formattedPrice: offer.formattedPrice,
									intervalUnit: offer.intervalUnit,
									intervalCount: offer.intervalCount,
								} ) }
							</p>
						</div>
						<div className="wc-calypso-bridge-woocommerce-admin-introductory-offer-banner__actions">
							<a
								href={ `https://wordpress.com/plans/${ window.wcCalypsoBridge.siteSlug }` }
								className="wc-calypso-bridge-woocommerce-admin-introductory-offer-banner__upgrade-button components-button is-secondary"
							>
								{ __( 'Upgrade now', 'wc-calypso-bridge' ) }
							</a>
							<Button
								className="wc-calypso-bridge-woocommerce-admin-introductory-offer-banner__dismiss-button"
								label={ __(
									'Dismiss this banner.',
									'wc-calypso-bridge'
								) }
								icon={
									<span className="dashicons dashicons-no-alt"></span>
								}
								onClick={ dismissModal }
							></Button>
						</div>
					</div>
				</Card>
			</Fill>
		)
	);
};
