<?php
/**
 * Extends the original WC setup wizard so we can add in new templates to the view.
 *
 * @package WC_Calypso_Bridge/Classes
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) || ! class_exists( 'WC_Admin_Setup_Wizard' ) ) {
	exit;
}

/**
 * WC_Admin_Setup_Wizard class.
 */
class WC_Calypso_Bridge_Admin_Setup_Wizard extends WC_Admin_Setup_Wizard {

	/**
	 * Current step
	 *
	 * @var string
	 */
	private $step = '';

	/**
	 * Steps for the setup wizard
	 *
	 * @var array
	 */
	private $steps = array();

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			add_action( 'admin_init', array( $this, 'skip_empty_payment_step' ), 9 );
			add_action( 'admin_init', array( $this, 'setup_wizard' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_calypsoify_scripts' ) );
		}
	}

	/**
	 * Setup Wizard Header.
	 */
	public function setup_wizard_header() {
		set_current_screen();
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?> class="wc-setup-page">
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php esc_html_e( 'WooCommerce &rsaquo; Setup Wizard', 'wc-calypso-bridge' ); ?></title>
			<?php do_action( 'admin_enqueue_scripts' ); ?>
			<?php wp_print_scripts( array( 'wc-setup', 'wc-calypso-bridge-calypsoify-obw' ) ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_print_scripts' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="wc-setup wp-core-ui <?php echo esc_attr( 'wc-setup-step__' . $this->step ); ?>">
			<?php wp_admin_bar_render(); ?>
		<?php
	}

	/**
	 * Output the step header.
	 */
	public function setup_wizard_steps() {
		$step = $this->steps[ $this->step ];
		?>
		<div class="wc-step-heading">
			<h1><?php echo esc_html( $step['name'] ); ?></h1>
			<?php if ( isset( $step['subheading'] ) ) { ?>
				<h2><?php echo esc_html( $step['subheading'] ); ?></h2>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		if ( empty( $_GET['page'] ) || 'wc-setup' !== $_GET['page'] ) { // WPCS: CSRF ok, input var ok.
			return;
		}
		$default_steps = array(
			'store_setup' => array(
				'name'       => __( 'Let\'s start setting up.', 'wc-calypso-bridge' ),
				'subheading' => __( 'First we need to determine some basic information about your store.', 'wc-calypso-bridge' ),
				'view'       => array( $this, 'wc_setup_store_setup' ),
				'handler'    => array( $this, 'wc_setup_store_setup_save' ),
			),
			'payment'     => array(
				'name'       => __( 'Select payment methods.', 'wc-calypso-bridge' ),
				'subheading' => __( 'Additional payment methods can be setup later.', 'wc-calypso-bridge' ),
				'view'       => array( $this, 'wc_setup_payment' ),
				'handler'    => array( $this, 'wc_setup_payment_save' ),
			),
		);

		$this->steps = apply_filters( 'wc_calypso_bridge_setup_wizard_steps', $default_steps );
		$this->step  = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) ); // WPCS: CSRF ok, input var ok.

		$this->save_step();

		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
	}

	/**
	 * Output the content for the current step.
	 */
	public function setup_wizard_content() {
		echo '<div class="wc-setup-content">';
		if ( ! empty( $this->steps[ $this->step ]['view'] ) ) {
			call_user_func( $this->steps[ $this->step ]['view'], $this );
		}
		echo '</div>';
	}

	/**
	 * Setup Wizard Footer.
	 */
	public function setup_wizard_footer() {
		?>
			<div class="wc-setup-footer">
				<button class="button-primary button button-large" value="<?php esc_attr_e( "Let's go!", 'wc-calypso-bridge' ); ?>" name="save_step"><?php esc_html_e( 'Continue', 'wc-calypso-bridge' ); ?></button>
			</div>
			<?php do_action( 'admin_footer', '' ); ?>
			<?php do_action( 'admin_print_footer_scripts' ); ?>
			</body>
		</html>
		<?php
	}

	/**
	 * Initial "store setup" step.
	 * Location, product type, page setup, and tracking opt-in.
	 */
	public function wc_setup_store_setup() {
		$address        = WC()->countries->get_base_address();
		$address_2      = WC()->countries->get_base_address_2();
		$city           = WC()->countries->get_base_city();
		$state          = WC()->countries->get_base_state();
		$country        = WC()->countries->get_base_country();
		$postcode       = WC()->countries->get_base_postcode();
		$currency       = get_option( 'woocommerce_currency', 'GBP' );
		$product_type   = get_option( 'woocommerce_product_type', 'both' );
		$sell_in_person = get_option( 'woocommerce_sell_in_person', 'none_selected' );
		if ( empty( $country ) ) {
			$user_location = WC_Geolocation::geolocate_ip();
			$country       = $user_location['country'];
			$state         = $user_location['state'];
		}
		$locale_info         = include WC()->plugin_path() . '/i18n/locale-info.php';
		$currency_by_country = wp_list_pluck( $locale_info, 'currency_code' );
		$classes             = array( 'address-step' );
		if ( $address && $city && $state && $postcode && $country ) {
			$classes[] = 'store-address-preview-mode';
		}
		?>
		<form method="post" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<input type="hidden" name="save_step" value="store_setup" />
			<?php wp_nonce_field( 'wc-setup' ); ?>
			<p class="store-setup"><?php esc_html_e( 'The following wizard will help you configure your store and get you started quickly.', 'wc-calypso-bridge' ); ?></p>

			<div class="store-address-preview-container">
				<label for="store_address" class="location-prompt"><?php esc_html_e( 'Where is your store based?', 'wc-calypso-bridge' ); ?></label>
				<div class="store-address-preview">
					<p>
						<?php echo esc_attr( $address ); ?><br>
						<?php if ( $address_2 ) { ?>
							<?php echo esc_attr( $address_2 ); ?><br>
						<?php } ?>
						<?php echo sprintf( '%s, %s %s', esc_attr( $city ), esc_attr( $state ), esc_attr( $postcode ) ); ?><br>
						<?php echo esc_attr( WC()->countries->countries[ $country ] ); ?>
					</p>					
					<button type="button" class="button button-large toggle-store_address_edit" value="<?php esc_attr_e( 'Edit', 'wc-calypso-bridge' ); ?>"><?php esc_html_e( 'Edit', 'wc-calypso-bridge' ); ?></button>
				</div>
			</div>

			<div class="store-address-container">
				<label for="store_address" class="location-prompt"><?php esc_html_e( 'Where is your store based?', 'wc-calypso-bridge' ); ?></label>
				<input type="text" id="store_address" class="location-input" name="store_address" required value="<?php echo esc_attr( $address ); ?>" />
				<?php if ( empty( $address_2 ) ) { ?>
					<a href="#" class="toggle-store_address_2"><span class="plus-sign"></span><?php esc_html_e( 'Add address line 2', 'wc-calypso-bridge' ); ?></a>
				<?php } ?>
				<input type="text" id="store_address_2" class="location-input <?php echo empty( $address_2 ) ? '' : 'is-visible'; ?>" name="store_address_2" value="<?php echo esc_attr( $address_2 ); ?>" />

				<div>
					<label class="location-prompt" for="store_city"><?php esc_html_e( 'City', 'wc-calypso-bridge' ); ?></label>
					<input type="text" id="store_city" class="location-input" name="store_city" required value="<?php echo esc_attr( $city ); ?>" />
				</div>

				<div class="store-state-container hidden">
					<label for="store_state" class="location-prompt">
						<?php esc_html_e( 'State', 'wc-calypso-bridge' ); ?>
					</label>
					<select id="store_state" name="store_state" data-placeholder="<?php esc_attr_e( 'Choose a state&hellip;', 'wc-calypso-bridge' ); ?>" aria-label="<?php esc_attr_e( 'State', 'wc-calypso-bridge' ); ?>" class="location-input wc-enhanced-select dropdown"></select>
				</div>

				<div class="city-and-postcode">
					<div>
						<label class="location-prompt" for="store_country"><?php esc_html_e( 'Country', 'wc-calypso-bridge' ); ?></label>
						<select id="store_country" name="store_country" required data-placeholder="<?php esc_attr_e( 'Choose a country&hellip;', 'wc-calypso-bridge' ); ?>" aria-label="<?php esc_attr_e( 'Country', 'wc-calypso-bridge' ); ?>" class="location-input wc-enhanced-select dropdown">
							<?php foreach ( WC()->countries->get_countries() as $code => $label ) : ?>
								<option <?php selected( $code, $country ); ?> value="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div>
						<label class="location-prompt" for="store_postcode"><?php esc_html_e( 'Postcode / ZIP', 'wc-calypso-bridge' ); ?></label>
						<input type="text" id="store_postcode" class="location-input" name="store_postcode" required value="<?php echo esc_attr( $postcode ); ?>" />
					</div>
				</div>
			</div>

			<div class="store-currency-container">
				<label class="location-prompt" for="currency_code">
					<?php esc_html_e( 'What currency do you accept payments in?', 'wc-calypso-bridge' ); ?>
				</label>
			<select
				id="currency_code"
				name="currency_code"
				required
				data-placeholder="<?php esc_attr_e( 'Choose a currency&hellip;', 'wc-calypso-bridge' ); ?>"
				class="location-input wc-enhanced-select dropdown"
			>
				<option value=""><?php esc_html_e( 'Choose a currency&hellip;', 'wc-calypso-bridge' ); ?></option>
				<?php foreach ( get_woocommerce_currencies() as $code => $name ) : ?>
					<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $currency, $code ); ?>>
						<?php
						$symbol = get_woocommerce_currency_symbol( $code );
						if ( $symbol === $code ) {
							/* translators: 1: currency name 2: currency code */
							echo esc_html( sprintf( __( '%1$s (%2$s)', 'wc-calypso-bridge' ), $name, $code ) );
						} else {
							/* translators: 1: currency name 2: currency symbol, 3: currency code */
							echo esc_html( sprintf( __( '%1$s (%2$s / %3$s)', 'wc-calypso-bridge' ), $name, get_woocommerce_currency_symbol( $code ), $code ) );
						}
						?>
					</option>
				<?php endforeach; ?>
			</select>
			<script type="text/javascript">
				var wc_setup_currencies = <?php echo wp_json_encode( $currency_by_country ); ?>;
				var wc_base_state       = "<?php echo esc_js( $state ); ?>";
			</script>
			</div>

			<div class="product-type-container">
				<label class="location-prompt" for="product_type">
					<?php esc_html_e( 'What type of products do you plan to sell?', 'wc-calypso-bridge' ); ?>
				</label>
				<select id="product_type" name="product_type" required class="location-input wc-enhanced-select dropdown">
					<option value="both" <?php selected( $product_type, 'both' ); ?>><?php esc_html_e( 'I plan to sell both physical and digital products', 'wc-calypso-bridge' ); ?></option>
					<option value="physical" <?php selected( $product_type, 'physical' ); ?>><?php esc_html_e( 'I plan to sell physical products', 'wc-calypso-bridge' ); ?></option>
					<option value="virtual" <?php selected( $product_type, 'virtual' ); ?>><?php esc_html_e( 'I plan to sell digital products', 'wc-calypso-bridge' ); ?></option>
				</select>
			</div>

			<input
				type="checkbox"
				id="woocommerce_sell_in_person"
				name="sell_in_person"
				value="yes"
				<?php checked( $sell_in_person, true ); ?>
			/>
			<label class="location-prompt" for="woocommerce_sell_in_person">
				<?php esc_html_e( 'I will also be selling products or services in person.', 'wc-calypso-bridge' ); ?>
			</label>
			<input type="checkbox" id="wc_tracker_checkbox" name="wc_tracker_checkbox" value="yes" />
			<?php $this->tracking_modal(); ?>
			<p class="wc-setup-actions step">
				<button class="button-primary button button-large button-next" value="<?php esc_attr_e( "Let's go!", 'wc-calypso-bridge' ); ?>" name="save_step"><?php esc_html_e( "Let's go!", 'wc-calypso-bridge' ); ?></button>
			</p>
		</form>
		<?php
	}

	/**
	 * Enqueue calypsoify scripts if
	 */
	public function enqueue_calypsoify_scripts() {
		$asset_path = WC_Calypso_Bridge::$plugin_asset_path ? WC_Calypso_Bridge::$plugin_asset_path : WC_Calypso_Bridge::MU_PLUGIN_ASSET_PATH;
		wp_register_script( 'wc-calypso-bridge-calypsoify-obw', $asset_path . 'assets/js/calypsoify-obw.js', array( 'jquery' ), WC_CALYPSO_BRIDGE_CURRENT_VERSION, true );
	}

	/**
	 * Get the URL for the next step's screen.
	 *
	 * @param string $step  slug (default: current step).
	 * @return string       URL for next step if a next step exists.
	 *                      Admin URL if it's the last step.
	 *                      Empty string on failure.
	 * @since 3.0.0
	 */
	public function get_next_step_link( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}
		$keys = array_keys( $this->steps );
		if ( end( $keys ) === $step ) {
			return admin_url( '/admin.php?page=wc-setup-checklist' );
		}
		$step_index = array_search( $step, $keys, true );
		if ( false === $step_index ) {
			return '';
		}
		return add_query_arg( 'step', $keys[ $step_index + 1 ], remove_query_arg( 'activate_error' ) );
	}

	/**
	 * Save step after continuing to next step
	 */
	public function save_step() {
		// @codingStandardsIgnoreStart
		if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
			WC_Calypso_Bridge::record_event( 'atomic_wc_obw_step_complete', array( 'name' => $this->step ) );
			if ( method_exists( $this, 'pre_' . $this->steps[ $this->step ]['handler'][1] ) ) {
				$pre_save_handler    = $this->steps[ $this->step ]['handler'];
				$pre_save_handler[1] = 'pre_' . $this->steps[ $this->step ]['handler'][1];
				call_user_func( $pre_save_handler, $this );
			}
			call_user_func( $this->steps[ $this->step ]['handler'], $this );
		}
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Activate geo-based shipping plugins automatically
	 */
	public function pre_wc_setup_store_setup_save() {
		if ( isset( $_POST['store_country'] ) ) { // WPCS: CSRF ok.
			$country = sanitize_text_field( $_POST['store_country'] ); // WPCS: Sanitization ok, input var ok.
			switch ( $country ) {
				case 'US':
					activate_plugin( 'woocommerce-shipping-usps/woocommerce-shipping-usps.php' );
					break;
				case 'CA':
					activate_plugin( 'woocommerce-shipping-canada-post/woocommerce-shipping-canada-post.php' );
					break;
				case 'AU':
					activate_plugin( 'woocommerce-shipping-australia-post/woocommerce-shipping-australia-post.php' );
					break;
				case 'GB':
					activate_plugin( 'woocommerce-shipping-royalmail/woocommerce-shipping-royalmail.php' );
					break;
			}
		}
		activate_plugin( 'woocommerce-shipping-ups/woocommerce-shipping-ups.php' );
		flush_rewrite_rules();

		// Always track usage for eCommerce plans: https://github.com/Automattic/wc-calypso-bridge/issues/361.
		$_POST['wc_tracker_checkbox'] = 'yes';
	}

	/**
	 * Skip the empty payment step if no gateways are present
	 */
	public function skip_empty_payment_step() {
		if ( isset( $_GET['step'] ) && 'payment' === $_GET['step'] ) { // WPCS: CSRF ok.
			$gateways = $this->get_wizard_in_cart_payment_gateways();
			if ( empty( $gateways ) ) {
				wp_safe_redirect( admin_url( 'admin.php?page=wc-setup-checklist' ) );
			}
		}
	}

}

new WC_Calypso_Bridge_Admin_Setup_Wizard();
