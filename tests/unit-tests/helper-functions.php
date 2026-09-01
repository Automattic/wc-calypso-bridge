<?php
/**
 * Helper functions tests.
 */

class WC_Calypso_Bridge_Helper_Functions_Test extends WC_Unit_Test_Case {

	/**
	 * Test feature flag deprecation checks for specific WooCommerce versions.
	 *
	 * @dataProvider feature_flag_deprecation_provider
	 *
	 * @param string $feature              Feature slug.
	 * @param string $woocommerce_version  WooCommerce version.
	 * @param bool   $expected              Expected result.
	 */
	public function test_feature_flag_deprecation_for_version( $feature, $woocommerce_version, $expected ) {
		$method = new ReflectionMethod(
			WC_Calypso_Bridge_Helper_Functions::class,
			'is_wc_admin_feature_flag_deprecated_in_version'
		);
		$method->setAccessible( true );

		$this->assertSame( $expected, $method->invoke( null, $feature, $woocommerce_version ) );
	}

	/**
	 * Feature flag deprecation test cases.
	 *
	 * @return array<string, array{string, string, bool}>
	 */
	public function feature_flag_deprecation_provider() {
		return array(
			'stable deprecation version' => array( 'launch-your-store', '11.1.0', true ),
			'newer prerelease version'   => array( 'launch-your-store', '11.2.0-dev', true ),
			'beta deprecation version'   => array( 'launch-your-store', '11.1.0-beta.1', false ),
			'dev deprecation version'    => array( 'launch-your-store', '11.1.0-dev', false ),
			'older version'              => array( 'launch-your-store', '11.0.0', false ),
			'unknown feature'            => array( 'unknown-feature', '11.1.0', false ),
		);
	}
}
