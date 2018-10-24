<?php
/**
 * WC_Calypso_Bridge_Setup.
 * 
 * Adds the functionality needed to bridge the WooCommerce onboarding wizard.
 */
class WC_Calypso_Bridge_Setup {

    /**
	 * Class instance.
	 */
    static $instance = false;

	public static function getInstance() {
		if ( !self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

    /**
	 * Constructor.
	 */
    private function __construct() {
		add_filter( 'woocommerce_setup_wizard_steps', array( $this, 'remove_unused_steps' ) );
    }
    
    /**
     * Remove unused steps from the wizard
     * 
     * @param array $default_steps Default steps used by WC wizard
     * @return array
     */
    public function remove_unused_steps( $default_steps ) {
        $whitelist = array( 'store_setup', 'payment' );
        $steps = array_intersect_key( $default_steps, array_flip( $whitelist ) );
        return $steps;
    }

}

$WC_Calypso_Bridge_Setup = WC_Calypso_Bridge_Setup::getInstance();
