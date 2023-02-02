<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! current_user_can('manage_options') ) { 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}


## AutoFix|AutoWhitelist|AutoSetup|AutoCleanup: Automatically creates fixes/setups or whitelist rules for any known issues with other plugins.
## List of fixes by plugin and CC text box: https://forum.ait-pro.com/forums/topic/setup-wizard-autofix/.
/*
Root Custom Code Text Box:
1. CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE:
*/

// WPSC setup & cleanup: Creates the WPSC htaccess code in BPS Custom Code & the WPSC code in the wp-config.php file.
// Note: htaccess code is created in the site root htaccess file for GWIOD site types.
// 6.0: No longer doing AutoSetup for WPSC. WPSC no longer writes its htaccess code to the root htaccess file unless
// the generic WP Rewrite htaccess code exists. 
function bpsPro_Pwizard_Autofix_WPSC() {
	
	$AutoFix_Options = get_option('bulletproof_security_options_wizard_autofix');
	
	if ( isset($AutoFix_Options['bps_wizard_autofix']) && $AutoFix_Options['bps_wizard_autofix'] == 'Off' ) {
		return;
	}

	$wpsc_plugin = 'wp-super-cache/wp-cache.php';
	$wpsc_plugin_active = in_array( $wpsc_plugin, apply_filters('active_plugins', get_option('active_plugins')));

	// 1. CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_cache = htmlspecialchars_decode( $CC_Options_root['bps_customcode_one'], ENT_QUOTES );
	$bps_customcode_cache_array = array();
	$bps_customcode_cache_array[] = $bps_customcode_cache;
	$cc_cache_array = array();	
	
	if ( $wpsc_plugin_active == 1 || is_plugin_active_for_network( $wpsc_plugin ) ) {

		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}

		## GWIOD site type: AutoSetup is not required since htaccess code is written to the site root htaccess file.
		if ( $bpsSiteUrl != $bpsHomeUrl ) {
			$text = '<strong><font color="green">'.__('WP Super Cache (WPSC) Plugin AutoSetup not required: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-20"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('GWIOD site types do not require AutoSetup because WPSC creates htaccess code in the site root htaccess file.', 'bulletproof-security').'</span></span></font></strong><br>';
			echo $text;	
			
		return;
		}

		## Remove any existing WPSC htaccess code in Custom Code from the $cc_cache_array so that new wpsc htaccess code is created each time.
		## Remove any existing WPSC placeholder text if it exists. Note: If duplicate wpsc placeholders exists then a problem may occur.
		## Important Note: If dots are used (.*) then newlines and spaces are ignored when using the /s modifier.
		// preg_match_all() would need to be used to preg_replace duplicate wpsc placeholder text. Wait and see - Do not do that for now.
		// Cleans up extra Newlines, Returns & whitespaces.
		foreach ( $bps_customcode_cache_array as $key => $value ) {
			
			if ( preg_match( '/#\sBEGIN\sWPSuperCache(.*)#\sEND\sWPSuperCache/s', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN\sWPSuperCache(.*)#\sEND\sWPSuperCache/s', "", $value);
			}

			if ( preg_match( '/#\sBEGIN\sWPSuperCache\n\n#\sEND\sWPSuperCache/', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN\sWPSuperCache\n\n#\sEND\sWPSuperCache/', "", $value);
			}
			
			if ( preg_match('/(\n\r){2,}/', $value, $matches) ) {	
				$value = preg_replace("/(\n\r){2,}/", "\n", $value);
			}				
			
			$cc_cache_array[] = trim( $value, " \t\n\r");
		}	
	
		/* //not going to do anything with WPSC wp-config.php code
		$wpconfig = ABSPATH . 'wp-config.php';		
		
		if ( ! file_exists( $wpconfig ) ) {
				
			$text = '<strong><font color="#fb0101">'.__('Error: The Pre-Installation Wizard is unable to add the WP Super Cache WP_CACHE code in your wp-config.php file.', 'bulletproof-security').'</font><br>'.__('A wp-config.php file was NOT found in your WordPress website root folder. If you have moved your wp-config.php file to another folder location then you will need to either move the wp-config.php file back to its default WordPress folder location and run the Pre-Installation Wizard again or manually edit your wp-config.php file and add the WP Super Cache WP_CACHE code. Click this link for the steps to manually edit your wp-config.php file: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/manually-editing-the-wordpress-wp-config-php-file/" target="_blank" title="Link opens in a new Browser window">'.__('Manually Edit the WordPress wp-config.php file', 'bulletproof-security').'</a><br>'; 			
			echo $text;
		}
		*/

		$rootHtaccess = ABSPATH . '.htaccess';
		
		if ( file_exists($rootHtaccess) ) {
			
			$sapi_type = php_sapi_name();
			$permsRootHtaccess = substr(sprintf('%o', fileperms($rootHtaccess)), -4);

			/*
			if ( file_exists( $wpconfig ) ) {
			
				$perms_wpconfig = substr(sprintf('%o', fileperms($wpconfig)), -4);
				
				if ( substr($sapi_type, 0, 6) != 'apache' || $perms_wpconfig != '0666' || $perms_wpconfig != '0777' ) { // Windows IIS, XAMPP, etc
					chmod( $wpconfig, 0644 );
				}
			}
			*/

			if ( substr($sapi_type, 0, 6) != 'apache' || $permsRootHtaccess != '0666' || $permsRootHtaccess != '0777' ) {
				chmod( $rootHtaccess, 0644 );
			}

			$root_htaccess_file_contents = file_get_contents($rootHtaccess);			

			$wpsc_htaccess_code = array();

			## Remove the WPSC htaccess code from the Root htaccess file after putting any WPSC code into an array and updating the CC DB options.
			if ( preg_match( '/#\sBEGIN\sWPSuperCache(.*)#\sEND\sWPSuperCache/s', $root_htaccess_file_contents, $matches ) ) {
				$wpsc_htaccess_code[] = $matches[0];
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sWPSuperCache(.*)#\sEND\sWPSuperCache/s', "", $root_htaccess_file_contents);
			}

			$bps_customcode_cache_merge = array_merge($cc_cache_array, $wpsc_htaccess_code);
			$cc_cache_unique = array_unique($bps_customcode_cache_merge);
 			// needs to be \n
 			$bps_customcode_cache_implode = implode( "\n", $cc_cache_unique );
			
			if ( ! is_multisite() ) {

				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);
				
			} else {
					
				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);					
			}

			foreach( $Root_CC_Options as $key => $value ) {
				update_option('bulletproof_security_options_customcode', $Root_CC_Options);
			}		

			## Not going to do anything with WPSC wp-config.php code.
			/*
			define( 'WPCACHEHOME', 'C:\xampp\htdocs16\demo9\wp-content\plugins\wp-super-cache/' );
			define('WP_CACHE', true);
			*/
			/*
			if ( file_exists( $wpconfig ) ) {
				$wp_config_contents = file_get_contents($wpconfig);
			
				if ( ! preg_match( '/define(.*)\((.*)WP_CACHE(.*)(true|false)(.*)\);/', $wp_config_contents, $matches ) ) {
					$wp_config_contents = preg_replace( '/<\?php(.*\s*){1}/', '<?php'."\ndefine('WP_CACHE', true);\n", $wp_config_contents);
					file_put_contents($wpconfig, $wp_config_contents);
				}
			}
			*/		
			
			## Remove LiteSpeed Cache htaccess code from the Root htaccess file
			if ( file_put_contents($rootHtaccess, $root_htaccess_file_contents) ) {	

				$Root_Autolock = get_option('bulletproof_security_options_autolock');
				
				if ( $Root_Autolock['bps_root_htaccess_autolock'] == 'On' ) {
					chmod($rootHtaccess, 0404);
				}
			}

			$text = '<strong><font color="green">'.__('WP Super Cache (WPSC) Plugin AutoSetup Successful: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-20"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('Important Note: If you change any of your WP Super Cache settings at any time, re-run the Setup Wizards again.', 'bulletproof-security').'</span></span></font></strong><br>';
			echo $text;	
		}

	} else {
	
		## WPSC Cleanup: Either not installed or activated. Removes any/all WPSC htaccess code from BPS Custom Code and Root htaccess file.
		if ( $wpsc_plugin_active != 1 && ! is_plugin_active_for_network( $wpsc_plugin ) ) { 

			## Remove any existing WPSC htaccess code in Custom Code from the $cc_cache_array.
			foreach ( $bps_customcode_cache_array as $key => $value ) {
				
				if ( preg_match( '/#\sBEGIN\sWPSuperCache(.*)#\sEND\sWPSuperCache/s', $value, $matches ) ) {
					$value = preg_replace( '/#\sBEGIN\sWPSuperCache(.*)#\sEND\sWPSuperCache/s', "", $value);
				}

				if ( preg_match( '/#\sBEGIN\sWPSuperCache\n\n#\sEND\sWPSuperCache/', $value, $matches ) ) {
					$value = preg_replace( '/#\sBEGIN\sWPSuperCache\n\n#\sEND\sWPSuperCache/', "", $value);
				}
				
				if ( preg_match('/(\n\r){2,}/', $value, $matches) ) {	
					$value = preg_replace("/(\n\r){2,}/", "\n", $value);
				}				
			
				$cc_cache_array[] = trim( $value, " \t\n\r");
			}
			
			$bps_customcode_cache_implode = implode( "\n\n", $cc_cache_array );

			if ( ! is_multisite() ) {

				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);
				
			} else {
					
				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);					
			}

			foreach ( $Root_CC_Options as $key => $value ) {
				update_option('bulletproof_security_options_customcode', $Root_CC_Options);
			}

			## Remove any existing LiteSpeed Cache htaccess code in the Root htaccess file.
			$rootHtaccess = ABSPATH . '.htaccess';			
			
			if ( file_exists($rootHtaccess) ) {
				$sapi_type = php_sapi_name();
				$permsRootHtaccess = substr(sprintf('%o', fileperms($rootHtaccess)), -4);
			
				if ( substr($sapi_type, 0, 6) != 'apache' || $permsRootHtaccess != '0666' || $permsRootHtaccess != '0777' ) {
					chmod( $rootHtaccess, 0644 );
				}			
			
				$root_htaccess_file_contents = file_get_contents($rootHtaccess);			
			
				if ( preg_match( '/#\sBEGIN\sWPSuperCache(.*)#\sEND\sWPSuperCache/s', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sWPSuperCache(.*)#\sEND\sWPSuperCache/s', "", $root_htaccess_file_contents);
				}

				if ( preg_match( '/#\sBEGIN\sWPSuperCache\n\n#\sEND\sWPSuperCache/', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sWPSuperCache\n\n#\sEND\sWPSuperCache/', "", $root_htaccess_file_contents);
				}			
			
				file_put_contents($rootHtaccess, $root_htaccess_file_contents);			
			
				$Root_Autolock = get_option('bulletproof_security_options_autolock');
				
				if ( isset($Root_Autolock['bps_root_htaccess_autolock']) && $Root_Autolock['bps_root_htaccess_autolock'] == 'On' ) {
					chmod($rootHtaccess, 0404);
				}
				
				$text = '<strong><font color="green">'.__('WP Super Cache (WPSC) Plugin AutoCleanup Successful: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-60"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('AutoCleanup has removed all WPSC htaccess code from BPS Custom Code and your Root htaccess file if it existed. If you have WPSC installed and are still planning on using WPSC then re-run the Setup Wizards after you have activated the WPSC plugin again and resaved your WPSC plugin settings again.', 'bulletproof-security').'</span></span></font></strong><br>';
				echo $text;	
			}
		}
	}	
}

// W3TC Setup & Cleanup: Creates the W3TC htaccess code in BPS Custom Code & the W3TC code in the wp-config.php file.
// IMPORTANT: It is not possible to access W3TC classes to get W3TC htaccess code. W3TC uses private functions in classes. 
// Members declared as private may only be accessed by the class that defines the member. You cannot redeclare private properties.
// Get the W3TC htaccess code from the Root htaccess file and save it to BPS Custom Code.
// The Setup Wizard will either lock or not lock the wp-config.php file in later processing.
## GWIOD site types do not need to do any of this since W3TC creates the htaccess code in the site root htaccess file and not the BPS Root htaccess file.
## IMPORTANT: This function: bpsPro_w3tc_dashboard_iframe_preload() in wizard.php preloads the w3tc_dashboard page in an iFrame on Setup Wizard page access.
// The iFrame cannot be loaded from this function because things do not happen in time for processing data.
// The Root htaccess file and wp-config.php file are unlocked in this function: bpsPro_w3tc_dashboard_iframe_preload()
// Note: htaccess code is created in the site root htaccess file for GWIOD site types.
function bpsPro_Pwizard_Autofix_W3TC() {
	
	$AutoFix_Options = get_option('bulletproof_security_options_wizard_autofix');
	
	if ( $AutoFix_Options['bps_wizard_autofix'] == 'Off' ) {
		return;
	}

	$w3tc_plugin = 'w3-total-cache/w3-total-cache.php';
	$w3tc_plugin_active = in_array( $w3tc_plugin, apply_filters('active_plugins', get_option('active_plugins')));

	// CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_cache = htmlspecialchars_decode( $CC_Options_root['bps_customcode_one'], ENT_QUOTES );
	$bps_customcode_cache_array = array();
	$bps_customcode_cache_array[] = $bps_customcode_cache;
	$cc_cache_array = array();

	if ( $w3tc_plugin_active == 1 || is_plugin_active_for_network( $w3tc_plugin ) ) {

		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}

		## GWIOD site type: AutoSetup is not required since htaccess code is written to the site root htaccess file.
		if ( $bpsSiteUrl != $bpsHomeUrl ) {
			$text = '<strong><font color="green">'.__('W3 Total Cache (W3TC) Plugin AutoSetup not required: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-20"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('GWIOD site types do not require AutoSetup because W3TC creates htaccess code in the site root htaccess file.', 'bulletproof-security').'</span></span></font></strong><br>';
			echo $text;	
			return;
		}

		## Remove any existing W3TC htaccess code in Custom Code from the $cc_cache_array so that new W3TC htaccess code is created each time.
		## Remove any existing W3TC placeholder text if it exists. Note: If duplicate W3TC placeholders exists then a problem may occur.
		## Important Note: If dots are used (.*) then newlines and spaces are ignored when using the /s modifier.
		// preg_match_all() would need to be used to preg_replace duplicate W3TC placeholder text. Wait and see - Do not do that for now.
		// Cleans up extra Newlines, Returns & whitespaces.
		// W3TC Markers: Browser Cache, Page Cache core, Page Cache cache, Skip 404 error handling by WordPress for static files, Minify core, Minify cache & CDN
		// These Markers appear to only be created in the /wp-content/cache htaccess files: Page Cache cache, Minify cache & Minify core, but I am leaving
		// the preg_replace conditions just in case any of these are also created in the root htaccess file. Will not hurt anything either way.
		// The W3TC Order of Markers in the root htaccess file appear to be: Browser Cache, Page Cache core & Skip 404 error...
		// CDN Marker appears to be created in the /wp-content/cache folder somewhere.
		foreach ( $bps_customcode_cache_array as $key => $value ) {
		
			if ( preg_match( '/#\sBEGIN\sW3TC\sBrowser\sCache(.*)#\sEND\sW3TC\sBrowser\sCache/s', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN\sW3TC\sBrowser\sCache(.*)#\sEND\sW3TC\sBrowser\sCache/s', "", $value);
			}
		
			if ( preg_match( '/#\sBEGIN\sW3TC\sPage\sCache\score(.*)#\sEND\sW3TC\sPage\sCache\score/s', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN\sW3TC\sPage\sCache\score(.*)#\sEND\sW3TC\sPage\sCache\score/s', "", $value);
			}

			if ( preg_match( '/#\sBEGIN\sW3TC\sPage\sCache\scache(.*)#\sEND\sW3TC\sPage\sCache\scache/s', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN\sW3TC\sPage\sCache\scache(.*)#\sEND\sW3TC\sPage\sCache\scache/s', "", $value);
			}

			if ( preg_match( '/#\sBEGIN\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles(.*)#\sEND\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles/s', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles(.*)#\sEND\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles/s', "", $value);
			}		
		
			if ( preg_match( '/#\sBEGIN\sW3TC\sMinify\score(.*)#\sEND\sW3TC\sMinify\score/s', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN\sW3TC\sMinify\score(.*)#\sEND\sW3TC\sMinify\score/s', "", $value);
			}

			if ( preg_match( '/#\sBEGIN\sW3TC\sMinify\scache(.*)#\sEND\sW3TC\sMinify\scache/s', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN\sW3TC\sMinify\scache(.*)#\sEND\sW3TC\sMinify\scache/s', "", $value);
			}

			if ( preg_match( '/#\sBEGIN\sW3TC\sCDN(.*)#\sEND\sW3TC\sCDN/s', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN\sW3TC\sCDN(.*)#\sEND\sW3TC\sCDN/s', "", $value);
			}

			if ( preg_match( '/#\sBEGIN\sW3TC\n\n#\sEND\sW3TC/', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN\sW3TC\n\n#\sEND\sW3TC/', "", $value);
			}
			
			if ( preg_match('/(\n\r){2,}/', $value, $matches) ) {	
				$value = preg_replace("/(\n\r){2,}/", "\n", $value);
			}
			
			$cc_cache_array[] = trim( $value, " \t\n\r");
		}
		
		$wpconfig = ABSPATH . 'wp-config.php';		
		
		if ( ! file_exists( $wpconfig ) ) {
				
			$text = '<strong><font color="#fb0101">'.__('Error: The Pre-Installation Wizard is unable to add the W3 Total Cache WP_CACHE code in your wp-config.php file.', 'bulletproof-security').'</font><br>'.__('A wp-config.php file was NOT found in your WordPress website root folder. If you have moved your wp-config.php file to another folder location then you will need to either move the wp-config.php file back to its default WordPress folder location and run the Pre-Installation Wizard again or manually edit your wp-config.php file and add the W3 Total Cache WP_CACHE code. Click this link for the steps to manually edit your wp-config.php file: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/manually-editing-the-wordpress-wp-config-php-file/" target="_blank" title="Link opens in a new Browser window">'.__('Manually Edit the WordPress wp-config.php file', 'bulletproof-security').'</a><br>';
			echo $text;
		}	
		
		## Get new W3TC htacces code from the Root htaccess file and save it in Custom Code.
		$rootHtaccess = ABSPATH . '.htaccess';
		
		if ( file_exists($rootHtaccess) ) {
		
			// Don't bother trying to check any W3TC options settings since w3tc does not store "on|off" settings in the WP DB.
			// W3TC option settings are in these files: /wp-content/w3tc-config/master.php and master-admin.php
			// Since any/new W3TC htaccess code will be created/recreated in the root htaccess file by W3TC when the iFrame loads in this 
			// function: bpsPro_w3tc_dashboard_iframe_preload() then get any/all W3TC htaccess code if it exists - already in CC or new code in the Root htaccess file.
			## The W3TC Order of Markers in the root htaccess file appear to be: Browser Cache, Page Cache core & Skip 404 error...
			// Get each block of W3TC code and put them in arrays and then merge the arrays.
			## Remove the W3TC htaccess code from the Root htaccess file after putting any W3TC code into arrays and updating the CC DB options.
			$root_htaccess_file_contents = file_get_contents($rootHtaccess);
	
			$browser_cache = array();
			$page_cache_core = array();
			$page_cache_cache = array();
			$skip_404_error = array();
			$minify_core = array();
			$minify_cache = array();		
			$CDN = array();		
		
			if ( preg_match( '/#\sBEGIN\sW3TC\sBrowser\sCache(.*)#\sEND\sW3TC\sBrowser\sCache/s', $root_htaccess_file_contents, $matches ) ) {
				$browser_cache[] = $matches[0];
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sW3TC\sBrowser\sCache(.*)#\sEND\sW3TC\sBrowser\sCache/s', "", $root_htaccess_file_contents);
			}
		
			if ( preg_match( '/#\sBEGIN\sW3TC\sPage\sCache\score(.*)#\sEND\sW3TC\sPage\sCache\score/s', $root_htaccess_file_contents, $matches ) ) {
				$page_cache_core[] = $matches[0];
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sW3TC\sPage\sCache\score(.*)#\sEND\sW3TC\sPage\sCache\score/s', "", $root_htaccess_file_contents);
			}

			if ( preg_match( '/#\sBEGIN\sW3TC\sPage\sCache\scache(.*)#\sEND\sW3TC\sPage\sCache\scache/s', $root_htaccess_file_contents, $matches ) ) {
				$page_cache_cache[] = $matches[0];
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sW3TC\sPage\sCache\scache(.*)#\sEND\sW3TC\sPage\sCache\scache/s', "", $root_htaccess_file_contents);
			}

			if ( preg_match( '/#\sBEGIN\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles(.*)#\sEND\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles/s', $root_htaccess_file_contents, $matches ) ) {
				$skip_404_error[] = $matches[0];
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles(.*)#\sEND\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles/s', "", $root_htaccess_file_contents);
			}		
		
			if ( preg_match( '/#\sBEGIN\sW3TC\sMinify\score(.*)#\sEND\sW3TC\sMinify\score/s', $root_htaccess_file_contents, $matches ) ) {
				$minify_core[] = $matches[0];
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sW3TC\sMinify\score(.*)#\sEND\sW3TC\sMinify\score/s', "", $root_htaccess_file_contents);
			}

			if ( preg_match( '/#\sBEGIN\sW3TC\sMinify\scache(.*)#\sEND\sW3TC\sMinify\scache/s', $root_htaccess_file_contents, $matches ) ) {
				$minify_cache[] = $matches[0];
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sW3TC\sMinify\scache(.*)#\sEND\sW3TC\sMinify\scache/s', "", $root_htaccess_file_contents);
			}

			if ( preg_match( '/#\sBEGIN\sW3TC\sCDN(.*)#\sEND\sW3TC\sCDN/s', $root_htaccess_file_contents, $matches ) ) {
				$CDN[] = $matches[0];
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sW3TC\sCDN(.*)#\sEND\sW3TC\sCDN/s', "", $root_htaccess_file_contents);
			}		

			if ( empty($browser_cache) && empty($page_cache_core) && empty($page_cache_cache) && empty($skip_404_error) && empty($minify_core) && empty($minify_cache) && empty($CDN) ) {			
				$w3tc_marker_array = array();
				$w3tc_marker_array[] = "# BEGIN W3TC\n\n# END W3TC\n";
				$bps_customcode_cache_merge = array_merge($cc_cache_array, $w3tc_marker_array);	

			} else {
				
				$bps_customcode_cache_merge = array_merge($cc_cache_array, $browser_cache, $page_cache_core, $page_cache_cache, $skip_404_error, $minify_core, $minify_cache, $CDN);
			}

			$cc_cache_unique = array_unique($bps_customcode_cache_merge);
 			// needs to be \n
 			$bps_customcode_cache_implode = implode( "\n", $cc_cache_unique );

			if ( ! is_multisite() ) {

				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);
				
			} else {
					
				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);					
			}

			foreach( $Root_CC_Options as $key => $value ) {
				update_option('bulletproof_security_options_customcode', $Root_CC_Options);
			}		

			$text = '<strong><font color="green">'.__('W3 Total Cache (W3TC) Plugin AutoSetup Successful: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-20"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('Important Note: If you change any of your W3 Total Cache settings at any time, re-run the Setup Wizards again.', 'bulletproof-security').'</span></span></font></strong><br>';
			echo $text;			
			
			## Remove W3TC htaccess code from the Root htaccess file
			if ( file_put_contents($rootHtaccess, $root_htaccess_file_contents) ) {	

				$Root_Autolock = get_option('bulletproof_security_options_autolock');
				
				if ( $Root_Autolock['bps_root_htaccess_autolock'] == 'On' ) {
					chmod($rootHtaccess, 0404);
				}
			}
		}

	} else {
	
		## W3TC Cleanup: Either not installed or activated. Removes any/all W3TC htaccess code from BPS Custom Code and Root htaccess file.
		if ( $w3tc_plugin_active != 1 && ! is_plugin_active_for_network( $w3tc_plugin ) ) { 

			## Remove any existing W3TC htaccess code in Custom Code from the $cc_cache_array.
			foreach ( $bps_customcode_cache_array as $key => $value ) {
				
				if ( preg_match( '/#\sBEGIN\sW3TC\sBrowser\sCache(.*)#\sEND\sW3TC\sBrowser\sCache/s', $value, $matches ) ) {
					$value = preg_replace( '/#\sBEGIN\sW3TC\sBrowser\sCache(.*)#\sEND\sW3TC\sBrowser\sCache/s', "", $value);
				}
		
				if ( preg_match( '/#\sBEGIN\sW3TC\sPage\sCache\score(.*)#\sEND\sW3TC\sPage\sCache\score/s', $value, $matches ) ) {
					$value = preg_replace( '/#\sBEGIN\sW3TC\sPage\sCache\score(.*)#\sEND\sW3TC\sPage\sCache\score/s', "", $value);
				}

				if ( preg_match( '/#\sBEGIN\sW3TC\sPage\sCache\scache(.*)#\sEND\sW3TC\sPage\sCache\scache/s', $value, $matches ) ) {
					$value = preg_replace( '/#\sBEGIN\sW3TC\sPage\sCache\scache(.*)#\sEND\sW3TC\sPage\sCache\scache/s', "", $value);
				}

				if ( preg_match( '/#\sBEGIN\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles(.*)#\sEND\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles/s', $value, $matches ) ) {
					$value = preg_replace( '/#\sBEGIN\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles(.*)#\sEND\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles/s', "", $value);
				}		
		
				if ( preg_match( '/#\sBEGIN\sW3TC\sMinify\score(.*)#\sEND\sW3TC\sMinify\score/s', $value, $matches ) ) {
					$value = preg_replace( '/#\sBEGIN\sW3TC\sMinify\score(.*)#\sEND\sW3TC\sMinify\score/s', "", $value);
				}

				if ( preg_match( '/#\sBEGIN\sW3TC\sMinify\scache(.*)#\sEND\sW3TC\sMinify\scache/s', $value, $matches ) ) {
					$value = preg_replace( '/#\sBEGIN\sW3TC\sMinify\scache(.*)#\sEND\sW3TC\sMinify\scache/s', "", $value);
				}

				if ( preg_match( '/#\sBEGIN\sW3TC\sCDN(.*)#\sEND\sW3TC\sCDN/s', $value, $matches ) ) {
					$value = preg_replace( '/#\sBEGIN\sW3TC\sCDN(.*)#\sEND\sW3TC\sCDN/s', "", $value);
				}

				if ( preg_match( '/#\sBEGIN\sW3TC\n\n#\sEND\sW3TC/', $value, $matches ) ) {
						$value = preg_replace( '/#\sBEGIN\sW3TC\n\n#\sEND\sW3TC/', "", $value);
				}
			
				if ( preg_match('/(\n\r){2,}/', $value, $matches) ) {	
					$value = preg_replace("/(\n\r){2,}/", "\n", $value);
				}
			
				$cc_cache_array[] = trim( $value, " \t\n\r");
			}
			
			$bps_customcode_cache_implode = implode( "\n\n", $cc_cache_array );

			if ( ! is_multisite() ) {

				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);
				
			} else {
					
				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);					
			}

			foreach( $Root_CC_Options as $key => $value ) {
				update_option('bulletproof_security_options_customcode', $Root_CC_Options);
			}

			## Remove any existing W3TC htaccess code in the Root htaccess file.
			$rootHtaccess = ABSPATH . '.htaccess';			
			
			if ( file_exists($rootHtaccess) ) {
				$sapi_type = php_sapi_name();
				$permsRootHtaccess = substr(sprintf('%o', fileperms($rootHtaccess)), -4);
			
				if ( substr($sapi_type, 0, 6) != 'apache' || $permsRootHtaccess != '0666' || $permsRootHtaccess != '0777' ) {
					chmod( $rootHtaccess, 0644 );
				}			
			
				$root_htaccess_file_contents = file_get_contents($rootHtaccess);			
					
				if ( preg_match( '/#\sBEGIN\sW3TC\sBrowser\sCache(.*)#\sEND\sW3TC\sBrowser\sCache/s', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sW3TC\sBrowser\sCache(.*)#\sEND\sW3TC\sBrowser\sCache/s', "", $root_htaccess_file_contents);
				}
		
				if ( preg_match( '/#\sBEGIN\sW3TC\sPage\sCache\score(.*)#\sEND\sW3TC\sPage\sCache\score/s', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sW3TC\sPage\sCache\score(.*)#\sEND\sW3TC\sPage\sCache\score/s', "", $root_htaccess_file_contents);
				}

				if ( preg_match( '/#\sBEGIN\sW3TC\sPage\sCache\scache(.*)#\sEND\sW3TC\sPage\sCache\scache/s', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sW3TC\sPage\sCache\scache(.*)#\sEND\sW3TC\sPage\sCache\scache/s', "", $root_htaccess_file_contents);
				}

				if ( preg_match( '/#\sBEGIN\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles(.*)#\sEND\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles/s', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles(.*)#\sEND\sW3TC\sSkip\s404\serror\shandling\sby\sWordPress\sfor\sstatic\sfiles/s', "", $root_htaccess_file_contents);
				}		
		
				if ( preg_match( '/#\sBEGIN\sW3TC\sMinify\score(.*)#\sEND\sW3TC\sMinify\score/s', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sW3TC\sMinify\score(.*)#\sEND\sW3TC\sMinify\score/s', "", $root_htaccess_file_contents);
				}

				if ( preg_match( '/#\sBEGIN\sW3TC\sMinify\scache(.*)#\sEND\sW3TC\sMinify\scache/s', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sW3TC\sMinify\scache(.*)#\sEND\sW3TC\sMinify\scache/s', "", $root_htaccess_file_contents);
				}

				if ( preg_match( '/#\sBEGIN\sW3TC\sCDN(.*)#\sEND\sW3TC\sCDN/s', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sW3TC\sCDN(.*)#\sEND\sW3TC\sCDN/s', "", $root_htaccess_file_contents);
				}					
					
				if ( preg_match( '/#\sBEGIN\sW3TC\n\n#\sEND\sW3TC/', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sW3TC\n\n#\sEND\sW3TC/', "", $root_htaccess_file_contents);
				}					
					
				file_put_contents($rootHtaccess, $root_htaccess_file_contents);			
			
				$Root_Autolock = get_option('bulletproof_security_options_autolock');
				
				if ( isset($Root_Autolock['bps_root_htaccess_autolock']) && $Root_Autolock['bps_root_htaccess_autolock'] == 'On' ) {
					chmod($rootHtaccess, 0404);
				}
				
				$text = '<strong><font color="green">'.__('W3 Total Cache (W3TC) Plugin AutoCleanup Successful: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-60"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('AutoCleanup has removed all W3TC htaccess code from BPS Custom Code and your Root htaccess file if it existed. If you have W3TC installed and are still planning on using W3TC then re-run the Setup Wizards after you have activated the W3TC plugin again and resaved your W3TC plugin settings again.', 'bulletproof-security').'</span></span></font></strong><br>';		
				
				echo $text;	
			}
		}
	}	
}

// Comet Cache (free and Pro) Setup & Cleanup: Creates the Comet Cache htaccess code in BPS Custom Code & the Comet Cache code in the wp-config.php file.
// Get Comet Cache htaccess code from pre-made template files here: /src/includes/templates/htaccess based on CC db option values.
// Unlock the Root htaccess file and remove any existing CC htaccess code. Unlock the wp-config.php file and write the define( 'WP_CACHE', true ); if it does not exist.
// Comet Cache DB options: htaccess_browser_caching_enable, htaccess_gzip_enable, htaccess_enforce_exact_host_name, htaccess_enforce_canonical_urls & 
// htaccess_access_control_allow_origin.
// CC free only uses htaccess_gzip_enable & Pro has all other DB options.
// Notes: htaccess_access_control_allow_origin is for CDN htaccess code, but do not check the cdn_enable DB option value.
// htaccess_enforce_canonical_urls uses 2 different template files (-no-ts- and -ts-) based on whether a trailing slash or no trailing slash is being used in permalinks.
// Note: htaccess code is created in the site root htaccess file for GWIOD site types.
function bpsPro_Pwizard_Autofix_Comet_Cache() {
	
	$AutoFix_Options = get_option('bulletproof_security_options_wizard_autofix');
	
	if ( $AutoFix_Options['bps_wizard_autofix'] == 'Off' ) {
		return;
	}

	$comet_cache = 'comet-cache/comet-cache.php';
	$comet_cache_pro = 'comet-cache-pro/comet-cache-pro.php';
	$comet_cache_active = in_array( $comet_cache, apply_filters('active_plugins', get_option('active_plugins')));
	$comet_cache_pro_active = in_array( $comet_cache_pro, apply_filters('active_plugins', get_option('active_plugins')));

	// CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_cache = htmlspecialchars_decode( $CC_Options_root['bps_customcode_one'], ENT_QUOTES );
	$bps_customcode_cache_array = array();
	$bps_customcode_cache_array[] = $bps_customcode_cache;
	$cc_cache_array = array();

	if ( $comet_cache_active == 1 || is_plugin_active_for_network( $comet_cache ) || $comet_cache_pro_active == 1 || is_plugin_active_for_network( $comet_cache_pro ) ) {

		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}

		## GWIOD site type: AutoSetup is not required since htaccess code is written to the site root htaccess file.
		if ( $bpsSiteUrl != $bpsHomeUrl ) {
			$text = '<strong><font color="green">'.__('Comet Cache Plugin AutoSetup not required: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-20"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('GWIOD site types do not require AutoSetup because Comet Cache creates htaccess code in the site root htaccess file.', 'bulletproof-security').'</span></span></font></strong><br>';
			echo $text;	
			return;
		}

		## Remove any existing Comet Cache htaccess code in Custom Code from the $cc_cache_array so that new CC htaccess code is created each time.
		## Important Note: If dots are used (.*) then newlines and spaces are ignored when using the /s modifier.
		// Cleans up extra Newlines, Returns & whitespaces.
		foreach ( $bps_customcode_cache_array as $key => $value ) {
				
			if ( preg_match( '/#\sBEGIN\sComet\sCache(.*)#\sEND\sComet\sCache\sWmVuQ2FjaGU/s', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN\sComet\sCache(.*)#\sEND\sComet\sCache\sWmVuQ2FjaGU/s', "", $value);
			}

			if ( preg_match('/(\n\r){2,}/', $value, $matches) ) {	
				$value = preg_replace("/(\n\r){2,}/", "\n", $value);
			}				
				
			$cc_cache_array[] = trim( $value, " \t\n\r");
		}
		
		$wpconfig = ABSPATH . 'wp-config.php';		
		
		if ( ! file_exists( $wpconfig ) ) {
				
			$text = '<strong><font color="#fb0101">'.__('Error: The Pre-Installation Wizard is unable to add the Comet Cache WP_CACHE code in your wp-config.php file.', 'bulletproof-security').'</font><br>'.__('A wp-config.php file was NOT found in your WordPress website root folder. If you have moved your wp-config.php file to another folder location then you will need to either move the wp-config.php file back to its default WordPress folder location and run the Pre-Installation Wizard again or manually edit your wp-config.php file and add the Comet Cache WP_CACHE code. Click this link for the steps to manually edit your wp-config.php file: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/manually-editing-the-wordpress-wp-config-php-file/" target="_blank" title="Link opens in a new Browser window">'.__('Manually Edit the WordPress wp-config.php file', 'bulletproof-security').'</a><br>'; 
			echo $text;
		}	
		
		## Delete any Comet Cache htacces code in the Root htaccess file.
		$rootHtaccess = ABSPATH . '.htaccess';
		
		if ( file_exists($rootHtaccess) ) {
		
			$sapi_type = php_sapi_name();
			$permsRootHtaccess = substr(sprintf('%o', fileperms($rootHtaccess)), -4);

			if ( file_exists( $wpconfig ) ) {
			
				$perms_wpconfig = substr(sprintf('%o', fileperms($wpconfig)), -4);
				
				if ( substr($sapi_type, 0, 6) != 'apache' || $perms_wpconfig != '0666' || $perms_wpconfig != '0777' ) { // Windows IIS, XAMPP, etc
					chmod( $wpconfig, 0644 );
				}
			}
	
			if ( substr($sapi_type, 0, 6) != 'apache' || $permsRootHtaccess != '0666' || $permsRootHtaccess != '0777' ) {
				chmod( $rootHtaccess, 0644 );
			}

			$root_htaccess_file_contents = file_get_contents($rootHtaccess);

			if ( preg_match( '/#\sBEGIN\sComet\sCache(.*)#\sEND\sComet\sCache\sWmVuQ2FjaGU/s', $root_htaccess_file_contents, $matches ) ) {
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sComet\sCache(.*)#\sEND\sComet\sCache\sWmVuQ2FjaGU/s', "", $root_htaccess_file_contents);
			}
		
			file_put_contents($rootHtaccess, $root_htaccess_file_contents);			
			
			$Root_Autolock = get_option('bulletproof_security_options_autolock');
				
			if ( $Root_Autolock['bps_root_htaccess_autolock'] == 'On' ) {
				chmod($rootHtaccess, 0404);
			}

			## Get new Comet Cache htaccess code from template files.		
			$comet_cache_options = get_option('comet_cache_options');

			if ( $comet_cache_active == 1 || is_plugin_active_for_network( $comet_cache ) ) {
				$access_control_allow_origin_enable = WP_PLUGIN_DIR . '/comet-cache/src/includes/templates/htaccess/access-control-allow-origin-enable.txt';
				$browser_caching_enable = WP_PLUGIN_DIR . '/comet-cache/src/includes/templates/htaccess/browser-caching-enable.txt';
				$canonical_urls_no_ts_enable = WP_PLUGIN_DIR . '/comet-cache/src/includes/templates/htaccess/canonical-urls-no-ts-enable.txt';
				$canonical_urls_ts_enable = WP_PLUGIN_DIR . '/comet-cache/src/includes/templates/htaccess/canonical-urls-ts-enable.txt';
				$enforce_exact_host_name = WP_PLUGIN_DIR . '/comet-cache/src/includes/templates/htaccess/enforce-exact-host-name.txt';
				$gzip_enable = WP_PLUGIN_DIR . '/comet-cache/src/includes/templates/htaccess/gzip-enable.txt';
			}
			
			if ( $comet_cache_pro_active == 1 || is_plugin_active_for_network( $comet_cache_pro ) ) {
				$access_control_allow_origin_enable = WP_PLUGIN_DIR . '/comet-cache-pro/src/includes/templates/htaccess/access-control-allow-origin-enable.txt';
				$browser_caching_enable = WP_PLUGIN_DIR . '/comet-cache-pro/src/includes/templates/htaccess/browser-caching-enable.txt';
				$canonical_urls_no_ts_enable = WP_PLUGIN_DIR . '/comet-cache-pro/src/includes/templates/htaccess/canonical-urls-no-ts-enable.txt';
				$canonical_urls_ts_enable = WP_PLUGIN_DIR . '/comet-cache-pro/src/includes/templates/htaccess/canonical-urls-ts-enable.txt';
				$enforce_exact_host_name = WP_PLUGIN_DIR . '/comet-cache-pro/src/includes/templates/htaccess/enforce-exact-host-name.txt';
				$gzip_enable = WP_PLUGIN_DIR . '/comet-cache-pro/src/includes/templates/htaccess/gzip-enable.txt';				
			}

			$access_control_allow_origin_enable_array = array();
			$browser_caching_enable_array = array();
			$canonical_urls_no_ts_enable_array = array();
			$canonical_urls_ts_enable_array = array();
			$enforce_exact_host_name_array = array();
			$gzip_enable_array = array();
			
			global $wp_rewrite;

			if ( $comet_cache_options['htaccess_gzip_enable'] == '1' ) {
				$gzip_enable_array[] = file_get_contents($gzip_enable);
			}

			if ( $comet_cache_options['htaccess_access_control_allow_origin'] == '1' ) {
				$access_control_allow_origin_enable_array[] = file_get_contents($access_control_allow_origin_enable);
			}

			if ( $comet_cache_options['htaccess_browser_caching_enable'] == '1' ) {
				$browser_caching_enable_array[] = file_get_contents($browser_caching_enable);
			}

			if ( $comet_cache_options['htaccess_enforce_exact_host_name'] == '1' ) {
				$enforce_exact_host_name_array[] = file_get_contents($enforce_exact_host_name);
			}

			if ( $comet_cache_options['htaccess_enforce_canonical_urls'] == '1' ) {

				if ( $wp_rewrite->permalink_structure ) {
					
					if ( ! $wp_rewrite->use_trailing_slashes || $wp_rewrite->use_trailing_slashes != 1 ) {
						$canonical_urls_no_ts_enable_array[] = file_get_contents($canonical_urls_no_ts_enable);
					} elseif ( $wp_rewrite->use_trailing_slashes == 1 ) {
						$canonical_urls_ts_enable_array[] = file_get_contents($canonical_urls_ts_enable);
					}
					
				}
			}

			if ( empty($gzip_enable_array) && empty($access_control_allow_origin_enable_array) && empty($browser_caching_enable_array) && empty($enforce_exact_host_name_array) && empty($canonical_urls_no_ts_enable_array) && empty($canonical_urls_ts_enable_array) ) {			
				
				$comet_cache_array_replace = array();
				
			} else {
				
				$comet_cache_begin_marker = array();
				$comet_cache_begin_marker[] = "\n\n# BEGIN Comet Cache WmVuQ2FjaGU (the WmVuQ2FjaGU marker is required for Comet Cache; do not remove)\n";
				
				$comet_cache_end_marker = array();
				$comet_cache_end_marker[] = "# END Comet Cache WmVuQ2FjaGU\n";
				
				$comet_cache_array_merge = array_merge($comet_cache_begin_marker, $gzip_enable_array, $access_control_allow_origin_enable_array, $browser_caching_enable_array, $enforce_exact_host_name_array, $canonical_urls_no_ts_enable_array, $canonical_urls_ts_enable_array, $comet_cache_end_marker);
			
				$pattern_array = array( '/%%REWRITE_BASE%%/', '/%%HOST_NAME_AS_REGEX_FRAG%%/', '/%%REST_REQUEST_PREFIX_AS_REGEX_FRAG%%/' );
				$replace_array = array( bps_wp_get_root_folder(), bpsGetDomainRoot(), rest_get_url_prefix() );
				$comet_cache_array_replace = preg_replace($pattern_array, $replace_array, $comet_cache_array_merge);
			}
	
			$bps_customcode_cache_merge = array_merge($cc_cache_array, $comet_cache_array_replace);
			$cc_cache_unique = array_unique($bps_customcode_cache_merge);
					
			// Needs to be \n
			$bps_customcode_cache_implode = implode( "\n", $cc_cache_unique );		

			if ( ! is_multisite() ) {

				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);
				
			} else {
					
				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);					
			}

			foreach( $Root_CC_Options as $key => $value ) {
				update_option('bulletproof_security_options_customcode', $Root_CC_Options);
			}		

			## Add the define( 'WP_CACHE', true ); code in the wp-config.php file if it does not exist
			if ( file_exists( $wpconfig ) ) {
				$wp_config_contents = file_get_contents($wpconfig);
			
				if ( ! preg_match( '/define(.*)\((.*)WP_CACHE(.*)(true|false)(.*)\);/', $wp_config_contents, $matches ) ) {
					$wp_config_contents = preg_replace( '/<\?php(.*\s*){1}/', '<?php'."\ndefine( 'WP_CACHE', true );\n", $wp_config_contents);
					file_put_contents($wpconfig, $wp_config_contents);
				}
			}
			
			$text = '<strong><font color="green">'.__('Comet Cache Plugin AutoSetup Successful: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-20"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('Important Note: If you change any of your Comet Cache settings at any time, re-run the Setup Wizards again.', 'bulletproof-security').'</span></span></font></strong><br>';
			echo $text;
		}

	} else {
	
		## Comet Cache Cleanup: Either not installed or activated. Removes any/all Comet Cache htaccess code from BPS Custom Code and Root htaccess file.
		if ( $comet_cache_active != 1 && $comet_cache_pro_active != 1 && ! is_plugin_active_for_network( $comet_cache ) && ! is_plugin_active_for_network( $comet_cache_pro ) ) { 

			## Remove any existing Comet Cache htaccess code in Custom Code from the $cc_cache_array.
			foreach ( $bps_customcode_cache_array as $key => $value ) {
				
				if ( preg_match( '/#\sBEGIN\sComet\sCache(.*)#\sEND\sComet\sCache\sWmVuQ2FjaGU/s', $value, $matches ) ) {
					$value = preg_replace( '/#\sBEGIN\sComet\sCache(.*)#\sEND\sComet\sCache\sWmVuQ2FjaGU/s', "", $value);
				}

				if ( preg_match('/(\n\r){2,}/', $value, $matches) ) {	
					$value = preg_replace("/(\n\r){2,}/", "\n", $value);
				}				
			
				$cc_cache_array[] = trim( $value, " \t\n\r");
			}
			
			$bps_customcode_cache_implode = implode( "\n\n", $cc_cache_array );

			if ( ! is_multisite() ) {

				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);
				
			} else {
					
				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);					
			}

			foreach ( $Root_CC_Options as $key => $value ) {
				update_option('bulletproof_security_options_customcode', $Root_CC_Options);
			}

			## Remove any existing Comet Cache htaccess code in the Root htaccess file.
			$rootHtaccess = ABSPATH . '.htaccess';			
			
			if ( file_exists($rootHtaccess) ) {
				$sapi_type = php_sapi_name();
				$permsRootHtaccess = substr(sprintf('%o', fileperms($rootHtaccess)), -4);
			
				if ( substr($sapi_type, 0, 6) != 'apache' || $permsRootHtaccess != '0666' || $permsRootHtaccess != '0777' ) {
					chmod( $rootHtaccess, 0644 );
				}			
			
				$root_htaccess_file_contents = file_get_contents($rootHtaccess);			
			
				if ( preg_match( '/#\sBEGIN\sComet\sCache(.*)#\sEND\sComet\sCache\sWmVuQ2FjaGU/s', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sComet\sCache(.*)#\sEND\sComet\sCache\sWmVuQ2FjaGU/s', "", $root_htaccess_file_contents);
				}

				file_put_contents($rootHtaccess, $root_htaccess_file_contents);			
			
				$Root_Autolock = get_option('bulletproof_security_options_autolock');
				
				if ( isset($Root_Autolock['bps_root_htaccess_autolock']) && $Root_Autolock['bps_root_htaccess_autolock'] == 'On' ) {
					chmod($rootHtaccess, 0404);
				}
				
				$text = '<strong><font color="green">'.__('Comet Cache Plugin AutoCleanup Successful: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-60"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('AutoCleanup has removed all Comet Cache htaccess code from BPS Custom Code and your Root htaccess file if it existed. If you have Comet Cache installed and are still planning on using Comet Cache then re-run the Setup Wizards after you have activated the Comet Cache plugin again and resaved your Comet Cache plugin settings again.', 'bulletproof-security').'</span></span></font></strong><br>';
				echo $text;	
			}
		}
	}	
}

// Endurance Page Cache (EPC) Setup & Cleanup: Creates the EPC htaccess code in BPS Custom Code & removes EPC htaccess code from the Root htaccess file.
// The EPC plugin does not add/create any code in the wp-config.php file.
// Requires Prerequisite Manual Steps by User to generate EPC htaccess code: HUD message displayed to Unlock Root htaccess file, save EPC settings & run the Wizards.
// Unlock the Root htaccess file, get the EPC htaccess code and then remove any existing EPC htaccess code in the Root htaccess file.
// Notes: The EPC plugin uses the standard/default WP Markers instead of using unique Markers for its htaccess code.
// That will probably change eventually so check each EPC plugin version to see if/when the EPC Markers are changed.
// Old EPC code using these custom Markers needs to be removed from Custom Code: # BEGIN|END ENDURANCE PAGE CACHE
// The EPC plugin only sees and uses the default WP Markers and will continue to create additional EPC htaccess code if it does not find the default WP Markers
// Latest EPC plugin version checked: .9
// Note: htaccess code is created in the site root htaccess file for GWIOD site types.
function bpsPro_Pwizard_Autofix_Endurance() {
	
	$AutoFix_Options = get_option('bulletproof_security_options_wizard_autofix');
	
	if ( $AutoFix_Options['bps_wizard_autofix'] == 'Off' ) {
		return;
	}

	// CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_cache = htmlspecialchars_decode( $CC_Options_root['bps_customcode_one'], ENT_QUOTES );
	$bps_customcode_cache_array = array();
	$bps_customcode_cache_array[] = $bps_customcode_cache;
	$cc_cache_array = array();

	$epc_options = get_option( 'mm_cache_settings' );
	$epc_cache_level_options = get_option( 'endurance_cache_level' );
	$epc_file = WP_CONTENT_DIR . '/mu-plugins/endurance-page-cache.php';
	
	if ( file_exists($epc_file) && $epc_options['page'] == 'enabled' ) {
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}

		## GWIOD site type: AutoSetup is not required since htaccess code is written to the site root htaccess file.
		if ( $bpsSiteUrl != $bpsHomeUrl ) {
			$text = '<strong><font color="green">'.__('Endurance Page Cache (EPC) Plugin AutoSetup not required: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-20"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('GWIOD site types do not require AutoSetup because EPC creates htaccess code in the site root htaccess file.', 'bulletproof-security').'</span></span></font></strong><br>';
			echo $text;	
			return;
		}

		## Remove any existing EPC htaccess code in Custom Code from the $cc_cache_array so that new EPC htaccess code is created each time.
		## Important Note: If dots are used (.*) then newlines and spaces are ignored when using the /s modifier.
		// Cleans up extra Newlines, Returns & whitespaces.
		foreach ( $bps_customcode_cache_array as $key => $value ) {
				
			if ( preg_match( '/#\sBEGIN\sWordPress(.*)endurance-page-cache(.*)#\sEND\sWordPress/s', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN\sWordPress(.*)endurance-page-cache(.*)#\sEND\sWordPress/s', "", $value);
			}
			
			if ( preg_match( '/#\sBEGIN\sENDURANCE\sPAGE\sCACHE(.*)#\sEND\sENDURANCE\sPAGE\sCACHE/s', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN\sENDURANCE\sPAGE\sCACHE(.*)#\sEND\sENDURANCE\sPAGE\sCACHE/s', "", $value);
			}

			if ( preg_match('/(\n\r){2,}/', $value, $matches) ) {	
				$value = preg_replace("/(\n\r){2,}/", "\n", $value);
			}				
				
			$cc_cache_array[] = trim( $value, " \t\n\r");
		}
		
		$rootHtaccess = ABSPATH . '.htaccess';
		
		if ( file_exists($rootHtaccess) ) {
			
			$sapi_type = php_sapi_name();
			$permsRootHtaccess = substr(sprintf('%o', fileperms($rootHtaccess)), -4);

			if ( substr($sapi_type, 0, 6) != 'apache' || $permsRootHtaccess != '0666' || $permsRootHtaccess != '0777' ) {
				chmod( $rootHtaccess, 0644 );
			}

			$root_htaccess_file_contents = file_get_contents($rootHtaccess);			

			$wp_default_rewrite_code = '/<IfModule\smod_rewrite\.c>\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule(.*)\s*<\/IfModule>\n/';
			$epc_htaccess_code = array();

			## Remove the EPC htaccess code from the Root htaccess file after putting any EPC code into an array and updating the CC DB options.
			if ( preg_match( '/#\sBEGIN\sWordPress(.*)endurance-page-cache(.*)#\sEND\sWordPress/s', $root_htaccess_file_contents, $matches ) ) {
				$epc_htaccess_code[] = preg_replace( $wp_default_rewrite_code, "", $matches[0] );
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sWordPress(.*)endurance-page-cache(.*)#\sEND\sWordPress/s', "", $root_htaccess_file_contents);
			}

			// Suppress the coding mistake/php error "Illegal string offset" in the EPC plugin. 
			// The endurance_cache_level DB option value is not saved as an array and is incorrectly saved as a string value instead. 
			if ( empty($epc_htaccess_code) && $epc_cache_level_options['endurance_cache_level'] > 0 ) {
				$text = '<strong><font color="#fb0101">'.__('Error: Endurance Page Cache (EPC) Plugin AutoSetup Unsuccessful - ', 'bulletproof-security').'</font><font color="blue">'.__('The Setup Wizard did not find any Endurance Page Cache htaccess code in your Root htaccess file. Do these steps to fix the problem: Go to the BPS htaccess File Editor page, click the Unlock htaccess File button, go to the WordPress Settings > General page, scroll down to Endurance Cache settings, click the Save Changes button, go back to this Setup Wizard page and run the Pre-Installation Wizard and Setup Wizard again.', 'bulletproof-security').'</font></strong><br>';
				echo $text;
				return;
			}
			
			$bps_customcode_cache_merge = array_merge($cc_cache_array, $epc_htaccess_code);
			$cc_cache_unique = array_unique($bps_customcode_cache_merge);
 			// needs to be \n
 			$bps_customcode_cache_implode = implode( "\n", $cc_cache_unique );
			
			if ( ! is_multisite() ) {

				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);
				
			} else {
					
				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);					
			}

			foreach( $Root_CC_Options as $key => $value ) {
				update_option('bulletproof_security_options_customcode', $Root_CC_Options);
			}		

			$text = '<strong><font color="green">'.__('Endurance Page Cache (EPC) Plugin AutoSetup Successful: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-20"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('Important Note: If you disable or enable the Endurance Page Cache plugin at any time, re-run the Setup Wizards again.', 'bulletproof-security').'</span></span></font></strong><br>';
			echo $text;

			## Remove Endurance Page Cache htaccess code from the Root htaccess file
			if ( file_put_contents($rootHtaccess, $root_htaccess_file_contents) ) {	

				$Root_Autolock = get_option('bulletproof_security_options_autolock');
				
				if ( $Root_Autolock['bps_root_htaccess_autolock'] == 'On' ) {
					chmod($rootHtaccess, 0404);
				}
			}
		}

	} else {
		
		## EPC Cleanup: Either not installed or disabled. Removes any/all EPC htaccess code from BPS Custom Code and Root htaccess file.
		if ( ! file_exists($epc_file) || $epc_options['page'] == 'disabled' ) { 

			## Remove any existing EPC htaccess code in Custom Code from the $cc_cache_array.
			foreach ( $bps_customcode_cache_array as $key => $value ) {
				
				if ( preg_match( '/#\sBEGIN\sWordPress(.*)endurance-page-cache(.*)#\sEND\sWordPress/s', $value, $matches ) ) {
					$value = preg_replace( '/#\sBEGIN\sWordPress(.*)endurance-page-cache(.*)#\sEND\sWordPress/s', "", $value);
				}
			
				if ( preg_match( '/#\sBEGIN\sENDURANCE\sPAGE\sCACHE(.*)#\sEND\sENDURANCE\sPAGE\sCACHE/s', $value, $matches ) ) {
					$value = preg_replace( '/#\sBEGIN\sENDURANCE\sPAGE\sCACHE(.*)#\sEND\sENDURANCE\sPAGE\sCACHE/s', "", $value);
				}
				
				if ( preg_match('/(\n\r){2,}/', $value, $matches) ) {	
					$value = preg_replace("/(\n\r){2,}/", "\n", $value);
				}				
			
				$cc_cache_array[] = trim( $value, " \t\n\r");
			}
			
			$bps_customcode_cache_implode = implode( "\n\n", $cc_cache_array );

			if ( ! is_multisite() ) {

				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);
				
			} else {
					
				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);					
			}

			foreach( $Root_CC_Options as $key => $value ) {
				update_option('bulletproof_security_options_customcode', $Root_CC_Options);
			}

			## Remove any existing EPC htaccess code in the Root htaccess file.
			$rootHtaccess = ABSPATH . '.htaccess';			
			
			if ( file_exists($rootHtaccess) ) {
			
				$permsRootHtaccess = substr(sprintf('%o', fileperms($rootHtaccess)), -4);
			
				if ( substr($sapi_type, 0, 6) != 'apache' || $permsRootHtaccess != '0666' || $permsRootHtaccess != '0777' ) {
					chmod( $rootHtaccess, 0644 );
				}			
			
				$root_htaccess_file_contents = file_get_contents($rootHtaccess);			
			
				if ( preg_match( '/#\sBEGIN\sWordPress(.*)endurance-page-cache(.*)#\sEND\sWordPress/s', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sWordPress(.*)endurance-page-cache(.*)#\sEND\sWordPress/s', "", $root_htaccess_file_contents);
				}
			
				file_put_contents($rootHtaccess, $root_htaccess_file_contents);			
			
				$Root_Autolock = get_option('bulletproof_security_options_autolock');
				
				if ( $Root_Autolock['bps_root_htaccess_autolock'] == 'On' ) {
					chmod($rootHtaccess, 0404);
				}
				
				$text = '<strong><font color="green">'.__('Endurance Page Cache (EPC) Plugin AutoCleanup Successful: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-60"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('AutoCleanup has removed all Endurance Page Cache htaccess code from BPS Custom Code and your Root htaccess file if it existed. If you have disabled the Endurance Page Cache plugin and are still planning on using Endurance Page Cache then re-run the Setup Wizards after you have enabled the Endurance Page Cache plugin again.', 'bulletproof-security').'</span></span></font></strong><br>';
				echo $text;	
			}
		}
	}	
}

// WP Fastest Cache (free & Premium) Setup & Cleanup: Creates the WPFC htaccess code in BPS Custom Code & removes WPFC htaccess code from the Root htaccess file.
// WPFC does not create code in the wp-config.php file by default. Only creates code if the wp-postviews plugin is installed. Let WPFC handle that.
// WPFC does not automatically create htaccess code in the root htaccess file on (first) plugin activation, but does add/remove htaccess code on reactivation & deactivation.
// Requires Prerequisite Manual Steps by User to generate WPFC htaccess code: HUD message displayed to Unlock Root htaccess file, save WPFC settings & run the Wizards.
// Unlock the Root htaccess file, get the WPFC htaccess code and then remove any existing WPFC htaccess code in the Root htaccess file.
// Notes: WPFC htaccess writing code is in: /inc/admin.php. Writes htaccess code to the top of the root htaccess file. WPFC Premium version tested: 1.3.9 released April 2017.
// The premium version installs a new plugin folder: /wp-fastest-cache-premium/, but the free version must still be installed as well.
// Note: htaccess code is created in the site root htaccess file for GWIOD site types, but WPFC fails to correctly detect the site root htaccess file.
// BPS 5.1: Commented out the WPFC option checking code. Things have changed in WPFC. So no longer use the WPFC option checking code.
// Note: On WPFC plugin deactivation the htaccess code is removed from the root htaccess file.
// The WPFC plugin deactivated condition will only fire if someone activates Root BPM if they still have WPFC htaccess code in CC.
function bpsPro_Pwizard_Autofix_WPFC() {
	
	$AutoFix_Options = get_option('bulletproof_security_options_wizard_autofix');
	
	if ( $AutoFix_Options['bps_wizard_autofix'] == 'Off' ) {
		return;
	}

	// CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_cache = htmlspecialchars_decode( $CC_Options_root['bps_customcode_one'], ENT_QUOTES );
	$bps_customcode_cache_array = array();
	$bps_customcode_cache_array[] = $bps_customcode_cache;
	$cc_cache_array = array();	
	
	//$wpfc_options = get_option('WpFastestCache');
	$wpfc_plugin = 'wp-fastest-cache/wpFastestCache.php';
	$wpfc_plugin_active = in_array( $wpfc_plugin, apply_filters('active_plugins', get_option('active_plugins')));

	// WPFC currently does not work on Multisite, but leave the network condition in case that changes in the future.
	if ( $wpfc_plugin_active == 1 || is_plugin_active_for_network( $wpfc_plugin ) ) {

		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}

		## GWIOD site type: AutoSetup is not required since htaccess code is written to the site root htaccess file.
		if ( $bpsSiteUrl != $bpsHomeUrl ) {
			$text = '<strong><font color="green">'.__('WP Fastest Cache (WPFC) Plugin AutoSetup not required: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-20"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('GWIOD site types do not require AutoSetup because WPFC creates htaccess code in the site root htaccess file.', 'bulletproof-security').'</span></span></font></strong><br>';
			echo $text;	
			return;
		}

		## Remove any existing WPFC htaccess code in Custom Code from the $cc_cache_array so that new WPFC htaccess code is created each time.
		## Important Note: If dots are used (.*) then newlines and spaces are ignored when using the /s modifier.
		// Cleans up extra Newlines, Returns & whitespaces.
		foreach ( $bps_customcode_cache_array as $key => $value ) {
				
			if ( preg_match( '/#\sBEGIN(.*)WpFastestCache(.*)#\sEND(.*)WpFastestCache/s', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN(.*)WpFastestCache(.*)#\sEND(.*)WpFastestCache/s', "", $value);
			}

			if ( preg_match('/(\n\r){2,}/', $value, $matches) ) {	
				$value = preg_replace("/(\n\r){2,}/", "\n", $value);
			}				
				
			$cc_cache_array[] = trim( $value, " \t\n\r");
		}
	
		$rootHtaccess = ABSPATH . '.htaccess';
		
		if ( file_exists($rootHtaccess) ) {
			
			$sapi_type = php_sapi_name();
			$permsRootHtaccess = substr(sprintf('%o', fileperms($rootHtaccess)), -4);

			if ( substr($sapi_type, 0, 6) != 'apache' || $permsRootHtaccess != '0666' || $permsRootHtaccess != '0777' ) {
				chmod( $rootHtaccess, 0644 );
			}

			$root_htaccess_file_contents = file_get_contents($rootHtaccess);			

			$wpfc_default_code = array();
			$wpfc_gzip_code = array();
			$wpfc_lbc_code = array();
			$wpfc_webp_code = array();
			
			## Remove the WPFC htaccess code from the Root htaccess file after putting any WPFC code into an array and updating the CC DB options.
			// Notes: WPFC has a HTTP_HOST rewrite section of code at the top of the WPFC htaccess code that probably should not be there. Leave it for now - don't strip it out.
			// Need to get individual blocks of WPFC code since there is a fubar Regex coding mistake in WPFC that splits and moves htaccess code incorrectly.
			// WPFC default htaccess Marker/code order: WpFastestCache, GzipWpFastestCache, LBCWpFastestCache and WEBPWpFastestCache. 
			if ( preg_match( '/#\sBEGIN\sWpFastestCache(.*)#\sEND\sWpFastestCache/s', $root_htaccess_file_contents, $matches ) ) {
				$wpfc_default_code[] = $matches[0];
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sWpFastestCache(.*)#\sEND\sWpFastestCache/s', "", $root_htaccess_file_contents);
			}

			if ( preg_match( '/#\sBEGIN\sGzipWpFastestCache(.*)#\sEND\sGzipWpFastestCache/s', $root_htaccess_file_contents, $matches ) ) {
				$wpfc_gzip_code[] = $matches[0];
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sGzipWpFastestCache(.*)#\sEND\sGzipWpFastestCache/s', "", $root_htaccess_file_contents);
			}			
			
			if ( preg_match( '/#\sBEGIN\sLBCWpFastestCache(.*)#\sEND\sLBCWpFastestCache/s', $root_htaccess_file_contents, $matches ) ) {
				$wpfc_lbc_code[] = $matches[0];
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sLBCWpFastestCache(.*)#\sEND\sLBCWpFastestCache/s', "", $root_htaccess_file_contents);
			}			
			
			if ( preg_match( '/#\sBEGIN\sWEBPWpFastestCache(.*)#\sEND\sWEBPWpFastestCache/s', $root_htaccess_file_contents, $matches ) ) {
				$wpfc_webp_code[] = $matches[0];
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sWEBPWpFastestCache(.*)#\sEND\sWEBPWpFastestCache/s', "", $root_htaccess_file_contents);
			}

			// Check the WPFC wpFastestCacheStatus == on db option value for the default Cache System enable|disable: creates default & page caching htaccess code.
			if ( /*$wpfc_options['wpFastestCacheStatus'] == 'on' && */ empty($wpfc_default_code) ) {			

				$text = '<strong><font color="#fb0101">'.__('Error: WP Fastest Cache (WPFC) Plugin AutoSetup Unsuccessful: ', 'bulletproof-security').'</font><font color="black">'.__('The Setup Wizard did not find any WPFC htaccess code in your Root htaccess file. Do these steps to fix the problem: Go to the BPS htaccess File Editor page, click the Unlock htaccess File button, go to the WPFC plugin Settings page, click the Submit button, go back to this Setup Wizard page and run the Pre-Installation Wizard and Setup Wizard again.', 'bulletproof-security').'</font></strong><br>';
				echo $text;
				return;
			
			} else {
				
				$bps_customcode_cache_merge = array_merge($cc_cache_array, $wpfc_default_code, $wpfc_gzip_code, $wpfc_lbc_code, $wpfc_webp_code);
			}

			$cc_cache_unique = array_unique($bps_customcode_cache_merge);
 			// MUST be \n\n
 			$bps_customcode_cache_implode = implode( "\n\n", $cc_cache_unique );
			
			if ( ! is_multisite() ) {

				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);
				
			} else {
					
				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);					
			}

			foreach( $Root_CC_Options as $key => $value ) {
				update_option('bulletproof_security_options_customcode', $Root_CC_Options);
			}		

			$text = '<strong><font color="green">'.__('WP Fastest Cache (WPFC) Plugin AutoSetup Successful: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-20"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('Important Note: If you change any of your WP Fastest Cache settings at any time, re-run the Setup Wizards again.', 'bulletproof-security').'</span></span></font></strong><br>';
			echo $text;

			## Remove WP Fastest Cache htaccess code from the Root htaccess file
			if ( file_put_contents($rootHtaccess, $root_htaccess_file_contents) ) {	

				$Root_Autolock = get_option('bulletproof_security_options_autolock');
				
				if ( $Root_Autolock['bps_root_htaccess_autolock'] == 'On' ) {
					chmod($rootHtaccess, 0404);
				}
			}
		}

	} else {
		
		## WPFC Cleanup: Either not installed or activated. Removes any/all WPFC htaccess code from BPS Custom Code and Root htaccess file.
		if ( $wpfc_plugin_active != 1 && ! is_plugin_active_for_network( $wpfc_plugin ) ) {
			
			## Remove any existing WPFC htaccess code in Custom Code from the $cc_cache_array.
			foreach ( $bps_customcode_cache_array as $key => $value ) {
				
				if ( preg_match( '/#\sBEGIN(.*)WpFastestCache(.*)#\sEND(.*)WpFastestCache/s', $value, $matches ) ) {
					$value = preg_replace( '/#\sBEGIN(.*)WpFastestCache(.*)#\sEND(.*)WpFastestCache/s', "", $value);
				}

				if ( preg_match('/(\n\r){2,}/', $value, $matches) ) {	
					$value = preg_replace("/(\n\r){2,}/", "\n", $value);
				}				
			
				$cc_cache_array[] = trim( $value, " \t\n\r");
			}
			
			$bps_customcode_cache_implode = implode( "\n\n", $cc_cache_array );

			if ( ! is_multisite() ) {

				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);
				
			} else {
					
				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);					
			}

			foreach( $Root_CC_Options as $key => $value ) {
				update_option('bulletproof_security_options_customcode', $Root_CC_Options);
			}

			## Remove any existing WPFC htaccess code in the Root htaccess file.
			$rootHtaccess = ABSPATH . '.htaccess';			
			
			if ( file_exists($rootHtaccess) ) {
				$sapi_type = php_sapi_name();
				$permsRootHtaccess = substr(sprintf('%o', fileperms($rootHtaccess)), -4);
			
				if ( substr($sapi_type, 0, 6) != 'apache' || $permsRootHtaccess != '0666' || $permsRootHtaccess != '0777' ) {
					chmod( $rootHtaccess, 0644 );
				}			
			
				$root_htaccess_file_contents = file_get_contents($rootHtaccess);			
			
				if ( preg_match( '/#\sBEGIN(.*)WpFastestCache(.*)#\sEND(.*)WpFastestCache/s', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sBEGIN(.*)WpFastestCache(.*)#\sEND(.*)WpFastestCache/s', "", $root_htaccess_file_contents);
				}

				file_put_contents($rootHtaccess, $root_htaccess_file_contents);			
			
				$Root_Autolock = get_option('bulletproof_security_options_autolock');
				
				if ( isset($Root_Autolock['bps_root_htaccess_autolock']) && $Root_Autolock['bps_root_htaccess_autolock'] == 'On' ) {
					chmod($rootHtaccess, 0404);
				}
				
				$text = '<strong><font color="green">'.__('WP Fastest Cache (WPFC) Plugin AutoCleanup Successful: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-60"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('AutoCleanup has removed all WPFC htaccess code from BPS Custom Code and your Root htaccess file if it existed. If you have WPFC installed and are still planning on using WPFC then re-run the Setup Wizards after you have activated the WPFC plugin again and resaved your WPFC plugin settings again.', 'bulletproof-security').'</span></span></font></strong><br>';
				echo $text;	
			}
		}
	}	
}


// WP Rocket Setup & Cleanup: Creates the WP Rocket htaccess code in BPS Custom Code & wp-config.php & removes WP Rocket htaccess code from the Root htaccess file.
// WPR creates htaccess code in the Root htaccess file at the top of the Root htaccess file & code in the wp-config.php file on plugin activation.
// WPR removes htaccess code on plugin deactivation, but leaves whitespaces at the top of the root htaccess file and 
// compresses all other existing htaccess code in the Root htacces file so that all newlines are removed from all other htaccess code.
// Requires Prerequisite Manual Steps by User to generate WPR htaccess code: HUD message displayed to Unlock Root htaccess file, save WPR settings & run the Wizards.
// Unlock the Root htaccess file, get the WPR htaccess code and then remove any existing WPR htaccess code in the Root htaccess file.
// Notes: WPR version tested: 2.10.3 released June 2017. Writes htaccess code to the top of the root htaccess file every time and does not replace/overwrite old code.
// Note: htaccess code is created in the site root htaccess file for GWIOD site types.
// 3.1: Delete the WP Rocket plugin skip/bypass rule code.
function bpsPro_Pwizard_Autofix_WPR() {
	
	$AutoFix_Options = get_option('bulletproof_security_options_wizard_autofix');
	
	if ( $AutoFix_Options['bps_wizard_autofix'] == 'Off' ) {
		return;
	}

	$wpr_plugin = 'wp-rocket/wp-rocket.php';
	$wpr_plugin_active = in_array( $wpr_plugin, apply_filters('active_plugins', get_option('active_plugins')));

	// 1. CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE
	// 10. CUSTOM CODE PLUGIN/THEME SKIP/BYPASS RULES
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_cache = htmlspecialchars_decode( $CC_Options_root['bps_customcode_one'], ENT_QUOTES );
	$bps_customcode_two = htmlspecialchars_decode( $CC_Options_root['bps_customcode_two'], ENT_QUOTES );
	$bps_customcode_cache_array = array();
	$bps_customcode_two_array = array();
	$bps_customcode_cache_array[] = $bps_customcode_cache;
	$bps_customcode_two_array[] = $bps_customcode_two;
	$cc_cache_array = array();	
	$cc_two_array = array();

	if ( $wpr_plugin_active == 1 || is_plugin_active_for_network( $wpr_plugin ) ) {

		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}

		## GWIOD site type: AutoSetup is not required since htaccess code is written to the site root htaccess file.
		if ( $bpsSiteUrl != $bpsHomeUrl ) {
			$text = '<strong><font color="green">'.__('WP Rocket Plugin AutoSetup not required: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-20"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('GWIOD site types do not require AutoSetup because WP Rocket creates htaccess code in the site root htaccess file.', 'bulletproof-security').'</span></span></font></strong><br>';
			echo $text;	
			return;
		}

		## Remove any existing WPR htaccess code in Custom Code from the $cc_cache_array so that new WPR htaccess code is created each time.
		## Important Note: If dots are used (.*) then newlines and spaces are ignored when using the /s modifier.
		// Cleans up extra Newlines, Returns & whitespaces.
		foreach ( $bps_customcode_cache_array as $key => $value ) {
				
			if ( preg_match( '/#\sBEGIN\sWP\sRocket(.*)#\sEND\sWP\sRocket/s', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN\sWP\sRocket(.*)#\sEND\sWP\sRocket/s', "", $value);
			}

			if ( preg_match('/(\n\r){2,}/', $value, $matches) ) {	
				$value = preg_replace("/(\n\r){2,}/", "\n", $value);
			}				
				
			$cc_cache_array[] = trim( $value, " \t\n\r");
		}	
	
		$wpconfig = ABSPATH . 'wp-config.php';		
		
		if ( ! file_exists( $wpconfig ) ) {
				
			$text = '<strong><font color="#fb0101">'.__('Error: The Pre-Installation Wizard is unable to add the WP Rocket WP_CACHE code in your wp-config.php file.', 'bulletproof-security').'</font><br>'.__('A wp-config.php file was NOT found in your WordPress website root folder. If you have moved your wp-config.php file to another folder location then you will need to either move the wp-config.php file back to its default WordPress folder location and run the Pre-Installation Wizard again or manually edit your wp-config.php file and add the WP Rocket WP_CACHE code. Click this link for the steps to manually edit your wp-config.php file: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/manually-editing-the-wordpress-wp-config-php-file/" target="_blank" title="Link opens in a new Browser window">'.__('Manually Edit the WordPress wp-config.php file', 'bulletproof-security').'</a><br>'; 
			echo $text;
		}

		$rootHtaccess = ABSPATH . '.htaccess';
		
		if ( file_exists($rootHtaccess) ) {
			
			$sapi_type = php_sapi_name();
			$permsRootHtaccess = substr(sprintf('%o', fileperms($rootHtaccess)), -4);

			if ( file_exists( $wpconfig ) ) {
			
				$perms_wpconfig = substr(sprintf('%o', fileperms($wpconfig)), -4);
				
				if ( substr($sapi_type, 0, 6) != 'apache' || $perms_wpconfig != '0666' || $perms_wpconfig != '0777' ) { // Windows IIS, XAMPP, etc
					chmod( $wpconfig, 0644 );
				}
			}

			if ( substr($sapi_type, 0, 6) != 'apache' || $permsRootHtaccess != '0666' || $permsRootHtaccess != '0777' ) {
				chmod( $rootHtaccess, 0644 );
			}

			$root_htaccess_file_contents = file_get_contents($rootHtaccess);			

			$wpr_htaccess_code = array();

			## Remove the WP Rocket htaccess code from the Root htaccess file after putting any WPR code into an array and updating the CC DB options.
			if ( preg_match( '/#\sBEGIN\sWP\sRocket(.*)#\sEND\sWP\sRocket/s', $root_htaccess_file_contents, $matches ) ) {
				$wpr_htaccess_code[] = $matches[0];
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sWP\sRocket(.*)#\sEND\sWP\sRocket/s', "", $root_htaccess_file_contents);
			}

			$bps_customcode_cache_merge = array_merge($cc_cache_array, $wpr_htaccess_code);
			$cc_cache_unique = array_unique($bps_customcode_cache_merge);
 			// needs to be \n
 			$bps_customcode_cache_implode = implode( "\n", $cc_cache_unique );
			
			if ( ! is_multisite() ) {

				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);
				
			} else {
					
				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);					
			}

			foreach( $Root_CC_Options as $key => $value ) {
				update_option('bulletproof_security_options_customcode', $Root_CC_Options);
			}		

			## Add the define('WP_CACHE', true); code in the wp-config.php file if it does not exist
			if ( file_exists( $wpconfig ) ) {
				$wp_config_contents = file_get_contents($wpconfig);
			
				if ( ! preg_match( '/define(.*)\((.*)WP_CACHE(.*)(true|false)(.*)\);/', $wp_config_contents, $matches ) ) {
					$wp_config_contents = preg_replace( '/<\?php(.*\s*){1}/', '<?php'."\ndefine('WP_CACHE', true);\n", $wp_config_contents);
					file_put_contents($wpconfig, $wp_config_contents);
				}
			}			
			
			## Remove WP Rocket Cache htaccess code from the Root htaccess file
			if ( file_put_contents($rootHtaccess, $root_htaccess_file_contents) ) {	

				$Root_Autolock = get_option('bulletproof_security_options_autolock');
				
				if ( $Root_Autolock['bps_root_htaccess_autolock'] == 'On' ) {
					chmod($rootHtaccess, 0404);
				}
			}

			$text = '<strong><font color="green">'.__('WP Rocket Plugin AutoSetup Successful: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-20"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('Important Note: If you change any of your WP Rocket settings at any time, re-run the Setup Wizards again.', 'bulletproof-security').'</span></span></font></strong><br>';
			echo $text;
		}

	} else {
	
		## WP Rocket Cleanup: Either not installed or activated. Removes any/all WP Rocket htaccess code from BPS Custom Code and Root htaccess file.
		// 3.1: Remove the WP Rocket plugin skip/bypass code.
		if ( $wpr_plugin_active != 1 && ! is_plugin_active_for_network( $wpr_plugin ) ) { 

			## Remove any existing WP Rocket htaccess code in Custom Code from the $cc_cache_array.
			foreach ( $bps_customcode_cache_array as $key => $value ) {
				
				if ( preg_match( '/#\sBEGIN\sWP\sRocket(.*)#\sEND\sWP\sRocket/s', $value, $matches ) ) {
					$value = preg_replace( '/#\sBEGIN\sWP\sRocket(.*)#\sEND\sWP\sRocket/s', "", $value);
				}

				if ( preg_match('/(\n\r){2,}/', $value, $matches) ) {	
					$value = preg_replace("/(\n\r){2,}/", "\n", $value);
				}				
			
				$cc_cache_array[] = trim( $value, " \t\n\r");
			}
			
			$bps_customcode_cache_implode = implode( "\n\n", $cc_cache_array );

			## 13.6: Remove any existing WP Rocket htaccess code in Custom Code from the $cc_two_array.
			foreach ( $bps_customcode_two_array as $key => $value ) {
				
				if ( preg_match( '/#\sWP\sRocket\splugin\sskip\/bypass\srule(\s*){1}RewriteCond(.*)wp-rocket\/\s\[NC\](\s*){1}RewriteRule\s\.\s\-\s\[S=\d{1,2}\]/s', $value, $matches ) ) {
					$value = preg_replace( '/#\sWP\sRocket\splugin\sskip\/bypass\srule(\s*){1}RewriteCond(.*)wp-rocket\/\s\[NC\](\s*){1}RewriteRule\s\.\s\-\s\[S=\d{1,2}\]/s', "", $value);
				}

				if ( preg_match('/(\n\r){2,}/', $value, $matches) ) {	
					$value = preg_replace("/(\n\r){2,}/", "\n", $value);
				}				
			
				$cc_two_array[] = trim( $value, " \t\n\r");
			}
			
			$bps_customcode_two_implode = implode( "\n\n", $cc_two_array );

			if ( ! is_multisite() ) {

				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);
				
			} else {
					
				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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

			foreach ( $Root_CC_Options as $key => $value ) {
				update_option('bulletproof_security_options_customcode', $Root_CC_Options);
			}

			## Remove any existing WP Rocket htaccess code in the Root htaccess file.
			$rootHtaccess = ABSPATH . '.htaccess';			
			
			if ( file_exists($rootHtaccess) ) {
				$sapi_type = php_sapi_name();
				$permsRootHtaccess = substr(sprintf('%o', fileperms($rootHtaccess)), -4);
			
				if ( substr($sapi_type, 0, 6) != 'apache' || $permsRootHtaccess != '0666' || $permsRootHtaccess != '0777' ) {
					chmod( $rootHtaccess, 0644 );
				}			
			
				$root_htaccess_file_contents = file_get_contents($rootHtaccess);			
			
				if ( preg_match( '/#\sBEGIN\sWP\sRocket(.*)#\sEND\sWP\sRocket/s', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sWP\sRocket(.*)#\sEND\sWP\sRocket/s', "", $root_htaccess_file_contents);
				}

				if ( preg_match( '/#\sWP\sRocket\splugin\sskip\/bypass\srule(\s*){1}RewriteCond(.*)wp-rocket\/\s\[NC\](\s*){1}RewriteRule\s\.\s\-\s\[S=\d{1,2}\]/s', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sWP\sRocket\splugin\sskip\/bypass\srule(\s*){1}RewriteCond(.*)wp-rocket\/\s\[NC\](\s*){1}RewriteRule\s\.\s\-\s\[S=\d{1,2}\]/s', "", $root_htaccess_file_contents);
				}

				file_put_contents($rootHtaccess, $root_htaccess_file_contents);			
			
				$Root_Autolock = get_option('bulletproof_security_options_autolock');
				
				if ( isset($Root_Autolock['bps_root_htaccess_autolock']) && $Root_Autolock['bps_root_htaccess_autolock'] == 'On' ) {
					chmod($rootHtaccess, 0404);
				}
				
				$text = '<strong><font color="green">'.__('WP Rocket Plugin AutoCleanup Successful: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-60"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('AutoCleanup has removed all WP Rocket htaccess code from BPS Custom Code and your Root htaccess file if it existed. If you have WP Rocket installed and are still planning on using WP Rocket then re-run the Setup Wizards after you have activated the WP Rocket plugin again and resaved your WP Rocket plugin settings again.', 'bulletproof-security').'</span></span></font></strong><br>';
				echo $text;	
			}
		}
	}	
}

// LiteSpeed Cache Setup & Cleanup: Creates the LSCACHE htaccess code in BPS Custom Code & wp-config.php & removes LSCACHE htaccess code from the Root htaccess file.
// It is not possible to access or use LSCACHE functions - they are private class functions.
// LSCACHE creates htaccess code in the Root htaccess file at the top of the Root htaccess file & code in the wp-config.php file on plugin activation.
// LSCACHE removes htaccess code on plugin deactivation, but leaves its Markers at the top of the root htaccess file. see Markers below.
// The LSCACHE Markers need to be removed in the Setup Wizard so that the HUD AutoSetup check works correctly.
# BEGIN LSCACHE
# END LSCACHE
# BEGIN NON_LSCACHE
# END NON_LSCACHE
// Requires Prerequisite Manual Steps by User to generate LSCACHE htaccess code: HUD message displayed to Unlock Root htaccess file, save LSCACHE settings & run the Wizards.
// Unlock the Root htaccess file, get the LSCACHE htaccess code and then remove any existing LSCACHE htaccess code in the Root htaccess file.
// Note: htaccess code is created in the site root htaccess file for GWIOD site types.
function bpsPro_Pwizard_Autofix_LSCACHE() {
	
	$AutoFix_Options = get_option('bulletproof_security_options_wizard_autofix');
	
	if ( $AutoFix_Options['bps_wizard_autofix'] == 'Off' ) {
		return;
	}

	$lscache_plugin = 'litespeed-cache/litespeed-cache.php';
	$lscache_plugin_active = in_array( $lscache_plugin, apply_filters('active_plugins', get_option('active_plugins')));

	// 1. CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_cache = htmlspecialchars_decode( $CC_Options_root['bps_customcode_one'], ENT_QUOTES );
	$bps_customcode_cache_array = array();
	$bps_customcode_cache_array[] = $bps_customcode_cache;
	$cc_cache_array = array();	
	
	if ( $lscache_plugin_active == 1 || is_plugin_active_for_network( $lscache_plugin ) ) {

		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}

		## GWIOD site type: AutoSetup is not required since htaccess code is written to the site root htaccess file.
		if ( $bpsSiteUrl != $bpsHomeUrl ) {
			$text = '<strong><font color="green">'.__('LiteSpeed Cache Plugin AutoSetup not required: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-20"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('GWIOD site types do not require AutoSetup because LiteSpeed Cache creates htaccess code in the site root htaccess file.', 'bulletproof-security').'</span></span></font></strong><br>';
			echo $text;	
			return;
		}

		## Remove any existing LSCACHE htaccess code in Custom Code from the $cc_cache_array so that new LSCACHE htaccess code is created each time.
		## Important Note: If dots are used (.*) then newlines and spaces are ignored when using the /s modifier.
		// Cleans up extra Newlines, Returns & whitespaces.
		foreach ( $bps_customcode_cache_array as $key => $value ) {
				
			if ( preg_match( '/#\sBEGIN\sLSCACHE(.*)#\sEND\sNON_LSCACHE/s', $value, $matches ) ) {
				$value = preg_replace( '/#\sBEGIN\sLSCACHE(.*)#\sEND\sNON_LSCACHE/s', "", $value);
			}

			if ( preg_match('/(\n\r){2,}/', $value, $matches) ) {	
				$value = preg_replace("/(\n\r){2,}/", "\n", $value);
			}				
				
			$cc_cache_array[] = trim( $value, " \t\n\r");
		}	
	
		$wpconfig = ABSPATH . 'wp-config.php';		
		
		if ( ! file_exists( $wpconfig ) ) {
				
			$text = '<strong><font color="#fb0101">'.__('Error: The Pre-Installation Wizard is unable to add the LiteSpeed Cache WP_CACHE code in your wp-config.php file.', 'bulletproof-security').'</font><br>'.__('A wp-config.php file was NOT found in your WordPress website root folder. If you have moved your wp-config.php file to another folder location then you will need to either move the wp-config.php file back to its default WordPress folder location and run the Pre-Installation Wizard again or manually edit your wp-config.php file and add the LiteSpeed Cache WP_CACHE code. Click this link for the steps to manually edit your wp-config.php file: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/manually-editing-the-wordpress-wp-config-php-file/" target="_blank" title="Link opens in a new Browser window">'.__('Manually Edit the WordPress wp-config.php file', 'bulletproof-security').'</a><br>'; 
			echo $text;
		}

		$rootHtaccess = ABSPATH . '.htaccess';
		
		if ( file_exists($rootHtaccess) ) {
			
			$sapi_type = php_sapi_name();
			$permsRootHtaccess = substr(sprintf('%o', fileperms($rootHtaccess)), -4);

			if ( file_exists( $wpconfig ) ) {
			
				$perms_wpconfig = substr(sprintf('%o', fileperms($wpconfig)), -4);
				
				if ( substr($sapi_type, 0, 6) != 'apache' || $perms_wpconfig != '0666' || $perms_wpconfig != '0777' ) { // Windows IIS, XAMPP, etc
					chmod( $wpconfig, 0644 );
				}
			}

			if ( substr($sapi_type, 0, 6) != 'apache' || $permsRootHtaccess != '0666' || $permsRootHtaccess != '0777' ) {
				chmod( $rootHtaccess, 0644 );
			}

			$root_htaccess_file_contents = file_get_contents($rootHtaccess);			

			$lscache_htaccess_code = array();

			## Remove the WP Rocket htaccess code from the Root htaccess file after putting any WPR code into an array and updating the CC DB options.
			if ( preg_match( '/#\sBEGIN\sLSCACHE(.*)#\sEND\sNON_LSCACHE/s', $root_htaccess_file_contents, $matches ) ) {
				$lscache_htaccess_code[] = $matches[0];
				$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sLSCACHE(.*)#\sEND\sNON_LSCACHE/s', "", $root_htaccess_file_contents);
			}

			$bps_customcode_cache_merge = array_merge($cc_cache_array, $lscache_htaccess_code);
			$cc_cache_unique = array_unique($bps_customcode_cache_merge);
 			// needs to be \n
 			$bps_customcode_cache_implode = implode( "\n", $cc_cache_unique );
			
			if ( ! is_multisite() ) {

				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);
				
			} else {
					
				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);					
			}

			foreach( $Root_CC_Options as $key => $value ) {
				update_option('bulletproof_security_options_customcode', $Root_CC_Options);
			}		

			## Add the define('WP_CACHE', true); code in the wp-config.php file if it does not exist
			if ( file_exists( $wpconfig ) ) {
				$wp_config_contents = file_get_contents($wpconfig);
			
				if ( ! preg_match( '/define(.*)\((.*)WP_CACHE(.*)(true|false)(.*)\);/', $wp_config_contents, $matches ) ) {
					$wp_config_contents = preg_replace( '/<\?php(.*\s*){1}/', '<?php'."\ndefine('WP_CACHE', true);\n", $wp_config_contents);
					file_put_contents($wpconfig, $wp_config_contents);
				}
			}			
			
			## Remove LiteSpeed Cache htaccess code from the Root htaccess file
			if ( file_put_contents($rootHtaccess, $root_htaccess_file_contents) ) {	

				$Root_Autolock = get_option('bulletproof_security_options_autolock');
				
				if ( $Root_Autolock['bps_root_htaccess_autolock'] == 'On' ) {
					chmod($rootHtaccess, 0404);
				}
			}

			$text = '<strong><font color="green">'.__('LiteSpeed Cache Plugin AutoSetup Successful: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-20"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('Important Note: If you change any of your LiteSpeed Cache settings at any time, re-run the Setup Wizards again.', 'bulletproof-security').'</span></span></font></strong><br>';
			echo $text;
		}

	} else {
	
		## LSCACHE Cleanup: Either not installed or activated. Removes any/all LSCACHE htaccess code from BPS Custom Code and Root htaccess file.
		if ( $lscache_plugin_active != 1 && ! is_plugin_active_for_network( $lscache_plugin ) ) { 

			## Remove any existing LiteSpeed Cache htaccess code in Custom Code from the $cc_cache_array.
			foreach ( $bps_customcode_cache_array as $key => $value ) {
				
				if ( preg_match( '/#\sBEGIN\sLSCACHE(.*)#\sEND\sNON_LSCACHE/s', $value, $matches ) ) {
					$value = preg_replace( '/#\sBEGIN\sLSCACHE(.*)#\sEND\sNON_LSCACHE/s', "", $value);
				}

				if ( preg_match('/(\n\r){2,}/', $value, $matches) ) {	
					$value = preg_replace("/(\n\r){2,}/", "\n", $value);
				}				
			
				$cc_cache_array[] = trim( $value, " \t\n\r");
			}
			
			$bps_customcode_cache_implode = implode( "\n\n", $cc_cache_array );

			if ( ! is_multisite() ) {

				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);
				
			} else {
					
				$Root_CC_Options = array(
				'bps_customcode_one' 				=> $bps_customcode_cache_implode, 
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
				'bps_customcode_bpsqse' 			=> $CC_Options_root['bps_customcode_bpsqse'], 
				'bps_customcode_wp_rewrite_end' 	=> $CC_Options_root['bps_customcode_wp_rewrite_end'], 
				'bps_customcode_deny_files' 		=> $CC_Options_root['bps_customcode_deny_files'], 
				'bps_customcode_three' 				=> $CC_Options_root['bps_customcode_three'] 
				);					
			}

			foreach ( $Root_CC_Options as $key => $value ) {
				update_option('bulletproof_security_options_customcode', $Root_CC_Options);
			}

			## Remove any existing LiteSpeed Cache htaccess code in the Root htaccess file.
			$rootHtaccess = ABSPATH . '.htaccess';			
			
			if ( file_exists($rootHtaccess) ) {
				$sapi_type = php_sapi_name();
				$permsRootHtaccess = substr(sprintf('%o', fileperms($rootHtaccess)), -4);
			
				if ( substr($sapi_type, 0, 6) != 'apache' || $permsRootHtaccess != '0666' || $permsRootHtaccess != '0777' ) {
					chmod( $rootHtaccess, 0644 );
				}			
			
				$root_htaccess_file_contents = file_get_contents($rootHtaccess);			
			
				if ( preg_match( '/#\sBEGIN\sLSCACHE(.*)#\sEND\sNON_LSCACHE/s', $root_htaccess_file_contents, $matches ) ) {
					$root_htaccess_file_contents = preg_replace( '/#\sBEGIN\sLSCACHE(.*)#\sEND\sNON_LSCACHE/s', "", $root_htaccess_file_contents);
				}

				file_put_contents($rootHtaccess, $root_htaccess_file_contents);			
			
				$Root_Autolock = get_option('bulletproof_security_options_autolock');
				
				if ( isset($Root_Autolock['bps_root_htaccess_autolock']) && $Root_Autolock['bps_root_htaccess_autolock'] == 'On' ) {
					chmod($rootHtaccess, 0404);
				}
				
				$text = '<strong><font color="green">'.__('LiteSpeed Cache Plugin AutoCleanup Successful: ', 'bulletproof-security').'</font><font color="black"><span class="arq-tooltip-sw-60"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;right:1px;" /><span>'.__('AutoCleanup has removed all LiteSpeed Cache htaccess code from BPS Custom Code and your Root htaccess file if it existed. If you have LiteSpeed Cache installed and are still planning on using LiteSpeed Cache then re-run the Setup Wizards after you have activated the LiteSpeed Cache plugin again and resaved your LiteSpeed Cache plugin settings again.', 'bulletproof-security').'</span></span></font></strong><br>';
				echo $text;	
			}
		}
	}	
}
?>