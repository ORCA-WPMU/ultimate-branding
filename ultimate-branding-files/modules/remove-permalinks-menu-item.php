<?php
/*
Plugin Name: Remove Permalinks Menu Item
Description: Removes the 'permalinks' configuration options
 */
if ( ! class_exists( 'ub_menu_perlmalinks' ) ) {
	class ub_menu_perlmalinks extends ub_helper {
		public function __construct() {
			add_action( 'ultimatebranding_settings_permalinks', array( $this, 'admin_options_page' ) );
			add_action( 'admin_menu', array( $this, 'remove_permalinks_menu_item' ) );
		}
		public function remove_permalinks_menu_item() {
			global $submenu;
			/**
			 * Check parent menu
			 */
			if ( ! isset( $submenu['options-general.php'] ) || ! is_array( $submenu['options-general.php'] ) ) {
				return;
			}

			foreach ( $submenu['options-general.php'] as $key => $data ) {
				if ( 'options-permalink.php' == $data[2] ) {
					unset( $submenu['options-general.php'][ $key ] );
					return;
				}
			}
		}
		protected function set_options() {
			$description = '<ul>';
			$description .= sprintf( '<li>%s</li>', __( 'The Permalinks menu item is hidden.', 'ub' ) );
			$description .= sprintf( '<li>%s</li>', __( 'This module has no configuration.', 'ub' ) );
			$description .= '</ul>';
			$this->options = array(
				'description' => array(
					'title' => __( 'Description', 'ub' ),
					'description' => $description,
				),
			);
		}
	}
}
new ub_menu_perlmalinks();