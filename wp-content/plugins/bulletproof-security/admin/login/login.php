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
	
	if ( esc_html($_SERVER['REQUEST_METHOD']) == 'POST' && ! isset( $_POST['Submit-Login-Security-search'] ) || isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true ) {

		bpsPro_Browser_UA_scroll_animation();
	}
}

// Get Real IP address - USE EXTREME CAUTION!!!
function bpsPro_get_real_ip_address_lsm() {
	
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

// Create a new Deny All .htaccess file on first page load with users current IP address to allow the lsm-master.zip file to be downloaded
// Create a new Deny All .htaccess file if IP address is not current
function bpsPro_Core_LSM_deny_all() {

	if ( is_admin() && current_user_can('manage_options') ) {
		
		$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
		$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
		$Zip_download_Options = get_option('bulletproof_security_options_zip_fix');
		
		if ( isset( $HFiles_options['bps_htaccess_files'] ) && $HFiles_options['bps_htaccess_files'] == 'disabled' || isset( $Zip_download_Options['bps_zip_download_fix'] ) && $Zip_download_Options['bps_zip_download_fix'] == 'On' ) {
			return;
		}

		if ( isset($Apache_Mod_options['bps_apache_mod_ifmodule']) && $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {	
	
			$denyall_content = "# BPS mod_authz_core IfModule BC\n<IfModule mod_authz_core.c>\nRequire ip ". bpsPro_get_real_ip_address_lsm()."\n</IfModule>\n\n<IfModule !mod_authz_core.c>\n<IfModule mod_access_compat.c>\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address_lsm()."\n</FilesMatch>\n</IfModule>\n</IfModule>";
	
		} else {
		
			$denyall_content = "# BPS mod_access_compat\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address_lsm()."\n</FilesMatch>";		
		}		
		
		$create_denyall_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/login/.htaccess';

		if ( ! file_exists($create_denyall_htaccess_file) ) { 
			$handle = fopen( $create_denyall_htaccess_file, 'w+b' );
    		fwrite( $handle, $denyall_content );
    		fclose( $handle );
		}			
		
		if ( file_exists($create_denyall_htaccess_file) ) {
			
			$check_string = file_get_contents($create_denyall_htaccess_file);
			
			if ( ! strpos( $check_string, bpsPro_get_real_ip_address_lsm() ) ) { 
				$handle = fopen( $create_denyall_htaccess_file, 'w+b' );
				fwrite( $handle, $denyall_content );
				fclose( $handle );
			}
		}
	}
}
bpsPro_Core_LSM_deny_all();

?>

<h2 class="bps-tab-title"><?php _e('Login Security Options', 'bulletproof-security'); ?></h2>
<div id="message" class="updated" style="border:1px solid #999;background-color:#000;">

<?php
// General all purpose "Settings Saved." message for forms
if ( current_user_can('manage_options') ) {
if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true) {
	$text = '<p style="font-size:1em;font-weight:bold;padding:2px 0px 2px 5px;margin:0px -11px 0px -11px;background-color:#dfecf2;-webkit-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);""><font color="green"><strong>'.__('Settings Saved', 'bulletproof-security').'</strong></font></p>';
	echo $text;
	}
}

$bpsSpacePop = '-------------------------------------------------------------';

// Replace ABSPATH = wp-content/plugins
$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
// Replace ABSPATH = wp-content
$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
// Top div & bottom div echo
$bps_topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
$bps_bottomDiv = '</p></div>';

	require_once WP_PLUGIN_DIR . '/bulletproof-security/admin/login/lsm-export.php';
	require_once WP_PLUGIN_DIR . '/bulletproof-security/admin/login/lsm-help-text.php';
?>
</div>

<!-- jQuery UI Tab Menu -->
<div id="bps-tabs" class="bps-menu">
    <div id="bpsHead"><img src="<?php echo plugins_url('/bulletproof-security/admin/images/bps-plugin-logo.jpg'); ?>" /></div>
		<ul>
			<li><a href="#bps-tabs-1"><?php _e('Login Security & Monitoring', 'bulletproof-security'); ?></a></li>
			<li><a href="#bps-tabs-2"><?php _e('JTC-Lite', 'bulletproof-security'); ?></a></li>
 			<?php if ( is_multisite() && $blog_id != 1 ) { ?>
            <!-- <li><a href="#bps-tabs-3"><?php //_e('Idle Session Logout', 'bulletproof-security'); ?></a></li> -->  
            <?php } else { ?>
            <li><a href="#bps-tabs-3"><?php _e('Idle Session Logout|Auth Cookie Expiration', 'bulletproof-security'); ?></a></li>
            <?php } ?>
			<li><a href="#bps-tabs-4"><?php _e('Force Strong Passwords', 'bulletproof-security'); ?></a></li>
			<li><a href="#bps-tabs-5"><?php _e('Help &amp; FAQ', 'bulletproof-security'); ?></a></li>
		</ul>
            
<div id="bps-tabs-1" class="bps-tab-page">

<?php
	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	
	if ( isset($BPS_wpadmin_Options['bps_wpadmin_restriction']) && $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' ) {
		$text = '<h3><strong><span style="font-size:1em;"><font color="blue">'.__('Notice: ', 'bulletproof-security').'</font></span><span style="font-size:.75em;">'.__('You have disabled wp-admin BulletProof Mode on the Security Modes page.', 'bulletproof-security').'<br>'.__('If you have Go Daddy "Managed WordPress Hosting" click this link: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/gdmw/" target="_blank" title="Link opens in a new Browser window">'.__('Go Daddy Managed WordPress Hosting', 'bulletproof-security').'</a>.</span></strong></h3>';
		echo $text;
	}
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 10px 0px;"><?php _e('Login Security & Monitoring', 'bulletproof-security'); ?>  <button id="bps-open-modal1" class="button bps-modal-button">
<img src="<?php echo plugins_url('/bulletproof-security/admin/images/question-mark-large.jpg'); ?>" style="margin:0px 0px 0px -10px" /></button></h3>

<div id="bps-modal-content1" class="bps-dialog-hide" title="<?php _e('Login Security & Monitoring', 'bulletproof-security'); ?>">
	<div id="dialog-anchor" style="position:relative;top:-30px;left:0px"><a href="#"></a></div>
	<p>
	<?php 
		$text = '<strong>'.__('This Question Mark Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text;

	    $bpsPro_text = '<strong><font color="blue">'.__('Want even more security protection for the ridiculously cheap one-time price of $69.95', 'bulletproof-security').'</font><br>'.__('BPS Pro comes with free unlimited installations, upgrades & support for life. No yearly subscriptions or additional costs.', 'bulletproof-security').'<br><br>'.__('BBS Pro has an amazing track record. BPS Pro is installed on 60,000+ websites. Not a single one of those websites has been hacked in 10+ years.', 'bulletproof-security').'<br><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Get BPS Pro">'.__('Get BPS Pro', 'bulletproof-security').'</a><br><a href="https://www.ait-pro.com/bps-features/" target="_blank" title="BPS Pro Features">'.__('BPS Pro Features', 'bulletproof-security').'</a></strong><br><br>';	
		echo $bpsPro_text;

		echo $bps_modal_content1; 
	?>
	</p>
</div>

<?php

// Standard Static visible Login Security form proccessing - Lock, Unlock or Delete user login status from DB
if ( isset($_POST['Submit-Login-Security-Radio'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_login_security');
	
	$LSradio = isset($_POST['LSradio']) ? $_POST['LSradio'] : '';
	$bpspro_login_table = $wpdb->prefix . "bpspro_login_security";

	switch( $_POST['Submit-Login-Security-Radio'] ) {
		case __('Submit', 'bulletproof-security'):
		
		$delete_users = array();
		$unlock_users = array();
		$lock_users = array();		
		
		if ( ! empty($LSradio) ) {
			
			foreach ( $LSradio as $key => $value ) {
				
				if ( $value == 'deleteuser' ) {
					$delete_users[] = $key;
				
				} elseif ( $value == 'unlockuser' ) {
					$unlock_users[] = $key;
				
				} elseif ( $value == 'lockuser' ) {
					$lock_users[] = $key;
				}
			}
		}
			
		if ( ! empty($delete_users) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $delete_users as $delete_user ) {
				
				$LoginSecurityRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE user_id = %s", $delete_user ) );
			
				foreach ( $LoginSecurityRows as $row ) {
					
					$delete_row = $wpdb->query( $wpdb->prepare( "DELETE FROM $bpspro_login_table WHERE user_id = %s", $delete_user ) );
				
					$textDelete = '<font color="green">'.$row->username.__(' has been deleted from the Login Security Database Table.', 'bulletproof-security').'</font><br>';
					echo $textDelete;
				}
			}
			echo '</p></div>';		
		}
		
		if ( ! empty($unlock_users) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $unlock_users as $unlock_user ) {
				
				$LoginSecurityRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE user_id = %s", $unlock_user ) );
			
				foreach ( $LoginSecurityRows as $row ) {
					$NLstatus = 'Not Locked';
					$lockout_time = '0';		
					$failed_logins ='0';

					$update_rows = $wpdb->update( $bpspro_login_table, array( 'status' => $NLstatus, 'user_id' => $row->user_id, 'username' => $row->username, 'public_name' => $row->public_name, 'email' => $row->email, 'role' => $row->role, 'human_time' => current_time('mysql'), 'login_time' => $row->login_time, 'lockout_time' => $lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $row->ip_address, 'hostname' => $row->hostname, 'request_uri' => $row->request_uri ), array( 'user_id' => $row->user_id ) );
				
					$textUnlock = '<font color="green">'.$row->username.__(' has been Unlocked.', 'bulletproof-security').'</font><br>';
					echo $textUnlock;				
				}			
			}
			echo '</p></div>';		
		}

		if ( ! empty($lock_users) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $lock_users as $lock_user ) {

				$LoginSecurityRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE user_id = %s", $lock_user ) );
			
				foreach ( $LoginSecurityRows as $row ) {
					$Lstatus = 'Locked';
					$manual_lockout_time = time() + (60 * $BPSoptions['bps_manual_lockout_duration']); // default is 1 hour/3600 seconds
					$BPSoptions = get_option('bulletproof_security_options_login_security');
					$failed_logins = $BPSoptions['bps_max_logins'];	

					$update_rows = $wpdb->update( $bpspro_login_table, array( 'status' => $Lstatus, 'user_id' => $row->user_id, 'username' => $row->username, 'public_name' => $row->public_name, 'email' => $row->email, 'role' => $row->role, 'human_time' => current_time('mysql'), 'login_time' => $row->login_time, 'lockout_time' => $manual_lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $row->ip_address, 'hostname' => $row->hostname, 'request_uri' => $row->request_uri ), array( 'user_id' => $row->user_id ) );

					$textLock = '<font color="green">'.$row->username.__(' has been Locked.', 'bulletproof-security').'</font><br>';
					echo $textLock;
				}			
			}
			echo '</p></div>';		
		}
		break;
	} // end Switch
}

// Search Form - Login Security Dynamic Search Form - Lock, Unlock or Delete user login status from DB
if ( isset($_POST['Submit-Login-Search-Radio'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_login_security_search');
	
	$LSradio = isset($_POST['LSradio']) ? $_POST['LSradio'] : '';
	$bpspro_login_table = $wpdb->prefix . "bpspro_login_security";
	
	switch( $_POST['Submit-Login-Search-Radio'] ) {
		case __('Submit', 'bulletproof-security'):
		
		$delete_users = array();
		$unlock_users = array();
		$lock_users = array();		
		
		if ( ! empty($LSradio) ) {
			
			foreach ( $LSradio as $key => $value ) {
				
				if ( $value == 'deleteuser' ) {
					$delete_users[] = $key;
				
				} elseif ( $value == 'unlockuser' ) {
					$unlock_users[] = $key;
				
				} elseif ( $value == 'lockuser' ) {
					$lock_users[] = $key;
				}
			}
		}
			
		if ( ! empty($delete_users) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $delete_users as $delete_user ) {
				
				$LoginSecurityRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE user_id = %s", $delete_user ) );
			
				foreach ( $LoginSecurityRows as $row ) {
					
					$delete_row = $wpdb->query( $wpdb->prepare( "DELETE FROM $bpspro_login_table WHERE user_id = %s", $delete_user ) );
				
					$textDelete = '<font color="green">'.$row->username.__(' has been deleted from the Login Security Database Table.', 'bulletproof-security').'</font><br>';
					echo $textDelete;
				}
			}
			echo '</p></div>';		
		}
		
		if ( ! empty($unlock_users) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $unlock_users as $unlock_user ) {
				
				$LoginSecurityRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE user_id = %s", $unlock_user ) );
			
				foreach ( $LoginSecurityRows as $row ) {
					$NLstatus = 'Not Locked';
					$lockout_time = '0';		
					$failed_logins ='0';						
					
					$update_rows = $wpdb->update( $bpspro_login_table, array( 'status' => $NLstatus, 'user_id' => $row->user_id, 'username' => $row->username, 'public_name' => $row->public_name, 'email' => $row->email, 'role' => $row->role, 'human_time' => current_time('mysql'), 'login_time' => $row->login_time, 'lockout_time' => $lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $row->ip_address, 'hostname' => $row->hostname, 'request_uri' => $row->request_uri ), array( 'user_id' => $row->user_id ) );
				
					$textUnlock = '<font color="green">'.$row->username.__(' has been Unlocked.', 'bulletproof-security').'</font><br>';
					echo $textUnlock;
				}			
			}
			echo '</p></div>';
		}

		if ( ! empty($lock_users) ) {
			
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			foreach ( $lock_users as $lock_user ) {
				
				$LoginSecurityRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE user_id = %s", $lock_user ) );
			
				foreach ( $LoginSecurityRows as $row ) {
					$Lstatus = 'Locked';
					$manual_lockout_time = time() + (60 * $BPSoptions['bps_manual_lockout_duration']); // default is 1 hour/3600 seconds 	
					$BPSoptions = get_option('bulletproof_security_options_login_security');
					$failed_logins = $BPSoptions['bps_max_logins'];

					$update_rows = $wpdb->update( $bpspro_login_table, array( 'status' => $Lstatus, 'user_id' => $row->user_id, 'username' => $row->username, 'public_name' => $row->public_name, 'email' => $row->email, 'role' => $row->role, 'human_time' => current_time('mysql'), 'login_time' => $row->login_time, 'lockout_time' => $manual_lockout_time, 'failed_logins' => $failed_logins, 'ip_address' => $row->ip_address, 'hostname' => $row->hostname, 'request_uri' => $row->request_uri ), array( 'user_id' => $row->user_id ) );

					$textLock = '<font color="green">'.$row->username.__(' has been Locked.', 'bulletproof-security').'</font><br>';
					echo $textLock;
				}			
			}
			echo '</p></div>';
		}
		break;
	} // end Switch
}
?>

<div id="LoginSecurityOptions" style="width:100%;">

<form name="LoginSecurityOptions" action="options.php" method="post">
	<?php settings_fields('bulletproof_security_options_login_security'); 
	$BPSoptions = get_option('bulletproof_security_options_login_security'); 
	$bps_max_logins = ! empty($BPSoptions['bps_max_logins']) ? $BPSoptions['bps_max_logins'] : '3';
	$bps_lockout_duration = ! empty($BPSoptions['bps_lockout_duration']) ? $BPSoptions['bps_lockout_duration'] : '15';	
	$bps_manual_lockout_duration = ! empty($BPSoptions['bps_manual_lockout_duration']) ? $BPSoptions['bps_manual_lockout_duration'] : '60';
	$bps_max_db_rows_display = isset($BPSoptions['bps_max_db_rows_display']) ? $BPSoptions['bps_max_db_rows_display'] : '';
	$bps_enable_lsm_woocommerce = ! empty($BPSoptions['bps_enable_lsm_woocommerce']) ? checked( $BPSoptions['bps_enable_lsm_woocommerce'], 1, false ) : '';
	$bps_login_security_OnOff = isset($BPSoptions['bps_login_security_OnOff']) ? $BPSoptions['bps_login_security_OnOff'] : '';
	$bps_login_security_logging = isset($BPSoptions['bps_login_security_logging']) ? $BPSoptions['bps_login_security_logging'] : '';	
	$bps_login_security_errors = isset($BPSoptions['bps_login_security_errors']) ? $BPSoptions['bps_login_security_errors'] : '';
	$bps_login_security_remaining = isset($BPSoptions['bps_login_security_remaining']) ? $BPSoptions['bps_login_security_remaining'] : '';
	$bps_login_security_pw_reset = isset($BPSoptions['bps_login_security_pw_reset']) ? $BPSoptions['bps_login_security_pw_reset'] : '';
	$bps_login_security_sort = isset($BPSoptions['bps_login_security_sort']) ? $BPSoptions['bps_login_security_sort'] : '';	
	?>
 
<table border="0">
  <tr>
    <td><label for="LSLog"><?php _e('Max Login Attempts:', 'bulletproof-security'); ?></label></td>
    <td>
     <input type="text" name="bulletproof_security_options_login_security[bps_max_logins]" class="regular-text-50-fixed" value="<?php echo esc_html( $bps_max_logins ); ?>" />
     </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Automatic Lockout Time:', 'bulletproof-security'); ?></label></td>
    <td><input type="text" name="bulletproof_security_options_login_security[bps_lockout_duration]" class="regular-text-50-fixed" value="<?php echo esc_html( $bps_lockout_duration ); ?>" />
    </td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px;"><strong><?php _e('Minutes', 'bulletproof-security'); ?></strong></label></td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Manual Lockout Time:', 'bulletproof-security'); ?></label></td>
    <td><input type="text" name="bulletproof_security_options_login_security[bps_manual_lockout_duration]" class="regular-text-50-fixed" value="<?php echo esc_html( $bps_manual_lockout_duration ); ?>" /></td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px;"><strong><?php _e('Minutes', 'bulletproof-security'); ?></strong></label></td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Max DB Rows To Show:', 'bulletproof-security'); ?></label></td>
    <td><input type="text" name="bulletproof_security_options_login_security[bps_max_db_rows_display]" class="regular-text-50-fixed" value="<?php echo esc_html( $bps_max_db_rows_display ); ?>" /></td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px;"><strong><?php _e('Blank = Show All Rows', 'bulletproof-security'); ?></strong></label></td>
  </tr>
</table>

	<div id="LSM-woocommerce" style="margin:10px 0px 10px 0px">
	<input type="checkbox" name="bulletproof_security_options_login_security[bps_enable_lsm_woocommerce]" value="" <?php echo esc_html($bps_enable_lsm_woocommerce); ?> /><label><?php _e(' Enable Login Security for WooCommerce (BPS Pro Only)', 'bulletproof-security'); ?></label>
	</div>

<table border="0">
  <tr>
    <td><label for="LSLog"><?php _e('Turn On|Turn Off:', 'bulletproof-security'); ?></label></td>
    <td><select name="bulletproof_security_options_login_security[bps_login_security_OnOff]" class="form-220">
<option value="On" <?php selected('On', $bps_login_security_OnOff); ?>><?php _e('Login Security On', 'bulletproof-security'); ?></option>
<option value="Off" <?php selected('Off', $bps_login_security_OnOff); ?>><?php _e('Login Security Off', 'bulletproof-security'); ?></option>
<option value="pwreset" <?php selected('pwreset', $bps_login_security_OnOff); ?>><?php _e('Login Security Off|Use Password Reset Option ONLY', 'bulletproof-security'); ?></option>
</select>
	</td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Logging Options:', 'bulletproof-security'); ?></label></td>
    <td><select name="bulletproof_security_options_login_security[bps_login_security_logging]" class="form-220">
<option value="logLockouts" <?php selected('logLockouts', $bps_login_security_logging); ?>><?php _e('Log Only Account Lockouts', 'bulletproof-security'); ?></option>
<option value="logAll" <?php selected('logAll', $bps_login_security_logging); ?>><?php _e('Log All Account Logins', 'bulletproof-security'); ?></option>
</select>
	</td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Error Messages:', 'bulletproof-security'); ?></label></td>
    <td><select name="bulletproof_security_options_login_security[bps_login_security_errors]" class="form-220">
<option value="wpErrors" <?php selected('wpErrors', $bps_login_security_errors); ?>><?php _e('Standard WP Login Errors', 'bulletproof-security'); ?></option>
<option value="generic" <?php selected('generic', $bps_login_security_errors); ?>><?php _e('User|Pass Invalid Entry Error', 'bulletproof-security'); ?></option>
<option value="genericAll" <?php selected('genericAll', $bps_login_security_errors); ?>><?php _e('User|Pass|Lock Invalid Entry Error', 'bulletproof-security'); ?></option>
</select>
	</td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Attempts Remaining:', 'bulletproof-security'); ?></label></td>
    <td><select name="bulletproof_security_options_login_security[bps_login_security_remaining]" class="form-220">
<option value="On" <?php selected('On', $bps_login_security_remaining); ?>><?php _e('Show Login Attempts Remaining', 'bulletproof-security'); ?></option>
<option value="Off" <?php selected('Off', $bps_login_security_remaining); ?>><?php _e('Do Not Show Login Attempts Remaining', 'bulletproof-security'); ?></option>
</select>
	</td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Password Reset:', 'bulletproof-security'); ?></label></td>
    <td><select name="bulletproof_security_options_login_security[bps_login_security_pw_reset]" class="form-220">
<option value="enable" <?php selected('enable', $bps_login_security_pw_reset); ?>><?php _e('Enable Password Reset', 'bulletproof-security'); ?></option>
<option value="disableFrontend" <?php selected('disableFrontend', $bps_login_security_pw_reset); ?>><?php _e('Disable Password Reset Frontend Only', 'bulletproof-security'); ?></option>
<option value="disable" <?php selected('disable', $bps_login_security_pw_reset); ?>><?php _e('Disable Password Reset Frontend & Backend', 'bulletproof-security'); ?></option>
</select>
	</td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Sort DB Rows:', 'bulletproof-security'); ?></label></td>
    <td><select name="bulletproof_security_options_login_security[bps_login_security_sort]" class="form-220">
<option value="ascending" <?php selected('ascending', $bps_login_security_sort); ?>><?php _e('Ascending - Show Oldest Login First', 'bulletproof-security'); ?></option>
<option value="descending" <?php selected('descending', $bps_login_security_sort); ?>><?php _e('Descending - Show Newest Login First', 'bulletproof-security'); ?></option>
</select>
	</td>
  </tr>
</table>

<input type="submit" name="Submit-Security-Log-Options" class="button bps-button" style="margin:10px 0px 0px 0px;" value="<?php esc_attr_e('Save Options', 'bulletproof-security') ?>" />
</form>
</div>

<div id="LSMExportButton">
<form name="bpsLSMExport" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php' ); ?>" method="post">
	<?php wp_nonce_field('bulletproof_security_lsm_export'); ?>
	<input type="submit" name="Submit-LSM-Export" class="button bps-button" value="<?php esc_attr_e('Export|Download Login Security Table', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Clicking OK will Export (copy) the Login Security Table into the lsm-master.csv file, which you can then download to your computer by clicking the Download Zip Export button displayed in the Login Security Table Export success message.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Export the Login Security Table or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	<?php bpsPro_LSM_Table_CSV(); ?>
</form>
</div>

<div id="LoginSecuritySearch">
<form name="LoginSecuritySearchForm" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#LSM-DB-Table' ); ?>" method="post">
	<?php wp_nonce_field('bulletproof_security_login_security_search'); 
	$login_security_search = isset($_POST['LSSearch']) ? $_POST['LSSearch'] : '';
	?>
    <input type="text" name="LSSearch" class="LSSearch-text" style="width:170px" value="<?php echo esc_html($login_security_search); ?>" />
    <input type="submit" name="Submit-Login-Security-search" class="button bps-button" value="<?php esc_attr_e('Search', 'bulletproof-security') ?>" />
    </form>
</div>

<?php

function bpsDBRowCount() {
global $wpdb;
	$bpspro_login_table = $wpdb->prefix . "bpspro_login_security";
	$id = '0';
	$DB_row_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $bpspro_login_table WHERE id != %d", $id ) );
	$BPSoptions = get_option('bulletproof_security_options_login_security');
	$Max_db_rows = ! isset($BPSoptions['bps_max_db_rows_display']) ? '' : $BPSoptions['bps_max_db_rows_display'];

	echo '<div id="LoginSecurityDBRowCount">';
	
	if ( isset($BPSoptions['bps_max_db_rows_display']) && $BPSoptions['bps_max_db_rows_display'] != '') {
		$text = $Max_db_rows.__(' out of ', 'bulletproof-security')."{$DB_row_count}".__(' Database Rows are currently being displayed', 'bulletproof-security');
		echo $text;
	} else {
		$text = __('Total number of Database Rows is: ', 'bulletproof-security')."{$DB_row_count}";
		echo $text;	
	}
	echo '</div>';
}
bpsDBRowCount();

// Login Security Search Form
if ( isset( $_POST['Submit-Login-Security-search'] ) && current_user_can('manage_options') ) {
	check_admin_referer('bulletproof_security_login_security_search');
	
	$bpspro_login_table = $wpdb->prefix . "bpspro_login_security";
	$search = isset($_POST['LSSearch']) ? sanitize_text_field($_POST['LSSearch']) : '';

	$getLoginSecurityTable = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE (status = %s) OR (user_id = %s) OR (username LIKE %s) OR (public_name LIKE %s) OR (email LIKE %s) OR (role LIKE %s) OR (ip_address LIKE %s) OR (hostname LIKE %s) OR (request_uri LIKE %s)", $search, $search, "%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%" ) );

	echo '<form name="bpsLoginSecuritySearchDBRadio" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php' ).'" method="post">';
	wp_nonce_field('bulletproof_security_login_security_search');

	echo '<div id="LoginSecurityCheckall">';
	echo '<table class="widefat">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col" style="width:10%;font-size:16px;"><strong>'.__('Login Status', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><input type="checkbox" class="checkallLock" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Lock', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><input type="checkbox" class="checkallUnlock" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Unlock', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><input type="checkbox" class="checkallDelete" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Delete', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('User ID', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Username', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Display Name', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Email', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Role', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Login Time', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Lockout Expires', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('IP Address', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Hostname', 'bulletproof-security').'</strong></th>';
	echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Request URI', 'bulletproof-security').'</strong></th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	echo '<tr>';
	
	foreach ( $getLoginSecurityTable as $row ) {

		if ( $wpdb->num_rows != 0 ) {
			$gmt_offset = get_option( 'gmt_offset' ) * 3600;
		
			if ( $row->status == 'Locked' ) {
				echo '<th scope="row" style="border-bottom:none;color:red;font-weight:bold;">'.esc_html($row->status).'</th>';
			} else {
				echo '<th scope="row" style="border-bottom:none;">'.esc_html($row->status).'</th>';
			}
	
			echo "<td><input type=\"checkbox\" id=\"lockuser\" name=\"LSradio[$row->user_id]\" value=\"lockuser\" class=\"lockuserALL\" /><br><span style=\"font-size:10px;\">".__('Lock', 'bulletproof-security')."</span></td>";
			echo "<td><input type=\"checkbox\" id=\"unlockuser\" name=\"LSradio[$row->user_id]\" value=\"unlockuser\" class=\"unlockuserALL\" /><br><span style=\"font-size:10px;\">".__('Unlock', 'bulletproof-security')."</span></td>";
			echo "<td><input type=\"checkbox\" id=\"deleteuser\" name=\"LSradio[$row->user_id]\" value=\"deleteuser\" class=\"deleteuserALL\" /><br><span style=\"font-size:10px;\">".__('Delete', 'bulletproof-security')."</span></td>";
		
			echo '<td>'.esc_html($row->user_id).'</td>';
			echo '<td>'.esc_html($row->username).'</td>';
			echo '<td>'.esc_html($row->public_name).'</td>';	
			echo '<td>'.esc_html($row->email).'</td>';	
			echo '<td>'.esc_html($row->role).'</td>';	
			echo '<td>'.date_i18n(get_option('date_format').' '.get_option('time_format'), esc_html($row->login_time) + $gmt_offset).'</td>';
			
			if ( $row->lockout_time == 0 ) { 
			echo '<td>'.__('NA', 'bulletproof-security').'</td>';
			} else {
			echo '<td>'.date_i18n(get_option('date_format').' '.get_option('time_format'), esc_html($row->lockout_time) + $gmt_offset).'</td>';
			}
			
			echo '<td>'.esc_html($row->ip_address).'</td>';	
			echo '<td>'.esc_html($row->hostname).'</td>';
			echo '<td>'.esc_html($row->request_uri).'</td>';	
			echo '</tr>';			
		}
	} 
	
	if ( $wpdb->num_rows == 0 ) {		
		echo '<th scope="row" style="border-bottom:none;">'.__('No Logins|Locked', 'bulletproof-security').'</th>';
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo '<td></td>';		
		echo '<td></td>'; 
		echo '<td></td>';		
		echo '<td></td>'; 
		echo '<td></td>';
		echo '<td></td>';		
		echo '<td></td>'; 
		echo '</tr>';		
	}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';	

	echo "<input type=\"submit\" name=\"Submit-Login-Search-Radio\" value=\"".esc_attr__('Submit', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"return confirm('".__('Locking and Unlocking a User is reversible, but Deleting a User is not.\n\n-------------------------------------------------------------\n\nWhen you delete a User you are deleting that User database row from the BPS Login Security Database Table and not from the WordPress User Database Table.\n\n-------------------------------------------------------------\n\nTo delete a User Account from your WordPress website use the standard/normal WordPress Users page.\n\n-------------------------------------------------------------\n\nClick OK to proceed or click Cancel', 'bulletproof-security')."')\" />&nbsp;&nbsp;<input type=\"button\" name=\"cancel\" value=\"".__('Clear|Refresh', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"javascript:history.go(0)\" /></form><br>";

	} else { // if the LSM Search form is not submitted then display the static LSM form

		echo '<form name="bpsLoginSecurityDBRadio" class="LSM-DBRadio-Form" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php' ).'" method="post">';
		wp_nonce_field('bulletproof_security_login_security');

		$bpspro_login_table = $wpdb->prefix . "bpspro_login_security";
		$searchAll = ''; // return all rows
		$BPSoptions = get_option('bulletproof_security_options_login_security');
	
		if ( ! isset($BPSoptions['bps_login_security_sort']) || isset($BPSoptions['bps_login_security_sort']) && $BPSoptions['bps_login_security_sort'] == 'ascending' ) {
			$sorting = 'ASC';
		} else {
			$sorting = 'DESC';
		}
	
		if ( isset($BPSoptions['bps_max_db_rows_display']) && $BPSoptions['bps_max_db_rows_display'] != '' ) {
			$db_row_limit = 'LIMIT '. $BPSoptions['bps_max_db_rows_display'];
			$getLoginSecurityTable = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE login_time != %s ORDER BY login_time $sorting $db_row_limit", "%$searchAll%" ) );
	
		} else {
			$getLoginSecurityTable = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $bpspro_login_table WHERE login_time != %s ORDER BY login_time $sorting", "%$searchAll%" ) );	
		}

		echo '<div id="LoginSecurityCheckall">';
		echo '<table class="widefat">';
		echo '<thead>';
		echo '<tr>';
		echo '<th scope="col" style="width:10%;font-size:16px;"><strong>'.__('Login Status', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><input type="checkbox" class="checkallLock" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Lock', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><input type="checkbox" class="checkallUnlock" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Unlock', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><input type="checkbox" class="checkallDelete" style="text-align:left;margin-left:2px;" /><br><strong>'.__('Delete', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('User ID', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Username', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Display Name', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Email', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Role', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Login Time', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Lockout Expires', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('IP Address', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Hostname', 'bulletproof-security').'</strong></th>';
		echo '<th scope="col" style="width:5%;font-size:12px;"><strong>'.__('Request URI', 'bulletproof-security').'</strong></th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		echo '<tr>';
		
		foreach ( $getLoginSecurityTable as $row ) {

			if ( $wpdb->num_rows != 0 ) {
				$gmt_offset = get_option( 'gmt_offset' ) * 3600;
				
				if ( $row->status == 'Locked' ) {
					echo '<th scope="row" style="border-bottom:none;color:red;font-weight:bold;">'.esc_html($row->status).'</th>';
				} else {
					echo '<th scope="row" style="border-bottom:none;">'.esc_html($row->status).'</th>';
				}
	
				echo "<td><input type=\"checkbox\" id=\"lockuser\" name=\"LSradio[$row->user_id]\" value=\"lockuser\" class=\"lockuserALL\" /><br><span style=\"font-size:10px;\">".__('Lock', 'bulletproof-security')."</span></td>";
				echo "<td><input type=\"checkbox\" id=\"unlockuser\" name=\"LSradio[$row->user_id]\" value=\"unlockuser\" class=\"unlockuserALL\" /><br><span style=\"font-size:10px;\">".__('Unlock', 'bulletproof-security')."</span></td>";
				echo "<td><input type=\"checkbox\" id=\"deleteuser\" name=\"LSradio[$row->user_id]\" value=\"deleteuser\" class=\"deleteuserALL\" /><br><span style=\"font-size:10px;\">".__('Delete', 'bulletproof-security')."</span></td>";
		
				echo '<td>'.esc_html($row->user_id).'</td>';
				echo '<td>'.esc_html($row->username).'</td>';
				echo '<td>'.esc_html($row->public_name).'</td>';	
				echo '<td>'.esc_html($row->email).'</td>';	
				echo '<td>'.esc_html($row->role).'</td>';	
				echo '<td>'.date_i18n(get_option('date_format').' '.get_option('time_format'), esc_html($row->login_time) + $gmt_offset).'</td>';
				
				if ( $row->lockout_time == 0 ) { 
				echo '<td>'.__('NA', 'bulletproof-security').'</td>';
				} else {
				echo '<td>'.date_i18n(get_option('date_format').' '.get_option('time_format'), esc_html($row->lockout_time) + $gmt_offset).'</td>';
				}
				
				echo '<td>'.esc_html($row->ip_address).'</td>';	
				echo '<td>'.esc_html($row->hostname).'</td>';
				echo '<td>'.esc_html($row->request_uri).'</td>';	
				echo '</tr>';			
			}
		} 
		
		if ( $wpdb->num_rows == 0 ) {		
			echo '<th scope="row" style="border-bottom:none;">'.__('No Logins|Locked', 'bulletproof-security').'</th>';
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo '<td></td>';		
			echo '<td></td>'; 
			echo '<td></td>';		
			echo '<td></td>'; 
			echo '<td></td>';
			echo '<td></td>';		
			echo '<td></td>'; 
			echo '</tr>';		
		}
		echo '</tbody>';
		echo '</table>';
		echo '</div>';	

		echo "<input type=\"submit\" name=\"Submit-Login-Security-Radio\" value=\"".esc_attr__('Submit', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"return confirm('".__('Locking and Unlocking a User is reversible, but Deleting a User is not.\n\n-------------------------------------------------------------\n\nWhen you delete a User you are deleting that User database row from the BPS Login Security Database Table and not from the WordPress User Database Table.\n\n-------------------------------------------------------------\n\nTo delete a User Account from your WordPress website use the standard/normal WordPress Users page.\n\n-------------------------------------------------------------\n\nClick OK to proceed or click Cancel', 'bulletproof-security')."')\" />&nbsp;&nbsp;<input type=\"button\" name=\"cancel\" value=\"".__('Clear|Refresh', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"javascript:history.go(0)\" /></form><br>";
	}
?>
<br />
<br />

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($) {
	$( "#LoginSecurityCheckall tr:odd" ).css( "background-color", "#f9f9f9" );
});
/* ]]> */
</script>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
//jQuery(function() {
    $('.checkallLock').click(function() {
        $(this).parents('#LoginSecurityCheckall:eq(0)').find('.lockuserALL:checkbox').attr('checked', this.checked);
    });
});
/* ]]> */
</script>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
//jQuery(function() {
    $('.checkallUnlock').click(function() {
        $(this).parents('#LoginSecurityCheckall:eq(0)').find('.unlockuserALL:checkbox').attr('checked', this.checked);
    });
});
/* ]]> */
</script>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
//jQuery(function() {
    $('.checkallDelete').click(function() {
        $(this).parents('#LoginSecurityCheckall:eq(0)').find('.deleteuserALL:checkbox').attr('checked', this.checked);
    });
});
/* ]]> */
</script>

</td>
  </tr>
</table>
</div>

<div id="bps-tabs-2" class="bps-tab-page">
	
<?php
	// Nonce for Crypto-js
	$bps_nonceValue = 'ghbhnyxu';
	$bpsSpacePop = '-------------------------------------------------------------';

	$GDMW_options = get_option('bulletproof_security_options_GDMW');
	
	if ( isset($GDMW_options['bps_gdmw_hosting']) && $GDMW_options['bps_gdmw_hosting'] == 'yes' ) {
		$text = '<h3><strong><span style="font-size:1em;"><font color="blue">'.__('Notice: ', 'bulletproof-security').'</font></span><span style="font-size:.75em;">'.__('The Setup Wizard Go Daddy "Managed WordPress Hosting" option is set to Yes.', 'bulletproof-security').'<br>'.__('If you have Go Daddy "Managed WordPress Hosting" click this link: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/gdmw/" target="_blank" title="Link opens in a new Browser window">'.__('Go Daddy Managed WordPress Hosting', 'bulletproof-security').'</a>.<br>'.__('If you do not have Go Daddy "Managed WordPress Hosting" then change the Go Daddy "Managed WordPress Hosting" Setup Wizard option to No.', 'bulletproof-security').'</span></strong></h3>';
		echo $text;
	}
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 5px 0px;"><?php _e('JTC-Lite', 'bulletproof-security'); ?>  <button id="bps-open-modal2" class="button bps-modal-button">
<img src="<?php echo plugins_url('/bulletproof-security/admin/images/question-mark-large.jpg'); ?>" style="margin:0px 0px 0px -10px" /></button></h3>

<div id="bps-modal-content2" class="bps-dialog-hide" title="<?php _e('JTC-Lite', 'bulletproof-security'); ?>">
	<div id="dialog-anchor" style="position:relative;top:-30px;left:0px"><a href="#"></a></div>
	<p>
	<?php
		$text = '<strong>'.__('This Question Mark Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text;

	    $bpsPro_text = '<strong><font color="blue">'.__('Want even more security protection for the ridiculously cheap one-time price of $69.95', 'bulletproof-security').'</font><br>'.__('BPS Pro comes with free unlimited installations, upgrades & support for life. No yearly subscriptions or additional costs.', 'bulletproof-security').'<br><br>'.__('BBS Pro has an amazing track record. BPS Pro is installed on 60,000+ websites. Not a single one of those websites has been hacked in 10+ years.', 'bulletproof-security').'<br><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Get BPS Pro">'.__('Get BPS Pro', 'bulletproof-security').'</a><br><a href="https://www.ait-pro.com/bps-features/" target="_blank" title="BPS Pro Features">'.__('BPS Pro Features', 'bulletproof-security').'</a></strong><br><br>';	
		echo $bpsPro_text;

		echo $bps_modal_content2;
	?>
    </p>
</div>

<?php
// JTC Form processing
if ( isset( $_POST['Submit-Security-Log-Options-JTC'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bps_login_security_jtc' );
	
	$Custom_Roles = empty($_POST['bps_jtc_custom_roles']) ? array( 'bps', '' ) : $_POST['bps_jtc_custom_roles'];		
	
	switch( $_POST['Submit-Security-Log-Options-JTC'] ) {
		case __('Save Options', 'bulletproof-security'):
		
		if ( ! empty($Custom_Roles) ) {
			
			$Custom_Roles_array = array();
			
			foreach ( $Custom_Roles as $key => $value ) {
				
				if ( $value == '1' ) {
					$Custom_Roles_array[$key] = '';
				} 
			}
		
		} else {
			
			$Custom_Roles_array = array( 'bps', '' );
		}

		$Encryption = new bpsProPHPEncryption();
		$nonceValue = 'ghbhnyxu';
	
		$pos1 = strpos( $_POST['bps_jtc_custom_form_error'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos2 = strpos( $_POST['bps_jtc_comment_form_error'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos3 = strpos( $_POST['bps_jtc_comment_form_label'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos4 = strpos( $_POST['bps_jtc_comment_form_input'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
	
	$pattern = '/<script>|<\/script>|javascript|onload|onunload|onabort|onbeforeprint|onbeforeunload|onchange|onfocusin|onfocusout|onfocus|onblur|onerror|oninput|onopen|onmessage|onmouseover|onmousedown|onmouseup|onmouseout|onmouseleave|onmousemove|onmouseenter|onclick|ondblclick|onkeyup|onkeydown|onkeypress|onsubmit|onselect|onoffline|ononline|onpagehide|onpageshow|onpaste|onresize|onreset|onscroll|onsearch|onshow|ontoggle|ontouchcancel|ontouchend|ontouchmove|ontouchstart|onhashchange|oninvalid|onanimationend|onanimationcancel|onanimationiteration|onauxclick|oncancel|oncanplay|oncanplaythrough|onloadeddata|oncontextmenu|ondurationchange|onended|onformdata|ongotpointercapture|onloadedmetadata|onloadend|onloadstart|onlostpointercapture|onplay|onplaying|onpointercancel|onpointerdown|onpointerenter|onpointerleave|onpointermove|onpointerout|onpointerover|onpointerup|onsecuritypolicyviolation|onselectionchange|onselectstart|onslotchange|ontransitioncancel|ontransitionend|onwheel|allowscriptaccess|currentTarget|addEventListener|getElementById|getElementsByTagName|getElementsByClassName|documentElement|innerHTML|setAttribute|createElement|createDocumentType|createDocument|createHTMLDocument|DOMImplementation|dispatchEvent|EventTarget|HTMLSlotElement|HTMLTemplateElement|DOMError|DocumentFragment|TextDecoder|TextEncoder|removeChild|appendChild|replaceChild|parentNode|childNodes|createTextNode|nodeValue|NodeIterator|firstChild|lastChild|querySelectorAll|querySelector|EventSource|AbortController/i';
		
		$post_array = array( $_POST['bps_jtc_custom_form_error'], $_POST['bps_jtc_comment_form_error'], $_POST['bps_jtc_comment_form_label'], $_POST['bps_jtc_comment_form_input'] );
	
		$javascript_matches_array = array();

		foreach ( $post_array as $key => $value ) {

			$javascript_post_matches = preg_match_all( $pattern, $value, $matches );
		
			foreach( $matches[0] as $key => $value ) {
		
				$javascript_matches_array[] = htmlspecialchars($value);
			}
		
			if ( ! empty($javascript_matches_array) ) {
				
				echo $bps_topDiv;
				$text = '<strong><font color="#fb0101">'.__('Error: Sorry, JavaScript is not allowed in the JTC-Lite text boxes. Only HTML, CSS and regular text are allowed.', 'bulletproof-security').'</font><br>'.__('JavaScript Matches:', 'bulletproof-security') . '</strong><br>';;
				echo $text;	
				
				foreach( $javascript_matches_array as $key => $value ) {
					
					echo esc_html($value) . '<br>';
				}
		
				echo $bps_bottomDiv;
			return;
	
			}
		}

		// The JavaScript filter condition above will not allow any js to get this far.
		if ( $pos1 === false ) {
			$bps_jtc_custom_form_error = stripslashes($_POST['bps_jtc_custom_form_error']);
		} else {
			$bps_jtc_custom_form_error = $Encryption->decrypt($_POST['bps_jtc_custom_form_error'], $nonceValue);
		}
	
		if ( $pos2 === false ) {
			$bps_jtc_comment_form_error = stripslashes($_POST['bps_jtc_comment_form_error']);
		} else {
			$bps_jtc_comment_form_error = $Encryption->decrypt($_POST['bps_jtc_comment_form_error'], $nonceValue);
		}
	
		if ( $pos3 === false ) {
			$bps_jtc_comment_form_label = stripslashes($_POST['bps_jtc_comment_form_label']);
		} else {
			$bps_jtc_comment_form_label = $Encryption->decrypt($_POST['bps_jtc_comment_form_label'], $nonceValue);
		}
	
		if ( $pos4 === false ) {
			$bps_jtc_comment_form_input = stripslashes($_POST['bps_jtc_comment_form_input']);
		} else {
			$bps_jtc_comment_form_input = $Encryption->decrypt($_POST['bps_jtc_comment_form_input'], $nonceValue);
		}
	}

	$bps_jtc_login_form 						= ! empty($_POST['bps_jtc_login_form']) ? '1' : '';
	$bps_tooltip_captcha_key 					= sanitize_text_field($_POST['bps_tooltip_captcha_key']);
	$bps_tooltip_captcha_hover_text 			= sanitize_text_field($_POST['bps_tooltip_captcha_hover_text']);
	$bps_tooltip_captcha_title 					= sanitize_text_field($_POST['bps_tooltip_captcha_title']);	
	
	$bps_tooltip_captcha_title_bold 			= ! empty($_POST['bps_tooltip_captcha_title_bold']) ? '1' : '';
	$bps_tooltip_captcha_title_hidden 			= ! empty($_POST['bps_tooltip_captcha_title_hidden']) ? sanitize_text_field($_POST['bps_tooltip_captcha_title_hidden']) : '';
	$bps_tooltip_captcha_title_after 			= ! empty($_POST['bps_tooltip_captcha_title_after']) ? sanitize_text_field($_POST['bps_tooltip_captcha_title_after']) : '';		
	$bps_tooltip_captcha_title_after_bold 		= ! empty($_POST['bps_tooltip_captcha_title_after_bold']) ? '1' : '';	
	$bps_tooltip_captcha_title_after_hidden 	= ! empty($_POST['bps_tooltip_captcha_title_after_hidden']) ? sanitize_text_field($_POST['bps_tooltip_captcha_title_after_hidden']): '';

	$JTC_Options = array(
	'bps_tooltip_captcha_key' 					=> $bps_tooltip_captcha_key, 
	'bps_tooltip_captcha_hover_text'			=> $bps_tooltip_captcha_hover_text, 
	'bps_tooltip_captcha_title' 				=> $bps_tooltip_captcha_title, 
	'bps_tooltip_captcha_title_bold' 			=> $bps_tooltip_captcha_title_bold,
	'bps_tooltip_captcha_title_hidden' 			=> $bps_tooltip_captcha_title_hidden,
	'bps_tooltip_captcha_title_after' 			=> $bps_tooltip_captcha_title_after,
	'bps_tooltip_captcha_title_after_bold'		=> $bps_tooltip_captcha_title_after_bold,
	'bps_tooltip_captcha_title_after_hidden'	=> $bps_tooltip_captcha_title_after_hidden,
	'bps_tooltip_captcha_logging' 				=> 'Off', 
	'bps_jtc_login_form' 						=> $bps_jtc_login_form, 
	'bps_jtc_register_form' 					=> '', 
	'bps_jtc_lostpassword_form' 				=> '', 
	'bps_jtc_comment_form' 						=> '', 
	'bps_jtc_mu_register_form' 					=> '', 
	'bps_jtc_buddypress_register_form' 			=> '', 
	'bps_jtc_buddypress_sidebar_form' 			=> '', 
	'bps_jtc_administrator' 					=> '', 
	'bps_jtc_editor' 							=> '', 
	'bps_jtc_author' 							=> '', 
	'bps_jtc_contributor' 						=> '', 
	'bps_jtc_subscriber' 						=> '', 
	'bps_jtc_comment_form_error' 				=> $bps_jtc_comment_form_error, 
	'bps_jtc_comment_form_label' 				=> $bps_jtc_comment_form_label, 
	'bps_jtc_comment_form_input' 				=> $bps_jtc_comment_form_input, 
	'bps_jtc_custom_roles' 						=> $Custom_Roles_array, 
	'bps_enable_jtc_woocommerce' 				=> '', 
	'bps_jtc_custom_form_error' 				=> $bps_jtc_custom_form_error
	);	
	
	foreach( $JTC_Options as $key => $value ) {
		update_option('bulletproof_security_options_login_security_jtc', $JTC_Options);
	}

	echo $bps_topDiv;
	echo '<strong><font color="green">'.__('JTC-Lite Settings Saved.', 'bulletproof-security').'</font></strong><br>';
	echo $bps_bottomDiv;
}
?>

<div id="LoginSecurityJTC" style="position:relative;top:0px;left:0px;margin:0px 0px 0px 0px;">

<form name="LoginSecurityJTC" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-2' ); ?>" method="post">
	<?php wp_nonce_field('bps_login_security_jtc');
	$BPSoptionsJTC = get_option('bulletproof_security_options_login_security_jtc'); 
	$bps_tooltip_captcha_key = ! empty($BPSoptionsJTC['bps_tooltip_captcha_key']) ? $BPSoptionsJTC['bps_tooltip_captcha_key'] : 'jtc';	
	$bps_tooltip_captcha_hover_text = ! empty($BPSoptionsJTC['bps_tooltip_captcha_hover_text']) ? $BPSoptionsJTC['bps_tooltip_captcha_hover_text'] : 'Type/Enter:  jtc';		
	$bps_tooltip_captcha_title = ! empty($BPSoptionsJTC['bps_tooltip_captcha_title']) ? $BPSoptionsJTC['bps_tooltip_captcha_title'] : 'Hover or click the text box below';		

	$bps_tooltip_captcha_title_bold = ! empty($BPSoptionsJTC['bps_tooltip_captcha_title_bold']) ? checked( $BPSoptionsJTC['bps_tooltip_captcha_title_bold'], 1, false ) : '';
	$bps_tooltip_captcha_title_after = ! empty($BPSoptionsJTC['bps_tooltip_captcha_title_after']) ? $BPSoptionsJTC['bps_tooltip_captcha_title_after'] : '';		
	$bps_tooltip_captcha_title_after_bold = ! empty($BPSoptionsJTC['bps_tooltip_captcha_title_after_bold']) ? checked( $BPSoptionsJTC['bps_tooltip_captcha_title_after_bold'], 1, false ) : '';
	$bps_tooltip_captcha_logging = isset($BPSoptionsJTC['bps_tooltip_captcha_logging']) ? $BPSoptionsJTC['bps_tooltip_captcha_logging'] : '';		

	if ( ! empty($BPSoptionsJTC['bps_tooltip_captcha_title_hidden']) ) {
		$bps_tooltip_captcha_title_hidden = isset($_REQUEST['bps_tooltip_captcha_title_hidden']) ? $_REQUEST['bps_tooltip_captcha_title_hidden'] : $BPSoptionsJTC['bps_tooltip_captcha_title_hidden'];
	} else {
		$bps_tooltip_captcha_title_hidden = isset($_REQUEST['bps_tooltip_captcha_title_hidden']) ? $_REQUEST['bps_tooltip_captcha_title_hidden'] : '';		
	}

	if ( ! empty($BPSoptionsJTC['bps_tooltip_captcha_title_after_hidden']) ) {
		$bps_tooltip_captcha_title_after_hidden = isset($_REQUEST['bps_tooltip_captcha_title_after_hidden']) ? $_REQUEST['bps_tooltip_captcha_title_after_hidden'] : $BPSoptionsJTC['bps_tooltip_captcha_title_after_hidden'];
	} else {
		$bps_tooltip_captcha_title_after_hidden = isset($_REQUEST['bps_tooltip_captcha_title_after_hidden']) ? $_REQUEST['bps_tooltip_captcha_title_after_hidden'] : '';		
	}
	
	$title_hidden_style = ! empty($BPSoptionsJTC['bps_tooltip_captcha_title_hidden']) ? 'color:'.$BPSoptionsJTC['bps_tooltip_captcha_title_hidden'].';' : '';
	$title_after_hidden_style = ! empty($BPSoptionsJTC['bps_tooltip_captcha_title_after_hidden']) ? 'color:'.$BPSoptionsJTC['bps_tooltip_captcha_title_after_hidden'].';' : '';	
	$title_bold_style = ! empty($BPSoptionsJTC['bps_tooltip_captcha_title_bold']) ? 'font-weight:bold' : '';
	$title_after_bold_style = ! empty($BPSoptionsJTC['bps_tooltip_captcha_title_after_bold']) ? 'font-weight:bold' : '';
	$title_style = $title_hidden_style . $title_bold_style;
	$title_after_style = $title_after_hidden_style . $title_after_bold_style;
	?>

<style>
input#colorPickerText.title {width:250px;<?php echo $title_style; ?>}
input#colorPickerText2.title-after {width:250px;<?php echo $title_after_style; ?>}
</style>

<table border="0">
  <tr>
    <td><label for="LSLog"><?php _e('JTC CAPTCHA:', 'bulletproof-security'); ?></label></td>
    <td><input type="text" name="bps_tooltip_captcha_key" class="regular-text-250" value="<?php echo esc_html($bps_tooltip_captcha_key); ?>" /></td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px;font-style:italic;font-weight:normal;"><?php _e('jtc', 'bulletproof-security'); ?></label></td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('JTC ToolTip:', 'bulletproof-security'); ?></label></td>
    <td><input type="text" name="bps_tooltip_captcha_hover_text" class="regular-text-250" value="<?php echo esc_html($bps_tooltip_captcha_hover_text); ?>" /></td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px;font-style:italic;font-weight:normal;"><?php _e('Type/Enter:  jtc. Enter a blank space for no text (Spacebar Key)', 'bulletproof-security'); ?></label></td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('JTC Title|Text:', 'bulletproof-security'); ?></label></td>
    <td>
    <input type="text" id="colorPickerText" name="bps_tooltip_captcha_title" class="title" value="<?php echo esc_html($bps_tooltip_captcha_title); ?>" />
    </td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px;font-style:italic;font-weight:normal;"></label>
    <label for="colorPicker"></label>
	<input type="color" id="colorPicker" value="#000000">
    <input type="hidden" name="bps_tooltip_captcha_title_hidden" id="colorPickerHidden" value="<?php echo esc_html( $bps_tooltip_captcha_title_hidden ); ?>">
    <input type="checkbox" name="bps_tooltip_captcha_title_bold" style="margin:0px 0px 10px 8px;" value="1" <?php echo esc_html($bps_tooltip_captcha_title_bold); ?> /><label style="vertical-align:super;margin-left:4px;"><?php _e(' Bold', 'bulletproof-security'); ?></label>
    </td>
  </tr>
   <tr>
    <td><label for="LSLog"><?php _e('JTC Title|Text After:', 'bulletproof-security'); ?></label></td>
    <td><input type="text" id="colorPickerText2" name="bps_tooltip_captcha_title_after" class="title-after" value="<?php echo esc_html($bps_tooltip_captcha_title_after); ?>" /></td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px;font-style:italic;font-weight:normal;"></label>
    <label for="colorPicker2"></label>
	<input type="color" id="colorPicker2" value="#ff0000">
    <input type="hidden" name="bps_tooltip_captcha_title_after_hidden" id="colorPickerHidden2" value="<?php echo esc_html( $bps_tooltip_captcha_title_after_hidden ); ?>">
    <input type="checkbox" name="bps_tooltip_captcha_title_after_bold" style="margin:0px 0px 10px 8px;" value="1" <?php echo esc_html($bps_tooltip_captcha_title_after_bold); ?> /><label style="vertical-align: super;margin-left:4px;"><?php _e(' Bold', 'bulletproof-security'); ?></label>
    <label for="LSLog" style="vertical-align:super;margin-left:4px;font-style:italic;font-weight:normal;color:#000000;"><?php _e('Example: ', 'bulletproof-security'); ?></label>
    <label for="LSLog" style="vertical-align:super;margin-left:4px;font-weight:600;color:#ff0000;"><?php _e('Required *', 'bulletproof-security'); ?></label>
	<label for="LSLog" style="vertical-align:super;margin-left:4px;font-style:italic;font-weight:normal;color:#000000;"><?php _e(' or just: ', 'bulletproof-security'); ?></label>
    <label for="LSLog" style="vertical-align:super;margin-left:4px;font-weight:600;color:#ff0000;"><?php _e('*', 'bulletproof-security'); ?></label>
    </td>
  </tr>

<?php if ( is_multisite() && $blog_id != 1 ) { echo '<div style="margin:0px 0px 0px 0px;"></div>'; } else { ?>

  <tr>
    <td><label for="LSLog"><?php _e('JTC Logging:', 'bulletproof-security'); ?></label></td>
    <td><select name="bps_tooltip_captcha_logging" class="form-250">
	<option value="Off" <?php selected('Off', $bps_tooltip_captcha_logging); ?>><?php _e('JTC Logging Off', 'bulletproof-security'); ?></option>
	</select>
	</td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px; font-style:italic;font-weight:normal;"><?php _e('Logged in the Security Log (BPS Pro Only)', 'bulletproof-security'); ?></label></td>
  </tr>

<?php } ?>
<!-- Important: </table> needs to come after the closing php tag above for Network subsites -->
</table>
	
	<?php
	$bps_enable_jtc_woocommerce = ! empty($BPSoptionsJTC['bps_enable_jtc_woocommerce']) ? checked( $BPSoptionsJTC['bps_enable_jtc_woocommerce'], 1, false ) : '';	
	$bps_jtc_login_form = ! empty($BPSoptionsJTC['bps_jtc_login_form']) ? checked( $BPSoptionsJTC['bps_jtc_login_form'], 1, false ) : '';	
	$bps_jtc_register_form = ! empty($BPSoptionsJTC['bps_jtc_register_form']) ? checked(  $BPSoptionsJTC['bps_jtc_register_form'], 1, false ) : '';	
	$bps_jtc_lostpassword_form = ! empty($BPSoptionsJTC['bps_jtc_lostpassword_form']) ? checked( $BPSoptionsJTC['bps_jtc_lostpassword_form'], 1, false ) : '';	
	$bps_jtc_comment_form = ! empty($BPSoptionsJTC['bps_jtc_comment_form']) ? checked( $BPSoptionsJTC['bps_jtc_comment_form'], 1, false ) : '';	
	$bps_jtc_mu_register_form = ! empty($BPSoptionsJTC['bps_jtc_mu_register_form']) ? checked( $BPSoptionsJTC['bps_jtc_mu_register_form'], 1, false ) : '';	
	$bps_jtc_buddypress_register_form = ! empty($BPSoptionsJTC['bps_jtc_buddypress_register_form']) ? checked( $BPSoptionsJTC['bps_jtc_buddypress_register_form'], 1, false ) : '';	
	$bps_jtc_buddypress_sidebar_form = ! empty($BPSoptionsJTC['bps_jtc_buddypress_sidebar_form']) ? checked( $BPSoptionsJTC['bps_jtc_buddypress_sidebar_form'], 1, false ) : '';	
	$bps_jtc_administrator = ! empty($BPSoptionsJTC['bps_jtc_administrator']) ? checked( $BPSoptionsJTC['bps_jtc_administrator'], 1, false ) : '';	
	$bps_jtc_editor = ! empty($BPSoptionsJTC['bps_jtc_editor']) ? checked( $BPSoptionsJTC['bps_jtc_editor'], 1, false ) : '';	
	$bps_jtc_author = ! empty($BPSoptionsJTC['bps_jtc_author']) ? checked( $BPSoptionsJTC['bps_jtc_author'], 1, false ) : '';	
	$bps_jtc_contributor = ! empty($BPSoptionsJTC['bps_jtc_contributor']) ? checked( $BPSoptionsJTC['bps_jtc_contributor'], 1, false ) : '';	
	$bps_jtc_subscriber = ! empty($BPSoptionsJTC['bps_jtc_subscriber']) ? checked( $BPSoptionsJTC['bps_jtc_subscriber'], 1, false ) : '';	
	?>

    <div id="JTC-woocommerce" style="margin:10px 0px 10px 0px">
	<input type="checkbox" name="bps_enable_jtc_woocommerce" value="1" <?php echo esc_html($bps_enable_jtc_woocommerce); ?> /><label><?php _e(' Enable JTC for WooCommerce (BPS Pro Only)', 'bulletproof-security'); ?></label>
	</div>

   <label><strong><?php _e('Enable|Disable JTC For These Forms: ', 'bulletproof-security'); ?></strong></label><br />
   <label><i><?php _e('Check to Enable. Uncheck to Disable.', 'bulletproof-security'); ?></i></label><br />
    <input type="checkbox" name="bps_jtc_login_form" value="1" <?php echo esc_html($bps_jtc_login_form); ?> /><label><?php _e(' Login Form', 'bulletproof-security'); ?></label><br />
    <input type="checkbox" name="bps_jtc_register_form" value="1" <?php echo esc_html($bps_jtc_register_form); ?> /><label><?php _e(' Register Form (BPS Pro Only)', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_jtc_lostpassword_form" value="1" <?php echo esc_html($bps_jtc_lostpassword_form); ?> /><label><?php _e(' Lost Password Form (BPS Pro Only)', 'bulletproof-security'); ?></label><br />    
	<input type="checkbox" name="bps_jtc_comment_form" value="1" <?php echo esc_html($bps_jtc_comment_form); ?> /><label><?php _e(' Comment Form (BPS Pro Only)', 'bulletproof-security'); ?></label><br />
    <input type="checkbox" name="bps_jtc_mu_register_form" value="1" <?php echo esc_html($bps_jtc_mu_register_form); ?> /><label><?php _e(' Multisite Register Form (BPS Pro Only)', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_jtc_buddypress_register_form" value="1" <?php echo esc_html($bps_jtc_buddypress_register_form); ?> /><label><?php _e(' BuddyPress Register Form (BPS Pro Only)', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_jtc_buddypress_sidebar_form" value="1" <?php echo esc_html($bps_jtc_buddypress_sidebar_form); ?> /><label><?php _e(' BuddyPress Sidebar Login Form (BPS Pro Only)', 'bulletproof-security'); ?></label><br /><br />

    <label><strong><?php _e('Comment Form: (BPS Pro Only)', 'bulletproof-security'); ?></strong></label><br />
    <label><strong><?php _e('Enable|Disable JTC For These Registered/Logged In User Roles (BPS Pro Only): ', 'bulletproof-security'); ?></strong></label><br />  
  <label><i><?php _e('Check to Enable. Uncheck to Disable.', 'bulletproof-security'); ?></i></label><br />
    <div id="Roles-scroller">
    <input type="checkbox" name="bps_jtc_administrator" value="1" <?php echo esc_html($bps_jtc_administrator); ?> /><label><?php _e(' Administrator', 'bulletproof-security'); ?></label><br />
    <input type="checkbox" name="bps_jtc_editor" value="1" <?php echo esc_html($bps_jtc_editor); ?> /><label><?php _e(' Editor', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_jtc_author" value="1" <?php echo esc_html($bps_jtc_author); ?> /><label><?php _e(' Author', 'bulletproof-security'); ?></label><br />    
	<input type="checkbox" name="bps_jtc_contributor" value="1" <?php echo esc_html($bps_jtc_contributor); ?> /><label><?php _e(' Contributor', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_jtc_subscriber" value="1" <?php echo esc_html($bps_jtc_subscriber); ?> /><label><?php _e(' Subscriber', 'bulletproof-security'); ?></label><br />

<?php

	foreach ( get_editable_roles() as $role_name => $role_info ) {
	
		if ( $role_name != 'administrator' && $role_name != 'editor' && $role_name != 'author' && $role_name != 'contributor' && $role_name != 'subscriber' ) {
			
			$bps_jtc_custom_roles = ! empty($BPSoptionsJTC['bps_jtc_custom_roles'][$role_name]) ? checked( $BPSoptionsJTC['bps_jtc_custom_roles'][$role_name], 1, false ) : '';
			
			echo "<input type=\"checkbox\" name=\"bps_jtc_custom_roles[$role_name]\" value=\"1\"";
			echo esc_html($bps_jtc_custom_roles);
			echo " /><label> ". esc_html($role_info['name']) ."</label>".'<br>';
		}
	}
?> 
</div>
<br />
    
    <?php
	/*
	$bps_jtc_custom_form_error = ! empty($BPSoptionsJTC['bps_jtc_custom_form_error']) ? $BPSoptionsJTC['bps_jtc_custom_form_error'] : '<strong>ERROR</strong>: Incorrect CAPTCHA Entered.';
	$bps_jtc_comment_form_error = ! empty($BPSoptionsJTC['bps_jtc_comment_form_error']) ? $BPSoptionsJTC['bps_jtc_comment_form_error'] :'<strong>ERROR</strong>: Incorrect JTC CAPTCHA Entered. Click your Browser back button and re-enter the JTC CAPTCHA.';
	$bps_jtc_comment_form_label = ! empty($BPSoptionsJTC['bps_jtc_comment_form_label']) ? $BPSoptionsJTC['bps_jtc_comment_form_label'] : 'position:relative;top:0px;left:0px;padding:0px 0px 0px 0px;margin:0px 0px 0px 0px;';
	$bps_jtc_comment_form_input = ! empty($BPSoptionsJTC['bps_jtc_comment_form_input']) ? $BPSoptionsJTC['bps_jtc_comment_form_input'] : 'position:relative;top:0px;left:0px;padding:0px 0px 0px 0px;margin:0px 0px 0px 0px;';
	*/	
	
	$bps_jtc_custom_form_error = ! empty($BPSoptionsJTC['bps_jtc_custom_form_error']) ? '<strong>ERROR</strong>: Incorrect CAPTCHA Entered.' : '<strong>ERROR</strong>: Incorrect CAPTCHA Entered.';
	$bps_jtc_comment_form_error = ! empty($BPSoptionsJTC['bps_jtc_comment_form_error']) ? '<strong>ERROR</strong>: Incorrect JTC CAPTCHA Entered. Click your Browser back button and re-enter the JTC CAPTCHA.' : '<strong>ERROR</strong>: Incorrect JTC CAPTCHA Entered. Click your Browser back button and re-enter the JTC CAPTCHA.';
	$bps_jtc_comment_form_label = ! empty($BPSoptionsJTC['bps_jtc_comment_form_label']) ? 'position:relative;top:0px;left:0px;padding:0px 0px 0px 0px;margin:0px 0px 0px 0px;' : 'position:relative;top:0px;left:0px;padding:0px 0px 0px 0px;margin:0px 0px 0px 0px;';
	$bps_jtc_comment_form_input = ! empty($BPSoptionsJTC['bps_jtc_comment_form_input']) ? 'position:relative;top:0px;left:0px;padding:0px 0px 0px 0px;margin:0px 0px 0px 0px;' : 'position:relative;top:0px;left:0px;padding:0px 0px 0px 0px;margin:0px 0px 0px 0px;';	
	?>
    
    <label for="LSLog"><?php _e('Login Form: CAPTCHA Error message (BPS Pro Only)', 'bulletproof-security'); ?></label><br />
    <input type="text" id="crypt29" name="bps_jtc_custom_form_error" class="regular-text-short-fixed" style="width:75%;" value="<?php echo esc_html($bps_jtc_custom_form_error); ?>" /><br /><br />

    <label for="LSLog"><?php _e('Comment Form: CAPTCHA Error message (BPS Pro Only)', 'bulletproof-security'); ?></label><br />
    <input type="text" id="crypt30" name="bps_jtc_comment_form_error" class="regular-text-short-fixed" style="width:75%;" value="<?php echo esc_html($bps_jtc_comment_form_error); ?>" /><br /><br />
    
    <label><strong><?php _e('Comment Form: CSS Styling (BPS Pro Only)', 'bulletproof-security'); ?></strong></label><br />
    <label><?php _e('Comment Form Label (BPS Pro Only): <i>The JTC Title|Text above the Form Input text box</i>', 'bulletproof-security'); ?></label><br />
    <input type="text" id="crypt31" name="bps_jtc_comment_form_label" class="regular-text-short-fixed" style="width:75%;" value="<?php echo esc_html($bps_jtc_comment_form_label); ?>" /><br />
    <label><?php _e('Comment Form Input Text Box (BPS Pro Only): <i>The JTC CAPTCHA Form Input text box</i>', 'bulletproof-security'); ?></label><br />
    <input type="text" id="crypt32" name="bps_jtc_comment_form_input" class="regular-text-short-fixed" style="width:75%;" value="<?php echo esc_html($bps_jtc_comment_form_input); ?>" /><br /><br />

	<?php echo '<div id="jtc-tooltip" style="margin:0px 0px 10px 0px;max-width:640px"><label for="bps-mscan-label" style="">'.__('If you see an error or are unable to save your JTC option settings then click the Encrypt JTC Code button first and then click the Save Options button. Mouse over the question mark image to the right for help info.', 'bulletproof-security').'</label><strong><font color="black"><span class="tooltip-350-225"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:5px;" /><span>'.__('If your web host currently has ModSecurity installed or installs ModSecurity at a later time then ModSecurity will prevent you from saving your JTC options settings and CSS code unless you encrypt it first by clicking the Encrypt JTC Code button.', 'bulletproof-security').'<br><br>'.__('If you click the Encrypt JTC Code button and then want to edit your CSS code again click the Decrypt JTC Code button. After you are done editing click the Encrypt JTC Code button before clicking the Save Options button.', 'bulletproof-security').'<br><br>'.__('Click the JTC Anti-Spam|Anti-Hacker Question Mark help button for more help info.', 'bulletproof-security').'</span></span></font></strong></div>'; ?>

<input type="submit" name="Submit-Security-Log-Options-JTC" class="button bps-button"  style="margin-top:5px;" value="<?php esc_attr_e('Save Options', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Click OK to Proceed or click Cancel.', 'bulletproof-security'); echo $text; ?>')"/>
</form><br />
</div>  

	<button onclick="bpsJTCEncrypt()" class="button bps-encrypt-button"><?php esc_attr_e('Encrypt JTC Code', 'bulletproof-security'); ?></button> 
	<button onclick="bpsJTCDecrypt()" class="button bps-decrypt-button"><?php esc_attr_e('Decrypt JTC Code', 'bulletproof-security'); ?></button>

<?php
	$bps_tooltip_captcha_title_hidden_js = ! empty($BPSoptionsJTC['bps_tooltip_captcha_title_hidden']) ? $BPSoptionsJTC['bps_tooltip_captcha_title_hidden'] : '#000000';
	$bps_tooltip_captcha_title_after_hidden_js = ! empty($BPSoptionsJTC['bps_tooltip_captcha_title_after_hidden']) ? $BPSoptionsJTC['bps_tooltip_captcha_title_after_hidden'] : '#ff0000';
?>

<script type="text/javascript">
/* <![CDATA[ */
var colorPicker;
var colorPickerHidden;

// do not use json_encode here. unnecessary and breaks stuff
var defaultColor = "<?php echo $bps_tooltip_captcha_title_hidden_js; ?>";

window.addEventListener("load", startup, false);

function startup() {
	colorPicker = document.querySelector("#colorPicker");
	colorPicker.value = defaultColor;
	colorPicker.addEventListener("input", updateFirst, false);
	colorPicker.addEventListener("change", updateAll, false);
	colorPicker.select();
}

function updateFirst(event) {
	colorPickerText = document.querySelector("#colorPickerText");  
	colorPickerHidden = document.querySelector("#colorPickerHidden");

	if (colorPickerText) {
		colorPickerText.style.color = event.target.value;
		colorPickerHidden.value = event.target.value;
	}
}

function updateAll(event) {
	document.querySelectorAll("#colorPicker").forEach(function(colorPickerText) {
    colorPickerText.style.color = event.target.value;
	colorPickerHidden.value = event.target.value;
	});
}

var colorPicker2;
var colorPickerHidden2;

// do not use json_encode here. unnecessary and breaks stuff
var defaultColor2 = "<?php echo $bps_tooltip_captcha_title_after_hidden_js; ?>";

window.addEventListener("load", startup2, false);

function startup2() {
	colorPicker2 = document.querySelector("#colorPicker2");
	colorPicker2.value = defaultColor2;
	colorPicker2.addEventListener("input", updateFirst2, false);
	colorPicker2.addEventListener("change", updateAll2, false);
	colorPicker2.select();
}

function updateFirst2(event) {
	colorPickerText2 = document.querySelector("#colorPickerText2");  
	colorPickerHidden2 = document.querySelector("#colorPickerHidden2");

	if (colorPickerText2) {
		colorPickerText2.style.color = event.target.value;
		colorPickerHidden2.value = event.target.value;
	}
}

function updateAll2(event) {
	document.querySelectorAll("#colorPicker2").forEach(function(colorPickerText2) {
    colorPickerText2.style.color = event.target.value;
	colorPickerHidden2.value = event.target.value;
	});
}
/* ]]> */
</script>

<script type="text/javascript">
/* <![CDATA[ */
function bpsJTCEncrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var String1 = document.getElementById("crypt29").value;
  var String2 = document.getElementById("crypt30").value;  
  var String3 = document.getElementById("crypt31").value;
  var String4 = document.getElementById("crypt32").value; 

  // Prevent Double, Triple, etc. encryption
  // The includes() method is not supported in IE 11 (and earlier versions)
  var NoEncrypt1 = String1.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  var NoEncrypt2 = String2.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  var NoEncrypt3 = String3.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  var NoEncrypt4 = String4.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt1);

  let encryption = new bpsProJSEncryption();

  if (String1 != '' && NoEncrypt1 === false) {
  	var encrypted1 = encryption.encrypt(String1, nonceValue);
  }
  if (String2 != '' && NoEncrypt2 === false) {
  	var encrypted2 = encryption.encrypt(String2, nonceValue);
  }
  if (String3 != '' && NoEncrypt3 === false) {
  	var encrypted3 = encryption.encrypt(String3, nonceValue);
  }
  if (String4 != '' && NoEncrypt4 === false) {
  	var encrypted4 = encryption.encrypt(String4, nonceValue);
  }
  //console.log(encrypted1); 
  
  if (String1 != '' && NoEncrypt1 === false) {
  	document.getElementById("crypt29").value = encrypted1;
  }
  if (String2 != '' && NoEncrypt2 === false) {
  	document.getElementById("crypt30").value = encrypted2;
  }
  if (String3 != '' && NoEncrypt3 === false) {
  	document.getElementById("crypt31").value = encrypted3;
  }
  if (String4 != '' && NoEncrypt4 === false) {
  	document.getElementById("crypt32").value = encrypted4;
  }
}

function bpsJTCDecrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var String1 = document.getElementById("crypt29").value;
  var String2 = document.getElementById("crypt30").value;
  var String3 = document.getElementById("crypt31").value;
  var String4 = document.getElementById("crypt32").value;

  let encryption = new bpsProJSEncryption();

  if (String1 != '') {
	var decrypted1 = encryption.decrypt(String1, nonceValue);
  }
  if (String2 != '') {
	var decrypted2 = encryption.decrypt(String2, nonceValue);
  }
  if (String3 != '') {
	var decrypted3 = encryption.decrypt(String3, nonceValue);
  }
  if (String4 != '') {
	var decrypted4 = encryption.decrypt(String4, nonceValue);
  }
  //console.log(decrypted1);
  
  if (String1 != '') {
	document.getElementById("crypt29").value = decrypted1;
  }
  if (String2 != '') {
	document.getElementById("crypt30").value = decrypted2;
  }
  if (String1 != '') {
	document.getElementById("crypt31").value = decrypted3;
  }
  if (String1 != '') {
	document.getElementById("crypt32").value = decrypted4;
  }
}
/* ]]> */
</script>

</td>
  </tr>
</table>

</div>

<?php if ( is_multisite() && $blog_id != 1 ) { echo '<div style="margin:0px 0px 0px 0px;"></div>'; } else { ?>

<div id="bps-tabs-3" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<h3 style="margin:0px 0px 5px 0px;"><?php _e('Idle Session Logout|Auth Cookie Expiration', 'bulletproof-security'); ?>  <button id="bps-open-modal3" class="button bps-modal-button"><img src="<?php echo plugins_url('/bulletproof-security/admin/images/question-mark-large.jpg'); ?>" style="margin:0px 0px 0px -10px" /></button></h3>

<div id="bps-modal-content3" class="bps-dialog-hide" title="<?php _e('Idle Session Logout|Auth Cookie Expiration', 'bulletproof-security'); ?>">

	<div id="dialog-anchor" style="position:relative;top:-30px;left:0px"><a href="#"></a></div>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-readme-table">
  <tr>
    <td class="bps-readme-table-td">

<?php 
	$text = '<strong>'.__('This Question Mark Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
	echo $text; 	
	
	$bpsPro_text = '<strong><font color="blue">'.__('Want even more security protection for the ridiculously cheap one-time price of $69.95', 'bulletproof-security').'</font><br>'.__('BPS Pro comes with free unlimited installations, upgrades & support for life. No yearly subscriptions or additional costs.', 'bulletproof-security').'<br><br>'.__('BBS Pro has an amazing track record. BPS Pro is installed on 60,000+ websites. Not a single one of those websites has been hacked in 10+ years.', 'bulletproof-security').'<br><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Get BPS Pro">'.__('Get BPS Pro', 'bulletproof-security').'</a><br><a href="https://www.ait-pro.com/bps-features/" target="_blank" title="BPS Pro Features">'.__('BPS Pro Features', 'bulletproof-security').'</a></strong><br><br>';	
	echo $bpsPro_text;

	$text = '<strong><font color="blue">'.__('Forum Help Links: ', 'bulletproof-security').'</font></strong><br>'; 	
	echo $text;
?>
	<strong><a href="https://forum.ait-pro.com/forums/topic/idle-session-logout-isl-and-authentication-cookie-expiration-ace" title="ISL and ACE" target="_blank">
	<?php _e('ISL and ACE Forum Topic', 'bulletproof-security'); ?></a></strong><br /><br />

<?php
	echo $bps_modal_content3;
	$text = '<strong>'.__('The Help & FAQ tab pages contain help links.', 'bulletproof-security').'</strong>'; 
	echo $text;
?>
    </td>
  </tr> 
</table> 

</div>

<?php

	$scrolltoISLMessage = isset($_REQUEST['scrolltoISLMessage']) ? (int) $_REQUEST['scrolltoISLMessage'] : 0;

// ISL Form processing
if ( isset( $_POST['Submit-ISL-Options'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bps_isl_logout' );
	
	$Custom_Roles = empty($_POST['bps_isl_custom_roles']) ? array( 'bps', '' ) : $_POST['bps_isl_custom_roles'];
		
	switch( $_POST['Submit-ISL-Options'] ) {
		case __('Save Options', 'bulletproof-security'):
		
		if ( ! empty($Custom_Roles) ) {
			
			$Custom_Roles_array = array();
			
			foreach ( $Custom_Roles as $key => $value ) {
				
				if ( $value == '1' ) {
					$Custom_Roles_array[$key] = $value;
				} 
			}
		
		} else {
			
			$Custom_Roles_array = array( 'bps', '' );
		}

		$Encryption = new bpsProPHPEncryption();
		$nonceValue = 'ghbhnyxu';
	
		$pos1 = strpos( $_POST['bps_isl_custom_css_1'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos2 = strpos( $_POST['bps_isl_custom_css_2'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos3 = strpos( $_POST['bps_isl_custom_css_3'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
		$pos4 = strpos( $_POST['bps_isl_custom_css_4'], 'eyJjaXBoZXJ0ZXh0Ijoi' );
	
		if ( $pos1 === false ) {
			$bps_isl_custom_css_1 = sanitize_text_field(stripslashes($_POST['bps_isl_custom_css_1']));
		} else {
			$bps_isl_custom_css_1 = $Encryption->decrypt($_POST['bps_isl_custom_css_1'], $nonceValue);
		}
	
		if ( $pos2 === false ) {
			$bps_isl_custom_css_2 = sanitize_text_field(stripslashes($_POST['bps_isl_custom_css_2']));
		} else {
			$bps_isl_custom_css_2 = $Encryption->decrypt($_POST['bps_isl_custom_css_2'], $nonceValue);
		}
	
		if ( $pos3 === false ) {
			$bps_isl_custom_css_3 = sanitize_text_field(stripslashes($_POST['bps_isl_custom_css_3']));
		} else {
			$bps_isl_custom_css_3 = $Encryption->decrypt($_POST['bps_isl_custom_css_3'], $nonceValue);
		}
	
		if ( $pos4 === false ) {
			$bps_isl_custom_css_4 = sanitize_text_field(stripslashes($_POST['bps_isl_custom_css_4']));
		} else {
			$bps_isl_custom_css_4 = $Encryption->decrypt($_POST['bps_isl_custom_css_4'], $nonceValue);
		}
	}

	$bps_isl							= sanitize_text_field($_POST['bps_isl']);
	$bps_isl_timeout 					= sanitize_text_field($_POST['bps_isl_timeout']);
	$bps_isl_logout_url 				= sanitize_url($_POST['bps_isl_logout_url']);
	$bps_isl_login_url 					= sanitize_url($_POST['bps_isl_login_url']);
	$bps_isl_custom_message 			= sanitize_textarea_field($_POST['bps_isl_custom_message']);	
	$bps_isl_user_account_exceptions 	= sanitize_text_field($_POST['bps_isl_user_account_exceptions']);
	$bps_isl_uri_exclusions 			= sanitize_textarea_field($_POST['bps_isl_uri_exclusions']);

	$bps_isl_administrator 				= ! empty($_POST['bps_isl_administrator']) ? '1' : '';
	$bps_isl_editor 					= ! empty($_POST['bps_isl_editor']) ? '1' : '';
	$bps_isl_author 					= ! empty($_POST['bps_isl_author']) ? '1' : '';
	$bps_isl_contributor 				= ! empty($_POST['bps_isl_contributor']) ? '1' : '';
	$bps_isl_subscriber 				= ! empty($_POST['bps_isl_subscriber']) ? '1' : '';
	$bps_isl_tinymce 					= ! empty($_POST['bps_isl_tinymce']) ? '1' : '';

	$ISL_Options = array(
	'bps_isl' 							=> $bps_isl, 
	'bps_isl_timeout' 					=> $bps_isl_timeout, 
	'bps_isl_logout_url' 				=> $bps_isl_logout_url, 
	'bps_isl_login_url' 				=> $bps_isl_login_url,
	'bps_isl_custom_message' 			=> $bps_isl_custom_message,
	'bps_isl_custom_css_1' 				=> $bps_isl_custom_css_1,
	'bps_isl_custom_css_2' 				=> $bps_isl_custom_css_2,
	'bps_isl_custom_css_3' 				=> $bps_isl_custom_css_3,
	'bps_isl_custom_css_4' 				=> $bps_isl_custom_css_4,
	'bps_isl_user_account_exceptions' 	=> $bps_isl_user_account_exceptions, 
	'bps_isl_administrator' 			=> $bps_isl_administrator, 
	'bps_isl_editor' 					=> $bps_isl_editor, 
	'bps_isl_author' 					=> $bps_isl_author, 
	'bps_isl_contributor' 				=> $bps_isl_contributor, 
	'bps_isl_subscriber' 				=> $bps_isl_subscriber, 
	'bps_isl_tinymce' 					=> $bps_isl_tinymce, 
	'bps_isl_uri_exclusions' 			=> $bps_isl_uri_exclusions, 
	'bps_isl_custom_roles' 				=> $Custom_Roles_array  
	);	
	
	foreach( $ISL_Options as $key => $value ) {
		update_option('bulletproof_security_options_idle_session', $ISL_Options);
	}
	
	if ( $bps_isl == 'On' ) {
		echo $bps_topDiv;
		echo '<strong><font color="green">'.__('Settings Saved. ISL has been turned On.', 'bulletproof-security').'</font></strong><br>';
		echo $bps_bottomDiv;
	}
	
	if ( $bps_isl == 'Off' ) {
		echo $bps_topDiv;
		echo '<strong><font color="green">'.__('Settings Saved. ISL has been turned Off.', 'bulletproof-security').'</font></strong><br>';
		echo $bps_bottomDiv;
	}
}
?>

<div id="Idle-Session-Logout">

<form name="IdleSessionLogout" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-3' ); ?>" method="post">
	
	<?php wp_nonce_field('bps_isl_logout'); 
	$BPS_ISL_options = get_option('bulletproof_security_options_idle_session'); 
	$ISL_on_off = isset($BPS_ISL_options['bps_isl']) ? $BPS_ISL_options['bps_isl'] : '';
	$ISL_timeout = ! empty($BPS_ISL_options['bps_isl_timeout']) ? $BPS_ISL_options['bps_isl_timeout'] : '60';
	$ISL_logout_url = ! empty($BPS_ISL_options['bps_isl_logout_url']) ? $BPS_ISL_options['bps_isl_logout_url'] : plugins_url('/bulletproof-security/isl-logout.php');
	$ISL_login_url = ! empty($BPS_ISL_options['bps_isl_login_url']) ? $BPS_ISL_options['bps_isl_login_url'] : site_url('/wp-login.php');
	$ISL_exclusions = isset($BPS_ISL_options['bps_isl_uri_exclusions']) ? $BPS_ISL_options['bps_isl_uri_exclusions'] : '';
	$ISL_message = isset($BPS_ISL_options['bps_isl_custom_message']) ? $BPS_ISL_options['bps_isl_custom_message'] : '';
	$ISL_css_1 = ! empty($BPS_ISL_options['bps_isl_custom_css_1']) ? $BPS_ISL_options['bps_isl_custom_css_1'] : 'background-color:#fff;line-height:normal;';
	$ISL_css_2 = ! empty($BPS_ISL_options['bps_isl_custom_css_2']) ? $BPS_ISL_options['bps_isl_custom_css_2'] : 'position:fixed;top:20%;left:0%;text-align:center;height:100%;width:100%;';
	$ISL_css_3 = ! empty($BPS_ISL_options['bps_isl_custom_css_3']) ? $BPS_ISL_options['bps_isl_custom_css_3'] : 'border:5px solid gray;background-color:#BCE2F1;';
	$ISL_css_4 = ! empty($BPS_ISL_options['bps_isl_custom_css_4']) ? $BPS_ISL_options['bps_isl_custom_css_4'] : 'font-family:Verdana, Arial, Helvetica, sans-serif;font-size:18px;font-weight:bold;';
	$ISL_exceptions = isset($BPS_ISL_options['bps_isl_user_account_exceptions']) ? $BPS_ISL_options['bps_isl_user_account_exceptions'] : '';	

	$bps_isl_administrator = ! empty($BPS_ISL_options['bps_isl_administrator']) ? checked( $BPS_ISL_options['bps_isl_administrator'], 1, false ) : '';	
	$bps_isl_editor = ! empty($BPS_ISL_options['bps_isl_editor']) ? checked( $BPS_ISL_options['bps_isl_editor'], 1, false ) : '';	
	$bps_isl_author = ! empty($BPS_ISL_options['bps_isl_author']) ? checked( $BPS_ISL_options['bps_isl_author'], 1, false ) : '';	
	$bps_isl_contributor = ! empty($BPS_ISL_options['bps_isl_contributor']) ? checked( $BPS_ISL_options['bps_isl_contributor'], 1, false ) : '';	
	$bps_isl_subscriber = ! empty($BPS_ISL_options['bps_isl_subscriber']) ? checked( $BPS_ISL_options['bps_isl_subscriber'], 1, false ) : '';	
	$bps_isl_tinymce = ! empty($BPS_ISL_options['bps_isl_tinymce']) ? checked( $BPS_ISL_options['bps_isl_tinymce'], 1, false ) : '';
	
	?>
 
 <h3><?php _e('Idle Session Logout (ISL) Settings', 'bulletproof-security'); ?></h3>   
    
<table border="0">
  <tr>
    <td>
    <label for="LSLog"><?php _e('Turn On|Turn Off:', 'bulletproof-security'); ?></label><br />
    <select name="bps_isl" class="form-250">
	<option value="Off" <?php selected('Off', $ISL_on_off); ?>><?php _e('ISL Off', 'bulletproof-security'); ?></option>
	<option value="On" <?php selected('On', $ISL_on_off); ?>><?php _e('ISL On', 'bulletproof-security'); ?></option>
	</select>
	</td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Idle Session Logout Time in Minutes:', 'bulletproof-security'); ?></label><br />
    <input type="text" name="bps_isl_timeout" class="regular-text-250" value="<?php echo esc_html($ISL_timeout); ?>" />
    </td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Idle Session Logout Page URL:', 'bulletproof-security'); ?></label><br />
    <input type="text" name="bps_isl_logout_url" class="regular-text-450" value="<?php echo esc_url($ISL_logout_url); ?>" />
    </td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Idle Session Logout Page Login URL:', 'bulletproof-security'); ?></label><br />
    <label><strong><i><?php _e('Enter/Type: "No" (without quotes) if you do not want a Login URL displayed.', 'bulletproof-security'); ?></i></strong></label><br />
    <input type="text" name="bps_isl_login_url" class="regular-text-450" value="<?php echo esc_url($ISL_login_url); ?>" />
    </td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Idle Session Logout Exclude URLs|URIs:', 'bulletproof-security'); ?></label><br />
	<label><strong><i><?php _e('Enter URIs separated by a comma and a space: /some-post/, /some-page/', 'bulletproof-security'); ?></i></strong></label><br />
 	<textarea style="width:450px" class="PFW-Allow-From-Text-Area" name="bps_isl_uri_exclusions" tabindex="1"><?php echo esc_textarea($ISL_exclusions); ?></textarea>
	<input type="hidden" name="scrolltoISLMessage" id="scrolltoISLMessage" value="<?php echo esc_html( $scrolltoISLMessage ); ?>" />
    </td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Idle Session Logout Page Custom Message:', 'bulletproof-security'); ?></label><br />
 	<textarea style="width:450px" class="PFW-Allow-From-Text-Area" name="bps_isl_custom_message" tabindex="1"><?php echo esc_textarea($ISL_message); ?></textarea>
	<input type="hidden" name="scrolltoISLMessage" id="scrolltoISLMessage" value="<?php echo esc_html( $scrolltoISLMessage ); ?>" />
    </td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Idle Session Logout Page Custom CSS Style:', 'bulletproof-security'); ?></label><br />
	<label><strong><?php echo 'body CSS property'; ?></strong></label><br />
    <input type="text" id="crypt33" name="bps_isl_custom_css_1" class="regular-text-450" value="<?php echo esc_html($ISL_css_1); ?>" />
    <br />
	<label><strong><?php echo '#bpsMessage CSS property'; ?></strong></label><br />
    <input type="text" id="crypt34" name="bps_isl_custom_css_2" class="regular-text-450" value="<?php echo esc_html($ISL_css_2); ?>" />
    <br />
	<label><strong><?php echo '#bpsMessageTextBox CSS property'; ?></strong></label><br />
    <input type="text" id="crypt35" name="bps_isl_custom_css_3" class="regular-text-450" value="<?php echo esc_html($ISL_css_3); ?>" />
    <br />
	<label><strong><?php echo 'p CSS property'; ?></strong></label><br />
    <input type="text" id="crypt36" name="bps_isl_custom_css_4" class="regular-text-450" value="<?php echo esc_html($ISL_css_4); ?>" />
    <br />
    </td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('User Account Exceptions:', 'bulletproof-security'); ?></label><br />
    <label for="LSLog"><i><?php _e('Enter User Account names separated by a comma and a space: johnDoe, janeDoe', 'bulletproof-security'); ?></i></label><br />
    <label for="LSLog"><i><?php _e('Idle Session Logout Time Will Not Be Applied For These User Accounts.', 'bulletproof-security'); ?></i></label><br />
    <input type="text" name="bps_isl_user_account_exceptions" class="regular-text-450" value="<?php echo esc_html($ISL_exceptions); ?>" />
	</td>
  </tr>
  <tr>
	<td>
    <label><strong><?php _e('Enable|Disable Idle Session Logouts For These User Roles: ', 'bulletproof-security'); ?></strong></label><br />  
  	<label><strong><i><?php _e('Check to Enable. Uncheck to Disable. See the Question Mark help button for details.', 'bulletproof-security'); ?></i></strong></label><br />
    <div id="Roles-scroller">
   <input type="checkbox" name="bps_isl_administrator" value="1" <?php echo esc_html($bps_isl_administrator); ?> /><label><?php _e(' Administrator', 'bulletproof-security'); ?></label><br />
    <input type="checkbox" name="bps_isl_editor" value="1" <?php echo esc_html($bps_isl_editor); ?> /><label><?php _e(' Editor', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_isl_author" value="1" <?php echo esc_html($bps_isl_author); ?> /><label><?php _e(' Author', 'bulletproof-security'); ?></label><br />    
	<input type="checkbox" name="bps_isl_contributor" value="1" <?php echo esc_html($bps_isl_contributor); ?> /><label><?php _e(' Contributor', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_isl_subscriber" value="1" <?php echo esc_html($bps_isl_subscriber); ?> /><label><?php _e(' Subscriber', 'bulletproof-security'); ?></label><br />

<?php

	foreach ( get_editable_roles() as $role_name => $role_info ) {
	
		if ( $role_name != 'administrator' && $role_name != 'editor' && $role_name != 'author' && $role_name != 'contributor' && $role_name != 'subscriber' ) {
			
			$bps_isl_custom_roles = ! empty($BPS_ISL_options['bps_isl_custom_roles'][$role_name]) ? checked( $BPS_ISL_options['bps_isl_custom_roles'][$role_name], 1, false ) : '';	
	
			echo "<input type=\"checkbox\" name=\"bps_isl_custom_roles[$role_name]\" value=\"1\""; 
			echo esc_html($bps_isl_custom_roles);	
			echo " /><label> ". esc_html($role_info['name']) ."</label>".'<br>';
		}
	}
?> 
</div>

	</td>
  </tr>
  <tr>
	<td>
    <label><strong><?php _e('Enable|Disable Idle Session Logouts For TinyMCE Editors: ', 'bulletproof-security'); ?></strong></label><br />  
  <label><strong><i><?php _e('Check to Disable. Uncheck to Enable. See the Question Mark help button for details.', 'bulletproof-security'); ?></i></strong></label><br />
    <input type="checkbox" name="bps_isl_tinymce" value="1" <?php  echo esc_html($bps_isl_tinymce); ?> /><label><?php _e(' Enable|Disable ISL For TinyMCE Editor', 'bulletproof-security'); ?></label><br /><br />

	<?php echo '<div id="jtc-tooltip" style="margin:0px 0px 10px 0px;max-width:640px"><label for="bps-mscan-label" style="">'.__('If you see an error or are unable to save your ISL option settings then click the Encrypt ISL Code button first and then click the Save Options button. Mouse over the question mark image to the right for help info.', 'bulletproof-security').'</label><strong><font color="black"><span class="tooltip-350-225"><img src="'.plugins_url('/bulletproof-security/admin/images/question-mark.png').'" style="position:relative;top:3px;left:5px;" /><span>'.__('If your web host currently has ModSecurity installed or installs ModSecurity at a later time then ModSecurity will prevent you from saving your ISL option settings and CSS code unless you encrypt it first by clicking the Encrypt ISL Code button.', 'bulletproof-security').'<br><br>'.__('If you click the Encrypt ISL Code button and then want to edit your CSS code again click the Decrypt ISL Code button. After you are done editing click the Encrypt ISL Code button before clicking the Save Options button.', 'bulletproof-security').'<br><br>'.__('Click the Idle Session Logout|Auth Cookie Expiration Question Mark help button for more help info.', 'bulletproof-security').'</span></span></font></strong></div>'; ?>

<input type="submit" name="Submit-ISL-Options" class="button bps-button"  style="margin:5px 0px 15px 0px;" value="<?php esc_attr_e('Save Options', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Click OK to Proceed or click Cancel.', 'bulletproof-security'); echo $text; ?>')"/>
</form>

</td>
</tr>
</table>
</form>

</div> 

	<button onclick="bpsISLEncrypt()" class="button bps-encrypt-button"><?php esc_attr_e('Encrypt ISL Code', 'bulletproof-security'); ?></button> 
	<button onclick="bpsISLDecrypt()" class="button bps-decrypt-button"><?php esc_attr_e('Decrypt ISL Code', 'bulletproof-security'); ?></button>
    
<script type="text/javascript">
/* <![CDATA[ */
function bpsISLEncrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var String1 = document.getElementById("crypt33").value;
  var String2 = document.getElementById("crypt34").value;  
  var String3 = document.getElementById("crypt35").value;
  var String4 = document.getElementById("crypt36").value; 

  // Prevent Double, Triple, etc. encryption
  // The includes() method is not supported in IE 11 (and earlier versions)
  var NoEncrypt1 = String1.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  var NoEncrypt2 = String2.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  var NoEncrypt3 = String3.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  var NoEncrypt4 = String4.includes("eyJjaXBoZXJ0ZXh0Ijoi");
  //console.log(NoEncrypt1);

  let encryption = new bpsProJSEncryption();

  if (String1 != '' && NoEncrypt1 === false) {
  	var encrypted1 = encryption.encrypt(String1, nonceValue);
  }
  if (String2 != '' && NoEncrypt2 === false) {
  	var encrypted2 = encryption.encrypt(String2, nonceValue);
  }
  if (String3 != '' && NoEncrypt3 === false) {
  	var encrypted3 = encryption.encrypt(String3, nonceValue);
  }
  if (String4 != '' && NoEncrypt4 === false) {
  	var encrypted4 = encryption.encrypt(String4, nonceValue);
  }
  //console.log(encrypted1); 
  
  if (String1 != '' && NoEncrypt1 === false) {
  	document.getElementById("crypt33").value = encrypted1;
  }
  if (String2 != '' && NoEncrypt2 === false) {
  	document.getElementById("crypt34").value = encrypted2;
  }
  if (String3 != '' && NoEncrypt3 === false) {
  	document.getElementById("crypt35").value = encrypted3;
  }
  if (String4 != '' && NoEncrypt4 === false) {
  	document.getElementById("crypt36").value = encrypted4;
  }
}

function bpsISLDecrypt() {

  var nonceValue = '<?php echo $bps_nonceValue; ?>';

  var String1 = document.getElementById("crypt33").value;
  var String2 = document.getElementById("crypt34").value;
  var String3 = document.getElementById("crypt35").value;
  var String4 = document.getElementById("crypt36").value;

  let encryption = new bpsProJSEncryption();

  if (String1 != '') {
	var decrypted1 = encryption.decrypt(String1, nonceValue);
  }
  if (String2 != '') {
	var decrypted2 = encryption.decrypt(String2, nonceValue);
  }
  if (String3 != '') {
	var decrypted3 = encryption.decrypt(String3, nonceValue);
  }
  if (String4 != '') {
	var decrypted4 = encryption.decrypt(String4, nonceValue);
  }
  //console.log(decrypted1);
  
  if (String1 != '') {
	document.getElementById("crypt33").value = decrypted1;
  }
  if (String2 != '') {
	document.getElementById("crypt34").value = decrypted2;
  }
  if (String1 != '') {
	document.getElementById("crypt35").value = decrypted3;
  }
  if (String1 != '') {
	document.getElementById("crypt36").value = decrypted4;
  }
}
/* ]]> */
</script>

<div id="ACE-Menu-Link"></div>

<h3 style="border-bottom:1px solid #999999;"><?php _e('WordPress Authentication Cookie Expiration (ACE) Settings', 'bulletproof-security'); ?></h3>

<div id="ACE-logout" style="position:relative;top:0px;left:0px;margin:0px 0px 0px 0px;">

<?php
// ACE Form processing
if ( isset( $_POST['Submit-ACE-Options'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bps_auth_cookie_expiration' );
	
	$Custom_Roles = empty($_POST['bps_ace_custom_roles']) ? array( 'bps', '' ) : $_POST['bps_ace_custom_roles'];		
	
	switch( $_POST['Submit-ACE-Options'] ) {
		case __('Save Options', 'bulletproof-security'):
		
		if ( ! empty($Custom_Roles) ) {
			
			$Custom_Roles_array = array();
			
			foreach ( $Custom_Roles as $key => $value ) {
				
				if ( $value == '1' ) {
					$Custom_Roles_array[$key] = $value;
				} 
			}
		
		} else {
			
			$Custom_Roles_array = array( 'bps', '' );
		}
	}

	$bps_ace 							= sanitize_text_field($_POST['bps_ace']);
	$bps_ace_expiration 				= sanitize_text_field($_POST['bps_ace_expiration']);
	$bps_ace_rememberme_expiration 		= sanitize_text_field($_POST['bps_ace_rememberme_expiration']);
	$bps_ace_user_account_exceptions 	= sanitize_text_field($_POST['bps_ace_user_account_exceptions']);		

	$bps_ace_administrator 				= ! empty($_POST['bps_ace_administrator']) ? '1' : '';
	$bps_ace_editor 					= ! empty($_POST['bps_ace_editor']) ? '1' : '';
	$bps_ace_author 					= ! empty($_POST['bps_ace_author']) ? '1' : '';
	$bps_ace_contributor 				= ! empty($_POST['bps_ace_contributor']) ? '1' : '';
	$bps_ace_subscriber 				= ! empty($_POST['bps_ace_subscriber']) ? '1' : '';
	$bps_ace_rememberme_disable 		= ! empty($_POST['bps_ace_rememberme_disable']) ? '1' : '';

	$ACE_Options = array(
	'bps_ace' 							=> $bps_ace, 
	'bps_ace_expiration' 				=> $bps_ace_expiration, 
	'bps_ace_rememberme_expiration' 	=> $bps_ace_rememberme_expiration, 
	'bps_ace_user_account_exceptions' 	=> $bps_ace_user_account_exceptions, 
	'bps_ace_administrator' 			=> $bps_ace_administrator, 
	'bps_ace_editor' 					=> $bps_ace_editor, 
	'bps_ace_author' 					=> $bps_ace_author, 
	'bps_ace_contributor' 				=> $bps_ace_contributor, 
	'bps_ace_subscriber' 				=> $bps_ace_subscriber, 
	'bps_ace_rememberme_disable' 		=> $bps_ace_rememberme_disable, 
	'bps_ace_custom_roles' 				=> $Custom_Roles_array  
	);	
	
	foreach( $ACE_Options as $key => $value ) {
		update_option('bulletproof_security_options_auth_cookie', $ACE_Options);
	}
	
	if ( $bps_ace == 'On' ) {
		echo $bps_topDiv;
		echo '<strong><font color="green">'.__('Settings Saved. ACE has been turned On.', 'bulletproof-security').'</font></strong><br>';
		echo $bps_bottomDiv;
	}
	
	if ( $bps_ace == 'Off' ) {
		echo $bps_topDiv;
		echo '<strong><font color="green">'.__('Settings Saved. ACE has been turned Off.', 'bulletproof-security').'</font></strong><br>';
		echo $bps_bottomDiv;
	}
}
?>

<form name="ACELogout" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-3' ); ?>" method="post">
	<?php wp_nonce_field('bps_auth_cookie_expiration');
	$BPS_ACE_options = get_option('bulletproof_security_options_auth_cookie'); 
	$ACE_on_off = ! isset($BPS_ACE_options['bps_ace']) ? '' : $BPS_ACE_options['bps_ace'];
	$ACE_Expiration = ! empty($BPS_ACE_options['bps_ace_expiration']) ? $BPS_ACE_options['bps_ace_expiration'] : '2880';
	$ACE_RM_Expiration = ! empty($BPS_ACE_options['bps_ace_rememberme_expiration']) ? $BPS_ACE_options['bps_ace_rememberme_expiration'] : '20160';
	$bps_ace_rememberme_disable = ! empty($BPS_ACE_options['bps_ace_rememberme_disable']) ? checked( $BPS_ACE_options['bps_ace_rememberme_disable'], 1, false ) : '';	
	$ACE_exceptions = isset($BPS_ACE_options['bps_ace_user_account_exceptions']) ? esc_html($BPS_ACE_options['bps_ace_user_account_exceptions']) : '';	

	$bps_ace_administrator = ! empty($BPS_ACE_options['bps_ace_administrator']) ? checked( $BPS_ACE_options['bps_ace_administrator'], 1, false ) : '';	
	$bps_ace_editor = ! empty($BPS_ACE_options['bps_ace_editor']) ? checked( $BPS_ACE_options['bps_ace_editor'], 1, false ) : '';	
	$bps_ace_author = ! empty($BPS_ACE_options['bps_ace_author']) ? checked( $BPS_ACE_options['bps_ace_author'], 1, false ) : '';	
	$bps_ace_contributor = ! empty($BPS_ACE_options['bps_ace_contributor']) ? checked( $BPS_ACE_options['bps_ace_contributor'], 1, false ) : '';	
	$bps_ace_subscriber = ! empty($BPS_ACE_options['bps_ace_subscriber']) ? checked( $BPS_ACE_options['bps_ace_subscriber'], 1, false ) : '';
	?>

<table border="0">
  <tr>
    <td>
    <label for="LSLog"><?php _e('Turn On|Turn Off:', 'bulletproof-security'); ?></label><br />
    <select name="bps_ace" class="form-250"><br />
	<option value="Off" <?php selected('Off', $ACE_on_off); ?>><?php _e('ACE Off', 'bulletproof-security'); ?></option>
	<option value="On" <?php selected('On', $ACE_on_off); ?>><?php _e('ACE On', 'bulletproof-security'); ?></option>
	</select>
	</td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Auth Cookie Expiration Time in Minutes:', 'bulletproof-security'); ?></label><br />
    <label for="LSLog"><?php _e('WP Default setting is 2880 Minutes/2 Days:', 'bulletproof-security'); ?></label><br />
    <input type="text" name="bps_ace_expiration" class="regular-text-250" value="<?php echo esc_html($ACE_Expiration); ?>" />
    </td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('Remember Me Auth Cookie Expiration Time in Minutes:', 'bulletproof-security'); ?></label><br />
    <label for="LSLog"><?php _e('WP Default setting is 20160 Minutes/14 Days:', 'bulletproof-security'); ?></label><br />
    <input type="text" name="bps_ace_rememberme_expiration" class="regular-text-250" value="<?php echo esc_html($ACE_RM_Expiration); ?>" />
	</td>
  </tr>
  <tr>
	<td>
    <label><strong><?php _e('Enable|Disable Remember Me Checkbox:', 'bulletproof-security'); ?></strong></label><br />  
  <label><strong><i><?php _e('Check to Disable. Uncheck to Enable. See the Question Mark help button for details.', 'bulletproof-security'); ?></i></strong></label><br />
    <input type="checkbox" name="bps_ace_rememberme_disable" value="1" <?php echo esc_html($bps_ace_rememberme_disable); ?> /><label><?php _e(' Disable & do not display the Remember Me checkbox', 'bulletproof-security'); ?></label><br />
</td>
  </tr>
  <tr>
    <td>
    <label for="LSLog"><?php _e('User Account Exceptions:', 'bulletproof-security'); ?></label><br />
    <label for="LSLog"><i><?php _e('Enter User Account names separated by a comma and a space: johnDoe, janeDoe', 'bulletproof-security'); ?></i></label><br />
    <label for="LSLog"><i><?php _e('Auth Cookie Expiration Time Will Not Be Applied To These User Accounts.', 'bulletproof-security'); ?></i></label><br />
    <input type="text" name="bps_ace_user_account_exceptions" class="regular-text-450" value="<?php echo esc_html($ACE_exceptions); ?>" />
	</td>
  </tr>
  <tr>
	<td>
    <label><strong><?php _e('Enable|Disable Auth Cookie Expiration Time For These User Roles: ', 'bulletproof-security'); ?></strong></label><br />  
  <label><strong><i><?php _e('Check to Enable. Uncheck to Disable. See the Question Mark help button for details.', 'bulletproof-security'); ?></i></strong></label><br />
    
    <div id="Roles-scroller">
    <input type="checkbox" name="bps_ace_administrator" value="1" <?php echo esc_html($bps_ace_administrator); ?> /><label><?php _e(' Administrator', 'bulletproof-security'); ?></label><br />
    <input type="checkbox" name="bps_ace_editor" value="1" <?php echo esc_html($bps_ace_editor); ?> /><label><?php _e(' Editor', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_ace_author" value="1" <?php echo esc_html($bps_ace_author); ?> /><label><?php _e(' Author', 'bulletproof-security'); ?></label><br />    
	<input type="checkbox" name="bps_ace_contributor" value="1" <?php echo esc_html($bps_ace_contributor); ?> /><label><?php _e(' Contributor', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_ace_subscriber" value="1" <?php echo esc_html($bps_ace_subscriber); ?> /><label><?php _e(' Subscriber', 'bulletproof-security'); ?></label><br />

<?php

	foreach ( get_editable_roles() as $role_name => $role_info ) {
	
		if ( $role_name != 'administrator' && $role_name != 'editor' && $role_name != 'author' && $role_name != 'contributor' && $role_name != 'subscriber' ) {
			
			$bps_ace_custom_roles = ! empty($BPS_ACE_options['bps_ace_custom_roles'][$role_name]) ? checked( $BPS_ACE_options['bps_ace_custom_roles'][$role_name], 1, false ) : '';

			echo "<input type=\"checkbox\" name=\"bps_ace_custom_roles[$role_name]\" value=\"1\"";
			echo esc_html($bps_ace_custom_roles);
			echo " /><label> ". esc_html($role_info['name']) ."</label>".'<br>';
		}
	}
?>    
	</div>    

	<input type="submit" name="Submit-ACE-Options" class="button bps-button" style="margin:15px 0px 15px 0px;" value="<?php esc_attr_e('Save Options', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Click OK to Proceed or click Cancel.', 'bulletproof-security'); echo $text; ?>')"/>
</form><br />
</div> 

</td>
  </tr>
</table> 

<?php } ?>

</td>
  </tr>
</table>

</div>

<div id="bps-tabs-4" class="bps-tab-page" style="">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help" style="max-width:800px;">

<h3 style="margin:0px 0px 10px 0px;"><?php _e('Force Strong Passwords', 'bulletproof-security'); ?>  <button id="bps-open-modal4" class="button bps-modal-button">
<img src="<?php echo plugins_url('/bulletproof-security/admin/images/question-mark-large.jpg'); ?>" style="margin:0px 0px 0px -10px" /></button></h3>

<div id="bps-modal-content4" class="bps-dialog-hide" title="<?php _e('Force Strong Passwords', 'bulletproof-security'); ?>">
	<div id="dialog-anchor" style="position:relative;top:-30px;left:0px"><a href="#"></a></div>
	<p>
	<?php 
		$text = '<strong>'.__('This Question Mark Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text; 

	    $bpsPro_text = '<strong><font color="blue">'.__('Want even more security protection for the ridiculously cheap one-time price of $69.95', 'bulletproof-security').'</font><br>'.__('BPS Pro comes with free unlimited installations, upgrades & support for life. No yearly subscriptions or additional costs.', 'bulletproof-security').'<br><br>'.__('BBS Pro has an amazing track record. BPS Pro is installed on 60,000+ websites. Not a single one of those websites has been hacked in 10+ years.', 'bulletproof-security').'<br><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Get BPS Pro">'.__('Get BPS Pro', 'bulletproof-security').'</a><br><a href="https://www.ait-pro.com/bps-features/" target="_blank" title="BPS Pro Features">'.__('BPS Pro Features', 'bulletproof-security').'</a></strong><br><br>';	
		echo $bpsPro_text;

		echo $bps_modal_content4; 
	?>
	
    </p>
</div>

<?php 

// FSP Form processing
if ( isset( $_POST['Submit-FSP-Options'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bps_fsp_settings' );
	
	$bps_fsp_on_off 		= sanitize_text_field($_POST['bps_fsp_on_off']);	
	$bps_fsp_char_length 	= sanitize_text_field($_POST['bps_fsp_char_length']);
	$bps_fsp_lower_case 	= ! empty($_POST['bps_fsp_lower_case']) ? '1' : '';
	$bps_fsp_upper_case 	= ! empty($_POST['bps_fsp_upper_case']) ? '1' : '';
	$bps_fsp_number 		= ! empty($_POST['bps_fsp_number']) ? '1' : '';
	$bps_fsp_special_char 	= ! empty($_POST['bps_fsp_special_char']) ? '1' : '';
	$bps_fsp_message 		= sanitize_textarea_field($_POST['bps_fsp_message']);

	$FSP_Options = array(
	'bps_fsp_on_off' 		=> $bps_fsp_on_off, 
	'bps_fsp_char_length' 	=> $bps_fsp_char_length, 
	'bps_fsp_lower_case' 	=> $bps_fsp_lower_case, 
	'bps_fsp_upper_case' 	=> $bps_fsp_upper_case,
	'bps_fsp_number' 		=> $bps_fsp_number,
	'bps_fsp_special_char'	=> $bps_fsp_special_char,
	'bps_fsp_message' 		=> $bps_fsp_message 
	);	
	
	foreach( $FSP_Options as $key => $value ) {
		update_option('bulletproof_security_options_fsp', $FSP_Options);
	}
	
	if ( $_POST['bps_fsp_on_off'] == 'On' ) {
		echo $bps_topDiv;
		echo '<strong><font color="green">'.__('Settings Saved. FSP is turned On.', 'bulletproof-security').'</font></strong><br>';
		echo $bps_bottomDiv;
	}
	
	if ( $_POST['bps_fsp_on_off'] == 'Off' ) {
		echo $bps_topDiv;
		echo '<strong><font color="green">'.__('Settings Saved. FSP is turned Off.', 'bulletproof-security').'</font></strong><br>';
		echo $bps_bottomDiv;
	}
}

$scrolltoFSPMessage = isset($_REQUEST['scrolltoFSPMessage']) ? (int) $_REQUEST['scrolltoFSPMessage'] : 0; 

?>

<form name="BPS-FSP" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-4' ); ?>" method="post">
	
<?php 
	wp_nonce_field('bps_fsp_settings'); 
	$BPS_FSP_options = get_option('bulletproof_security_options_fsp');
	$bps_fsp_on_off = isset($BPS_FSP_options['bps_fsp_on_off']) ? $BPS_FSP_options['bps_fsp_on_off'] : '';
	$bps_fsp_char_length = ! empty($BPS_FSP_options['bps_fsp_char_length']) ? $BPS_FSP_options['bps_fsp_char_length'] : '12';	
	$FSP_Message = ! empty($BPS_FSP_options['bps_fsp_message']) ? $BPS_FSP_options['bps_fsp_message'] : 'Password must contain 1 lowercase letter, 1 uppercase letter, 1 number, 1 special character and be a minimum of 12 characters long.';	

	$bps_fsp_lower_case = ! empty($BPS_FSP_options['bps_fsp_lower_case']) ? checked( $BPS_FSP_options['bps_fsp_lower_case'], 1, false ) : '';	
	$bps_fsp_upper_case = ! empty($BPS_FSP_options['bps_fsp_upper_case']) ? checked( $BPS_FSP_options['bps_fsp_upper_case'], 1, false ) : '';	
	$bps_fsp_number = ! empty($BPS_FSP_options['bps_fsp_number']) ? checked( $BPS_FSP_options['bps_fsp_number'], 1, false ) : '';	
	$bps_fsp_special_char = ! empty($BPS_FSP_options['bps_fsp_special_char']) ? checked( $BPS_FSP_options['bps_fsp_special_char'], 1, false ) : '';	
?>

<table border="0">
  <tr>
    <td><label for="LSLog"><?php _e('Turn FSP On|Turn FSP Off:', 'bulletproof-security'); ?></label></td>
    <td><select name="bps_fsp_on_off" class="regular-text-150" style="width:120px;">
		<option value="Off" <?php selected('Off', $bps_fsp_on_off); ?>><?php _e('FSP Off', 'bulletproof-security'); ?></option>
		<option value="On" <?php selected('On', $bps_fsp_on_off); ?>><?php _e('FSP On', 'bulletproof-security'); ?></option>
		</select>
	</td>
  </tr>
  <tr>
    <td><label for="LSLog"><?php _e('Password Character Length:', 'bulletproof-security'); ?></label></td>
    <td><input type="text" name="bps_fsp_char_length" class="regular-text-150" style="width:120px;" value="<?php echo esc_html($bps_fsp_char_length); ?>" /></td>
    <td><label for="LSLog" style="margin:0px 0px 0px 5px;font-style:italic;font-weight:normal;"><?php _e('Example: 12', 'bulletproof-security'); ?></label></td>
  </tr>
</table>

<br />

   <label><strong><?php _e('Password Criteria Requirements: ', 'bulletproof-security'); ?></strong></label><br />
   <label><i><?php _e('Check to require. Uncheck to remove requirement.', 'bulletproof-security'); ?></i></label><br />
    <input type="checkbox" name="bps_fsp_lower_case" value="1" <?php echo esc_html($bps_fsp_lower_case); ?> /><label><?php _e(' At least 1 lowercase letter', 'bulletproof-security'); ?></label><br />
     <input type="checkbox" name="bps_fsp_upper_case" value="1" <?php echo esc_html($bps_fsp_upper_case); ?> /><label><?php _e(' At least 1 uppercase letter', 'bulletproof-security'); ?></label><br />
	<input type="checkbox" name="bps_fsp_number" value="1" <?php echo esc_html($bps_fsp_number); ?> /><label><?php _e(' At least 1 number', 'bulletproof-security'); ?></label><br />    
	<input type="checkbox" name="bps_fsp_special_char" value="1" <?php echo esc_html($bps_fsp_special_char); ?> /><label><?php _e(' At least 1 special character', 'bulletproof-security'); ?></label><br />

<br />

<table border="0">
  <tr>
    <td>
    <label for="LSLog"><?php _e('Displayed Message/Error Message:', 'bulletproof-security'); ?></label><br />
 	<textarea class="PFW-Allow-From-Text-Area" name="bps_fsp_message" tabindex="1"><?php echo esc_textarea($FSP_Message); ?></textarea>
	<input type="hidden" name="scrolltoFSPMessage" id="scrolltoFSPMessage" value="<?php echo esc_html( $scrolltoFSPMessage ); ?>" />
    </td>
  </tr>
</table>
	<input type="submit" name="Submit-FSP-Options" class="button bps-button" style="margin:15px 0px 15px 0px;" value="<?php esc_attr_e('Save Options', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Click OK to Proceed or click Cancel.', 'bulletproof-security'); echo $text; ?>')"/>
</form>

</td>
  </tr>
</table>

</div>

<div id="bps-tabs-5" class="bps-tab-page">

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