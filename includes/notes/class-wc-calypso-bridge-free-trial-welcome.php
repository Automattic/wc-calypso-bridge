<?php
/**
 * WooCommerce Calypso Bridge
 * Free Trial Welcome Note
 *
 * @package WC_Calypso_Bridge/Notes
 * @since   x.x.x
 * @version x.x.x
 */

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Free_Trial_Welcome_Note
 */
class WC_Calypso_Bridge_Free_Trial_Welcome_Note {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-calypso-bridge-free-trial-welcome';

	/**
	 * Get the note.
	 *
	 * @return void|Note
	 */
	public static function get_note() {

		// Note is added from the woocommerce_create_pages one-time operation.
		$note = new Note();
		$note->set_title( __( 'Your Woo Express free trial has just started', 'wc-calypso-bridge' ) );
		$note->set_content(
			__(
				'Welcome to your 14-day free trial of Woo Express – everything you need to start and grow a successful online business, all in one place. The journey toward your first sale has just begun! Ready to explore?',
				'wc-calypso-bridge'
			)
		);
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'wc-calypso-bridge' );
		$note->add_action(
			'add-product',
			__( 'Create your first product', 'wc-calypso-bridge' ),
			admin_url( 'admin.php?page=wc-admin&task=products' )
		);

		return $note;
	}
}
