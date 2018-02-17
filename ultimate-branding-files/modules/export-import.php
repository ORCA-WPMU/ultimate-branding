<?php
/*
Plugin Name: Export & Import
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

if ( ! class_exists( 'ub_export_import' ) ) {

	class ub_export_import extends ub_helper {

		public function __construct() {
			add_action( 'ultimatebranding_settings_export_import', array( $this, 'admin_options_page' ) );
			add_action( 'ultimatebranding_settings_export_import', array( $this, 'disable_save' ) );
			add_action( 'ultimatebranding_settings_export_import_process', array( $this, 'update' ) );
			add_filter( 'ultimatebranding_settings_export_import_messages', array( $this, 'import_messages' ) );
		}

		/**
		 * Handle form send
		 *
		 * @since 2.8.6
		 */
		public function update( $status ) {
			if ( ! isset( $_REQUEST['simple_options'] ) ) {
				return;
			}
			/**
			 * export
			 */
			if (
				isset( $_REQUEST['simple_options']['export'] )
				&& isset( $_REQUEST['simple_options']['export']['button'] )
			) {
				$this->export();
			}
			/**
			 * import
			 */
			if (
				isset( $_REQUEST['simple_options']['import'] )
				&& isset( $_REQUEST['simple_options']['import']['button'] )
				&& isset( $_FILES['import'] )
			) {
				$file = $_FILES['import'];
				if ( ! empty( $file['error'] ) ) {
					return;
				}
				if ( ! preg_match( '/json$/i', $file['name'] ) ) {
					return;
				}
				$import = wp_import_handle_upload();
				$import_id = $import['id'];
				$filename = $import['file'];
				$file_content = file_get_contents( $filename );
				$options = json_decode( $file_content, true );
				if ( ! is_array( $options ) ) {
					return;
				}
				if ( isset( $options['activate_module'] ) ) {
					update_ub_activated_modules( $options['activate_module'] );
				}
				if ( isset( $options['modules'] ) ) {
					foreach ( $options['modules'] as $meta_key => $meta_value ) {
						ub_update_option( $meta_key, $meta_value );
					}
					/**
					 * Action allow to handle custom import data.
					 *
					 * @since 1.9.2
					 */
					do_action( 'ultimate_branding_import', $options['modules'] );
				}
				return true;
			}
		}

		/**
		 * Prepare export file
		 *
		 * @since 2.8.6
		 */
		private function export() {
			global $ub_version;
			$options_names = apply_filters( 'ultimate_branding_options_names', array() );
			$data = array(
				'version' => $ub_version,
				'activate_module' => get_ub_activated_modules(),
				'modules' => array(),
			);
			$data = apply_filters( 'ultimate_branding_export_data', $data );
			foreach ( $options_names as $name ) {
				$data['modules'][ $name ] = ub_get_option( $name );
			}
			$sitename = sanitize_key( get_bloginfo( 'name' ) );
			if ( empty( $sitename ) ) {
				$sitename = 'website';
			}
			/**
			 * add debug information
			 *
			 * @since 1.8.7
			 */
			if (
				isset( $_POST['simple_options'] )
				&& isset( $_POST['simple_options']['export'] )
				&& isset( $_POST['simple_options']['export']['debug'] )
			) {
				$data['debug'] = array(
					'plugins' => get_plugins(),
					'themes' => get_themes(),
				);
			}
			/**
			 * filename
			 */
			$wp_filename = sprintf( '%s.ultimate_branding.%s.json', $sitename, date( 'Y-m-d' ) );
			/**
			 * send it to browser
			 */
			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $wp_filename );
			header( 'Content-Type: text/json; charset=' . get_option( 'blog_charset' ), true );
			/**
			 * Check PHP version, for PHP < 3 do not add options
			 */
			$version = phpversion();
			$compare = version_compare( $version, '5.3', '<' );
			if ( $compare ) {
				echo json_encode( $data );
				exit;
			}
			$option = defined( 'JSON_PRETTY_PRINT' )? JSON_PRETTY_PRINT : null;
			echo json_encode( $data, $option );
			exit;
		}

		/**
		 * Build form with options.
		 *
		 * @since 2.8.6
		 */
		protected function set_options() {
			$this->options = array(
			'import' => array(
				'title' => __( 'Import Configuration', 'ub' ),
				'hide-reset' => true,
				'hide-th' => true,
				'fields' => array(
					'desc' => array(
						'type' => 'description',
						'value' => $this->greet_import(),
					),
					'file' => array(
						'type' => 'file',
						'name' => 'import',
					),
					'button' => array(
						'type' => 'submit',
						'value' => __( 'Upload file and import', 'ub' ),
						'classes' => array( 'button-primary' ),
						'disabled' => true,
					),
				),
			),
			'export' => array(
				'title' => __( 'Export Configuration', 'ub' ),
				'hide-reset' => true,
				'hide-th' => true,
				'fields' => array(
					'desc' => array(
						'type' => 'description',
						'value' => $this->greet_export(),
					),
					'add_debug_information' => array(
						'type' => 'checkbox',
						'checkbox_label' => __( 'Add debug information', 'ub' ),
						'description' => __( 'Check this to allow export debug information about installed themes and plugins.', 'ub' ),
					),
					'button' => array(
						'type' => 'submit',
						'value' => __( 'Export', 'ub' ),
						'classes' => array( 'button-primary' ),
					),
				),
			),
			);
		}

		/**
		 * helper with help text.
		 *
		 * since 1.8.6
		 */
		private function greet_import() {
			$content = '';
			$content .= '<p>'.__( 'Howdy! Upload your JSON file and we&#8217;ll import Ultimate Branding configuration.', 'ub' ).'</p>';
			$content .= '<p>'.__( 'Choose a JSON (.json) file to upload, then click Upload file and import.', 'ub' ).'</p>';
			return $content;
		}

		/**
		 * helper with help text.
		 *
		 * since 1.8.6
		 */
		private function greet_export() {
			$content = '';
			$content .= '<p>'. __( 'When you click the button below Ultimate Branding will create an JSON file for you to save to your computer.', 'ub' ) .'</p>';
			$content .= '<p>'. __( 'This format will contain your Ultimate Branding settings.' ) .'</p>';
			$content .= '<p>'. __( 'Once you&#8217;ve saved the download file, you can use the Import function in another WordPress installation to import the configuration from this site.' ) .'</p>';
			return $content;
		}

		public function import_messages( $messages ) {
			$messages[1] = __( 'Ultimate Branding settings was imported successfully.', 'ub' );
			$messages[2] = __( 'There was an error during import settings, please try again.', 'ub' );
			return $messages;
		}
	}

	new ub_export_import();
}
