<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! function_exists ('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

// Display HUD AutoFix Alerts in WP Dashboard
function bps_HUD_autofix_whitelist_WP_Dashboard() {
	
	if ( current_user_can('manage_options') ) { 
		bpsPro_HUD_autofix_whitelist_check();
	}
}

add_action('admin_notices', 'bps_HUD_autofix_whitelist_WP_Dashboard');

## Setup Wizard AutoFix (AutoWhitelist|AutoSetup|AutoCleanup)
## Detects 100+ known issues in other plugins and themes that require Custom Code whitelist rules.
## IMPORTANT: This dumbed down simple code significantly outperformed all other "fancy" coding methods of processing 
## these checks in benchmark testing. Do not change this simplified code. No Loops please.
function bpsPro_HUD_autofix_whitelist_check() {
	
	if ( ! get_option('bulletproof_security_options_wizard_free') ) {
		return;
	}

	$AutoFix_Options = get_option('bulletproof_security_options_wizard_autofix');
	
	if ( isset($AutoFix_Options['bps_wizard_autofix']) && $AutoFix_Options['bps_wizard_autofix'] == 'Off' ) {
		return;
	}

	global $blog_id;
	
	if ( is_multisite() && $blog_id != 1 ) {
		return;
	}

	if ( isset( $_POST['Submit-Setup-Wizard'] ) && $_POST['Submit-Setup-Wizard'] == true ) {
		return;
	}

	$autofix_message = 0;
	$CC_Options_root = get_option('bulletproof_security_options_customcode');
	$CC_Options_wpadmin = get_option('bulletproof_security_options_customcode_WPA');	

	## 9. CUSTOM CODE REQUEST METHODS FILTERED
	$bps_customcode_request_methods = htmlspecialchars_decode( $CC_Options_root['bps_customcode_request_methods'], ENT_QUOTES );
	$pattern_RMF = '/#{1,}(\s|){1,}RewriteCond\s\%\{REQUEST_METHOD\}\s\^\(HEAD\)\s\[NC\](.*\s*){1}(#{1,}(\s|){1,}RewriteRule\s\^\(\.\*\)\$\s(.*)\/bulletproof-security\/405\.php\s(\[L\]|\[R,L\])|#{1,}(\s|){1,}RewriteRule\s\^\(\.\*\)\$\s\-\s\[R=405,L\])/';
	$debug_RMF = '';

	$jetpack = 'jetpack/jetpack.php';
	$jetpack_active = in_array( $jetpack, apply_filters('active_plugins', get_option('active_plugins')));
	$marmoset_viewer = 'marmoset-viewer/marmoset-viewer.php';
	$marmoset_viewer_active = in_array( $marmoset_viewer, apply_filters('active_plugins', get_option('active_plugins')));
	$backwpup = 'backwpup/backwpup.php';
	$backwpup_active = in_array( $backwpup, apply_filters('active_plugins', get_option('active_plugins')));
	$mailpoet = 'wysija-newsletters/index.php';
	$mailpoet_active = in_array( $mailpoet, apply_filters('active_plugins', get_option('active_plugins')));
	$backupwordpress = 'backupwordpress/backupwordpress.php';
	$backupwordpress_active = in_array( $backupwordpress, apply_filters('active_plugins', get_option('active_plugins')));	
	$broken_link_checker = 'broken-link-checker/broken-link-checker.php';
	$broken_link_checker_active = in_array( $broken_link_checker, apply_filters('active_plugins', get_option('active_plugins')));
	$mailchimp = 'mailchimp-for-wp/mailchimp-for-wp.php';
	$mailchimp_active = in_array( $mailchimp, apply_filters('active_plugins', get_option('active_plugins')));
	$powerpress = 'powerpress/powerpress.php';
	$powerpress_active = in_array( $powerpress, apply_filters('active_plugins', get_option('active_plugins')));		

	if ( $jetpack_active == 1 || is_plugin_active_for_network( $jetpack ) || $marmoset_viewer_active == 1 || is_plugin_active_for_network( $marmoset_viewer ) || $backwpup_active == 1 || is_plugin_active_for_network( $backwpup ) || $mailpoet_active == 1 || is_plugin_active_for_network( $mailpoet ) || $backupwordpress_active == 1 || is_plugin_active_for_network( $backupwordpress ) || $broken_link_checker_active == 1 || is_plugin_active_for_network( $broken_link_checker ) || $mailchimp_active == 1 || is_plugin_active_for_network( $mailchimp ) || $powerpress_active == 1 || is_plugin_active_for_network( $powerpress ) ) {
		
		if ( ! preg_match( $pattern_RMF, $bps_customcode_request_methods ) ) {
			$autofix_message = 1;
			$debug_RMF .= __('CC Root Text Box 9: Allow HEAD Requests General Rule for Jetpack, Marmoset Viewer, BackWPup, MailPoet Newsletters (wysija newsletters), BackUpWordPress, Broken Link Checker, MailChimp for WordPress Plugins', 'bulletproof-security').'<br>';
		}		
	}

	## 10. CUSTOM CODE PLUGIN/THEME SKIP/BYPASS RULES
	$bps_customcode_two = htmlspecialchars_decode( $CC_Options_root['bps_customcode_two'], ENT_QUOTES );
	$debug_PTSB = '';	
	
	$woocommerce = 'woocommerce/woocommerce.php';
	$woocommerce_active = in_array( $woocommerce, apply_filters('active_plugins', get_option('active_plugins')));
	$simple_lightbox = 'simple-lightbox/main.php';
	$simple_lightbox_active = in_array( $simple_lightbox, apply_filters('active_plugins', get_option('active_plugins')));
	$visual_composer = 'js_composer/js_composer.php';
	$visual_composer_active = in_array( $visual_composer, apply_filters('active_plugins', get_option('active_plugins')));
	$ee_attendee = 'eea-attendee-mover/eea-attendee-mover.php';
	$ee_attendee_active = in_array( $ee_attendee, apply_filters('active_plugins', get_option('active_plugins')));
	$wp_rocket = 'wp-rocket/wp-rocket.php';
	$wp_rocket_active = in_array( $wp_rocket, apply_filters('active_plugins', get_option('active_plugins')));	
	$emg_pro = 'easy-media-gallery-pro/easy-media-gallery-pro.php';
	$emg_pro_active = in_array( $emg_pro, apply_filters('active_plugins', get_option('active_plugins')));	
	$nextend_fb_connect = 'nextend-facebook-connect/nextend-facebook-connect.php';
	$nextend_fb_connect_active = in_array( $nextend_fb_connect, apply_filters('active_plugins', get_option('active_plugins')));	
	$shashin = 'shashin/start.php';
	$shashin_active = in_array( $shashin, apply_filters('active_plugins', get_option('active_plugins')));
	$nocturnal_theme = wp_get_theme( 'nocturnal' );
	$shopp = 'shopp/Shopp.php';
	$shopp_active = in_array( $shopp, apply_filters('active_plugins', get_option('active_plugins')));
	$wp_invoice = 'wp-invoice/wp-invoice.php';
	$wp_invoice_active = in_array( $wp_invoice, apply_filters('active_plugins', get_option('active_plugins')));
	$wp_greet = 'wp-greet/wp-greet.php';
	$wp_greet_active = in_array( $wp_greet, apply_filters('active_plugins', get_option('active_plugins')));
	$wp_juicebox = 'wp-juicebox/wp-juicebox.php';
	$wp_juicebox_active = in_array( $wp_juicebox, apply_filters('active_plugins', get_option('active_plugins')));	
	$prayer_engine = 'prayerengine_plugin/prayerengine_plugin.php';
	$prayer_engine_active = in_array( $prayer_engine, apply_filters('active_plugins', get_option('active_plugins')));	
	$appointment_calendar = 'appointment-calendar/appointment-calendar.php';
	$appointment_calendar_active = in_array( $appointment_calendar, apply_filters('active_plugins', get_option('active_plugins')));	
	$thirsty_affiliates = 'thirstyaffiliates/thirstyaffiliates.php';
	$thirsty_affiliates_active = in_array( $thirsty_affiliates, apply_filters('active_plugins', get_option('active_plugins')));
	$woo_ogone = 'woocommerce_ogonecw/woocommerce_ogonecw.php';
	$woo_ogone_active = in_array( $woo_ogone, apply_filters('active_plugins', get_option('active_plugins')));
	$OIOpublisher = WP_PLUGIN_DIR . '/oiopub-direct/wp.php';	
	
	if ( $woocommerce_active == 1 || is_plugin_active_for_network( $woocommerce ) ) {
		$ptsb1 = '/RewriteCond\s%{REQUEST_URI}\s\^\.\*\/\(shop\|cart\|checkout\|wishlist\)\.\*\s\[NC\]/';	
		$ptsb2 = '/RewriteCond\s%{QUERY_STRING}\s\.\*\(order\|wc-ajax=\)\.\*\s\[NC\]/';
		if ( ! preg_match( $ptsb1, $bps_customcode_two ) || ! preg_match( $ptsb2, $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: WooCommerce Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $simple_lightbox_active == 1 || is_plugin_active_for_network( $simple_lightbox ) ) {
		if ( ! preg_match( '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/simple-lightbox\/\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: Simple Lightbox Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $visual_composer_active == 1 || is_plugin_active_for_network( $visual_composer ) ) {
		if ( ! preg_match( '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/js_composer\/\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: WPBakery Visual Composer Plugin', 'bulletproof-security').'<br>';
		}
	}
	if ( $ee_attendee_active == 1 || is_plugin_active_for_network( $ee_attendee ) ) {
		if ( ! preg_match( '/RewriteCond\s%{QUERY_STRING}\slimit%5B%5D=\(\.\*\)\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: Event Espresso Attendee Mover Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $wp_rocket_active == 1 || is_plugin_active_for_network( $wp_rocket ) ) {
		if ( ! preg_match( '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/wp-rocket\/\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: WP Rocket Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $emg_pro_active == 1 || is_plugin_active_for_network( $emg_pro ) ) {
		if ( ! preg_match( '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/easy-media-gallery-pro\/\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: Easy Media Gallery Pro Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $nextend_fb_connect_active == 1 || is_plugin_active_for_network( $nextend_fb_connect ) ) {
		if ( ! preg_match( '/RewriteCond\s%{QUERY_STRING}\sloginFacebook=\(\.\*\)\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: Nextend Facebook Connect Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $shashin_active == 1 || is_plugin_active_for_network( $shashin ) ) {
		if ( ! preg_match( '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/shashin\/\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: Shashin Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $nocturnal_theme->exists() ) {
		if ( ! preg_match( '/RewriteCond\s%{QUERY_STRING}\splayerInstance=\(\.\*\)\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: Nocturnal Theme', 'bulletproof-security').'<br>';
		}		
	}
	if ( $shopp_active == 1 || is_plugin_active_for_network( $shopp ) ) {
		if ( ! preg_match( '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/shopp\/\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: Shopp Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $wp_invoice_active == 1 || is_plugin_active_for_network( $wp_invoice ) ) {
		if ( ! preg_match( '/RewriteCond\s%{QUERY_STRING}\spage=wpi_\(\.\*\)\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: WP-Invoice - Web Invoice and Billing Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $wp_greet_active == 1 || is_plugin_active_for_network( $wp_greet ) ) {
		if ( ! preg_match( '/RewriteCond\s%{QUERY_STRING}\sgallery=([0-9]+)&image=\(\.\*\)\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: wp-greet Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $wp_juicebox_active == 1 || is_plugin_active_for_network( $wp_juicebox ) ) {
		if ( ! preg_match( '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/wp-juicebox\/\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: WP-Juicebox Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $prayer_engine_active == 1 || is_plugin_active_for_network( $prayer_engine ) ) {
		if ( ! preg_match( '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/prayerengine_plugin\/\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: Prayer Engine Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $appointment_calendar_active == 1 || is_plugin_active_for_network( $appointment_calendar ) ) {
		if ( ! preg_match( '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/appointment-calendar\/\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: Appointment Calendar Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $thirsty_affiliates_active == 1 || is_plugin_active_for_network( $thirsty_affiliates ) ) {
		if ( ! preg_match( '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/thirstyaffiliates\/\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: ThirstyAffiliates Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $woo_ogone_active == 1 || is_plugin_active_for_network( $woo_ogone ) ) {
		if ( ! preg_match( '/RewriteCond\s%{REQUEST_URI}\s\^(.*)\/plugins\/woocommerce_ogonecw\/\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: WooCommerce Ogone Payment Gateway Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( file_exists($OIOpublisher) ) {
		if ( ! preg_match( '/RewriteCond\s%{REQUEST_URI}\s\^\/advertise\/uploads\/\s\[NC\]/', $bps_customcode_two ) ) {
			$autofix_message = 1;
			$debug_PTSB .= __('CC Root Text Box 10: OIOpublisher Ad Manager Plugin', 'bulletproof-security').'<br>';
		}		
	}

	## 11. CUSTOM CODE TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE
	$bps_customcode_rfi = htmlspecialchars_decode( $CC_Options_root['bps_customcode_timthumb_misc'], ENT_QUOTES );
	$debug_RFI = '';
	
	$pdf_viewer = 'pdf-viewer/pdf-viewer.php';
	$pdf_viewer_active = in_array( $pdf_viewer, apply_filters('active_plugins', get_option('active_plugins')));	
	$marmoset_viewer = 'marmoset-viewer/marmoset-viewer.php';
	$marmoset_viewer_active = in_array( $marmoset_viewer, apply_filters('active_plugins', get_option('active_plugins')));	
	$pdf_viewer_themencode = 'pdf-viewer-for-wordpress/pdf-viewer-for-wordpress.php';
	$pdf_viewer_themencode_active = in_array( $pdf_viewer_themencode, apply_filters('active_plugins', get_option('active_plugins')));	
	$jupdf_pdf_viewer = 'jupdf-pdf-viewer/jupdf-pdf-viewer.php';
	$jupdf_pdf_viewer_active = in_array( $jupdf_pdf_viewer, apply_filters('active_plugins', get_option('active_plugins')));	
	$userPro = 'userpro/index.php';
	$userPro_active = in_array( $userPro, apply_filters('active_plugins', get_option('active_plugins')));
	$NativeChurch_theme = wp_get_theme( 'NativeChurch' );
	$user_avatar = 'user-avatar/user-avatar.php';
	$user_avatar_active = in_array( $user_avatar, apply_filters('active_plugins', get_option('active_plugins')));	
	$OIOpublisher = WP_PLUGIN_DIR . '/oiopub-direct/wp.php';	
	$DAPLiveLinks = 'DAP-WP-LiveLinks/DAP-WP-LiveLinks.php';
	$DAPLiveLinks_active = in_array( $DAPLiveLinks, apply_filters('active_plugins', get_option('active_plugins')));
	$easy_pagination = WP_PLUGIN_DIR . '/easy-pagination/images/thumbnail.php';
	$itheme2_theme = wp_get_theme( 'itheme2' );
	$smoothv41_theme = wp_get_theme( 'SmoothV4.1' );

	if ( $pdf_viewer_active == 1 || is_plugin_active_for_network( $pdf_viewer ) ) {
		if ( ! preg_match( '/viewer\\\.html/', $bps_customcode_rfi ) ) {
			$autofix_message = 1;
			$debug_RFI .= __('CC Root Text Box 11: PDF Viewer (Envigeek Web Services) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $marmoset_viewer_active == 1 || is_plugin_active_for_network( $marmoset_viewer ) ) {
		if ( ! preg_match( '/mviewer\\\.php/', $bps_customcode_rfi ) ) {
			$autofix_message = 1;
			$debug_RFI .= __('CC Root Text Box 11: Marmoset Viewer Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $pdf_viewer_themencode_active == 1 || is_plugin_active_for_network( $pdf_viewer_themencode ) ) {
		if ( ! preg_match( '/themencode-pdf-viewer-sc/', $bps_customcode_rfi ) ) {
			$autofix_message = 1;
			$debug_RFI .= __('CC Root Text Box 11: PDF viewer for WordPress (ThemeNcode code canyon) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $jupdf_pdf_viewer_active == 1 || is_plugin_active_for_network( $jupdf_pdf_viewer ) ) {
		if ( ! preg_match( '/jupdf\/index\\\.html/', $bps_customcode_rfi ) ) {
			$autofix_message = 1;
			$debug_RFI .= __('CC Root Text Box 11: jupdf pdf viewer Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $userPro_active == 1 || is_plugin_active_for_network( $userPro ) ) {
		if ( ! preg_match( '/instagramAuth\\\.php\|linkedinAuth\\\.php/', $bps_customcode_rfi ) ) {
			$autofix_message = 1;
			$debug_RFI .= __('CC Root Text Box 11: UserPro (code canyon) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $NativeChurch_theme->exists() ) {
		if ( ! preg_match( '/download\\\.php/', $bps_customcode_rfi ) ) {
			$autofix_message = 1;
			$debug_RFI .= __('CC Root Text Box 11: NativeChurch Theme', 'bulletproof-security').'<br>';
		}		
	}
	if ( $user_avatar_active == 1 || is_plugin_active_for_network( $user_avatar ) ) {
		if ( ! preg_match( '/user-avatar-pic\\\.php/', $bps_customcode_rfi ) ) {
			$autofix_message = 1;
			$debug_RFI .= __('CC Root Text Box 11: User Avatar (CTLT DEV) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( file_exists($OIOpublisher) ) {
		if ( ! preg_match( '/go\\\.php\|purchase\\\.php\|bubble\\\.js\|oiopub\\\.js/', $bps_customcode_rfi ) ) {
			$autofix_message = 1;
			$debug_RFI .= __('CC Root Text Box 11: OIOpublisher Ad Manager Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $DAPLiveLinks_active == 1 || is_plugin_active_for_network( $DAPLiveLinks ) ) {
		if ( ! preg_match( '/authenticate\\\.php\|signup_submit\\\.php/', $bps_customcode_rfi ) ) {
			$autofix_message = 1;
			$debug_RFI .= __('CC Root Text Box 11: Digital Access Pass (DAP) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( file_exists($easy_pagination) ) {
		if ( ! preg_match( '/thumbnail\\\.php/', $bps_customcode_rfi ) ) {
			$autofix_message = 1;
			$debug_RFI .= __('CC Root Text Box 11: Easy Pagination (code canyon) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $itheme2_theme->exists() ) {
		if ( ! preg_match( '/img\\\.php/', $bps_customcode_rfi ) ) {
			$autofix_message = 1;
			$debug_RFI .= __('CC Root Text Box 11: iTheme2 Theme', 'bulletproof-security').'<br>';
		}		
	}
	if ( $smoothv41_theme->exists() ) {
		if ( ! preg_match( '/thumbnail\\\.php/', $bps_customcode_rfi ) ) {
			$autofix_message = 1;
			$debug_RFI .= __('CC Root Text Box 11: SmoothV4.1 Theme', 'bulletproof-security').'<br>';
		}		
	}

	## 12. CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS
	$bps_customcode_bpsqse = htmlspecialchars_decode( $CC_Options_root['bps_customcode_bpsqse'], ENT_QUOTES );
	$debug_BPSQSE = '';

	$woo_PagSeguro = 'woocommerce-pagseguro/woocommerce-pagseguro.php';
	$woo_PagSeguro_active = in_array( $woo_PagSeguro, apply_filters('active_plugins', get_option('active_plugins')));
	$event_espresso1 = WP_PLUGIN_DIR . '/event-espresso-decaf/espresso.php';
	$event_espresso2 = WP_PLUGIN_DIR . '/event-espresso-free/espresso.php';
	$event_espresso3 = WP_PLUGIN_DIR . '/event-espresso/espresso.php';
	$event_espresso4 = WP_PLUGIN_DIR . '/event-espresso-core-master/espresso.php';
	$woo_serial_key = 'woocommerce-serial-key/serial-key.php';
	$woo_serial_key_active = in_array( $woo_serial_key, apply_filters('active_plugins', get_option('active_plugins')));	
	$woo_worldpay = 'woocommerce/woocommerce.php';
	$woo_worldpay_active = in_array( $woo_worldpay, apply_filters('active_plugins', get_option('active_plugins')));	
	$kama_click_counter = 'kama-clic-counter/kama_click_counter.php';
	$kama_click_counter_active = in_array( $kama_click_counter, apply_filters('active_plugins', get_option('active_plugins')));
	$riva_slider_pro = 'riva-slider-pro/setup.php';
	$riva_slider_pro_active = in_array( $riva_slider_pro, apply_filters('active_plugins', get_option('active_plugins')));
	$wp_auto_spinner = 'wp-auto-spinner/wp-auto-spinner.php';
	$wp_auto_spinner_active = in_array( $wp_auto_spinner, apply_filters('active_plugins', get_option('active_plugins')));
	$AgriTurismo_theme = wp_get_theme( 'agritourismo-theme' );
	$wccp_pro = 'wccp-pro/preventer-index.php';
	$wccp_pro_active = in_array( $wccp_pro, apply_filters('active_plugins', get_option('active_plugins')));
	$panopress = 'panopress/panopress.php';
	$panopress_active = in_array( $panopress, apply_filters('active_plugins', get_option('active_plugins')));
	$essb_code_canyon = 'easy-social-share-buttons3/easy-social-share-buttons3.php';
	$essb_code_canyon_active = in_array( $essb_code_canyon, apply_filters('active_plugins', get_option('active_plugins')));
	$mainwp = 'mainwp/mainwp.php';
	$mainwp_active = in_array( $mainwp, apply_filters('active_plugins', get_option('active_plugins')));
	$clevercourse_theme = wp_get_theme( 'clevercourse' );
	$wp_estore = 'wp-cart-for-digital-products/wp_cart_for_digital_products.php';
	$wp_estore_active = in_array( $wp_estore, apply_filters('active_plugins', get_option('active_plugins')));
	$wp_emember = 'wp-eMember/wp_eMember.php';
	$wp_emember_active = in_array( $wp_emember, apply_filters('active_plugins', get_option('active_plugins')));
	$easy_digital_downloads = 'easy-digital-downloads/easy-digital-downloads.php';
	$easy_digital_downloads_active = in_array( $easy_digital_downloads, apply_filters('active_plugins', get_option('active_plugins')));
	$mailpoet = 'wysija-newsletters/index.php';
	$mailpoet_active = in_array( $mailpoet, apply_filters('active_plugins', get_option('active_plugins')));
	$mailchimp = 'mailchimp-for-wp/mailchimp-for-wp.php';
	$mailchimp_active = in_array( $mailchimp, apply_filters('active_plugins', get_option('active_plugins')));
	$DAPLiveLinks = 'DAP-WP-LiveLinks/DAP-WP-LiveLinks.php';
	$DAPLiveLinks_active = in_array( $DAPLiveLinks, apply_filters('active_plugins', get_option('active_plugins')));
	$wp_newsletter = 'wp-mailinglist/wp-mailinglist.php';
	$wp_newsletter_active = in_array( $wp_newsletter, apply_filters('active_plugins', get_option('active_plugins')));
	$sctocr = 'subscribe-to-comments-reloaded/subscribe-to-comments-reloaded.php';
	$sctocr_active = in_array( $sctocr, apply_filters('active_plugins', get_option('active_plugins')));
	$nextend_social_login = 'nextend-facebook-connect/nextend-facebook-connect.php';
	$nextend_social_login_active = in_array( $nextend_social_login, apply_filters('active_plugins', get_option('active_plugins')));
	$business_directory_plugin = 'business-directory-plugin/business-directory-plugin.php';
	$business_directory_plugin_active = in_array( $business_directory_plugin, apply_filters('active_plugins', get_option('active_plugins')));	
	$constant_contact_woocommerce_plugin = 'constant-contact-woocommerce/plugin.php';
	$constant_contact_woocommerce_plugin_active = in_array( $constant_contact_woocommerce_plugin, apply_filters('active_plugins', get_option('active_plugins')));
	$constant_contact_forms_plugin = 'constant-contact-forms/constant-contact-forms.php';
	$constant_contact_forms_plugin_active = in_array( $constant_contact_forms_plugin, apply_filters('active_plugins', get_option('active_plugins')));

	## BPSQSE RegEx Patterns
	// 3 variations for both UA rules below: only java, java and curl, java, curl and wget
	$useragent1_j = '/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(havij\|libwww-perl\|(.*)python\|nikto\|(.*)scan\|winhttp\|clshttp\|loader\)\s\[NC,OR\]/';
	$useragent1_jc = '/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(havij\|libwww-perl\|(.*)python\|nikto\|scan\|winhttp\|clshttp\|loader\)\s\[NC,OR\]/';	
	$useragent1_jcw = '/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(havij\|libwww-perl\|python\|nikto\|scan\|winhttp\|clshttp\|loader\)\s\[NC,OR\]/';	
	$useragent2_j = '/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(;\|\<\|\>\|\'\|\"\|(.*)libwww-perl\|(.*)python\|nikto\|(.*)scan\|winhttp\|(.*)miner\)\s\[NC,OR\]/';
	$useragent2_jc = '/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(;\|\<\|\>\|\'\|\"\|(.*)libwww-perl\|(.*)python\|nikto\|scan\|winhttp\|(.*)miner\)\s\[NC,OR\]/';	
	$useragent2_jcw = '/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(;\|\<\|\>\|\'\|\"\|(.*)libwww-perl\|python\|nikto\|scan\|winhttp\|(.*)miner\)\s\[NC,OR\]/';	
	
	$marker1 = '/BPS\sAutoWhitelist\sQS1/'; // RewriteCond %{HTTP_REFERER} (%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
	$marker2 = '/BPS\sAutoWhitelist\sQS2/';	// RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(http|https):// [NC,OR]
	$marker3 = '/BPS\sAutoWhitelist\sQS3/';	// RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=/([a-z0-9_.]//?)+ [NC,OR]
	$marker4 = '/BPS\sAutoWhitelist\sQS4/'; // RewriteCond %{QUERY_STRING} (http|https)\: [NC,OR]
	$marker5 = '/BPS\sAutoWhitelist\sQS5/'; // RewriteCond %{QUERY_STRING} ^.*(\(|\)|<|>|%3c|%3e).* [NC,OR]

	$query_string1 = '/RewriteCond\s%\{HTTP_USER_AGENT\}\s\(%0A\|%0D\|%3C\|%3E\|%00\)\s\[NC,OR\]/'; // single quote removed	
	// $referer = '/RewriteCond\s%\{HTTP_REFERER\}\s\(%0A\|%0D\|%3C\|%3E\|%00\)\s\[NC,OR\]/'; // single quote removed & QS1 but don't check this.
	// $query_string2 = '/RewriteCond\s%\{QUERY_STRING\}\s\^\.\*\(\<\|\>\|%3c\|%3e\)\.\*\s\[NC,OR\]/'; // round brackets removed & QS5 but don't check this.
	$query_string2 = '/RewriteCond\s%\{QUERY_STRING\}\s\(<\|>\|%0A\|%0D\|%3C\|%3E\|%00\)\s\[NC,OR\]/'; // single quote removed
	$query_string_sql = '/RewriteCond\s%\{QUERY_STRING\}\s\(;\|\<\|\>\|\'\|(.*)\|alter\|declare\|script\|set\|md5\|benchmark\|encode\)\s\[NC,OR\]/';	

	if ( $woo_PagSeguro_active == 1 || is_plugin_active_for_network( $woo_PagSeguro ) ) {
		if ( ! preg_match( $useragent1_j, $bps_customcode_bpsqse ) || ! preg_match( $useragent2_j, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: WooCommerce PagSeguro Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( file_exists($event_espresso1) || file_exists($event_espresso2) || file_exists($event_espresso3) || file_exists($event_espresso4) ) {
		if ( ! preg_match( $marker1, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: Event Espresso Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $woo_serial_key_active == 1 || is_plugin_active_for_network( $woo_serial_key ) ) {
		if ( ! preg_match( $marker2, $bps_customcode_bpsqse ) || ! preg_match( $marker3, $bps_customcode_bpsqse ) || ! preg_match( $marker4, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: WooCommerce Serial Key Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $woo_worldpay_active == 1 || is_plugin_active_for_network( $woo_worldpay ) ) {
		if ( ! preg_match( $useragent1_j, $bps_customcode_bpsqse ) || ! preg_match( $useragent2_j, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: WooCommerce WorldPay Extension', 'bulletproof-security').'<br>';
		}		
	}
	if ( $kama_click_counter_active == 1 || is_plugin_active_for_network( $kama_click_counter ) ) {
		if ( ! preg_match( $marker2, $bps_customcode_bpsqse ) || ! preg_match( $marker3, $bps_customcode_bpsqse ) || ! preg_match( $marker4, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: Kama Click Counter Plugin', 'bulletproof-security').'<br>';
		}
	}
	if ( $riva_slider_pro_active == 1 || is_plugin_active_for_network( $riva_slider_pro ) ) {
		if ( ! preg_match( $marker2, $bps_customcode_bpsqse ) || ! preg_match( $marker3, $bps_customcode_bpsqse ) || ! preg_match( $marker4, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: Riva Slider Pro Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $wp_auto_spinner_active == 1 || is_plugin_active_for_network( $wp_auto_spinner ) ) {
		if ( ! preg_match( $useragent1_jc, $bps_customcode_bpsqse ) || ! preg_match( $useragent2_jc, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: WordPress Auto Spinner Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $AgriTurismo_theme->exists() ) {
		if ( ! preg_match( $marker5, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: AgriTurismo Theme', 'bulletproof-security').'<br>';
		}		
	}
	if ( $wccp_pro_active == 1 || is_plugin_active_for_network( $wccp_pro ) ) {
		if ( ! preg_match( $marker2, $bps_customcode_bpsqse ) || ! preg_match( $marker3, $bps_customcode_bpsqse ) || ! preg_match( $marker4, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: WP Content Copy Protection Plugin', 'bulletproof-security').'<br>';
		}
	}
	if ( $panopress_active == 1 || is_plugin_active_for_network( $panopress ) ) {
		if ( ! preg_match( $marker2, $bps_customcode_bpsqse ) || ! preg_match( $marker3, $bps_customcode_bpsqse ) || ! preg_match( $marker4, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: PanoPress Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $essb_code_canyon_active == 1 || is_plugin_active_for_network( $essb_code_canyon ) ) {
		if ( ! preg_match( $marker2, $bps_customcode_bpsqse ) || ! preg_match( $marker3, $bps_customcode_bpsqse ) || ! preg_match( $marker4, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: Easy Social Share Buttons (Code Canyon) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $mainwp_active == 1 || is_plugin_active_for_network( $mainwp ) ) {
		if ( ! preg_match( $query_string_sql, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: MainWP Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $clevercourse_theme->exists() ) {
		if ( ! preg_match( $marker2, $bps_customcode_bpsqse ) || ! preg_match( $marker3, $bps_customcode_bpsqse ) || ! preg_match( $marker4, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: Clever Course Theme', 'bulletproof-security').'<br>';
		}		
	}
	if ( $wp_estore_active == 1 || is_plugin_active_for_network( $wp_estore ) ) {
		if ( ! preg_match( $useragent1_jc, $bps_customcode_bpsqse ) || ! preg_match( $useragent2_jc, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: WP eStore (WP Cart for Digital Products) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $wp_emember_active == 1 || is_plugin_active_for_network( $wp_emember ) ) {
		if ( ! preg_match( $useragent1_jc, $bps_customcode_bpsqse ) || ! preg_match( $useragent2_jc, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: WP eMember Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $easy_digital_downloads_active == 1 || is_plugin_active_for_network( $easy_digital_downloads ) ) {
		if ( ! preg_match( $marker2, $bps_customcode_bpsqse ) || ! preg_match( $marker3, $bps_customcode_bpsqse ) || ! preg_match( $marker4, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: Easy Digital Downloads Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $mailpoet_active == 1 || is_plugin_active_for_network( $mailpoet ) ) {
		if ( ! preg_match( $useragent1_jcw, $bps_customcode_bpsqse ) || ! preg_match( $useragent2_jcw, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: MailPoet Newsletters (wysija newsletters) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $mailchimp_active == 1 || is_plugin_active_for_network( $mailchimp ) ) {
		if ( ! preg_match( $query_string1, $bps_customcode_bpsqse ) || ! preg_match( $query_string2, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: MailChimp for WordPress Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $DAPLiveLinks_active == 1 || is_plugin_active_for_network( $DAPLiveLinks ) ) {
		if ( ! preg_match( $marker2, $bps_customcode_bpsqse ) || ! preg_match( $marker3, $bps_customcode_bpsqse ) || ! preg_match( $marker4, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: Digital Access Pass (DAP) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $wp_newsletter_active == 1 || is_plugin_active_for_network( $wp_newsletter ) ) {
		if ( ! preg_match( $useragent1_jcw, $bps_customcode_bpsqse ) || ! preg_match( $useragent2_jcw, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: WordPress Newsletter (tribulant) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $sctocr_active == 1 || is_plugin_active_for_network( $sctocr ) ) {
		if ( ! preg_match( $marker2, $bps_customcode_bpsqse ) || ! preg_match( $marker3, $bps_customcode_bpsqse ) || ! preg_match( $marker4, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: Subscribe To Comments Reloaded Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $nextend_social_login_active == 1 || is_plugin_active_for_network( $nextend_social_login ) ) {
		if ( ! preg_match( $marker2, $bps_customcode_bpsqse ) || ! preg_match( $marker3, $bps_customcode_bpsqse ) || ! preg_match( $marker4, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: Nextend Social Login Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $business_directory_plugin_active == 1 || is_plugin_active_for_network( $business_directory_plugin ) ) {
		if ( ! preg_match( $marker2, $bps_customcode_bpsqse ) || ! preg_match( $marker3, $bps_customcode_bpsqse ) || ! preg_match( $marker4, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: Business Directory Plugin', 'bulletproof-security').'<br>';
		}		
	}	
	if ( $constant_contact_woocommerce_plugin_active == 1 || is_plugin_active_for_network( $constant_contact_woocommerce_plugin ) ) {
		if ( ! preg_match( $marker2, $bps_customcode_bpsqse ) || ! preg_match( $marker3, $bps_customcode_bpsqse ) || ! preg_match( $marker4, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: Constant Contact + WooCommerce Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $constant_contact_forms_plugin_active == 1 || is_plugin_active_for_network( $constant_contact_forms_plugin ) ) {
		if ( ! preg_match( $marker2, $bps_customcode_bpsqse ) || ! preg_match( $marker3, $bps_customcode_bpsqse ) || ! preg_match( $marker4, $bps_customcode_bpsqse ) ) {
			$autofix_message = 1;
			$debug_BPSQSE .= __('CC Root Text Box 12: Constant Contact Forms Plugin', 'bulletproof-security').'<br>';
		}		
	}

	## 3. CUSTOM CODE WPADMIN PLUGIN/FILE SKIP RULES
	$bps_customcode_two_wpa = htmlspecialchars_decode( $CC_Options_wpadmin['bps_customcode_two_wpa'], ENT_QUOTES );
	$debug_wpadmin_PSB = '';
	
	$woo_pfeed_pro = 'webappick-product-feed-for-woocommerce-pro/webappick-product-feed-for-woocommerce-pro.php';
	$woo_pfeed_pro_active = in_array( $woo_pfeed_pro, apply_filters('active_plugins', get_option('active_plugins')));	
	$visual_composer = 'js_composer/js_composer.php';
	$visual_composer_active = in_array( $visual_composer, apply_filters('active_plugins', get_option('active_plugins')));
	$bookly_booking = 'appointment-booking/main.php';
	$bookly_booking_active = in_array( $bookly_booking, apply_filters('active_plugins', get_option('active_plugins')));
	$beaver_builder = 'bb-plugin/fl-builder.php';
	$beaver_builder_active = in_array( $beaver_builder, apply_filters('active_plugins', get_option('active_plugins')));
	$wp_reset = 'wp-reset/wp-reset.php';
	$wp_reset_active = in_array( $wp_reset, apply_filters('active_plugins', get_option('active_plugins')));
	$emg_pro = 'easy-media-gallery-pro/easy-media-gallery-pro.php';
	$emg_pro_active = in_array( $emg_pro, apply_filters('active_plugins', get_option('active_plugins')));
	$nextgen_gallery = 'nextgen-gallery/nggallery.php';
	$nextgen_gallery_active = in_array( $nextgen_gallery, apply_filters('active_plugins', get_option('active_plugins')));
	$OptimizePress_theme = wp_get_theme( 'optimizePressTheme' );
	$wp_checkout = 'wp-checkout/wp-checkout.php';
	$wp_checkout_active = in_array( $wp_checkout, apply_filters('active_plugins', get_option('active_plugins')));
	$video_showcase = 'videoshowcase/videoshowcase.php';
	$video_showcase_active = in_array( $video_showcase, apply_filters('active_plugins', get_option('active_plugins')));
	$wp_invoice = 'wp-invoice/wp-invoice.php';
	$wp_invoice_active = in_array( $wp_invoice, apply_filters('active_plugins', get_option('active_plugins')));
	$yoast_seo = 'wordpress-seo/wp-seo.php';
	$yoast_seo_active = in_array( $yoast_seo, apply_filters('active_plugins', get_option('active_plugins')));
	$formidable_pro = WP_PLUGIN_DIR . '/formidable/pro/formidable-pro.php';
	$google_typography = 'google-typography/google-typography.php';
	$google_typography_active = in_array( $google_typography, apply_filters('active_plugins', get_option('active_plugins')));
	$flare = 'flare/flare.php';
	$flare_active = in_array( $flare, apply_filters('active_plugins', get_option('active_plugins')));
	$bbPress = 'bbpress/bbpress.php';
	$bbPress_active = in_array( $bbPress, apply_filters('active_plugins', get_option('active_plugins')));
	$spider_calendar = 'spider-event-calendar/calendar.php';
	$spider_calendar_active = in_array( $spider_calendar, apply_filters('active_plugins', get_option('active_plugins')));
	$buddypress = 'buddypress/bp-loader.php';
	$buddypress_active = in_array( $buddypress, apply_filters('active_plugins', get_option('active_plugins')));
	$wpml_transman = 'wpml-translation-management/plugin.php';
	$wpml_transman_active = in_array( $wpml_transman, apply_filters('active_plugins', get_option('active_plugins')));
	$events_manager = 'events-manager/events-manager.php';
	$events_manager_active = in_array( $events_manager, apply_filters('active_plugins', get_option('active_plugins')));
	$mailpoet = 'wysija-newsletters/index.php';
	$mailpoet_active = in_array( $mailpoet, apply_filters('active_plugins', get_option('active_plugins')));
	$event_espresso1 = WP_PLUGIN_DIR . '/event-espresso-decaf/espresso.php';
	$event_espresso2 = WP_PLUGIN_DIR . '/event-espresso-free/espresso.php';
	$event_espresso3 = WP_PLUGIN_DIR . '/event-espresso/espresso.php';
	$event_espresso4 = WP_PLUGIN_DIR . '/event-espresso-core-master/espresso.php';
	$content_egg = 'content-egg/content-egg.php';
	$content_egg_active = in_array( $content_egg, apply_filters('active_plugins', get_option('active_plugins')));
	$flatsome_theme = wp_get_theme( 'flatsome' );
	$bloom = 'bloom/bloom.php';
	$bloom_active = in_array( $bloom, apply_filters('active_plugins', get_option('active_plugins')));	

	## wp-admin plugin skip/bypass RegEx patterns
	$post_psb = '/RewriteCond\s%{REQUEST_URI}\s\(post\\\.php\)\s\[NC\]/';
	$admin_ajax_psb = '/RewriteCond\s%{REQUEST_URI}\s\(admin-ajax\\\.php\)\s\[NC\]/';
	
	if ( $woo_pfeed_pro_active == 1 || is_plugin_active_for_network( $woo_pfeed_pro ) ) {
		if ( ! preg_match( '/RewriteCond\s%{QUERY_STRING}\spage=woo_feed_manage_feed\(\.\*\)\s\[NC\]/', $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: WooCommerce Product Feed Pro Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $visual_composer_active == 1 || is_plugin_active_for_network( $visual_composer ) ) {
		if ( ! preg_match( $post_psb, $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: WPBakery Visual Composer Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $bookly_booking_active == 1 || is_plugin_active_for_network( $bookly_booking ) ) {
		if ( ! preg_match( $admin_ajax_psb, $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: Bookly Booking Plugin', 'bulletproof-security').'<br>';
		}
	}
	if ( $beaver_builder_active == 1 || is_plugin_active_for_network( $beaver_builder ) ) {
		if ( ! preg_match( $admin_ajax_psb, $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: Beaver Builder Plugin', 'bulletproof-security').'<br>';
		}
	}
	if ( $wp_reset_active == 1 || is_plugin_active_for_network( $wp_reset ) ) {
		if ( ! preg_match( $admin_ajax_psb, $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: WP Reset Plugin', 'bulletproof-security').'<br>';
		}
	}
	if ( $emg_pro_active == 1 || is_plugin_active_for_network( $emg_pro ) ) {
		if ( ! preg_match( $admin_ajax_psb, $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: Easy Media Gallery Pro Plugin', 'bulletproof-security').'<br>';
		}
	}
	if ( $nextgen_gallery_active == 1 || is_plugin_active_for_network( $nextgen_gallery ) ) {
		if ( ! preg_match( '/RewriteCond\s%{QUERY_STRING}\spage=nggallery-manage-gallery\(\.\*\)\s\[NC\]/', $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: NextGen Gallery Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $OptimizePress_theme->exists() ) {
		if ( ! preg_match( '/RewriteCond\s%{QUERY_STRING}\spage=optimizepress-page-builder\(\.\*\)\s\[NC\]/', $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: OptimizePress Theme', 'bulletproof-security').'<br>';
		}		
	}
	if ( $wp_checkout_active == 1 || is_plugin_active_for_network( $wp_checkout ) ) {
		if ( ! preg_match( $admin_ajax_psb, $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: tribulant Shopping Cart (WP Checkout) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $video_showcase_active == 1 || is_plugin_active_for_network( $video_showcase ) ) {
		if ( ! preg_match( $admin_ajax_psb, $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: ithemes Video Showcase Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $wp_invoice_active == 1 || is_plugin_active_for_network( $wp_invoice ) ) {
		if ( ! preg_match( '/RewriteCond\s%{QUERY_STRING}\spage=wpi_\(\.\*\)\s\[NC\]/', $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: WP-Invoice - Web Invoice and Billing Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $yoast_seo_active == 1 || is_plugin_active_for_network( $yoast_seo ) ) {
		if ( ! preg_match( '/RewriteCond\s%{QUERY_STRING}\spage=wpseo_social&key=\(\.\*\)\s\[NC\]/', $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: Yoast SEO Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( file_exists($formidable_pro) ) {
		if ( ! preg_match( '/RewriteCond\s%{QUERY_STRING}\splugin=formidable&controller=settings\(\.\*\)\s\[NC\]/', $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: Formidable Pro Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $google_typography_active == 1 || is_plugin_active_for_network( $google_typography ) ) {
		if ( ! preg_match( $admin_ajax_psb, $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: Google Typography Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $flare_active == 1 || is_plugin_active_for_network( $flare ) ) {
		if ( ! preg_match( $admin_ajax_psb, $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: Flare Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $bbPress_active == 1 || is_plugin_active_for_network( $bbPress ) ) {
		if ( ! preg_match( $post_psb, $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: bbPress Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $spider_calendar_active == 1 || is_plugin_active_for_network( $spider_calendar ) ) {
		if ( ! preg_match( $admin_ajax_psb, $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: Spider Event Calendar (WordPress Event Calendar) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $buddypress_active == 1 || is_plugin_active_for_network( $buddypress ) ) {
		$bp_active_components = bp_get_option( 'bp-active-components' );
		foreach ( $bp_active_components as $key => $value ) {
			if ( $key == 'messages' ) {
				if ( ! preg_match( $admin_ajax_psb, $bps_customcode_two_wpa ) ) {
					$autofix_message = 1;
					$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: BuddyPress Plugin', 'bulletproof-security').'<br>';
				}
			}
		}		
	}
	if ( $wpml_transman_active == 1 || is_plugin_active_for_network( $wpml_transman ) ) {
		if ( ! preg_match( '/RewriteCond\s%{QUERY_STRING}\spage=wpml-translation-management\(\.\*\)\s\[NC\]/', $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: WPML Translation Management Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $events_manager_active == 1 || is_plugin_active_for_network( $events_manager ) ) {
		if ( ! preg_match( $admin_ajax_psb, $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: Events Manager Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $mailpoet_active == 1 || is_plugin_active_for_network( $mailpoet ) ) {
		if ( ! preg_match( $admin_ajax_psb, $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: MailPoet Newsletters (wysija newsletters) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( file_exists($event_espresso1) || file_exists($event_espresso2) || file_exists($event_espresso3) || file_exists($event_espresso4) ) {
		if ( ! preg_match( '/RewriteCond\s%{REQUEST_URI}\s\(admin\\\.php\)\s\[NC\]/', $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: Event Espresso Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $content_egg_active == 1 || is_plugin_active_for_network( $content_egg ) ) {
		if ( ! preg_match( $admin_ajax_psb, $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: Content Egg (Free and Pro) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $flatsome_theme->exists() ) {
		if ( ! preg_match( '/RewriteCond\s%{REQUEST_URI}\s\(customize\\\.php\)\s\[NC\]/', $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: Flatsome Theme', 'bulletproof-security').'<br>';
		}		
	}
	if ( $bloom_active == 1 || is_plugin_active_for_network( $bloom ) ) {
		if ( ! preg_match( '/RewriteCond\s%{QUERY_STRING}\soption_page=et_dashboard\(\.\*\)\s\[NC\]/', $bps_customcode_two_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_PSB .= __('CC wp-admin Text Box 3: Bloom Email Opt-in Plugin', 'bulletproof-security').'<br>';
		}		
	}

	## 4. CUSTOM CODE BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS
	$bps_customcode_bpsqse_wpa = htmlspecialchars_decode( $CC_Options_wpadmin['bps_customcode_bpsqse_wpa'], ENT_QUOTES );
	$debug_wpadmin_BPSQSE = '';

	$content_egg = 'content-egg/content-egg.php';
	$content_egg_active = in_array( $content_egg, apply_filters('active_plugins', get_option('active_plugins')));
	$event_espresso1 = WP_PLUGIN_DIR . '/event-espresso-decaf/espresso.php';
	$event_espresso2 = WP_PLUGIN_DIR . '/event-espresso-free/espresso.php';
	$event_espresso3 = WP_PLUGIN_DIR . '/event-espresso/espresso.php';
	$event_espresso4 = WP_PLUGIN_DIR . '/event-espresso-core-master/espresso.php';
	$owa_plugin = 'owa/wp_plugin.php';
	$owa_plugin_active = in_array( $owa_plugin, apply_filters('active_plugins', get_option('active_plugins')));
	$uberGrid = 'uber-grid/uber-grid.php';
	$uberGrid_active = in_array( $uberGrid, apply_filters('active_plugins', get_option('active_plugins')));
	$jetpack = 'jetpack/jetpack.php';
	$jetpack_active = in_array( $jetpack, apply_filters('active_plugins', get_option('active_plugins')));
	$restrict_content_pro = 'restrict-content-pro/restrict-content-pro.php';
	$restrict_content_pro_active = in_array( $restrict_content_pro, apply_filters('active_plugins', get_option('active_plugins')));
	$link_whisper = 'link-whisper/link-whisper.php';
	$link_whisper_active = in_array( $link_whisper, apply_filters('active_plugins', get_option('active_plugins')));
	$link_whisper_premium = 'link-whisper-premium/link-whisper.php';
	$link_whisper_premium_active = in_array( $link_whisper_premium, apply_filters('active_plugins', get_option('active_plugins')));
	$convert_pro = 'convertpro/convertpro.php';
	$convert_pro_active = in_array( $convert_pro, apply_filters('active_plugins', get_option('active_plugins')));
	$wp_mail_smtp = 'wp-mail-smtp/wp_mail_smtp.php';
	$wp_mail_smtp_active = in_array( $wp_mail_smtp, apply_filters('active_plugins', get_option('active_plugins')));	
	$gmail_smtp = 'gmail-smtp/main.php';
	$gmail_smtp_active = in_array( $gmail_smtp, apply_filters('active_plugins', get_option('active_plugins')));	
	$bit_integrations = 'bit-integrations/bitwpfi.php';
	$bit_integrations_active = in_array( $bit_integrations, apply_filters('active_plugins', get_option('active_plugins')));
	$piotnetforms = 'piotnetforms/piotnetforms.php';
	$piotnetforms_active = in_array( $piotnetforms, apply_filters('active_plugins', get_option('active_plugins')));
	$post_smtp_mailer = 'post-smtp/postman-smtp.php';
	$post_smtp_mailer_active = in_array( $post_smtp_mailer, apply_filters('active_plugins', get_option('active_plugins')));
	$product_feed_manager = 'best-woocommerce-feed/rex-product-feed.php';
	$product_feed_manager_active = in_array( $product_feed_manager, apply_filters('active_plugins', get_option('active_plugins')));
	$product_feed_manager_pro = 'best-woocommerce-feed-pro/rex-product-feed-pro.php';
	$product_feed_manager_pro_active = in_array( $product_feed_manager_pro, apply_filters('active_plugins', get_option('active_plugins')));

	## wp-admin BPSQSE RegEx patterns
	$marker_wpadmin1 = '/BPS\sAutoWhitelist\sQS1/'; // Primary SQL Injection rule
	$marker_wpadmin2 = '/BPS\sAutoWhitelist\sQS2/';	// RewriteCond %{HTTP_REFERER} (%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
	$marker_wpadmin3 = '/BPS\sAutoWhitelist\sQS3/';	// RewriteCond %{QUERY_STRING} (<|>|'|%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
	$marker_wpadmin4 = '/BPS\sAutoWhitelist\sQS4/'; // RewriteCond %{QUERY_STRING} ^.*(\(|\)|<|>).* [NC,OR]
	$marker_wpadmin5 = '/BPS\sAutoWhitelist\sQS5/';	// RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(http|https):// [NC,OR]
	$marker_wpadmin6 = '/BPS\sAutoWhitelist\sQS6/';	// RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=/([a-z0-9_.]//?)+ [NC,OR]
	$marker_wpadmin7 = '/BPS\sAutoWhitelist\sQS7/'; // RewriteCond %{QUERY_STRING} (http|https)\: [NC,OR]

	if ( $content_egg_active == 1 || is_plugin_active_for_network( $content_egg ) ) {
		if ( ! preg_match( $marker_wpadmin1, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: Content Egg (Free and Pro) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( file_exists($event_espresso1) || file_exists($event_espresso2) || file_exists($event_espresso3) || file_exists($event_espresso4) ) {
		if ( ! preg_match( $marker_wpadmin2, $bps_customcode_bpsqse_wpa ) || ! preg_match( $marker_wpadmin3, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: Event Espresso Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $owa_plugin_active == 1 || is_plugin_active_for_network( $owa_plugin ) ) {
		if ( ! preg_match( $marker_wpadmin2, $bps_customcode_bpsqse_wpa ) || ! preg_match( $marker_wpadmin3, $bps_customcode_bpsqse_wpa ) 
			|| ! preg_match( $marker_wpadmin4, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: Open Web Analytics (github) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $uberGrid_active == 1 || is_plugin_active_for_network( $uberGrid ) ) {
		if ( ! preg_match( $marker_wpadmin2, $bps_customcode_bpsqse_wpa ) || ! preg_match( $marker_wpadmin3, $bps_customcode_bpsqse_wpa ) 
			|| ! preg_match( $marker_wpadmin4, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: UberGrid (code canyon) Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $jetpack_active == 1 || is_plugin_active_for_network( $jetpack ) ) {
		if ( ! preg_match( $marker_wpadmin5, $bps_customcode_bpsqse_wpa ) || ! preg_match( $marker_wpadmin6, $bps_customcode_bpsqse_wpa ) 
			|| ! preg_match( $marker_wpadmin7, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: Jetpack Plugin - SSO feature', 'bulletproof-security').'<br>';
		}		
	}
	if ( $restrict_content_pro_active == 1 || is_plugin_active_for_network( $restrict_content_pro ) ) {
		if ( ! preg_match( $marker_wpadmin3, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: Restrict Content Pro Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $link_whisper_active == 1 || is_plugin_active_for_network( $link_whisper ) ) {
		if ( ! preg_match( $marker_wpadmin3, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: Link Whisper Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $link_whisper_premium_active == 1 || is_plugin_active_for_network( $link_whisper_premium ) ) {
		if ( ! preg_match( $marker_wpadmin3, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: Link Whisper Premium Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $convert_pro_active == 1 || is_plugin_active_for_network( $convert_pro ) ) {
		if ( ! preg_match( $marker_wpadmin4, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: Convert Pro Plugin', 'bulletproof-security').'<br>';
		}		
	}		
	if ( $wp_mail_smtp_active == 1 || is_plugin_active_for_network( $wp_mail_smtp ) ) {
		if ( ! preg_match( $marker_wpadmin5, $bps_customcode_bpsqse_wpa ) || ! preg_match( $marker_wpadmin6, $bps_customcode_bpsqse_wpa ) 
			|| ! preg_match( $marker_wpadmin7, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: WP Mail SMTP Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $gmail_smtp_active == 1 || is_plugin_active_for_network( $gmail_smtp ) ) {
		if ( ! preg_match( $marker_wpadmin5, $bps_customcode_bpsqse_wpa ) || ! preg_match( $marker_wpadmin6, $bps_customcode_bpsqse_wpa ) 
			|| ! preg_match( $marker_wpadmin7, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: Gmail SMTP Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $bit_integrations_active == 1 || is_plugin_active_for_network( $bit_integrations ) ) {
		if ( ! preg_match( $marker_wpadmin5, $bps_customcode_bpsqse_wpa ) || ! preg_match( $marker_wpadmin6, $bps_customcode_bpsqse_wpa ) 
			|| ! preg_match( $marker_wpadmin7, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: Bit Integrations Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $piotnetforms_active == 1 || is_plugin_active_for_network( $piotnetforms ) ) {
		if ( ! preg_match( $marker_wpadmin5, $bps_customcode_bpsqse_wpa ) || ! preg_match( $marker_wpadmin6, $bps_customcode_bpsqse_wpa ) 
			|| ! preg_match( $marker_wpadmin7, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: Piotnetforms Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $post_smtp_mailer_active == 1 || is_plugin_active_for_network( $post_smtp_mailer ) ) {
		if ( ! preg_match( $marker_wpadmin5, $bps_customcode_bpsqse_wpa ) || ! preg_match( $marker_wpadmin6, $bps_customcode_bpsqse_wpa ) 
			|| ! preg_match( $marker_wpadmin7, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: Post SMTP Mailer Plugin', 'bulletproof-security').'<br>';
		}		
	}
	if ( $product_feed_manager_active == 1 || is_plugin_active_for_network( $product_feed_manager ) ) {
		if ( ! preg_match( $marker_wpadmin5, $bps_customcode_bpsqse_wpa ) || ! preg_match( $marker_wpadmin6, $bps_customcode_bpsqse_wpa ) 
			|| ! preg_match( $marker_wpadmin7, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: Product Feed Manager for WooCommerce Plugin', 'bulletproof-security').'<br>';
		}		
	}	
	if ( $product_feed_manager_pro_active == 1 || is_plugin_active_for_network( $product_feed_manager_pro ) ) {
		if ( ! preg_match( $marker_wpadmin5, $bps_customcode_bpsqse_wpa ) || ! preg_match( $marker_wpadmin6, $bps_customcode_bpsqse_wpa ) 
			|| ! preg_match( $marker_wpadmin7, $bps_customcode_bpsqse_wpa ) ) {
			$autofix_message = 1;
			$debug_wpadmin_BPSQSE .= __('CC wp-admin Text Box 4: Product Feed Manager for WooCommerce Pro Plugin', 'bulletproof-security').'<br>';
		}		
	}	

	## Display Setup Wizard AutoFix WP Dashboard message
	if ( $autofix_message == 1 ) {
		$text_wp = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS Setup Wizard AutoFix (AutoWhitelist|AutoSetup|AutoCleanup) Notice', 'bulletproof-security').'</font><br>'.__('One or more of your plugins or your theme requires a BPS Custom Code whitelist rule to be automatically created by the Setup Wizard.', 'bulletproof-security').'<br>'.__('Click this ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="Setup Wizard AutoFix">'.__('Setup Wizard link', 'bulletproof-security').'</a>'.__(' and click the Setup Wizard button to automatically create BPS Custom Code whitelist rules.', 'bulletproof-security').'<br>'.__('This BPS AutoFix check can be turned Off on the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php#bps-tabs-2' ).'" title="Setup Wizard Options">'.__('Setup Wizard Options', 'bulletproof-security').'</a>'.__(' page if you do not want BPS to check for any plugin or theme whitelist rules.', 'bulletproof-security').'<br>'.__('If this Notice does not go away after running the Setup Wizard, use the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/theme-skin/theme-skin.php' ).'" title="BPS UI|UX|AutoFix Debug tool">'.__('BPS UI|UX|AutoFix Debug tool', 'bulletproof-security').'</a>.'.__(' Click the UI|UX Options page Question Mark help button for more information.', 'bulletproof-security').'</div>';
		echo $text_wp;
	}

	## Display Custom Code Text Box Number and Plugin or Theme Name for any detected CC whitelist rules when BPS Pro AutoFix Debug is turned On
	$Debug_options = get_option('bulletproof_security_options_debug');

	if ( $Debug_options['bps_debug'] == 'On' ) {
		echo '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);">';
		echo '<font color="blue"><strong>'.__('BPS AutoFix Debug: Custom Code Text Box Number and Plugin or Theme Name', 'bulletproof-security').'</strong></font><br>';
			
		if ( $debug_RMF == '' && $debug_PTSB == '' && $debug_RFI == '' && $debug_BPSQSE == '' && $debug_wpadmin_PSB == '' && $debug_wpadmin_BPSQSE == '' ) {
			echo __('No Plugin or Theme AutoFix Custom Code Whitelist Rules were found', 'bulletproof-security');
		} else {
			echo $debug_RMF . $debug_PTSB . $debug_RFI . $debug_BPSQSE . $debug_wpadmin_PSB . $debug_wpadmin_BPSQSE;
		}
		echo '</div>';
	}
}

?>