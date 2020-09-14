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

	/**
	 * Test removing CBD industry from industries list.
	 *
	 */
	public function test_remove_not_allowed_industries() {
		$wc_calypso_bridge_setup = WC_Calypso_Bridge_Setup::get_instance();
		$industries              = array(
			'fashion-apparel-accessories'     => array(
				'label'             => 'Fashion, apparel, and accessories',
				'use_description'   => false,
				'description_label' => '',
			),
			'cbd-other-hemp-derived-products' => array(
				'label'             => 'CBD and other hemp-derived products',
				'use_description'   => false,
				'description_label' => '',
			),
			'other'                           => array(
				'label'             => 'Other',
				'use_description'   => true,
				'description_label' => 'Description',
			),
		);
		$filtered_industries     = $wc_calypso_bridge_setup->remove_not_allowed_industries( $industries );
		$this->assertEquals( count( $filtered_industries ), 2 );
	}

}
