<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! current_user_can('manage_options') ) { 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}


## AutoFix|AutoWhitelist|AutoSetup: Automatically creates fixes/setups or whitelist rules for any known issues with other plugins.
## List of fixes by plugin and CC text box: https://forum.ait-pro.com/forums/topic/setup-wizard-autofix/.
/*
Root Custom Code Text Boxes:
1. CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE: Individual functions for each plugin setup in file pwizard-autofix-setup.php
9. CUSTOM CODE REQUEST METHODS FILTERED: bpsPro_Pwizard_Autofix_Request_methods()
10. CUSTOM CODE PLUGIN/THEME SKIP/BYPASS RULES: bpsPro_Pwizard_Autofix_plugin_skip_bypass_root()
11. CUSTOM CODE TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE: bpsPro_Pwizard_Autofix_RFI()
12. CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS: bpsPro_Pwizard_Autofix_BPSQSE_root()

wp-admin Custom Code Text Boxes:
3. CUSTOM CODE WPADMIN PLUGIN/FILE SKIP RULES: bpsPro_Pwizard_Autofix_plugin_skip_bypass_wpadmin()
4. CUSTOM CODE BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS: bpsPro_Pwizard_Autofix_BPSQSE_wpadmin()

Additional Notes: The QS1, QS2, etc. markers are used/checked in the HUD autofix whitelist includes file 
so that duplicate/redundant Setup Wizard AutoFix alerts/fixes are not needed. ie if a fix for 1 plugin is the same as another plugin
then there is no need to display the Setup Wizard AutoFix again since the fix already exists.
*/

## 9. CUSTOM CODE REQUEST METHODS FILTERED
## Note: If someone has other custom code and wants to use that custom code instead then they would need to 
## add these 2 lines of code below so that the AutoFix check does not display.
## #RewriteCond %{REQUEST_METHOD} ^(HEAD) [NC]
## #RewriteRule ^(.*)$ /wp-content/plugins/bulletproof-security/405.php [L]
function bpsPro_Pwizard_Autofix_Request_methods() {
	
	$AutoFix_Options = get_option('bulletproof_security_options_wizard_autofix');
	
	if ( isset($AutoFix_Options['bps_wizard_autofix']) && $AutoFix_Options['bps_wizard_autofix'] == 'Off' ) {
		return;
	}

	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_request_methods_option = ! isset($CC_Options_root['bps_customcode_request_methods']) ? '' : $CC_Options_root['bps_customcode_request_methods'];
	$bps_customcode_request_methods = htmlspecialchars_decode( $bps_customcode_request_methods_option, ENT_QUOTES );
	$bps_customcode_request_methods_array = array();
	$bps_customcode_request_methods_array[] = $bps_customcode_request_methods;
	$bps_get_wp_root_secure = bps_wp_get_root_folder();
	$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
	$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
	//$pattern1 = '/REQUEST\sMETHODS\sFILTERED/';
	$pattern_rmf = '/#{1,}(\s|){1,}RewriteCond\s\%\{REQUEST_METHOD\}\s\^\(HEAD\)\s\[NC\](.*\s*){1}(#{1,}(\s|){1,}RewriteRule\s\^\(\.\*\)\$\s(.*)\/bulletproof-security\/405\.php\s(\[L\]|\[R,L\])|#{1,}(\s|){1,}RewriteRule\s\^\(\.\*\)\$\s\-\s\[R=405,L\])/';	

	$request_methods_code = "# REQUEST METHODS FILTERED
# If you want to allow HEAD Requests use BPS Custom Code and copy 
# this entire REQUEST METHODS FILTERED section of code to this BPS Custom Code 
# text box: CUSTOM CODE REQUEST METHODS FILTERED.
# See the CUSTOM CODE REQUEST METHODS FILTERED help text for additional steps.
RewriteCond %{REQUEST_METHOD} ^(TRACE|DELETE|TRACK|DEBUG) [NC]
RewriteRule ^(.*)$ - [F]
#RewriteCond %{REQUEST_METHOD} ^(HEAD) [NC]
#RewriteRule ^(.*)$ " . $bps_get_wp_root_secure . $bps_plugin_dir . "/bulletproof-security/405.php [L]";
	
	## Jetpack Plugin: whitelist rules
	$jetpack = 'jetpack/jetpack.php';
	$jetpack_active = in_array( $jetpack, apply_filters('active_plugins', get_option('active_plugins')));
	$jetpack_array = array();
	$jetpack_fix = '';

	if ( $jetpack_active == 1 || is_plugin_active_for_network( $jetpack ) ) {
		$jetpack_fix = __('Jetpack Plugin Request Methods AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern_rmf, $bps_customcode_request_methods ) ) {
			$jetpack_array[] = $request_methods_code;
		}
	}
	
	## Marmoset Viewer Plugin: whitelist rules
	$marmoset_viewer = 'marmoset-viewer/marmoset-viewer.php';
	$marmoset_viewer_active = in_array( $marmoset_viewer, apply_filters('active_plugins', get_option('active_plugins')));
	$marmoset_viewer_array = array();
	$marmoset_viewer_fix = '';

	if ( $marmoset_viewer_active == 1 || is_plugin_active_for_network( $marmoset_viewer ) ) {
		$marmoset_viewer_fix = __('Marmoset Viewer Plugin Request Methods AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern_rmf, $bps_customcode_request_methods ) ) {
			$marmoset_viewer_array[] = $request_methods_code;
		}
	}
	
	## BackWPup Plugin: whitelist rules
	$backwpup = 'backwpup/backwpup.php';
	$backwpup_active = in_array( $backwpup, apply_filters('active_plugins', get_option('active_plugins')));
	$backwpup_array = array();
	$backwpup_fix = '';

	if ( $backwpup_active == 1 || is_plugin_active_for_network( $backwpup ) ) {
		$backwpup_fix = __('BackWPup Plugin Request Methods AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern_rmf, $bps_customcode_request_methods ) ) {
			$backwpup_array[] = $request_methods_code;
		}
	}
	
	## MailPoet Newsletters (wysija newsletters) Plugin: whitelist rules
	$mailpoet = 'wysija-newsletters/index.php';
	$mailpoet_active = in_array( $mailpoet, apply_filters('active_plugins', get_option('active_plugins')));
	$mailpoet_array = array();
	$mailpoet_fix = '';
	
	if ( $mailpoet_active == 1 || is_plugin_active_for_network( $mailpoet ) ) {
		$mailpoet_fix = __('MailPoet Newsletters (wysija newsletters) Plugin Request Methods AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern_rmf, $bps_customcode_request_methods ) ) {
			$mailpoet_array[] = $request_methods_code;
		}
	}
	
	## BackUpWordPress Plugin: whitelist rules
	$backupwordpress = 'backupwordpress/backupwordpress.php';
	$backupwordpress_active = in_array( $backupwordpress, apply_filters('active_plugins', get_option('active_plugins')));
	$backupwordpress_array = array();
	$backupwordpress_fix = '';

	if ( $backupwordpress_active == 1 || is_plugin_active_for_network( $backupwordpress ) ) {
		$backupwordpress_fix = __('BackUpWordPress Plugin Request Methods AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern_rmf, $bps_customcode_request_methods ) ) {
			$backupwordpress_array[] = $request_methods_code;
		}
	}
	
	## Broken Link Checker Plugin: whitelist rules
	$broken_link_checker = 'broken-link-checker/broken-link-checker.php';
	$broken_link_checker_active = in_array( $broken_link_checker, apply_filters('active_plugins', get_option('active_plugins')));
	$broken_link_checker_array = array();
	$broken_link_checker_fix = '';

	if ( $broken_link_checker_active == 1 || is_plugin_active_for_network( $broken_link_checker ) ) {
		$broken_link_checker_fix = __('Broken Link Checker Plugin Request Methods AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern_rmf, $bps_customcode_request_methods ) ) {
			$broken_link_checker_array[] = $request_methods_code;
		}
	}
	
	## MailChimp for WordPress Plugin: whitelist rules
	$mailchimp = 'mailchimp-for-wp/mailchimp-for-wp.php';
	$mailchimp_active = in_array( $mailchimp, apply_filters('active_plugins', get_option('active_plugins')));
	$mailchimp_array = array();
	$mailchimp_fix = '';

	if ( $mailchimp_active == 1 || is_plugin_active_for_network( $mailchimp ) ) {
		$mailchimp_fix = __('MailChimp for WordPress Plugin Request Methods AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern_rmf, $bps_customcode_request_methods ) ) {
			$mailchimp_array[] = $request_methods_code;
		}
	}

	## PowerPress Podcasting Plugin: whitelist rules
	$powerpress = 'powerpress/powerpress.php';
	$powerpress_active = in_array( $powerpress, apply_filters('active_plugins', get_option('active_plugins')));
	$powerpress_array = array();
	$powerpress_fix = '';

	if ( $powerpress_active == 1 || is_plugin_active_for_network( $broken_link_checker ) ) {
		$powerpress_fix = __('PowerPress Podcasting Plugin Request Methods AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern_rmf, $bps_customcode_request_methods ) ) {
			$powerpress_array[] = $request_methods_code;
		}
	}

	// cleans up whitespace, newlines, etc in the $bps_customcode_request_methods_array values.
	$cc_request_methods_array = array();

	foreach ( $bps_customcode_request_methods_array as $key => $value ) {
		$cc_request_methods_array[] = trim( $value, " \t\n\r");
	}

	$bps_customcode_request_methods_merge = array_merge($cc_request_methods_array, $jetpack_array, $marmoset_viewer_array, $backwpup_array, $mailpoet_array, $backupwordpress_array, $broken_link_checker_array, $mailchimp_array, $powerpress_array);
	$cc_request_methods_unique = array_unique($bps_customcode_request_methods_merge);

 	$bps_customcode_request_methods_implode = implode( "\n\n", $cc_request_methods_unique );

	$bps_customcode_one 					= ! isset($CC_Options_root['bps_customcode_one']) ? '' : $CC_Options_root['bps_customcode_one'];
	$bps_customcode_server_signature 		= ! isset($CC_Options_root['bps_customcode_server_signature']) ? '' : $CC_Options_root['bps_customcode_server_signature'];
	$bps_customcode_directory_index 		= ! isset($CC_Options_root['bps_customcode_directory_index']) ? '' : $CC_Options_root['bps_customcode_directory_index'];
	$bps_customcode_server_protocol 		= ! isset($CC_Options_root['bps_customcode_server_protocol']) ? '' : $CC_Options_root['bps_customcode_server_protocol'];
	$bps_customcode_error_logging 			= ! isset($CC_Options_root['bps_customcode_error_logging']) ? '' : $CC_Options_root['bps_customcode_error_logging'];
	$bps_customcode_deny_dot_folders 		= ! isset($CC_Options_root['bps_customcode_deny_dot_folders']) ? '' : $CC_Options_root['bps_customcode_deny_dot_folders'];
	$bps_customcode_admin_includes 			= ! isset($CC_Options_root['bps_customcode_admin_includes']) ? '' : $CC_Options_root['bps_customcode_admin_includes'];
	$bps_customcode_wp_rewrite_start 		= ! isset($CC_Options_root['bps_customcode_wp_rewrite_start']) ? '' : $CC_Options_root['bps_customcode_wp_rewrite_start'];
	//$bps_customcode_request_methods 		= ! isset($CC_Options_root['bps_customcode_request_methods']) ? '' : $CC_Options_root['bps_customcode_request_methods'];
	$bps_customcode_two 					= ! isset($CC_Options_root['bps_customcode_two']) ? '' : $CC_Options_root['bps_customcode_two'];
	$bps_customcode_timthumb_misc 			= ! isset($CC_Options_root['bps_customcode_timthumb_misc']) ? '' : $CC_Options_root['bps_customcode_timthumb_misc'];
	$bps_customcode_bpsqse 					= ! isset($CC_Options_root['bps_customcode_bpsqse']) ? '' : $CC_Options_root['bps_customcode_bpsqse'];
	$bps_customcode_wp_rewrite_end 			= ! isset($CC_Options_root['bps_customcode_wp_rewrite_end']) ? '' : $CC_Options_root['bps_customcode_wp_rewrite_end'];
	$bps_customcode_deny_files 				= ! isset($CC_Options_root['bps_customcode_deny_files']) ? '' : $CC_Options_root['bps_customcode_deny_files'];
	$bps_customcode_three 					= ! isset($CC_Options_root['bps_customcode_three']) ? '' : $CC_Options_root['bps_customcode_three'];

	if ( ! is_multisite() ) {

		$Root_CC_Options = array(
		'bps_customcode_one' 				=> $bps_customcode_one, 
		'bps_customcode_server_signature' 	=> $bps_customcode_server_signature, 
		'bps_customcode_directory_index' 	=> $bps_customcode_directory_index, 
		'bps_customcode_server_protocol' 	=> $bps_customcode_server_protocol, 
		'bps_customcode_error_logging' 		=> $bps_customcode_error_logging, 
		'bps_customcode_deny_dot_folders' 	=> $bps_customcode_deny_dot_folders, 
		'bps_customcode_admin_includes' 	=> $bps_customcode_admin_includes, 
		'bps_customcode_wp_rewrite_start' 	=> $bps_customcode_wp_rewrite_start, 
		'bps_customcode_request_methods' 	=> trim($bps_customcode_request_methods_implode), 
		'bps_customcode_two' 				=> $bps_customcode_two, 
		'bps_customcode_timthumb_misc' 		=> $bps_customcode_timthumb_misc, 
		'bps_customcode_bpsqse' 			=> $bps_customcode_bpsqse, 
		'bps_customcode_deny_files' 		=> $bps_customcode_deny_files, 
		'bps_customcode_three' 				=> $bps_customcode_three 
		);
				
	} else {
					
		$Root_CC_Options = array(
		'bps_customcode_one' 				=> $bps_customcode_one, 
		'bps_customcode_server_signature' 	=> $bps_customcode_server_signature, 
		'bps_customcode_directory_index' 	=> $bps_customcode_directory_index, 
		'bps_customcode_server_protocol' 	=> $bps_customcode_server_protocol, 
		'bps_customcode_error_logging' 		=> $bps_customcode_error_logging, 
		'bps_customcode_deny_dot_folders' 	=> $bps_customcode_deny_dot_folders, 
		'bps_customcode_admin_includes' 	=> $bps_customcode_admin_includes, 
		'bps_customcode_wp_rewrite_start' 	=> $bps_customcode_wp_rewrite_start, 
		'bps_customcode_request_methods' 	=> trim($bps_customcode_request_methods_implode), 
		'bps_customcode_two' 				=> $bps_customcode_two, 
		'bps_customcode_timthumb_misc' 		=> $bps_customcode_timthumb_misc, 
		'bps_customcode_bpsqse' 			=> $bps_customcode_bpsqse, 
		'bps_customcode_wp_rewrite_end' 	=> $bps_customcode_wp_rewrite_end, 
		'bps_customcode_deny_files' 		=> $bps_customcode_deny_files, 
		'bps_customcode_three' 				=> $bps_customcode_three 
		);					
	}

	foreach( $Root_CC_Options as $key => $value ) {
		update_option('bulletproof_security_options_customcode', $Root_CC_Options);
	}

	$success_array = array($jetpack_fix, $marmoset_viewer_fix, $backwpup_fix, $mailpoet_fix, $backupwordpress_fix, $broken_link_checker_fix, $mailchimp_fix, $powerpress_fix);
	
	foreach ( $success_array as $successMessage ) {
		
		if ( $successMessage != '' ) {
			echo '<font color="green"><strong>'.$successMessage.'</strong></font><br>';
		}
	}
}

## 10. CUSTOM CODE PLUGIN/THEME SKIP/BYPASS RULES
function bpsPro_Pwizard_Autofix_plugin_skip_bypass_root() {
	
	$AutoFix_Options = get_option('bulletproof_security_options_wizard_autofix');
	
	if ( $AutoFix_Options['bps_wizard_autofix'] == 'Off' ) {
		return;
	}
	
	global $counter;
	$counter = 13;
	
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_two = htmlspecialchars_decode( $CC_Options_root['bps_customcode_two'], ENT_QUOTES );
	$bps_customcode_two_array = array();
	$bps_customcode_two_array[] = $bps_customcode_two;
	$bps_get_wp_root_secure = bps_wp_get_root_folder();
	$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
	$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
	
	## WooCommerce Plugin: whitelist rules
	$woocommerce = 'woocommerce/woocommerce.php';
	$woocommerce_active = in_array( $woocommerce, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern1 = '/RewriteCond\s%{REQUEST_URI}\s\^\.\*\/\(shop\|cart\|checkout\|wishlist\)\.\*\s\[NC\]/';	
	$pattern2 = '/RewriteCond\s%{QUERY_STRING}\s\.\*\(order\|wc-ajax=\)\.\*\s\[NC\]/';
	$woocommerce_array1 = array();
	$woocommerce_array2 = array();
	$woocommerce_fix = '';

	if ( $woocommerce_active == 1 || is_plugin_active_for_network( $woocommerce ) ) {
		$woocommerce_fix = __('WooCommerce Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern1, $bps_customcode_two ) ) {

			$woocommerce_array1[] = "# WooCommerce order & wc-ajax= Query String skip/bypass rule
RewriteCond %{QUERY_STRING} .*(order|wc-ajax=).* [NC]
RewriteRule . - [S=99]";
		}	

		if ( ! preg_match( $pattern2, $bps_customcode_two ) ) {

			$woocommerce_array2[] = "# WooCommerce shop, cart, checkout & wishlist URI skip/bypass rule
RewriteCond %{REQUEST_URI} ^.*/(shop|cart|checkout|wishlist).* [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Simple Lightbox Plugin: whitelist rules
	$simple_lightbox = 'simple-lightbox/main.php';
	$simple_lightbox_active = in_array( $simple_lightbox, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern3 = '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/simple-lightbox\/\s\[NC\]/';
	$simple_lightbox_array = array();
	$simple_lightbox_fix = '';

	if ( $simple_lightbox_active == 1 || is_plugin_active_for_network( $simple_lightbox ) ) {
		$simple_lightbox_fix = __('Simple Lightbox Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern3, $bps_customcode_two ) ) {

			$simple_lightbox_array[] = "# Simple Lightbox plugin skip/bypass rule
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/simple-lightbox/ [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## WPBakery Visual Composer Plugin: whitelist rules
	$visual_composer = 'js_composer/js_composer.php';
	$visual_composer_active = in_array( $visual_composer, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern4 = '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/js_composer\/\s\[NC\]/';
	$visual_composer_array = array();
	$visual_composer_fix = '';
	
	if ( $visual_composer_active == 1 || is_plugin_active_for_network( $visual_composer ) ) {
		$visual_composer_fix = __('WPBakery Visual Composer Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');		

		if ( ! preg_match( $pattern4, $bps_customcode_two ) ) {

			$visual_composer_array[] = "# WPBakery Visual Composer plugin skip/bypass rule
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/js_composer/ [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Event Espresso Attendee Mover Plugin: whitelist rules
	$ee_attendee = 'eea-attendee-mover/eea-attendee-mover.php';
	$ee_attendee_active = in_array( $ee_attendee, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern5 = '/RewriteCond\s%{QUERY_STRING}\slimit%5B%5D=\(\.\*\)\s\[NC\]/';
	$ee_attendee_array = array();
	$ee_attendee_fix = '';
	
	if ( $ee_attendee_active == 1 || is_plugin_active_for_network( $ee_attendee ) ) {
		$ee_attendee_fix = __('Event Espresso Attendee Mover Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern5, $bps_customcode_two ) ) {

			$ee_attendee_array[] = "# Event Espresso Attendee Mover Query String skip/bypass rule
RewriteCond %{QUERY_STRING} limit%5B%5D=(.*) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## WP Rocket Plugin: whitelist rules
	$wp_rocket = 'wp-rocket/wp-rocket.php';
	$wp_rocket_active = in_array( $wp_rocket, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern6 = '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/wp-rocket\/\s\[NC\]/';
	$wp_rocket_array = array();
	$wp_rocket_fix = '';

	if ( $wp_rocket_active == 1 || is_plugin_active_for_network( $wp_rocket ) ) {
		$wp_rocket_fix = __('WP Rocket Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern6, $bps_customcode_two ) ) {

			$wp_rocket_array[] = "# WP Rocket plugin skip/bypass rule
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/wp-rocket/ [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Easy Media Gallery Pro Plugin: whitelist rules
	$emg_pro = 'easy-media-gallery-pro/easy-media-gallery-pro.php';
	$emg_pro_active = in_array( $emg_pro, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern7 = '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/easy-media-gallery-pro\/\s\[NC\]/';
	$emg_pro_array = array();
	$emg_pro_fix = '';

	if ( $emg_pro_active == 1 || is_plugin_active_for_network( $emg_pro ) ) {
		$emg_pro_fix = __('Easy Media Gallery Pro Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern7, $bps_customcode_two ) ) {

			$emg_pro_array[] = "# Easy Media Gallery Pro plugin skip/bypass rule
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/easy-media-gallery-pro/ [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Nextend Facebook Connect Plugin: whitelist rules
	$nextend_fb_connect = 'nextend-facebook-connect/nextend-facebook-connect.php';
	$nextend_fb_connect_active = in_array( $nextend_fb_connect, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern8 = '/RewriteCond\s%{QUERY_STRING}\sloginFacebook=\(\.\*\)\s\[NC\]/';
	$nextend_fb_connect_array = array();
	$nextend_fb_connect_fix = '';
	
	if ( $nextend_fb_connect_active == 1 || is_plugin_active_for_network( $nextend_fb_connect ) ) {
		$nextend_fb_connect_fix = __('Nextend Facebook Connect Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern8, $bps_customcode_two ) ) {

			$nextend_fb_connect_array[] = "# Nextend Facebook Connect Query String skip/bypass rule
RewriteCond %{QUERY_STRING} loginFacebook=(.*) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Shashin Plugin: whitelist rules
	$shashin = 'shashin/start.php';
	$shashin_active = in_array( $shashin, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern9 = '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/shashin\/\s\[NC\]/';
	$shashin_array = array();
	$shashin_fix = '';

	if ( $shashin_active == 1 || is_plugin_active_for_network( $shashin ) ) {
		$shashin_fix = __('Shashin Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern9, $bps_customcode_two ) ) {

			$shashin_array[] = "# Shashin plugin skip/bypass rule
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/shashin/ [NC]
RewriteRule . - [S=99]";
		}
	}

	## Nocturnal Theme: whitelist rules
	$nocturnal_theme = wp_get_theme( 'nocturnal' );
	$pattern10 = '/RewriteCond\s%{QUERY_STRING}\splayerInstance=\(\.\*\)\s\[NC\]/';
	$nocturnal_array = array();
	$nocturnal_fix = '';

	if ( $nocturnal_theme->exists() ) {
		$nocturnal_fix = __('Nocturnal Theme skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern10, $bps_customcode_two ) ) {

			$nocturnal_array[] = "# Nocturnal Theme Query String skip/bypass rule
RewriteCond %{QUERY_STRING} playerInstance=(.*) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Shopp Plugin: whitelist rules
	$shopp = 'shopp/Shopp.php';
	$shopp_active = in_array( $shopp, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern11 = '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/shopp\/\s\[NC\]/';
	$shopp_array = array();
	$shopp_fix = '';

	if ( $shopp_active == 1 || is_plugin_active_for_network( $shopp ) ) {
		$shopp_fix = __('Shopp Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern11, $bps_customcode_two ) ) {

			$shopp_array[] = "# Shopp plugin skip/bypass rule
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/shopp/ [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## WP-Invoice - Web Invoice and Billing Plugin: whitelist rules
	$wp_invoice = 'wp-invoice/wp-invoice.php';
	$wp_invoice_active = in_array( $wp_invoice, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern12 = '/RewriteCond\s%{QUERY_STRING}\spage=wpi_\(\.\*\)\s\[NC\]/';
	$wp_invoice_array = array();
	$wp_invoice_fix = '';

	if ( $wp_invoice_active == 1 || is_plugin_active_for_network( $wp_invoice ) ) {
		$wp_invoice_fix = __('WP-Invoice - Web Invoice and Billing Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern12, $bps_customcode_two ) ) {

			$wp_invoice_array[] = "# WP-Invoice - Web Invoice and Billing Query String skip/bypass rule
RewriteCond %{QUERY_STRING} page=wpi_(.*) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## wp-greet Plugin: whitelist rules
	$wp_greet = 'wp-greet/wp-greet.php';
	$wp_greet_active = in_array( $wp_greet, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern13 = '/RewriteCond\s%{QUERY_STRING}\sgallery=([0-9]+)&image=\(\.\*\)\s\[NC\]/';
	$wp_greet_array = array();
	$wp_greet_fix = '';

	if ( $wp_greet_active == 1 || is_plugin_active_for_network( $wp_greet ) ) {
		$wp_greet_fix = __('wp-greet Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern13, $bps_customcode_two ) ) {

			$wp_greet_array[] = "# wp-greet Query String skip/bypass rule
RewriteCond %{QUERY_STRING} gallery=([0-9]+)&image=(.*) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## WP Juicebox Plugin: whitelist rules
	$wp_juicebox = 'wp-juicebox/wp-juicebox.php';
	$wp_juicebox_active = in_array( $wp_juicebox, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern14 = '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/wp-juicebox\/\s\[NC\]/';
	$wp_juicebox_array = array();
	$wp_juicebox_fix = '';

	if ( $wp_juicebox_active == 1 || is_plugin_active_for_network( $wp_juicebox ) ) {
		$wp_juicebox_fix = __('WP Juicebox Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern14, $bps_customcode_two ) ) {

			$wp_juicebox_array[] = "# WP Juicebox plugin skip/bypass rule
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/wp-juicebox/ [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Prayer Engine Plugin: whitelist rules
	$prayer_engine = 'prayerengine_plugin/prayerengine_plugin.php';
	$prayer_engine_active = in_array( $prayer_engine, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern15 = '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/prayerengine_plugin\/\s\[NC\]/';
	$prayer_engine_array = array();
	$prayer_engine_fix = '';

	if ( $prayer_engine_active == 1 || is_plugin_active_for_network( $prayer_engine ) ) {
		$prayer_engine_fix = __('Prayer Engine Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern15, $bps_customcode_two ) ) {

			$prayer_engine_array[] = "# Prayer Engine plugin skip/bypass rule
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/prayerengine_plugin/ [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Appointment Calendar Plugin: whitelist rules
	$appointment_calendar = 'appointment-calendar/appointment-calendar.php';
	$appointment_calendar_active = in_array( $appointment_calendar, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern16 = '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/appointment-calendar\/\s\[NC\]/';
	$appointment_calendar_array = array();
	$appointment_calendar_fix = '';

	if ( $appointment_calendar_active == 1 || is_plugin_active_for_network( $appointment_calendar ) ) {
		$appointment_calendar_fix = __('Appointment Calendar Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern16, $bps_customcode_two ) ) {

			$appointment_calendar_array[] = "# Appointment Calendar plugin skip/bypass rule
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/appointment-calendar/ [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## ThirstyAffiliates Plugin: whitelist rules
	$thirsty_affiliates = 'thirstyaffiliates/thirstyaffiliates.php';
	$thirsty_affiliates_active = in_array( $thirsty_affiliates, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern17 = '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/thirstyaffiliates\/\s\[NC\]/';
	$thirsty_affiliates_array = array();
	$thirsty_affiliates_fix = '';

	if ( $thirsty_affiliates_active == 1 || is_plugin_active_for_network( $thirsty_affiliates ) ) {
		$thirsty_affiliates_fix = __('ThirstyAffiliates Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern17, $bps_customcode_two ) ) {

			$thirsty_affiliates_array[] = "# ThirstyAffiliates plugin skip/bypass rule
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/thirstyaffiliates/ [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## WooCommerce Ogone Payment Gateway Plugin: whitelist rules
	$woo_ogone = 'woocommerce_ogonecw/woocommerce_ogonecw.php';
	$woo_ogone_active = in_array( $woo_ogone, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern18 = '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/woocommerce_ogonecw\/\s\[NC\]/';
	$woo_ogone_array = array();
	$woo_ogone_fix = '';

	if ( $woo_ogone_active == 1 || is_plugin_active_for_network( $woo_ogone ) ) {
		$woo_ogone_fix = __('WooCommerce Ogone Payment Gateway Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern18, $bps_customcode_two ) ) {

			$woo_ogone_array[] = "# WooCommerce Ogone Payment Gateway plugin skip/bypass rule
RewriteCond %{REQUEST_URI} ^" . $bps_get_wp_root_secure . $bps_plugin_dir . "/woocommerce_ogonecw/ [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## OIOpublisher Ad Manager Plugin: whitelist rules
	$OIOpublisher = WP_PLUGIN_DIR . '/oiopub-direct/wp.php';
	$pattern19 = '/RewriteCond\s%{REQUEST_URI}\s\^\/advertise\/uploads\/\s\[NC\]/';
	$OIOpublisher_array = array();
	$OIOpublisher_fix = '';

	if ( file_exists($OIOpublisher) ) {
		$OIOpublisher_fix = __('OIOpublisher Ad Manager Plugin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern19, $bps_customcode_two ) ) {

			$OIOpublisher_array[] = "# OIOpublisher Ad Manager plugin skip/bypass rule
RewriteCond %{REQUEST_URI} ^/advertise/uploads/ [NC]
RewriteRule . - [S=99]";
		}
	}

	$bps_customcode_two_array_impload = implode( "]", $bps_customcode_two_array );
	$bps_customcode_two_array_preg_split = preg_split("/\[S=\d{1,2}\]/", $bps_customcode_two_array_impload);
	$bps_customcode_two_array_preg_replace = preg_replace("/RewriteRule\s\.\s-\s/", "RewriteRule . - [S=99]", $bps_customcode_two_array_preg_split);
	$bps_customcode_two_array_filter = array_filter($bps_customcode_two_array_preg_replace);
	
	// Break the $bps_customcode_two_array value into separate arrays and cleans up the $bps_customcode_two_array values.
	$cc2_array = array();

	foreach ( $bps_customcode_two_array_filter as $key => $value ) {
		$cc2_array[] = trim( $value, " \t\n\r");
	}

	$bps_customcode_two_merge = array_merge($cc2_array, $woocommerce_array1, $woocommerce_array2, $simple_lightbox_array, $visual_composer_array, $ee_attendee_array, $wp_rocket_array, $emg_pro_array, $nextend_fb_connect_array, $shashin_array, $nocturnal_array, $shopp_array, $wp_invoice_array, $wp_greet_array, $wp_juicebox_array, $prayer_engine_array, $appointment_calendar_array, $thirsty_affiliates_array, $woo_ogone_array, $OIOpublisher_array);
	
	$cc2_unique = array_unique($bps_customcode_two_merge);
	$S_replace = preg_replace_callback( '/(S=\d{1,2})/', 'bpsPro_S_number_count_replace', $cc2_unique );
	$cc2_reversed = array_reverse($S_replace);
 	$bps_customcode_two_implode = implode( "\n\n", $cc2_reversed );

	if ( ! is_multisite() ) {

		$Root_CC_Options = array(
		'bps_customcode_one' 				=> $CC_Options_root['bps_customcode_one'], 
		'bps_customcode_server_signature' 	=> $CC_Options_root['bps_customcode_server_signature'], 
		'bps_customcode_directory_index' 	=> $CC_Options_root['bps_customcode_directory_index'], 
		'bps_customcode_server_protocol' 	=> $CC_Options_root['bps_customcode_server_protocol'], 
		'bps_customcode_error_logging' 		=> $CC_Options_root['bps_customcode_error_logging'], 
		'bps_customcode_deny_dot_folders' 	=> $CC_Options_root['bps_customcode_deny_dot_folders'], 
		'bps_customcode_admin_includes' 	=> $CC_Options_root['bps_customcode_admin_includes'], 
		'bps_customcode_wp_rewrite_start' 	=> $CC_Options_root['bps_customcode_wp_rewrite_start'], 
		'bps_customcode_request_methods' 	=> $CC_Options_root['bps_customcode_request_methods'], 
		'bps_customcode_two' 				=> $bps_customcode_two_implode, 
		'bps_customcode_timthumb_misc' 		=> $CC_Options_root['bps_customcode_timthumb_misc'], // not sure if i should attempt this one or not
		'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
		'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
		'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
		);
				
	} else {
					
		$Root_CC_Options = array(
		'bps_customcode_one' 				=> $CC_Options_root['bps_customcode_one'], 
		'bps_customcode_server_signature' 	=> $CC_Options_root['bps_customcode_server_signature'], 
		'bps_customcode_directory_index' 	=> $CC_Options_root['bps_customcode_directory_index'], 
		'bps_customcode_server_protocol' 	=> $CC_Options_root['bps_customcode_server_protocol'], 
		'bps_customcode_error_logging' 		=> $CC_Options_root['bps_customcode_error_logging'], 
		'bps_customcode_deny_dot_folders' 	=> $CC_Options_root['bps_customcode_deny_dot_folders'], 
		'bps_customcode_admin_includes' 	=> $CC_Options_root['bps_customcode_admin_includes'], 
		'bps_customcode_wp_rewrite_start' 	=> $CC_Options_root['bps_customcode_wp_rewrite_start'], 
		'bps_customcode_request_methods' 	=> $CC_Options_root['bps_customcode_request_methods'], 
		'bps_customcode_two' 				=> $bps_customcode_two_implode, 
		'bps_customcode_timthumb_misc' 		=> $CC_Options_root['bps_customcode_timthumb_misc'], 
		'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
		'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
		'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
		'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
		);					
	}

	foreach( $Root_CC_Options as $key => $value ) {
		update_option('bulletproof_security_options_customcode', $Root_CC_Options);
	}

	$success_array = array($woocommerce_fix, $simple_lightbox_fix, $visual_composer_fix, $ee_attendee_fix, $wp_rocket_fix, $emg_pro_fix, $nextend_fb_connect_fix, $shashin_fix, $nocturnal_fix, $shopp_fix, $wp_invoice_fix, $wp_greet_fix, $wp_juicebox_fix, $prayer_engine_fix, $appointment_calendar_fix, $thirsty_affiliates_fix, $woo_ogone_fix, $OIOpublisher_fix);
	
	foreach ( $success_array as $successMessage ) {
		
		if ( $successMessage != '' ) {
			echo '<font color="green"><strong>'.$successMessage.'</strong></font><br>';
		}
	}
}

function bpsPro_S_number_count_replace($matches) {
    global $counter;
    $result = "S={$counter}";
    $counter++;

    return $result;
}

## 11. CUSTOM CODE TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE
function bpsPro_Pwizard_Autofix_RFI() {
	
	$AutoFix_Options = get_option('bulletproof_security_options_wizard_autofix');
	
	if ( $AutoFix_Options['bps_wizard_autofix'] == 'Off' ) {
		return;
	}

	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_rfi = htmlspecialchars_decode( $CC_Options_root['bps_customcode_timthumb_misc'], ENT_QUOTES );
	$bps_customcode_rfi_array = array();
	$bps_customcode_rfi_array[] = $bps_customcode_rfi;
	$pattern1 = '/TIMTHUMB\sFORBID\sRFI\sand\sMISC\sAND\sFILE\sSKIP\/BYPASS RULE/';
	$bps_customcode_rfi_code_array = array();
	
	$bps_customcode_rfi_code_array[] = "# TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE
# Use BPS Custom Code to modify/edit/change this code and to save it permanently.
# Remote File Inclusion (RFI) security rules
# Note: Only whitelist your additional domains or files if needed - do not whitelist hacker domains or files
RewriteCond %{QUERY_STRING} ^.*(http|https|ftp)(%3A|:)(%2F|/)(%2F|/)(w){0,3}.?(blogger|picasa|blogspot|tsunami|petapolitik|photobucket|imgur|imageshack|wordpress\.com|img\.youtube|tinypic\.com|upload\.wikimedia|kkc|start-thegame).*$ [NC,OR]
RewriteCond %{THE_REQUEST} ^.*(http|https|ftp)(%3A|:)(%2F|/)(%2F|/)(w){0,3}.?(blogger|picasa|blogspot|tsunami|petapolitik|photobucket|imgur|imageshack|wordpress\.com|img\.youtube|tinypic\.com|upload\.wikimedia|kkc|start-thegame).*$ [NC]
RewriteRule .* index.php [F]
# 
# Example: Whitelist additional misc files: (example\.php|another-file\.php|phpthumb\.php|thumb\.php|thumbs\.php)
RewriteCond %{REQUEST_URI} (timthumb\.php|phpthumb\.php|thumb\.php|thumbs\.php) [NC]
# Example: Whitelist additional website domains: RewriteCond %{HTTP_REFERER} ^.*(YourWebsite.com|AnotherWebsite.com).*
RewriteCond %{HTTP_REFERER} ^.*" . bpsGetDomainRoot() . ".*
RewriteRule . - [S=1]\n";

	## PDF Viewer (Envigeek Web Services) Plugin: whitelist rules
	$pdf_viewer = 'pdf-viewer/pdf-viewer.php';
	$pdf_viewer_active = in_array( $pdf_viewer, apply_filters('active_plugins', get_option('active_plugins')));
	$viewer_html = '';
	$pdf_viewer_fix = '';

	if ( $pdf_viewer_active == 1 || is_plugin_active_for_network( $pdf_viewer ) ) {
		$pdf_viewer_fix = __('PDF Viewer (Envigeek Web Services) Plugin RFI AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( '/viewer\\\.html/', $bps_customcode_rfi ) ) {		
			$viewer_html = 'viewer\.html|';
		}
	}

	## Marmoset Viewer Plugin: whitelist rules
	$marmoset_viewer = 'marmoset-viewer/marmoset-viewer.php';
	$marmoset_viewer_active = in_array( $marmoset_viewer, apply_filters('active_plugins', get_option('active_plugins')));
	$mviewer_php = '';
	$marmoset_viewer_fix = '';

	if ( $marmoset_viewer_active == 1 || is_plugin_active_for_network( $marmoset_viewer ) ) {
		$marmoset_viewer_fix = __('Marmoset Viewer Plugin RFI AutoWhitelist successful', 'bulletproof-security');
		
		if ( ! preg_match( '/mviewer\\\.php/', $bps_customcode_rfi ) ) {
			$mviewer_php = 'mviewer\.php|';
		}
	}

	## PDF viewer for WordPress (ThemeNcode code canyon) Plugin: whitelist rules
	$pdf_viewer_themencode = 'pdf-viewer-for-wordpress/pdf-viewer-for-wordpress.php';
	$pdf_viewer_themencode_active = in_array( $pdf_viewer_themencode, apply_filters('active_plugins', get_option('active_plugins')));
	$themencode_pdf_viewer = '';
	$pdf_viewer_themencode_fix = '';

	if ( $pdf_viewer_themencode_active == 1 || is_plugin_active_for_network( $pdf_viewer_themencode ) ) {
		$pdf_viewer_themencode_fix = __('PDF viewer for WordPress (ThemeNcode code canyon) Plugin RFI AutoWhitelist successful', 'bulletproof-security');
		
		if ( ! preg_match( '/themencode-pdf-viewer-sc/', $bps_customcode_rfi ) ) {		
			$themencode_pdf_viewer = 'themencode-pdf-viewer-sc|';
		}
	}
	
	## jupdf pdf viewer Plugin: whitelist rules
	$jupdf_pdf_viewer = 'jupdf-pdf-viewer/jupdf-pdf-viewer.php';
	$jupdf_pdf_viewer_active = in_array( $jupdf_pdf_viewer, apply_filters('active_plugins', get_option('active_plugins')));
	$jupdf_index_html = '';
	$jupdf_pdf_viewer_fix = '';

	if ( $jupdf_pdf_viewer_active == 1 || is_plugin_active_for_network( $jupdf_pdf_viewer ) ) {
		$jupdf_pdf_viewer_fix = __('jupdf pdf viewer Plugin RFI AutoWhitelist successful', 'bulletproof-security');
		
		if ( ! preg_match( '/jupdf\/index\\\.html/', $bps_customcode_rfi ) ) {		
			$jupdf_index_html = 'jupdf/index\.html|';
		}
	}

	## UserPro (code canyon) Plugin: whitelist rules
	$userPro = 'userpro/index.php';
	$userPro_active = in_array( $userPro, apply_filters('active_plugins', get_option('active_plugins')));
	$auth_php_files = '';
	$userPro_fix = '';

	if ( $userPro_active == 1 || is_plugin_active_for_network( $userPro ) ) {
		$userPro_fix = __('UserPro (code canyon) Plugin RFI AutoWhitelist successful', 'bulletproof-security');
		
		if ( ! preg_match( '/instagramAuth\\\.php\|linkedinAuth\\\.php/', $bps_customcode_rfi ) ) {		
			$auth_php_files = 'instagramAuth\.php|linkedinAuth\.php|';
		}
	}

	## NativeChurch Theme: whitelist rules
	$NativeChurch_theme = wp_get_theme( 'NativeChurch' );
	$NativeChurch_theme_file = '';
	$NativeChurch_theme_fix = '';

	if ( $NativeChurch_theme->exists() ) {
		$NativeChurch_theme_fix = __('NativeChurch Theme RFI AutoWhitelist successful', 'bulletproof-security');
		
		if ( ! preg_match( '/download\\\.php/', $bps_customcode_rfi ) ) {		
			$NativeChurch_theme_file = 'download\.php|';
		}
	}
	
	## User Avatar (CTLT DEV) Plugin: whitelist rules
	$user_avatar = 'user-avatar/user-avatar.php';
	$user_avatar_active = in_array( $user_avatar, apply_filters('active_plugins', get_option('active_plugins')));
	$user_avatar_pic_php = '';
	$user_avatar_fix = '';

	if ( $user_avatar_active == 1 || is_plugin_active_for_network( $user_avatar ) ) {
		$user_avatar_fix = __('User Avatar (CTLT DEV) Plugin RFI AutoWhitelist successful', 'bulletproof-security');
		
		if ( ! preg_match( '/user-avatar-pic\\\.php/', $bps_customcode_rfi ) ) {		
			$user_avatar_pic_php = 'user-avatar-pic\.php|';
		}
	}	

	## OIOpublisher Ad Manager Plugin: whitelist rules
	$OIOpublisher = WP_PLUGIN_DIR . '/oiopub-direct/wp.php';
	$OIOpublisher_files = '';
	$OIOpublisher_fix = '';

	if ( file_exists($OIOpublisher) ) {
		$OIOpublisher_fix = __('OIOpublisher Ad Manager Plugin RFI AutoWhitelist successful', 'bulletproof-security');
		
		if ( ! preg_match( '/go\\\.php\|purchase\\\.php\|bubble\\\.js\|oiopub\\\.js/', $bps_customcode_rfi ) ) {		
			$OIOpublisher_files = 'go\.php|purchase\.php|bubble\.js|oiopub\.js|';
		}
	}	

	## Digital Access Pass (DAP) Plugin: whitelist rules
	$DAPLiveLinks = 'DAP-WP-LiveLinks/DAP-WP-LiveLinks.php';
	$DAPLiveLinks_active = in_array( $DAPLiveLinks, apply_filters('active_plugins', get_option('active_plugins')));
	$DAPLiveLinks_files = '';
	$DAPLiveLinks_fix = '';

	if ( $DAPLiveLinks_active == 1 || is_plugin_active_for_network( $DAPLiveLinks ) ) {
		$DAPLiveLinks_fix = __('Digital Access Pass (DAP) Plugin RFI AutoWhitelist successful', 'bulletproof-security');
		
		if ( ! preg_match( '/authenticate\\\.php\|signup_submit\\\.php/', $bps_customcode_rfi ) ) {		
			$DAPLiveLinks_files = 'authenticate\.php|signup_submit\.php|';
		}
	}

	## Easy Pagination (code canyon) Plugin: whitelist rules
	$easy_pagination = WP_PLUGIN_DIR . '/easy-pagination/images/thumbnail.php';
	$ep_thumbnail_php = '';
	$easy_pagination_fix = '';

	if ( file_exists($easy_pagination) ) {
		$easy_pagination_fix = __('Easy Pagination (code canyon) Plugin RFI AutoWhitelist successful', 'bulletproof-security');
		
		if ( ! preg_match( '/thumbnail\\\.php/', $bps_customcode_rfi ) ) {		
			$ep_thumbnail_php = 'thumbnail\.php|';
		}
	}

	## iTheme2 Theme: whitelist rules
	$itheme2_theme = wp_get_theme( 'itheme2' );
	$itheme2_img_php = '';
	$itheme2_theme_fix = '';
	
	if ( $itheme2_theme->exists() ) {
		$itheme2_theme_fix = __('iTheme2 Theme RFI AutoWhitelist successful', 'bulletproof-security');
		
		if ( ! preg_match( '/img\\\.php/', $bps_customcode_rfi ) ) {		
			$itheme2_img_php = 'img\.php|';
		}
	}

	## SmoothV4.1 Theme: whitelist rules
	$smoothv41_theme = wp_get_theme( 'SmoothV4.1' );
	$smoothv41_thumbnail_php = '';
	$smoothv41_theme_fix = '';

	if ( $smoothv41_theme->exists() ) {
		$smoothv41_theme_fix = __('SmoothV4.1 Theme RFI AutoWhitelist successful', 'bulletproof-security');
		
		if ( ! preg_match( '/thumbnail\\\.php/', $bps_customcode_rfi ) ) {		
			$smoothv41_thumbnail_php = 'thumbnail\.php|';
		}
	}

	$pattern = '/RewriteCond\s%\{REQUEST_URI\}\s\(/';
	$replace = "RewriteCond %{REQUEST_URI} (". $viewer_html . $mviewer_php . $themencode_pdf_viewer . $jupdf_index_html . $auth_php_files . $NativeChurch_theme_file . $user_avatar_pic_php . $OIOpublisher_files . $DAPLiveLinks_files . $ep_thumbnail_php . $itheme2_img_php . $smoothv41_thumbnail_php;

	if ( $CC_Options_root['bps_customcode_timthumb_misc'] != '' ) {		
		$bps_customcode_timthumb_misc_replace = preg_replace($pattern, $replace, $bps_customcode_rfi_array);
	} else {
		$bps_customcode_timthumb_misc_replace = preg_replace($pattern, $replace, $bps_customcode_rfi_code_array);			
	}

	$bps_customcode_timthumb_misc_implode = implode( "\n", $bps_customcode_timthumb_misc_replace );

	if ( ! is_multisite() ) {

		$Root_CC_Options = array(
		'bps_customcode_one' 				=> $CC_Options_root['bps_customcode_one'], 
		'bps_customcode_server_signature' 	=> $CC_Options_root['bps_customcode_server_signature'], 
		'bps_customcode_directory_index' 	=> $CC_Options_root['bps_customcode_directory_index'], 
		'bps_customcode_server_protocol' 	=> $CC_Options_root['bps_customcode_server_protocol'], 
		'bps_customcode_error_logging' 		=> $CC_Options_root['bps_customcode_error_logging'], 
		'bps_customcode_deny_dot_folders' 	=> $CC_Options_root['bps_customcode_deny_dot_folders'], 
		'bps_customcode_admin_includes' 	=> $CC_Options_root['bps_customcode_admin_includes'], 
		'bps_customcode_wp_rewrite_start' 	=> $CC_Options_root['bps_customcode_wp_rewrite_start'], 
		'bps_customcode_request_methods' 	=> $CC_Options_root['bps_customcode_request_methods'], 
		'bps_customcode_two' 				=> $CC_Options_root['bps_customcode_two'], 
		'bps_customcode_timthumb_misc' 		=> trim($bps_customcode_timthumb_misc_implode), 
		'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
		'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
		'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
		);
				
	} else {
					
		$Root_CC_Options = array(
		'bps_customcode_one' 				=> $CC_Options_root['bps_customcode_one'], 
		'bps_customcode_server_signature' 	=> $CC_Options_root['bps_customcode_server_signature'], 
		'bps_customcode_directory_index' 	=> $CC_Options_root['bps_customcode_directory_index'], 
		'bps_customcode_server_protocol' 	=> $CC_Options_root['bps_customcode_server_protocol'], 
		'bps_customcode_error_logging' 		=> $CC_Options_root['bps_customcode_error_logging'], 
		'bps_customcode_deny_dot_folders' 	=> $CC_Options_root['bps_customcode_deny_dot_folders'], 
		'bps_customcode_admin_includes' 	=> $CC_Options_root['bps_customcode_admin_includes'], 
		'bps_customcode_wp_rewrite_start' 	=> $CC_Options_root['bps_customcode_wp_rewrite_start'], 
		'bps_customcode_request_methods' 	=> $CC_Options_root['bps_customcode_request_methods'], 
		'bps_customcode_two' 				=> $CC_Options_root['bps_customcode_two'], 
		'bps_customcode_timthumb_misc' 		=> trim($bps_customcode_timthumb_misc_implode), 
		'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
		'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
		'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
		'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
		);					
	}

	foreach( $Root_CC_Options as $key => $value ) {
		update_option('bulletproof_security_options_customcode', $Root_CC_Options);
	}

	$success_array = array($pdf_viewer_fix, $marmoset_viewer_fix, $pdf_viewer_themencode_fix, $jupdf_pdf_viewer_fix, $userPro_fix, $NativeChurch_theme_fix, $user_avatar_fix, $OIOpublisher_fix, $DAPLiveLinks_fix, $easy_pagination_fix, $itheme2_theme_fix, $smoothv41_theme_fix);
	
	foreach ( $success_array as $successMessage ) {
		
		if ( $successMessage != '' ) {
			echo '<font color="green"><strong>'.$successMessage.'</strong></font><br>';
		}
	}	
}

## 12. CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS
function bpsPro_Pwizard_Autofix_BPSQSE_root() {
	
	$AutoFix_Options = get_option('bulletproof_security_options_wizard_autofix');
	
	if ( $AutoFix_Options['bps_wizard_autofix'] == 'Off' ) {
		return;
	}

	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_bpsqse = htmlspecialchars_decode( $CC_Options_root['bps_customcode_bpsqse'], ENT_QUOTES );
	$bps_customcode_bpsqse_array = array();
	$bps_customcode_bpsqse_array[] = $bps_customcode_bpsqse;
	$bps_get_wp_root_secure = bps_wp_get_root_folder();
	$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
	$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
	$pattern1 = '/BPSQSE\sBPS\sQUERY\sSTRING\sEXPLOITS/';
	
	$bps_customcode_bpsqse_code_array = array();
	
	## The escaping is necessary in this String for processing
	$bps_customcode_bpsqse_code_array[] = "# BEGIN BPSQSE BPS QUERY STRING EXPLOITS
# The libwww-perl User Agent is forbidden - Many bad bots use libwww-perl modules, but some good bots use it too.
# Good sites such as W3C use it for their W3C-LinkChecker. 
# Use BPS Custom Code to add or remove user agents temporarily or permanently from the 
# User Agent filters directly below or to modify/edit/change any of the other security code rules below.
RewriteCond %{HTTP_USER_AGENT} (havij|libwww-perl|wget|python|nikto|curl|scan|java|winhttp|clshttp|loader) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} (%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} (;|<|>|'|".'"'."|\)|\(|%0A|%0D|%22|%27|%28|%3C|%3E|%00).*(libwww-perl|wget|python|nikto|curl|scan|java|winhttp|HTTrack|clshttp|archiver|loader|email|harvest|extract|grab|miner) [NC,OR]
RewriteCond %{THE_REQUEST} (\?|\*|%2a)+(%20+|\\\\s+|%20+\\\\s+|\\\\s+%20+|\\\\s+%20+\\\\s+)(http|https)(:/|/) [NC,OR]
RewriteCond %{THE_REQUEST} etc/passwd [NC,OR]
RewriteCond %{THE_REQUEST} cgi-bin [NC,OR]
RewriteCond %{THE_REQUEST} (%0A|%0D|\\"."\\"."r|\\"."\\"."n) [NC,OR]
RewriteCond %{REQUEST_URI} owssvr\.dll [NC,OR]
RewriteCond %{HTTP_REFERER} (%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
RewriteCond %{HTTP_REFERER} \.opendirviewer\. [NC,OR]
RewriteCond %{HTTP_REFERER} users\.skynet\.be.* [NC,OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(http|https):// [NC,OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(\.\.//?)+ [NC,OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=/([a-z0-9_.]//?)+ [NC,OR]
RewriteCond %{QUERY_STRING} \=PHP[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12} [NC,OR]
RewriteCond %{QUERY_STRING} (\.\./|%2e%2e%2f|%2e%2e/|\.\.%2f|%2e\.%2f|%2e\./|\.%2e%2f|\.%2e/) [NC,OR]
RewriteCond %{QUERY_STRING} ftp\: [NC,OR]
RewriteCond %{QUERY_STRING} (http|https)\: [NC,OR] 
RewriteCond %{QUERY_STRING} \=\|w\| [NC,OR]
RewriteCond %{QUERY_STRING} ^(.*)/self/(.*)$ [NC,OR]
RewriteCond %{QUERY_STRING} ^(.*)cPath=(http|https)://(.*)$ [NC,OR]
RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (<|%3C)([^s]*s)+cript.*(>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (\<|%3C).*embed.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (<|%3C)([^e]*e)+mbed.*(>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (\<|%3C).*object.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (<|%3C)([^o]*o)+bject.*(>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (\<|%3C).*iframe.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (<|%3C)([^i]*i)+frame.*(>|%3E) [NC,OR] 
RewriteCond %{QUERY_STRING} base64_encode.*\(.*\) [NC,OR]
RewriteCond %{QUERY_STRING} base64_(en|de)code[^(]*\([^)]*\) [NC,OR]
RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} ^.*(\(|\)|<|>|%3c|%3e).* [NC,OR]
RewriteCond %{QUERY_STRING} ^.*(\\x00|\\x04|\\x08|\\x0d|\\x1b|\\x20|\\x3c|\\x3e|\\x7f).* [NC,OR]
RewriteCond %{QUERY_STRING} (NULL|OUTFILE|LOAD_FILE) [OR]
RewriteCond %{QUERY_STRING} (\.{1,}/)+(motd|etc|bin) [NC,OR]
RewriteCond %{QUERY_STRING} (localhost|loopback|127\.0\.0\.1) [NC,OR]
RewriteCond %{QUERY_STRING} (<|>|'|%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
RewriteCond %{QUERY_STRING} concat[^\(]*\( [NC,OR]
RewriteCond %{QUERY_STRING} union([^s]*s)+elect [NC,OR]
RewriteCond %{QUERY_STRING} union([^a]*a)+ll([^s]*s)+elect [NC,OR]
RewriteCond %{QUERY_STRING} \-[sdcr].*(allow_url_include|allow_url_fopen|safe_mode|disable_functions|auto_prepend_file) [NC,OR]
RewriteCond %{QUERY_STRING} (;|<|>|'|".'"'."|\)|%0A|%0D|%22|%27|%3C|%3E|%00).*(/\*|union|select|insert|drop|delete|update|cast|create|char|convert|alter|declare|order|script|set|md5|benchmark|encode) [NC,OR]
RewriteCond %{QUERY_STRING} (sp_executesql) [NC]
RewriteRule ^(.*)$ - [F]
# END BPSQSE BPS QUERY STRING EXPLOITS\n";

	## WooCommerce PagSeguro Plugin: whitelist rules removes: java
	$woo_PagSeguro = 'woocommerce-pagseguro/woocommerce-pagseguro.php';
	$woo_PagSeguro_active = in_array( $woo_PagSeguro, apply_filters('active_plugins', get_option('active_plugins')));
	$woo_PagSeguro_fix = '';

	if ( $woo_PagSeguro_active == 1 || is_plugin_active_for_network( $woo_PagSeguro ) ) {
		$woo_PagSeguro_fix = __('WooCommerce PagSeguro Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p1 = array('/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(havij(.*)\[NC,OR\]/', '/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(;\|\<\|\>\|\'\|\"\|(.*)\[NC,OR\]/');
		$r1 = array("RewriteCond %{HTTP_USER_AGENT} (havij|libwww-perl|wget|python|nikto|curl|scan|winhttp|clshttp|loader) [NC,OR]", "RewriteCond %{HTTP_USER_AGENT} (;|<|>|'|".'"'."|\)|\(|%0A|%0D|%22|%27|%28|%3C|%3E|%00).*(libwww-perl|wget|python|nikto|curl|scan|winhttp|HTTrack|clshttp|archiver|loader|email|harvest|extract|grab|miner) [NC,OR]");

	} else {
		$p1 = array();
		$r1 = array();
	}

	## Event Espresso Plugin: whitelist rules Note: covers all versions of Espresso and the premium versions
	$event_espresso1 = WP_PLUGIN_DIR . '/event-espresso-decaf/espresso.php';
	$event_espresso2 = WP_PLUGIN_DIR . '/event-espresso-free/espresso.php';
	$event_espresso3 = WP_PLUGIN_DIR . '/event-espresso/espresso.php';
	$event_espresso4 = WP_PLUGIN_DIR . '/event-espresso-core-master/espresso.php';
	$event_espresso_fix = '';

	if ( file_exists($event_espresso1) || file_exists($event_espresso2) || file_exists($event_espresso3) || file_exists($event_espresso4) ) {
		$event_espresso_fix = __('Event Espresso Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
			
		$p2 = array('/RewriteCond\s%\{HTTP_REFERER\}\s\(%0A\|%0D\|%27\|%3C\|%3E\|%00\)\s\[NC,OR\]/');
		$r2 = array("# BPS AutoWhitelist QS1: Event Espresso Plugin");
	} else {
		$p2 = array();
		$r2 = array();
	}

	## WooCommerce Serial Key Plugin: whitelist rules
	$woo_serial_key = 'woocommerce-serial-key/serial-key.php';
	$woo_serial_key_active = in_array( $woo_serial_key, apply_filters('active_plugins', get_option('active_plugins')));
	$woo_serial_key_fix = '';
	
	if ( $woo_serial_key_active == 1 || is_plugin_active_for_network( $woo_serial_key ) ) {
		$woo_serial_key_fix = __('WooCommerce Serial Key Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p3 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r3 = array("# BPS AutoWhitelist QS2: WooCommerce Serial Key Plugin", "# BPS AutoWhitelist QS3: WooCommerce Serial Key Plugin", "# BPS AutoWhitelist QS4: WooCommerce Serial Key Plugin");

	} else {
		$p3 = array();
		$r3 = array();
	}

	## WooCommerce WorldPay Extension: whitelist rules removes: java
	$woo_worldpay = 'woocommerce/woocommerce.php';
	$woo_worldpay_active = in_array( $woo_worldpay, apply_filters('active_plugins', get_option('active_plugins')));
	$woo_worldpay_fix = '';

	if ( $woo_worldpay_active == 1 || is_plugin_active_for_network( $woo_worldpay ) ) {
		$woo_worldpay_fix = __('WooCommerce WorldPay Extension BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p4 = array('/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(havij(.*)\[NC,OR\]/', '/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(;\|\<\|\>\|\'\|\"\|(.*)\[NC,OR\]/');
		$r4 = array("RewriteCond %{HTTP_USER_AGENT} (havij|libwww-perl|wget|python|nikto|curl|scan|winhttp|clshttp|loader) [NC,OR]", "RewriteCond %{HTTP_USER_AGENT} (;|<|>|'|".'"'."|\)|\(|%0A|%0D|%22|%27|%28|%3C|%3E|%00).*(libwww-perl|wget|python|nikto|curl|scan|winhttp|HTTrack|clshttp|archiver|loader|email|harvest|extract|grab|miner) [NC,OR]");

	} else {
		$p4 = array();
		$r4 = array();
	}

	## Kama Click Counter Plugin: whitelist rules
	$kama_click_counter = 'kama-clic-counter/kama_click_counter.php';
	$kama_click_counter_active = in_array( $kama_click_counter, apply_filters('active_plugins', get_option('active_plugins')));
	$kama_click_counter_fix = '';

	if ( $kama_click_counter_active == 1 || is_plugin_active_for_network( $kama_click_counter ) ) {
		$kama_click_counter_fix = __('Kama Click Counter Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p5 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r5 = array("# BPS AutoWhitelist QS2: Kama Click Counter Plugin", "# BPS AutoWhitelist QS3: Kama Click Counter Plugin", "# BPS AutoWhitelist QS4: Kama Click Counter Plugin");

	} else {
		$p5 = array();
		$r5 = array();
	}

	## Riva Slider Pro Plugin: whitelist rules
	$riva_slider_pro = 'riva-slider-pro/setup.php';
	$riva_slider_pro_active = in_array( $riva_slider_pro, apply_filters('active_plugins', get_option('active_plugins')));
	$riva_slider_pro_fix = '';

	if ( $riva_slider_pro_active == 1 || is_plugin_active_for_network( $riva_slider_pro ) ) {
		$riva_slider_pro_fix = __('Riva Slider Pro Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p6 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r6 = array("# BPS AutoWhitelist QS2: Riva Slider Pro Plugin", "# BPS AutoWhitelist QS3: Riva Slider Pro Plugin", "# BPS AutoWhitelist QS4: Riva Slider Pro Plugin");

	} else {
		$p6 = array();
		$r6 = array();
	}

	## WordPress Auto Spinner Plugin: whitelist rules removes: curl and java
	$wp_auto_spinner = 'wp-auto-spinner/wp-auto-spinner.php';
	$wp_auto_spinner_active = in_array( $wp_auto_spinner, apply_filters('active_plugins', get_option('active_plugins')));
	$wp_auto_spinner_fix = '';

	if ( $wp_auto_spinner_active == 1 || is_plugin_active_for_network( $wp_auto_spinner ) ) {
		$wp_auto_spinner_fix = __('WordPress Auto Spinner Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p7 = array('/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(havij(.*)\[NC,OR\]/', '/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(;\|\<\|\>\|\'\|\"\|(.*)\[NC,OR\]/');
		$r7 = array("RewriteCond %{HTTP_USER_AGENT} (havij|libwww-perl|wget|python|nikto|scan|winhttp|clshttp|loader) [NC,OR]", "RewriteCond %{HTTP_USER_AGENT} (;|<|>|'|".'"'."|\)|\(|%0A|%0D|%22|%27|%28|%3C|%3E|%00).*(libwww-perl|wget|python|nikto|scan|winhttp|HTTrack|clshttp|archiver|loader|email|harvest|extract|grab|miner) [NC,OR]");

	} else {
		$p7 = array();
		$r7 = array();
	}

	## AgriTurismo theme Theme: whitelist rules
	$AgriTurismo_theme = wp_get_theme( 'agritourismo-theme' );
	$AgriTurismo_theme_fix = '';

	if ( $AgriTurismo_theme->exists() ) {
		$AgriTurismo_theme_fix = __('AgriTurismo Theme BPSQSE AutoWhitelist successful', 'bulletproof-security');

		$p8 = array('/RewriteCond\s%\{QUERY_STRING\}\s\^\.\*\(.*\|\<\|\>\|%3c\|%3e\)\.\*\s\[NC,OR\]/');
		$r8 = array("# BPS AutoWhitelist QS5: AgriTurismo Theme");

	} else {
		$p8 = array();
		$r8 = array();
	}

	## WP Content Copy Protection Pro Plugin: whitelist rules
	$wccp_pro = 'wccp-pro/preventer-index.php';
	$wccp_pro_active = in_array( $wccp_pro, apply_filters('active_plugins', get_option('active_plugins')));
	$wccp_pro_fix = '';

	if ( $wccp_pro_active == 1 || is_plugin_active_for_network( $wccp_pro ) ) {
		$wccp_pro_fix = __('WP Content Copy Protection Pro Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p9 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r9 = array("# BPS AutoWhitelist QS2: WP Content Copy Protection Pro Plugin", "# BPS AutoWhitelist QS3: WP Content Copy Protection Pro Plugin", "# BPS AutoWhitelist QS4: WP Content Copy Protection Pro Plugin");

	} else {
		$p9 = array();
		$r9 = array();
	}

	## PanoPress Plugin: whitelist rules
	$panopress = 'panopress/panopress.php';
	$panopress_active = in_array( $panopress, apply_filters('active_plugins', get_option('active_plugins')));
	$panopress_fix = '';

	if ( $panopress_active == 1 || is_plugin_active_for_network( $panopress ) ) {
		$panopress_fix = __('PanoPress Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p10 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r10 = array("# BPS AutoWhitelist QS2: PanoPress Plugin", "# BPS AutoWhitelist QS3: PanoPress Plugin", "# BPS AutoWhitelist QS4: PanoPress Plugin");

	} else {
		$p10 = array();
		$r10 = array();
	}

	## Easy Social Share Buttons (Code Canyon) Plugin: whitelist rules
	$essb_code_canyon = 'easy-social-share-buttons3/easy-social-share-buttons3.php';
	$essb_code_canyon_active = in_array( $essb_code_canyon, apply_filters('active_plugins', get_option('active_plugins')));
	$essb_code_canyon_fix = '';

	if ( $essb_code_canyon_active == 1 || is_plugin_active_for_network( $essb_code_canyon ) ) {
		$essb_code_canyon_fix = __('Easy Social Share Buttons (Code Canyon) Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p11 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r11 = array("# BPS AutoWhitelist QS2: Easy Social Share Buttons (Code Canyon) Plugin", "# BPS AutoWhitelist QS3: Easy Social Share Buttons (Code Canyon) Plugin", "# BPS AutoWhitelist QS4: Easy Social Share Buttons (Code Canyon) Plugin");

	} else {
		$p11 = array();
		$r11 = array();
	}

	## MainWP Plugin: whitelist rules removes: order
	$mainwp = 'mainwp/mainwp.php';
	$mainwp_active = in_array( $mainwp, apply_filters('active_plugins', get_option('active_plugins')));
	$mainwp_fix = '';

	if ( $mainwp_active == 1 || is_plugin_active_for_network( $mainwp ) ) {
		$mainwp_fix = __('MainWP Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p12 = array('/RewriteCond\s%\{QUERY_STRING\}\s\(;\|\<\|\>\|\'\|(.*)order\|script\|set\|md5\|benchmark\|encode\)\s\[NC,OR\]/');
		$r12 = array("RewriteCond %{QUERY_STRING} (;|<|>|'|".'"'."|\)|%0A|%0D|%22|%27|%3C|%3E|%00).*(/\*|union|select|insert|drop|delete|update|cast|create|char|convert|alter|declare|script|set|md5|benchmark|encode) [NC,OR]");

	} else {
		$p12 = array();
		$r12 = array();
	}

	## Clever Course Theme: whitelist rules
	$clevercourse_theme = wp_get_theme( 'clevercourse' );
	$clevercourse_theme_fix = '';

	if ( $clevercourse_theme->exists() ) {
		$clevercourse_theme_fix = __('Clever Course Theme BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p13 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r13 = array("# BPS AutoWhitelist QS2: Clever Course Theme", "# BPS AutoWhitelist QS3: Clever Course Theme", "# BPS AutoWhitelist QS4: Clever Course Theme");

	} else {
		$p13 = array();
		$r13 = array();
	}

	## WP eStore (wp cart for digital products): whitelist rules CCBill Webhooks removes: curl and java
	$wp_estore = 'wp-cart-for-digital-products/wp_cart_for_digital_products.php';
	$wp_estore_active = in_array( $wp_estore, apply_filters('active_plugins', get_option('active_plugins')));
	$wp_estore_fix = '';

	if ( $wp_estore_active == 1 || is_plugin_active_for_network( $wp_estore ) ) {
		$wp_estore_fix = __('WP eStore (WP Cart for Digital Products) Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p14 = array('/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(havij(.*)\[NC,OR\]/', '/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(;\|\<\|\>\|\'\|\"\|(.*)\[NC,OR\]/');
		$r14 = array("RewriteCond %{HTTP_USER_AGENT} (havij|libwww-perl|wget|python|nikto|scan|winhttp|clshttp|loader) [NC,OR]", "RewriteCond %{HTTP_USER_AGENT} (;|<|>|'|".'"'."|\)|\(|%0A|%0D|%22|%27|%28|%3C|%3E|%00).*(libwww-perl|wget|python|nikto|scan|winhttp|HTTrack|clshttp|archiver|loader|email|harvest|extract|grab|miner) [NC,OR]");

	} else {
		$p14 = array();
		$r14 = array();
	}

	## WP eMember: whitelist rules CCBill Webhooks removes: curl and java
	$wp_emember = 'wp-eMember/wp_eMember.php';
	$wp_emember_active = in_array( $wp_emember, apply_filters('active_plugins', get_option('active_plugins')));
	$wp_emember_fix = '';

	if ( $wp_emember_active == 1 || is_plugin_active_for_network( $wp_emember ) ) {
		$wp_emember_fix = __('WP eMember Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p15 = array('/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(havij(.*)\[NC,OR\]/', '/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(;\|\<\|\>\|\'\|\"\|(.*)\[NC,OR\]/');
		$r15 = array("RewriteCond %{HTTP_USER_AGENT} (havij|libwww-perl|wget|python|nikto|scan|winhttp|clshttp|loader) [NC,OR]", "RewriteCond %{HTTP_USER_AGENT} (;|<|>|'|".'"'."|\)|\(|%0A|%0D|%22|%27|%28|%3C|%3E|%00).*(libwww-perl|wget|python|nikto|scan|winhttp|HTTrack|clshttp|archiver|loader|email|harvest|extract|grab|miner) [NC,OR]");

	} else {
		$p15 = array();
		$r15 = array();
	}

	## Easy Digital Downloads Plugin: whitelist rules
	$easy_digital_downloads = 'easy-digital-downloads/easy-digital-downloads.php';
	$easy_digital_downloads_active = in_array( $easy_digital_downloads, apply_filters('active_plugins', get_option('active_plugins')));
	$easy_digital_downloads_fix = '';

	if ( $easy_digital_downloads_active == 1 || is_plugin_active_for_network( $easy_digital_downloads ) ) {
		$easy_digital_downloads_fix = __('Easy Digital Downloads Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p16 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r16 = array("# BPS AutoWhitelist QS2: Easy Digital Downloads Plugin", "# BPS AutoWhitelist QS3: Easy Digital Downloads Plugin", "# BPS AutoWhitelist QS4: Easy Digital Downloads Plugin");

	} else {
		$p16 = array();
		$r16 = array();
	}

	## MailPoet Newsletters (wysija newsletters) Plugin: whitelist rules removes: wget, curl and java
	$mailpoet = 'wysija-newsletters/index.php';
	$mailpoet_active = in_array( $mailpoet, apply_filters('active_plugins', get_option('active_plugins')));
	$mailpoet_fix = '';

	if ( $mailpoet_active == 1 || is_plugin_active_for_network( $mailpoet ) ) {
		$mailpoet_fix = __('MailPoet Newsletters (wysija newsletters) Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p17 = array('/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(havij(.*)\[NC,OR\]/', '/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(;\|\<\|\>\|\'\|\"\|(.*)\[NC,OR\]/');
		$r17 = array("RewriteCond %{HTTP_USER_AGENT} (havij|libwww-perl|python|nikto|scan|winhttp|clshttp|loader) [NC,OR]", "RewriteCond %{HTTP_USER_AGENT} (;|<|>|'|".'"'."|\)|\(|%0A|%0D|%22|%27|%28|%3C|%3E|%00).*(libwww-perl|python|nikto|scan|winhttp|HTTrack|clshttp|archiver|loader|email|harvest|extract|grab|miner) [NC,OR]");

	} else {
		$p17 = array();
		$r17 = array();
	}

	## MailChimp for WordPress Plugin: whitelist rules remove apostrophes and round brackets
	$mailchimp = 'mailchimp-for-wp/mailchimp-for-wp.php';
	$mailchimp_active = in_array( $mailchimp, apply_filters('active_plugins', get_option('active_plugins')));
	$mailchimp_fix = '';

	if ( $mailchimp_active == 1 || is_plugin_active_for_network( $mailchimp ) ) {
		$mailchimp_fix = __('MailChimp for WordPress Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p18 = array('/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(%0A\|%0D\|%27\|%3C\|%3E\|%00\)\s\[NC,OR\]/', '/RewriteCond\s%\{HTTP_REFERER\}\s\(%0A\|%0D\|%27\|%3C\|%3E\|%00\)\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\^\.\*\(.*\|\<\|\>\|%3c\|%3e\)\.\*\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(<\|>\|\'\|%0A\|%0D\|%27\|%3C\|%3E\|%00\)\s\[NC,OR\]/');
		$r18 = array("RewriteCond %{HTTP_USER_AGENT} (%0A|%0D|%3C|%3E|%00) [NC,OR]", "RewriteCond %{HTTP_REFERER} (%0A|%0D|%3C|%3E|%00) [NC,OR]", "RewriteCond %{QUERY_STRING} ^.*(<|>|%3c|%3e).* [NC,OR]", "RewriteCond %{QUERY_STRING} (<|>|%0A|%0D|%3C|%3E|%00) [NC,OR]");

	} else {
		$p18 = array();
		$r18 = array();
	}

	## Digital Access Pass (DAP) Plugin: whitelist rules
	$DAPLiveLinks = 'DAP-WP-LiveLinks/DAP-WP-LiveLinks.php';
	$DAPLiveLinks_active = in_array( $DAPLiveLinks, apply_filters('active_plugins', get_option('active_plugins')));
	$DAPLiveLinks_fix = '';

	if ( $DAPLiveLinks_active == 1 || is_plugin_active_for_network( $DAPLiveLinks ) ) {
		$DAPLiveLinks_fix = __('Digital Access Pass (DAP) Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p19 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r19 = array("# BPS AutoWhitelist QS2: Digital Access Pass (DAP) Plugin", "# BPS AutoWhitelist QS3: Digital Access Pass (DAP) Plugin", "# BPS AutoWhitelist QS4: Digital Access Pass (DAP) Plugin");

	} else {
		$p19 = array();
		$r19 = array();
	}

	// WordPress Newsletter (tribulant) Plugin: whitelist rules removes: wget, curl and java
	$wp_newsletter = 'wp-mailinglist/wp-mailinglist.php';
	$wp_newsletter_active = in_array( $wp_newsletter, apply_filters('active_plugins', get_option('active_plugins')));
	$wp_newsletter_fix = '';

	if ( $wp_newsletter_active == 1 || is_plugin_active_for_network( $wp_newsletter ) ) {
		$wp_newsletter_fix = __('WordPress Newsletter (tribulant) Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p20 = array('/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(havij(.*)\[NC,OR\]/', '/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(;\|\<\|\>\|\'\|\"\|(.*)\[NC,OR\]/');
		$r20 = array("RewriteCond %{HTTP_USER_AGENT} (havij|libwww-perl|python|nikto|scan|winhttp|clshttp|loader) [NC,OR]", "RewriteCond %{HTTP_USER_AGENT} (;|<|>|'|".'"'."|\)|\(|%0A|%0D|%22|%27|%28|%3C|%3E|%00).*(libwww-perl|python|nikto|scan|winhttp|HTTrack|clshttp|archiver|loader|email|harvest|extract|grab|miner) [NC,OR]");		
		
	} else {
		$p20 = array();
		$r20 = array();
	}

	## Subscribe To Comments Reloaded Plugin: whitelist rules
	$sctocr = 'subscribe-to-comments-reloaded/subscribe-to-comments-reloaded.php';
	$sctocr_active = in_array( $sctocr, apply_filters('active_plugins', get_option('active_plugins')));
	$sctocr_fix = '';

	if ( $sctocr_active == 1 || is_plugin_active_for_network( $sctocr ) ) {
		$sctocr_fix = __('Subscribe To Comments Reloaded Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p21 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r21 = array("# BPS AutoWhitelist QS2: Subscribe To Comments Reloaded Plugin", "# BPS AutoWhitelist QS3: Subscribe To Comments Reloaded Plugin", "# BPS AutoWhitelist QS4: Subscribe To Comments Reloaded Plugin");

	} else {
		$p21 = array();
		$r21 = array();
	}

	## Nextend Social Login Plugin: whitelist rules
	$nextend_social_login = 'nextend-facebook-connect/nextend-facebook-connect.php';
	$nextend_social_login_active = in_array( $nextend_social_login, apply_filters('active_plugins', get_option('active_plugins')));
	$nextend_social_login_fix = '';
	
	if ( $nextend_social_login_active == 1 || is_plugin_active_for_network( $nextend_social_login ) ) {
		$nextend_social_login_fix = __('Nextend Social Login Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p22 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r22 = array("# BPS AutoWhitelist QS2: Nextend Social Login Plugin", "# BPS AutoWhitelist QS3: Nextend Social Login Plugin", "# BPS AutoWhitelist QS4: Nextend Social Login Plugin");

	} else {
		$p22 = array();
		$r22 = array();
	}

	## Business Directory Plugin: whitelist rules
	$business_directory_plugin = 'business-directory-plugin/business-directory-plugin.php';
	$business_directory_plugin_active = in_array( $business_directory_plugin, apply_filters('active_plugins', get_option('active_plugins')));
	$business_directory_plugin_fix = '';
	
	if ( $business_directory_plugin_active == 1 || is_plugin_active_for_network( $business_directory_plugin ) ) {
		$business_directory_plugin_fix = __('Business Directory Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p23 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r23 = array("# BPS AutoWhitelist QS2: Business Directory Plugin", "# BPS AutoWhitelist QS3: Business Directory Plugin", "# BPS AutoWhitelist QS4: Business Directory Plugin");

	} else {
		$p23 = array();
		$r23 = array();
	}

	## Constant Contact + WooCommerce Plugin: whitelist rules
	$constant_contact_woocommerce_plugin = 'constant-contact-woocommerce/plugin.php';
	$constant_contact_woocommerce_plugin_active = in_array( $constant_contact_woocommerce_plugin, apply_filters('active_plugins', get_option('active_plugins')));
	$constant_contact_woocommerce_plugin_fix = '';
	
	if ( $constant_contact_woocommerce_plugin_active == 1 || is_plugin_active_for_network( $constant_contact_woocommerce_plugin ) ) {
		$constant_contact_woocommerce_plugin_fix = __('Constant Contact + WooCommerce Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p24 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r24 = array("# BPS AutoWhitelist QS2: Constant Contact + WooCommerce Plugin", "# BPS AutoWhitelist QS3: Constant Contact + WooCommerce Plugin", "# BPS AutoWhitelist QS4: Constant Contact + WooCommerce Plugin");

	} else {
		$p24 = array();
		$r24 = array();
	}

	## Constant Contact Forms Plugin: whitelist rules
	$constant_contact_forms_plugin = 'constant-contact-forms/constant-contact-forms.php';
	$constant_contact_forms_plugin_active = in_array( $constant_contact_forms_plugin, apply_filters('active_plugins', get_option('active_plugins')));
	$constant_contact_forms_plugin_fix = '';
	
	if ( $constant_contact_forms_plugin_active == 1 || is_plugin_active_for_network( $constant_contact_forms_plugin ) ) {
		$constant_contact_forms_plugin_fix = __('Constant Contact Forms Plugin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p25 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r25 = array("# BPS AutoWhitelist QS2: Constant Contact Forms Plugin", "# BPS AutoWhitelist QS3: Constant Contact Forms Plugin", "# BPS AutoWhitelist QS4: Constant Contact Forms Plugin");

	} else {
		$p25 = array();
		$r25 = array();
	}

	$pattern_array = array_merge($p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9, $p10, $p11, $p12, $p13, $p14, $p15, $p16, $p17, $p18, $p19, $p20, $p21, $p22, $p23, $p24, $p25);
	$replace_array = array_merge($r1, $r2, $r3, $r4, $r5, $r6, $r7, $r8, $r9, $r10, $r11, $r12, $r13, $r14, $r15, $r16, $r17, $r18, $r19, $r20, $r21, $r22, $r23, $r24, $r25);

	if ( $CC_Options_root['bps_customcode_bpsqse'] != '' ) {		
		$bps_customcode_bpsqse_replace = preg_replace($pattern_array, $replace_array, $bps_customcode_bpsqse_array);
	} else {
		$bps_customcode_bpsqse_replace = preg_replace($pattern_array, $replace_array, $bps_customcode_bpsqse_code_array);			
	}

	$bps_customcode_bpsqse_implode = implode( "\n", $bps_customcode_bpsqse_replace );

	if ( ! is_multisite() ) {

		$Root_CC_Options = array(
		'bps_customcode_one' 				=> $CC_Options_root['bps_customcode_one'], 
		'bps_customcode_server_signature' 	=> $CC_Options_root['bps_customcode_server_signature'], 
		'bps_customcode_directory_index' 	=> $CC_Options_root['bps_customcode_directory_index'], 
		'bps_customcode_server_protocol' 	=> $CC_Options_root['bps_customcode_server_protocol'], 
		'bps_customcode_error_logging' 		=> $CC_Options_root['bps_customcode_error_logging'], 
		'bps_customcode_deny_dot_folders' 	=> $CC_Options_root['bps_customcode_deny_dot_folders'], 
		'bps_customcode_admin_includes' 	=> $CC_Options_root['bps_customcode_admin_includes'], 
		'bps_customcode_wp_rewrite_start' 	=> $CC_Options_root['bps_customcode_wp_rewrite_start'], 
		'bps_customcode_request_methods' 	=> $CC_Options_root['bps_customcode_request_methods'], 
		'bps_customcode_two' 				=> $CC_Options_root['bps_customcode_two'], 
		'bps_customcode_timthumb_misc' 		=> $CC_Options_root['bps_customcode_timthumb_misc'], 
		'bps_customcode_bpsqse' 			=> trim($bps_customcode_bpsqse_implode), 
		'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
		'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
		);
				
	} else {
					
		$Root_CC_Options = array(
		'bps_customcode_one' 				=> $CC_Options_root['bps_customcode_one'], 
		'bps_customcode_server_signature' 	=> $CC_Options_root['bps_customcode_server_signature'], 
		'bps_customcode_directory_index' 	=> $CC_Options_root['bps_customcode_directory_index'], 
		'bps_customcode_server_protocol' 	=> $CC_Options_root['bps_customcode_server_protocol'], 
		'bps_customcode_error_logging' 		=> $CC_Options_root['bps_customcode_error_logging'], 
		'bps_customcode_deny_dot_folders' 	=> $CC_Options_root['bps_customcode_deny_dot_folders'], 
		'bps_customcode_admin_includes' 	=> $CC_Options_root['bps_customcode_admin_includes'], 
		'bps_customcode_wp_rewrite_start' 	=> $CC_Options_root['bps_customcode_wp_rewrite_start'], 
		'bps_customcode_request_methods' 	=> $CC_Options_root['bps_customcode_request_methods'], 
		'bps_customcode_two' 				=> $CC_Options_root['bps_customcode_two'], 
		'bps_customcode_timthumb_misc' 		=> $CC_Options_root['bps_customcode_timthumb_misc'], 
		'bps_customcode_bpsqse' 			=> trim($bps_customcode_bpsqse_implode), 
		'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
		'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
		'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
		);					
	}

	foreach( $Root_CC_Options as $key => $value ) {
		update_option('bulletproof_security_options_customcode', $Root_CC_Options);
	}

	$success_array = array($woo_PagSeguro_fix, $event_espresso_fix, $woo_serial_key_fix, $woo_worldpay_fix, $kama_click_counter_fix, $riva_slider_pro_fix, $wp_auto_spinner_fix, $AgriTurismo_theme_fix, $wccp_pro_fix, $panopress_fix, $essb_code_canyon_fix, $mainwp_fix, $clevercourse_theme_fix, $wp_estore_fix, $wp_emember_fix, $easy_digital_downloads_fix, $mailpoet_fix, $mailchimp_fix, $DAPLiveLinks_fix, $wp_newsletter_fix, $sctocr_fix, $nextend_social_login_fix, $business_directory_plugin_fix, $constant_contact_woocommerce_plugin_fix, $constant_contact_forms_plugin_fix);
	
	foreach ( $success_array as $successMessage ) {
		
		if ( $successMessage != '' ) {
			echo '<font color="green"><strong>'.$successMessage.'</strong></font><br>';
		}
	}
}

## 3. CUSTOM CODE WPADMIN PLUGIN/FILE SKIP RULES
function bpsPro_Pwizard_Autofix_plugin_skip_bypass_wpadmin() {

	$AutoFix_Options = get_option('bulletproof_security_options_wizard_autofix');
	
	if ( $AutoFix_Options['bps_wizard_autofix'] == 'Off' ) {
		return;
	}	
	
	global $counter;
	$counter = 2;
	
	$CC_Options_wpadmin = get_option('bulletproof_security_options_customcode_WPA');
	$bps_customcode_two_wpa_options = ! isset($CC_Options_wpadmin['bps_customcode_two_wpa']) ? '' : $CC_Options_wpadmin['bps_customcode_two_wpa'];
	$bps_customcode_two_wpa = htmlspecialchars_decode( $bps_customcode_two_wpa_options, ENT_QUOTES );
	$bps_customcode_two_wpa_array = array();
	$bps_customcode_two_wpa_array[] = $bps_customcode_two_wpa;
	$bps_get_wp_root_secure = bps_wp_get_root_folder();
	$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
	$bps_theme_dir = str_replace( ABSPATH, '', get_theme_root() );
	$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
	
	## WooCommerce Product Feed Pro Plugin: whitelist rules
	$woo_pfeed_pro = 'webappick-product-feed-for-woocommerce-pro/webappick-product-feed-for-woocommerce-pro.php';
	$woo_pfeed_pro_active = in_array( $woo_pfeed_pro, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern1 = '/RewriteCond\s%{QUERY_STRING}\spage=woo_feed_manage_feed\(\.\*\)\s\[NC\]/';
	$woo_pfeed_pro_array = array();
	$woo_pfeed_pro_fix = '';
	
	if ( $woo_pfeed_pro_active == 1 || is_plugin_active_for_network( $woo_pfeed_pro ) ) {
		$woo_pfeed_pro_fix = __('WooCommerce Product Feed Pro Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');		

		if ( ! preg_match( $pattern1, $bps_customcode_two_wpa ) ) {
		
			$woo_pfeed_pro_array[] = "# WooCommerce Product Feed Pro Query String skip/bypass rule
RewriteCond %{QUERY_STRING} page=woo_feed_manage_feed(.*) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## WPBakery Visual Composer Plugin: whitelist rules
	$visual_composer = 'js_composer/js_composer.php';
	$visual_composer_active = in_array( $visual_composer, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern2 = '/RewriteCond\s%{REQUEST_URI}\s\(post\\\.php\)\s\[NC\]/';
	$visual_composer_array = array();
	$visual_composer_fix = '';
	
	if ( $visual_composer_active == 1 || is_plugin_active_for_network( $visual_composer ) ) {
		$visual_composer_fix = __('WPBakery Visual Composer Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern2, $bps_customcode_two_wpa ) ) {
		
			$visual_composer_array[] = "# post.php skip/bypass rule
RewriteCond %{REQUEST_URI} (post\.php) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Bookly Booking Plugin: whitelist rules
	$bookly_booking = 'appointment-booking/main.php';
	$bookly_booking_active = in_array( $bookly_booking, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern3 = '/RewriteCond\s%{REQUEST_URI}\s\(admin-ajax\\\.php\)\s\[NC\]/';
	$bookly_booking_array = array();
	$bookly_booking_fix = '';

	if ( $bookly_booking_active == 1 || is_plugin_active_for_network( $bookly_booking ) ) {
		$bookly_booking_fix = __('Bookly Booking Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern3, $bps_customcode_two_wpa ) ) {
		
			$bookly_booking_array[] = "# admin-ajax.php skip/bypass rule
RewriteCond %{REQUEST_URI} (admin-ajax\.php) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Easy Media Gallery Pro Plugin: whitelist rules
	$emg_pro = 'easy-media-gallery-pro/easy-media-gallery-pro.php';
	$emg_pro_active = in_array( $emg_pro, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern4 = '/RewriteCond\s%{REQUEST_URI}\s\(admin-ajax\\\.php\)\s\[NC\]/';
	$emg_pro_array = array();
	$emg_pro_fix = '';
	
	if ( $emg_pro_active == 1 || is_plugin_active_for_network( $emg_pro ) ) {
		$emg_pro_fix = __('Easy Media Gallery Pro Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern4, $bps_customcode_two_wpa ) ) {

			$emg_pro_array[] = "# admin-ajax.php skip/bypass rule
RewriteCond %{REQUEST_URI} (admin-ajax\.php) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## NextGen Gallery Plugin: whitelist rules
	$nextgen_gallery = 'nextgen-gallery/nggallery.php';
	$nextgen_gallery_active = in_array( $nextgen_gallery, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern5 = '/RewriteCond\s%{QUERY_STRING}\spage=nggallery-manage-gallery\(\.\*\)\s\[NC\]/';
	$nextgen_gallery_array = array();
	$nextgen_gallery_fix = '';
	
	if ( $nextgen_gallery_active == 1 || is_plugin_active_for_network( $nextgen_gallery ) ) {
		$nextgen_gallery_fix = __('NextGen Gallery Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern5, $bps_customcode_two_wpa ) ) {

			$nextgen_gallery_array[] = "# NextGen Gallery Query String skip/bypass rule
RewriteCond %{QUERY_STRING} page=nggallery-manage-gallery(.*) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## OptimizePress Theme: whitelist rules
	$OptimizePress_theme = wp_get_theme( 'optimizePressTheme' );
	$pattern6 = '/RewriteCond\s%{QUERY_STRING}\spage=optimizepress-page-builder\(\.\*\)\s\[NC\]/';
	$OptimizePress_theme_array = array();
	$OptimizePress_theme_fix = '';

	if ( $OptimizePress_theme->exists() ) {
		$OptimizePress_theme_fix = __('OptimizePress Theme wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern6, $bps_customcode_two_wpa ) ) {

			$OptimizePress_theme_array[] = "# OptimizePress Theme Query String skip/bypass rule
RewriteCond %{QUERY_STRING} page=optimizepress-page-builder(.*) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## tribulant Shopping Cart (WP Checkout) Plugin: whitelist rules
	$wp_checkout = 'wp-checkout/wp-checkout.php';
	$wp_checkout_active = in_array( $wp_checkout, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern7 = '/RewriteCond\s%{REQUEST_URI}\s\(admin-ajax\\\.php\)\s\[NC\]/';
	$wp_checkout_array = array();
	$wp_checkout_fix = '';

	if ( $wp_checkout_active == 1 || is_plugin_active_for_network( $wp_checkout ) ) {
		$wp_checkout_fix = __('tribulant Shopping Cart (WP Checkout) Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern7, $bps_customcode_two_wpa ) ) {

			$wp_checkout_array[] = "# admin-ajax.php skip/bypass rule
RewriteCond %{REQUEST_URI} (admin-ajax\.php) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## ithemes Video Showcase Plugin: whitelist rules
	$video_showcase = 'videoshowcase/videoshowcase.php';
	$video_showcase_active = in_array( $video_showcase, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern8 = '/RewriteCond\s%{REQUEST_URI}\s\(admin-ajax\\\.php\)\s\[NC\]/';
	$video_showcase_array = array();
	$video_showcase_fix = '';
	
	if ( $video_showcase_active == 1 || is_plugin_active_for_network( $video_showcase ) ) {
		$video_showcase_fix = __('ithemes Video Showcase Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern8, $bps_customcode_two_wpa ) ) {

			$video_showcase_array[] = "# admin-ajax.php skip/bypass rule
RewriteCond %{REQUEST_URI} (admin-ajax\.php) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## WP-Invoice - Web Invoice and Billing Plugin: whitelist rules
	$wp_invoice = 'wp-invoice/wp-invoice.php';
	$wp_invoice_active = in_array( $wp_invoice, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern9 = '/RewriteCond\s%{QUERY_STRING}\spage=wpi_\(\.\*\)\s\[NC\]/';
	$wp_invoice_array = array();
	$wp_invoice_fix = '';
	
	if ( $wp_invoice_active == 1 || is_plugin_active_for_network( $wp_invoice ) ) {
		$wp_invoice_fix = __('WP-Invoice - Web Invoice and Billing Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern9, $bps_customcode_two_wpa ) ) {

			$wp_invoice_array[] = "# WP-Invoice - Web Invoice and Billing Query String skip/bypass rule
RewriteCond %{QUERY_STRING} page=wpi_(.*) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Yoast SEO Plugin: whitelist rules
	$yoast_seo = 'wordpress-seo/wp-seo.php';
	$yoast_seo_active = in_array( $yoast_seo, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern10 = '/RewriteCond\s%{QUERY_STRING}\spage=wpseo_social&key=\(\.\*\)\s\[NC\]/';
	$yoast_seo_array = array();
	$yoast_seo_fix = '';
	
	if ( $yoast_seo_active == 1 || is_plugin_active_for_network( $yoast_seo ) ) {
		$yoast_seo_fix = __('Yoast SEO Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern10, $bps_customcode_two_wpa ) ) {

			$yoast_seo_array[] = "# Yoast SEO Query String skip/bypass rule
RewriteCond %{QUERY_STRING} page=wpseo_social&key=(.*) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Formidable Pro Plugin: whitelist rules
	$formidable_pro = WP_PLUGIN_DIR . '/formidable/pro/formidable-pro.php';
	$pattern11 = '/RewriteCond\s%{QUERY_STRING}\splugin=formidable&controller=settings\(\.\*\)\s\[NC\]/';
	$formidable_pro_array = array();
	$formidable_pro_fix = '';

	if ( file_exists($formidable_pro) ) {
		$formidable_pro_fix = __('Formidable Pro Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern11, $bps_customcode_two_wpa ) ) {

			$formidable_pro_array[] = "# Formidable Pro Query String skip/bypass rule
RewriteCond %{QUERY_STRING} plugin=formidable&controller=settings(.*) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Google Typography Plugin: whitelist rules
	$google_typography = 'google-typography/google-typography.php';
	$google_typography_active = in_array( $google_typography, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern12 = '/RewriteCond\s%{REQUEST_URI}\s\(admin-ajax\\\.php\)\s\[NC\]/';
	$google_typography_array = array();
	$google_typography_fix = '';

	if ( $google_typography_active == 1 || is_plugin_active_for_network( $google_typography ) ) {
		$google_typography_fix = __('Google Typography Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern12, $bps_customcode_two_wpa ) ) {

			$google_typography_array[] = "# admin-ajax.php skip/bypass rule
RewriteCond %{REQUEST_URI} (admin-ajax\.php) [NC]
RewriteRule . - [S=99]";
		}
	}

	## Flare Plugin: whitelist rules
	$flare = 'flare/flare.php';
	$flare_active = in_array( $flare, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern13 = '/RewriteCond\s%{REQUEST_URI}\s\(admin-ajax\\\.php\)\s\[NC\]/';
	$flare_array = array();
	$flare_fix = '';

	if ( $flare_active == 1 || is_plugin_active_for_network( $flare ) ) {
		$flare_fix = __('Flare Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern13, $bps_customcode_two_wpa ) ) {

			$flare_array[] = "# admin-ajax.php skip/bypass rule
RewriteCond %{REQUEST_URI} (admin-ajax\.php) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## bbPress Plugin: whitelist rules
	$bbPress = 'bbpress/bbpress.php';
	$bbPress_active = in_array( $bbPress, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern14 = '/RewriteCond\s%{REQUEST_URI}\s\(post\\\.php\)\s\[NC\]/';
	$bbPress_array = array();
	$bbPress_fix = '';
	
	if ( $bbPress_active == 1 || is_plugin_active_for_network( $bbPress ) ) {
		$bbPress_fix = __('bbPress Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');		
		
		if ( ! preg_match( $pattern14, $bps_customcode_two_wpa ) ) {

			$bbPress_array[] = "# post.php skip/bypass rule
RewriteCond %{REQUEST_URI} (post\.php) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Spider Event Calendar (WordPress Event Calendar) Plugin: whitelist rules
	$spider_calendar = 'spider-event-calendar/calendar.php';
	$spider_calendar_active = in_array( $spider_calendar, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern15 = '/RewriteCond\s%{REQUEST_URI}\s\(admin-ajax\\\.php\)\s\[NC\]/';
	$spider_calendar_array = array();
	$spider_calendar_fix = '';
	
	if ( $spider_calendar_active == 1 || is_plugin_active_for_network( $spider_calendar ) ) {
		$spider_calendar_fix = __('Spider Event Calendar (WordPress Event Calendar) Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern15, $bps_customcode_two_wpa ) ) {

			$spider_calendar_array[] = "# admin-ajax.php skip/bypass rule
RewriteCond %{REQUEST_URI} (admin-ajax\.php) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## BuddyPress Plugin: whitelist rules Note: Only adds this whitelist rule if this option is set/checked: Private Messaging
	$buddypress = 'buddypress/bp-loader.php';
	$buddypress_active = in_array( $buddypress, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern16 = '/RewriteCond\s%{REQUEST_URI}\s\(admin-ajax\\\.php\)\s\[NC\]/';
	$buddypress_array = array();
	$buddypress_fix = '';

	if ( $buddypress_active == 1 || is_plugin_active_for_network( $buddypress ) ) {
		$bp_active_components = bp_get_option( 'bp-active-components' );
	
		foreach ( $bp_active_components as $key => $value ) {
			if ( $key == 'messages' ) {
				$buddypress_fix = __('BuddyPress Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

				if ( ! preg_match( $pattern16, $bps_customcode_two_wpa ) ) {

					$buddypress_array[] = "# admin-ajax.php skip/bypass rule
RewriteCond %{REQUEST_URI} (admin-ajax\.php) [NC]
RewriteRule . - [S=99]";
				}
			}
		}
	}

	## WPML Translation Management Plugin: whitelist rules
	$wpml_transman = 'wpml-translation-management/plugin.php';
	$wpml_transman_active = in_array( $wpml_transman, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern17 = '/RewriteCond\s%{QUERY_STRING}\spage=wpml-translation-management\(\.\*\)\s\[NC\]/';
	$wpml_transman_array = array();
	$wpml_transman_fix = '';
	
	if ( $wpml_transman_active == 1 || is_plugin_active_for_network( $wpml_transman ) ) {
		$wpml_transman_fix = __('WPML Translation Management Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern17, $bps_customcode_two_wpa ) ) {

			$wpml_transman_array[] = "# WPML Translation Management Query String skip/bypass rule
RewriteCond %{QUERY_STRING} page=wpml-translation-management(.*) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Events Manager Plugin: whitelist rules
	$events_manager = 'events-manager/events-manager.php';
	$events_manager_active = in_array( $events_manager, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern18 = '/RewriteCond\s%{REQUEST_URI}\s\(admin-ajax\\\.php\)\s\[NC\]/';
	$events_manager_array = array();
	$events_manager_fix = '';

	if ( $events_manager_active == 1 || is_plugin_active_for_network( $events_manager ) ) {
		$events_manager_fix = __('Events Manager Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern18, $bps_customcode_two_wpa ) ) {

			$events_manager_array[] = "# admin-ajax.php skip/bypass rule
RewriteCond %{REQUEST_URI} (admin-ajax\.php) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## MailPoet Newsletters (wysija newsletters) Plugin: whitelist rules
	$mailpoet = 'wysija-newsletters/index.php';
	$mailpoet_active = in_array( $mailpoet, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern19 = '/RewriteCond\s%{REQUEST_URI}\s\(admin-ajax\\\.php\)\s\[NC\]/';
	$mailpoet_array = array();
	$mailpoet_fix = '';

	if ( $mailpoet_active == 1 || is_plugin_active_for_network( $mailpoet ) ) {
		$mailpoet_fix = __('MailPoet Newsletters (wysija newsletters) Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');
	
		if ( ! preg_match( $pattern19, $bps_customcode_two_wpa ) ) {

			$mailpoet_array[] = "# admin-ajax.php skip/bypass rule
RewriteCond %{REQUEST_URI} (admin-ajax\.php) [NC]
RewriteRule . - [S=99]";
		}
	}
	
	## Event Espresso Plugin: whitelist rules
	$event_espresso1 = WP_PLUGIN_DIR . '/event-espresso-decaf/espresso.php';
	$event_espresso2 = WP_PLUGIN_DIR . '/event-espresso-free/espresso.php';
	$event_espresso3 = WP_PLUGIN_DIR . '/event-espresso/espresso.php';
	$event_espresso4 = WP_PLUGIN_DIR . '/event-espresso-core-master/espresso.php';
	$pattern20 = '/RewriteCond\s%{REQUEST_URI}\s\(admin\\\.php\)\s\[NC\]/';
	$event_espresso_array = array();
	$event_espresso_fix = '';

	if ( file_exists($event_espresso1) || file_exists($event_espresso2) || file_exists($event_espresso3) || file_exists($event_espresso4) ) {
		$event_espresso_fix = __('Event Espresso Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');	
		
		if ( ! preg_match( $pattern20, $bps_customcode_two_wpa ) ) {		
			$event_espresso_array[] = "# admin.php skip/bypass rule
RewriteCond %{REQUEST_URI} (admin\.php) [NC]
RewriteRule . - [S=99]";
		}
	}

	## Content Egg Free and Pro Plugin: whitelist rules
	$content_egg = 'content-egg/content-egg.php';
	$content_egg_active = in_array( $content_egg, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern21 = '/RewriteCond\s%{REQUEST_URI}\s\(admin-ajax\\\.php\)\s\[NC\]/';
	$content_egg_array = array();
	$content_egg_fix = '';
	
	if ( $content_egg_active == 1 || is_plugin_active_for_network( $content_egg ) ) {
		$content_egg_fix = __('Content Egg Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern21, $bps_customcode_two_wpa ) ) {

			$content_egg_array[] = "# admin-ajax.php skip/bypass rule
RewriteCond %{REQUEST_URI} (admin-ajax\.php) [NC]
RewriteRule . - [S=99]";
		}
	}

	## Flatsome Theme: whitelist rules
	$flatsome_theme = wp_get_theme( 'flatsome' );
	$pattern22 = '/RewriteCond\s%{REQUEST_URI}\s\(customize\\\.php\)\s\[NC\]/';
	$flatsome_theme_array = array();
	$flatsome_theme_fix = '';

	if ( $flatsome_theme->exists() ) {
		$flatsome_theme_fix = __('Flatsome Theme wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern22, $bps_customcode_two_wpa ) ) {

			$flatsome_theme_array[] = "# Flatsome Theme customize.php skip/bypass rule
RewriteCond %{REQUEST_URI} (customize\.php) [NC]
RewriteRule . - [S=99]";
		}
	}

	## Beaver Builder|Ultimate Addons Contact Form Plugin: whitelist rules
	$beaver_builder = 'bb-plugin/fl-builder.php';
	$beaver_builder_active = in_array( $beaver_builder, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern23 = '/RewriteCond\s%{REQUEST_URI}\s\(admin-ajax\\\.php\)\s\[NC\]/';
	$beaver_builder_array = array();
	$beaver_builder_fix = '';

	if ( $beaver_builder_active == 1 || is_plugin_active_for_network( $beaver_builder ) ) {
		$beaver_builder_fix = __('Beaver Builder Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern23, $bps_customcode_two_wpa ) ) {
		
			$beaver_builder_array[] = "# admin-ajax.php skip/bypass rule
RewriteCond %{REQUEST_URI} (admin-ajax\.php) [NC]
RewriteRule . - [S=99]";
		}
	}

	## WP Reset free and Pro Plugin: whitelist rules
	$wp_reset = 'wp-reset/wp-reset.php';
	$wp_reset_active = in_array( $wp_reset, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern24 = '/RewriteCond\s%{REQUEST_URI}\s\(admin-ajax\\\.php\)\s\[NC\]/';
	$wp_reset_array = array();
	$wp_reset_fix = '';

	if ( $wp_reset_active == 1 || is_plugin_active_for_network( $wp_reset ) ) {
		$wp_reset_fix = __('WP Reset Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');

		if ( ! preg_match( $pattern24, $bps_customcode_two_wpa ) ) {
		
			$wp_reset_array[] = "# admin-ajax.php skip/bypass rule
RewriteCond %{REQUEST_URI} (admin-ajax\.php) [NC]
RewriteRule . - [S=99]";
		}
	}

	## Bloom Email Opt-in Plugin: whitelist rules
	$bloom = 'bloom/bloom.php';
	$bloom_active = in_array( $bloom, apply_filters('active_plugins', get_option('active_plugins')));
	$pattern25 = '/RewriteCond\s%{QUERY_STRING}\soption_page=et_dashboard\(\.\*\)\s\[NC\]/';
	$bloom_array = array();
	$bloom_fix = '';
	
	if ( $bloom_active == 1 || is_plugin_active_for_network( $bloom ) ) {
		$bloom_fix = __('Bloom Email Opt-in Plugin wp-admin skip/bypass rule AutoWhitelist successful', 'bulletproof-security');		

		if ( ! preg_match( $pattern25, $bps_customcode_two_wpa ) ) {
		
			$bloom_array[] = "# Bloom Email Opt-In plugin Query String skip/bypass rule
RewriteCond %{QUERY_STRING} option_page=et_dashboard(.*) [NC]
RewriteRule . - [S=99]";
		}
	}

	$bps_customcode_two_wpa_array_impload = implode( "]", $bps_customcode_two_wpa_array );
	$bps_customcode_two_wpa_array_preg_split = preg_split("/\[S=\d{1,2}\]/", $bps_customcode_two_wpa_array_impload);
	$bps_customcode_two_wpa_array_preg_replace = preg_replace("/RewriteRule\s\.\s-\s/", "RewriteRule . - [S=99]", $bps_customcode_two_wpa_array_preg_split);
	$bps_customcode_two_wpa_array_filter = array_filter($bps_customcode_two_wpa_array_preg_replace);
	
	// Break the $bps_customcode_two_wpa_array value into separate arrays and cleans up the $bps_customcode_two_wpa_array values.
	$cc2_array = array();

	foreach ( $bps_customcode_two_wpa_array_filter as $key => $value ) {
		$cc2_array[] = trim( $value, " \t\n\r");
	}
	
	$bps_customcode_two_wpa_array_merge = array_merge($cc2_array, $woo_pfeed_pro_array, $visual_composer_array, $bookly_booking_array, $emg_pro_array, $nextgen_gallery_array, $OptimizePress_theme_array, $wp_checkout_array, $video_showcase_array, $wp_invoice_array, $yoast_seo_array, $formidable_pro_array, $google_typography_array, $flare_array, $bbPress_array, $spider_calendar_array, $buddypress_array, $wpml_transman_array, $events_manager_array, $mailpoet_array, $event_espresso_array, $content_egg_array, $flatsome_theme_array, $beaver_builder_array, $wp_reset_array, $bloom_array);

	$cc2_unique = array_unique($bps_customcode_two_wpa_array_merge);
	$S_replace = preg_replace_callback( '/(S=\d{1,2})/', 'bpsPro_S_number_count_replace', $cc2_unique );
	$cc2_reversed = array_reverse($S_replace);
 	$bps_customcode_two_wpa_implode = implode( "\n\n", $cc2_reversed );

	$bps_customcode_deny_files_wpa 	= ! isset($CC_Options_wpadmin['bps_customcode_deny_files_wpa']) ? '' : $CC_Options_wpadmin['bps_customcode_deny_files_wpa'];
	$bps_customcode_one_wpa 		= ! isset($CC_Options_wpadmin['bps_customcode_one_wpa']) ? '' : $CC_Options_wpadmin['bps_customcode_one_wpa'];
	$bps_customcode_bpsqse_wpa 		= ! isset($CC_Options_wpadmin['bps_customcode_bpsqse_wpa']) ? '' : $CC_Options_wpadmin['bps_customcode_bpsqse_wpa'];
	
	$wpadmin_CC_Options = array(
	'bps_customcode_deny_files_wpa' => $bps_customcode_deny_files_wpa, 
	'bps_customcode_one_wpa' 		=> $bps_customcode_one_wpa, 
	'bps_customcode_two_wpa' 		=> $bps_customcode_two_wpa_implode, 
	'bps_customcode_bpsqse_wpa' 	=> $bps_customcode_bpsqse_wpa 
	);
			
	foreach( $wpadmin_CC_Options as $key => $value ) {
		update_option('bulletproof_security_options_customcode_WPA', $wpadmin_CC_Options);
	}

	$success_array = array($woo_pfeed_pro_fix, $visual_composer_fix, $bookly_booking_fix, $emg_pro_fix, $nextgen_gallery_fix, $OptimizePress_theme_fix, $wp_checkout_fix, $video_showcase_fix, $wp_invoice_fix, $yoast_seo_fix, $formidable_pro_fix, $google_typography_fix, $flare_fix, $bbPress_fix, $spider_calendar_fix, $buddypress_fix, $wpml_transman_fix, $events_manager_fix, $mailpoet_fix, $event_espresso_fix, $content_egg_fix, $flatsome_theme_fix, $beaver_builder_fix, $wp_reset_fix, $bloom_fix );
	
	foreach ( $success_array as $successMessage ) {
		
		if ( $successMessage != '' ) {
			echo '<font color="green"><strong>'.$successMessage.'</strong></font><br>';
		}
	}
}

## 4. CUSTOM CODE BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS
function bpsPro_Pwizard_Autofix_BPSQSE_wpadmin() {
	
	$AutoFix_Options = get_option('bulletproof_security_options_wizard_autofix');
	
	if ( $AutoFix_Options['bps_wizard_autofix'] == 'Off' ) {
		return;
	}

	$CC_Options_wpadmin = get_option('bulletproof_security_options_customcode_WPA'); 
	$bps_customcode_bpsqse = htmlspecialchars_decode( $CC_Options_wpadmin['bps_customcode_bpsqse_wpa'], ENT_QUOTES );
	$bps_customcode_bpsqse_array = array();
	$bps_customcode_bpsqse_array[] = $bps_customcode_bpsqse;
	$pattern1 = '/BPSQSE-check\sBPS\sQUERY\sSTRING\sEXPLOITS\sAND\sFILTERS/';
	
	$bps_customcode_bpsqse_code_array = array();
	
	## The escaping is necessary in this String for processing
	$bps_customcode_bpsqse_code_array[] = "# BEGIN BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS
# WORDPRESS WILL BREAK IF ALL THE BPSQSE FILTERS ARE DELETED
# Use BPS wp-admin Custom Code to modify/edit/change this code and to save it permanently.
RewriteCond %{HTTP_USER_AGENT} (%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
RewriteCond %{HTTP_USER_AGENT} (;|<|>|'|".'"'."|\)|\(|%0A|%0D|%22|%27|%28|%3C|%3E|%00).*(libwww-perl|wget|python|nikto|curl|scan|java|winhttp|HTTrack|clshttp|archiver|loader|email|harvest|extract|grab|miner) [NC,OR]
RewriteCond %{THE_REQUEST} (\?|\*|%2a)+(%20+|\\\\s+|%20+\\\\s+|\\\\s+%20+|\\\\s+%20+\\\\s+)(http|https)(:/|/) [NC,OR]
RewriteCond %{THE_REQUEST} etc/passwd [NC,OR]
RewriteCond %{THE_REQUEST} cgi-bin [NC,OR]
RewriteCond %{THE_REQUEST} (%0A|%0D) [NC,OR]
RewriteCond %{REQUEST_URI} owssvr\.dll [NC,OR]
RewriteCond %{HTTP_REFERER} (%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
RewriteCond %{HTTP_REFERER} \.opendirviewer\. [NC,OR]
RewriteCond %{HTTP_REFERER} users\.skynet\.be.* [NC,OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(http|https):// [NC,OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(\.\.//?)+ [NC,OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=/([a-z0-9_.]//?)+ [NC,OR]
RewriteCond %{QUERY_STRING} \=PHP[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12} [NC,OR]
RewriteCond %{QUERY_STRING} (\.\./|%2e%2e%2f|%2e%2e/|\.\.%2f|%2e\.%2f|%2e\./|\.%2e%2f|\.%2e/) [NC,OR]
RewriteCond %{QUERY_STRING} ftp\: [NC,OR]
RewriteCond %{QUERY_STRING} (http|https)\: [NC,OR] 
RewriteCond %{QUERY_STRING} \=\|w\| [NC,OR]
RewriteCond %{QUERY_STRING} ^(.*)/self/(.*)$ [NC,OR]
RewriteCond %{QUERY_STRING} ^(.*)cPath=(http|https)://(.*)$ [NC,OR]
RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (<|%3C)([^s]*s)+cript.*(>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (\<|%3C).*iframe.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} (<|%3C)([^i]*i)+frame.*(>|%3E) [NC,OR] 
RewriteCond %{QUERY_STRING} base64_encode.*\(.*\) [NC,OR]
RewriteCond %{QUERY_STRING} base64_(en|de)code[^(]*\([^)]*\) [NC,OR]
RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} ^.*(\(|\)|<|>).* [NC,OR]
RewriteCond %{QUERY_STRING} (NULL|OUTFILE|LOAD_FILE) [OR]
RewriteCond %{QUERY_STRING} (\.{1,}/)+(motd|etc|bin) [NC,OR]
RewriteCond %{QUERY_STRING} (localhost|loopback|127\.0\.0\.1) [NC,OR]
RewriteCond %{QUERY_STRING} (<|>|'|%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
RewriteCond %{QUERY_STRING} concat[^\(]*\( [NC,OR]
RewriteCond %{QUERY_STRING} union([^s]*s)+elect [NC,OR]
RewriteCond %{QUERY_STRING} union([^a]*a)+ll([^s]*s)+elect [NC,OR]
RewriteCond %{QUERY_STRING} (;|<|>|'|".'"'."|\)|%0A|%0D|%22|%27|%3C|%3E|%00).*(/\*|union|select|insert|drop|delete|update|cast|create|char|convert|alter|declare|order|script|set|md5|benchmark|encode) [NC,OR]
RewriteCond %{QUERY_STRING} (sp_executesql) [NC]
RewriteRule ^(.*)$ - [F]
# END BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS\n";

	## Content Egg Free and Pro Plugin: whitelist rules
	$content_egg = 'content-egg/content-egg.php';
	$content_egg_active = in_array( $content_egg, apply_filters('active_plugins', get_option('active_plugins')));
	$content_egg_fix = '';

	if ( $content_egg_active == 1 || is_plugin_active_for_network( $content_egg ) ) {
		$content_egg_fix = __('Content Egg Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p1 = array('/RewriteCond\s%\{QUERY_STRING\}\s\(;\|\<\|\>\|\'\|(.*)order\|script\|set\|md5\|benchmark\|encode\)\s\[NC,OR\]/');
		$r1 = array("# BPS AutoWhitelist QS1: Content Egg Plugin");

	} else {
		$p1 = array();
		$r1 = array();
	}

	## Event Espresso Plugin: whitelist rules Note: covers all versions of Espresso and the premium versions
	$event_espresso1 = WP_PLUGIN_DIR . '/event-espresso-decaf/espresso.php';
	$event_espresso2 = WP_PLUGIN_DIR . '/event-espresso-free/espresso.php';
	$event_espresso3 = WP_PLUGIN_DIR . '/event-espresso/espresso.php';
	$event_espresso4 = WP_PLUGIN_DIR . '/event-espresso-core-master/espresso.php';
	$event_espresso_fix = '';

	if ( file_exists($event_espresso1) || file_exists($event_espresso2) || file_exists($event_espresso3) || file_exists($event_espresso4) ) {
		$event_espresso_fix = __('Event Espresso Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
			
		$p2 = array('/RewriteCond\s%\{HTTP_REFERER\}\s\(%0A\|%0D\|%27\|%3C\|%3E\|%00\)\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(<\|>\|\'\|%0A\|%0D\|%27\|%3C\|%3E\|%00\)\s\[NC,OR\]/');
		$r2 = array("# BPS AutoWhitelist QS2: Event Espresso Plugin", "# BPS AutoWhitelist QS3: Event Espresso Plugin");
	} else {
		$p2 = array();
		$r2 = array();
	}

	## Open Web Analytics (github) Plugin: whitelist rules
	$owa_plugin = 'owa/wp_plugin.php';
	$owa_plugin_active = in_array( $owa_plugin, apply_filters('active_plugins', get_option('active_plugins')));
	$owa_plugin_fix = '';

	if ( $owa_plugin_active == 1 || is_plugin_active_for_network( $owa_plugin ) ) {
		$owa_plugin_fix = __('Open Web Analytics (github) Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p3 = array('/RewriteCond\s%\{HTTP_REFERER\}\s\(%0A\|%0D\|%27\|%3C\|%3E\|%00\)\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\^\.\*\(.*\|\<\|\>\)\.\*\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(<\|>\|\'\|%0A\|%0D\|%27\|%3C\|%3E\|%00\)\s\[NC,OR\]/');
		$r3 = array("# BPS AutoWhitelist QS2: Open Web Analytics (github) Plugin", "# BPS AutoWhitelist QS4: Open Web Analytics (github) Plugin", "# BPS AutoWhitelist QS3: Open Web Analytics (github) Plugin");

	} else {
		$p3 = array();
		$r3 = array();
	}

	## UberGrid (code canyon) Plugin: whitelist rules
	$uberGrid = 'uber-grid/uber-grid.php';
	$uberGrid_active = in_array( $uberGrid, apply_filters('active_plugins', get_option('active_plugins')));
	$uberGrid_fix = '';

	if ( $uberGrid_active == 1 || is_plugin_active_for_network( $uberGrid ) ) {
		$uberGrid_fix = __('UberGrid (code canyon) Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p4 = array('/RewriteCond\s%\{HTTP_REFERER\}\s\(%0A\|%0D\|%27\|%3C\|%3E\|%00\)\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\^\.\*\(.*\|\<\|\>\)\.\*\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(<\|>\|\'\|%0A\|%0D\|%27\|%3C\|%3E\|%00\)\s\[NC,OR\]/');
		$r4 = array("# BPS AutoWhitelist QS2: UberGrid (code canyon) Plugin", "# BPS AutoWhitelist QS4: UberGrid (code canyon) Plugin", "# BPS AutoWhitelist QS3: UberGrid (code canyon) Plugin");

	} else {
		$p4 = array();
		$r4 = array();
	}

	## Jetpack Plugin SSO feature: whitelist rules
	$jetpack = 'jetpack/jetpack.php';
	$jetpack_active = in_array( $jetpack, apply_filters('active_plugins', get_option('active_plugins')));
	$jetpack_fix = '';
	
	if ( $jetpack_active == 1 || is_plugin_active_for_network( $jetpack ) ) {
		$jetpack_fix = __('Jetpack Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p5 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r5 = array("# BPS AutoWhitelist QS5: Jetpack Plugin", "# BPS AutoWhitelist QS6: Jetpack Plugin", "# BPS AutoWhitelist QS7: Jetpack Plugin");

	} else {
		$p5 = array();
		$r5 = array();
	}

	## Restrict Content Pro Plugin: whitelist rules
	$restrict_content_pro = 'restrict-content-pro/restrict-content-pro.php';
	$restrict_content_pro_active = in_array( $restrict_content_pro, apply_filters('active_plugins', get_option('active_plugins')));
	$restrict_content_pro_fix = '';

	if ( $restrict_content_pro_active == 1 || is_plugin_active_for_network( $restrict_content_pro ) ) {
		$restrict_content_pro_fix = __('Restrict Content Pro Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p6 = array('/RewriteCond\s%\{QUERY_STRING\}\s\(<\|>\|\'\|%0A\|%0D\|%27\|%3C\|%3E\|%00\)\s\[NC,OR\]/');
		$r6 = array("# BPS AutoWhitelist QS3: Restrict Content Pro Plugin");

	} else {
		$p6 = array();
		$r6 = array();
	}

	## Link Whisper free Plugin: whitelist rules
	$link_whisper = 'link-whisper/link-whisper.php';
	$link_whisper_active = in_array( $link_whisper, apply_filters('active_plugins', get_option('active_plugins')));
	$link_whisper_fix = '';

	if ( $link_whisper_active == 1 || is_plugin_active_for_network( $link_whisper ) ) {
		$link_whisper_fix = __('Link Whisper Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p7 = array('/RewriteCond\s%\{QUERY_STRING\}\s\(<\|>\|\'\|%0A\|%0D\|%27\|%3C\|%3E\|%00\)\s\[NC,OR\]/');
		$r7 = array("# BPS AutoWhitelist QS3: Link Whisper Plugin");

	} else {
		$p7 = array();
		$r7 = array();
	}

	## Link Whisper premium Plugin: whitelist rules
	$link_whisper_premium = 'link-whisper-premium/link-whisper.php';
	$link_whisper_premium_active = in_array( $link_whisper_premium, apply_filters('active_plugins', get_option('active_plugins')));
	$link_whisper_premium_fix = '';

	if ( $link_whisper_premium_active == 1 || is_plugin_active_for_network( $link_whisper_premium ) ) {
		$link_whisper_premium_fix = __('Link Whisper Premium Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p8 = array('/RewriteCond\s%\{QUERY_STRING\}\s\(<\|>\|\'\|%0A\|%0D\|%27\|%3C\|%3E\|%00\)\s\[NC,OR\]/');
		$r8 = array("# BPS AutoWhitelist QS3: Link Whisper Premium Plugin");

	} else {
		$p8 = array();
		$r8 = array();
	}

	## Convert Pro Plugin: whitelist rules
	$convert_pro = 'convertpro/convertpro.php';
	$convert_pro_active = in_array( $convert_pro, apply_filters('active_plugins', get_option('active_plugins')));
	$convert_pro_fix = '';

	if ( $convert_pro_active == 1 || is_plugin_active_for_network( $convert_pro ) ) {
		$convert_pro_fix = __('Convert Pro Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p9 = array('/RewriteCond\s%\{QUERY_STRING\}\s\^\.\*\(.*\|\<\|\>\)\.\*\s\[NC,OR\]/');
		$r9 = array("# BPS AutoWhitelist QS4: Convert Pro Plugin");

	} else {
		$p9 = array();
		$r9 = array();
	}

	## WP Mail SMTP Plugin: whitelist rules
	$wp_mail_smtp = 'wp-mail-smtp/wp_mail_smtp.php';
	$wp_mail_smtp_active = in_array( $wp_mail_smtp, apply_filters('active_plugins', get_option('active_plugins')));
	$wp_mail_smtp_fix = '';
	
	if ( $wp_mail_smtp_active == 1 || is_plugin_active_for_network( $wp_mail_smtp ) ) {
		$wp_mail_smtp_fix = __('WP Mail SMTP Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p10 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r10 = array("# BPS AutoWhitelist QS5: WP Mail SMTP Plugin", "# BPS AutoWhitelist QS6: WP Mail SMTP Plugin", "# BPS AutoWhitelist QS7: WP Mail SMTP Plugin");

	} else {
		$p10 = array();
		$r10 = array();
	}

	## Gmail SMTP Plugin: whitelist rules
	$gmail_smtp = 'gmail-smtp/main.php';
	$gmail_smtp_active = in_array( $gmail_smtp, apply_filters('active_plugins', get_option('active_plugins')));
	$gmail_smtp_fix = '';
	
	if ( $gmail_smtp_active == 1 || is_plugin_active_for_network( $gmail_smtp ) ) {
		$gmail_smtp_fix = __('Gmail SMTP Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p11 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r11 = array("# BPS AutoWhitelist QS5: Gmail SMTP Plugin", "# BPS AutoWhitelist QS6: Gmail SMTP Plugin", "# BPS AutoWhitelist QS7: Gmail SMTP Plugin");

	} else {
		$p11 = array();
		$r11 = array();
	}

	## Bit Integrations Plugin: whitelist rules
	$bit_integrations = 'bit-integrations/bitwpfi.php';
	$bit_integrations_active = in_array( $bit_integrations, apply_filters('active_plugins', get_option('active_plugins')));
	$bit_integrations_fix = '';
	
	if ( $bit_integrations_active == 1 || is_plugin_active_for_network( $bit_integrations ) ) {
		$bit_integrations_fix = __('Bit Integrations Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p12 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r12 = array("# BPS AutoWhitelist QS5: Bit Integrations Plugin", "# BPS AutoWhitelist QS6: Bit Integrations Plugin", "# BPS AutoWhitelist QS7: Bit Integrations Plugin");

	} else {
		$p12 = array();
		$r12 = array();
	}

	## Piotnetforms Plugin: whitelist rules
	$piotnetforms = 'piotnetforms/piotnetforms.php';
	$piotnetforms_active = in_array( $piotnetforms, apply_filters('active_plugins', get_option('active_plugins')));
	$piotnetforms_fix = '';
	
	if ( $piotnetforms_active == 1 || is_plugin_active_for_network( $piotnetforms ) ) {
		$piotnetforms_fix = __('Piotnetforms Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p13 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r13 = array("# BPS AutoWhitelist QS5: Piotnetforms Plugin", "# BPS AutoWhitelist QS6: Piotnetforms Plugin", "# BPS AutoWhitelist QS7: Piotnetforms Plugin");

	} else {
		$p13 = array();
		$r13 = array();
	}

	## Post SMTP Mailer Plugin: whitelist rules
	$post_smtp_mailer = 'post-smtp/postman-smtp.php';
	$post_smtp_mailer_active = in_array( $post_smtp_mailer, apply_filters('active_plugins', get_option('active_plugins')));
	$post_smtp_mailer_fix = '';
	
	if ( $post_smtp_mailer_active == 1 || is_plugin_active_for_network( $post_smtp_mailer ) ) {
		$post_smtp_mailer_fix = __('Post SMTP Mailer Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p14 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r14 = array("# BPS AutoWhitelist QS5: Post SMTP Mailer Plugin", "# BPS AutoWhitelist QS6: Post SMTP Mailer Plugin", "# BPS AutoWhitelist QS7: Post SMTP Mailer Plugin");

	} else {
		$p14 = array();
		$r14 = array();
	}

	## Product Feed Manager for WooCommerce Plugin: whitelist rules
	$product_feed_manager = 'best-woocommerce-feed/rex-product-feed.php';
	$product_feed_manager_active = in_array( $product_feed_manager, apply_filters('active_plugins', get_option('active_plugins')));
	$product_feed_manager_fix = '';
	
	if ( $product_feed_manager_active == 1 || is_plugin_active_for_network( $product_feed_manager ) ) {
		$product_feed_manager_fix = __('Product Feed Manager for WooCommerce Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p15 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r15 = array("# BPS AutoWhitelist QS5: Product Feed Manager for WooCommerce Plugin", "# BPS AutoWhitelist QS6: Product Feed Manager for WooCommerce Plugin", "# BPS AutoWhitelist QS7: Product Feed Manager for WooCommerce Plugin");

	} else {
		$p15 = array();
		$r15 = array();
	}

	## Product Feed Manager for WooCommerce Pro Plugin: whitelist rules
	$product_feed_manager_pro = 'best-woocommerce-feed-pro/rex-product-feed-pro.php';
	$product_feed_manager_pro_active = in_array( $product_feed_manager_pro, apply_filters('active_plugins', get_option('active_plugins')));
	$product_feed_manager_pro_fix = '';
	
	if ( $product_feed_manager_pro_active == 1 || is_plugin_active_for_network( $product_feed_manager_pro ) ) {
		$product_feed_manager_pro_fix = __('Product Feed Manager for WooCommerce Plugin wp-admin BPSQSE AutoWhitelist successful', 'bulletproof-security');
		
		$p16 = array('/RewriteCond\s%\{QUERY_STRING}\s\[a-zA-Z0-9_\]=\(http\|https\):\/\/\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\[a-zA-Z0-9_\]=\/\(\[a-z0-9_\.\]\/\/\?\)\+\s\[NC,OR\]/', '/RewriteCond\s%\{QUERY_STRING\}\s\(http\|https\)\\\:\s\[NC,OR\]/');
		$r16 = array("# BPS AutoWhitelist QS5: Product Feed Manager for WooCommerce Pro Plugin", "# BPS AutoWhitelist QS6: Product Feed Manager for WooCommerce Pro Plugin", "# BPS AutoWhitelist QS7: Product Feed Manager for WooCommerce Pro Plugin");

	} else {
		$p16 = array();
		$r16 = array();
	}

	$pattern_array = array_merge($p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9, $p10, $p11, $p12, $p13, $p14, $p15, $p16);
	$replace_array = array_merge($r1, $r2, $r3, $r4, $r5, $r6, $r7, $r8, $r9, $r10, $r11, $r12, $r13, $r14, $r15, $r16);

	if ( $CC_Options_wpadmin['bps_customcode_bpsqse_wpa'] != '' ) {		
		$bps_customcode_bpsqse_replace = preg_replace($pattern_array, $replace_array, $bps_customcode_bpsqse_array);
	} else {
		$bps_customcode_bpsqse_replace = preg_replace($pattern_array, $replace_array, $bps_customcode_bpsqse_code_array);			
	}
		
	$bps_customcode_bpsqse_implode = implode( "\n", $bps_customcode_bpsqse_replace );

	$wpadmin_CC_Options = array(
	'bps_customcode_deny_files_wpa' => $CC_Options_wpadmin['bps_customcode_deny_files_wpa'], 
	'bps_customcode_one_wpa' 		=> $CC_Options_wpadmin['bps_customcode_one_wpa'], 
	'bps_customcode_two_wpa' 		=> $CC_Options_wpadmin['bps_customcode_two_wpa'], 
	'bps_customcode_bpsqse_wpa' 	=> $bps_customcode_bpsqse_implode 
	);
			
	foreach( $wpadmin_CC_Options as $key => $value ) {
		update_option('bulletproof_security_options_customcode_WPA', $wpadmin_CC_Options);
	}

	$success_array = array($content_egg_fix, $event_espresso_fix, $owa_plugin_fix, $uberGrid_fix, $jetpack_fix, $restrict_content_pro_fix, $link_whisper_fix, $link_whisper_premium_fix, $convert_pro_fix, $wp_mail_smtp_fix, $gmail_smtp_fix, $bit_integrations_fix, $piotnetforms_fix, $post_smtp_mailer_fix, $product_feed_manager_fix, $product_feed_manager_pro_fix);
	
	foreach ( $success_array as $successMessage ) {
		
		if ( $successMessage != '' ) {
			echo '<font color="green"><strong>'.$successMessage.'</strong></font><br>';
		}
	}
}

?>