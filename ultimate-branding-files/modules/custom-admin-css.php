<?php
/*
Plugin Name: Admin CSS
Description: Add extra CSS to the admin panel
Author: Barry (Incsub)
Version: 1.0.1
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

class ub_custom_admin_css extends ub_helper {

	protected $option_name = 'global_admin_css';

	function __construct() {
		parent::__construct();
		add_action( 'ultimatebranding_settings_css', array( &$this, 'custom_admin_css_options' ) );
		add_filter( 'ultimatebranding_settings_css_process', array( &$this, 'update_custom_admin_css' ), 10, 1 );

		add_action( 'admin_head', array( &$this, 'custom_admin_css_output' ), 99 );
	}

	function update_custom_admin_css( $status ) {

		$admincss = $_POST['admincss'];
		if ( $admincss == '' ) {
			$admincss = 'empty';
		}

		ub_update_option( $this->option_name, $admincss );

		if ( $status === false ) {
			return $status;
		} else {
			return true;
		}
	}

	function custom_admin_css_output() {
		$admincss = ub_get_option( $this->option_name );
		if ( $admincss == 'empty' ) {
			$admincss = '';
		}
		if ( ! empty( $admincss ) ) {
?>
            <style type="text/css">
                <?php echo stripslashes( $admincss ); ?>
            </style>
<?php
		}
	}

	function custom_admin_css_options() {

		global $wpdb, $wp_roles, $current_user, $global_footer_content_settings_page;
		$admincss = ub_get_option( $this->option_name );
		if ( $admincss == 'empty' ) {
			$admincss = '';
		}

?>
            <div class="postbox">
            <h3 class="hndle" style='cursor:auto;'><span><?php _e( 'Admin CSS', 'ub' ) ?></span></h3>
            <div class="inside">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e( 'CSS Styles', 'ub' ) ?></th>
                        <td>
                            <textarea name='admincss' id="ub_admincss" style='display: none'><?php echo stripslashes( $admincss );  ?></textarea>
                            <div class="ub_css_editor" id="ub_admincss_editor" data-input="#ub_admincss" style='width:100%; height: 20em;'><?php echo stripslashes( $admincss );  ?></div>
                            <br />
                            <?php _e( 'What is added here will be added to the header of every admin page for every site.', 'ub' ) ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
<?php
	}
}

$ub_custom_admin_css = new ub_custom_admin_css();

?>