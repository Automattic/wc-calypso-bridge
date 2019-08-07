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
	 * Move page actions to action header.
	 */
	$( '.page-title-action, .add-new-h2' ).appendTo( '#action-header .action-header__actions' );

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
	 * Toggle taxonomy form.
	 */
	function toggleTaxonomyForm() {
		$( '#col-container > #col-left' ).toggle();
		$( '#col-container > #col-right' ).toggle();
		$( '.taxonomy-form-toggle' ).toggle();
		$( '.wrap .search-form' ).toggle();
		$( '.form-wrap h2:first' ).hide();
		const formTitle = $( '.form-wrap h2:first' ).text();
		if ( ! $( '#breadcrumb-taxonomy' ).length ) {
			$( '.action-header__breadcrumbs' ).append(
				'<span id="breadcrumb-taxonomy" style="display: none;">' + formTitle + '</span>'
			);
		}
		$( '#breadcrumb-taxonomy' ).toggle();
	}

	/**
	 * Click handler for taxonomy add new/cancel buttons.
	 */
	$( '.taxonomy-form-toggle' ).click( function( e ) {
		e.preventDefault();
		toggleTaxonomyForm();
		history.pushState( {}, $( '.action-header__breadcrumbs span:last' ).text(), window.location );
	} );

	/**
	 * Toggle form on back button.
	 */
	$( window ).on( 'popstate', function( e ) {
		toggleTaxonomyForm();
	} );

	/**
	 * Move cancel button.
	 */
	$( '.taxonomy-form-cancel-button' ).appendTo( 'p.submit' );

	/**
	 * Product attributes form is not AJAX'ed so toggle back if any errors.
	 */
	if ( $( '#woocommerce_errors' ).length ) {
		toggleTaxonomyForm();
	}

	/**
	 * Add cancel button to taxonomy edit forms.
	 */
	$( '.edit-tag-actions .button:first' ).after(
		'<a href="' + ( 'undefined' !== typeof taxonomy ? taxonomy.listUrl : '#' ) + '" class="button button-secondary button-large taxonomy-edit-cancel-button">' + translations.cancel + '</a>'
	);

	/**
	 * Move search box to subnav.
	 */
	var $subNav = $( '.subsubsub' );
	if ( $subNav.length ) {
		var $searchBoxListItem = $( '<li class="subsubsub__search-item"></li>' ).appendTo( $subNav );
		var $searchBox = $( '#posts-filter .search-box' );
		var $searchInput = $searchBox.find( 'input[type=search]' );
		var $searchLabel = $searchInput.siblings( 'label' );
		var uniqueId = Math.floor( Math.random() * 26 ) + Date.now();

		$searchInput.attr( 'placeholder', $searchLabel.text().replace( /\:$/, '' ) );
		$searchBox.closest( 'form' ).attr( 'data-form-id', uniqueId );
		$searchBox.attr( 'data-target-form-id', uniqueId );
		$searchBox.appendTo( $searchBoxListItem );
		$subNav.addClass( 'has-search' );
		if ( $searchInput.val() && $searchInput.val().length ) {
			$searchBox.addClass( 'is-expanded' );
		}
	}

	/**
	 * Add icons to search boxes.
	 */
	$( '.search-box' ).prepend( '<button class="search-box__search-icon" aria-label="' + translations.openSearch + '">' + icons.search + '</button>' );
	$( '.search-box' ).append( '<button class="search-box__close-icon" aria-label="' + translations.closeSearch + '">' + icons.cross + '</button>' );

	/**
	 * Focus search input on open icon click.
	 */
	$( document ).on( 'click', '.search-box__search-icon', function( e ) {
		e.preventDefault();
		$( this ).closest( '.search-box' ).addClass( 'has-focus' );
		// Defer focus when expanding since input is not displayed
		var $searchInput = $( this ).siblings( 'input[name="s"]' );
		setTimeout( function() {
			$searchInput.focus();
		}, 0 );
	} );

	/**
	 * Open search when inside nav.
	 */
	$( document ).on( 'click', '.subsubsub .search-box__search-icon', function( e ) {
		$( this ).closest( '.search-box' ).addClass( 'is-expanded' );
	} );

	/**
	 * Close search when inside nav.
	 */
	$( document ).on( 'click', '.subsubsub .search-box__close-icon', function( e ) {
		e.preventDefault();
		$( this ).closest( '.search-box' ).removeClass( 'is-expanded' );
	} );

	/**
	 * Add focus class to search box wrapper on focus.
	 */
	$( document ).on( 'focus', 'input[name="s"]', function() {
		$( this ).closest( '.search-box' ).addClass( 'has-focus' );
	} );

	/**
	 * Remove focus on blur.
	 */
	$( document ).on( 'blur', 'input[name="s"]', function() {
		$( this ).closest( '.search-box' ).removeClass( 'has-focus' );
	} );

	/**
	 * Fix search for inputs outside of forms by appending inputs on enter/click.
	 */
	function appendInputsToForm( e ) {
		if ( e.type === 'click' || e.which === 13 ) {
			e.preventDefault();
			const formId = $( this ).closest( '.search-box' ).data( 'target-form-id' );
			const $form = $( 'form[data-form-id="' + formId + '"' );
			const $searchInput = $( this ).closest( '.search-box' ).find( 'input[type="search"]' );
			$( '<input>' ).attr( {
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
	 * Submit regular search forms on enter.
	 */
	$( document ).on( 'keypress', 'div:not(.subsubsub) .search-box input[type=search]', function( e ) {
		if ( e.which === 13 ) {
			e.preventDefault();
			$( this ).closest( 'form' ).submit();
		}
	} );

	/**
	 * Disable autocomplete for search inputs.
	 */
	$( document ).on( 'focus', 'input[type=search]', function() {
		$( this ).attr( 'autocomplete', 'off' );
	} );

	/**
	 * Clear the search query when clicking the close icon not inside subnav.
	 */
	$( document ).on( 'click', 'div:not(.subsubsub) .search-box__close-icon', function( e ) {
		e.preventDefault();
		$( this ).closest( '.search-box' ).find( 'input[type=search]' ).val( '' );
		$( this ).closest( '.search-box' ).removeClass( 'has-value' );
	} );

	/**
	 * Add has-value class on type.
	 */
	$( document ).on( 'keyup', '.search-box input[type="search"]', function( e ) {
		if ( $( this ).val() ) {
			$( this ).closest( '.search-box' ).addClass( 'has-value' );
		} else {
			$( this ).closest( '.search-box' ).removeClass( 'has-value' );
		}
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
	 * Detect changes to tag/category table.
	 */
	var addedTags = [];
	$( '#the-list[data-wp-lists="list:tag"] tr .name .row-title' ).each( function() {
		addedTags.push( $( this ).text() );
	} );
	$( window ).load( function() {
		$( 'body' ).on( 'DOMSubtreeModified', '#the-list[data-wp-lists="list:tag"]', function() {
			var tagName = $( this ).find( 'tr:first .name .row-title' ).text();
			if ( $.inArray( tagName, addedTags ) === -1 ) {
				addedTags.push( tagName );
				toggleTaxonomyForm();
				appendNotice( translations.taxonomySuccess.replace( '{name}', tagName ), 'success' );
			}
		} );
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

	/**
	 * Copy WooCommerce Admin breadcrumbs to Calypso header.
	 */
	var wca_crumb_retry = 0;
	function wca_update_crumbs() {
		if ( ! $( '.wrap > #root' ).length ) {
			return;
		}

		var wc_breadcrumbs = $( '.woocommerce-layout__header-breadcrumbs' ).children();
		var cb_crumb_wrap  = $( '.action-header__breadcrumbs' );

		if ( ! wc_breadcrumbs.length ) {
			if ( 20 < ++wca_crumb_retry ) {
				setTimeout( wca_update_crumbs, 100 );
			} else {
				wca_crumb_retry = 0;
			}

			return;
		}

		wca_crumb_retry = 0;
		cb_crumb_wrap.children().remove();
		wc_breadcrumbs.clone().appendTo( cb_crumb_wrap );
	}

	$('#wpwrap').click( wca_update_crumbs );

	wca_update_crumbs();

} )( jQuery );