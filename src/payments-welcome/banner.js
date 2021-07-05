/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Card, CardBody } from '@wordpress/components';

/**
 * Internal dependencies
 */
import WCPayLogo from './wcpay-logo';

const Banner = ({ style }) => {
	let logoWidth,
		logoHeight,
		showPill,
		className = 'woocommerce-payments-banner';
	if (style === 'account-page') {
		logoWidth = 196;
		logoHeight = 65;
		showPill = true;
		className += ' account-page';
	} else {
		logoWidth = 257;
		logoHeight = 70;
		showPill = false;
	}
	return (
		<Card size="large" className={className}>
			<CardBody>
				<WCPayLogo width={logoWidth} height={logoHeight} />
				{showPill && (
					<div className="woocommerce-payments-banner-pill">
						<div>{__('Recommended', 'woocommerce-payments')}</div>
					</div>
				)}
			</CardBody>
		</Card>
	);
};

export default Banner;
