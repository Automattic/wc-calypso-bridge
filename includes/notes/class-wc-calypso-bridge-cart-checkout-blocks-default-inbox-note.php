<?php
/**
 * WooCommerce Calypso Bridge
 * Cart Checkout Blocks Inbox Note
 *
 * @package WC_Calypso_Bridge/Notes
 * @since   1.9.8
 * @version 1.9.8
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
	 */
	public static function get_note() {

		// Note is added from the woocommerce_create_pages one-time operation.
		$note = new Note();
		$note->set_title( __( 'Meet our new, customizable checkout', 'wc-calypso-bridge' ) );
		$note->set_content(
			__(
				'To deliver a smooth checkout experience to your shoppers, we have supercharged your store with our brand-new, conversion-optimized checkout. Please take a few minutes to review some important information on Extension compatibility with the new Cart and Checkout Blocks. Then, go ahead and customize your store\'s Cart and Checkout pages.',
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

}
