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
	
	/**
	 * Track 'I'm done' completion on task list
	 */
	$( '.setup-footer a' ).click( function(e) {
		e.preventDefault();
		const progressNumber = $( '.checklist__header-progress-number' ).text().split( '/' );
		const complete = progressNumber[0];
		const total = progressNumber[1];
		const percentage = parseFloat( complete / total ).toFixed( 2 ) * 100;
		const href = $( this ).attr('href');
		$( this ).addClass( 'disabled' );
		
		if ( window.jpTracksAJAX ) {
			const trackedEvent = window.jpTracksAJAX.record_ajax_event(
				'atomic_wc_tasklist_finish',
				'click',
				{ 
					complete: complete,
					total: total,
					percentage: percentage
				}
			);
			trackedEvent.complete( function() {
				window.location = href;
			} );
		} else {
			window.location = href;
		}
	} );
		
	/**
	 * Checklist task click event
	 */
	$( '.checklist__task-title a, .checklist__task-secondary a' ).click( function( e ) {
		e.preventDefault();
		const $task = $( this ).closest( '.checklist__task' )
		const status = $task.hasClass( 'is-completed' ) ? 'complete' : 'incomplete';
		const taskId = $task.data('id');
		const taskTitle = $task.data('title');
		const href = $( this ).attr('href');
		$( this ).addClass( 'disabled' );
		
		$.when(
			trackTaskClick( taskId, taskTitle, status ),
			setActiveTask( taskId )
		).done( function() {
			window.location = href;
		} );
	} );
			
	/**
	 * Track task clicks in Jetpack if enabled
	 *
	 * @param {string} taskId Task ID
	 * @param {string} taskTitle Task title
	 * @param {string} status Status - complete/incomplete
	 */
	function trackTaskClick( taskId, taskTitle, status ) {
		if ( window.jpTracksAJAX ) {
			return window.jpTracksAJAX.record_ajax_event(
				'atomic_wc_tasklist_click',
				'click',
				{
					id: taskId,
					title: taskTitle, 
					status: status,
				}
			);
		} else {
			return true;
		}
	}
				
	/**
	 * Sets the active task for return notices
	 *
	 * @param {string} taskId Task ID
	 */
	function setActiveTask( taskId ) {
		return $.ajax(
			{
				url: wccb.ajaxUrl,
				data: (
					{
						action: 'set_woocommerce_setup_active_task',
						nonce: wccb.nonce,
						taskId: taskId
					}
				),
			}
		);
	}
						
	/**
	 * Persist setup checklist notice dismiss
	 */
	$( document ).on( 'click', '#woocommerce-setup-return-notice .notice-dismiss', function() {
		$.ajax(
			{
				url: wccb.ajaxUrl,
				data: (
					{
						action: 'clear_woocommerce_setup_active_task',
						nonce: wccb.nonce,
					}
				),
			}
		);
	} );
			
} )( jQuery );
