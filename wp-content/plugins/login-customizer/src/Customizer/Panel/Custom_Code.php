<?php
/**
 * Custom CSS & JS output to customize login page.
 *
 *
 * @author 			WPBrigade
 * @copyright 		Copyright (c) 2021, WPBrigade
 * @link 			https://loginpress.pro/
 * @license			https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

namespace LoginCustomizer\Customizer\Panel;
 
 class Custom_Code {

	function __construct() {
		add_action( 'login_enqueue_scripts', array( $this, 'logincust_render_styles' ) );
		add_action( 'login_footer', 		 array( $this, 'logincust_render_script' ) );
	}

	function logincust_render_styles() {
		// Enqueue stylesheet for login styles
		wp_enqueue_style( 'logincust_styles', LOGINCUST_FREE_URL . 'Customizer/Panel/Assets/CSS/customizer.css' );

		// Get plugin options array
		$options = get_option( 'login_customizer_options' );

		// Initialize empty string
		$custom_css = '';

		// Login Page Background CSS
		$custom_css .= 'body.login {';
		if ( ! empty( $options['logincust_bg_image'] ) ) {
			$custom_css .= 'background-image: url(" ' . $options['logincust_bg_image'] . ' ");';
		}
		if ( ! empty( $options['logincust_bg_color'] ) ) {
			$custom_css .= 'background-color: ' . $options['logincust_bg_color'] . ';';
		}
		if ( ! empty( $options['logincust_bg_image_size'] ) ) {
			if ( $options['logincust_bg_image_size'] === 'custom' ) {
				$custom_css .= 'background-size: ' . $options['logincust_bg_size'] . ';';
			} else {
				$custom_css .= 'background-size: ' . $options['logincust_bg_image_size'] . ';';
			}
		}
		if ( ! empty( $options['logincust_bg_image_repeat'] ) ) {
			$custom_css .= 'background-repeat: ' . $options['logincust_bg_image_repeat'] . ';';
		}
		if ( ! empty( $options['logincust_bg_image_position_x'] ) && ! empty( $options['logincust_bg_image_position_y'] ) ) {
			$custom_css .= 'background-position: ' . $options['logincust_bg_image_position_x'] . ' ' . $options['logincust_bg_image_position_y'] . ';';
		}
		$custom_css .= '}';

		// Login Page Logo CSS
		$custom_css .= 'body.login div#login h1 a {';
		if ( ! empty( $options['logincust_logo_show'] ) && $options['logincust_logo_show'] === 1 ) {
			$custom_css .= 'display: none;';
		} else {
			if ( ! empty( $options['logincust_logo'] ) ) {
				$custom_css .= 'background-image: url(" ' . $options['logincust_logo'] . ' ");';
			}
			if ( ! empty( $options['logincust_logo_width'] ) ) {
				$custom_css .= 'width: ' . $options['logincust_logo_width'] . ';';
			}
			if ( ! empty( $options['logincust_logo_height'] ) ) {
				$custom_css .= 'height: ' . $options['logincust_logo_height'] . ';';
			}
			if ( ! empty( $options['logincust_logo_width'] ) || ! empty( $options['logincust_logo_height'] ) ) {

				$options['logincust_logo_height'] 	= isset( $options['logincust_logo_height'] ) ? $options['logincust_logo_height'] : '84px';
				$options['logincust_logo_width'] 	= isset( $options['logincust_logo_width'] ) ? $options['logincust_logo_width'] : '84px';
				
				$custom_css .= 'background-size: ' . $options['logincust_logo_width'] . ' ' . $options['logincust_logo_height'] . ';';
			}
			if ( ! empty( $options['logincust_logo_padding'] ) ) {
				$custom_css .= 'padding-bottom: ' . $options['logincust_logo_padding'] . ';';
			}
		}
		$custom_css .= '}';

		// Login Page Form CSS
		$custom_css .= '#login form#loginform, #login form#registerform, #login form#lostpasswordform {';
		if ( ! empty( $options['logincust_form_bg_image'] ) ) {
			$custom_css .= 'background-image: url(" ' . $options['logincust_form_bg_image'] . ' ");';
		}
		if ( ! empty( $options['logincust_form_bg_color'] ) ) {
			$custom_css .= 'background-color: ' . $options['logincust_form_bg_color'] . ';';
		}
		if ( ! empty( $options['logincust_form_height'] ) ) {
			$custom_css .= 'height: ' . $options['logincust_form_height'] . ';';
		}
		if ( ! empty( $options['logincust_form_padding'] ) ) {
			$custom_css .= 'padding: ' . $options['logincust_form_padding'] . ';';
		}
		if ( ! empty( $options['logincust_form_radius'] ) ) {
			$custom_css .= 'border-radius: ' . $options['logincust_form_radius'] . ';';
		}
		if ( ! empty( $options['logincust_form_shadow_spread'] ) && ! empty( $options['logincust_form_shadow'] ) ) {
			$custom_css .= 'box-shadow: 0 1px ' . $options['logincust_form_shadow_spread'] . ' ' . $options['logincust_form_shadow'] . ';';
		}
		$custom_css .= '}';

		// Login Form Width CSS
		if ( ! empty( $options['logincust_form_width'] ) ) {
			$custom_css .= 'div#login {';
				$custom_css .= 'width: ' . $options['logincust_form_width'] . ';';
			$custom_css .= '}';
		}

		// Rememer Me Link CSS
		if ( ! empty( $options['logincust_field_remember_me'] ) && $options['logincust_field_remember_me'] === 1 ) {
			$custom_css .= '#login form .forgetmenot {';
				$custom_css .= 'display: none;';
			$custom_css .= '}';
		}
		if ( ! empty( $options['logincust_privacy_policy_link'] ) && $options['logincust_privacy_policy_link'] === 1 ) {
			$custom_css .= '.login .privacy-policy-page-link {';
				$custom_css .= 'display: none;';
			$custom_css .= '}';
		}
		// Register Link CSS
		if ( is_customize_preview() ) {
			if ( ! empty( $options['logincust_field_register_link'] ) && $options['logincust_field_register_link'] === 1 ) {
				$custom_css .= '#login #nav a:first-child {';
					$custom_css .= 'display: none;';
				$custom_css .= '}';
			}
			if ( ! empty( $options['logincust_privacy_policy_link'] ) && $options['logincust_privacy_policy_link'] === 1 ) {
				$custom_css .= '.login .privacy-policy-page-link {';
					$custom_css .= 'display: none;';
				$custom_css .= '}';
			}
		}

		// Lost Password Link CSS
		if ( ! empty( $options['logincust_field_lost_password'] ) && $options['logincust_field_lost_password'] === 1 ) {
			$custom_css .= '#login #nav a:last-child {';
				$custom_css .= 'display: none;';
			$custom_css .= '}';
		}

		// Login Page Fields CSS
		$custom_css .= '#login form#loginform .input, #login form#registerform .input, #login form#lostpasswordform .input {';
		if ( ! empty( $options['logincust_field_width'] ) ) {
			$custom_css .= 'width: ' . $options['logincust_field_width'] . ';';
		}
		if ( ! empty( $options['logincust_field_font_size'] ) ) {
			$custom_css .= 'font-size: ' . $options['logincust_field_font_size'] . ';';
		}
		if ( ! empty( $options['logincust_field_border_width'] ) ) {
			$custom_css .= 'border-width: ' . $options['logincust_field_border_width'] . ';';
		}
		if ( ! empty( $options['logincust_field_border_color'] ) ) {
			$custom_css .= 'border-color: ' . $options['logincust_field_border_color'] . ';';
		}
		if ( ! empty( $options['logincust_field_radius'] ) ) {
			$custom_css .= 'border-radius: ' . $options['logincust_field_radius'] . ';';
		}
		if ( ! empty( $options['logincust_field_box_shadow'] ) && $options['logincust_field_box_shadow'] === 1 ) {
			$custom_css .= 'box-shadow: unset;';
		}
		if ( ! empty( $options['logincust_field_margin'] ) ) {
			$custom_css .= 'margin: ' . $options['logincust_field_margin'] . ';';
		}
		if ( ! empty( $options['logincust_field_padding'] ) ) {
			$custom_css .= 'padding: ' . $options['logincust_field_padding'] . ';';
		}
		if ( ! empty( $options['logincust_field_bg'] ) ) {
			$custom_css .= 'background-color: ' . $options['logincust_field_bg'] . ';';
		}
		if ( ! empty( $options['logincust_field_color'] ) ) {
			$custom_css .= 'color: ' . $options['logincust_field_color'] . ';';
		}
		$custom_css .= '}';

		// Login Form Labels CSS
		$custom_css .= '#login form#loginform label, #login form#registerform label, #login form#lostpasswordform label {';
		if ( ! empty( $options['logincust_field_label'] ) ) {
			$custom_css .= 'color: ' . $options['logincust_field_label'] . ';';
		}
		if ( ! empty( $options['logincust_field_label_font_size'] ) ) {
			$custom_css .= 'font-size: ' . $options['logincust_field_label_font_size'] . ';';
		}
		$custom_css .= '}';
		$custom_css .= '#login form#loginform .forgetmenot label, #login form#registerform .forgetmenot label, #login form#lostpasswordform .forgetmenot label {';
		if ( ! empty( $options['logincust_field_label_font_size'] ) ) {
			$custom_css .= 'font-size: ' . ( intval( $options['logincust_field_label_font_size'] ) - 2 ) . 'px;';
		}
		$custom_css .= '}';

		// Login Button CSS
		$custom_css .= '#login form .submit .button {';
			$custom_css .= 'height: auto;';
		if ( ! empty( $options['logincust_button_bg'] ) ) {
			$custom_css .= 'background-color: ' . $options['logincust_button_bg'] . ';';
		}
		if ( ! empty( $options['logincust_button_font_size'] ) ) {
			$custom_css .= 'font-size: ' . $options['logincust_button_font_size'] . ';';
		}
		if ( ! empty( $options['logincust_button_height_width'] ) && $options['logincust_button_height_width'] === 'custom' ) {
			if ( ! empty( $options['logincust_button_width_size'] ) ) {
				$custom_css .= 'width: ' . $options['logincust_button_width_size'] . ';';
			}
			if ( ! empty( $options['logincust_button_height_size'] ) ) {
				$custom_css .= 'height: ' . $options['logincust_button_height_size'] . ';';
			}
		}
		if ( ! empty( $options['logincust_button_color'] ) ) {
			$custom_css .= 'color: ' . $options['logincust_button_color'] . ';';
		}
		if ( ! empty( $options['logincust_button_padding'] ) ) {
			$custom_css .= 'padding: ' . $options['logincust_button_padding'] . ';';
		}
		if ( ! empty( $options['logincust_button_border_width'] ) ) {
			$custom_css .= 'border-width: ' . $options['logincust_button_border_width'] . ';';
		}
		if ( ! empty( $options['logincust_button_border_radius'] ) ) {
			$custom_css .= 'border-radius: ' . $options['logincust_button_border_radius'] . ';';
		}
		if ( ! empty( $options['logincust_button_border'] ) ) {
			$custom_css .= 'border-color: ' . $options['logincust_button_border'] . ';';
		}
		if ( ! empty( $options['logincust_button_shadow'] ) && ! empty( $options['logincust_button_shadow_spread'] ) ) {
			$custom_css .= 'box-shadow: 0px 1px ' . $options['logincust_button_shadow_spread'] . ' ' . $options['logincust_button_shadow'] . ';';
		}
		if ( ! empty( $options['logincust_button_text_shadow'] ) ) {
			$custom_css .= 'text-shadow: 0 -1px 1px ' . $options['logincust_button_text_shadow'] . ',1px 0 1px ' . $options['logincust_button_text_shadow'] . ',0 1px 1px ' . $options['logincust_button_text_shadow'] . ',-1px 0 1px ' . $options['logincust_button_text_shadow'] . ';';
		}
		$custom_css .= '}';

		// Login Button on Hover CSS
		$custom_css .= '#login form .submit .button:hover, #login form .submit .button:focus {';
		if ( ! empty( $options['logincust_button_hover_bg'] ) ) {
			$custom_css .= 'background-color: ' . $options['logincust_button_hover_bg'] . ';';
		}
		if ( ! empty( $options['logincust_button_hover_border'] ) ) {
			$custom_css .= 'border-color: ' . $options['logincust_button_hover_border'] . ';';
		}
		$custom_css .= '}';

		// Other Styling
		if ( ! empty( $options['logincust_field_back_blog'] ) && $options['logincust_field_back_blog'] === 1 ) {
			$custom_css .= '#login #backtoblog {';
				$custom_css .= 'display: none;';
			$custom_css .= '}';
		}
		if ( ! empty( $options['logincust_other_font_size'] ) ) {
			$custom_css .= '.login #nav, .login #nav a, .login #backtoblog a {';
				$custom_css .= 'font-size: ' . $options['logincust_other_font_size'] . ';';
			$custom_css .= '}';
		}
		if ( ! empty( $options['logincust_other_color'] ) ) {
			$custom_css .= '.login #nav, .login #nav a, .login #backtoblog a, .login .privacy-policy-page-link a {';
				$custom_css .= 'color: ' . $options['logincust_other_color'] . ';';
			$custom_css .= '}';
		}
		if ( ! empty( $options['logincust_other_color_hover'] ) ) {
			$custom_css .= '.login #backtoblog a:hover, .login #nav a:hover, .login .privacy-policy-page-link a:hover {';
				$custom_css .= 'color: ' . $options['logincust_other_color_hover'] . ';';
			$custom_css .= '}';
		}

		// Custom CSS
		if ( ! empty( $options['logincust_other_css'] ) ) {
			$custom_css .= $options['logincust_other_css'];
		}

		// Hook inline styles to stylesheet
		wp_add_inline_style( 'logincust_styles', $custom_css );

	}

// Hook stylesheet to login page

	function logincust_render_script() {
		// Get plugin options array
		$options = get_option( 'login_customizer_options' );
		if ( ! empty( $options['logincust_other_js'] ) ) {
			echo '<script>' . "\n" . $options['logincust_other_js'] . "\n" . '</script>' . "\n";
		}
	}
 }


// Hook script to login page
