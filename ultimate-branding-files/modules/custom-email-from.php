<?php
/*
  Plugin Name: E-mail From
  Author: Marko Miljus (Incsub)
  Version: 1.1.1
 */

class ub_custom_email_from {

	function __construct() {
		add_action( 'ultimatebranding_settings_from_email', array( $this, 'admin_options_page' ) );
		add_filter( 'ultimatebranding_settings_from_email_process', array( $this, 'update' ), 10, 1 );
		add_filter( 'wp_mail_from', array( $this, 'from_email' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'from_email_name' ) );
		add_filter( 'ultimate_branding_export_data', array( $this, 'export' ) );
	}

	public function admin_options_page() {

		$ub_from_email = ub_get_option( 'ub_from_email', ub_get_option( 'admin_email' ) );
		$ub_from_name = ub_get_option( 'ub_from_name', ub_get_option( 'blogname', ub_get_option( 'site_name' ) ) );
?>
        <div class="postbox">
            <h3 class="hndle" style='cursor:auto;'><span><?php _e( 'Custom E-mail From Headers', 'ub' ) ?></span></h3>
            <div class="inside">
                <table class="form-table">

                    <tr valign="top">
                        <th scope="row"><?php _e( 'E-mail Address', 'ub' ) ?></th>
                        <td>
                            <input type="text" name="ub_from_email" value="<?php echo esc_attr( $ub_from_email ); ?>" />
                            <?php _e( 'Default FROM E-email address', 'ub' ) ?>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e( 'Sender Name', 'ub' ) ?></th>
                        <td>
                            <input type="text" name="ub_from_name" value="<?php echo esc_attr( $ub_from_name ); ?>" />
                            <?php _e( 'Default FROM Sender Name', 'ub' ) ?>
                        </td>
                    </tr>

                </table>
            </div>
        </div>
<?php
	}

	public function update( $status ) {
		ub_update_option( 'ub_from_name', $_POST['ub_from_name'] );
		ub_update_option( 'ub_from_email', $_POST['ub_from_email'] );
		if ( $status === false ) {
			return $status;
		} else {
			return true;
		}
	}

	function from_email( $email ) {
		return ub_get_option( 'ub_from_email', ub_get_option( 'admin_email' ) );
	}

	function from_email_name( $email ) {
		return ub_get_option( 'ub_from_name', ub_get_option( 'blogname', ub_get_option( 'site_name' ) ) );
	}

	/**
	 * Export data.
	 *
	 * @since 1.8.6
	 */
	public function export( $data ) {
		$options = array(
			'ub_from_email',
			'ub_from_name',
		);
		foreach ( $options as $key ) {
			$data['modules'][ $key ] = ub_get_option( $key );
		}
		return $data;
	}
}

$ub_custom_email_from = new ub_custom_email_from();

