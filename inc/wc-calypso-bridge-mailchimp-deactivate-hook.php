<?php
/**
 * Clear out MailChimp tables on deactivation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_deactivation_hook( 'mailchimp-for-woocommerce/mailchimp-woocommerce.php', 'wc_calypso_bridge_clear_mailchimp_tables' );

function wc_calypso_bridge_clear_mailchimp_tables() {
	global $wpdb;
	$log = new WC_Logger();

	$mailchimp_tables = array(
		"{$wpdb->prefix}queue",
		"{$wpdb->prefix}failed_jobs",
		"{$wpdb->prefix}mailchimp_carts",
	);

	foreach( $mailchimp_tables as $table ) {
		$sql = "TRUNCATE TABLE `$table`";

		if ( $wpdb->query( $sql ) ) {
			$log->add( 'mailchimp-for-woocommerce', "Plugin Deactivated: success clearing `$table` table." );
		} else {
			$log->add( 'mailchimp-for-woocommerce', "Plugin Deactivated: FAILURE clearing `$table` table." );
		}
	}
}
