/**
 * External dependencies
 */
import md5 from 'crypto-js/md5';
import {
	setLocaleData,
	hasTranslation,
	__,
	_n,
	_nx,
	_x,
} from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';

const defaultLocale = 'en_US';

/**
 * Some gettext calls in this package use the `woocommerce` text domain
 * and expect that the translation data is already loaded from WooCommerce.
 * If a translation is missing, these filters will attempt to translate the string
 * using the `wc-calypso-bridge` text domain as a fallback.
 */
addFilter(
	'i18n.gettext_woocommerce',
	'wc-calypso-bridge',
	( translation, text, domain ) => {
		if (
			text === translation &&
			! hasTranslation( text, undefined, domain )
		) {
			// eslint-disable-next-line @wordpress/i18n-no-variables
			return __( text, 'wc-calypso-bridge' );
		}

		return translation;
	}
);
addFilter(
	'i18n.ngettext_woocommerce',
	'wc-calypso-bridge',
	( translation, single, plural, number, domain ) => {
		if (
			( single === translation || plural === translation ) &&
			! hasTranslation( single, undefined, domain )
		) {
			// eslint-disable-next-line @wordpress/i18n-no-variables
			return _n( single, plural, number, 'wc-calypso-bridge' );
		}

		return translation;
	}
);
addFilter(
	'i18n.gettext_with_context_woocommerce',
	'wc-calypso-bridge',
	( translation, text, context, domain ) => {
		if (
			text === translation &&
			! hasTranslation( text, context, domain )
		) {
			// eslint-disable-next-line @wordpress/i18n-no-variables
			return _x( text, context, 'wc-calypso-bridge' );
		}

		return translation;
	}
);
addFilter(
	'i18n.ngettext_with_context_woocommerce',
	'wc-calypso-bridge',
	( translation, single, plural, number, context, domain ) => {
		if (
			( single === translation || plural === translation ) &&
			! hasTranslation( single, context, domain )
		) {
			// eslint-disable-next-line @wordpress/i18n-no-variables
			return _nx( single, plural, number, context, 'wc-calypso-bridge' );
		}

		return translation;
	}
);

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

export default { loadTranslations };
