( function( $ ) {
	'use strict';

	/**
	 * Action header mobile navigation.
	 */
	$( document ).on( 'click', '.action-header:not(.action-header-sidebar) .action-header__ground-control-back', function( e ) {
		if ( $( window ).width() < 661 ) {
			e.preventDefault();
			$( '#wp-admin-bar-menu-toggle .ab-item' ).click();
		}
	} );

// @todo Remove the notice handlers after https://github.com/Automattic/jetpack/pull/13126 has been released.
	/**
	 * Prepend icons to notices.
	 */
	$( 'div.notice, div.error, div.updated, div.warning' ).each( function() {
		if ( $( this ).children( '.wc-calypso-bridge-notice-content' ).length ) {
			return;
		}

		var icon = icons.info;
		if ( $( this ).hasClass( 'notice-success' ) ) {
			icon = icons.checkmark;
		} else if ( $( this ).hasClass( 'error' ) || $( this ).hasClass( 'notice-warning' ) ) {
			icon = icons.notice;
		}
		$( this ).prepend( '<span class="wc-calypso-bridge-notice-icon-wrapper">' + icon + '</span>' );
	} );

	/**
	 * Replace dismissal buttons in notices.
	 */
	$( document ).ready( function() {
		$( '.notice-dismiss' ).html( icons.cross );
	} );

	/**
	 * Place notice content inside it's own tag.
	 *
	 * Used to prevent side by side content in flexbox when multiple paragraphs exist.
	 */
	$( 'div.notice, div.error, div.updated, div.warning' ).each( function() {
		if ( $( this ).children( '.wc-calypso-bridge-notice-content' ).length ) {
			return;
		}

		var $noticeContent = $( '<div class="wc-calypso-bridge-notice-content"></div>' );
		$( this ).find( '.wc-calypso-bridge-notice-icon-wrapper' ).after( $noticeContent );
		$( this ).find( 'p:not(.submit)' ).appendTo( $noticeContent );
	} );
// @todo End

	/**
	 * Move notices on pages with sub navigation.
	 *
	 * WP Core moves notices with jQuery so this is needed to move them again since
	 * we can't control their position.
	 */
	$( document ).ready( function() {
		var $subNavigation = $( '.wrap .subsubsub' );
		if ( $subNavigation.length ) {
		$( 'div.notice, div.error, div.updated, div.warning' ).insertAfter( $subNavigation.first() );
			$( '.jetpack-jitm-message, .jitm-card' ).insertAfter( $subNavigation.first() );
		}
	} );

	/**
	 * Append subnav dropdown for mobile.
	 */
	$( document ).ready( function() {
		$( '#wpbody-content .subsubsub, #wpbody-content .nav-tab-wrapper' ).each( function() {
			const currentText = $( this ).find( 'a.current, .nav-tab-active' ).text();
			const $toggle = $( '<div class="nav-tab-toggle"><span class="nav-tab-toggle__current-page">' + currentText + '</span>' + icons.chevronDown + '</div>' );
			$( this ).wrap( '<div class="nav-tab-container"></div>' );
			$( this ).before( $toggle );
		} );
	} );

	/**
	 * Append subnav dropdown for mobile.
	 */
	$( document ).on( 'click', '.nav-tab-toggle', function() {
		$( this ).parent().toggleClass( 'is-open' );
	} );

	/**
	 * Remove auto-fold for admin sidebar menu.
	 */
	function removeAutoFold() {
		if ( $( this ).width() > 660 && $( this ).width() <= 960 ) {
			$( 'body' ).removeClass( 'auto-fold' );
		} else {
			$( 'body' ).addClass( 'auto-fold' );
		}
	}
	$( window ).on( 'resize', removeAutoFold );
	$( document ).on( 'ready', removeAutoFold );

	/**
	 * Table scrolling shadow.
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
	 * Append notice.
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

	/**
	 * Focus select2 input on click.
	 */
	$( document ).on( 'click', '.select2', function() {
		setTimeout( function() {
			$( '.select2-container--open .select2-search__field' ).focus();
		}, 0 );
	} );

	$( document ).ready( function() {
		$( '.toplevel_page_wc-support' ).attr( 'target', '_blank' );
	} );

	/**
	 * Support link click event.
	 */
	$( '.wc-support-link' ).unbind( 'click' ).click( function( e ) {
		const source = $( this ).data( 'source' )
		const href = $( this ).attr( 'href' );
		trackSupportClick( source, href );
	} );

	/**
	 * Track support link clicks in Jetpack if enabled.
	 *
	 * @param {string} source Source of click (footer or sidebar)
	 * @param {string} href Link location
	 */
	function trackSupportClick( source, href ) {
		if ( window.jpTracksAJAX ) {
			return window.jpTracksAJAX.record_ajax_event(
				'atomic_wc_support_link_click',
				'click',
				{
					href,
					source,
				}
			);
		} else {
			return true;
		}
	}

} )( jQuery );