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
 * @version 2.2.0
 *
 */
namespace LoginCustomizer\Customizer\Panel\Sections;
use LoginCustomizer\Customizer\Panel\Sanitizers;

include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
include_once ABSPATH . 'wp-includes/customize/class-wp-customize-background-position-control.php';

/**
 * Backgruond Section in customizer
 * 
 * @version 2.2.0
 */
class Background{

	function __construct( $wp_customize ) {
		
		$wp_customize->add_section(
			'logincust_background_section',
			array(
				'priority' => 5,
				'title' => __( 'Background', 'login-customizer' ),
				'panel'  => 'logincust_panel',
			)
		);
		
		$wp_customize->add_setting(
			'login_customizer_options[logincust_bg_color]',
			array(
				'default' => '#F1F1F1',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);
		
		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'login_customizer_options[logincust_bg_color]', array(
					'label' => __( 'Background Color', 'login-customizer' ),
					'section' => 'logincust_background_section',
					'priority' => 5,
					'settings' => 'login_customizer_options[logincust_bg_color]',
				)
			)
		);
		
		$wp_customize->add_setting(
			'login_customizer_options[logincust_bg_image]',
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
				'login_customizer_options[logincust_bg_image]',
				array(
					'label' => __( 'Background Image', 'login-customizer' ),
					'section' => 'logincust_background_section',
					'priority' => 10,
					'settings' => 'login_customizer_options[logincust_bg_image]',
				)
			)
		);
		
		$wp_customize->add_setting(
			'login_customizer_options[logincust_bg_image_size]',
			array(
				'default' => 'auto',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'logincust_radio_option',
				'transport' => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'login_customizer_options[logincust_bg_image_size]',
			array(
				'label' => __( 'Background Size', 'login-customizer' ),
				'section' => 'logincust_background_section',
				'type' => 'select',
				'choices' => array(
					'auto' => __( 'Original', 'login-customizer' ),
					'contain' => __( 'Fit to Screen', 'login-customizer' ),
					'cover' => __( 'Fill Screen', 'login-customizer' ),
					'custom' => __( 'Custom', 'login-customizer' ),
				),
				'priority' => 15,
				'settings' => 'login_customizer_options[logincust_bg_image_size]',
			)
		);
		
		$wp_customize->add_setting(
			'login_customizer_options[logincust_bg_size]',
			array(
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
				'transport' => 'postMessage',
			)
		);
		
		$wp_customize->add_control(
			'login_customizer_options[logincust_bg_size]',
			array(
				'label' => __( 'Custom Size', 'login-customizer' ),
				'section' => 'logincust_background_section',
				'priority' => 20,
				'settings' => 'login_customizer_options[logincust_bg_size]',
			)
		);
		
		$wp_customize->add_setting(
			'login_customizer_options[logincust_bg_image_repeat]',
			array(
				'default' => 'no-repeat',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'logincust_radio_option',
				'transport' => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'login_customizer_options[logincust_bg_image_repeat]',
			array(
				'label'   => __( 'Background Repeat', 'login-customizer' ),
				'section' => 'logincust_background_section',
				'type'    => 'select',
				'choices' => array(
					'no-repeat' => __( 'No Repeat', 'login-customizer' ),
					'repeat' => __( 'Repeat', 'login-customizer' ),
					'repeat-x' => __( 'Repeat Horizontally', 'login-customizer' ),
					'repeat-y'   => __( 'Repeat Vertically', 'login-customizer' ),
				),
				'priority' => 25,
				'settings' => 'login_customizer_options[logincust_bg_image_repeat]',
			)
		);

		$wp_customize->add_setting(
			'login_customizer_options[logincust_bg_image_position_x]',
			array(
				'default' => 'left',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'logincust_sanitize_position',
				'transport' => 'postMessage',
			)
		);
		
		
		$wp_customize->add_setting(
			'login_customizer_options[logincust_bg_image_position_y]',
			array(
				'default' => 'top',
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'logincust_sanitize_position',
				'transport' => 'postMessage',
			)
		);
		
		$wp_customize->add_control(
			new \WP_Customize_Background_Position_Control(
				$wp_customize,
				'login_customizer_options[logincust_bg_image_position]',
				array(
					'label' => __( 'Background Position', 'login-customizer' ),
					'section' => 'logincust_background_section',
					'priority' => 30,
					'settings' => array(
						'x' => 'login_customizer_options[logincust_bg_image_position_x]',
						'y' => 'login_customizer_options[logincust_bg_image_position_y]',
					),
				)
			)
		);
	}




}