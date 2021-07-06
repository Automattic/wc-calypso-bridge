/**
 * External dependencies
 */
import { Card } from '@woocommerce/components';
import { Button, Notice } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import strings from './strings';
import Banner from './banner';
import Visa from './cards/visa.js';
import MasterCard from './cards/mastercard.js';
import Amex from './cards/amex.js';
import ApplePay from './cards/applepay.js';
import CB from './cards/cb.js';
import DinersClub from './cards/diners.js';
import Discover from './cards/discover.js';
import GPay from './cards/gpay.js';
import JCB from './cards/jcb.js';
import UnionPay from './cards/unionpay.js';
import './style.scss';
import FrequentlyAskedQuestions from './faq';
import wcpayTracks from './tracks';

const wcpaySettings = {
	connectUrl: wcCalypsoBridge.wcpayConnectUrl,
};

const LearnMore = () => {
	const handleClick = () => {
		wcpayTracks.recordEvent(wcpayTracks.events.CONNECT_ACCOUNT_LEARN_MORE);
	};
	return (
		<a
			onClick={handleClick}
			href="https://woocommerce.com/payments/"
			target="_blank"
			rel="noreferrer"
		>
			{strings.learnMore}
		</a>
	);
};

const PaymentMethods = () => (
	<div className="wcpay-connect-account-page-payment-methods">
		<Visa />
		<MasterCard />
		<Amex />
		<DinersClub />
		<CB />
		<Discover />
		<UnionPay />
		<JCB />
		<GPay />
		<ApplePay />
	</div>
);

const TermsOfService = () => (
	<span className="wcpay-connect-account-page-terms-of-service">
		{strings.terms}
	</span>
);

const ConnectPageError = ( { errorMessage } ) => {
	if ( ! errorMessage ) {
		return null;
	}
	return (
		<Notice
			className="wcpay-connect-error-notice"
			status="error"
			isDismissible={ false }
		>
			{ errorMessage }
		</Notice>
	);
};

const ConnectPageOnboarding = ( { setErrorMessage } ) => {
	const [isSubmitted, setSubmitted] = useState(false);
	const [isNoThanksClicked, setNoThanksClicked] = useState(false);
	const { connectUrl } = wcpaySettings;

	const handleSetup = async () => {
		setSubmitted(true);
		wcpayTracks.recordEvent(wcpayTracks.events.CONNECT_ACCOUNT_CLICKED, {
			// Since we're in WPCOM where users can't disconnect Jetpack, this can be safely hardcoded.
			// eslint-disable-next-line camelcase
			wpcom_connection: 'Yes',
		});

		const installAndActivateResponse = await wp.data.dispatch('wc/admin/plugins').installAndActivatePlugins( [ 'woocommerce-payments' ] );
		if ( installAndActivateResponse?.success ) {
			// Redirect to KYC
			window.location = connectUrl;
		} else {
			// Display error
			setErrorMessage( installAndActivateResponse.message );
			setSubmitted( false );
		}
	};

	const handleNoThanks = () => {
		setNoThanksClicked(true);
	};

	return (
		<>
			<p>
				{strings.onboarding.description} <LearnMore />
			</p>

			<h3>{strings.paymentMethodsHeading}</h3>

			<PaymentMethods />

			<hr className="full-width" />

			<p className="connect-account__action">
				<TermsOfService />
				<Button
					isPrimary
					isBusy={isSubmitted}
					disabled={isSubmitted}
					onClick={handleSetup}
				>
					{strings.button}
				</Button>
				<Button
					isBusy={isNoThanksClicked}
					disabled={isNoThanksClicked}
					onClick={handleNoThanks}
					className="btn-nothanks"
				>
					{strings.nothanks}
				</Button>
			</p>
		</>
	);
};

const ConnectAccountPage = () => {
	useEffect(() => {
		wcpayTracks.recordEvent(wcpayTracks.events.CONNECT_ACCOUNT_VIEW, {
			path: 'payments_connect_v2',
		});
	}, []);

	const [ errorMessage, setErrorMessage ] = useState('');

	return (
		<div className="connect-account-page">
			<div className="woocommerce-payments-page is-narrow connect-account">
			<ConnectPageError errorMessage={ errorMessage }/>
				<Card className="connect-account__card">
					<Banner style="account-page" />
					<div className="content">
						<ConnectPageOnboarding setErrorMessage={ setErrorMessage }/>
					</div>
				</Card>
				<Card className="faq__card">
					<div className="content">
						<FrequentlyAskedQuestions />
					</div>
				</Card>
			</div>
		</div>
	);
};

export default ConnectAccountPage;
