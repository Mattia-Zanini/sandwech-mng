<?php
/**
 * Essential tools for Plugin
 * 
 * @package 	    LoginCustomizer
 * @author 			WPBrigade
 * @copyright 		Copyright (c) 2021, WPBrigade
 * @link 			https://loginpress.pro/
 * @license			https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace LoginCustomizer;

/**
 * Constant class.
 *
 * @since  2.2.0
 * @version 2.2.0
 * @access public
 */

class Essentials {

	/**
	 * Plugin Version
	 *
	 * @var string
	 */
	public $version = '2.2.0';

	/**
	 * Class Essentials construct
	 * @version 2.2.0
	 */
	public function __construct() {

		$this->define_constants();
		add_action( 'plugins_loaded', array($this, 'load_text_domain' ) );
	}

	/**
	 * Defining Constants 
	 *
	 * @since  1.0.0
	 * @version 2.2.0 
	 * @access public
	 * @return void
	 */
	public function define_constants() {

		$this->define( 'LOGINCUST_FREE_URL', plugin_dir_url( __FILE__ ) );
		$this->define( 'LOGINCUST_DIR_PATH', plugin_dir_path( __FILE__ ) );
		$this->define( 'LOGINCUST_FREE_VERSION', $this->version );
		$this->define( 'LOGINCUST_FEEDBACK_SERVER', 'https://wpbrigade.com/' );
		$this->define( 'LOGINCUST_FREE_RESOURCES', plugins_url( 'resources', dirname( __FILE__ ) ) );
	}

	/**
	 * Plugin Translation languages
	 * @version 2.2.0
	 * 
	 * @return void
	 */
	public function load_text_domain() {

		load_plugin_textdomain( 'login-customizer', false, LOGINCUST_FREE_RESOURCES . '/languages/' );
	}

	/**
    * Callback to Define constant if not already set
    * @param  string $name
    * @param  string|bool $value
    */
    private function define( $name, $value ) {
      if ( ! defined( $name ) ) {
        define( $name, $value );
      }
	}
}

