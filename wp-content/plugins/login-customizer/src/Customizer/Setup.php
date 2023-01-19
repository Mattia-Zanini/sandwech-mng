<?php
/**
 * Include Login Customizer Setup class.
 */
use LoginCustomizer\Essentials;
new Essentials;

include( plugin_dir_path( __FILE__ ) . '/Initial_Setup.php' );

/**
 * Setup login page for new and updated instances.
 */
if ( get_option( 'login_customizer_settings' ) ) {

	$db_version = get_option( 'login_customizer_settings', array() );
	$db_version = $db_version['version'];
	
	if ( isset( $db_version ) ) {	

		// Compare version of plugin with previously saved version
		if ( version_compare( $db_version, LOGINCUST_FREE_VERSION, '<' ) || ! LoginCustomizerSetup::page_id() ) {
			// Setup login page
			return LoginCustomizerSetup::get_instance();
		}
	}
} else {
	// Array of old options
	$options = array(
		'logincust_logo',
		'logincust_logo_width',
		'logincust_logo_height',
		'logincust_logo_padding',
		'logincust_bg_image',
		'logincust_bg_color',
		'logincust_bg_size',
		'logincust_form_bg_image',
		'logincust_form_bg_color',
		'logincust_form_width',
		'logincust_form_height',
		'logincust_form_padding',
		'logincust_field_width',
		'logincust_field_margin',
		'logincust_field_bg',
		'logincust_field_color',
		'logincust_field_label',
		'logincust_button_bg',
		'logincust_button_border',
		'logincust_button_shadow',
		'logincust_button_color',
		'logincust_button_hover_bg',
		'logincust_button_hover_border',
		'logincust_other_color',
		'logincust_other_color_hover',
		'logincust_other_css',
	);

	// if old options exist, update to new system
	foreach ( $options as $key ) {
		if ( $existing = get_option( $key ) ) {
			$options[ $key ] = $existing;
			delete_option( $key );
		}
	}

	// Add new plugin options
	add_option( 'login_customizer_options', $options );
	add_option( 'login_customizer_settings', array() );

	// Setup login page
	return LoginCustomizerSetup::get_instance();
}
