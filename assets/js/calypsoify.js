( function( $ ) {
    'use strict';

    /**
     * Record checklist task click
     */
    $( '.checklist__task-title a, .checklist__task-secondary a' ).click( function() {
        var $task = $( this ).closest( '.checklist__task' )
        var status = $task.hasClass( 'is-completed' ) ? 'complete' : 'incomplete';
        var taskTitle = $task.data('title');

        window.jpTracksAJAX.record_ajax_event(
            'atomic_wc_tasklist_clicked',
            'click',
            { 
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
        window.jpTracksAJAX.record_ajax_event(
            'atomic_wc_tasklist_finished',
            'click',
            { 
                complete: complete,
                total: total,
                percentage: percentage
            }
        );
    } );

} )( jQuery );