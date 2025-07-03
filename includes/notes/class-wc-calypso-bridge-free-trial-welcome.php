<?php
/**
 * WooCommerce Calypso Bridge
 * Free Trial Welcome Note
 *
 * @package WC_Calypso_Bridge/Notes
 * @since   2.2.20
 * @version 2.2.20
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
		$plan_name = 'Entrepreneur';

		/* translators: %s: trial plan name, e.g. Entrepreneur */
		$note_title = sprintf( __( 'Your %s free trial has just started', 'wc-calypso-bridge' ), $plan_name );

		/* translators: %s: trial plan name, e.g. Entrepreneur */
		$note_content = sprintf(
			__( 'Welcome to your 14-day free trial of the %s plan – everything you need to start and grow a successful online business, all in one place. The journey toward your first sale has just begun! Ready to explore?', 'wc-calypso-bridge' ),
			$plan_name
		);

		$note = new Note();
		$note->set_title( $note_title );
		$note->set_content( $note_content );
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'wc-calypso-bridge' );
		$note->add_action(
			'woo-express-add-product',
			__( 'Create your first product', 'wc-calypso-bridge' ),
			admin_url( 'admin.php?page=wc-admin&task=products' )
		);

		return $note;
	}

	/**
	 * Checks if the note can and should be added.
	 *
	 * @throws NotesUnavailableException Throws exception when notes are unavailable.
	 * @return bool
	 */
	public static function can_be_added() {
		$note = self::get_note();

		if ( ! $note instanceof Note && ! $note instanceof WC_Admin_Note ) {
			return;
		}

		if ( self::note_exists() ) {
			return false;
		}

		// Free trial plan.
		if ( ! wc_calypso_bridge_is_ecommerce_trial_plan() ) {
			return false;
		}

		return true;
	}
}
