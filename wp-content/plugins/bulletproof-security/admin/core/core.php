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
	
	if ( esc_html($_SERVER['REQUEST_METHOD']) == 'POST' || isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true ) {

		bpsPro_Browser_UA_scroll_animation();
	}
}
?>

<?php

// Get Real IP address - USE EXTREME CAUTION!!!
function bpsPro_get_real_ip_address_cc() {
	
	if ( is_admin() && current_user_can('manage_options') ) {
	
		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = esc_html( $_SERVER['HTTP_CLIENT_IP'] );
			
			if ( ! is_array($ip) ) {
				
				if ( preg_match( '/(\d+\.){3}\d+/', $ip, $matches ) ) {

					return $matches[0];	
				
				} elseif ( preg_match( '/([:\d\w]+\.(\d+\.){2}\d+|[:\d\w]+)/', $ip, $matches ) ) {
				
					return $matches[0];	
		
				} else {
					
					return $ip;
				}
			
			} else {
				
				return current($ip);				
			}
		
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = esc_html( $_SERVER['HTTP_X_FORWARDED_FOR'] );
			
			if ( ! is_array($ip) ) {
				
				if ( preg_match( '/(\d+\.){3}\d+/', $ip, $matches ) ) {

					return $matches[0];	
				
				} elseif ( preg_match( '/([:\d\w]+\.(\d+\.){2}\d+|[:\d\w]+)/', $ip, $matches ) ) {
				
					return $matches[0];	
		
				} else {
					
					return $ip;
				}
			
			} else {
				
				return current($ip);				
			}
		
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = esc_html( $_SERVER['REMOTE_ADDR'] );
			return $ip;
		}
	}
}	

// Create a new Deny All .htaccess file on first page load with users current IP address to allow the cc-master.zip file to be downloaded
// Create a new Deny All .htaccess file if IP address is not current
function bpsPro_Core_CC_deny_all() {

	if ( is_admin() && current_user_can('manage_options') ) {
		
		$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
		$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
		$Zip_download_Options = get_option('bulletproof_security_options_zip_fix');
		
		if ( isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'disabled' || isset($Zip_download_Options['bps_zip_download_fix']) && $Zip_download_Options['bps_zip_download_fix'] == 'On' ) {
			return;
		}

		if ( isset($Apache_Mod_options['bps_apache_mod_ifmodule']) && $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {	
	
			$denyall_content = "# BPS mod_authz_core IfModule BC\n<IfModule mod_authz_core.c>\nRequire ip ". bpsPro_get_real_ip_address_cc()."\n</IfModule>\n\n<IfModule !mod_authz_core.c>\n<IfModule mod_access_compat.c>\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address_cc()."\n</FilesMatch>\n</IfModule>\n</IfModule>";
	
		} else {
		
			$denyall_content = "# BPS mod_access_compat\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address_cc()."\n</FilesMatch>";		
		}		
		
		$create_denyall_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/core/.htaccess';
		
		if ( ! file_exists($create_denyall_htaccess_file) ) { 
			$handle = fopen( $create_denyall_htaccess_file, 'w+b' );
    		fwrite( $handle, $denyall_content );
    		fclose( $handle );
		}			
		
		if ( file_exists($create_denyall_htaccess_file) ) {
			
			$check_string = file_get_contents($create_denyall_htaccess_file);
			
			if ( ! strpos( $check_string, bpsPro_get_real_ip_address_cc() ) ) { 
				$handle = fopen( $create_denyall_htaccess_file, 'w+b' );
				fwrite( $handle, $denyall_content );
				fclose( $handle );
			}
		}
	}
}
bpsPro_Core_CC_deny_all();

?>  

<h2 class="bps-tab-title"><?php _e('htaccess File Options', 'bulletproof-security'); ?></h2>

<div id="message" class="updated" style="border:1px solid #999;background-color:#000;">

<?php
// Apache IfModule htaccess file code check & creation: run on page load with 15 minute time restriction.
// System Info page: performs check in real-time without a 15 minute time restriction, but does not create htaccess files.
bpsPro_apache_mod_directive_check();

// default.htaccess, secure.htaccess, fwrite content for all WP site types
$bps_get_domain_root = bpsGetDomainRoot();
$bps_get_wp_root_default = bps_wp_get_root_folder();
// Replace ABSPATH = wp-content/plugins
$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
// Replace ABSPATH = wp-content
$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
// Replace ABSPATH = wp-content/uploads
$wp_upload_dir = wp_upload_dir();
$bps_uploads_dir = str_replace( ABSPATH, '', $wp_upload_dir['basedir'] );
// Nonce for Crypto-js
$bps_nonceValue = 'ghbhnyxu';
$bpsSpacePop = '-------------------------------------------------------------';

$bps_topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
$bps_bottomDiv = '</p></div>';

// General all purpose "Settings Saved." message for forms
if ( current_user_can('manage_options') ) {
if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true) {
	$text = '<p style="font-size:1em;font-weight:bold;padding:2px 0px 2px 5px;margin:0px -11px 0px -11px;background-color:#dfecf2;-webkit-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);""><font color="green"><strong>'.__('Settings Saved', 'bulletproof-security').'</strong></font></p>';
	echo $text;
	}
}

require_once WP_PLUGIN_DIR . '/bulletproof-security/admin/core/core-help-text.php';

// WBM, HPF, MBM, BBM: activate and deactivate and all other form code
if ( isset( $_POST['Submit-WBM-Activate'] ) || isset( $_POST['Submit-WBM-Deactivate'] ) || isset( $_POST['Submit-Hidden-Plugins'] ) || isset( $_POST['Hidden-Plugins-Ignore-Submit'] ) || isset( $_POST['Submit-MBM-Activate'] ) || isset( $_POST['Submit-MBM-Deactivate'] ) || isset( $_POST['Submit-BBM-Activate'] ) || isset( $_POST['Submit-BBM-Deactivate'] ) || isset( $_POST['Submit-Backup-htaccess-Files'] ) || isset( $_POST['Submit-Restore-htaccess-Files'] ) ) {
require_once WP_PLUGIN_DIR . '/bulletproof-security/admin/core/core-forms.php';	
}

// RBM: activate and deactivate form code
if ( isset( $_POST['Submit-RBM-Activate'] ) || isset( $_POST['Submit-RBM-Deactivate'] ) || isset( $_POST['Submit-RBM-Activate-Network'] ) || isset( $_POST['Submit-RBM-Deactivate-Network'] ) ) {
require_once WP_PLUGIN_DIR . '/bulletproof-security/admin/core/core-htaccess-code.php';
}

?>
</div>

<!-- jQuery UI Tabs Menu -->
<div id="bps-tabs" class="bps-menu">
    <div id="bpsHead"><img src="<?php echo plugins_url('/bulletproof-security/admin/images/bps-plugin-logo.jpg'); ?>" /></div>
   
	<ul>
		<li><a href="#bps-tabs-1"><?php _e('Security Modes', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-tabs-6"><?php _e('htaccess File Editor', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-tabs-7"><?php _e('Custom Code', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-tabs-9"><?php _e('My Notes', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-tabs-10"><?php _e('Whats New', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-tabs-11"><?php _e('Help &amp; FAQ', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-tabs-12"><?php _e('BPS Pro Features', 'bulletproof-security'); ?></a></li>
	</ul>
            
<div id="bps-tabs-1" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

    <h3 style="margin-top:0px"><?php _e('Root Folder BulletProof Mode (RBM)', 'bulletproof-security'); ?>  <button id="bps-open-modal1" class="button bps-modal-button">
    <img src="<?php echo plugins_url('/bulletproof-security/admin/images/question-mark-large.jpg'); ?>" style="margin:0px 0px 0px -10px" /></button></h3>
    
<div id="bps-modal-content1" class="bps-dialog-hide" title="<?php _e('Root Folder BulletProof Mode (RBM)', 'bulletproof-security'); ?>">
	<div id="dialog-anchor" style="position:relative;top:-30px;left:0px"><a href="#"></a></div>
	<p>
	<?php
        $text = '<strong>'.__('This Question Mark Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text; 
		
	    $bpsPro_text = '<strong><font color="blue">'.__('Want even more security protection for the ridiculously cheap one-time price of $69.95', 'bulletproof-security').'</font><br>'.__('BPS Pro comes with free unlimited installations, upgrades & support for life. No yearly subscriptions or additional costs.', 'bulletproof-security').'<br><br>'.__('BBS Pro has an amazing track record. BPS Pro is installed on 60,000+ websites. Not a single one of those websites has been hacked in 10+ years.', 'bulletproof-security').'<br><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Get BPS Pro">'.__('Get BPS Pro', 'bulletproof-security').'</a><br><a href="https://www.ait-pro.com/bps-features/" target="_blank" title="BPS Pro Features">'.__('BPS Pro Features', 'bulletproof-security').'</a></strong><br><br>';	
		echo $bpsPro_text;	
		
		$text = '<strong><font color="blue">'.__('Forum Help Links: ', 'bulletproof-security').'</font></strong><br>'; 	
		echo $text;	
	?>
	<strong><a href="https://forum.ait-pro.com/video-tutorials/" title="Setup Wizard & Other Video Tutorials" target="_blank"><?php _e('Setup Wizard & Other Video Tutorials', 'bulletproof-security'); ?></a></strong><br />
    <strong><a href="https://forum.ait-pro.com/forums/topic/read-me-first-free/#bps-free-general-troubleshooting" title="BPS Troubleshooting Steps" target="_blank"><?php _e('BPS Troubleshooting Steps', 'bulletproof-security'); ?></a></strong><br /><br />

	<?php echo $bps_general_help_info; echo $bps_rbm_content; ?>
    </p>
</div> 

<?php
// RBM Status: real-time status check
// 4 possible RBM Status indicators: Activated, Deactivated, Disabled or Root htaccess File Does Not Exist
function bpsPro_rbm_status() {
global $bps_version;
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	$filename = ABSPATH . '.htaccess';
	
	if ( file_exists($filename) ) {
		$check_string = file_get_contents($filename);
	}
	
	if ( isset ( $_POST['Submit-RBM-Activate'] ) ) {
		$_POST['Submit-RBM-Activate'] = true;
	} else {
		$_POST['Submit-RBM-Activate'] = null;
	}
	
	if ( isset ( $_POST['Submit-RBM-Deactivate'] ) )  {
		$_POST['Submit-RBM-Deactivate'] = true;
	} else {
		$_POST['Submit-RBM-Deactivate'] = null;
	}

	if ( $_POST['Submit-RBM-Activate'] != true && $_POST['Submit-RBM-Deactivate'] != true ) {
	
		if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<span class="core-status-disabled">'.__('Disabled', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<span class="core-status-error">'.__('Root htaccess File Does Not Exist', 'bulletproof-security').'</span></strong></h3>';
			echo $text;	
		} elseif ( strpos( $check_string, "BULLETPROOF $bps_version" ) && strpos( $check_string, "BPSQSE" ) ) {
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<span class="core-status-activated">'.__('Activated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		} elseif ( strpos( $check_string, "BULLETPROOF DEFAULT .HTACCESS" ) ) {
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<span class="core-status-deactivated">'.__('Deactivated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;			
		}
	}

	if ( $_POST['Submit-RBM-Activate'] == true || $_POST['Submit-RBM-Deactivate'] == true ) {
		
		if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<span class="core-status-disabled">'.__('Disabled', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<span class="core-status-error">'.__('Root htaccess File Does Not Exist', 'bulletproof-security').'</span></strong></h3>';
			echo $text;	
		} elseif ( strpos( $check_string, "BULLETPROOF $bps_version" ) && strpos( $check_string, "BPSQSE" ) ) {
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<span class="core-status-activated">'.__('Activated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		} elseif ( strpos( $check_string, "BULLETPROOF DEFAULT .HTACCESS" ) ) {
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<span class="core-status-deactivated">'.__('Deactivated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;			
		}
	}
}
?>

<div id="RBM-Status"><?php bpsPro_rbm_status(); ?></div>

<div id="root-bulletproof-mode" style="border-bottom:1px solid #999999;">

<?php if ( ! is_multisite() ) { ?>

<form name="RBM-Activate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_rbm_activate'); ?>

	<div id="RBM-buttons" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-RBM-Activate" style="margin:10px 0px 10px 0px;width:84px;height:auto;white-space:normal" value="<?php esc_attr_e('Activate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Activate Root Folder BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<form name="RBM-Deactivate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_rbm_deactivate'); ?>

	<div id="RBM-buttons" style="">
    <input type="submit" name="Submit-RBM-Deactivate" style="margin:10px 0px 10px 0px;width:84px;height:auto;white-space:normal" value="<?php esc_attr_e('Deactivate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Deactivate Root Folder BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<?php } else { ?>

<form name="RBM-Activate-Network" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_rbm_activate_network'); ?>

	<div id="RBM-buttons" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-RBM-Activate-Network" style="margin:10px 0px 10px 0px;width:84px;height:auto;white-space:normal" value="<?php esc_attr_e('Activate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Activate Root Folder BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<form name="RBM-Deactivate-Network" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_rbm_deactivate_network'); ?>

	<div id="RBM-buttons" style="">
    <input type="submit" name="Submit-RBM-Deactivate-Network" style="margin:10px 0px 10px 0px;width:84px;height:auto;white-space:normal" value="<?php esc_attr_e('Deactivate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Deactivate Root Folder BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<?php } ?>

</div>

<h3><?php _e('wp-admin Folder BulletProof Mode (WBM)', 'bulletproof-security'); ?>  <button id="bps-open-modal2" class="button bps-modal-button">
<img src="<?php echo plugins_url('/bulletproof-security/admin/images/question-mark-large.jpg'); ?>" style="margin:0px 0px 0px -10px" /></button></h3>

<div id="bps-modal-content2" class="bps-dialog-hide" title="<?php _e('Root Folder BulletProof Mode (RBM)', 'bulletproof-security'); ?>">
	<div id="dialog-anchor" style="position:relative;top:-30px;left:0px"><a href="#"></a></div>
	<p>
	<?php
        $text = '<strong>'.__('This Question Mark Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text; 

	    $bpsPro_text = '<strong><font color="blue">'.__('Want even more security protection for the ridiculously cheap one-time price of $69.95', 'bulletproof-security').'</font><br>'.__('BPS Pro comes with free unlimited installations, upgrades & support for life. No yearly subscriptions or additional costs.', 'bulletproof-security').'<br><br>'.__('BBS Pro has an amazing track record. BPS Pro is installed on 60,000+ websites. Not a single one of those websites has been hacked in 10+ years.', 'bulletproof-security').'<br><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Get BPS Pro">'.__('Get BPS Pro', 'bulletproof-security').'</a><br><a href="https://www.ait-pro.com/bps-features/" target="_blank" title="BPS Pro Features">'.__('BPS Pro Features', 'bulletproof-security').'</a></strong><br><br>';	
		echo $bpsPro_text;

		$text = '<strong><font color="blue">'.__('Forum Help Links: ', 'bulletproof-security').'</font></strong><br>'; 	
		echo $text;	
	?>
	<strong><a href="https://forum.ait-pro.com/video-tutorials/" title="Setup Wizard & Other Video Tutorials" target="_blank"><?php _e('Setup Wizard & Other Video Tutorials', 'bulletproof-security'); ?></a></strong><br />
	<strong><a href="https://forum.ait-pro.com/forums/topic/read-me-first-free/#bps-free-general-troubleshooting" title="BPS Troubleshooting Steps" target="_blank"><?php _e('BPS Troubleshooting Steps', 'bulletproof-security'); ?></a></strong><br /><br />

	<?php echo $bps_general_help_info; echo $bps_wbm_content; ?>
    </p>
</div> 

<div id="PFWScan-Menu-Link"></div>

<?php
// WBM Status: real-time status check
// 3 possible WBM Status indicators: Activated, Deactivated or Disabled.
function bpsPro_wbm_status() {
global $bps_version;
	
	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	$GDMW_options = get_option('bulletproof_security_options_GDMW');	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	
	$filename = ABSPATH . 'wp-admin/.htaccess';
	
	if ( file_exists($filename) ) {
		$check_string = file_get_contents($filename);
	}
	
	if ( isset ( $_POST['Submit-WBM-Activate'] ) ) {
		$_POST['Submit-WBM-Activate'] = true;
	} else {
		$_POST['Submit-WBM-Activate'] = null;
	}
	
	if ( isset ( $_POST['Submit-WBM-Deactivate'] ) )  {
		$_POST['Submit-WBM-Deactivate'] = true;
	} else {
		$_POST['Submit-WBM-Deactivate'] = null;
	}

	if ( $_POST['Submit-WBM-Activate'] != true && $_POST['Submit-WBM-Deactivate'] != true ) {
	
		if ( ! file_exists($filename) && isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'disabled' || isset($BPS_wpadmin_Options['bps_wpadmin_restriction']) && $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' || isset($GDMW_options['bps_gdmw_hosting']) && $GDMW_options['bps_gdmw_hosting'] == 'yes' ) {
			$text = '<h3><strong>'.__('WBM Status: ', 'bulletproof-security').'<span class="core-status-disabled">'.__('Disabled', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) ) {	
			$text = '<h3><strong>'.__('WBM Status: ', 'bulletproof-security').'<span class="core-status-deactivated">'.__('Deactivated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;	
		} elseif ( strpos( $check_string, "BULLETPROOF $bps_version" ) && strpos( $check_string, "BPSQSE-check" ) ) {
			$text = '<h3><strong>'.__('WBM Status: ', 'bulletproof-security').'<span class="core-status-activated">'.__('Activated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		}
	}

	if ( $_POST['Submit-WBM-Activate'] == true || $_POST['Submit-WBM-Deactivate'] == true ) {
		
		if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] == 'disabled' || $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' || $GDMW_options['bps_gdmw_hosting'] == 'yes' ) {
			$text = '<h3><strong>'.__('WBM Status: ', 'bulletproof-security').'<span class="core-status-disabled">'.__('Disabled', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) ) {	
			$text = '<h3><strong>'.__('WBM Status: ', 'bulletproof-security').'<span class="core-status-deactivated">'.__('Deactivated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;	
		} elseif ( strpos( $check_string, "BULLETPROOF $bps_version" ) && strpos( $check_string, "BPSQSE-check" ) ) {
			$text = '<h3><strong>'.__('WBM Status: ', 'bulletproof-security').'<span class="core-status-activated">'.__('Activated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		}
	}
}
?>

<div id="WBM-Status"><?php bpsPro_wbm_status(); ?></div>

<div id="wpadmin-bulletproof-mode" style="border-bottom:1px solid #999999;">

<form name="WBM-Activate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_wbm_activate'); ?>

	<div id="WBM-buttons" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-WBM-Activate" style="margin:10px 0px 10px 0px;width:84px;height:auto;white-space:normal" value="<?php esc_attr_e('Activate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Activate wp-admin Folder BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<form name="WBM-Deactivate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_wbm_deactivate'); ?>

	<div id="WBM-buttons" style="">
    <input type="submit" name="Submit-WBM-Deactivate" style="margin:10px 0px 10px 0px;width:84px;height:auto;white-space:normal" value="<?php esc_attr_e('Deactivate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Deactivate wp-admin Folder BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

</div>

<div id="UAEG-Menu-Link"></div>

<h3><?php _e('Hidden Plugin Folders|Files Cron (HPF)', 'bulletproof-security'); ?>  <button id="bps-open-modal5" class="button bps-modal-button">
<img src="<?php echo plugins_url('/bulletproof-security/admin/images/question-mark-large.jpg'); ?>" style="margin:0px 0px 0px -10px" /></button></h3>

<div id="bps-modal-content5" class="bps-dialog-hide" title="<?php _e('Hidden Plugin Folders|Files Cron (HPF)', 'bulletproof-security'); ?>">
	<div id="dialog-anchor" style="position:relative;top:-30px;left:0px"><a href="#"></a></div>
	<p>
	<?php
        $text = '<strong>'.__('This Question Mark Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text;

	    $bpsPro_text = '<strong><font color="blue">'.__('Want even more security protection for the ridiculously cheap one-time price of $69.95', 'bulletproof-security').'</font><br>'.__('BPS Pro comes with free unlimited installations, upgrades & support for life. No yearly subscriptions or additional costs.', 'bulletproof-security').'<br><br>'.__('BBS Pro has an amazing track record. BPS Pro is installed on 60,000+ websites. Not a single one of those websites has been hacked in 10+ years.', 'bulletproof-security').'<br><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Get BPS Pro">'.__('Get BPS Pro', 'bulletproof-security').'</a><br><a href="https://www.ait-pro.com/bps-features/" target="_blank" title="BPS Pro Features">'.__('BPS Pro Features', 'bulletproof-security').'</a></strong><br><br>';	
		echo $bpsPro_text;

		echo $bps_general_help_info; 
		echo $bps_hpf_content;
	?>
    </p>
</div>

<?php
// HPF Status: real-time status check
// 2 possible HPF Status indicators: HPF Cron On, HPF Cron Off.
function bpsPro_hpf_status() {
	
	$hpf_options = get_option('bulletproof_security_options_hpf_cron');	
	
	if ( isset ( $_POST['Submit-Hidden-Plugins'] ) ) {
		$_POST['Submit-Hidden-Plugins'] = true;
	} else {
		$_POST['Submit-Hidden-Plugins'] = null;
	}
	
	if ( isset ( $_POST['Hidden-Plugins-Ignore-Submit'] ) )  {
		$_POST['Hidden-Plugins-Ignore-Submit'] = true;
	} else {
		$_POST['Hidden-Plugins-Ignore-Submit'] = null;
	}

	if ( $_POST['Submit-Hidden-Plugins'] != true && $_POST['Hidden-Plugins-Ignore-Submit'] != true ) {
	
		if ( isset($hpf_options['bps_hidden_plugins_cron']) && $hpf_options['bps_hidden_plugins_cron'] == 'On' ) {
			$text = '<h3><strong>'.__('HPF Status: ', 'bulletproof-security').'<span class="core-status-activated">'.__('HPF Cron On', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		} elseif ( isset($hpf_options['bps_hidden_plugins_cron']) && $hpf_options['bps_hidden_plugins_cron'] == 'Off' ) {
			$text = '<h3><strong>'.__('HPF Status: ', 'bulletproof-security').'<span class="core-status-deactivated">'.__('HPF Cron Off', 'bulletproof-security').'</span></strong></h3>';
			echo $text;	
		}
	}

	if ( $_POST['Submit-Hidden-Plugins'] == true || $_POST['Hidden-Plugins-Ignore-Submit'] == true ) {
		
		if ( isset($hpf_options['bps_hidden_plugins_cron']) && $hpf_options['bps_hidden_plugins_cron'] == 'On' ) {
			$text = '<h3><strong>'.__('HPF Status: ', 'bulletproof-security').'<span class="core-status-activated">'.__('HPF Cron On', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		} elseif ( isset($hpf_options['bps_hidden_plugins_cron']) && $hpf_options['bps_hidden_plugins_cron'] == 'Off' ) {
			$text = '<h3><strong>'.__('HPF Status: ', 'bulletproof-security').'<span class="core-status-deactivated">'.__('HPF Cron Off', 'bulletproof-security').'</span></strong></h3>';
			echo $text;	
		}
	}
}
?>

<div id="HPF-Status"><?php bpsPro_hpf_status(); ?></div>

<div id="HPF1">
<div id="HPF2" style="position:relative;top:10px;left:0px;float:left;margin:0px 15px 0px 0px;">
    
<?php
	// Form: Hidden|Empty Plugin Folders|Files Cron
	echo '<form name="HPFCron" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ).'" method="post">';
	wp_nonce_field('bulletproof_security_hpf_cron');

	$hpf_options = get_option('bulletproof_security_options_hpf_cron');
	$bps_hidden_plugins_cron_frequency = ! isset($hpf_options['bps_hidden_plugins_cron_frequency']) ? '' : $hpf_options['bps_hidden_plugins_cron_frequency'];
	$bps_hidden_plugins_cron = ! isset($hpf_options['bps_hidden_plugins_cron']) ? '' : $hpf_options['bps_hidden_plugins_cron'];
	
	echo '<label for="bps-hpf">'.__('HPF Cron Check Frequency:', 'bulletproof-security').'</label><br>';
	echo '<select name="hpf_cron_frequency" class="form-340">';
	echo '<option value="1"'. selected('1', $bps_hidden_plugins_cron_frequency).'>'.__('Run Check Every 1 Minute', 'bulletproof-security').'</option>';
	echo '<option value="5"'. selected('5', $bps_hidden_plugins_cron_frequency).'>'.__('Run Check Every 5 Minutes', 'bulletproof-security').'</option>';
	echo '<option value="10"'. selected('10', $bps_hidden_plugins_cron_frequency).'>'.__('Run Check Every 10 Minutes', 'bulletproof-security').'</option>';
	echo '<option value="15"'. selected('15', $bps_hidden_plugins_cron_frequency).'>'.__('Run Check Every 15 Minutes', 'bulletproof-security').'</option>';
	echo '<option value="30"'. selected('30', $bps_hidden_plugins_cron_frequency).'>'.__('Run Check Every 30 Minutes', 'bulletproof-security').'</option>';
	echo '<option value="60"'. selected('60', $bps_hidden_plugins_cron_frequency).'>'.__('Run Check Every 60 Minutes', 'bulletproof-security').'</option>';
	echo '<option value="daily"'. selected('daily', $bps_hidden_plugins_cron_frequency).'>'.__('Run Check Once Daily', 'bulletproof-security').'</option>';
	echo '</select><br><br>';

	echo '<label for="bps-hpf">'.__('HPF Cron On|Off:', 'bulletproof-security').'</label><br>';
	echo '<select name="hpf_on_off" class="form-340">';
	echo '<option value="On"'. selected('On', $bps_hidden_plugins_cron).'>'.__('HPF Cron On', 'bulletproof-security').'</option>';
	echo '<option value="Off"'. selected('Off', $bps_hidden_plugins_cron).'>'.__('HPF Cron Off', 'bulletproof-security').'</option>';
	echo '</select>';
	
	echo "<p style=\"margin-top:14px\"><input type=\"submit\" name=\"Submit-Hidden-Plugins\" value=\"".esc_attr__('Save HPF Cron Options', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"return confirm('".__('The default Cron Frequency is: Run Check Every 15 Minutes. This is a lightweight check that uses an insignificant amount of resources/memory so 4 checks per hour will not cause any performance issues whatsoever.\n\n-------------------------------------------------------------\n\nEven choosing Run Check Every 1 Minute would not cause any significant performance issues whatsoever.\n\n-------------------------------------------------------------\n\nClick OK to proceed or click Cancel', 'bulletproof-security')."')\" /></p></form>";

$scrolltoHiddenPlugins = isset($_REQUEST['scrolltoHiddenPlugins']) ? (int) $_REQUEST['scrolltoHiddenPlugins'] : 0; 

$hover_icon_hpf = '<strong><font color="black"><span class="tooltip-250-120"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:10px;" /><span>'.__('Add Ignore rules using plugin folder names or file names.', 'bulletproof-security').'<br>'.__('Use a comma and a space between folder and/or file names.', 'bulletproof-security').'<br>'.__('Example: plugin-folder-name, example-file-name.php', 'bulletproof-security').'</span></span></font></strong><br>';
?>

</div>

<div id="HPF3" style="position:relative;bottom:6px;left:0px;float:left;margin:0px 0px 0px 0px;">

<form name="Hidden-Plugins" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
    <?php wp_nonce_field('bulletproof_security_hpf_cron_ignore'); ?>
	<?php $hpfi_options = get_option('bulletproof_security_options_hidden_plugins'); 
		$bps_hidden_plugins_check = ! isset($hpfi_options['bps_hidden_plugins_check']) ? '' : $hpfi_options['bps_hidden_plugins_check'];
	?>

	<div id="HPF4" style="position:relative;top:0px;left:0px;margin:10px 0px 10px 0px;">
	<strong><label><?php _e('Ignore Hidden Plugin Folders & Files:', 'bulletproof-security'); echo $hover_icon_hpf; ?></label></strong>
    
    <textarea class="PFW-Allow-From-Text-Area" name="bps_hidden_plugins_check" style="margin-top:5px;" tabindex="1"><?php echo esc_textarea(trim($bps_hidden_plugins_check, ", \t\n\r")); ?></textarea>
	<input type="hidden" name="scrolltoHiddenPlugins" id="scrolltoHiddenPlugins" value="<?php echo esc_html( $scrolltoHiddenPlugins ); ?>" />
	</div>

	<div id="HPF5" style="position:relative;top:0px;left:0px;margin:10px 0px 10px 0px;">
    <input type="submit" name="Hidden-Plugins-Ignore-Submit" class="button bps-button" value="<?php esc_attr_e('Save Plugin Folder|Files Ignore Rules', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('This option is for adding ignore rules for Hidden or Empty Plugin Folders Detected by BPS or Non-standard WP files detected by BPS in your /plugins/ folder.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('This is an independent option setting that does not require clicking any other buttons.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to proceed or click Cancel.', 'bulletproof-security'); echo $text; ?>')"/>
	</div>

</form>
</div>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#PFW-Hidden-Plugins').submit(function(){ $('#scrolltoHiddenPlugins').val( $('#bps_hidden_plugins_check').scrollTop() ); });
	$('#bps_hidden_plugins_check').scrollTop( $('#scrolltoHiddenPlugins').val() );
});
/* ]]> */
</script>
</div>

<div id="MC1" style="position:relative;top:0px;left:0px;float:left;margin:0px 0px 0px 0px;width:100%;border-top:1px solid #999999;">

<h3><?php _e('Master htaccess Folder BulletProof Mode (MBM)', 'bulletproof-security'); ?>  <button id="bps-open-modal6" class="button bps-modal-button">
<img src="<?php echo plugins_url('/bulletproof-security/admin/images/question-mark-large.jpg'); ?>" style="margin:0px 0px 0px -10px" /></button></h3>

<div id="bps-modal-content6" class="bps-dialog-hide" title="<?php _e('MBM BulletProof Modes', 'bulletproof-security'); ?>">
	<div id="dialog-anchor" style="position:relative;top:-30px;left:0px"><a href="#"></a></div>
	<p>
	<?php 
	$text = '<strong>'.__('This Question Mark Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
	echo $text;

	$bpsPro_text = '<strong><font color="blue">'.__('Want even more security protection for the ridiculously cheap one-time price of $69.95', 'bulletproof-security').'</font><br>'.__('BPS Pro comes with free unlimited installations, upgrades & support for life. No yearly subscriptions or additional costs.', 'bulletproof-security').'<br><br>'.__('BBS Pro has an amazing track record. BPS Pro is installed on 60,000+ websites. Not a single one of those websites has been hacked in 10+ years.', 'bulletproof-security').'<br><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Get BPS Pro">'.__('Get BPS Pro', 'bulletproof-security').'</a><br><a href="https://www.ait-pro.com/bps-features/" target="_blank" title="BPS Pro Features">'.__('BPS Pro Features', 'bulletproof-security').'</a></strong><br><br>';	
	echo $bpsPro_text;

	echo $bps_general_help_info; 
	echo $bps_mbm_content; 
	?>
    </p>
</div>

<?php
// MBM Status: real-time status check
// 3 possible MBM Status indicators: Activated, Deactivated or Disabled.
function bpsPro_mbm_status() {
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	$filename = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/.htaccess';
	
	if ( isset ( $_POST['Submit-MBM-Activate'] ) ) {
		$_POST['Submit-MBM-Activate'] = true;
	} else {
		$_POST['Submit-MBM-Activate'] = null;
	}
	
	if ( isset ( $_POST['Submit-MBM-Deactivate'] ) )  {
		$_POST['Submit-MBM-Deactivate'] = true;
	} else {
		$_POST['Submit-MBM-Deactivate'] = null;
	}

	if ( $_POST['Submit-MBM-Activate'] != true && $_POST['Submit-MBM-Deactivate'] != true ) {
	
		if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<h3><strong>'.__('MBM Status: ', 'bulletproof-security').'<span class="core-status-disabled">'.__('Disabled', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<h3><strong>'.__('MBM Status: ', 'bulletproof-security').'<span class="core-status-deactivated">'.__('Deactivated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;	
		} elseif ( file_exists($filename) ) {
			$text = '<h3><strong>'.__('MBM Status: ', 'bulletproof-security').'<span class="core-status-activated">'.__('Activated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		}
	}

	if ( $_POST['Submit-MBM-Activate'] == true || $_POST['Submit-MBM-Deactivate'] == true ) {
		
		if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<h3><strong>'.__('MBM Status: ', 'bulletproof-security').'<span class="core-status-disabled">'.__('Disabled', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<h3><strong>'.__('MBM Status: ', 'bulletproof-security').'<span class="core-status-deactivated">'.__('Deactivated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;	
		} elseif ( file_exists($filename) ) {
			$text = '<h3><strong>'.__('MBM Status: ', 'bulletproof-security').'<span class="core-status-activated">'.__('Activated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		}
	}
}
?>

<div id="MBM-Status"><?php bpsPro_mbm_status(); ?></div>

<div id="mbm-bulletproof-mode" style="">

<form name="MBM-Activate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_mbm_activate'); ?>

	<div id="MBM-buttons" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-MBM-Activate" style="margin:10px 0px 10px 0px;width:84px;height:auto;white-space:normal" value="<?php esc_attr_e('Activate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Activate MBM BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<form name="MBM-Deactivate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_mbm_deactivate'); ?>

	<div id="MBM-buttons" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-MBM-Deactivate" style="margin:10px 0px 10px 0px;width:84px;height:auto;white-space:normal" value="<?php esc_attr_e('Deactivate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Deactivate MBM BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

</div>
</div>

<div id="MC2" style="position:relative;top:0px;left:0px;float:left;margin:0px 0px 0px 0px;width:100%;border-top:1px solid #999999;">

<h3><?php _e('BPS Backup Folder BulletProof Mode (BBM)', 'bulletproof-security'); ?>  <button id="bps-open-modal7" class="button bps-modal-button">
<img src="<?php echo plugins_url('/bulletproof-security/admin/images/question-mark-large.jpg'); ?>" style="margin:0px 0px 0px -10px" /></button></h3>

<div id="bps-modal-content7" class="bps-dialog-hide" title="<?php _e('BBM BulletProof Modes', 'bulletproof-security'); ?>">
	<div id="dialog-anchor" style="position:relative;top:-30px;left:0px"><a href="#"></a></div>
	<p>
	<?php 
	$text = '<strong>'.__('This Question Mark Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
	echo $text;

	$bpsPro_text = '<strong><font color="blue">'.__('Want even more security protection for the ridiculously cheap one-time price of $69.95', 'bulletproof-security').'</font><br>'.__('BPS Pro comes with free unlimited installations, upgrades & support for life. No yearly subscriptions or additional costs.', 'bulletproof-security').'<br><br>'.__('BBS Pro has an amazing track record. BPS Pro is installed on 60,000+ websites. Not a single one of those websites has been hacked in 10+ years.', 'bulletproof-security').'<br><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Get BPS Pro">'.__('Get BPS Pro', 'bulletproof-security').'</a><br><a href="https://www.ait-pro.com/bps-features/" target="_blank" title="BPS Pro Features">'.__('BPS Pro Features', 'bulletproof-security').'</a></strong><br><br>';	
	echo $bpsPro_text;

	echo $bps_general_help_info; 
	echo $bps_bbm_content; 
	?>
    </p>
</div>

<?php
// BBM Status: real-time status check
// 3 possible BBM Status indicators: Activated, Deactivated or Disabled.
function bpsPro_bbm_status() {
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	$filename = WP_CONTENT_DIR . '/bps-backup/.htaccess';
	
	if ( isset ( $_POST['Submit-BBM-Activate'] ) ) {
		$_POST['Submit-BBM-Activate'] = true;
	} else {
		$_POST['Submit-BBM-Activate'] = null;
	}
	
	if ( isset ( $_POST['Submit-BBM-Deactivate'] ) )  {
		$_POST['Submit-BBM-Deactivate'] = true;
	} else {
		$_POST['Submit-BBM-Deactivate'] = null;
	}

	if ( $_POST['Submit-BBM-Activate'] != true && $_POST['Submit-BBM-Deactivate'] != true ) {
	
		if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<h3><strong>'.__('BBM Status: ', 'bulletproof-security').'<span class="core-status-disabled">'.__('Disabled', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<h3><strong>'.__('BBM Status: ', 'bulletproof-security').'<span class="core-status-deactivated">'.__('Deactivated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;	
		} elseif ( file_exists($filename) ) {
			$text = '<h3><strong>'.__('BBM Status: ', 'bulletproof-security').'<span class="core-status-activated">'.__('Activated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		}
	}

	if ( $_POST['Submit-BBM-Activate'] == true || $_POST['Submit-BBM-Deactivate'] == true ) {
		
		if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<h3><strong>'.__('BBM Status: ', 'bulletproof-security').'<span class="core-status-disabled">'.__('Disabled', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<h3><strong>'.__('BBM Status: ', 'bulletproof-security').'<span class="core-status-deactivated">'.__('Deactivated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;	
		} elseif ( file_exists($filename) ) {
			$text = '<h3><strong>'.__('BBM Status: ', 'bulletproof-security').'<span class="core-status-activated">'.__('Activated', 'bulletproof-security').'</span></strong></h3>';
			echo $text;
		}
	}
}
?>

<div id="BBM-Status"><?php bpsPro_bbm_status(); ?></div>

<div id="bbm-bulletproof-mode" style="">

<form name="BBM-Activate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_bbm_activate'); ?>

	<div id="BBM-buttons" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-BBM-Activate" style="margin:10px 0px 10px 0px;width:84px;height:auto;white-space:normal" value="<?php esc_attr_e('Activate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Activate BBM BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<form name="BBM-Deactivate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_bbm_deactivate'); ?>

	<div id="BBM-buttons" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-BBM-Deactivate" style="margin:10px 0px 10px 0px;width:84px;height:auto;white-space:normal" value="<?php esc_attr_e('Deactivate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Caution: BPS Backup Folder BulletProof Mode (BBM) should only be deactivated for testing or troubleshooting. Be sure to activate BBM BulletProof Mode after you are done testing or troubleshooting.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Deactivate BBM BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

</div>
</div>

<div id="MC3" style="position:relative;top:0px;left:0px;float:left;margin:0px 0px 0px 0px;width:100%;border-top:1px solid #999999;">

<h3><?php _e('Backup & Restore BPS htaccess Files', 'bulletproof-security'); ?> <button id="bps-open-modal8" class="button bps-modal-button">
<img src="<?php echo plugins_url('/bulletproof-security/admin/images/question-mark-large.jpg'); ?>" style="margin:0px 0px 0px -10px" /></button></h3>

<div id="bps-modal-content8" class="bps-dialog-hide" title="<?php _e('Backup & Restore BPS htaccess Files', 'bulletproof-security'); ?>">
	<div id="dialog-anchor" style="position:relative;top:-30px;left:0px"><a href="#"></a></div>
	<p>
	<?php 
	$text = '<strong>'.__('This Question Mark Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
	echo $text;

	$bpsPro_text = '<strong><font color="blue">'.__('Want even more security protection for the ridiculously cheap one-time price of $69.95', 'bulletproof-security').'</font><br>'.__('BPS Pro comes with free unlimited installations, upgrades & support for life. No yearly subscriptions or additional costs.', 'bulletproof-security').'<br><br>'.__('BBS Pro has an amazing track record. BPS Pro is installed on 60,000+ websites. Not a single one of those websites has been hacked in 10+ years.', 'bulletproof-security').'<br><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Get BPS Pro">'.__('Get BPS Pro', 'bulletproof-security').'</a><br><a href="https://www.ait-pro.com/bps-features/" target="_blank" title="BPS Pro Features">'.__('BPS Pro Features', 'bulletproof-security').'</a></strong><br><br>';	
	echo $bpsPro_text;

	echo $bps_backup_restore_content; 
	?>
    </p>
</div>

<div id="backup-restore-mode">

<form name="Backup-htaccess-Files" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_backup_active_htaccess_files'); ?>

	<div id="Backup-htaccess-Files" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-Backup-htaccess-Files" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Backup htaccess Files', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Backup BPS htaccess files or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<form name="Restore-htaccess-Files" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_restore_active_htaccess_files'); ?>

	<div id="Restore-htaccess-Files" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-Restore-htaccess-Files" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Restore htaccess Files', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Restore BPS htaccess files or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

</div>
</div>

</td>
  </tr>
</table>
</div>
            
<div id="bps-tabs-6" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"></td>
  </tr>
  <tr>
    <td class="bps-table_cell">    

<h3 style="margin:0px 0px 5px 5px;"><?php _e('htaccess File Editing', 'bulletproof-security'); ?>  <button id="bps-open-modal9" class="button bps-modal-button">
<img src="<?php echo plugins_url('/bulletproof-security/admin/images/question-mark-large.jpg'); ?>" style="margin:0px 0px 0px -10px" /></button></h3>

<div id="bps-modal-content9" class="bps-dialog-hide" title="<?php _e('htaccess File Editing', 'bulletproof-security'); ?>">  
	<div id="dialog-anchor" style="position:relative;top:-30px;left:0px"><a href="#"></a></div>
	<p>
	<?php 
    $text = '<strong>'.__('This Question Mark Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
	echo $text; 
		
	$bpsPro_text = '<strong><font color="blue">'.__('Want even more security protection for the ridiculously cheap one-time price of $69.95', 'bulletproof-security').'</font><br>'.__('BPS Pro comes with free unlimited installations, upgrades & support for life. No yearly subscriptions or additional costs.', 'bulletproof-security').'<br><br>'.__('BBS Pro has an amazing track record. BPS Pro is installed on 60,000+ websites. Not a single one of those websites has been hacked in 10+ years.', 'bulletproof-security').'<br><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Get BPS Pro">'.__('Get BPS Pro', 'bulletproof-security').'</a><br><a href="https://www.ait-pro.com/bps-features/" target="_blank" title="BPS Pro Features">'.__('BPS Pro Features', 'bulletproof-security').'</a></strong><br><br>';	
	echo $bpsPro_text;	
	
	echo $bps_hfe_content; 
	?>
    </p>
</div>

<table width="100%" border="0">
  <tr>
    <td colspan="2">
    
    <div id="bps_file_editor" class="bps_file_editor_update">

<?php
echo bps_secure_htaccess_file_check();
echo bps_default_htaccess_file_check();
echo bps_wpadmin_htaccess_file_check();

function bpsPro_secure_htaccess_write_check() {
	
	if ( isset ( $_POST['submit1'] ) ) {
		$_POST['submit1'] = true;
	} else {
		$_POST['submit1'] = null;
	}

	if ( $_POST['submit1'] != true ) {

		$secure_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/secure.htaccess';
		$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
		
		if ( isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<font color="blue" style="font-size:12px;"><strong>'.__('htaccess Files Disabled: secure.htaccess Master file is disabled.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
		
		} elseif ( ! file_exists($secure_htaccess_file) && isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('ERROR: A secure.htaccess Master file was NOT found.', 'bulletproof-security').'</strong></font><br>';
			echo $text;	
			
		} else {
			
			if ( file_exists($secure_htaccess_file) ) {	
		
				if ( is_writable($secure_htaccess_file) ) {
		
					$text = '<font color="green" style="font-size:12px;"><strong>'.__('File Open and Write test successful! The secure.htaccess Master file is writable.', 'bulletproof-security').'</strong></font><br>';
					echo $text;

				} else {
				
					$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('Cannot write to file: ', 'bulletproof-security').$secure_htaccess_file . '</strong></font><br>';
					echo $text;
				}	
			}
		}
	}
}
	
bpsPro_secure_htaccess_write_check();

	if ( isset( $_POST['submit1'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_save_settings_1' );
	
		if ( isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			echo $bps_topDiv;
			$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: secure.htaccess Master file writing is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
			echo $text;
    		echo $bps_bottomDiv;
			return;
		}

		$Encryption = new bpsProPHPEncryption();
		$nonceValue = 'ghbhnyxu';
		$secure_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/secure.htaccess';
		
		$pos = strpos( $_POST['newcontent1'], 'eyJjaXBoZXJ0ZXh0Ijoi' );

		if ( $pos === false ) {
			$newcontent1 = stripslashes($_POST['newcontent1']);
		} else {
			$newcontent1 = $Encryption->decrypt($_POST['newcontent1'], $nonceValue);
		}

		if ( ! is_writable($secure_htaccess_file) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to write to the secure.htaccess Master file.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
			echo $bps_bottomDiv;
		
		} else {
	
			if ( ! $handle = fopen($secure_htaccess_file, 'w+b') ) {
				exit;
			}
			
			if ( fwrite($handle, $newcontent1) === false ) {
				exit;
			}
	
				echo $bps_topDiv;
				$text = '<font color="green"><strong>'.__('The secure.htaccess Master file has been updated.', 'bulletproof-security').'</strong></font><br>';
				echo $text;
				echo $bps_bottomDiv;
			
			fclose($handle);
		}
	}

function bpsPro_default_htaccess_write_check() {
	
	if ( isset ( $_POST['submit2'] ) ) {
		$_POST['submit2'] = true;
	} else {
		$_POST['submit2'] = null;
	}

	if ( $_POST['submit2'] != true ) {

		$default_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/default.htaccess';
		$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
		
		if ( isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<font color="blue" style="font-size:12px;"><strong>'.__('htaccess Files Disabled: default.htaccess Master file is disabled.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
		
		} elseif ( ! file_exists($default_htaccess_file) && isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('ERROR: A default.htaccess Master file was NOT found.', 'bulletproof-security').'</strong></font><br>';
			echo $text;	
			
		} else {
			
			if ( file_exists($default_htaccess_file) ) {		
		
				if ( is_writable($default_htaccess_file) ) {
			
					$text = '<font color="green" style="font-size:12px;"><strong>'.__('File Open and Write test successful! The default.htaccess Master file is writable.', 'bulletproof-security').'</strong></font><br>';
					echo $text;

				} else {
				
					$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('Cannot write to file: ', 'bulletproof-security').$default_htaccess_file . '</strong></font><br>';
					echo $text;
				}	
			}
		}
	}
}
	
bpsPro_default_htaccess_write_check();

	if ( isset( $_POST['submit2'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_save_settings_2' );
	
		if ( isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			echo $bps_topDiv;
			$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: default.htaccess Master file writing is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
			echo $text;
    		echo $bps_bottomDiv;
			return;
		}

		$Encryption = new bpsProPHPEncryption();
		$nonceValue = 'ghbhnyxu';
		$default_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/default.htaccess';
		
		$pos = strpos( $_POST['newcontent2'], 'eyJjaXBoZXJ0ZXh0Ijoi' );

		if ( $pos === false ) {
			$newcontent2 = stripslashes($_POST['newcontent2']);
		} else {
			$newcontent2 = $Encryption->decrypt($_POST['newcontent2'], $nonceValue);
		}

		if ( ! is_writable($default_htaccess_file) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to write to the default.htaccess Master file.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
			echo $bps_bottomDiv;
		
		} else {
	
			if ( ! $handle = fopen($default_htaccess_file, 'w+b') ) {
				exit;
			}
			
			if ( fwrite($handle, $newcontent2) === false ) {
				exit;
			}
	
				echo $bps_topDiv;
				$text = '<font color="green"><strong>'.__('The default.htaccess Master file has been updated.', 'bulletproof-security').'</strong></font><br>';
				echo $text;
				echo $bps_bottomDiv;
			
			fclose($handle);
		}
		
		$custom_default_htaccess = WP_CONTENT_DIR . '/bps-backup/master-backups/default.htaccess';

		// .53.9: Save the Custom default.htaccess file to /bps-backup/master-backups/default.htaccess
		if ( ! copy($default_htaccess_file, $custom_default_htaccess) ) {
			echo $bps_topDiv;
			$text = '<strong><font color="#fb0101">'.__('Failed to copy your Custom default.htaccess file: ', 'bulletproof-security').'</font>'.$default_htaccess_file.__(' to: ', 'bulletproof-security').$custom_default_htaccess.__(' Check that the /bps-backup/ and /master-backups/ folders exist and the folder permissions or Ownership for these folders.', 'bulletproof-security').'</strong><br>';
			echo $text;
			echo $bps_bottomDiv;
		} else {
			echo $bps_topDiv;
			$text = '<strong><font color="green">'.__('Your Custom default.htaccess Master file has been successfully saved to: ', 'bulletproof-security').'</font>'.$custom_default_htaccess.'</strong><br>';
			echo $text;
			echo $bps_bottomDiv;
		}
	}

function bpsPro_wpadmin_secure_htaccess_write_check() {

	if ( isset ( $_POST['submit4'] ) ) {
		$_POST['submit4'] = true;
	} else {
		$_POST['submit4'] = null;
	}

	if ( $_POST['submit4'] != true ) {

		$wpadmin_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/wpadmin-secure.htaccess';
		$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
		$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
		$GDMW_options = get_option('bulletproof_security_options_GDMW');	
		
		if ( isset($BPS_wpadmin_Options['bps_wpadmin_restriction']) && $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' || isset($GDMW_options['bps_gdmw_hosting']) && $GDMW_options['bps_gdmw_hosting'] == 'yes' ) {
			$text = '<strong><font color="black">'.__('wpadmin-secure.htaccess file writing is disabled.', 'bulletproof-security').'</font></strong><br>';
			echo $text;
		
		} else {
	
			if ( isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
				$text = '<font color="blue" style="font-size:12px;"><strong>'.__('htaccess Files Disabled: wpadmin-secure.htaccess Master file is disabled.', 'bulletproof-security').'</strong></font><br>';
				echo $text;
		
			} elseif ( ! file_exists($wpadmin_htaccess_file) && isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
				$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('ERROR: A wpadmin-secure.htaccess Master file was NOT found.', 'bulletproof-security').'</strong></font><br>';
				echo $text;	
			
			} else {
			
				if ( file_exists($wpadmin_htaccess_file) ) {	
	
					if ( is_writable($wpadmin_htaccess_file) ) {
	
						$text = '<font color="green" style="font-size:12px;"><strong>'.__('File Open and Write test successful! The wpadmin-secure.htaccess Master file is writable.', 'bulletproof-security').'</strong></font><br>';
						echo $text;
					
					} else {
				
						$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('Cannot write to file: ', 'bulletproof-security').$wpadmin_htaccess_file . '</strong></font><br>';
						echo $text;
					}	
				}
			}
		}
	}
}
	
bpsPro_wpadmin_secure_htaccess_write_check();
	
	if ( isset( $_POST['submit4'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_save_settings_4' );
	
		if ( isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			echo $bps_topDiv;
			$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: wpadmin-secure.htaccess Master file writing is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
			echo $text;
    		echo $bps_bottomDiv;
			return;
		}

		$Encryption = new bpsProPHPEncryption();
		$nonceValue = 'ghbhnyxu';
		$wpadmin_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/wpadmin-secure.htaccess';
		
		$pos = strpos( $_POST['newcontent4'], 'eyJjaXBoZXJ0ZXh0Ijoi' );

		if ( $pos === false ) {
			$newcontent4 = stripslashes($_POST['newcontent4']);
		} else {
			$newcontent4 = $Encryption->decrypt($_POST['newcontent4'], $nonceValue);
		}

		if ( ! is_writable($wpadmin_htaccess_file) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to write to the wpadmin-secure.htaccess Master file.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
			echo $bps_bottomDiv;
		
		} else {
	
			if ( ! $handle = fopen($wpadmin_htaccess_file, 'w+b') ) {
				exit;
			}
			
			if ( fwrite($handle, $newcontent4) === false ) {
				exit;
			}
	
				echo $bps_topDiv;
				$text = '<font color="green"><strong>'.__('The wpadmin-secure.htaccess Master file has been updated.', 'bulletproof-security').'</strong></font><br>';
				echo $text;
				echo $bps_bottomDiv;
			
			fclose($handle);
		}
	}

function bpsPro_root_htaccess_write_check() {

	if ( isset ( $_POST['submit7'] ) ) {
		$_POST['submit7'] = true;
	} else {
		$_POST['submit7'] = null;
	}

	if ( $_POST['submit7'] != true ) {
	
		$root_htaccess_file = ABSPATH . '.htaccess';
		$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
		
		if ( ! file_exists($root_htaccess_file) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<font color="blue" style="font-size:12px;"><strong>'.__('htaccess Files Disabled: Root htaccess file does not exist.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
		
		} elseif ( ! file_exists($root_htaccess_file) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('ERROR: An htaccess file was NOT found in your root folder', 'bulletproof-security').'</strong></font><br>';
			echo $text;	
			
		} else {
			
			if ( file_exists($root_htaccess_file) ) {
	
				if ( is_writable($root_htaccess_file) ) {
				
					$text = '<font color="green" style="font-size:12px;"><strong>'.__('File Open and Write test successful! Your root htaccess file is writable.', 'bulletproof-security').'</strong></font><br>';
					echo $text;
				
				} else {
				
					$text = '<font color="blue" style="font-size:12px;"><strong>'.__('Your root htaccess file is Locked with Read Only Permissions.', 'bulletproof-security').'<br>'.__('Use the Lock and Unlock buttons below to Lock or Unlock your root htaccess file for editing.', 'bulletproof-security').'</strong></font><br>';
					echo $text;
				}
			}
		}
	}
}

bpsPro_root_htaccess_write_check();
	
	if ( isset( $_POST['submit5'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_save_settings_5' );
	
		if ( isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			echo $bps_topDiv;
			$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: Root htaccess file writing is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
			echo $text;
    		echo $bps_bottomDiv;
			return;
		}

		$Encryption = new bpsProPHPEncryption();
		$nonceValue = 'ghbhnyxu';
		$root_htaccess_file = ABSPATH . '.htaccess';
		
		$pos = strpos( $_POST['newcontent5'], 'eyJjaXBoZXJ0ZXh0Ijoi' );

		if ( $pos === false ) {
			$newcontent5 = stripslashes($_POST['newcontent5']);
		} else {
			$newcontent5 = $Encryption->decrypt($_POST['newcontent5'], $nonceValue);
		}

		if ( ! is_writable($root_htaccess_file) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to write to the Root htaccess file. If your Root htaccess file is locked you must unlock first.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
			echo $bps_bottomDiv;
		
		} else {
	
			if ( ! $handle = fopen($root_htaccess_file, 'w+b') ) {
				exit;
			}
			
			if ( fwrite($handle, $newcontent5) === false ) {
				exit;
			}
	
				echo $bps_topDiv;
				$text = '<font color="green"><strong>'.__('Your root htaccess file has been updated.', 'bulletproof-security').'</strong></font><br>';
				echo $text;
				echo $bps_bottomDiv;
			
			fclose($handle);
		}
	}

function bpsPro_wpadmin_htaccess_write_check() {

	if ( isset ( $_POST['submit8'] ) ) {
		$_POST['submit8'] = true;
	} else {
		$_POST['submit8'] = null;
	}

	if ( $_POST['submit8'] != true ) {

		$current_wpadmin_htaccess_file = ABSPATH . 'wp-admin/.htaccess';
		$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
		$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
		$GDMW_options = get_option('bulletproof_security_options_GDMW');	
		
		if ( isset($BPS_wpadmin_Options['bps_wpadmin_restriction']) && $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' || isset($GDMW_options['bps_gdmw_hosting']) && $GDMW_options['bps_gdmw_hosting'] == 'yes' ) {
			$text = '<font color="blue" style="font-size:12px;"><strong>'.__('wp-admin active htaccess file writing is disabled.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
		
		} else {
	
			if ( ! file_exists($current_wpadmin_htaccess_file) && isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
				$text = '<font color="blue" style="font-size:12px;"><strong>'.__('htaccess Files Disabled: wp-admin folder htaccess file does not exist.', 'bulletproof-security').'</strong></font><br>';
				echo $text;
		
			} elseif ( ! file_exists($current_wpadmin_htaccess_file) && isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
				$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('ERROR: An htaccess file was NOT found in your wp-admin folder', 'bulletproof-security').'</strong></font><br>';
				echo $text;	
			
			} else {
			
				if ( file_exists($current_wpadmin_htaccess_file) ) {
	
					if ( is_writable($current_wpadmin_htaccess_file) ) {
			
						$text = '<font color="green" style="font-size:12px;"><strong>'.__('File Open and Write test successful! Your wp-admin htaccess file is writable.', 'bulletproof-security').'</strong></font><br>';
						echo $text;
				
					} else {
		
						$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('Cannot write to file: ', 'bulletproof-security').$current_wpadmin_htaccess_file . '</strong></font><br>';
						echo $text;
					}
				}
			}
		}
	}
}
	
bpsPro_wpadmin_htaccess_write_check();
	
	if ( isset( $_POST['submit6'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_save_settings_6' );
	
		if ( isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			echo $bps_topDiv;
			$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: wp-admin htaccess file writing is disabled. ', 'bulletproof-security').'</strong></font>'.__('Click this link for help information: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-files-disabled-setup-wizard-enable-disable-htaccess-files/" target="_blank" title="htaccess Files Disabled Forum Topic">'.__('htaccess Files Disabled Forum Topic', 'bulletproof-security').'</a><br>';
			echo $text;
    		echo $bps_bottomDiv;
			return;
		}

		$Encryption = new bpsProPHPEncryption();
		$nonceValue = 'ghbhnyxu';
		$current_wpadmin_htaccess_file = ABSPATH . 'wp-admin/.htaccess';
		
		$pos = strpos( $_POST['newcontent6'], 'eyJjaXBoZXJ0ZXh0Ijoi' );

		if ( $pos === false ) {
			$newcontent6 = stripslashes($_POST['newcontent6']);
		} else {
			$newcontent6 = $Encryption->decrypt($_POST['newcontent6'], $nonceValue);
		}

		if ( ! is_writable($current_wpadmin_htaccess_file) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to write to the wp-admin htaccess file.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
			echo $bps_bottomDiv;
		
		} else {
	
			if ( ! $handle = fopen($current_wpadmin_htaccess_file, 'w+b') ) {
				exit;
			}
			
			if ( fwrite($handle, $newcontent6) === false ) {
				exit;
			}
	
				echo $bps_topDiv;
				$text = '<font color="green"><strong>'.__('Your wp-admin htaccess file has been updated.', 'bulletproof-security').'</strong></font><br>';
				echo $text;
				echo $bps_bottomDiv;
			
			fclose($handle);
		}
	}
	
// Lock and Unlock Root .htaccess file 
if ( isset( $_POST['submit-ProFlockLock'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_flock_lock' );

	$bpsRootHtaccessOL = ABSPATH . '.htaccess';
	
	if ( file_exists($bpsRootHtaccessOL) ) {
		chmod($bpsRootHtaccessOL, 0404);
		echo $bps_topDiv;
		$text = '<font color="green"><strong><br>'.__('Your Root htaccess file has been Locked.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
		echo $bps_bottomDiv;
	} else {
		echo $bps_topDiv;
		$text = '<font color="#fb0101"><strong><br>'.__('Unable to Lock your Root htaccess file.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
		echo $bps_bottomDiv;
	}
}
	
if ( isset( $_POST['submit-ProFlockUnLock'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_flock_unlock' );
	
	$bpsRootHtaccessOL = ABSPATH . '.htaccess';
		
	if ( file_exists($bpsRootHtaccessOL) ) {
		chmod($bpsRootHtaccessOL, 0644);
		echo $bps_topDiv;
		$text = '<font color="green"><strong><br>'.__('Your Root htaccess file has been Unlocked.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
		echo $bps_bottomDiv;
	} else {
		echo $bps_topDiv;
		$text = '<font color="#fb0101"><strong><br>'.__('Unable to Unlock your Root htaccess file.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
		echo $bps_bottomDiv;
	}
}
?>

</div>

</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>

<?php // Detect the SAPI - display form submit button only if sapi is cgi
	$sapi_type = php_sapi_name();
	if ( substr($sapi_type, 0, 6) != 'apache' ) {	
?>    
 
<div id="bpsLockHtaccess">  
<form name="bpsFlockLockForm" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_flock_lock'); ?>
	<input type="submit" name="submit-ProFlockLock" value="<?php esc_attr_e('Lock htaccess File', 'bulletproof-security'); ?>" class="button bps-button" style="width:138px;height:auto;white-space:normal" onclick="return confirm('<?php $text = __('Click OK to Lock your Root htaccess file or click Cancel.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Note: The File Open and Write Test window will still display the last status of the file as Unlocked. To see the current status refresh your browser.', 'bulletproof-security'); echo $text; ?>')" />
</form>
</div>

<div id="bpsUnLockHtaccess">    
<form name="bpsFlockUnLockForm" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_flock_unlock'); ?>

	<input type="submit" name="submit-ProFlockUnLock" value="<?php esc_attr_e('Unlock htaccess File', 'bulletproof-security'); ?>" class="button bps-button" style="width:138px;height:auto;white-space:normal" onclick="return confirm('<?php $text = __('Click OK to Unlock your Root htaccess file or click Cancel.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Note: The File Open and Write Test window will still display the last status of the file as Locked. To see the current status refresh your browser.', 'bulletproof-security'); echo $text; ?>')" />
</form>
</div>

<div id="bpsAutoLockOn">
<form name="bpsRootAutoLock-On" action="options.php#bps-tabs-6" method="post">
    <?php settings_fields('bulletproof_security_options_autolock'); ?>
	<?php $options = get_option('bulletproof_security_options_autolock'); ?>
	<input type="hidden" name="bulletproof_security_options_autolock[bps_root_htaccess_autolock]" value="On" />
	<input type="submit" name="submit-RootHtaccessAutoLock-On" value="<?php esc_attr_e('Turn On AutoLock', 'bulletproof-security'); ?>" class="button bps-button" style="width:138px;height:auto;white-space:normal" onclick="return confirm('<?php $text = __('Turning AutoLock On will allow BPS Pro to automatically lock your Root .htaccess file. For some folks this causes a problem because their Web Hosts do not allow the Root .htaccess file to be locked. For most folks allowing BPS Pro to AutoLock the Root .htaccess file works fine.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Turn AutoLock On or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />

<?php if ( isset($options['bps_root_htaccess_autolock']) && $options['bps_root_htaccess_autolock'] == '' ||isset($options['bps_root_htaccess_autolock']) && $options['bps_root_htaccess_autolock'] == 'On' ) { echo '<label class="autolock_status" style="font-weight:bold;">'.__('On', 'bulletproof-security').'</label>'; } ?>

</form>
</div>

<div id="bpsAutoLockOff">
<form name="bpsRootAutoLock-Off" action="options.php#bps-tabs-6" method="post">
    <?php settings_fields('bulletproof_security_options_autolock'); ?>
	<?php $options = get_option('bulletproof_security_options_autolock'); ?>
	<input type="hidden" name="bulletproof_security_options_autolock[bps_root_htaccess_autolock]" value="Off" />
	<input type="submit" name="submit-RootHtaccessAutoLock-Off" value="<?php esc_attr_e('Turn Off AutoLock', 'bulletproof-security'); ?>" class="button bps-button" style="width:138px;height:auto;white-space:normal" onclick="return confirm('<?php $text = __('Turning AutoLock Off will prevent BPS Pro from automatically locking your Root .htaccess file. For some folks this is necessary because their Web Hosts do not allow the Root .htaccess file to be locked. For most folks allowing BPS Pro to AutoLock the Root .htaccess file works fine.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Turn AutoLock Off or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />

<?php if ( isset($options['bps_root_htaccess_autolock']) && $options['bps_root_htaccess_autolock'] == 'Off') { echo '<label class="autolock_status" style="font-weight:bold;">'.__('Off', 'bulletproof-security').'</label>'; } ?>

</form>
</div>

<?php } ?>

</td>
  <tr>
    <td colspan="2">
    
    <!-- jQuery UI File Editor Tab Menu -->
<div id="bps-edittabs" class="bps-edittabs-class">
		
	<ul>
		<li><a href="#bps-edittabs-1"><?php _e('secure.htaccess', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-edittabs-2"><?php _e('default.htaccess', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-edittabs-4"><?php _e('wpadmin-secure.htaccess', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-edittabs-5"><?php _e('Root htaccess File', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-edittabs-6"><?php _e('wp-admin htaccess File', 'bulletproof-security'); ?></a></li>
	</ul>
       
<?php 
$scrollto1 = isset($_REQUEST['scrollto1']) ? (int) $_REQUEST['scrollto1'] : 0; 
$scrollto2 = isset($_REQUEST['scrollto2']) ? (int) $_REQUEST['scrollto2'] : 0;
$scrollto4 = isset($_REQUEST['scrollto4']) ? (int) $_REQUEST['scrollto4'] : 0;
$scrollto5 = isset($_REQUEST['scrollto5']) ? (int) $_REQUEST['scrollto5'] : 0;
$scrollto6 = isset($_REQUEST['scrollto6']) ? (int) $_REQUEST['scrollto6'] : 0;
?>

<div id="bps-edittabs-1" class="bps-edittabs-page-class">
<form name="template1" id="template1" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_save_settings_1'); 
	$secure_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/secure.htaccess';
?>
    <div>
    <textarea id="crypt21" class="bps-text-area-600x700" name="newcontent1" id="newcontent1" tabindex="1"><?php bps_get_secure_htaccess(); ?></textarea>
	<input type="hidden" name="action" value="update" />
    <input type="hidden" name="filename" value="<?php echo esc_attr( $secure_htaccess_file ) ?>" />
	<input type="hidden" name="scrollto1" id="scrollto1" value="<?php echo esc_html( $scrollto1 ); ?>" />
    
    <p class="submit">

	<?php echo '<div id="bps-edittabs-tooltip" style="margin:-40px 0px 10px 0px"><label for="bps-mscan-label" style="">'.__('If you see an error or are unable to save your editing changes then click the Encrypt htaccess Code button first and then click the Update File button. Mouse over the question mark image to the right for help info.', 'bulletproof-security').'</label><strong><font color="black"><span class="tooltip-350-225"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:5px;" /><span>'.__('If your web host currently has ModSecurity installed or installs ModSecurity at a later time then ModSecurity will prevent you from saving your htaccess code unless you encrypt it first by clicking the Encrypt htaccess Code button.', 'bulletproof-security').'<br><br>'.__('If you click the Encrypt htaccess Code button and then want to edit your code again click the Decrypt htaccess Code button. After you are done editing click the Encrypt htaccess Code button before clicking the Update File button.', 'bulletproof-security').'<br><br>'.__('Click the htaccess File Editing Question Mark help button for more help info.', 'bulletproof-security').'</span></span></font></strong></div>'; ?>

	<input type="submit" name="submit1" class="button bps-button" value="<?php esc_attr_e('Update File', 'bulletproof-security') ?>" />
    </p>
</div>
</form>

	<button onclick="bpsSecureFileEncrypt()" class="button bps-encrypt-button"><?php esc_attr_e('Encrypt htaccess Code', 'bulletproof-security'); ?></button> 
	<button onclick="bpsSecureFileDecrypt()" class="button bps-decrypt-button"><?php esc_attr_e('Decrypt htaccess Code', 'bulletproof-security'); ?></button>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#template1').submit(function(){ $('#scrollto1').val( $('#newcontent1').scrollTop() ); });
	$('#newcontent1').scrollTop( $('#scrollto1').val() ); 
});

function bpsSecureFileEncrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt21").value;
  
  // Prevent Double, Triple, etc. encryption
  // The includes() method is not supported in IE 11 (and earlier versions)
  var NoEncrypt1 = CCString1.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt1);

  let encryption = new bpsProJSEncryption();

  if (CCString1 != '' && NoEncrypt1 === false) {
  var encrypted1 = encryption.encrypt(CCString1, nonceValue);
  }
  //console.log(encrypted1); 
  
  if (CCString1 != '' && NoEncrypt1 === false) {
  document.getElementById("crypt21").value = encrypted1;
  }
}

function bpsSecureFileDecrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt21").value;

  let encryption = new bpsProJSEncryption();

  if (CCString1 != '') {
  var decrypted1 = encryption.decrypt(CCString1, nonceValue);
  }
  //console.log(decrypted1);
  
  if (CCString1 != '') {
  document.getElementById("crypt21").value = decrypted1;
  }
}
/* ]]> */
</script>     
</div>

<div id="bps-edittabs-2" class="bps-edittabs-page-class">
<form name="template2" id="template2" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_save_settings_2'); 
	$default_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/default.htaccess';
?>
	<div>
    <textarea id="crypt22" class="bps-text-area-600x700" name="newcontent2" id="newcontent2" tabindex="2"><?php bps_get_default_htaccess(); ?></textarea>
	<input type="hidden" name="action" value="update" />
    <input type="hidden" name="filename" value="<?php echo esc_attr( $default_htaccess_file ) ?>" />
	<input type="hidden" name="scrollto2" id="scrollto2" value="<?php echo esc_html( $scrollto2 ); ?>" />
    
    <p class="submit">

	<?php echo '<div id="bps-edittabs-tooltip" style="margin:-40px 0px 10px 0px"><label for="bps-mscan-label" style="">'.__('If you see an error or are unable to save your editing changes then click the Encrypt htaccess Code button first and then click the Update File button. Mouse over the question mark image to the right for help info.', 'bulletproof-security').'</label><strong><font color="black"><span class="tooltip-350-225"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:5px;" /><span>'.__('If your web host currently has ModSecurity installed or installs ModSecurity at a later time then ModSecurity will prevent you from saving your htaccess code unless you encrypt it first by clicking the Encrypt htaccess Code button.', 'bulletproof-security').'<br><br>'.__('If you click the Encrypt htaccess Code button and then want to edit your code again click the Decrypt htaccess Code button. After you are done editing click the Encrypt htaccess Code button before clicking the Update File button.', 'bulletproof-security').'<br><br>'.__('Click the htaccess File Editing Question Mark help button for more help info.', 'bulletproof-security').'</span></span></font></strong></div>'; ?>

	<input type="submit" name="submit2" class="button bps-button" value="<?php esc_attr_e('Update File', 'bulletproof-security') ?>" />
    </p>
</div>

	<button onclick="bpsDefaultFileEncrypt()" class="button bps-encrypt-button"><?php esc_attr_e('Encrypt htaccess Code', 'bulletproof-security'); ?></button> 
	<button onclick="bpsDefaultFileDecrypt()" class="button bps-decrypt-button"><?php esc_attr_e('Decrypt htaccess Code', 'bulletproof-security'); ?></button>

</form>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#template2').submit(function(){ $('#scrollto2').val( $('#newcontent2').scrollTop() ); });
	$('#newcontent2').scrollTop( $('#scrollto2').val() );
});

function bpsDefaultFileEncrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt22").value;
  
  // Prevent Double, Triple, etc. encryption
  // The includes() method is not supported in IE 11 (and earlier versions)
  var NoEncrypt1 = CCString1.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt1);

  let encryption = new bpsProJSEncryption();

  if (CCString1 != '' && NoEncrypt1 === false) {
  var encrypted1 = encryption.encrypt(CCString1, nonceValue);
  }
  //console.log(encrypted1); 
  
  if (CCString1 != '' && NoEncrypt1 === false) {
  document.getElementById("crypt22").value = encrypted1;
  }
}

function bpsDefaultFileDecrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt22").value;

  let encryption = new bpsProJSEncryption();

  if (CCString1 != '') {
  var decrypted1 = encryption.decrypt(CCString1, nonceValue);
  }
  //console.log(decrypted1);
  
  if (CCString1 != '') {
  document.getElementById("crypt22").value = decrypted1;
  }
}
/* ]]> */
</script>     
</div>

<div id="bps-edittabs-4" class="bps-edittabs-page-class">
<form name="template4" id="template4" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_save_settings_4'); 
	$wpadmin_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/wpadmin-secure.htaccess';
?>
	<div>
    <textarea id="crypt23" class="bps-text-area-600x700" name="newcontent4" id="newcontent4" tabindex="4"><?php bps_get_wpadmin_htaccess(); ?></textarea>
	<input type="hidden" name="action" value="update" />
    <input type="hidden" name="filename" value="<?php echo esc_attr( $wpadmin_htaccess_file ) ?>" />
	<input type="hidden" name="scrollto4" id="scrollto4" value="<?php echo esc_html( $scrollto4 ); ?>" />
    
    <p class="submit">

	<?php echo '<div id="bps-edittabs-tooltip" style="margin:-40px 0px 10px 0px"><label for="bps-mscan-label" style="">'.__('If you see an error or are unable to save your editing changes then click the Encrypt htaccess Code button first and then click the Update File button. Mouse over the question mark image to the right for help info.', 'bulletproof-security').'</label><strong><font color="black"><span class="tooltip-350-225"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:5px;" /><span>'.__('If your web host currently has ModSecurity installed or installs ModSecurity at a later time then ModSecurity will prevent you from saving your htaccess code unless you encrypt it first by clicking the Encrypt htaccess Code button.', 'bulletproof-security').'<br><br>'.__('If you click the Encrypt htaccess Code button and then want to edit your code again click the Decrypt htaccess Code button. After you are done editing click the Encrypt htaccess Code button before clicking the Update File button.', 'bulletproof-security').'<br><br>'.__('Click the htaccess File Editing Question Mark help button for more help info.', 'bulletproof-security').'</span></span></font></strong></div>'; ?>

	<input type="submit" name="submit4" class="button bps-button" value="<?php esc_attr_e('Update File', 'bulletproof-security') ?>" />
    </p>
</div>
</form>

	<button onclick="bpsWpadminSecureFileEncrypt()" class="button bps-encrypt-button"><?php esc_attr_e('Encrypt htaccess Code', 'bulletproof-security'); ?></button> 
	<button onclick="bpsWpadminSecureFileDecrypt()" class="button bps-decrypt-button"><?php esc_attr_e('Decrypt htaccess Code', 'bulletproof-security'); ?></button>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#template4').submit(function(){ $('#scrollto4').val( $('#newcontent4').scrollTop() ); });
	$('#newcontent4').scrollTop( $('#scrollto4').val() );
});

function bpsWpadminSecureFileEncrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt23").value;
  
  // Prevent Double, Triple, etc. encryption
  // The includes() method is not supported in IE 11 (and earlier versions)
  var NoEncrypt1 = CCString1.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt1);

  let encryption = new bpsProJSEncryption();

  if (CCString1 != '' && NoEncrypt1 === false) {
  var encrypted1 = encryption.encrypt(CCString1, nonceValue);
  }
  //console.log(encrypted1); 
  
  if (CCString1 != '' && NoEncrypt1 === false) {
  document.getElementById("crypt23").value = encrypted1;
  }
}

function bpsWpadminSecureFileDecrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt23").value;

  let encryption = new bpsProJSEncryption();

  if (CCString1 != '') {
  var decrypted1 = encryption.decrypt(CCString1, nonceValue);
  }
  //console.log(decrypted1);
  
  if (CCString1 != '') {
  document.getElementById("crypt23").value = decrypted1;
  }
}
/* ]]> */
</script>     
</div>

<?php
// File Editor Root .htaccess file Lock check with pop up Confirm message
function bpsStatusRHE() {

	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( isset($HFiles_options['bps_htaccess_files']) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		$perms = '';
		
		return $perms;
	}
	
	clearstatcache();
	$filename = ABSPATH . '.htaccess';
	$sapi_type = php_sapi_name();
	
	if ( file_exists($filename) && substr($sapi_type, 0, 6) != 'apache') {
		$perms = substr(sprintf('%o', fileperms($filename)), -4);
		
		return $perms;
	}
}
?>

<div id="bps-edittabs-5" class="bps-edittabs-page-class">
<form name="template5" id="template5" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_save_settings_5'); 
	$root_htaccess_file = ABSPATH . '.htaccess';
	$perms = '';
?>
	<div>
    <textarea id="crypt26" class="bps-text-area-600x700" name="newcontent5" id="newcontent5" tabindex="5"><?php bps_get_root_htaccess(); ?></textarea>
	<input type="hidden" name="action" value="update" />
    <input type="hidden" name="filename" value="<?php echo esc_attr( $root_htaccess_file ) ?>" />
	<input type="hidden" name="scrollto5" id="scrollto5" value="<?php echo esc_html( $scrollto5 ); ?>" />
    
    <p class="submit">
    
	<?php if ( bpsStatusRHE($perms) == '0404' ) { ?>
	
    <input type="submit" name="submit5" value="<?php esc_attr_e('Update File', 'bulletproof-security') ?>" class="button bps-button" onClick="return confirm('<?php $text = __('YOUR ROOT HTACCESS FILE IS LOCKED.', 'bulletproof-security').'\n\n'.__('YOUR FILE EDITS|CHANGES CANNOT BE SAVED.', 'bulletproof-security').'\n\n'.__('Click Cancel, copy the file editing changes you made to save them and then click the Unlock .htaccess File button to unlock your Root .htaccess file. After your Root .htaccess file is unlocked paste your file editing changes back into your Root .htaccess file and click this Update File button again to save your file edits/changes.', 'bulletproof-security'); echo $text; ?>')" />
	
	<?php } else { ?>

	<?php echo '<div id="bps-edittabs-tooltip" style="margin:-40px 0px 10px 0px"><label for="bps-mscan-label" style="">'.__('If you see an error or are unable to save your editing changes then click the Encrypt htaccess Code button first and then click the Update File button. Mouse over the question mark image to the right for help info.', 'bulletproof-security').'</label><strong><font color="black"><span class="tooltip-350-225"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:5px;" /><span>'.__('If your web host currently has ModSecurity installed or installs ModSecurity at a later time then ModSecurity will prevent you from saving your htaccess code unless you encrypt it first by clicking the Encrypt htaccess Code button.', 'bulletproof-security').'<br><br>'.__('If you click the Encrypt htaccess Code button and then want to edit your code again click the Decrypt htaccess Code button. After you are done editing click the Encrypt htaccess Code button before clicking the Update File button.', 'bulletproof-security').'<br><br>'.__('Click the htaccess File Editing Question Mark help button for more help info.', 'bulletproof-security').'</span></span></font></strong></div>'; ?>

	<input type="submit" name="submit5" class="button bps-button" value="<?php esc_attr_e('Update File', 'bulletproof-security') ?>" />
    </p>
<?php } ?>

</div>
</form>

	<button onclick="bpsRootFileEncrypt()" class="button bps-encrypt-button"><?php esc_attr_e('Encrypt htaccess Code', 'bulletproof-security'); ?></button> 
	<button onclick="bpsRootFileDecrypt()" class="button bps-decrypt-button"><?php esc_attr_e('Decrypt htaccess Code', 'bulletproof-security'); ?></button>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#template5').submit(function(){ $('#scrollto5').val( $('#newcontent5').scrollTop() ); });
	$('#newcontent5').scrollTop( $('#scrollto5').val() );
});

function bpsRootFileEncrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt26").value;
  
  // Prevent Double, Triple, etc. encryption
  // The includes() method is not supported in IE 11 (and earlier versions)
  var NoEncrypt1 = CCString1.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt1);

  let encryption = new bpsProJSEncryption();

  if (CCString1 != '' && NoEncrypt1 === false) {
  var encrypted1 = encryption.encrypt(CCString1, nonceValue);
  }
  //console.log(encrypted1); 
  
  if (CCString1 != '' && NoEncrypt1 === false) {
  document.getElementById("crypt26").value = encrypted1;
  }
}

function bpsRootFileDecrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt26").value;

  let encryption = new bpsProJSEncryption();

  if (CCString1 != '') {
  var decrypted1 = encryption.decrypt(CCString1, nonceValue);
  }
  //console.log(decrypted1);
  
  if (CCString1 != '') {
  document.getElementById("crypt26").value = decrypted1;
  }
}
/* ]]> */
</script>     
</div>

<div id="bps-edittabs-6" class="bps-edittabs-page-class">
<form name="template6" id="template6" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_save_settings_6'); 
	$current_wpadmin_htaccess_file = ABSPATH . 'wp-admin/.htaccess';
?>
	<div>
    <textarea id="crypt27" class="bps-text-area-600x700" name="newcontent6" id="newcontent6" tabindex="6"><?php bps_get_current_wpadmin_htaccess_file(); ?></textarea>
	<input type="hidden" name="action" value="update" />
    <input type="hidden" name="filename" value="<?php echo esc_attr( $current_wpadmin_htaccess_file ) ?>" />
	<input type="hidden" name="scrollto6" id="scrollto6" value="<?php echo esc_html( $scrollto6 ); ?>" />
    
    <p class="submit">

	<?php echo '<div id="bps-edittabs-tooltip" style="margin:-40px 0px 10px 0px"><label for="bps-mscan-label" style="">'.__('If you see an error or are unable to save your editing changes then click the Encrypt htaccess Code button first and then click the Update File button. Mouse over the question mark image to the right for help info.', 'bulletproof-security').'</label><strong><font color="black"><span class="tooltip-350-225"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:5px;" /><span>'.__('If your web host currently has ModSecurity installed or installs ModSecurity at a later time then ModSecurity will prevent you from saving your htaccess code unless you encrypt it first by clicking the Encrypt htaccess Code button.', 'bulletproof-security').'<br><br>'.__('If you click the Encrypt htaccess Code button and then want to edit your code again click the Decrypt htaccess Code button. After you are done editing click the Encrypt htaccess Code button before clicking the Update File button.', 'bulletproof-security').'<br><br>'.__('Click the htaccess File Editing Question Mark help button for more help info.', 'bulletproof-security').'</span></span></font></strong></div>'; ?>

	<input type="submit" name="submit6" class="button bps-button" value="<?php esc_attr_e('Update File', 'bulletproof-security') ?>" />
    </p>
</div>
</form>

	<button onclick="bpsWpadminFileEncrypt()" class="button bps-encrypt-button"><?php esc_attr_e('Encrypt htaccess Code', 'bulletproof-security'); ?></button> 
	<button onclick="bpsWpadminFileDecrypt()" class="button bps-decrypt-button"><?php esc_attr_e('Decrypt htaccess Code', 'bulletproof-security'); ?></button>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#template6').submit(function(){ $('#scrollto6').val( $('#newcontent6').scrollTop() ); });
	$('#newcontent6').scrollTop( $('#scrollto6').val() );
});

function bpsWpadminFileEncrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt27").value;
  
  // Prevent Double, Triple, etc. encryption
  // The includes() method is not supported in IE 11 (and earlier versions)
  var NoEncrypt1 = CCString1.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt1);

  let encryption = new bpsProJSEncryption();

  if (CCString1 != '' && NoEncrypt1 === false) {
  var encrypted1 = encryption.encrypt(CCString1, nonceValue);
  }
  //console.log(encrypted1); 
  
  if (CCString1 != '' && NoEncrypt1 === false) {
  document.getElementById("crypt27").value = encrypted1;
  }
}

function bpsWpadminFileDecrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt27").value;

  let encryption = new bpsProJSEncryption();

  if (CCString1 != '') {
  var decrypted1 = encryption.decrypt(CCString1, nonceValue);
  }
  //console.log(decrypted1);
  
  if (CCString1 != '') {
  document.getElementById("crypt27").value = decrypted1;
  }
}
/* ]]> */
</script>     
</div>
</div>

</td>
  </tr>
</table>

</td>
  </tr>
</table>
</div>

<div id="bps-tabs-7" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">
    
<h3 style="margin:0px 0px 5px 0px;"><?php _e('Custom Code', 'bulletproof-security'); ?>  <button id="bps-open-modal10" class="button bps-modal-button">
<img src="<?php echo plugins_url('/bulletproof-security/admin/images/question-mark-large.jpg'); ?>" style="margin:0px 0px 0px -10px" /></button></h3>

<div id="bps-modal-content10" class="bps-dialog-hide" title="<?php _e('Custom Code', 'bulletproof-security'); ?>">
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
	<strong><a href="https://forum.ait-pro.com/video-tutorials/" title="Custom Code Video Tutorial" target="_blank"><?php _e('Custom Code Video Tutorial', 'bulletproof-security'); ?></a></strong><br />

	<strong><a href="https://forum.ait-pro.com/read-me-first/" title="BulletProof Security Pro Forum" target="_blank"><?php _e('BulletProof Security Pro Forum', 'bulletproof-security'); ?></a></strong><br />

	<strong><a href="https://forum.ait-pro.com/forums/topic/protect-login-page-from-brute-force-login-attacks/" title="Brute Force Login Page Protection code" target="_blank"><?php _e('Brute Force Login Page Protection code', 'bulletproof-security'); ?></a></strong><br /><br />

	<?php echo $bps_customcode_content; ?>
    
    </p>
</div>

<table width="100%" border="0">
  <tr>
    <td style="width:615px;">
    
<?php 

	require_once WP_PLUGIN_DIR . '/bulletproof-security/admin/core/core-custom-code.php';
	require_once WP_PLUGIN_DIR . '/bulletproof-security/admin/core/core-export-import.php';
?>

    </td>
    <td>

<div id="CC-Import" style="margin-top:18px">
<form name="bpsImport" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-7' ); ?>" method="post" enctype="multipart/form-data">
	<?php wp_nonce_field('bulletproof_security_cc_import'); ?>
	<input type="file" name="bps_cc_import" id="bps_cc_import" />
	<input type="submit" name="Submit-CC-Import" class="button bps-button" style="margin-top:1px;" value="<?php esc_attr_e('Import', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Clicking OK will Import all of your Root and wp-admin Custom Code from the cc-master.zip file on your computer.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Import Custom Code or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	<?php bpsPro_CC_Import(); ?>
</form>
</div>

<div id="CC-Export">
<form name="bpsExport" id="bpsExport" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-7' ); ?>" method="post">
	<?php wp_nonce_field('bulletproof_security_cc_export'); ?>
    <input type="submit" name="Submit-CC-Export" class="button bps-button" value="<?php esc_attr_e('Export', 'bulletproof-security') ?>" onclick="return confirm('<?php 
$text = __('Clicking OK will Export (copy) all of your Root and wp-admin Custom Code into the cc-master.zip file, which you can then download to your computer by clicking the Download Zip Export button displayed in the Custom Code Export success message.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Export Custom Code or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	<?php bpsPro_CC_Export(); ?>
</form>
</div>

<div id="CC-Delete">
<form name="bpsDeleteCC" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-7' ); ?>" method="post">
	<?php wp_nonce_field('bulletproof_security_cc_delete'); ?>
	<input type="submit" name="Submit-CC-Delete" class="button bps-button" value="<?php esc_attr_e('Delete', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Clicking OK will delete all of your Root and wp-admin Custom Code from all of the Custom Code text boxes.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Delete Custom Code or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	<?php bpsPro_CC_Delete(); ?>
</form>
</div>

    </td>
  </tr>
</table>

<div id="bps-whitespace-275" style="min-height:275px"></div>

</td>
  </tr>
</table>

</div>

<div id="bps-tabs-9" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">
    
    <h3 style="margin-top:0px"><?php _e('Save Personal Notes and htaccess Code Notes to your WordPress Database', 'bulletproof-security'); ?></h3>
	
<?php 
// My Notes Form
function bpsPro_My_Notes_values_form() {
global $bps_topDiv, $bps_bottomDiv;

	if ( isset( $_POST['myNotes_submit'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_My_Notes' );
		
		$Encryption = new bpsProPHPEncryption();
		$nonceValue = 'ghbhnyxu';
		
		$pos = strpos( $_POST['bps_my_notes'], 'eyJjaXBoZXJ0ZXh0Ijoi' );

		if ( $pos === false ) {
			$bps_my_notes = stripslashes($_POST['bps_my_notes']);
		} else {
			$bps_my_notes = $Encryption->decrypt($_POST['bps_my_notes'], $nonceValue);
		}
		
		$MyNotes_Options = array( 'bps_my_notes' => $bps_my_notes );

		foreach( $MyNotes_Options as $key => $value ) {
			update_option('bulletproof_security_options_mynotes', $MyNotes_Options);
		}		
	
	echo $bps_topDiv;
	$text = '<strong><font color="green">'.__('Your My Notes Personal Notes and/or htaccess Code Notes saved successfully to your WordPress Database.', 'bulletproof-security').'</font></strong>';
	echo $text;		
	echo $bps_bottomDiv;	
	
	}
}	
	
	$scrolltoNotes = isset( $_REQUEST['scrolltoNotes'] ) ? (int) $_REQUEST['scrolltoNotes'] : 0; 
?>

<div id="my-notes-float" style="float:left">

	<button onclick="bpsMyNotesEncrypt()" class="button bps-encrypt-button"><?php esc_attr_e('Encrypt My Notes', 'bulletproof-security'); ?></button> 
	<button onclick="bpsMyNotesDecrypt()" class="button bps-decrypt-button" style="margin:0px 0px 10px 0px"><?php esc_attr_e('Decrypt My Notes', 'bulletproof-security'); ?></button>

<form name="myNotes" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-8' ); ?>" method="post">
<?php 
	wp_nonce_field('bulletproof_security_My_Notes'); 
	bpsPro_My_Notes_values_form();
	$My_Notes_options = get_option('bulletproof_security_options_mynotes');
	$bps_my_notes = ! empty( $My_Notes_options['bps_my_notes'] ) ? $My_Notes_options['bps_my_notes'] : '';
	// note: esc_textarea() is not needed here because the DB value is already converted to HTML entities.
	// What is echoed in the textarea input is the DB value, not POST.
?>

	<textarea id="crypt20" class="bps-text-area-600x700" name="bps_my_notes" tabindex="1"><?php echo $bps_my_notes; ?></textarea>
    <input type="hidden" name="scrolltoNotes" value="<?php echo esc_html( $scrolltoNotes ); ?>" />
	
	<?php echo '<div id="bps-my-notes-tooltip"><label for="bps-mscan-label" style="">'.__('If you are unable to save custom htaccess code and/or see an error message when trying to save custom htaccess code, ', 'bulletproof-security').'<br>'.__('click the Encrypt My Notes button first and then click the Save My Notes button.', 'bulletproof-security').'<br>'.__('Mouse over the question mark image to the right for help info.', 'bulletproof-security').'</label><strong><font color="black"><span class="tooltip-350-250"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:5px;" /><span>'.__('If your web host currently has ModSecurity installed or installs ModSecurity at a later time then ModSecurity will prevent you from saving your custom htaccess code unless you encrypt it first by clicking the Encrypt My Notes button.', 'bulletproof-security').'<br><br>'.__('If you click the Encrypt My Notes button, but then want to add or edit additional custom code click the Decrypt My Notes button. After you are done adding or editing custom code click the Encrypt My Notes button before clicking the Save My Notes button.', 'bulletproof-security').'<br><br>'.__('Click the Custom Code Question Mark help button for more help info.', 'bulletproof-security').'</span></span></font></strong></div>'; ?>

    <input type="submit" name="myNotes_submit" class="button bps-button" style="margin:10px 0px 10px 0px;height:auto;white-space:normal" value="<?php esc_attr_e('Save My Notes', 'bulletproof-security') ?>" />
</form>

	<button onclick="bpsMyNotesEncrypt()" class="button bps-encrypt-button"><?php esc_attr_e('Encrypt My Notes', 'bulletproof-security'); ?></button> 
	<button onclick="bpsMyNotesDecrypt()" class="button bps-decrypt-button"><?php esc_attr_e('Decrypt My Notes', 'bulletproof-security'); ?></button>

</div>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#myNotes').submit(function(){ $('#scrolltoNotes').val( $('#bps_my_notes').scrollTop() ); });
	$('#bps_my_notes').scrollTop( $('#scrolltoNotes').val() ); 
});

function bpsMyNotesEncrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt20").value;
  
  // Prevent Double, Triple, etc. encryption
  // The includes() method is not supported in IE 11 (and earlier versions)
  var NoEncrypt1 = CCString1.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt1);

  let encryption = new bpsProJSEncryption();

  if (CCString1 != '' && NoEncrypt1 === false) {
  var encrypted1 = encryption.encrypt(CCString1, nonceValue);
  }
  //console.log(encrypted); 
  
  if (CCString1 != '' && NoEncrypt1 === false) {
  document.getElementById("crypt20").value = encrypted1;
  }
}

function bpsMyNotesDecrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var CCString1 = document.getElementById("crypt20").value;

  let encryption = new bpsProJSEncryption();

  if (CCString1 != '') {
  var decrypted1 = encryption.decrypt(CCString1, nonceValue);
  }
  //console.log(decrypted);
  
  if (CCString1 != '') {
  document.getElementById("crypt20").value = decrypted1;
  }
}
/* ]]> */
</script>

</td>
  </tr>
</table>
</div>

<div id="bps-tabs-10">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-whats_new_table">
  <tr>
   <td class="bps-table_title_no_border">
	<h2><?php _e('Whats New in ', 'bulletproof-security'); ?><?php echo esc_html($bps_version); _e(' and General Help Info & Tips', 'bulletproof-security'); ?></h2>
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_no_border">
	
		<?php $text = '<h3><strong>'.__('The BPS Changelog|Whats New page has been moved to the ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/bps-changelog/" target="_blank" title="BulletProof Security Forum Changelog|Whats New Forum Topic">BulletProof Security Forum Changelog|Whats New Forum Topic</a></strong></h3>'; 
		echo $text; 
	
		$bpsPro_text = '<h3><span class="blue-bold">'.__('Want even more security protection for the ridiculously cheap one-time price of $69.95', 'bulletproof-security').'<br><br>'.__('BPS Pro comes with free unlimited installations, upgrades & support for life. No yearly subscriptions or additional costs.', 'bulletproof-security').'<br><br>'.__('BBS Pro has an amazing track record. BPS Pro is installed on 60,000+ websites. Not a single one of those websites has been hacked in 10+ years.', 'bulletproof-security').'<br><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Get BPS Pro">'.__('Get BPS Pro', 'bulletproof-security').'</a><br><a href="https://www.ait-pro.com/bps-features/" target="_blank" title="BPS Pro Features">'.__('BPS Pro Features', 'bulletproof-security').'</a></span></h3>';	
		echo $bpsPro_text;
		?>
   
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_no_border"></td>
  </tr>
  <tr>
    <td class="bps-table_cell_no_border"></td>
  </tr>
  <tr>
    <td class="bps-table_cell_no_border"><?php $text = '<h2><strong>'.__('General Help Info & Tips:', 'bulletproof-security').'</strong></h2>'; echo $text; ?></td>
  </tr>
   <tr>
    <td class="bps-table_cell_no_border"></td>
  </tr> 
  <tr>
    <td class="bps-table_cell_no_border" style="font-size:1.13em"><?php $text = __('BPS Video Tutorials|Setup Wizard: ', 'bulletproof-security').'<strong><a href="https://forum.ait-pro.com/video-tutorials/" target="_blank" title="BPS Video Tutorials">'.__('BPS Pro Video Tutorials', 'bulletproof-security').'</a></strong><br><br>'; echo $text; ?></td>
  </tr>   
   <tr>
    <td class="bps-table_cell_no_border"></td>
  </tr>
  <tr>
    <td class="bps-table_cell_no_border" style="font-size:1.13em"><?php $text = __('BPS Setup Wizard AutoFix automatically creates whitelist rules for 100+ known issues with plugins and themes: ', 'bulletproof-security').'<strong><a href="https://forum.ait-pro.com/forums/topic/setup-wizard-autofix/" target="_blank" title="Setup Wizard AutoFix">Setup Wizard AutoFix</a></strong><br><br>'.__('All BPS plugin features can be turned Off/On individually to confirm, eliminate or isolate a problem or issue that may or may not be caused by BPS: ', 'bulletproof-security').'<strong><a href="https://forum.ait-pro.com/forums/topic/read-me-first-pro/#bps-free-general-troubleshooting" target="_blank" title="BPS Troubleshooting Steps">Troubleshooting Steps</a></strong><br><br>'.__('The BPS Security Log is a primary troubleshooting tool. If BPS is blocking something legitimate in another plugin or theme then a Security Log entry will be logged for exactly<br>what is being blocked. A whitelist rule can then be created to allow a plugin or theme to do what it needs to do without being blocked: ', 'bulletproof-security').'<strong><a href="https://forum.ait-pro.com/video-tutorials/#security-log-firewall" target="_blank" title="BPS Security Log Video Tutorial">Security Log Video Tutorial</a></strong><br><br>'.__('BPS Security Forum: ', 'bulletproof-security').'<strong><a href="https://forum.ait-pro.com/forums/forum/bulletproof-security-free/" target="_blank" title="BPS Security Forum">BPS Security Forum</a></strong>'; echo $text; ?></td>
  </tr> 
   <tr>
    <td class="bps-table_cell_no_border">&nbsp;</td>
  </tr>
</table>
</div>

<div id="bps-tabs-11">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
   <tr>
    <td class="bps-table_title"></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_links">
    
    <a href="https://forum.ait-pro.com/forums/topic/security-log-event-codes/" target="_blank"><?php _e('Security Log Event Codes', 'bulletproof-security'); ?></a><br /><br />
    <a href="https://forum.ait-pro.com/forums/topic/plugin-conflicts-actively-blocked-plugins-plugin-compatibility/" target="_blank"><?php _e('Forum: Search, Troubleshooting Steps & Post Questions For Assistance', 'bulletproof-security'); ?></a><br /><br />
    <a href="https://forum.ait-pro.com/video-tutorials/" target="_blank"><?php _e('Custom Code Video Tutorial', 'bulletproof-security'); ?></a>
    
	<div id="bps-whitespace-275" style="min-height:275px"></div>    
    
    </td>
  </tr>
</table>
</div>

<div id="bps-tabs-12">

<div id="bpsPro-Features-Table">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td colspan="2" class="bps-table_title"><h2 style="margin:5px 0px 0px 10px"><?php _e('BulletProof Security Pro Feature Highlights', 'bulletproof-security'); ?></h2></td>
  </tr>
  <tr>
    <td width="62%" valign="top" class="bps-table_cell_help">

<div id="bpsProLogo"><?php echo '<a href="'.esc_url('https://affiliates.ait-pro.com/po/').'" target="_blank" title="Get BulletProof Security Pro">
<img src="'. plugins_url('/bulletproof-security/admin/images/bpspro-plugin-logo.jpg') . '" /></a>'; ?>
</div>

<div id="bpsProText">
<?php $text = '<h3><span class="blue-bold">'.__('The Ultimate Security Protection', 'bulletproof-security').'</span></h3>'; echo $text; ?>

<div id="bpsProLinks">
<div class="pro-links"><?php echo '<a href="'.esc_url('https://forum.ait-pro.com/video-tutorials/').'" target="_blank" title="Link Opens in New Browser Window">'. __('BPS Pro One-Click Setup Wizard & Demo Video Tutorial', 'bulletproof-security') . '</a>'; ?></div><br /><br />
<div class="pro-links"><?php echo '<a href="'.esc_url('https://www.ait-pro.com/bps-features/').'" target="_blank" title="Link Opens in New Browser Window">'. __('View All BPS Pro Features', 'bulletproof-security') . '</a>'; ?></div>
</div>
</div>

<div id="bpsProFeatures">

<?php 
$text = '<h3><span class="blue-bold">'.__('Want even more security protection for the ridiculously cheap one-time price of $69.95', 'bulletproof-security').'</span></h3>

<h3><span class="blue-bold">'.__('BPS Pro comes with free unlimited installations, upgrades & support for life. No yearly subscriptions or additional costs.', 'bulletproof-security').'</span></h3>

<h3><span class="blue-bold">'.__('The Complete Website Security Solution for Hacker and Spammer Protection', 'bulletproof-security').'</span></h3><h3><span class="blue-bold">'.__('BulletProof Security Pro has an amazing track record. BPS Pro has been publicly available for 10+ years and is installed on over 60,000 websites worldwide. Not a single one of those 60,000+ websites in 10+ years has been hacked.', 'bulletproof-security').'</span></h3><h3><span class="blue-bold">'.__('Why pay 10 times or more for other premium WordPress Security Plugins with recurring yearly subscriptions when you can get the best WordPress Security Plugin for an extremely low one-time purchase price?', 'bulletproof-security').'<br><a href="https://affiliates.ait-pro.com/po/" target="_blank">'.__('View Cost Comparison', 'bulletproof-security').'</a></span></h3><h3><span class="blue-bold">'.__('30-Day Money-Back Guarantee: If you are dissatisfied with BulletProof Security Pro for any reason. We offer a no questions asked full refund.', 'bulletproof-security').'</span></h3>'; echo $text; 
?>

<?php echo '<p><span class="blue-bold">'; _e('One-Click Setup Wizard Installation: ', 'bulletproof-security'); echo '</span>'; _e('Fast, simple and complete BPS Pro installation and setup in less than 1 minute.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('One-Click Upgrade: ', 'bulletproof-security'); echo '</span>'; _e('One-click plugin upgrade on the WordPress Plugins page.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('AutoRestore|Quarantine Intrusion Detection and Prevention System (ARQ IDPS): ', 'bulletproof-security'); echo '</span>'; _e('ARQ IDPS is a real-time file scanner that automatically quarantines malicious hacker files and autorestores legitimate website files if they have been altered or tampered with. ARQ IDPS uses a much more effective and reliable method of checking and monitoring website files instead of scanning file contents for malicious code. Hacker files that do not contain any malicious code will never be detected by any/all scanners, but will be detected by ARQ IDPS. ARQ IDPS quarantines all hacker files whether or not they contain malicious code. Quarantine Options: Quarantined files can be viewed, restored or deleted. ARQ IDPS works seamlessly with WordPress, Plugin and Theme Automatic, Manual and Shiny installations and updates.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('MScan Malware Scanner: ', 'bulletproof-security'); echo '</span>'; _e('MScan Scheduled Scans are available in BPS Pro only. The BPS Pro ARQ IDPS scanner is far superior to malware scanners including MScan, but both the MScan and ARQ IDPS scanners can be scheduled to automatically run on a website if someone would like to do that.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('Plugin Firewall|Plugin Firewall AutoPilot Mode: ', 'bulletproof-security'); echo '</span>'; _e('The Plugin Firewall protects all of your Plugins (plugin folders and files) with an IP Address Firewall, which prevents/blocks/forbids Remote Access to the plugins folder from external sources (remote script execution, hacker recon, remote scanning, remote accessibility, etc.) and only allows internal access to the plugins folder based on this criteria: Domain name, Server IP Address and Public IP|Your Computer IP Address. The Plugin Firewall uses a true IP Address based Firewall that automatically updates your IP Address in real-time. Plugin Firewall AutoPilot Mode automatically detects and creates Plugin Firewall whitelist rules in real-time for any Plugins that require firewall whitelist rules.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('JTC Anti-Spam|Anti-Hacker (JTC): ', 'bulletproof-security'); echo '</span>'; _e('Blocks 100% of all SpamBot and HackerBot Brute Force Login attacks (auto-registering, auto-logins, auto-posting, auto-commenting). 99% of all spamming and hacking is automated with SpamBots and HackerBots. JTC provides website security protection as well as website Anti-Spam protection. JTC protects these website Pages|Forms: Login Page|Form, Registration Page|Form, Lost Password Page|Form, Comment Page|Form, BuddyPress Register Page|Form, BuddyPress Sidebar Login Form and WooCommerce Login and Registration Pages|Forms with a user friendly & customizable jQuery ToolTip CAPTCHA. JTC also includes a SpamBot Trap.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('Uploads Folder Anti-Exploit Guard (UAEG): ', 'bulletproof-security'); echo '</span>'; _e('Protects the WordPress Uploads folder. ONLY safe image files with valid image file extensions such as jpg, gif, png, etc. can be accessed, opened or viewed from the uploads folder. UAEG blocks files by file extension names in the uploads folder from being accessed, opened, viewed, processed or executed. Malicious files cannot be accessed, opened, viewed, processed or executed in the WordPress Uploads folder.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('DB Monitor Intrusion Detection System (IDS): ', 'bulletproof-security'); echo '</span>'; _e('The DB Monitor is an automated Intrusion Detection System (IDS) that alerts you via email anytime a change/modification occurs in your WordPress database or a new database table is created in your WordPress database. The DB Monitor email alert contains information about what database change/modification occurred and other relevant help info. Your DB Monitor Log also logs any changes/modifications to your WordPress database and other relevant help info.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('DB Diff Tool: ', 'bulletproof-security'); echo '</span>'; _e('The DB Diff Tool compares old database tables from DB backups to current database tables and displays any differences in the data/content of those 2 database tables. The DB Diff Tool can also be used to compare any data and not only just DB data.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('DB Status & Info: ', 'bulletproof-security'); echo '</span>'; _e('General DB Info shows commonly checked DB status and info about your WordPress database at a glance. Extensive DB Info shows extensive DB status information using: SHOW PRIVILEGES, SHOW TABLE STATUS|SIZE, SHOW STORAGE ENGINES, SHOW FULL PROCESSLIST, SHOW GLOBAL STATUS, SHOW SESSION STATUS, SHOW GLOBAL VARIABLES and SHOW SESSION VARIABLES.', 'bulletproof-security').'</p>'; ?>

<?php  echo '<p><span class="blue-bold">'; _e('Display & Alert Options: ', 'bulletproof-security'); echo '</span>'; _e('Centralized Display & Alert Options where you can manage and choose BPS Pro settings for Dashboard Alerts, Dashboard Status Display|Inpage Status Display, Email Alerts, Automated Log file handling, Error checking, etc. Having BPS Pro monitoring, alerting and log file handling options all in one centralized location makes it simple and easy to change all/any BPS Pro settings to your particular preferences.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('Advanced Real-Time Alerting & Heads Up Dashboard Status Display: ', 'bulletproof-security'); echo '</span>';  _e('BPS Pro checks and displays error, warning, notifications and alert messages in real time. You can choose how you want these messages displayed to you with S-Monitor Monitoring &amp; Alerting Options - Display in your WP Dashboard, BPS Pro pages only, Turned off, Email Alerts, Logging...', 'bulletproof-security'); echo '</p>'; ?>
<img src="<?php echo plugins_url('/bulletproof-security/admin/images/bpspro-dashboard-status-display.jpg'); ?>" />

<?php echo '<p><span class="blue-bold">'; _e('Pro Tools: 16 mini-plugins: ', 'bulletproof-security'); echo '</span>'; _e('Online Base64 Decoder, Offline Base64 Decode|Encode, Mcrypt ~ Decrypt|Encrypt, Crypt Encryption, Scheduled Crons (display and reschedule/reset Cron Jobs), String|Function Finder (find any string - name of a function, code, text, etc. - in any files anywhere under your hosting account), String Replacer|Remover (search and replace any string/text/code in any files anywhere under your hosting account), DB String Finder (search your entire database for strings/text/code), DB Table Cleaner|Remover (empty/drop DB Tables), DNS Finder (find all DNS Records for websites by Domain Name), Ping Website|Server (check if a website domain is Up/Down/connection/blocking), cURL Scan (scan website Source Code for strings/text/code), Website Headers (check and display Headers using GET or HEAD Requests), WP Automatic Update (Turn WP Automatic Updates On or Off and other settings), Plugin Update Check (force new Plugin update check), BPS Pro Plugin auto-update (Custom BPS Pro plugin auto-update options), XML-RPC Exploit Checker (check your website or a remote website to see if the website is protected against or vulnerable to an XML-RPC exploit).', 'bulletproof-security').'</p>'; ?>
</div>	

    </td>
    <td width="38%" valign="top" class="bps-table_cell_help">

<div id="bpsProVersions" style="height:650px;overflow:auto;border-left:1px solid #cdcdcd;border-bottom:1px solid #cdcdcd">

<?php 
echo '<a href="'.esc_url( 'https://forum.ait-pro.com/forums/topic/bulletproof-security-pro-version-release-dates/').'" target="_blank" title="Link Opens in New Browser Window" style="font-size:22px;">' . __('BPS Pro Version Release Dates', 'bulletproof-security') . '</a><br><br>';
?>

<div class="pro-links">
<?php
   echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '16.9', 'https://www.ait-pro.com/aitpro-blog/5774/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-16-7/' ).'<br>';  
?>
</div>
<div id="milestone"><?php echo sprintf( __( '11 Year Milestone: 8-1-2022 | %1$s' ), 'First Public Release: 8-1-2011'); ?></div>
<div class="pro-links"> 
<?php
   echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '16.8', 'https://www.ait-pro.com/aitpro-blog/5774/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-16-7/' ).'<br>'; 
   echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '16.7', 'https://www.ait-pro.com/aitpro-blog/5774/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-16-7/' ).'<br>';
   echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '16.6', 'https://www.ait-pro.com/aitpro-blog/5771/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-16-6/' ).'<br>';
   echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '16.5', 'https://www.ait-pro.com/aitpro-blog/5768/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-16-5/' ).'<br>';
   echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '16.4', 'https://www.ait-pro.com/aitpro-blog/5762/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-16-4/' ).'<br>';
   echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '16.3', 'https://www.ait-pro.com/aitpro-blog/5746/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-16-3/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '16.2', 'https://www.ait-pro.com/aitpro-blog/5741/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-16-2/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '16.1', 'https://www.ait-pro.com/aitpro-blog/5737/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-16-1/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '16', 'https://www.ait-pro.com/aitpro-blog/5733/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-16/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '15.9', 'https://www.ait-pro.com/aitpro-blog/5729/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-15-9/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '15.8', 'https://www.ait-pro.com/aitpro-blog/5718/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-15-8/' ).'<br>';
?>
</div>
<div id="milestone"><?php echo sprintf( __( '10 Year Milestone: 8-1-2021 | %1$s' ), 'First Public Release: 8-1-2011'); ?></div>
<div class="pro-links">
<?php
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '15.6/15.7', 'https://www.ait-pro.com/aitpro-blog/5704/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-15-6/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '15.5', 'https://www.ait-pro.com/aitpro-blog/5697/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-15-5/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '15.4', 'https://www.ait-pro.com/aitpro-blog/5689/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-15-4/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '15.3', 'https://www.ait-pro.com/aitpro-blog/5678/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-15-3/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '15.2', 'https://www.ait-pro.com/aitpro-blog/5674/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-15-2/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '15.1', 'https://www.ait-pro.com/aitpro-blog/5671/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-15-1/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '15', 'https://www.ait-pro.com/aitpro-blog/5665/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-15/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '14.9', 'https://www.ait-pro.com/aitpro-blog/5662/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-14-9/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '14.8', 'https://www.ait-pro.com/aitpro-blog/5657/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-14-8/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '14.7', 'https://www.ait-pro.com/aitpro-blog/5650/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-14-7/' ).'<br>';
?>
</div>
<div id="milestone"><?php echo sprintf( __( '9 Year Milestone: 8-1-2020 | %1$s' ), 'First Public Release: 8-1-2011'); ?></div>
<div class="pro-links">
<?php
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '14.6', 'https://www.ait-pro.com/aitpro-blog/5644/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-14-6/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '14.5', 'https://www.ait-pro.com/aitpro-blog/5613/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-14-5/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '14.4', 'https://www.ait-pro.com/aitpro-blog/5598/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-14-4/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '14.3', 'https://www.ait-pro.com/aitpro-blog/5592/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-14-3/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '14.2', 'https://www.ait-pro.com/aitpro-blog/5574/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-14-2/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '14.1', 'https://www.ait-pro.com/aitpro-blog/5567/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-14-1/' ).'<br>';
?>
</div>
<div id="milestone"><?php echo sprintf( __( '8 Year Milestone: 8-1-2019 | %1$s' ), 'First Public Release: 8-1-2011'); ?></div>
<div class="pro-links">
<?php
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '14', 'https://www.ait-pro.com/aitpro-blog/5551/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-14/' ).'<br>';
  echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '13.9', 'https://www.ait-pro.com/aitpro-blog/5545/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-13-9/' ).'<br>';
 echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '13.8', 'https://www.ait-pro.com/aitpro-blog/5537/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-13-8/' ).'<br>';
 echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '13.7', 'https://www.ait-pro.com/aitpro-blog/5518/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-13-7/' ).'<br>'; ?>
</div>
<div id="milestone"><?php echo sprintf( __( '7 Year Milestone: 8-1-2018 | %1$s' ), 'First Public Release: 8-1-2011'); ?></div>
<div class="pro-links">
<?php
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '13.6', 'https://www.ait-pro.com/aitpro-blog/5509/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-13-6/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '13.5', 'https://www.ait-pro.com/aitpro-blog/5505/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-13-5/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '13.4.1', 'https://www.ait-pro.com/aitpro-blog/5494/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-13-4-1/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '13.4', 'https://www.ait-pro.com/aitpro-blog/5485/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-13-4/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '13.3/13.3.1/13.3.2/13.3.3', 'https://www.ait-pro.com/aitpro-blog/5471/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-13-3/' ).'<br>'; ?>
</div>
<div id="milestone"><?php echo sprintf( __( '6 Year Milestone: 8-1-2017 | %1$s' ), 'First Public Release: 8-1-2011'); ?></div>
<div class="pro-links">
<?php 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '13.2', 'https://www.ait-pro.com/aitpro-blog/5466/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-13-2/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '13/13.1', 'https://www.ait-pro.com/aitpro-blog/5457/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-13/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '12.9/12.9.1', 'https://www.ait-pro.com/aitpro-blog/5446/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-12-9/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '12.8', 'https://www.ait-pro.com/aitpro-blog/5440/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-12-8/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '12.7', 'https://www.ait-pro.com/aitpro-blog/5430/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-12-7/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '12.6/12.6.1', 'https://www.ait-pro.com/aitpro-blog/5403/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-12-6/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '12.5', 'https://www.ait-pro.com/aitpro-blog/5388/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-12-5/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '12.4/12.4.1', 'https://www.ait-pro.com/aitpro-blog/5287/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-12-4/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '12.3', 'https://www.ait-pro.com/aitpro-blog/5273/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-12-3/' ).'<br>'; ?>
</div>
<div id="milestone"><?php echo sprintf( __( '5 Year Milestone: 8-1-2016 | %1$s' ), 'First Public Release: 8-1-2011'); ?></div>
<div class="pro-links">
<?php 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '12/12.1/12.2', 'https://www.ait-pro.com/aitpro-blog/5265/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-12/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.9/11.9.1', 'https://www.ait-pro.com/aitpro-blog/5253/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-9/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.8', 'https://www.ait-pro.com/aitpro-blog/5246/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-8/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.7/11.7.1', 'https://www.ait-pro.com/aitpro-blog/5237/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-7/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.6/11.6.1', 'https://www.ait-pro.com/aitpro-blog/5226/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-6/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.5', 'https://www.ait-pro.com/aitpro-blog/5221/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-5/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.4', 'https://www.ait-pro.com/aitpro-blog/5211/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-4/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.2/11.3', 'https://www.ait-pro.com/aitpro-blog/5201/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-2/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.1', 'https://www.ait-pro.com/aitpro-blog/5195/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-1/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11', 'https://www.ait-pro.com/aitpro-blog/5190/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.9', 'https://www.ait-pro.com/aitpro-blog/5183/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-9/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.8', 'https://www.ait-pro.com/aitpro-blog/5181/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-8/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.7', 'https://www.ait-pro.com/aitpro-blog/5177/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-7/' ).'<br>';
?>
</div>
<div id="milestone"><?php echo sprintf( __( '4 Year Milestone: 8-1-2015 | %1$s' ), 'First Public Release: 8-1-2011'); ?></div>
<div class="pro-links">
<?php 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.6', 'https://www.ait-pro.com/aitpro-blog/5169/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-6/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.4/10.5', 'https://www.ait-pro.com/aitpro-blog/5157/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-4/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.3', 'https://www.ait-pro.com/aitpro-blog/5150/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-3/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.2', 'https://www.ait-pro.com/aitpro-blog/5141/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-2/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.1', 'https://www.ait-pro.com/aitpro-blog/5109/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-1/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10', 'https://www.ait-pro.com/aitpro-blog/5094/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.9.1', 'https://www.ait-pro.com/aitpro-blog/5087/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-9-1/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.9', 'https://www.ait-pro.com/aitpro-blog/5080/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-9/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.8', 'https://www.ait-pro.com/aitpro-blog/5075/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-8/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.7', 'https://www.ait-pro.com/aitpro-blog/5066/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-7/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.6', 'https://www.ait-pro.com/aitpro-blog/5062/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-6/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.5', 'https://www.ait-pro.com/aitpro-blog/5056/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-5/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.3/9.4', 'https://www.ait-pro.com/aitpro-blog/5046/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-3/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.2', 'https://www.ait-pro.com/aitpro-blog/5039/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-2/' ).'<br>';
?>
</div>
<div id="milestone"><?php echo sprintf( __( '3 Year Milestone: 8-1-2014 | %1$s' ), 'First Public Release: 8-1-2011'); ?></div>
<div class="pro-links">
<?php 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.1', 'https://www.ait-pro.com/aitpro-blog/5027/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-1/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.0', 'https://www.ait-pro.com/aitpro-blog/5009/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-0/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '8.3', 'https://www.ait-pro.com/aitpro-blog/4994/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-8-3/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '8.2', 'https://www.ait-pro.com/aitpro-blog/4953/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-8-2/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '8.1', 'https://www.ait-pro.com/aitpro-blog/4940/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-8-1/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '8.0', 'https://www.ait-pro.com/aitpro-blog/4926/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-8-0/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '7.9', 'https://www.ait-pro.com/aitpro-blog/4916/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-7-9/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '7.8', 'https://www.ait-pro.com/aitpro-blog/4905/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-7-8/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '7.7', 'https://www.ait-pro.com/aitpro-blog/4900/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-7-7/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '7.6', 'https://www.ait-pro.com/aitpro-blog/4895/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-7-6/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '7.5', 'https://www.ait-pro.com/aitpro-blog/4889/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-7-5/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '7.0', 'https://www.ait-pro.com/aitpro-blog/4876/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-7-0/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '6.5', 'https://www.ait-pro.com/aitpro-blog/4845/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-6-5/' ).'<br>';
?>
</div>
<div id="milestone"><?php echo sprintf( __( '2 Year Milestone: 8-1-2013 | %1$s' ), 'First Public Release: 8-1-2011'); ?></div>
<div class="pro-links">
<?php 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '6.0', 'https://www.ait-pro.com/aitpro-blog/4827/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-6-0/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.9', 'https://www.ait-pro.com/aitpro-blog/4811/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-9/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.8/5.8.1/5.8.2', 'https://www.ait-pro.com/aitpro-blog/4780/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-8/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.7/5.7.1/5.7.2', 'https://www.ait-pro.com/aitpro-blog/4744/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-7/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.6/5.6.1', 'https://www.ait-pro.com/aitpro-blog/4709/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-6/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.5', 'https://www.ait-pro.com/aitpro-blog/4683/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-5/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.4/5.4.1', 'https://www.ait-pro.com/aitpro-blog/4653/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-4/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.3/5.3.1/5.3.2/5.3.3', 'https://www.ait-pro.com/aitpro-blog/4628/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-3/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.2/5.2.1/5.2.2', 'https://www.ait-pro.com/aitpro-blog/4563/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-2/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.9', 'https://www.ait-pro.com/aitpro-blog/4442/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-9/' ).'<br>';
?>
</div>
<div id="milestone"><?php echo sprintf( __( '1 Year Milestone: 8-1-2012 | %1$s' ), 'First Public Release: 8-1-2011'); ?></div>
<div class="pro-links">
<?php 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.8/5.1.8.1/5.1.8.2/5.1.8.3/5.1.8.4', 'https://www.ait-pro.com/aitpro-blog/4197/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-8/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.7', 'https://www.ait-pro.com/aitpro-blog/4144/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-7/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.6', 'https://www.ait-pro.com/aitpro-blog/4029/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-6/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.5', 'https://www.ait-pro.com/aitpro-blog/3845/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-5/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.4', 'https://www.ait-pro.com/aitpro-blog/3732/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-4/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.3', 'https://www.ait-pro.com/aitpro-blog/3605/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-3' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.2', 'https://www.ait-pro.com/aitpro-blog/3529/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-2/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.1', 'https://www.ait-pro.com/aitpro-blog/3510/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-1/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1', 'https://www.ait-pro.com/aitpro-blog/3510/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-1/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.0', 'https://www.ait-pro.com/aitpro-blog/2835/bulletproof-security-pro/bulletproof-security-pro-features/' ).'<br>';
?>
</div>
<div id="milestone"><?php _e('BPS Pro 1.0 - 4.0 | 1-1-2011 - 8-1-2011 | Private Use|Development', 'bulletproof-security'); ?></div>
</div>  
    
    </td>
  </tr>
</table>
</div>
</div>
<?php echo $bps_footer; ?>
</div>
</div>