<?php
/**
 * Customizer controls for Form Section
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

/**
 * Form Section in customizer
 * 
 * @version 2.2.0
 */
class Form{

	function __construct( $wp_customize ) {
				
		$wp_customize->add_section(
			'logincust_form_section',
			array(
				'priority' => 15,
				'title' => __( 'Form', 'login-customizer' ),
				'panel'  => 'logincust_panel',
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_form_bg_image]',
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
				'login_customizer_options[logincust_form_bg_image]',
				array(
					'label' => __( 'Background Image', 'login-customizer' ),
					'section' => 'logincust_form_section',
					'priority' => 5,
					'settings' => 'login_customizer_options[logincust_form_bg_image]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_form_bg_color]',
			array(
				'default' => '#FFFFFF',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Alpha(
				$wp_customize,
				'login_customizer_options[logincust_form_bg_color]',
				array(
					'label' => __( 'Background Color', 'login-customizer' ),
					'section' => 'logincust_form_section',
					'priority' => 10,
					'settings' => 'login_customizer_options[logincust_form_bg_color]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_form_width]',
			array(
				'default' => '320px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Range_Slider(
				$wp_customize,
				'login_customizer_options[logincust_form_width]',
				array(
					'label' => __( 'Width', 'login-customizer' ),
					'section' => 'logincust_form_section',
					'priority' => 15,
					'settings' => 'login_customizer_options[logincust_form_width]',
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
			'login_customizer_options[logincust_form_height]',
			array(
				'default' => '194px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Range_Slider(
				$wp_customize,
				'login_customizer_options[logincust_form_height]',
				array(
					'label' => __( 'Height', 'login-customizer' ),
					'section' => 'logincust_form_section',
					'priority' => 20,
					'settings' => 'login_customizer_options[logincust_form_height]',
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
			'login_customizer_options[logincust_form_padding]',
			array(
				'default' => '26px 24px 46px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Padding(
				$wp_customize,
				'login_customizer_options[logincust_form_padding]',
				array(
					'label' => __( 'Padding', 'login-customizer' ),
					'section' => 'logincust_form_section',
					'priority' => 25,
					'settings' => 'login_customizer_options[logincust_form_padding]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_form_radius]',
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
				'login_customizer_options[logincust_form_radius]',
				array(
					'label' => __( 'Radius', 'login-customizer' ),
					'section' => 'logincust_form_section',
					'priority' => 30,
					'settings' => 'login_customizer_options[logincust_form_radius]',
					'choices' => array(
						'percent' => false,
					),
					'input_attrs' => array(
						'min'    => 0,
						'max'    => 500,
						'step'   => 1,
					),
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_form_shadow_spread]',
			array(
				'default' => '3px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Range_Slider(
				$wp_customize,
				'login_customizer_options[logincust_form_shadow_spread]',
				array(
					'label' => __( 'Shadow Spread', 'login-customizer' ),
					'section' => 'logincust_form_section',
					'priority' => 35,
					'settings' => 'login_customizer_options[logincust_form_shadow_spread]',
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
			'login_customizer_options[logincust_form_shadow]',
			array(
				'default' => 'rgba(0,0,0, 0.13)',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Alpha(
				$wp_customize,
				'login_customizer_options[logincust_form_shadow]',
				array(
					'label' => __( 'Box Shadow', 'login-customizer' ),
					'section' => 'logincust_form_section',
					'priority' => 40,
					'settings' => 'login_customizer_options[logincust_form_shadow]',
				)
			)
		);

	}
}