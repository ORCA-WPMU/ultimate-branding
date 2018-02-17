<?php
/*
Plugin Name: MultiSite Registration e-mails
Description:
Author: Marcin (Incsub)
Version: 1.0
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

if ( ! class_exists( 'ub_custom_ms_register_emails' ) ) {

	class ub_custom_ms_register_emails extends ub_helper {

		protected $option_name = 'global_ms_register_mails';

		public function __construct() {
			parent::__construct();
			add_action( 'ultimatebranding_settings_custom_ms_register_emails', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_custom_ms_register_emails_process', array( $this, 'update' ), 10, 1 );
			/**
			 * replace
			 */
			/** This filter is documented in wp-includes/ms-functions.php */
			add_filter( 'wpmu_signup_blog_notification_email', array( $this, 'wpmu_signup_blog_notification_email' ), 10, 8 );
			/** This filter is documented in wp-includes/ms-functions.php */
			add_filter( 'wpmu_signup_blog_notification_subject', array( $this, 'wpmu_signup_blog_notification_subject' ), 10, 8 );
			/** This filter is documented in wp-includes/ms-functions.php */
			add_filter( 'wpmu_signup_user_notification_email', array( $this, 'wpmu_signup_user_notification_email' ), 10, 5 );
			/** This filter is documented in wp-includes/ms-functions.php */
			add_filter( 'wpmu_signup_user_notification_subject', array( $this, 'wpmu_signup_user_notification_subject' ), 10, 5 );
		}

		/**
		 * modify option name
		 *
		 * @since 1.9.2
		 */
		public function get_module_option_name( $option_name, $module ) {
			if ( is_string( $module ) && 'custom-ms-register-emails' == $module ) {
				return $this->option_name;
			}
			return $option_name;
		}

		public function wpmu_signup_blog_notification_email( $email, $domain, $path, $title, $user_login, $user_email, $key, $meta ) {
			$this->set_data();
			if ( 'on' == $this->get_value( 'wpmu_signup_blog_notification', 'status' ) ) {
				return $this->get_value( 'wpmu_signup_blog_notification', 'message' );
			}
			return $email;
		}

		public function wpmu_signup_blog_notification_subject( $subject, $domain, $path, $title, $user_login, $user_email, $key, $meta ) {
			$this->set_data();
			if ( 'on' == $this->get_value( 'wpmu_signup_blog_notification', 'status' ) ) {
				return $this->get_value( 'wpmu_signup_blog_notification', 'title' );
			}
			return $subject;
		}

		public function wpmu_signup_user_notification_email( $email, $user_login, $user_email, $key, $meta ) {
			$this->set_data();
			if ( 'on' == $this->get_value( 'wpmu_signup_user_notification', 'status' ) ) {
				return $this->get_value( 'wpmu_signup_user_notification', 'message' );
			}
		}

		public function wpmu_signup_user_notification_subject( $subject, $user_login, $user_email, $key, $meta ) {
			$this->set_data();
			if ( 'on' == $this->get_value( 'wpmu_signup_user_notification', 'status' ) ) {
				return $this->get_value( 'wpmu_signup_user_notification', 'title' );
			}
			return $subject;
		}

		public function update( $status ) {
			$value = $_POST['simple_options'];
			if ( $value == '' ) {
				$value = 'empty';
			}
			$this->set_options();
			foreach ( $this->options as $section_key => $section_data ) {
				if ( ! isset( $section_data['fields'] ) ) {
					continue;
				}
				foreach ( $section_data['fields'] as $key => $data ) {
					switch ( $data['type'] ) {
						case 'checkbox':
							if ( isset( $value[ $section_key ][ $key ] ) ) {
								$value[ $section_key ][ $key ] = 'on';
							} else {
								$value[ $section_key ][ $key ] = 'off';
							}
					}
				}
			}
			ub_update_option( $this->option_name , $value );
			if ( $status === false ) {
				return $status;
			} else {
				return true;
			}
		}

		protected function set_options() {
			$new_blog_message = __( "To activate your blog, please click the following link:\n\n%s\n\nAfter you activate, you will receive *another e-mail* with your login.\n\nAfter you activate, you can visit your site here:\n\n%s" );
			$new_blog_title = _x( '[%1$s] Activate %2$s', 'New site notification e-mail subject' );
			$new_sign_up_message = __( "To activate your user, please click the following link:\n\n%s\n\nAfter you activate, you will receive *another e-mail* with your login." );
			$new_sign_up_title = _x( '[%1$s] Activate %2$s', 'New user notification e-mail subject' );
			$welcome_email = __( 'Howdy USERNAME,

Your new SITE_NAME site has been successfully set up at:
BLOG_URL

You can log in to the administrator account with the following information:

Username: USERNAME
Password: PASSWORD
Log in here: BLOG_URLwp-login.php

We hope you enjoy your new site. Thanks!

--The Team @ SITE_NAME' );

			$this->options = array(
			'wpmu_signup_blog_notification' => array(
				'title' => __( 'Filters the message content of the new blog notification e-mail', 'ub' ),
				'fields' => array(
					'status' => array(
						'type' => 'checkbox',
						'label' => __( 'Status', 'ub' ),
						'description' => __( 'Would you like to replace the new blog notification e-mail?', 'ub' ),
						'options' => array(
							'on' => __( 'Yes', 'ub' ),
							'off' => __( 'No', 'ub' ),
						),
						'default' => 'off',
						'classes' => array( 'switch-button' ),
						'slave-class' => 'wpmu_signup_blog_notification',
					),
					'title' => array(
						'type' => 'text',
						'label' => __( 'The title', 'ub' ),
						'classes' => array( 'large-text' ),
						'master' => 'wpmu_signup_blog_notification',
						'default' => $new_blog_title,
					),
					'message' => array(
						'type' => 'textarea',
						'label' => __( 'The message', 'ub' ),
						'classes' => array( 'large-text' ),
						'master' => 'wpmu_signup_blog_notification',
						'default' => $new_blog_message,
					),
				),
			),
			'wpmu_signup_user_notification' => array(
				'title' => __( 'Filters the content of the notification e-mail for new user sign-up', 'ub' ),
				'fields' => array(
					'status' => array(
						'type' => 'checkbox',
						'label' => __( 'Status', 'ub' ),
						'description' => __( 'Would you like to replace the new user sign-up notification e-mail?', 'ub' ),
						'options' => array(
							'on' => __( 'Yes', 'ub' ),
							'off' => __( 'No', 'ub' ),
						),
						'default' => 'off',
						'classes' => array( 'switch-button' ),
						'slave-class' => 'wpmu_signup_user_notification',
					),
					'title' => array(
						'type' => 'text',
						'label' => __( 'The title', 'ub' ),
						'classes' => array( 'large-text' ),
						'master' => 'wpmu_signup_user_notification',
						'default' => $new_sign_up_title,
					),
					'message' => array(
						'type' => 'textarea',
						'label' => __( 'The message', 'ub' ),
						'classes' => array( 'large-text' ),
						'master' => 'wpmu_signup_user_notification',
						'default' => $new_sign_up_message,
					),
				),
			),
			'wpmu_welcome_notification' => array(
				'title' => __( 'The welcome e-mail after site activation', 'ub' ),
				'fields' => array(
					'status' => array(
						'type' => 'checkbox',
						'label' => __( 'Status', 'ub' ),
						'description' => __( 'Would you like to replace the new blog notification e-mail?', 'ub' ),
						'options' => array(
							'on' => __( 'Yes', 'ub' ),
							'off' => __( 'No', 'ub' ),
						),
						'default' => 'off',
						'classes' => array( 'switch-button' ),
						'slave-class' => 'wpmu_welcome_notification',
					),
					'title' => array(
						'type' => 'text',
						'label' => __( 'The title', 'ub' ),
						'classes' => array( 'large-text' ),
						'master' => 'wpmu_welcome_notification',
						'default' => __( 'New %1$s Site: %2$s' ),
					),
					'message' => array(
						'type' => 'textarea',
						'label' => __( 'The message', 'ub' ),
						'classes' => array( 'large-text' ),
						'master' => 'wpmu_welcome_notification',
						'default' => $welcome_email,
					),
				),
			),
			);
		}
	}

	new ub_custom_ms_register_emails();
}
