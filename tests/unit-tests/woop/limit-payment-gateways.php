<?php
/**
 * Tests.
 */

if ( ! class_exists( 'WC_Payment_Gateway_WCPay_Subscriptions_Compat' ) ) {
	class WC_Payment_Gateway_WCPay_Subscriptions_Compat {}
}

class LimitPaymentGatewaysTest extends WC_Calypso_Bridge_Test {
	public function test_limits_strings_to_cod() {
		Atomic_Plan_Manager::set_current_plan_slug( 'free' );
		$this->assertEquals(
			[ 'WC_Gateway_COD' ],
			wc_calypso_bridge_limit_payment_gateways( [ 'WC_Gateway_COD', 'WC_Gateway_BACS' ] )
		);
	}

	public function test_limits_classes_to_WCPay() {
		$this->assertEquals(
			[ new WC_Payment_Gateway_WCPay_Subscriptions_Compat() ],
			wc_calypso_bridge_limit_payment_gateways( [ new WC_Payment_Gateway_WCPay_Subscriptions_Compat(), new \stdClass() ] )
		);
	}

	public function test_does_not_limit_on_biz() {
		Atomic_Plan_Manager::set_current_plan_slug( Atomic_Plan_Manager::BUSINESS_PLAN_SLUG );
		$this->assertEquals(
			[ 'WC_Gateway_COD', 'WC_Gateway_BACS' ],
			wc_calypso_bridge_limit_payment_gateways( [ 'WC_Gateway_COD', 'WC_Gateway_BACS' ] )
		);
		$this->assertEquals(
			[ new WC_Payment_Gateway_WCPay_Subscriptions_Compat(), new \stdClass() ],
			wc_calypso_bridge_limit_payment_gateways( [ new WC_Payment_Gateway_WCPay_Subscriptions_Compat(), new \stdClass() ] )
		);
	}

	public function test_does_not_limit_on_ecom() {
		Atomic_Plan_Manager::set_current_plan_slug( Atomic_Plan_Manager::ECOMMERCE_PLAN_SLUG );
		$this->assertEquals(
			[ 'WC_Gateway_COD', 'WC_Gateway_BACS' ],
			wc_calypso_bridge_limit_payment_gateways( [ 'WC_Gateway_COD', 'WC_Gateway_BACS' ] )
		);
		$this->assertEquals(
			[ new WC_Payment_Gateway_WCPay_Subscriptions_Compat(), new \stdClass() ],
			wc_calypso_bridge_limit_payment_gateways( [ new WC_Payment_Gateway_WCPay_Subscriptions_Compat(), new \stdClass() ] )
		);
	}
}
