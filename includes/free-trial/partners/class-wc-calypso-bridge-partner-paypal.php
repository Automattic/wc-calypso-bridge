<?php

/**
 * WC Calypso Bridge Partner PayPal
 *
 * @since   x.x.x
 * @version x.x.x
 *
 * This file includes customizations for the sites that were created through /start/paypal on woo.com.
 * woocommerce_onboarding_profile.partner must get 'paypal'
 */
class WC_Calypso_Bridge_Partner_PayPal {

    /**
     * Class instance.
     *
     * @var WC_Calypso_Bridge_Partner_PayPal instance
     */
    protected static $instance = false;

    /**
     * Get class instance
     */
    public static function get_instance() {
        if ( ! self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        // Only for free trials.
        // TODO: Uncomment this line when the free trial plan is available.
        // if ( ! wc_calypso_bridge_is_ecommerce_trial_plan() ) {
        //     return;
        // }
        $onboarding_profile = get_option( 'woocommerce_onboarding_profile', array() );
        if ( ! isset( $onboarding_profile['partner'] ) ) {
            return;
        }

        if ( $onboarding_profile['partner'] !== 'paypal' ) {
            return;
        }

        $this->add_paypal_setup_task();
        $this->add_paypal_connect_url_to_js();
        $this->remove_woo_payments_from_payments_suggestions_feed();
    }

    /**
     * Remove woo payments from the payments suggestions feed.
     *
     * @return void
     */
    private function remove_woo_payments_from_payments_suggestions_feed() {
        add_filter( 'woocommerce_admin_payment_gateway_suggestion_specs', function( $specs ) {
            $keys = array(
                'woocommerce_payments',
                'woocommerce_payments:with-in-person-payments',
                'woocommerce_payments:without-in-person-payments',
            );
            foreach ( $keys as $key ) {
                if ( isset( $specs[ $key ] ) ) {
                    unset( $specs[ $key ] );
                }
            }

            return $specs;
        });
    }

    private function has_paypal_plugin_class() {
        return class_exists( '\WooCommerce\PayPalCommerce\PluginModule' );
    }

    /**
     * Add PayPal setup task to the setup tasklist.
     */
    private function add_paypal_setup_task() {
        add_filter( 'woocommerce_admin_experimental_onboarding_tasklists', function( $lists ) {
            if ( !$this->has_paypal_plugin_class() ){
                return $lists;
            }

            if ( isset( $lists['setup'] ) ) {
                require_once __DIR__ . '/../../tasks/class-wc-calypso-task-get-paid-with-paypal.php';
                // Place it at the third position.
                array_splice( $lists['setup']->tasks, 2, 0, array( new \Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\WCBridgeGetPaidWithPayPal( $lists['setup'] ) ) );
            }
            return $lists;
        } );
    }

    /**
     * Add PayPal connect URL to the JS.
     *
     * @return void
     */
    private function add_paypal_connect_url_to_js() {
        add_filter( 'wc_calypso_bridge_shared_params', function( $params ) {
            if ( !$this->has_paypal_plugin_class() ){
                return $params;
            }

            try {
                $connection_tab_id = \WooCommerce\PayPalCommerce\WcGateway\Settings\Settings::CONNECTION_TAB_ID;
                $params['paypal_connect_url'] = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=ppcp-gateway&ppcp-tab=' . $connection_tab_id );
            } catch (\Throwable $e) {
                // Fallback to the settings page
                $params['paypal_connect_url'] = add_query_arg( array(
                    'page' => 'wc-settings',
                    'tab' => 'paypal',
                ), admin_url( 'admin.php' ) );
            }

            return $params;
        });
    }
}

WC_Calypso_Bridge_Partner_PayPal::get_instance();