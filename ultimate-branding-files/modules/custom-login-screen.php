<?php
/*
Plugin Name: Login Screen
Plugin URI:
Description:
Author: Marcin (Incsub)
Version: 1.0.1
Author URI:
Network: true

Copyright 2017 Incsub (email: admin@incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if ( ! class_exists( 'ub_custom_login_screen' ) ) {

	class ub_custom_login_screen extends ub_helper {

		private $proceed_gettext = false;
		private $patterns = array();
		protected $option_name = 'global_login_screen';

		/**
		 * predefined login themes
		 */
		private $dirs = array(
			'black-ice',
			'dandelion',
			'moon',
			'moss',
		);

		public function __construct() {
			parent::__construct();
			$this->set_options();
			add_action( 'ultimatebranding_settings_login_screen', array( $this, 'module_admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_login_screen_process', array( $this, 'update' ), 10, 1 );
			add_action( 'login_head', array( $this, 'output' ), 99 );
			add_filter( 'login_headerurl', array( $this, 'login_headerurl' ) );
			add_filter( 'login_headertitle', array( $this, 'login_headertitle' ) );
			add_filter( 'wp_login_errors', array( $this, 'wp_login_errors' ) );
			add_filter( 'wp_login_errors', array( $this, 'set_remember_me' ) );
			add_filter( 'gettext', array( $this, 'gettext_login_form_labels' ), 20, 3 );
			add_filter( 'mime_types', array( $this, 'add_svg_to_allowed_mime_types' ) );
			add_filter( 'logout_redirect', array( $this, 'logout_redirect' ), 99, 3 );
			add_filter( 'login_redirect', array( $this, 'login_redirect' ), 99, 3 );
			add_action( 'ub_helper_admin_options_page_before_options', array( $this, 'before_admin_options_page' ) );

			/**
			 * Signup Password
			 *
			 * Add password field on register form
			 *
			 * @since 1.9.5
			 */
			add_action( 'init', array( $this, 'signup_password_init' ) );
		}

		/**
		 * Load signup password submodule.
		 *
		 * @since 1.9.5
		 */
		public function signup_password_init() {
			$value = $this->get_value( 'form', 'signup_password', 'off' );
			if ( 'on' != $value ) {
				return;
			}
			$file = ub_files_dir( 'modules/custom-login-screen/signup-password.php' );
			include_once $file;
			new ub_signup_password();
		}

		/**
		 * modify option name
		 *
		 * @since 1.9.2
		 */
		public function get_module_option_name( $option_name, $module ) {
			if ( is_string( $module ) && 'login-screen' == $module ) {
				return $this->option_name;
			}
			return $option_name;
		}

		public function output() {
			$this->proceed_gettext = true;
			$value = ub_get_option( $this->option_name );
			if ( $value == 'empty' ) {
				$value = '';
			}
			if ( empty( $value ) ) {
				return;
			}
			printf( '<style type="text/css" id="%s">', esc_attr( __CLASS__ ) );

			/**
			 * Logo & Background
			 */
			if ( isset( $value['logo_and_background'] ) ) {
				$v = $value['logo_and_background'];
				/**
				 * show_logo
				 */
				$this->css_hide( $v, 'show_logo', '.login h1' );
				/**
				 * rounded_form
				 */
				if ( isset( $v['logo_rounded'] ) && '0' != $v['logo_rounded'] ) {
?>
#login h1 a {
    -webkit-border-radius: <?php echo intval( $v['logo_rounded'] ); ?>px;
    -moz-border-radius: <?php echo intval( $v['logo_rounded'] ); ?>px;
    border-radius: <?php echo intval( $v['logo_rounded'] ); ?>px;
}
<?php
				}
				/**
				 * logo
				 */
				if ( isset( $v['logo_upload_meta'] ) ) {
					$src = $v['logo_upload_meta'][0];
					if ( ! empty( $src ) ) {
						$image = $v['logo_upload_meta'];
						$width = $image[1];
						$height = $image[2];

						if ( isset( $v['logo_width'] ) ) {

							$scale = $v['logo_width'] / $width;
							$width = $v['logo_width'];
							$height = intval( $height * $scale );
						} elseif ( $width > 320 ) {
							$scale = 320 / $width ;
							$height = intval( $height * $scale );
							$width = intval( $width * $scale );
						}
?>
#login h1 a {
    background-image: url(<?php echo $src; ?>);
    background-size: 100%;
    height: <?php echo $height; ?>px;
    width: <?php echo $width; ?>px;
}
<?php
					}
				}
				/**
				 * logo_bottom_margin
				 */
				if ( isset( $v['logo_bottom_margin'] ) ) {
?>
#login h1 a {
    margin-bottom: <?php echo $v['logo_bottom_margin']; ?>px;
}
<?php
				}
				/**
				 * logo_transparency
				 */
				$this->css_opacity( $v, 'logo_transparency', '#login h1' );
				/**
				 * bg_color
				 */
				$this->css_background_color( $v, 'bg_color', 'body' );
				/**
				 * fullscreen_bg
				 */
				if ( isset( $v['fullscreen_bg_meta'] ) ) {
					$src = $v['fullscreen_bg_meta'][0];
					if ( ! empty( $src ) ) {
?>
html {
    background: url(<?php echo $src; ?>) no-repeat center center fixed;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
}
body {
    background-color: transparent;
}
<?php
					}
				}
			}

			/**
			 * Form
			 */
			if ( isset( $value['form'] ) ) {
				$v = $value['form'];
				/**
				 * rounded_form
				 */
				if ( isset( $v['rounded_nb'] ) && '0' != $v['rounded_nb'] ) {
?>
.login form {
    -webkit-border-radius: <?php echo intval( $v['rounded_nb'] ); ?>px;
    -moz-border-radius: <?php echo intval( $v['rounded_nb'] ); ?>px;
    border-radius: <?php echo intval( $v['rounded_nb'] ); ?>px;
}
<?php
				}
				/**
				 * label_color
				 */
				$this->css_color( $v, 'label_color', '.login form label' );

				/**
				 * form_bg
				 */
				if ( isset( $v['form_bg_meta'] ) ) {
					$src = $v['form_bg_meta'][0];
					if ( ! empty( $src ) ) {
?>
 .login form {
    background: url(<?php echo $src; ?>) no-repeat center center;
    -webkit-background-size: 100%;
    -moz-background-size: 100%;
    -o-background-size: 100%;
    background-size: 100%;
}
<?php
					}
				}

				/**
				 * input_border_color_focus
				 */
				if ( isset( $v['input_border_color_focus'] ) ) {
					$color = $v['input_border_color_focus'];
					$shadow = $this->convert_hex_to_rbg( $color );
					$shadow = implode( ',', $shadow ).',0.8';
?>
.login form input[type=text]:focus,
.login form input[type=password]:focus,
.login form input[type=checkbox]:focus,
.login form input[type=submit]:focus
{
    border-color:<?php echo esc_attr( $color ); ?>;
    -webkit-box-shadow:0 0 2px rgba(<?php echo esc_attr( $shadow ); ?>);
    -moz-box-shadow:0 0 2px rgba(<?php echo esc_attr( $shadow ); ?>);
    box-shadow:0 0 2px rgba(<?php echo esc_attr( $shadow ); ?>);
}
<?php
				}

				/**
				 * form_bg_color
				 * form_bg_transparency
				 */

				$this->css_background_transparency( $v, 'form_bg_color', 'form_bg_transparency', '.login form' );

				/**
				 * form_style
				 */
				if ( isset( $v['form_style'] ) && 'shadow' == $v['form_style'] ) {
?>
.login form {
    -webkit-box-shadow: 10px 10px 15px 0px rgba(0,0,0,0.5);
    -moz-box-shadow: 10px 10px 15px 0px rgba(0,0,0,0.5);
    box-shadow: 10px 10px 15px 0px rgba(0,0,0,0.5);
}
<?php
				}
				/**
				 * form_button_color
				 */
				$this->css_background_color( $v, 'form_button_color', '.login form .button' );
				$this->css_background_color( $v, 'form_button_color_active', '.login form .button:active' );
				$this->css_background_color( $v, 'form_button_color_focus', '.login form .button:focus' );
				$this->css_background_color( $v, 'form_button_color_hover', '.login form .button:hover' );
				/**
				 * form_button_text_color
				 */
				$this->css_color( $v, 'form_button_text_color', '.login form .button' );
				/**
				 * show_remember_me
				 */
				$this->css_hide( $v, 'show_remember_me', '.login .forgetmenot' );
				/**
				 * form_button_border
				 */
				if ( isset( $v['form_button_border'] ) ) {
?>
.login form input[type=submit] {
    border-width: <?php echo intval( $v['form_button_border'] ); ?>px;
}
<?php
				}
				/**
				 * form_button_shadow
				 */
				if ( isset( $v['form_button_shadow'] ) && 'off' == $v['form_button_shadow'] ) {
?>
.login form input[type=submit] {
    -webkit-box-shadow: none;
    -moz-box-shadow: none;
    box-shadow: none;
}
<?php
				}
				/**
				 * form_button_text_shadow
				 */
				if ( isset( $v['form_button_text_shadow'] ) && 'off' == $v['form_button_text_shadow'] ) {
?>
.login form input[type=submit] {
    text-shadow: none;
}
<?php
				}
				/**
				 * rounded_form
				 */
				if ( isset( $v['form_button_rounded'] ) ) {
?>
.login form input[type=submit] {
    -webkit-border-radius: <?php echo intval( $v['form_button_rounded'] ); ?>px;
    -moz-border-radius: <?php echo intval( $v['form_button_rounded'] ); ?>px;
    border-radius: <?php echo intval( $v['form_button_rounded'] ); ?>px;
}
<?php
				}
			}

			/**
			 * Form Error Messages
			 */
			if ( isset( $value['form_errors'] ) ) {
				$v = $value['form_errors'];
				/**
				 * login_error_background_color
				 */
				$this->css_background_color( $v, 'login_error_background_color', '.login #login #login_error' );

				/**
				 * login_error_border_color
				 */
				if ( isset( $v['login_error_border_color'] ) ) {
?>
.login #login #login_error {
    border-color: <?php echo $v['login_error_border_color']; ?>;
}
<?php
				}
				/**
				 * login_error_text_color
				 */
				$this->css_color( $v, 'login_error_text_color', '.login #login #login_error' );
				$this->css_color( $v, 'login_error_link_color', '.login #login #login_error a' );
				$this->css_color( $v, 'login_error_link_color_hover', '.login #login #login_error a:hover' );
				$this->css_opacity( $v, 'login_error_transarency', '.login #login #login_error' );
			}

			/**
			 * below_form
			 */
			if ( isset( $value['below_form'] ) ) {
				$v = $value['below_form'];
				/**
				 * show_register_and_lost
				 */
				$this->css_hide( $v, 'show_register_and_lost', '.login #nav' );
				/**
				 * show_back_to
				 */
				$this->css_hide( $v, 'show_back_to', '.login #backtoblog' );
				/**
				 * register_and_lost_color_link
				 */
				$this->css_color( $v, 'register_and_lost_color_link', '.login #nav a' );
				/**
				 * register_and_lost_color_hover
				 */
				$this->css_color( $v, 'register_and_lost_color_hover', '.login #nav a:hover' );
				/**
				 * back_to_color_link
				 */
				$this->css_color( $v, 'back_to_color_link', '.login #backtoblog a' );
				/**
				 * back_to_color_hover
				 */
				$this->css_color( $v, 'back_to_color_hover', '.login #backtoblog a:hover' );
			}

			/**
			 * form_canvas
			 */
			if ( isset( $value['form_canvas'] ) ) {
				echo '#login{';
				$v = $value['form_canvas'];
				if ( isset( $v['position'] ) ) {
					if ( preg_match( '/^(left|right)$/', $v['position'] ) ) {
						printf( 'margin-%s: 0;', esc_attr( $v['position'] ) );
					}
				}
				if ( isset( $v['padding_top'] ) ) {
					printf( 'padding-top: %d%%;', $v['padding_top'] );
				}
				if ( isset( $v['width'] ) ) {
					printf( 'width: %dpx;', $v['width'] );
				}
				if ( isset( $v['fit'] ) && 'on' == $v['fit'] ) {
					echo 'position: absolute;';
					echo 'top: 0;';
					echo 'bottom: 0;';
					if ( isset( $v['position'] ) ) {
						if ( preg_match( '/^(left|right)$/', $v['position'] ) ) {
							printf( '%s: 0;', esc_attr( $v['position'] ) );
						}
					}
				}
				echo '}';
				if ( isset( $v['form_margin'] ) ) {
					echo '.login form, .login #nav, .login #backtoblog {';
					printf( 'margin-left: %dpx;', $v['form_margin'] );
					printf( 'margin-right: %dpx;', $v['form_margin'] );
					echo '}';
				}
				if ( isset( $v['fit'] ) && 'on' == $v['fit'] ) {
					echo '.login form {';
					echo 'margin-top:0;';
					echo '}';
				}
				$this->css_background_transparency( $v, 'background_color', 'background_transparency', '#login' );
			}

			echo '</style>';
		}

		protected function set_options() {

			$login_header_url   = __( 'https://wordpress.org/', 'ub' );
			$login_header_title = __( 'Powered by WordPress', 'ub' );

			if ( is_multisite() ) {
				$login_header_url   = network_home_url();
				$login_header_title = get_network()->site_name;
			}

			/**
			 * invalid username
			 */
			$invalid_username = __( '<strong>ERROR</strong>: Invalid username.' );
			$invalid_username .= ' <a href="WP_LOSTPASSWORD_URL">';
			$invalid_username  .= __( 'Lost your password?', 'ub' );
			$invalid_username  .= '</a>';

			$invalid_password = __( '<strong>ERROR</strong>: The password you entered for the username %s is incorrect.', 'ub' );
			$invalid_password .= ' <a href="WP_LOSTPASSWORD_URL">';
			$invalid_password .= __( 'Lost your password?', 'ub' );
			$invalid_password .= '</a>';

			$this->options = array(
				'logo_and_background' => array(
					'title' => __( 'Logo & Background', 'ub' ),
					'fields' => array(
						'show_logo' => array(
							'type' => 'checkbox',
							'label' => __( 'Logo', 'ub' ),
							'description' => __( 'Would you like to show the logo?', 'ub' ),
							'options' => array(
								'on' => __( 'Show', 'ub' ),
								'off' => __( 'Hide', 'ub' ),
							),
							'default' => 'on',
							'classes' => array( 'switch-button' ),
							'slave-class' => 'logo-related',
						),
						'logo_upload' => array(
							'type' => 'media',
							'label' => __( 'Logo image', 'ub' ),
							'description' => __( 'Upload your own logo. Max width: 320px', 'ub' ),
							'master' => 'logo-related',
						),
						'logo_width' => array(
							'type' => 'number',
							'label' => __( 'Logo width', 'ub' ),
							'default' => 84,
							'min' => 0,
							'max' => 320,
							'classes' => array( 'ui-slider' ),
							'master' => 'logo-related',
							'after' => __( 'px', 'ub' ),
						),
						'logo_transparency' => array(
							'type' => 'number',
							'label' => __( 'Logo transparency', 'ub' ),
							'min' => 0,
							'max' => 100,
							'default' => 100,
							'classes' => array( 'ui-slider' ),
							'master' => 'logo-related',
							'after' => '%',
						),
						'logo_rounded' => array(
							'type' => 'number',
							'label' => __( 'Logo radius corners', 'ub' ),
							'description' => __( 'How much would you like to round the border?', 'ub' ),
							'attributes' => array( 'placeholder' => '20' ),
							'default' => 0,
							'min' => 0,
							'classes' => array( 'ui-slider' ),
							'master' => 'logo-related',
							'after' => __( 'px', 'ub' ),
						),
						'login_header_url' => array(
							'type' => 'text',
							'label' => __( 'Logo URL', 'ub' ),
							'default' => $login_header_url,
							'classes' => array( 'logo-related', 'large-text' ),
							'master' => 'logo-related',
						),
						'login_header_title' => array(
							'type' => 'text',
							'label' => __( 'Logo Alt text', 'ub' ),
							'default' => $login_header_title,
							'classes' => array( 'logo-related', 'large-text' ),
							'master' => 'logo-related',
						),
						'logo_bottom_margin' => array(
							'type' => 'number',
							'label' => __( 'Logo bottom margin', 'ub' ),
							'description' => __( 'The default value will work for most users, but you may change the margin height here.', 'ub' ),
							'attributes' => array( 'placeholder' => '25' ),
							'default' => 25,
							'min' => 0,
							'classes' => array( 'ui-slider' ),
							'master' => 'logo-related',
							'after' => __( 'px', 'ub' ),
						),
						'bg_color' => array(
							'type' => 'color',
							'label' => __( 'Background color', 'ub' ),
							'default' => '#f1f1f1',
						),
						'fullscreen_bg' => array(
							'type' => 'media',
							'label' => __( 'Background Image', 'ub' ),
							'description' => __( 'You can upload a background image here. The image will stretch to fit the page, and will automatically resize as the window size changes. You\'ll have the best results by using images with a minimum width of 1024px.', 'ub' ),
						),
					),
				),
				'form' => array(
					'title' => __( 'Form', 'ub' ),
					'fields' => array(
						'rounded_nb' => array(
							'type' => 'number',
							'label' => __( 'Radius form corner', 'ub' ),
							'description' => __( 'How much would you like to round the border?', 'ub' ),
							'attributes' => array( 'placeholder' => '20' ),
							'default' => 0,
							'min' => 0,
							'classes' => array( 'ui-slider' ),
							'after' => __( 'px', 'ub' ),
						),
						'show_remember_me' => array(
							'type' => 'radio',
							'label' => __( 'Show "Remember Me" checkbox', 'ub' ),
							'description' => __( 'Would you like to show the "Remember Me" checkbox?', 'ub' ),
							'options' => array(
								'on' => __( 'Show "Remember Me" checkbox.', 'ub' ),
								'off' => __( 'Hide "Remember Me" checkbox.', 'ub' ),
							),
							'default' => 'on',
							'slave-class' => 'remember-me-related',
						),
						'check_remember_me' => array(
							'label' => __( 'Check "Remember Me" checkbox', 'ub' ),
							'type' => 'checkbox',
							'description' => __( 'Check by defulat "Remember Me" checkbox', 'ub' ),
							'options' => array(
								'on' => __( 'Checked', 'ub' ),
								'off' => __( 'Unchecked', 'ub' ),
							),
							'default' => 'off',
							'classes' => array( 'switch-button' ),
							'master' => 'remember-me-related',
						),
						'signup_password' => array(
							'label' => __( 'Signup Password', 'ub' ),
							'type' => 'checkbox',
							'description' => __( 'Add password field on register screen.', 'ub' ),
							'options' => array(
								'on' => __( 'Show', 'ub' ),
								'off' => __( 'Hide', 'ub' ),
							),
							'default' => 'off',
							'classes' => array( 'switch-button' ),
						),
						'label_color' => array(
							'type' => 'color',
							'label' => __( 'Label color', 'ub' ),
							'default' => '#777777',
						),
						'input_border_color_focus' => array(
							'type' => 'color',
							'label' => __( 'Input border on focus', 'ub' ),
							'default' => '#5b9dd9',
							'description' => __( 'Allows changing border &amp; shadow for focused inputs.', 'ub' ),
						),
						'form_bg_color' => array(
							'type' => 'color',
							'label' => __( 'Background color', 'ub' ),
							'default' => '#ffffff',
						),
						'form_bg' => array(
							'type' => 'media',
							'label' => __( 'Background image', 'ub' ),
						),
						'form_bg_transparency' => array(
							'type' => 'number',
							'label' => __( 'Background transparency', 'ub' ),
							'min' => 0,
							'max' => 100,
							'default' => 100,
							'description' => __( 'It will not affected if you set for background image.', 'ub' ),
							'classes' => array( 'ui-slider' ),
							'after' => '%',
						),
						'form_style' => array(
							'type' => 'radio',
							'label' => __( 'Form Style', 'ub' ),
							'options' => array(
								'flat' => __( 'Flat', 'ub' ),
								'shadow' => __( 'Shadowed Box', 'ub' ),
							),
							'description' => __( 'Choose the style of the form.', 'ub' ),
							'default' => 'flat',
						),
						'form_button_color' => array(
							'type' => 'color',
							'label' => __( 'Button color', 'ub' ),
							'default' => '#2ea2cc',
						),
						'form_button_color_active' => array(
							'type' => 'color',
							'label' => __( 'Button color on active', 'ub' ),
							'default' => '#0073aa',
						),
						'form_button_color_focus' => array(
							'type' => 'color',
							'label' => __( 'Button color on focus', 'ub' ),
							'default' => '#5b9dd92',
						),
						'form_button_text_color_focus' => array(
							'type' => 'color',
							'label' => __( 'Button Text color focus', 'ub' ),
							'default' => '#ffffff',
						),
						'form_button_text_color_hover' => array(
							'type' => 'color',
							'label' => __( 'Button Text color hover', 'ub' ),
							'default' => '#ffffff',
						),
						'form_button_text_shadow' => array(
							'type' => 'checkbox',
							'label' => __( 'Button text shadow', 'ub' ),
							'description' => __( 'Would you like to add button text shadow?', 'ub' ),
							'options' => array(
								'on' => __( 'Yes', 'ub' ),
								'off' => __( 'No', 'ub' ),
							),
							'default' => 'on',
							'classes' => array( 'switch-button' ),
						),
						'form_button_border' => array(
							'type' => 'number',
							'label' => __( 'Button border width', 'ub' ),
							'min' => 0,
							'max' => 10,
							'default' => 1,
							'classes' => array( 'ui-slider' ),
							'after' => __( 'px', 'ub' ),
						),
						'form_button_shadow' => array(
							'type' => 'checkbox',
							'label' => __( 'Form button shadow', 'ub' ),
							'description' => __( 'Would you like to add button shadow?', 'ub' ),
							'options' => array(
								'on' => __( 'Yes', 'ub' ),
								'off' => __( 'No', 'ub' ),
							),
							'default' => 'on',
							'classes' => array( 'switch-button' ),
						),
						'form_button_rounded' => array(
							'type' => 'number',
							'label' => __( 'Button radius corners', 'ub' ),
							'min' => 0,
							'default' => 3,
							'classes' => array( 'ui-slider' ),
							'after' => __( 'px', 'ub' ),
						),
					),
				),
				/**
				 * Form labels
				 */
				'form_labels' => array(
					'title' => __( 'Form labels', 'ub' ),
					'fields' => array(
						'label_username' => array(
							'type' => 'text',
							'label' => __( 'Label username text', 'ub' ),
							'default' => __( 'Username or E-mail Address', 'ub' ),
						),
						'label_password' => array(
							'type' => 'text',
							'label' => __( 'Label password text', 'ub' ),
							'default' => __( 'Password', 'ub' ),
						),
						'label_log_in' => array(
							'type' => 'text',
							'label' => __( 'Login button text', 'ub' ),
							'default' => __( 'Log In', 'ub' ),
						),
					),
				),
				/**
				 * Form error messages
				 */
				'form_errors' => array(
					'title' => __( 'Error messages', 'ub' ),
					'fields' => array(
						'empty_username' => array(
							'type' => 'text',
							'label' => __( 'Empty username', 'ub' ),
							'default' => __( '<strong>ERROR</strong>: The username field is empty.', 'ub' ),
						),
						'invalid_username' => array(
							'type' => 'text',
							'label' => __( 'Invalid username', 'ub' ),
							'description' => __( 'Use "WP_LOSTPASSWORD_URL" placeholder to replace it by WordPress.', 'ub' ),
							'default' => $invalid_username,
						),
						'empty_password' => array(
							'type' => 'text',
							'label' => __( 'Empty password', 'ub' ),
							'default' => __( '<strong>ERROR</strong>: The password field is empty.', 'ub' ),
						),
						'incorrect_password' => array(
							'type' => 'text',
							'label' => __( 'Invalid password', 'ub' ),
							'description' => __( 'Use "WP_LOSTPASSWORD_URL", "USERNAME" placeholder to replace it by WordPress.', 'ub' ),
							'default' => $invalid_password,
						),
						'login_error_background_color' => array(
							'type' => 'color',
							'label' => __( 'Background color', 'ub' ),
							'default' => '#ffffff',
						),
						'login_error_border_color' => array(
							'type' => 'color',
							'label' => __( 'Border color', 'ub' ),
							'default' => '#dc3232',
						),
						'login_error_text_color' => array(
							'type' => 'color',
							'label' => __( 'Text color', 'ub' ),
							'default' => '#444444',
						),
						'login_error_link_color' => array(
							'type' => 'color',
							'label' => __( 'Link color', 'ub' ),
							'default' => '#0073aa',
						),
						'login_error_link_color_hover' => array(
							'type' => 'color',
							'label' => __( 'Hover link color', 'ub' ),
							'default' => '#00a0d2',
						),
						'login_error_transarency' => array(
							'type' => 'number',
							'label' => __( 'Transparency', 'ub' ),
							'min' => 0,
							'max' => 100,
							'default' => 100,
							'classes' => array( 'ui-slider' ),
							'after' => '%',
						),
					),
				),
				/**
				 * Below Form Links
				 */
				'below_form' => array(
					'title' => __( 'Below Form Links', 'ub' ),
					'fields' => array(
						'show_register_and_lost' => array(
							'type' => 'checkbox',
							'label' => __( '"Register | Lost your password?" links', 'ub' ),
							'description' => __( 'Would you like to show the "Register | Lost your password?" links?', 'ub' ),
							'options' => array(
								'on' => __( 'Show', 'ub' ),
								'off' => __( 'Hide', 'ub' ),
							),
							'default' => 'on',
							'classes' => array( 'switch-button' ),
						),
						'show_back_to' => array(
							'type' => 'checkbox',
							'label' => __( '"Back to" link', 'ub' ),
							'description' => __( 'Would you like to show the "&larr; Back to" link?', 'ub' ),
							'options' => array(
								'on' => __( 'Show', 'ub' ),
								'off' => __( 'Hide', 'ub' ),
							),
							'default' => 'on',
							'classes' => array( 'switch-button' ),
						),
						'register_and_lost_color_link' => array(
							'type' => 'color',
							'label' => __( '"Register | Lost your password?" links color', 'ub' ),
							'default' => '#999999',
						),
						'register_and_lost_color_hover' => array(
							'type' => 'color',
							'label' => __( '"Register | Lost your password?" link hover color', 'ub' ),
							'default' => '#2ea2cc',
						),
						'back_to_color_link' => array(
							'type' => 'color',
							'label' => __( '"Back to" link color', 'ub' ),
							'default' => '#999999',
						),
						'back_to_color_hover' => array(
							'type' => 'color',
							'label' => __( '"Back to" link hover color', 'ub' ),
							'default' => '#2ea2cc',
						),
					),
				),
				/**
				 * Form Position
				 */
				'form_canvas' => array(
					'title' => __( 'Form Canvas & Position', 'ub' ),
					'fields' => array(
						'position' => array(
							'type' => 'radio',
							'label' => __( 'Position', 'ub' ),
							'options' => array(
								'default' => __( 'Default postion', 'ub' ),
								'left' => __( 'Left', 'ub' ),
								'right' => __( 'Right', 'ub' ),
							),
							'default' => 'default',
						),
						'padding_top' => array(
							'type' => 'number',
							'label' => __( 'Padding top', 'ub' ),
							'min' => 0,
							'max' => 50,
							'default' => 8,
							'classes' => array( 'ui-slider' ),
							'after' => '%',
						),
						'width' => array(
							'type' => 'number',
							'label' => __( 'Width', 'ub' ),
							'min' => 200,
							'max' => 2000,
							'default' => 320,
							'classes' => array( 'ui-slider' ),
							'after' => __( 'px', 'ub' ),
						),
						'form_margin' => array(
							'type' => 'number',
							'label' => __( 'Form margin', 'ub' ),
							'min' => 0,
							'default' => 0,
							'classes' => array( 'ui-slider' ),
							'after' => __( 'px', 'ub' ),
						),
						'fit' => array(
							'type' => 'checkbox',
							'label' => __( 'Fit height', 'ub' ),
							'options' => array(
								'off' => __( 'Default', 'ub' ),
								'on' => __( 'Fit', 'ub' ),
							),
							'default' => 'off',
							'classes' => array( 'switch-button' ),
						),
						'background_color' => array(
							'type' => 'color',
							'label' => __( 'Background color', 'ub' ),
							'default' => 'transparent',
						),
						'background_transparency' => array(
							'type' => 'number',
							'label' => __( 'Background transparency', 'ub' ),
							'min' => 0,
							'max' => 100,
							'default' => 0,
							'classes' => array( 'ui-slider' ),
							'after' => '%',
						),
					),
				),
				/**
				 * Redirects
				 */
				'redirect' => array(
					'title' => __( 'Redirects', 'ub' ),
					'fields' => array(
						'login' => array(
							'type' => 'checkbox',
							'label' => __( 'Login', 'ub' ),
							'options' => array(
								'on' => __( 'On', 'ub' ),
								'off' => __( 'Off', 'ub' ),
							),
							'default' => 'off',
							'classes' => array( 'switch-button' ),
							'slave-class' => 'login-related',
						),
						'login_url' => array(
							'type' => 'url',
							'label' => __( 'Login URL', 'ub' ),
							'master' => 'login-related',
						),
						'logout' => array(
							'type' => 'checkbox',
							'label' => __( 'Logout', 'ub' ),
							'options' => array(
								'on' => __( 'On', 'ub' ),
								'off' => __( 'Off', 'ub' ),
							),
							'default' => 'off',
							'classes' => array( 'switch-button' ),
							'slave-class' => 'logout-related',
						),
						'logout_url' => array(
							'type' => 'url',
							'label' => __( 'Logout URL', 'ub' ),
							'master' => 'logout-related',
						),
					),
				),
			);
		}

		public function login_headerurl( $value ) {
			$new = $this->get_value( 'logo_and_background', 'login_header_url' );
			if ( null === $new ) {
				return $value;
			}
			return $new;
		}

		public function login_headertitle( $value ) {
			$new = $this->get_value( 'logo_and_background', 'login_header_title' );
			if ( null === $new ) {
				return $value;
			}
			return $new;
		}

		public function wp_login_errors( $errors ) {
			$value = $this->get_value( 'form_errors' );
			if ( is_array( $value ) ) {
				foreach ( $value as $code => $message ) {
					if ( isset( $errors->errors[ $code ] ) ) {
						$errors->errors[ $code ][0] = stripslashes( $this->replace_placeholders( $message, $code ) );
					}
				}
			}
			return $errors;
		}

		public function gettext_login_form_labels( $translated_text, $text, $domain ) {
			if ( $this->proceed_gettext && 'default' == $domain ) {
				if ( empty( $this->patterns ) ) {
					$options = $this->options['form_labels'];
					foreach ( $options['fields'] as $key => $data ) {
						$this->patterns[ $data['default'] ] = $this->get_value( 'form_labels', $key );
					}
				}
				if ( isset( $this->patterns[ $translated_text ] ) ) {
					return stripslashes( $this->patterns[ $translated_text ] );
				}
			}

			return $translated_text;
		}

		private function replace_placeholders( $string, $code = '' ) {
			/**
			 * Exception for user name
			 * https://app.asana.com/0/47431170559378/47431170559399
			 */
			if ( 'incorrect_password' == $code ) {
				$string = sprintf( $string, 'USERNAME' );
			}
			$lost_password_url = wp_lostpassword_url();
			$string = preg_replace( '/WP_LOSTPASSWORD_URL/', $lost_password_url, $string );
			$username = '';
			if ( isset( $_POST['log'] ) ) {
				$username = esc_attr( $_POST['log'] );
			}
			$string = preg_replace( '/USERNAME/', $username, $string );
			return $string;
		}

		private function convert_hex_to_rbg( $hex ) {
			if ( preg_match( '/^#.{6}$/', $hex ) ) {
				return sscanf( $hex, '#%02x%02x%02x' );
			}
			return $hex;
		}

		private function css_color( $data, $key, $selector ) {
			if ( isset( $data[ $key ] ) && ! empty( $data[ $key ] ) ) {
				printf( '%s{color:%s}', $selector, $data[ $key ] );
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					echo PHP_EOL;
				}
			}
		}

		private function css_background_color( $data, $key, $selector ) {
			if ( isset( $data[ $key ] ) && ! empty( $data[ $key ] ) ) {
				printf( '%s{background-color:%s}', $selector, $data[ $key ] );
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					echo PHP_EOL;
				}
			}
		}

		/**
		 * Background color with transparency.
		 *
		 * @since x.x.x
		 */
		private function css_background_transparency( $v, $color, $transparency, $selector ) {
			$change = false;
			$bg_color = 'none';
			$bg_transparency = 0;
			if ( isset( $v[ $color ] ) ) {
				$bg_color = $v[ $color ];
				$change = true;
			}
			if ( isset( $v[ $transparency ] ) ) {
				$bg_transparency = $v[ $transparency ];
				$change = true;
			}
			if ( $change ) {
				if ( 'none' != $bg_color ) {
					echo $selector;
					echo ' {';
					if ( 0 < $bg_transparency ) {
						$bg_color = $this->convert_hex_to_rbg( $bg_color );
						printf( 'background-color: rgba( %s, %0.2f);', implode( ', ', $bg_color ), $bg_transparency / 100 );
					} else {
						printf( 'background-color: %s;', $bg_color );
					}
					echo '}';
				}
			}
		}

		private function css_opacity( $data, $key, $selector ) {
			if ( isset( $data[ $key ] ) && ! empty( $data[ $key ] ) ) {
				printf( '%s{opacity:%0.2f}', $selector, $data[ $key ] / 100 );
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					echo PHP_EOL;
				}
			}
		}

		private function css_hide( $data, $key, $selector ) {
			if ( isset( $data[ $key ] ) && 'off' == $data[ $key ] ) {
				printf( '%s{display:none}', $selector );
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					echo PHP_EOL;
				}
			}
		}

		/**
		 * Allow to uload SVG files.
		 *
		 * @since 1.8.9
		 */
		public function add_svg_to_allowed_mime_types( $mimes ) {
			$mimes['svg'] = 'image/svg+xml';
			return $mimes;
		}


		/**
		 * Add button to predefined configuration
		 *
		 * @since 1.8.9
		 */
		public function before_admin_options_page( $option_name ) {
			if ( $this->option_name != $option_name ) {
				return;
			}
			$this->set_theme();
			$button = __( 'Predefined  Configuration', 'ub' );
			$url = $this->get_base_url();
			if ( isset( $_REQUEST['panel'] ) && 'predefined' == $_REQUEST['panel'] ) {
				$url = remove_query_arg( 'panel', $url );
				$button = __( 'Custom Configuration', 'ub' );
			} else {
				$url = add_query_arg( 'panel', 'predefined', $url );
			}
			printf(
				'<p><a href="%s" class="button">%s</a></p>',
				esc_url( $url ),
				esc_html( $button )
			);
		}

		/**
		 * handle custom screen
		 *
		 * @since 1.8.9
		 */
		public function module_admin_options_page() {
			if ( isset( $_REQUEST['panel'] ) && 'predefined' == $_REQUEST['panel'] ) {
				$this->themes();
			} else {
				$this->admin_options_page();
			}
		}

		/**
		 * handle themes screen
		 *
		 * @since 1.8.9
		 */
		private function themes() {
			$this->before_admin_options_page( $this->option_name );
			add_filter( 'ultimatebranding_settings_panel_show_submit', '__return_false' );
			printf( '<h2>%s</h2>', __( 'Select predefined login screen', 'ub' ) );
			$themes = $this->get_themes();
			if ( false === $themes ) {
				echo '<div class="ub-module-error"><p>';
				_e( 'There is no predefined configurations.', 'ub' );
				echo '</p></div>';
				return;
			}
?>

<div class="theme-browser">
	<div class="themes wp-clearfix">

<?php
foreach ( $themes as $theme ) {
	$aria_action = esc_attr( $theme['id'] . '-action' );
	$aria_name   = esc_attr( $theme['id'] . '-name' );
	?>
<div class="theme" tabindex="0" aria-describedby="<?php echo $aria_action . ' ' . $aria_name; ?>">
	<?php if ( ! empty( $theme['screenshot'] ) ) { ?>
		<div class="theme-screenshot">
			<img src="<?php echo $theme['screenshot']; ?>" alt="" />
		</div>
	<?php } else { ?>
		<div class="theme-screenshot blank"></div>
	<?php } ?>
	<div class="theme-author"><?php printf( __( 'By %s', 'ub' ), $theme['Author'] ); ?></div>
	<h2 class="theme-name" id="<?php echo $aria_name; ?>"><?php echo $theme['Name']; ?></h2>
	<div class="theme-actions">
		<?php
		/* translators: %s: Theme name */
		$aria_label = sprintf( _x( 'Import settings %s', 'ub' ), '{{ data.name }}' );
		/**
	 * import link
	 */
		$url = $this->get_base_url();
		$url = add_query_arg(
			array(
			'theme' => $theme['id'],
			'panel' => 'predefined',
			),
			$url
		);
		$url = wp_nonce_url( $url, 'import-'.$theme['id'] );

		?>
        <a class="button activate" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( 'Import settings', 'ub' ); ?></a>
	</div>
</div>
<?php }  ?>
	</div>
</div>
<?php
		}

		/**
		 * get themes array
		 *
		 * Based on wp-includes/theme.php search_theme_directories() function.
		 *
		 * @since 1.8.9
		 */
		private function get_themes() {
			if ( empty( $this->dirs ) ) {
				return false;
			}
			$found_themes = array();
			$file_headers = array(
				'Name'        => 'Theme Name',
				'ThemeURI'    => 'Theme URI',
				'Description' => 'Description',
				'Author'      => 'Author',
				'AuthorURI'   => 'Author URI',
				'Version'     => 'Version',
			);
			$theme_root = dirname( __FILE__ ).'/custom-login-screen/themes/';
			foreach ( $this->dirs as $dir ) {
				if ( ! is_dir( $theme_root . '/' . $dir ) || $dir[0] == '.' || $dir == 'CVS' ) {
					continue;
				}
				if ( file_exists( $theme_root . '/' . $dir . '/style.css' ) ) {
					// wp-content/themes/a-single-theme
					// wp-content/themes is $theme_root, a-single-theme is $dir
					$found_themes[ $dir ] = array(
						'id' => sanitize_title( $dir ),
						'theme_file' => $dir . '/style.css',
						'theme_root' => $theme_root,
					);
					foreach ( array( 'png', 'gif', 'jpg', 'jpeg' ) as $ext ) {
						$file = $theme_root. $dir . "/screenshot.$ext";
						if ( file_exists( $file ) ) {
							$found_themes[ $dir ]['screenshot'] = plugins_url( 'themes/'.$dir . "/screenshot.$ext", $theme_root );
						}
					}
					$data = get_file_data( $theme_root.$dir.'/style.css', $file_headers, 'theme' );
					if ( is_array( $data ) ) {
						$found_themes[ $dir ] = array_merge( $found_themes[ $dir ], $data );
					}
				}
			}
			return $found_themes;
		}

		/**
		 * import theme data
		 *
		 * @since 1.8.9
		 */
		private function set_theme() {
			if ( ! isset( $_REQUEST['theme'] ) ) {
				return;
			}
			if ( ! isset( $_REQUEST['_wpnonce'] ) ) {
				return;
			}
			if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'import-'.$_REQUEST['theme'] ) ) {
				return;
			}
			$id = $_REQUEST['theme'];
			$themes = $this->get_themes();
			if ( ! isset( $themes[ $id ] ) ) {
				$this->notice( __( 'Selected theme was not found!', 'ub' ), 'error' );
				return;
			}
			$theme = $themes[ $id ];
			$data = include_once( $theme['theme_root'].$id.'/index.php' );
			if ( empty( $data ) ) {
				$message = sprintf(
					__( 'Failed to load "%s" theme configuration!', 'ub' ),
					$theme['Name']
				);
				$this->notice( $message, 'error' );
				return;
			}
			ub_update_option( $this->option_name, $data );
			$message = '<p>';
			$message .= sprintf(
				__( '"%s" theme configuration was successfully loaded!', 'ub' ),
				sprintf( '<b>%s</b>', esc_html( $theme['Name'] ) )
			);
			$message .= '</p>';
			$message .= '<p>';
			$message .= __( 'You can change options on "Custom Configuration" panel.', 'ub' );
			$message .= '</p>';

			$this->notice( $message, 'success' );
		}

		/**
		 *
		 * @since 1.9.4
		 */
		public function set_remember_me( $errors ) {
			$value = $this->get_value( 'form', 'check_remember_me' );
			if ( 'on' === $value ) {
				$_POST['rememberme'] = 1;
			}
			return $errors;
		}

		/**
		 * login_redirect
		 *
		 * @since 1.9.4
		 */
		public function login_redirect( $redirect_to, $requested_redirect_to, $user ) {
			$value = $this->get_value( 'redirect', 'login' );
			if ( 'on' === $value ) {
				$value = $this->get_value( 'redirect', 'login_url' );
				if ( ! empty( $value ) ) {
					$redirect_to = $value;
				}
			}
			return $redirect_to;
		}

		/**
		 * logout_redirect
		 *
		 * @since 1.9.4
		 */
		public function logout_redirect( $redirect_to, $requested_redirect_to, $user ) {
			$value = $this->get_value( 'redirect', 'logout' );
			if ( 'on' === $value ) {
				$value = $this->get_value( 'redirect', 'logout_url' );
				if ( ! empty( $value ) ) {
					$redirect_to = $value;
				}
			}
			return $redirect_to;
		}
	}

}

new ub_custom_login_screen();
