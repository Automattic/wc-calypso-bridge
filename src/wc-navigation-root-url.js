/**
 * External dependencies
 */
import { addFilter } from '@wordpress/hooks';

/**
 * Internal dependencies
 */
import storage from './utils/storage';

const LOCAL_STORAGE_PROP = 'wc_nav_root_url_referrer';

const rootUrlStorage = storage( LOCAL_STORAGE_PROP, null );

const getRedirectUrl = ( homeUrl ) => {
	const protocolRegex = /.*?:\/\//i;
	const slug = homeUrl.replace( protocolRegex, '' ).replace( '/', '::' );

	return `https://wordpress.com/home/${ slug }`;
};

export default () => {
	const { isWooPage, homeUrl } = window.wcCalypsoBridge;
	const fromCalypso =
		document.referrer.indexOf( 'https://wordpress.com' ) === 0;

	if ( fromCalypso ) {
		rootUrlStorage.set( 'calypso' );
	} else if ( ! isWooPage ) {
		rootUrlStorage.set( 'wcadmin' );
	}

	// Only continue to add filter if referred by Calypso
	if ( rootUrlStorage.get() !== 'calypso' ) {
		return;
	}

	addFilter(
		'woocommerce_navigation_root_back_url',
		'wc-calypso-bridge',
		() => getRedirectUrl( homeUrl )
	);
};
