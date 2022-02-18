<?php
/**
 * REST API Payments Controller
 *
 * Handles requests to the /payments endpoint.
 *
 * @author   Automattic
 * @category API
 * @package  WooCommerce/API
 */

use Automattic\WooCommerce\Admin\Notes\Note;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Settings controller class.
 *
 * @package WooCommerce/API
 */
class WC_Payments_Controller extends WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-calypso-bridge/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'payments';

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/activate-promo',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'activate_promo_note' ),
					'permission_callback' => array( $this, 'check_permission' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/view-welcome',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'store_view_welcome_time' ),
					'permission_callback' => array( $this, 'check_permission' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Verify access.
	 *
	 * Override this method if custom permissions required.
	 */
	public function check_permission() {
		return current_user_can( 'manage_woocommerce' );
	}

	/**
	 * Set action to promo note to give the user discount eligibility.
	 */
	public function activate_promo_note() {
		$promo_name       = 'wcpay-promo-2022-3-incentive-100-off';
		$data_store       = WC_Data_Store::load( 'admin-note' );
		$add_where_clause = function( $where_clause ) use ( $promo_name ) {
			return $where_clause . " AND name = '$promo_name'";
		};

		add_filter( 'woocommerce_note_where_clauses', $add_where_clause );
		$notes = $data_store->get_notes();
		remove_filter( 'woocommerce_note_where_clauses', $add_where_clause );

		if ( ! empty( $notes ) ) {
			$note = new Note( $notes[0] );
			$note->set_status( Note::E_WC_ADMIN_NOTE_ACTIONED );
			$data_store->update( $note );
		} else {
			// Promo note doesn't exist, this could happen in cases where
			// user might have disabled RemoteInboxNotications via disabling
			// marketing suggestions. Thus we'd have to manually add the note.
			$note = new Note();
			$note->set_name( $promo_name );
			$note->set_status( Note::E_WC_ADMIN_NOTE_ACTIONED );
			$data_store->create( $note );
		}

		return rest_ensure_response(
			array(
				'success' => true,
			)
		);
	}

	/**
	 * Save the time of viewing welcome to option in order to activate a remind me
	 * note after 3 days.
	 */
	public function store_view_welcome_time() {
		if ( ! get_option( 'wc_calypso_bridge_payments_view_welcome_timestamp', false ) ) {
			update_option( 'wc_calypso_bridge_payments_view_welcome_timestamp', time() );
		}

		return rest_ensure_response(
			array(
				'success' => true,
			)
		);
	}

}
