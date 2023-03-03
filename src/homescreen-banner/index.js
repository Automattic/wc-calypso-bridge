/**
 * External dependencies
 */
import { Fill, Card, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { OPTIONS_STORE_NAME } from '@woocommerce/data';
import { useSelect, useDispatch } from '@wordpress/data';
import { recordEvent } from '@woocommerce/tracks';

/**
 * Internal dependencies
 */
import './style.scss';
export const WC_CALYPSO_BRIDGE_HOMESCREEN_BANNER_HIDDEN =
	'wc_calypso_bridge_homescreen_banner_hidden';

export const CalypsoBridgeHomescreenBanner = () => {
	const { updateOptions } = useDispatch( OPTIONS_STORE_NAME );
	const { isModalHidden } = useSelect( ( select ) => {
		const { getOption, hasFinishedResolution } =
			select( OPTIONS_STORE_NAME );

		return {
			isModalHidden:
				getOption( WC_CALYPSO_BRIDGE_HOMESCREEN_BANNER_HIDDEN ) ===
					'yes' ||
				! hasFinishedResolution( 'getOption', [
					WC_CALYPSO_BRIDGE_HOMESCREEN_BANNER_HIDDEN,
				] ),
		};
	} );

	const dismissModal = () => {
		updateOptions( {
			[ WC_CALYPSO_BRIDGE_HOMESCREEN_BANNER_HIDDEN ]: 'yes',
		} );
	};

	return (
		! isModalHidden && (
			<Fill name="woocommerce_homescreen_experimental_header_banner_item">
				<Card>
					<div className="wc-calypso-bridge-woocommerce-admin-homescreen-banner">
						<p className="wc-calypso-bridge-woocommerce-admin-homescreen-banner__text">
							{ __(
								'This is your free trial test store where you can start exploring what\'s available! To find out more about the free trial, click "Learn more".',
								'wc-calypso-bridge'
							) }
						</p>
						<a
							href={ `https://wordpress.com/plans/${ window.wcCalypsoBridge.siteSlug }` }
							className="wc-calypso-bridge-woocommerce-admin-homescreen-banner__learn-more-button components-button is-secondary"
							onClick={ () => {
								recordEvent( 'free_trial_learn_more' );
							} }
						>
							{ __( 'Learn more', 'wc-calypso-bridge' ) }
						</a>
						<Button
							className="wc-calypso-bridge-woocommerce-admin-homescreen-banner__dismiss-button"
							label={ __(
								'Dismiss this free trial informational banner.'
							) }
							icon={
								<span className="dashicons dashicons-no-alt"></span>
							}
							onClick={ dismissModal }
						></Button>
					</div>
				</Card>
			</Fill>
		)
	);
};
