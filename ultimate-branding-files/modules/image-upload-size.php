<?php
/*
  Plugin Name: Image Upload Size
  Plugin URI: http://premium.wpmudev.org/project/ultimate-branding
  Description: Allows you to limit the filesize of uploaded images
  Author: Vaughan (Incsub)
  Version: 1.0
  Network: true
  WDP ID: 169
 */

/*
  Copyright 2007-2017 Incsub (http://incsub.com)

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

if ( ! class_exists( 'ub_Image_Upload_Size' ) ) {
	class ub_Image_Upload_Size extends ub_helper {

		private $roles = array();
		private $filesize = array();

		public function __construct() {
			$this->set_options();
			// Admin interface
			add_action( 'ultimatebranding_settings_images', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_images_process', array( $this, 'update' ) );

			// Hooking into upload prefilter to validate the uploaded image file.
			add_filter( 'upload_size_limit', array( $this, 'upload_size_limit' ), 10, 3 );
			add_filter( 'ultimatebranding_reset_section', array( $this, 'reset' ), 10, 3 );

			/**
			 * export
			 */
			add_filter( 'ultimate_branding_export_data', array( $this, 'export' ) );
		}

		protected function set_options() {
			//Check for backwards compatibility
			$this->roles = wp_roles()->get_names();
			$this->options = array(
				'limit' => array(
					'title' => __( 'Set Image filesize Limit', 'ub' ) . ' - ' . __( 'Default WP upload limit: ' ) . round($this->get_wp_limit() / 1000) . __('Mb', 'ub'),
					'description' => __( 'Entering 0 will set the Default WordPress upload limit.', 'ub' ),
					'fields' => array(),
				),
			);
			$max = $this->get_wp_limit();
			foreach ( $this->roles as $slug => $title ) {
				$role = get_role( $slug );
				/**
				 * Do not show limit for roles without permissions to add
				 * files.
				 */
				if ( ! $role->has_cap( 'upload_files' ) ) {
					continue;
				}
				$option_name = $this->get_name( $slug );
				$this->options['limit']['fields'][ $slug ] = array(
					'type' => 'number',
					'label' => $title,
					'min' => 0,
					'max' => $max,
					'classes' => array( 'ui-slider' ),
					'after' => __( 'kB', 'ub' ),
					'value' => ub_get_option( $option_name, 0 ),
				);
			}
		}

		public function update( $status ) {
			if ( ! isset( $_POST['simple_options'] ) ) {
				return;
			}
			$value = $_POST['simple_options'];
			$max = $this->get_wp_limit();
			foreach ( $this->options as $section_key => $section_data ) {
				if ( ! isset( $section_data['fields'] ) ) {
					continue;
				}
				foreach ( $section_data['fields'] as $key => $data ) {
					$v = 0;
					if ( isset( $value[ $section_key ][ $key ] ) ) {
						$v = filter_var( $value[ $section_key ][ $key ], FILTER_SANITIZE_NUMBER_INT );
						$option_name = $this->get_name( $this->roles[ $key ] );
					}
					if ( empty( $v ) || $max == $v ) {
						ub_delete_option( $option_name );
					} else {
						ub_update_option( $option_name, $v );
					}
				}
			}
		}

		public function get_fs_limit() {
			$limit = $role_limit = 0;
			$current_user = wp_get_current_user();
			foreach ( $this->roles as $role ) {
				$role = strtolower( $role );
				if ( in_array( $role, $current_user->roles ) ) {
					$option_name = $this->get_name( $role );
					$value = ub_get_option( $option_name, false );
					if ( ! empty( $value ) && 0 !== $value ) {
						$role_limit = $value;
						if ( $role_limit > $limit ) {
							$limit = $role_limit;
						}
					}
				}
			}
			if ( 0 === $role_limit ) {
				return $this->get_wp_limit();
			}
			return $limit;
		}

		public function get_wp_limit() {
			remove_filter( 'upload_size_limit', array( $this, 'upload_size_limit' ), 10, 3 );
			$size = round( wp_max_upload_size() );
			$size = round( $size / MB_IN_BYTES ) * KB_IN_BYTES; // convert to kb
			add_filter( 'upload_size_limit', array( $this, 'upload_size_limit' ), 10, 3 );
			return $size;
		}

		/**
		 * Export data.
		 *
		 * @since 1.9.2
		 */
		public function export( $data ) {
			$options = array();
			foreach ( $this->roles as $slug => $title ) {
				$options[ $slug ] = $this->get_name( $slug );
			}
			foreach ( $options as $key => $val ) {
				$data['modules'][ $val ] = ub_get_option( $val );
			}
			return $data;
		}

		/**
		 * @since 1.9.2
		 */
		public function upload_size_limit( $size, $u_bytes, $p_bytes ) {
			$limit = $this->get_fs_limit();
			if ( ! empty( $limit ) ) {
				return KB_IN_BYTES * $limit;
			}
			return $size;
		}

		/**
		 * @since 1.9.2
		 */
		public function reset() {
			foreach ( $this->roles as $slug => $title ) {
				$option_name = $this->get_name( $slug );
				ub_delete_option( $option_name );
			}
			return true;
		}

		private function get_name( $sufix ) {
			return sprintf( 'ub_img_upload_filesize_%s', $sufix );
		}
	}


}

$ub_Image_Upload_Size = new ub_Image_Upload_Size();