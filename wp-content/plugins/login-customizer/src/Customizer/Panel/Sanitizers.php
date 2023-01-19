<?php
/**
 * Custom sanitizers for Customizer controls
 */

/**
 * Sanitizer's Claas for Customizer Controls
 * 
 * @package 	    LoginCustomizer
 * @author 			WPBrigade
 * @copyright 		Copyright (c) 2021, WPBrigade
 * @link 			https://loginpress.pro/
 * @license			https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * 
 */
namespace LoginCustomizer\Customizer\Panel;
include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';

 class Sanitizers {

	static function radio_option( $input, $setting ) {
		// global wp_customize
		global $wp_customize;
	
		// Get control ID
		$control = $wp_customize->get_control( $setting->id );

		// Check if option exists in choice array
		if ( array_key_exists( $input, $control->choices ) ) {
			// If it does, return the value
			return $input;
		} else {
			// Else, return default value
			return $setting->default;
		}
	}
	
	/**
	 * Sanitizer for Background Position Control
	 */
	function logincust_sanitize_position( $input, $setting ) {
		// Check if value is one of the positions
		if ( in_array( $input, array(  'top', 'bottom', 'left', 'right', 'center' ), true ) ) {
			// If it does, return the value
			return $input;
		} else {
			// Else, return default value
			return $setting->default;
		}
	}
 }
