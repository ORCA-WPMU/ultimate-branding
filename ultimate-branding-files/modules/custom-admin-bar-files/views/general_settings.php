<?php
/**
 * Renders general setting box
 */
?>
<div class="postbox-container">
    <div class="meta-box-sortables not-sortable">
        <div class="postbox">
            <div class="handlediv" title="Click to toggle"><br></div>
            <h3 class="hndle" style='cursor:auto;'>
                <span><?php _e( 'General Settings','ub' ); ?></span>
            </h3>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <?php _e( 'Custom entry', 'ub' ) ?>
                        </td>
                        <td>
                            <?php UB_Admin_Bar_Forms::create_enabled_box() ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e( 'The Toolbar visibility', 'ub' ) ?>
                        </td>
                        <td>
                            <?php UB_Admin_Bar_Forms::create_show_box() ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
                    <?php if ( $enable_hiding_default_bars ) :  ?>
<div class="postbox-container">
    <div class="meta-box-sortables not-sortable">
        <div class="postbox">
            <div class="handlediv" title="Click to toggle"><br></div>
            <h3 class="hndle" style='cursor:auto;'>
                <span><?php _e( 'Hide WordPress Menu Items','ub' ); ?></span>
            </h3>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <?php  _e( 'Menu Items', 'ub' ) ?>
                        </th>
                        <td>
                            <?php UB_Admin_Bar_Forms::create_disable_box() ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Select User Roles affected by above', 'ub' ) ?>
                        </th>
                        <td>
                            <?php UB_Admin_Bar_Forms::create_roles_box( 'wp_menu_roles' ) ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
                    <?php endif; ?>
<div class="postbox-container">
    <div class="meta-box-sortables not-sortable">
        <div class="postbox">
            <div class="handlediv" title="Click to toggle"><br></div>
            <h3 class="hndle" style='cursor:auto;'>
                <span><?php _e( 'Advance Settings','ub' ); ?></span>
            </h3>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <?php  _e( 'Reorder Admin Bar Menus', 'ub' ) ?>
                        </th>
                        <td>
                            <form method="post">
                                <button id="ub_admin_bar_start_ordering" class="button-secondary"><?php _e( 'Reorder Menus', 'ub' ); ?></button>
                                <button type="submit" class="button-secondary" name="ub_admin_bar_restore_default_order"><?php _e( 'Restore Default Order', 'ub' ); ?></button>
                            </form>
                            <p class="description">
                                <?php _e( "Select 'Reorder Menus' then drag and drop to reorder your menu items. 'Restore Default Order' reverts them back to their original order, overriding any ordering you have set up.", 'ub' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php  _e( 'Admin Bar CSS Styles', 'ub' ) ?>
                        </th>
                        <td>
                            <textarea style="display: none" type="text" name="ub_admin_bar_style" id="ub_admin_bar_style_input"><?php echo UB_Admin_Bar::styles( true );  ?></textarea>
                            <div id="ub_admin_bar_style_editor" data-input="#ub_admin_bar_style_input"  class="ub_css_editor"><?php echo UB_Admin_Bar::styles( true );  ?></div>
                            <p class="description">
                                <?php _e( "Styles defined here only apply to the admin bar, no other part of WordPress will be affected. Leave empty if no change to the default style is being made. Please don't use more than one selector for each set of rules.", 'ub' ); ?>
                            </p>
                            <p class="description">
                                <?php _e( 'Style <code>.ab-item .dashicons</code> to change styling of images in the admin bar.', 'ub' ); ?>
                            </p>
                            <p class="description">
                                <?php _e( 'Style <code>#wpadminbar</code> to change color or other styling of the admin bar.', 'ub' ); ?>
                            </p>
                            <p class="description">
                                <?php _e( 'Style <code>.ab-item</code> to change font  or other styling of the links in admin bar.', 'ub' ); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
