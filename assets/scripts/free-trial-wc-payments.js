( function () {
	const __ = wp.i18n.__;
	/**
	 * Wait for an element.
	 * This is required as there's no official way of waiting for an React component to finish.
	 *
	 * Source: https://stackoverflow.com/questions/5525071/how-to-wait-until-an-element-exists
	 */
	const waitForElm = ( selector ) => {
		return new Promise( ( resolve ) => {
			if ( document.querySelector( selector ) ) {
				return resolve( document.querySelector( selector ) );
			}
			const observer = new MutationObserver( ( mutations ) => {
				if ( document.querySelector( selector ) ) {
					resolve( document.querySelector( selector ) );
					observer.disconnect();
				}
			} );

			observer.observe( document.body, {
				childList: true,
				subtree: true,
			} );
		} );
	};

	const getNotice = ( copySelector ) => {
		const defaultCopy = __(
			"During the trial period you can only make test payments. To process real transactions, <a href='%s'>upgrade now.</a>",
			'wc-calypso-bridge'
		);

		const copies = {
			default: defaultCopy,
			transactions: defaultCopy,
			deposits: __(
				"During the trial period you’ll not be able to get deposits. To receive payments and payouts, <a href='%s'>upgrade now.</a>",
				'wc-calypso-bridge'
			),
		};

		const upgradeNoticeText = wp.i18n.sprintf(
			copies[ copySelector ],
			'https://wordpress.com/plans/' + window.wcCalypsoBridge.siteSlug
		);

		const upgradeNotice = document.createElement( 'div' );
		upgradeNotice.className = 'wc-calypso-notice';
		upgradeNotice.innerHTML = upgradeNoticeText;

		return upgradeNotice;
	};

	const customizeConnectPage = () => {
		waitForElm( '.connect-account' ).then( function ( element ) {
			element.prepend( getNotice( 'default' ) );
			const h2s = element.querySelectorAll( 'h2' );
			if ( h2s.lengths === 2 ) {
				h2s[ 1 ].innerText = __(
					'You’re only steps away from getting ready to be paid',
					'wc-calypso-bridge'
				);
			}

			const stepItems = element.querySelectorAll(
				'.connect-page-onboarding-steps-item'
			);

			if ( stepItems.length === 3 ) {
				const { 2: setupCompleteStepElement } = stepItems;
				const p = setupCompleteStepElement.querySelector( 'p' );
				if ( p ) {
					p.innerText = __(
						'You’re ready to start testing the features and benefits of WooCommerce Payments',
						'wc-calypso-bridge'
					);
				}
			}
		} );
	};

	const addNotice = ( selector, notice ) => {
		waitForElm( selector ).then( function ( element ) {
			element.prepend( notice );
		} );
	};

	const detectPageAndRunCustomization = () => {
		switch ( new URLSearchParams( window.location.search ).get( 'path' ) ) {
			case '/payments/connect':
				customizeConnectPage();
				break;
			case '/payments/overview':
				addNotice( '.wcpay-overview', getNotice( 'default' ) );
				break;
			case '/payments/transactions':
				addNotice(
					'.woocommerce-payments-page',
					getNotice( 'transactions' )
				);
				break;
			case '/payments/deposits':
				addNotice(
					'.woocommerce-payments-page',
					getNotice( 'deposits' )
				);
				break;
		}
	};

	/**
	 * Detect when page changes via React Router and run one of the customizations again.
	 */
	let url = location.href;
	document.body.addEventListener(
		'click',
		() => {
			requestAnimationFrame( () => {
				if ( url !== location.href ) {
					url = location.href;
					detectPageAndRunCustomization();
				}
			} );
		},
		true
	);

	detectPageAndRunCustomization();
} )();
