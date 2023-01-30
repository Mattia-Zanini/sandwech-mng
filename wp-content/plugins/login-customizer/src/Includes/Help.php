<?php

/**
 * Log file to know more about users website environment.
 * helps in debugging and providing support.
 *
 * @package		LoginCustomizer
 * @since		2.2.0
 * @version		2.2.0
 */
namespace LoginCustomizer\Includes;
use LoginCustomizer\Essentials;

class Help {

	/**
	 * Returns the plugin & system information.
	 * @access public
	 * @return string
	 */
	public static function get_sysinfo() {

		global $wpdb;
		new Essentials;

		$settings         = get_option( 'logincust_setting' );
		$login_customizer = get_option( 'login_customizer_options' );
		$login_order      = isset( $settings['login_order'] ) ? $settings['login_order'] : 'Default';
		$auto_remember_me = isset( $settings['auto_remember_me'] ) ? $settings['auto_remember_me'] : 'off';
		$auto_remember_me = ( 'off' === $auto_remember_me ) ? 'Disabled' : 'Enabled';
		$customization    = isset( $login_customizer ) ? print_r( $login_customizer, true ) : 'No customization yet';
		$enable_switcher  = isset( $settings['enable_language_switcher'] ) ? $settings['enable_language_switcher'] : 'off';
		$enable_switcher  = ( 'off' === $enable_switcher ) ? 'Disabled' : 'Enabled';

		$html = '### Begin System Info ###' . "\n\n";

		// Basic site info
		$html .= '-- WordPress Configuration --' . "\n\n";
		$html .= 'Site URL:                 ' . site_url() . "\n";
		$html .= 'Home URL:                 ' . home_url() . "\n";
		$html .= 'Multisite:                ' . ( is_multisite() ? 'Yes' : 'No' ) . "\n";
		$html .= 'Version:                  ' . get_bloginfo( 'version' ) . "\n";
		$html .= 'Language:                 ' . get_locale() . "\n";
		$html .= 'Table Prefix:             ' . 'Length: ' . strlen( $wpdb->prefix ) . "\n";
		$html .= 'WP_DEBUG:                 ' . ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ) . "\n";
		$html .= 'Memory Limit:             ' . WP_MEMORY_LIMIT . "\n";

		// Plugin Configuration
		$html .= "\n" . '-- Login Customizer Configuration --' . "\n\n";
		$html .= 'Plugin Version:           ' . LOGINCUST_FREE_VERSION . "\n";
		$html .= 'Login Order:              ' . ucfirst( $login_order ) . "\n";
		$html .= 'Auto Remember Me:         ' . ucfirst( $auto_remember_me ) . "\n";

		/**
		 * Add option to remove language switcher option
		 *
		 * @since 2.1.7
		 */
		if ( version_compare( $GLOBALS['wp_version'], '5.9', '>=' ) && ! empty( get_available_languages() ) ) {

		$html .= 'Language Switcher:        ' . ucfirst( $enable_switcher ) . "\n";

		}
		// Server Configuration.
		$html .= "\n" . '-- Server Configuration --' . "\n\n";
		$html .= 'Operating System:         ' . php_uname( 's' ) . "\n";
		$html .= 'PHP Version:              ' . PHP_VERSION . "\n";
		$html .= 'MySQL Version:            ' . $wpdb->db_version() . "\n";

		$html .= 'Server Software:          ' . $_SERVER['SERVER_SOFTWARE'] . "\n";

		// PHP configs... now we're getting to the important stuff
		$html .= "\n" . '-- PHP Configuration --' . "\n\n";
		// $html .= 'Safe Mode:                ' . ( ini_get( 'safe_mode' ) ? 'Enabled' : 'Disabled' . "\n" );
		$html .= 'Memory Limit:             ' . ini_get( 'memory_limit' ) . "\n";
		$html .= 'Post Max Size:            ' . ini_get( 'post_max_size' ) . "\n";
		$html .= 'Upload Max Filesize:      ' . ini_get( 'upload_max_filesize' ) . "\n";
		$html .= 'Time Limit:               ' . ini_get( 'max_execution_time' ) . "\n";
		$html .= 'Max Input Vars:           ' . ini_get( 'max_input_vars' ) . "\n";
		$html .= 'Display Errors:           ' . ( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ) . "\n";

		// WordPress active themes
		$html .= "\n" . '-- WordPress Active Theme --' . "\n\n";
		$my_theme = wp_get_theme();
		$html .= 'Name:                     ' . $my_theme->get( 'Name' ) . "\n";
		$html .= 'URI:                      ' . $my_theme->get( 'ThemeURI' ) . "\n";
		$html .= 'Author:                   ' . $my_theme->get( 'Author' ) . "\n";
		$html .= 'Version:                  ' . $my_theme->get( 'Version' ) . "\n";

		// WordPress active plugins
		$html .= "\n" . '-- WordPress Active Plugins --' . "\n\n";
		$plugins = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );


		foreach ( $plugins as $plugin_path => $plugin ) {
			if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
				continue;
				$html .= $plugin['Name'] . ': v(' . $plugin['Version'] . ")\n";
			}
		}

		// WordPress inactive plugins
		$html .= "\n" . '-- WordPress Inactive Plugins --' . "\n\n";
		foreach( $plugins as $plugin_path => $plugin ) {
			if( in_array( $plugin_path, $active_plugins ) )
				continue;
			$html .= $plugin['Name'] . ': v(' . $plugin['Version'] . ")\n";
		}

		if( is_multisite() ) {
			// WordPress Multisite active plugins
			$html .= "\n" . '-- Network Active Plugins --' . "\n\n";
			$plugins = wp_get_active_network_plugins();
			$active_plugins = get_site_option( 'active_sitewide_plugins', array() );
			foreach( $plugins as $plugin_path ) {
				$plugin_base = plugin_basename( $plugin_path );
				if( ! array_key_exists( $plugin_base, $active_plugins ) )
					continue;
				$plugin  = get_plugin_data( $plugin_path );
				$html .= $plugin['Name'] . ': v(' . $plugin['Version'] . ")\n";
			}
		}
		$html .= "\n\n". __( 'Total Customized Fields:  ', 'login-customizer' ) . count( $login_customizer ) . "\n";
		$html .= __( 'Customization Detail:', 'login-customizer' );
		$html .= $customization;


		$html .= "\n" . '### End System Info ###';
		return $html;
	}
} // End of Class.
