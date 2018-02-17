<?php
/*
Plugin Name: Add password field on register form
Description:
Version: 1.1.2.3
Author URI: http://premium.wpmudev.org/
Network: true
 */

if ( ! class_exists( 'ub_Signup_Password existed ub_helper' ) ) {

	class ub_Signup_Password extends ub_helper {
		var $signup_password_use_encryption = 'yes'; //Either 'yes' OR 'no'
		var $signup_password_form_printed = 0;

		function __construct() {
			global $signup_password_form_printed;
			add_action( 'ultimatebranding_settings_signuppassword', array( $this, 'admin_options_page' ) );
			add_action( 'init', array( &$this, 'initialise_plugin' ) );
		}

		function initialise_plugin() {
			//add_action('init', 'wpmu_signup_password_init');
			add_action( 'template_redirect', array( &$this, 'wpmu_signup_password_init_sessions' ) );
			add_action( 'wp_footer', array( &$this, 'wpmu_signup_password_stylesheet' ) );
			add_action( 'login_footer', array( &$this, 'wpmu_signup_password_stylesheet' ) );
			add_action( 'register_form', array( &$this, 'wpmu_signup_password_fields' ) );
			add_action( 'signup_extra_fields', array( &$this, 'wpmu_signup_password_fields' ) );
			add_filter( 'wpmu_validate_user_signup', array( &$this, 'wpmu_signup_password_filter' ) );
			add_filter( 'signup_blogform', array( &$this, 'wpmu_signup_password_fields_pass_through' ) );
			add_filter( 'add_signup_meta', array( &$this, 'wpmu_signup_password_meta_filter' ), 99 );
			add_filter( 'random_password', array( &$this, 'wpmu_signup_password_random_password_filter' ) );
			add_filter( 'wp_new_user_notification_email', array( $this, 'new_user_notification_email' ), 10, 3 );
		}

		protected function set_options() {
			$message = __( 'The Signup Password module is active.', 'ub' );
			if ( is_multisite() ) {
				$allow_new_registrations = get_site_option( 'registration' );
				switch ( $allow_new_registrations ) {
					case 'none':
					case 'blog':
						$message = __( 'The Signup Password module is active, but registration is disabled.', 'ub' );
					break;
				}
			} else {
				$user_can_register = get_option( 'users_can_register' );
				if ( 0 == $user_can_register ) {
					$message = __( 'The Signup Password module is active, but registration is disabled.', 'ub' );
				}
			}
			$description = '<ul>';
			/**
			 * message abut filter
			 */
			$wp_version = get_bloginfo( 'version' );
			$compare = version_compare( $wp_version, '4.9', '<' );
			if ( $compare ) {
				$text = __( 'You are using WordPress %s and email to user will be incorrect, becouse filter "wpmu_signup_password_encrypt" was introduced in WordPress 4.9. Please upgrade your WordPress instalation.', 'ub' );
				$text = sprintf( $text, $wp_version );
				$description .= sprintf( '<li><strong>%s</strong></li>', $text );
			}
			/**
			 * other messages
			 */
			$description .= sprintf( '<li>%s</li>', $message );
			$description .= sprintf( '<li>%s</li>', __( 'This module has no configuration.', 'ub' ) );
			$description .= '</ul>';
			$this->options = array(
				'description' => array(
					'title' => __( 'Description', 'ub' ),
					'description' => $description,
				),
			);
		}

		function signup_password_encrypt() {
			return self::wpmu_signup_password_encrypt();
		}

		function wpmu_signup_password_encrypt( $data ) {
			if ( ! isset( $chars ) ) {
				// 3 different symbols (or combinations) for obfuscation
				// these should not appear within the original text
				$sym = array( '∂', '•xQ', '|' );

				foreach ( range( 'a','z' ) as $key => $val ) {
					$chars[ $val ] = str_repeat( $sym[0],($key + 1) ).$sym[1];
				}
				$chars[' '] = $sym[2];

				unset( $sym );
			}

			// encrypt
			$data = base64_encode( strtr( $data, $chars ) );
			return $data;

		}

		function signup_password_decrypt() {
			return self::wpmu_signup_password_decrypt();
		}

		function wpmu_signup_password_decrypt( $data ) {
			if ( ! isset( $chars ) ) {
				// 3 different symbols (or combinations) for obfuscation
				// these should not appear within the original text
				$sym = array( '∂', '•xQ', '|' );

				foreach ( range( 'a','z' ) as $key => $val ) {
					$chars[ $val ] = str_repeat( $sym[0],($key + 1) ).$sym[1]; }
				$chars[' '] = $sym[2];

				unset( $sym );
			}

			// decrypt
			$charset = array_flip( $chars );
			$charset = array_reverse( $charset, true );

			$data = strtr( base64_decode( $data ), $charset );
			unset( $charset );

			return $data;
		}

		function signup_password_filter() {
			return self::wpmu_signup_password_filter();
		}

		function wpmu_signup_password_filter( $content ) {
			$password_1 = isset( $_POST['password_1'] ) ? $_POST['password_1'] : '';
			$password_2 = isset( $_POST['password_2'] ) ? $_POST['password_2'] : '';
			if ( ! empty( $password_1 ) && $_POST['stage'] == 'validate-user-signup' ) {
				if ( $password_1 != $password_2 ) {
					$content['errors']->add( 'password_1', __( 'Passwords do not match.', 'ub' ) );
				}
			}

			return $content;
		}

		function signup_password_meta_filter() {
			return self::wpmu_signup_password_meta_filter();
		}

		function wpmu_signup_password_meta_filter( $meta ) {
			global $signup_password_use_encryption;

			$password_1 = isset( $_POST['password_1'] ) ? $_POST['password_1'] : '';
			if ( ! empty( $password_1 ) ) {
				if ( $signup_password_use_encryption == 'yes' ) {
					$password_1 = self::wpmu_signup_password_encrypt( $password_1 );
				}
				$add_meta = array( 'password' => $password_1 );
				$meta = array_merge( $add_meta, $meta );
			}

			return $meta;
		}

		function signup_password_random_password_filter() {
			return self::wpmu_signup_password_random_password_filter();
		}

		function wpmu_signup_password_random_password_filter( $password ) {
			global $wpdb, $signup_password_use_encryption;

			if ( isset( $_GET['key'] ) && ! empty( $_GET['key'] ) ) {
				$key = $_GET['key'];
			} elseif ( isset( $_POST['key'] ) && ! empty( $_POST['key'] ) ) {
				$key = $_POST['key'];
			}
			if ( ! empty( $_POST['password_1'] ) ) {
				$password = $_POST['password_1'];
			} elseif ( ! empty( $key ) ) {
				$signup = $wpdb->get_row(
					$wpdb->prepare( "SELECT * FROM $wpdb->signups WHERE activation_key = '%s'", $key )
				);
				if ( ! (empty( $signup ) || $signup->active) ) {
					//check for password in signup meta
					$meta = maybe_unserialize( $signup->meta );
					if ( ! empty( $meta['password'] ) ) {
						if ( $signup_password_use_encryption == 'yes' ) {
							$password = self::wpmu_signup_password_decrypt( $meta['password'] );
						} else {
							$password = $meta['password'];
						}
						unset( $meta['password'] );
						$meta = maybe_serialize( $meta );
						$wpdb->update(
							$wpdb->signups,
							array( 'meta' => $meta ),
							array( 'activation_key' => $key ),
							array( '%s' ),
							array( '%s' )
						);
					}
				}
			}

			return $password;
		}

		function signup_password_stylesheet() {
			return self::wpmu_signup_password_stylesheet();
		}

		function wpmu_signup_password_stylesheet() {
			global $signup_password_form_printed;

			if ( $signup_password_form_printed ) {
?>
            <style type="text/css">
                .mu_register #password_1,
                .mu_register #password_2 {width: 100%; font-size: 24px; margin:5px 0;}
.ultimate-branding-password {
    padding-bottom: 16px;
}
.login form .ultimate-branding-password .input {
    margin-bottom: 2px;
}
.ultimate-branding-password span {
    font-size: .9em;
    opacity: .8;
}
            </style>
<?php
			}
		}

		function signup_password_fields_pass_through() {
			return self::wpmu_signup_password_fields_pass_through();
		}

		function wpmu_signup_password_fields_pass_through() {
			global $signup_password_form_printed;

			if ( ! empty( $_POST['password_1'] ) && ! empty( $_POST['password_2'] ) ) {
				$signup_password_form_printed = 1;
?>
            <input type="hidden" name="password_1" value="<?php echo $_POST['password_1']; ?>" />
<?php
				$_SESSION['password_1'] = $_POST['password_1'];
			} elseif ( isset( $_SESSION['password_1'] ) && ! empty( $_SESSION['password_1'] ) ) {
				$signup_password_form_printed = 1;
?>
            <input type="hidden" name="password_1" value="<?php echo $_SESSION['password_1']; ?>" />
<?php
			}
		}

		function signup_password_fields() {
			return self::wpmu_signup_password_fields();
		}

		function wpmu_signup_password_fields( $errors ) {
			global $signup_password_form_printed;

			if ( $errors && method_exists( $errors, 'get_error_message' ) ) {
				$error = $errors->get_error_message( 'password_1' );
			} else {
				$error = false;
			}
			$signup_password_form_printed = 1;
?>
<p class="ultimate-branding-password">
        <label for="password"><?php _e( 'Password', 'ub' ); ?>:</label>
<?php
if ( $error ) {
	echo '<p class="error">' . $error . '</p>';
}
?>
        <input name="password_1" type="password" id="password_1" value="" autocomplete="off" maxlength="20" class="input"/>
        <span><?php _e( 'Leave fields blank for a random password to be generated.', 'ub' ) ?></span>
</p>
<p class="ultimate-branding-password">
        <label for="password"><?php _e( 'Confirm Password', 'ub' ); ?>:</label>
        <input name="password_2" type="password" id="password_2" value="" autocomplete="off" maxlength="20" class="input" /><br />
        <span><?php _e( 'Type your new password again.', 'ub' ) ?></span>
</p>
<?php
		}

		function signup_password_init_sessions() {
			return self::wpmu_signup_password_init_sessions();
		}

		function wpmu_signup_password_init_sessions() {
			if ( is_user_logged_in() ) { return; }

			if ( ! session_id() ) {
				session_start();
			}
		}

		/**
		 * Verify if plugin is network activated
		 **/
		function is_plugin_active_for_network( $plugin ) {
			if ( ! is_multisite() ) {
				return false; }

			$plugins = get_site_option( 'active_sitewide_plugins' );
			if ( isset( $plugins[ $plugin ] ) ) {
				return true; }

			return false;
		}

		/**
		 * new user notification email
		 *
		 * @since 1.9.4
		 */
		public function new_user_notification_email( $email, $user, $blogname ) {
			$text = __( 'Howdy USERNAME,

Your new account is set up.

You can log in with the following information:
Username: USERNAME
Password: PASSWORD
LOGINLINK

Thanks!

--The Team @ SITE_NAME', 'ub' );
			$text = preg_replace( '/USERNAME/', $user->user_login, $text );
			$text = preg_replace( '/PASSWORD/', $_REQUEST['password_1'], $text );
			$text = preg_replace( '/LOGINLINK/', network_site_url( 'wp-login.php' ), $text );
			$text = preg_replace( '/SITE_NAME/', $blogname, $text );
			$email['message'] = $text;
			return $email;
		}
	}
}

new ub_Signup_Password();