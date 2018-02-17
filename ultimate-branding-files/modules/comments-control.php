<?php
/*
Plugin Name: Comments Control
Description: Fine tune comment throttling
 */

/*
Copyright 2007-2017 Incsub, (http://incsub.com)
Author - S H Mohanjith (Incsub), Marcin Pietrzak (Incsub)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */


if ( ! class_exists( 'ub_comments_control' ) ) {

	class ub_comments_control extends ub_helper {

		protected $option_name = 'ub_comments_control';

		public function __construct() {
			parent::__construct();
			$this->set_options();
			add_filter( 'comment_flood_filter', array( $this, 'limit_comments_flood_filter' ), 10, 3 );
			add_action( 'ultimatebranding_settings_comments_control', array( $this, 'admin_options_page' ) );
			add_action( 'ultimatebranding_settings_comments_control_process', array( $this, 'update' ) );
			$this->upgrade();
		}

		public function upgrade() {
			$value = ub_get_option( $this->option_name );
			if ( empty( $value ) ) {
				/**
				 * migrate data from plugin Comments Control
				 * https://premium.wpmudev.org/project/comments-control/
				 */
				$value['rules']['whitelist'] = ub_get_option( 'limit_comments_allowed_ips' );
				$value['rules']['blacklist'] = ub_get_option( 'limit_comments_denied_ips' );
				ub_update_option( $this->option_name, $value );
				ub_delete_option( 'limit_comments_allowed_ips' );
				ub_delete_option( 'limit_comments_denied_ips' );
			}
		}

		public function limit_comments_flood_filter( $flood_die, $time_lastcomment, $time_newcomment ) {
			global $user_id;
			if ( intval( $user_id ) > 0 ) {
				return false;
			}
			/**
			 * get settings
			 */
			$value = ub_get_option( $this->option_name, 'rules' );
			$whitelist = $blacklist = '';
			if ( isset( $value['rules'] ) ) {
				if ( isset( $value['rules']['whitelist'] ) ) {
					$whitelist = $value['rules']['whitelist'];
				}
				if ( isset( $value['rules']['blacklist'] ) ) {
					$blacklist = $value['rules']['blacklist'];
				}
			}
			if ( trim( $whitelist ) != '' || trim( $blacklist ) != '' ) {
				$_remote_addr = isset( $_SERVER['HTTP_X_FORWARDED_FOR'] )?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
				$_remote_addr = preg_replace( '/\./', '\.', $_remote_addr );
				if ( preg_match( '/'.$_remote_addr.'/i', $whitelist ) > 0 ) {
					return false;
				}
				if ( preg_match( '/'.$_remote_addr.'/i', $blacklist ) > 0 ) {
					return true;
				}
			}
			return $flood_die;
		}

		/**
		 * Set options Configuration
		 *
		 * @since 1.8.6
		 */
		protected function set_options() {
			$this->options = array(
				'rules' => array(
					'title' => __( 'Allowed rules apply before denied rules', 'ub' ),
					'hide-reset' => true,
					'fields' => array(
						'whitelist' => array(
							'type' => 'textarea',
							'label' => __( 'IP whitelist', 'ub' ),
							'description' => __( 'IPs for which comments will not be throttled. One IP per line or comma separated.', 'ub' ),
							'classes' => array( 'large-text' ),
						),
						'blacklist' => array(
							'type' => 'textarea',
							'label' => __( 'IP blacklist', 'ub' ),
							'description' => __( 'IPs for which comments will be throttled. One IP per line or comma separated.', 'ub' ),
							'classes' => array( 'large-text' ),
						),
					),
				),
			);
		}
	}
}

new ub_comments_control();