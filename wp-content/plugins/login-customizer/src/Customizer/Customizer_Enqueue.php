<?php


/**
 * Enqeueue Scripts and Styles for Plugin
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
 * Class to enqueue CSS and JS for Pro-templates.
 *
 * @since  2.2.0
 * @access public
 */

class Customizer_Enqueue {

	/**
	 * Enqueue script to Customizer
	 */
	function logincust_customizer_script() {
			add_action( 'customize_controls_print_scripts',array( $this, 'Enqueue_js_script' ) );
	
	}

	public function Enqueue_js_script(){

			// Enqueue script to Customizer
			wp_enqueue_script( 'logincust_controls_free_js', LOGINCUST_FREE_URL . '/Templates/js/customizer.js', array( 'jquery' ), null, true );

				// Generate the redirect url.
				$options = get_option( 'login_customizer_settings', array() );
		
				$localize = array(
					'page' => get_permalink( $options['page'] ),
					'url' => LOGINCUST_FREE_URL,
				);
				
				// Localize Script
				wp_localize_script( 'logincust_controls_free_js', 'logincust_free', $localize );
	}

}
