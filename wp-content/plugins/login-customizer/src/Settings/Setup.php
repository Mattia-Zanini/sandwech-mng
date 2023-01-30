<?php
/**
 * login Customizer setup settings
 * Which includes the WordPress Settings API and Log Data.
 * 
 * @package 	    LoginCustomizer
 * @author 			WPBrigade
 * @copyright 		Copyright (c) 2021, WPBrigade
 * @link 			https://loginpress.pro/
 * @license			https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since 2.2.0
 */

namespace LoginCustomizer\Settings;
use LoginCustomizer\Settings\API;
use LoginCustomizer\Includes\Help;

class Setup {

	private $settings_api;

	/**
	 * Constructor
	 * @since 2.2.0
	 * 
	 */
	function __construct() {

		$this->settings_api = new API;
		add_action( 'admin_init', array( $this, 'logincust_setting_init' ) );
		add_action( 'admin_menu', array( $this, 'logincust_setting_menu' ) );
		// add_action( 'wp_enqueue_scripts', array( $this, 'logincust_setting_enqueue' ) );
		add_action( 'wp_ajax_logincust_help', array( $this, 'logincust_help' ) );
    }

	/**
	 * Download log file functionality callback function.
	 *
	 * @since 2.2.0
	 * @return void
	 */
	function logincust_help() {

		check_ajax_referer( 'login_customizer_log_nonce', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'No cheating, huh!' );
		}

		$log =  Help::get_sysinfo();
		echo $log;
		wp_die();
	}

	/**
	 * Initialize Login Customizer settings.
	 */
	function logincust_setting_init() {

		//Set the settings sections & fields.
		$this->settings_api->set_sections( $this->get_settings_sections() );
		$this->settings_api->set_fields( $this->get_settings_fields() );

		//Initialize settings.
		$this->settings_api->admin_init();
	}

	/**
	 * Login Customizer Admin Menu's
	 * @since 2.2.0
	 */ 
	function logincust_setting_menu() {

		global $submenu;
		add_action( 'admin_head', array( $this, 'loginCustomizerIcon' ) ); // admin_head is a hook loginCustomizerIcon is a function we are adding it to the hook

		$options = get_option( 'login_customizer_settings', array() );

		$url = add_query_arg( array(
			'autofocus[panel]' => 'logincust_panel',
			'url' => rawurlencode( get_permalink( $options['page'] ) ),
			),
			admin_url( 'customize.php' )
		);

		$submenu['themes.php'][] = array( 'Login Customizer', 'manage_options', $url, 'login-customizer' );
		//Side-bar Menu listing
		add_menu_page( __( 'Login Customizer', 'login-customizer' ), __( 'Login Customizer', 'login-customizer' ), 'manage_options', "login-customizer-settings", array( $this, 'plugin_page' ), false, 50 );

		add_submenu_page( 'login-customizer-settings', __( 'Settings', 'login-customizer' ), __( 'Settings', 'login-customizer' ), 'manage_options', "login-customizer-settings", array( $this, 'plugin_page' ) );

		add_submenu_page( 'login-customizer-settings', __( 'Customizer', 'login-customizer' ), __( 'Customizer', 'login-customizer' ), 'manage_options', "$url" );

		add_submenu_page( 'login-customizer-settings', __( 'Help', 'login-customizer' ), __( 'Help', 'login-customizer' ), 'manage_options', "login-customizer-help", array( $this, 'logincust_help_page' ) );

	}

	/**
	 * Style for Login Customizer Dashicon
	 * @since 2.2.0
	 * @return void
	 */
	function loginCustomizerIcon() {

		$dashicon = LOGINCUST_FREE_RESOURCES . '/login-customizer-dashicon/icomoon.';
		$rand = '?mhskqw';

		$ttf   = $dashicon . 'ttf' . $rand;
		$woff  = $dashicon . 'woff' . $rand;
		$svg   = $dashicon . 'svg' . $rand;
		$eotie = $dashicon . 'eot' . $rand . '#iefix';
		$eot   = $dashicon . 'eot' . $rand;

		echo "<style>
		@font-face {
		  font-family: 'login-customizer';
		  src:  url('".$eot."');
		  src:  url('".$eotie."') format('embedded-opentype'),
			url('".$ttf."') format('truetype'),
			url('".$woff."') format('woff'),
			url('".$svg."') format('svg');
		  font-weight: normal;
		  font-style: normal;
		}
  
		.icon-login-customizer-dashicon:before {
		  content: '\\e901';
		  color: #fff;
		}
  
		#adminmenu li#toplevel_page_login-customizer-settings>a>div.wp-menu-image:before{
		  content: '\\e901';
		  font-family: 'login-customizer' !important;
		  speak: none;
		  font-style: normal;
		  font-weight: normal;
		  font-variant: normal;
		  text-transform: none;
		  line-height: 1;
  
		  /* Better Font Rendering =========== */
		  -webkit-font-smoothing: antialiased;
		  -moz-osx-font-smoothing: grayscale;
		}
		</style>";
	  }

	/**
	* Settings section/s 
	* @since 2.2.0
	* @return void
	*/
	function get_settings_sections() {

		$options = get_option( 'login_customizer_settings', array() );

		$url = add_query_arg(
		array(
			'autofocus[panel]' => 'logincust_panel',
			'url' => rawurlencode( get_permalink( $options['page'] ) ),
			),
			admin_url( 'customize.php' )
		);

		$logincust_general_tab = array(
			array(
			'id'    => 'logincust_setting',
			'title' => __( 'Settings', 'login-customizer' ),
			'desc'  => sprintf( __( 'Everything else is customizable through %1$sLogin Customizer%2$s.', 'login-customizer' ), '<a href="' . $url . '">', '</a>' ),
			),
		);

		$sections = apply_filters( 'login_customizer_settings_tab', $logincust_general_tab );

		return $sections;
	}

	/**
	* Returns all the settings fields
	*
	* @return array settings fields
	*/
	function get_settings_fields() {

		/**
		* [$free_fields array of free fields]
		* @var array
		*/
		$free_fields = array(
			array(
				'name'  => 'auto_remember_me',
				'label' => __( 'Auto Remember Me', 'login-customizer' ),
				'desc'  => __( 'Keep remember me option always checked on login page', 'login-customizer' ),
				'type'  => 'checkbox',
			),
			array(
				'name'    => 'login_order',
				'label'   => __( 'Login Order', 'login-customizer' ),
				'desc'    => __( 'Enable users to login using their username or email address.', 'login-customizer' ),
				'type'    => 'radio',
				'default' => 'default',
				'options' => array(
					'default'  => __( 'Both Username Or Email Address', 'login-customizer' ),
					'username' => __( 'Only Username', 'login-customizer' ),
					'email'    => __( 'Only Email Address', 'login-customizer' ),
				),
			),
		);

		if ( '0' !== get_option( 'users_can_register' ) ) {
			$free_fields = $this->logincust_custom_register_field( $free_fields );
		}

		/**
		 * Add option to remove language switcher option
		 *
		 * @since 2.1.7
		 */
		if ( version_compare( $GLOBALS['wp_version'], '5.9', '>=' ) && ! empty( get_available_languages() ) ) {
			$free_fields = $this->logincust_language_switcher( $free_fields );
		}

		// Add Login Customizer uninstall field for the main blog on multi-site.
		$_free_fields     = $this->logincust_uninstall_tool( $free_fields );

		$_settings_fields = apply_filters( 'login_customizer_pro_settings', $_free_fields );
		$settings_fields  = array( 'logincust_setting' => $_settings_fields );
		$tab              = apply_filters( 'login_customizer_settings_fields', $settings_fields );

		return $tab;
	}

	/**
	 * Function logincust_uninstall_field [merge a uninstall Login Customizer field with array of element.]
	 * @param array $fields_list The free fields of Login Customizer.
	 * @since 2.1.9
	 * @return array the total fields which are to be removed on uninstall.
	 */
	function logincust_uninstall_field( $fields_list ) {

		$logincust_uninstall = array(
			array(
				'name'  => 'logincust_delete_all',
				'label' => __( 'Delete All Settings', 'login-customizer' ),
				'desc'  => esc_html__( 'Enable this option to delete every settings of this plugin on uninstall', 'login-customizer' ),
				'type'  => 'checkbox',
			)
		);
		return array_merge( $fields_list, $logincust_uninstall ); // merge an array and return.
	}

	/**
	 * Function logincust_uninstall_tool[Pass return true in logincust_multisite_uninstall_tool filter's callback for enable uninstall control on each site.]
	 *
	 * @param array $_free_fields [array of free fields]
	 * @since 2.1.9
	 * @return array
	 */
	function logincust_uninstall_tool( $_free_fields ) {

		if ( is_multisite() && ! apply_filters( 'logincust_multisite_uninstall_tool', false ) ) {
			if ( get_current_blog_id() == '1' ) {
				$_free_fields = $this->logincust_uninstall_field( $_free_fields );
			}
		} else {
				$_free_fields = $this->logincust_uninstall_field( $_free_fields );
		}

		return $_free_fields;
	}

	/**
	 * Function logincust_language_switcher [merge a language switcher in the settings element of array.]
	 *
	 * @param  array $fields_list The free fields of Login customizer.
	 * @since 2.1.7
	 * @version 2.1.9
	 * @return array the total fields including the added field of language switcher
	 */
	public function logincust_language_switcher( $fields_list ) {

		$language_switcher = array(
			array(
				'name'  => 'enable_language_switcher',
				'label' => __( 'Language Switcher', 'login-customizer' ),
				'desc'  => __( 'Remove Language Switcher Dropdown On Login Page. ', 'login-customizer' ),
				'type'  => 'checkbox',
			),
		);
		return array_merge( $fields_list, $language_switcher ); // merge an array and return.
	}

	/**
	* Function logincust_custom_register_field [merge a custom password field in the settings element of array.]
	*
	* @param  array $fields_list The free fields of Login customizer.
	* @since 2.1.7
	* @version 2.1.9
	* @return array the total fields including the added field of custom password field
	*/
	public function logincust_custom_register_field( $fields_list ) {

		$register_field_option  = array(
			array(
				'name'  => 'enable_reg_pass_field',
				'label' => __( 'Custom Password Fields', 'login-customizer' ),
				'desc'  => __( 'Enable custom password fields on registration form.', 'login-customizer' ),
				'type'  => 'checkbox',
			),
		);
		return array_merge( $fields_list, $register_field_option ); // merge an array and return.
	}


	/**
	 * Create the plugin's settings page.
	 * @since 2.2.0
	 * @return void
	 */
	function plugin_page() {

		$image = LOGINCUST_FREE_URL.'Settings/Assets/logincust-settings-icon.png';
		echo '<div class="wrap logincust-admin-setting">';
		echo '<h2 class="admin-settings-title">';
		echo "<img src= '$image' class='settings-img'>";
		_e( 'Login Customizer - Design your boring WordPress Login page', 'login-customizer' );
		echo '</h2>';

		$this->settings_api->show_navigation();
		$this->settings_api->show_forms();

		echo '</div>';
	}

	/**
	* Create the plugin's Help page which load the plugin log information.
	* @since 2.2.0
	* @return void
	*/
	function logincust_help_page() {

		$html = '<div class="logincust-help-page">';
		$html .= '<h2>Help & Troubleshooting</h2>';
		$html .= sprintf( __( 'Free plugin support is available on the %1$s plugin support forums%2$s.', 'login-customizer' ), '<a href="https://wordpress.org/support/plugin/login-customizer" target="_blank">', '</a>' );
		// $html .="<br /><br />";

		$html .="<br />";
		// $html .= 'Found a bug or have a feature request? Please submit an issue <a href="https://loginpress.pro/contact/" target="_blank">here</a>!';
		$html .= '<pre><textarea rows="25" cols="75" readonly="readonly">';
		$html .=  Help::get_sysinfo();
		$html .= '</textarea></pre>';
		$html .= '<input type="button" class="button logincust-log-file" value="' . __( 'Download Log File', 'login-customizer' ) . '"/>';
		$html .= '<span class="log-file-sniper"><img src="' . admin_url( 'images/wpspin_light.gif' ) . '" /></span>';
		$html .= '<span class="log-file-text">Login Customizer\'s Log File Downloaded Successfully!</span>';
		$html .= '</div>';
		echo $html;
	}

	/**
	* Get all the pages
	* @since 2.2.0
	* @return array page names with key value pairs
	*/
	function get_pages() {

		$pages = get_pages();
		$pages_options = array();
		if ( $pages ) {
			foreach ($pages as $page) {
				$pages_options[$page->ID] = $page->post_title;
			}
		}

		return $pages_options;
	}

}

