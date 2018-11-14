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
        var icon = icons.info;
        if ( $( this ).hasClass( 'notice-success') ) {
            icon = icons.checkmark;
        } else if ( $( this ).hasClass( 'error' ) || $( this ).hasClass( 'notice-warning' ) ) {
            icon = icons.notice;
        }
        $( this ).prepend( '<span class="wc-calypso-bridge-notice-icon-wrapper">' + icon + '</span>' );
    } );

    /**
     * Replace dismissal buttons in notices
     */
    $( document ).ready( function() {
        $( '.notice-dismiss' ).html( icons.cross );
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


    /** 
     * Move notices on pages with sub navigation
     * 
     * WP Core moves notices with jQuery so this is needed to move them again since
     * we can't control their position.
     */
    $( document ).ready(function() {
        var $subNavigation = $( '.wrap > form > .subsubsub' );
        if ( $subNavigation.length ) {
            $( 'div.notice, div.error, div.updated, div.warning' ).insertAfter( $subNavigation );
        }
    } );

    /**
     * Toggle taxonomy form
     */
    $( '.taxonomy-form-toggle' ).click( function(e) {
        e.preventDefault();
        $( '#col-container > #col-left' ).toggle();
        $( '#col-container > #col-right' ).toggle();
        $( '.taxonomy-form-toggle' ).toggle();
    } );

    /**
     * Move cancel button
     */
    $( '.taxonomy-form-cancel-button' ).appendTo( '#addtag p.submit' );

} )( jQuery );