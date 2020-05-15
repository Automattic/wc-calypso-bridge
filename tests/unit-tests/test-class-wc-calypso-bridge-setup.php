<?php
/**
 * Tests for WC_Calypso_Bridge_Setup
 */

class WC_Calypso_Bridge_Setup_Test extends WC_Calypso_Bridge_Test {

	/**
	 * Test getting a single note.
	 *
	 * @since 3.5.0
	 */
	public function test_remove_paid_extension_upsells() {
		$wc_calypso_bridge_setup = WC_Calypso_Bridge_Setup::get_instance();
		$product_types           = array(
			'physical'      => array(
				'label'       => 'Physical products',
				'description' => 'Products you ship to customers.',
			),
			'subscriptions' => array(
				'label'       => 'Physical products',
				'description' => 'Products you ship to customers.',
				'product'     => '12345',
			),
		);
		$filtered_products       = $wc_calypso_bridge_setup->remove_paid_extension_upsells( $product_types );
		$this->assertEquals( count( $filtered_products ), 1 );
		$this->assertEquals( $filtered_products['physical']['label'], 'Physical products' );
	}

}
