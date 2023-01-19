<?php
/**
 * Customizer controls for Background Section
 * 
 * @package 	    LoginCustomizer
 * @author 			WPBrigade
 * @copyright 		Copyright (c) 2021, WPBrigade
 * @link 			https://loginpress.pro/
 * @license			https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since 2.2.0
 *
 */
namespace LoginCustomizer\Customizer\Panel\Sections;

use LoginCustomizer\Customizer\Panel\Controls\Radio_Images;
use LoginCustomizer\Customizer\Panel\Sanitizers;

include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';

class Templates extends Sanitizers{
	
	function __construct( $wp_customize ) {

		$free_templates = array(
			'original'	=> LOGINCUST_FREE_URL . 'Customizer/Templates/Light/assets/original.png',
			'dark' 		=> LOGINCUST_FREE_URL . 'Customizer/Templates/Dark/assets/dark.png',
			'material'	=> LOGINCUST_FREE_URL . 'Customizer/Templates/Material/assets/material.png',
		);

		$wp_customize->add_section(
			'logincust_templates',
			array(
				'priority' => 0,
				'title' => __( 'Templates', 'login-customizer' ),
				'panel'  => 'logincust_panel',
			)
		);

		$templates = apply_filters( 'pro_theme_inclusion', $free_templates );

		$wp_customize->add_setting(
			'login_customizer_options[logincust_templates_control]',
			array(
				'default' => 'original',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => function( $input, $setting ) {
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
				},
			)
		);


		$wp_customize->add_control(
			new Radio_Images(
				$wp_customize,
				'login_customizer_options[logincust_templates_control]',
				array(
					'label' => __( 'Templates', 'login-customizer' ),
					'section' => 'logincust_templates',
					'priority' => 5,
					'settings' => 'login_customizer_options[logincust_templates_control]',
					'choices' => $templates
				)
			)
		);
	}

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
}