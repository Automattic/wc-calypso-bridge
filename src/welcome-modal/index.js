/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Guide } from '@wordpress/components';
import { createInterpolateElement, useState, useEffect } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import { withDispatch, withSelect } from '@wordpress/data';
import { OPTIONS_STORE_NAME } from '@woocommerce/data';
import { recordEvent } from '@woocommerce/tracks';

/**
 * Internal dependencies
 */
import escape from '../utils/escape';
import './style.scss';

const WelcomeModal = ( { isDismissed, isResolving, updateOptions } ) => {
	const [ isOpen, setIsOpen ] = useState( true );

	if ( isDismissed || isResolving ) {
		return null;
	}

	if ( ! isOpen ) {
		return null;
	}

	const closeHandler = () => {
		setIsOpen( false );
		updateOptions( {
			woocommerce_ecommerce_welcome_modal_dismissed: 'yes',
		} );
		recordEvent( 'ecommerce_welcome_modal_close' );
	};

	return <Modal closeHandler={closeHandler} />
};

const Modal = ({ closeHandler }) => {

	useEffect( () => {
		recordEvent( 'ecommerce_welcome_modal_open' );
	}, [] );

	const ASSET_URL = escape(
		window.wcCalypsoBridge.homeUrl +
			window.wcCalypsoBridge.assetPath +
			'assets/'
	);

	return (
		<Guide
			onFinish={ closeHandler }
			className={ 'ecommerce__welcome-modal' }
			finishButtonText={ __( "Get started", 'wc-calypso-bridge' ) }
			pages={ [
				{
					image: (
						<img
							src={
								ASSET_URL +
								'images/welcome-modal-illustration-2.png'
							}
						/>
					),
					content: (
						<div className="ecommerce__welcome-modal__page-content">
							<h2 className="ecommerce__welcome-modal__page-content__header">
								{ __( 'Meet your new Home', 'wc-calypso-bridge' ) }
							</h2>
							<p className="ecommerce__welcome-modal__page-content__body">
								{ __(
									'Get tips and insights on your store’s performance every time you jump back into your WordPress.com dashboard.',
									'wc-calypso-bridge'
								) }
							</p>
						</div>
					),
				},
				{
					image: (
						<img
							src={
								ASSET_URL +
								'images/welcome-modal-illustration-1.png'
							}
						/>
					),
					content: (
						<div className="ecommerce__welcome-modal__page-content">
							<h2 className="ecommerce__welcome-modal__page-content__header">
								{ __(
									'Move faster with our new navigation',
									'wc-calypso-bridge'
								) }
							</h2>
							<p className="ecommerce__welcome-modal__page-content__body">
								{ createInterpolateElement(
									__(
										'Getting things done with WooCommerce just got faster. <a>Learn more</a> about our new navigation — or go ahead and explore on your own.',
										'wc-calypso-bridge'
									),
									{
										a: (
											<a
												href={ 'https://wordpress.com/support/navigating-the-ecommerce-plan/' }
											/>
										),
									}
								) }
							</p>
						</div>
					),
				},
			] }
		/>
	);
}

export default compose(
	withSelect( ( select ) => {
		const { getOption, hasFinishedResolution } = select(
			OPTIONS_STORE_NAME
		);

		const MODAL_DISMISS_OPTION_NAME =
			'woocommerce_ecommerce_welcome_modal_dismissed';

		return {
			isDismissed: getOption( MODAL_DISMISS_OPTION_NAME ) === 'yes',
			isResolving:
				! hasFinishedResolution( 'getOption', [
					MODAL_DISMISS_OPTION_NAME,
				] ) ||
				typeof getOption( MODAL_DISMISS_OPTION_NAME ) === 'undefined',
		};
	} ),
	withDispatch( ( dispatch ) => {
		const { updateOptions } = dispatch( OPTIONS_STORE_NAME );

		return {
			updateOptions,
		};
	} )
)( WelcomeModal );
