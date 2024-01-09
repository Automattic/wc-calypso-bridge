/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';

export const getOfferMessage = ( {
	formattedPrice,
	intervalUnit,
	intervalCount,
}: {
	formattedPrice: string;
	intervalUnit: string;
	intervalCount: string;
} ): string => {
	let message = '';
	if ( intervalCount === '1' ) {
		switch ( intervalUnit ) {
			case 'day':
				message = sprintf(
					/* translators:  placeholders do not need to be translated */
					__(
						'Upgrade your plan for %(formattedPrice)s for your first day',
						'wc-calypso-bridge'
					),
					{
						formattedPrice,
					}
				);
				break;
			case 'month':
				message = sprintf(
					/* translators:  placeholders do not need to be translated */
					__(
						'Upgrade your plan for %(formattedPrice)s for your first month',
						'wc-calypso-bridge'
					),
					{
						formattedPrice,
					}
				);
				break;
			case 'year':
				message = sprintf(
					/* translators:  placeholders do not need to be translated */
					__(
						'Upgrade your plan for %(formattedPrice)s for your first year',
						'wc-calypso-bridge'
					),
					{
						formattedPrice,
					}
				);
				break;
			default:
				message = sprintf(
					/* translators:  placeholders do not need to be translated */
					__(
						'Upgrade your plan for %(formattedPrice)s',
						'wc-calypso-bridge'
					),
					{
						formattedPrice,
					}
				);
		}
	} else {
		switch ( intervalUnit ) {
			case 'day':
				message = sprintf(
					/* translators:  placeholders do not need to be translated */
					__(
						'Upgrade your plan for %(formattedPrice)s for your first %(intervalCount)s days',
						'wc-calypso-bridge'
					),
					{
						formattedPrice,
						intervalCount,
					}
				);
				break;
			case 'month':
				message = sprintf(
					/* translators:  placeholders do not need to be translated */
					__(
						'Upgrade your plan for %(formattedPrice)s for your first %(intervalCount)s months',
						'wc-calypso-bridge'
					),
					{
						formattedPrice,
						intervalCount,
					}
				);
				break;
			case 'year':
				message = sprintf(
					/* translators:  placeholders do not need to be translated */
					__(
						'Upgrade your plan for %(formattedPrice)s for your first %(intervalCount)s years',
						'wc-calypso-bridge'
					),
					{
						formattedPrice,
						intervalCount,
					}
				);
				break;
			default:
				message = sprintf(
					/* translators:  placeholders do not need to be translated */
					__(
						'Upgrade your plan for %(formattedPrice)s',
						'wc-calypso-bridge'
					),
					{
						formattedPrice,
					}
				);
		}
	}

	return message;
};
