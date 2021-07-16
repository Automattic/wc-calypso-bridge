/**
 * External dependencies.
 */
import { Icon, help } from '@wordpress/icons'

/**
 * Internal dependencies.
 */
import strings from './strings';

const FrequentlyAskedQuestions = () => {
	return (
		<>
			<h2>Frequently asked questions</h2>
			<h3>How will I save money using WooCommerce Payments?</h3>
			<p>
			Stores accepted into the promotional program will receive a 100% discount on transaction fees (excluding currency conversion fees) for the first $25,000 in payments, or 3 months, whichever comes first. Simply install the extension and if eligible you’ll be entered into the promotional offer. 
			</p>
			<p>
			To be eligible for this promotional offer, your store must: (1) meet the WooCommerce Payments usage requirements; (2) be a U.S.-based business; (3) not have processed payments through WooCommerce Payments before; and (4) be accepted into the promotional program.
			</p>
			<h3>What are the fees for WooCommerce Payments?</h3>
			<p>
				WooCommerce Payments uses a pay-as-you-go pricing model. You pay only for activity on the account. No setup fee or monthly fee. Fees differ based on the country of your account and country of your customer’s card. The full list of fees for available countries is listed below.
			</p>
			<p>
				<h4>United States</h4>
				<ul>
					<li>2.9% + $0.30 USD per transaction for U.S. issued credit or debit card
						<ul>
							<li>+1% for transactions paid using a card issued outside the US</li>
							<li><a href='https://docs.woocommerce.com/document/payments/faq/fees/currency-conversion/' target='_blank'>+1% for conversion of currencies other than USD</a></li>
						</ul>
					</li>
					<li>$15 USD fee per dispute (refunded if you win the dispute)</li>
					<li>1.5% fee on the payout amount for <a href='https://docs.woocommerce.com/document/payments/instant-deposits/' target='_blank'>instant deposits</a></li>
				</ul>
				<a href='https://href.li/?https://docs.woocommerce.com/document/payments/faq/fees/' target='_blank'>View all fees</a>
			</p>
			<h3>When will I receive deposits for my WooCommerce Payments account balance?</h3>
			<p>
			For most accounts, WooCommerce Payments automatically pays out your available account balance into your nominated account daily after a standard pending period.
			</p>
			<p>
				Payments received each day become part of the pending balance. That pending balance will become available after a pending period. On the day it becomes available, it will be automatically paid out to your bank account. The pending period is based on the country of the account.
			</p>
			<p>
				For example, a business based in New Zealand has a pending period of 4 business days. Payments made to this account on Wednesday will be paid out on the next Tuesday.
			</p>
			<p>
			Most banks will reflect the deposit in your account as soon as they receive the transfer from WooCommerce Payments. Some may take a few extra days to make the balance available to you.
			</p>
			<p>
				<a href='https://href.li/?https://docs.woocommerce.com/document/payments/faq/deposit-schedule/' target='_blank'>All deposits details</a>
			</p>
			<h3>What products are not permitted on my store when accepting payments with WooCommerce Payments?</h3>
			<p>
				Due to restrictions from card networks, our payment service providers, and their financial service providers, some businesses and product types that are not allowed to transact using WooCommerce Payments, including but not limited to:
				<ul>
					<li>Virtual currency, including video game or virtual world credits</li>
					<li>Counterfeit goods</li>
					<li>Adult content and services</li>
					<li>Drug paraphernalia (including e-cigarette, vapes and nutraceuticals)</li>
					<li>Multi-level marketing</li>
					<li>Pseudo pharmaceuticals</li>
					<li>Social media activity, like Twitter followers, Facebook likes, YouTube views</li>
					<li>Substances designed to mimic illegal drugs</li>
					<li>Firearms, ammunition</li>
				</ul>
			</p>
			<p>
				The full list of these businesses can be found in <a href='https://stripe.com/restricted-businesses' target='_blank'>Stripe’s Restricted Businesses list</a>.
			</p>
			<p>
				By signing up to use WooCommerce Payments, you agree not to accept payments in connection with these restricted activities, practices or products. We also work to ensure that no prohibited activity is conducted on WooCommerce Payments.
			</p>
			<p>
				If we become aware of prohibited activity, we may restrict or shutdown the account responsible.
			</p>
			<h3>Can I use WooCommerce Payments alongside other payment gateways?</h3>
			<p>
				Yes! WooCommerce Payments works alongside other payment service providers, including Stripe, PayPal, and all others. We’ve built it with this flexibility in mind so that you can ensure your store is working to meet your business needs. 				
			</p>
			<div className='help-section'>
				<Icon icon={ help } />
				<span>{strings.haveMoreQuestions}</span>
				<a href="https://www.woocommerce.com/my-account/tickets/" target="_blank">{strings.getInTouch}</a>
			</div>
		</>
	);
};

export default FrequentlyAskedQuestions;
