<?php

/**
 * Class WC_Calypso_Bridge_Free_Trial_Expired_Plan_Redirects.
 *
 * @since   2.0.12
 * @version 2.0.12
 *
 * Detects when we have an expired eCommerce trial plan, and redirects to Calypso for that case.
 */
class WC_Calypso_Bridge_Free_Trial_Expired_Plan_Redirects

{
	/**
	 * The single instance of the class.
	 *
	 * @var WC_Calypso_Bridge_Free_Trial_Expired_Plan_Redirects
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return WC_Calypso_Bridge_Free_Trial_Expired_Plan_Redirects Instance.
	 */
	final public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		if ( ! wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'maybe_redirect_wp_admin_to_expired_plan_page' ) );
	}

	/**
	 * When we are handling an incoming WP Admin UI request
	 * AND the site has an expired eCommerce trial,
	 * redirect to the expired trial page in Calypso.
	 *
	 * @return void
	 */
	public function maybe_redirect_wp_admin_to_expired_plan_page() {
		if ( ! wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		if ( defined( 'REST_API_REQUEST' ) && REST_API_REQUEST ) {
			return;
		}

		if ( ! function_exists( 'wpcom_get_site_purchases' ) || ! function_exists( 'wpcom_datetime_to_iso8601' ) ) {
			return;
		}

		$site_purchases = wpcom_get_site_purchases();

		$trial_plan_purchases = array_filter(
			$site_purchases,
			function ( $site_purchase ) {
				return 'ecommerce-trial-bundle-monthly' === $site_purchase->product_slug;
			}
		);

		// If we don't have a trial plan purchase, we either have no purchase,
		// or we have some other eCommerce plan.
		// We want to bail either way.
		if ( empty( $trial_plan_purchases ) ) {
			return;
		}

		$trial_plan_purchase = array_shift( $trial_plan_purchases );

		$current_timestamp = wpcom_datetime_to_iso8601( 'now' );

		if (
			$trial_plan_purchase
			&& ! empty( $trial_plan_purchase->expiry_date )
			&& $trial_plan_purchase->expiry_date < $current_timestamp
		) {
			$expired_trial_url = 'https://wordpress.com/plans/my-plan/trial-expired/' . WC_Calypso_Bridge_Instance()->get_site_slug();

			wp_redirect( $expired_trial_url );
			exit;
		}
	}
}

WC_Calypso_Bridge_Free_Trial_Expired_Plan_Redirects::get_instance();
