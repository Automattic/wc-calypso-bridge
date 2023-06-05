<?php
/**
 * Skips OBW by overriding onboarding option.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   2.1.3
 * @version 2.1.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC Calypso Bridge Skip OBW
 */
class WC_Calypso_Bridge_Skip_OBW {

	/**
	 * Class Instance.
	 *
	 * @var WC_Calypso_Bridge_Skip_OBW instance
	 */
	protected static $instance = null;

	/**
	 * Get class instance
	 */
	public static function get_instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Only in Ecommerce or free trial.
		if ( ! ( wc_calypso_bridge_has_ecommerce_features() || wc_calypso_bridge_is_ecommerce_trial_plan() ) ) {
			return;
		}

		add_action( 'plugins_loaded', array( $this, 'initialize' ), 2 );
	}

	/**
	 * Initialize.
	 */
	public function initialize() {
		/**
		 * Force skip OBW by appending to existing option `woocommerce_onboarding_profile`.
		 *
		 * @since 1.9.4
		 *
		 * @param  mixed  $value
		 * @return array
		 */
		add_filter( 'option_woocommerce_onboarding_profile', array( $this, 'add_skipped_state' ) );

		/**
		 * Force skip OBW when option `woocommerce_onboarding_profile` does not exist.
		 *
		 * @since 2.1.3
		 *
		 * @param  mixed  $value
		 * @return array
		 */
		add_filter( 'default_option_woocommerce_onboarding_profile', array( $this, 'add_skipped_state' ) );
	}

	/**
	 * Skip the OBW by appending the skipped state to the option value.
	 *
	 * @param  mixed  $value
	 * @return array
	 */
	public function add_skipped_state( $option_value ) {
		$value = $option_value ?? array();

		if ( ! is_array( $value ) ) {
			// If we don't have an array, return a valid option value.
			$value = array();
		}

		$value['skipped'] = true;

		return $value;
	}
}

WC_Calypso_Bridge_Skip_OBW::get_instance();
