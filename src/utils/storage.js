export default ( storageId, defaultValue ) => {
	const storage = {
		set: ( query ) => {
			window.localStorage.setItem( storageId, JSON.stringify( query ) );
		},

		get: () => {
			try {
				return (
					JSON.parse( window.localStorage.getItem( storageId ) ) ||
					defaultValue
				);
			} catch ( e ) {
				/* eslint-disable no-console */
				console.warn(
					`Unable to parse localstorage property ${ storageId }`,
					e.message
				);
			}
		},

		reset: () => {
			storage.set( defaultValue );
		},
	};

	return window.localStorage ? storage : null;
};
