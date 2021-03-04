export default ( storageId, defaultValue = null ) => {
	const errorWrapper = ( fn ) => ( ...args ) => {
		try {
			return fn( ...args );
		} catch ( e ) {
			/* eslint-disable no-console */
			console.warn(
				`Error encountered when attempting to use localStorage: `,
				e.message
			);

			return null;
		}
	};

	const storage = {
		set: errorWrapper( ( query ) => {
			window.localStorage.setItem( storageId, JSON.stringify( query ) );
		} ),

		get: errorWrapper( () => {
			const val = JSON.parse( window.localStorage.getItem( storageId ) );
			return typeof val === 'boolean' || val ? val : defaultValue;
		} ),

		reset: () => {
			storage.set( defaultValue );
		},
	};

	return storage;
};
