<?php
/*
  Plugin Name: Login Image
  Plugin URI: http://premium.wpmudev.org/project/login-image
  Description: Allows you to change the login image
  Author: Marko Miljus (Incsub), Andrew Billits, Ulrich Sossou (Incsub)
  Version: 2.1.1
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

class ub_Login_Image {

	function __construct() {
		global $wp_version;

		//Check for backwards compatibility

		$uploaddir = ub_wp_upload_dir();
		$login_image_old = ub_get_option( 'ub_login_image_url', false );
		$login_image = ub_get_option( 'ub_login_image', false );

		if ( ! isset( $login_image_old ) || $login_image_old == '' ) {
			//there is no any old record
			if ( ! $login_image ) {//add default image if not exists
				$response = wp_remote_head( admin_url() . 'images/wordpress-logo.svg' );
				if ( ! is_wp_error( $response ) && ! empty( $response['response']['code'] ) && $response['response']['code'] == '200' ) {//support for 3.8+
					ub_update_option( 'ub_login_image', admin_url() . 'images/wordpress-logo.svg' );
				} else {
					ub_update_option( 'ub_login_image', admin_url() . 'images/wordpress-logo.png' ); // for older version
				}
			}
		} else { //there IS an OLD RECORD
			ub_update_option( 'ub_login_image', $login_image_old ); //we will assume that file is in place
			ub_update_option( 'ub_login_image_url', '' );
		}

		// Admin interface

		add_action( 'ultimatebranding_settings_images', array( &$this, 'manage_output' ) );
		add_filter( 'ultimatebranding_settings_images_process', array( &$this, 'process' ) );

		// Login interface
		add_action( 'login_head', array( &$this, 'stylesheet' ), 999 );

		if ( ! is_multisite() ) {
			add_filter( 'login_headerurl', array( &$this, 'home_url' ) );
		}
		/**
		 * export
		 */
		add_filter( 'ultimate_branding_export_data', array( $this, 'export' ) );
	}

	/**
	 * Add site admin page
	 * */
	function change_wp_login_title() {
		return esc_attr( bloginfo( 'name' ) );
	}

	function home_url() {
		return home_url();
	}

	function stylesheet() {
		global $current_site;

		$login_image_old = ub_get_option( 'ub_login_image_url', false );
		$login_image_id = ub_get_option( 'ub_login_image_id', false );
		$login_image_size = ub_get_option( 'ub_login_image_size', false );
		$login_image_width = ub_get_option( 'ub_login_image_width', 64 );
		$login_image_height = ub_get_option( 'ub_login_image_height', 64 );
		$login_image = ub_get_option( 'ub_login_image', false );

		$login_image_width = 0 === intval( $login_image_width ) ? 64 : $login_image_width;
		$login_image_height = 0 === intval( $login_image_height ) ? 64 : $login_image_height;

		if ( isset( $login_image_old ) && trim( $login_image_old ) !== '' ) {
			$login_image = $login_image_old;
		} else {
			if ( $login_image_id ) {
				if ( is_multisite() && function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'ultimate-branding/ultimate-branding.php' ) ) {
					switch_to_blog( 1 );
					$login_image_src = wp_get_attachment_image_src( $login_image_id, $login_image_size, $icon = false );
					restore_current_blog();
				} else {
					$login_image_src = wp_get_attachment_image_src( $login_image_id, $login_image_size, $icon = false );
				}

				$login_image = $login_image_src[0];
				$width = ! $login_image_width ?  $login_image_src[1] : $login_image_width;
				$height = ! $login_image_height ?  $login_image_src[2] : $login_image_height;

			} else if ( $login_image ) {
				if ( ! $login_image_width || ! $login_image_height ) {
					try {
						list($width, $height) = getimagesize( set_url_scheme( $this->get_absolute_url( $login_image ), is_ssl() ? 'https' : 'http' ) );
						if ( $width ) {
							ub_update_option( 'ub_login_image_width', $width ); }

						if ( $height ) {
							ub_update_option( 'ub_login_image_height', $height ); }
					} catch (Exception $e) {

					}
				}
			} else {
				$response = wp_remote_head( admin_url() . 'images/wordpress-logo.svg' );

				if ( ! is_wp_error( $response ) && ! empty( $response['response']['code'] ) && $response['response']['code'] == '200' ) {//support for 3.8+
					$login_image = admin_url() . 'images/wordpress-logo.svg';
				} else {
					$login_image = admin_url() . 'images/wordpress-logo.png';
				}
			}
		}

		$login_image = ub_get_url_valid_shema( $login_image );

		$width = empty( $width ) ? $login_image_width : $width;
		$height = empty( $height ) ? $login_image_height : $height;

		$width = empty( $width ) ? '100%' : $width . 'px';
		$height = empty( $height ) ? '100%' : $height . 'px';

?>
        <style type="text/css">
            .login h1 a {
                background-image: url("<?php echo $login_image; ?>");
                background-size: <?php echo $width; ?>  <?php echo $height; ?>;
                background-position: center top;
                background-repeat: no-repeat;
                color: rgb(153, 153, 153);
                height: <?php echo $height; ?>;
                font-size: 20px;
                font-weight: 400;
                line-height: 1.3em;
                margin: 0px auto 25px;
                padding: 0px;
                text-decoration: none;
                width: <?php echo $width; ?>;
                text-indent: -9999px;
                outline: 0px none;
                overflow: hidden;
                display: block;
            }
        </style>
<?php
	}

	function process() {
		global $plugin_page;

		if ( isset( $_GET['reset'] ) && isset( $_GET['page'] ) && $_GET['page'] == 'branding' ) {
			//login_image_save
			ub_delete_option( 'ub_login_image' );
			ub_delete_option( 'ub_login_image_id' );
			ub_delete_option( 'ub_login_image_size' );
			ub_delete_option( 'ub_login_image_width' );
			ub_delete_option( 'ub_login_image_height' );

			$uploaddir = ub_wp_upload_dir();
			$uploadurl = ub_wp_upload_url();

			$response = wp_remote_head( admin_url() . 'images/wordpress-logo.svg' );

			if ( ! is_wp_error( $response ) && ! empty( $response['response']['code'] ) && $response['response']['code'] == '200' ) {//support for 3.8+
				ub_update_option( 'ub_login_image', admin_url() . 'images/wordpress-logo.svg' );
			} else {
				ub_update_option( 'ub_login_image', admin_url() . 'images/wordpress-logo.png' );
			}

			wp_redirect( 'admin.php?page=branding&tab=images' );
		} elseif ( isset( $_POST['wp_login_image'] ) ) {

			ub_update_option( 'ub_login_image', filter_input( INPUT_POST,  'wp_login_image', FILTER_SANITIZE_STRING ) );
			ub_update_option( 'ub_login_image_id', filter_input( INPUT_POST,  'wp_login_image_id', FILTER_SANITIZE_NUMBER_INT ) );
			ub_update_option( 'ub_login_image_size', filter_input( INPUT_POST,  'wp_login_image_size', FILTER_SANITIZE_STRING ) );
			ub_update_option( 'ub_login_image_width', filter_input( INPUT_POST,  'wp_login_image_width', FILTER_SANITIZE_NUMBER_FLOAT ) );
			ub_update_option( 'ub_login_image_height', filter_input( INPUT_POST, 'wp_login_image_height', FILTER_SANITIZE_NUMBER_FLOAT ) );
		}

		return true;
	}

	function manage_output() {
		global $wpdb, $current_site, $page;

		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_media();
		wp_enqueue_script( 'media-upload' );

		$page = $_GET['page'];

		if ( isset( $_GET['error'] ) ) {
			echo '<div id="message" class="error fade"><p>' . __( 'There was an error uploading the file, please try again.', 'ub' ) . '</p></div>'; } elseif ( isset( $_GET['updated'] ) ) {
			echo '<div id="message" class="updated fade"><p>' . __( 'Changes saved.', 'ub' ) . '</p></div>'; }

?>
        <div class='wrap nosubsub'>
            <h2><?php _e( 'Login Image', 'ub' ) ?></h2>
            <!--<form name="login_image_form" id="login_image_form" method="post">-->
<?php ub_deprecated_module( __( 'Login Image', 'ub' ), __( 'Login Screen', 'ub' ), 'login-screen' ); ?>
            <div class="postbox">
                <div class="inside">
                    <p class='description'><?php _e( 'This is the image that is displayed on the login page (wp-login.php) - ', 'ub' ); ?>
                        <a href='<?php echo wp_nonce_url( 'admin.php?page=' . $page . '&amp;tab=images&amp;reset=yes&amp;action=process', 'ultimatebranding_settings_menu_images' ) ?>'><?php _e( 'Reset the image', 'ub' ) ?></a>
                    </p>
<?php
			$login_image_old = ub_get_option( 'ub_login_image_url', false );
			$login_image_id = ub_get_option( 'ub_login_image_id', false );
			$login_image_size = ub_get_option( 'ub_login_image_size', false );
			$login_image_width = ub_get_option( 'ub_login_image_width', false );
			$login_image_height = ub_get_option( 'ub_login_image_height', false );
			$login_image = ub_get_option( 'ub_login_image', false );

			$login_image_width = 0 === intval( $login_image_width ) ? 64 : $login_image_width;
			$login_image_height = 0 === intval( $login_image_height ) ? 64 : $login_image_height;

if ( isset( $login_image_old ) && trim( $login_image_old ) !== '' ) {
	$login_image = $login_image_old;
} elseif ( ! $login_image ) {
	if ( $login_image_id ) {
		if ( is_multisite() && function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'ultimate-branding/ultimate-branding.php' ) ) {
			switch_to_blog( 1 );
			$login_image_src = wp_get_attachment_image_src( $login_image_id, $login_image_size, $icon = false );
			restore_current_blog();
		} else {
			$login_image_src = wp_get_attachment_image_src( $login_image_id, $login_image_size, $icon = false );
		}
		$login_image = $login_image_src[0];
	} else {
		$response = wp_remote_head( admin_url() . 'images/wordpress-logo.svg' );
		if ( ! is_wp_error( $response ) && ! empty( $response['response']['code'] ) && $response['response']['code'] == '200' ) {//support for 3.8+
			$login_image = admin_url() . 'images/wordpress-logo.svg';
		} else {
			$login_image = admin_url() . 'images/wordpress-logo.png';
		}
	}
}
?>
                    <img height="<?php echo $login_image_height ?>" width="<?php echo $login_image_width ?>" id="wp_login_image_el" src="<?php echo $login_image . '?' . md5( time() ); ?>" />
                    </p>

                    <h4><?php _e( 'Change Image', 'login_image' ); ?></h4>

                    <input class="upload-url" id="wp_login_image" type="text" size="36" name="wp_login_image" value="<?php echo esc_attr( $login_image ); ?>" />
                    <input class="st_upload_button button" id="wp_login_image_button" type="button" value="<?php _e( 'Browse', 'ub' ); ?>" />
                    <input type="hidden" name="wp_login_image_id" id="wp_login_image_id" value="<?php echo esc_attr( $login_image_id ); ?>" />
                    <input type="hidden" name="wp_login_image_size" id="wp_login_image_size" value="<?php echo esc_attr( $login_image_size ); ?>" />
                    <p id="wp_login_image_width_wrap" class="<?php echo ! $login_image_id ? 'hidden' : ''  ?>">
                        <label for="wp_login_image_width">
                            <?php _e( 'Login Image Width', 'ub' ); ?>
                            <input type="<?php echo ! $login_image_id ? 'hidden' : 'number'  ?>" name="wp_login_image_width" id="wp_login_image_width" value="<?php echo esc_attr( $login_image_width ); ?>" />
                        </label>
                    </p>
                    <p id="wp_login_image_height_wrap" class="<?php echo ! $login_image_id ? 'hidden' : ''  ?>">
                        <label for="wp_login_image_height">
                            <?php _e( 'Login Image Height', 'ub' ); ?>
                            <input type="<?php echo ! $login_image_id ? 'hidden' : 'number'  ?>" name="wp_login_image_height" id="wp_login_image_height" value="<?php echo esc_attr( $login_image_height ); ?>" />
                        </label>
                    </p>
                </div>
            </div>
        </div>

<?php
	}

	protected function get_attachment_by_guid( $guid ) {
		global $wpdb;
		$table = $wpdb->base_prefix . 'posts';

		return $wpdb->get_var( $wpdb->prepare( "SELECT `ID` FROM  $table WHERE `guid`=%s", $guid ) );
	}
	protected function is_relative( $url ) {
		return  ( parse_url( $url, PHP_URL_SCHEME ) === '' || parse_url( $url, PHP_URL_SCHEME ) === null );
	}

	protected function get_absolute_url( $url ) {
		if ( $this->is_relative( $url ) ) {
			return trailingslashit( get_home_url() ) . ltrim( $url, '/\\' );
		}
		return $url;
	}

	/**
	 * Export data.
	 *
	 * @since 1.8.6
	 */
	public function export( $data ) {
		$options = array(
			'ub_login_image',
			'ub_login_image_height',
			'ub_login_image_id',
			'ub_login_image_size',
			'ub_login_image_width',
		);
		foreach ( $options as $key ) {
			$data['modules'][ $key ] = ub_get_option( $key );
		}
		return $data;
	}
}

$ub_loginimage = new ub_Login_Image();