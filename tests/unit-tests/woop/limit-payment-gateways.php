<?php
/**
 * Tests.
 */

namespace WCPay\Payment_Methods {
	if ( ! class_exists( 'WCPay\Payment_Methods\CC_Payment_Gateway' ) ) {
		class CC_Payment_Gateway {}
		class UPE_Payment_Gateway {}
		class Giropay_Payment_Gateway {}
		class Sepa_Payment_Gateway {}
		class Sofort_Payment_Gateway {}
	}
}

namespace {
	use WCPay\Payment_Methods\CC_Payment_Gateway;
	use WCPay\Payment_Methods\UPE_Payment_Gateway;
	use WCPay\Payment_Methods\Giropay_Payment_Gateway;
	use WCPay\Payment_Methods\Sepa_Payment_Gateway;
	use WCPay\Payment_Methods\Sofort_Payment_Gateway;

	class LimitPaymentGatewaysTest extends WC_Calypso_Bridge_Test {
		public static $core_gateways = [
			'WC_Gateway_BACS',
			'WC_Gateway_Cheque',
			'WC_Gateway_COD',
		];
		public static $wcpay_gateways = [];

		public function setUp() {
			parent::setUp();
			static::$wcpay_gateways = [
				new CC_Payment_Gateway(),
				new UPE_Payment_Gateway(),
				new Giropay_Payment_Gateway(),
				new Sepa_Payment_Gateway(),
				new Sofort_Payment_Gateway(),
			];
		}

		public function test_limits_strings_to_core_gateways() {
			Atomic_Plan_Manager::set_current_plan_slug( Atomic_Plan_Manager::FREE_PLAN_SLUG );
			$this->assertSame(
				static::$core_gateways,
				wc_calypso_bridge_limit_payment_gateways( array_merge( static::$core_gateways, [ 'WC_Gateway_Paypal' ] ) )
			);
		}

		public function test_limits_classes_to_WCPay() {
			Atomic_Plan_Manager::set_current_plan_slug( Atomic_Plan_Manager::FREE_PLAN_SLUG );
			$this->assertEquals(
				static::$wcpay_gateways,
				wc_calypso_bridge_limit_payment_gateways( array_merge( static::$wcpay_gateways, [ new \stdClass() ] ) )
			);
		}

		public function test_does_not_limit_on_biz() {
			Atomic_Plan_Manager::set_current_plan_slug( Atomic_Plan_Manager::BUSINESS_PLAN_SLUG );
			$this->assertEquals(
				array_merge( static::$core_gateways, [ 'WC_Gateway_Paypal' ] ),
				wc_calypso_bridge_limit_payment_gateways( array_merge( static::$core_gateways, [ 'WC_Gateway_Paypal' ] ) )
			);
			$this->assertEquals(
				array_merge( static::$wcpay_gateways, [ new \stdClass() ] ),
				wc_calypso_bridge_limit_payment_gateways( array_merge( static::$wcpay_gateways, [ new \stdClass() ] ) )
			);
		}

		public function test_does_not_limit_on_ecom() {
			Atomic_Plan_Manager::set_current_plan_slug( Atomic_Plan_Manager::ECOMMERCE_PLAN_SLUG );
			$this->assertEquals(
				array_merge( static::$core_gateways, [ 'WC_Gateway_Paypal' ] ),
				wc_calypso_bridge_limit_payment_gateways( array_merge( static::$core_gateways, [ 'WC_Gateway_Paypal' ] ) )
			);
			$this->assertEquals(
				array_merge( static::$wcpay_gateways, [ new \stdClass() ] ),
				wc_calypso_bridge_limit_payment_gateways( array_merge( static::$wcpay_gateways, [ new \stdClass() ] ) )
			);
		}
	}
}
