<?php
// Direct calls to this file are Forbidden when core files are not present
if ( ! current_user_can('manage_options') ) { 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}
?>

<!-- force the vertical scrollbar -->
<style>
#wpwrap{min-height:100.1%};
</style>

<div id="bps-container" class="wrap">

<noscript><div id="message" class="updated" style="font-weight:600;font-size:13px;padding:5px;background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><span style="color:blue">BPS Warning: JavaScript is disabled in your Browser</span><br />BPS plugin pages will not display visually correct and all BPS JavaScript functionality will not work correctly.</div></noscript>

<?php 
$ScrollTop_options = get_option('bulletproof_security_options_scrolltop');

if ( isset( $ScrollTop_options['bps_scrolltop'] ) && $ScrollTop_options['bps_scrolltop'] != 'Off' ) {
	
	if ( esc_html($_SERVER['REQUEST_METHOD']) == 'POST' && ! isset( $_POST['Submit-SecLog-Search'] ) || isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true ) {

		bpsPro_Browser_UA_scroll_animation();
	}
}
?>

<h2 class="bps-tab-title"><?php _e('Alerts|Logs|Email Options', 'bulletproof-security'); ?></h2>
<div id="message" class="updated" style="border:1px solid #999;background-color:#000;">

<?php
// General all purpose "Settings Saved." message for forms
if ( current_user_can('manage_options') ) {
if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true ) {
	$text = '<p style="font-size:1em;font-weight:bold;padding:2px 0px 2px 5px;margin:0px -11px 0px -11px;background-color:#dfecf2;-webkit-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);""><font color="green"><strong>'.__('Settings Saved', 'bulletproof-security').'</strong></font></p>';
	echo $text;
	}
}

$bpsSpacePop = '-------------------------------------------------------------';

// Replace ABSPATH = wp-content/plugins
$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
// Replace ABSPATH = wp-content
$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
// Top div echo & bottom div echo
$bps_topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
$bps_bottomDiv = '</p></div>';

// Reset/Recheck Dismiss Notices
if ( isset( $_POST['bpsResetDismissSubmit'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_reset_dismiss_notices' );	  

	$user_id = $current_user->ID;

	echo '<div id="message" class="updated fade" style="color:#000000;font-weight:600;background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

	if ( ! delete_user_meta($user_id, 'bps_ignore_iis_notice') ) {
		$text = __('The Windows IIS Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Windows IIS check is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice') ) {
		$text = __('The Bonus Custom Code: Dismiss All Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: Dismiss All Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_brute_force_login_protection_notice') ) {
		$text = __('The Bonus Custom Code: Brute Force Login Protection Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: Brute Force Login Protection Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_speed_boost_cache_notice') ) {
		$text = __('The Bonus Custom Code: Speed Boost Cache Code Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: Speed Boost Cache Code Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_author_enumeration_notice') ) {
		$text = __('The Bonus Custom Code: Author Enumeration BOT Probe Code Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: Author Enumeration BOT Probe Code Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_xmlrpc_ddos_notice') ) {
		$text = __('The Bonus Custom Code: XML-RPC DDoS Protection Code Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: XML-RPC DDoS Protection Code Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_post_request_attack_notice') ) {
		$text = __('The Bonus Custom Code: POST Request Attack Protection Code Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: POST Request Attack Protection Code Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_sniff_driveby_notice') ) {
		$text = __('The Bonus Custom Code: Mime Sniffing|Drive-by Download Attack Protection Code Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: Mime Sniffing|Drive-by Download Attack Protection Code Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_iframe_clickjack_notice') ) {
		$text = __('The Bonus Custom Code: External iFrame|Clickjacking Protection Code Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Bonus Custom Code: External iFrame|Clickjacking Protection Code Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice') ) {
		$text = __('The PHP|php.ini handler htaccess code check Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The PHP|php.ini handler htaccess code check is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_ignore_safemode_notice') ) {
		$text = __('The Safe Mode HUD Check Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Safe Mode HUD Check is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_ignore_Permalinks_notice') ) {
		$text = __('The Custom Permalinks HUD Check Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Custom Permalinks HUD Check is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_ignore_wpfirewall2_notice') ) {
		$text = __('The WordPress Firewall 2 Plugin Dismiss Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;>'.__('Success! The WordPress Firewall 2 Plugin check is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}	

	if ( ! delete_user_meta($user_id, 'bpsPro_ignore_speed_boost_notice') ) {
		$text = __('The New Improved BPS Speed Boost Cache Code Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The New Improved BPS Speed Boost Cache Code Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_ignore_jtc_lite_notice') ) {
		$text = __('The JTC-Lite New Feature Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The JTC-Lite New Feature Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_ignore_rate_notice') ) {
		$text = __('The BPS Plugin Star Rating Request Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The BPS Plugin Star Rating Request Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bpsPro_ignore_mod_security_notice') ) {
		$text = __('The Mod Security Module is Loaded|Enabled Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Mod Security Module is Loaded|Enabled Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bpsPro_ignore_gdpr_compliance_notice') ) {
		$text = __('The GDPR Compliance Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The GDPR Compliance Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bps_ignore_root_version_check_notice') ) {
		$text = __('The Root htaccess File Version Check Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Root htaccess File Version Check Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bpsPro_ignore_mu_wp_automatic_updates_notice') ) {
		$text = __('The BPS wp-config.php file WP Automatic Update constants detected Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The BPS wp-config.php file WP Automatic Update constants detected Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bpsPro_hud_owner_uid_check_notice') ) {
		$text = __('The Script|File Owner User ID Mismatch Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The Script|File Owner User ID Mismatch Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bpsPro_ignore_bpspro_sale_notice') ) {
		$text = __('The BPS Pro Sale Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The BPS Pro Sale Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	if ( ! delete_user_meta($user_id, 'bpsPro_hud_new_feature_notice') ) {
		$text = __('The New Feature Notice is NOT set. Nothing to reset.', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<span style="color:#008000;">'.__('Success! The New Feature Notice is reset.', 'bulletproof-security').'</span><br>';
		echo $text;
	}

	echo '<div class="bps-message-button" style="width:90px;margin-bottom:9px;"><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/email-log-settings/email-log-settings.php' ).'">'.__('Refresh Status', 'bulletproof-security').'</a></div>';
	echo '</p></div>';
	}

?>
</div>

<!-- jQuery UI Tab Menu -->
<div id="bps-tabs" class="bps-menu">
    <div id="bpsHead"><img src="<?php echo plugins_url('/bulletproof-security/admin/images/bps-plugin-logo.jpg'); ?>" /></div>
		<ul>
			<li><a href="#bps-tabs-1"><?php _e('Alerts|Logs|Email Options', 'bulletproof-security'); ?></a></li>
			<li><a href="#bps-tabs-2"><?php _e('Help &amp; FAQ', 'bulletproof-security'); ?></a></li>
		</ul>
            
<div id="bps-tabs-1" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 10px 0px;"><?php _e('Alerts|Logs|Email Options', 'bulletproof-security'); ?>  <button id="bps-open-modal1" class="button bps-modal-button">
<img src="<?php echo plugins_url('/bulletproof-security/admin/images/question-mark-large.jpg'); ?>" style="margin:0px 0px 0px -10px" /></button></h3>

<div id="bps-modal-content1" class="bps-dialog-hide" title="<?php _e('Alerts|Logs|Email Options', 'bulletproof-security'); ?>">
	<div id="dialog-anchor" style="position:relative;top:-30px;left:0px"><a href="#"></a></div>
	<p>
	<?php
        $text = '<strong>'.__('This Question Mark Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text; 

	    $bpsPro_text = '<strong><font color="blue">'.__('Want even more security protection for the ridiculously cheap one-time price of $69.95', 'bulletproof-security').'</font><br>'.__('BPS Pro comes with free unlimited installations, upgrades & support for life. No yearly subscriptions or additional costs.', 'bulletproof-security').'<br><br>'.__('BBS Pro has an amazing track record. BPS Pro is installed on 60,000+ websites. Not a single one of those websites has been hacked in 10+ years.', 'bulletproof-security').'<br><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Get BPS Pro">'.__('Get BPS Pro', 'bulletproof-security').'</a><br><a href="https://www.ait-pro.com/bps-features/" target="_blank" title="BPS Pro Features">'.__('BPS Pro Features', 'bulletproof-security').'</a></strong><br><br>';	
		echo $bpsPro_text;

		// Forum Help Links or of course both
		$text = '<strong><font color="blue">'.__('Forum Help Links: ', 'bulletproof-security').'</font></strong><br>'; 	
		echo $text;	
	?>
	<strong><a href="https://forum.ait-pro.com/forums/topic/read-me-first-free/#bps-free-general-troubleshooting" title="BPS Troubleshooting Steps" target="_blank"><?php _e('BPS Troubleshooting Steps', 'bulletproof-security'); ?></a></strong><br /><br />		
	
	<?php $text = '<strong>'.__('Reset|Recheck Dismiss Notices:', 'bulletproof-security').'</strong><br>'.__('Clicking this button resets ALL Dismiss Notices such as Bonus Code Dismiss Notices and ALL other Dismiss Notices. If you previously dismissed a Dismiss Notice and want to display it again at a later time click this button.', 'bulletproof-security').'<br><br><strong>'.__('Email Alerts & Log File Settings', 'bulletproof-security').'</strong><br>'.__('The email address fields To, From, Cc and Bcc can be email addresses for your hosting account, your WordPress Administrator email address or 3rd party email addresses like gmail or yahoo email. If you are sending emails to multiple email recipients then separate the email addresses with a comma. Example: someone@somewhere.com, someoneelse@somewhereelse.com. You can add a space or not add a space after the comma between email addresses.', 'bulletproof-security').'<br><br><strong>'.__('Note: ', 'bulletproof-security').'</strong>'.__('Email Alerting and Log file options are located in S-Monitor in BPS Pro.', 'bulletproof-security').'<br><br><strong>'.__('Login Security: Send Email Alert When...', 'bulletproof-security').'</strong><br>'.__('There are 5 different email options. Choose to have email alerts sent when a User Account is locked out, An Administrator Logs in, An Administrator Logs in and when a User Account is locked out, Any User logs in and when a User Account is locked out or Do Not Send Email Alerts.', 'bulletproof-security').'<br><br>'.__('The email alerts contain the action that occurred with Timestamp and these fields: Username, Status, Role, Email, Lockout Time, Lockout Time Expires, User IP Address, User Hostname, Request URI and URL link for the website where the action occurred.', 'bulletproof-security').'<br><br><strong>'.__('HPF: Hidden Plugin Folders|Files (HPF) Cron', 'bulletproof-security').'</strong><br>'.__('If you do not want to receive HPF email alerts then set this option setting to: Do Not Send Email Alerts.', 'bulletproof-security').'<br><br><strong>'.__('Security Log File Email|Delete Log File When...', 'bulletproof-security').'</strong><br>'.__('Select the maximum Log File size that you want to allow for your Security Log File and then select the option that you want when your log file reaches that maximum size. Choose to either automatically Email the Log file to you and delete it or just delete it without emailing the log file to you first.', 'bulletproof-security').'<br><br><strong>'.__('DB Backup Log File Email|Delete Log File When...', 'bulletproof-security').'</strong><br>'.__('Select the maximum Log File size that you want to allow for your DB Backup Log File and then select the option that you want when your log file reaches that maximum size. Choose to either automatically Email the Log file to you and delete it or just delete it without emailing the log file to you first.', 'bulletproof-security').'<br><br><strong>'.__('MScan Malware Scanner Email|Delete Log File When...', 'bulletproof-security').'</strong><br>'.__('Select the maximum Log File size that you want to allow for your MScan Log File and then select the option that you want when your log file reaches that maximum size. Choose to either automatically Email the Log file to you and delete it or just delete it without emailing the log file to you first.', 'bulletproof-security').'<br><br><strong>'.__('Plugin Updates Available Email Alert:', 'bulletproof-security').'</strong><br>'.__('Choose whether or not to have email alerts sent if new Plugin version updates are available. The default setting is "Do Not Send Email Alerts". You can choose either to send email alerts for all Plugins or only Active Plugins.', 'bulletproof-security').'<br><br><strong>'.__('Theme Updates Available Email Alert:', 'bulletproof-security').'</strong><br>'.__('Choose whether or not to have email alerts sent if new Theme version updates are available. The default setting is "Do Not Send Email Alerts". You can choose either to send email alerts for all Themes or only the Active Theme.', 'bulletproof-security'); echo $text; ?></p>
</div>

<div id="ResetDismissNotices">
<form name="bpsResetDismissNotices" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/email-log-settings/email-log-settings.php' ); ?>" method="post">
	<?php wp_nonce_field('bulletproof_security_reset_dismiss_notices'); ?>
    
    <p><strong><label for="Status-Display"><?php _e('Reset|Recheck Dismiss Notices: ', 'bulletproof-security'); ?></label>
	<input type="hidden" name="bpsRDN" value="bps-RDN" />
	<input type="submit" name="bpsResetDismissSubmit" class="button bps-button" value="<?php esc_attr_e('Reset|Recheck', 'bulletproof-security') ?>" />
	</strong></p>
</form>
</div>

<div id="EmailOptions" style="width:100%;">   

<form name="bpsEmailAlerts" action="options.php" method="post">
    <?php settings_fields('bulletproof_security_options_email');
	$options = get_option('bulletproof_security_options_email'); 
	$admin_email = get_option('admin_email'); 
	$bps_send_email_to = ! empty($options['bps_send_email_to']) ? $options['bps_send_email_to'] : $admin_email;
	$bps_send_email_from = ! empty($options['bps_send_email_from']) ? $options['bps_send_email_from'] : $admin_email;
	$bps_send_email_cc = ! isset($options['bps_send_email_cc']) ? '' : $options['bps_send_email_cc'];
	$bps_send_email_bcc = ! isset($options['bps_send_email_bcc']) ? '' : $options['bps_send_email_bcc'];
	$bps_login_security_email = ! isset($options['bps_login_security_email']) ? '' : $options['bps_login_security_email'];
	$bps_hpf_email = ! isset($options['bps_hpf_email']) ? '' : $options['bps_hpf_email'];
	$bps_security_log_size = ! isset($options['bps_security_log_size']) ? '' : $options['bps_security_log_size'];
	$bps_security_log_emailL = ! isset($options['bps_security_log_emailL']) ? '' : $options['bps_security_log_emailL'];
	$bps_dbb_log_size = ! isset($options['bps_dbb_log_size']) ? '' : $options['bps_dbb_log_size'];
	$bps_dbb_log_email = ! isset($options['bps_dbb_log_email']) ? '' : $options['bps_dbb_log_email'];
	$bps_mscan_log_size = ! isset($options['bps_mscan_log_size']) ? '' : $options['bps_mscan_log_size'];
	$bps_mscan_log_email = ! isset($options['bps_mscan_log_email']) ? '' : $options['bps_mscan_log_email'];
	$bps_plugin_updates_frequency = ! isset($options['bps_plugin_updates_frequency']) ? '' : $options['bps_plugin_updates_frequency'];
	$bps_plugin_updates_email = ! isset($options['bps_plugin_updates_email']) ? '' : $options['bps_plugin_updates_email'];
	$bps_theme_updates_frequency = ! isset($options['bps_theme_updates_frequency']) ? '' : $options['bps_theme_updates_frequency'];
	$bps_theme_updates_email = ! isset($options['bps_theme_updates_email']) ? '' : $options['bps_theme_updates_email'];
	?>

<table border="0">
  <tr>
    <td><label for="bps-monitor-email"><?php _e('Send Email Alerts & Log Files To:', 'bulletproof-security'); ?> </label><br />
    <input type="text" name="bulletproof_security_options_email[bps_send_email_to]" class="regular-text-340" value="<?php echo esc_html( $bps_send_email_to ); ?>" /></td>
  </tr>
  <tr>
    <td><label for="bps-monitor-email"><?php _e('Send Email Alerts & Log Files From:', 'bulletproof-security'); ?> </label><br />
    <input type="text" name="bulletproof_security_options_email[bps_send_email_from]" class="regular-text-340" value="<?php echo esc_html( $bps_send_email_from ); ?>" /></td>
  </tr>
  <tr>
    <td><label for="bps-monitor-email"><?php _e('Send Email Alerts & Log Files Cc:', 'bulletproof-security'); ?> </label><br />
    <input type="text" name="bulletproof_security_options_email[bps_send_email_cc]" class="regular-text-340" value="<?php echo esc_html( $bps_send_email_cc ); ?>" /></td>
  </tr>
  <tr>
    <td><label for="bps-monitor-email"><?php _e('Send Email Alerts & Log Files Bcc:', 'bulletproof-security'); ?> </label><br />
    <input type="text" name="bulletproof_security_options_email[bps_send_email_bcc]" class="regular-text-340" value="<?php echo esc_html( $bps_send_email_bcc ); ?>" /></td>
  </tr>
</table>
<br />

<table border="0">
  <tr>
    <td><strong><label for="bps-monitor-email"><?php _e('Login Security: Send Login Security Email Alert When...', 'bulletproof-security'); ?></label></strong><br />
<select name="bulletproof_security_options_email[bps_login_security_email]" class="form-340">
<option value="lockoutOnly" <?php selected( $bps_login_security_email, 'lockoutOnly'); ?>><?php _e('A User Account Is Locked Out', 'bulletproof-security'); ?></option>
<option value="adminLoginOnly" <?php selected( $bps_login_security_email, 'adminLoginOnly'); ?>><?php _e('An Administrator Logs In', 'bulletproof-security'); ?></option>
<option value="adminLoginLock" <?php selected( $bps_login_security_email, 'adminLoginLock'); ?>><?php _e('An Administrator Logs In & A User Account is Locked Out', 'bulletproof-security'); ?></option>
<option value="anyUserLoginLock" <?php selected( $bps_login_security_email, 'anyUserLoginLock'); ?>><?php _e('Any User Logs In & A User Account is Locked Out', 'bulletproof-security'); ?></option>
<option value="no" <?php selected( $bps_login_security_email, 'no'); ?>><?php _e('Do Not Send Email Alerts', 'bulletproof-security'); ?></option>
</select></td>
  </tr>
  <tr>
    <td><strong><label for="bps-monitor-email"><?php _e('HPF: Hidden Plugin Folders|Files (HPF) Cron', 'bulletproof-security'); ?></label></strong><br />
<select name="bulletproof_security_options_email[bps_hpf_email]" class="form-340" style="margin-bottom:10px">
<option value="yes" <?php selected( $bps_hpf_email, 'yes'); ?>><?php _e('Send Email Alerts', 'bulletproof-security'); ?></option>
<option value="no" <?php selected( $bps_hpf_email, 'no'); ?>><?php _e('Do Not Send Email Alerts', 'bulletproof-security'); ?></option>
</select>
	</td>
  </tr>
  <tr>
    <td style="padding-top:5px;"><strong><label for="bps-monitor-email-log"><?php _e('Security Log: Email|Delete Security Log File When...', 'bulletproof-security'); ?></label></strong><br />
<select name="bulletproof_security_options_email[bps_security_log_size]" class="form-80">
<option value="500KB" <?php selected( $bps_security_log_size, '500KB' ); ?>><?php _e('500KB', 'bulletproof-security'); ?></option>
<option value="256KB" <?php selected( $bps_security_log_size, '256KB'); ?>><?php _e('256KB', 'bulletproof-security'); ?></option>
<option value="1MB" <?php selected( $bps_security_log_size, '1MB' ); ?>><?php _e('1MB', 'bulletproof-security'); ?></option>
</select>
<select name="bulletproof_security_options_email[bps_security_log_emailL]" class="form-255">
<option value="email" <?php selected( $bps_security_log_emailL, 'email' ); ?>><?php _e('Email Log & Then Delete Log File', 'bulletproof-security'); ?></option>
<option value="delete" <?php selected( $bps_security_log_emailL, 'delete' ); ?>><?php _e('Delete Log File', 'bulletproof-security'); ?></option>
</select></td>
  </tr>
  <tr>
    <td style="padding-top:5px;"><strong><label for="bps-monitor-email-log"><?php _e('DB Backup Log: Email|Delete DB Backup Log File When...', 'bulletproof-security'); ?></label></strong><br />
<select name="bulletproof_security_options_email[bps_dbb_log_size]" class="form-80">
<option value="500KB" <?php selected( $bps_dbb_log_size, '500KB' ); ?>><?php _e('500KB', 'bulletproof-security'); ?></option>
<option value="256KB" <?php selected( $bps_dbb_log_size, '256KB'); ?>><?php _e('256KB', 'bulletproof-security'); ?></option>
<option value="1MB" <?php selected( $bps_dbb_log_size, '1MB' ); ?>><?php _e('1MB', 'bulletproof-security'); ?></option>
</select>
<select name="bulletproof_security_options_email[bps_dbb_log_email]" class="form-255">
<option value="email" <?php selected( $bps_dbb_log_email, 'email' ); ?>><?php _e('Email Log & Then Delete Log File', 'bulletproof-security'); ?></option>
<option value="delete" <?php selected( $bps_dbb_log_email, 'delete' ); ?>><?php _e('Delete Log File', 'bulletproof-security'); ?></option>
</select>
	</td>
  <tr>
    <td style="padding-top:5px;"><strong><label for="bps-monitor-email-log"><?php _e('MScan Malware Scanner Email|Delete Log File When...', 'bulletproof-security'); ?></label></strong><br />
<select name="bulletproof_security_options_email[bps_mscan_log_size]" class="form-80">
<option value="500KB" <?php selected( $bps_mscan_log_size, '500KB' ); ?>><?php _e('500KB', 'bulletproof-security'); ?></option>
<option value="256KB" <?php selected( $bps_mscan_log_size, '256KB'); ?>><?php _e('256KB', 'bulletproof-security'); ?></option>
<option value="1MB" <?php selected( $bps_mscan_log_size, '1MB' ); ?>><?php _e('1MB', 'bulletproof-security'); ?></option>
</select>
<select name="bulletproof_security_options_email[bps_mscan_log_email]" class="form-255">
<option value="email" <?php selected( $bps_mscan_log_email, 'email' ); ?>><?php _e('Email Log & Then Delete Log File', 'bulletproof-security'); ?></option>
<option value="delete" <?php selected( $bps_mscan_log_email, 'delete' ); ?>><?php _e('Delete Log File', 'bulletproof-security'); ?></option>
</select>
	</td>
  <tr>
    <td style="padding-top:5px;"><strong><label for="bps-monitor-email-log"><?php _e('Plugin Updates Available Email Alert:', 'bulletproof-security'); ?></label></strong><br />
<select name="bulletproof_security_options_email[bps_plugin_updates_frequency]" class="form-80">
<option value="1Hour" <?php selected( $bps_plugin_updates_frequency, '1Hour' ); ?>><?php _e('1 Hour', 'bulletproof-security'); ?></option>
<option value="12Hours" <?php selected( $bps_plugin_updates_frequency, '12Hours'); ?>><?php _e('12 Hours', 'bulletproof-security'); ?></option>
<option value="1Day" <?php selected( $bps_plugin_updates_frequency, '1Day' ); ?>><?php _e('1 Day', 'bulletproof-security'); ?></option>
</select>
<select name="bulletproof_security_options_email[bps_plugin_updates_email]" class="form-255">
<option value="no" <?php selected( $bps_plugin_updates_email, 'no'); ?>><?php _e('Do Not Send Email Alerts', 'bulletproof-security'); ?></option>
<option value="yes_all" <?php selected( $bps_plugin_updates_email, 'yes_all'); ?>><?php _e('Send Email Alerts for All Plugins', 'bulletproof-security'); ?></option>
<option value="yes_active" <?php selected( $bps_plugin_updates_email, 'yes_active'); ?>><?php _e('Send Email Alerts for Active Plugins Only', 'bulletproof-security'); ?></option>
</select>
	</td>
  <tr>
    <td style="padding-top:5px;"><strong><label for="bps-monitor-email-log"><?php _e('Theme Updates Available Email Alert:', 'bulletproof-security'); ?></label></strong><br />
<select name="bulletproof_security_options_email[bps_theme_updates_frequency]" class="form-80">
<option value="1Hour" <?php selected( $bps_theme_updates_frequency, '1Hour' ); ?>><?php _e('1 Hour', 'bulletproof-security'); ?></option>
<option value="12Hours" <?php selected( $bps_theme_updates_frequency, '12Hours'); ?>><?php _e('12 Hours', 'bulletproof-security'); ?></option>
<option value="1Day" <?php selected( $bps_theme_updates_frequency, '1Day' ); ?>><?php _e('1 Day', 'bulletproof-security'); ?></option>
</select>
<select name="bulletproof_security_options_email[bps_theme_updates_email]" class="form-255">
<option value="no" <?php selected( $bps_theme_updates_email, 'no'); ?>><?php _e('Do Not Send Email Alerts', 'bulletproof-security'); ?></option>
<option value="yes_all" <?php selected( $bps_theme_updates_email, 'yes_all'); ?>><?php _e('Send Email Alerts for All Themes', 'bulletproof-security'); ?></option>
<option value="yes_active" <?php selected( $bps_theme_updates_email, 'yes_active'); ?>><?php _e('Send Email Alerts for Active Theme Only', 'bulletproof-security'); ?></option>
</select>
	</td> 
  </tr>
</table>

<input type="hidden" name="bpsEMA" value="bps-EMA" />
<input type="submit" name="bpsEmailAlertSubmit" class="button bps-button" style="margin:15px 0px 20px 0px;" value="<?php esc_attr_e('Save Options', 'bulletproof-security') ?>" />
</form>
</div>

</td>
  </tr>
</table>

</div>

<div id="bps-tabs-2" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
   <tr>
    <td class="bps-table_title"></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_links">
    
    <a href="https://forum.ait-pro.com/forums/topic/security-log-event-codes/" target="_blank"><?php _e('Security Log Event Codes', 'bulletproof-security'); ?></a><br /><br />
    <a href="https://forum.ait-pro.com/forums/topic/plugin-conflicts-actively-blocked-plugins-plugin-compatibility/" target="_blank"><?php _e('Forum: Search, Troubleshooting Steps & Post Questions For Assistance', 'bulletproof-security'); ?></a>

	<div id="bps-whitespace-275" style="min-height:275px"></div>

    </td>
  </tr>
</table>
</div>
<?php echo $bps_footer; ?>
</div>
</div>