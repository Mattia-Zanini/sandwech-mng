<?php
/**
 * Template Name: Login Customizer
 *
 * Template to display login page for customization purposes. It's used to avoid loading wp-login.php page, which isn't the best way to do it.
 * A stripped-down version of wp-login.php form made to work with Login Customizer.
 */

 /**
  * Redirect to homepage if not loaded inside Customizer.
  */
	if ( ! is_customize_preview() ) {
		if ( is_multisite() ) {
			$url = esc_url( network_home_url( '/' ) );
		} else {
			$url = esc_url( home_url( '/' ) );
		}
		wp_safe_redirect( $url );

	}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php
		$login_title = sprintf(
			__( '%1$s &lsaquo; %2$s &#8212; WordPress', 'login-customizer' ),
			__( 'Log In', 'login-customizer' ),
			get_bloginfo( 'name', 'display' )
		);


		?>
		<title><?php echo esc_attr( $login_title ); ?></title>
		<?php
			wp_enqueue_style( 'login' );
			do_action( 'login_enqueue_scripts' );
			do_action( 'login_head' );
		?>
	</head>
	<?php
		do_action( 'login_form_login' );

		$action = 'login';

		$login_link_separator = apply_filters( 'login_link_separator', ' | ' );

		if ( is_multisite() ) {
			$login_header_url   = network_home_url();
			$login_header_title = get_network()->site_name;
		} else {
			$login_header_url   = __( 'https://wordpress.org/', 'login-customizer' );
			$login_header_title = __( 'Powered by WordPress', 'login-customizer' );
		}

		$login_header_url = apply_filters( 'login_headerurl', $login_header_url );


		//login_headertitle was deprecated in WordPress 5.2
			
		if ( version_compare( $GLOBALS['wp_version'], '5.2', '<' ) ) {
			$login_header_title = apply_filters( 'login_headertitle', $login_header_title );
		} else {
			$login_header_title = apply_filters( 'login_headertext', $login_header_title );
		}
		if ( is_multisite() ) {
			$login_header_text = get_bloginfo( 'name', 'display' );
		} else {
			$login_header_text = $login_header_title;
		}

			$classes = array( 'login-action-' . $action, 'wp-core-ui' );
		if ( is_rtl() ) {
			$classes[] = 'rtl';
		}

		$classes = apply_filters( 'login_body_class', $classes, $action );

	?>
	<body class="login <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<?php do_action( 'login_header' ); ?>
		<div id="login">
			<h1><a href="<?php echo esc_url( $login_header_url ); ?>" title="<?php echo esc_attr( $login_header_title ); ?>" tabindex="-1"><?php echo $login_header_text; ?></a></h1>
			<form name="loginform" id="loginform">
				<p>
					<label for="user_login"><?php _e( 'Username or Email Address', 'login-customizer' ); ?><br />
					<input type="text" name="log" id="user_login" class="input" value="<?php echo esc_attr( $user_login ); ?>" size="20" autocapitalize="off" /></label>
				</p>
				<p>
					<label for="user_pass"><?php _e( 'Password', 'login-customizer' ); ?><br />
					<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" /></label>
				</p>
				<?php do_action( 'login_form' ); ?>
				<p class="forgetmenot"><label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php esc_html_e( 'Remember Me', 'login-customizer' ); ?></label></p>
				<p class="submit">
					<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Log In', 'login-customizer' ); ?>" />
				</p>
			</form>
			<p id="nav">
				<?php
				if ( get_option( 'users_can_register' ) ) :
					$registration_url = sprintf( '<a href="%s">%s</a>', esc_url( wp_registration_url() ), __( 'Register', 'login-customizer' ) );
					/** This filter is documented in wp-includes/general-template.php */
					echo apply_filters( 'register', $registration_url );
					echo esc_html( $login_link_separator );
					endif;
				?>
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'login-customizer' ); ?></a>
			</p>
			<p id="backtoblog">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php
						/* translators: %s: site title */
						printf( _x( '&larr; Back to %s', 'site', 'login-customizer' ), get_bloginfo( 'title', 'display' ) );
					?>
				</a>
			</p>
			<div class="privacy-policy-page-link">
				<a class="privacy-policy-link" href="<?php get_privacy_policy_url()?>"><?php _e( 'Privacy Policy', 'login-customizer' ); ?></a>
			</div>
		</div>
		<?php
		/**
		 * Filters the Languages select input activation on the login screen.
		 *
		 * @since 2.1.7
		 */
		if ( version_compare( $GLOBALS['wp_version'], '5.9', '>=' ) ) {

			$interim_login = isset( $_REQUEST['interim-login'] );

			if (
				! $interim_login &&
				/**
				 * Filters the Languages select input activation on the login screen.
				 *
				 * @since 2.1.7
				 *
				 * @param bool Whether to display the Languages select input on the login screen.
				 */
				apply_filters( 'login_display_language_dropdown', false )
			) {
				$languages = get_available_languages();

				if ( ! empty( $languages ) ) {
					?>
					<div class="language-switcher">
						<form id="language-switcher" action="" method="get">

							<label for="language-switcher-locales">
								<span class="dashicons dashicons-translation" aria-hidden="true"></span>
								<span class="screen-reader-text"><?php _e( 'Language' ); ?></span>
							</label>

							<?php
							$args = array(
								'id'                          => 'language-switcher-locales',
								'name'                        => 'wp_lang',
								'selected'                    => determine_locale(),
								'show_available_translations' => false,
								'explicit_option_en_us'       => true,
								'languages'                   => $languages,
							);

							/**
							 * Filters default arguments for the Languages select input on the login screen.
							 *
							 * @since 5.9.0
							 *
							 * @param array $args Arguments for the Languages select input on the login screen.
							 */
							wp_dropdown_languages( apply_filters( 'login_language_dropdown_args', $args ) );
							?>

							<?php if ( $interim_login ) { ?>
								<input type="hidden" name="interim-login" value="1" />
							<?php } ?>

							<?php if ( isset( $_GET['redirect_to'] ) && '' !== $_GET['redirect_to'] ) { ?>
								<input type="hidden" name="redirect_to" value="<?php echo esc_url_raw( $_GET['redirect_to'] ); ?>" />
							<?php } ?>

							<?php if ( isset( $_GET['action'] ) && '' !== $_GET['action'] ) { ?>
								<input type="hidden" name="action" value="<?php echo esc_attr( $_GET['action'] ); ?>" />
							<?php } ?>

								<input type="submit" class="button" value="<?php esc_attr_e( 'Change' ); ?>">

							</form>
						</div>
				<?php } ?>
			<?php } ?>
		<?php } ?>
		<?php
			do_action( 'login_footer' );
			$options = get_option( 'login_customizer_options' );

			if( isset( $options['logincust_other_js'] ) && !empty( $options['logincust_other_js'] ) ){
				echo '<script type="text/javascript">' . $options['logincust_other_js'] . '</script>';

			}
			wp_footer();
		?>
	</body>
</html>
