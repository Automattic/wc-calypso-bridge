document.addEventListener( 'DOMContentLoaded', function () {
	// Prefer wc.tracks.recordEvent since it supports debugging.
	let recordEvent = null;
	if ( window.wc && window.wc.tracks && window.wc.tracks.recordEvent ) {
		recordEvent = window.wc.tracks.recordEvent;
	} else if ( window.wcTracks && window.wcTracks.recordEvent ) {
		recordEvent = window.wcTracks.recordEvent;
	} else {
		recordEvent = function () {};
	}

	const el = document.getElementById( 'banner_button' );
	if ( el ) {
		el.addEventListener( 'click', function () {
			recordEvent( 'free_trial_upgrade_now', {
				source: 'frontend_banner',
			} );
		} );
	}
} );
