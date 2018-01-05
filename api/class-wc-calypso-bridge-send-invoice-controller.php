<?php
/**
 * REST API WC Calypso Bridge Send Invoice
 *
 * Adds an endpoint to trigger sending an order invoice email
 *
 * @author   Automattic
 * @category API
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package WooCommerce/API
 */
class WC_Calypso_Bridge_Send_Invoice_Controller extends WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'orders';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'shop_order';

	/**
	 * Register Currency route
	 */
	public function register_routes() {
		# POST wc/v3/orders/<id>/send_invoice
		register_rest_route( $this->namespace, $this->rest_base . '/(?P<id>[\d]+)/send_invoice' , array(
			'args' => array(
				'id' => array(
					'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
					'type'        => 'integer',
				)
			),
			array(
				'methods'             => WP_REST_SERVER::EDITABLE,
				'callback'            => array( $this, 'send_invoice' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		) );
	}

	/**
	 * Makes sure the current user has permissions.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|boolean
	 */
	public function permissions_check( $request ) {
		$order = wc_get_order( (int) $request['id'] );
		if ( $order && ! wc_rest_check_post_permissions( $this->post_type, 'read', $order->get_id() ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}
    
	/**
	 * Prepare a single order note output for response.
	 *
	 * @param WP_Comment $note Order note object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $note, $request ) {
		$data = array(
			'id'               => (int) $note->comment_ID,
			'author'           => __( 'WooCommerce', 'woocommerce' ) === $note->comment_author ? 'system' : $note->comment_author,
			'date_created'     => wc_rest_prepare_date_response( $note->comment_date ),
			'date_created_gmt' => wc_rest_prepare_date_response( $note->comment_date_gmt ),
			'note'             => $note->comment_content,
			'customer_note'    => (bool) get_comment_meta( $note->comment_ID, 'is_customer_note', true ),
		);
		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		// Wrap the data in a response object.
		return rest_ensure_response( $data );
	}

	/**
	 * Send order invoice email.
	 *
	 * @param WP_REST_Request
	 * @return WP_REST_Response
	 *
	 */
	public function send_invoice( $request ) {
		$order = wc_get_order( (int) $request['id'] );
		if ( ! $order || $this->post_type !== $order->get_type() ) {
			return new WP_Error( 'woocommerce_rest_order_invalid_id', __( 'Invalid order ID.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		do_action( 'woocommerce_before_resend_order_emails', $order, 'customer_invoice' );
		// Send the customer invoice email.
		WC()->payment_gateways();
		WC()->shipping();
		WC()->mailer()->customer_invoice( $order );
		// Note the event.
		$note_id = $order->add_order_note( __( 'Order details manually sent to customer.', 'woocommerce' ), false, true );
		if ( ! $note_id ) {
			return new WP_Error( 'woocommerce_api_cannot_create_order_note', __( 'Cannot create order note, please try again.', 'woocommerce' ), array( 'status' => 500 ) );
		}
		$note = get_comment( $note_id );

		do_action( 'woocommerce_after_resend_order_email', $order, 'customer_invoice' );
		$response = $this->prepare_item_for_response( $note, $request );
		$response = rest_ensure_response( $response );
		return $response;
	}

}
