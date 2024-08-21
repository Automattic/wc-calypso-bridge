<?php
/**
 * Contains the logic for removing installed extensions from the search results.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.5.6
 * @version 2.5.6
 */

 defined( 'ABSPATH' ) || exit;

 use Automattic\Jetpack\Connection\Client;
 use Automattic\Jetpack\Status\Visitor;

/**
 * WC Calypso Bridge Launch Your Store
 */
class WC_Calypso_Bridge_Launch_Your_Store {

	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			return;
		}

    if (isset($_REQUEST['lol'])){
      // var_dump(get_option( 'launch-status' ), get_option( 'wpcom_public_coming_soon' ), get_option('blog_public') );

      $ip      = ( new Visitor() )->get_ip( true );
      $headers = array(
        'X-Forwarded-For' => $ip,
      );
      $wpcom_blog_id = Jetpack_Options::get_option( 'id' );
			$endpoint = sprintf( '/sites/%d/settings', $wpcom_blog_id );
			$wpcom_request = Client::wpcom_json_api_request_as_blog(
				$endpoint,
				'1.1',
				array(
					'method'  => 'POST',
					'headers' => $headers,
        ),
        array(
          'blog_public' => '1',
        ),
			);

      var_dump('KIKIKIK',$wpcom_request);
      die();

    }

    add_action( 'update_option_woocommerce_coming_soon' , array( $this, 'update_atomic_coming_soon_state' ), 10, 2 );
		// $this->init();
	}

  public function update_atomic_coming_soon_state( $old_value, $new_value ) {
    // The option is `launched` once the store has launched the first time. Will not change after that.
    $is_atomic_launched = ( 'launched' === get_option( 'launch-status' ) );
    // delete_option( 'launch-status' );
    // var_dump("Hmmmsdsds");
    // $atomic_launch_status = get_option( 'launch-status' );
    if ( ! $is_atomic_launched ) {
      if ( 'yes' === $new_value ) {
        $this->send_launch_atomic();
      }
    } else {
      $this->update_atomic_settings( true );
      // update_option( 'atomic_coming_soon_state', 'no' );
    }
  }

  public function update_atomic_settings( $coming_soon = false ) {
    if ( ! class_exists( '\Jetpack_Options' ) ) {
      return;
    }

		$blog_id  = \Jetpack_Options::get_option( 'id' );
		$response = Client::wpcom_json_api_request_as_blog(
			sprintf( '/sites/%d/settings', $blog_id ),
			'1.4',
			array( 'method' => 'POST' ),
			json_encode( array(
				'blog_public' => $coming_soon ? '1' : '0'
      ) ),
			'rest'
		);

    var_dump('Update!!', $response);
    die();
  }

  public function send_launch_atomic() {
    if ( ! class_exists( '\Jetpack_Options' ) ) {
      return;
    }

		$blog_id  = \Jetpack_Options::get_option( 'id' );
		$response = Client::wpcom_json_api_request_as_user(
			sprintf( '/sites/%d/launch', $blog_id ),
			'2',
			[ 'method' => 'POST' ],
			json_encode( [
				'site' => $blog_id
			] ),
			'wpcom'
		);

    var_dump($response);
    die();

		// Handle error.
		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			// $body  = wp_remote_retrieve_body( $response );
			// $error = json_decode( $body, true );
			// wp_send_json_error( new WP_Error( $error[ 'code' ], $error[ 'message' ] ), 400 );
		}

  }

}

WC_Calypso_Bridge_Launch_Your_Store::get_instance();
