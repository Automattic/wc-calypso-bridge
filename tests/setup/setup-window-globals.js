// Check if we're in a JSDOM test or not
if ( global.window ) {
	window.wcCalypsoBridge = {
		siteSlug: 'test-site',
	};
}
