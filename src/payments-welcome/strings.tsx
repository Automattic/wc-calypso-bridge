/* eslint-disable max-len */
/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
// @ts-ignore
import { __experimentalCreateInterpolateElement as createInterpolateElement } from 'wordpress-element';

export default {
	button: __('Install', 'wc-calypso-bridge'),
	nothanks: __('No thanks', 'wc-calypso-bridge'),
	limitedTimeOffer: __('Limited time offer', 'wc-calypso-bridge'),
	heading: __('WooCommerce Payments', 'wc-calypso-bridge'),
	bannerHeading: __(
		'Save big with WooCommerce Payments',
		'wc-calypso-bridge'
	),
	bannerCopy: __(
		'No card transaction fees for up to 3 months (or $25,000 in payments)',
		'wc-calypso-bridge'
	),
	discountCopy: __(
		'Discount will be applied upon install and completed setup of WooCommerce Payments',
		'wc-calypso-bridge'
	),
	learnMore: __('Learn more', 'wc-calypso-bridge'),

	onboarding: {
		description: __(
			"Save up to $800 in fees by managing transactions with WooCommerce Payments. With WooCommerce Payments, you can securely accept major cards, Apple Pay, and payments in over 100 currencies. Track cash flow and manage recurring revenue directly from your store's dashboard - with no setup costs or monthly fees.",
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
		'It’s something else (Please share below)',
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
			'By clicking "Install", you agree to the <a>Terms of Service</a>',
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

		question1: __('What is WooCommerce Payments?', 'wc-calypso-bridge'),

		question1Answer1: __(
			'WooCommerce Payments is an integrated payment solution, built by WooCommerce, for WooCommerce. Use WooCommerce Payments to manage your payments, track cash flow, and manage revenue from your dashboard.',
			'wc-calypso-bridge'
		),

		question1Answer2: __(
			'You can securely accept credit and debit card payments, Apple Pay, bank transfers, recurring revenue, accelerated checkout, and more - in over 100+ currencies with WooCommerce Payments.',
			'wc-calypso-bridge'
		),

		question2: __(
			'Can I use WooCommerce Payments alongside other payment gateways?',
			'wc-calypso-bridge'
		),

		question2Answer1: __(
			'Yes. WooCommerce Payments works alongside other payment service providers, including Stripe, PayPal, and all others. We’ve built it with this flexibility in mind so that you can ensure your store is working to meet your business needs.',
			'wc-calypso-bridge'
		),

		question3: __(
			'Why should I choose WCPay over other payment gateways?',
			'wc-calypso-bridge'
		),

		question3Answer1: __(
			"Native dashboard: track cash flow from the same  WordPress dashboard you're already using to manage product catalogue, inventory, orders, fulfilment, and otherwise run your online storefront.",
			'wc-calypso-bridge'
		),

		question3Answer2: __(
			'In-person payments: the only payment method that helps you sell online and offline with the official WooCommerce mobile app instead of a paid POS solution.',
			'wc-calypso-bridge'
		),

		question3Answer3: __(
			'Subscription functionality: offer customers a recurring option for your inventory without purchasing an additional extension.',
			'wc-calypso-bridge'
		),

		question3Answer4: __(
			'Native multi-currency: increase sales by making it easier for customers outside your country to purchase from your store, without an extension.',
			'wc-calypso-bridge'
		),

		question4: __(
			'How will I save money using WooCommerce Payments?',
			'wc-calypso-bridge'
		),

		question4Answer1: __(
			'Stores accepted into the promotional program will receive a 100% discount on transaction fees (excluding currency conversion fees) for the first $25,000 in payments, or 3 months, whichever comes first. Simply install the extension and if eligible you’ll be entered into the promotional offer.',
			'wc-calypso-bridge'
		),

		question4Answer2: __(
			'To be eligible for this promotional offer, your store must: (1) meet the WooCommerce Payments usage requirements; (2) be a U.S.-based business; (3) not have processed payments through WooCommerce Payments before; and (4) be accepted into the promotional program.',
			'wc-calypso-bridge'
		),

		question5: __(
			'What are the fees for WooCommerce Payments?',
			'wc-calypso-bridge'
		),

		question5Answer1: __(
			'WooCommerce Payments uses a pay-as-you-go pricing model. You pay only for activity on the account. No setup fee or monthly fee. Fees differ based on the country of your account and country of your customer’s card.',
			'wc-calypso-bridge'
		),

		question5Answer2: createInterpolateElement(
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

		question5Answer7: createInterpolateElement(
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

		question5Answer8: createInterpolateElement(
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

		question6: __(
			'When will I receive deposits for my WooCommerce Payments account balance?',
			'wc-calypso-bridge'
		),

		question6Answer1: createInterpolateElement(
			__(
				'For most accounts, <a>WooCommerce Payments</a> automatically pays out your available account balance into your nominated account daily after a standard pending period.',
				'wc-calypso-bridge'
			),
			{
				a: (
					// eslint-disable-next-line jsx-a11y/anchor-has-content
					<a
						href="https://woocommerce.com/payments/"
						target="_blank"
						rel="noopener noreferrer"
					/>
				),
			}
		),

		question6Answer2: __(
			'Payments received each day become part of the pending balance. That pending balance will become available after a pending period. On the day it becomes available, it will be automatically paid out to your bank account. The pending period is based on the country of the account.',
			'wc-calypso-bridge'
		),

		question6Answer3: __(
			'For example, a business based in New Zealand has a pending period of 4 business days. Payments made to this account on Wednesday will be paid out on the next Tuesday.',
			'wc-calypso-bridge'
		),

		question6Answer4: __(
			'Most banks will reflect the deposit in your account as soon as they receive the transfer from WooCommerce Payments. Some may take a few extra days to make the balance available to you.',
			'wc-calypso-bridge'
		),

		question6Answer5: createInterpolateElement(
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

		question7: __(
			'What products are not permitted on my store when accepting payments with WooCommerce Payments?',
			'wc-calypso-bridge'
		),

		question7Answer1: __(
			'Due to restrictions from card networks, our payment service providers, and their financial service providers, some businesses and product types that are not allowed to transact using WooCommerce Payments, including but not limited to:',
			'wc-calypso-bridge'
		),

		question7Answer2: __(
			'Virtual currency, including video game or virtual world credits',
			'wc-calypso-bridge'
		),

		question7Answer3: __('Counterfeit goods', 'wc-calypso-bridge'),

		question7Answer4: __('Adult content and services', 'wc-calypso-bridge'),

		question7Answer5: __(
			'Drug paraphernalia (including e-cigarette, vapes and nutraceuticals)',
			'wc-calypso-bridge'
		),

		question7Answer6: __('Multi-level marketing', 'wc-calypso-bridge'),

		question7Answer7: __('Pseudo pharmaceuticals', 'wc-calypso-bridge'),

		question7Answer8: __(
			'Social media activity, like Twitter followers, Facebook likes, YouTube views',
			'wc-calypso-bridge'
		),

		question7Answer9: __(
			'Substances designed to mimic illegal drugs',
			'wc-calypso-bridge'
		),

		question7Answer10: __('Firearms, ammunition', 'wc-calypso-bridge'),

		question7Answer11: createInterpolateElement(
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

		question7Answer12: __(
			'By signing up to use WooCommerce Payments, you agree not to accept payments in connection with these restricted activities, practices or products. We also work to ensure that no prohibited activity is conducted on WooCommerce Payments.',
			'wc-calypso-bridge'
		),

		question7Answer13: __(
			'If we become aware of prohibited activity, we may restrict or shutdown the account responsible.',
			'wc-calypso-bridge'
		),

		question8: __(
			'Can WooCommerce Payments support Subscriptions and recurring payment options?',
			'wc-calypso-bridge'
		),

		question8Answer1: createInterpolateElement(
			__(
				'WooCommerce Payments supports charging automatic recurring payments via the <a>WooCommerce Subscriptions</a> plugin.',
				'wc-calypso-bridge'
			),
			{
				a: (
					// eslint-disable-next-line jsx-a11y/anchor-has-content
					<a
						href="https://woocommerce.com/products/woocommerce-subscriptions/"
						target="_blank"
						rel="noopener noreferrer"
					/>
				),
			}
		),

		question8Answer2: __(
			'WooCommerce Payments offers full compatibility with WooCommerce Subscriptions’ features, including:',
			'wc-calypso-bridge'
		),

		question8Answer3: __('Subscription suspension', 'wc-calypso-bridge'),

		question8Answer4: __('Subscription cancellation', 'wc-calypso-bridge'),

		question8Answer5: __('Subscription reactivation', 'wc-calypso-bridge'),

		question8Answer6: __('Multiple subscriptions', 'wc-calypso-bridge'),

		question8Answer7: __('Recurring total changes', 'wc-calypso-bridge'),

		question8Answer8: __('Payment date changes', 'wc-calypso-bridge'),

		question8Answer9: __(
			'Customer & Store Manager payment method changes',
			'wc-calypso-bridge'
		),

		question8Answer10: createInterpolateElement(
			__(
				'For more details on the subscription features that WooCommerce Payments offers, refer to the <a>subscription section of the WooCommerce Payments start up guide</a>.',
				'wc-calypso-bridge'
			),
			{
				a: (
					// eslint-disable-next-line jsx-a11y/anchor-has-content
					<a
						href="https://docs.woocommerce.com/document/payments/#subscriptions"
						target="_blank"
						rel="noopener noreferrer"
					/>
				),
			}
		),

		haveMoreQuestions: __('Have more questions?', 'wc-calypso-bridge'),

		getInTouch: __('Get in touch', 'wc-calypso-bridge'),
	},
};
