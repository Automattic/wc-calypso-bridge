/**
 * External dependencies
 */
import { recordEvent } from '@woocommerce/tracks';
import { __ } from '@wordpress/i18n';
import { useEffect } from 'react';

const PluginsPage = () => {
	useEffect( () => {
		document.body.classList.add(
			'woocommerce_page_wc-bridge-landing-page',
			'woocommerce_page_wc-plugins-landing-page'
		);
		return () => {
			document.body.classList.remove(
				'woocommerce_page_wc-bridge-landing-page',
				'woocommerce_page_wc-plugins-landing-page'
			);
		};
	} );

	return (
		<div className="woocommerce woocommerce-page wc-bridge-landing-page">
			<h1 className="wc-bridge-landing-page__title">
				{ __(
					'Make anything happen, with plugins',
					'wc-calypso-bridge'
				) }
			</h1>

			<p className="wc-bridge-landing-page__description">
				{ __(
					'Turn your WordPress site into anything you set your mind on. Upgrade to a paid plan and gain access to thousands of free and paid plugins. Curious to find out what’s possible?',
					'wc-calypso-bridge'
				) }
			</p>

			<div className="wc-bridge-landing-page__button-container">
				<a
					href={
						'https://wordpress.com/plans/' +
						window.wcCalypsoBridge.siteSlug
					}
					className="button button-primary"
					id="upgrade_now_button"
					onClick={ () => {
						recordEvent( 'free_trial_upgrade_now', {
							source: 'plugins',
						} );
					} }
				>
					{ __( 'Upgrade now', 'wc-calypso-bridge' ) }
				</a>
				<a
					href="https://wordpress.org/plugins/"
					target="_blank"
					className="button button-secondary"
					id="browse_plugins_button"
					rel="noreferrer"
					onClick={ () => {
						recordEvent( 'free_trial_browse_plugins', {
							source: 'plugins',
						} );
					} }
				>
					{ __( 'Browse plugins', 'wc-calypso-bridge' ) }
				</a>
			</div>

			<div className="wc-bridge-landing-page__hero-image-container">
				<img
					src={
						window.wcCalypsoBridge.assetPath +
						'assets/images/extensions-landing-page-hero-desktop.png'
					}
					alt={ __(
						'Take your store to the next level, with extensions',
						'wc-calypso-bridge'
					) }
				/>
			</div>
			<div className="wc-bridge-landing-page__hero-image-container--mobile">
				<img
					src={
						window.wcCalypsoBridge.assetPath +
						'assets/images/extensions-landing-page-hero-mobile.png'
					}
					alt={ __(
						'Take your store to the next level, with extensions',
						'wc-calypso-bridge'
					) }
				/>
			</div>

			<div className="wc-bridge-landing-page__features-grid">
				<div className="wc-bridge-landing-page__features-grid__item">
					<div className="wc-bridge-landing-page__features-grid__item__icon">
						<svg
							width="32"
							height="32"
							viewBox="0 0 32 32"
							fill="none"
							xmlns="http://www.w3.org/2000/svg"
						>
							<path
								d="M20.0628 15.9842C20.0628 13.7309 18.2359 11.904 15.9826 11.904C13.7292 11.904 11.9023 13.7309 11.9023 15.9842C11.9023 18.2376 13.7292 20.0645 15.9826 20.0645C18.2359 20.0645 20.0628 18.2376 20.0628 15.9842Z"
								fill="var( --color-accent-light )"
							/>
							<path
								d="M32 17.2307H28.8978C28.3246 23.4014 23.4014 28.3245 17.2308 28.8978V32H14.7693V28.8978C8.59855 28.3246 3.67546 23.4014 3.10222 17.2307H0V14.7692H3.10222C3.67543 8.59851 8.59861 3.67542 14.7693 3.10218V-4.57764e-05H17.2308V3.10218C23.4015 3.67538 28.3246 8.59857 28.8978 14.7692H32V17.2307ZM17.2308 5.56366V8.56473H14.7693V5.56366C9.98107 6.13687 6.13701 9.9473 5.56377 14.7692H8.56483V17.2307H5.56377C6.13698 22.0189 9.94741 25.8629 14.7693 26.4362V23.4351H17.2308V26.4362C22.019 25.863 25.863 22.0525 26.4363 17.2307H23.4352V14.7692H26.4363C25.8294 9.94736 22.019 6.1369 17.2308 5.56366Z"
								fill="var( --color-accent )"
							/>
						</svg>
					</div>
					<h3>
						{ __( 'Transform WordPress', 'wc-calypso-bridge' ) }
					</h3>
					<p>
						{ __(
							'Make WordPress do anything. Create a store, host a podcast, or showcase your work. You are in control with over 55,000 plugins.',
							'wc-calypso-bridge'
						) }
					</p>
					<a
						href="https://wordpress.org/plugins/"
						target="_blank"
						id="browse_plugins_button_2"
						rel="noreferrer"
						onClick={ () => {
							recordEvent( 'free_trial_browse_plugins', {
								source: 'plugins',
							} );
						} }
					>
						{ __( 'Browse plugins', 'wc-calypso-bridge' ) }
					</a>
				</div>

				<div className="wc-bridge-landing-page__features-grid__item">
					<div className="wc-bridge-landing-page__features-grid__item__icon">
						<svg
							xmlns="http://www.w3.org/2000/svg"
							width={ 29 }
							height={ 29 }
							fill="none"
							style={ {
								position: 'relative',
								top: '-2px',
								right: '-2px',
							} }
						>
							<path
								fill="var( --color-accent )"
								fillRule="evenodd"
								d="M14.892 5.36H3.158A2.947 2.947 0 0 0 .211 8.306V25.99a2.947 2.947 0 0 0 2.947 2.947h17.684a2.947 2.947 0 0 0 2.948-2.947V14.259h-2.21v11.732c0 .407-.33.737-.737.737H9.053V14.926l9.518.033.008-2.211-16.158-.056V8.307c0-.407.33-.737.737-.737h11.734V5.36Zm-12.47 9.543v11.088c0 .407.33.737.736.737h3.685v-11.81l-4.422-.015Z"
								clipRule="evenodd"
							/>
							<path
								fill="var( --color-accent-light )"
								d="m22.751.158 1.499 4.05 4.05 1.498-4.05 1.499-1.499 4.05-1.498-4.05-4.05-1.499 4.05-1.498L22.75.158Z"
							/>
						</svg>
					</div>
					<h3>{ __( 'Extend your store', 'wc-calypso-bridge' ) }</h3>
					<p>
						{ __(
							'Looking for more payment and shipping methods? Want to build a memberships site, or accept donations? Choose among hundreds of Extensions hand-picked by our team.',
							'wc-calypso-bridge'
						) }
					</p>
					<a
						href="https://woocommerce.com/product-category/woocommerce-extensions/?categoryIds=1021&collections=product&page=1&utm_source=wooextensionstab&utm_medium=product&utm_campaign=woocommerceplugin"
						target="_blank"
						id="discover_extensions_button"
						rel="noreferrer"
						onClick={ () => {
							recordEvent( 'free_trial_discover_extensions', {
								source: 'plugins',
							} );
						} }
					>
						{ __( 'Discover Extensions', 'wc-calypso-bridge' ) }
					</a>
				</div>

				<div className="wc-bridge-landing-page__features-grid__item">
					<div className="wc-bridge-landing-page__features-grid__item__icon">
						<svg
							xmlns="http://www.w3.org/2000/svg"
							width={ 32 }
							height={ 22 }
							fill="none"
						>
							<path
								fill="var( --color-accent )"
								fillRule="evenodd"
								d="M18.393 21.422v-3.828a5.263 5.263 0 0 0-5.263-5.263H5.474a5.263 5.263 0 0 0-5.263 5.263v3.828h2.87v-3.828a2.393 2.393 0 0 1 2.393-2.392h7.656a2.393 2.393 0 0 1 2.392 2.392v3.828h2.87ZM11.216 5.154a1.914 1.914 0 1 1-3.828 0 1.914 1.914 0 0 1 3.828 0Zm2.87 0a4.785 4.785 0 1 1-9.569 0 4.785 4.785 0 0 1 9.57 0Z"
								clipRule="evenodd"
							/>
							<path
								fill="var( --color-accent-light )"
								fillRule="evenodd"
								d="M22.699 9.939a4.785 4.785 0 1 0 0-9.57 4.785 4.785 0 0 0 0 9.57Zm9.09 7.655v3.828h-2.87v-3.828a2.393 2.393 0 0 0-2.393-2.392h-4.784V12.33h4.784a5.263 5.263 0 0 1 5.264 5.263ZM22.7 7.068a1.914 1.914 0 1 0 0-3.829 1.914 1.914 0 0 0 0 3.829Z"
								clipRule="evenodd"
							/>
						</svg>
					</div>
					<h3>{ __( 'Do it your way', 'wc-calypso-bridge' ) }</h3>
					<p>
						{ __(
							'Can’t find the right plugin for your needs? From service integrations to exotic customizations, our team of experts can help you get the job done right.',
							'wc-calypso-bridge'
						) }
					</p>
					<a
						href="https://woocommerce.com/customizations?utm_source=wooextensionstab&utm_medium=product&utm_campaign=woocommerceplugin"
						target="_blank"
						id="get_inspired_button"
						rel="noreferrer"
						onClick={ () => {
							recordEvent( 'free_trial_get_inspired', {
								source: 'plugins',
							} );
						} }
					>
						{ __( 'Get inspired', 'wc-calypso-bridge' ) }
					</a>
				</div>
			</div>
		</div>
	);
};
export default PluginsPage;
