<?php
/**
 * Main File which is customized and changes make to a.k.a the login page
 * 
 * @package 	    LoginCustomizer
 * @author 			WPBrigade
 * @copyright 		Copyright (c) 2021, WPBrigade
 * @link 			https://loginpress.pro/
 * @license			https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * 
 */
namespace LoginCustomizer\Customizer\Panel;

 class Functions {
 
	function __construct() {

		 // Get plugin options array
		$options = get_option( 'login_customizer_options' );

		if( !is_customize_preview() ) {
			
			$logincust_setting = get_option( 'logincust_setting' );
			$is_auto_remeber_on = isset( $logincust_setting['auto_remember_me'] ) ? $logincust_setting['auto_remember_me'] : 'off';
	
			if ( 'off' != $is_auto_remeber_on ) {
				add_filter( 'login_footer', array( $this, 'logincust_always_checked_rememberme' ) );
			}
			add_filter( 'gettext', array( $this, 'change_username_label'), 20, 3 );
		}

		add_action( 'login_head', 	array( $this, 'login_page_custom_head' ) );


		// Hook to login_headerurl
		if ( ! empty( $options['logincust_logo_link'] ) ) {
			add_filter( 'login_headerurl', array( $this, 'logincust_login_logo_url' ), 99 );
		}

		/**
		 * Change login page title 
		 * @since 2.1.4
		 */
		if ( ! empty( $options['logincust_login_title'] ) ) {
			add_filter( 'login_title',	array( $this, 'logincust_login_page_title' ), 99 );
		}

		/**
		 * Compare WP version: login_headertitle was deprecated in WordPress 5.2
		 *
		 * @since 2.1.0
		 * 
		 **/
		if ( version_compare( $GLOBALS['wp_version'], '5.2', '<' ) ) {
			add_filter( 'login_headertitle', array( $this, 'logincust_login_logo_url_title' ) );
		} else {
			add_filter( 'login_headertext', array( $this, 'logincust_login_logo_url_title' ) );
		}


		// Hook to register and login_link_separator
		if ( ! is_customize_preview() ) {

			if ( ! empty( $options['logincust_field_register_link'] ) && $options['logincust_field_register_link'] === 1 ) {
				add_filter( 'register', array( $this, 'logincust_no_register_link' ) );
				add_filter( 'login_link_separator', array( $this, 'logincust_no_register_link' ) );
			}
			if ( ! empty( $options['logincust_field_lost_password'] ) && $options['logincust_field_lost_password'] === 1 ) {
				add_filter( 'login_link_separator', array( $this, 'logincust_no_register_link' ) );
			}
		}

	}

		
	/**
	 * Change login logo title attribute
	 * 
	 * @since 2.0.0
	 * 
	 */
	function logincust_login_logo_url_title() {
		// Get blog title
		$title = get_bloginfo( 'name', 'display' );

		// Return blog title
		return $title;
	}


	/**
	 * Change login logo URL
	 * 
	 * @since 2.0.0
	 *  
	 */

	function logincust_login_logo_url() {
		// Return logo link option
		$options = get_option( 'login_customizer_options' );

		return $options['logincust_logo_link'];
	}

	/**
	 * Change login page title
	 * @since 2.1.4
	 */

	function logincust_login_page_title() {
		
		// Return logo link option
		$logincust_setting 	= get_option( 'login_customizer_options' );
		$login_title 		= isset( $logincust_setting['logincust_login_title'] ) ? $logincust_setting['logincust_login_title'] : '';

		return $login_title;
	}
	


	/**
	 * Change Label of the Username from login Form.
	 * 
	 * @param  [string] $translated_text
	 * @param  [string] $text           
	 * @param  [string] $domain         
	 * @return string
	 * 
	 * @since 2.2.0
	 * @version 2.2.0
	 * 
	 */
	function change_username_label( $translated_text, $text, $domain ){

		$logincust_setting = get_option( 'logincust_setting' );
		$login_order 	= isset( $logincust_setting['login_order'] ) ? $logincust_setting['login_order'] : '';

		if ( $logincust_setting ) {

			$default = 'Username or Email Address';

			$login_order 	= isset( $logincust_setting['login_order'] ) ? $logincust_setting['login_order'] : '';

			// If the option does not exist, return the text unchanged.
			if ( ! $logincust_setting && $default === $text ) {
				return $translated_text;
			}

			// If options exsit, then translate away.
			if ( $logincust_setting && $default === $text ) {

				// Check if the option exists.
				if ( '' != $login_order && 'default' != $login_order ) {
					
					if ( 'username' == $login_order ) {
						$label = __( 'Username', 'login-customizer' );
					} elseif ( 'email' == $login_order ) {
						$label = __( 'Email Address', 'login-customizer' );
					} else {
						$label = __('Username or Email Address', 'login-customizer' );
					}

					$translated_text = esc_html( $label );
				} else {
					return $translated_text;
				}
			}
		}
		return $translated_text;
	}

	
	/**
	 * Remove register link
	 */
	function logincust_no_register_link( $url ) {
		return '';
	}


	/**
	 * logincust_always_checked_rememberme 
	 * 
	* @since 2.2.0
	* @version 2.0.0
	
	* @return void
	*/
	function logincust_always_checked_rememberme() {
		echo "<script>document.getElementById('rememberme').checked = true;</script>";
	}


	/**
	 * Manage the Login Head
	*
	* @since 2.0.0
	* @version 2.2.0
	* * * * * * * * * * * */
	function login_page_custom_head() {

		do_action( 'logincust_header_menu' );

	}

}






