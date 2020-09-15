<?php
/**
 * Tests for WC_Calypso_Bridge_Filters
 */

class WC_Calypso_Bridge_Filters_Test extends WC_Calypso_Bridge_Test {
	/**
	 * Test removing `CBD and other hemp-derived products` option from industries list
	 *
	 */
	public function test_remove_not_allowed_industries() {
		$wc_calypso_bridge_filters     = WC_Calypso_Bridge_Filters::get_instance();
		$industries                    = array(
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
		$industries_variation          = array(
			array( 'slug' => 'fashion-apparel-accessories' ),
			array( 'slug' => 'cbd-other-hemp-derived-products' ),
			array( 'slug' => 'other' ),
		);
		$filtered_industries           = $wc_calypso_bridge_filters->remove_not_allowed_industries( $industries );
		$filtered_industries_variation = $wc_calypso_bridge_filters->remove_not_allowed_industries( $industries_variation );
		$this->assertEquals( count( $filtered_industries ), 2 );
		$this->assertEquals( count( $filtered_industries_variation ), 2 );
	}
}
