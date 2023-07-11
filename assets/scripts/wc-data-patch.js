/**
 * Patch the wc.data to work with Gutenberg 15.5+ and wc <= 7.6.0
 *
 */

const patch = () => {
	const { createElement, useEffect } = window.wp.element;
	const { useSelect, useDispatch } = window.wp.data;
	const createHigherOrderComponent =
		window.wp.compose.createHigherOrderComponent;

	const useOptionsHydration = ( data ) => {
		const shouldHydrate = useSelect( ( select ) => {
			const { isResolving, hasFinishedResolution } = select(
				window.wc.data.OPTIONS_STORE_NAME
			);
			if ( ! data ) {
				return {};
			}

			return Object.fromEntries(
				Object.keys( data ).map( ( name ) => {
					const hydrate =
						! isResolving( 'getOption', [ name ] ) &&
						! hasFinishedResolution( 'getOption', [ name ] );
					return [ name, hydrate ];
				} )
			);
		}, [] );
		const { startResolution, finishResolution, receiveOptions } =
			useDispatch( window.wc.data.OPTIONS_STORE_NAME );
		useEffect( () => {
			Object.entries( shouldHydrate ).forEach( ( _ref ) => {
				const [ name, hydrate ] = _ref;

				if ( hydrate ) {
					startResolution( 'getOption', [ name ] );
					receiveOptions( {
						[ name ]: data[ name ],
					} );
					finishResolution( 'getOption', [ name ] );
				}
			} );
		}, [ shouldHydrate ] );
	};

	const override = {
		// https://github.com/woocommerce/woocommerce/pull/37882
		withOptionsHydration: ( data ) =>
			createHigherOrderComponent(
				( OriginalComponent ) => ( props ) => {
					useOptionsHydration( data );
					return createElement( OriginalComponent, { ...props } );
				},
				'withOptionsHydration'
			),
		// https://github.com/woocommerce/woocommerce/pull/37901
		withNavigationHydration: ( data ) =>
			createHigherOrderComponent(
				( OriginalComponent ) => ( props ) => {
					const shouldHydrate = useSelect( ( select ) => {
						if ( ! data ) {
							return;
						}

						const { isResolving, hasFinishedResolution } = select(
							window.wc.data.NAVIGATION_STORE_NAME
						);
						return (
							! isResolving( 'getMenuItems' ) &&
							! hasFinishedResolution( 'getMenuItems' )
						);
					} );
					const { startResolution, finishResolution, setMenuItems } =
						useDispatch( window.wc.data.NAVIGATION_STORE_NAME );

					useEffect( () => {
						if ( ! shouldHydrate ) {
							return;
						}

						startResolution( 'getMenuItems', [] );
						setMenuItems( data.menuItems );
						finishResolution( 'getMenuItems', [] );
					}, [ shouldHydrate ] );

					return createElement( OriginalComponent, { ...props } );
				},
				'withNavigationHydration'
			),
		// https://github.com/woocommerce/woocommerce/pull/37908
		withCurrentUserHydration: ( currentUser ) =>
			createHigherOrderComponent(
				( OriginalComponent ) => ( props ) => {
					const shouldHydrate = useSelect( ( select ) => {
						if ( ! currentUser ) {
							return;
						} // @ts-expect-error both functions are not defined in the wp.data typings

						const { isResolving, hasFinishedResolution } = select(
							window.wc.data.USER_STORE_NAME
						);
						return (
							! isResolving( 'getCurrentUser' ) &&
							! hasFinishedResolution( 'getCurrentUser' )
						);
					} );
					const {
						// @ts-expect-error startResolution is not defined in the wp.data typings
						startResolution,
						// @ts-expect-error finishResolution is not defined in the wp.data typings
						finishResolution,
						receiveCurrentUser,
					} = useDispatch( window.wc.data.USER_STORE_NAME );

					if ( shouldHydrate ) {
						startResolution( 'getCurrentUser', [] );
						receiveCurrentUser( currentUser );
						finishResolution( 'getCurrentUser', [] );
					}

					return createElement( OriginalComponent, { ...props } );
				},
				'withCurrentUserHydration'
			),
		// https://github.com/woocommerce/woocommerce/pull/37896
		withPluginsHydration: ( data ) =>
			createHigherOrderComponent(
				( OriginalComponent ) => ( props ) => {
					const shouldHydrate = useSelect( ( select ) => {
						if ( ! data ) {
							return;
						}

						const { isResolving, hasFinishedResolution } = select(
							window.wc.data.PLUGINS_STORE_NAME
						);
						return (
							! isResolving( 'getActivePlugins', [] ) &&
							! hasFinishedResolution( 'getActivePlugins', [] )
						);
					}, [] );

					const {
						startResolution,
						finishResolution,
						updateActivePlugins,
						updateInstalledPlugins,
						updateIsJetpackConnected,
					} = useDispatch( window.wc.data.PLUGINS_STORE_NAME );
					useEffect( () => {
						if ( ! shouldHydrate ) {
							return;
						}

						startResolution( 'getActivePlugins', [] );
						startResolution( 'getInstalledPlugins', [] );
						startResolution( 'isJetpackConnected', [] );
						updateActivePlugins( data.activePlugins, true );
						updateInstalledPlugins( data.installedPlugins, true );
						updateIsJetpackConnected(
							data.jetpackStatus && data.jetpackStatus.isActive
								? true
								: false
						);
						finishResolution( 'getActivePlugins', [] );
						finishResolution( 'getInstalledPlugins', [] );
						finishResolution( 'isJetpackConnected', [] );
					}, [ shouldHydrate ] );
					return createElement( OriginalComponent, { ...props } );
				},
				'withPluginsHydration'
			),
	};

	window.wc.data = {
		...window.wc.data,
		...override,
	};

	Object.keys( override ).forEach( ( key ) => {
		Object.defineProperty( window.wc.data, key, {
			configurable: false,
			writable: false,
		} );
	} );
};

try {
	if ( window.wc && window.wc.data ) {
		patch();
	}
} catch ( error ) {
	// eslint-disable-next-line no-console
	console.error( error );
}
