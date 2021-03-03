/**
 * External dependencies
 */
import { addFilter } from '@wordpress/hooks';

/**
 * Internal dependencies
 */
import storage from './utils/storage';

export default () => {
	const rootUrlStorage = storage( 'wc-nav-root-url', null );
	const isWooPage =
		window.wcCalypsoBridge && window.wcCalypsoBridge.isWooPage;
	const fromCalypso =
		document.referrer.indexOf( 'https://wordpress.com' ) === 0;

	if ( fromCalypso ) {
		rootUrlStorage.set( 'calypso' );
	} else if ( ! isWooPage ) {
		rootUrlStorage.set( 'wcadmin' );
	}

	addFilter(
		'woocommerce_navigation_root_back_url',
		'plugin-domain',
		( rootUrl ) => {
			if ( rootUrlStorage.get() === 'calypso' ) {
				return 'https://wordpress.com/home/';
			}

			return rootUrl;
		}
	);
};
