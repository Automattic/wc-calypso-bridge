<?php

/**
 * Class WC_Calypso_Bridge_Free_Trial_Orders_Notice.
 *
 * @since   1.9.16
 * @version 1.9.16
 *
 * Renders an admin notice on Orders page.
 */
class WC_Calypso_Bridge_Free_Trial_Orders_Notice  {
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

	public function __construct(){
		add_action('admin_notices', function() {
			$screen = get_current_screen();
			if ( 'edit-shop_order' === $screen->id ) {
				?>
				<div class="free-trial-orders-notice notice notice-info">
                    <div>
                        <h3><?=__('Start selling to everyone', 'wc-calypso-bridge')?></h3>
                        <p>
							<?=__("During the trial period you can only place test orders! To receive orders from customers, upgrade to a paid plan and you'll be ready to start selling.", 'wc-calypso-bridge')?>
                        </p>
                    </div>
					<div class="upgrade-action">
						<a href="<?=$this->get_action_url()?>" class="button is-primary"><?=__('Upgrade now', 'wc-calypso-bridge')?></a>
					</div>

				</div>
				<?php
			}
		});
	}

	/**
	 * Action URL.
	 *
	 * @return string
	 */
	public function get_action_url() {
		$status      = new \Automattic\Jetpack\Status();
		$site_suffix = $status->get_site_suffix();

		return sprintf( "https://wordpress.com/plans/%s", $site_suffix );
	}

}

WC_Calypso_Bridge_Free_Trial_Orders_Notice::get_instance();