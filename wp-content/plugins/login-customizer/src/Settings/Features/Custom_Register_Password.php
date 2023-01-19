<?php
/**
* Login Customizer Custom_Password
*
* Description: Enable Custom Password for Register User.
*
* @package Login Customizer
* @since 2.2.0
*/

namespace LoginCustomizer\Settings\Features;
use WP_Error;

class Custom_Register_Password {

    /* * * * * * * * * *
    * Class constructor
    * * * * * * * * * */
    public function __construct() {

      $this->add_custom_fields();
    }

	/**
	 * Custom Password Fields on Register form
	 *
	 * @return void
	 */
    public function add_custom_fields() {

		add_action( 'register_form',                  array( $this, 'logincust_reg_password_fields' ) );
		add_filter( 'registration_errors',            array( $this, 'logincust_reg_pass_errors' ), 10, 3 );
		add_filter( 'random_password',                array( $this, 'logincust_set_password' ) );
		add_action( 'register_new_user',              array( $this, 'update_default_password_nag' ) );
		add_filter( 'wp_new_user_notification_email', array( $this, 'logincust_new_user_email_notification' ) );
    }

    /**
     * Custom Password Fields on Registration Form.
     *
     * @access  public
	 * @since   2.2.0
     * @return  string html.
     */
    public function logincust_reg_password_fields() { ?>
		<style>
			#registerform {
			height: 540px !important;
			/* background: transparent; */
		}
		</style>
		<p class="logincust-reg-pass-wrap">
			<label for="logincust-reg-pass"><?php _e( 'Password', 'login-customizer' ); ?></label>
			<input autocomplete="off" name="logincust-reg-pass" id="logincust-reg-pass" class="input" size="20" value="" type="password" />
		</p>
		<p class="logincust-reg-pass-2-wrap">
			<label for="logincust-reg-pass-2"><?php _e( 'Confirm Password', 'login-customizer' ); ?></label>
			<input autocomplete="off" name="logincust-reg-pass-2" id="logincust-reg-pass-2" class="input" size="20" value="" type="password" />
		</p>
      	<?php
    }

    /**
    * Handles password field errors for registration form.
    *
    * @access public
    * @param Object $errors WP_Error
    * @param Object $sanitized_user_login user login.
	* @param Object $user_email user email.
	*
	* @since 2.2.0
    * @return WP_Error object.
    */
    public function logincust_reg_pass_errors( $errors, $sanitized_user_login, $user_email ) {

      	// Ensure passwords aren't empty.
      	if ( empty( $_POST['logincust-reg-pass'] ) || empty( $_POST['logincust-reg-pass-2'] ) ) {
        	$errors->add( 'empty_password', __( '<strong>ERROR</strong>: Please enter your password twice.', 'login-customizer' ) );

		// Ensure passwords are matched.
		} elseif ( $_POST['logincust-reg-pass'] != $_POST['logincust-reg-pass-2'] ) {
			$errors->add( 'password_mismatch', __( '<strong>ERROR</strong>: Please enter the same password in the end password fields.', 'login-customizer' ) );

		// Password Set? assign password to a user_pass
		} else {
			$_POST['user_pass'] = $_POST['logincust-reg-pass'];
		}

      	return $errors;
    }

    /**
    * Let's set the user password.
    *
    * @access public
	* @param string $password Auto-generated password passed in from filter.
	*
	* @since 2.2.0
    * @return string Password Choose by User.
    */
    public function logincust_set_password( $password ) {

		// Make sure password field isn't empty.
		if ( ! empty( $_POST['user_pass'] ) ) {
			$password = $_POST['user_pass'];
		}

		return $password;
    }

    /**
    * Sets the value of default password nag.
    *
    * @access public
	* @param int $user_id.
	*
	* @since 2.2.0
    */
    public function update_default_password_nag( $user_id ) {

		// False => User not using WordPress default password.
		update_user_meta( $user_id, 'default_password_nag', false );
    }

    /**
     * Filter the new user email notification.
     *
     * @param array $email The new user email notification parameters.
     * @return array The new user email notification parameters.
	 * 
	 * @since 2.2.0
     */
    function logincust_new_user_email_notification( $email ) {

    	$email['message'] .= "\r\n" . __( 'If you have already set your own password, you may disregard this email and use the password you have already set.', 'login-customizer' );

    	return $email;
    }

} // End Of Class.


