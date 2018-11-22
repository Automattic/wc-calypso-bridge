( function( $ ) {
    'use strict';

    /**
     * Action header mobile navigation
     */
    $( document ).on( 'click', '.action-header:not(.action-header-sidebar) .action-header__ground-control-back', function( e ) {
        if ( $( window ).width() < 661 ) {
            e.preventDefault();
            $( '#wp-admin-bar-menu-toggle .ab-item' ).click();
        }
    } );

    /**
     * Record checklist task click
     */
    $( '.checklist__task-title a, .checklist__task-secondary a' ).click( function() {
        const $task = $( this ).closest( '.checklist__task' )
        const status = $task.hasClass( 'is-completed' ) ? 'complete' : 'incomplete';
        const taskId = $task.data('id');
        const taskTitle = $task.data('title');
        const href = $( this ).attr('href');
        $( this ).addClass( 'disabled' );

        if ( window.jpTracksAJAX ) {
            const trackedEvent = window.jpTracksAJAX.record_ajax_event(
                'atomic_wc_tasklist_click',
                'click',
                {
                    id: taskId,
                    title: taskTitle, 
                    status: status,
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
     * Move page actions to action header
     */
    $( '.page-title-action, .add-new-h2' ).appendTo( '#action-header .action-header__actions' );

    /** 
     * Move notices on pages with sub navigation
     * 
     * WP Core moves notices with jQuery so this is needed to move them again since
     * we can't control their position.
     */
    $( document ).ready(function() {
        var $subNavigation = $( '.wrap .subsubsub' );
        if ( $subNavigation.length ) {
            $( 'div.notice, div.error, div.updated, div.warning' ).insertAfter( $subNavigation.first() );
        }
    } );


    /**
     * Append subnav dropdown for mobile
     */
    $( document ).ready(function() {
        const $subNavigation = $( '.wrap .subsubsub' );
        if ( $subNavigation.length ) {
            const currentPage = $subNavigation.find( 'a.current' ).text();
            const $toggle = $( '<div class="subsubsub-toggle"><span class="subsubsub-toggle__current-page">' + currentPage + '</span>' + icons.chevronDown + '</div>' );
            $subNavigation.first().wrap( '<div class="subsubsub-wrapper"></div>' );
            $( '.subsubsub-wrapper' ).prepend( $toggle );
        }
    } );

    /**
     * Append subnav dropdown for mobile
     */
    $( document ).on( 'click', '.subsubsub-toggle', function() {
        $( this ).parent().toggleClass( 'is-open' );
    } );

    /**
     * Toggle taxonomy form
     */
    function toggleTaxonomyForm() {
        $( '#col-container > #col-left' ).toggle();
        $( '#col-container > #col-right' ).toggle();
        $( '.taxonomy-form-toggle' ).toggle();
        $( '.wrap .search-form' ).toggle();
    }

    /**
     * Click handler for taxonomy add new/cancel buttons
     */
    $( '.taxonomy-form-toggle' ).click( function(e) {
        e.preventDefault();
        toggleTaxonomyForm();
    } );

    /**
     * Move cancel button
     */
    $( '.taxonomy-form-cancel-button' ).appendTo( '#addtag p.submit' );

    /**
     * Move search box to subnav
     */
    var $subNav = $( '.subsubsub' );
    if ( $subNav.length ) {
        var $searchBoxListItem = $( '<li class="subsubsub__search-item"></li>').appendTo( $subNav );
        var $searchBox = $( '#posts-filter .search-box' );
        var $searchInput = $searchBox.find( 'input[type=search]' );
        var uniqueId = Math.floor(Math.random() * 26) + Date.now();

        $searchBox.closest( 'form' ).attr( 'data-form-id', uniqueId );
        $searchBox.attr( 'data-target-form-id', uniqueId );
        $searchBox.appendTo( $searchBoxListItem );
        $subNav.addClass( 'has-search' );
        if ( $searchInput.val() && $searchInput.val().length ) {
            $searchBox.addClass( 'is-expanded' );
        }
    }

    /**
     * Add icons to search boxes
     */
    $( '.search-box' ).prepend( '<button class="search-box__search-icon" aria-label="' + wcb.openSearch + '"><svg class="gridicon gridicons-search" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M21 19l-5.154-5.154C16.574 12.742 17 11.42 17 10c0-3.866-3.134-7-7-7s-7 3.134-7 7 3.134 7 7 7c1.42 0 2.742-.426 3.846-1.154L19 21l2-2zM5 10c0-2.757 2.243-5 5-5s5 2.243 5 5-2.243 5-5 5-5-2.243-5-5z"/></g></svg></button>' );
    $( '.search-box' ).append( '<button class="search-box__close-icon" aria-label="' + wcb.closeSearch + '"><svg class="gridicon gridicons-cross" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M18.36 19.78L12 13.41l-6.36 6.37-1.42-1.42L10.59 12 4.22 5.64l1.42-1.42L12 10.59l6.36-6.36 1.41 1.41L13.41 12l6.36 6.36z"/></g></svg></button>' );

    /**
     * Focus search input on open icon click
     */
    $( document ).on( 'click', '.search-box__search-icon', function(e) {
        e.preventDefault();
        $( this ).closest( '.search-box' ).addClass( 'has-focus' );
        // Defer focus when expanding since input is not displayed
        var $searchInput = $( this ).siblings( 'input[name="s"]' );
        setTimeout( function() {
            $searchInput.focus();
        }, 0 );
    } );

    /**
     * Open search when inside nav
     */
    $( document ).on( 'click', '.subsubsub .search-box__search-icon', function(e) {
        $( this ).closest( '.search-box' ).addClass( 'is-expanded' );
    } );

    /**
     * Close search when inside nav
     */
    $( document ).on( 'click', '.subsubsub .search-box__close-icon', function(e) {
        e.preventDefault();
        $( this ).closest( '.search-box' ).removeClass( 'is-expanded' );
    } );

    /**
     * Add focus class to search box wrapper on focus
     */
    $( document ).on( 'focus', 'input[name="s"]', function() {
        $( this ).closest( '.search-box' ).addClass( 'has-focus' );
    } );

    /**
     * Remove focus on blur
     */
    $( document ).on( 'blur', 'input[name="s"]', function() {
        $( this ).closest( '.search-box' ).removeClass( 'has-focus' );
    } );

    /**
     * Fix search for inputs outside of forms by appending inputs on enter/click
     */
    function appendInputsToForm( e ) {
        if ( e.type === 'click' || e.which === 13 ) {
            e.preventDefault();
            const formId = $( this ).closest( '.search-box' ).data( 'target-form-id' );
            const $form = $( 'form[data-form-id="' + formId + '"' );
            const $searchInput = $( this ).closest( '.search-box' ).find( 'input[type="search"]' );
            $( '<input>' ).attr(
                {
                    type: 'hidden',
                    id: $searchInput.attr( 'id' ),
                    name: $searchInput.attr( 'name' ),
                    value: $searchInput.val(),
                }
            ).appendTo( $form );
            $form.submit();
        }
    }
    $( document ).on( 'click', '.subsubsub .search-box input[type=submit]', appendInputsToForm );
    $( document ).on( 'keypress', '.subsubsub .search-box input[type=search]', appendInputsToForm );

    /**
     * Submit regular search forms on enter
     */
    $( document ).on( 'keypress', 'div:not(.subsubsub) .search-box input[type=search]', function( e ) {
        if ( e.which === 13 ) {
            e.preventDefault();
            $( this ).closest( 'form' ).submit();
        }
    } );

    /** 
     * Disable autocomplete for search inputs
     */
    $( document ).on( 'focus', 'input[type=search]', function() {
        $( this ).attr( 'autocomplete', 'off' );
    } );

    /**
     * Clear the search query when clicking the close icon not inside subnav
     */
    $( document ).on( 'click', 'div:not(.subsubsub) .search-box__close-icon', function(e) {
        e.preventDefault();
        $( this ).closest( '.search-box' ).find( 'input[type=search]' ).val( '' );
        $( this ).closest( '.search-box' ).removeClass( 'has-value' );
    } );

    /**
     * Add has-value class on type
     */
    $( document ).on( 'keyup', '.search-box input[type="search"]', function(e) {
        if ( $( this ).val() ) {
            $( this ).closest( '.search-box' ).addClass( 'has-value' );
        } else {
            $( this ).closest( '.search-box' ).removeClass( 'has-value' );
        }
    } );

    /**
     * Remove auto-fold for admin sidebar menu
     */
    function removeAutoFold() {
        if ( $(this).width() > 660 && $(this).width() <= 960 ) {
            $( 'body' ).removeClass('auto-fold');
        } else {
            $( 'body' ).addClass('auto-fold');
        }
    }
    $( window ).on( 'resize', removeAutoFold );
    $( document ).on( 'ready', removeAutoFold );

    /**
     * Table scrolling shadow
     */
    function checkTableScroll() {
        const scrolledToEnd = $( this )[0].scrollWidth - $( this )[0].scrollLeft <= $( this )[0].offsetWidth;
        if ( ! scrolledToEnd ) {
            $( this ).parent().addClass( 'is-scrollable' )
        } else {
            $( this ).parent().removeClass( 'is-scrollable' );
        }
    }
    $( '.wp-list-table-wrapper__inner' ).scroll( checkTableScroll );
    $( '.wp-list-table-wrapper__inner' ).scroll();
    $( window ).resize( function() {
        $( '.wp-list-table-wrapper__inner' ).scroll();
    } );

    /**
     * Detect changes to tag/category table
     */
    var addedTags = [];
    $( window ).load( function() {
        $( 'body' ).on( 'DOMSubtreeModified', '#the-list[data-wp-lists="list:tag"]', function() {
            var tagName = $( this ).find( 'tr:first .name .row-title' ).text();
            if ( $.inArray( tagName, addedTags ) === -1 ) {
                addedTags.push( tagName );
                toggleTaxonomyForm();
                appendNotice( wcb.taxonomySuccess.replace( '{name}', tagName ), 'success' );
            }
        } );
    } );

    /**
     * Append notice
     */
    function appendNotice( content, type ) {
        var html = '';
        var icon = icons.info;
        var classes = [ 'notice' ];
        if ( 'success' === type ) {
            icon = icons.checkmark;
            classes.push( 'notice-success' );
        } else if ( 'error' === type ) {
            icon = icons.notice;
            classes.push( 'error' );
        }
        html += '<div class="' + classes.join( ' ' ) + '">';
        html += '<span class="wc-calypso-bridge-notice-icon-wrapper">';
        html += icon;
        html += '</span>';
        html += '<div class="wc-calypso-bridge-notice-content"><p>' + content + '</p></div>'
        html += '</div>';
        $( html ).insertAfter( 'h1.wp-heading-inline:first' );
    }


} )( jQuery );