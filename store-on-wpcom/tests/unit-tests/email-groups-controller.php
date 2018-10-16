<?php
/**
 * Tests for Email Groups Endpoint accounts in the REST API.
 */
class Email_Groups_Controller extends WC_REST_Unit_Test_Case {

	/**
	 * Setup our test server, endpoints, and user info.
	 */
	public function setUp() {
		parent::setUp();
		$this->endpoint = new WC_Calypso_Bridge_Settings_Email_Groups_Controller();
		$this->user = $this->factory->user->create( array(
			'role' => 'administrator',
		) );
		$this->subscriber = $this->factory->user->create( array(
			'role' => 'subscriber',
		) );
	}

	/**
	 * Fetch all settings and check that the format is valid.
	 *
	 * @since 3.0.0
	 */
	public function test_get_email_group_settings() {
		wp_set_current_user( $this->user );

		$response      = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v3/settings_email_groups' ) );
		$response_data = $response->get_data();

		// Create settings array to test against.
		$settings = array( 
			0 => array( 
				'id' => 'woocommerce_email_from_name',
				'value' => 'Test Blog',
				'group_id' => 'email',
				'default' => 'Test Blog',
			),
			1 => array( 
				'id' => 'woocommerce_email_from_address',
				'value' => 'admin@example.org',
				'group_id' => 'email',
				'default' => 'admin@example.org',
			),
			2 => array(
				'id' => 'woocommerce_email_footer_text',
				'value' => '{site_title}<br/>Powered by <a href="https://woocommerce.com/">WooCommerce</a>',
				'group_id' => 'email',
				'default' => '{site_title}<br/>Powered by <a href="https://woocommerce.com/">WooCommerce</a>',
			),
			3 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_new_order',
				'default' => 'yes'
			),
			4 => array(
				'id' => 'recipient',
				'value' => '',
				'group_id' => 'email_new_order',
				'default' => '',
			),
			5 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_cancelled_order',
				'default' => 'yes'
			),
			6 => array(
				'id' => 'recipient',
				'value' => '',
				'group_id' => 'email_cancelled_order',
				'default' => '',
			),
			7 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_failed_order',
				'default' => 'yes',
			),
			8 => array(
				'id' => 'recipient',
				'value' => '',
				'group_id' => 'email_failed_order',
				'default' => '',
			),
			9 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_customer_on_hold_order',
				'default' => 'yes',
			),
			10 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_customer_processing_order',
				'default' => 'yes',
			),
			11 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_customer_completed_order',
				'default' => 'yes',
			),
			12 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_customer_refunded_order',
				'default' => 'yes',
			),
			13 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_customer_new_account',
				'default' => 'yes',
			),
		);

		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( $settings, $response_data );
	}

	public function test_get_email_group_settings_invalid_credentials() {
		wp_set_current_user( $this->subscriber );

		$response      = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v3/settings_email_groups' ) );
		$response_data = $response->get_data();
		$this->assertEquals( 403, $response->get_status() );
	}

}