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
	}, [] );

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
					'Add extra functionality and features to your website at a click of a button. Upgrade to a paid plan and gain access to thousands of free and paid WordPress plugins. Curious to find out what’s possible?',
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
						'assets/images/plugins-landing-page-hero-desktop.png'
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
						'assets/images/plugins-landing-page-hero-desktop.png'
					}
					alt={ __(
						'Take your store to the next level, with extensions',
						'wc-calypso-bridge'
					) }
				/>
			</div>

			<div className="wc-bridge-landing-page__features-grid wc-bridge-landing-page__features-grid--align-left">
				<div className="wc-bridge-landing-page__features-grid__item">
					<div className="wc-bridge-landing-page__features-grid__item__icon">
						<svg
							width="29"
							height="24"
							viewBox="0 0 29 24"
							fill="none"
							xmlns="http://www.w3.org/2000/svg"
						>
							<path
								fill="var(--wp-admin-theme-color)"
								d="M9.0625 1.71429V5.14286H27.1875V3.42857C27.1875 2.48036 26.3775 1.71429 25.375 1.71429H9.0625ZM7.25 1.71429H3.625C2.62246 1.71429 1.8125 2.48036 1.8125 3.42857V5.14286H7.25V1.71429ZM1.8125 6.85714V20.5714C1.8125 21.5196 2.62246 22.2857 3.625 22.2857H25.375C26.3775 22.2857 27.1875 21.5196 27.1875 20.5714V6.85714H1.8125ZM0 3.42857C0 1.5375 1.62559 0 3.625 0H25.375C27.3744 0 29 1.5375 29 3.42857V20.5714C29 22.4625 27.3744 24 25.375 24H3.625C1.62559 24 0 22.4625 0 20.5714V3.42857Z"
							/>
						</svg>
					</div>
					<h3>
						{__( 'Customize your website', 'wc-calypso-bridge' ) }
					</h3>
					<p>
						{ __(
							'From SEO tools to multi-language functionality, with WordPress plugins, you can customize your website to do anything you need.',
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
							width="29"
							height="29"
							viewBox="0 0 29 29"
							fill="none"
							xmlns="http://www.w3.org/2000/svg"
							style={{
								position: 'relative',
								top: '-1px',
								left: '3px',
							}}
						>
							<path
								fill="var(--wp-admin-theme-color)"
								d="M14.5 5.98125L14.1375 5.14863L13.5938 5.38652V5.98125H14.5ZM14.5 8.15625H13.5938V9.0625H14.5V8.15625ZM20.8438 8.15625H21.75V7.25H20.8438V8.15625ZM20.8438 14.5H19.9375V15.4062H20.8438V14.5ZM23.0187 14.5V15.4062H23.6135L23.8514 14.8625L23.0187 14.5ZM23.0187 18.125L23.8514 17.7625L23.6135 17.2188H23.0187V18.125ZM20.8438 18.125V17.2188H19.9375V18.125H20.8438ZM20.8438 28.0938V29H21.75V28.0938H20.8438ZM14.5 28.0938H13.5938V29H14.5V28.0938ZM14.5 25.9188L14.1375 25.0861L13.5938 25.324V25.9188H14.5ZM10.875 25.9188H11.7812V25.324L11.2375 25.0861L10.875 25.9188ZM10.875 28.0938V29H11.7812V28.0938H10.875ZM0.90625 28.0938H0V29H0.90625V28.0938ZM0.90625 18.125V17.2188H0V18.125H0.90625ZM3.08125 18.125L3.91387 17.7625L3.67598 17.2188H3.08125V18.125ZM3.08125 14.5V15.4062H3.67598L3.91387 14.8625L3.08125 14.5ZM0.90625 14.5H0V15.4062H0.90625V14.5ZM0.90625 8.15625V7.25H0V8.15625H0.90625ZM10.875 8.15625V9.0625H11.7812V8.15625H10.875ZM10.875 5.98125H11.7812V5.38652L11.2375 5.14863L10.875 5.98125ZM17.2188 3.625C17.2188 2.52617 16.624 1.59727 15.7971 0.979883C14.9701 0.3625 13.8656 0 12.6875 0V1.8125C13.5088 1.8125 14.2225 2.06172 14.7096 2.42988C15.1967 2.79805 15.4062 3.22285 15.4062 3.625H17.2188ZM14.8625 6.81387C16.1539 6.24746 17.2188 5.10898 17.2188 3.625H15.4062C15.4062 4.15742 15.0098 4.76914 14.1375 5.14863L14.8568 6.81387H14.8625ZM15.4062 8.15625V5.98125H13.5938V8.15625H15.4062ZM20.8438 7.25H14.5V9.0625H20.8438V7.25ZM21.75 14.5V8.15625H19.9375V14.5H21.75ZM23.0187 13.5938H20.8438V15.4062H23.0187V13.5938ZM25.375 11.7812C23.891 11.7812 22.7525 12.8461 22.1861 14.1375L23.8514 14.8568C24.2309 13.9902 24.8426 13.5938 25.375 13.5938V11.7812ZM29 16.3125C29 15.1344 28.6432 14.0355 28.0201 13.2029C27.3971 12.3703 26.4738 11.7812 25.375 11.7812V13.5938C25.7771 13.5938 26.2076 13.809 26.5701 14.2904C26.9326 14.7719 27.1875 15.4855 27.1875 16.3125H29ZM25.375 20.8438C26.4738 20.8438 27.4027 20.249 28.0201 19.4221C28.6375 18.5951 29 17.4906 29 16.3125H27.1875C27.1875 17.1338 26.9383 17.8475 26.5701 18.3346C26.202 18.8217 25.7715 19.0312 25.375 19.0312V20.8438ZM22.1861 18.4875C22.7469 19.7846 23.891 20.8438 25.375 20.8438V19.0312C24.8426 19.0312 24.2309 18.6348 23.8514 17.7625L22.1861 18.4818V18.4875ZM20.8438 19.0312H23.0187V17.2188H20.8438V19.0312ZM21.75 28.0938V18.125H19.9375V28.0938H21.75ZM14.5 29H20.8438V27.1875H14.5V29ZM13.5938 25.9188V28.0938H15.4062V25.9188H13.5938ZM15.4062 23.5625C15.4062 24.0949 15.0098 24.7066 14.1375 25.0861L14.8568 26.7514C16.1539 26.1906 17.2131 25.0465 17.2131 23.5625H15.4062ZM12.6875 21.75C13.5088 21.75 14.2225 21.9992 14.7096 22.3674C15.1967 22.7355 15.4062 23.166 15.4062 23.5625H17.2188C17.2188 22.4637 16.624 21.5348 15.7971 20.9174C14.9701 20.3 13.8656 19.9375 12.6875 19.9375V21.75ZM9.96875 23.5625C9.96875 23.1604 10.184 22.7299 10.6654 22.3674C11.1469 22.0049 11.8605 21.75 12.6875 21.75V19.9375C11.5094 19.9375 10.4105 20.2943 9.57793 20.9174C8.74531 21.5404 8.15625 22.4637 8.15625 23.5625H9.96875ZM11.2375 25.0861C10.3652 24.7066 9.96875 24.0949 9.96875 23.5625H8.15625C8.15625 25.0465 9.22109 26.185 10.5125 26.7514L11.2318 25.0861H11.2375ZM11.7812 28.0938V25.9188H9.96875V28.0938H11.7812ZM0.90625 29H10.875V27.1875H0.90625V29ZM0 18.125V28.0938H1.8125V18.125H0ZM3.08125 17.2188H0.90625V19.0312H3.08125V17.2188ZM5.4375 19.0312C4.90508 19.0312 4.29336 18.6348 3.91387 17.7625L2.24863 18.4875C2.81504 19.7789 3.95352 20.8438 5.4375 20.8438V19.0312ZM7.25 16.3125C7.25 17.1338 7.00078 17.8475 6.63262 18.3346C6.26445 18.8217 5.83965 19.0312 5.4375 19.0312V20.8438C6.53633 20.8438 7.46523 20.249 8.08262 19.4221C8.7 18.5951 9.0625 17.4906 9.0625 16.3125H7.25ZM5.4375 13.5938C5.83965 13.5938 6.27012 13.809 6.63262 14.2904C6.99512 14.7719 7.25 15.4855 7.25 16.3125H9.0625C9.0625 15.1344 8.70566 14.0355 8.08262 13.2029C7.45957 12.3703 6.53633 11.7812 5.4375 11.7812V13.5938ZM3.91387 14.8625C4.29336 13.9902 4.90508 13.5938 5.4375 13.5938V11.7812C3.95352 11.7812 2.81504 12.8461 2.24863 14.1375L3.91387 14.8568V14.8625ZM0.90625 15.4062H3.08125V13.5938H0.90625V15.4062ZM0 8.15625V14.5H1.8125V8.15625H0ZM10.875 7.25H0.90625V9.0625H10.875V7.25ZM9.96875 5.98125V8.15625H11.7812V5.98125H9.96875ZM8.15625 3.625C8.15625 5.10898 9.22109 6.24746 10.5125 6.81387L11.2318 5.14863C10.3652 4.76914 9.96875 4.15742 9.96875 3.625H8.15625ZM12.6875 0C11.5094 0 10.4105 0.356836 9.57793 0.979883C8.74531 1.60293 8.15625 2.52617 8.15625 3.625H9.96875C9.96875 3.22285 10.184 2.79238 10.6654 2.42988C11.1469 2.06738 11.8662 1.8125 12.6875 1.8125V0Z"
							/>
						</svg>
					</div>
					<h3>{ __( 'Extend your store', 'wc-calypso-bridge' ) }</h3>
					<p>
						{ __(
							'Looking to expand on the ecommerce functionality of your store? The Woo Marketplace has hundreds of trusted extensions designed specifically for your store.',
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
							width="25"
							height="25"
							viewBox="0 0 25 25"
							fill="none"
							xmlns="http://www.w3.org/2000/svg"
						>
							<path
								fill="var(--wp-admin-theme-color)"
								d="M3.57143 1.78571C2.58371 1.78571 1.78571 2.58371 1.78571 3.57143V21.4286C1.78571 22.4163 2.58371 23.2143 3.57143 23.2143H21.4286C22.4163 23.2143 23.2143 22.4163 23.2143 21.4286V3.57143C23.2143 2.58371 22.4163 1.78571 21.4286 1.78571H3.57143ZM0 3.57143C0 1.60156 1.60156 0 3.57143 0H21.4286C23.3984 0 25 1.60156 25 3.57143V21.4286C25 23.3984 23.3984 25 21.4286 25H3.57143C1.60156 25 0 23.3984 0 21.4286V3.57143ZM13.298 4.51451L15.3181 8.61049L19.8438 9.26897C20.1786 9.3192 20.4576 9.55357 20.5636 9.87723C20.6696 10.2009 20.5804 10.558 20.3404 10.7924L17.0703 13.9844L17.8404 18.4877C17.8962 18.8225 17.7623 19.1629 17.4833 19.3583C17.2042 19.5536 16.8415 19.5871 16.5458 19.4252L12.5 17.3047L8.45424 19.4308C8.1529 19.5871 7.79018 19.5647 7.51674 19.3638C7.2433 19.1629 7.10379 18.8281 7.1596 18.4933L7.92969 13.99L4.6596 10.7924C4.41406 10.5525 4.33036 10.2009 4.43638 9.87723C4.54241 9.55357 4.82143 9.3192 5.15625 9.26897L9.67634 8.61049L11.6964 4.51451C11.8471 4.20759 12.1596 4.01786 12.4944 4.01786C12.8292 4.01786 13.1473 4.21317 13.2924 4.51451H13.298ZM11.0658 9.82143C10.9375 10.0837 10.6864 10.2679 10.3962 10.3125L7.19866 10.7757L9.51451 13.0301C9.72656 13.2366 9.82143 13.5324 9.77121 13.8225L9.22433 17.0089L12.0815 15.5078C12.3438 15.3683 12.6507 15.3683 12.9129 15.5078L15.7701 17.0089L15.2232 13.8225C15.173 13.5324 15.2679 13.2366 15.4799 13.0301L17.8013 10.7757L14.6038 10.3125C14.3136 10.2679 14.0625 10.0893 13.9342 9.82143L12.5 6.9308L11.0714 9.82701L11.0658 9.82143Z"
							/>
						</svg>
					</div>
					<h3>{ __( 'Do it your way', 'wc-calypso-bridge' ) }</h3>
					<p>
						{ __(
							'Can’t find what you’re looking for? Hire one of our expert agencies! From new extensions to full-store builds, our Woo Expertsthey can help you create the store of your dreams.',
							'wc-calypso-bridge'
						) }
					</p>
					<a
						href="https://woocommerce.com/customizations?utm_source=wooextensionstab&utm_medium=product&utm_campaign=woocommerceplugin"
						target="_blank"
						id="hire_expert_button"
						rel="noreferrer"
						onClick={ () => {
							recordEvent( 'free_trial_hire_an_expert', {
								source: 'plugins',
							} );
						} }
					>
						{ __( 'Hire an expert', 'wc-calypso-bridge' ) }
					</a>
				</div>
			</div>
		</div>
	);
};
export default PluginsPage;
