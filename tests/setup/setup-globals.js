import { registerStore } from '@wordpress/data';

// Mock core/notices store for components dispatching core notices
registerStore( 'core/notices', {
	reducer: () => {
		return {};
	},
	actions: {
		createNotice: () => {},
	},
	selectors: {},
} );

