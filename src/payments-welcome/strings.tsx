/* eslint-disable max-len */
/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
// @ts-ignore
import { __experimentalCreateInterpolateElement as createInterpolateElement } from 'wordpress-element';

export default {
	button: __('Get started', 'wc-calypso-bridge'),
	nothanks: __('No thanks', 'wc-calypso-bridge'),
	limitedTimeOffer: __('Limited time offer', 'wc-calypso-bridge'),
	heading: __('WooCommerce Payments', 'wc-calypso-bridge'),
	bannerHeading: __(
		'Save big with WooCommerce Payments',
		'wc-calypso-bridge'
	),
	bannerCopy: __(
		'No transaction fees for up to 3 months (or $25,000 in payments)',
		'wc-calypso-bridge'
	),
	learnMore: __('Learn more', 'wc-calypso-bridge'),

	onboarding: {
		description: __(
			"With WooCommerce Payments, you can securely accept major cards, Apple Pay, and payments in over 100 currencies. Manage transactions directly from your store's dashboard -- with no setup costs or monthly fees.",
			'wc-calypso-bridge'
		),
	},

	paymentMethodsHeading: __('Accepted payment methods', 'wc-calypso-bridge'),

	surveyTitle: __( 'Remove WooCommerce Payments', 'wc-calypso-bridge' ) ,

	surveyIntro: createInterpolateElement(
		// Note: \xa0 is used to create a non-breaking space.
		__(
			'Please take a moment to tell us why you’d like to remove WooCommerce Payments. This will remove WooCommerce\xa0Payments from the navigation. In order to enable it again, go to <strong>WooCommerce\xa0Settings\xa0>\xa0Payments</strong>.',
			'wc-calypso-bridge'
		),
		{
			strong: (
				<strong />
			),
		}
	),

	surveyQuestion: __('What made you disable the new payments experience?', 'wc-calypso-bridge'),

	surveyHappyLabel: __('I’m already happy with my payments setup', 'wc-calypso-bridge'),

	surveyInstallLabel: __('I don’t want to install another plugin', 'wc-calypso-bridge'),

	surveyMoreInfoLabel: __('I need more information about WooCommerce Payments', 'wc-calypso-bridge'),

	surveyAnotherTimeLabel: __('I’m open to installing it another time', 'wc-calypso-bridge'),

	surveySomethingElseLabel: __('It’s something else (Please share below', 'wc-calypso-bridge'),

	surveyCommentsLabel: __('Comments (Optional)', 'wc-calypso-bridge'),
	
	surveyCancelButton: __('Just remove WooCommerce Payments', 'wc-calypso-bridge'),
	
	surveySubmitButton: __('Remove and send feedback', 'wc-calypso-bridge'),

	terms: createInterpolateElement(
		__(
			'Upon clicking "Get started", you agree to the <a>Terms of Service</a>. Next we\'ll ask you to share a few details about your business to create your account.',
			'wc-calypso-bridge'
		),
		{
			a: (
				// eslint-disable-next-line jsx-a11y/anchor-has-content
				<a
					href="https://wordpress.com/tos"
					target="_blank"
					rel="noopener noreferrer"
				/>
			),
		}
	),
};
