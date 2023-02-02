<?php ob_start(); ?>
<?php session_cache_limiter('nocache'); ?>
<?php session_start(); ?>
<?php error_reporting(0); ?>
<?php session_destroy(); ?>
<?php 
# BEGIN HEADERS
header($_SERVER['SERVER_PROTOCOL'].' 400 Bad Request', true, 400);
header('Status: 400 Bad Request');
header('Content-type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate' ); 
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache' );
# END HEADERS
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>400 Bad Request</title>

<style type="text/css">
<!--
body { 
	background-color:#fff;
	line-height:normal;
	/* If you want to add a background image uncomment the CSS properties below */
	/* background-image:url(http://www.example.com/path-to-some-image-file/example-image-file.jpg); /*
	/* background-repeat:repeat; */
}

#bpsMessage {
	text-align:center; 
	background-color:#fff;
	padding:0px;
}

p {
    font-family:Verdana, Arial, Helvetica, Tahoma, sans-serif;
	line-height:21px;
	font-size:14px;
	font-weight:normal;
}
-->
</style>

</head>

<body>

<div id="bpsMessage">
	<!-- This code needs to be standard php code (not WP code) in case wp-load.php is not loaded -->
    <?php 
	$http_status_code = '<p style="font-size:21px;font-weight:600">400 Bad Request Error</p>';
	$message = '<p>If you arrived here due to a search or clicking on a link click your <br>Browser\'s back button to return to the previous page. Thank you.</p>';
	$bps_hostname = '<p>Website: ' . htmlspecialchars( $_SERVER['SERVER_NAME'], ENT_QUOTES ) . '</p>';
	$ip_address = '<p>Your IP Address: ' .  htmlspecialchars( $_SERVER['REMOTE_ADDR'], ENT_QUOTES ) . '</p>';
	$bps_plugin_footer = '<p>BPS Plugin 400 Error Page</p>';
	
	echo $http_status_code . $message . $bps_hostname . $ip_address . $bps_plugin_footer;
	?>

</div>

<?php 

if ( file_exists( dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php' ) ) {
	require_once '../../../wp-load.php';
} else {
	ob_end_flush();
	return;
}

$bpsPro_http_referer = false;

if ( array_key_exists('HTTP_REFERER', $_SERVER) ) {
	$bpsPro_http_referer = $_SERVER['HTTP_REFERER'];
}

$bpsPro_http_user_agent = false;

if ( array_key_exists('HTTP_USER_AGENT', $_SERVER) ) {
	$bpsPro_http_user_agent = $_SERVER['HTTP_USER_AGENT'];
}

	$bpsProLog = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';
	$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	
	$query_string = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);

	if ( ! get_option( 'gmt_offset' ) ) {
		$timestamp = date("F j, Y g:i a", time() );
	} else {
		$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' - ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	}	 

	$event = 'The request could not be understood by the server due to malformed syntax.';
	$solution = 'N/A - Malformed Request - Not an Attack';	
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

		$log_contents = "\r\n" . '[400 POST Bad Request: ' . $timestamp . ']' . "\r\n" . 'BPS: ' . $bps_version . "\r\n" . 'WP: ' . $wp_version . "\r\n" . 'Event Code: ' . $event . "\r\n" . 'Solution: ' . $solution . "\r\n" . 'REMOTE_ADDR: '.$bpsPro_remote_addr . "\r\n" . 'Host Name: ' . $hostname . "\r\n" . 'SERVER_PROTOCOL: ' . $_SERVER['SERVER_PROTOCOL'] . "\r\n" . 'HTTP_CLIENT_IP: ' . $bpsPro_http_client_ip . "\r\n" . 'HTTP_FORWARDED: ' . $bpsPro_http_forwarded . "\r\n" . 'HTTP_X_FORWARDED_FOR: ' . $bpsPro_http_x_forwarded_for . "\r\n" . 'HTTP_X_CLUSTER_CLIENT_IP: ' . $bpsPro_http_x_cluster_client_ip."\r\n" . 'REQUEST_METHOD: '.$_SERVER['REQUEST_METHOD']."\r\n" . 'HTTP_REFERER: '.$bpsPro_http_referer."\r\n" . 'REQUEST_URI: '.$_SERVER['REQUEST_URI']."\r\n" . 'QUERY_STRING: '.$query_string."\r\n" . 'HTTP_USER_AGENT: '.$bpsPro_http_user_agent."\r\n";

		if ( is_writable( $bpsProLog ) ) {
	
			if ( !$handle = fopen( $bpsProLog, 'a' ) ) {
				 exit;
			}
		
			if ( fwrite( $handle, $log_contents) === false ) {
				exit;
			}

    	fclose($handle);
		}
	}

	if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
	
		$log_contents = "\r\n" . '[400 GET Bad Request: ' . $timestamp . ']' . "\r\n" . 'BPS: ' . $bps_version . "\r\n" . 'WP: ' . $wp_version . "\r\n" . 'Event Code: ' . $event . "\r\n" . 'Solution: ' . $solution . "\r\n" . 'REMOTE_ADDR: '.$bpsPro_remote_addr . "\r\n" . 'Host Name: ' . $hostname . "\r\n" . 'SERVER_PROTOCOL: ' . $_SERVER['SERVER_PROTOCOL'] . "\r\n" . 'HTTP_CLIENT_IP: ' . $bpsPro_http_client_ip . "\r\n" . 'HTTP_FORWARDED: ' . $bpsPro_http_forwarded . "\r\n" . 'HTTP_X_FORWARDED_FOR: ' . $bpsPro_http_x_forwarded_for . "\r\n" . 'HTTP_X_CLUSTER_CLIENT_IP: ' . $bpsPro_http_x_cluster_client_ip."\r\n" . 'REQUEST_METHOD: '.$_SERVER['REQUEST_METHOD']."\r\n" . 'HTTP_REFERER: '.$bpsPro_http_referer."\r\n" . 'REQUEST_URI: '.$_SERVER['REQUEST_URI']."\r\n" . 'QUERY_STRING: '.$query_string."\r\n" . 'HTTP_USER_AGENT: '.$bpsPro_http_user_agent."\r\n";

		if ( is_writable( $bpsProLog ) ) {
	
			if ( !$handle = fopen( $bpsProLog, 'a' ) ) {
				 exit;
			}
		
			if ( fwrite( $handle, $log_contents) === false ) {
				exit;
			}
	
		fclose($handle);
		}
	}
?>
</body>
</html>
<?php ob_end_flush(); ?>