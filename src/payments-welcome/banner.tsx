/**
 * External dependencies
 */
import { Card, CardBody } from '@wordpress/components';

/**
 * Internal dependencies
 */
import strings from './strings';

const Banner = () => {
	return (
		<Card size="large" className="account-page woocommerce-payments-banner">
			<CardBody>
				<div className="limited-time-offer">
					{strings.limitedTimeOffer}
				</div>
				<h1>{strings.bannerHeading}</h1>
				<p>{strings.bannerCopy}</p>
			</CardBody>
		</Card>
	);
};

export default Banner;
