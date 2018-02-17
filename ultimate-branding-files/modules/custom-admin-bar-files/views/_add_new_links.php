<?php
/**
 * Renders add new links box
 */
?>
<div class="postbox-container">
    <div class="meta-box-sortables ui-sortable">
        <div class="postbox ub_add_new_link_box closed not-sortable">
            <div class="handlediv" title="Click to toggle"><br>
            </div>
            <h3 class="hndle">
                <span> <?php _e( 'New link', 'ub' ); ?> </span>
            </h3>
            <div class="inside">
                <p>
                    <label for="wdcab_last_wizard_step_url"><?php _e( 'URL:', 'ub' ); ?></label><br>
                    <select class="wdcab_last_wizard_step_url_type" name="ub_ab_prev[<?php echo $menu->id; ?>][links][_last_][url_type]">
                        <option value="admin" data-value="<?php echo trailingslashit( network_admin_url() ); ?>"><?php _e( 'Administrative page (e.g. "post-new.php" or "themes.php")', 'ub' ); ?></option>
                        <option value="site" data-value="<?php echo trailingslashit( network_site_url() ); ?>"><?php _e( 'Site page (e.g. "/" or "/2007-06-05/an-old-post")', 'ub' ); ?></option>
                        <option value="external" data-value=""><?php _e( 'External page (e.g. "http://www/example.com/2007-06-05/an-old-post")', 'ub' ); ?></option>
                    </select>
                    <span class="wdcab_url_preview"><?php _e( 'Preview:', 'ub' ); ?> <code><?php echo admin_url(); ?></code></span>
                </p>
                <p>
                    <input type="text" class="widefat wdcab_last_wizard_step_url" name="ub_ab_prev[<?php echo $menu->id ?>][links][_last_][url]">
                </p>
                <p>
                    <label for="wdcab_last_wizard_step_title"><?php _e( 'Title:', 'ub' ); ?></label>
                    <input type="text" class="widefat"  name="ub_ab_prev[<?php echo $menu->id ?>][links][_last_][title]">
                </p>
                <p>
                    <input type="checkbox" class="widefat wdcab_last_wizard_steptarget"  name="ub_ab_prev[<?php echo $menu->id ?>][links][_last_][target]">
                    <label for="wdcab_last_wizard_step_target"><?php _e( 'Open in new window', 'ub' ); ?></label>
                </p>
                <br>
                <p>
                    <button type="submit" class="button-primary"><?php _e( 'Add', 'ub' ); ?></button>
                </p>
            </div>
        </div>
    </div>
</div>