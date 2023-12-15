/**
 * External dependencies
 */
import { Fill, Card, Button } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import { OPTIONS_STORE_NAME } from '@woocommerce/data';
import { useSelect, useDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import './style.scss';

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
	};

	return (
		! isModalHidden && (
			<Fill name="woocommerce_homescreen_experimental_header_banner_item">
				<Card>
					<div className="wc-calypso-bridge-woocommerce-admin-introductory-offer-banner">
						<div className="wc-calypso-bridge-woocommerce-admin-introductory-offer-banner__text">
							<p>
								{ __(
									'Start selling — for less!',
									'wc-calypso-bridge'
								) }
							</p>
							<p>
								{ sprintf(
									/* translators: First two %s do not need to be translated. Possible values for the last %s is day, days, month, and months */
									__(
										'Upgrade your plan for %s for your first %s %s',
										'wc-calypso-bridge'
									),
									window.wcCalypsoBridge.introductoryOffer
										.formattedPrice,
									window.wcCalypsoBridge.introductoryOffer
										.intervalCount,
									window.wcCalypsoBridge.introductoryOffer
										.formattedIntervalUnit
								) }
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
									'Dismiss this introductory offer informational banner.'
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
