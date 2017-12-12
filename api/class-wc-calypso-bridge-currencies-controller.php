<?php
/**
 * REST API WC Calypso Bridge Currencies
 *
 * Adds an endpoint for returning WooCommerce Currency Data
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
class WC_Calypso_Bridge_Currencies_Controller extends WC_REST_Controller {

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
	protected $rest_base = 'currencies';

	/**
	 * Register Currency route
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_currencies' ),
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
		if ( ! wc_rest_check_manager_permissions( 'settings', 'edit' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_edit', __( 'Sorry, you cannot view this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Get WooCommerce Currencies.
	 *
	 * @param WP_REST_Request
	 * @return WP_REST_Response
	 *
	 */
	public function get_currencies( $request ) {
        $currencies = array();
        foreach ( get_woocommerce_currencies() as $code => $name ) {
            $currencies[] = array(
                'code'   => $code,
                'name'   => $name,
                'symbol' => html_entity_decode( get_woocommerce_currency_symbol( $code ) ),
            );
        }
        
		return rest_ensure_response( $currencies );
	}

}
