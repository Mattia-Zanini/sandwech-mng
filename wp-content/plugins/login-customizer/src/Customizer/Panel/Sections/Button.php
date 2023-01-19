<?php
/**
 * Customizer controls for Button Section
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

use LoginCustomizer\Customizer\Panel\Controls\Alpha;
use LoginCustomizer\Customizer\Panel\Controls\Padding;
use LoginCustomizer\Customizer\Panel\Controls\Range_Slider;

include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';

/**
 * Button Section in customizer
 * 
 * @version 2.2.0
 */

class Button{
	
	function __construct( $wp_customize ) {
				
		$wp_customize->add_section(
			'logincust_button_section',
			array(
				'priority' => 30,
				'title' => __( 'Button', 'login-customizer' ),
				'panel'  => 'logincust_panel',
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_button_bg]',
			array(
				'default' => '#2EA2CC',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				'login_customizer_options[logincust_button_bg]',
				array(
					'label' => __( 'Background', 'login-customizer' ),
					'section' => 'logincust_button_section',
					'priority' => 5,
					'settings' => 'login_customizer_options[logincust_button_bg]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_button_hover_bg]',
			array(
				'default' => '#1E8CBE',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				'login_customizer_options[logincust_button_hover_bg]',
				array(
					'label' => __( 'Background (Hover)', 'login-customizer' ),
					'section' => 'logincust_button_section',
					'priority' => 10,
					'settings' => 'login_customizer_options[logincust_button_hover_bg]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_button_height_width]',
			array(
				'default' => 'auto',
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
				'transport' => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'login_customizer_options[logincust_button_height_width]',
			array(
				'label' => __( 'Button Size', 'login-customizer' ),
				'section' => 'logincust_button_section',
				'type' => 'select',
				'choices' => array(
					'auto' => __( 'Auto', 'login-customizer' ),
					'custom' => __( 'Custom', 'login-customizer' ),
				),
				'priority' => 15,
				'settings' => 'login_customizer_options[logincust_button_height_width]',
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_button_width_size]',
			array(
				'default' => '63px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Range_Slider(
				$wp_customize,
				'login_customizer_options[logincust_button_width_size]',
				array(
					'label' => __( 'Width', 'login-customizer' ),
					'section' => 'logincust_button_section',
					'priority' => 20,
					'settings' => 'login_customizer_options[logincust_button_width_size]',
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
			'login_customizer_options[logincust_button_height_size]',
			array(
				'default' => '32px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Range_Slider(
				$wp_customize,
				'login_customizer_options[logincust_button_height_size]',
				array(
					'label' => __( 'Height', 'login-customizer' ),
					'section' => 'logincust_button_section',
					'priority' => 25,
					'settings' => 'login_customizer_options[logincust_button_height_size]',
					'choices' => array(
						'percent' => false,
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
			'login_customizer_options[logincust_button_font_size]',
			array(
				'default' => '13px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Range_Slider(
				$wp_customize,
				'login_customizer_options[logincust_button_font_size]',
				array(
					'label' => __( 'Font Size', 'login-customizer' ),
					'section' => 'logincust_button_section',
					'priority' => 30,
					'settings' => 'login_customizer_options[logincust_button_font_size]',
					'choices' => array(
						'percent' => false,
					),
					'input_attrs' => array(
						'min'    => 0,
						'max'    => 100,
						'step'   => 1,
					),
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_button_color]',
			array(
				'default' => '#FFF',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				'login_customizer_options[logincust_button_color]',
				array(
					'label' => __( 'Text Color', 'login-customizer' ),
					'section' => 'logincust_button_section',
					'priority' => 35,
					'settings' => 'login_customizer_options[logincust_button_color]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_button_padding]',
			array(
				'default' => '0 12px 2px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Padding(
				$wp_customize,
				'login_customizer_options[logincust_button_padding]',
				array(
					'label' => __( 'Padding', 'login-customizer' ),
					'section' => 'logincust_button_section',
					'priority' => 40,
					'settings' => 'login_customizer_options[logincust_button_padding]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_button_border_width]',
			array(
				'default' => '1px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Range_Slider(
				$wp_customize,
				'login_customizer_options[logincust_button_border_width]',
				array(
					'label' => __( 'Border Width', 'login-customizer' ),
					'section' => 'logincust_button_section',
					'priority' => 45,
					'settings' => 'login_customizer_options[logincust_button_border_width]',
					'choices' => array(
						'percent' => false,
					),
					'input_attrs' => array(
						'min'    => 0,
						'max'    => 20,
						'step'   => 1,
					),
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_button_border]',
			array(
				'default' => '#0074A2',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				'login_customizer_options[logincust_button_border]',
				array(
					'label' => __( 'Border', 'login-customizer' ),
					'section' => 'logincust_button_section',
					'priority' => 55,
					'settings' => 'login_customizer_options[logincust_button_border]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_button_hover_border]',
			array(
				'default' => '#0074A2',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				'login_customizer_options[logincust_button_hover_border]',
				array(
					'label' => __( 'Border (Hover)', 'login-customizer' ),
					'section' => 'logincust_button_section',
					'priority' => 60,
					'settings' => 'login_customizer_options[logincust_button_hover_border]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_button_shadow_spread]',
			array(
				'default' => '0px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Range_Slider(
				$wp_customize,
				'login_customizer_options[logincust_button_shadow_spread]',
				array(
					'label' => __( 'Shadow Spread', 'login-customizer' ),
					'section' => 'logincust_button_section',
					'priority' => 65,
					'settings' => 'login_customizer_options[logincust_button_shadow_spread]',
					'choices' => array(
						'percent' => false,
					),
					'input_attrs' => array(
						'min'    => 0,
						'max'    => 50,
						'step'   => 1,
					),
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_button_shadow]',
			array(
				'default' => '#78C8E6',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Alpha(
				$wp_customize,
				'login_customizer_options[logincust_button_shadow]',
				array(
					'label' => __( 'Box Shadow', 'login-customizer' ),
					'section' => 'logincust_button_section',
					'priority' => 70,
					'settings' => 'login_customizer_options[logincust_button_shadow]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_button_text_shadow]',
			array(
				'default' => '#006799',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				'login_customizer_options[logincust_button_text_shadow]',
				array(
					'label' => __( 'Text Shadow', 'login-customizer' ),
					'section' => 'logincust_button_section',
					'priority' => 75,
					'settings' => 'login_customizer_options[logincust_button_text_shadow]',
				)
			)
		);
	}
}