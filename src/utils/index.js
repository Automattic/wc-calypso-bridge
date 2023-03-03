/**
 * External dependencies
 */
import { dispatch } from '@wordpress/data';

/**
 * Gets the country code from a country:state value string.
 *
 * @param {string} countryState Country state string, e.g. US:GA.
 * @return {string} Country string.
 */
export function getCountryCode( countryState = '' ) {
	if ( ! countryState ) {
		return null;
	}

	return countryState.split( ':' )[ 0 ];
}

export function createNoticesFromResponse( response ) {
	const { createNotice } = dispatch( 'core/notices' );

	if (
		response.error_data &&
		response.errors &&
		Object.keys( response.errors ).length
	) {
		// Loop over multi-error responses.
		Object.keys( response.errors ).forEach( ( errorKey ) => {
			createNotice( 'error', response.errors[ errorKey ].join( ' ' ) );
		} );
	} else if ( response.message ) {
		// Handle generic messages.
		createNotice( response.code ? 'error' : 'success', response.message );
	}
}

export const getUpgradePlanLink = () => {
	return `https://wordpress.com/plans/${ window.wcCalypsoBridge.siteSlug }`;
};
