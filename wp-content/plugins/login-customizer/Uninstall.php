<?php
/**
 * Uninstall Login Customizer
 *
 * @since 2.1.6
 */

// if uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Get global wpdb.
global $wpdb;

$logincust_settings = get_option( 'logincust_setting' );

// If not a multisite.
if ( ! is_multisite() ) {

	if ( isset( $logincust_settings ) && isset( $logincust_settings['logincust_delete_all'] ) && 'on' === $logincust_settings['logincust_delete_all'] ) {
		delete_option( 'login_customizer_options' );
		delete_option( 'logincust_setting' );
	}

	$page_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_name = 'login-customizer' LIMIT 1;" );

	if ( $page_id ) {
		wp_delete_post( $page_id, true );
	}

	delete_option( 'login_customizer_settings' );
	delete_option( 'logincustomizer_review_dismiss' );
	delete_option( 'logincustomizer_active_time' );

} else {

	// if multisite then go through each blog and remove the page and its settings accordingly.
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

	foreach ( $blog_ids as $blog_id ) {

		// Switch to blogs if there are more than One(1).
		switch_to_blog( $blog_id );

		$page_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_name = 'login-customizer' LIMIT 1;" );
		if ( $page_id ) {
			wp_delete_post( $page_id, true );
		}

		if ( isset( $logincust_settings ) && isset( $logincust_settings['logincust_delete_all'] ) && 'on' === $logincust_settings['logincust_delete_all'] ) {
			delete_option( 'login_customizer_options' );
			delete_option( 'login_customizer_settings' );
			delete_option( 'logincust_setting' );
		}

		delete_option( 'logincustomizer_review_dismiss' );
		delete_option( 'logincustomizer_active_time' );

		restore_current_blog();

	}
}
