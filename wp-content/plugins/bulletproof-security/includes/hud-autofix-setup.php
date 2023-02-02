<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! function_exists ('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

// S-Monitor Display HUD AutoFix Alerts in WP Dashboard Only if wpOn
function bps_HUD_autofix_setup_WP_Dashboard() {
	
	if ( current_user_can('manage_options') ) { 

		if ( substr( esc_html( $_SERVER['SERVER_SOFTWARE'] ), 0, 5 ) == 'nginx' ) {		
			return;
		}

		// 3.2: No longer offering autosetup for the EPC plugin.
		// bpsPro_EPC_plugin_check();
		$w3tc_plugin = 'w3-total-cache/w3-total-cache.php';
		// $wpsc_plugin = 'wp-super-cache/wp-cache.php';
		bpsPro_w3tc_htaccess_check($w3tc_plugin);
		// WPSC now requires manual setup. No longer doing autosetup for WPSC
		// bpsPro_wpsc_htaccess_check($wpsc_plugin);
		bpsPro_comet_cache_htaccess_check();
		bpsPro_wpfc_htaccess_check();
		bpsPro_wp_rocket_htaccess_check();
		bpsPro_litespeed_cache_htaccess_check();		
	}
}

add_action('admin_notices', 'bps_HUD_autofix_setup_WP_Dashboard');

// Heads Up Display w/ Dismiss Notice - Check if Endurance Page Cache must-use plugin is installed.
// 13: Additional conditions added: check if EPC is enabled and Cache level is 1,2,3,4.
// Note: Keep this Notice as a Dismiss Notice since EPC is a special case.
function bpsPro_EPC_plugin_check() {
	
	if ( ! get_option('bulletproof_security_options_wizard_free') ) {
		return;
	}

	if ( isset ( $_POST['Submit-Setup-Wizard'] ) && $_POST['Submit-Setup-Wizard'] == true ) {
		return;
	}

	$EPC_plugin_file = WP_CONTENT_DIR . '/mu-plugins/endurance-page-cache.php';
	$epc_options = get_option( 'mm_cache_settings' );	
	$epc_cache_level_options = get_option( 'endurance_cache_level' );

	if ( file_exists($EPC_plugin_file) && $epc_options['page'] == 'enabled' && $epc_cache_level_options['endurance_cache_level'] > 0 ) {

		global $current_user;
		$user_id = $current_user->ID;		
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}

		if ( $bpsSiteUrl == $bpsHomeUrl ) {

			if ( ! get_user_meta($user_id, 'bpsPro_ignore_EPC_plugin_notice')) { 
		
			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}
		
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS Notice: The Endurance Page Cache (EPC) must-use plugin is installed', 'bulletproof-security').'</font><br>'.__('The EPC must-use plugin has been automatically installed by your Web Host and requires these additional BPS setup steps to make sure everything is setup correctly:', 'bulletproof-security').'<br>'.__('Go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ).'" title="htaccess File Editor">'.__('BPS htaccess File Editor page', 'bulletproof-security').'</a>,'.__(' click the Unlock htaccess File button, go to the WordPress Settings > General page, scroll down to Endurance Cache settings,', 'bulletproof-security').'<br>'.__('click the Save Changes button, click this link: ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' and click the Setup Wizard button.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_EPC_plugin_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
			}
		}
	}
}

// 3.2: No longer offering autofix for the EPC plugin.
//add_action('admin_init', 'bpsPro_EPC_plugin_nag_ignore');

function bpsPro_EPC_plugin_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_EPC_plugin_nag_ignore']) && '0' == $_GET['bpsPro_EPC_plugin_nag_ignore'] ) {
		add_user_meta($user_id, 'bpsPro_ignore_EPC_plugin_notice', 'true', true);
	}
}

// Heads Up Display - Check if W3TC is active or not and check root htaccess file for W3TC htaccess code 
function bpsPro_w3tc_htaccess_check($w3tc_plugin) {
	
	if ( ! get_option('bulletproof_security_options_wizard_free') ) {
		return;
	}

	if ( isset ( $_POST['Submit-Setup-Wizard'] ) && $_POST['Submit-Setup-Wizard'] == true ) {
		return;
	}

	$w3tc_plugin = 'w3-total-cache/w3-total-cache.php';
    $w3tc_plugin_active = in_array( $w3tc_plugin, apply_filters('active_plugins', get_option('active_plugins')));
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_cache = htmlspecialchars_decode( $CC_Options_root['bps_customcode_one'], ENT_QUOTES );
	$pattern = '/W3TC/';

	if ( $w3tc_plugin_active == 1 || is_plugin_active_for_network( $w3tc_plugin ) ) {
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}

		$filename = ABSPATH . '.htaccess';

		if ( file_exists($filename) ) {

			$string = file_get_contents($filename);	

			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				
				if ( ! strpos( $string, "W3TC" ) ) {
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('W3 Total Cache (W3TC) htaccess code was not found in your Root htaccess file', 'bulletproof-security').'</font><br>'.__('If you have deactivated Root Folder BulletProof Mode temporarily then disregard this message. When you activate Root Folder BulletProof Mode again this message will go away automatically.', 'bulletproof-security').'<br>'.__('If you just installed W3 Total Cache then go to the W3TC plugin settings page, choose and save the W3TC plugin settings that you want to use and then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' to automatically setup/combine W3TC and BPS htaccess code together.', 'bulletproof-security').'</div>';
					echo $text;
				}
			
				## 4.4: New condition: New caching plugin installations need to check Custom Code for the caching plugin's Marker.
				// This covers cases where the root htaccess file is not locked, the htaccess code is written to the root htaccess file, but does not exist in Custom Code.
				if ( ! preg_match( $pattern, $bps_customcode_cache ) ) {
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('W3 Total Cache (W3TC) Plugin htaccess code was not found in BPS Custom Code', 'bulletproof-security').'</font><br>'.__('If you just installed W3 Total Cache then go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ).'" title="htaccess File Editor">'.__('BPS htaccess File Editor page', 'bulletproof-security').'</a>,'.__(' click the Unlock htaccess File button, then go to the W3 Total Cache plugin settings page, choose and save the W3 Total Cache plugin settings that you want to use and then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' to automatically setup/combine W3 Total Cache and BPS htaccess code together.', 'bulletproof-security').'<br>'.__('Note: If you change your W3 Total Cache Plugin settings at a later time then repeat these steps.', 'bulletproof-security').'</div>';
					echo $text;
				}			
			}
		}
		
	} elseif ( $w3tc_plugin_active != 1 && ! is_plugin_active_for_network( $w3tc_plugin )) {
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}

		$filename = ABSPATH . '.htaccess';

		if ( file_exists($filename) ) {

			$string = file_get_contents($filename);			
		
			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				
				if ( strpos( $string, "W3TC" ) ) {
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('W3 Total Cache (W3TC) is deactivated and W3TC htaccess code was found in your Root htaccess file', 'bulletproof-security').'</font><br>'.__('If you have deactivated W3TC temporarily then disregard this message.', 'bulletproof-security').'<br>'.__('If you are planning on permanently uninstalling W3TC then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' after you have uninstalled/deleted the W3TC plugin.', 'bulletproof-security').'</div>';
					echo $text;
				} 
			}
		}
	}
}

// Heads Up Display - Check if WPSC is active or not and check root htaccess file for WPSC htaccess code 
function bpsPro_wpsc_htaccess_check($wpsc_plugin) {
	
	if ( ! get_option('bulletproof_security_options_wizard_free') ) {
		return;
	}

	if ( isset ( $_POST['Submit-Setup-Wizard'] ) && $_POST['Submit-Setup-Wizard'] == true ) {
		return;
	}

	$wpsc_plugin = 'wp-super-cache/wp-cache.php';
    $wpsc_plugin_active = in_array( $wpsc_plugin, apply_filters('active_plugins', get_option('active_plugins')));
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_cache = htmlspecialchars_decode( $CC_Options_root['bps_customcode_one'], ENT_QUOTES );
	$pattern = '/WPSuperCache/';

	if ( $wpsc_plugin_active == 1 || is_plugin_active_for_network( $wpsc_plugin ) ) {
		global $cache_enabled, $super_cache_enabled, $wp_cache_mod_rewrite;		

		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}
		
		$filename = ABSPATH . '.htaccess';

		if ( file_exists($filename) ) {

			$string = file_get_contents($filename);		
		
			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				## WPSC Caching On & Use mod_rewrite to serve cache files option selected.
				if ( $cache_enabled == true && $super_cache_enabled && $wp_cache_mod_rewrite == 1 && ! strpos($string, "WPSuperCache" ) ) {
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WP Super Cache (WPSC) htaccess code was not found in your Root htaccess file', 'bulletproof-security').'</font><br>'.__('If you have deactivated Root Folder BulletProof Mode temporarily then disregard this message. When you activate Root Folder BulletProof Mode again this message will go away automatically.', 'bulletproof-security').'<br>'.__('If you just installed WP Super Cache then go to the WPSC plugin settings page, choose and save the WPSC plugin settings that you want to use and then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' to automatically setup/combine WPSC and BPS htaccess code together.', 'bulletproof-security').'</div>';
					echo $text;
				}
			
				## 4.4: New condition: New caching plugin installations need to check Custom Code for the caching plugin's Marker.
				// This covers cases where the root htaccess file is not locked, the htaccess code is written to the root htaccess file, but does not exist in Custom Code.
				if ( ! preg_match( $pattern, $bps_customcode_cache ) ) {
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WP Super Cache (WPSC) Plugin htaccess code was not found in BPS Custom Code', 'bulletproof-security').'</font><br>'.__('If you just installed WP Super Cache then go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ).'" title="htaccess File Editor">'.__('BPS htaccess File Editor page', 'bulletproof-security').'</a>,'.__(' click the Unlock htaccess File button, then go to the WP Super Cache plugin settings page, choose and save the WP Super Cache plugin settings that you want to use and then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' to automatically setup/combine WP Super Cache and BPS htaccess code together.', 'bulletproof-security').'<br>'.__('Note: If you change your WP Super Cache Plugin settings at a later time then repeat these steps.', 'bulletproof-security').'</div>';
					echo $text;
				}			
			}
		}
	
	} elseif ( $wpsc_plugin_active != 1 && ! is_plugin_active_for_network( $wpsc_plugin ) ) {
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}
		
		$filename = ABSPATH . '.htaccess';

		if ( file_exists($filename) ) {

			$string = file_get_contents($filename);				
		
			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				
				if ( strpos($string, "WPSuperCache" ) ) {	
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WP Super Cache (WPSC) is deactivated and WPSC htaccess code was found in your Root htaccess file', 'bulletproof-security').'</font><br>'.__('If you have deactivated WPSC temporarily then disregard this message.', 'bulletproof-security').'<br>'.__('If you are planning on permanently uninstalling WPSC then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' after you have uninstalled/deleted the WPSC plugin.', 'bulletproof-security').'</div>';
					echo $text;
				} 
			}
		}
	}
}

// Heads Up Display - Check if Comet Cache is active or not and check root htaccess file for Comet Cache htaccess code 
function bpsPro_comet_cache_htaccess_check() {
	
	if ( ! get_option('bulletproof_security_options_wizard_free') ) {
		return;
	}

	if ( isset ( $_POST['Submit-Setup-Wizard'] ) && $_POST['Submit-Setup-Wizard'] == true ) {
		return;
	}

	$comet_cache = 'comet-cache/comet-cache.php';
	$comet_cache_pro = 'comet-cache-pro/comet-cache-pro.php';
	$comet_cache_active = in_array( $comet_cache, apply_filters('active_plugins', get_option('active_plugins')));
	$comet_cache_pro_active = in_array( $comet_cache_pro, apply_filters('active_plugins', get_option('active_plugins')));
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_cache = htmlspecialchars_decode( $CC_Options_root['bps_customcode_one'], ENT_QUOTES );
	$pattern = '/Comet\sCache/';

	if ( $comet_cache_active == 1 || is_plugin_active_for_network( $comet_cache ) || $comet_cache_pro_active == 1 || is_plugin_active_for_network( $comet_cache_pro ) ) {
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}
		
		$filename = ABSPATH . '.htaccess';

		if ( file_exists($filename) ) {

			$string = file_get_contents($filename);		
		
			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				$comet_cache_options = get_option('comet_cache_options');
				
				if ( $comet_cache_options['htaccess_gzip_enable'] == '1' || $comet_cache_options['htaccess_access_control_allow_origin'] == '1' || $comet_cache_options['htaccess_browser_caching_enable'] == '1' || $comet_cache_options['htaccess_enforce_exact_host_name'] == '1' || $comet_cache_options['htaccess_enforce_canonical_urls'] == '1' ) {
					
					if ( ! strpos($string, "Comet Cache" ) ) { 
						$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('Comet Cache htaccess code was not found in your Root htaccess file', 'bulletproof-security').'</font><br>'.__('If you have deactivated Root Folder BulletProof Mode temporarily then disregard this message. When you activate Root Folder BulletProof Mode again this message will go away automatically.', 'bulletproof-security').'<br>'.__('If you just installed Comet Cache then go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ).'" title="htaccess File Editor">'.__('BPS htaccess File Editor page', 'bulletproof-security').'</a>,'.__(' click the Unlock htaccess File button, go to the Comet Cache plugin settings page, choose and save the Comet Cache plugin settings that you want to use and then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' to automatically setup/combine Comet Cache and BPS htaccess code together.', 'bulletproof-security').'</div>';
						echo $text;
					}
				}
			
				## 4.4: New condition: New caching plugin installations need to check Custom Code for the caching plugin's Marker.
				// This covers cases where the root htaccess file is not locked, the htaccess code is written to the root htaccess file, but does not exist in Custom Code.
				if ( ! preg_match( $pattern, $bps_customcode_cache ) ) {
						$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('Comet Cache Plugin htaccess code was not found in BPS Custom Code', 'bulletproof-security').'</font><br>'.__('If you just installed Comet Cache then go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ).'" title="htaccess File Editor">'.__('BPS htaccess File Editor page', 'bulletproof-security').'</a>,'.__(' click the Unlock htaccess File button, then go to the Comet Cache plugin settings page, choose and save the Comet Cache plugin settings that you want to use and then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' to automatically setup/combine Comet Cache and BPS htaccess code together.', 'bulletproof-security').'<br>'.__('Note: If you change your Comet Cache Plugin settings at a later time then repeat these steps.', 'bulletproof-security').'</div>';
						echo $text;
				}			
			}
		}
	
	} elseif ( $comet_cache_active != 1 && $comet_cache_pro_active != 1 && ! is_plugin_active_for_network( $comet_cache ) && ! is_plugin_active_for_network( $comet_cache_pro ) ) {
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}
		
		$filename = ABSPATH . '.htaccess';

		if ( file_exists($filename) ) {

			$string = file_get_contents($filename);				
		
			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				
				if ( strpos($string, "Comet Cache" ) ) {	
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('Comet Cache is deactivated and Comet Cache htaccess code was found in your Root htaccess file', 'bulletproof-security').'</font><br>'.__('If you have deactivated Comet Cache temporarily then disregard this message.', 'bulletproof-security').'<br>'.__('If you are planning on permanently uninstalling Comet Cache then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' after you have uninstalled/deleted the Comet Cache plugin.', 'bulletproof-security').'</div>';
					echo $text;
				} 
			}
		}
	}
}

// Heads Up Display - Check if WPFC is active or not and check root htaccess file for WPFC htaccess code 
// BPS 5.1: Commented out the WPFC option checking code. Things have changed in WPFC. So no longer use the WPFC option checking code.
// Note: On WPFC plugin deactivation the htaccess code is removed from the root htaccess file.
// The WPFC plugin deactivated condition will only fire if someone activates Root BPM if they still have WPFC htaccess code in CC.
function bpsPro_wpfc_htaccess_check() {
	
	if ( ! get_option('bulletproof_security_options_wizard_free') ) {
		return;
	}

	if ( isset ( $_POST['Submit-Setup-Wizard'] ) && $_POST['Submit-Setup-Wizard'] == true ) {
		return;
	}

	$wpfc_plugin = 'wp-fastest-cache/wpFastestCache.php';
	$wpfc_plugin_active = in_array( $wpfc_plugin, apply_filters('active_plugins', get_option('active_plugins')));
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_cache = htmlspecialchars_decode( $CC_Options_root['bps_customcode_one'], ENT_QUOTES );
	$pattern = '/WpFastestCache/';

	if ( $wpfc_plugin_active == 1 || is_plugin_active_for_network( $wpfc_plugin ) ) {
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}
		
		$filename = ABSPATH . '.htaccess';

		if ( file_exists($filename) ) {

			$string = file_get_contents($filename);		
		
			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				//$wpfc_options = get_option('WpFastestCache');
				
				// If someone has not chosen any WPFC htaccess code options then just return here.
				if ( ! strpos($string, "WpFastestCache" ) && ! preg_match( $pattern, $bps_customcode_cache ) ) {
					return;
				}

				if ( /*$wpfc_options['wpFastestCacheStatus'] == 'on' && */ ! strpos($string, "WpFastestCache" ) ) {
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WP Fastest Cache (WPFC) htaccess code was not found in your Root htaccess file', 'bulletproof-security').'</font><br>'.__('If you have deactivated Root Folder BulletProof Mode temporarily then disregard this message. When you activate Root Folder BulletProof Mode again this message will go away automatically.', 'bulletproof-security').'<br>'.__('If you just installed WP Fastest Cache then go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ).'" title="htaccess File Editor">'.__('BPS htaccess File Editor page', 'bulletproof-security').'</a>,'.__(' click the Unlock htaccess File button, then go to the WPFC plugin settings page, choose and save the WPFC plugin settings that you want to use and then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' to automatically setup/combine WPFC and BPS htaccess code together.', 'bulletproof-security').'</div>';
					echo $text;
				}
				
				## 4.4: New condition: New caching plugin installations need to check Custom Code for the caching plugin's Marker.
				// This covers cases where the root htaccess file is not locked, the htaccess code is written to the root htaccess file, but does not exist in Custom Code.
				if ( ! preg_match( $pattern, $bps_customcode_cache ) ) {
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WP Fastest Cache (WPFC) Plugin htaccess code was not found in BPS Custom Code', 'bulletproof-security').'</font><br>'.__('If you just installed WP Fastest Cache then go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ).'" title="htaccess File Editor">'.__('BPS htaccess File Editor page', 'bulletproof-security').'</a>,'.__(' click the Unlock htaccess File button, then go to the WP Fastest Cache plugin settings page, choose and save the WP Fastest Cache plugin settings that you want to use and then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' to automatically setup/combine WP Fastest Cache and BPS htaccess code together.', 'bulletproof-security').'<br>'.__('Note: If you change your WP Fastest Cache Plugin settings at a later time then repeat these steps.', 'bulletproof-security').'</div>';
					echo $text;
				}			
			}
		}
	
	} elseif ( $wpfc_plugin_active != 1 && ! is_plugin_active_for_network( $wpfc_plugin ) ) {
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}
		
		$filename = ABSPATH . '.htaccess';

		if ( file_exists($filename) ) {

			$string = file_get_contents($filename);				
		
			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				
				if ( strpos($string, "WpFastestCache" ) ) {	
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WP Fastest Cache (WPFC) is deactivated and WPFC htaccess code was found in your Root htaccess file', 'bulletproof-security').'</font><br>'.__('If you have deactivated WPFC temporarily then disregard this message.', 'bulletproof-security').'<br>'.__('If you are planning on permanently uninstalling WPFC then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' after you have uninstalled/deleted the WPFC plugin.', 'bulletproof-security').'</div>';
					echo $text;
				} 
			}
		}
	}
}

// Heads Up Display - Check if WP Rocket is active or not and check root htaccess file for WP Rocket htaccess code 
function bpsPro_wp_rocket_htaccess_check() {
	
	if ( ! get_option('bulletproof_security_options_wizard_free') ) {
		return;
	}

	if ( isset ( $_POST['Submit-Setup-Wizard'] ) && $_POST['Submit-Setup-Wizard'] == true ) {
		return;
	}

	$wpr_plugin = 'wp-rocket/wp-rocket.php';
	$wpr_plugin_active = in_array( $wpr_plugin, apply_filters('active_plugins', get_option('active_plugins')));
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_cache = htmlspecialchars_decode( $CC_Options_root['bps_customcode_one'], ENT_QUOTES );
	$pattern = '/WP\sRocket/';

	if ( $wpr_plugin_active == 1 || is_plugin_active_for_network( $wpr_plugin ) ) {
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}
		
		$filename = ABSPATH . '.htaccess';

		if ( file_exists($filename) ) {

			$string = file_get_contents($filename);		
		
			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				
				if ( ! strpos($string, "WP Rocket" ) ) {
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WP Rocket htaccess code was not found in your Root htaccess file', 'bulletproof-security').'</font><br>'.__('If you have deactivated Root Folder BulletProof Mode temporarily then disregard this message. When you activate Root Folder BulletProof Mode again this message will go away automatically.', 'bulletproof-security').'<br>'.__('If you just installed WP Rocket then go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ).'" title="htaccess File Editor">'.__('BPS htaccess File Editor page', 'bulletproof-security').'</a>,'.__(' click the Unlock htaccess File button, then go to the WP Rocket plugin settings page, choose and save the WP Rocket plugin settings that you want to use and then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' to automatically setup/combine WP Rocket and BPS htaccess code together.', 'bulletproof-security').'</div>';
					echo $text;
				}
			
				## 4.4: New condition: New caching plugin installations need to check Custom Code for the caching plugin's Marker.
				// This covers cases where the root htaccess file is not locked, the htaccess code is written to the root htaccess file, but does not exist in Custom Code.
				if ( ! preg_match( $pattern, $bps_customcode_cache ) ) {
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WP Rocket Plugin htaccess code was not found in BPS Custom Code', 'bulletproof-security').'</font><br>'.__('If you just installed WP Rocket then go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ).'" title="htaccess File Editor">'.__('BPS htaccess File Editor page', 'bulletproof-security').'</a>,'.__(' click the Unlock htaccess File button, then go to the WP Rocket plugin settings page, choose and save the WP Rocket plugin settings that you want to use and then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' to automatically setup/combine WP Rocket and BPS htaccess code together.', 'bulletproof-security').'<br>'.__('Note: If you change your WP Rocket Plugin settings at a later time then repeat these steps.', 'bulletproof-security').'</div>';
					echo $text;			
				}			
			}
		}
	
	} elseif ( $wpr_plugin_active != 1 && ! is_plugin_active_for_network( $wpr_plugin ) ) {
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}
		
		$filename = ABSPATH . '.htaccess';

		if ( file_exists($filename) ) {

			$string = file_get_contents($filename);				
		
			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				
				if ( strpos($string, "WP Rocket" ) ) {	
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WP Rocket is deactivated and WP Rocket htaccess code was found in your Root htaccess file', 'bulletproof-security').'</font><br>'.__('If you have deactivated WP Rocket temporarily then disregard this message.', 'bulletproof-security').'<br>'.__('If you are planning on permanently uninstalling WP Rocket then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' after you have uninstalled/deleted the WP Rocket plugin.', 'bulletproof-security').'</div>';
					echo $text;
				} 
			}
		}
	}
}

// Heads Up Display - Check if LiteSpeed Cache is active or not and check root htaccess file for LiteSpeed Cache htaccess code.
function bpsPro_litespeed_cache_htaccess_check() {
	
	if ( ! get_option('bulletproof_security_options_wizard_free') ) {
		return;
	}

	if ( isset ( $_POST['Submit-Setup-Wizard'] ) && $_POST['Submit-Setup-Wizard'] == true ) {
		return;
	}

	$lscache_plugin = 'litespeed-cache/litespeed-cache.php';
	$lscache_plugin_active = in_array( $lscache_plugin, apply_filters('active_plugins', get_option('active_plugins')));
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$bps_customcode_cache = htmlspecialchars_decode( $CC_Options_root['bps_customcode_one'], ENT_QUOTES );
	$pattern = '/LSCACHE/';

	if ( $lscache_plugin_active == 1 || is_plugin_active_for_network( $lscache_plugin ) ) {
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}
		
		$filename = ABSPATH . '.htaccess';

		if ( file_exists($filename) ) {

			$string = file_get_contents($filename);		
		
			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				
				if ( ! strpos($string, "LSCACHE" ) ) {
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('LiteSpeed Cache Plugin htaccess code was not found in your Root htaccess file', 'bulletproof-security').'</font><br>'.__('If you have deactivated Root Folder BulletProof Mode temporarily then disregard this message. When you activate Root Folder BulletProof Mode again this message will go away automatically.', 'bulletproof-security').'<br>'.__('If you just installed LiteSpeed Cache then go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ).'" title="htaccess File Editor">'.__('BPS htaccess File Editor page', 'bulletproof-security').'</a>,'.__(' click the Unlock htaccess File button, then go to the LiteSpeed Cache plugin settings page, choose and save the LiteSpeed Cache plugin settings that you want to use and then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' to automatically setup/combine LiteSpeed Cache and BPS htaccess code together.', 'bulletproof-security').'</div>';
					echo $text;
				}
			
				## 4.4: New condition: New caching plugin installations need to check Custom Code for the caching plugin's Marker.
				// This covers cases where the root htaccess file is not locked, the htaccess code is written to the root htaccess file, but does not exist in Custom Code.
				if ( ! preg_match( $pattern, $bps_customcode_cache ) ) {
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('LiteSpeed Cache Plugin htaccess code was not found in BPS Custom Code', 'bulletproof-security').'</font><br>'.__('If you just installed LiteSpeed Cache then go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ).'" title="htaccess File Editor">'.__('BPS htaccess File Editor page', 'bulletproof-security').'</a>,'.__(' click the Unlock htaccess File button, then go to the LiteSpeed Cache plugin settings page, choose and save the LiteSpeed Cache plugin settings that you want to use and then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' to automatically setup/combine LiteSpeed Cache and BPS htaccess code together.', 'bulletproof-security').'<br>'.__('Note: If you change your LiteSpeed Cache Plugin settings at a later time then repeat these steps.', 'bulletproof-security').'</div>';
					echo $text;
				}
			}
		}
	
	} elseif ( $lscache_plugin_active != 1 && ! is_plugin_active_for_network( $lscache_plugin ) ) {
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}
		
		$filename = ABSPATH . '.htaccess';

		if ( file_exists($filename) ) {

			$string = file_get_contents($filename);				
		
			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				
				if ( strpos($string, "LSCACHE" ) ) {	

					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('LiteSpeed Cache Plugin is deactivated and LiteSpeed Cache htaccess code was found in your Root htaccess file', 'bulletproof-security').'</font><br>'.__('If you have deactivated LiteSpeed Cache temporarily then disregard this message.', 'bulletproof-security').'<br>'.__('If you are planning on permanently uninstalling LiteSpeed Cache then run the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard">'.__('BPS Setup Wizard', 'bulletproof-security').'</a>'.__(' after you have uninstalled/deleted the LiteSpeed Cache plugin.', 'bulletproof-security').'</div>';
					echo $text;

				} 
			}
		}
	}
}
?>