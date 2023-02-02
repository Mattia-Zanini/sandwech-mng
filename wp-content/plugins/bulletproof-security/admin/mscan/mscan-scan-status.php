<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>MScan Scan Status</title>

<style>
body {background:white;}
html.wp-toolbar{padding:0px}
#wpcontent{margin-left:0px}
#wpadminbar{height:0}
#adminmenuback{}
#adminmenuwrap{display:none}
#adminmenu div.wp-menu-name{display:none}
ul#adminmenu{display:none}
#footer-thankyou{display:none}
div#wpfooter{display:none}
div#bps-inpage-message{display:none}
div.update-nag{display:none}
div.notice{display:none}
div.notice-message{display:none}
div.notice-success{display:none}
div.notice-error{display:none}
div.notice-warning{display:none}
div.notice-info{display:none}
div.is-dismissible{display:none}
div#message{display:none}
div#bps-status-display{}
div#query-monitor-main{visibility:hidden}
div#MScan-Time-Container {z-index:999999999;position:relative;top:0px;left:0px;background-color:#fff;}
div#mscantimer {z-index:999999999;color:#000;font-size:13px!important;font-weight:600!important;line-height:18px;padding:4px 5px 0px 0px;position:relative;top:0px;left:0px;}
#MscanProgressBar {z-index:999999999;position:relative;top:0px;left:0px;width:98%;height:25px;background-color:#e8e8e8;border-radius:2px;-webkit-box-shadow:inset 0 2px 3px rgba(0, 0, 0, 0.25);-moz-box-shadow:inset 0 2px 3px rgba(0, 0, 0, 0.25);box-shadow:inset 0 2px 3px rgba(0, 0, 0, 0.25);}
#MscanBar {z-index:999999999;width:0%;height:25px;font-size:12px!important;font-weight:600!important;text-align:center;line-height:25px;color:white;}
.mscan-progress-bar {z-index:999999999;width:0;height:100%;background:#0e8bcb;background:-moz-linear-gradient(top, #0e8bcb 0%, #08496b 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,#0e8bcb), color-stop(100%,#08496b));background:-webkit-linear-gradient(top, #0e8bcb 0%,#08496b 100%);background:-o-linear-gradient(top, #0e8bcb 0%,#08496b 100%);background:-ms-linear-gradient(top, #0e8bcb 0%,#08496b 100%);background:linear-gradient(to bottom, #0e8bcb 0%,#08496b 100%);-webkit-transition:width 1s ease-in-out;-moz-transition:width 1s ease-in-out;-o-transition:width 1s ease-in-out;transition:width 1s ease-in-out;}

@media screen and (min-width: 280px) and (max-width: 1043px){
div#bps-status-display{display:none}
}
@media screen and (min-width: 280px) and (max-width: 960px){
div#wpadminbar{display:none}
div#adminmenu, div#adminmenu .wp-submenu, div#adminmenuwrap{display:none}
}
</style>

<script type="text/javascript">
<!--
function AutoRefreshOnce( m ) {
	   
	// The hash is not seen on initial page load, but is seen after the first reload.
	if ( !window.location.hash ) {
		window.location = window.location + '#loaded';
		setTimeout( "location.reload(true);", m );
    }
}
//-->
</script>
</head>

<body onload="JavaScript:AutoRefreshOnce(1000);">

<?php

	if ( ! function_exists( 'get_option' ) ) {
		$wp_load_file1 = dirname(__FILE__) . '/wp-load.php';
		$wp_load_file2 = dirname(dirname(__FILE__)) . '/wp-load.php';
		$wp_load_file3 = dirname(dirname(dirname(__FILE__))) . '/wp-load.php';
		$wp_load_file4 = dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';
		$wp_load_file5 = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php';
		$wp_load_file6 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/wp-load.php';
		$wp_load_file7 = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))) . '/wp-load.php';		
		$wp_load_file8 = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))))) . '/wp-load.php';		
		$wp_load_file9 = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))))) . '/wp-load.php';		
		$wp_load_file10 = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))))))) . '/wp-load.php';		
		$wp_load_file11 = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))))))) . '/wp-load.php';		
		$wp_load_file12 = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))))))))) . '/wp-load.php';		

		if ( file_exists( $wp_load_file1 ) ) {
			require_once $wp_load_file1;		
		} elseif ( file_exists( $wp_load_file2 ) ) {
			require_once $wp_load_file2;		
		} elseif ( file_exists( $wp_load_file3 ) ) {
			require_once $wp_load_file3;
		} elseif ( file_exists( $wp_load_file4 ) ) {
			require_once $wp_load_file4;	
		} elseif ( file_exists( $wp_load_file5 ) ) {		
			require_once $wp_load_file5;		
		} elseif ( file_exists( $wp_load_file6 ) ) {
			require_once $wp_load_file6;
		} elseif ( file_exists( $wp_load_file7 ) ) {
			require_once $wp_load_file7;
		} elseif ( file_exists( $wp_load_file8 ) ) {
			require_once $wp_load_file8;
		} elseif ( file_exists( $wp_load_file9 ) ) {
			require_once $wp_load_file9;
		} elseif ( file_exists( $wp_load_file10 ) ) {
			require_once $wp_load_file10;		
		} elseif ( file_exists( $wp_load_file11 ) ) {
			require_once $wp_load_file11;		
		} elseif ( file_exists( $wp_load_file12 ) ) {
			require_once $wp_load_file12;		
		} else {
			echo '<strong><font color="#fb0101">BPS cannot find and load the WordPress wp-load.php file. MScan cannot be used on this website until that problem is fixed.</font></strong>';
			exit();
		}
	}

function bpsPro_mscan_completed() {

	$MScan_status = get_option('bulletproof_security_options_MScan_status');
	$MScan_options = get_option('bulletproof_security_options_MScan');
	$mstime = ! isset($MScan_options['mscan_max_time_limit']) ? '' : $MScan_options['mscan_max_time_limit'];
	ini_set('max_execution_time', $mstime);	 

	if ( isset($MScan_status['bps_mscan_status']) && $MScan_status['bps_mscan_status'] == '9' ) {
	 
		$MScan_status_db = array( 
		'bps_mscan_time_start' 					=> $MScan_status['bps_mscan_time_start'],  
		'bps_mscan_time_stop' 					=> $MScan_status['bps_mscan_time_stop'], 
		'bps_mscan_time_end' 					=> $MScan_status['bps_mscan_time_end'],  
		'bps_mscan_time_remaining' 				=> $MScan_status['bps_mscan_time_remaining'],
		'bps_mscan_status' 						=> '8', 
		'bps_mscan_last_scan_timestamp' 		=> $MScan_status['bps_mscan_last_scan_timestamp'], 
		'bps_mscan_total_time' 					=> $MScan_status['bps_mscan_total_time'], 
		'bps_mscan_total_website_files' 		=> '', 
		'bps_mscan_total_wp_core_files' 		=> $MScan_status['bps_mscan_total_wp_core_files'], 
		'bps_mscan_total_non_image_files' 		=> $MScan_status['bps_mscan_total_non_image_files'], 
		'bps_mscan_total_image_files' 			=> '', 
		'bps_mscan_total_all_scannable_files' 	=> $MScan_status['bps_mscan_total_all_scannable_files'], 
		'bps_mscan_total_skipped_files' 		=> $MScan_status['bps_mscan_total_skipped_files'], 
		'bps_mscan_total_suspect_files' 		=> $MScan_status['bps_mscan_total_suspect_files'], 
		'bps_mscan_suspect_skipped_files' 		=> $MScan_status['bps_mscan_suspect_skipped_files'], 
		'bps_mscan_total_suspect_db' 			=> $MScan_status['bps_mscan_total_suspect_db'], 
		'bps_mscan_total_ignored_files' 		=> $MScan_status['bps_mscan_total_ignored_files'],
		'bps_mscan_total_plugin_files' 			=> $MScan_status['bps_mscan_total_plugin_files'], 			 
		'bps_mscan_total_theme_files' 			=> $MScan_status['bps_mscan_total_theme_files'] 
		);		
		
		foreach( $MScan_status_db as $key => $value ) {
			update_option('bulletproof_security_options_MScan_status', $MScan_status_db);
		}	 
	}

	if ( isset($MScan_status['bps_mscan_status']) && $MScan_status['bps_mscan_status'] == '3' || isset($MScan_status['bps_mscan_status']) && $MScan_status['bps_mscan_status'] == '2' ) {
	 
		$MScan_status_db = array( 
		'bps_mscan_time_start' 					=> $MScan_status['bps_mscan_time_start'],  
		'bps_mscan_time_stop' 					=> $MScan_status['bps_mscan_time_stop'], 
		'bps_mscan_time_end' 					=> $MScan_status['bps_mscan_time_end'],  
		'bps_mscan_time_remaining' 				=> $MScan_status['bps_mscan_time_remaining'],
		'bps_mscan_status' 						=> '4', 
		'bps_mscan_last_scan_timestamp' 		=> $MScan_status['bps_mscan_last_scan_timestamp'], 
		'bps_mscan_total_time' 					=> $MScan_status['bps_mscan_total_time'], 
		'bps_mscan_total_website_files' 		=> '', 
		'bps_mscan_total_wp_core_files' 		=> $MScan_status['bps_mscan_total_wp_core_files'], 
		'bps_mscan_total_non_image_files' 		=> $MScan_status['bps_mscan_total_non_image_files'], 
		'bps_mscan_total_image_files' 			=> '', 
		'bps_mscan_total_all_scannable_files' 	=> $MScan_status['bps_mscan_total_all_scannable_files'], 
		'bps_mscan_total_skipped_files' 		=> $MScan_status['bps_mscan_total_skipped_files'], 
		'bps_mscan_total_suspect_files' 		=> $MScan_status['bps_mscan_total_suspect_files'], 
		'bps_mscan_suspect_skipped_files' 		=> $MScan_status['bps_mscan_suspect_skipped_files'], 
		'bps_mscan_total_suspect_db' 			=> $MScan_status['bps_mscan_total_suspect_db'], 
		'bps_mscan_total_ignored_files' 		=> $MScan_status['bps_mscan_total_ignored_files'],
		'bps_mscan_total_plugin_files' 			=> $MScan_status['bps_mscan_total_plugin_files'], 			 
		'bps_mscan_total_theme_files' 			=> $MScan_status['bps_mscan_total_theme_files'] 
		);		
		
		foreach( $MScan_status_db as $key => $value ) {
			update_option('bulletproof_security_options_MScan_status', $MScan_status_db);
		}	 
	}
}

	$MScan_status = get_option('bulletproof_security_options_MScan_status');
	$MScan_options = get_option('bulletproof_security_options_MScan');

	$mscan_start_time = ! isset($MScan_status['bps_mscan_time_start']) ? '' : $MScan_status['bps_mscan_time_start']; 
	$mscan_time_stop = ! isset($MScan_status['bps_mscan_time_stop']) ? '' : $MScan_status['bps_mscan_time_stop'];
	$mscan_future_time = ! isset($MScan_status['bps_mscan_time_remaining']) ? '' : $MScan_status['bps_mscan_time_remaining'];
	$mscan_status = ! isset($MScan_status['bps_mscan_status']) ? '' : $MScan_status['bps_mscan_status'];
	$mscan_timestamp = ! isset($MScan_status['bps_mscan_last_scan_timestamp']) ? '' : $MScan_status['bps_mscan_last_scan_timestamp'];
	$mscan_total_time = ! isset($MScan_status['bps_mscan_total_time']) ? '' : $MScan_status['bps_mscan_total_time'];	
	$mscan_suspect_files = ! isset($MScan_status['bps_mscan_total_suspect_files']) ? '' : $MScan_status['bps_mscan_total_suspect_files'];
	$mscan_suspect_skipped_files = ! isset($MScan_status['bps_mscan_suspect_skipped_files']) ? '' : $MScan_status['bps_mscan_suspect_skipped_files'];	
	$mscan_suspect_db = ! isset($MScan_status['bps_mscan_total_suspect_db']) ? '' : $MScan_status['bps_mscan_total_suspect_db'];
	$mscan_skipped_files = ! isset($MScan_status['bps_mscan_total_skipped_files']) ? '' : $MScan_status['bps_mscan_total_skipped_files']; 

	if ( isset($MScan_options['mscan_scan_skipped_files']) && $MScan_options['mscan_scan_skipped_files'] == 'On' ) {
		$mscan_total_files = $MScan_status['bps_mscan_total_skipped_files'];
		$skipped_scan = 1;
	} else {
		$mscan_total_files = ! isset($MScan_status['bps_mscan_total_all_scannable_files']) ? '' : $MScan_status['bps_mscan_total_all_scannable_files'];
		$skipped_scan = 0;
	}

	if ( isset($MScan_options['mscan_scan_database']) && $MScan_options['mscan_scan_database'] == 'On' ) {
		$mscan_db_scan = 1;
	} else {
		$mscan_db_scan = 0;
	}

	$mscan_hash_status_options = get_option('bulletproof_security_options_mscan_hash_status');
	
	$mscan_wp_core_hash_status = isset($mscan_hash_status_options['mscan_wp_core_hash_status']) ? $mscan_hash_status_options['mscan_wp_core_hash_status'] : '';
	$mscan_wp_core_hash_count = isset($mscan_hash_status_options['mscan_wp_core_hash_count']) ? $mscan_hash_status_options['mscan_wp_core_hash_count'] : '';
	$mscan_plugin_hash_status = isset($mscan_hash_status_options['mscan_plugin_hash_status']) ? $mscan_hash_status_options['mscan_plugin_hash_status'] : '';
	$mscan_plugin_hash_count = isset($mscan_hash_status_options['mscan_plugin_hash_count']) ? $mscan_hash_status_options['mscan_plugin_hash_count'] : '';
	$mscan_theme_hash_status = isset($mscan_hash_status_options['mscan_theme_hash_status']) ? $mscan_hash_status_options['mscan_theme_hash_status'] : '';
	$mscan_theme_hash_count = isset($mscan_hash_status_options['mscan_theme_hash_count']) ? $mscan_hash_status_options['mscan_theme_hash_count'] : '';
	
	if ( $mscan_wp_core_hash_status == '1' ) {
		$mscan_wp_core_status = 'WP Core Zip Files: ' . $mscan_wp_core_hash_count;
	} elseif ( $mscan_wp_core_hash_status == '0' ) {
		$mscan_wp_core_status = 'WP Core Zip Files: Error';
	} else {
		$mscan_wp_core_status = 'WP Core Zip Files: 0';
	}

	if ( $mscan_plugin_hash_status == '1' ) {
		$mscan_plugin_status = 'Plugin Zip Files: ' . $mscan_plugin_hash_count;
	} elseif ( $mscan_plugin_hash_status == '0' ) {
		$mscan_plugin_status = 'Plugin Zip Files: Error';
	} else {
		$mscan_plugin_status = 'Plugin Zip Files: 0';
	}

	if ( $mscan_theme_hash_status == '1' ) {
		$mscan_theme_status = 'Theme Zip Files: ' . $mscan_theme_hash_count;
	} elseif ( $mscan_theme_hash_status == '0' ) {
		$mscan_theme_status = 'Theme Zip Files: Error';
	} else {
		$mscan_theme_status = 'Theme Zip Files: 0';
	}

if ( isset($MScan_status['bps_mscan_status']) && $MScan_status['bps_mscan_status'] == '2' || isset($MScan_status['bps_mscan_status']) && $MScan_status['bps_mscan_status'] == '3' || isset($MScan_status['bps_mscan_status']) && $MScan_status['bps_mscan_status'] == '5' || isset($MScan_status['bps_mscan_status']) && $MScan_status['bps_mscan_status'] == '9' ) { ?>

<div id="MscanProgressBar">
  	<div id="MscanBar" class="mscan-progress-bar"></div>
</div>

<?php } ?>

<div id="MScan-Time-Container">
	<div id="mscantimer"></div>
</div>

<script type="text/javascript">
/* <![CDATA[ */
	var currentTimeI = new Date().getTime() / 1000;
	var futureTimeI = <?php echo json_encode( $mscan_future_time, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var scanStartI = <?php echo json_encode( $mscan_start_time, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var mscanStatusI = <?php echo json_encode( $mscan_status, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var timeStampI = <?php echo json_encode( $mscan_timestamp, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var totalScanTimeI = <?php echo json_encode( $mscan_total_time, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var totalFilesI = <?php echo json_encode( $mscan_total_files, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var skippedFilesI = <?php echo json_encode( $mscan_skipped_files, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var skippedScanI = <?php echo json_encode( $skipped_scan, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var dbScanI = <?php echo json_encode( $mscan_db_scan, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var suspectI = <?php echo json_encode( $mscan_suspect_files, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var suspectSkipI = <?php echo json_encode( $mscan_suspect_skipped_files, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var suspectDBI = <?php echo json_encode( $mscan_suspect_db, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var wpCoreZipI = <?php echo json_encode( $mscan_wp_core_status, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var pluginZipI = <?php echo json_encode( $mscan_plugin_status, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var themeZipI = <?php echo json_encode( $mscan_theme_status, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;

	var timeRemainingI = futureTimeI - currentTimeI;
	var minuteI = 60;
	var hourI = 60 * 60;
	var dayI = 60 * 60 * 24;
	var dayFloorI = Math.floor(totalScanTimeI / dayI);
	var hourFloorI = Math.floor((totalScanTimeI - dayFloorI * dayI) / hourI);
	var minuteFloorI = Math.floor((totalScanTimeI - dayFloorI * dayI - hourFloorI * hourI) / minuteI);
	var secondFloorI = Math.floor((totalScanTimeI - dayFloorI * dayI - hourFloorI * hourI - minuteFloorI * minuteI));
	var hourFloorFI = ("0" + hourFloorI).slice(-2);	
	var minuteFloorFI = ("0" + minuteFloorI).slice(-2);	
	var secondFloorFI = ("0" + secondFloorI).slice(-2);

	if ( totalFilesI == "" ) {
		totalFilesI = 0;
	}

	if ( skippedFilesI == "" ) {
		skippedFilesI = 0;
	}

	if ( suspectI == "" ) {
		suspectI = 0;
	}

	if ( suspectSkipI == "" ) {
		suspectSkipI = 0;
	}

	if ( suspectDBI == "" ) {
		suspectDBI = 0;
	}

	if ( mscanStatusI == 8 && futureTimeI < currentTimeI ) {
		document.getElementById("mscantimer").innerHTML = "Hash Maker Completed [" + timeStampI + "] : Total Time: "  + hourFloorFI + ":" + minuteFloorFI + ":" + secondFloorFI + "<br />" + "Total Zip Files Downloaded, Extracted and File Hashes Created: " + wpCoreZipI + " : " + pluginZipI + " : " + themeZipI + "<br />" + "Scanning is turned off when WP Core, Plugin or Theme zip files are being processed. Click the Start Scan button to run a file scan.";
		window.opener.location.reload();
		console.log( "Status: 8 : Future Time < Time : Hash Maker Completed" );		
	}

	if ( mscanStatusI == 4 && futureTimeI < currentTimeI ) {

		if ( skippedScanI == 0 ) {
			
			if ( dbScanI == 1 ) {		
				document.getElementById("mscantimer").innerHTML = "Scan Completed [" + timeStampI + "] : Total Scan Time: "  + hourFloorFI + ":" + minuteFloorFI + ":" + secondFloorFI + "<br />" + "Total Files Scanned: " + totalFilesI + " : Skipped Files: " + skippedFilesI + " : Suspicious Files: " + suspectI + " : Suspicious DB Entries: " + suspectDBI + "<br />" + "To view the detailed Scan Report click the View Report button below. Please view the Scan Report before clicking the Suspicious Files and DB Entries accordion tabs below.";
				window.opener.location.reload();
				console.log( "Status: 4 : Future Time < Time : Skipped Files: Off : DB Scan: On" );			
			
			} else {
				
				document.getElementById("mscantimer").innerHTML = "Scan Completed [" + timeStampI + "] : Total Scan Time: "  + hourFloorFI + ":" + minuteFloorFI + ":" + secondFloorFI + "<br />" + "Total Files Scanned: " + totalFilesI + " : Skipped Files: " + skippedFilesI + " : Suspicious Files: " + suspectI + "<br />" + "To view the detailed Scan Report click the View Report button below. Please view the Scan Report before clicking the Suspicious Files and DB Entries accordion tabs below.";
				window.opener.location.reload();
				console.log( "Status: 4 : Future Time < Time : Skipped Files: Off : DB Scan: Off" );
			}
		}
		
		if ( skippedScanI == 1 ) {
			document.getElementById("mscantimer").innerHTML = "Skipped File Scan Completed [" + timeStampI + "] : Total Scan Time: "  + hourFloorFI + ":" + minuteFloorFI + ":" + secondFloorFI + "<br />" + "Total Files Scanned: " + totalFilesI + " : Suspicious Files: " + suspectSkipI + "<br />" + "To view the detailed Scan Report click the View Report button below. Please view the Scan Report before clicking the Suspicious Files and DB Entries accordion tabs below.";
			window.opener.location.reload();
			console.log( "Status: 4 : Future Time < Time : Skipped Files: On : DB Scan: NA" );		
		}
	}
	
var MScan = setInterval(function(){ MScanTimer() }, 1000);

function MScanTimer() {

	var currentTime = new Date().getTime() / 1000;
	var futureTime = <?php echo json_encode( $mscan_future_time, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var scanStart = <?php echo json_encode( $mscan_start_time, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var scanStop = <?php echo json_encode( $mscan_time_stop, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var totalFiles = <?php echo json_encode( $mscan_total_files, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var mscanStatus = <?php echo json_encode( $mscan_status, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;	
	
	var timeRemaining = futureTime - (currentTime - 10); 
	var timeRemainingTE = futureTime - (currentTime + 30); 
	var timeRemainingPB = futureTime - currentTime;
	
	var minute = 60;
	var hour = 60 * 60;
	var day = 60 * 60 * 24;
	
	var pBarPercentWidthDecrease = Math.round(timeRemainingPB/(futureTime - scanStart) * 100);
	var pBarPercentWidthIncrease = 100 - pBarPercentWidthDecrease;

	var dayFloor = Math.floor(timeRemaining / day);
	var hourFloor = Math.floor((timeRemaining - dayFloor * day) / hour);
	var minuteFloor = Math.floor((timeRemaining - dayFloor * day - hourFloor * hour) / minute);
	var secondFloor = Math.floor((timeRemaining - dayFloor * day - hourFloor * hour - minuteFloor * minute));
	var hourFloorF = ("0" + hourFloor).slice(-2);	
	var minuteFloorF = ("0" + minuteFloor).slice(-2);	
	var secondFloorF = ("0" + secondFloor).slice(-2);

	var dayFloorPB = Math.floor(timeRemainingPB / day);
	var hourFloorPB = Math.floor((timeRemainingPB - dayFloorPB * day) / hour);
	var minuteFloorPB = Math.floor((timeRemainingPB - dayFloorPB * day - hourFloorPB * hour) / minute);
	var secondFloorPB = Math.floor((timeRemainingPB - dayFloorPB * day - hourFloorPB * hour - minuteFloorPB * minute));
	var hourFloorFPB = ("0" + hourFloorPB).slice(-2);	
	var minuteFloorFPB = ("0" + minuteFloorPB).slice(-2);	
	var secondFloorFPB = ("0" + secondFloorPB).slice(-2);

	var dayFloorTE = Math.floor(timeRemainingTE / day);
	var hourFloorTE = Math.floor((timeRemainingTE - dayFloorTE * day) / hour);
	var minuteFloorTE = Math.floor((timeRemainingTE - dayFloorTE * day - hourFloorTE * hour) / minute);
	var secondFloorTE = Math.floor((timeRemainingTE - dayFloorTE * day - hourFloorTE * hour - minuteFloorTE * minute));
	var hourFloorFTE = ("0" + hourFloorTE).slice(-2);	
	var minuteFloorFTE = ("0" + minuteFloorTE).slice(-2);	
	var secondFloorFTE = ("0" + secondFloorTE).slice(-2);

	var ScanCompleted = "<?php bpsPro_mscan_completed(); ?>";
	
	if ( futureTime > currentTime ) {
		
		if ( mscanStatus == 1 && secondFloorF <= 10 ) {
			window.location.reload(true);
			console.log( "Status: 1 : Future Time > Time : secondFloor <= 10 : " + secondFloorF );		
		}
		
		if ( mscanStatus == 1 && secondFloorF > 9 ) {
			document.getElementById("mscantimer").innerHTML = "Calculating Scan Time: " + hourFloorF + ":" + minuteFloorF + ":" + secondFloorF + "<br />" + "You can leave the MScan page while a scan is in progress and the scan will continue until it is completed.";
			console.log( "Status: 1 : Future Time > Time : Calculating Scan Time : secondFloorF > 9 : " + secondFloorF );			
		}

		if ( mscanStatus == 2 && totalFiles != "" || mscanStatus == 3 && totalFiles != ""  ) {	
			document.getElementById("MscanBar").style.width = pBarPercentWidthIncrease + '%';
			document.getElementById("MscanBar").innerHTML = pBarPercentWidthIncrease + '%';
			document.getElementById("mscantimer").innerHTML = "Scan Completion Time Remaining: " + hourFloorFPB + ":" + minuteFloorFPB + ":" + secondFloorFPB + " : Scanning " + totalFiles + " Files";
			console.log( "Status: 2 or 3: Future Time > Time : Total Files: not blank" );
		}

		if ( mscanStatus == 2 && totalFiles == "" ) {
			document.getElementById("MscanBar").style.width = pBarPercentWidthIncrease + '%';
			document.getElementById("MscanBar").innerHTML = pBarPercentWidthIncrease + '%';
			document.getElementById("mscantimer").innerHTML = "Processing Total File Count: Still scanning files: 00:00:" + secondFloorFTE;
			console.log( "Status: 2: Future Time > Time : Total Files: blank" );
		}
		
		if ( mscanStatus == 9 ) {	
			document.getElementById("MscanBar").style.width = pBarPercentWidthIncrease + '%';
			document.getElementById("MscanBar").innerHTML = pBarPercentWidthIncrease + '%';
			document.getElementById("mscantimer").innerHTML = "File Hash Maker Time Remaining: " + hourFloorFPB + ":" + minuteFloorFPB + ":" + secondFloorFPB + " : " + "Downloading and extracting zip files";
			console.log( "Status: 9 : Future Time > Time : Hash Maker : File Scanning Stopped" );
		}

	} else {

		if ( mscanStatus == 9 && futureTime < currentTime ) {
			window.location.reload(true);
			document.getElementById("mscantimer").innerHTML = ScanCompleted;
			console.log( "Status: 9 : Future Time < Time : Hash Maker Completed" );
		}

		if ( mscanStatus == 5 && futureTime < currentTime ) {
			window.location.reload(true);
			console.log( "Status: 5 : Future Time < Time" );
		}
	
		if ( mscanStatus == 4 && futureTime < currentTime && totalFiles == "" && scanStart != "" && scanStop != "stop" ) {
			window.location.reload(true);
			console.log( "Status: 4 : Future Time < Time : Total Files: blank : Start: not blank : Stop: not stop" );
		}
	
		if ( mscanStatus == 3 && futureTime < currentTime ) {
			window.location.reload(true);
			document.getElementById("mscantimer").innerHTML = ScanCompleted;
			console.log( "Status: 3 : Future Time < Time : Scan Completed" );
		}
	
		if ( mscanStatus == 2 && futureTime < currentTime ) {
			window.location.reload(true);
			console.log( "Status: 2 : Future Time < Time : No HTML is echoed: " + secondFloorF );
		}
	
		if ( mscanStatus == 1 && futureTime < currentTime && secondFloorF <= 10 && scanStart != "" ) {
			window.location.reload(true);
			document.getElementById("mscantimer").innerHTML = "Calculating Scan Time Exceeded: Still calculating estimated scan time: " + secondFloorFTE;
			console.log( "Status: 1 : Future Time < Time : secondFloorF <= 10 : Scan Start: not blank : Calculating Scan Time Exceeded: " + secondFloorF );
		}
	}	
}
/* ]]> */
</script>
</body>
</html>