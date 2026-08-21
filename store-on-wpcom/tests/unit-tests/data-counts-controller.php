<?php
/**
 * Tests for the Data Counts endpoint in the REST API.
 *
 * The product counts compare stock levels against the WooCommerce low-stock and
 * no-stock thresholds. Those thresholds come from options, which are not
 * guaranteed to hold plain integers, so the counts need to stay correct and
 * stable whatever the stored value looks like.
 */
class Data_Counts_Controller extends WC_REST_Unit_Test_Case {

	/**
	 * Setup our test server, endpoint, and user info.
	 */
	public function setUp() {
		parent::setUp();

		$this->endpoint = new WC_Calypso_Bridge_Data_Counts_Controller();

		$this->user = $this->factory->user->create( array(
			'role' => 'administrator',
		) );

		$this->shop_manager = $this->factory->user->create( array(
			'role' => 'shop_manager',
		) );

		$this->customer = $this->factory->user->create( array(
			'role' => 'customer',
		) );

		// Three published products that manage stock, one at each interesting
		// level relative to the thresholds used below.
		$this->create_stock_product( 0 );  // Out of stock.
		$this->create_stock_product( 1 );  // Low inventory.
		$this->create_stock_product( 50 ); // Neither.

		update_option( 'woocommerce_notify_no_stock_amount', 0 );
		update_option( 'woocommerce_notify_low_stock_amount', 2 );
	}

	/**
	 * Create a published product with stock management on and a known level.
	 *
	 * @param int $stock Stock quantity.
	 * @return WC_Product
	 */
	protected function create_stock_product( $stock ) {
		$product = WC_Helper_Product::create_simple_product();
		$product->set_manage_stock( true );
		$product->set_stock_quantity( $stock );
		$product->set_status( 'publish' );
		$product->save();

		return $product;
	}

	/**
	 * The counts are cached for the request, so drop the cache between reads.
	 *
	 * @return array
	 */
	protected function get_fresh_product_counts() {
		wp_cache_delete( 'wc-counts-products' );

		return $this->endpoint->get_product_counts();
	}

	/**
	 * Threshold values that are not plain integers. Each one should be reduced
	 * to its leading integer and used as an ordinary stock level.
	 *
	 * @return array
	 */
	public function malformed_threshold_provider() {
		return array(
			'trailing text'      => array( '2 items' ),
			'leading whitespace' => array( ' 2' ),
			'decimal'            => array( '2.9' ),
			'negative'           => array( '-2' ),
			'quoted fragment'    => array( "2' OR 1=1 OR '" ),
			'inverted fragment'  => array( "2' OR 1=2 OR '" ),
			'nested select'      => array( "2' OR ( SELECT 1 ) OR '" ),
		);
	}

	/**
	 * Benign thresholds produce the counts the fixtures imply.
	 */
	public function test_product_counts_with_integer_thresholds() {
		$counts = $this->get_fresh_product_counts();

		$this->assertEquals( 3, $counts['all'] );
		$this->assertEquals( 1, $counts['out-of-stock'] );
		$this->assertEquals( 1, $counts['low-inventory'] );
	}

	/**
	 * A low-stock threshold that is not a plain integer is reduced to 2, which
	 * is the value the benign case uses, so the counts do not move.
	 *
	 * @dataProvider malformed_threshold_provider
	 * @param string $threshold Stored threshold value.
	 */
	public function test_malformed_low_stock_threshold_keeps_counts_stable( $threshold ) {
		$expected = $this->get_fresh_product_counts();

		update_option( 'woocommerce_notify_low_stock_amount', $threshold );
		$actual = $this->get_fresh_product_counts();

		$this->assertEquals( $expected['low-inventory'], $actual['low-inventory'] );
		$this->assertEquals( $expected['out-of-stock'], $actual['out-of-stock'] );
	}

	/**
	 * The no-stock threshold is the same kind of value and behaves the same way.
	 */
	public function test_malformed_no_stock_threshold_keeps_counts_stable() {
		$expected = $this->get_fresh_product_counts();

		update_option( 'woocommerce_notify_no_stock_amount', "0' OR 1=1 OR '" );
		$actual = $this->get_fresh_product_counts();

		$this->assertEquals( $expected['out-of-stock'], $actual['out-of-stock'] );
		$this->assertEquals( $expected['low-inventory'], $actual['low-inventory'] );
	}

	/**
	 * Two thresholds that reduce to the same integer must produce the same
	 * counts, whatever text follows the integer.
	 */
	public function test_thresholds_with_equal_integer_value_agree() {
		update_option( 'woocommerce_notify_low_stock_amount', "2' OR 1=1 OR '" );
		$first = $this->get_fresh_product_counts();

		update_option( 'woocommerce_notify_low_stock_amount', "2' OR 1=2 OR '" );
		$second = $this->get_fresh_product_counts();

		$this->assertEquals( $first['low-inventory'], $second['low-inventory'] );
	}

	/**
	 * A threshold with no leading integer degrades to zero rather than
	 * producing a broken query, so the endpoint keeps answering.
	 */
	public function test_non_numeric_threshold_degrades_to_zero() {
		update_option( 'woocommerce_notify_low_stock_amount', 'not a number' );

		$counts = $this->get_fresh_product_counts();

		$this->assertEquals( 3, $counts['all'] );
		$this->assertEquals( 0, $counts['low-inventory'] );
	}

	/**
	 * The route answers a shop manager and returns the product counts.
	 */
	public function test_get_items_as_shop_manager() {
		wp_set_current_user( $this->shop_manager );
		wp_cache_delete( 'wc-counts-products' );

		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v3/data/counts' ) );
		$data     = $response->get_data();

		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( 3, $data['products']['all'] );
		$this->assertEquals( 1, $data['products']['low-inventory'] );
	}

	/**
	 * A customer is signed in but lacks the capability, so the request is
	 * forbidden rather than unauthorized.
	 */
	public function test_get_items_as_customer_is_denied() {
		wp_set_current_user( $this->customer );

		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v3/data/counts' ) );

		$this->assertEquals( 403, $response->get_status() );
	}

	/**
	 * A signed-out request is unauthorized.
	 */
	public function test_get_items_when_signed_out_is_denied() {
		wp_set_current_user( 0 );

		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v3/data/counts' ) );

		$this->assertEquals( 401, $response->get_status() );
	}
}
