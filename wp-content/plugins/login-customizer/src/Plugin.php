<?php
/**
 * Main Plugin File to run Everything
 *
 * Runs every main function
 *
 * @author 			WPBrigade
 * @copyright 		Copyright (c) 2021, WPBrigade
 * @link 			https://loginpress.pro/
 * @license			https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace LoginCustomizer;

use LoginCustomizer\Essentials;
use LoginCustomizer\Includes\Plugin_Meta;
use LoginCustomizer\Includes\Notification;
use LoginCustomizer\Settings\Setup;
use LoginCustomizer\Customizer\Create_Customizer;
use LoginCustomizer\Settings\Features\Login_Order;
use LoginCustomizer\Settings\Features\Custom_Register_Password;
/**
 * Constant class.
 *
 * @since  2.2.0
 * @version 2.2.0
 * @access public
 */

class Plugin {

	function __construct() {

		/**
		 * Instance of Essentials Class for Defining Variables
		 */
		add_action( 'init', function() {
			new Essentials;
		}, 1 );

		// Customizer Settings Creation
		$customizer_settings = new Create_Customizer;
		$customizer_settings->customizer_settings_creation();

		/**
		 * Plugin Settings API and Plugin Meta
		 */
		$settings = new Setup;

		// PLugin Meta in Plugins.php
		$plugin_meta = new Plugin_Meta;
		$plugin_meta->hooks();

		/**
		 * Settings
		 */
		new Notification();
		$logincust_setting     = get_option( 'logincust_setting' );
		$login_order           = isset( $logincust_setting['login_order'] ) ? $logincust_setting['login_order'] : '';
		$enable_reg_pass_field = isset( $logincust_setting['enable_reg_pass_field'] ) ? $logincust_setting['enable_reg_pass_field'] : 'off';

		/**
		 * Custom Register Fields if option is enbled from Login Customizer and WordPress Settings.
		 */
		if ( 'off' != $enable_reg_pass_field && get_option( 'users_can_register' ) !== '0' ) {
			new Custom_Register_Password;
		}

		 /**
		 * Check if the language is downloaded and WordPress has 5.9 or higher version.
		 *
		 * @since 2.1.7
		 */
		if ( version_compare( $GLOBALS['wp_version'], '5.9', '>=' ) && ! empty( get_available_languages() ) ) {
			$enable_lang_switcher 	= isset( $logincust_setting['enable_language_switcher'] ) ? $logincust_setting['enable_language_switcher'] : 'off';

			/**
			 * Filters the Languages select input activation on the login screen.
			 *
			 * @since 2.1.7
			 * @param bool Whether to display the Languages select input on the login screen.
			 */
			if ( 'off' !== $enable_lang_switcher ) {
				add_filter( 'login_display_language_dropdown', '__return_false' );
			} else {
				add_filter( 'login_display_language_dropdown', '__return_true' );
			}
		}

		//Login Order
		if ( 'default' != $login_order ) {
			new Login_Order();
		}

	}
}
