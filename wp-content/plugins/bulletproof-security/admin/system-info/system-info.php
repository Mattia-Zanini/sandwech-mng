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
	
	if ( esc_html($_SERVER['REQUEST_METHOD']) == 'POST' ) {

		bpsPro_Browser_UA_scroll_animation();
	}
}
?>

<h2 class="bps-tab-title"><?php _e('System Info', 'bulletproof-security'); ?></h2>
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
// Replace ABSPATH = wp-content/uploads
$wp_upload_dir = wp_upload_dir();
$bps_uploads_dir = str_replace( ABSPATH, '', $wp_upload_dir['basedir'] );
$bps_topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
$bps_bottomDiv = '</p></div>';

?>
</div>

<!-- jQuery UI Tab Menu -->
<div id="bps-tabs" class="bps-menu">
    <div id="bpsHead"><img src="<?php echo plugins_url('/bulletproof-security/admin/images/bps-plugin-logo.jpg'); ?>" /></div>
		<ul>
			<li><a href="#bps-tabs-1"><?php _e('System Info', 'bulletproof-security'); ?></a></li>
            <li><a href="#bps-tabs-2"><?php _e('Website Headers Check Tool', 'bulletproof-security'); ?></a></li>			
            <li><a href="#bps-tabs-3"><?php _e('Help &amp; FAQ', 'bulletproof-security'); ?></a></li>
		</ul>
            
<div id="bps-tabs-1">
<h2><?php _e('System Information', 'bulletproof-security'); ?></h2>

<div id="SysInfoBorder">

<h3><?php _e('File|Folder Permissions & UID', 'bulletproof-security'); ?>  <button id="bps-open-modal1" class="button bps-modal-button">
<img src="<?php echo plugins_url('/bulletproof-security/admin/images/question-mark-large.jpg'); ?>" style="margin:0px 0px 0px -10px" /></button></h3>

<div id="bps-modal-content1" class="bps-dialog-hide" title="<?php _e('File|Folder Permissions & UID', 'bulletproof-security'); ?>">
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
	<strong><a href="https://forum.ait-pro.com/forums/topic/dso-setup-steps/" title="DSO Server Setup Steps" target="_blank"><?php _e('DSO Server Setup Steps', 'bulletproof-security'); ?></a></strong><br /><br />		
	
	<?php $text = '<strong>'.__('File|Folder Diagnostic & Troubleshooting Info','bulletproof-security').'</strong><br>'.__('The file/folder permissions and UID checks are mainly for diagnostic troubleshooting so that you can check permissions or the UID of mission critical WP & BPS folders and files at a glance. There is some security benefit to changing file and folder permissions to more secure permissions, but this is not an essential or critical thing to do these days.', 'bulletproof-security').'<br><br><strong>'.__('Script Owner User ID (UID)|File Owner User ID','bulletproof-security').'</strong><br>'.__('Your Script Owner User ID (UID) and File Owner User ID should match. If they do not match for any folders then you will need to change the Owner of that folder so that both match. If you have a DSO server type see the DSO Server Setup Steps Forum Help Link at the top of this Question Mark help window.', 'bulletproof-security').'<br><br><strong>'.__('CGI And DSO File And Folder Permission Recommendations','bulletproof-security').'</strong><br>'.__('If your Server API (SAPI) is CGI you will see a table displayed with recommendations for file and folder permissions for CGI. If your SAPI is DSO/Apache/mod_php you will see a table listing file and folder permission recommendations for DSO.', 'bulletproof-security').'<br><br>'.__('If your Host is using CGI, but they do not allow you to set your folder permissions more restrictive to 705 and file permissions more restrictive to 604 then most likely when you change your folder and file permissions they will automatically be changed back to 755 and 644 by your Host or you may see a 403 or 500 error and will need to change the folder permissions back to what they were before. CGI 705 folder permissions have been thoroughly tested with WordPress and no problems have been discovered with WP or with WP Plugins on several different Web Hosts, but all web hosts have different things that they specifically allow or do not allow.', 'bulletproof-security').'<br><br>'.__('Most Hosts now use 705 Root folder permissions. Your Host might not be doing this or allow this, but typically 755 is fine for your Root folder. CGI 604 file permissions have been thoroughly tested with WordPress and no problems have been discovered with WP or with WP Plugins.','bulletproof-security').'<br><br><strong>'.__('The /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup/ folder permission recommendation is 755 for CGI or DSO for compatibility reasons. The /bps-backup folder has a deny all htaccess file in it so that it cannot be accessed by anyone other than you so the folder permissions for this folder are irrelevant.','bulletproof-security').'</strong><br><br>'.__('Your current file and folder permissions are shown below with suggested/recommended file and folder permissions. ','bulletproof-security').'<strong>'.__('Not all web hosts will allow you to set your folder permissions to these Recommended folder permissions.', 'bulletproof-security').'</strong> '.__('If you see 500 errors after changing your folder permissions than change them back to what they were before.','bulletproof-security').'<br><br><strong>'.__('BPS Pro Video Tutorial links can be found in the Help & FAQ pages.','bulletproof-security').'</strong>'; echo $text; ?></p>
</div>
</div>

<div id="System-Info-Table">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-system_info_table">
  <tr>
    <td width="49%" class="bps-table_title"><?php _e('Website|Server Info|Apache Modules|Directives', 'bulletproof-security'); ?></td>
    <td width="2%">&nbsp;</td>
    <td width="49%" class="bps-table_title"><?php _e('Database Info|WordPress Site Info|Misc Checks', 'bulletproof-security'); ?></td>
  </tr>
  <tr>
    <td class="bps-table_cell">
<?php 

	$time_start = microtime( true );
	
	$bpsTargetNS = '';
	$bpsTarget = '';

	 $sapi_type = php_sapi_name();
	
	// XAMPP, MAMP, WAMP, LAMP, etc.
	if ( substr($sapi_type, 0, 6) != 'apache' && ! preg_match( '#\\\\#', ABSPATH, $matches ) ) {
		
		// Get DNS Name Server from [target] Root Domain
		// Note: This code runs fastest in this format vs nesting conditions
		if ( isset( $_SERVER['SERVER_NAME'] ) ) {
			$bpsHostName = esc_html($_SERVER['SERVER_NAME']);	
		} elseif ( isset( $_SERVER['HTTP_HOST'] ) ) {
			$bpsHostName = esc_html($_SERVER['HTTP_HOST']);	
		}
	
		$label_1 = preg_match( '/(([a-zA-Z0-9-])+\.){1}([a-zA-Z0-9-])+$/', $bpsHostName, $matches_1 );
		$label_2 = preg_match( '/(([a-zA-Z0-9-])+\.){2}([a-zA-Z0-9-])+$/', $bpsHostName, $matches_2 );
		$label_3 = preg_match( '/(([a-zA-Z0-9-])+\.){3}([a-zA-Z0-9-])+$/', $bpsHostName, $matches_3 );
		
		if ( ! empty($matches_1) ) {
			$domain_labels = array( $matches_1[0] );
		}
		if ( ! empty($matches_2) ) {
			$domain_labels = array( $matches_2[0] );
		}
		if ( ! empty($matches_3) ) {
			$domain_labels = array( $matches_3[0] );
		}
	
		//$domain_labels = array( $matches_1[0], $matches_2[0], $matches_3[0] );
		$labels = array_filter( $domain_labels, 'strlen' );
		
		foreach ( $labels as $domain ) {
	
			if ( filter_var( gethostbyname($domain), FILTER_VALIDATE_IP ) ) {
	
				$bpsGetDNS = dns_get_record( $domain, DNS_NS );
		
				if ( empty( $bpsGetDNS[0]['target'] ) ) {
				
				} else {
					
					$bpsTargetNS = $bpsGetDNS[0]['target'];
				}
		
				if ( empty( $bpsTargetNS ) ) {
					
					dns_get_record( $domain, DNS_ALL, $authns, $addtl );
			
					if ( empty( $authns[0]['target'] ) ) {
	
					} else {
						
						$bpsTarget = $authns[0]['target'];
					}
				}	
		
				if ( empty( $bpsTarget ) && empty( $bpsTargetNS ) ) {
					
					dns_get_record( $domain, DNS_ANY, $authns, $addtl );
			
					if ( empty( $authns[0]['target'] ) ) {
	
					} else {
						
						$bpsTarget = $authns[0]['target'];
					}
				}
			}
		}
	}

// Get Server IP address
function bps_get_server_ip_address_sysinfo() {

	if ( is_admin() && current_user_can('manage_options') ) {
	
		if ( isset( $_SERVER['SERVER_ADDR'] ) ) {
			$ip = esc_html($_SERVER['SERVER_ADDR']);
			echo '<strong><span class="sysinfo-label-text">'.__('Server|Website IP Address: ', 'bulletproof-security').'</span></strong>'.$ip.'<br>';
		} elseif ( isset( $_SERVER['HTTP_HOST'] ) ) {
			$ip = esc_html( gethostbyname( $_SERVER['HTTP_HOST'] ) );
			echo '<strong><span class="sysinfo-label-text">'.__('Server|Website IP Address: ', 'bulletproof-security').'</span></strong>'.$ip.'<br>';		
		} else { 
			$sapi_type = php_sapi_name();			
			if ( substr($sapi_type, 0, 6) != 'apache' && ! preg_match( '#\\\\#', ABSPATH, $matches ) ) {
				$ip = dns_get_record( bpsGetDomainRoot(), DNS_ALL );
				echo '<strong><span class="sysinfo-label-text">'.__('Server|Website IP Address: ', 'bulletproof-security').'</span></strong>'.$ip[0]['ip'].'<br>';
			}
		}
	}
}

// Get Real IP address - USE EXTREME CAUTION!!!
function bps_get_proxy_real_ip_address() {
	
	if ( is_admin() && current_user_can('manage_options') ) {
		
		if ( isset($_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = esc_html($_SERVER['HTTP_CLIENT_IP']);
			echo '<strong><span class="sysinfo-label-text">'.__('HTTP_CLIENT_IP IP Address: ', 'bulletproof-security').'</span></strong>'.$ip.'<br>';
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = esc_html($_SERVER['HTTP_X_FORWARDED_FOR']);
			echo '<strong><span class="sysinfo-label-text">'.__('Proxy X-Forwarded-For IP Address: ', 'bulletproof-security').'</span></strong>'.$ip.'<br>';
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = esc_html($_SERVER['REMOTE_ADDR']);
			echo '<strong><span class="sysinfo-label-text">'.__('Public Internet IP Address (ISP): ', 'bulletproof-security').'</span></strong>'.$ip.'<br>';
		}
	}
}

// Get Plugins List
function bpsPro_get_plugins_list() {

	$all_plugins = get_plugins();

	foreach ( $all_plugins as $key => $value ) {
			
		if ( ! empty($key) ) {
		
			$active_plugins = in_array( $key, apply_filters('active_plugins', get_option('active_plugins')));

			if ( 1 == $active_plugins || is_plugin_active_for_network( $key ) ) {
			
				echo '<strong>';
				print_r($value['Name']);
				echo ' ';
				print_r($value['Version']);
				echo ' - <font color="green">'.__('Activated', 'bulletproof-security').'</font>';
				echo ':</strong> '. $key .'<br>';
			
			} else {
				
				echo '<strong>';
				print_r($value['Name']);
				echo ' ';
				print_r($value['Version']);
				echo ' - <font color="blue">'.__('Deactivated', 'bulletproof-security').'</font>';
				echo ':</strong> '. $key .'<br>';				
			}
		}
	}

	$all_mu_plugins = get_mu_plugins();

	echo '<br><strong><font color="black">'.__('Must-Use Plugins', 'bulletproof-security').':</font></strong><br>';

	foreach ( $all_mu_plugins as $key => $value ) {
			
		if ( ! empty($key) ) {
			
			echo '<strong>';
			print_r($value['Name']);
			echo ' ';
			print_r($value['Version']);
			echo ' - <font color="green">'.__('Installed', 'bulletproof-security').'</font>';
			echo ':</strong> '. $key .'<br>';
		}
	}

	$active_theme = wp_get_theme();
	$all_themes = wp_get_themes();
	$all_themes_array = array();

	echo '<br><strong><font color="black">'.__('Themes', 'bulletproof-security').':</font></strong><br>';

	foreach ( $all_themes as $key => $value ) {
			
		if ( $active_theme->get( 'Name' ) == $value['Name'] && $active_theme->get( 'Version' ) == $value['Version'] ) {
			$active_theme_value = ': <font color="green">'.__('Activated', 'bulletproof-security').'</font>: ';
		} else {
			$active_theme_value = ': <font color="blue">'.__('Deactivated', 'bulletproof-security').'</font>: ';
		}
		
		if ( ! empty($key) ) {
			$all_themes_array[] = '<strong>' . $value['Name'] . ' ' . $value['Version'] . $active_theme_value . '</strong>' . __('Theme Folder Name', 'bulletproof-security').': '. $key;
		}
	}
	
	foreach ( $all_themes_array as $key => $value ) {
		echo $value . '<br>';
	}
}

// Get Total # Plugins Installed
function bpsPro_count_installed_plugins($count) {

	$all_plugins = get_plugins();
	$count = 0;

	foreach ( $all_plugins as $key => $value ) {
			
		if ( ! empty($key) ) {
			$count++;
		}
	}
	return $count;
}

// Get Total # of must-use Plugins Installed
function bpsPro_count_mu_plugins($count) {

	$all_mu_plugins = get_mu_plugins();
	$count = 0;

	foreach ( $all_mu_plugins as $key => $value ) {
			
		if ( ! empty($key) ) {
			$count++;
		}
	}
	return $count;
}

// Get Total # Plugins Activated
function bpsPro_count_activated_plugins($count) {

	$activated_plugins = get_option('active_plugins');
	$count = 0;

	foreach ( $activated_plugins as $key => $value ) {
			
		if ( ! empty($value) ) {
			$count++;
		}
	}
	return $count;
}

// Get Total # Plugins Network Activated
function bpsPro_count_network_activated_plugins($count) {

	if ( is_multisite() ) {

		$activated_network_plugins = get_site_option( 'active_sitewide_plugins');
		$count = 0;

		foreach ( $activated_network_plugins as $key => $value ) {
			
			if ( ! empty($value) ) {
				$count++;
			}
		}
	return $count;
	}
}

	echo '<span class="system-info-text">';

	echo '<strong><span class="sysinfo-label-text">'.__('Website Root URL', 'bulletproof-security').':</span></strong> ' . get_site_url() . '<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('Document Root Path', 'bulletproof-security').':</span></strong> ' . esc_html( $_SERVER['DOCUMENT_ROOT'] ) . '<br>'; 
	echo '<strong><span class="sysinfo-label-text">'.__('WP ABSPATH', 'bulletproof-security').':</span></strong> ' . ABSPATH . '</strong><br>';
	echo '<strong><span class="sysinfo-label-text">'.__('get_home_path()', 'bulletproof-security').':</span></strong> ' . get_home_path() . '</strong><br>';
	echo '<strong><span class="sysinfo-label-text">'.__('Parent Directory', 'bulletproof-security').':</span></strong> ' . dirname( ABSPATH ) . '<br>';  
	bps_get_server_ip_address_sysinfo(); 
	echo '<strong><span class="sysinfo-label-text">'.__('Server Port', 'bulletproof-security').':</span></strong> ' . esc_html( $_SERVER['SERVER_PORT'] ) . '<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('Server Protocol', 'bulletproof-security').':</span></strong> ' . esc_html( $_SERVER['SERVER_PROTOCOL'] ) . '<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('Host by Address', 'bulletproof-security').':</span></strong> ' . esc_html( gethostbyaddr( $_SERVER['SERVER_ADDR'] ) ) . '<br>';    
	echo '<strong><span class="sysinfo-label-text">'.__('DNS Name Server', 'bulletproof-security').':</span></strong> '; 
	
	if ( empty( $bpsTarget ) && empty( $bpsTargetNS ) ) {
		echo __('DNS Name Server Not Available', 'bulletproof-security');
	
	} else { 
	
		if ( ! empty( $bpsTarget ) ) {
			echo $bpsTarget; 
		} else {
			echo $bpsTargetNS;
		}
	}
	echo '<br>';
	
	bps_get_proxy_real_ip_address();
	echo '<strong><span class="sysinfo-label-text">'.__('Server Type', 'bulletproof-security').':</span></strong> ' . esc_html( $_SERVER['SERVER_SOFTWARE'] ) . '<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('Operating System', 'bulletproof-security').':</span></strong> ' . PHP_OS . '</strong><br>';  
	echo '<strong><span class="sysinfo-label-text">'.__('WP Filesystem API Method', 'bulletproof-security').':</span></strong> ' . get_filesystem_method() . '<br>';	
	if ( get_filesystem_method() != 'direct' && function_exists('getmyuid') && function_exists('fileowner') ) {
	echo '<strong><span class="sysinfo-label-text">'.__('Script Owner ID', 'bulletproof-security').':</span></strong> ' . getmyuid() . '</strong><br>';
	echo '<strong><span class="sysinfo-label-text">'.__('File Owner ID', 'bulletproof-security').':</span></strong> ' . fileowner( WP_PLUGIN_DIR . '/bulletproof-security/admin/system-info/system-info.php' ).'<br>';
	}
	if ( get_filesystem_method() != 'direct' && function_exists('get_current_user') ) {
	echo '<strong><span class="sysinfo-label-text">'.__('Script Owner Name', 'bulletproof-security').':</span></strong> ' . get_current_user() . '<br>';
	}
	echo '<strong><span class="sysinfo-label-text">'.__('Server API', 'bulletproof-security').':</span></strong> ';
	
		$sapi_type = php_sapi_name();
	if ( substr( $sapi_type, 0, 6) != 'apache' ) {		
		echo $sapi_type.__(' CGI Host Server Type', 'bulletproof-security');
	} else {
    	echo $sapi_type.__(' DSO Host Server Type', 'bulletproof-security');
	}
	echo '<br>';
	
	// This function is in /includes/functions.php
	bpsPro_apache_mod_directive_check();

	echo '<strong><span class="sysinfo-label-text">'.__('cURL', 'bulletproof-security').':</span></strong> ';
	if ( extension_loaded('curl') ) {
		_e('cURL Extension is Loaded Version: ', 'bulletproof-security');
		$curl_version = curl_version();	
		echo $curl_version['version'].'<br>';
		echo '<strong><span class="sysinfo-label-text">'.__('cURL OpenSSL Version (Used by PayPal, etc.)', 'bulletproof-security').':</span></strong> ';
		echo $curl_version['ssl_version'].'<br>';
	} else {
		_e('cURL Extension is Not Loaded', 'bulletproof-security').'<br>';
	}

	echo '<strong><span class="sysinfo-label-text">'.__('OpenSSL Library', 'bulletproof-security').':</span></strong> ';
	if ( defined('OPENSSL_VERSION_TEXT') ) {
	echo OPENSSL_VERSION_TEXT . '<br>';
	}

	echo '<strong><span class="sysinfo-label-text">'.__('Zend Engine Version', 'bulletproof-security').':</span></strong> ' . zend_version() . '</strong><br>'; 
	echo '<strong><span class="sysinfo-label-text">'.__('Zend Guard|Optimizer', 'bulletproof-security').':</span></strong> ';
	if ( extension_loaded('Zend Optimizer+') && ini_get('zend_optimizerplus.enable') == 1 || ini_get('zend_optimizerplus.enable') == 'On' ) {
		_e('Zend Optimizer+ Extension is Loaded and Enabled', 'bulletproof-security');
	}
	if ( extension_loaded('Zend Optimizer') ) {
		_e('Zend Optimizer Extension is Loaded', 'bulletproof-security');
	}
	if ( extension_loaded('Zend Guard Loader') ) {
		_e('Zend Guard Loader Extension is Loaded', 'bulletproof-security');
	} else {
	if ( ! extension_loaded('Zend Optimizer+') && ! extension_loaded('Zend Optimizer') && ! extension_loaded('Zend Guard Loader') ) {
		_e('A Zend Extension is Not Loaded', 'bulletproof-security');		
	}
	}
	echo '<br>';    
	
	$opcache_restrict_api = trim(ini_get('opcache.restrict_api'));
	
	if ( $opcache_restrict_api == false || $opcache_restrict_api != false && $opcache_restrict_api == ''  ) {

		$disable_functions = explode( ',', ini_get('disable_functions') );
		
		if ( in_array( 'opcache_get_status', $disable_functions ) || in_array( 'opcache_get_configuration', $disable_functions ) ) {
			echo '<strong><span class="sysinfo-label-text">'.__('OPcache', 'bulletproof-security').':</span></strong> ';
			echo __('opcache_get_status and/or opcache_get_configuration functions are disabled.', 'bulletproof-security');			
			
		} else {

			if ( function_exists('opcache_get_status') ) {
				$opcache_get_status = opcache_get_status();
				echo '<strong><span class="sysinfo-label-text">'.__('OPcache', 'bulletproof-security').':</span></strong> '; 
				
				if ( $opcache_get_status['opcache_enabled'] == '1' ) {
					if ( function_exists('opcache_get_configuration') ) {
						$opcache_get_config = opcache_get_configuration();
						echo $opcache_get_config['version']['opcache_product_name'].' '.$opcache_get_config['version']['version'].' '.__('is Enabled', 'bulletproof-security');
					}
				} else {
					echo __('OPcache is Not Enabled', 'bulletproof-security');
				}
				
			} else {
				echo '<strong><span class="sysinfo-label-text">'.__('OPcache', 'bulletproof-security').':</span></strong> ';
				echo __('OPcache is Not Enabled', 'bulletproof-security');
			}
		}
	
	} else {
		echo '<strong><span class="sysinfo-label-text">'.__('OPcache', 'bulletproof-security').':</span></strong> ';
		echo __('opcache.restrict_api directive is in use.', 'bulletproof-security');		
	}
	
	echo '<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('ionCube Loader', 'bulletproof-security').':</span></strong> '; 
	if ( extension_loaded('IonCube Loader') && function_exists('ioncube_loader_iversion') ) {
		echo __('ionCube Loader Extension is Loaded ', 'bulletproof-security').__('Version: ', 'bulletproof-security') . ioncube_loader_iversion();
	} else {
		echo __('ionCube Loader Extension is Not Loaded', 'bulletproof-security');
	}
	echo '<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('Suhosin', 'bulletproof-security').':</span></strong> ';
	
		$bpsconstants = get_defined_constants();
	if ( isset( $bpsconstants['SUHOSIN_PATCH'] ) && $bpsconstants['SUHOSIN_PATCH'] == 1 ) {
		_e('The Suhosin-Patch is installed', 'bulletproof-security');
	}
	if ( extension_loaded('suhosin') ) {
		_e('Suhosin-Extension is Loaded', 'bulletproof-security');	
	} else {
		_e('Suhosin is Not Installed|Loaded', 'bulletproof-security');			
	}
	echo '<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('APC', 'bulletproof-security').':</span></strong> ';
	if ( extension_loaded('apc') && ini_get('apc.enabled') == 1 || ini_get('apc.enabled') == 'On' ) {
		_e('APC Extension is Loaded and Enabled', 'bulletproof-security');
	} 
	elseif ( extension_loaded('apc') && ini_get('apc.enabled') == 0 || ini_get('apc.enabled') == 'Off' ) {
		_e('APC Extension is Loaded but Not Enabled', 'bulletproof-security');
	} else {
		_e('APC Extension is Not Loaded', 'bulletproof-security');	
	}
	echo '<br>';  	    
	echo '<strong><span class="sysinfo-label-text">'.__('eAccelerator', 'bulletproof-security').':</span></strong> ';
	if ( extension_loaded('eaccelerator') && ini_get('eaccelerator.enable') == 1 || ini_get('eaccelerator.enable') == 'On' ) {
		_e('eAccelerator Extension is Loaded and Enabled', 'bulletproof-security');
	} 
	elseif ( extension_loaded('eaccelerator') && ini_get('eaccelerator.enable') == 0 || ini_get('eaccelerator.enable') == 'Off' ) {
		_e('eAccelerator Extension is Loaded but Not Enabled', 'bulletproof-security');
	} else {
		_e('eAccelerator Extension is Not Loaded', 'bulletproof-security');	
	}	
	echo '<br>';  	  
	echo '<strong><span class="sysinfo-label-text">'.__('XCache', 'bulletproof-security').':</span></strong> ';
	if ( extension_loaded('xcache') && ini_get('xcache.size') > 0 && ini_get('xcache.cacher') == 'On' || ini_get('xcache.cacher') == '1' ) {
		_e('XCache Extension is Loaded and Enabled', 'bulletproof-security');
	} 
	elseif ( extension_loaded('xcache') && ini_get('xcache.size') <= 0 && ini_get('xcache.cacher') == 'Off' || ini_get('xcache.cacher') == '0' ) {
		_e('XCache Extension is Loaded but Not Enabled', 'bulletproof-security');
	} else {
		_e('XCache Extension is Not Loaded', 'bulletproof-security');	
	}	
	echo '<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('Varnish', 'bulletproof-security').':</span></strong> ';
	if ( extension_loaded('varnish') ) {
		_e('Varnish Extension is Loaded', 'bulletproof-security');
	} else {
		_e('Varnish Extension is Not Loaded', 'bulletproof-security');	
	}	
	echo '<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('Memcache', 'bulletproof-security').':</span></strong> ';
	if ( extension_loaded('memcache') ) {
		echo __('Memcache Extension is Loaded', 'bulletproof-security');
	} else {
		_e('Memcache Extension is Not Loaded', 'bulletproof-security');	
	}	
	echo '<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('Memcached', 'bulletproof-security').':</span></strong> ';
	if ( extension_loaded('memcached') ) {
	echo __('Memcached Extension is Loaded', 'bulletproof-security');
	} else {
		_e('Memcached Extension is Not Loaded', 'bulletproof-security');	
	}
	echo '<br>';

	echo '</span>';
	?>

    </td>
    <td>&nbsp;</td>
    <td rowspan="2" class="bps-table_cell">
	
	<?php 
	if ( is_multisite() && $blog_id != 1 ) {
		echo '<span class="sysinfo-label-text"><strong>'.__('DB Info is not displayed on Network/Multisite subsites', 'bulletproof-security').'</strong></span><br><br>';
	
	} else {
	
	echo '<span class="system-info-text">';
	
	function bps_mysqli_get_client_info() {
		if ( function_exists('mysqli_get_client_info') ) { 
			return mysqli_get_client_info(); 
		}
	}
	
	$sql_version = 'version';
	$sql_mode_var = 'sql_mode';
	$sqlversion = $wpdb->get_var( $wpdb->prepare( "SELECT VERSION() AS %s", $sql_version ) );
	$mysqlinfo = $wpdb->get_results( $wpdb->prepare( "SHOW VARIABLES LIKE %s", $sql_mode_var ) );
	
	if ( is_array( $mysqlinfo ) ) { 
		$sql_mode = $mysqlinfo[0]->Value;
		
		if ( empty( $sql_mode ) ) { 
			$sql_mode = __('Not Set', 'bulletproof-security');
		} else {
			$sql_mode = __('Off', 'bulletproof-security');
		}
	}
	
	$hostname_search = 'hostname';
	$port_search = 'port';
	$connect_timeout_search = 'connect_timeout';
	$storage_engine_search = 'storage_engine';
	$max_allowed_packet = 'max_allowed_packet';	
	
	$DB_Hostname = '';
	$DB_Port = '';
	$DB_Connect_timeout = '';
	$DB_Storage_engine = '';

	$result = $wpdb->get_results( $wpdb->prepare( "SHOW GLOBAL VARIABLES WHERE (Variable_name = %s) OR (Variable_name = %s) OR (Variable_name = %s) OR (Variable_name = %s) OR (Variable_name = %s)", $hostname_search, $port_search, $connect_timeout_search, $storage_engine_search, $max_allowed_packet ) );

	foreach ( $result as $data ) {

		if ( $data->Variable_name == 'hostname' ) {
			$DB_Hostname = $data->Value;
		}		
		if ( $data->Variable_name == 'port' ) {
			$DB_Port = $data->Value;
		}
		if ( $data->Variable_name == 'connect_timeout' ) {
			$DB_Connect_timeout = $data->Value;
		}		
		if ( $data->Variable_name == 'storage_engine' ) {
			$DB_Storage_engine = $data->Value;
		}
		if ( $data->Variable_name == 'max_allowed_packet' ) {
			$DB_Max_Allowed_Packet = $data->Value;
		}
	}
	
	if ( ! empty($DB_Hostname) ) {
		$DB_Hostname_ip = gethostbyname($DB_Hostname);
	} else {
		$DB_Hostname_ip = '';
	}

	$text = '<strong><span class="sysinfo-label-text">'.__('DB Version: ', 'bulletproof-security').'</span></strong>'.$sqlversion.
	'<br><strong><span class="sysinfo-label-text">'.__('DB Client Version: ', 'bulletproof-security').'</span></strong>'.bps_mysqli_get_client_info().
	'<br><strong><span class="sysinfo-label-text">'.__('DB Hostname (Local): ', 'bulletproof-security').'</span></strong>'.DB_HOST.
	'<br><strong><span class="sysinfo-label-text">'.__('DB Hostname (Server): ', 'bulletproof-security').'</span></strong>'.$DB_Hostname.
	'<br><strong><span class="sysinfo-label-text">'.__('DB Hostname IP (Server): ', 'bulletproof-security').'</span></strong>'.$DB_Hostname_ip.
	'<br><strong><span class="sysinfo-label-text">'.__('DB Storage Engine: ', 'bulletproof-security').'</span></strong>'.$DB_Storage_engine.
	'<br><strong><span class="sysinfo-label-text">'.__('DB Connect Timeout: ', 'bulletproof-security').'</span></strong>'.$DB_Connect_timeout.__(' seconds', 'bulletproof-security').
	'<br><strong><span class="sysinfo-label-text">'.__('DB max_allowed_packet: ', 'bulletproof-security').'</span></strong>'.$DB_Max_Allowed_Packet.
	'<br><strong><span class="sysinfo-label-text">'.__('DB Port: ', 'bulletproof-security').'</span></strong>'.$DB_Port.
	'<br><strong><span class="sysinfo-label-text">'.__('DB Name: ', 'bulletproof-security').'</span></strong>'.DB_NAME.
	'<br><strong><span class="sysinfo-label-text">'.__('DB Table Prefix: ', 'bulletproof-security').'</span></strong>'.$wpdb->base_prefix.
	'<br><strong><span class="sysinfo-label-text">'.__('DB Charset: ', 'bulletproof-security').'</span></strong>'.$wpdb->charset.
	'<br><strong><span class="sysinfo-label-text">'.__('DB Collation: ', 'bulletproof-security').'</span></strong>'.$wpdb->collate.
	'<br><strong><span class="sysinfo-label-text">'.__('SQL Mode: ', 'bulletproof-security').'</span></strong>'.$sql_mode.'<br>';
	
	echo $text;
	echo bps_wpdb_errors_off();	
	
	if ( function_exists('mysql_connect') ) {
		$text = '<strong><span class="sysinfo-label-text">'.__('MySQL Extension: ', 'bulletproof-security').'</span></strong>'.__('Installed|Enabled', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<strong><span class="sysinfo-label-text">'.__('MySQL Extension: ', 'bulletproof-security').'</span></strong>'.__('Not Installed|Enabled', 'bulletproof-security').'<br>';		
		echo $text;
	}
	
	if ( function_exists('mysqli_connect') ) {
		$text = '<strong><span class="sysinfo-label-text">'.__('MySQLi Extension: ', 'bulletproof-security').'</span></strong>'.__('Installed|Enabled', 'bulletproof-security').'<br>';
		echo $text;
	} else {
		$text = '<strong><span class="sysinfo-label-text">'.__('MySQLi Extension: ', 'bulletproof-security').'</span></strong>'.__('Not Installed|Enabled', 'bulletproof-security').'<br>';		
		echo $text;
	}	
	
	echo '<br>';
	}
	
	echo '<strong><span class="sysinfo-label-text">'.__('WordPress Installation Folder', 'bulletproof-security').':</span></strong> ';
	echo bps_wp_get_root_folder().'<br>';

	echo '<strong><span class="sysinfo-label-text">'.__('WordPress Installation Type', 'bulletproof-security').':</span></strong> ';
	echo bps_wp_get_root_folder_display_type().'<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('Standard|GWIOD Site Type', 'bulletproof-security').':</span></strong> ';
	echo bps_gwiod_site_type_check().'<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('Network|Multisite', 'bulletproof-security').':</span></strong> ';
	echo bps_multisite_check().'<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('BuddyPress', 'bulletproof-security').':</span></strong> ';
	echo bps_buddypress_site_type_check().'<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('bbPress', 'bulletproof-security').':</span></strong> ';
	echo bps_bbpress_site_type_check().'<br>';	
	echo '<strong><span class="sysinfo-label-text">'.__('Plugins Folder', 'bulletproof-security').':</span></strong> ';
	echo str_replace( ABSPATH, '', WP_PLUGIN_DIR ).'<br>';	
	 $wp_upload_dir = wp_upload_dir();
	if ( is_dir( $wp_upload_dir['basedir'] ) ) {
	echo '<strong><span class="sysinfo-label-text">'.__('Uploads Folder', 'bulletproof-security').':</span></strong> ';
	echo str_replace( ABSPATH, '', $wp_upload_dir['basedir'] );	
	}

	// check UPLOADS CONSTANT = wp-content/blogs.dir/1/files = default / not really defined
	if ( is_multisite() && defined( 'UPLOADS' ) ) {
	echo '<br><strong><span class="sysinfo-label-text">'.__('UPLOADS Constant', 'bulletproof-security').':</span></strong> ';
	echo str_replace( trailingslashit( WP_CONTENT_FOLDERNAME ), '', untrailingslashit( UPLOADS ) ).'</strong>';
	}

	echo '<br><strong><span class="sysinfo-label-text">'.__('WP Permalink Structure', 'bulletproof-security').':</span></strong> ';
	 $permalink_structure = get_option('permalink_structure'); 
	echo $permalink_structure.'<br>';
	
	if ( defined('DISABLE_WP_CRON') && DISABLE_WP_CRON === true ) {
		echo '<strong><span class="sysinfo-label-text">'.__('DISABLE_WP_CRON constant', 'bulletproof-security').':</span></strong> '.__('Standard WP Crons are disabled on your website.', 'bulletproof-security').'<br>';
	} else {
		echo '<strong><span class="sysinfo-label-text">'.__('DISABLE_WP_CRON constant', 'bulletproof-security').':</span></strong> '.__('Standard WP Crons are not disabled on your website.', 'bulletproof-security').'<br>';
	}

 	echo '<strong><span class="sysinfo-label-text">'.__('Total Plugins Installed', 'bulletproof-security').':</span></strong> ';
	echo bpsPro_count_installed_plugins($count).'<br>';
 	echo '<strong><span class="sysinfo-label-text">'.__('Total Must-Use Plugins Installed', 'bulletproof-security').':</span></strong> ';	
	echo bpsPro_count_mu_plugins($count).'<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('Total Plugins Activated', 'bulletproof-security').':</span></strong> ';
	echo bpsPro_count_activated_plugins($count).'<br>';
	if ( is_multisite() ) {
	echo '<strong><span class="sysinfo-label-text">'.__('Total Plugins Network Activated', 'bulletproof-security').':</span></strong> ';
	echo bpsPro_count_network_activated_plugins($count).'<br>';
	}

?>

<h3><button id="bps-open-modal600" class="button bps-encrypt-button"><?php _e('Get Plugins|Themes List', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content600" class="bps-dialog-hide" title="<?php _e('Get Plugins|Themes List', 'bulletproof-security'); ?>">
	<div id="dialog-anchor" style="position:relative;top:-30px;left:0px"><a href="#"></a></div>
	<p><?php $text = '<strong>'.__('This window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>'; 
	echo $text; bpsPro_get_plugins_list(); ?></p>
</div>	

<?php
	echo '<strong><span class="sysinfo-label-text">'.__('Browser Compression Supported', 'bulletproof-security').':</span></strong> '.esc_html($_SERVER['HTTP_ACCEPT_ENCODING']);
	
	echo '<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('GD Library', 'bulletproof-security').':</span></strong> ';
	if ( extension_loaded('gd') ) {
	if ( function_exists('gd_info') ) {
		$ver_info = gd_info();
		$gd_ver = $ver_info['GD Version'];
	echo __('GD Extension is Loaded - ', 'bulletproof-security').__('Version: ', 'bulletproof-security') . $gd_ver;
	}
	} else {
		_e('GD Extension is Not Loaded', 'bulletproof-security');
	}	
	echo '<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('ImageMagick', 'bulletproof-security').':</span></strong> ';
	if ( extension_loaded('imagick') ) {
    if ( class_exists('Imagick') ) {
		$imagick = new Imagick();
		$imagick_version = $imagick->getVersion();
		$imagick_version_string = $imagick_version['versionString'];
	echo __('ImageMagick Extension is Loaded - ', 'bulletproof-security').__('Version: ', 'bulletproof-security') . str_replace( array( 'ImageMagick', 'http://www.imagemagick.org' ), "", $imagick_version_string );			
	}
	} else {
		_e('ImageMagick Extension is Not Loaded', 'bulletproof-security');
	}	
	echo '<br><br>';

	echo '</span>';
	?>
     
      </td>
  </tr>
  <tr>
    <td class="bps-table_cell">&nbsp;</td>
    <td>&nbsp;</td>
    <!-- <td class="bps-table_cell">&nbsp;</td> -->
    </tr>
  <tr>
    <td class="bps-table_title"><?php _e('PHP Server|PHP.ini Info', 'bulletproof-security'); ?></td>
    <td>&nbsp;</td>
    <td class="bps-table_title"><?php _e('File|Folder Permissions (CGI or DSO)|Script Owner User ID (UID)|File Owner User ID', 'bulletproof-security'); ?></td>
  </tr>
  <tr>
    <td class="bps-table_cell">
	
<?php 
	echo '<span class="system-info-text">';

	echo '<strong><span class="sysinfo-label-text">'.__('PHP Version', 'bulletproof-security').':</span></strong> ' . PHP_VERSION . '<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('PHP Memory Usage', 'bulletproof-security').':</span></strong> ' . round( memory_get_usage(false) / 1024 / 1024, 2 ) . __(' MB') . '<br>'; 
	echo '<strong><span class="sysinfo-label-text">'.__('WordPress Admin Memory Limit', 'bulletproof-security').':</span></strong> '; 
		$memory_limit = ini_get('memory_limit');
	echo $memory_limit.'<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('WordPress Base Memory Limit', 'bulletproof-security').':</span></strong> ' . WP_MEMORY_LIMIT . '<br>';
	
	// Parse phpinfo() and return the PHP memory_limit Local Value
	bpsPro_phpinfo_memory_limit_array();

	$bps_php_memory_limit = get_option('bulletproof_security_options_php_memory_limit');

	$memoryLimitM = $bps_php_memory_limit['bps_php_memory_limit'];
	$memoryLimit = str_replace( 'M', '', $memoryLimitM );
	$pos_memory_limit = strpos( $memoryLimitM, 'Unable to get the PHP memory_limit value from the server' );

	if ( $pos_memory_limit !== false ) {
		echo '<strong><span class="sysinfo-label-text">'.__('PHP Actual Configuration Memory Limit: ', 'bulletproof-security').'</span><font color="black">'.__('The Memory Limit value is not available from your Server.', 'bulletproof-security').'</font></strong><br>';

	} else {

		switch ( $memoryLimit ) {
			case $memoryLimit >= '128':
				echo '<strong><span class="sysinfo-label-text">'.__('PHP Actual Configuration Memory Limit: ', 'bulletproof-security').'</span><font color="green">'
				.$memoryLimitM.'</font></strong><br>';		
			break;
			case $memoryLimit >= '65' && $memoryLimit < '128':
				echo '<strong><span class="sysinfo-label-text">'.__('PHP Actual Configuration Memory Limit: ', 'bulletproof-security').'</span><font color="black">'
				.$memoryLimitM.__(' Recommendation: Increase Memory Limit to at least 128M, 256M is even better.', 'bulletproof-security').'</font></strong><br>';
			break;
			case $memoryLimit > '0' && $memoryLimit <= '64':
				echo '<strong><span class="sysinfo-label-text">'.__('PHP Actual Configuration Memory Limit: ', 'bulletproof-security').'</span><font color="#fb0101">'
				.$memoryLimitM.__(' Recommendation: Increase Memory Limit to at least 128M, 256M is even better.', 'bulletproof-security').'</font></strong><br>';	
			break;
 		}
	}
	
	if ( function_exists('get_cfg_var') ) {
		$php_config_file = get_cfg_var('cfg_file_path');
		
		if ( $php_config_file != '' ) {
			echo '<strong><span class="sysinfo-label-text">'.__('PHP Configuration File (php.ini)', 'bulletproof-security').':</span></strong> '.$php_config_file.'<br>';		
		} else {
			echo '<strong><span class="sysinfo-label-text">'.__('PHP Configuration File (php.ini)', 'bulletproof-security').':</span></strong> '.__('None/Not in use', 'bulletproof-security').'<br>';	
		}
	}

 	/*
	* WP get_temp_dir() preference is to return the value of sys_get_temp_dir(),
 	* followed by your PHP temporary upload directory, followed by WP_CONTENT_DIR,
 	* before finally defaulting to /tmp/
	* In the event that this function does not find a writable location,
 	* It may be overridden by the WP_TEMP_DIR constant in your wp-config.php file.
	* WP will use sys_get_temp_dir() for the temporary uploads folder.
 	*/

	echo '<strong><span class="sysinfo-label-text">'.__('WP Temp Dir: ', 'bulletproof-security').'</span></strong> ' . get_temp_dir() . '<br>';

	if ( defined('WP_TEMP_DIR') ) {
		echo '<strong><span class="sysinfo-label-text">'.__('The WP_TEMP_DIR constant is being used in wp-config.php file', 'bulletproof-security').'</span></strong><br>';
	}

	if ( function_exists('sys_get_temp_dir') ) {
		$sys_get_temp_dir = sys_get_temp_dir();
		if ( is_dir( $sys_get_temp_dir ) && is_readable( $sys_get_temp_dir ) ) {
			echo '<strong><span class="sysinfo-label-text">'.__('PHP Temp Dir: ', 'bulletproof-security').'</span></strong> ' . $sys_get_temp_dir . '<br>';			
		} else {
			echo '<strong><span class="sysinfo-label-text">'.__('PHP Temp Dir: ', 'bulletproof-security').'</span></strong> ' .__('Not set/defined or directory is not writable', 'bulletproof-security'). '<br>';	
		}
	}

	// The temporary directory used for storing files when doing file upload. 
	// Must be writable by whatever user PHP is running as. If not specified PHP will use the system's default.
	// WP will use sys_get_temp_dir() for the temporary uploads folder.
	$upload_tmp_dir = ini_get('upload_tmp_dir');
	if ( is_dir( $upload_tmp_dir ) && is_readable( $upload_tmp_dir ) ) {
		echo '<strong><span class="sysinfo-label-text">'.__('PHP Upload Temp Dir: ', 'bulletproof-security').'</span></strong> ' . $upload_tmp_dir . '<br>';
	} else {
		echo '<strong><span class="sysinfo-label-text">'.__('PHP Upload Temp Dir: ', 'bulletproof-security').'</span></strong> ' .__('Not set/defined or directory is not writable', 'bulletproof-security'). '<br>';
	}
	
	// Current directory used to save session data.
	$session_save_path = ini_get('session.save_path');
	if ( is_dir( $session_save_path ) && is_readable( $session_save_path ) ) {
		echo '<strong><span class="sysinfo-label-text">'.__('Session Save Path: ', 'bulletproof-security').'</span></strong> ' . $session_save_path . '<br>';
	} else {
		echo '<strong><span class="sysinfo-label-text">'.__('Session Save Path: ', 'bulletproof-security').'</span></strong> ' .__('Not set/defined or directory is not writable', 'bulletproof-security'). '<br>';
	}

	if ( function_exists('gc_enabled') && function_exists('gc_collect_cycles') ) {
	if ( gc_enabled() ) {
		$garbage = '<span class="sysinfo-label-text">'.__('On', 'bulletproof-security').'<strong> | '.__('Cycles: ', 'bulletproof-security') . '</span></strong>' . gc_collect_cycles();		
	} else {
		$garbage = 'Off';
	}
	echo '<strong><span class="sysinfo-label-text">'.__('Garbage Collector: ', 'bulletproof-security').'</span></strong> ' . $garbage . '<br>';
	}

	echo '<strong><span class="sysinfo-label-text">'.__('PHP Max Upload Size', 'bulletproof-security').':</span></strong> '; 
		$upload_max = ini_get('upload_max_filesize');
	echo $upload_max.'<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('PHP Max Post Size', 'bulletproof-security').':</span></strong> '; 
		$post_max = ini_get('post_max_size');
	echo $post_max.'<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('PHP Safe Mode', 'bulletproof-security').':</span></strong> ';
	
	if ( ini_get('safe_mode') == 1 ) { 
		$text = __('On', 'bulletproof-security');
		echo $text.'<br>'; 
	} else { 
		$text = __('Off', 'bulletproof-security');
		echo $text.'<br>';
	}
	echo '<strong><span class="sysinfo-label-text">'.__('PHP Allow URL fopen', 'bulletproof-security').':</span></strong> ';
	if ( ini_get('allow_url_fopen') == 1 ) { 
		$text = __('On', 'bulletproof-security');
		echo $text.'<br>';
	} else { 
		$text = __('Off', 'bulletproof-security');
		echo $text.'<br>';
	}	
	echo '<strong><span class="sysinfo-label-text">'.__('PHP Allow URL Include', 'bulletproof-security').':</span></strong> ';
	if ( ini_get('allow_url_include') == 1 ) { 
		$text = __('On', 'bulletproof-security');
		echo $text.'<br>'; 
	} else { 
		$text = __('Off', 'bulletproof-security');
		echo $text.'<br>';
	} 
	echo '<strong><span class="sysinfo-label-text">'.__('PHP Display Errors', 'bulletproof-security').':</span></strong> ';
	if ( ini_get('display_errors') == 1 ) { 
		$text = __('On', 'bulletproof-security');
		echo $text.'<br>'; 
	} else { 
		$text = __('Off', 'bulletproof-security');
		echo $text.'<br>';
	}
	echo '<strong><span class="sysinfo-label-text">'.__('PHP Display Startup Errors', 'bulletproof-security').':</span></strong> ';
	if ( ini_get('display_startup_errors') == 1 ) { 
		$text = __('On', 'bulletproof-security');
		echo $text.'<br>';
	} else { 
		$text = __('Off', 'bulletproof-security');
		echo $text.'<br>';
	}
	echo '<strong><span class="sysinfo-label-text">'.__('PHP Expose PHP', 'bulletproof-security').':</span></strong> ';
	if ( ini_get('expose_php') == 1 ) { 
		$text = __('On', 'bulletproof-security');
		echo $text.'<br>';
	} else { 
		$text = __('Off', 'bulletproof-security');
		echo $text.'<br>';
	}
	echo '<strong><span class="sysinfo-label-text">'.__('PHP Register Globals', 'bulletproof-security').':</span></strong> ';
	if ( ini_get('register_globals') == 1 ) { 
		$text = __('On', 'bulletproof-security');
		echo $text.'<br>';
	} else { 
		$text = __('Off', 'bulletproof-security');
		echo $text.'<br>';
	}
	echo '<strong><span class="sysinfo-label-text">'.__('PHP MySQL Allow Persistent Connections', 'bulletproof-security').':</span></strong> ';
	if ( ini_get('mysql.allow_persistent') == 1 ) { 
		$text = __('On', 'bulletproof-security');
		echo $text.'<br>'; 
	} else { 
		$text = __('Off', 'bulletproof-security');
		echo $text.'<br>';
	}
	echo '<strong><span class="sysinfo-label-text">'.__('PHP Output Buffering', 'bulletproof-security').':</span></strong> ';
		$output_buffering = ini_get('output_buffering');
	if ( ini_get('output_buffering') != 0 ) { 
		echo $output_buffering.'<br>';
	} else { 
		$text = __('Off', 'bulletproof-security');
		echo $text.'<br>';
	}
	echo '<strong><span class="sysinfo-label-text">'.__('PHP Max Script Execution Time', 'bulletproof-security').':</span></strong> '; 
		$max_execute = ini_get('max_execution_time');
	echo $max_execute.' Seconds<br>';
	echo '<strong><span class="sysinfo-label-text">'.__('PHP Magic Quotes GPC', 'bulletproof-security').':</span></strong> ';
	if ( ini_get('magic_quotes_gpc') == 1 ) { 
		$text = __('On', 'bulletproof-security');
		echo $text.'<br>'; 
	} else { 
		$text = __('Off', 'bulletproof-security');
		echo $text.'<br>'; 
	}
	echo '<strong><span class="sysinfo-label-text">'.__('PHP open_basedir', 'bulletproof-security').':</span></strong> ';
		$open_basedir = ini_get('open_basedir');
	if ( $open_basedir != '' ) {
		echo $open_basedir.'<br>';
	} else {
		echo __('Off/Not in use', 'bulletproof-security').'<br>';	
	}
	echo '<strong><span class="sysinfo-label-text">'.__('PHP XML Support', 'bulletproof-security').':</span></strong> ';
	if ( is_callable('xml_parser_create') ) { 
		$text = __('Yes', 'bulletproof-security');
		echo $text.'<br>';
	} else { 
		$text = __('No', 'bulletproof-security');
		echo $text.'<br>';
	}
	echo '<strong><span class="sysinfo-label-text">'.__('PHP IPTC Support', 'bulletproof-security').':</span></strong> ';
	if ( is_callable('iptcparse') ) { 
		$text = __('Yes', 'bulletproof-security');
		echo $text.'<br>';
	} else { 
		$text = __('No', 'bulletproof-security');
		echo $text.'<br>';
	}
	echo '<strong><span class="sysinfo-label-text">'.__('PHP Exif Support', 'bulletproof-security').':</span></strong> ';
	if ( is_callable('exif_read_data') ) { 
		$text = __('Yes', 'bulletproof-security');
		echo $text.'<br>';
	} else { 
		$text = __('No', 'bulletproof-security');
		echo $text.'<br>';
	}
	
	$disable_functions = ini_get('disable_functions');
	$suhosin_functions = ini_get('suhosin.executor.func.blacklist');	
	
	if ( ! extension_loaded( 'suhosin' ) ) {
		echo '<strong><span class="sysinfo-label-text">'.__('PHP Disable Functions', 'bulletproof-security').':</span></strong> ';
		if ( ini_get('disable_functions') != '' && ini_get('disable_functions') != false ) {
			echo '<div style="max-width:300px;word-wrap:break-word;overflow-wrap:break-word">'.$disable_functions.'</div>';
		} else {
			$text = __('No PHP functions are disabled.', 'bulletproof-security');
			echo $text.'<br>';
		}
	
	} elseif ( extension_loaded( 'suhosin' ) ) {
		echo '<strong><span class="sysinfo-label-text">'.__('PHP Suhosin Function Blacklist', 'bulletproof-security').':</span></strong> ';
		if ( ini_get('suhosin.executor.func.blacklist') != '' && ini_get('suhosin.executor.func.blacklist') != false ) {
			echo $suhosin_functions.'<br>';
		} else {
			$text = __('No PHP functions are blacklisted.', 'bulletproof-security');
			echo $text.'<br>';
		}
	}

	echo '</span>';
	?>
	
    </td>      
    <td>&nbsp;</td>
    <td rowspan="2" class="bps-table_cell_perms_blank">
	
	<?php 
	if ( is_admin() && current_user_can('manage_options') ) {
	
	$sapi_type = php_sapi_name();
	$DBBoptions = get_option('bulletproof_security_options_db_backup');
	$bps_db_backup_folder = ! isset($DBBoptions['bps_db_backup_folder']) ? '' : $DBBoptions['bps_db_backup_folder'];
	$db_backup = str_replace( array( '\\', '//'), "/", $bps_db_backup_folder );
	$wpcontent_single_slash = str_replace( array( '\\', '//'), "/", WP_CONTENT_DIR );
	$bps_root_htaccess_file = ABSPATH . '.htaccess';
	$bps_wpconfig_file = ABSPATH . 'wp-config.php';	

	if ( substr($sapi_type, 0, 6) != 'apache' ) {		
	
	echo '<div style=\'padding:0px 0px 5px 5px;color:#000;\'><strong>'; _e('CGI File and Folder Permissions|Recommendations', 'bulletproof-security'); echo '</strong></div>';
	echo '<table style="width:100%;color:#000;background-color:#A9F5A0;border-bottom:1px solid black;border-top:1px solid black;">';
	echo '<tr>';
    echo '<td style="padding:2px;width:40%;font-weight:bold;">'; _e('File Path', 'bulletproof-security'); echo '<br>'; _e('Folder Path', 'bulletproof-security'); echo '</td>';
    echo '<td style="padding:2px;width:15%;font-weight:bold;">'; _e('Recommended', 'bulletproof-security'); echo '<br>'; _e('Permissions', 'bulletproof-security'); echo '</td>';
    echo '<td style="padding:2px;width:15%;font-weight:bold;">'; _e('Current', 'bulletproof-security'); echo '<br>'; _e('Permissions', 'bulletproof-security'); echo '</td>';
	echo '<td style="padding:2px;width:15%;font-weight:bold;">'; _e('Script Owner', 'bulletproof-security'); echo '<br>'; _e(' User ID (UID)', 'bulletproof-security'); echo '</td>';
	echo '<td style="padding:2px;width:15%;font-weight:bold;">'; _e('File Owner', 'bulletproof-security'); echo '<br>'; _e(' User ID', 'bulletproof-security'); echo '</td>';
	echo '</tr>';
    echo '</table>';

	bps_check_perms("../", "705");

	if ( file_exists($bps_root_htaccess_file) ) {
	bps_check_perms("../.htaccess", "404");
	} else {
	echo '<table style="width:100%;background-color:#fff;">';
	echo '<tr>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:40%;">' . '../.htaccess' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '</tr>';
	echo '</table>';
	}
	if ( file_exists($bps_wpconfig_file) ) {
	bps_check_perms("../wp-config.php", "604");
	} else {
	echo '<table style="width:100%;background-color:#fff;">';
	echo '<tr>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:40%;">' . '../wp-config.php' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '</tr>';
	echo '</table>';		
	}

	bps_check_perms("../wp-admin", "705");
	bps_check_perms("../wp-includes", "705");
	bps_check_perms("../$bps_wpcontent_dir", "705");
	bps_check_perms("../$bps_plugin_dir", "705");
	bps_check_perms( str_replace( WP_CONTENT_DIR, "../$bps_wpcontent_dir", get_theme_root() ), "705");
	bps_check_perms("../$bps_uploads_dir", "705");
	bps_check_perms("../$bps_wpcontent_dir/upgrade", "755");
	bps_check_perms("../$bps_wpcontent_dir/mu-plugins", "705");
	bps_check_perms("../$bps_wpcontent_dir/bps-backup", "705");
	bps_check_perms("../$bps_wpcontent_dir/bps-backup/logs", "705");
	bps_check_perms("../$bps_wpcontent_dir/bps-backup/master-backups", "705");
	bps_check_perms("../$bps_wpcontent_dir/bps-backup/mscan", "705");
	bps_check_perms("../$bps_wpcontent_dir/bps-backup/wp-hashes", "705");	
	bps_check_perms("../$bps_wpcontent_dir/bps-backup/plugin-hashes", "705");
	bps_check_perms("../$bps_wpcontent_dir/bps-backup/theme-hashes", "705");	
	
	if ( function_exists('sys_get_temp_dir') && is_dir( $sys_get_temp_dir ) ) {
	bps_check_perms("$sys_get_temp_dir", "---");
	}
	if ( isset($DBBoptions['bps_db_backup_folder']) && $DBBoptions['bps_db_backup_folder'] != '' ) {
	bps_check_perms( str_replace( $wpcontent_single_slash, "../$bps_wpcontent_dir", $db_backup ), "705");
	}
	echo '<div style=\'padding-bottom:15px;\'></div>';
	
	} else {
	
	echo '<div style=\'padding:0px 0px 5px 5px;color:#000;\'><strong>'; _e('DSO File and Folder Permissions|Recommendations', 'bulletproof-security'); echo '</strong></div>';
	echo '<table style="width:100%;color:#000;background-color:#A9F5A0;border-bottom:1px solid black;border-top:1px solid black;">';
	echo '<tr>';
    echo '<td style="padding:2px;width:40%;font-weight:bold;">'; _e('File Path', 'bulletproof-security'); echo '<br>'; _e('Folder Path', 'bulletproof-security'); echo '</td>';
    echo '<td style="padding:2px;width:15%;font-weight:bold;">'; _e('Recommended', 'bulletproof-security'); echo '<br>'; _e('Permissions', 'bulletproof-security'); echo '</td>';
    echo '<td style="padding:2px;width:15%;font-weight:bold;">'; _e('Current', 'bulletproof-security'); echo '<br>'; _e('Permissions', 'bulletproof-security'); echo '</td>';
	echo '<td style="padding:2px;width:15%;font-weight:bold;">'; _e('Script Owner', 'bulletproof-security'); echo '<br>'; _e(' User ID (UID)', 'bulletproof-security'); echo '</td>';
	echo '<td style="padding:2px;width:15%;font-weight:bold;">'; _e('File Owner', 'bulletproof-security'); echo '<br>'; _e(' User ID', 'bulletproof-security'); echo '</td>';
	echo '</tr>';
    echo '</table>';
	
	bps_check_perms("../", "755");

	if ( file_exists($bps_root_htaccess_file) ) {
	bps_check_perms("../.htaccess", "644");
	} else {
	echo '<table style="width:100%;background-color:#fff;">';
	echo '<tr>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:40%;">' . '../.htaccess' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '</tr>';
	echo '</table>';
	}
	if ( file_exists($bps_wpconfig_file) ) {
	bps_check_perms("../wp-config.php", "644");
	} else {
	echo '<table style="width:100%;background-color:#fff;">';
	echo '<tr>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:40%;">' . '../wp-config.php' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . '---' . '</td>';
    echo '</tr>';
	echo '</table>';		
	}

	bps_check_perms("../wp-admin", "755");
	bps_check_perms("../wp-includes", "755");
	bps_check_perms("../$bps_wpcontent_dir", "755");
	bps_check_perms("../$bps_plugin_dir", "755");
	bps_check_perms( str_replace( WP_CONTENT_DIR, "../$bps_wpcontent_dir", get_theme_root() ), "755");
	bps_check_perms("../$bps_uploads_dir", "755");
	bps_check_perms("../$bps_wpcontent_dir/upgrade", "755");
	bps_check_perms("../$bps_wpcontent_dir/mu-plugins", "755");
	bps_check_perms("../$bps_wpcontent_dir/bps-backup", "755");
	bps_check_perms("../$bps_wpcontent_dir/bps-backup/logs", "755");
	bps_check_perms("../$bps_wpcontent_dir/bps-backup/master-backups", "755");
	bps_check_perms("../$bps_wpcontent_dir/bps-backup/mscan", "755");
	bps_check_perms("../$bps_wpcontent_dir/bps-backup/wp-hashes", "755");
	bps_check_perms("../$bps_wpcontent_dir/bps-backup/plugin-hashes", "755");
	bps_check_perms("../$bps_wpcontent_dir/bps-backup/theme-hashes", "755");

	if ( is_dir( $upload_tmp_dir ) ) {
	bps_check_perms("$upload_tmp_dir", "---");
	}
	if ( function_exists('sys_get_temp_dir') && is_dir( $sys_get_temp_dir ) ) {
	bps_check_perms("$sys_get_temp_dir", "---");
	}
	if ( isset($DBBoptions['bps_db_backup_folder']) && $DBBoptions['bps_db_backup_folder'] != '' ) {
	bps_check_perms( str_replace( $wpcontent_single_slash, "../$bps_wpcontent_dir", $db_backup ), "755");
	}
	echo '<div style=\'padding-bottom:15px;\'></div>';
	}
	}

	$time_end = microtime( true );
	$run_time = $time_end - $time_start;
	$time_display = '<strong>'.__('System Info Processing Completion Time: ', 'bulletproof-security').'</strong>'. round( $run_time, 2 ) . ' Seconds';	
	
	echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
	echo bpsPro_memory_resource_usage();
	echo $time_display;
	echo '</p></div>';
?>

    </td>
  </tr>
  <tr>
    <td class="bps-table_cell">&nbsp;</td>
    <td>&nbsp;</td>
    <!-- <td class="bps-table_cell">&nbsp;</td> -->
  </tr>
  <tr>
    <td class="bps-table_cell_bottom">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="bps-table_cell_bottom">&nbsp;</td>
  </tr>
</table>
<br />
</div>
</div>

<div id="bps-tabs-2" class="bps-tab-page">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title"></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

	<?php 
    
	_e('Check your website Headers or another website\'s Headers by making a GET Request', 'bulletproof-security').'<br>';

// Form - wp_remote_get Headers check - GET Request Method
// Note: htmlspecialchars displays the sanitized esc_html output
function bps_sysinfo_get_headers_get() {
global $bps_topDiv, $bps_bottomDiv;
	
	if ( isset( $_POST['Submit-Headers-Check-Get'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bpsHeaderCheckGet' );

		$url 		= isset( $_POST['bpsURLGET'] ) ? esc_url($_POST['bpsURLGET']) : '';
		$response 	= wp_remote_get( $url );
	
		if ( ! is_wp_error( $response ) ) {	
	
			echo $bps_topDiv;
			echo '<strong>'.__('GET Request Headers: ', 'bulletproof-security').'</strong>'. esc_url($url) .'<br>';
			echo '<pre>';
			echo 'HTTP Status Code: ';
			print_r($response['response']['code']);
			echo ' ';
			print_r($response['response']['message']);
			echo '<br><br>';
			echo 'Headers: ';
			print_r($response['headers']);
			echo '</pre>';	
			echo $bps_bottomDiv;
	
		} else {
			
			echo $bps_topDiv;		
			$text = '<font color="#fb0101"><strong>'.__('Error: The WordPress wp_remote_get function is not available or is blocked on your website/server. Or you did not enter a valid URL.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
			echo $bps_bottomDiv;
		}
	}
}
?>

<form name="bpsHeadersGet" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/system-info/system-info.php#bps-tabs-2' ); ?>" method="post">
<?php wp_nonce_field('bpsHeaderCheckGet'); 
$bpsURLGET = isset( $_POST['bpsURLGET'] ) ? $_POST['bpsURLGET'] : '';
?>

<div><label for="bpsHeaders"><strong><?php _e('Enter a Website URL - Example: ', 'bulletproof-security'); echo get_site_url(); ?></strong></label><br />
    <input type="text" name="bpsURLGET" class="form-300" value="<?php echo esc_url($bpsURLGET); ?>" /> <br />
    <div style="margin:10px 0px">
	<input type="submit" name="Submit-Headers-Check-Get" class="button bps-button" value="<?php esc_attr_e('Check Headers GET Request', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('This Headers check makes a GET Request using the WordPress wp_remote_get function.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('You can use the Check Headers HEAD Request tool to check headers using HEAD instead of GET.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to proceed or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
    </div>
</div>
<?php bps_sysinfo_get_headers_get(); ?>
</form>

<div id="bps-whitespace-275" style="min-height:275px"></div>

</td>
  </tr>
</table>
</div>

<div id="bps-tabs-3" class="bps-tab-page">

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