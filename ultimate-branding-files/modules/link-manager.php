<?php
/*
  Plugin Name:  Link Manager
  Description: Enables the Link Manager that existed in WordPress until version 3.5.
 */

if ( ! class_exists( 'ub_link_manager' ) ) {
	class ub_link_manager extends ub_helper {
		public function __construct() {
			add_action( 'ultimatebranding_settings_link_manager', array( $this, 'admin_options_page' ) );
			add_filter( 'pre_option_link_manager_enabled', '__return_true' );
		}
		protected function set_options() {
			$description = '<ul>';
			$description .= sprintf( '<li>%s</li>', __( 'Links menu is now visble.', 'ub' ) );
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
new ub_link_manager();
