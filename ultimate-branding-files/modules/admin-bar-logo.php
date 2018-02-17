<?php
/*
Plugin Name: Admin Bar Logo
Plugin URI:
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

if ( ! class_exists( 'ub_admin_bar_logo' ) ) {

	class ub_admin_bar_logo extends ub_helper {

		protected $option_name = 'admin_bar_logo';

		public function __construct() {
			parent::__construct();
			$this->set_options();
			add_action( 'ultimatebranding_settings_adminbar', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_adminbar_process', array( $this, 'update' ), 10, 1 );
			add_action( 'admin_print_styles', array( $this, 'output' ) );
			add_action( 'wp_head', array( $this, 'output' ) );
		}

		/**
		 * set options
		 *
		 * @since x.x.x
		 */
		protected function set_options() {
			$this->options = array(
				'admin_bar_logo' => array(
					'title' => __( 'Admin Bar Logo', 'ub' ),
					'hide-reset' => true,
					'fields' => array(
						'logo_upload' => array(
							'type' => 'media',
							'label' => __( 'Logo image', 'ub' ),
							'description' => __( 'Upload your own logo.', 'ub' ),
						),
					),
				),
			);
		}

		/**
		 * output
		 *
		 * @since x.x.x
		 */
		public function output() {
			$value = ub_get_option( $this->option_name );
			if ( $value == 'empty' ) {
				$value = '';
			}
			if ( empty( $value ) ) {
				return;
			}
			printf( '<style type="text/css" id="%s">', esc_attr( __CLASS__ ) );
			/**
			 * Logo
			 */
			if ( isset( $value['admin_bar_logo'] ) ) {
				$v = $value['admin_bar_logo'];
				if ( isset( $v['logo_upload_meta'] ) ) {
					$src = $v['logo_upload_meta'][0];
?>
body #wpadminbar #wp-admin-bar-wp-logo > .ab-item {
    background-image: url(<?php echo $src; ?>);
    background-repeat: no-repeat;
    background-position: 50%;
    background-size: 80%;
}
body #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
    content: " ";
}
<?php
				}
			}
			echo '</style>';
		}
	}

}

new ub_admin_bar_logo();
