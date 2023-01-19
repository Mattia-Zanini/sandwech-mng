<?php
/**
 * Plugin Meta Class to add meta tags on Plugins page.
 *
 *
* @package 			LoginCustomizer
* @author 			WPBrigade
* @copyright 		Copyright (c) 2021, WPBrigade
* @link 			https://Loginpress.pro/
* @license			https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace LoginCustomizer\Includes;
use LoginCustomizer\Includes\Disband;


/**
 * Plugin Meta class.
 *
 * @since  2.2.0
 * @version 2.1.5
 * @access public
 */
class Plugin_Meta {

	/* * * * * * * * * *
	* Class constructor
	* * * * * * * * * */
		
	public function __construct() {

		// $this->hooks();

	}

	/**
    * Hook into actions and filters
    * @since  2.2.0
 	* @version 2.1.5
	*/
	function hooks( ) {

		// include_once( LOGINCUST_DIR_PATH . 'Includes/Ajax.php' );

		// add_action( 'admin_footer',           array( $this, 'disband_model' ) );
		add_filter( 'plugin_row_meta',        array( $this, '_row_meta'), 10, 2 );
		add_action( 'plugin_action_links', 	  array( $this, 'login_customizer_action_links' ), 10, 2 );

	}

 	/**
    * Add rating icon on plugins page.
    *
    * @since 2.2.0
	* @return void 
    */

    public function _row_meta( $meta_fields, $file ) {

		if ( $file != 'login-customizer/login-customizer.php' ) {
  
		  return $meta_fields;
		}
  
		echo "<style>.login-customizer-rate-stars { display: inline-block; color: #ffb900; position: relative; top: 3px; }.login-customizer-rate-stars svg{ fill:#ffb900; } .login-customizer-rate-stars svg:hover{ fill:#ffb900 } .login-customizer-rate-stars svg:hover ~ svg{ fill:none; } </style>";
  
		$plugin_rate   = "https://wordpress.org/support/plugin/login-customizer/reviews/?rate=5#new-post";
		$plugin_filter = "https://wordpress.org/support/plugin/login-customizer/reviews/?filter=5";
		$plugin_support = "https://wordpress.org/support/plugin/login-customizer/";
		$svg_xmlns     = "https://www.w3.org/2000/svg";
		$svg_icon      = '';
  
		for ( $i = 0; $i < 5; $i++ ) {
		  	$svg_icon .= "<svg xmlns='" . esc_url( $svg_xmlns ) . "' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>";
		}
		$meta_fields[] = '<a href="' . esc_url( $plugin_support ) . '" target="_blank">' . __( 'Support', 'login-customizer' ) . '</a>';
		// Set icon for thumbsup.
		$meta_fields[] = '<a href="' . esc_url( $plugin_filter ) . '" target="_blank"><span class="dashicons dashicons-thumbs-up"></span>' . __( 'Vote!', 'login-customizer' ) . '</a>';
  
		// Set icon for 5-star reviews. v1.1.22
		$meta_fields[] = "<a href='" . esc_url( $plugin_rate ) . "' target='_blank' title='" . esc_html__( 'Rate', 'login-customizer' ) . "'><i class='login-customizer-rate-stars'>" . $svg_icon . "</i></a>";
  
		return $meta_fields;
	}

	/**
	* Add a link to the settings page to the plugins list
	*
	* @since  2.2.0
	* @return void
	*/
	public function login_customizer_action_links( $links, $file ) {

		static $this_plugin;
		$options = get_option( 'login_customizer_settings', array() );

		if ( empty( $this_plugin ) ) {

			$this_plugin = 'login-customizer/login-customizer.php';
		}

		if ( $file == $this_plugin ) {

			$settings_link = sprintf( esc_html__( '%1$s Customize %2$s', 'login-customizer' ), '<a href="' . add_query_arg(
				array(
					'autofocus[panel]' => 'logincust_panel',
					'url' => rawurlencode( get_permalink( $options['page'] ) ),
				),
				admin_url( 'customize.php' )
			) . '">', '</a>' );
			$settings_link .= sprintf( esc_html__( ' | %1$s Settings %2$s ', 'login-customizer'), '<a href="' . admin_url( 'admin.php?page=login-customizer-settings' ) . '">', '</a>' );

      		array_unshift( $links, $settings_link );

		}

		return $links;
	}

	/**
	 * Disband Model Box
	 * 
	 * [Description] Option form shown on deactivation of Login Customizer
	 * 
	 * @version 2.2.0
	 */
	function disband_model() {
		global $pagenow;

		if ( 'plugins.php' !== $pagenow ) {
		  return;
		}

		new Disband;

	}

}

