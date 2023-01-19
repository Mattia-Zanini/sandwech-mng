<?php
/**
 * Customizer controls for Logo Section
 * 
 * @package 	    LoginCustomizer
 * @author 			WPBrigade
 * @copyright 		Copyright (c) 2021, WPBrigade
 * @link 			https://loginpress.pro/
 * @license			https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since 2.2.0
 * @version 2.2.0
 *
 */
namespace LoginCustomizer\Customizer\Panel\Sections;

use LoginCustomizer\Customizer\Panel\Controls\Toggle;
use LoginCustomizer\Customizer\Panel\Controls\Range_Slider;

/**
 * Logo Section in customizer
 * 
 * @version 2.2.0
 */
class Logo{
	
	function __construct( $wp_customize ) {

		$wp_customize->add_section(
			'logincust_logo_section',
			array(
				'priority' => 10,
				'title' => __( 'Logo', 'login-customizer' ),
				'panel'  => 'logincust_panel',
			)
		);
		
		$wp_customize->add_setting(
			'login_customizer_options[logincust_logo_show]',
			array(
				'default' => false,
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			)
		);
		
		$wp_customize->add_control(
			new Toggle(
				$wp_customize,
				'login_customizer_options[logincust_logo_show]',
				array(
					'label' => __( 'Disable Logo?', 'login-customizer' ),
					'section' => 'logincust_logo_section',
					'priority' => 5,
					'settings' => 'login_customizer_options[logincust_logo_show]',
				)
			)
		);
		
		$wp_customize->add_setting(
			'login_customizer_options[logincust_logo]',
			array(
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
				'transport' => 'postMessage',
			)
		);
		
		$wp_customize->add_control(
			new \WP_Customize_Image_Control(
				$wp_customize,
				'login_customizer_options[logincust_logo]',
				array(
					'label' => __( 'Logo', 'login-customizer' ),
					'section' => 'logincust_logo_section',
					'priority' => 10,
					'settings' => 'login_customizer_options[logincust_logo]',
				)
			)
		);
		
		$wp_customize->add_setting(
			'login_customizer_options[logincust_logo_width]',
			array(
				'default' => '84px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);
		
		$wp_customize->add_control(
			new Range_Slider(
				$wp_customize,
				'login_customizer_options[logincust_logo_width]',
				array(
					'label' => __( 'Logo Width', 'login-customizer' ),
					'section' => 'logincust_logo_section',
					'priority' => 15,
					'settings' => 'login_customizer_options[logincust_logo_width]',
					'choices' => array(
						'percent' => true,
					),
					'input_attrs' => array(
						'min'    => 0,
						'max'    => 1000,
						'step'   => 1,
					),
				)
			)
		);
		
		$wp_customize->add_setting(
			'login_customizer_options[logincust_logo_height]',
			array(
				'default' => '84px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);
		
		$wp_customize->add_control(
			new Range_Slider(
				$wp_customize,
				'login_customizer_options[logincust_logo_height]',
				array(
					'label' => __( 'Logo Height', 'login-customizer' ),
					'section' => 'logincust_logo_section',
					'priority' => 20,
					'settings' => 'login_customizer_options[logincust_logo_height]',
					'choices' => array(
						'percent' => true,
					),
					'input_attrs' => array(
						'min'    => 0,
						'max'    => 1000,
						'step'   => 1,
					),
				)
			)
		);
		
		$wp_customize->add_setting(
			'login_customizer_options[logincust_logo_padding]',
			array(
				'default' => '5px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);
		
		$wp_customize->add_control(
			new Range_Slider(
				$wp_customize,
				'login_customizer_options[logincust_logo_padding]',
				array(
					'label' => __( 'Padding Bottom', 'login-customizer' ),
					'section' => 'logincust_logo_section',
					'priority' => 25,
					'settings' => 'login_customizer_options[logincust_logo_padding]',
					'choices' => array(
						'percent' => true,
					),
					'input_attrs' => array(
						'min'    => 0,
						'max'    => 1000,
						'step'   => 1,
					),
				)
			)
		);
		
		$wp_customize->add_setting(
			'login_customizer_options[logincust_logo_link]',
			array(
				'default' => 'https://wordpress.org/',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		
		$wp_customize->add_control(
			'login_customizer_options[logincust_logo_link]',
			array(
				'label' => __( 'Logo URL', 'login-customizer' ),
				'description' => __( 'The page where your logo will take you.', 'login-customizer' ),
				'section' => 'logincust_logo_section',
				'priority' => 30,
				'settings' => 'login_customizer_options[logincust_logo_link]',
			)
		);
		$wp_customize->add_setting(
			'login_customizer_options[logincust_login_title]',
			array(
				'default' => '',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
			)
		);

		$wp_customize->add_control(
			'login_customizer_options[logincust_login_title]',
			array(
				'label' => __( 'Login Page title', 'login-customizer' ),
				'description' => __( 'Login page title that is shown on WordPress login page.', 'login-customizer' ),
				'section' => 'logincust_logo_section',
				'priority' => 30,
				'settings' => 'login_customizer_options[logincust_login_title]',
			)
		);
	}
}