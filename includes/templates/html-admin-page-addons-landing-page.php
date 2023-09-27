<?php
/**
* Admin View: Page - Addons
*
* @package WC_Calypso_Bridge/Templates
* @since   2.0.4
* @version 2.2.4
*
* @uses $upgrade_url
*/
?>
<div class="woocommerce woocommerce-page wc-bridge-landing-page">

	<h1 class="wc-bridge-landing-page__title">
		<?php esc_html_e( 'Take your store to the next level, with extensions', 'wc-calypso-bridge' ); ?>
	</h1>

	<p class="wc-bridge-landing-page__description">
		<?php esc_html_e( 'Customize your store to work perfectly for your business, with WooCommerce Extensions. Upgrade to a paid plan to gain access to hundreds of tools and features that can help you achieve your goals. Curious about what’s available? Browse our Extensions Marketplace.', 'wc-calypso-bridge' ); ?>
	</p>

	<div class="wc-bridge-landing-page__button-container">
		<a href="<?php echo esc_url( $upgrade_url ); ?>" class="button button-primary" id="upgrade_now_button">
			<?php esc_html_e( 'Upgrade now', 'wc-calypso-bridge' ); ?>
		</a>
		<a href="https://woocommerce.com/product-category/woocommerce-extensions/?categoryIds=1021&collections=product&page=1&utm_source=wooextensionstab&utm_medium=product&utm_campaign=woocommerceplugin" target="_blank" class="button button-secondary" id="browse_extension_button">
			<?php esc_html_e( 'Browse Extensions', 'wc-calypso-bridge' ); ?>
		</a>
	</div>

	<div class="wc-bridge-landing-page__hero-image-container">
		<img src="<?php echo WC_Calypso_Bridge_Instance()->get_asset_path() . 'assets/images/extensions-landing-page-hero-desktop.png' ?>" alt="<?php esc_html_e( 'Take your store to the next level, with extensions', 'wc-calypso-bridge' ); ?>">
	</div>
	<div class="wc-bridge-landing-page__hero-image-container--mobile">
		<img src="<?php echo WC_Calypso_Bridge_Instance()->get_asset_path() . 'assets/images/extensions-landing-page-hero-mobile.png' ?>" alt="<?php esc_html_e( 'Take your store to the next level, with extensions', 'wc-calypso-bridge' ); ?>">
	</div>

	<div class="wc-bridge-landing-page__features-grid">

		<div class="wc-bridge-landing-page__features-grid__item">
			<div class="wc-bridge-landing-page__features-grid__item__icon">
				<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M20.0628 15.9842C20.0628 13.7309 18.2359 11.904 15.9826 11.904C13.7292 11.904 11.9023 13.7309 11.9023 15.9842C11.9023 18.2376 13.7292 20.0645 15.9826 20.0645C18.2359 20.0645 20.0628 18.2376 20.0628 15.9842Z" fill="var( --color-accent-light )"/>
					<path d="M32 17.2307H28.8978C28.3246 23.4014 23.4014 28.3245 17.2308 28.8978V32H14.7693V28.8978C8.59855 28.3246 3.67546 23.4014 3.10222 17.2307H0V14.7692H3.10222C3.67543 8.59851 8.59861 3.67542 14.7693 3.10218V-4.57764e-05H17.2308V3.10218C23.4015 3.67538 28.3246 8.59857 28.8978 14.7692H32V17.2307ZM17.2308 5.56366V8.56473H14.7693V5.56366C9.98107 6.13687 6.13701 9.9473 5.56377 14.7692H8.56483V17.2307H5.56377C6.13698 22.0189 9.94741 25.8629 14.7693 26.4362V23.4351H17.2308V26.4362C22.019 25.863 25.863 22.0525 26.4363 17.2307H23.4352V14.7692H26.4363C25.8294 9.94736 22.019 6.1369 17.2308 5.56366Z" fill="var( --color-accent )"/>
				</svg>
			</div>
			<h3><?php esc_html_e( 'Customize and extend', 'wc-calypso-bridge' ); ?></h3>
			<p>
				<?php esc_html_e( 'Add extra features and functionality, or integrate with other platforms and tools.', 'wc-calypso-bridge' ); ?>
			</p>
			<a href="https://woocommerce.com/product-category/woocommerce-extensions/?categoryIds=1021&collections=product&page=1&utm_source=wooextensionstab&utm_medium=product&utm_campaign=woocommerceplugin" target="_blank" id="browse_extension_button_2"><?php esc_html_e( 'Browse extensions', 'wc-calypso-bridge' ); ?></a>
		</div>

		<div class="wc-bridge-landing-page__features-grid__item">
			<div class="wc-bridge-landing-page__features-grid__item__icon">
				<svg width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg" style="position:relative;top:-2px;right:-2px;">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M14.8923 5.35938H3.15831C1.53052 5.35938 0.210938 6.67896 0.210938 8.30674V25.991C0.210938 27.6187 1.53052 28.9383 3.15831 28.9383H20.8425C22.4703 28.9383 23.7899 27.6187 23.7899 25.991V14.259H21.5794V25.991C21.5794 26.3979 21.2495 26.7278 20.8425 26.7278H9.05304V14.9258L18.5714 14.9585L18.579 12.748L2.42146 12.6924V8.30674C2.42146 7.8998 2.75136 7.5699 3.15831 7.5699H14.8923V5.35938ZM2.42146 14.903V25.991C2.42146 26.3979 2.75136 26.7278 3.15831 26.7278H6.84252L6.84252 14.9182L2.42146 14.903Z" fill="var( --color-accent )"/>
					<path d="M22.7511 0.158203L24.2496 4.20773L28.2991 5.70619L24.2496 7.20465L22.7511 11.2542L21.2527 7.20465L17.2031 5.70619L21.2527 4.20773L22.7511 0.158203Z" fill="var( --color-accent-light )"/>
				</svg>
			</div>
			<h3><?php esc_html_e( 'Curated collections', 'wc-calypso-bridge' ); ?></h3>
			<p>
				<?php esc_html_e( 'Quickly get started with our curated extension collections.', 'wc-calypso-bridge' ); ?>
			</p>
			<a href="https://woocommerce.com/collections?utm_source=wooextensionstab&utm_medium=product&utm_campaign=woocommerceplugin" target="_blank" id="discover_collections_button"><?php esc_html_e( 'Discover collections', 'wc-calypso-bridge' ); ?></a>
		</div>

		<div class="wc-bridge-landing-page__features-grid__item">
			<div class="wc-bridge-landing-page__features-grid__item__icon">
				<svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M18.3928 21.4218V17.594C18.3928 16.1981 17.8382 14.8594 16.8512 13.8724C15.8642 12.8854 14.5255 12.3309 13.1296 12.3309H5.4741C4.07822 12.3309 2.73951 12.8854 1.75248 13.8724C0.765447 14.8594 0.210938 16.1981 0.210938 17.594V21.4218H3.08175V17.594C3.08175 16.2734 4.15352 15.2017 5.4741 15.2017H13.1296C14.4502 15.2017 15.5219 16.2734 15.5219 17.594V21.4218H18.3928ZM11.2157 5.15383C11.2157 5.66142 11.0141 6.14822 10.6552 6.50714C10.2962 6.86607 9.80944 7.06771 9.30185 7.06771C8.79426 7.06771 8.30745 6.86607 7.94853 6.50714C7.58961 6.14822 7.38797 5.66142 7.38797 5.15383C7.38797 4.64624 7.58961 4.15944 7.94853 3.80052C8.30745 3.44159 8.79426 3.23995 9.30185 3.23995C9.80944 3.23995 10.2962 3.44159 10.6552 3.80052C11.0141 4.15944 11.2157 4.64624 11.2157 5.15383ZM14.0865 5.15383C14.0865 6.42281 13.5824 7.63981 12.6851 8.53712C11.7878 9.43442 10.5708 9.93852 9.30185 9.93852C8.03287 9.93852 6.81586 9.43442 5.91856 8.53712C5.02126 7.63981 4.51716 6.42281 4.51716 5.15383C4.51716 3.88485 5.02126 2.66785 5.91856 1.77054C6.81586 0.87324 8.03287 0.369141 9.30185 0.369141C10.5708 0.369141 11.7878 0.87324 12.6851 1.77054C13.5824 2.66785 14.0865 3.88485 14.0865 5.15383Z" fill="var( --color-accent )"/>
					<path fill-rule="evenodd" clip-rule="evenodd" d="M22.6988 9.93852C23.9677 9.93852 25.1847 9.43442 26.082 8.53712C26.9793 7.63981 27.4834 6.42281 27.4834 5.15383C27.4834 3.88485 26.9793 2.66785 26.082 1.77054C25.1847 0.87324 23.9677 0.369141 22.6988 0.369141C21.4298 0.369141 20.2128 0.87324 19.3155 1.77054C18.4182 2.66785 17.9141 3.88485 17.9141 5.15383C17.9141 6.42281 18.4182 7.63981 19.3155 8.53712C20.2128 9.43442 21.4298 9.93852 22.6988 9.93852ZM31.7897 17.594V21.4218H28.9188V17.594C28.9188 16.2734 27.8471 15.2017 26.5265 15.2017H21.7418V12.3309H26.5265C27.9224 12.3309 29.2611 12.8854 30.2481 13.8724C31.2351 14.8594 31.7897 16.1981 31.7897 17.594ZM22.6988 7.06759C23.2063 7.06759 23.6931 6.86595 24.0521 6.50703C24.411 6.1481 24.6126 5.6613 24.6126 5.15371C24.6126 4.64612 24.411 4.15932 24.0521 3.8004C23.6931 3.44148 23.2063 3.23984 22.6988 3.23984C22.1912 3.23984 21.7044 3.44148 21.3454 3.8004C20.9865 4.15932 20.7849 4.64612 20.7849 5.15371C20.7849 5.6613 20.9865 6.1481 21.3454 6.50703C21.7044 6.86595 22.1912 7.06759 22.6988 7.06759Z" fill="var( --color-accent-light )"/>
				</svg>
			</div>
			<h3><?php esc_html_e( 'Get inspired', 'wc-calypso-bridge' ); ?></h3>
			<p>
				<?php esc_html_e( 'Tips, tricks, and ecommerce inspiration from our blog.', 'wc-calypso-bridge' ); ?>
			</p>
			<a href="https://woocommerce.com/blog?utm_source=wooextensionstab&utm_medium=product&utm_campaign=woocommerceplugin" target="_blank" id="get_inspired_button"><?php esc_html_e( 'Get inspired', 'wc-calypso-bridge' ); ?></a>
		</div>

	</div>

</div>

<script>
document.addEventListener( 'DOMContentLoaded', function() {
	// Prefer wc.tracks.recordEvent since it supports debugging.
	let recordEvent = null;
	if ( window.wc && window.wc.tracks && window.wc.tracks.recordEvent ) {
		recordEvent = window.wc.tracks.recordEvent;
	} else if ( window.wcTracks && window.wcTracks.recordEvent ) {
		recordEvent = window.wcTracks.recordEvent;
	} else {
		recordEvent = function() {};
	}

	const recordButtonEvent = function( buttonId, eventName ) {
		const el = document.getElementById( buttonId );
		if ( el ) {
			el.addEventListener( 'click', function() {
				recordEvent( eventName, {
					source: 'extensions',
				} );
			} );
		}
	};

	recordButtonEvent( 'upgrade_now_button', 'free_trial_upgrade_now' );
	recordButtonEvent( 'browse_extension_button', 'free_trial_browse_extensions' );
	recordButtonEvent( 'browse_extension_button_2', 'free_trial_browse_extensions' );
	recordButtonEvent( 'discover_collections_button', 'free_trial_discover_collections' );
	recordButtonEvent( 'get_inspired_button', 'free_trial_get_inspired' );
} );
</script>
