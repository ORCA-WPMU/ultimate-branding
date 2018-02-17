<?php
/**
 * Renders submenus
 *
 * @var $menu UB_Admin_Bar_Menu
 * @var $sub UB_Admin_Bar_Menu
 */
?>
<?php $i = 1; foreach ( $menu->subs as $sub ) : ?>
    <div class="postbox closed ub_admin_bar_submenu">
        <div class="handlediv" title="Click to toggle"><br>
        </div>
        <h3 class="hndle">
            <a href="#" data-id="<?php echo $sub->id; ?>" class="wdcab_step_delete button-secondary pull-right"><?php _e( 'Delete', 'ub' ); ?></a>
            <span>
                <span class="wdcab_step_count"><?php echo $i; ?> </span>:&nbsp;
                <?php echo $sub->title_image;  ?>
            </span>
        </h3>
        <div class="inside">
            <?php _e( 'Type:', 'ub' ); ?>&nbsp; <h3 class="inline"><?php echo ucfirst( $sub->link_type ); ?></h3>
            <p>
                <label for="<?php echo $menu->get_field_id( 'ub_admin_bar_title' ); ?>"><?php _e( 'Title:', 'ub' ); ?></label>
                <input type="text" id="<?php echo $menu->get_field_id( 'ub_admin_bar_title' ); ?>" class="wdcab_step_title widefat" name="ub_ab_prev[<?php echo $menu->id ?>][links][<?php echo $sub->id ?>][title]" value="<?php echo esc_attr( $sub->title ); ?>">
            </p>
            <p>
                <label for="<?php echo $menu->get_field_id( 'ub_admin_bar_url' ); ?>"><?php _e( 'URL:', 'ub' ); ?></label>
                <input type="text" id="<?php echo $menu->get_field_id( 'ub_admin_bar_url' ); ?>" class="wdcab_step_url widefat" name="ub_ab_prev[<?php echo $menu->id ?>][links][<?php echo $sub->id ?>][url]" value="<?php echo $sub->get_link_url( true ); ?>">
            </p>
            <input type="hidden" class="wdcab_step_url_type" name="ub_ab_prev[<?php echo $menu->id ?>][links][<?php echo $sub->id ?>][url_type]" value="<?php echo $sub->link_type ?>">
            <p>
                <input id="<?php echo $menu->get_field_id( 'ub_admin_bar_target' ) . $sub->id; ?>" type="checkbox" class="wdcab_step_target" name="ub_ab_prev[<?php echo $menu->id ?>][links][<?php echo $sub->id ?>][target]" <?php  checked( $sub->target, '_blank' ); ?>>
                <label for="<?php echo $menu->get_field_id( 'ub_admin_bar_target' ) . $sub->id ; ?>"><?php _e( 'Open in new window', 'ub' ) ?></label><br>
            </p>
        </div>
    </div>
<?php $i++; endforeach; ?>