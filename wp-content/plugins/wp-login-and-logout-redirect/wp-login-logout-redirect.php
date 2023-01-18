<?php
/*
Plugin Name: WP Login and Logout Redirect
Plugin URI: https://wordpress.org/plugins/wp-login-and-logout-redirect/
Description: This plugin which enables you to redirect users to specific URL on login or logout or both.
Version: 1.1
Author: Aminur Islam
Author URI: https://github.com/aminurislamarnob
License: GPLv2 or later
Text Domain: wp-login-logout-redirect
Domain Path: /languages
*/


/**
 * Restrict this file to call directly
*/
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Currently plugin version.
*/
define('WPLALR_PLUGIN_VERSION', '1.0');

/**
 * Plugin Dir
 * **/
define( 'WPLALR_PLUGIN', __FILE__ );
define( 'WPLALR_PLUGIN_DIR', untrailingslashit( dirname( WPLALR_PLUGIN ) ) );

 
/**
 * Load plugin textdomain.
 */
function wplalr_login_logout_load_textdomain() {
    load_plugin_textdomain( 'wp-login-logout-redirect', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}
add_action( 'init', 'wplalr_login_logout_load_textdomain' );


/**
 * Plugin settings page
 */
function wplalr_login_logout_register() {
    
    // register a new section
    add_settings_section(
        'wplalr_login_logout_settings_section', 
        __('WP Login and Logout Redirect Options', 'wp-login-logout-redirect'), 'wplalr_login_logout_section_text', 
        'wplalr_login_logout_section'
    );

    // register a new field in the "wplalr_login_logout_settings_section" section
    add_settings_field(
        'wplalr_login_redirect', 
        __('Login Redirect URL','wp-login-logout-redirect'), 'wplalr_login_field_callback', 
        'wplalr_login_logout_section',  
        'wplalr_login_logout_settings_section'
    );

    // register a new setting for login redirect field
	register_setting('wplalr_login_logout_settings_section', 'wplalr_login_redirect');

    // register a new field in the "wplalr_login_logout_settings_section" section
	add_settings_field(
        'wplalr_logout_redirect', 
        __('Logout Redirect URL', 'wp-login-logout-redirect'), 'wplalr_logout_field_callback', 
        'wplalr_login_logout_section',  
        'wplalr_login_logout_settings_section'
    );

    // register a new setting for logout redirect field
	register_setting('wplalr_login_logout_settings_section', 'wplalr_logout_redirect');

}
add_action('admin_init', 'wplalr_login_logout_register');


//Login redirect field content
function wplalr_login_field_callback(){
    $wplalr_login_redirect_value = get_option('wplalr_login_redirect');
	printf('<input name="wplalr_login_redirect" type="text" class="regular-text" value="%s"/>', $wplalr_login_redirect_value);
}

//Logout redirect field content
function wplalr_logout_field_callback() {
    $wplalr_logout_redirect_value = get_option('wplalr_logout_redirect');
	printf('<input name="wplalr_logout_redirect" type="text" class="regular-text" value="%s"/>', $wplalr_logout_redirect_value);
}

//Plugin settings page section text
function wplalr_login_logout_section_text() {
	printf('%s %s %s', '<p>', __('You can change WordPress Default login or logout or both redirect URL', 'wp-login-logout-redirect'), '</p>');
}


//Register plugin admin menu
add_action('admin_menu', 'wplalr_login_logout_redirect_menu');
function wplalr_login_logout_redirect_menu() {
	add_menu_page(__('WP Login and Logout Redirect Options', 'wp-login-logout-redirect'), __('Redirect Options', 'wp-login-logout-redirect'), 'manage_options', 'wplalr_login_logout_redirect', 'wplalr_login_logout_redirect_output', 'dashicons-randomize');
}


//Plugin options form
function wplalr_login_logout_redirect_output(){
    settings_errors();
    ?>
	<form action="options.php" method="POST">
		<?php do_settings_sections('wplalr_login_logout_section');?>
		<?php settings_fields('wplalr_login_logout_settings_section');?>
		<?php submit_button();?>
	</form>
<?php }


/**
 * Add settings page link with plugin.
 */
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wplalr_login_logout_action_links' );
function wplalr_login_logout_action_links( $links ){
    $wplalr_login_logout_plugin_action_links = array(
    '<a href="' . admin_url( 'admin.php?page=wplalr_login_logout_redirect' ) . '"> '. __('Settings', 'wp-login-logout-redirect') . '</a>',
    );
    return array_merge( $links, $wplalr_login_logout_plugin_action_links );
}


/**
 * Login redirect to user specific URL.
 */
function wplalr_wp_login_redirect( $redirect_to, $request, $user ) {
    $redirect_to =  get_option('wplalr_login_redirect');

    if(empty($redirect_to)){
        $redirect_to = admin_url();
    }

    return $redirect_to;
}
add_filter( 'login_redirect', 'wplalr_wp_login_redirect', 10, 3 );


/**
 * Logout redirect to user specific URL.
 */
function wplalr_wp_logout_redirect(){
    $wplalr_logout_redirect =  get_option('wplalr_logout_redirect');

    if(empty($wplalr_logout_redirect)){
        $wplalr_logout_redirect = home_url();
    }

    wp_redirect( $wplalr_logout_redirect );
    exit();
}
add_action('wp_logout', 'wplalr_wp_logout_redirect');


/**
 * Include user functions
*/
require_once WPLALR_PLUGIN_DIR . '/includes/login-user-time/login-user-time.php';