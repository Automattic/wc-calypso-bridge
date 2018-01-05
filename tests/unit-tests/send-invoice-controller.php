<?php
/**
 * Tests for Sending Order Invoices via the REST API.
 */

class Send_Invoice_Controller extends WC_REST_Unit_Test_Case {
    /**
     * Array of note IDs to track.
     * @var array
     */
    protected $note_ids = array();

    /**
     * An order to hold these notes.
     * @var int
     */
    protected $order_id;

    /**
     * Setup our test server, endpoints, and user info.
     */
    public function setUp() {
        parent::setUp();
        $this->endpoint = new WC_Calypso_Bridge_Send_Invoice_Controller();
        $this->user = $this->factory->user->create( array(
            'role' => 'administrator',
        ) );

        $order = WC_Helper_Order::create_order();
        $this->order_id = $order->get_id();
    }

    /**
     * Cleanup.
     */
    public function stoppit_and_tidyup() {
        wp_delete_post( $this->order_id, true );
        foreach ( $this->note_ids as $note ) {
            wc_delete_order_note( $note );
        }
        $this->note_ids = array();
    }

    /**
     * Test sending an invoice on a valid order
     *
     */
    public function test_sending_invoice() {
        wp_set_current_user( $this->user );

        $response = $this->server->dispatch( new WP_REST_Request( 'POST', '/wc/v3/orders/' . $this->order_id . '/send_invoice' ) );
        $note = $response->get_data();
        $this->note_ids[] = $note['id'];

        $this->assertEquals( 200, $response->get_status() );
        $this->assertEquals( $note['note'], 'Order details manually sent to customer.' );
        $this->assertEquals( $note['customer_note'], false );
        $this->stoppit_and_tidyup();
    }

    /**
     * Test an invalid order ID results in a 404
     */
    public function test_invalid_order_id() {
        wp_set_current_user( $this->user );

        $response = $this->server->dispatch( new WP_REST_Request( 'POST', '/wc/v3/orders/1111111/send_invoice' ) );
        $this->assertEquals( 404, $response->get_status() );
        $this->stoppit_and_tidyup();
    }

    /**
     * Test unauthed requests can not send invoices
     */
    public function test_unauthed_request_fails() {
        $response = $this->server->dispatch( new WP_REST_Request( 'POST', '/wc/v3/orders/' . $this->order_id . '/send_invoice' ) );
        $this->assertEquals( 401, $response->get_status() );
        $this->stoppit_and_tidyup();
    }
}
