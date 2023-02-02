<?php
/*
Plugin Name: BulletProof Security
Plugin URI: https://forum.ait-pro.com/read-me-first/
Text Domain: bulletproof-security
Domain Path: /languages/
Description: <strong>Feature Highlights:</strong> Setup Wizard &bull; MScan Malware Scanner &bull; .htaccess Website Security Protection (Firewalls) &bull; Security Logging|HTTP Error Logging &bull; DB Backup &bull; DB Table Prefix Changer &bull; Login Security & Monitoring &bull; JTC-Lite Login Form Bot Lockout Protection &bull; Idle Session Logout (ISL) &bull; Auth Cookie Expiration (ACE) &bull; System Info: Extensive System, Server and Security Status Information &bull; FrontEnd|BackEnd Maintenance Mode &bull; WP Automatic Update Options (BPS MU Tools must-use plugin) &bull; Force Strong Passwords &bull; Email Alerts When New Plugins And Themes Are Available.
Version: 6.7
Author: AITpro Website Security
Author URI: https://forum.ait-pro.com/read-me-first/
*/

/*  Copyright (C) Edward Alexander | AITpro.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// BPS Global variables
// 3.4: It is not a mistake or retarded to add the global keyword to global variables outside of functions per PHP.net, but yeah it does appear to be retarded. 
// WP_CLI requires that all global variables outside of functions MUST explicitly use the global keyword since WP_CLI loads WP within a function
// and cannot access the global variables within functions in BPS. Luckily this does not break BPS or WordPress in any way and PHP.net states this is technically not an error.
global $bps_last_version, $bps_version, $bps_footer, $aitpro_bullet, $bps_topDiv, $bps_bottomDiv, $bpsPro_remote_addr, $bpsPro_http_client_ip, $bpsPro_http_forwarded, $bpsPro_http_x_forwarded_for, $bpsPro_http_x_cluster_client_ip, $bps_wpcontent_dir, $bps_plugin_dir, $plugin_hashes, $theme_hashes;

define( 'BULLETPROOF_VERSION', '6.7' );
$bps_last_version = '6.6';
$bps_version = '6.7';
$bps_footer = '<div id="AITpro-link">' . __('BulletProof Security ', 'bulletproof-security') . esc_html($bps_version) . __(' Plugin by ', 'bulletproof-security') . '<a href="'.esc_url('https://www.ait-pro.com/').'" target="_blank" title="AITpro Website Security">' . __( 'AITpro Website Security', 'bulletproof-security') . '</a></div>';
$aitpro_bullet = '<img src="'.plugins_url('/bulletproof-security/admin/images/aitpro-bullet.png').'" style="padding:0px 3px 0px 3px;" />';
// Top div & bottom div
$bps_topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
$bps_bottomDiv = '</p></div>';
$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );

// Setup Wizard Options: GDPR Compliance Global Variables
$GDPR_Options = get_option('bulletproof_security_options_gdpr');

if ( isset( $GDPR_Options['bps_gdpr_on_off'] ) && $GDPR_Options['bps_gdpr_on_off'] != 'On' ) {

	$bpsPro_remote_addr = false;
	if ( array_key_exists('REMOTE_ADDR', $_SERVER) ) {
	$bpsPro_remote_addr = $_SERVER['REMOTE_ADDR'];
	}	
	$bpsPro_http_client_ip = false;
	if ( array_key_exists('HTTP_CLIENT_IP', $_SERVER) ) {
	$bpsPro_http_client_ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	$bpsPro_http_forwarded = false;
	if ( array_key_exists('HTTP_FORWARDED', $_SERVER) ) {
	$bpsPro_http_forwarded = $_SERVER['HTTP_FORWARDED'];
	}
	$bpsPro_http_x_forwarded_for = false;
	if ( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) ) {
	$bpsPro_http_x_forwarded_for = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}			
	$bpsPro_http_x_cluster_client_ip = false;
	if ( array_key_exists('HTTP_X_CLUSTER_CLIENT_IP', $_SERVER) ) {
	$bpsPro_http_x_cluster_client_ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
	}		

} else {
	
	$bpsPro_remote_addr = 'GDPR Compliance On';
	$bpsPro_http_client_ip = 'GDPR Compliance On';
	$bpsPro_http_forwarded = 'GDPR Compliance On';		
	$bpsPro_http_x_forwarded_for = 'GDPR Compliance On';	
	$bpsPro_http_x_cluster_client_ip = 'GDPR Compliance On';		
}

// Load BPS Global class - not doing anything with this Class in BPS Free
//require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/class.php';

add_action( 'init', 'bulletproof_security_load_plugin_textdomain' );

// Load i18n Language Translation
function bulletproof_security_load_plugin_textdomain() {
	load_plugin_textdomain('bulletproof-security', false, dirname(plugin_basename(__FILE__)).'/languages/');
}

// BPS upgrade functions
require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/functions.php';
// MScan Plugin and Theme file hash variables - added to global variables above: $plugin_hashes, $theme_hashes
if ( file_exists( WP_CONTENT_DIR . '/bps-backup/plugin-hashes/plugin-hashes.php' ) ) {
require_once WP_CONTENT_DIR . '/bps-backup/plugin-hashes/plugin-hashes.php';
}
if ( file_exists( WP_CONTENT_DIR . '/bps-backup/theme-hashes/theme-hashes.php' ) ) {
require_once WP_CONTENT_DIR . '/bps-backup/theme-hashes/theme-hashes.php';
}
// MScan AJAX functions
require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/mscan-ajax-functions.php';
// BPS HUD Dimiss functions - includes AutoFix AutoSetup checks
require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/hud-autofix-setup.php';
// BPS HUD Dimiss functions - includes AutoFix AutoWhitelist checks
require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/hud-autofix-whitelist.php';
// BPS HUD Dimiss functions - General Error Checks & Misc checks
require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/hud-dismiss-functions.php';
// BPS Zip & Email Log File Cron functions
require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/zip-email-cron-functions.php';
// General functions
require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/general-functions.php';
// BPS Login Security
require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/login-security.php';
// BPS Force Strong Passwords
require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/force-strong-passwords.php';
// BPS DB Backup
require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/db-security.php';
// BPS Hidden Plugin Folders|Files (HPF) Cron
require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/hidden-plugin-folders-cron.php';
// Idle Session Logout (ISL)
$BPS_ISL_options = get_option('bulletproof_security_options_idle_session');
if ( isset( $BPS_ISL_options['bps_isl'] ) && $BPS_ISL_options['bps_isl'] == 'On' ) {
require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/idle-session-logout.php';
}
// PHP Encryption|Decryption class using openssl_decrypt() and openssl_encrypt()
// Web hosts may see this file as malicious and block or delete it. So a file_exists check needs to be here.
$bpsPro_encrypt_decrypt_class = WP_PLUGIN_DIR . '/bulletproof-security/includes/encrypt-decrypt-class.php';
if ( file_exists ( $bpsPro_encrypt_decrypt_class ) ) {
require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/encrypt-decrypt-class.php';
}

// If in single site Admin Dashboard
if ( is_admin() ) {
    
require_once WP_PLUGIN_DIR . '/bulletproof-security/admin/includes/admin.php';
	
	register_activation_hook(__FILE__, 'bulletproof_security_install');
	register_deactivation_hook(__FILE__, 'bulletproof_security_deactivation');
    register_uninstall_hook(__FILE__, 'bulletproof_security_uninstall');

	add_action( 'admin_init', 'bulletproof_security_admin_init' );
    add_action( 'admin_menu', 'bulletproof_security_admin_menu' );
}

// If in Network Admin Dashboard for BPS Uninstaller
if ( is_multisite() && is_network_admin() ) {
	add_action( 'network_admin_menu', 'bulletproof_security_network_admin_menu' ); 	
}

// "Settings" link on Plugins Options Page 
function bps_plugin_actlinks( $links, $file ) {
static $this_plugin;
	if ( ! $this_plugin ) 
		$this_plugin = plugin_basename(__FILE__);
	if ( $file == $this_plugin ) {
		if ( ! is_multisite() ) {	
		$links[] = '<br><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="'.esc_attr( 'BPS Setup Wizard' ).'">'.__('Setup Wizard', 'bulletproof-security').'</a>';
		$links[] = '<br><a href="'.admin_url( 'plugins.php?page=bulletproof-security/admin/includes/uninstall.php' ).'" title="'.esc_attr( 'Select an uninstall option for BPS plugin deletion' ).'">'.__('Uninstall Options', 'bulleproof-security').'</a>';
		} elseif ( is_multisite() ) {
		$links[] = '<br><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="'.esc_attr( 'BPS Setup Wizard' ).'">'.__('Setup Wizard', 'bulletproof-security').'</a>';		
		// The Uninstall Options Form does not work on Network|Multisite so do not show the Uninstall Options link in Action Links
		//$links[] = '<br><a href="'.network_admin_url( 'plugins.php?page=bulletproof-security/admin/includes/uninstall.php' ).'" title="'.esc_attr( 'Select an uninstall option for BPS plugin deletion' ).'">'.__('Uninstall Options', 'bulleproof-security').'</a>';
		}
	}
	return $links;
}

add_filter( 'plugin_action_links', 'bps_plugin_actlinks', 10, 2 );
add_filter( 'network_admin_plugin_action_links', 'bps_plugin_actlinks', 10, 2 );

// Add links on plugins page
function bps_plugin_extra_links( $links, $file ) {
static $this_plugin;

	if ( ! current_user_can('install_plugins') )
		return $links;
	if ( ! $this_plugin ) 
		$this_plugin = plugin_basename(__FILE__);
	if ( $file == $this_plugin ) {
		$links[] = '<a href="https://forum.ait-pro.com/forums/topic/plugin-conflicts-actively-blocked-plugins-plugin-compatibility/" title="BulletProof Security Forum" target="_blank">'.__('Forum - Support', 'bulleproof-security').'</a>';
		$links[] = '<a href="https://affiliates.ait-pro.com/po/" title="Upgrade to BPS Pro" target="_blank">'.__('Upgrade', 'bulleproof-security').'</a>';
		$links[] = '<a href="https://www.ait-pro.com/bps-features/" title="BPS Pro Features" target="_blank">'.__('BPS Pro Features', 'bulleproof-security').'</a>';
	}
	return $links;
}

add_filter( 'plugin_row_meta', 'bps_plugin_extra_links', 10, 2 );

?>