<?php

class WC_Calypso_Bridge_Free_Trial_Product_Form_Changes {
	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		// Remove pinterest tab.
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'remove_pinterest' ], 99);

		// remove "Sharing" meta box.
		add_filter( 'sharing_meta_box_show', '__return_false' );

		// Customize Channel visibility meta box.
		add_action( 'admin_enqueue_scripts', [ $this, 'add_scripts_for_google_listing_ads' ], 10, 10);
	}

	public function remove_pinterest( $tabs ) {
		if ( isset( $tabs[ 'pinterest_attributes' ] ) ) {
			unset( $tabs[ 'pinterest_attributes' ] );
		}
		return $tabs;
	}

	public function add_scripts_for_google_listing_ads( $hook ) {
	    global $post;
		if ( $hook === 'post-new.php' || $hook == 'post.php' ) {
			if ( $post->post_type === 'product' ) {
				add_action('admin_print_footer_scripts', function() {
					$status       = new \Automattic\Jetpack\Status();
					$site_suffix  = $status->get_site_suffix();
					$signup_link = "https://wordpress.com/plans/" . $site_suffix;
					$signup_text = __( 'Sign up for a plan', 'wc-calypso-bridge' );
					$description = __( 'To sync your products directly to Google, manage your product feed, and create Google Ad campaigns, all you need to do is sign up for a plan.', 'wc-calypso-bridge' );
					?>
						<script>
                            jQuery('.gla-channel-visibility-box a').text('<?=$signup_text?>').attr('href', '<?=$signup_link?>');
                            jQuery(jQuery('.gla-channel-visibility-box p')[1]).text('<?=$description?>');
						</script>
					<?php
				});
			}
		}
	}
}

WC_Calypso_Bridge_Free_Trial_Product_Form_Changes::get_instance();