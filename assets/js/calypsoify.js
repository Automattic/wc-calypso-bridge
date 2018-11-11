( function( $ ) {
    'use strict';

    /**
     * Record checklist task click
     */
    $( '.checklist__task-title a, .checklist__task-secondary a' ).click( function() {
        var $task = $( this ).closest( '.checklist__task' )
        var status = $task.hasClass( 'is-completed' ) ? 'complete' : 'incomplete';
        var taskId = $task.data('id');
        var taskTitle = $task.data('title');

        window.jpTracksAJAX.record_ajax_event(
            'atomic_wc_tasklist_click',
            'click',
            {
                id: taskId,
                title: taskTitle, 
                status: status,
            }
        );
    } );

    /**
     * Track 'I'm done' completion on task list
     */
    $( '.setup-footer a' ).click( function(e) {
        e.preventDefault();
        var progressNumber = $( '.checklist__header-progress-number' ).text().split( '/' );
        var complete = progressNumber[0];
        var total = progressNumber[1];
        var percentage = parseFloat( complete / total ).toFixed( 2 ) * 100;
        if (window.jpTracksAJAX) {
            window.jpTracksAJAX.record_ajax_event(
                'atomic_wc_tasklist_finish',
                'click',
                { 
                    complete: complete,
                    total: total,
                    percentage: percentage
                }
            );
        }
    } );

    /**
     * Append icons to notices
     */
    $( 'div.notice, div.error, div.updated, div.warning' ).each( function() {
        var icon = '<svg class="gridicon gridicons-info" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></g></svg>';
        if ( $( this ).hasClass( 'notice-success') ) {
            icon = '<svg class="gridicon gridicons-checkmark" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M9 19.414l-6.707-6.707 1.414-1.414L9 16.586 20.293 5.293l1.414 1.414"/></g></svg>';
        } else if ( $( this ).hasClass( 'error' ) || $( this ).hasClass( 'notice-warning' ) ) {
            icon = '<svg class="gridicon gridicons-notice" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm1 15h-2v-2h2v2zm0-4h-2l-.5-6h3l-.5 6z"/></g></svg>';
        }
        $( this ).prepend( '<span class="wc-calypso-bridge-notice-icon-wrapper">' + icon + '</span>' );
    } );

    /**
     * Replace dismissal buttons in notices
     */
    $( document ).ready( function() {
        $( '.notice-dismiss' ).html( '<svg class="gridicon gridicons-cross" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M18.36 19.78L12 13.41l-6.36 6.37-1.42-1.42L10.59 12 4.22 5.64l1.42-1.42L12 10.59l6.36-6.36 1.41 1.41L13.41 12l6.36 6.36z"/></g></svg>' );
    } );

    /**
     * Place notice content inside it's own tag
     * 
     * Used to prevent side by side content in flexbox when multiple paragraphs exist.
     */
    $( 'div.notice, div.error, div.updated, div.warning' ).each( function() {
        var $noticeContent = $( '<div class="wc-calypso-bridge-notice-content"></div>' );
        $( this ).find( '.wc-calypso-bridge-notice-icon-wrapper' ).after( $noticeContent );
        $( this ).find( 'p:not(.submit)' ).appendTo( $noticeContent );
    } );

    /**
     * Wrap page title actions to align right
     */
    var $pageTitleActionsContainer = $( '<div class="page-title-actions"></div>' );
    $pageTitleActionsContainer.insertAfter( 'h1.wp-heading-inline' );
    $( '.page-title-action' ).appendTo( $pageTitleActionsContainer );

} )( jQuery );