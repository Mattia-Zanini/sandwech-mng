<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! function_exists ('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}
	
// Get the Current|Last Modifed time of the MScan Log File - Seconds - Wizard & formality since no Dashboard alerts
function bpsPro_MScan_LogLastMod_wp_secs() {
$filename = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
$gmt_offset = get_option( 'gmt_offset' ) * 3600;

if ( file_exists($filename) ) {
	$last_modified = date( "F d Y H:i:s", filemtime($filename) + $gmt_offset );
	return $last_modified;
	}
}

function bpsPro_mscan_scan_processing() {

	if ( isset( $_POST['post_var'] ) && $_POST['post_var'] == 'bps_mscan' && current_user_can('manage_options') ) {
		check_ajax_referer( 'bulletproof_security_mscan_start', 'mscan_nonce' );

		$MScanStop = WP_CONTENT_DIR . '/bps-backup/master-backups/mscan-stop.txt';
		file_put_contents($MScanStop, "run");
		
		$MScan_options = get_option('bulletproof_security_options_MScan');
		$mstime = ! isset($MScan_options['mscan_max_time_limit']) ? '300' : $MScan_options['mscan_max_time_limit'];
		ini_set('max_execution_time', $mstime);		
		
		require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/mscan-wp-core-hash-maker.php';
		require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/mscan-plugin-hash-maker.php';
		require_once WP_PLUGIN_DIR . '/bulletproof-security/includes/mscan-theme-hash-maker.php';	

		if ( bpsPro_mscan_calculate_scan_time($mstime) == true ) {
			if ( bpsPro_wp_zip_download($mstime) == true ) {
				if ( bpsPro_wp_zip_extractor() == true ) {
					if ( bpsPro_wp_hash_maker() == true ) {
						if ( bpsPro_plugin_zip_download($mstime) == true ) {
							if ( bpsPro_plugin_zip_extractor() == true ) {							
								if ( bpsPro_plugin_hash_maker() == true ) {
									if ( bpsPro_theme_zip_download($mstime) == true ) {
										if ( bpsPro_theme_zip_extractor() == true ) {
											if ( bpsPro_theme_hash_maker() == true ) {
												bpsPro_mscan_file_scan($mstime);
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	wp_die();
}

add_action('wp_ajax_bps_mscan_scan_processing', 'bpsPro_mscan_scan_processing');

function bpsPro_mscan_scan_estimate() {

	if ( isset( $_POST['post_var'] ) && $_POST['post_var'] == 'bps_mscan_estimate' && current_user_can('manage_options') ) {
		check_ajax_referer( 'bulletproof_security_mscan_time_estimate', 'mscan_nonce' );		

		$MScanStop = WP_CONTENT_DIR . '/bps-backup/master-backups/mscan-stop.txt';
		file_put_contents($MScanStop, "run");

		$MScan_options = get_option('bulletproof_security_options_MScan');
		$mstime = ! isset($MScan_options['mscan_max_time_limit']) ? '300' : $MScan_options['mscan_max_time_limit'];
		ini_set('max_execution_time', $mstime);	

		// ONLY the scan time estimate function is executed.
		if ( bpsPro_mscan_calculate_scan_time($mstime) == true ) {
		
			$MScan_status = get_option('bulletproof_security_options_MScan_status');
	
			$MScan_status_db = array( 
			'bps_mscan_time_start' 					=> $MScan_status['bps_mscan_time_start'], 
			'bps_mscan_time_stop' 					=> $MScan_status['bps_mscan_time_stop'], 
			'bps_mscan_time_end' 					=> $MScan_status['bps_mscan_time_end'], 
			'bps_mscan_time_remaining' 				=> $MScan_status['bps_mscan_time_remaining'], 
			'bps_mscan_status' 						=> '5', 
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
	wp_die();
}

add_action('wp_ajax_bps_mscan_scan_estimate', 'bpsPro_mscan_scan_estimate');

if ( version_compare( PHP_VERSION, '9.0.0', '>=' ) ) {

	// Note: On Windows XAMPP ONLY backslashes \ are used in getSubPathName paths. On Linux ONLY forward slashes / are used in paths.
	class BPSMScanRecursiveFilterIterator extends RecursiveFilterIterator {
	
		public function accept(): bool {
			$MScan_options = get_option('bulletproof_security_options_MScan');
			$excluded_dirs = array();
			$excluded_dirs_gwiod = array();
			$wp_abspath_forward_slashes = str_replace( '\\', '/', ABSPATH );
			$wp_install_folder = str_replace( array( get_home_path(), '/', ), "", $wp_abspath_forward_slashes );
			
			foreach ( $MScan_options['bps_mscan_dirs'] as $key => $value ) {
				if ( $value == '' ) {
					$excluded_dirs[] = $key;
					$excluded_dirs_gwiod[] = $wp_install_folder . DIRECTORY_SEPARATOR . $key;
				}
			}
	
			$dir_filter_array_merge = array_unique(array_merge($excluded_dirs, $excluded_dirs_gwiod));
			
			return (bool) !in_array( $this->getSubPathName(), $dir_filter_array_merge, true );
		}
	}

} else {
	
	class BPSMScanRecursiveFilterIterator extends RecursiveFilterIterator {
	
		#[\ReturnTypeWillChange]
		public function accept() {
			$MScan_options = get_option('bulletproof_security_options_MScan');
			$excluded_dirs = array();
			$excluded_dirs_gwiod = array();
			$wp_abspath_forward_slashes = str_replace( '\\', '/', ABSPATH );
			$wp_install_folder = str_replace( array( get_home_path(), '/', ), "", $wp_abspath_forward_slashes );
			
			foreach ( $MScan_options['bps_mscan_dirs'] as $key => $value ) {
				if ( $value == '' ) {
					$excluded_dirs[] = $key;
					$excluded_dirs_gwiod[] = $wp_install_folder . DIRECTORY_SEPARATOR . $key;
				}
			}
	
			$dir_filter_array_merge = array_unique(array_merge($excluded_dirs, $excluded_dirs_gwiod));
			
			return !in_array( $this->getSubPathName(), $dir_filter_array_merge, true );
		}
	}	
}

function bpsPro_mscan_calculate_scan_time($mstime) {
global $wp_version, $wpdb, $plugin_hashes, $theme_hashes;	
	
	$time_start = microtime( true );

	$MScan_options = get_option('bulletproof_security_options_MScan');
	$MScan_status = get_option('bulletproof_security_options_MScan_status');
	$bps_mscan_status = isset($MScan_status['bps_mscan_status']) ? $MScan_status['bps_mscan_status'] : '';
	$mstime = ! isset($MScan_options['mscan_max_time_limit']) ? '300' : $MScan_options['mscan_max_time_limit'];

	set_time_limit($mstime);
	ini_set('max_execution_time', $mstime);
	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	$mscan_log = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
	$MScanStop = WP_CONTENT_DIR . '/bps-backup/master-backups/mscan-stop.txt';

	$handle = fopen( $mscan_log, 'a' );

	fwrite( $handle, "\r\n[MScan Scan Start: $timestamp]\r\n" );
	fwrite( $handle, "MScan Status: ".$bps_mscan_status."\r\n" );
	fwrite( $handle, "Scan Time Calculation: Start Count total files to scan.\r\n" );
	
	if ( $MScan_options['mscan_scan_skipped_files'] == 'On' ) {	
		fwrite( $handle, "Scan Time Calculation: Skipped File Scan is set to On. Only Skipped files will be scanned.\r\n" );		
	} else {
		fwrite( $handle, "Scan Time Calculation: Max File Size Limit to Scan: ".$MScan_options['mscan_max_file_size']." KB\r\n" );
	}

	$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
	$bps_plugin_dir = str_replace( WP_CONTENT_DIR, '', WP_PLUGIN_DIR );
	$bps_themes_dir = str_replace( WP_CONTENT_DIR, '', get_theme_root() );
	$bps_plugin_dir_no_slash = str_replace( array( '\\', '/'), '', $bps_plugin_dir );
	$bps_themes_dir_no_slash = str_replace( array( '\\', '/'), '', $bps_themes_dir );

     if ( ! function_exists('get_home_path')) {
         require_once ABSPATH . 'wp-admin/includes/file.php';
     }

	$source = get_home_path();

	if ( is_dir($source) ) {
		
		$dirItr    = new RecursiveDirectoryIterator($source);
		$filterItr = new BPSMScanRecursiveFilterIterator($dirItr);
		$iterator  = new RecursiveIteratorIterator($filterItr, RecursiveIteratorIterator::SELF_FIRST);		

		$file_path_array = array();
		$wp_core_file_array = array();
		$skipped_image_file_path_array = array();
		$skipped_nonimage_file_path_array = array();		

		$wp_core_root_file_array = array( 'wp-activate.php', 'wp-blog-header.php', 'wp-comments-post.php', 'wp-config-sample.php', 'wp-cron.php', 'wp-links-opml.php', 'wp-load.php', 'wp-login.php', 'wp-mail.php', 'wp-settings.php', 'wp-signup.php', 'wp-trackback.php' );		

		foreach ( $iterator as $files ) {
    		
			try {
				if ( $files->isFile() ) {
					
					if ( file_get_contents($MScanStop) != 'run' ) { 
						fwrite( $handle, "Scan Time Calculation: MScan Scanning was Stopped\r\n" );
						fclose($handle);
						exit();
							 
					} else {				
					
						if ( ! preg_match( '/(.*)((\/|\\\)'.$bps_wpcontent_dir.'(\/|\\\)bps-backup(\/|\\\))(.*)/', $files->getPathname() ) && ! preg_match( '/(.*)((\/|\\\)'.$bps_wpcontent_dir.'(\/|\\\)'.$bps_plugin_dir_no_slash.'(\/|\\\))(.*)/', $files->getPathname() ) && ! preg_match( '/(.*)((\/|\\\)'.$bps_wpcontent_dir.'(\/|\\\)'.$bps_themes_dir_no_slash.'(\/|\\\))(.*)/', $files->getPathname() ) && ! preg_match( '/(.*)((\/|\\\)'.$bps_wpcontent_dir.'(\/|\\\)index\.php)/', $files->getPathname() ) ) {
						
							if ( $files->getFilename() == 'index.php' ) {
								$pattern = '/define\((\s|)\'WP_USE_THEMES/';
								$check_string1 = file_get_contents( $files->getPath() . '/index.php' );
								$pos1 = preg_match( $pattern, $check_string1 );
							}
							
							if ( $files->getFilename() == 'readme.html' ) {
								$check_string2 = file_get_contents( $files->getPath() . '/readme.html' );
								$pos2 = strpos( $check_string2, "https://wordpress.org/" );
							}					
		
							if ( $files->getFilename() == 'xmlrpc.php' ) {
								$check_string3 = file_get_contents( $files->getPath() . '/xmlrpc.php' );
								$pos3 = strpos( $check_string3, "XML-RPC protocol support for WordPress" );
							}
		
							if ( $MScan_options['mscan_exclude_dirs'] != '' ) {
							
								$mscan_exclude_dirs = str_replace('\\\\', '\\', $MScan_options['mscan_exclude_dirs']);
								$mscan_exclude_dirs_array = explode( "\n", $mscan_exclude_dirs );
			
								$mscan_exclude_dirs_regex_array = array();
				
								foreach ( $mscan_exclude_dirs_array as $mscan_exclude_dir ) {
									$search_array = array( "\n", "\r\n", "\r", '\\', '/', '[', ']', '(', ')', '+', ' ');
									$replace_array = array( "", "", "", '\\\\', '\/', '\[', '\]', '\(', '\)', '\+', '\s');
									$mscan_exclude_dir = str_replace( $search_array, $replace_array, $mscan_exclude_dir );
									$mscan_exclude_dirs_regex_array[] = '(.*)'.$mscan_exclude_dir.'(.*)|';
								}
							
								$glue = implode("", $mscan_exclude_dirs_regex_array);
								$mscan_exclude_dir_regex = preg_replace( '/\|$/', '', $glue);
								$exclude_dirs_pattern = '/('.$mscan_exclude_dir_regex.')/';
								
							} else {
								$exclude_dirs_pattern = '/(\/bps-no-dirs\/)/';
							}
							
							$core_pattern = '/(.*)((\/|\\\)wp-admin(\/|\\\)|(\/|\\\)wp-includes(\/|\\\))(.*)/';
		
							if ( preg_match( $core_pattern, $files->getPathname() ) || $files->getFilename() == 'index.php' && $pos1 !== false || $files->getFilename() == 'readme.html' && $pos2 !== false || $files->getFilename() == 'xmlrpc.php' && $pos3 !== false || in_array($files->getFilename(), $wp_core_root_file_array) ) {
								$wp_core_file_array[] = $files->getPathname();
							}
		
							if ( ! preg_match( $core_pattern, $files->getPathname() ) && ! in_array($files->getFilename(), $wp_core_root_file_array) && ! preg_match( $exclude_dirs_pattern, $files->getPathname() ) ) {
		
								$ext = pathinfo( strtolower($files->getPathname()), PATHINFO_EXTENSION );
							
								if ( $files->getSize() <= $MScan_options['mscan_max_file_size'] * 1024 ) {
						
									if ( $ext == 'htm' || $ext == 'html' || $ext == 'htaccess' || $ext == 'js' || $ext == 'php' || $ext == 'phps' || $ext == 'php5' || $ext == 'php4' || $ext == 'php3' || $ext == 'phtml' || $ext == 'phpt' || $ext == 'shtm' || $ext == 'shtml' || $ext == 'xhtml' || $ext == 'ico' || $ext == 'bak' ) {
										$file_path_array[] = $files->getPathname();
									}					
						
								} else { 
							
									if ( $ext == 'htm' || $ext == 'html' || $ext == 'htaccess' || $ext == 'js' || $ext == 'php' || $ext == 'phps' || $ext == 'php5' || $ext == 'php4' || $ext == 'php3' || $ext == 'phtml' || $ext == 'phpt' || $ext == 'shtm' || $ext == 'shtml' || $ext == 'xhtml' || $ext == 'ico' || $ext == 'bak' ) {
										$skipped_nonimage_file_path_array[] = $files->getPathname();
									}
								}
							}
						}
					}
				}
			} catch (RuntimeException $e) {   
				
			}
		} 
		
		$skipped_file_path_array = $skipped_nonimage_file_path_array;		
		
		$MStable = $wpdb->prefix . "bpspro_mscan";
		
		$ignored_rows = 'ignore';
		$MScanIgnoreRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_ignored = %s", $ignored_rows ) );
		
		$mscan_file_ignore_array = array();

		if ( $wpdb->num_rows != 0 ) {
		
			foreach ( $MScanIgnoreRows as $row ) {
				$mscan_file_ignore_array[] = $row->mscan_path;
			}
		}
		
		foreach ( $wp_core_file_array as $key => $value ) {
			
			if ( preg_match( $exclude_dirs_pattern, $value ) ) {
				unset($wp_core_file_array[$key]);
			}
		
			if ( in_array( $value, $mscan_file_ignore_array ) ) {
				unset($wp_core_file_array[$key]);
			}
		}

		foreach ( $file_path_array as $key => $value ) {
			
			if ( preg_match( '/index\.php/', $value ) ) {
				$pattern = '/define\((\s|)\'WP_USE_THEMES/';
				$check_string4 = file_get_contents( $value );
				if ( preg_match( $pattern, $check_string4 ) ) {
					unset($file_path_array[$key]);
				}
			}
			
			if ( preg_match( '/readme\.html/', $value ) ) {
				$check_string5 = file_get_contents( $value );
				$pos5 = strpos( $check_string5, "https://wordpress.org/" );
				if ( $pos5 !== false ) {
					unset($file_path_array[$key]);
				}
			}			

			if ( preg_match( '/xmlrpc\.php/', $value ) ) {
				$check_string6 = file_get_contents( $value );
				$pos6 = strpos( $check_string6, "XML-RPC protocol support for WordPress" );
				if ( $pos6 !== false ) {
					unset($file_path_array[$key]);
				}
			}			
		
			if ( in_array( $value, $mscan_file_ignore_array ) ) {
				unset($file_path_array[$key]);
			}		
		}

		foreach ( $skipped_file_path_array as $key => $value ) {
			
			if ( in_array( $value, $mscan_file_ignore_array ) ) {
				unset($skipped_file_path_array[$key]);
			}			
		}

		$mscan_dirs_array = array();
		
		foreach ( $MScan_options['bps_mscan_dirs'] as $key => $value ) {
			
			if ( $value == '1' ) {
				$mscan_dirs_array[] = $key;
			}
		}

		$total_wp_core_files = count($wp_core_file_array);

		$plugins_dir = WP_PLUGIN_DIR;
		
		$plugin_file_path_array = array();
		
		if ( in_array( $bps_wpcontent_dir, $mscan_dirs_array ) ) {

			if ( is_dir($plugins_dir) ) {
				$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($plugins_dir), RecursiveIteratorIterator::SELF_FIRST);		
				
				foreach ( $iterator as $files ) {
					
					if ( $files->isFile() ) {
		
						if ( $files->getPathname() != WP_PLUGIN_DIR . '/.htaccess' && $files->getPathname() != WP_PLUGIN_DIR . '\.htaccess' && $files->getPathname() != WP_PLUGIN_DIR . '\index.php' && $files->getPathname() != WP_PLUGIN_DIR . '/index.php' ) {
						
							$plugin_file_path_array[] = $files->getPathname();
						}
					}
				}
			}
		}
		
		$themes_dir = get_theme_root();
		
		$theme_file_path_array = array();
		
		if ( in_array( $bps_wpcontent_dir, $mscan_dirs_array ) ) {

			if ( is_dir($themes_dir) ) {
				$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($themes_dir), RecursiveIteratorIterator::SELF_FIRST);		
				
				foreach ( $iterator as $files ) {
					
					if ( $files->isFile() ) {
		
						if ( $files->getPathname() != get_theme_root() . '\index.php' && $files->getPathname() != get_theme_root() . '/index.php' ) {
						
							$theme_file_path_array[] = $files->getPathname();
						}
					}
				}
			}
		}

		if ( ! in_array( $bps_wpcontent_dir, $mscan_dirs_array ) ) {
			$total_plugin_files = 0;
			$total_theme_files = 0;
		} else {
			
			if ( ! empty($plugin_hashes) ) {
				$total_plugin_files = count($plugin_hashes); 
			} else {
				$total_plugin_files = count($plugin_file_path_array); 
			}
			
			if ( ! empty($theme_hashes) ) {
				$total_theme_files = count($theme_hashes); 
			} else {
				$total_theme_files = count($theme_file_path_array); 
			}				
		}
		
		$total_non_image_files = count($file_path_array);
		$total_skipped_files = count($skipped_file_path_array);
		$total_scan_files = $total_wp_core_files + $total_plugin_files + $total_theme_files + $total_non_image_files;
		
		if ( $MScan_options['mscan_scan_skipped_files'] == 'On' ) {
			
			fwrite( $handle, "Scan Time Calculation: Total Skipped Files to Scan: ".$total_skipped_files."\r\n" );			
			
		} else {

			fwrite( $handle, "Scan Time Calculation: Total Skipped Files (larger than ".$MScan_options['mscan_max_file_size']." KB): ".$total_skipped_files."\r\n" );	
			fwrite( $handle, "Scan Time Calculation: Total WP Core Files to Scan: ".$total_wp_core_files."\r\n" );
			fwrite( $handle, "Scan Time Calculation: Total Plugin Files to Scan: ".$total_plugin_files."\r\n" );			
			fwrite( $handle, "Scan Time Calculation: Total Theme Files to Scan: ".$total_theme_files."\r\n" );			
			fwrite( $handle, "Scan Time Calculation: Total non-WP php, html, etc Files to Scan: ".$total_non_image_files."\r\n" );	
			fwrite( $handle, "Scan Time Calculation: Total Files to Scan: ".$total_scan_files."\r\n" );
		}
		
		if ( $MScan_options['bps_mscan_dirs'] != '' ) {
			
			$mscan_dirs = implode( ', ', $mscan_dirs_array );
			fwrite( $handle, "Scan Time Calculation: Website Folders & Files To Scan: ".$mscan_dirs."\r\n" );
		}
		
		if ( $MScan_options['mscan_exclude_dirs'] != '' ) {	
		
			$mscan_exclude_dirs = implode( '', explode( "\n", $MScan_options['mscan_exclude_dirs'] ) );
			fwrite( $handle, "Scan Time Calculation: Excluded Folders: \r\n".$mscan_exclude_dirs."\r\n" );
		}

		$wp_hashes_file = WP_CONTENT_DIR . '/bps-backup/wp-hashes/wp-hashes.php';
		$wp_hash_time = '0';
		$mscan_new_wp_core_installed = '0';
		
		if ( file_exists($wp_hashes_file) ) {
			$check_string = file_get_contents($wp_hashes_file);
		
			if ( ! strpos( $check_string, "WordPress $wp_version Hashes" ) ) {
				$wp_hash_time = '110';
				$mscan_new_wp_core_installed = '1';
			}
		}
		
		$all_plugins = get_plugins();
		
		$plugins_array = array();
		$hello_dolly_plugin_array = array();
	
		foreach ( $all_plugins as $key => $value ) {
				
			if ( ! empty($key) ) {
				
				$pos = strpos($key, '/');
				$dolly_pos = strpos($value['Name'], 'Hello Dolly');				
				
				if ( $pos !== false ) {
	
					$plugin_name = strstr($key, '/', true);
					$plugins_array[$plugin_name] = $value['Version'];
				
				} else {
					
					if ( $dolly_pos !== false ) {
						$hello_dolly_plugin_array['hello-dolly'] = $value['Version'];
					}				
				}
			}
		}
	
		$plugins_array_merged = array_merge($plugins_array, $hello_dolly_plugin_array);
		$mscan_plugin_hash = get_option('bulletproof_security_options_mscan_plugin_hash');
		$mscan_new_plugins_installed = '0';
		$mscan_new_themes_installed = '0';		

		if ( ! isset( $mscan_plugin_hash['bps_mscan_plugin_hash_version_check'] ) ) {
			
			$plugin_array_count = count($plugins_array_merged);
			$plugin_hash_time = $plugin_array_count * 10;
		
		} else {
			
			$array_diff_plugin_version = array_diff( $plugins_array_merged, $mscan_plugin_hash['bps_mscan_plugin_hash_version_check'] );
			$count_plugin_array = count($array_diff_plugin_version);			
			
			if ( $count_plugin_array >= 1 ) {
				
				$plugin_hash_time = $count_plugin_array * 10;
				$mscan_new_plugins_installed = '1';
			
			} else {
			
				$plugin_array_count = count($plugins_array_merged);
				$plugin_hash_time = $plugin_array_count * 9;
			}
		}

		$all_themes = wp_get_themes();
		$all_themes_array = array();
	
		foreach ( $all_themes as $key => $value ) {
				
			if ( ! empty($key) ) {
				$all_themes_array[$key] = $value['Version'];
			}
		}
	
		$mscan_theme_hash = get_option('bulletproof_security_options_mscan_theme_hash');

		if ( ! isset( $mscan_theme_hash['bps_mscan_theme_hash_version_check'] ) ) {
			
			$theme_array_count = count($all_themes_array);
			$theme_hash_time = $theme_array_count * 10;
		
		} else {

			$array_diff_theme_version = array_diff( $all_themes_array, $mscan_theme_hash['bps_mscan_theme_hash_version_check'] );
			$count_theme_array = count($array_diff_theme_version);

			if ( $count_theme_array >= 1 ) {
				
				$theme_hash_time = $count_theme_array * 10;
				$mscan_new_themes_installed = '1';
			
			} else {

				$theme_array_count = count($all_themes_array);
				$theme_hash_time = $theme_array_count * 9;
			}
		}
		
		if ( $MScan_options['mscan_scan_skipped_files'] == 'On' ) {
			
			$filesize_array = array();
			
			foreach ( $skipped_file_path_array as $file ) {
				
				if ( file_exists($file) ) {
					$filesize_array[] = filesize($file);
				}
			}
			
			$total_filesize_bytes = array_sum($filesize_array);
			$mbytes = number_format( $total_filesize_bytes / ( 1024 * 1024 ), 2 );
			$skipped_files_time_math = $mbytes * 1.15;
			$total_time_estimate = round($skipped_files_time_math);

			$bps_mscan_time_remaining = time() + $wp_hash_time + $plugin_hash_time + $theme_hash_time + $total_time_estimate;
		
			fwrite( $handle, "Scan Time Calculation: Total Size of all Skipped Files: ".$mbytes." MB\r\n" );
			fwrite( $handle, "Scan Time Calculation: WP Core Hash Time Estimate: +".$wp_hash_time." Seconds\r\n" );		
			fwrite( $handle, "Scan Time Calculation: Plugins Hash Time Estimate: +".$plugin_hash_time." Seconds\r\n" );		
			fwrite( $handle, "Scan Time Calculation: Themes Hash Time Estimate: +".$theme_hash_time." Seconds\r\n" );				
			fwrite( $handle, "Scan Time Calculation: Skipped Files Time Estimate: ".$total_time_estimate." Seconds\r\n" );
		
		} else {
			
			$wp_core_files_time_math = $total_wp_core_files / 400;
			$wp_core_files_time = round($wp_core_files_time_math);
			$plugin_files_time_math = $total_plugin_files / 850;
			$plugin_files_time = round($plugin_files_time_math);
			$theme_files_time_math = $total_theme_files / 850;
			$theme_files_time = round($theme_files_time_math);			
			
			if ( version_compare( PHP_VERSION, '7.0.0' ) >= 0 ) {
			
				$non_image_files_time_math = $total_non_image_files / 150;
				$non_image_files_time = round($non_image_files_time_math);
			
			} else {
				
				$non_image_files_time_math = $total_non_image_files / 100;
				$non_image_files_time = round($non_image_files_time_math);				
			}
		
			$rows = '';
			$size = 0;
			$result = $wpdb->get_results( $wpdb->prepare( "SHOW TABLE STATUS WHERE Name != %s", $rows ) );

			foreach ( $result as $data ) {
				$size += $data->Data_length + $data->Index_length;
			}
	
			$kbytes = $size / 1024;
			$db_size_time_math = $kbytes / 4000;
			$db_size_time = round($db_size_time_math) * 4;

			$last_scan_time_unix = isset($MScan_status['bps_mscan_last_scan_timestamp']) ? strtotime($MScan_status['bps_mscan_last_scan_timestamp']) : 0;
			$time_now = time() + $gmt_offset;
			
			if ( $time_now - $last_scan_time_unix > 10800 ) {
				$linux_base_time = 60;
			} else {
				$linux_base_time = 30;
			}

			$bps_mscan_time_remaining = time() + $wp_hash_time + $plugin_hash_time + $theme_hash_time + $wp_core_files_time + $plugin_files_time + 
			$theme_files_time + $non_image_files_time + $db_size_time + $linux_base_time;
			
			$total_time_estimate = $wp_hash_time + $plugin_hash_time + $theme_hash_time + $wp_core_files_time + $plugin_files_time + $theme_files_time + 
			$non_image_files_time + $db_size_time + $linux_base_time;
			
			fwrite( $handle, "Scan Time Calculation: WP Core Hash Time Estimate: +".$wp_hash_time." Seconds\r\n" );		
			fwrite( $handle, "Scan Time Calculation: Plugins Hash Time Estimate: +".$plugin_hash_time." Seconds\r\n" );	
			fwrite( $handle, "Scan Time Calculation: Themes Hash Time Estimate: +".$theme_hash_time." Seconds\r\n" );			
			fwrite( $handle, "Scan Time Calculation: WP Core Files Time Estimate: +".$wp_core_files_time." Seconds\r\n" );
			fwrite( $handle, "Scan Time Calculation: Plugin Files Time Estimate: +".$plugin_files_time." Seconds\r\n" );			
			fwrite( $handle, "Scan Time Calculation: Theme Files Time Estimate: +".$theme_files_time." Seconds\r\n" );			
			fwrite( $handle, "Scan Time Calculation: non-WP php, html, etc Files Time Estimate: +".$non_image_files_time." Seconds\r\n" );
			fwrite( $handle, "Scan Time Calculation: DB Size Time Estimate: +".$db_size_time." Seconds\r\n" );
			fwrite( $handle, "Scan Time Calculation: Session Scan Variance Time Estimate: +".$linux_base_time." Seconds\r\n" );
			fwrite( $handle, "Scan Time Calculation: Scan Time Estimate: ".$total_time_estimate." Seconds\r\n" );
		}

		$MScan_status = get_option('bulletproof_security_options_MScan_status');
		$bps_mscan_time_stop = isset($MScan_status['bps_mscan_time_stop']) ? $MScan_status['bps_mscan_time_stop'] : '';
		$bps_mscan_time_end = isset($MScan_status['bps_mscan_time_end']) ? $MScan_status['bps_mscan_time_end'] : '';
		$bps_mscan_last_scan_timestamp = isset($MScan_status['bps_mscan_last_scan_timestamp']) ? $MScan_status['bps_mscan_last_scan_timestamp'] : '';				
		$bps_mscan_total_suspect_files = isset($MScan_status['bps_mscan_total_suspect_files']) ? $MScan_status['bps_mscan_total_suspect_files'] : '';		
		$bps_mscan_suspect_skipped_files = isset($MScan_status['bps_mscan_suspect_skipped_files']) ? $MScan_status['bps_mscan_suspect_skipped_files'] : '';
		$bps_mscan_total_suspect_db = isset($MScan_status['bps_mscan_total_suspect_db']) ? $MScan_status['bps_mscan_total_suspect_db'] : '';
		$bps_mscan_total_ignored_files = isset($MScan_status['bps_mscan_total_ignored_files']) ? $MScan_status['bps_mscan_total_ignored_files'] : '';
		
		if ( ! get_option('bulletproof_security_options_mscan_theme_hash') && ! get_option('bulletproof_security_options_mscan_plugin_hash') && ! get_option( 'bulletproof_security_options_mscan_p_hash_new' ) && ! get_option( 'bulletproof_security_options_mscan_t_hash_new' ) ) {
			
			$bps_mscan_status_run = '9';
		
		} else {
			
			if ( $mscan_new_plugins_installed == '1' || $mscan_new_themes_installed == '1' || $mscan_new_wp_core_installed == '1' ) {
				$bps_mscan_status_run = '9';
			
			} else {
			
				$bps_mscan_status_run = '2';
			}
		}

		$MScan_status_db = array( 
		'bps_mscan_time_start' 					=> time(), 
		'bps_mscan_time_stop' 					=> $bps_mscan_time_stop, 
		'bps_mscan_time_end' 					=> $bps_mscan_time_end, 
		'bps_mscan_time_remaining' 				=> $bps_mscan_time_remaining, 
		'bps_mscan_status' 						=> $bps_mscan_status_run, 
		'bps_mscan_last_scan_timestamp' 		=> $bps_mscan_last_scan_timestamp, 
		'bps_mscan_total_time' 					=> $total_time_estimate, 
		'bps_mscan_total_website_files' 		=> '', 
		'bps_mscan_total_wp_core_files' 		=> $total_wp_core_files, 
		'bps_mscan_total_non_image_files' 		=> $total_non_image_files, 
		'bps_mscan_total_image_files' 			=> '', 
		'bps_mscan_total_all_scannable_files' 	=> $total_scan_files, 
		'bps_mscan_total_skipped_files' 		=> $total_skipped_files, 
		'bps_mscan_total_suspect_files' 		=> $bps_mscan_total_suspect_files, 
		'bps_mscan_suspect_skipped_files' 		=> $bps_mscan_suspect_skipped_files, 
		'bps_mscan_total_suspect_db' 			=> $bps_mscan_total_suspect_db, 
		'bps_mscan_total_ignored_files' 		=> $bps_mscan_total_ignored_files, 
		'bps_mscan_total_plugin_files' 			=> $total_plugin_files, 			 
		'bps_mscan_total_theme_files' 			=> $total_theme_files 
		);		
		
		foreach( $MScan_status_db as $key => $value ) {
			update_option('bulletproof_security_options_MScan_status', $MScan_status_db);
		}
	}

	$time_end = microtime( true );
	$file_count_time = $time_end - $time_start;

	$hours = (int)($file_count_time / 60 / 60);
	$minutes = (int)($file_count_time / 60) - $hours * 60;
	$seconds = (int)$file_count_time - $hours * 60 * 60 - $minutes * 60;
	$hours_format = $hours == 0 ? "00" : $hours;
	$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
	$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);
	
	$file_count_log = 'Scan Time Calculation Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;
	$MScan_status = get_option('bulletproof_security_options_MScan_status');

	fwrite( $handle, "MScan Status: ".$MScan_status['bps_mscan_status']."\r\n" );
	fwrite( $handle, "$file_count_log\r\n" );
	fclose($handle);
	
	return true;
}

function bpsPro_mscan_file_scan($mstime) {
global $wp_version, $wpdb, $plugin_hashes, $theme_hashes;	
	
	$time_start = microtime( true );

	$MScan_options = get_option('bulletproof_security_options_MScan');
	$mstime = ! isset($MScan_options['mscan_max_time_limit']) ? '300' : $MScan_options['mscan_max_time_limit'];
	
	set_time_limit($mstime);
	ini_set('max_execution_time', $mstime);
	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	$mscan_log = WP_CONTENT_DIR . '/bps-backup/logs/mscan_log.txt';
	$MScanStop = WP_CONTENT_DIR . '/bps-backup/master-backups/mscan-stop.txt';
	$send_email = '';

	$handle = fopen( $mscan_log, 'a' );
	
	$MScan_status = get_option('bulletproof_security_options_MScan_status');
	
	if ( isset($MScan_status['bps_mscan_status']) && $MScan_status['bps_mscan_status'] == '9' ) {
		
		fwrite( $handle, "MScan Status: 9\r\n" );	
		fwrite( $handle, "Scanning Files: Aborted. The Hash Maker is creating WP Core, Plugin or Theme file hashes.\r\n" );

		$bps_mscan_total_time = time() - $MScan_status['bps_mscan_time_start'];

		$hours = (int)($bps_mscan_total_time / 60 / 60);
		$minutes = (int)($bps_mscan_total_time / 60) - $hours * 60;
		$seconds = (int)$bps_mscan_total_time - $hours * 60 * 60 - $minutes * 60;
		$hours_format = $hours == 0 ? "00" : $hours;
		$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
		$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);
		
		$file_scan_log = 'Hash Maker Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;
		
		fwrite( $handle, "$file_scan_log\r\n" );
		fclose($handle);	
		
		return;		
	}

	$mscan_plugin_hash_new_array_keys = array();
	
	if ( get_option( 'bulletproof_security_options_mscan_p_hash_new' ) ) {
		
		$mscan_plugin_hash_new = get_option('bulletproof_security_options_mscan_p_hash_new');
		$mscan_plugin_hash_new_array_keys = array();
		
		foreach ( $mscan_plugin_hash_new['bps_mscan_plugin_hash_paths_new'] as $key => $value ) {
			
			foreach ( $value as $inner_key => $inner_value ) {
			
				if ( ! empty($inner_value) ) {
					$mscan_plugin_hash_new_array_keys[] = $key;
				}
			}
		}
	}

	if ( get_option( 'bulletproof_security_options_mscan_t_hash_new' ) ) {

		$mscan_theme_hash_new = get_option('bulletproof_security_options_mscan_t_hash_new');
		$mscan_theme_hash_new_array_keys = array();
		
		foreach ( $mscan_theme_hash_new['bps_mscan_theme_hash_paths_new'] as $key => $value ) {
			
			foreach ( $value as $inner_key => $inner_value ) {
			
				if ( ! empty($inner_value) ) {
					$mscan_theme_hash_new_array_keys[] = $key;
				}
			}
		}		
	}

	if ( ! get_option('bulletproof_security_options_mscan_theme_hash') || ! get_option('bulletproof_security_options_mscan_plugin_hash') || ! empty($mscan_plugin_hash_new_array_keys ) || ! empty($mscan_theme_hash_new_array_keys ) ) {
		
		$MScan_status = get_option('bulletproof_security_options_MScan_status');
		
		$MScan_status_db = array( 
		'bps_mscan_time_start' 					=> $MScan_status['bps_mscan_time_start'], 
		'bps_mscan_time_stop' 					=> $MScan_status['bps_mscan_time_stop'], 
		'bps_mscan_time_end' 					=> $MScan_status['bps_mscan_time_end'], 
		'bps_mscan_time_remaining' 				=> $MScan_status['bps_mscan_time_remaining'], 
		'bps_mscan_status' 						=> '3', 
		'bps_mscan_last_scan_timestamp' 		=> $MScan_status['bps_mscan_last_scan_timestamp'], 
		'bps_mscan_total_time' 					=> $MScan_status['bps_mscan_total_time'], 
		'bps_mscan_total_website_files' 		=> '', 
		'bps_mscan_total_wp_core_files' 		=> $MScan_status['bps_mscan_total_wp_core_files'], 
		'bps_mscan_total_non_image_files' 		=> $MScan_status['bps_mscan_total_non_image_files'], 
		'bps_mscan_total_image_files' 			=> '', 
		'bps_mscan_total_all_scannable_files' 	=> 'New Hash Files Created: Run A New Scan', 
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
		
		fwrite( $handle, "Scanning Files: Files not scanned: First time scan, the Delete File Hashes Tool was used or plugins/themes installed/updated.\r\n" );
		fclose($handle);
		
		return;
	}
	
	$MScan_status = get_option('bulletproof_security_options_MScan_status');
	
	fwrite( $handle, "Scanning Files: Start scanning files.\r\n" );
	fwrite( $handle, "MScan Status: ".$MScan_status['bps_mscan_status']."\r\n" );

	$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
	$bps_plugin_dir = str_replace( WP_CONTENT_DIR, '', WP_PLUGIN_DIR );
	$bps_themes_dir = str_replace( WP_CONTENT_DIR, '', get_theme_root() );
	$bps_plugin_dir_no_slash = str_replace( array( '\\', '/'), '', $bps_plugin_dir );
	$bps_themes_dir_no_slash = str_replace( array( '\\', '/'), '', $bps_themes_dir );

	$source = get_home_path();
	
	if ( is_dir($source) ) {
		
		$dirItr    = new RecursiveDirectoryIterator($source);
		$filterItr = new BPSMScanRecursiveFilterIterator($dirItr);
		$iterator  = new RecursiveIteratorIterator($filterItr, RecursiveIteratorIterator::SELF_FIRST);

		$file_path_array = array();
		$wp_core_file_array = array();
		$skipped_image_file_path_array = array();
		$skipped_nonimage_file_path_array = array();
		
		$wp_core_root_file_array = array( 'wp-activate.php', 'wp-blog-header.php', 'wp-comments-post.php', 'wp-config-sample.php', 'wp-cron.php', 'wp-links-opml.php', 'wp-load.php', 'wp-login.php', 'wp-mail.php', 'wp-settings.php', 'wp-signup.php', 'wp-trackback.php' );

		foreach ( $iterator as $files ) {
    		
			try {
				if ( $files->isFile() ) {
					
					if ( file_get_contents($MScanStop) != 'run' ) { 
						fwrite( $handle, "Scanning Files: MScan Scanning was Stopped\r\n" );
						fclose($handle);
						exit();
							 
					} else {
	
						if ( ! preg_match( '/(.*)((\/|\\\)'.$bps_wpcontent_dir.'(\/|\\\)bps-backup(\/|\\\))(.*)/', $files->getPathname() ) && ! preg_match( '/(.*)((\/|\\\)'.$bps_wpcontent_dir.'(\/|\\\)'.$bps_plugin_dir_no_slash.'(\/|\\\))(.*)/', $files->getPathname() ) && ! preg_match( '/(.*)((\/|\\\)'.$bps_wpcontent_dir.'(\/|\\\)'.$bps_themes_dir_no_slash.'(\/|\\\))(.*)/', $files->getPathname() ) && ! preg_match( '/(.*)((\/|\\\)'.$bps_wpcontent_dir.'(\/|\\\)index\.php)/', $files->getPathname() ) ) {
						
							if ( $files->getFilename() == 'index.php' ) {
								$pattern = '/define\((\s|)\'WP_USE_THEMES/';
								$check_string1 = file_get_contents( $files->getPath() . '/index.php' );
								$pos1 = preg_match( $pattern, $check_string1 );
							}

							if ( $files->getFilename() == 'readme.html' ) {
								$check_string2 = file_get_contents( $files->getPath() . '/readme.html' );
								$pos2 = strpos( $check_string2, "https://wordpress.org/" );
							}					
		
							if ( $files->getFilename() == 'xmlrpc.php' ) {
								$check_string3 = file_get_contents( $files->getPath() . '/xmlrpc.php' );
								$pos3 = strpos( $check_string3, "XML-RPC protocol support for WordPress" );
							}
		
							if ( $MScan_options['mscan_exclude_dirs'] != '' ) {
							
								$mscan_exclude_dirs = str_replace('\\\\', '\\', $MScan_options['mscan_exclude_dirs']);
								$mscan_exclude_dirs_array = explode( "\n", $mscan_exclude_dirs );
			
								$mscan_exclude_dirs_regex_array = array();
				
								foreach ( $mscan_exclude_dirs_array as $mscan_exclude_dir ) {
									$search_array = array( "\n", "\r\n", "\r", '\\', '/', '[', ']', '(', ')', '+', ' ');
									$replace_array = array( "", "", "", '\\\\', '\/', '\[', '\]', '\(', '\)', '\+', '\s');
									$mscan_exclude_dir = str_replace( $search_array, $replace_array, $mscan_exclude_dir );
									$mscan_exclude_dirs_regex_array[] = '(.*)'.$mscan_exclude_dir.'(.*)|';
								}
							
								$glue = implode("", $mscan_exclude_dirs_regex_array);
								$mscan_exclude_dir_regex = preg_replace( '/\|$/', '', $glue);
								$exclude_dirs_pattern = '/('.$mscan_exclude_dir_regex.')/'; // file_path_array preg_match condition.
							
							} else {
								$exclude_dirs_pattern = '/(\/bps-no-dirs\/)/';
							}					
		
							$core_pattern = '/(.*)((\/|\\\)wp-admin(\/|\\\)|(\/|\\\)wp-includes(\/|\\\))(.*)/';
							
							if ( preg_match( $core_pattern, $files->getPathname() ) || $files->getFilename() == 'index.php' && $pos1 !== false || $files->getFilename() == 'readme.html' && $pos2 !== false || $files->getFilename() == 'xmlrpc.php' && $pos3 !== false || in_array($files->getFilename(), $wp_core_root_file_array) ) {
								$wp_core_file_array[] = $files->getPathname();
							}
		
							if ( ! preg_match( $core_pattern, $files->getPathname() ) && ! in_array($files->getFilename(), $wp_core_root_file_array) && ! preg_match( $exclude_dirs_pattern, $files->getPathname() ) ) {
								
								$ext = pathinfo( strtolower($files->getPathname()), PATHINFO_EXTENSION );
							
								if ( $files->getSize() <= $MScan_options['mscan_max_file_size'] * 1024 ) {
						
									if ( $ext == 'htm' || $ext == 'html' || $ext == 'htaccess' || $ext == 'js' || $ext == 'php' || $ext == 'phps' || $ext == 'php5' || $ext == 'php4' || $ext == 'php3' || $ext == 'phtml' || $ext == 'phpt' || $ext == 'shtm' || $ext == 'shtml' || $ext == 'xhtml' || $ext == 'ico' || $ext == 'bak' ) {
										$file_path_array[] = $files->getPathname();
									}					
						
								} else { 
							
									if ( $ext == 'htm' || $ext == 'html' || $ext == 'htaccess' || $ext == 'js' || $ext == 'php' || $ext == 'phps' || $ext == 'php5' || $ext == 'php4' || $ext == 'php3' || $ext == 'phtml' || $ext == 'phpt' || $ext == 'shtm' || $ext == 'shtml' || $ext == 'xhtml' || $ext == 'ico' || $ext == 'bak' ) {
										$skipped_nonimage_file_path_array[] = $files->getPathname();
									}
								}
							}
						}
					}
				}
			} catch (RuntimeException $e) {   
				
			}		
		}

		$mscan_dirs_array = array();
		
		foreach ( $MScan_options['bps_mscan_dirs'] as $key => $value ) {
			
			if ( $value == '1' ) {
				$mscan_dirs_array[] = $key;
			}
		}

		$bps_plugin_files_whitelist = '/(.*)((\/|\\\)'.$bps_wpcontent_dir.'(\/|\\\)'.$bps_plugin_dir_no_slash.'(\/|\\\)bulletproof-security(.*)(\.htaccess|\.zip|plugins-htaccess-master\.txt|class\.php|plugins-allow-from\.txt|sec-log-master\.txt|bps-maintenance-values\.php))/';

		$other_plugins_files_whitelist = '/(.*)((\/|\\\)'.$bps_wpcontent_dir.'(\/|\\\)'.$bps_plugin_dir_no_slash.'(\/|\\\)(tinymce-advanced(.*)tinymce-advanced\.php|google-sitemap-generator(.*)sitemap\.php))/';

		$plugins_dir = WP_PLUGIN_DIR;
		
		$plugin_file_path_array = array();
		
		if ( in_array( $bps_wpcontent_dir, $mscan_dirs_array ) ) {

			if ( is_dir($plugins_dir) ) {
				$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($plugins_dir), RecursiveIteratorIterator::SELF_FIRST);		
				
				foreach ( $iterator as $files ) {
					
					if ( $files->isFile() ) {
		
						if ( $files->getPathname() != WP_PLUGIN_DIR . '/.htaccess' && $files->getPathname() != WP_PLUGIN_DIR . '\.htaccess' && $files->getPathname() != WP_PLUGIN_DIR . '\index.php' && $files->getPathname() != WP_PLUGIN_DIR . '/index.php' && ! preg_match( $bps_plugin_files_whitelist, $files->getPathname() ) && ! preg_match( $other_plugins_files_whitelist, $files->getPathname() ) ) {
						
							$plugin_file_path_array[] = $files->getPathname();
						}
					}
				}
			}
		}
		
		$theme_file_whitelist = '/(.*)((\/|\\\)'.$bps_wpcontent_dir.'(\/|\\\)'.$bps_themes_dir_no_slash.'(\/|\\\)(twentythirty|twentytwentynine|twentytwentyeight|twentytwentyseven|twentytwentysix|twentytwentyfive|twentytwentyfour|twentytwentythree|twentytwentytwo|twentytwentyone|twentytwenty|twentynineteen|twentyseventeen|twentysixteen|twentyfifteen|twentyfourteen|twentythirteen|twentytwelve|twentyeleven|twentyten)(.*)\.style(.*))/';

		$themes_dir = get_theme_root();
		
		$theme_file_path_array = array();
		
		if ( in_array( $bps_wpcontent_dir, $mscan_dirs_array ) ) {

			if ( is_dir($themes_dir) ) {
				$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($themes_dir), RecursiveIteratorIterator::SELF_FIRST);		
				
				foreach ( $iterator as $files ) {
					
					if ( $files->isFile() ) {
		
						if ( $files->getPathname() != get_theme_root() . '\index.php' && $files->getPathname() != get_theme_root() . '/index.php' && ! preg_match( $theme_file_whitelist, $files->getPathname() ) ) {
						
							$theme_file_path_array[] = $files->getPathname();
						}
					}
				}
			}
		}
		
		$skipped_file_path_array = array_merge($skipped_image_file_path_array, $skipped_nonimage_file_path_array);
		
		$MStable = $wpdb->prefix . "bpspro_mscan";
		$ignored_rows = 'ignore';
		$MScanIgnoreRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_ignored = %s", $ignored_rows ) );
		
		$mscan_file_ignore_array = array();
		$mscan_db_ignore_array = array();
		$mscan_db_ignore_pattern_array = array();
		$mscan_ignored_total_array = array();
		
		if ( $wpdb->num_rows != 0 ) {		
		
			foreach ( $MScanIgnoreRows as $row ) {
				$mscan_file_ignore_array[] = $row->mscan_path;
				$mscan_db_ignore_array[] = $row->mscan_db_pkid;
				$mscan_db_ignore_pattern_array[] = $row->mscan_pattern;
				$mscan_ignored_total_array[] = $row->mscan_ignored;
			}
		}
		
		$mscan_db_pattern_match_options = get_option('bulletproof_security_options_mscan_patterns');

		foreach ( $mscan_db_pattern_match_options['mscan_pattern_match_files'] as $key => $value ) {
			
			foreach ( $value as $inner_key => $inner_value ) {
				
				if ( $inner_key == 'js_patterns' ) {
					$js_pattern = $inner_value;
				}
				if ( $inner_key == 'htaccess_patterns' ) {
					$htaccess_pattern = $inner_value;
				}
				if ( $inner_key == 'php_patterns' ) {
					$php_pattern = $inner_value;
				}
			}
		}

		foreach ( $mscan_db_pattern_match_options['mscan_pattern_match_db'] as $key => $value ) {
			
			foreach ( $value as $inner_key => $inner_value ) {
				
				if ( $inner_key == 'search1' ) {
					$search1 = $inner_value;
				}
				if ( $inner_key == 'search2' ) {
					$search2 = $inner_value;
				}			
				if ( $inner_key == 'search3' ) {
					$search3 = $inner_value;
				}
				if ( $inner_key == 'search4' ) {
					$search4 = $inner_value;
				}
				if ( $inner_key == 'search5' ) {
					$search5 = $inner_value;
				}
				if ( $inner_key == 'search6' ) {
					$search6 = $inner_value;
				}
				if ( $inner_key == 'search7' ) {
					$search7 = $inner_value;
				}
				if ( $inner_key == 'search8' ) {
					$search8 = $inner_value;
				}
				if ( $inner_key == 'search9' ) {
					$search9 = $inner_value;
				}
				if ( $inner_key == 'eval_match' ) {
					$eval_match = $inner_value;
				}
				if ( $inner_key == 'b64_decode_match' ) {
					$base64_decode_match = $inner_value;
				}
				if ( $inner_key == 'eval_text' ) {
					$eval_text = $inner_value;
				}
				if ( $inner_key == 'b64_decode_text' ) {
					$base64_decode_text = $inner_value;
				}
			}
		}
		
		$js_code_match = 0;
		$htaccess_code_match = 0;
		$php_code_match = 0;

		if ( $MScan_options['mscan_scan_skipped_files'] == 'Off' ) {
			
			$skipped_rows = 'skipped';
			$MScanSkipRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_skipped = %s", $skipped_rows ) );

			$mscan_file_skipped_path_array = array();

			if ( $wpdb->num_rows != 0 ) {		
		
				foreach ( $MScanSkipRows as $row ) {
					$mscan_file_skipped_path_array[] = $row->mscan_path;
				}
			}

			if ( ! empty($skipped_file_path_array) ) {
			
				foreach ( $skipped_file_path_array as $key => $value ) {
				
					$ext = pathinfo( strtolower($value), PATHINFO_EXTENSION );
					$file_contents = file_get_contents($value);		
					
					if ( $ext == 'js' ) {
							
						if ( ! in_array($value, $mscan_file_skipped_path_array) ) {
							$insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => '', 'mscan_type' => 'js', 'mscan_path' => $value, 'mscan_pattern' => '', 'mscan_skipped' => 'skipped', 'mscan_ignored' => '', 'mscan_db_table' => '', 'mscan_db_column' => '', 'mscan_db_pkid' => '', 'mscan_time' => current_time('mysql') ) );  
						}
					}			
	
					if ( $ext == 'htaccess' ) {
						
						if ( ! in_array($value, $mscan_file_skipped_path_array) ) {
							$insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => '', 'mscan_type' => 'htaccess', 'mscan_path' => $value, 'mscan_pattern' => '', 'mscan_skipped' => 'skipped', 'mscan_ignored' => '', 'mscan_db_table' => '', 'mscan_db_column' => '', 'mscan_db_pkid' => '', 'mscan_time' => current_time('mysql') ) );  
						}
					}
	
					if ( $ext == 'htm' || $ext == 'html' || $ext == 'php' || $ext == 'phps' || $ext == 'php5' || $ext == 'php4' || $ext == 'php3' || $ext == 'phtml' || $ext == 'phpt' || $ext == 'shtm' || $ext == 'shtml' || $ext == 'xhtml' || $ext == 'ico' || $ext == 'bak' ) {
						
						if ( ! in_array($value, $mscan_file_skipped_path_array) ) {
							$insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => '', 'mscan_type' => 'php|html|other', 'mscan_path' => $value, 'mscan_pattern' => '', 'mscan_skipped' => 'skipped', 'mscan_ignored' => '', 'mscan_db_table' => '', 'mscan_db_column' => '', 'mscan_db_pkid' => '', 'mscan_time' => current_time('mysql') ) );  
						}
					}
				}
			}
			
			foreach ( $wp_core_file_array as $key => $value ) {
				
				if ( preg_match( $exclude_dirs_pattern, $value ) ) {
					unset($wp_core_file_array[$key]);
				}
			
				if ( in_array( $value, $mscan_file_ignore_array ) ) {
					unset($wp_core_file_array[$key]);
				}
				
				$value_regex = preg_replace( '/(\/|\\\)/', "\\\\\\", $value ); 
				$wp_root_index_file = get_home_path() . 'index.php';
				$root_index_regex = preg_replace( '/(\/|\\\)/', "\\\\", $wp_root_index_file );
				
				if ( preg_match( '/index\.php/', $value ) ) {
				
					if ( ! preg_match( '/'.$value_regex.'/', $root_index_regex ) ) {	
						unset($wp_core_file_array[$key]);
					}
				}
			}
			
			if ( ! empty( $plugin_file_path_array ) ) {

				foreach ( $plugin_file_path_array as $key => $value ) {
					
					if ( preg_match( $exclude_dirs_pattern, $value ) ) {
						unset($plugin_file_path_array[$key]);
					}
				
					if ( in_array( $value, $mscan_file_ignore_array ) ) {
						unset($plugin_file_path_array[$key]);
					}
				}		
			}

			if ( ! empty( $theme_file_path_array ) ) {

				foreach ( $theme_file_path_array as $key => $value ) {
					
					if ( preg_match( $exclude_dirs_pattern, $value ) ) {
						unset($theme_file_path_array[$key]);
					}
				
					if ( in_array( $value, $mscan_file_ignore_array ) ) {
						unset($theme_file_path_array[$key]);
					}
				}		
			}
			
			foreach ( $file_path_array as $key => $value ) {
				
				$value_regex = preg_replace( '/(\/|\\\)/', "\\\\\\", $value ); 
				$wp_root_index_file = get_home_path() . 'index.php';
				$root_index_regex = preg_replace( '/(\/|\\\)/', "\\\\", $wp_root_index_file );
				
				if ( preg_match( '/'.$value_regex.'/', $root_index_regex ) ) {
					$pattern = '/define\((\s|)\'WP_USE_THEMES/';
					$check_string4 = file_get_contents( $value );
					if ( preg_match( $pattern, $check_string4 ) ) {
						unset($file_path_array[$key]);
					}
				}

				if ( preg_match( '/readme\.html/', $value ) ) {
					$check_string5 = file_get_contents( $value );
					$pos5 = strpos( $check_string5, "https://wordpress.org/" );
					if ( $pos5 !== false ) {
						unset($file_path_array[$key]);
					}
				}			
	
				if ( preg_match( '/xmlrpc\.php/', $value ) ) {
					$check_string6 = file_get_contents( $value );
					$pos6 = strpos( $check_string6, "XML-RPC protocol support for WordPress" );
					if ( $pos6 !== false ) {
						unset($file_path_array[$key]);
					}
				}			
			
				if ( in_array( $value, $mscan_file_ignore_array ) ) {
					unset($file_path_array[$key]);
				}		
			}
			
			$blank_rows = ''; 
			$MScanFileRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_path != %s AND mscan_skipped != %s", $blank_rows, $skipped_rows ) );

			$mscan_file_path_array = array();
					
			if ( $wpdb->num_rows != 0 ) {
			
				foreach ( $MScanFileRows as $row ) {
					$mscan_file_path_array[] = $row->mscan_path;
				}
			}
			
			$MScanDBRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_type != %s", $blank_rows ) );		
			
			$mscan_db_pkid_array = array();
			$mscan_db_pattern_array = array();
	
			if ( $wpdb->num_rows != 0 ) {
			
				foreach ( $MScanDBRows as $row ) {
					$mscan_db_pkid_array[] = $row->mscan_db_pkid;
					$mscan_db_pattern_array[] = $row->mscan_pattern;
				}
			}
			
			fwrite( $handle, "Scanning Files: Start WP Core file scan.\r\n" );
			
			$core_dir_flip = array_flip($wp_core_file_array);
			
			$core_md5_array = array();
			
			foreach ( $core_dir_flip as $key => $value ) {
				$core_md5_array[$key] = md5_file($key);
			}	

			require_once WP_CONTENT_DIR . '/bps-backup/wp-hashes/wp-hashes.php';
			
			$core_diff_array = array_diff($core_md5_array, $wp_hashes);
	
			foreach ( $core_diff_array as $key => $value ) {
				
				if ( preg_match( '/(.*)(\/|\\\)wp-admin(\/|\\\).htaccess/', $key ) ) {
					unset($core_diff_array[$key]);
				}
				
				if ( file_get_contents($MScanStop) != 'run' ) { 
   					 fwrite( $handle, "Scanning Files: MScan Scanning was Stopped\r\n" );
					 fclose($handle);
					 exit();
						 
				} else {

					if ( ! empty($core_diff_array) ) {
						
						if ( ! preg_match( '/(.*)(\/|\\\)wp-admin(\/|\\\).htaccess/', $key ) ) {
		
							$file_path_forward_slashes = str_replace( '\\', '/', $key );
							$rel_file_path = str_replace( get_home_path(), '', $file_path_forward_slashes );
							fwrite( $handle, "Scanning Files WP Core: Suspicious|Modified|Unknown WP Core file: ../$rel_file_path\r\n" );
						
							if ( ! in_array($key, $mscan_file_path_array) ) {
							
								if ( $insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => 'suspect', 'mscan_type' => 'core', 'mscan_path' => $key, 'mscan_pattern' => 'Altered or unknown WP Core file', 'mscan_skipped' => '', 'mscan_ignored' => '', 'mscan_db_table' => '', 'mscan_db_column' => '', 'mscan_db_pkid' => '', 'mscan_time' => current_time('mysql') ) ) ) {
									$send_email = 'send';	
								}
							}
						}
					
					} else {
						fwrite( $handle, "Scanning Files WP Core: No Suspicious|Modified|Unknown WP Core files were found.\r\n" );
					}
				}
			}		
	
			fwrite( $handle, "Scanning Files: WP Core file scan completed.\r\n" );

			if ( ! empty( $plugin_file_path_array ) ) {			
			
				$plugins_hash_match = 0;
				
				fwrite( $handle, "Scanning Files: Start Plugins file scan.\r\n" );
	
				if ( empty($plugin_hashes) || $plugin_hashes == null ) {
					
					fwrite( $handle, "Scanning Files: Plugins: The plugin-hashes.php array is empty or null. Plugin files will not be scanned.\r\n" );
				
				} else {
				
					$plugin_files_flip = array_flip($plugin_file_path_array);
					
					$plugin_md5_array = array();
					
					foreach ( $plugin_files_flip as $key => $value ) {
						$plugin_md5_array[$key] = md5_file($key);
					}
				
					$plugin_diff_array = array_diff($plugin_md5_array, $plugin_hashes);
				
					$mscan_nodownload = get_option('bulletproof_security_options_mscan_nodownload');
					
					$plugin_hashes_file = WP_CONTENT_DIR . '/bps-backup/plugin-hashes/plugin-hashes.php';
					
					if ( file_exists( $plugin_hashes_file ) ) {
						$plugin_hashes_file_contents = file_get_contents($plugin_hashes_file);
					}
				
					$plugin_diff_array_clean = array();
					
					foreach ( $plugin_diff_array as $key1 => $value1 ) {
						
						foreach ( $mscan_nodownload['bps_plugin_nodownload'] as $key2 => $value2 ) {
							
							if ( preg_match( '/(.*)'.$value2.'(.*)/', $key1, $matches ) && ! preg_match( '/##\sBEGIN\s'.$value2.'\s##/', $plugin_hashes_file_contents ) ) {
								
								unset($key1);
								$key1 = ! isset($key1) ? '' : $key1;
							}
						}
						
						if ( ! empty($key1) && ! preg_match( '/(.*)readme\.txt/', $key1 ) ) {
						
							$plugin_diff_array_clean[] = $key1;
						}
					}
		
					foreach ( $plugin_diff_array_clean as $key => $value ) {
						
						if ( file_get_contents($MScanStop) != 'run' ) { 
							 fwrite( $handle, "Scanning Files: MScan Scanning was Stopped\r\n" );
							 fclose($handle);
							 exit();
								 
						} else {
		
							if ( ! empty($plugin_diff_array_clean) ) {
								
								$plugins_hash_match = 1;
								
								$file_path_forward_slashes = str_replace( '\\', '/', $value );
								$rel_file_path = str_replace( get_home_path(), '', $file_path_forward_slashes );
								fwrite( $handle, "Scanning Files: Plugins: Suspicious|Modified|Unknown Plugin file: ../$rel_file_path\r\n" );
							
								if ( ! in_array($value, $mscan_file_path_array) ) {
								
									if ( $insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => 'suspect', 'mscan_type' => 'plugins', 'mscan_path' => $value, 'mscan_pattern' => 'Altered or unknown Plugin file', 'mscan_skipped' => '', 'mscan_ignored' => '', 'mscan_db_table' => '', 'mscan_db_column' => '', 'mscan_db_pkid' => '', 'mscan_time' => current_time('mysql') ) ) ) {
										$send_email = 'send';	
									}
								}
							}
						}
					}		
			
					if ( $plugins_hash_match == 0 ) {
						fwrite( $handle, "Scanning Files: Plugins: No Suspicious|Modified|Unknown Plugin files were found.\r\n" );
					}
				}
				fwrite( $handle, "Scanning Files: Plugins file scan completed.\r\n" );
			}
			
			if ( ! empty( $theme_file_path_array ) ) {

				$themes_hash_match = 0;
				
				fwrite( $handle, "Scanning Files: Start Themes file scan.\r\n" );
				
				if ( empty($theme_hashes) || $theme_hashes == null ) {
					
					fwrite( $handle, "Scanning Files: Plugins: The theme-hashes.php array is empty or null. Theme files will not be scanned.\r\n" );
				
				} else {

					$theme_files_flip = array_flip($theme_file_path_array);
					
					$theme_md5_array = array();
					
					foreach ( $theme_files_flip as $key => $value ) {
						$theme_md5_array[$key] = md5_file($key);
					}
				
					$theme_diff_array = array_diff($theme_md5_array, $theme_hashes);
					
					$mscan_nodownload = get_option('bulletproof_security_options_mscan_nodownload');
					
					$theme_hashes_file = WP_CONTENT_DIR . '/bps-backup/theme-hashes/theme-hashes.php';
					
					if ( file_exists( $theme_hashes_file ) ) {
						$theme_hashes_file_contents = file_get_contents($theme_hashes_file);
					}
				
					$theme_diff_array_clean = array();
					
					foreach ( $theme_diff_array as $key1 => $value1 ) {
						
						foreach ( $mscan_nodownload['bps_theme_nodownload'] as $key2 => $value2 ) {
							
							if ( preg_match( '/(.*)'.$value2.'(.*)/', $key1, $matches ) && ! preg_match( '/##\sBEGIN\s'.$value2.'\s##/', $theme_hashes_file_contents ) ) {
								
								unset($key1);
								$key1 = ! isset($key1) ? '' : $key1; 
							}
						}
						
						if ( ! empty($key1) ) {
						
							$theme_diff_array_clean[] = $key1;
						}
					}
		
					foreach ( $theme_diff_array_clean as $key => $value ) {
						
						if ( file_get_contents($MScanStop) != 'run' ) { 
							 fwrite( $handle, "Scanning Files: MScan Scanning was Stopped\r\n" );
							 fclose($handle);
							 exit();
								 
						} else {
		
							if ( ! empty($theme_diff_array_clean) ) {
								
								$themes_hash_match = 1;
								
								$file_path_forward_slashes = str_replace( '\\', '/', $value );
								$rel_file_path = str_replace( get_home_path(), '', $file_path_forward_slashes );
								fwrite( $handle, "Scanning Files: Themes: Suspicious|Modified|Unknown Theme file: ../$rel_file_path\r\n" );
							
								if ( ! in_array($value, $mscan_file_path_array) ) {
								
									if ( $insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => 'suspect', 'mscan_type' => 'themes', 'mscan_path' => $value, 'mscan_pattern' => 'Altered or unknown Theme file', 'mscan_skipped' => '', 'mscan_ignored' => '', 'mscan_db_table' => '', 'mscan_db_column' => '', 'mscan_db_pkid' => '', 'mscan_time' => current_time('mysql') ) ) ) {
										$send_email = 'send';	
									}
								}
							}
						}
					}			
					
					if ( $themes_hash_match == 0 ) {	
						fwrite( $handle, "Scanning Files: Themes: No Suspicious|Modified|Unknown Theme files were found.\r\n" );
					}
				}
				fwrite( $handle, "Scanning Files: Themes file scan completed.\r\n" );			
			}
			
			fwrite( $handle, "Scanning Files: Start wp-content, plugins and themes root index.php files scan.\r\n" );

			$index_file_hash_match = 0;
			$wp_content_index_file = WP_CONTENT_DIR . '/index.php';
			$plugins_index_file = WP_PLUGIN_DIR . '/index.php';
			$themes_index_file = get_theme_root() . '/index.php';
			
			$index_files_array = array( $wp_content_index_file, $plugins_index_file, $themes_index_file );
			
			foreach ( $index_files_array as $key => $value ) {
		
				if ( file_exists($value) ) {
				
					if ( md5_file($value) != '67442c5615eba73d105c0715c6620850' ) {
						
						$index_file_hash_match = 1;
						
						$file_path_forward_slashes = str_replace( '\\', '/', $value );
						$rel_file_path = str_replace( get_home_path(), '', $file_path_forward_slashes );
						fwrite( $handle, "Scanning Files: Suspicious|Modified|Unknown root index.php file detected: ../$rel_file_path\r\n" );

						if ( ! in_array($value, $mscan_file_path_array) ) {
							
							if ( $insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => 'suspect', 'mscan_type' => 'index.php files', 'mscan_path' => $value, 'mscan_pattern' => 'Altered or unknown index.php file', 'mscan_skipped' => '', 'mscan_ignored' => '', 'mscan_db_table' => '', 'mscan_db_column' => '', 'mscan_db_pkid' => '', 'mscan_time' => current_time('mysql') ) ) ) {
								$send_email = 'send';	
							}
						}				
					}
				
				} else {
					fwrite( $handle, "Scanning Files: index.php files: Missing File (file does not exist): $value\r\n" );
				}
			}

			if ( $index_file_hash_match == 0 ) {	
				fwrite( $handle, "Scanning Files: Themes: No Suspicious|Modified|Unknown root index.php files were found.\r\n" );
			}
			
			fwrite( $handle, "Scanning Files: wp-content, plugins and themes index.php files scan completed.\r\n" );

			fwrite( $handle, "Scanning Files: Start php, js, etc file scanning.\r\n" );
			fwrite( $handle, "Scanning Files: Suspicious code pattern matches:\r\n" );
	
			foreach ( $file_path_array as $key => $value ) {
		
				if ( file_get_contents($MScanStop) != 'run' ) { 
   					 fwrite( $handle, "Scanning Files: MScan Scanning was Stopped\r\n" );
					 fclose($handle);
					 exit();
						 
				} else {

					if ( is_readable($value) ) {

						$ext = pathinfo( strtolower($value), PATHINFO_EXTENSION );
						$file_contents = file_get_contents($value);
			
						if ( $ext == 'js' ) {
			
							if ( preg_match( $js_pattern, $file_contents, $matches ) ) {
								
								$js_code_match = 1;
							
								$string_length = strlen($matches[0]);
			
								if ( $string_length > 30 ) {
									$mscan_pattern = substr($matches[0], 0, 30);
								} else {
									$mscan_pattern = $matches[0];
								}							
	
								$file_path_forward_slashes = str_replace( '\\', '/', $value );
								$rel_file_path = str_replace( get_home_path(), '', $file_path_forward_slashes );
								fwrite( $handle, "Scanning Files .js: File: ../$rel_file_path\r\n" );
								fwrite( $handle, "Scanning Files .js: Code Pattern Match: $mscan_pattern\r\n" );
								
								if ( ! in_array($value, $mscan_file_path_array) ) {
								
									if ( $insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => 'suspect', 'mscan_type' => 'js', 'mscan_path' => $value, 'mscan_pattern' => esc_html($mscan_pattern), 'mscan_skipped' => '', 'mscan_ignored' => '', 'mscan_db_table' => '', 'mscan_db_column' => '', 'mscan_db_pkid' => '', 'mscan_time' => current_time('mysql') ) ) ) {
								
										$send_email = 'send';	
									}
								}				
							}
						}
			
						if ( $ext == 'htaccess' ) {
							
							if ( preg_match( $htaccess_pattern, $file_contents, $matches ) ) {
								
								$htaccess_code_match = 1;
								
								$string_length = strlen($matches[0]);
			
								if ( $string_length > 30 ) {
									$mscan_pattern = substr($matches[0], 0, 30);
								} else {
									$mscan_pattern = $matches[0];
								}							
								
								$file_path_forward_slashes = str_replace( '\\', '/', $value );
								$rel_file_path = str_replace( get_home_path(), '', $file_path_forward_slashes );
								fwrite( $handle, "Scanning Files .htaccess: File: ../$rel_file_path\r\n" );
								fwrite( $handle, "Scanning Files .htaccess: Code Pattern Match: $mscan_pattern\r\n" );
								
								if ( ! in_array($value, $mscan_file_path_array) ) {
								
									if ( $insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => 'suspect', 'mscan_type' => 'htaccess', 'mscan_path' => $value, 'mscan_pattern' => esc_html($mscan_pattern), 'mscan_skipped' => '', 'mscan_ignored' => '', 'mscan_db_table' => '', 'mscan_db_column' => '', 'mscan_db_pkid' => '', 'mscan_time' => current_time('mysql') ) ) ) {
										$send_email = 'send';	
									}
								}
							}
						}
				
						if ( $ext == 'htm' || $ext == 'html' || $ext == 'php' || $ext == 'phps' || $ext == 'php5' || $ext == 'php4' || $ext == 'php3' || $ext == 'phtml' || $ext == 'phpt' || $ext == 'shtm' || $ext == 'shtml' || $ext == 'xhtml' || $ext == 'ico' || $ext == 'bak' ) {
							
							if ( preg_match( $php_pattern, $file_contents, $matches ) ) {					
			
								$php_code_match = 1;
								
								$string_length = strlen($matches[0]);
			
								if ( $string_length > 30 ) {
									$mscan_pattern = substr($matches[0], 0, 30);
								} else {
									$mscan_pattern = $matches[0];
								}							
								
								$file_path_forward_slashes = str_replace( '\\', '/', $value );
								$rel_file_path = str_replace( get_home_path(), '', $file_path_forward_slashes );
								fwrite( $handle, "Scanning Files php, html, etc: File: ../$rel_file_path\r\n" );
								fwrite( $handle, "Scanning Files php, html, etc: Code Pattern Match: $mscan_pattern\r\n" );
			
								if ( ! in_array($value, $mscan_file_path_array) ) {
									
									if ( $insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => 'suspect', 'mscan_type' => 'php|html|other', 'mscan_path' => $value, 'mscan_pattern' => esc_html($mscan_pattern), 'mscan_skipped' => '', 'mscan_ignored' => '', 'mscan_db_table' => '', 'mscan_db_column' => '', 'mscan_db_pkid' => '', 'mscan_time' => current_time('mysql') ) ) ) {
										$send_email = 'send';	
									}
								}
							}
						}
					
					} else {
						
						$file_path_forward_slashes = str_replace( '\\', '/', $value );
						$rel_file_path = str_replace( get_home_path(), '', $file_path_forward_slashes );
						fwrite( $handle, "Scanning Files: php, js, html, etc: This file is not readable: ../$rel_file_path\r\n" );						
					}
				}
			} 
	
			if ( $js_code_match == 0 ) {
				fwrite( $handle, "Scanning Files .js: No Suspicious .js code pattern matches were found.\r\n" );
			}
	
			if ( $htaccess_code_match == 0 ) {
				fwrite( $handle, "Scanning Files .htaccess: No Suspicious .htaccess code pattern matches were found.\r\n" );
			}
	
			if ( $php_code_match == 0 ) {
				fwrite( $handle, "Scanning Files php, html, etc: No Suspicious php, html, etc code pattern matches were found.\r\n" );
			}		
			
			fwrite( $handle, "Scanning Files: php, js, etc file scanning completed.\r\n" );
	
			$sapi_type = php_sapi_name();
			$tmp_file_path_array = array();
			$tmp_file_code_match = 0;
			$plugins_folder = WP_PLUGIN_DIR;
			$file_owner_plugins_dir = fileowner( $plugins_folder );				

			if ( substr($sapi_type, 0, 6) == 'apache' && preg_match( '#\\\\#', ABSPATH, $matches ) ) {
				$upload_tmp_dir = ini_get('upload_tmp_dir');
				$file_owner_tmp_dir = fileowner( $upload_tmp_dir );			
				
				if ( is_dir( $upload_tmp_dir ) && is_readable( $upload_tmp_dir ) && $file_owner_plugins_dir == $file_owner_tmp_dir ) {
				
					$local_tmp_files = scandir($upload_tmp_dir);
					
					foreach ( $local_tmp_files as $file ) {
						
						if ( $file != '.' && $file != '..' && $file != 'mysql.sock' && $file != '.s.PGSQL.5432' && $file != '.per-user' ) {
	
							$tmp_file_path_array[] = $upload_tmp_dir .'/'. $file;;
						}
					}
				}
	
			} else {
	
				if ( function_exists('sys_get_temp_dir') ) {
					$sys_get_temp_dir = sys_get_temp_dir();
					$file_owner_tmp_dir = fileowner( $sys_get_temp_dir );				
					
					if ( is_dir( $sys_get_temp_dir ) && is_readable( $sys_get_temp_dir ) && $file_owner_plugins_dir == $file_owner_tmp_dir ) {
		
						$tmp_files = scandir($sys_get_temp_dir);
						
						foreach ( $tmp_files as $file ) {
					
							if ( $file != '.' && $file != '..' && $file != 'mysql.sock' && $file != '.s.PGSQL.5432' && $file != '.per-user' ) {
								
								$tmp_file_path_array[] = $sys_get_temp_dir .'/'. $file;
							}
						}
					}
				}
			}

			fwrite( $handle, "Scanning Files Tmp Files.\r\n" );
			
			if ( ! empty($tmp_file_path_array) ) {
			
				foreach ( $tmp_file_path_array as $key => $value ) {
			
					if ( file_get_contents($MScanStop) != 'run' ) { 
						 fwrite( $handle, "Scanning Files: MScan Scanning was Stopped\r\n" );
						 fclose($handle);
						 exit();
							 
					} else {
	
						if ( is_readable($value) ) {
						
							$file_contents = file_get_contents($value);
		
							if ( preg_match( $php_pattern, $file_contents, $matches ) ) {					
			
								$tmp_file_code_match = 1;
								
								$string_length = strlen($matches[0]);
			
								if ( $string_length > 30 ) {
									$mscan_pattern = substr($matches[0], 0, 30);
								} else {
									$mscan_pattern = $matches[0];
								}							
								
								if ( substr($sapi_type, 0, 6) == 'apache' && preg_match( '#\\\\#', ABSPATH, $matches ) ) {
									$upload_tmp_dir = ini_get('upload_tmp_dir');
									$rel_file_path = str_replace( $upload_tmp_dir, '', $value );
								} else {
									$sys_get_temp_dir = sys_get_temp_dir();
									$rel_file_path = str_replace( $sys_get_temp_dir, '', $value );
								}
								
								fwrite( $handle, "Scanning Files Tmp Files: File: ..$rel_file_path\r\n" );
								fwrite( $handle, "Scanning Files Tmp Files: Code Pattern Match: $mscan_pattern\r\n" );
			
								if ( ! in_array($value, $mscan_file_path_array) ) {
									
									if ( $insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => 'suspect', 'mscan_type' => 'tmp', 'mscan_path' => $value, 'mscan_pattern' => esc_html($mscan_pattern), 'mscan_skipped' => '', 'mscan_ignored' => '', 'mscan_db_table' => '', 'mscan_db_column' => '', 'mscan_db_pkid' => '', 'mscan_time' => current_time('mysql') ) ) ) {
										$send_email = 'send';	
									}
								}
							}
						
						} else {
							
							if ( substr($sapi_type, 0, 6) == 'apache' && preg_match( '#\\\\#', ABSPATH, $matches ) ) {
								$upload_tmp_dir = ini_get('upload_tmp_dir');
								$rel_file_path = str_replace( $upload_tmp_dir, '', $value );
							} else {
								$sys_get_temp_dir = sys_get_temp_dir();
								$rel_file_path = str_replace( $sys_get_temp_dir, '', $value );
							}
							
							fwrite( $handle, "Scanning Files Tmp Files: This tmp file is not readable: ..$rel_file_path\r\n" );
						}
					}
				}
			}

			if ( $tmp_file_code_match == 0 ) {
				fwrite( $handle, "Scanning Files Tmp Files: No Suspicious tmp file code pattern matches were found.\r\n" );
			}	
			
			fwrite( $handle, "Scanning Files: Scanning files completed.\r\n" );
			
			if ( $MScan_options['mscan_scan_database'] == 'On' ) {
			
				fwrite( $handle, "Scanning Database: Start database scan.\r\n" );
			
				$db_code_match = 0;
				$DBTables = '';
				$getDBTables = $wpdb->get_results( $wpdb->prepare( "SHOW TABLE STATUS WHERE Name != %s", $DBTables ) );
			
				foreach ( $getDBTables as $Table ) {
		
					if ( $Table->Name != $wpdb->prefix . "bpspro_mscan" ) {
					
						$getColumns = $wpdb->get_results( "SHOW COLUMNS FROM $Table->Name" );
						
						foreach ( $getColumns as $column ) {
			
							$Search_Tables = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `$Table->Name` WHERE `$column->Field` LIKE %s OR `$column->Field` LIKE %s OR `$column->Field` LIKE %s OR `$column->Field` LIKE %s OR `$column->Field` LIKE %s OR `$column->Field` LIKE %s OR `$column->Field` LIKE %s OR `$column->Field` LIKE %s OR `$column->Field` LIKE %s", "%$search1%", "%$search2%", "%$search3%", "%$search4%", "%$search5%", "%$search6%", "%$search7%", "%$search8%", "%$search9%" ) );
							
							if ( $wpdb->num_rows != 0 ) {
			
								foreach ( $Search_Tables as $results ) {
									
									if ( file_get_contents($MScanStop) != 'run' ) { 
   					 					fwrite( $handle, "Scanning Database: MScan Scanning was Stopped\r\n" );
					 					fclose($handle);
					 					exit();
						 
									} else {									
									
										if ( @!preg_match( '/_transient_feed_(.*)/', $results->option_name ) && @!preg_match( '/bulletproof_security_options_mscan_(.*)/', $results->option_name ) ) {
										
											$getKey = $wpdb->get_results( "SHOW KEYS FROM $Table->Name WHERE Key_name = 'PRIMARY'" );
											
											foreach ( $getKey as $PKey ) {
				
											}
				
											$json_array = json_decode(json_encode($results), true);
											$patterns = array ( '/</', '/>/' );
											$replace = array ( '&lt;', '&gt;' );
											$json_array_converted = preg_replace( $patterns, $replace, $json_array );

											if ( in_array( $json_array_converted[$PKey->Column_name], $mscan_db_ignore_array ) ) {
												unset($json_array[$column->Field]);
											}
											
											if ( preg_grep( $eval_match, $json_array ) ) {
												$db_code_match = 1;
												
												$db_table_no_prefix = preg_replace( '/'.$wpdb->prefix.'/', "", $Table->Name );
			
												fwrite( $handle, "Scanning Database: DB Table (w/o Prefix): $db_table_no_prefix | Column|Field: $column->Field | Primary Key ID: ".$json_array_converted[$PKey->Column_name]."\r\n" );
												fwrite( $handle, "Scanning Database: Code Pattern Match: $eval_text\r\n" );
			
												if ( ! in_array($json_array_converted[$PKey->Column_name], $mscan_db_pkid_array) ) {
									
													if ( $insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => 'suspect', 'mscan_type' => 'db', 'mscan_path' => '', 'mscan_pattern' => esc_html($eval_text), 'mscan_skipped' => '', 'mscan_ignored' => '', 'mscan_db_table' => $Table->Name, 'mscan_db_column' => $column->Field, 'mscan_db_pkid' => $json_array_converted[$PKey->Column_name], 'mscan_time' => current_time('mysql') ) ) ) {
									
														$send_email = 'send';	
													}
												}
											}
											
											if ( preg_grep( '/<script/i', $json_array ) ) {
												$db_code_match = 1;
			
												$db_table_no_prefix = preg_replace( '/'.$wpdb->prefix.'/', "", $Table->Name );
												
												fwrite( $handle, "Scanning Database: DB Table (w/o Prefix): $db_table_no_prefix | Column|Field: $column->Field | Primary Key ID: ".$json_array_converted[$PKey->Column_name]."\r\n" );
												fwrite( $handle, "Scanning Database: Code Pattern Match: <script\r\n" );
												
												if ( ! in_array($json_array_converted[$PKey->Column_name], $mscan_db_pkid_array) ) {
									
													if ( $insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => 'suspect', 'mscan_type' => 'db', 'mscan_path' => '', 'mscan_pattern' => esc_html('<script'), 'mscan_skipped' => '', 'mscan_ignored' => '', 'mscan_db_table' => $Table->Name, 'mscan_db_column' => $column->Field, 'mscan_db_pkid' => $json_array_converted[$PKey->Column_name], 'mscan_time' => current_time('mysql') ) ) ) {
									
														$send_email = 'send';	
													}
												}
											}							
											
											if ( preg_grep( '/<iframe/i', $json_array ) ) {
												$db_code_match = 1;
				
												$db_table_no_prefix = preg_replace( '/'.$wpdb->prefix.'/', "", $Table->Name );
												
												fwrite( $handle, "Scanning Database: DB Table (w/o Prefix): $db_table_no_prefix | Column|Field: $column->Field | Primary Key ID: ".$json_array_converted[$PKey->Column_name]."\r\n" );
												fwrite( $handle, "Scanning Database: Code Pattern Match: <iframe\r\n" );
			
												if ( ! in_array($json_array_converted[$PKey->Column_name], $mscan_db_pkid_array) ) {
									
													if ( $insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => 'suspect', 'mscan_type' => 'db', 'mscan_path' => '', 'mscan_pattern' => esc_html('<iframe'), 'mscan_skipped' => '', 'mscan_ignored' => '', 'mscan_db_table' => $Table->Name, 'mscan_db_column' => $column->Field, 'mscan_db_pkid' => $json_array_converted[$PKey->Column_name], 'mscan_time' => current_time('mysql') ) ) ) {
									
														$send_email = 'send';	
													}
												}
											}
											
											if ( preg_grep( '/<noscript/i', $json_array ) ) {
												$db_code_match = 1;
											
												$db_table_no_prefix = preg_replace( '/'.$wpdb->prefix.'/', "", $Table->Name );
												
												fwrite( $handle, "Scanning Database: DB Table (w/o Prefix): $db_table_no_prefix | Column|Field: $column->Field | Primary Key ID: ".$json_array_converted[$PKey->Column_name]."\r\n" );
												fwrite( $handle, "Scanning Database: Code Pattern Match: <noscript\r\n" );
			
												if ( ! in_array($json_array_converted[$PKey->Column_name], $mscan_db_pkid_array) ) {
									
													if ( $insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => 'suspect', 'mscan_type' => 'db', 'mscan_path' => '', 'mscan_pattern' => esc_html('<noscript'), 'mscan_skipped' => '', 'mscan_ignored' => '', 'mscan_db_table' => $Table->Name, 'mscan_db_column' => $column->Field, 'mscan_db_pkid' => $json_array_converted[$PKey->Column_name], 'mscan_time' => current_time('mysql') ) ) ) {
									
														$send_email = 'send';	
													}
												}							
											}
				
											if ( preg_grep( '/visibility:/i', $json_array ) ) {
												$db_code_match = 1;
				
												$db_table_no_prefix = preg_replace( '/'.$wpdb->prefix.'/', "", $Table->Name );
												
												fwrite( $handle, "Scanning Database: DB Table (w/o Prefix): $db_table_no_prefix | Column|Field: $column->Field | Primary Key ID: ".$json_array_converted[$PKey->Column_name]."\r\n" );
												fwrite( $handle, "Scanning Database: Code Pattern Match: visibility:\r\n" );									
												
												if ( ! in_array($json_array_converted[$PKey->Column_name], $mscan_db_pkid_array) ) {
									
													if ( $insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => 'suspect', 'mscan_type' => 'db', 'mscan_path' => '', 'mscan_pattern' => esc_html('visibility:'), 'mscan_skipped' => '', 'mscan_ignored' => '', 'mscan_db_table' => $Table->Name, 'mscan_db_column' => $column->Field, 'mscan_db_pkid' => $json_array_converted[$PKey->Column_name], 'mscan_time' => current_time('mysql') ) ) ) {
									
														$send_email = 'send';	
													}
												}
											}
				
											if ( preg_grep( $base64_decode_match, $json_array ) ) {
												$db_code_match = 1;
				
												$db_table_no_prefix = preg_replace( '/'.$wpdb->prefix.'/', "", $Table->Name );
												
												fwrite( $handle, "Scanning Database: DB Table (w/o Prefix): $db_table_no_prefix | Column|Field: $column->Field | Primary Key ID: ".$json_array_converted[$PKey->Column_name]."\r\n" );
												fwrite( $handle, "Scanning Database: Code Pattern Match: $base64_decode_text\r\n" );								
			
			
												if ( ! in_array($json_array_converted[$PKey->Column_name], $mscan_db_pkid_array) ) {
									
													if ( $insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => 'suspect', 'mscan_type' => 'db', 'mscan_path' => '', 'mscan_pattern' => esc_html($base64_decode_text), 'mscan_skipped' => '', 'mscan_ignored' => '', 'mscan_db_table' => $Table->Name, 'mscan_db_column' => $column->Field, 'mscan_db_pkid' => $json_array_converted[$PKey->Column_name], 'mscan_time' => current_time('mysql') ) ) ) {
									
														$send_email = 'send';	
													}
												}							
											}							
										}
									}					
								}
							}
						}
					}
				}		
			
				$search10 = 'wp_check_hash';		
				$search11 = 'ftp_credentials';
				$search12 = 'class_generic_support';
				$search13 = 'widget_generic_support';
				
				$pharma_hack = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name = %s OR option_name = %s OR option_name = %s OR option_name = %s", $search10, $search11, $search12, $search13 ) );
				
				if ( $wpdb->num_rows != 0 ) {
				
					foreach ( $pharma_hack as $row ) {
				
						if ( ! in_array( 'PharmaHack', $mscan_db_ignore_pattern_array ) ) {
							$db_code_match = 1;
					
							$db_table_no_prefix = preg_replace( '/'.$wpdb->prefix.'/', "", $wpdb->options );
							
							fwrite( $handle, "Scanning Database: DB Table (w/o Prefix): $db_table_no_prefix | Column|Field: option_name\r\n" );
							fwrite( $handle, "Scanning Database: Pharma Hack found. Delete these option_name rows below from your WP Database:\r\n" );
							fwrite( $handle, "Scanning Database: wp_check_hash, class_generic_support, widget_generic_support, ftp_credentials and fwp.\r\n" );			
					
						}
					
						if ( ! in_array( 'PharmaHack', $mscan_db_pattern_array ) ) {
							
							if ( $insert_rows = $wpdb->insert( $MStable, array( 'mscan_status' => 'suspect', 'mscan_type' => 'db', 'mscan_path' => '', 'mscan_pattern' => 'PharmaHack', 'mscan_skipped' => '', 'mscan_ignored' => '', 'mscan_db_table' => $wpdb->options, 'mscan_db_column' => 'option_name', 'mscan_db_pkid' => '999999', 'mscan_time' => current_time('mysql') ) ) ) {
							
								$send_email = 'send';	
							}
						}		
					}
				}
				
				if ( $db_code_match == 0 ) {
					fwrite( $handle, "Scanning Database: No Suspicious code was found in any database tables.\r\n" );
				}				
				
				fwrite( $handle, "Scanning Database: Database scan completed.\r\n" );
			} 
		} 

		if ( $MScan_options['mscan_scan_skipped_files'] == 'On' ) {

			$skipped_rows = 'skipped';
			$ignored_rows = 'ignore';
			$MScanSkipRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_skipped = %s AND mscan_ignored != %s", $skipped_rows, $ignored_rows ) );
			
			if ( $wpdb->num_rows != 0 ) {

				$image_code_match = 0;
				fwrite( $handle, "Scanning Skipped Files: Start Skipped file scan.\r\n" );
				fwrite( $handle, "Scanning Skipped Files: Suspicious code pattern matches:\r\n" );

				foreach ( $MScanSkipRows as $row ) {

					if ( file_get_contents($MScanStop) != 'run' ) { 
   						 fwrite( $handle, "Scanning Skipped Files: MScan Scanning was Stopped\r\n" );
						 fclose($handle);
						 exit();
						 
					} else {
					
						$file_contents = file_get_contents($row->mscan_path);	
					
						if ( $row->mscan_type == 'js' ) {
	
							if ( preg_match( $js_pattern, $file_contents, $matches ) ) {
	
								$js_code_match = 1;
								$file_path_forward_slashes = str_replace( '\\', '/', $row->mscan_path );
								$rel_file_path = str_replace( get_home_path(), '', $file_path_forward_slashes );
								fwrite( $handle, "Scanning Skipped Files .js: File: ../$rel_file_path\r\n" );
								fwrite( $handle, "Scanning Skipped Files .js: Code Pattern Match: $matches[0]\r\n" );
						
								$update_rows = $wpdb->update( $MStable, array( 'mscan_status' => 'suspect', 'mscan_pattern' => esc_html($matches[0]), 'mscan_time' => current_time('mysql') ), array( 'mscan_path' => $row->mscan_path ) );
								
								$send_email = 'send';							
	
							} else {
								$update_rows = $wpdb->update( $MStable, array( 'mscan_status' => 'clean', 'mscan_time' => current_time('mysql') ), array( 'mscan_path' => $row->mscan_path ) );							
							}
						}
	
						if ( $row->mscan_type == 'htaccess' ) {
					
							if ( preg_match( $htaccess_pattern, $file_contents, $matches ) ) {
						
								$htaccess_code_match = 1;
								$file_path_forward_slashes = str_replace( '\\', '/', $row->mscan_path );
								$rel_file_path = str_replace( get_home_path(), '', $file_path_forward_slashes );
								fwrite( $handle, "Scanning Skipped Files .htaccess: File: ./$rel_file_path\r\n" );
								fwrite( $handle, "Scanning Skipped Files .htaccess: Code Pattern Match: $matches[0]\r\n" );
						
								$update_rows = $wpdb->update( $MStable, array( 'mscan_status' => 'suspect', 'mscan_pattern' => esc_html($matches[0]), 'mscan_time' => current_time('mysql') ), array( 'mscan_path' => $row->mscan_path ) );
								
								$send_email = 'send';

							} else {
								$update_rows = $wpdb->update( $MStable, array( 'mscan_status' => 'clean', 'mscan_time' => current_time('mysql') ), array( 'mscan_path' => $row->mscan_path ) );							
							}
						}
		
						if ( $row->mscan_type == 'php|html|other' ) {

							if ( preg_match( $php_pattern, $file_contents, $matches ) ) {					
		
								$php_code_match = 1;
								$file_path_forward_slashes = str_replace( '\\', '/', $row->mscan_path );
								$rel_file_path = str_replace( get_home_path(), '', $file_path_forward_slashes );
								fwrite( $handle, "Scanning Skipped Files php, html, etc: File: ./$rel_file_path\r\n" );
								fwrite( $handle, "Scanning Skipped Files php, html, etc: Code Pattern Match: $matches[0]\r\n" );
		
								$update_rows = $wpdb->update( $MStable, array( 'mscan_status' => 'suspect', 'mscan_pattern' => esc_html($matches[0]), 'mscan_time' => current_time('mysql') ), array( 'mscan_path' => $row->mscan_path ) );
								
								$send_email = 'send';
	
							} else {
								$update_rows = $wpdb->update( $MStable, array( 'mscan_status' => 'clean', 'mscan_time' => current_time('mysql') ), array( 'mscan_path' => $row->mscan_path ) );							
							}
						}
					}
				}			
			
				if ( $js_code_match == 0 ) {
					fwrite( $handle, "Scanning Skipped Files .js: No Suspicious .js code pattern matches were found.\r\n" );
				}
		
				if ( $htaccess_code_match == 0 ) {
					fwrite( $handle, "Scanning Skipped Files .htaccess: No Suspicious .htaccess code pattern matches were found.\r\n" );
				}
		
				if ( $php_code_match == 0 ) {
					fwrite( $handle, "Scanning Skipped Files: php, html, etc: No Suspicious php, html, etc code pattern matches were found.\r\n" );
				}		
				
				fwrite( $handle, "Scanning Skipped Files: Skipped file scan completed.\r\n" );
		
			} else {
				fwrite( $handle, "Scanning Skipped Files: Either there are no skipped files to scan or a Skipped File Scan was run before a regular scan was run.\r\n" );
			}
		} 

		$suspect_rows = 'suspect';
		$ignored_rows = 'ignore';
		$skipped_rows = 'skipped';
		$db_rows = 'db';
		
		$MScanSuspectFilesRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_status = %s AND mscan_ignored != %s AND mscan_skipped != %s AND mscan_type != %s", $suspect_rows, $ignored_rows, $skipped_rows, $db_rows ) );
		
		$mscan_suspect_files_total_array = array();

		if ( $wpdb->num_rows != 0 ) {
		
			foreach ( $MScanSuspectFilesRows as $row ) {
				$mscan_suspect_files_total_array[] = $row->mscan_status;
			}
		}

		$MScanSuspectSkippedFilesRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_status = %s AND mscan_skipped = %s AND mscan_ignored != %s", $suspect_rows, $skipped_rows, $ignored_rows ) );

		$mscan_suspect_skipped_files_total_array = array();

		if ( $wpdb->num_rows != 0 ) {
		
			foreach ( $MScanSuspectSkippedFilesRows as $row ) {
				$mscan_suspect_skipped_files_total_array[] = $row->mscan_status;
			}
		}

		$MScanSuspectDBRows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $MStable WHERE mscan_status = %s AND mscan_type = %s AND mscan_ignored != %s", $suspect_rows, $db_rows, $ignored_rows ) );

		$mscan_suspect_db_total_array = array();

		if ( $wpdb->num_rows != 0 ) {
		
			foreach ( $MScanSuspectDBRows as $row ) {
				$mscan_suspect_db_total_array[] = $row->mscan_status;
			}
		}

		$MScan_status = get_option('bulletproof_security_options_MScan_status');

		$total_ignored_file_db_count = count($mscan_ignored_total_array);
		$total_suspect_file_count = count($mscan_suspect_files_total_array);
		$total_suspect_skipped_files_file_count = count($mscan_suspect_skipped_files_total_array);
		$total_suspect_db_count = count($mscan_suspect_db_total_array);
		$bps_mscan_total_time = time() - $MScan_status['bps_mscan_time_start'];
		
		$MScan_status_db = array( 
		'bps_mscan_time_start' 					=> $MScan_status['bps_mscan_time_start'], 
		'bps_mscan_time_stop' 					=> $MScan_status['bps_mscan_time_stop'], 
		'bps_mscan_time_end' 					=> time(), 
		'bps_mscan_time_remaining' 				=> $MScan_status['bps_mscan_time_remaining'], 
		'bps_mscan_status' 						=> '3', 
		'bps_mscan_last_scan_timestamp' 		=> $timestamp, 
		'bps_mscan_total_time' 					=> $bps_mscan_total_time, 
		'bps_mscan_total_website_files' 		=> '', 
		'bps_mscan_total_wp_core_files' 		=> $MScan_status['bps_mscan_total_wp_core_files'], 
		'bps_mscan_total_non_image_files' 		=> $MScan_status['bps_mscan_total_non_image_files'], 
		'bps_mscan_total_image_files' 			=> '', 
		'bps_mscan_total_all_scannable_files' 	=> $MScan_status['bps_mscan_total_all_scannable_files'], 
		'bps_mscan_total_skipped_files' 		=> $MScan_status['bps_mscan_total_skipped_files'], 
		'bps_mscan_total_suspect_files' 		=> $total_suspect_file_count, 
		'bps_mscan_suspect_skipped_files' 		=> $total_suspect_skipped_files_file_count, 
		'bps_mscan_total_suspect_db' 			=> $total_suspect_db_count, 
		'bps_mscan_total_ignored_files' 		=> $total_ignored_file_db_count, 
		'bps_mscan_total_plugin_files' 			=> $MScan_status['bps_mscan_total_plugin_files'], 			 
		'bps_mscan_total_theme_files' 			=> $MScan_status['bps_mscan_total_theme_files'] 
		);		
		
		foreach( $MScan_status_db as $key => $value ) {
			update_option('bulletproof_security_options_MScan_status', $MScan_status_db);
		}
	}

	$time_end = microtime( true );
	$file_scan_time = $time_end - $time_start;

	$hours = (int)($file_scan_time / 60 / 60);
	$minutes = (int)($file_scan_time / 60) - $hours * 60;
	$seconds = (int)$file_scan_time - $hours * 60 * 60 - $minutes * 60;
	$hours_format = $hours == 0 ? "00" : $hours;
	$minutes_format = $minutes == 0 ? "00" : ($minutes < 10 ? "0".$minutes : $minutes);
	$seconds_format = $seconds == 0 ? "00" : ($seconds < 10 ? "0".$seconds : $seconds);
	
	$hours2 = (int)($bps_mscan_total_time / 60 / 60);
	$minutes2 = (int)($bps_mscan_total_time / 60) - $hours2 * 60;
	$seconds2 = (int)$bps_mscan_total_time - $hours2 * 60 * 60 - $minutes2 * 60;
	$hours_format2 = $hours2 == 0 ? "00" : $hours2;
	$minutes_format2 = $minutes2 == 0 ? "00" : ($minutes2 < 10 ? "0".$minutes2 : $minutes2);
	$seconds_format2 = $seconds2 == 0 ? "00" : ($seconds2 < 10 ? "0".$seconds2 : $seconds2);

	if ( $MScan_options['mscan_scan_skipped_files'] == 'On' ) {
		$file_scan_log = 'Scanning Skipped Files Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;
		$file_scan_log_total_time = 'Total Scan Time: '. $hours_format2 . ':'. $minutes_format2 . ':' . $seconds_format2;	

	} else {

		if ( $MScan_options['mscan_scan_database'] == 'On' ) {
			$file_scan_log = 'Scanning Files & Database Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;
			$file_scan_log_total_time = 'Total Scan Time: '. $hours_format2 . ':'. $minutes_format2 . ':' . $seconds_format2;
		} else{
			$file_scan_log = 'Scanning Files Completion Time: '. $hours_format . ':'. $minutes_format . ':' . $seconds_format;
			$file_scan_log_total_time = 'Total Scan Time: '. $hours_format2 . ':'. $minutes_format2 . ':' . $seconds_format2;
		}
	}

	$MScan_status = get_option('bulletproof_security_options_MScan_status');	
	
	fwrite( $handle, "MScan Status: ".$MScan_status['bps_mscan_status']."\r\n" );	
	fwrite( $handle, "$file_scan_log\r\n" );

	if ( $MScan_options['mscan_scan_delete_tmp_files'] == 'On' ) {
		bpsPro_delete_temp_files();
		fwrite( $handle, "Delete /tmp Files: tmp files have been deleted.\r\n" );
	}

	fwrite( $handle, "$file_scan_log_total_time\r\n" );
	
	fclose($handle);

	if ( $send_email != '' ) {
		
	}
}

function bpsPro_delete_temp_files() {
	
	$MScan_options = get_option('bulletproof_security_options_MScan');
	
	if ( $MScan_options['mscan_exclude_tmp_files'] != '' ) {
		$mscan_exclude_tmp_files_array = explode( "\n", $MScan_options['mscan_exclude_tmp_files'] );
	}

	if ( ! empty($mscan_exclude_tmp_files_array) ) {
	
		$mscan_exclude_tmp_files_array_trim = array();

		foreach ( $mscan_exclude_tmp_files_array as $key => $value ) {
			$mscan_exclude_tmp_files_array_trim[] = trim($value);
		}
		
		$mscan_exclude_tmp_files_array_filter = array_filter($mscan_exclude_tmp_files_array_trim);
	
		$sapi_type = php_sapi_name();
		
		if ( substr($sapi_type, 0, 6) == 'apache' && preg_match( '#\\\\#', ABSPATH, $matches ) ) {
			$upload_tmp_dir = ini_get('upload_tmp_dir');
		
			if ( is_dir( $upload_tmp_dir ) && is_readable( $upload_tmp_dir ) ) {
			
				$local_tmp_files = scandir($upload_tmp_dir);
				$local_tmp_files_array_diff = array_diff( $local_tmp_files, $mscan_exclude_tmp_files_array_filter );
				
				foreach ( $local_tmp_files_array_diff as $file ) {
					
					if ( $file != '.' && $file != '..' && $file != 'why.tmp' && $file != 'mysql.sock' && $file != '.s.PGSQL.5432' && $file != '.per-user' ) {
						unlink($upload_tmp_dir.'/'.$file);
					}
				}
			}
	
		} else {
		
			if ( function_exists('sys_get_temp_dir') ) {
				$sys_get_temp_dir = sys_get_temp_dir();
			
				if ( is_dir( $sys_get_temp_dir ) && is_readable( $sys_get_temp_dir ) ) {
	
					$tmp_files = scandir($sys_get_temp_dir);
					$tmp_files_array_diff = array_diff( $tmp_files, $mscan_exclude_tmp_files_array_filter );			
					
					foreach ( $tmp_files_array_diff as $file ) {
				
						if ( $file != '.' && $file != '..' && $file != 'mysql.sock' && $file != '.s.PGSQL.5432' && $file != '.per-user' ) {
							unlink($sys_get_temp_dir.'/'.$file);
						}
					}
				}
			}
		}
	}
}
?>