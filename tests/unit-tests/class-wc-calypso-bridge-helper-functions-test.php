<?php
/**
 * Helper functions tests.
 */

class WC_Calypso_Bridge_Helper_Functions_Test extends WC_Unit_Test_Case {

	/**
	 * Test stable features whose deprecation version has been reached.
	 *
	 * @dataProvider stable_feature_provider
	 *
	 * @param string $feature              Feature slug.
	 * @param string $woocommerce_version  WooCommerce version.
	 * @param bool   $expected              Expected result.
	 */
	public function test_stable_feature_enabled_for_version( $feature, $woocommerce_version, $expected ) {
		$this->assertSame(
			$expected,
			WC_Calypso_Bridge_Helper_Functions::is_wc_admin_feature_enabled( $feature, $woocommerce_version )
		);
	}

	/**
	 * Test that features missing from the dictionary use the normal feature flag flow.
	 */
	public function test_unlisted_feature_uses_normal_feature_flag_flow() {
		$feature        = 'wc-calypso-bridge-test-feature';
		$enable_feature = function( $features ) use ( $feature ) {
			$features[] = $feature;
			return $features;
		};

		add_filter( 'woocommerce_admin_features', $enable_feature, PHP_INT_MAX );

		try {
			$this->assertTrue(
				WC_Calypso_Bridge_Helper_Functions::is_wc_admin_feature_enabled( $feature, '11.1.0' )
			);
		} finally {
			remove_filter( 'woocommerce_admin_features', $enable_feature, PHP_INT_MAX );
		}
	}

	/**
	 * Test that optional features continue to respect their options.
	 *
	 * @dataProvider optional_feature_provider
	 *
	 * @param string $feature     Feature slug.
	 * @param string $option_name Option name.
	 */
	public function test_optional_feature_respects_option( $feature, $option_name ) {
		update_option( $option_name, 'no' );

		$this->assertFalse(
			WC_Calypso_Bridge_Helper_Functions::is_wc_admin_feature_enabled( $feature, '11.1.0' )
		);

		update_option( $option_name, 'yes' );

		$this->assertTrue(
			WC_Calypso_Bridge_Helper_Functions::is_wc_admin_feature_enabled( $feature, '11.1.0' )
		);
	}

	/**
	 * Optional feature test cases.
	 *
	 * @return array<string, array{string, string}>
	 */
	public function optional_feature_provider() {
		return array(
			'analytics'                  => array( 'analytics', 'woocommerce_analytics_enabled' ),
			'remote inbox notifications' => array( 'remote-inbox-notifications', 'woocommerce_show_marketplace_suggestions' ),
		);
	}

	/**
	 * Stable feature test cases.
	 *
	 * @return array<string, array{string, string, bool}>
	 */
	public function stable_feature_provider() {
		return array(
			'stable deprecation version' => array( 'launch-your-store', '11.1.0', true ),
			'newer prerelease version'   => array( 'launch-your-store', '11.2.0-dev', true ),
			'unknown feature'            => array( 'unknown-feature', '11.1.0', false ),
		);
	}
}
