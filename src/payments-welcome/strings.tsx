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
		'50% transaction fee discount for up to $125,000 in payments or six months',
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
	surveyTitle: __('Remove WooCommerce Payments', 'wc-calypso-bridge'),

	surveyIntro: createInterpolateElement(
		// Note: \xa0 is used to create a non-breaking space.
		__(
			'Please take a moment to tell us why you’d like to remove WooCommerce Payments. This will remove WooCommerce\xa0Payments from the navigation. You can enable it again in <strong>WooCommerce\xa0Settings\xa0>\xa0Payments</strong>, however the promotion will not apply.',
			'wc-calypso-bridge'
		),
		{
			strong: <strong />,
		}
	),

	surveyQuestion: __(
		'What made you disable the new payments experience?',
		'wc-calypso-bridge'
	),

	surveyHappyLabel: __(
		'I’m already happy with my payments setup',
		'wc-calypso-bridge'
	),

	surveyInstallLabel: __(
		'I don’t want to install another plugin',
		'wc-calypso-bridge'
	),

	surveyMoreInfoLabel: __(
		'I need more information about WooCommerce Payments',
		'wc-calypso-bridge'
	),

	surveyAnotherTimeLabel: __(
		'I’m open to installing it another time',
		'wc-calypso-bridge'
	),

	surveySomethingElseLabel: __(
		'It’s something else (Please share below',
		'wc-calypso-bridge'
	),

	surveyCommentsLabel: __('Comments (Optional)', 'wc-calypso-bridge'),

	surveyCancelButton: __(
		'Just remove WooCommerce Payments',
		'wc-calypso-bridge'
	),

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

	faq: {
		faqHeader: __('Frequently asked questions', 'wc-calypso-bridge'),

		question1: __(
			'How will I save money using WooCommerce Payments?',
			'wc-calypso-bridge'
		),

		question1Answer1: __(
			'Stores accepted into the promotional program will receive a 50% discount on transaction fees for the first $125,000 in payments, or 6 months, whichever comes first. Simply install the extension and if eligible you’ll be entered into the promotional offer.',
			'wc-calypso-bridge'
		),

		question1Answer2: __(
			'To be eligible for this promotional offer, your store must: (1) meet the WooCommerce Payments usage requirements; (2) be a U.S.-based business; (3) not have processed payments through WooCommerce Payments before; and (4) be accepted into the promotional program.',
			'wc-calypso-bridge'
		),

		question2: __(
			'What are the fees for WooCommerce Payments?',
			'wc-calypso-bridge'
		),

		question2Answer1: __(
			'WooCommerce Payments uses a pay-as-you-go pricing model. You pay only for activity on the account. No setup fee or monthly fee. Fees differ based on the country of your account and country of your customer’s card. The full list of fees for available countries is listed below.',
			'wc-calypso-bridge'
		),

		question2Answer2: __('United States', 'wc-calypso-bridge'),

		question2Answer3: __(
			'2.9% + $0.30 USD per transaction for U.S. issued credit or debit card',
			'wc-calypso-bridge'
		),

		question2Answer4: __(
			'+1% for transactions paid using a card issued outside the US',
			'wc-calypso-bridge'
		),

		question2Answer5: createInterpolateElement(
			__(
				'<a>+1% for conversion of currencies other than USD</a>',
				'wc-calypso-bridge'
			),
			{
				a: (
					// eslint-disable-next-line jsx-a11y/anchor-has-content
					<a
						href="https://docs.woocommerce.com/document/payments/faq/fees/currency-conversion/"
						target="_blank"
						rel="noopener noreferrer"
					/>
				),
			}
		),

		question2Answer6: __(
			'$15 USD fee per dispute (refunded if you win the dispute)',
			'wc-calypso-bridge'
		),

		question2Answer7: createInterpolateElement(
			__(
				'1.5% fee on the payout amount for <a>instant deposits</a>',
				'wc-calypso-bridge'
			),
			{
				a: (
					// eslint-disable-next-line jsx-a11y/anchor-has-content
					<a
						href="https://docs.woocommerce.com/document/payments/instant-deposits/"
						target="_blank"
						rel="noopener noreferrer"
					/>
				),
			}
		),

		question2Answer8: createInterpolateElement(
			__('<a>View all fees</a>', 'wc-calypso-bridge'),
			{
				a: (
					// eslint-disable-next-line jsx-a11y/anchor-has-content
					<a
						href="https://docs.woocommerce.com/document/payments/faq/fees/"
						target="_blank"
						rel="noopener noreferrer"
					/>
				),
			}
		),

		question3: __(
			'When will I receive deposits for my WooCommerce Payments account balance?',
			'wc-calypso-bridge'
		),

		question3Answer1: __(
			'For most accounts, WooCommerce Payments automatically pays out your available account balance into your nominated account daily after a standard pending period.',
			'wc-calypso-bridge'
		),

		question3Answer2: __(
			'Payments received each day become part of the pending balance. That pending balance will become available after a pending period. On the day it becomes available, it will be automatically paid out to your bank account. The pending period is based on the country of the account.',
			'wc-calypso-bridge'
		),

		question3Answer3: __(
			'For example, a business based in New Zealand has a pending period of 4 business days. Payments made to this account on Wednesday will be paid out on the next Tuesday.',
			'wc-calypso-bridge'
		),

		question3Answer4: __(
			'Most banks will reflect the deposit in your account as soon as they receive the transfer from WooCommerce Payments. Some may take a few extra days to make the balance available to you.',
			'wc-calypso-bridge'
		),

		question3Answer5: createInterpolateElement(
			__('<a>All deposits details</a>', 'wc-calypso-bridge'),
			{
				a: (
					// eslint-disable-next-line jsx-a11y/anchor-has-content
					<a
						href="https://docs.woocommerce.com/document/payments/faq/deposit-schedule/"
						target="_blank"
						rel="noopener noreferrer"
					/>
				),
			}
		),

		question4: __(
			'What products are not permitted on my store when accepting payments with WooCommerce Payments?',
			'wc-calypso-bridge'
		),

		question4Answer1: __(
			'Due to restrictions from card networks, our payment service providers, and their financial service providers, some businesses and product types that are not allowed to transact using WooCommerce Payments, including but not limited to:',
			'wc-calypso-bridge'
		),

		question4Answer2: __(
			'Virtual currency, including video game or virtual world credits',
			'wc-calypso-bridge'
		),

		question4Answer3: __('Counterfeit goods', 'wc-calypso-bridge'),

		question4Answer4: __('Adult content and services', 'wc-calypso-bridge'),

		question4Answer5: __(
			'Drug paraphernalia (including e-cigarette, vapes and nutraceuticals)',
			'wc-calypso-bridge'
		),

		question4Answer6: __('Multi-level marketing', 'wc-calypso-bridge'),

		question4Answer7: __('Pseudo pharmaceuticals', 'wc-calypso-bridge'),

		question4Answer8: __(
			'Social media activity, like Twitter followers, Facebook likes, YouTube views',
			'wc-calypso-bridge'
		),

		question4Answer9: __(
			'Substances designed to mimic illegal drugs',
			'wc-calypso-bridge'
		),

		question4Answer10: __('Firearms, ammunition', 'wc-calypso-bridge'),

		question4Answer11: createInterpolateElement(
			__(
				'The full list of these businesses can be found in <a>Stripe’s Restricted Businesses list</a>.',
				'wc-calypso-bridge'
			),
			{
				a: (
					// eslint-disable-next-line jsx-a11y/anchor-has-content
					<a
						href="https://stripe.com/restricted-businesses"
						target="_blank"
						rel="noopener noreferrer"
					/>
				),
			}
		),

		question4Answer12: __(
			'By signing up to use WooCommerce Payments, you agree not to accept payments in connection with these restricted activities, practices or products. We also work to ensure that no prohibited activity is conducted on WooCommerce Payments.',
			'wc-calypso-bridge'
		),

		question4Answer13: __(
			'If we become aware of prohibited activity, we may restrict or shutdown the account responsible.',
			'wc-calypso-bridge'
		),

		question5: __(
			'Can I use WooCommerce Payments alongside other payment gateways?',
			'wc-calypso-bridge'
		),

		question5Answer1: __(
			'Yes! WooCommerce Payments works alongside other payment service providers, including Stripe, PayPal, and all others. We’ve built it with this flexibility in mind so that you can ensure your store is working to meet your business needs.',
			'wc-calypso-bridge'
		),

		haveMoreQuestions: __('Have more questions?', 'wc-calypso-bridge'),

		getInTouch: __('Get in touch', 'wc-calypso-bridge'),
	},
};
