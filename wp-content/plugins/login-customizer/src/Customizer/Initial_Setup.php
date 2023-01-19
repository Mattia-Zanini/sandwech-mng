<?php
/**
 * Automatically create a login page for the Customizer to use.
 */

use LoginCustomizer\Essentials;
new Essentials;

class LoginCustomizerSetup {

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * Returns an instance of this class.
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new LoginCustomizerSetup();
		}

		return self::$instance;
	}

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct() {
		// Add a filter to load check_page function when all plugins have been loaded
		add_action( 'wp_loaded', array( $this, 'check_page' ) );
	}

	/**
	 * Check if Login Customizer page exists.
	 */
	public function check_page() {

		$page_id = $this->page_id();
		// Update page if exists, else create a new one
		if ( $page_id ) {
			$this->update_page( $page_id );
		} else {
			$this->setup_page( 'login-customizer' );
		}
	}

	/**
	 * Retrive page ID from DB for login-customizer page.
	 * 
	 * @since 2.1.6
	 */
	public static function page_id(){

		// Get global wpdb
		global $wpdb;

		// Get page ID of page with Login Customizer template
		$page_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_name = 'login-customizer' LIMIT 1;" );

		return $page_id;
	}

	/**
	 * If page exists, check if it's setup properly.
	 */
	public function update_page( $page_id ) {

		// Get page template from post meta
		$page_template = get_post_meta( $page_id, '_wp_page_template', true );

		// Check if using Login Customizer's page template
		if ( $page_template != 'template-login-customizer.php' ) {
			update_post_meta( $page_id, '_wp_page_template', 'template-login-customizer.php' );
		}

		// Get status of the post
		$post_status = get_post_status( $page_id );

		// Check and update post status to publish
		if ( $post_status != 'publish' ) {
			$page_data = array(
				'ID'          => $page_id,
				'post_status' => 'publish',
			);
			wp_update_post( $page_data );
		}

		// Run update_option function
		$this->update_option( $page_id );

	}

	/**
	 * Create new page if Login Customizer page doesn't exist.
	 */
	public function setup_page( $slug ) {

		// Store data for the page
		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => $slug,
			'post_title'     => __( 'Login Customizer', 'login-customizer' ),
			'post_content'   => __( 'This page is used for Login Customizer plugin. It will not be visible to your readers. Do not delete it.', 'login-customizer' ),
			'comment_status' => 'closed',
		);

		// Insert post data
		$page_id = wp_insert_post( $page_data );

		// Set page template to Login Customizer template
		update_post_meta( $page_id, '_wp_page_template', 'template-login-customizer.php' );

		// Run update_option function
		$this->update_option( $page_id );

	}

	/**
	 * Update plugin options.
	 */
	public function update_option( $page_id ) {

		// Update plugin option with page ID and plugin version
		if ( get_option( 'login_customizer_settings' ) !== false ) {
			$options = get_option( 'login_customizer_settings', array() );
			$options['page'] = $page_id;
			$options['version'] = LOGINCUST_FREE_VERSION;
			update_option( 'login_customizer_settings', $options );
		}

	}

}
