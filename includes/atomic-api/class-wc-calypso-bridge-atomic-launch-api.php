<?php

use Automattic\Jetpack\Connection\Client;

/**
 * Class WC_Calypso_Bridge_Atomic_Launch_API.
 *
 * @since   2.6.0
 * @version 2.6.0
 *
 * API for launch on Atomic.
 */
class WC_Calypso_Bridge_Atomic_Launch_API {
	/**
	 * Launch Atomic.
	 */
	public static function launch_site() {
		if ( ! class_exists( '\Jetpack_Options' ) ) {
			return;
		}

		$blog_id = \Jetpack_Options::get_option( 'id' );
		return Client::wpcom_json_api_request_as_user(
			sprintf( '/sites/%d/launch', $blog_id ),
			'2',
			array(
				'method' => 'POST',
			),
			json_encode( array(
				'site' => $blog_id
			 ) ),
			'wpcom'
		);
	}

	/**
	 * Update coming soon.
	 */
	public static function update_coming_soon( $is_coming_soon ) {
		if ( ! class_exists( '\Jetpack_Options' ) ) {
			return;
		}

		$blog_id = \Jetpack_Options::get_option( 'id' );
		return Client::wpcom_json_api_request_as_user(
			sprintf( '/sites/%d/coming-soon', $blog_id ),
			'2',
			array(
				'method' => 'POST',
			),
			array(
				'is_coming_soon' => $is_coming_soon ? 1 : 0
			),
			'wpcom'
		);
	}
}
