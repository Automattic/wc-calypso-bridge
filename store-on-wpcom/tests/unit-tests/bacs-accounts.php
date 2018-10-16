<?php
/**
 * Tests for BACS accounts in the REST API.
 */

class BACS_Accounts extends WC_REST_Unit_Test_Case {

	/**
	 * Setup our test server, endpoints, and user info.
	 */
	public function setUp() {
		parent::setUp();
		$this->endpoint = new WC_REST_Payment_Gateways_Controller();
		$this->user = $this->factory->user->create( array(
			'role' => 'administrator',
		) );
	}

	/**
	 * Test getting a single BACS item has account data
	 * which defaults to an empty array.
	 *
	 * @since 3.0.0
	 */
	public function test_get_bacs_payment_gateway() {
		wp_set_current_user( $this->user );

		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v3/payment_gateways/bacs' ) );
		$bacs   = $response->get_data();

		// Create settings array to test against.
		$settings = array_diff_key(
			$this->get_settings( 'WC_Gateway_BACS' ),
			array( 'enabled' => false, 'description' => false )
		);
		$settings[ 'accounts' ] = array(
			'id'    => 'accounts',
			'value' => array(),
		);

		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( array(
			'id'                 => 'bacs',
			'title'              => 'Direct bank transfer',
			'description'        => "Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.",
			'order'              => '',
			'enabled'            => false,
			'method_title'       => 'Direct bank transfer',
			'method_description' => 'Take payments in person via BACS. More commonly known as direct bank/wire transfer',
			'method_supports'    => array( 'products' ),
			'settings'           => $settings,
		), $bacs );
	}

	/**
	 * Test that non-BACS payment gateways do not get an
	 * accounts key added to settings.
	 */
	public function test_get_non_bacs_payment_gateway() {
		wp_set_current_user( $this->user );

		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v3/payment_gateways/paypal' ) );
		$paypal   = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );
		$this->assertFalse( array_key_exists( 'accounts', $paypal[ 'settings' ] ) );
	}

	/**
	 * Test that getting all payment gateways also includes
	 * account data on BACS
	 */
	public function test_get_all_payment_gateways_includes_bacs_accounts() {
		wp_set_current_user( $this->user );

		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v3/payment_gateways' ) );
		$gateways   = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );
		$filtered_gateways = wp_filter_object_list( $gateways, array( 'id' => 'bacs' ) );
		$bacs = array_pop( $filtered_gateways );
		$this->assertTrue( array_key_exists( 'accounts', $bacs[ 'settings' ] ) );
		$this->assertEquals( $bacs[ 'settings' ][ 'accounts' ], array(
			'id'    => 'accounts',
			'value' => array(),
		) );
	}

	/**
	 * Test actual account data is returned if set.
	 */
	public function test_get_all_payment_gateways_includes_actual_bacs_accounts() {
		wp_set_current_user( $this->user );
		$account_data = array(
			array(
				'account_name'   => 'chicken and ribs',
				'account_number' => '123yummy',
				'bank_name'      => 'tasty bbq',
				'sort_code'      => 'half rack',
				'iban'           => 'brisket',
				'bic'            => 'yes',
			)
		);
		update_option( 'woocommerce_bacs_accounts', $account_data );

		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v3/payment_gateways' ) );
		$gateways   = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );
		$filtered_gateways = wp_filter_object_list( $gateways, array( 'id' => 'bacs' ) );
		$bacs = array_pop( $filtered_gateways );
		$this->assertTrue( array_key_exists( 'accounts', $bacs[ 'settings' ] ) );
		$this->assertEquals( $bacs[ 'settings' ][ 'accounts' ], array(
			'id'    => 'accounts',
			'value' => $account_data,
		) );
	}

	/**
	 * Loads a particular gateway's settings so we can correctly test API output.
	 *
	 * @param string $gateway_class Name of WC_Payment_Gateway class.
	 */
	private function get_settings( $gateway_class ) {
		$gateway = new $gateway_class;
		$settings = array();
		$gateway->init_form_fields();
		foreach ( $gateway->form_fields as $id => $field ) {
			// Make sure we at least have a title and type
			if ( empty( $field['title'] ) || empty( $field['type'] ) ) {
				continue;
			}
			// Ignore 'title' settings/fields -- they are UI only
			if ( 'title' === $field['type'] ) {
				continue;
			}
			$data = array(
				'id'          => $id,
				'label'       => empty( $field['label'] ) ? $field['title'] : $field['label'],
				'description' => empty( $field['description'] ) ? '' : $field['description'],
				'type'        => $field['type'],
				'value'       => $gateway->settings[ $id ],
				'default'     => empty( $field['default'] ) ? '' : $field['default'],
				'tip'         => empty( $field['description'] ) ? '' : $field['description'],
				'placeholder' => empty( $field['placeholder'] ) ? '' : $field['placeholder'],
			);
			if ( ! empty( $field['options'] ) ) {
				$data['options'] = $field['options'];
			}
			$settings[ $id ] = $data;
		}
		return $settings;
	}

	/**
	 * Test updating bacs gateway with no account data supplied.
	 */
	public function test_update_bacs_payment_gateway_with_no_account_data() {
		wp_set_current_user( $this->user );

		$request = new WP_REST_Request( 'POST', '/wc/v3/payment_gateways/bacs' );
		$request->set_body_params( array(
			'settings' => array(
				'title' => 'Pay with bank transfer!',
			),
		) );
		$response = $this->server->dispatch( $request );
		$bacs = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( 'Pay with bank transfer!', $bacs['settings']['title']['value'] );
		$this->assertEquals( array(), $bacs['settings']['accounts']['value'] );
	}

	/**
	 * Test updating bacs gateway with account data supplied.
	 */
	public function test_update_bacs_payment_gateway_with_account_data() {
		wp_set_current_user( $this->user );

		$new_accounts = array(
			array(
				'account_name'   => 'scrooge mcduck',
				'account_number' => 'bling',
				'bank_name'      => 'disney bank',
				'sort_code'      => '111',
				'iban'           => '222',
				'bic'            => 'wat',
			),
			array(
				'sort_code'      => '111',
				'iban'           => '222',
				'bic'            => 'wat',
				'account_name'   => 'something else',
				'account_number' => '123134844584548',
				'bank_name'      => 'blaaaaahhhh',
			)
		);

		$accounts_request = new WP_REST_Request( 'POST', '/wc/v3/payment_gateways/bacs' );
		$accounts_request->set_body_params( array(
			'settings' => array(
				'accounts' => $new_accounts,
			),
		) );
		$accounts_response = $this->server->dispatch( $accounts_request );
		$bacs_accounts = $accounts_response->get_data();
		$this->assertEquals( 200, $accounts_response->get_status() );
		$this->assertEquals( $new_accounts, $bacs_accounts['settings']['accounts']['value'] );
	}

	/**
	 * Test updating bacs gateway with account, and non-account data supplied.
	 */
	public function test_update_bacs_payment_gateway_with_account_data_and_title() {
		wp_set_current_user( $this->user );

		$new_accounts = array(
			array(
				'account_name'   => 'scrooge mcduck',
				'account_number' => 'bling',
				'bank_name'      => 'disney bank',
				'sort_code'      => '111',
				'iban'           => '222',
				'bic'            => 'wat',
			)
		);

		$accounts_request = new WP_REST_Request( 'POST', '/wc/v3/payment_gateways/bacs' );
		$accounts_request->set_body_params( array(
			'settings' => array(
				'accounts' => $new_accounts,
				'title'    => 'Give me money',
			),
		) );
		$accounts_response = $this->server->dispatch( $accounts_request );
		$bacs_accounts = $accounts_response->get_data();
		$this->assertEquals( 200, $accounts_response->get_status() );
		$this->assertEquals( $new_accounts, $bacs_accounts['settings']['accounts']['value'] );
		$this->assertEquals( 'Give me money', $bacs_accounts['settings']['title']['value'] );
	}

	/**
	 * Test updating bacs gateway with account invalid data supplied.
	 */
	public function test_update_bacs_payment_gateway_with_malformed_account_data() {
		wp_set_current_user( $this->user );

		$new_accounts = array(
			array(
				'bad' => 'data',
				'omg' => 'jibberish',
			)
		);

		$request = new WP_REST_Request( 'POST', '/wc/v3/payment_gateways/bacs' );
		$request->set_body_params( array(
			'settings' => array(
				'accounts' => $new_accounts,
			),
		) );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 400, $response->get_status() );
	}

	/**
	 * Test updating bacs gateway with empty accounts.
	 */
	public function test_update_bacs_payment_gateway_with_empty_account_array() {
		wp_set_current_user( $this->user );

		$account_data = array(
			array(
				'account_name'   => 'chicken and ribs',
				'account_number' => '123yummy',
				'bank_name'      => 'tasty bbq',
				'sort_code'      => 'half rack',
				'iban'           => 'brisket',
				'bic'            => 'yes',
			)
		);
		update_option( 'woocommerce_bacs_accounts', $account_data );

		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v3/payment_gateways' ) );
		$gateways   = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );
		$filtered_gateways = wp_filter_object_list( $gateways, array( 'id' => 'bacs' ) );
		$bacs = array_pop( $filtered_gateways );
		$this->assertTrue( array_key_exists( 'accounts', $bacs[ 'settings' ] ) );
		$this->assertEquals( $bacs[ 'settings' ][ 'accounts' ], array(
			'id'    => 'accounts',
			'value' => $account_data,
		) );

		$empty_accounts = array( 
			array(
				'account_name'   => '',
				'account_number' => '',
				'bank_name'      => '',
				'sort_code'      => '',
				'iban'           => '',
				'bic'            => '',
			)
		);
		$request = new WP_REST_Request( 'POST', '/wc/v3/payment_gateways/bacs' );
		$request->set_body_params( array(
			'settings' => array(
				'accounts' => $empty_accounts,
			),
		) );
		$response = $this->server->dispatch( $request );
		$bacs   = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );
		$this->assertTrue( array_key_exists( 'accounts', $bacs[ 'settings' ] ) );
		$this->assertEquals( $bacs[ 'settings' ][ 'accounts' ], array(
			'id' => 'accounts',
			'value' => $empty_accounts,
		) );
	}

	/**
	 * Test updating bacs gateway with no account data, to effectively "delete"
	 * BACS accounts
	 */
	public function test_update_bacs_payment_gateway_with_empty_account_data_array() {
		wp_set_current_user( $this->user );

		$request = new WP_REST_Request( 'POST', '/wc/v3/payment_gateways/bacs' );
		$request->set_body_params( array(
			'settings' => array(
				'accounts' => array(),
			),
		) );
		$response = $this->server->dispatch( $request );
		$bacs   = $response->get_data();
		$this->assertEquals( 200, $response->get_status() );
		$this->assertTrue( array_key_exists( 'accounts', $bacs[ 'settings' ] ) );
		$this->assertEquals( $bacs[ 'settings' ][ 'accounts' ], array(
			'id' => 'accounts',
			'value' => array(),
		) );
	}
}
