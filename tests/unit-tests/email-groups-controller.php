<?php
/**
 * Tests for Email Groups Endpoint accounts in the REST API.
 */

class Email_Groups_controller extends WC_REST_Unit_Test_Case {

	/**
	 * Setup our test server, endpoints, and user info.
	 */
	public function setUp() {
		parent::setUp();
		$this->endpoint = new WC_Calypso_Bridge_Settings_Email_Groups_Controller();
		$this->user = $this->factory->user->create( array(
			'role' => 'administrator',
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

		var_export( $response_data );

		// Create settings array to test against.
		$settings = array( 
			0 => array( 
				'id' => 'woocommerce_email_from_name',
				'value' => 'Test Blog',
				'group_id' => 'email',
			),
			1 => array( 
				'id' => 'woocommerce_email_from_address',
				'value' => 'admin@example.org',
				'group_id' => 'email',
			),  
			2 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_new_order',
			),
			3 => array(
				'id' => 'recipient',
				'value' => '',
				'group_id' => 'email_new_order',
			),
			4 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_cancelled_order',
			),
			5 => array(
				'id' => 'recipient',
				'value' => '',
				'group_id' => 'email_cancelled_order',
			),
			6 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_failed_order',
			),
			7 => array(
				'id' => 'recipient',
				'value' => '',
				'group_id' => 'email_failed_order',
			),
			8 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_customer_on_hold_order',
			),
			9 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_customer_processing_order',
			),
			10 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_customer_completed_order',
			),
			11 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_customer_refunded_order',
			),
			12 => array(
				'id' => 'enabled',
				'value' => 'yes',
				'group_id' => 'email_customer_new_account',
			),
		);

		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( $settings, $response_data );
	}

}
