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
		message = sprintf(
			/* translators:  placeholders do not need to be translated */
			__(
				'Upgrade your plan for %(formattedPrice)s for your first %(intervalUnit)s',
				'wc-calypso-bridge'
			),
			{
				formattedPrice,
				intervalUnit,
			}
		);
	} else {
		message = sprintf(
			/* translators:  %(intervalUnit)s: one of day, month, and year, formattedPrice and intervalCount do not need to be translated. */
			__(
				'Upgrade your plan for %(formattedPrice)s for your first %(intervalCount)s %(intervalUnit)ss',
				'wc-calypso-bridge'
			),
			{
				formattedPrice,
				intervalCount,
				intervalUnit,
			}
		);
	}

	return message;
};
