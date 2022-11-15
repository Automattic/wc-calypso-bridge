/**
 * External dependencies
 */
import { Card, CardBody, CardHeader, Button, Notice } from '@wordpress/components';
import { useState, useEffect } from 'wordpress-element';
import apiFetch from '@wordpress/api-fetch';

const LaunchedSiteNotice = ( { errorMessage } ) => {

	if ( ! errorMessage ) {
		return null;
	}

	return (
		<Notice
			className="wcpay-connect-error-notice"
			status="error"
			isDismissible={false}
		>
			{errorMessage}
		</Notice>
	);
};

const LaunchStorePage = () => {

	return (
		<div>
			<h1>HEY THERE</h1>
			<LaunchedSiteNotice errorMessage="Error now"/>
		</div>
	);
};
export default LaunchStorePage;
