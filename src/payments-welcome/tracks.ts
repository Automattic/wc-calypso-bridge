/**
 * External dependencies
 */
import domReady from '@wordpress/dom-ready';

declare global {
	interface Window {
		wcTracks: {
			isEnabled: boolean;
		};
		wc: any;
	}
}

/**
 * Checks if site tracking is enabled.
 *
 * @return {boolean} True if site tracking is enabled.
 */
function isEnabled() {
	return window.wcTracks.isEnabled;
}

/**
 * Records site event.
 *
 * @param {string}  eventName       Name of the event.
 * @param {Object?} eventProperties Event properties.
 */
function recordEvent( eventName: string, eventProperties?: object ) {
	// Wc-admin track script is enqueued after ours, wrap in domReady
	// to make sure we're not too early.
	domReady( () => {
		const recordFunction =
			window.wc?.tracks?.recordEvent ?? window.wcTracks.recordEvent;
		recordFunction( eventName, eventProperties );
	} );
}

const events = {
	CONNECT_ACCOUNT_CLICKED: 'wcpay_connect_account_clicked',
	CONNECT_ACCOUNT_LEARN_MORE: 'wcpay_welcome_learn_more',
	CONNECT_ACCOUNT_VIEW: 'page_view',
	SURVEY_FEEDBACK: 'wcpay_exit_survey',
};

export default {
	isEnabled,
	recordEvent,
	events,
};
