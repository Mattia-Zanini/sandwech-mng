<?php
if ( ! defined( 'ABSPATH' ) ) {
  // Exit if accessed directly.
  exit;
}

/**
* Handling all the AJAX calls in Login Customizer.
*
* @since 2.2.0
* @version 2.1.5

* @class Deactivate_Login_Customizer
*/

if ( ! class_exists( 'Deactivate_Login_Customizer' ) ) :

  class Deactivate_Login_Customizer {

	/* * * * * * * * * *
	* Class constructor
	* * * * * * * * * */
	public function __construct() {

		$this::init();
	}

	/**
	 * Ajax Calls for Deactivation box
	 *
	 * @return void
	 * 
	 */
	public static function init() {

		$ajax_calls = array(
			// 'deactivate'       => false,
		);

		foreach ( $ajax_calls as $ajax_call => $no_priv ) {

			add_action( 'wp_ajax_login_customizer_' . $ajax_call, array( __CLASS__, $ajax_call ) );

			if ( $no_priv ) {
				add_action( 'wp_ajax_nopriv_login_customizer_' . $ajax_call, array( __CLASS__, $ajax_call ) );
			}
		}
	}


	/**
	 * [deactivate get response from user on deactivating plugin]
	 * @return [string] [response]
	 * @since   2.2.0
	 * @version 2.2.0
	 */
	public function deactivate() {

		check_ajax_referer( 'login-customizer-deactivate-nonce', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'No cheating, huh!' );
		}

		$email         = get_option( 'admin_email' );
		$_reason       = sanitize_text_field( wp_unslash( $_POST['reason'] ) );
		$reason_detail = sanitize_text_field( wp_unslash( $_POST['reason_detail'] ) );
		$reason        = '';

		/*
		**  I upgraded to login-customizer Pro
		*
		*   The above option doesn't send response to server that's why it is omitted.
		*
		**/


		if ( $_reason == '1' ) {
			$reason = 'I only needed the plugin for a short period';
		} elseif ( $_reason == '2' ) {
			$reason = 'I found a better plugin';
		} elseif ( $_reason == '3' ) {
			$reason = 'The plugin broke my site';
		} elseif ( $_reason == '4' ) {
			$reason = 'The plugin suddenly stopped working';
		} elseif ( $_reason == '5' ) {
			$reason = 'I no longer need the plugin';
		} elseif ( $_reason == '6' ) {
			$reason = 'It\'s a temporary deactivation. I\'m just debugging an issue.';
		} elseif ( $_reason == '7' ) {
			$reason = 'Other';
		}
		$fields = array(
			'email' 		    => $email,
			'website' 			=> get_site_url(),
			'action'            => 'Deactivate',
			'reason'            => $reason,
			'reason_detail'     => $reason_detail,
			'blog_language'     => get_bloginfo( 'language' ),
			'wordpress_version' => get_bloginfo( 'version' ),
			'php_version'       => PHP_VERSION,
			'plugin_version'    => LOGINCUST_FREE_VERSION,
			'plugin_name' 	    => 'Login Customizer Free',
		);

		$response = wp_remote_post( LOGINCUST_FEEDBACK_SERVER, array(
			'method'      => 'POST',
			'timeout'     => 5,
			'httpversion' => '1.0',
			'blocking'    => false,
			'headers'     => array(),
			'body'        => $fields,
		) );

		wp_die();
	}

  }

endif;
new Deactivate_Login_Customizer();
?>
