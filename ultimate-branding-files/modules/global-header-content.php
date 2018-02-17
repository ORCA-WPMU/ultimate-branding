<?php
/*
Plugin Name: Global Header Content
Description: Simply insert any code that you like into the header of every blog
Author: Marko Miljus (Incsub)
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


class ub_global_header_content extends ub_helper {

	var $global_header_content_settings_page;
	var $global_header_content_settings_page_long;
	protected $option_name = 'global_header_content';

	function __construct() {
		parent::__construct();
		add_action( 'ultimatebranding_settings_header', array( $this, 'global_header_content_site_admin_options' ) );
		add_filter( 'ultimatebranding_settings_header_process', array( $this, 'update_global_header_options' ), 10, 1 );
		add_action( 'wp_footer', array( $this, 'global_header_content_output' ) );
	}

	function update_global_header_options( $status ) {

		$global_header_content = $_POST[ $this->option_name ];
		if ( $global_header_content == '' ) {
			$global_header_content = 'empty';
		}

		ub_update_option( $this->option_name , $global_header_content );

		if ( $status === false ) {
			return $status;
		} else {
			return true;
		}
	}

	function global_header_content_output() {
		$global_header_content = ub_get_option( $this->option_name );
		if ( $global_header_content == 'empty' ) {
			$global_header_content = '';
		}
		if ( empty( $global_header_content ) ) {
			return;
		}
?>
        <script type="text/javascript">
        var node = document.createElement("div");
        var att = document.createAttribute("id");
        att.value = "ub_global_header_content";
        node.setAttributeNode(att);
        node.innerHTML = <?php echo json_encode( stripslashes( do_shortcode( $global_header_content ) ) ); ?>;
        document.getElementsByTagName("body")[0].insertBefore(node,document.getElementsByTagName("body")[0].firstChild);
        </script>
<?php
	}

	function global_header_content_site_admin_options() {

		global $wpdb, $wp_roles, $current_user, $global_header_content_settings_page;

		$global_header_content = ub_get_option( $this->option_name );
		if ( $global_header_content == 'empty' ) {
			$global_header_content = '';
		}

?>
            <div class="postbox">
            <h3 class="hndle" style='cursor:auto;'><span><?php _e( 'Global Header Content', 'ub' ) ?></span></h3>
            <div class="inside">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Header Content', 'ub' ) ?></th>
                        <td>
<?php
		$args = array( 'textarea_name' => $this->option_name, 'textarea_rows' => 5 );
		wp_editor( stripslashes( $global_header_content ), $this->option_name, $args );
?>
                            <br />
                            <?php _e( 'What is added here will be shown on every blog or site in your network. You can add tracking code, embeds, etc.', 'ub' ) ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
<?php
	}
}

$ub_globalheadertext = new ub_global_header_content();

