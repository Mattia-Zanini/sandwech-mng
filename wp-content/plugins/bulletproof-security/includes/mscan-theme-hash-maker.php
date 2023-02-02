<?php

function bpsPro_theme_zip_download($mstime) {
global $wp_version;
	
	$time_start = microtime( true );
	
	set_time_limit($mstime);
	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	$mscan_log = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
	
	$handle = fopen( $mscan_log, 'a' );

	if ( ! is_dir( WP_CONTENT_DIR . '/bps-backup/theme-hashes' ) ) {
		mkdir( WP_CONTENT_DIR . '/bps-backup/theme-hashes', 0755, true );
		chmod( WP_CONTENT_DIR . '/bps-backup/theme-hashes/', 0755 );
	}

	$theme_hashes_dir = WP_CONTENT_DIR . '/bps-backup/theme-hashes';
	
	if ( ! is_dir( $theme_hashes_dir ) ) {
		
		fwrite( $handle, "Theme Zip File Download Error: The $theme_hashes_dir folder does not exist.\r\n" );
		fwrite( $handle, "Troubleshooting: Check that the Ownership or folder permissions for the /bps-backup/ folder. The /bps-backup/ folder should have 755 or 705 permissions and the Owner of the /bps-backup/ folder should be the same Owner as all of your other website folders.\r\n" );
		fclose($handle);
		return false;
	}

	$theme_hash_file = WP_CONTENT_DIR . '/bps-backup/theme-hashes/theme-hashes.php';
	$blank_hash_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/wp-hashes.php';

	if ( ! file_exists($theme_hash_file) ) {
		
		if ( ! copy($blank_hash_file, $theme_hash_file) ) {
			fwrite( $handle, "Theme Zip File Download Error: Unable to create the Theme hash file: $theme_hash_file\r\n" );
		}
	}

	fwrite( $handle, "Theme Zip File Download: Start Theme zip file downloads.\r\n" );

	$all_themes = wp_get_themes();
	$all_themes_array = array();

	foreach ( $all_themes as $key => $value ) {
			
		if ( ! empty($key) ) {
			$all_themes_array[] = $key . '.' . $value['Version'];
		}
	}

	$bps_mscan_theme_hash_version_check_array = array();
	$bps_mscan_theme_hash_paths_array = array();
	$bps_mscan_theme_hash_zip_file_array = array();

	$mscan_theme_hash = get_option('bulletproof_security_options_mscan_theme_hash');
	$mscan_theme_hash_new = get_option('bulletproof_security_options_mscan_t_hash_new');
	$tmp_file = '';
	$theme_zip_file_download = '';
	$theme_no_zip_array = array();
	
	foreach ( $all_themes_array as $key => $value ) {
		
		$theme_zip_file = $value . '.zip';
		$local_zip_file = WP_CONTENT_DIR . '/bps-backup/theme-hashes/' . $theme_zip_file;

		$theme_name = strstr($value, '.', true); 
		$theme_version = strstr($value, '.'); 
		$theme_version_nodot = substr($theme_version, 1); 

 		$bps_mscan_theme_hash_version_check_array[$theme_name] = $theme_version_nodot;
		$bps_mscan_theme_hash_paths_array[$theme_name][] = '';
		
		$theme_zip_file_url = 'https://downloads.wordpress.org/theme/' . $theme_zip_file;
		
		$response = wp_remote_get( $theme_zip_file_url );
	
		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			
			if ( $response['response']['code'] == '404' ) {
				
				$value = preg_replace( '/\.\d(.*)/', "", $value );
				$theme_no_zip_array[] = $value;
			}
		}

		@$bps_mscan_theme_hash_version_check = isset($mscan_theme_hash['bps_mscan_theme_hash_version_check']) ? $mscan_theme_hash['bps_mscan_theme_hash_version_check']["$theme_name"] : '';
		
		if ( $bps_mscan_theme_hash_version_check == $theme_version_nodot ) {
			
		} else {

			if ( ! file_exists($local_zip_file) ) {
	
				if ( file_exists($theme_hash_file) ) {
					
					$url = 'https://downloads.wordpress.org/theme/' . $theme_zip_file;
					$tmp_file = download_url( $url, $timeout = 300 );
			
					if ( is_wp_error( $tmp_file ) ) {

						fwrite( $handle, "Theme Zip File Download: WP_Error: Unable to download Theme zip file: $theme_zip_file from WordPress.org.\r\n" );

					} else {
					
						if ( ! copy( $tmp_file, $local_zip_file )  ) {
							fwrite( $handle, "Theme Zip File Download: Unable to download this Plugin zip file: $theme_zip_file\r\n" );
						} else {
							fwrite( $handle, "Theme Zip File Download: Zip file download successful: $theme_zip_file\r\n" );
						
							$theme_zip_file_download = '1';						
						}
						
						unlink( $tmp_file );
					}
				}
			}
		}
	
		if ( file_exists( $local_zip_file ) ) {
			$zip_file = 'yes';
		} else {
			$zip_file = 'no';			
		}
		
		$bps_mscan_theme_hash_zip_file_array[$theme_name] = $zip_file;	
	}

	$mscan_nodownload = get_option('bulletproof_security_options_mscan_nodownload');
	$bps_plugin_nodownload = isset( $mscan_nodownload['bps_plugin_nodownload']) ? $mscan_nodownload['bps_plugin_nodownload'] : '';
	
	$mscan_nodownload_options = array(
	'bps_plugin_nodownload' 	=> $bps_plugin_nodownload, 
	'bps_theme_nodownload' 		=> $theme_no_zip_array 
	);

	foreach( $mscan_nodownload_options as $key => $value ) {
		update_option('bulletproof_security_options_mscan_nodownload', $mscan_nodownload_options);
	}

	$mscan_theme_hash_options_db = 'bulletproof_security_options_mscan_theme_hash';

	if ( ! get_option( $mscan_theme_hash_options_db ) ) {	
	
		$mscan_theme_hash_options = array(
		'bps_mscan_theme_hash_version_check' 	=> $bps_mscan_theme_hash_version_check_array, 
		'bps_mscan_theme_hash_paths' 			=> $bps_mscan_theme_hash_paths_array, 
		'bps_mscan_theme_hash_zip_file'			=> $bps_mscan_theme_hash_zip_file_array 
		);

		foreach( $mscan_theme_hash_options as $key => $value ) {
			update_option('bulletproof_security_options_mscan_theme_hash', $mscan_theme_hash_options);
		}

	} else {
		
		delete_option('bulletproof_security_options_mscan_t_hash_new');

		$mscan_theme_hash_options_new = array(
		'bps_mscan_theme_hash_version_check_new'	=> $bps_mscan_theme_hash_version_check_array, 
		'bps_mscan_theme_hash_paths_new' 			=> $bps_mscan_theme_hash_paths_array, 
		'bps_mscan_theme_hash_zip_file_new'			=> $bps_mscan_theme_hash_zip_file_array 
		);		

		foreach( $mscan_theme_hash_options_new as $key => $value ) {
			update_option('bulletproof_security_options_mscan_t_hash_new', $mscan_theme_hash_options_new);
		}

		$mscan_theme_hash = get_option('bulletproof_security_options_mscan_theme_hash');
		$mscan_theme_hash_new = get_option('bulletproof_security_options_mscan_t_hash_new');
		
		$theme_hash_version_check_update_array = array();
		
		foreach ( $mscan_theme_hash['bps_mscan_theme_hash_version_check'] as $key => $value ) {
			
			foreach ( $mscan_theme_hash_new['bps_mscan_theme_hash_version_check_new'] as $key_new => $value_new ) {
				
				if ( $key == $key_new ) {
		
					$theme_hash_version_check_update_array[$key] = $value_new;
				}
			}
		}

		$array_diff_key_theme_hash_version = array_diff_key($mscan_theme_hash_new['bps_mscan_theme_hash_version_check_new'], $mscan_theme_hash['bps_mscan_theme_hash_version_check']);
		$array_merge_new_theme_hash_version = array_merge($theme_hash_version_check_update_array, $array_diff_key_theme_hash_version);

		$mscan_theme_hash_options = array(
		'bps_mscan_theme_hash_version_check' 	=> $array_merge_new_theme_hash_version, 
		'bps_mscan_theme_hash_paths' 			=> $mscan_theme_hash['bps_mscan_theme_hash_paths'], 
		'bps_mscan_theme_hash_zip_file'			=> $mscan_theme_hash['bps_mscan_theme_hash_zip_file'] 
		);

		foreach( $mscan_theme_hash_options as $key => $value ) {
			update_option('bulletproof_security_options_mscan_theme_hash', $mscan_theme_hash_options);
		}
	}

	if ( $theme_zip_file_download == '1' ) {
	
		$theme_hash_folder = WP_CONTENT_DIR . '/bps-backup/theme-hashes/';
		$total_zip_files = preg_grep('~\.(zip)$~', scandir($theme_hash_folder));
		
		$total_zip_files_array = array();
		
		foreach ( $total_zip_files as $zip_file ) {
			$total_zip_files_array[] = $zip_file;
		}
		
		$zip_files_array_count = count($total_zip_files_array);
		$MScan_status = get_option('bulletproof_security_options_MScan_status');
		
		if ( $zip_files_array_count == 0 ) {
			
			$MScan_status_db = array( 
			'bps_mscan_time_start' 					=> $MScan_status['bps_mscan_time_start'], 
			'bps_mscan_time_stop' 					=> $MScan_status['bps_mscan_time_stop'], 
			'bps_mscan_time_end' 					=> $MScan_status['bps_mscan_time_end'], 
			'bps_mscan_time_remaining' 				=> $MScan_status['bps_mscan_time_remaining'], 
			'bps_mscan_status' 						=> '9', 
			'bps_mscan_last_scan_timestamp' 		=> $MScan_status['bps_mscan_last_scan_timestamp'], 
			'bps_mscan_total_time' 					=> $MScan_status['bps_mscan_total_time'], 
			'bps_mscan_total_website_files' 		=> '', 
			'bps_mscan_total_wp_core_files' 		=> $MScan_status['bps_mscan_total_wp_core_files'], 
			'bps_mscan_total_non_image_files' 		=> $MScan_status['bps_mscan_total_non_image_files'], 
			'bps_mscan_total_image_files' 			=> '', 
			'bps_mscan_total_all_scannable_files' 	=> 'Error: Theme Zip File Download Failed', 
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

			$mscan_hash_status_options = get_option('bulletproof_security_options_mscan_hash_status');
			
			$mscan_wp_core_hash_status 	= isset($mscan_hash_status_options['mscan_wp_core_hash_status']) ? $mscan_hash_status_options['mscan_wp_core_hash_status'] : '';
			$mscan_wp_core_hash_count 	= isset($mscan_hash_status_options['mscan_wp_core_hash_count']) ? $mscan_hash_status_options['mscan_wp_core_hash_count'] : '';
			$mscan_plugin_hash_status 	= isset($mscan_hash_status_options['mscan_plugin_hash_status']) ? $mscan_hash_status_options['mscan_plugin_hash_status'] : '';
			$mscan_plugin_hash_count 	= isset($mscan_hash_status_options['mscan_plugin_hash_count']) ? $mscan_hash_status_options['mscan_plugin_hash_count'] : '';
			$mscan_theme_hash_status 	= '0';
			$mscan_theme_hash_count 	= '0';
	
			$mscan_hash_status_options_db = array( 
			'mscan_wp_core_hash_status' => $mscan_wp_core_hash_status, 
			'mscan_wp_core_hash_count' 	=> $mscan_wp_core_hash_count, 
			'mscan_plugin_hash_status' 	=> $mscan_plugin_hash_status, 
			'mscan_plugin_hash_count' 	=> $mscan_plugin_hash_count, 
			'mscan_theme_hash_status' 	=> $mscan_theme_hash_status, 
			'mscan_theme_hash_count' 	=> $mscan_theme_hash_count 
			);		
				
			foreach( $mscan_hash_status_options_db as $key => $value ) {
				update_option('bulletproof_security_options_mscan_hash_status', $mscan_hash_status_options_db);
			}			
			
			fwrite( $handle, "Theme Zip File Download: Error: Unable to download or copy Theme zip files from WordPress.org.\r\n" );

			return false;

		} else {
			
			$mscan_hash_status_options = get_option('bulletproof_security_options_mscan_hash_status');
			
			$mscan_wp_core_hash_status 	= isset($mscan_hash_status_options['mscan_wp_core_hash_status']) ? $mscan_hash_status_options['mscan_wp_core_hash_status'] : '';
			$mscan_wp_core_hash_count 	= isset($mscan_hash_status_options['mscan_wp_core_hash_count']) ? $mscan_hash_status_options['mscan_wp_core_hash_count'] : '';
			$mscan_plugin_hash_status 	= isset($mscan_hash_status_options['mscan_plugin_hash_status']) ? $mscan_hash_status_options['mscan_plugin_hash_status'] : '';
			$mscan_plugin_hash_count 	= isset($mscan_hash_status_options['mscan_plugin_hash_count']) ? $mscan_hash_status_options['mscan_plugin_hash_count'] : '';
			$mscan_theme_hash_status 	= '1';
			$mscan_theme_hash_count 	= $zip_files_array_count;
	
			$mscan_hash_status_options_db = array( 
			'mscan_wp_core_hash_status' => $mscan_wp_core_hash_status, 
			'mscan_wp_core_hash_count' 	=> $mscan_wp_core_hash_count, 
			'mscan_plugin_hash_status' 	=> $mscan_plugin_hash_status, 
			'mscan_plugin_hash_count' 	=> $mscan_plugin_hash_count, 
			'mscan_theme_hash_status' 	=> $mscan_theme_hash_status, 
			'mscan_theme_hash_count' 	=> $mscan_theme_hash_count 
			);		
				
			foreach( $mscan_hash_status_options_db as $key => $value ) {
				update_option('bulletproof_security_options_mscan_hash_status', $mscan_hash_status_options_db);
			}				
			
			fwrite( $handle, "Theme Zip File Download: Total number of zip files downloaded: $zip_files_array_count.\r\n" );		
		}
	}

	$time_end = microtime( true );
	$download_time = $time_end - $time_start;

	$hours = (int)($download_time / 60 / 60);
	$minutes = (int)($download_time / 60) - $hours * 60;
	$seconds = (int)$download_time - $hours * 60 * 60 - $minutes * 60;
	$hours_format = $hours == 0 ? "00" : $hours;
	$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
	$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);

	$download_time_log = 'Theme Zip File Download Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;

	fwrite( $handle, "$download_time_log\r\n" );
	fclose($handle);
	
	return true;
}

function bpsPro_theme_zip_extractor() {
global $wp_version;
	
	$time_start = microtime( true );

	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	$mscan_log = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';

	$handle = fopen( $mscan_log, 'a' );
	
	$theme_hash_folder = WP_CONTENT_DIR . '/bps-backup/theme-hashes/';
	$zip_files = preg_grep('~\.(zip)$~', scandir($theme_hash_folder));
	
	if ( class_exists('ZipArchive') ) {	

		fwrite( $handle, "Theme Zip File Extraction: Start ZipArchive zip file extraction.\r\n" );
		
		foreach ( $zip_files as $zip_file ) {
		
			$zip_file_path = $theme_hash_folder . $zip_file;
			
			$ThemeZip = new ZipArchive;
			$res = $ThemeZip->open( $zip_file_path );
 
			if ( $res === TRUE ) {
		
				$ThemeZip->extractTo( WP_CONTENT_DIR . '/bps-backup/theme-hashes/' );
				$ThemeZip->close();
				
				fwrite( $handle, "Theme Zip File Extraction ZipArchive: Zip file extraction successful: $zip_file.\r\n" );
			} else {
				fwrite( $handle, "Theme Zip File Extraction ZipArchive Error: Unable to extract this Theme zip file: $zip_file.\r\n" );
			}
		}
			
		$time_end = microtime( true );
		$zip_extract_time = $time_end - $time_start;

		$hours = (int)($zip_extract_time / 60 / 60);
		$minutes = (int)($zip_extract_time / 60) - $hours * 60;
		$seconds = (int)$zip_extract_time - $hours * 60 * 60 - $minutes * 60;
		$hours_format = $hours == 0 ? "00" : $hours;
		$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
		$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);

		$zip_extract_time_log = 'Theme Zip File Extraction Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;

		fwrite( $handle, "$zip_extract_time_log\r\n" );
		fclose($handle);			
		
		return true;

	} else { 
		
		fwrite( $handle, "Theme Zip File Extraction: Start PclZip zip file extraction.\r\n" );

		define( 'PCLZIP_TEMPORARY_DIR', WP_CONTENT_DIR . '/bps-backup/theme-hashes/' );
		require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
	
		foreach ( $zip_files as $zip_file ) {
		
			$zip_file_path = $theme_hash_folder . $zip_file;

			$archive = new PclZip( $zip_file_path );
		
			if ( $archive->extract( PCLZIP_OPT_PATH, WP_CONTENT_DIR . '/bps-backup/theme-hashes', PCLZIP_OPT_REMOVE_PATH, WP_CONTENT_DIR . '/bps-backup/theme-hashes' ) ) {
				fwrite( $handle, "Theme Zip File Extraction PclZip: Zip file extracted successfully: $zip_file.\r\n" );
			} else {
				fwrite( $handle, "Theme Zip File Extraction PclZip Error: Unable to unzip this Theme zip file: $zip_file.\r\n" );
			}
		}
	
		$time_end = microtime( true );
		$zip_extract_time = $time_end - $time_start;

		$hours = (int)($zip_extract_time / 60 / 60);
		$minutes = (int)($zip_extract_time / 60) - $hours * 60;
		$seconds = (int)$zip_extract_time - $hours * 60 * 60 - $minutes * 60;
		$hours_format = $hours == 0 ? "00" : $hours;
		$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
		$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);

		$zip_extract_time_log = 'Theme Zip File Extraction Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;

		fwrite( $handle, "$zip_extract_time_log\r\n" );
		fclose($handle);			
		
		return true;	
	}
}

function bpsPro_wp_theme_crlf_format_conversion() {
	
	$mscan_log = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
	$handle = fopen( $mscan_log, 'a' );

	$wp_theme_array = array( 'twentythirty', 'twentytwentynine', 'twentytwentyeight', 'twentytwentyseven', 'twentytwentysix', 'twentytwentyfive', 'twentytwentyfour', 'twentytwentythree', 'twentytwentytwo', 'twentytwentyone', 'twentytwenty', 'twentynineteen', 'twentyseventeen', 'twentysixteen', 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentytwelve', 'twentyeleven', 'twentyten' );

	$source = WP_CONTENT_DIR . '/bps-backup/theme-hashes/';
	
	$theme_dir_path_array = array();

	if ( is_dir($source) ) {
		$iterator = new DirectoryIterator($source);
			
		foreach ( $iterator as $files ) {
			if ( $files->isDir() && ! $files->isDot() ) {
				
				if ( in_array( $files->getBasename(), $wp_theme_array ) ) {
				
					$theme_dir_path_array[] = $files->getPathname();
				}
			}
		}
	}

	if ( ! empty($theme_dir_path_array) ) {	
	
		fwrite( $handle, "Theme MD5 File Hash Maker: WP Theme CR LF conversion to LF format.\r\n" );
		
		foreach ( $theme_dir_path_array as $theme_dir_path ) {
			
			$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($theme_dir_path), RecursiveIteratorIterator::SELF_FIRST);
			
			foreach ( $objects as $files ) {
				
				if ( $files->isFile() ) {
					
					$ext = pathinfo( strtolower($files->getPathname()), PATHINFO_EXTENSION );
					
					if ( $ext == 'php' || $ext == 'js' || $ext == 'json' || $ext == 'txt' || $ext == 'css' || $ext == 'scss' || $ext == 'md' || $ext == 'html' ) {					
					
						$file_contents = file_get_contents( $files->getPathname() );
						$crlf_conversion = str_replace( "\r\n", "\n", $file_contents );
		
						file_put_contents( $files->getPathname(), $crlf_conversion );
					}
				}
			}
		}	

		$source_live =  get_theme_root();
		
		$theme_dir_path_live_array = array();
	
		if ( is_dir($source_live) ) {
			$iterator = new DirectoryIterator($source_live);
				
			foreach ( $iterator as $files ) {
				if ( $files->isDir() && ! $files->isDot() ) {
					
					if ( in_array( $files->getBasename(), $wp_theme_array ) ) {
					
						$theme_dir_path_live_array[] = $files->getPathname();
					}
				}
			}
		}
	
		if ( ! empty($theme_dir_path_live_array) ) {	
		
			foreach ( $theme_dir_path_live_array as $theme_dir_path_live ) {
				
				$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($theme_dir_path_live), RecursiveIteratorIterator::SELF_FIRST);
				
				foreach ( $objects as $files ) {
					
					if ( $files->isFile() ) {
						
						$ext = pathinfo( strtolower($files->getPathname()), PATHINFO_EXTENSION );
						
						if ( $ext == 'php' || $ext == 'js' || $ext == 'json' || $ext == 'txt' || $ext == 'css' || $ext == 'scss' || $ext == 'md' || $ext == 'html' ) {					
						
							$file_contents = file_get_contents( $files->getPathname() );
							$crlf_conversion = str_replace( "\r\n", "\n", $file_contents );
							
							file_put_contents( $files->getPathname(), $crlf_conversion );
						}
					}
				}
			}	
		}
	}
}

function bpsPro_theme_hash_maker() {
global $wp_version;
	
	$time_start = microtime( true );

	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	$mscan_log = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
	
	$handle = fopen( $mscan_log, 'a' );

	if ( ! is_array( spl_classes() ) ) {
		fwrite( $handle, "Theme MD5 File Hash Maker Error: The Standard PHP Library (SPL) is Not available/installed. Unable to create Theme MD5 hash file.\r\n" );
		fwrite( $handle, "Solution: Contact your web host and ask them to install the Standard PHP Library (SPL) on your server.\r\n" );
		fclose($handle);		
		
		return false;
	}

	$theme_hash_file = WP_CONTENT_DIR . '/bps-backup/theme-hashes/theme-hashes.php';
	
	if ( ! file_exists( $theme_hash_file ) ) {
		fwrite( $handle, "Theme MD5 File Hash Maker Error: The $theme_hash_file file does not exist.\r\n" );
		fwrite( $handle, "Troubleshooting: Check the Ownership or folder permissions for the /bps-backup/theme-hashes/ folder. The /bps-backup/theme-hashes/ folder should have 755 or 705 permissions and the Owner of the /bps-backup/theme-hashes/ folder should be the same Owner as all of your other website folders.\r\n" );
		fclose($handle);
		
		return false;
	}

	fwrite( $handle, "Theme MD5 File Hash Maker: Start creating the theme-hashes.php file.\r\n" );

	bpsPro_wp_theme_crlf_format_conversion();

	$source = WP_CONTENT_DIR . '/bps-backup/theme-hashes/';
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	
	$theme_dir_path_array = array();
	$theme_folder_name_array = array();

	if ( is_dir($source) ) {
		$iterator = new DirectoryIterator($source);
			
		foreach ( $iterator as $files ) {
			if ( $files->isDir() && ! $files->isDot() ) {

				$theme_dir_path_array[] = $files->getPathname();
			}
		}
	}

	$theme_name_key_array = array();

	if ( ! empty($theme_dir_path_array) ) {	
	
		$str1 = WP_CONTENT_DIR . '/bps-backup/theme-hashes/';
		$str2 = WP_CONTENT_DIR . '/bps-backup/theme-hashes\\';
		$str3 = WP_CONTENT_DIR . '\bps-backup\theme-hashes\\';
			
		$filePath = array();
	
		foreach ( $theme_dir_path_array as $theme_dir_path ) {
			
			$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($theme_dir_path), RecursiveIteratorIterator::SELF_FIRST);
			
			foreach ( $objects as $files ) {
				
				if ( $files->isFile() ) {
					
					$filePath[] = str_replace( array( $str1, $str2, $str3 ), "", $files->getPathname() ). '\' => \'' . md5_file($files->getPathname());
				}
			}
		}
		
		$mscan_theme_hash = get_option('bulletproof_security_options_mscan_theme_hash');
		$mscan_theme_hash_new = get_option('bulletproof_security_options_mscan_t_hash_new');
		
		foreach ( $filePath as $key => $value ) {
			
			$key_value = preg_replace( '/(\\\\.*|\/.*)/', "", $value);
	
			if ( ! empty($value) ) {
				$theme_name_key_array[$key_value][] = $value;
			}
		}
	}
	
	$theme_hash_folder = WP_CONTENT_DIR . '/bps-backup/theme-hashes/';
	$theme_files = preg_grep('~\.(php)$~', scandir($theme_hash_folder));
	
	foreach ( $theme_files as $theme_file ) {
		
		if ( $theme_file != 'theme-hashes.php' ) {
			
			fwrite( $handle, "Theme MD5 File Hash Maker Error: Files exist in the $theme_hash_folder that should not be there. If you copied theme files into this folder then delete them. Only Theme Zip files or extracted Theme folders should be in this folder.\r\n" );
			
			$MScan_status = get_option('bulletproof_security_options_MScan_status');

			$MScan_status_db = array( 
			'bps_mscan_time_start' 					=> $MScan_status['bps_mscan_time_start'], 
			'bps_mscan_time_stop' 					=> $MScan_status['bps_mscan_time_stop'], 
			'bps_mscan_time_end' 					=> $MScan_status['bps_mscan_time_end'], 
			'bps_mscan_time_remaining' 				=> $MScan_status['bps_mscan_time_remaining'], 
			'bps_mscan_status' 						=> '9', 
			'bps_mscan_last_scan_timestamp' 		=> $MScan_status['bps_mscan_last_scan_timestamp'], 
			'bps_mscan_total_time' 					=> $MScan_status['bps_mscan_total_time'], 
			'bps_mscan_total_website_files' 		=> '', 
			'bps_mscan_total_wp_core_files' 		=> $MScan_status['bps_mscan_total_wp_core_files'], 
			'bps_mscan_total_non_image_files' 		=> $MScan_status['bps_mscan_total_non_image_files'], 
			'bps_mscan_total_image_files' 			=> '', 
			'bps_mscan_total_all_scannable_files' 	=> 'Error: Files found in the theme-hashes folder', 
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
			
			return false;
		}
	}

	$mscan_theme_hash_options_db_new = 'bulletproof_security_options_mscan_t_hash_new';
	$mscan_theme_hash = get_option('bulletproof_security_options_mscan_theme_hash');
	$mscan_theme_hash_new = get_option('bulletproof_security_options_mscan_t_hash_new');
	
	if ( ! get_option( $mscan_theme_hash_options_db_new ) ) {

		$mscan_theme_hash_options = array(
		'bps_mscan_theme_hash_version_check' 	=> $mscan_theme_hash['bps_mscan_theme_hash_version_check'], 
		'bps_mscan_theme_hash_paths' 			=> $theme_name_key_array, 
		'bps_mscan_theme_hash_zip_file'			=> $mscan_theme_hash['bps_mscan_theme_hash_zip_file'] 
		);

		foreach( $mscan_theme_hash_options as $key => $value ) {
			update_option('bulletproof_security_options_mscan_theme_hash', $mscan_theme_hash_options);
		}

	} else { 
	
		if ( ! empty($theme_dir_path_array) ) {
			
			$mscan_theme_hash_options = array(
			'bps_mscan_theme_hash_version_check_new' 	=> $mscan_theme_hash_new['bps_mscan_theme_hash_version_check_new'], 
			'bps_mscan_theme_hash_paths_new' 			=> $theme_name_key_array, 
			'bps_mscan_theme_hash_zip_file_new'			=> $mscan_theme_hash_new['bps_mscan_theme_hash_zip_file_new'] 
			);		
	
			foreach( $mscan_theme_hash_options as $key => $value ) {
				update_option('bulletproof_security_options_mscan_t_hash_new', $mscan_theme_hash_options);
			}
		}
	}

	$mscan_theme_hash = get_option('bulletproof_security_options_mscan_theme_hash');
	$mscan_theme_hash_new = get_option('bulletproof_security_options_mscan_t_hash_new');
	
	$all_themes = wp_get_themes();
	$all_themes_array = array();

	foreach ( $all_themes as $key => $value ) {
			
		if ( ! empty($key) ) {
			$all_themes_array[] = $key;
		}
	}

	$mscan_theme_hash_new_array_keys = array();
	
	if ( get_option( 'bulletproof_security_options_mscan_t_hash_new' ) ) {

		foreach ( $mscan_theme_hash_new['bps_mscan_theme_hash_paths_new'] as $key => $value ) {
			
			foreach ( $value as $inner_key => $inner_value ) {
			
				if ( ! empty($inner_value) ) {
					$mscan_theme_hash_new_array_keys[] = $key;
				}
			}
		}		
	}

	$theme_hash_removal_array = array();

	if ( ! empty( $mscan_theme_hash['bps_mscan_theme_hash_paths'] ) ) {
	
		foreach ( $mscan_theme_hash['bps_mscan_theme_hash_paths'] as $key => $value ) {
			
			if ( ! in_array( $key, $all_themes_array ) ) {
				unset($value);
			}
	
			if ( in_array( $key, $mscan_theme_hash_new_array_keys ) ) {
				unset($value);
			}
			
			if ( ! empty($value) ) {
				$theme_hash_removal_array[$key] = $value;
			}
		}
	}
	
	$mscan_theme_hash_options = array(
	'bps_mscan_theme_hash_version_check' 	=> $mscan_theme_hash['bps_mscan_theme_hash_version_check'], 
	'bps_mscan_theme_hash_paths' 			=> $theme_hash_removal_array, 
	'bps_mscan_theme_hash_zip_file'			=> $mscan_theme_hash['bps_mscan_theme_hash_zip_file'] 
	);

	foreach( $mscan_theme_hash_options as $key => $value ) {
		update_option('bulletproof_security_options_mscan_theme_hash', $mscan_theme_hash_options);
	}

	$mscan_theme_hash = get_option('bulletproof_security_options_mscan_theme_hash');
	$mscan_theme_hash_new = get_option('bulletproof_security_options_mscan_t_hash_new');

	if ( empty($mscan_theme_hash_new_array_keys) ) {
		
		$result = $mscan_theme_hash['bps_mscan_theme_hash_paths'];
		
	} else {
		
		$result = array_merge($mscan_theme_hash_new['bps_mscan_theme_hash_paths_new'], $mscan_theme_hash['bps_mscan_theme_hash_paths']);
	}
	
	$mscan_theme_hash_options = array(
	'bps_mscan_theme_hash_version_check' 	=> $mscan_theme_hash['bps_mscan_theme_hash_version_check'], 
	'bps_mscan_theme_hash_paths' 			=> $result, 
	'bps_mscan_theme_hash_zip_file'			=> $mscan_theme_hash['bps_mscan_theme_hash_zip_file'] 
	);

	foreach( $mscan_theme_hash_options as $key => $value ) {
		update_option('bulletproof_security_options_mscan_theme_hash', $mscan_theme_hash_options);
	}

	$mscan_theme_hash = get_option('bulletproof_security_options_mscan_theme_hash');
	$final_result = $mscan_theme_hash['bps_mscan_theme_hash_paths'];

	$mscan_theme_hash_options_db = 'bulletproof_security_options_mscan_theme_hash';

	if ( ! empty($mscan_theme_hash_new_array_keys ) || ! get_option( 'bulletproof_security_options_mscan_t_hash_new' ) ) {

		$theme_hashes_file = WP_CONTENT_DIR . '/bps-backup/theme-hashes/theme-hashes.php';
		
		$handleH = fopen( $theme_hashes_file, 'wb' );
		fwrite( $handleH, "<?php\n" );
		fwrite( $handleH, "\$theme_hashes = array(\n" );
		
		foreach ( $final_result as $key => $value ) {
			
			fwrite( $handleH, "## BEGIN " . $key . " ##" . "\n" );
			
			foreach ( $value as $key2 => $value2 ) {
			
				fwrite( $handleH, "'" . $value2 . "', " . "\n" );
			}
		
			fwrite( $handleH, "## END " . $key . " ##" . "\n" );	
		}
	
		fwrite( $handleH, ");\n" );
		fwrite( $handleH, "?>" );	
		fclose( $handleH );
	
		fwrite( $handle, "Theme MD5 File Hash Maker & Cleanup: theme-hashes.php file created.\r\n" );
		fwrite( $handle, "Theme MD5 File Hash Maker & Cleanup: Start /bps-backup/theme-hashes/ folder cleanup.\r\n" );
		
		$theme_hash_folder = WP_CONTENT_DIR . '/bps-backup/theme-hashes/';
		$theme_hash_file = WP_CONTENT_DIR . '/bps-backup/theme-hashes/theme-hashes.php';
		
		if ( is_dir($theme_hash_folder) ) {
			
			$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($theme_hash_folder), RecursiveIteratorIterator::CHILD_FIRST);		
			
			foreach ( $iterator as $file ) {
				
				if ( $file->isFile() ) {
					
					if ( $file->getFilename() != 'theme-hashes.php' ) {
						unlink( $file->getRealPath() );
					}
	
				} else {
				
					if ( $file->isDir() )	{
						@rmdir( $file->getRealPath() );
					}
				}
			}
		}
	}
	
	$time_end = microtime( true );
	$hash_maker_time = $time_end - $time_start;

	$hours = (int)($hash_maker_time / 60 / 60);
	$minutes = (int)($hash_maker_time / 60) - $hours * 60;
	$seconds = (int)$hash_maker_time - $hours * 60 * 60 - $minutes * 60;
	$hours_format = $hours == 0 ? "00" : $hours;
	$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
	$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);

	$hash_maker_time_log = 'Theme MD5 File Hash Maker & Cleanup Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;

	if ( ! empty($mscan_theme_hash_new_array_keys ) || ! get_option( 'bulletproof_security_options_mscan_t_hash_new' ) ) {
		fwrite( $handle, "Theme MD5 File Hash Maker & Cleanup: Theme Zip files deleted.\r\n" );
		fwrite( $handle, "Theme MD5 File Hash Maker & Cleanup: Extracted Theme folders deleted.\r\n" );
	}

	fwrite( $handle, "$hash_maker_time_log\r\n" );
	fclose($handle);

	return true;
}

?>