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

} )( jQuery );