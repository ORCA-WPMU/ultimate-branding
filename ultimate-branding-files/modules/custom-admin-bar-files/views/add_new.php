<?php
/**
 * Renders hidden field for deleted menus
 * Renders template for new menus
 * Renders Add button
 */
?>
<input name="ub_ab_delete_links" type="hidden"/>
<script type="text/html" id="ub_admin_bar_template">

    <div class="meta-box-sortables parent_admin_bar parent_admin_bar_new not-sortable">
        <div class="postbox closed">
            <div class="handlediv" title="Click to toggle"><br>
            </div>
            <h3 class="hndle">
                <a href="#" data-id="" class="wdcab_step_delete button-secondary pull-right ub_delete_row">Delete</a>

                <span class="ub_ad_main_order"></span>
            </h3>
            <div class="inside">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><?php _e( 'Entry title', 'ub' ); ?><br><small><?php _e( '(text or image)', 'ub' ); ?></small></th>
                        <td>
                            <input type="text" class="widefat" name="ub_ab_tmp[][title]" value="">
                            <p><?php _e( "If you'd like to use an image instead of text, please paste the full URL of your image in the box (starting with <code>http://</code> - e.g. <code>http://example.com/myimage.gif</code>).", 'ub' ); ?></p>
                            <p><?php _e( 'For best results, make sure your image has a transparent background and is no more than 28px high.', 'ub' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Title link leads to</th>
                        <td>
                            <input type="radio" class="title_link-url-type" data-value="#" name="ub_ab_tmp[][url]"  value="#" checked="checked">
                            <label for="title_link-#"><?php _e( 'Nowhere, it is just a menu hub', 'ub' ); ?></label><br>
                            <?php if ( is_multisite() ) : ?>
                                <input id="title_link-url-type-network_site_url" class="title_link-url-type" data-value="<?php echo network_site_url(); ?>" type="radio" name="ub_ab_tmp[][url]"  value="network_site_url" >
                                <label for="title_link-url-type-network_site_url"><?php _e( 'Main site home URL', 'ub' ); ?></label><br>
                            <?php  endif; ?>

                            <input id="title_link-url-type-site" class="title_link-url-type" data-value="<?php echo trailingslashit( site_url() ); ?>" type="radio" name="ub_ab_tmp[][url]"  value="site_url" >
                            <label for="title_link-url-type-site"><?php _e( 'Current site home URL', 'ub' ); ?></label><br>

                            <input class="title_link-url-type" data-value="<?php echo network_admin_url(); ?>" type="radio" name="ub_ab_tmp[][url]"  value="admin_url">
                            <label for="title_link-admin_url"><?php _e( 'Site Admin area', 'ub' ); ?></label><br>
                            <input class="title_link-this_url-switch title_link-url-type" class="title_link-this_url-switch title_link-url-type" data-value="" type="radio" name="ub_ab_tmp[][url]" value="url"  >
                            <label for="title_link-this_url-switch"><?php _e( 'This URL:', 'ub' ); ?></label>
                            <input type="text" class="title_link-this_url"  size="48" name="ub_ab_tmp[][url]" value="" ><br>
                        </td>
                    </tr>
                    <tr>
	                    <th scope="row"><?php _e( 'Use Icon', 'ub' );?></th>
	                    <td>
		                    <input class="ub_adminbar_use_icon" type="checkbox"   name="ub_ab_tmp[][use_icon]">
	                    </td>
                    </tr>
                    <tr class="ub_adminbar_icon_tr hidden">
	                    <th scope="row"><?php _e( 'Icon', 'ub' ) ?> <br><small><?php _e( 'Icon to be used beside the menu text and shown on mobile devices', 'ub' ) ?></small></th>
	                    <td>
		                    <?php UB_Admin_Bar_Forms::render_dashicons_radios(); ?>
	                    </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Open in new window?', 'ub' ); ?></th>
                        <td>
                            <input type="checkbox" name="ub_ab_tmp[][target]" >
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Select User Roles allowed to see menu', 'ub' ); ?></th>
                        <td>
	                        <?php UB_Admin_Bar_Forms::create_submenu_roles(); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Add new link', 'ub' ); ?></th>
                        <td>        <div class="postbox-container">
                                <div class="meta-box-sortables ui-sortable">
                                    <div class="postbox ub_add_new_link_box closed">
                                        <div class="handlediv" title="Click to toggle"><br>
                                        </div>
                                        <h3 class="hndle">
                                            <span><?php _e( 'New link', 'ub' ); ?></span>
                                        </h3>
                                        <div class="inside">
                                            <p>
                                                <label for="wdcab_last_wizard_step_url"><?php _e( 'URL:', 'ub' ); ?></label><br>
                                                <select class="wdcab_last_wizard_step_url_type" name="ub_ab_tmp[][links][_last_][url_type]">
                                                    <option value="admin" data-value="<?php echo trailingslashit( network_admin_url() ); ?>"><?php _e( 'Administrative page (e.g. "post-new.php" or "themes.php")', 'ub' ); ?></option>
                                                    <option value="site" data-value="<?php echo trailingslashit( network_site_url() ); ?>"><?php _e( 'Site page (e.g. "/" or "/2007-06-05/an-old-post")', 'ub' ); ?></option>
                                                    <option value="external" data-value=""><?php _e( 'External page (e.g. "http://www/example.com/2007-06-05/an-old-post")', 'ub' ); ?></option>
                                                </select>
                                                <span class="wdcab_url_preview"><?php _e( 'Preview:', 'ub' ); ?><code></code></span>
                                            </p>
                                            <p>
                                                <input type="text" class="widefat wdcab_last_wizard_step_url" name="ub_ab_tmp[][links][_last_][url]">
                                            </p>
                                            <p>
                                                <label for="wdcab_last_wizard_step_title"><?php _e( 'Title:', 'ub' ); ?></label>
                                                <input type="text" class="widefat"  name="ub_ab_tmp[][links][_last_][title]">
                                            </p>
                                            <p>
                                                <input id="wdcab_last_wizard_step_target" type="checkbox" class="wdcab_step_target" name="ub_ab_tmp[][links][_last_][target]">
                                                <label for="wdcab_last_wizard_step_target"><?php _e( 'Open in new window', 'ub' ) ?></label><br>
                                            </p>
                                            <br>
                                            <p>
                                                <button type="submit" class="button-primary"><?php _e( 'Add', 'ub' ) ?></button>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Configure Links', 'ub' ); ?>
                        </th>
                        <td>
                            <p>
                                <?php _e( 'Drag and drop links to sort them into the order you want.', 'ub' ); ?>
                            </p>
                        </td>

                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</script>
<p><button class="button-secondary" id="ub_add_new_admin_bar">Add New Parent Menu</button></p>