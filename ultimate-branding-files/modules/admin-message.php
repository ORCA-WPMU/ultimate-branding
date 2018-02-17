<?php
/*
Plugin Name: Admin Message
Plugin URI: https://premium.wpmudev.org/project/admin-message/
Description: Display a message in admin dashboard
Author: WPMU DEV
Version: 1.1.1.2
Tested up to: 3.2.0
Network: true
Author URI: http://premium.wpmudev.org
WDP ID: 5
Text Domain: admin_message
 */

/*
Copyright 2009-2014 Incsub (http://incsub.com)
Author - S H Mohanjith
Contributors - Andrew Billits

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

/**
 * Class UB_Admin_Message
 * Admin message sub-module
 */
class UB_Admin_Message extends ub_helper{

	protected $option_name = 'admin_message';

	public  function __construct() {
		parent::__construct();
		/**
		 * Render settings panel
		 */
		add_action( 'ultimatebranding_settings_admin_message', array( $this, 'admin_message_page_output' ) );
		/**
		 * Save settings
		 */
		add_filter( 'ultimatebranding_settings_admin_message_process', array( $this, 'admin_message_save_settings' ) );
		/**
		 * Render module's output for admin pages
		 */
		add_action( 'admin_notices', array( $this, 'admin_message_output' ) );
		/**
		 * Render module's output for network admin pages
		 */
		add_action( 'network_admin_notices', array( $this, 'admin_message_output' ) );
	}

	/**
	 * Renders panel pages content
	 *
	 * @since 1.8
	 */
	function admin_message_page_output() {

		if ( ! current_user_can( 'manage_options' ) ) {
			echo '<p>' . __( 'Nice Try...', 'ub' ) . '</p>';  //If accessed properly, this message doesn't appear.
			return;
		}

		$admin_message = ub_get_option( $this->option_name );
		if ( $admin_message == 'empty' ) {
			$admin_message = '';
		}
		ub_deprecated_module( __( 'Admin Message', 'ub' ), __( 'Admin Panel Tips', 'ub' ), 'admin-panel-tips' );
?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Message', 'ub' ) ?></th>
                <td>
                    <textarea name="admin_message" type="text" rows="5" wrap="soft" id="admin_message" style="width: 95%"/><?php echo $admin_message ?></textarea>
                    <br /><?php _e( 'HTML allowed', 'ub' ) ?></td>
            </tr>
        </table>
<?php
	}


	/**
	 * Renders the admin message
	 *
	 * @since 1.8
	 */
	function admin_message_output() {
		$admin_message = get_site_option( 'admin_message' );
		if ( ! empty( $admin_message ) && $admin_message != 'empty' ) {
?>
            <div id="message" class="updated"><p><?php echo stripslashes( $admin_message ); ?></p></div>
<?php
		}
	}

	/**
	 * Saves settings
	 *
	 * @since 1.8
	 * @return int
	 */
	function admin_message_save_settings() {

		if ( isset( $_POST['Reset'] ) ) {
			update_site_option( 'admin_message', 'empty' );
			return 2;
		} else {
			$admin_message = isset( $_POST['admin_message'] ) ? $_POST['admin_message'] : '';
			if ( $admin_message == '' ) {
				$admin_message = 'empty';
			}
			update_site_option( 'admin_message', stripslashes( $admin_message ) );
			return 1;
		}

		return 3;
	}
}

/**
 * Kick start the module
 */
new UB_Admin_Message();

