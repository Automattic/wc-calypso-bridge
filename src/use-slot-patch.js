'use strict';
/*
 * This is a temporary fix to override the useSlot hook in the @woocommerce/experimental package until the WooCommerce updated to 7.5.0.
 */

const wpUseSlotFills = window.wp.components.__experimentalUseSlotFills;
const wpUseSlot = window.wp.components.__experimentalUseSlot;

// Override the useSlot hook only if the experimental useSlotFills hook is available (Gutenberg 14.3+).
if ( typeof wpUseSlotFills === 'function' ) {
	// We need to re-assign the useSlot hook to the experimental object because it doesn't allow us to redefine property directly.
	window.wc.experimental = {
		...window.wc.experimental,
		useSlot: ( name ) => {
			const slot = wpUseSlot( name );
			const fills = wpUseSlotFills( name );
			return {
				...slot,
				fills,
			};
		},
	};

	// Make the useSlot hook non-configurable and non-writable.
	Object.defineProperty( window.wc.experimental, 'useSlot', {
		configurable: false,
		writable: false,
	} );
}
