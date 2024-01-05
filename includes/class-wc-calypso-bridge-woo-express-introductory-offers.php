<?php
/**
 * Contains the logic for querying plans with introductory offers.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.3.1
 * @version 2.3.1
 */

use Automattic\Jetpack\Connection\Client;
use Automattic\Jetpack\Connection\Manager;

/**
 * WC_Calypso_Bridge_Woo_Express_Introductory_offers
 */
class WC_Calypso_Bridge_Woo_Express_Introductory_offers {

	const WOO_EXPRESS_PRODUCT_SLUGS = array(
		'wooexpress-small-bundle-yearly',
		'wooexpress-small-bundle-monthly',
		'wooexpress-medium-bundle-yearly',
		'wooexpress-medium-bundle-monthly'
	);

	CONST TRANSIENT_PREFIX = 'wc-calypso-bridge-introductory-plans-';

	/**
	 * Return introductory offers for the current blog.
	 *
	 * @return array|mixed
	 */
	public static function get_offers_for_current_blog(callable $filter = null) {
		$offers = static::get_offers_by_blog_id( Jetpack_Options::get_option( 'id' ) );
		if ( $filter ) {
			return array_filter( $offers, $filter );
		}

		return $offers;
	}

	/**
	 * Format offer data for JS.
	 *
	 * @param $offer
	 *
	 * @return array
	 */
	public static function extract_offer_data_for_js( $offer ) {
		return array(
			'rawPrice' => $offer['raw_price'],
			'formattedPrice' => $offer['formatted_price'],
			'intervalUnit' => $offer['interval_unit'],
			'intervalCount' => $offer['interval_count'],
		);
	}

	/**
	 * Return introductory offers by blog I.D
	 *
	 * @param $blog_id
	 *
	 * @return array|mixed
	 */
	public static function get_offers_by_blog_id( $blog_id ) {
		$manager = new Manager();
		$current_user_is_connection_owner = $manager->get_connection_owner_id() === get_current_user_id();

		if ( ! $manager->is_user_connected() || ! $current_user_is_connection_owner ) {
			return [];
		}

		$cached_offers = get_transient( static::TRANSIENT_PREFIX . $blog_id );
		if ( $cached_offers ) {
			return $cached_offers;
		}

		$headers = array();
		if ( class_exists( '\Automattic\Jetpack\Status\Visitor' ) ) {
			$headers['X-Forwarded-For'] = ( new \Automattic\Jetpack\Status\Visitor() )->get_ip( true );
		}

		$response = Client::wpcom_json_api_request_as_user(
			'/introductory-offers?site=' . $blog_id,
			'2',
			array(
				'method'  => 'GET',
				'headers' => $headers,
			),
		);

		$offers = [];

		if ( ! is_wp_error( $response ) && isset( $response[ 'http_response' ] ) && $response[ 'http_response' ] instanceof WP_HTTP_Requests_Response && 200 === $response[ 'http_response' ]->get_status() ) {
			$data = json_decode( $response['http_response']->get_data(), true );
			if ( is_array( $data ) && count( $data ) ) {
				$offers = $data;
			}
		}

		set_transient( static::TRANSIENT_PREFIX . $blog_id, $offers, 120 );

		return $offers;
	}
}
