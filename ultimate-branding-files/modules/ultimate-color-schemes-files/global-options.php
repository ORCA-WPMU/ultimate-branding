<p class="description"><?php _e( 'Check which color schemes will be visible within the User Profile. At least two color schemes have to be visible in order to see it as an option on the User Profile page.', 'ub' ); ?></p>

<div class="postbox">
    <div class="inside">
        <table class="form-table">
            <tbody>
                <?php
				global $_wp_admin_css_colors;
				?>
                <tr valign="top">
                    <th scope="row"><label><?php _e( 'Visible Color Schemes', 'ub' ); ?></label></th>
                    <td>
                        <fieldset id="color-picker" class="scheme-list">
                            <legend class="screen-reader-text"><span><?php _e( 'Admin Color Scheme' ); ?></span></legend>
                            <input name="ucs_visible_color_schemes[]" type="hidden" value="" />
                            <?php
							$visible_colors = ub_get_option( 'ucs_visible_color_schemes', false );

							foreach ( $_wp_admin_css_colors as $color => $color_info ) :
								?>
                                <div class="color-option" style="vertical-align:top">
                                    <input name="ucs_visible_color_schemes[]" id="admin_color_<?php echo esc_attr( $color ); ?>" type="checkbox" value="<?php echo esc_attr( $color ); ?>" class="tog" <?php echo ($visible_colors == false || in_array( $color, $visible_colors ) ? 'checked' : ''); ?> />
                                    <label for="admin_color_<?php echo esc_attr( $color ); ?>"><?php echo esc_html( $color_info->name ); ?></label>
                                    <table class="color-palette">
                                        <tr>
                                            <?php
											foreach ( $color_info->colors as $html_color ) {
												?>
                                                <td style="background-color: <?php echo esc_attr( $html_color ); ?>">&nbsp;</td>
                                                <?php
											}
											?>
                                        </tr>
                                    </table>
<?php
if ( 'wpi_custom_scheme' == $color ) {
	$url = add_query_arg(
		array(
		'page' => 'branding',
		'tab' => 'ultimate-color-schemes',
		'edit' => 1,
		),
		network_admin_url( 'admin.php' )
	);
	printf(
		'<p><a href="%s">%s</a></p>',
		esc_url( $url ),
		esc_html( sprintf( _x( 'Customize "%s" scheme', 'Label for link to edit custom theme', 'ub' ), $color_info->name ) )
	);
}
?>
                                </div>
                                <?php
							endforeach;
							?>
                        </fieldset>
                    </td>
                </tr>

                <?php $force_color = ub_get_option( 'ucs_force_color_scheme', false ); ?>

                <tr valign="top">
                    <th scope="row"><label><?php _e( 'Force Admin Color Scheme', 'ub' ); ?></label></th>
                    <td>

                        <select name="ucs_force_color_scheme" id="ucs_force_color_scheme">
                            <option value="false" <?php selected( $force_color, 'false', true );?>><?php _e( 'Do not force color scheme', 'ub' ); ?></option>
                            <?php
							foreach ( $_wp_admin_css_colors as $color => $color_info ) {
								?>
                                <option value="<?php echo $color; ?>" <?php selected( $force_color, $color, true );?>><?php echo $color_info->name; ?></option>
                                <?php
							}
							?>
                        </select>

                        <p class="description"><?php _e( 'Color scheme will be used for every user across website / network.', 'ub' ); ?></p>

                    </td>
                </tr>

                <?php $default_color = ub_get_option( 'ucs_default_color_scheme', false ); ?>

                <tr valign="top">
                    <th scope="row"><label><?php _e( 'Default Admin Color Scheme (for Newely Registered Users)', 'ub' ); ?></label></th>
                    <td>

                        <select name="ucs_default_color_scheme" id="ucs_default_color_scheme">
                            <option value="false" <?php selected( $default_color, 'false', true );?>><?php _e( 'Use WordPress defaults', 'ub' ); ?></option>
                            <?php
							foreach ( $_wp_admin_css_colors as $color => $color_info ) {
								?>
                                <option value="<?php echo $color; ?>" <?php selected( $default_color, $color, true );?>><?php echo $color_info->name; ?></option>
                                <?php
							}
							?>
                        </select>

                        <p class="description"><?php _e( 'Please note that user will see forced color scheme instead if you set it.', 'ub' ); ?></p>

                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
