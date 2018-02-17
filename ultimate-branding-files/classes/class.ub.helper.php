<?php
/*
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

if ( ! class_exists( 'ub_helper' ) ) {

	class ub_helper{
		protected $options;
		protected $data = null;
		protected $option_name;
		protected $url;
		protected $build;
		protected $tab_name;

		/**
		 * Module name
		 *
		 * @since 1.9.4
		 */
		protected $module = 'ub_helper';

		public function __construct() {
			if ( empty( $this->build ) ) {
				global $ub_version;
				$this->build = $ub_version;
			}
			if ( is_admin() ) {
				global $uba;
				$params = array(
					'page' => 'branding',
				);
				if ( is_a( $uba, 'UltimateBrandingAdmin' ) ) {
					$params['tab'] = $uba->get_current_tab();
				}
				$this->url = add_query_arg(
					$params,
					is_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' )
				);
			}
			add_filter( 'ultimate_branding_options_names', array( $this, 'add_option_name' ) );
			add_filter( 'ultimate_branding_get_option_name', array( $this, 'get_module_option_name' ), 10, 2 );
		}

		public function add_option_name( $options ) {
			if ( ! in_array( $this->option_name, $options ) ) {
				$options[] = $this->option_name;
			}
			return $options;
		}

		/**
		 * @since 1.9.1 added parameter $default
		 *
		 * @param mixed $default default value return if we do not have any.
		 */
		protected function get_value( $section = null, $name = null, $default = null ) {
			$this->set_data();
			$value = $this->data;
			if ( null == $section ) {
				return $value;;
			}
			if ( null == $name && isset( $value[ $section ] ) ) {
				return $value[ $section ];
			}
			if ( empty( $value ) ) {
				/**
				 * If default is empty, then try to return default defined by
				 * configuration.
				 *
				 * @since 1.9.5
				 */
				if (
					empty( $default )
					&& isset( $this->options )
					&& isset( $this->options[ $section ] )
					&& isset( $this->options[ $section ]['fields'] )
					&& isset( $this->options[ $section ]['fields'][ $name ] )
					&& isset( $this->options[ $section ]['fields'][ $name ]['default'] )
				) {
					$default = $this->options[ $section ]['fields'][ $name ]['default'];
				}
				return $default;
			}
			if ( isset( $value[ $section ] ) ) {
				if ( empty( $name ) ) {
					return $value[ $section ];
				} else if ( isset( $value[ $section ][ $name ] )
				) {
					if ( is_string( $value[ $section ][ $name ] ) ) {
						return stripslashes( $value[ $section ][ $name ] );
					}
					return $value[ $section ][ $name ];
				}
			}
			return $default;
		}

		public function admin_options_page() {
			$this->set_options();
			$this->set_data();
			$simple_options = new simple_options();
			do_action( 'ub_helper_admin_options_page_before_options', $this->option_name );
			echo $simple_options->build_options( $this->options, $this->data );
		}

		protected function set_data() {
			if ( null === $this->data ) {
				$value = ub_get_option( $this->option_name );
				if ( 'empty' !== $value ) {
					$this->data = $value;
				}
			}
		}

		/**
		 * Update settings
		 *
		 * @since 1.8.6
		 */
		public function update( $status ) {
			$value = $_POST['simple_options'];
			if ( $value == '' ) {
				$value = 'empty';
			}
			foreach ( $this->options as $section_key => $section_data ) {
				if ( ! isset( $section_data['fields'] ) ) {
					continue;
				}
				foreach ( $section_data['fields'] as $key => $data ) {
					if ( ! isset( $data['type'] ) ) {
						$data['type'] = 'text';
					}
					switch ( $data['type'] ) {
						case 'media':
							if ( isset( $value[ $section_key ][ $key ] ) ) {
								$image = wp_get_attachment_image_src( $value[ $section_key ][ $key ], 'full' );
								if ( false !== $image ) {
									$value[ $section_key ][ $key.'_meta' ] = $image;
								}
							}
						break;
						case 'checkbox':
							if ( isset( $value[ $section_key ][ $key ] ) ) {
								$value[ $section_key ][ $key ] = 'on';
							} else {
								$value[ $section_key ][ $key ] = 'off';
							}
							break;
							/**
							 * save extra data if field is a wp_editor
							 */
						case 'wp_editor':
							$value[ $section_key ][ $key.'_meta' ] = do_shortcode( $value[ $section_key ][ $key ] );
							break;
					}
				}
			}
			return $this->update_value( $value );
		}

		/**
		 * Update whole value
		 *
		 * @since 1.9.5
		 */
		protected function update_value( $value ) {
			ub_update_option( $this->option_name , $value );
			return true;
		}

		/**
		 * get base url
		 *
		 * @since 1.8.9
		 */
		protected function get_base_url() {
			$url = '';
			if ( ! is_admin() ) {
				return $url;
			}
			$screen = get_current_screen();
			if ( ! is_object( $screen ) ) {
				return $url;
			}
			$args = array(
				'page' => $screen->parent_base,
			);
			if ( isset( $_REQUEST['tab'] ) ) {
				$args['tab'] = $_REQUEST['tab'];
			}
			if ( is_network_admin() ) {
				$url = add_query_arg( $args, network_admin_url( 'admin.php' ) );
			} else {
				$url = add_query_arg( $args, admin_url( 'admin.php' ) );
			}
			return $url;
		}

		/**
		 * Admin notice wrapper
		 *
		 * @since 1.8.9
		 */
		protected function notice( $message, $class = 'info' ) {
			$allowed = array( 'error', 'warning', 'success', 'info' );
			if ( in_array( $class, $allowed ) ) {
				$class = 'notice-'.$class;
			} else {
				$class = '';
			}
			printf(
				'<div class="notice %s"><p>%s</p></div>',
				esc_attr( $class ),
				$message
			);
		}

		/**
		 * Handle filter for option name, it should be overwrite by module
		 * method.
		 *
		 * @since 1.9.2
		 */
		public function get_module_option_name( $option_name, $module ) {
			return $option_name;
		}

		/**
		 * Remove "Save Changes" button from page.
		 *
		 * @since 1.9.2
		 */
		public function disable_save() {
			add_filter( 'ultimatebranding_settings_panel_show_submit', '__return_false' );
		}

		/**
		 * get nonce action
		 *
		 * @since 1.9.4
		 *
		 * @param string $name nonce name
		 * @param integer $user_id User ID.
		 * @return nonce action name
		 */
		protected function get_nonce_action_name( $name = 'default', $user_id = 0 ) {
			if ( 0 === $user_id ) {
				$user_id = get_current_user_id();
			}
			$nonce_action = sprintf(
				'%s_%s_%d',
				__CLASS__,
				$name,
				$user_id
			);
			return $nonce_action;
		}
	}
}
