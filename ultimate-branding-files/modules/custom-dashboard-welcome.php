<?php
/*
Plugin Name: Dashboard Welcome
Description: Allow to change the dashboard welcome message
Author: Barry (Incsub), Sam Najian (Incsub)
Version: 1.2
Author URI:
Network: true

Copyright 2012-2017 Incsub (email: admin@incsub.com)

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

/**
 * Main class of Custom welcome message ( formerly hide dashboard welcome )
 * Class UB_Custom_Dashboard_Welcome
 */
class UB_Custom_Dashboard_Welcome extends ub_helper{

	/**
	 * Custom welcome message
	 *
	 * @since 1.2
	 * @var mixed|void
	 */
	private $_message;

	protected $option_name = 'ub_custom_welcome_message';

	/**
	 * Kick start the module
	 *
	 * @since 1.2
	 */
	public function __construct() {
		parent::__construct();
		$this->set_options();
		add_filter( 'get_user_metadata', array( $this, 'ub_remove_dashboard_welcome' ) , 10, 4 );
		/**
		 * new standard
		 */
		add_action( 'ultimatebranding_settings_widgets', array( $this, 'admin_options_page' ) );
		add_filter( 'ultimatebranding_settings_widgets_process', array( $this, 'update' ) );
		$this->_message = $this->_get_message();
		if ( ! empty( $this->_message ) && is_string( $this->_message ) ) {
			add_action( 'welcome_panel', array( $this, 'render_custom_message' ) );
		}
	}

	/**
	 * Retrieves custom message from db
	 *
	 * @since 1.2
	 * @return mixed|void
	 */
	private function _get_message() {
		$value = $this->get_value( 'dashboard_widget', 'text' );
		if ( empty( $value ) ) {
			$value = ub_get_option( $this->option_name );
		}
		return $value;
	}

	/**
	 * Removes default welcome message from dashboard
	 *
	 * @param $value
	 * @param $object_id
	 * @param $meta_key
	 * @param $single
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function ub_remove_dashboard_welcome( $value, $object_id, $meta_key, $single ) {
		global $wp_version;
		if ( version_compare( $wp_version, '3.5', '>=' ) ) {
			remove_action( 'welcome_panel', 'wp_welcome_panel' );
			return $value;
		} else {
			if ( $meta_key == 'show_welcome_panel' ) {
				return false;
			}
		}
		return $value;
	}

	/**
	 * Saves settings to db
	 *
	 * @since 1.2
	 * @return bool
	 */
	public function process( $status ) {
		$this->_save_message( $_POST['custom_admin_welcome_message'] );
		return $status && true;
	}

	/**
	 * Renders custom content
	 *
	 * @since 1.2
	 */
	public function render_custom_message() {
		$proceed_shortcodes = $this->get_value( 'dashboard_widget', 'shortocode' );
		$content = stripslashes( $this->_message );
		if ( 'on' == $proceed_shortcodes ) {
			$content = do_shortcode( $content );
		}
		echo wpautop( $content );
	}

	/**
	 * Set options
	 *
	 * @since 1.8.9
	 */
	protected function set_options() {
		$this->options = array(
			'dashboard_widget' => array(
				'title' => __( 'Dashboard Welcome' ),
				'fields' => array(
					'text' => array(
						'hide-th' => true,
						'type' => 'wp_editor',
						'label' => __( 'Dashboard Welcome', 'ub' ),
						'description' => __( 'Leave empty to remove custom welcome widget', 'ub' ),
						'default' => '',
						'value' => $this->_get_message(),
					),
					'shortocode' => array(
						'type' => 'checkbox',
						'label' => __( 'Shortcodes', 'ub' ),
						'description' => __( 'Be careful it can break compatibility with themes with UI builders.', 'ub' ),
						'options' => array(
							'on' => __( 'Parse shortocodes', 'ub' ),
							'off' => __( 'Stop parsing', 'ub' ),
						),
						'default' => 'off',
						'classes' => array( 'switch-button' ),
					),
				),
			),
		);
	}
}

new UB_Custom_Dashboard_Welcome();