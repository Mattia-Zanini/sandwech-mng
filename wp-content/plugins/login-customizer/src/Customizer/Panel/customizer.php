<?php
/**
 * Customizer controls for Login Customizer
 */
// ------------------------------------- Helping Classes --------------------------------------

use LoginCustomizer\Essentials;
use LoginCustomizer\Customizer\Panel\Sanitizers;
use LoginCustomizer\Customizer\Panel\Custom_Code;
use LoginCustomizer\Customizer\Panel\Functions;


// ------------------------------- Customizer Sections ----------------------------------------
use LoginCustomizer\Customizer\Panel\Sections\Templates;
use LoginCustomizer\Customizer\Panel\Sections\Background;
use LoginCustomizer\Customizer\Panel\Sections\Logo;
use LoginCustomizer\Customizer\Panel\Sections\Form;
use LoginCustomizer\Customizer\Panel\Sections\Fields;
use LoginCustomizer\Customizer\Panel\Sections\Button;
use LoginCustomizer\Customizer\Panel\Sections\Other;
use LoginCustomizer\Customizer\Panel\Sections\Code;

new Essentials;
new Custom_Code;
new Functions;

/**
 * Register login customizer settings
 */
function logincust_customize_register( $wp_customize ) {

	/**
	 * Sanitizer for Background Radio Control
	 */
	function logincust_radio_option( $input, $setting ) {
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

	// Login Customizer Panel
	$wp_customize->add_panel(
		'logincust_panel',
		array(
			'priority'       => 30,
			'capability'     => 'edit_theme_options',
			'title'          => __( 'Login Customizer', 'login-customizer' ),
			'description'    => __( 'This section allows you to customize the login page of your website. Made with ❤ by <a target="_blank" rel="nofollow" href="https://loginpress.pro/?utm_source=login-customizer-lite&utm_medium=customizer">Hardeep Asrani</a> team.', 'login-customizer' ),
		)
	);

	// Section #1: Templates
	new Templates( $wp_customize );

	// Section #2: Background
	new Background( $wp_customize );

	// Section #3: Logo
	new Logo( $wp_customize );

	// Section #4: Form
	new Form( $wp_customize );

	// Section #5: Fields
	new Fields( $wp_customize );

	// Section #6: Button
	new Button( $wp_customize );

	// Section #7: Other
	new Other( $wp_customize );

	// Section #8: Custom CSS & JS
	new Code( $wp_customize );

}
//Register Customizer Page
add_action( 'customize_register', 'logincust_customize_register' );

/**
 * Enqueue script to Customizer Page
 */
function logincust_customizer_script() {
	// Enqueue script to Customizer
	wp_enqueue_script( 'logincust_control_js', LOGINCUST_FREE_URL . 'Customizer/Panel/Assets/JS/customizer.js', array( 'jquery' ), LOGINCUST_FREE_VERSION, true );

	// Generate the redirect url.
	$options = get_option( 'login_customizer_settings', array() );

	$localize = array(
		'page' => get_permalink( $options['page'] ),
		'url' => LOGINCUST_FREE_URL,
	);

	// Localize Script
	wp_localize_script( 'logincust_control_js', 'logincust_script', $localize );
}
add_action( 'customize_controls_print_scripts', 'logincust_customizer_script' );

/**
 * Enqueue script/s to Customizer Preview
 */
function logincust_customizer_preview_script() {
	// Enqueue script to Customizer Preview
	wp_enqueue_script( 'logincust_control_preview', LOGINCUST_FREE_URL . 'Customizer/Panel/Assets/JS/customizer-preview.js', array( 'jquery', 'customize-preview' ), LOGINCUST_FREE_VERSION, true );

	// Generate the redirect url.
	$options = get_option( 'login_customizer_settings', array() );

	$localize = array(
		'page' => get_permalink( $options['page'] ),
	);

	// Action hook triggered after customize_controls_init was called
	wp_localize_script( 'logincust_control_preview', 'logincust_script', $localize );
}

add_action( 'customize_preview_init', 'logincust_customizer_preview_script' );
add_action( 'customize_controls_enqueue_scripts', 'logincust_customizer_preview_script' );
