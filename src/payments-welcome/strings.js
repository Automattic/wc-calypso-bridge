/* eslint-disable max-len */
/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { __experimentalCreateInterpolateElement as createInterpolateElement } from 'wordpress-element';

export default {
	button: __('Finish setup', 'woocommerce-payments'),

	heading: __('WooCommerce Payments', 'woocommerce-payments'),

	learnMore: __('Learn more', 'woocommerce-payments'),

	onboarding: {
		description: __(
			'With WooCommerce Payments, you can securely accept major cards, Apple Pay, and payments in over 100 currencies. Track cash flow and manage recurring revenue directly from your store’s dashboard - with no setup costs or monthly fees.',
			'woocommerce-payments'
		),
	},

	paymentMethodsHeading: __(
		'Accepted payment methods',
		'woocommerce-payments'
	),

	terms: createInterpolateElement(
		__(
			'By clicking “Finish setup”, you agree to the <a>Terms of Service</a>',
			'woocommerce-payments'
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

	onboardingDisabled: [
		__(
			"We've temporarily paused new account creation.",
			'woocommerce-payments'
		),
		__("We'll notify you when we resume!", 'woocommerce-payments'),
	],
};
