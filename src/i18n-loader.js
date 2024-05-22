/**
 * External dependencies
 */
import md5 from 'crypto-js/md5';
import { setLocaleData } from '@wordpress/i18n';

const defaultLocale = 'en_US';

/**
 * Load lazy loaded modules translation data.
 *
 * @param {string} path
 * @param {string} domain
 * @return {Promise}
 */
export async function loadTranslations( path, domain ) {
	const locale = window?.wcCalypsoBridge?.i18n?.locale ?? defaultLocale;
	const baseUrl = window?.wcCalypsoBridge?.i18n?.baseUrl;

	if ( locale === defaultLocale || baseUrl === undefined ) {
		return;
	}

	const [ relativePath, version ] = path.split( '?' );

	const hash = md5( relativePath ).toString();
	const filename =
		`${ domain }-${ locale }-${ hash }.json` +
		( version ? `?${ version }` : '' );

	const response = await fetch( `${ baseUrl }/${ filename }` );

	if ( ! response.ok ) {
		throw new Error(
			`HTTP request failed: ${ response.status } ${ response.statusText }`
		);
	}

	const data = await response.json();
	const localeData =
		data?.locale_data?.[ domain ] || data?.locale_data?.messages;

	setLocaleData( localeData, 'wc-calypso-bridge' );
}
