<?php
/**
 * WooCommerce Calypso Bridge Navigation Learn More Note
 *
 * @package WC_Calypso_Bridge/Notes
 */

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;
use Automattic\WooCommerce\Admin\Loader;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Calypso_Bridge_Navigation_Learn_More_Note
 */
class WC_Calypso_Bridge_Navigation_Learn_More_Note {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-navigation-learn-more';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'update_option_woocommerce_navigation_enabled', array( $this, 'possibly_add_note' ) );
		add_action( 'add_option_woocommerce_navigation_enabled', array( $this, 'possibly_add_note' ) );
	}

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		if ( ! Loader::is_feature_enabled( 'navigation' ) ) {
			return;
		}

		$content = __( 'Introducing a streamlined, commerce-first navigation experience, to help you save time and find the things that matter.', 'wc-calypso-bridge' );

		$note = new Note();
		$note->set_title( __( 'Welcome your new WooCommerce Navigation', 'wc-calypso-bridge' ) );
		$note->set_content( $content );
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'wc-calypso-bridge' );
		$note->add_action( 'learn-more', __( 'Learn more', 'wc-calypso-bridge' ), 'https://wordpress.com/support/?page_id=177515' );
		return $note;
	}
}
