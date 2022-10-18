<?php
/**
 * WooCommerce Calypso Bridge
 * Cart Checkout Blocks Inbox Note
 *
 * @package WC_Calypso_Bridge/Notes
 * @since   1.9.5
 * @version 1.9.5
 */

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Cart_Checkout_Blocks_Default_Inbox_Note
 */
class WC_Calypso_Bridge_Cart_Checkout_Blocks_Default_Inbox_Note {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-calypso-bridge-cart-checkout-blocks-default-inbox-note';

	/**
	 * Get the note.
	 *
	 * @return void|Note
	 * @todo Update note content and when to display the inbox note.
	 */
	public static function get_note() {
		if ( ! self::should_display_note() ) {
			return;
		}

		$note = new Note();
		$note->set_title( __( 'Meet our new, customizable checkout', 'wc-calypso-bridge' ) );
		$note->set_content(
			__(
				'To future-proof your store, we have enabled our brand-new, conversion-optimized Cart and Checkout Blocks. Please take a few minutes to review some important information on Extension compatibility. Then, go ahead and customize the Cart and Checkout pages to suit your needs.',
				'wc-calypso-bridge'
			)
		);
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'wc-calypso-bridge' );
		$note->add_action(
			'learn-more',
			__( 'Learn more', 'wc-calypso-bridge' ),
			'https://woocommerce.com/document/cart-checkout-blocks-support-status/'
		);

		return $note;
	}

	/**
	 * Returns true if we should display the note.
	 *
	 * @return Boolean
	 * @todo Set the correct Blocks version, after fixes have been released.
	 * @see  https://somewherewarmattic.wordpress.com/2022/10/07/enabling-cart-checkout-blocks-on-atomic-on-hold/
	 */
	public static function should_display_note() {

		if (
			class_exists( 'Automattic\WooCommerce\Blocks\Package' )
			&& version_compare( \Automattic\WooCommerce\Blocks\Package::get_version(), '7.0.0' ) > 0
			&& self::wc_admin_active_for( 2 * DAY_IN_SECONDS )
		) {
			return true;
		}

		return false;

	}

	/**
	 * Delete note if we shouldn't display it and not been actioned on.
	 *
	 * @return void
	 */
	public static function possibly_clear_note() {
		if ( ! self::should_display_note() && ! self::has_note_been_actioned() ) {
			self::possibly_delete_note();
		}
	}

}
