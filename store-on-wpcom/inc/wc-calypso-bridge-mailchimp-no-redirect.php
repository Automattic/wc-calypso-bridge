<?php
/**
 * Prevent redirection during MailChimp activation.
 *
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'option_mailchimp_woocommerce_plugin_do_activation_redirect', '__return_false' );
