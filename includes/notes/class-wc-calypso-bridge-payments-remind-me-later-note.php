<?php
/**
 * WooCommerce Calypso Bridge Payments Remind Me Later Note
 *
 * @package WC_Calypso_Bridge/Notes
 */

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;
use Automattic\WooCommerce\Admin\Loader;
use Automattic\WooCommerce\Admin\PluginsHelper;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Payments_Remind_Me_Later_Note
 */
class WC_Calypso_Bridge_Payments_Remind_Me_Later_Note {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-calypso-bridge-payments-remind-me-later';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		// Installed WCPay.
		$installed_plugins = PluginsHelper::get_active_plugin_slugs();
		if ( in_array( 'woocommerce-payments', $installed_plugins ) ) {
			return;
		}

		// Dismissed WCPay welcome page.
		if ( 'yes' === get_option( 'wc_calypso_bridge_payments_dismissed', 'no' ) ) {
			return;
		}

		// Less than 3 days since viewing welcome page.
		$view_timestamp = get_option( 'wc_calypso_bridge_payments_view_welcome_timestamp', false );
		if ( ! $view_timestamp ||
			( time() - $view_timestamp < 3 * DAY_IN_SECONDS )
		) {
			return;
		}

		$content = __( 'Save up to 50% in fees by managing transactions in WooCommerce Payments. With WooCommerce Payments, you can securely accept major cards, Apple Pay, and payments in over 100 currencies.', 'wc-calypso-bridge' );

		$note = new Note();
		$note->set_title( __( 'Save big with WooCommerce Payments', 'wc-calypso-bridge' ) );
		$note->set_content( $content );
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'wc-calypso-bridge' );
		$note->add_action( 'learn-more', __( 'Learn more', 'wc-calypso-bridge' ), admin_url( 'admin.php?page=wc-admin&path=/payments-welcome' ) );
		return $note;
	}
}
