<?php
/**
 * Essential things for Plugin
 *
 * Defines the plugin constants.
 *
* @author 			WPBrigade
* @copyright 		Copyright (c) 2021, WPBrigade
* @link 			https://loginpress.pro/
* @license			https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace LoginCustomizer\Customizer;

/**
 * Constant class.
 *
 * @since  2.2.0
 * @version 2.2.0
 * @access public
 */

class Create_Customizer {


	public function __construct() {


	}

	public function customizer_settings_creation() {

		require_once( plugin_dir_path( __FILE__ ) . 'Setup.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'Include_Page_Template.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'Panel/customizer.php' );
	}
}