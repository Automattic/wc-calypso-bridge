/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { useEffect } from 'react';
import { recordEvent } from '@woocommerce/tracks';

/**
 * Internal dependencies
 */
import AutomateWooBanner from './images/automate-woo.svg';
import GoogleAdsBanner from './images/google-ads.svg';
import SocialMediaBanner from './images/social-media.svg';
import GiftCardBanner from './images/gift-card.svg';
import BrowserImage from './browser-image';
import PeopleImage from './images/browser-people.jpg';
import './style.scss';

const FeaturedItem = ( {
	bannerImage,
	title,
	description,
	actionButton,
}: {
	bannerImage: ReactNode;
	title: ReactNode;
	description: ReactNode;
	actionButton: ReactNode;
} ) => {
	return (
		<div className="woocommerce-marketing-free-trial-featured-item">
			<div>
				<img src={ bannerImage } />
			</div>
			<div className="woocommerce-marketing-free-trial-featured-item-content">
				<h3>{ title }</h3>
				<p>{ description }</p>
				{ actionButton }
			</div>
		</div>
	);
};

const UpgradeButton = ( { primary = false }: { primary?: boolean } ) => {
	return (
		<Button
			href={
				'https://wordpress.com/plans/' + window.wcCalypsoBridge.siteSlug
			}
			variant={ primary ? 'primary' : 'secondary' }
			onClick={ () => {
				recordEvent( 'free_trial_upgrade_now', {
					source: 'marketing',
				} );
			} }
		>
			{ __( 'Upgrade now', 'wc-calypso-bridge' ) }
		</Button>
	);
};

export const Marketing = () => {
	useEffect( () => {
		const className = 'free-trial-page-marketing';
		document.body.classList.add( className );
		return () => {
			document.body.classList.remove( className );
		};
	}, [] );

	return (
		<div className="woocommerce-marketing-free-trial">
			<div className="woocommerce-marketing-free-trial-page-title">
				{ __( 'Marketing', 'wc-calypso-bridge' ) }
			</div>
			<div className="woocommerce-marketing-free-trial-welcome">
				<h1>
					{ __(
						'Get ready to grow your business',
						'wc-calypso-bridge'
					) }
				</h1>
				<p>
					{ __(
						'Reach more customers and grow your business with our built-in marketing and advertising tools. Upgrade to a paid plan to unlock our powerful marketing tools, and start growing your business today!',
						'wc-calypso-bridge'
					) }
				</p>
				<UpgradeButton primary={ true } />
			</div>
			<div className="woocommerce-marketing-free-trial-hero">
				<div className="woocommerce-marketing-free-trial-hero-image">
					<BrowserImage
						text={ __(
							'Grow your business with hundreds of extensions',
							'wc-calypso-bridge'
						) }
						image={ PeopleImage }
					/>
				</div>
			</div>
			<div className="woocommerce-marketing-free-trial-featured-items">
				<h2 className="woocommerce-marketing-free-trial-featured-items-title">
					{ __(
						'Discover our built-in marketing tools to reach more customers and boost sales',
						'wc-calypso-bridge'
					) }
				</h2>
				<FeaturedItem
					bannerImage={ AutomateWooBanner }
					title={ __(
						'Automate your marketing',
						'wc-calypso-bridge'
					) }
					description={ __(
						'Drive sales and build loyalty through automated marketing messages that respond to your customer’s purchase data.',
						'wc-calypso-bridge'
					) }
					actionButton={
						<Button
							href="/wp-admin/admin.php?page=automatewoo-dashboard"
							variant="secondary"
							onClick={ () => {
								recordEvent( 'free_trial_try_automatewoo', {
									source: 'marketing',
								} );
							} }
						>
							{ __( 'Try AutomateWoo', 'wc-calypso-bridge' ) }
						</Button>
					}
				/>
				<FeaturedItem
					bannerImage={ GoogleAdsBanner }
					title={ __(
						'Advertise your products on Google',
						'wc-calypso-bridge'
					) }
					description={ __(
						'Reach active shoppers across Google with product listings and ads that you can create and manage straight from your dashboard.',
						'wc-calypso-bridge'
					) }
					actionButton={ <UpgradeButton /> }
				/>
				<FeaturedItem
					bannerImage={ SocialMediaBanner }
					title={ __(
						'Reach more customers across social media',
						'wc-calypso-bridge'
					) }
					description={ __(
						'Get your products in front of millions of engaged shoppers browsing TikTok, Pinterest, and Meta platforms with social advertising.',
						'wc-calypso-bridge'
					) }
					actionButton={ <UpgradeButton /> }
				/>

				<FeaturedItem
					bannerImage={ GiftCardBanner }
					title={ __(
						'Increase customer loyalty with gift cards',
						'wc-calypso-bridge'
					) }
					description={ __(
						'Start selling and accepting digital gift cards to increase customer loyalty, drive more revenue, and introduce new customers to your store.',
						'wc-calypso-bridge'
					) }
					actionButton={
						<Button
							href={ '/wp-admin/admin.php?page=gc_giftcards' }
							variant="secondary"
							onClick={ () => {
								recordEvent(
									'free_trial_create_digital_giftcards',
									{
										source: 'marketing',
									}
								);
							} }
						>
							{ __(
								'Create digital gift cards',
								'wc-calypso-bridge'
							) }
						</Button>
					}
				/>
			</div>
		</div>
	);
};
