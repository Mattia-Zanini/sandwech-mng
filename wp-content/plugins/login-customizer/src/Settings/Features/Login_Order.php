<?php

/**
* login Customizer Login_Order.
*
* Enable user to login using their username and/or email address.
 * 
 * @package 	    LoginCustomizer
 * @author 			WPBrigade
 * @copyright 		Copyright (c) 2021, WPBrigade
 * @link 			https://loginpress.pro/
 * @license			https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * 
 * @since 2.2.0
 */

namespace LoginCustomizer\Settings\Features;
use WP_Error;

class Login_Order {

	/**
	* Variable that Check for login customizer settings.
	* @access public
	* @var string
	*/
	public $logincust_key;

	/* * * * * * * * * *
	* Class constructor
	* * * * * * * * * */
	public function __construct() {

		$this->logincust_key = get_option( 'logincust_setting' );
		$this->login_order();
	}

	/**
	 * Custom Login Order
	 * Lets you change the order of login to just email or username
	 *
	 * @return void
	 */
	public function login_order() {

		$wp_version = get_bloginfo( 'version' );
		$logincust_setting = get_option( 'logincust_setting' );
		$login_order = isset(	$logincust_setting['login_order'] ) ? $logincust_setting['login_order'] : 'default';

		if( 'default' != $login_order ) {
			remove_filter( 'authenticate', 	'wp_authenticate_username_password', 20, 3 );
			add_filter( 'authenticate', array( $this, 'logincust_login_order' ), 20, 3 );
		}

		if ( 'username' == $login_order && '4.5.0' < $wp_version ) {
			// For WP 4.5.0 remove email authentication.
			remove_filter( 'authenticate', 'wp_authenticate_email_password', 20 );
		}
	}

	/**
	* If an email address is entered in the username field, then look up the matching username and authenticate as per normal, using that.
	*
	* @param string $user
	* @param string $username
	* @param string $password
	* @since 2.2.0
	* @return Results of autheticating via wp_authenticate_username_password(), using the username found when looking up via email.
	*/
	function logincust_login_order( $user, $username, $password ) {

		if ( $user instanceof \WP_User ) {
			return $user;
		}

		// Is username or password field empty?
		if ( empty( $username ) || empty( $password ) ) {

			if ( is_wp_error( $user ) )
				return $user;

			$error = new WP_Error();

			$empty_username	= isset( $this->logincust_key['empty_username'] ) && ! empty( $this->logincust_key['empty_username'] ) ? $this->logincust_key['empty_username'] : sprintf( __( '%1$sError:%2$s The username field is empty.', 'logincust' ), '<strong>', '</strong>' );

			$empty_password	= isset( $this->logincust_key['empty_password'] ) && ! empty( $this->logincust_key['empty_password'] ) ? $this->logincust_key['empty_password'] : sprintf( __( '%1$sError:%2$s The password field is empty.', 'logincust' ), '<strong>', '</strong>' );

			if ( empty( $username ) )
				$error->add( 'empty_username', $empty_username );

			if ( empty( $password ) )
				$error->add( 'empty_password', $empty_password );

			return $error;
		} // close empty_username || empty_password.

		$logincust_setting = get_option( 'logincust_setting' );
		$login_order = isset(	$logincust_setting['login_order'] ) ? $logincust_setting['login_order'] : 'default';

		// Is login order is set to be 'email'.
		if ( 'email' == $login_order ) {

			if ( ! empty( $username ) && ! is_email( $username ) ) {

				$error = new WP_Error();

				$force_email_login= isset( $this->logincust_key['force_email_login'] ) && ! empty( $this->logincust_key['force_email_login'] ) ? $this->logincust_key['force_email_login'] : sprintf( __( '%1$sError:%2$s Invalid Email Address', 'logincust' ), '<strong>', '</strong>' );

				$error->add( 'logincust_use_email', $force_email_login );

				return $error;
			}

			if ( ! empty( $username ) && is_email( $username ) ) {

				$username = str_replace( '&', '&amp;', stripslashes( $username ) );
				$user = get_user_by( 'email', $username );

				if ( isset( $user, $user->user_login, $user->user_status ) && 0 === intval( $user->user_status ) )
				$username = $user->user_login;
				return wp_authenticate_username_password( null, $username, $password );
			}
		} // login order 'email'.

		// Is login order is set to be 'username'.
		if ( 'username' == $login_order ) {

			$user = get_user_by('login', $username);

			$invalid_usrname = array_key_exists( 'incorrect_username', $this->logincust_key ) && ! empty( $this->logincust_key['incorrect_username'] ) ? $this->logincust_key['incorrect_username'] : sprintf( __( '%1$sError:%2$s Invalid Username.', 'logincust' ), '<strong>', '</strong>' );

			if ( ! $user ) {
				return new WP_Error( 'invalid_username', $invalid_usrname );
			}

			if ( ! empty( $username ) || ! empty( $password ) ) {

				$username = str_replace( '&', '&amp;', stripslashes( $username ) );
				$user = get_user_by( 'login', $username );

				if ( isset( $user, $user->user_login, $user->user_status ) && 0 === intval( $user->user_status ) )
				$username = $user->user_login;
				if ( ! empty( $username ) && is_email( $username ) ) {
					return wp_authenticate_username_password( null, "", "" );
				} else {
					return wp_authenticate_username_password( null, $username, $password );
				}
			}
		} // login order 'username'.
	}
} // End Of Class.

