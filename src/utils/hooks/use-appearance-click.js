/**
 * External dependencies
 */
import { useDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import escape from '../escape';

const siteSlug = escape( window.wcCalypsoBridge.siteSlug );

export const useAppearanceClick = () => {
	const { actionTask } = useDispatch( 'wc/admin/onboarding' );
	const onClick = () => {
		actionTask( 'appearance' );
		window.location = `https://wordpress.com/themes/${ siteSlug }`;
	};

	return { onClick };
};
