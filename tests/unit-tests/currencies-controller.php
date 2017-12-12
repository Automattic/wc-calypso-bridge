<?php
/**
 * Tests for Currencies Controller in the REST API.
 */

class Currencies_Controller extends WC_REST_Unit_Test_Case {

	/**
	 * Setup our test server, endpoints, and user info.
	 */
	public function setUp() {
		parent::setUp();
		$this->endpoint = new WC_Calypso_Bridge_Currencies_Controller();
		$this->user = $this->factory->user->create( array(
			'role' => 'administrator',
		) );
	}

	/**
	 * Test getting currencies when authed.
	 *
	 * @since 3.0.0
	 */
	public function test_get_currencies() {
		wp_set_current_user( $this->user );

		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v3/currencies' ) );
		$data = $response->get_data();
        
        $currencies = array();
        foreach ( get_woocommerce_currencies() as $code => $name ) {
            $currencies[] = array(
                'code'   => $code,
                'name'   => $name,
                'symbol' => html_entity_decode( get_woocommerce_currency_symbol( $code ) ),
            );
        }

		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( $currencies, $data );
	}

	/**
	 * Test non authed requests fail
	 */
	public function test_unauthed_get_currencies() {
		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v3/currencies' ) );
		$this->assertEquals( 401, $response->get_status() );
	}
}
