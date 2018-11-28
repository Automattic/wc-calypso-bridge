var pagenow = 'wc-setup';

( function( $ ) {
    'use strict';
    
    /**
     * Simulate "next step" click on button outside form
     */
    $( document ).on ( 'click', '.wc-setup-footer .button-primary', function( e ) {
        e.preventDefault();
        $( '.wc-setup .button-next' ).click();

        var form = $( '.wc-setup .button-next' ).parents( 'form' ).get( 0 );
		if ( ( 'function' !== typeof form.checkValidity ) || form.checkValidity() ) {
			$( this ).attr( 'disabled', true );
		}
    } );

    /**
     * Toggle address line 2 on click
     */
    $( document ).on( 'click', '.toggle-store_address_2', function( e ) {
        e.preventDefault();
        $( this ).hide();
        $( '#store_address_2' ).addClass( 'is-visible' );
    } );

    /**
     * Edit address on click
     */
    $( document ).on( 'click', '.toggle-store_address_edit', function( e ) {
        e.preventDefault();
        $( this ).closest( 'form' ).removeClass( 'store-address-preview-mode' );
        if (window.jpTracksAJAX) {
            window.jpTracksAJAX.record_ajax_event( 'atomic_wc_obw_edit_address', 'click' );
        }
    } );

} )( jQuery );