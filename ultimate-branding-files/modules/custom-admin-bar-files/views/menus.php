<?php
/**
 * Renders menu boxes
 *
 * @var $menu UB_Admin_Bar_Menu
 * @var $sub UB_Admin_Bar_Menu
 */
$order = 1;
foreach ( UB_Admin_Bar::menus() as $menu ) :
?>
<div class="meta-box-sortables parent_admin_bar parent_admin_bar_prev not-sortable">
        <div class="postbox closed">
            <div class="handlediv" title="Click to toggle"><br>
            </div>
            <h3 class="hndle">
                <a href="#" data-id="<?php echo $menu->id; ?>" class="wdcab_step_delete button-secondary pull-right ub_delete_row"><?php _e( 'Delete', 'ub' ); ?></a>

                <?php echo $menu->title_image; ?>
            </h3>
            <div class="inside">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><?php _e( 'Entry title', 'ub' ) ?> <br><small><?php _e( '(text or image)', 'ub' ) ?></small></th>
                        <td>
                            <input type="text" class="widefat" name="ub_ab_prev[<?php echo $menu->id ?>][title]" value="<?php echo $menu->title; ?>">
                            <p><?php _e( "If you'd like to use an image instead of text, please paste the full URL of your image in the box (starting with <code>http://</code> - e.g. <code>http://example.com/myimage.gif</code>).", 'ub' ) ?></p>
                            <p><?php _e( 'For best results, make sure your image has a transparent background and is no more than 28px high.', 'ub' ) ?></p>
                        </td>
                    </tr>
                    <tr>
	                    <th scope="row"><?php _e( 'Use Icon', 'ub' );?></th>
	                    <td>
		                    <input class="ub_adminbar_use_icon" type="checkbox" <?php checked( $menu->use_icon, true ); ?>  name="ub_ab_prev[<?php echo $menu->id ?>][use_icon]">
	                    </td>
                    </tr>
                    <tr class="ub_adminbar_icon_tr <?php echo $menu->use_icon ? '' : 'ub_hidden'; ?>">
	                    <th scope="row"><?php _e( 'Icon', 'ub' ) ?> <br><small><?php _e( 'Icon to be used beside the menu text and shown on mobile devices', 'ub' ) ?></small></th>
	                    <td>
							<?php UB_Admin_Bar_Forms::render_dashicons_radios( $menu ); ?>
	                    </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Title link leads to', 'ub' ); ?></th>
                        <td>
                            <input id="<?php echo $menu->get_field_id( 'title_link-url-type-blank' ); ?>" type="radio" class="title_link-url-type" data-value="#" name="ub_ab_prev[<?php echo $menu->id; ?>][url]"  value="#" <?php checked( $menu->link_type, '#' ); ?>>
                            <label for="<?php echo $menu->get_field_id( 'title_link-url-type-blank' ); ?>"><?php _e( 'Nowhere, it is just a menu hub', 'ub' ); ?></label><br>

                            <?php if ( is_multisite() ) : ?>
                                <input id="<?php echo $menu->get_field_id( 'title_link-url-type-network_site_url' ); ?>" class="title_link-url-type" data-value="<?php echo network_site_url(); ?>" type="radio" name="ub_ab_prev[<?php echo $menu->id ?>][url]"  value="network_site_url" <?php checked( $menu->link_type, 'network_site_url' ); ?> >
                                <label for="<?php echo $menu->get_field_id( 'title_link-url-type-network_site_url' ); ?>"><?php _e( 'Main site home URL', 'ub' ); ?></label><br>
                            <?php  endif; ?>
                            <input id="<?php echo $menu->get_field_id( 'title_link-url-type-site' ); ?>" class="title_link-url-type" data-value="<?php echo trailingslashit( site_url() ); ?>" type="radio" name="ub_ab_prev[<?php echo $menu->id ?>][url]"  value="site_url" <?php checked( $menu->link_type, 'site_url' ); ?> >
                            <label for="<?php echo $menu->get_field_id( 'title_link-url-type-site' ); ?>"><?php _e( 'Current site home URL', 'ub' ); ?></label><br>

                            <input id="<?php echo $menu->get_field_id( 'title_link-url-type-admin' ); ?>" class="title_link-url-type" data-value="<?php echo network_admin_url(); ?>" type="radio" name="ub_ab_prev[<?php echo $menu->id ?>][url]"  value="admin_url" <?php checked( $menu->link_type, 'admin_url' ); ?>>
                            <label for="<?php echo $menu->get_field_id( 'title_link-url-type-admin' ); ?>"><?php _e( 'Site Admin area', 'ub' ); ?></label><br>

                            <input id="<?php echo $menu->get_field_id( 'title_link-url-type-url' ); ?>" class="title_link-this_url-switch title_link-url-type" class="title_link-this_url-switch title_link-url-type" data-value="<?php echo $menu->link_url; ?>" type="radio" name="ub_ab_prev[<?php echo $menu->id ?>][url]" value="url"  <?php checked( $menu->link_type, 'url' ); ?>>
                            <label for="<?php echo $menu->get_field_id( 'title_link-url-type-url' ); ?>"><?php _e( 'This URL:', 'ub' ); ?></label>

                            <input type="text" class="title_link-this_url"  size="48" name="ub_ab_prev[<?php echo $menu->id ?>][url]" value="<?php echo $menu->link_url ?>" <?php echo in_array( $menu->link_type, array( 'site_url', 'admin_url', 'network_site_url' ) ) ? "disabled='disabled'" : ''; ?> ><br>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Open in new window?', 'ub' ); ?></th>
                        <td>
                            <input type="checkbox" name="ub_ab_prev[<?php echo $menu->id ?>][target]"  <?php  checked( $menu->target, '_blank' ); ?> >
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Select User Roles allowed to see menu', 'ub' ); ?></th>
                        <td>
                        <?php UB_Admin_Bar_Forms::create_submenu_roles( $menu ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Add new link', 'ub' ); ?></th>
                        <td>
                            <?php UltimateBrandingAdmin::render( UB_Admin_Bar::NAME, '_add_new_links', array(
								'menu' => $menu,
							)); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Configure Links', 'ub' ); ?>
                        </th>
                        <td>

                            <div class="postbox-container">
                                <div class="meta-box-sortables submenu-box-sortables ui-sortable">
                                        <?php UltimateBrandingAdmin::render( UB_Admin_Bar::NAME, '_sub_menus', array(
											'menu' => $menu,
										)) ?>
                                </div>
                            </div>
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

<?php $order++; endforeach; ?>