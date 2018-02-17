<?php
/*
Plugin Name: Global Footer Content
Plugin URI: http://premium.wpmudev.org/project/global-footer-content
Description: Simply insert any code that you like into the footer of every blog
Author: Barry (Incsub), S H Mohanjith (Incsub), Andrew Billits (Incsub)
 */

class ub_global_footer_content extends ub_helper {

	var $global_footer_content_settings_page;
	var $global_footer_content_settings_page_long;
	var $global_footer_content;

	public function __construct() {
		add_action( 'ultimatebranding_settings_footer', array( &$this, 'global_footer_content_site_admin_options' ) );
		add_filter( 'ultimatebranding_settings_footer_process', array( &$this, 'update_global_footer_options' ), 10, 1 );
		add_action( 'wp_footer', array( &$this, 'global_footer_content_output' ), 10 );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );

		$this->global_footer_content = get_option( 'global_footer_content' );
		add_filter( 'ultimate_branding_export_data', array( $this, 'export' ) );
	}

	public function enqueue_scripts() {
		global $ub_version;
		wp_enqueue_style( 'ub_global_footer_style', ub_files_url( 'modules/global-footer-content-files/css/main.css' )  . '', false, $ub_version );
		wp_enqueue_script( 'ub_global_footer_js', ub_files_url( 'modules/global-footer-content-files/js/main.js' ), array(), $ub_version, true );
	}

	public function update_global_footer_options( $status ) {

		$global_footer = $_POST['ub_global_footer'];
		$global_footer_main = isset( $_POST['ub_global_footer_main'] )? $_POST['ub_global_footer_main']:'';

		if ( '' === $global_footer['content'] ) {
			$global_footer['content'] = 'empty';
		}

		if ( isset( $global_footer['themefooter'] ) ) {
			$global_footer['themefooter'] = ( 'on' === $global_footer['themefooter'] ) ? 'checked' : '';
		} else {
			$global_footer['themefooter'] = '';
		}

		if ( isset( $global_footer['shortcodes'] ) ) {
			$global_footer['shortcodes'] = ( 'on' === $global_footer['shortcodes'] ) ? 'checked' : '';
		} else {
			$global_footer['shortcodes'] = '';
		}

		$status *= ub_update_option( 'global_footer_content' , $global_footer['content'] );
		$status *= ub_update_option( 'global_footer_bgcolor', $global_footer['bgcolor'] );
		$status *= ub_update_option( 'global_footer_fixedheight', $global_footer['fixedheight'] );
		$status *= ub_update_option( 'global_footer_themefooter', $global_footer['themefooter'] );
		$status *= ub_update_option( 'global_footer_shortcodes', $global_footer['shortcodes'] );

		if ( is_multisite() ) {
			if ( '' === $global_footer_main['content'] ) {
				$global_footer_main['content'] = 'empty';
			}

			if ( isset( $global_footer_main['themefooter'] ) ) {
				$global_footer_main['themefooter'] = ( 'on' === $global_footer_main['themefooter'] ) ? 'checked' : '';
			} else {
				$global_footer_main['themefooter'] = '';
			}

			if ( isset( $global_footer_main['shortcodes'] ) ) {
				$global_footer_main['shortcodes'] = ( 'on' === $global_footer_main['shortcodes'] ) ? 'checked' : '';
			} else {
				$global_footer_main['shortcodes'] = '';
			}

			$status *= ub_update_option( 'global_footer_main_content' , $global_footer_main['content'] );
			$status *= ub_update_option( 'global_footer_main_bgcolor', $global_footer_main['bgcolor'] );
			$status *= ub_update_option( 'global_footer_main_fixedheight', $global_footer_main['fixedheight'] );
			$status *= ub_update_option( 'global_footer_main_themefooter', $global_footer_main['themefooter'] );
			$status *= ub_update_option( 'global_footer_main_shortcodes', $global_footer_main['shortcodes'] );
		}

		return true;
	}

	public function global_footer_content_output() {
		$global_footer_content = ub_get_option( 'global_footer_content' );
		$global_footer_main_content = ub_get_option( 'global_footer_main_content' );
		if ( $global_footer_content === 'empty' ) {
			$global_footer_content = '';
		}
		if ( $global_footer_main_content === 'empty' ) {
			$global_footer_main_content = '';
		}
		/**
		 * $global_footer_content
		 */
		if ( ! empty( $global_footer_content ) && ( ! is_multisite() || ! is_main_site() ) ) {

			/**
			 * Avoid using wp_content filter, because it breaks compatibility with themes with UI builders.
			 */
			$global_footer_content = apply_filters( 'wptexturize', $global_footer_content );
			$global_footer_content = apply_filters( 'convert_smilies', $global_footer_content );
			$global_footer_content = apply_filters( 'convert_chars', $global_footer_content );
			$global_footer_content = apply_filters( 'wpautop', $global_footer_content );

			$global_footer_bgcolor = ub_get_option( 'global_footer_bgcolor', '' );
			$global_footer_height = ub_get_option( 'global_footer_fixedheight', '' );
			$global_footer_themefooter = ub_get_option( 'global_footer_themefooter', '' );
			$global_footer_shortcodes = ub_get_option( 'global_footer_shortcodes', '' );

			if ( 'checked' == $global_footer_shortcodes ) {
				$global_footer_content = do_shortcode( $global_footer_content );
			}

			$style = '' !== $global_footer_bgcolor ? 'background-color:' . $global_footer_bgcolor . ';' : '';
			$style .= '' !== $global_footer_height ? 'height:' . $global_footer_height .'px;overflow:hidden;' : '';
			$class = 'checked' === $global_footer_themefooter ? 'ub_global_footer_inside' : '';
?>
            <div id="ub_global_footer_content" style="<?php echo $style ?>" class="<?php echo $class; ?>">
                <?php echo stripslashes( $global_footer_content );?>
            </div>
<?php
		}
		/**
		 * $global_footer_main_content
		 */
		if ( ! empty( $global_footer_main_content ) && ( is_multisite() && is_main_site() ) ) {
			/**
			 * Avoid using wp_content filter, because it breaks compatibility with themes with UI builders.
			 */
			$global_footer_main_content = apply_filters( 'wptexturize', $global_footer_main_content );
			$global_footer_main_content = apply_filters( 'convert_smilies', $global_footer_main_content );
			$global_footer_main_content = apply_filters( 'convert_chars', $global_footer_main_content );
			$global_footer_main_content = apply_filters( 'wpautop', $global_footer_main_content );

			$global_footer_main_bgcolor = ub_get_option( 'global_footer_main_bgcolor', '' );
			$global_footer_main_height = ub_get_option( 'global_footer_main_fixedheight', '' );
			$global_footer_main_themefooter = ub_get_option( 'global_footer_main_themefooter', '' );
			$global_footer_main_shortcodes = ub_get_option( 'global_footer_main_shortcodes', '' );

			if ( 'checked' == $global_footer_main_shortcodes ) {
				$global_footer_main_content = do_shortcode( $global_footer_main_content );
			}

			$style = '' !== $global_footer_main_bgcolor ? 'background-color:' . $global_footer_main_bgcolor . ';' : '';
			$style .= '' !== $global_footer_main_height ? 'height:' . $global_footer_main_height .'px;overflow:hidden;' : '';
			$class = 'checked' === $global_footer_main_themefooter ? 'ub_global_footer_inside' : '';
?>
            <div id="ub_global_footer_content"  style="<?php echo $style ?>" class="<?php echo $class; ?>">
                <?php echo stripslashes( $global_footer_main_content );?>
            </div>
<?php
		}
	}

	public function global_footer_content_site_admin_options() {

		// footer content
		$global_footer_content = ub_get_option( 'global_footer_content', '' );
		if ( $global_footer_content == 'empty' ) {
			$global_footer_content = '';
		}

		// footer background color
		$global_footer_bgcolor = ub_get_option( 'global_footer_bgcolor', '' );

		// fixed height
		$global_footer_fixedheight = ub_get_option( 'global_footer_fixedheight', '' );

		// integrate in theme footer
		$global_footer_themefooter = ub_get_option( 'global_footer_themefooter', '' );

		// proceed shortcodes
		$global_footer_shortcodes = ub_get_option( 'global_footer_shortcodes', '' );
?>
            <div class="postbox">
            <h3 class="hndle" style='cursor:auto;'><span><?php echo is_multisite() ? __( 'Global Footer Content For Subsites', 'ub' ) : __( 'Global Footer Content', 'ub' ) ?></span></h3>
            <div class="inside">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Footer Content', 'ub' ) ?></th>
                        <td>
<?php
		$args = array( 'textarea_name' => 'ub_global_footer[content]', 'textarea_rows' => 5 );
		wp_editor( stripslashes( $global_footer_content ), 'global_footer_content', $args );
?>
                            <br />
                            <?php _e( 'What is added here will be shown on every blog or site in your network. You can add tracking code, embeds, terms of service links, etc.', 'ub' ) ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Background Color', 'ub' ) ?></th>
                        <td>
                            <input name="ub_global_footer[bgcolor]" class="ub_color_picker" id="ub_footer_background_color" type="text"   value="<?php echo $global_footer_bgcolor; ?>"/>
                            <br />
                            <?php _e( "Click on 'clear' button to make background transparent", 'ub' ) ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Fixed Height', 'ub' ) ?></th>
                        <td>
                            <input class="text-60"  name="ub_global_footer[fixedheight]"  id="ub_footer_fixedheight" type="number" step="1"   value="<?php echo $global_footer_fixedheight; ?>"/>&nbsp;px
                            <br />
                            <?php _e( 'Choose height of footer. Leave blank to fit height to content', 'ub' ) ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Integrate in theme footer', 'ub' ) ?></th>
                        <td>
                            <input class="text-60"  name="ub_global_footer[themefooter]"  id="ub_footer_themefooter" type="checkbox" <?php echo $global_footer_themefooter; ?>/>
                            <br />
                            <?php _e( 'If selected, the plugin will try to place the footer content block inside the theme footer element.', 'ub' ) ?>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e( 'Proceed shortcode', 'ub' ) ?></th>
                        <td>
                            <input class="text-60"  name="ub_global_footer[shortcodes]"  id="ub_footer_shortcodes" type="checkbox" <?php echo $global_footer_shortcodes; ?>/>
                            <p class="description"><?php _e( 'Be careful it can break compatibility with themes with UI builders.', 'ub' ) ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
<?php if ( ( is_multisite() && is_super_admin() ) ) :
	$global_footer_main_content = ub_get_option( 'global_footer_main_content', '' );
	$global_footer_main_bgcolor = ub_get_option( 'global_footer_main_bgcolor', '' );
	$global_footer_main_fixedheight = ub_get_option( 'global_footer_main_fixedheight', '' );
	$global_footer_main_themefooter = ub_get_option( 'global_footer_main_themefooter', '' );
	$global_footer_main_shortcodes = ub_get_option( 'global_footer_main_shortcodes', '' );
	if ( $global_footer_main_content == 'empty' ) {
		$global_footer_main_content = '';
	}
?>
            <div class="postbox">
                <h3 class="hndle" style='cursor:auto;'><span><?php _e( 'Global Footer Content For Main Site', 'ub' ) ?></span></h3>
                <div class="inside">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php _e( 'Footer Content', 'ub' ) ?></th>
                            <td>
<?php
$args = array( 'textarea_name' => 'ub_global_footer_main[content]', 'textarea_rows' => 5 );
wp_editor( stripslashes( $global_footer_main_content ), 'global_footer_main_content', $args );
?>
                                <br />
                                <?php _e( 'What is added here will be shown on every blog or site in your network. You can add tracking code, embeds, terms of service links, etc.', 'ub' ) ?>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e( 'Background Color', 'ub' ) ?></th>
                            <td>
                                <input name="ub_global_footer_main[bgcolor]" class="ub_color_picker" id="ub_footer_main_background_color" type="text"   value="<?php echo $global_footer_main_bgcolor; ?>"/>
                                <br />
                                <?php _e( "Click on 'clear' button to make background transparent", 'ub' ) ?>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e( 'Fixed Height', 'ub' ) ?></th>
                            <td>
                                <input class="text-60"  name="ub_global_footer_main[fixedheight]"  id="ub_footer_main_fixedheight" type="number" step="1"   value="<?php echo $global_footer_main_fixedheight; ?>"/>&nbsp;px
                                <br />
                                <?php _e( 'Choose height of footer. Leave blank to fit height to content', 'ub' ) ?>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e( 'Integrate in theme footer', 'ub' ) ?></th>
                            <td>
                                <input class="text-60"  name="ub_global_footer_main[themefooter]"  id="ub_footer_main_themefooter" type="checkbox" <?php echo $global_footer_main_themefooter; ?>/>&nbsp
                                <br />
                                <?php _e( 'If selected, the plugin will try to place the footer content block inside the theme footer element.', 'ub' ) ?>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e( 'Proceed shortcode', 'ub' ) ?></th>
                            <td>
                                <input class="text-60"  name="ub_global_footer_main[shortcodes]"  id="ub_footer_main_shortcodes" type="checkbox" <?php echo $global_footer_main_shortcodes; ?>/>
                                <p class="description"><?php _e( 'Be careful it can break compatibility with themes with UI builders.', 'ub' ) ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php endif; ?>
<?php
	}
	/**
	 * Export data.
	 *
	 * @since 1.8.6
	 */
	public function export( $data ) {
		$options = array(
			'global_footer_bgcolor',
			'global_footer_content',
			'global_footer_fixedheight',
			'global_footer_main_bgcolor',
			'global_footer_main_content',
			'global_footer_main_fixedheight',
			'global_footer_main_themefooter',
			'global_footer_themefooter',
			'global_footer_main_shortcodes',
			'global_footer_shortcodes',
		);
		foreach ( $options as $key ) {
			$data['modules'][ $key ] = ub_get_option( $key );
		}
		return $data;
	}
}

$ub_globalfootertext = new ub_global_footer_content();