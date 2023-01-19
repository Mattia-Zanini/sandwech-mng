<?php
/**
 * Customizer controls for Fields Section
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

use LoginCustomizer\Customizer\Panel\Controls\Padding;
use LoginCustomizer\Customizer\Panel\Controls\Toggle;
use LoginCustomizer\Customizer\Panel\Controls\Range_Slider;

/**
 * Fields Section in customizer
 * 
 * @version 2.2.0
 */
class Fields{

	public function __construct( $wp_customize ) {

		$wp_customize->add_section(
			'logincust_field_section',
			array(
				'priority' => 25,
				'title' => __( 'Fields', 'login-customizer' ),
				'panel'  => 'logincust_panel',
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_field_remember_me]',
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
				'login_customizer_options[logincust_field_remember_me]',
				array(
					'label' => __( 'Disable Remember Me?', 'login-customizer' ),
					'section' => 'logincust_field_section',
					'priority' => 5,
					'settings' => 'login_customizer_options[logincust_field_remember_me]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_field_width]',
			array(
				'default' => '100%',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Range_Slider(
				$wp_customize,
				'login_customizer_options[logincust_field_width]',
				array(
					'label' => __( 'Width', 'login-customizer' ),
					'section' => 'logincust_field_section',
					'priority' => 10,
					'settings' => 'login_customizer_options[logincust_field_width]',
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
			'login_customizer_options[logincust_field_font_size]',
			array(
				'default' => '24px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Range_Slider(
				$wp_customize,
				'login_customizer_options[logincust_field_font_size]',
				array(
					'label' => __( 'Font Size', 'login-customizer' ),
					'section' => 'logincust_field_section',
					'priority' => 15,
					'settings' => 'login_customizer_options[logincust_field_font_size]',
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
			'login_customizer_options[logincust_field_border_width]',
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
				'login_customizer_options[logincust_field_border_width]',
				array(
					'label' => __( 'Border Width', 'login-customizer' ),
					'section' => 'logincust_field_section',
					'priority' => 20,
					'settings' => 'login_customizer_options[logincust_field_border_width]',
					'choices' => array(
						'percent' => false,
					),
					'input_attrs' => array(
						'min'    => 0,
						'max'    => 10,
						'step'   => 1,
					),
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_field_border_color]',
			array(
				'default' => '#DDD',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				'login_customizer_options[logincust_field_border_color]',
				array(
					'label' => __( 'Border Color', 'login-customizer' ),
					'section' => 'logincust_field_section',
					'priority' => 25,
					'settings' => 'login_customizer_options[logincust_field_border_color]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_field_radius]',
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
				'login_customizer_options[logincust_field_radius]',
				array(
					'label' => __( 'Radius', 'login-customizer' ),
					'section' => 'logincust_field_section',
					'priority' => 30,
					'settings' => 'login_customizer_options[logincust_field_radius]',
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
			'login_customizer_options[logincust_field_box_shadow]',
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
				'login_customizer_options[logincust_field_box_shadow]',
				array(
					'label' => __( 'Disable Box Shadow?', 'login-customizer' ),
					'section' => 'logincust_field_section',
					'priority' => 35,
					'settings' => 'login_customizer_options[logincust_field_box_shadow]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_field_margin]',
			array(
				'default' => '2px 6px 16px 0px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Padding(
				$wp_customize,
				'login_customizer_options[logincust_field_margin]',
				array(
					'label' => __( 'Margin', 'login-customizer' ),
					'section' => 'logincust_field_section',
					'priority' => 35,
					'settings' => 'login_customizer_options[logincust_field_margin]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_field_padding]',
			array(
				'default' => '3px 3px 3px 3px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Padding(
				$wp_customize,
				'login_customizer_options[logincust_field_padding]',
				array(
					'label' => __( 'Padding', 'login-customizer' ),
					'section' => 'logincust_field_section',
					'priority' => 40,
					'settings' => 'login_customizer_options[logincust_field_padding]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_field_bg]',
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
				'login_customizer_options[logincust_field_bg]',
				array(
					'label' => __( 'Background', 'login-customizer' ),
					'section' => 'logincust_field_section',
					'priority' => 45,
					'settings' => 'login_customizer_options[logincust_field_bg]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_field_color]',
			array(
				'default' => '#333',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				'login_customizer_options[logincust_field_color]',
				array(
					'label' => __( 'Text Color', 'login-customizer' ),
					'section' => 'logincust_field_section',
					'priority' => 50,
					'settings' => 'login_customizer_options[logincust_field_color]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_field_label]',
			array(
				'default' => '#777',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				'login_customizer_options[logincust_field_label]',
				array(
					'label' => __( 'Label Color', 'login-customizer' ),
					'section' => 'logincust_field_section',
					'priority' => 55,
					'settings' => 'login_customizer_options[logincust_field_label]',
				)
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_field_label_font_size]',
			array(
				'default' => '14px',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Range_Slider(
				$wp_customize,
				'login_customizer_options[logincust_field_label_font_size]',
				array(
					'label' => __( 'Label Font Size', 'login-customizer' ),
					'section' => 'logincust_field_section',
					'priority' => 60,
					'settings' => 'login_customizer_options[logincust_field_label_font_size]',
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
	}
}