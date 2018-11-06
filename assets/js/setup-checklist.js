( function( $ ) {
	'use strict';

	/**
	 * Toggle address line 2 on click
	 */
	$( document ).on( 'click', '#checklist.is-expanded .checklist__toggle', function( e ) {
		e.preventDefault();
		$( '#checklist' ).removeClass( 'is-expanded' );
		$( '.checklist-card.is-completed' ).hide();
		$( '.checklist__header-complete-label' ).text( i18nstrings.show );
	} );

	$( document ).on( 'click', '#checklist:not(.is-expanded) .checklist__toggle', function( e ) {
		e.preventDefault();
		$( '#checklist' ).addClass( 'is-expanded' );
		$( '.checklist-card.is-completed' ).show();
		$( '.checklist__header-complete-label' ).text( i18nstrings.hide );
	} );

} )( jQuery );