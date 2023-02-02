<?php
// Direct calls to this file are Forbidden when core files are not present
if ( ! function_exists ('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

// HUD Alerts in WP Dashboard
// Reset|Recheck Dismiss Notices is in core-forms.php
## 3.9: Commented out the Bonus Custom Code Dismiss Notice function and ModSecurity Check function.
function bps_HUD_WP_Dashboard() {
	
	if ( preg_match( '/page=stories-dashboard/', esc_html($_SERVER['QUERY_STRING']) ) || preg_match( '/page=backwpupbackups/', esc_html( $_SERVER['QUERY_STRING'] ) ) || preg_match( '/post_type=ai1ec_event/', esc_html( $_SERVER['QUERY_STRING'] ) ) ) {
		return;
	}

	if ( current_user_can('manage_options') ) { 
		bpsPro_hud_bpspro_sale();
		bps_check_php_version_error();
		bps_check_safemode();
		bps_check_permalinks_error();
		bps_check_iis_supports_permalinks();
		bps_hud_check_bpsbackup();
		//bpsPro_bonus_custom_code_dismiss_notices();
		bps_hud_PhpiniHandlerCheck();
		//bps_hud_check_sucuri();
		bps_hud_check_wordpress_firewall2();
		bps_hud_BPSQSE_old_code_check();
		bpsPro_BBM_htaccess_check();
		bpsPro_hud_speed_boost_cache_code();
		//bps_hud_check_autoupdate();
		//bpsPro_hud_mscan_notice();
		bpsPro_hud_jtc_lite_notice();
		bpsPro_hud_rate_notice();
		//bpsPro_hud_mod_security_check();
		bpsPro_hud_gdpr_compliance();
		//bps_hud_check_public_username();
		bpsPro_mu_wp_automatic_updates_notice();
		bpsPro_hud_new_feature_notice();
		bpsPro_hud_owner_uid_check_notice();
		bpsPro_wpcontent_htaccess_file_fix();
	}
}
add_action('admin_notices', 'bps_HUD_WP_Dashboard');

// Heads Up Display - Check PHP version - top error message new activations/installations
function bps_check_php_version_error() {
	
	if ( version_compare( PHP_VERSION, '5.0.0', '>=' ) ) {
		return;
	}
	
	if ( version_compare( PHP_VERSION, '5.0.0', '<' ) ) {
		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS requires at least PHP5 to function correctly. Your PHP version is: ', 'bulletproof-security').PHP_VERSION.'</font><br><a href="https://www.ait-pro.com/aitpro-blog/1166/bulletproof-security-plugin-support/bulletproof-security-plugin-guide-bps-version-45#bulletproof-security-issues-problems" target="_blank">'.__('BPS Guide - PHP5 Solution', 'bulletproof-security').'</a><br>'.__('The BPS Guide will open in a new browser window. You will not be directed away from your WordPress Dashboard.', 'bulletproof-security').'</div>';
		echo $text;
	}
}

// Heads Up Display w/ Dismiss - Check if PHP Safe Mode is On - 1 is On - 0 is Off
function bps_check_safemode() {
	
	if ( ini_get('safe_mode') == 1 ) {
		
		global $current_user;
		$user_id = $current_user->ID;
		
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}		
		
		if ( ! get_user_meta($user_id, 'bps_ignore_safemode_notice') ) { 
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS has detected that Safe Mode is set to On in your php.ini file.', 'bulletproof-security').'</font><br>'.__('If you see errors that BPS was unable to automatically create the backup folders this is probably the reason why.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_safemode_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bps_safemode_nag_ignore');

function bps_safemode_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_safemode_nag_ignore']) && '0' == $_GET['bps_safemode_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_safemode_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - Check if Permalinks are enabled - top error message new activations/installations
function bps_check_permalinks_error() {

	if ( current_user_can('manage_options') && get_option('permalink_structure') == '' ) {

		global $current_user;
		$user_id = $current_user->ID;
		
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}	
	
		if ( ! get_user_meta($user_id, 'bps_ignore_Permalinks_notice') ) { 
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: Custom Permalinks are NOT being used.', 'bulletproof-security').'</font><br>'.__('It is recommended that you use Custom Permalinks: ', 'bulletproof-security').'<a href="https://www.ait-pro.com/aitpro-blog/2304/wordpress-tips-tricks-fixes/permalinks-wordpress-custom-permalinks-wordpress-best-wordpress-permalinks-structure/" target="_blank" title="Link opens in a new Browser window">'.__('How to setup Custom Permalinks', 'bulletproof-security').'</a><br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_Permalinks_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;		
		}
	}
}

add_action('admin_init', 'bps_Permalinks_nag_ignore');

function bps_Permalinks_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_Permalinks_nag_ignore']) && '0' == $_GET['bps_Permalinks_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_Permalinks_notice', 'true', true);
	}
}

// Heads Up Display w/Dismiss - Check if Windows IIS server and if IIS7 supports permalink rewriting
function bps_check_iis_supports_permalinks() {
global $wp_rewrite, $is_IIS, $is_iis7, $current_user;
$user_id = $current_user->ID;	

	if ( current_user_can('manage_options') && $is_IIS && ! iis7_supports_permalinks() ) {
		
		if ( ! get_user_meta($user_id, 'bps_ignore_iis_notice')) {
	
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}
	
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS has detected that your Server is a Windows IIS Server that does not support htaccess rewriting.', 'bulletproof-security').'</font><br>'.__('Do NOT activate BulletProof Modes unless you know what you are doing.', 'bulletproof-security').'<br>'.__('Your Server Type is: ', 'bulletproof-security').esc_html( $_SERVER['SERVER_SOFTWARE'] ).'<br><a href="http://codex.wordpress.org/Using_Permalinks" target="_blank" title="This link will open in a new browser window.">'.__('WordPress Codex - Using Permalinks - see IIS section', 'bulletproof-security').'</a><br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_iis_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';		
			echo $text;
		}
	}
}

add_action('admin_init', 'bps_iis_nag_ignore');

function bps_iis_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_iis_nag_ignore'] ) && '0' == $_GET['bps_iis_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_iis_notice', 'true', true);
	}
}

// Heads Up Display - check if /bps-backup and /bps-backup/master-backups folders exist
function bps_hud_check_bpsbackup() {

	$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );	

	if ( ! is_dir( WP_CONTENT_DIR . '/bps-backup' ) ) {
		$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:0px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS was unable to automatically create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup folder.', 'bulletproof-security').'</font><br>'.__('You will need to create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup folder manually via FTP. The folder permissions for the bps-backup folder need to be set to 755 in order to successfully perform permanent online backups.', 'bulletproof-security').'<br>'.__('To remove this message permanently click ', 'bulletproof-security').'<a href="https://www.ait-pro.com/aitpro-blog/2566/bulletproof-security-plugin-support/bulletproof-security-error-messages" target="_blank">'.__('here.', 'bulletproof-security').'</a></div>';
		echo $text;
	}
	
	if ( ! is_dir( WP_CONTENT_DIR . '/bps-backup/master-backups' ) ) {
		$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:0px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS was unable to automatically create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup/master-backups folder.', 'bulletproof-security').'</font><br>'.__('You will need to create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup/master-backups folder manually via FTP. The folder permissions for the master-backups folder need to be set to 755 in order to successfully perform permanent online backups.', 'bulletproof-security').'<br>'.__('To remove this message permanently click ', 'bulletproof-security').'<a href="https://www.ait-pro.com/aitpro-blog/2566/bulletproof-security-plugin-support/bulletproof-security-error-messages" target="_blank">'.__('here.', 'bulletproof-security').'</a></div>';
		echo $text;
	}
}

// Heads Up Display - Bonus Custom Code with Dismiss Notices
function bpsPro_bonus_custom_code_dismiss_notices() {
global $current_user;
$user_id = $current_user->ID;	
	
	if ( current_user_can('manage_options') ) { 
		$text = '';
	
		// Setup Wizard DB option is saved by running the Setup Wizard, on BPS Upgrades & manual BPS setup
		if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
			return;
		}
	
		$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
	
		if ( isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			return;
		}
	
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}
			
		if ( get_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice') && ! get_user_meta($user_id, 'bps_post_request_attack_notice') ) {
	
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Bonus Custom Code:', 'bulletproof-security').'</font><br>'.__('Click the links below to get Bonus Custom Code or click the Dismiss Notice links or click this ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_bonus_code_dismiss_all_nag_ignore=0&bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss All Notices', 'bulletproof-security').'</a></span>'.__(' link. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br>';
	
	
			$text .= '<div id="BC5" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/post-request-protection-post-attack-protection-post-request-blocker/" title="Protects against POST Request Attacks" target="_blank">'.__('POST Request Attack Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
			echo $text;
			echo '</div>';
		}		
		
		if ( ! get_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice') ) {
	
			if ( ! get_user_meta($user_id, 'bps_brute_force_login_protection_notice') || ! get_user_meta($user_id, 'bps_speed_boost_cache_notice') || ! get_user_meta($user_id, 'bps_author_enumeration_notice') || ! get_user_meta($user_id, 'bps_xmlrpc_ddos_notice') || ! get_user_meta($user_id, 'bps_post_request_attack_notice') || ! get_user_meta($user_id, 'bps_sniff_driveby_notice') || ! get_user_meta($user_id, 'bps_iframe_clickjack_notice') ) { 		
				
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Bonus Custom Code:', 'bulletproof-security').'</font><br>'.__('Click the links below to get Bonus Custom Code or click the Dismiss Notice links or click this ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_bonus_code_dismiss_all_nag_ignore=0&bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss All Notices', 'bulletproof-security').'</a></span>'.__(' link. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br>';
				
			}
		
			if ( ! get_user_meta($user_id, 'bps_brute_force_login_protection_notice') ) { 	
				
				$text .= '<div id="BC1" style="">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/protect-login-page-from-brute-force-login-attacks/" title="Additional Protection for the Login Page from Brute Force Login Attacks" target="_blank">'.__('Brute Force Login Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_brute_force_login_protection_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
				
			}
				
			if ( ! get_user_meta($user_id, 'bps_speed_boost_cache_notice') ) { 	
		
				$text .= '<div id="BC2" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-caching-code-speed-boost-cache-code/" title="Speed up your website performance with Browser Cache code" target="_blank">'.__('Speed Boost Cache Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_speed_boost_cache_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
				
			}
				
			if ( ! get_user_meta($user_id, 'bps_author_enumeration_notice') ) { 
		
				$text .= '<div id="BC3" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/wordpress-author-enumeration-bot-probe-protection-author-id-user-id/" title="Protects against hacker and spammer bots finding Author names & User names on your website" target="_blank">'.__('Author Enumeration BOT Probe Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_author_enumeration_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
				
			}
				
			if ( ! get_user_meta($user_id, 'bps_xmlrpc_ddos_notice') ) { 		
		
				$text .= '<div id="BC4" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/wordpress-xml-rpc-ddos-protection-protect-xmlrpc-php-block-xmlrpc-php-forbid-xmlrpc-php/" title="Protects against the XML Quadratic Blowup Attack, DDoS Attacks as well as other various XML-RPC exploits" target="_blank">'.__('XML-RPC DDoS Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_xmlrpc_ddos_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
				
			}
			
			/*
			if ( ! get_user_meta($user_id, 'bps_referer_spam_notice') ) {
		
				$text .= '<div id="BC5" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/block-referer-spammers-semalt-kambasoft-ranksonic-buttons-for-website/" title="Protects against Referer Spamming and Phishing" target="_blank">'.__('Referer Spam|Phishing Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_referer_spam_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
				
			}
			*/
			
			if ( ! get_user_meta($user_id, 'bps_post_request_attack_notice') ) {
		
				$text .= '<div id="BC5" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/post-request-protection-post-attack-protection-post-request-blocker/" title="Protects against POST Request Attacks" target="_blank">'.__('POST Request Attack Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
				
			}
		
			if ( ! get_user_meta($user_id, 'bps_sniff_driveby_notice') ) {		
				
				$text .= '<div id="BC6" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/mime-sniffing-data-sniffing-content-sniffing-drive-by-download-attack-protection/" title="Protects against Mime Sniffing, Data Sniffing, Content Sniffing and Drive-by Download Attacks" target="_blank">'.__('Mime Sniffing|Drive-by Download Attack Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_sniff_driveby_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
			}
		
			if ( ! get_user_meta($user_id, 'bps_iframe_clickjack_notice') ) {		
				
				$text .= '<div id="BC7" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/rssing-com-good-or-bad/" title="Protects against external websites displaying your website pages or Feeds in iFrames and Clickjacking Protection" target="_blank">'.__('External iFrame|Clickjacking Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_iframe_clickjack_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
			}
	
			echo $text;
			
			if ( ! get_user_meta($user_id, 'bps_brute_force_login_protection_notice') || ! get_user_meta($user_id, 'bps_speed_boost_cache_notice') || ! get_user_meta($user_id, 'bps_author_enumeration_notice') || ! get_user_meta($user_id, 'bps_xmlrpc_ddos_notice') || ! get_user_meta($user_id, 'bps_post_request_attack_notice') || ! get_user_meta($user_id, 'bps_sniff_driveby_notice') || ! get_user_meta($user_id, 'bps_iframe_clickjack_notice') ) { 	
			echo '</div>';
			}
		}
	}
}

add_action('admin_init', 'bpsPro_bonus_custom_code_nag_ignores');

function bpsPro_bonus_custom_code_nag_ignores() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_bonus_code_dismiss_all_nag_ignore']) && '0' == $_GET['bps_bonus_code_dismiss_all_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice', 'true', true);
	}

	if ( isset($_GET['bps_brute_force_login_protection_nag_ignore']) && '0' == $_GET['bps_brute_force_login_protection_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_brute_force_login_protection_notice', 'true', true);
	}

	if ( isset($_GET['bps_speed_boost_cache_nag_ignore']) && '0' == $_GET['bps_speed_boost_cache_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_speed_boost_cache_notice', 'true', true);
	}

	if ( isset($_GET['bps_author_enumeration_nag_ignore']) && '0' == $_GET['bps_author_enumeration_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_author_enumeration_notice', 'true', true);
	}

	if ( isset($_GET['bps_xmlrpc_ddos_nag_ignore']) && '0' == $_GET['bps_xmlrpc_ddos_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_xmlrpc_ddos_notice', 'true', true);
	}

	/*
	if ( isset($_GET['bps_referer_spam_nag_ignore']) && '0' == $_GET['bps_referer_spam_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_referer_spam_notice', 'true', true);
	}
	*/
	
	if ( isset($_GET['bps_post_request_attack_nag_ignore']) && '0' == $_GET['bps_post_request_attack_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_post_request_attack_notice', 'true', true);
	}

	if ( isset($_GET['bps_sniff_driveby_nag_ignore']) && '0' == $_GET['bps_sniff_driveby_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_sniff_driveby_notice', 'true', true);
	}

	if ( isset($_GET['bps_iframe_clickjack_nag_ignore']) && '0' == $_GET['bps_iframe_clickjack_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_iframe_clickjack_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - Check if php.ini handler code exists in root .htaccess file, but not in Custom Code
// .53.6: Additional conditional check added for Wordfence WAF Firewall mess.
function bps_hud_PhpiniHandlerCheck() {
	global $current_user;
	$user_id = $current_user->ID;
	$file = ABSPATH . '.htaccess';
	$pre_background_image_url = site_url( '/wp-content/plugins/bulletproof-security/admin/images/pre_bg.png' );

	if ( esc_html($_SERVER['QUERY_STRING']) == 'page=bulletproof-security/admin/wizard/wizard.php' && ! get_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice') ) {
	
		if ( file_exists($file) ) {		

			$file_contents = file_get_contents($file);
			$CustomCodeoptions = get_option('bulletproof_security_options_customcode');
			$bps_customcode_one = ! isset($CustomCodeoptions['bps_customcode_one']) ? '' : $CustomCodeoptions['bps_customcode_one'];
			
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $file_contents, $matches);
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $bps_customcode_one, $DBmatches);

			if ( $matches[0] && ! $DBmatches[0] ) {
			
				preg_match_all('/(([#\s]{1,}|)(AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application).*\s*){1,}/', $file_contents, $h_matches );
	
				if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
					$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
				} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
					$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
				} else {
					$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
				}			
				
				if ( stripos( $file_contents, "Wordfence WAF" ) ) {
	
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: Wordfence PHP/php.ini handler htaccess code detected', 'bulletproof-security').'</font><br>'.__('Wordfence PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/wordfence-firewall-wp-contentwflogsconfig-php-file-quarantined/#wordfence-php-handler" target="_blank" title="Wordfence PHP Handler Fix">'.__('Click Here', 'bulletproof-security').'</a>'.__(' for the steps to fix this Wordfence problem before running the Setup Wizard.', 'bulletproof-security').'<br><font color="#fb0101">'.__('CAUTION: ', 'bulletproof-security').'</font>'.__('Using the Wordfence WAF Firewall may cause serious/critical problems for your website and BPS.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
					echo $text;
	
				} else {
					
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: PHP/php.ini handler htaccess code check', 'bulletproof-security').'</font><br>'.__('PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br>'.__('To automatically fix this click here: ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'">'.esc_attr__('Setup Wizard Pre-Installation Checks', 'bulletproof-security').'</a><br>'.__('The Setup Wizard Pre-Installation Checks feature will automatically fix this just by visiting the Setup Wizard page.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
					echo $text;			
					echo '<pre id="shown" style="overflow:auto;white-space:pre-wrap;height:65px;width:66%;margin:5px 0px 0px 2px;padding:5px;background:#fff url('.$pre_background_image_url.') top left repeat;border:1px solid #999;color:#000;display:block;font-family:"Courier New", Courier, monospace;font-size:11px;line-height:14px;">';
					echo '# PHP/php.ini handler htaccess code<br>';				
					
					foreach ( $h_matches[0] as $Key => $Value ) {
						echo $Value;
					}
					echo '</pre>';
				}
			}
		}
	}

	if ( esc_html($_SERVER['QUERY_STRING']) != 'page=bulletproof-security/admin/wizard/wizard.php' && ! get_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice') ) {

		if ( file_exists($file) ) {		

			$file_contents = file_get_contents($file);
			$CustomCodeoptions = get_option('bulletproof_security_options_customcode');
			$bps_customcode_one = ! isset($CustomCodeoptions['bps_customcode_one']) ? '' : $CustomCodeoptions['bps_customcode_one'];
			
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $file_contents, $matches);
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $bps_customcode_one, $DBmatches);
		
			if ( $matches[0] && ! $DBmatches[0] ) {
			
				preg_match_all('/(([#\s]{1,}|)(AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application).*\s*){1,}/', $file_contents, $h_matches );
	
				if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
					$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
				} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
					$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
				} else {
					$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
				}		
			
				if ( stripos( $file_contents, "Wordfence WAF" ) ) {
					
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: Wordfence PHP/php.ini handler htaccess code detected', 'bulletproof-security').'</font><br>'.__('Wordfence PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/wordfence-firewall-wp-contentwflogsconfig-php-file-quarantined/#wordfence-php-handler" target="_blank" title="Wordfence PHP Handler Fix">'.__('Click Here', 'bulletproof-security').'</a>'.__(' for the steps to fix this Wordfence problem.', 'bulletproof-security').'<br><font color="#fb0101">'.__('CAUTION: ', 'bulletproof-security').'</font>'.__('Using the Wordfence WAF Firewall may cause serious/critical problems for your website and BPS.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
					echo $text;				
				
				} else {

					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: PHP/php.ini handler htaccess code check', 'bulletproof-security').'</font><br>'.__('PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br>'.__('To automatically fix this click here: ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'">'.esc_attr__('Setup Wizard Pre-Installation Checks', 'bulletproof-security').'</a><br>'.__('The Setup Wizard Pre-Installation Checks feature will automatically fix this just by visiting the Setup Wizard page.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
					echo $text;			
					echo '<pre id="shown" style="overflow:auto;white-space:pre-wrap;height:65px;width:66%;margin:5px 0px 0px 2px;padding:5px;background:#fff url('.$pre_background_image_url.') top left repeat;border:1px solid #999;color:#000;display:block;font-family:"Courier New", Courier, monospace;font-size:11px;line-height:14px;">';
					echo '# PHP/php.ini handler htaccess code<br>';				
				
					foreach ( $h_matches[0] as $Key => $Value ) {
						echo $Value;
					}
					echo '</pre>';
				}
			}
		}
	}
}

add_action('admin_init', 'bps_PhpiniHandler_nag_ignore');

function bps_PhpiniHandler_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_PhpiniHandler_nag_ignore'] ) && '0' == $_GET['bps_PhpiniHandler_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - WordPress Firewall 2 plugin - breaks BPS and lots of other stuff
function bps_hud_check_wordpress_firewall2() {
	
	$firewall2 = 'wordpress-firewall-2/wordpress-firewall-2.php';
	$firewall2_active = in_array( $firewall2, apply_filters('active_plugins', get_option('active_plugins')));

	if ( $firewall2_active != 1 && ! is_plugin_active_for_network( $firewall2 ) ) {
		return;	
	}
	
	if ( $firewall2_active == 1 || is_plugin_active_for_network( $firewall2 ) ) {
	
		global $current_user;
		$user_id = $current_user->ID;			
		
		if ( ! get_user_meta($user_id, 'bps_ignore_wpfirewall2_notice') ) {
			
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}			
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('The WordPress Firewall 2 plugin is installed and activated', 'bulletproof-security').'</font><br>'.__('It is recommended that you delete the WordPress Firewall 2 plugin.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/wordpress-firewall-2-plugin-unable-to-save-custom-code/" target="_blank" title="Link opens in a new Browser window">'.__('Click Here', 'bulletproof-security').'</a>'.__(' for more information.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_wpfirewall2_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bps_wpfirewall2_nag_ignore');

function bps_wpfirewall2_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_wpfirewall2_nag_ignore'] ) && '0' == $_GET['bps_wpfirewall2_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_wpfirewall2_notice', 'true', true);
	}
}

// Check for older BPS Query String Exploits code saved to BPS Custom Code
function bps_hud_BPSQSE_old_code_check() {
$CustomCodeoptions = get_option('bulletproof_security_options_customcode');	

	if ( isset($CustomCodeoptions['bps_customcode_bpsqse']) && $CustomCodeoptions['bps_customcode_bpsqse'] == '' ) {
		return;
	}
	
	$subject = ! isset($CustomCodeoptions['bps_customcode_bpsqse']) ? '' : $CustomCodeoptions['bps_customcode_bpsqse'];	
	$pattern1 = '/RewriteCond\s%{QUERY_STRING}\s\(\\\.\/\|\\\.\.\/\|\\\.\.\.\/\)\+\(motd\|etc\|bin\)\s\[NC,OR\]/';
	$pattern2 = '/RewriteCond\s%\{THE_REQUEST\}\s(.*)\?(.*)\sHTTP\/\s\[NC,OR\]\s*RewriteCond\s%\{THE_REQUEST\}\s(.*)\*(.*)\sHTTP\/\s\[NC,OR\]/';
	$pattern3 = '/RewriteCond\s%\{THE_REQUEST\}\s.*\?\+\(%20\{1,\}.*\s*RewriteCond\s%\{THE_REQUEST\}\s.*\+\(.*\*\|%2a.*\s\[NC,OR\]/';

	if ( isset($CustomCodeoptions['bps_customcode_bpsqse']) && $CustomCodeoptions['bps_customcode_bpsqse'] != '' && preg_match($pattern1, $subject, $matches) || preg_match($pattern2, $subject, $matches) || preg_match($pattern3, $subject, $matches) ) {

		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Notice: BPS Query String Exploits Code Changes', 'bulletproof-security').'</font><br>'.__('Older BPS Query String Exploits code was found in BPS Custom Code. Several Query String Exploits rules were changed/added/modified in the root .htaccess file in BPS .49.6, .50.2 & .50.3.', 'bulletproof-security').'<br>'.__('Copy the new Query String Exploits section of code from your root .htaccess file and paste it into this BPS Custom Code text box: CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS and click the Save Root Custom Code button.', 'bulletproof-security').'<br>'.__('This Notice will go away once you have copied the new Query String Exploits code to BPS Custom Code and clicked the Save Root Custom Code button.', 'bulletproof-security').'</div>';
		echo $text;
	}
}

// Heads Up Display - Check if the /bps-backup/.htaccess file exists
function bpsPro_BBM_htaccess_check() {

	// New BPS installation - do not check or display error
	if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
		return;
	}

	$options = get_option('bulletproof_security_options_monitor');
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	$filename = WP_CONTENT_DIR . '/bps-backup/.htaccess';
	$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );

	if ( ! file_exists($filename) && isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] != 'disabled' && ! isset($_POST['Submit-BBM-Activate']) ) {
		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('BPS Alert! A BPS htaccess file was NOT found in the BPS Backup folder: ', 'bulletproof-security').'/'.$bps_wpcontent_dir.'/bps-backup/</font><br>'.__('Go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ).'">'.esc_attr__('Security Modes page', 'bulletproof-security').'</a>'.__(' and click the BPS Backup Folder BulletProof Mode Activate button.', 'bulletproof-security').'</div>';
		echo $text;
	}
}

## Checks for older BPS Speed Boost Cache code saved in BPS Custom Code
## 2.0: Checks for redundant Browser caching code & the BPS NOCHECK Marker in BPS Custom Code
function bpsPro_hud_speed_boost_cache_code() {
	
	$CC_options = get_option('bulletproof_security_options_customcode');
	$bps_customcode_one = ! isset($CC_options['bps_customcode_one']) ? '' : $CC_options['bps_customcode_one'];
	$bps_customcode_one_decode = htmlspecialchars_decode( $bps_customcode_one, ENT_QUOTES );	
	
	if ( isset($CC_options['bps_customcode_one']) && $CC_options['bps_customcode_one'] == '' || strpos( $bps_customcode_one_decode, "BPS NOCHECK" ) ) {
		return;
	}	
	
	if ( isset ( $_POST['bps_customcode_submit'] ) && $_POST['bps_customcode_submit'] == true ) {
		return;
	}

	global $current_user;
	$user_id = $current_user->ID;	
	
	$pattern1 = '/BEGIN\sWEBSITE\sSPEED\sBOOST/';
	$pattern2 = '/AddOutputFilterByType\sDEFLATE\stext\/plain\s*AddOutputFilterByType\sDEFLATE\stext\/html\s*AddOutputFilterByType\sDEFLATE\stext\/xml\s*AddOutputFilterByType\sDEFLATE\stext\/css\s*AddOutputFilterByType\sDEFLATE\sapplication\/xml\s*AddOutputFilterByType\sDEFLATE\sapplication\/xhtml\+xml\s*AddOutputFilterByType\sDEFLATE\sapplication\/rss\+xml\s*AddOutputFilterByType\sDEFLATE\sapplication\/javascript\s*AddOutputFilterByType\sDEFLATE\sapplication\/x-javascript\s*AddOutputFilterByType\sDEFLATE\sapplication\/x-httpd-php\s*AddOutputFilterByType\sDEFLATE\sapplication\/x-httpd-fastphp\s*AddOutputFilterByType\sDEFLATE\simage\/svg\+xml/';

	if ( ! get_user_meta($user_id, 'bpsPro_ignore_speed_boost_notice') ) { 
		
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}		

		if ( preg_match( $pattern1, htmlspecialchars_decode( $bps_customcode_one, ENT_QUOTES ), $matches1 ) && preg_match( $pattern2, htmlspecialchars_decode( $bps_customcode_one, ENT_QUOTES ), $matches2 ) ) {

			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('New Improved BPS Speed Boost Cache Code', 'bulletproof-security').'</font><br>'.__('Older BPS Speed Boost Cache Code was found saved in this BPS Custom Code text box: CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE', 'bulletproof-security').'.<br>'.__('Newer improved BPS Speed Boost Cache Code has been created, which should improve website load speed performance even more.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/htaccess-caching-code-speed-boost-cache-code/" target="_blank" title="BPS Speed Boost Cache Code">'.__('Get The New Improved BPS Speed Boost Cache Code', 'bulletproof-security').'</a>'.__('. To dismiss this Notice click the Dismiss Notice button below.', 'bulletproof-security').'<br>'.__('To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_hud_speed_boost_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}

		if ( strpos( $bps_customcode_one, "WEBSITE SPEED BOOST" ) ) {
			if ( strpos( $bps_customcode_one, "WPSuperCache" ) || strpos( $bps_customcode_one, "W3TC Browser Cache" ) || strpos( $bps_customcode_one, "Comet Cache" ) || strpos( $bps_customcode_one, "GzipWpFastestCache" ) || strpos( $bps_customcode_one, "LBCWpFastestCache" ) || strpos( $bps_customcode_one, "WP Rocket" ) ) {
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS Speed Boost Cache Custom Code Notice', 'bulletproof-security').'</font><br>'.__('BPS Speed Boost Cache Code was found in this BPS Custom Code text box: CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE', 'bulletproof-security').'<br>'.__('and another caching plugin\'s Marker text was also found in this BPS Custom Code text box.', 'bulletproof-security').'<br>'.__('Click this link: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/bps-speed-boost-cache-custom-code-notice/" target="_blank" title="BPS SBC Custom Code Forum Topic">'.__('BPS Speed Boost Cache Custom Code Notice Forum Topic', 'bulletproof-security').'</a>'.__(' for help information on what this Notice means and what to do next.', 'bulletproof-security').'</div>';
			echo $text;
			}
		}
	}
}

add_action('admin_init', 'bpsPro_hud_speed_boost_nag_ignore');

function bpsPro_hud_speed_boost_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_hud_speed_boost_nag_ignore']) && '0' == $_GET['bpsPro_hud_speed_boost_nag_ignore'] ) {
		add_user_meta($user_id, 'bpsPro_ignore_speed_boost_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - JTC-Lite New Feature Dismiss Notice
function bpsPro_hud_jtc_lite_notice() {

	$jtc_options = get_option('bulletproof_security_options_login_security_jtc');

	if ( isset($jtc_options['bps_jtc_login_form']) && $jtc_options['bps_jtc_login_form'] == '0' ) {
		
		global $current_user;
		$user_id = $current_user->ID;
	
		if ( ! get_user_meta($user_id, 'bps_ignore_jtc_lite_notice') ) {
				
			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}

			$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:0px 5px;margin:0px 0px 35px 0px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS New Feature Notice: JTC-Lite', 'bulletproof-security').'</font><br>'.__('JTC-Lite protects the WordPress Login page Form against automated SpamBot and HackerBot Brute Force Login attacks', 'bulletproof-security').'<br>'.__('and also prevents User Accounts from being locked repeatedly by Brute Force Login Bot attacks on your Login page Form.', 'bulletproof-security').'<br>'.__('To enable/turn On JTC-Lite, click this ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-2' ).'">'.esc_attr__('JTC-Lite link', 'bulletproof-security').'</a>'.__('. Click/check the Login Form Checkbox and click the Save Options button.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_jtc_lite_nag_ignore=0'.'" style="text-decoration:none;font-weight:600;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bpsPro_jtc_lite_nag_ignore');

function bpsPro_jtc_lite_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_jtc_lite_nag_ignore']) && '0' == $_GET['bpsPro_jtc_lite_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_jtc_lite_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - BPS plugin 30 day review/rating request Dismiss Notice
function bpsPro_hud_rate_notice() {

	global $current_user, $pagenow;
	$user_id = $current_user->ID;

	if ( ! get_option('bulletproof_security_options_rate_free') ) {
		return;
	}

	$options = get_option('bulletproof_security_options_rate_free');
	
	if ( time() >= $options['bps_free_rate_review'] ) {

		if ( preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches) || 'update-core.php' == $pagenow || 'plugins.php' == $pagenow ) {
	
			if ( ! get_user_meta($user_id, 'bps_ignore_rate_notice') ) {
					
				if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
					$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
				} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
					$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
				} else {
					$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
				}

				$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:0px 5px;margin:0px 0px 35px 0px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS Plugin Star Rating Request', 'bulletproof-security').'</font><br>'.__('A BPS star rating only takes a couple of minutes and would be very much appreciated. We are looking for 5 star ratings and not "fancy" reviews.', 'bulletproof-security').'<br>'.__('A simple review like "works great" or "has been protecting my website for X months or X years" is perfect.', 'bulletproof-security').'<br>'.__('Click this link to submit a BPS Plugin Star Rating: ', 'bulletproof-security').'<a href="https://wordpress.org/support/plugin/bulletproof-security/reviews/#postform" target="_blank" title="BPS Plugin Star Rating">'.__('BPS Plugin Star Rating', 'bulletproof-security').'</a>, '.__('login to the WordPress.org site and scroll to the bottom of the Reviews page.', 'bulletproof-security').'<br>'.__('To Dismiss this one-time Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_rate_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
				echo $text;
			}
		}
	}
}

add_action('admin_init', 'bpsPro_rate_nag_ignore');

function bpsPro_rate_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_rate_nag_ignore']) && '0' == $_GET['bpsPro_rate_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_rate_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss Notice - Check if Mod Security is Loaded|Enabled. Displays a link to a help forum topic.
function bpsPro_hud_mod_security_check() {
	
	$bps_mod_security_options = get_option('bulletproof_security_options_mod_security');

	if ( isset($bps_mod_security_options['bps_mod_security_check']) && $bps_mod_security_options['bps_mod_security_check'] == '1' ) {

		global $current_user;
		$user_id = $current_user->ID;		

		if ( ! get_user_meta($user_id, 'bpsPro_ignore_mod_security_notice')) { 
		
			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS Notice: Mod Security Module is Loaded|Enabled', 'bulletproof-security').'</font><br>'.__('Please take a minute to view this Mod Security help forum topic: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/mod-security-common-known-problems/" target="_blank" title="Mod Security Common Known Problems">'.__('Mod Security Common Known Problems', 'bulletproof-security').'</a>.<br>'.__('If you are not experiencing any of the problems listed in the Mod Security help forum topic then you can dismiss this Dismiss Notice.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_mod_security_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bpsPro_mod_security_nag_ignore');

function bpsPro_mod_security_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_mod_security_nag_ignore']) && '0' == $_GET['bpsPro_mod_security_nag_ignore'] ) {
		add_user_meta($user_id, 'bpsPro_ignore_mod_security_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss Notice - GDPR Compliance Dismiss Notice. Displays a link to a help forum topic.
function bpsPro_hud_gdpr_compliance() {
	
	// Setup Wizard DB option is saved by running the Setup Wizard, on BPS Upgrades & manual BPS setup
	if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
		return;
	}

	global $current_user;
	$user_id = $current_user->ID;		
	
	if ( ! get_user_meta($user_id, 'bpsPro_ignore_gdpr_compliance_notice')) { 
	
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}
		
		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS GDPR Compliance Notice', 'bulletproof-security').'</font><br>'.__('A new Setup Wizard Option has been created which allows you to turn off all IP address logging in BPS to make your website GDPR Compliant.', 'bulletproof-security').'<br>'.__('Click this ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php#bps-tabs-2' ).'">'.__('GDPR Compliance Setup Wizard Option link', 'bulletproof-security').'</a>. '.__('Choose the GDPR Compliance On setting.', 'bulletproof-security').'<br>'.__('For more information about GDPR Compliance click this ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/bps-gdpr-compliance/" target="_blank" title="GDPR Compliance">'.__('GDPR Compliance Forum Topic link', 'bulletproof-security').'</a>.<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_gdpr_compliance_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
		echo $text;
	}
}

add_action('admin_init', 'bpsPro_gdpr_compliance_nag_ignore');

function bpsPro_gdpr_compliance_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_gdpr_compliance_nag_ignore']) && '0' == $_GET['bpsPro_gdpr_compliance_nag_ignore'] ) {
		add_user_meta($user_id, 'bpsPro_ignore_gdpr_compliance_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss Notice: If someone has enabled any of the BPS Pro MU Tools WP Automatic Update options check the wp-config.php file for redundant constants.
function bpsPro_mu_wp_automatic_updates_notice() {
	
	if ( ! get_option('bulletproof_security_options_mu_wp_autoupdate') ) {
		return;
	}
	
	$wpconfig_file = ABSPATH . 'wp-config.php';
	
	// If someone has moved their wp-config.php file exit.
	if ( ! file_exists( $wpconfig_file ) ) {
		return;
	}

	if ( file_exists($wpconfig_file) ) {
		
		$file_contents = file_get_contents($wpconfig_file);
		$wp_auto_update_options = get_option('bulletproof_security_options_mu_wp_autoupdate');
		
		if ( $wp_auto_update_options['bps_automatic_updater_disabled'] == 'enabled' || $wp_auto_update_options['bps_auto_update_core_updates_disabled'] == 'enabled' || $wp_auto_update_options['bps_auto_update_core'] == 'enabled' || $wp_auto_update_options['bps_allow_dev_auto_core_updates'] == 'enabled' || $wp_auto_update_options['bps_allow_minor_auto_core_updates'] == 'enabled' || $wp_auto_update_options['bps_allow_major_auto_core_updates'] == 'enabled' ) {
		
			if ( preg_match( '/(WP_AUTO_UPDATE_CORE|AUTOMATIC_UPDATER_DISABLED)/', $file_contents ) ) {
	
				global $current_user;
				$user_id = $current_user->ID;		
			
				if ( ! get_user_meta($user_id, 'bpsPro_ignore_mu_wp_automatic_updates_notice') ) {
			
					if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
						$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
					} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
						$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
					} else {
						$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
					}
			
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS wp-config.php file WP Automatic Update constants detected', 'bulletproof-security').'</font><br>'.__('You are using the BPS MU Tools plugin option settings to handle WP Automatic Updates. BPS detected that you are also using one or both of these WP Automatic Update constants in your wp-config.php file: WP_AUTO_UPDATE_CORE and/or AUTOMATIC_UPDATER_DISABLED. Either comment out these constants in your wp-config.php file or delete these constants. If you choose to comment out these constants instead of deleting them then dismiss this Dismiss Notice after you have commented them out.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_mu_wp_automatic_updates_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
					echo $text;
				}
			}
		}
	}
}

add_action('admin_init', 'bpsPro_mu_wp_automatic_updates_nag_ignore');

function bpsPro_mu_wp_automatic_updates_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_mu_wp_automatic_updates_nag_ignore']) && '0' == $_GET['bpsPro_mu_wp_automatic_updates_nag_ignore'] ) {
		add_user_meta($user_id, 'bpsPro_ignore_mu_wp_automatic_updates_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - New feature or option Dismiss Notice - Used for very important new features and very rarely.
function bpsPro_hud_new_feature_notice() {

	if ( ! get_option('bulletproof_security_options_new_feature') ) {
		return;	
	}
	
	$new_feature_options = get_option('bulletproof_security_options_new_feature');

	if ( $new_feature_options['bps_mscan_rebuild'] == 'new2' ) {
		return;		
	}

	if ( $new_feature_options['bps_mscan_rebuild'] == 'upgrade2' ) {
	
		global $current_user;
		$user_id = $current_user->ID;			
		
		if ( ! get_user_meta($user_id, 'bpsPro_hud_new_feature_notice') ) { 
			
			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}			
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('MScan Significant Improvements Notice', 'bulletproof-security').'</font><br>'.__('Significant improvements have been made to MScan. ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/mscan/mscan.php' ).'">'.__('Run a new MScan scan', 'bulletproof-security').'</a><br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_new_feature_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;		
		}
	}
}

add_action('admin_init', 'bpsPro_new_feature_nag_ignore');

function bpsPro_new_feature_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_new_feature_nag_ignore']) && '0' == $_GET['bpsPro_new_feature_nag_ignore'] ) {
		add_user_meta($user_id, 'bpsPro_hud_new_feature_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - Script|File Owner User ID: Check if any folders or files have different Owner UID’s.
function bpsPro_hud_owner_uid_check_notice() {

	$root = ABSPATH;
	$root_htaccess = file_exists( ABSPATH . '.htaccess' ) ? ABSPATH . '.htaccess' : '';
	$wp_config_file = file_exists( ABSPATH . 'wp-config.php' ) ? ABSPATH . 'wp-config.php' : '';
	$wp_admin_folder = is_dir( ABSPATH . 'wp-admin' ) ? ABSPATH . 'wp-admin' : '';
	$wp_includes_folder = is_dir( ABSPATH . 'wp-includes' ) ? ABSPATH . 'wp-includes' : '';	
	$wp_content_folder = WP_CONTENT_DIR;	
	$plugins_folder = WP_PLUGIN_DIR;
	$themes_folder = get_theme_root();
	$wp_uploads_dir_array = wp_upload_dir();
	$wp_uploads_folder = $wp_uploads_dir_array['basedir'];
	$wp_upgrade_folder = is_dir( WP_CONTENT_DIR . '/upgrade' ) ? WP_CONTENT_DIR . '/upgrade' : '';
	$mu_plugins_folder = is_dir( WP_CONTENT_DIR . '/mu-plugins' ) ? WP_CONTENT_DIR . '/mu-plugins' : '';
	$bps_backup_folder = is_dir( WP_CONTENT_DIR . '/bps-backup' ) ? WP_CONTENT_DIR . '/bps-backup' : '';
	$autorestore_folder = is_dir( WP_CONTENT_DIR . '/bps-backup/autorestore' ) ? WP_CONTENT_DIR . '/bps-backup/autorestore' : '';
	$bps_logs_folder = is_dir( WP_CONTENT_DIR . '/bps-backup/logs' ) ? WP_CONTENT_DIR . '/bps-backup/logs' : '';
	$bps_master_backups_folder = is_dir( WP_CONTENT_DIR . '/bps-backup/master-backups' ) ? WP_CONTENT_DIR . '/bps-backup/master-backups' : '';
	$bps_quarantine_folder = is_dir( WP_CONTENT_DIR . '/bps-backup/quarantine' ) ? WP_CONTENT_DIR . '/bps-backup/quarantine' : '';
	$bps_mscan_folder = is_dir( WP_CONTENT_DIR . '/bps-backup/mscan' ) ? WP_CONTENT_DIR . '/bps-backup/mscan' : '';
	$bps_wp_hashes_folder = is_dir( WP_CONTENT_DIR . '/bps-backup/wp-hashes' ) ? WP_CONTENT_DIR . '/bps-backup/wp-hashes' : '';
	$bps_plugin_hashes_folder = is_dir( WP_CONTENT_DIR . '/bps-backup/plugin-hashes' ) ? WP_CONTENT_DIR . '/bps-backup/plugin-hashes' : '';
	$bps_theme_hashes_folder = is_dir( WP_CONTENT_DIR . '/bps-backup/theme-hashes' ) ? WP_CONTENT_DIR . '/bps-backup/theme-hashes' : '';
	$DBBoptions = get_option('bulletproof_security_options_db_backup');
	$bps_db_backup_option = isset($DBBoptions['bps_db_backup_folder']) ? $DBBoptions['bps_db_backup_folder'] : '';
	$bps_db_backup_folder = str_replace( array( '\\', '//'), "/", $bps_db_backup_option );

	$folder_array = array( $root, $root_htaccess, $wp_config_file, $wp_admin_folder, $wp_includes_folder, $wp_content_folder, $plugins_folder, $themes_folder, $wp_uploads_folder, $wp_upgrade_folder, $mu_plugins_folder, $bps_backup_folder, $autorestore_folder, $bps_logs_folder, $bps_master_backups_folder, $bps_quarantine_folder, $bps_mscan_folder, $bps_wp_hashes_folder, $bps_plugin_hashes_folder, $bps_theme_hashes_folder, $bps_db_backup_folder );
	
	$folder_script_uid_array = array();
	$folder_fileowner_uid_array = array();	
	
	foreach ( $folder_array as $key => $value ) {
		
		if ( $value != '' ) {
			$stat = @stat($value);
			$folder_script_uid_array[$value] = @$stat['uid'];
			$folder_fileowner_uid_array[$value] = @fileowner( $value );
		}
	}

	// this is for testing mismatches
	// array_push($folder_script_uid_array, '5');

	$folder_script_uid_array_unique = array_unique($folder_script_uid_array);
	$folder_fileowner_uid_array_unique = array_unique($folder_fileowner_uid_array);

	$folder_script_uid_count = count($folder_script_uid_array_unique);
	$folder_fileowner_uid_count = count($folder_fileowner_uid_array_unique);

	if ( $folder_script_uid_count != '1' || $folder_fileowner_uid_count != '1' ) {
	
		$uid_array_diff_script = array_diff( $folder_script_uid_array_unique, $folder_fileowner_uid_array_unique );
		$uid_array_diff_fileowner = array_diff( $folder_fileowner_uid_array_unique, $folder_script_uid_array_unique );
		$mismatch = '';
		
		if ( ! empty($uid_array_diff_script) ) {
			
			foreach ( $uid_array_diff_script as $key => $value ) {
				$mismatch = __('Folder|File', 'bullletproof-security') .': ' . $key . __(' Script UID', 'bulletproof-security').': '. $value .'<br>';
			}
		}
		
		if ( ! empty($uid_array_diff_fileowner) ) {
			
			foreach ( $uid_array_diff_fileowner as $key => $value ) {
				$mismatch = __('Folder|File', 'bullletproof-security') .': ' . $key . __(' File Owner UID', 'bulletproof-security').': '. $value .'<br>';
			}
		}

		global $current_user;
		$user_id = $current_user->ID;			
		
		if ( ! get_user_meta($user_id, 'bpsPro_hud_owner_uid_check_notice') ) {

			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}			
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('Script|File Owner User ID Mismatch Notice', 'bulletproof-security').'</font><br>'.__('You have different Script or File Owner User ID\'s for this folder or file: ', 'bulletproof-security').$mismatch.__('All Script and File Owner User ID\'s must be the same in order for BPS and other things to function normally.', 'bulletproof-security').'<br>'.__('For help fixing this problem, please post a new reply in this forum topic: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/scriptfile-owner-user-id-mismatch-notice-on-all-sites-but-no-mismatches/" target="_blank" title="Link opens in a new Browser window">'.__('Script|File Owner User ID Mismatch Forum Topic', 'bulletproof-security').'</a><br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_owner_uid_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;		
		}
	}
}

add_action('admin_init', 'bpsPro_owner_uid_nag_ignore');

function bpsPro_owner_uid_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_owner_uid_nag_ignore']) && '0' == $_GET['bpsPro_owner_uid_nag_ignore'] ) {
		add_user_meta($user_id, 'bpsPro_hud_owner_uid_check_notice', 'true', true);
	}
}

// Automatically adds a whitelist rule for the BPS plugin folder to any wp-content .htaccess files that break the BPS plugin.
// Sucuri, Defender, SiteGround Security, etc. plugins
// Notes: Order Allow,Deny needs to be changed to Deny,Allow in order for the BPS folder whitelist rule to work.
// iThemes Security now adds their plugins folder blocking (and other) htaccess code in the root htaccess file, but it doesn't
// break BPS Pro plugin files that are whitelisted in the BPS Pro Plugin Firewall.
// For BPS free I have created a new .htaccess file in the BPS root plugin folder that whitelists frontloading BPS plugin files.
function bpsPro_wpcontent_htaccess_file_fix() {

	$filename = WP_CONTENT_DIR . '/.htaccess';
	$pattern1 = '/Require\sall\sdenied/';
	$bps_code1 = '/Require\senv\swhitelist/';
	$pattern2 = '/Order\sAllow,Deny\s*Deny\sfrom\sall/i';
	$bps_code2 = '/Allow\sfrom\senv=whitelist/';
	$pattern3 = '/Order\sDeny,Allow\s*Deny\sfrom\sall/i';
	$pattern4 = '/<FilesMatch\s"\\\.\(\?i:php\)\$">\s*<IfModule\s!mod_authz_core\.c>\s*Order\sallow,deny\s*Deny\sfrom\sall\s*<\/IfModule>\s*<IfModule\smod_authz_core\.c>\s*Require\sall\sdenied\s*<\/IfModule>\s*<\/FilesMatch>/';
	$bps_code4 = '/SetEnvIf\sRequest_URI\s"bulletproof-security\/\.\*\$"\swhitelist/';	

	if ( file_exists($filename) ) {
		
		$file_contents = file_get_contents($filename);

		if ( preg_match( $pattern1, $file_contents ) && ! preg_match( $bps_code1, $file_contents ) ) { 
		
			$stringReplace1 = preg_replace( $pattern1, "<IfModule mod_setenvif.c>\nSetEnvIf Request_URI \"bulletproof-security/.*$\" whitelist\nRequire env whitelist\nRequire all denied\n</IfModule>", $file_contents );

			file_put_contents( $filename, $stringReplace1 );
		}

		if ( preg_match( $pattern2, $file_contents ) && ! preg_match( $bps_code2, $file_contents ) ) { 
		
			$stringReplace2 = preg_replace( $pattern2, "<IfModule mod_setenvif.c>\nSetEnvIf Request_URI \"bulletproof-security/.*$\" whitelist\nOrder Deny,Allow\nDeny from all\nAllow from env=whitelist\n</IfModule>", $file_contents );

			file_put_contents( $filename, $stringReplace2 );
		}

		if ( preg_match( $pattern3, $file_contents ) && ! preg_match( $bps_code2, $file_contents ) ) { 
		
			$stringReplace3 = preg_replace( $pattern3, "<IfModule mod_setenvif.c>\nSetEnvIf Request_URI \"bulletproof-security/.*$\" whitelist\nOrder Deny,Allow\nDeny from all\nAllow from env=whitelist\n</IfModule>", $file_contents );

			file_put_contents( $filename, $stringReplace3 );
		}

		if ( preg_match( $pattern4, $file_contents ) && preg_match( $bps_code4, $file_contents ) ) { 
		
			$stringReplace4 = preg_replace( $pattern4, "", $file_contents );

			file_put_contents( $filename, $stringReplace4 );
		}
	}
}

// Heads Up Display w/ Dismiss Notice - BPS Pro 25% off Sale Dismiss Notice.
function bpsPro_hud_bpspro_sale() {
	
	// Setup Wizard DB option is saved by running the Setup Wizard, on BPS Upgrades & manual BPS setup
	if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
		return;
	}
	
	$oct_6_2022 = '1665073080';
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;

	if ( time() < $oct_6_2022 ) {

		global $current_user;
		$user_id = $current_user->ID;		
		
		if ( ! get_user_meta($user_id, 'bpsPro_ignore_bpspro_sale_notice')) { 
		
			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS Pro 25% Off Sale September 26 - October 6', 'bulletproof-security').'</font><br>'.__('One-time Purchase Price: $52.50. No Recurring Yearly Costs Or Subscriptions. Unlimited installations. Free Upgrades For Life. Free Technical Support For Life.', 'bulletproof-security').'<br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Buy BPS Pro">'.__('Buy BPS Pro', 'bulletproof-security').'</a><div style="min-height:5px"></div>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Alerts|Logs|Email Options page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_bpspro_sale_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bpsPro_bpspro_sale_nag_ignore');

function bpsPro_bpspro_sale_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_bpspro_sale_nag_ignore']) && '0' == $_GET['bpsPro_bpspro_sale_nag_ignore'] ) {
		add_user_meta($user_id, 'bpsPro_ignore_bpspro_sale_notice', 'true', true);
	}
}

?>