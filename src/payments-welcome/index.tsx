/**
 * External dependencies
 */
import {
	Card,
	CardBody,
	CardHeader,
	Button,
	Notice,
} from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import strings from './strings';
import Banner from './banner';
import Visa from './cards/visa';
import MasterCard from './cards/mastercard';
import Maestro from './cards/maestro';
import Amex from './cards/amex';
import ApplePay from './cards/applepay';
import CB from './cards/cb';
import DinersClub from './cards/diners';
import Discover from './cards/discover';
import JCB from './cards/jcb';
import UnionPay from './cards/unionpay';
import './style.scss';
import FrequentlyAskedQuestions from './faq';
import wcpayTracks from './tracks';
import ExitSurveyModal from './exit-survey-modal';

declare global {
	interface Window {
		wp: any;
		wcCalypsoBridge: any;
		location: Location;
	}
}

const LearnMore = () => {
	const handleClick = () => {
		wcpayTracks.recordEvent(
			wcpayTracks.events.CONNECT_ACCOUNT_LEARN_MORE
		);
	};
	return (
		<a
			onClick={ handleClick }
			href="https://woocommerce.com/payments/"
			target="_blank"
			rel="noreferrer"
		>
			{ strings.learnMore }
		</a>
	);
};

const PaymentMethods = () => (
	<div className="wcpay-connect-account-page-payment-methods">
		<Visa />
		<MasterCard />
		<Maestro />
		<Amex />
		<DinersClub />
		<CB />
		<Discover />
		<UnionPay />
		<JCB />
		<ApplePay />
	</div>
);

const TermsOfService = () => (
	<span className="wcpay-connect-account-page-terms-of-service">
		{ strings.terms }
	</span>
);

const ConnectPageError = ( { errorMessage }: { errorMessage: string } ) => {
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

const ConnectPageOnboarding = ( {
	isJetpackConnected,
	installAndActivatePlugins,
	setErrorMessage,
	connectUrl,
}: {
	isJetpackConnected: string;
	installAndActivatePlugins: Function;
	setErrorMessage: Function;
	connectUrl: string;
} ) => {
	const [ isSubmitted, setSubmitted ] = useState( false );
	const [ isNoThanksClicked, setNoThanksClicked ] = useState( false );

	const [ isExitSurveyModalOpen, setExitSurveyModalOpen ] = useState( false );

	const renderErrorMessage = ( message: string ) => {
		setErrorMessage( message );
		setSubmitted( false );
	};

	const activatePromo = async () => {
		try {
			const activatePromoResponse = ( await apiFetch( {
				path: '/wc-calypso-bridge/v1/payments/activate-promo',
				method: 'POST',
			} ) ) as any;

			if ( activatePromoResponse?.success ) {
				window.location.href = connectUrl;
			}
		} catch ( e: any ) {
			renderErrorMessage( e.message );
		}
	};

	const handleSetup = async () => {
		setSubmitted( true );
		wcpayTracks.recordEvent( wcpayTracks.events.CONNECT_ACCOUNT_CLICKED, {
			// eslint-disable-next-line camelcase
			wpcom_connection: isJetpackConnected ? 'Yes' : 'No',
		} );

		try {
			const installAndActivateResponse = await installAndActivatePlugins(
				[ 'woocommerce-payments' ]
			);

			if ( installAndActivateResponse?.success ) {
				activatePromo();
			} else {
				renderErrorMessage( installAndActivateResponse.message );
			}
		} catch ( e: any ) {
			renderErrorMessage( e.message );
		}
	};

	const handleNoThanks = () => {
		setNoThanksClicked( true );
		setExitSurveyModalOpen( true );
	};

	return (
		<Card className="connect-account__card">
			<CardHeader>
				<div>
					<h1 className="banner-heading-copy">
						{ strings.bannerHeading }
					</h1>
					<TermsOfService />
				</div>
				<div className="connect-account__action">
					<Button
						isSecondary
						isBusy={ isNoThanksClicked && isExitSurveyModalOpen }
						disabled={ isNoThanksClicked && isExitSurveyModalOpen }
						onClick={ handleNoThanks }
						className="btn-nothanks"
					>
						{ strings.nothanks }
					</Button>
					<Button
						isPrimary
						isBusy={ isSubmitted }
						disabled={ isSubmitted }
						onClick={ handleSetup }
						className="btn-install"
					>
						{ strings.button }
					</Button>
					{ isExitSurveyModalOpen && (
						<ExitSurveyModal
							setExitSurveyModalOpen={ setExitSurveyModalOpen }
						/>
					) }
				</div>
			</CardHeader>
			<CardBody>
				<div className="content">
					<p className="onboarding-description">
						{ strings.onboarding.description }
						<br />
						<LearnMore />
					</p>

					<h3 className="accepted-payment-methods">
						{ strings.paymentMethodsHeading }
					</h3>

					<PaymentMethods />
				</div>
			</CardBody>
		</Card>
	);
};

/**
 * Submits a request to store viewing welcome time.
 */
const storeViewWelcome = async () => {
	const { hasViewedPayments } = window.wcCalypsoBridge;
	if ( hasViewedPayments ) {
		return;
	}

	try {
		await apiFetch( {
			path: '/wc-calypso-bridge/v1/payments/view-welcome',
			method: 'POST',
		} );
	} catch ( e: any ) {}
};

const ConnectAccountPage = () => {
	useEffect( () => {
		wcpayTracks.recordEvent( wcpayTracks.events.CONNECT_ACCOUNT_VIEW, {
			path: 'payments_connect_dotcom_test',
		} );

		storeViewWelcome();
	}, [] );
	const [ errorMessage, setErrorMessage ] = useState( '' );
	const onboardingProps = {
		isJetpackConnected: window.wp.data
			.select( 'wc/admin/plugins' )
			.isJetpackConnected(),
		installAndActivatePlugins:
			window.wp.data.dispatch( 'wc/admin/plugins' )
				.installAndActivatePlugins,
		setErrorMessage,
		connectUrl: window.wcCalypsoBridge.wcpayConnectUrl,
	};

	return (
		<div className="connect-account-page">
			<div className="woocommerce-payments-page is-narrow connect-account">
				<ConnectPageError errorMessage={ errorMessage } />
				<ConnectPageOnboarding { ...onboardingProps } />
				<Banner />
				<FrequentlyAskedQuestions />
			</div>
		</div>
	);
};
export default ConnectAccountPage;
