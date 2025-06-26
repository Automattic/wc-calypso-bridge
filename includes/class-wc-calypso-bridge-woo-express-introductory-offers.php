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
	 * Return introductory offers for the current blog - for WOO_EXPRESS_PRODUCT_SLUGS only.
	 *
	 * @return array|mixed
	 */
	public static function get_offers_for_current_blog() {
		$slugs   = static::WOO_EXPRESS_PRODUCT_SLUGS;
		$blog_id = Jetpack_Options::get_option( 'id' );
		$offers  = static::get_offers_by_blog_id( $blog_id, $slugs );
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
	 * Return introductory offers by blog I.D, optionally limited to the given product slugs.
	 *
	 * @param $blog_id
	 * @param array<string> $product_slugs
	 *
	 * @return array|mixed
	 */
	public static function get_offers_by_blog_id( $blog_id, array $product_slugs = [] ) {
		$manager = new Manager();
		$current_user_is_connection_owner = $manager->get_connection_owner_id() === get_current_user_id();

		if ( ! $manager->is_user_connected() || ! $current_user_is_connection_owner ) {
			return [];
		}

		// Build a cache key for this specific set of slugs.
		$slug_segment   = $product_slugs ? ':' . md5( implode( ',', $product_slugs ) ) : '';
		$cache_key      = static::TRANSIENT_PREFIX . $blog_id . $slug_segment;
		$cached_offers  = get_transient( $cache_key );

		if ( $cached_offers ) {
			return $cached_offers;
		}

		// Build query string: site=123&product_slugs=foo,bar
		$slug_query = ! empty( $product_slugs )
		   ? '&product_slugs=' . rawurlencode( implode( ',', $product_slugs ) )
		   : '';

		$headers = array();
		if ( class_exists( '\Automattic\Jetpack\Status\Visitor' ) ) {
			$headers['X-Forwarded-For'] = ( new \Automattic\Jetpack\Status\Visitor() )->get_ip( true );
		}

		$response = Client::wpcom_json_api_request_as_user(
			'/introductory-offers?site=' . $blog_id . $slug_query,
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

		set_transient( $cache_key, $offers, 60 * 60 * 3 ); // 3 Hours

		return $offers;
	}
}
