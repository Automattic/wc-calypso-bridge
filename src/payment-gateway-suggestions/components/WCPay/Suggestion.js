/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	WCPayBanner,
	WCPayBannerFooter,
	WCPayBenefits,
	WCPayBannerImageCut,
} from '@woocommerce/onboarding';
import { useDispatch } from '@wordpress/data';
import { CardBody } from '@wordpress/components';
import { Text } from '@woocommerce/experimental';
import interpolateComponents from '@automattic/interpolate-components';
import { Link } from '@woocommerce/components';

/**
 * Internal dependencies
 */

import { Action } from '../Action';
import { connectWcpay } from './utils';
import Notice from '../../../notice';
import './suggestion.scss';

const WCPayBannerText = ( { actionButton } ) => {
	return (
		<div className="woocommerce-recommended-payments-banner__text_container">
			<Text
				className="woocommerce-recommended-payments__header-title"
				variant="title.small"
				as="p"
				size="24"
				lineHeight="28px"
				padding="0 20px 0 0"
			>
				{ __(
					'Get ready to be paid and manage your business.',
					'wc-calypso-bridge'
				) }
			</Text>
			<Text
				className="woocommerce-recommended-payments__header-heading"
				variant="caption"
				as="p"
				size="12"
				lineHeight="16px"
			>
				{ interpolateComponents( {
					mixedString: __(
						'By using WooCommerce Payments you agree to be bound by our {{tosLink}}Terms of Service{{/tosLink}} and acknowledge that you have read our {{privacyLink}}Privacy Policy{{/privacyLink}} ',
						'wc-calypso-bridge'
					),
					components: {
						tosLink: (
							<Link
								href="https://wordpress.com/tos/"
								type="external"
								target="_blank"
							>
								<></>
							</Link>
						),
						privacyLink: (
							<Link
								href="https://automattic.com/privacy/"
								type="external"
								target="_blank"
							>
								<></>
							</Link>
						),
					},
				} ) }
			</Text>
			{ actionButton }
		</div>
	);
};

const WCPayBannerBody = ( { actionButton, textPosition, bannerImage } ) => {
	return (
		<CardBody className="woocommerce-recommended-payments-banner__body">
			{ textPosition === 'left' ? (
				<>
					<WCPayBannerText actionButton={ actionButton } />
					<div className="woocommerce-recommended-payments-banner__image_container">
						{ bannerImage }
					</div>
				</>
			) : (
				<>
					<div className="woocommerce-recommended-payments-banner__image_container">
						{ bannerImage }
					</div>
					<WCPayBannerText actionButton={ actionButton } />
				</>
			) }
		</CardBody>
	);
};

export const Suggestion = ( { paymentGateway, onSetupCallback = null } ) => {
	const {
		id,
		needsSetup,
		installed,
		enabled: isEnabled,
		installed: isInstalled,
	} = paymentGateway;

	const { createNotice } = useDispatch( 'core/notices' );
	// When the WC Pay is installed and onSetupCallback is null
	// Overwrite onSetupCallback to redirect to the setup page
	// when the user clicks on the "Finish setup" button.
	// WC Pay doesn't need to be configured in WCA.
	// It should be configured in its onboarding flow.
	if ( installed && onSetupCallback === null ) {
		onSetupCallback = () => {
			connectWcpay( createNotice );
		};
	}

	return (
		<div className="woocommerce-wcpay-suggestion">
			<Notice
				text={ interpolateComponents( {
					mixedString: __(
						"While you're in a free trial store, you'll only be able to place test orders. {{br}}{{/br}}To process real transactions, {{link}}pick a plan{{/link}}.",
						'wc-calypso-bridge'
					),
					components: {
						br: <br />,
						link: (
							<Link
								href="https://wordpress.com/"
								type="external"
								target="_blank"
							>
								<></>
							</Link>
						),
					},
				} ) }
			/>
			<WCPayBanner>
				<WCPayBannerBody
					textPosition="left"
					actionButton={
						<Action
							id={ id }
							hasSetup={ true }
							needsSetup={ needsSetup }
							isEnabled={ isEnabled }
							isRecommended={ true }
							isInstalled={ isInstalled }
							hasPlugins={ true }
							setupButtonText={ __(
								'Get started',
								'wc-calypso-bridge'
							) }
							onSetupCallback={ onSetupCallback }
						/>
					}
					bannerImage={ <WCPayBannerImageCut /> }
				/>
				<WCPayBannerFooter />
			</WCPayBanner>
			<WCPayBenefits />
		</div>
	);
};
