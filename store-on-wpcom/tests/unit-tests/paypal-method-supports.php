<?php
/**
 * Tests for PayPal method_supports in the REST API.
 */

class Paypal_Method_supports extends WC_REST_Unit_Test_Case {

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
     * Test getting PayPal payment method returns only `products` method_supports when no api credentials are set ( default state )
     */
    public function test_get_paypal_payment_gateway() {
        wp_set_current_user( $this->user );

        $response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v3/payment_gateways/paypal' ) );
        $paypal   = $response->get_data();

        // Create settings array to test against.
        $settings = array_diff_key(
            $this->get_settings( 'WC_Gateway_Paypal' ),
            array( 'enabled' => false, 'description' => false )
        );

        $this->assertEquals( 200, $response->get_status() );
        $this->assertEquals( array( 'products' ), $paypal['method_supports'] );
    }
    
    /**
     * Test setting PayPal payment gateway with all required api data, results in refunds being included in `method_supports`
     */
    public function test_update_paypal_payment_gateway_with_api_data_includes_supports_refund() {
        wp_set_current_user( $this->user );

        $request = new WP_REST_Request( 'POST', '/wc/v3/payment_gateways/paypal' );
        $request->set_body_params( array(
            'settings' => array(
                'api_username'  => 'champagne',
                'api_password'  => 'supernova',
                'api_signature' => 'intheksy',
            ),
        ) );
        $response = $this->server->dispatch( $request );
        $paypal = $response->get_data();
        $this->assertEquals( 200, $response->get_status() );
        $this->assertEquals( array( 'products', 'refunds' ), $paypal['method_supports'] );
    }
    
    /**
     * Test setting PayPal with one missing api data, returns only products in `method_supports`
     */
    public function test_update_paypal_payment_gateway_with_api_data_missing_password_omits_refund() {
        wp_set_current_user( $this->user );

        $request = new WP_REST_Request( 'POST', '/wc/v3/payment_gateways/paypal' );
        $request->set_body_params( array(
            'settings' => array(
                'api_username'  => 'champagne',
                'api_password'  => '',
                'api_signature' => 'intheksy',
            ),
        ) );
        $response = $this->server->dispatch( $request );
        $paypal = $response->get_data();
        $this->assertEquals( 200, $response->get_status() );
        $this->assertEquals( array( 'products' ), $paypal['method_supports'] );
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

}
