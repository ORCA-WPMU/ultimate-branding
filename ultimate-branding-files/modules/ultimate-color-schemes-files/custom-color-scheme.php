/* General */

html, body {
background: <?php echo ub_get_option( 'ucs_background_color', '#f1f1f1' ); ?>; }

/* Links */

a {
color: <?php echo ub_get_option( 'ucs_default_link_color', '#45B29D' ); ?>; }
a:hover, a:active, a:focus {
color: <?php echo ub_get_option( 'ucs_default_link_hover_color', '#E27A3F' ); ?>;
}

#rightnow a:hover, #media-upload a.del-link:hover, div.dashboard-widget-submit input:hover, .subsubsub a:hover, .subsubsub a.current:hover, .ui-tabs-nav a:hover, .plugins .inactive a:hover {
color: <?php echo ub_get_option( 'ucs_default_link_hover_color', '#E27A3F' ); ?>;
}

table.widefat span.delete a, table.widefat span.trash a, table.widefat span.spam a, .plugins a.delete, #all-plugins-table .plugins a.delete, #search-plugins-table .plugins a.delete, .submitbox .submitdelete, #media-items a.delete, #media-items a.delete-permanently, #nav-menu-footer .menu-delete{
color: <?php echo ub_get_option( 'ucs_delete_trash_spam_link_color', '#DF5A49' ); ?>;
}

table.widefat span.delete a:hover, table.widefat span.trash a:hover, table.widefat span.spam a:hover, .plugins a.delete:hover, #all-plugins-table .plugins a.delete:hover, #search-plugins-table .plugins a.delete:hover, .submitbox .submitdelete:hover, #media-items a.delete:hover, #media-items a.delete-permanently:hover, #nav-menu-footer .menu-delete:hover{
color: <?php echo ub_get_option( 'ucs_delete_trash_spam_link_hover_color', '#E27A3F' ); ?>;
}

.plugins .inactive a{
color: <?php echo ub_get_option( 'ucs_inactive_plugins_color', '#888' ); ?>;
}

/* Forms */

input[type=checkbox]:checked:before {
color: <?php echo ub_get_option( 'ucs_checkbox_radio_color', '#45B29D' ); ?>; }

input[type=radio]:checked:before {
background: <?php echo ub_get_option( 'ucs_checkbox_radio_color', '#45B29D' ); ?>; }

.wp-core-ui input[type="reset"]:hover, .wp-core-ui input[type="reset"]:active {
color: <?php echo ub_get_option( 'ucs_checkbox_radio_color', '#45B29D' ); ?>; }

/* Core UI */

.wp-core-ui .button-primary {
background: <?php echo ub_get_option( 'ucs_primary_button_background_color', '#334D5C' ); ?>;
border-color: <?php echo ub_get_option( 'ucs_primary_button_background_color', '#334D5C' ); ?>;
color: <?php echo ub_get_option( 'ucs_primary_button_text_color', '#ffffff' ); ?>;
-webkit-box-shadow: inset 0 1px 0 <?php echo ub_get_option( 'ucs_primary_button_background_color', '#334D5C' ); ?>, 0 1px 0 rgba(0, 0, 0, 0.15);
box-shadow: inset 0 1px 0 <?php echo ub_get_option( 'ucs_primary_button_background_color', '#334D5C' ); ?>, 0 1px 0 rgba(0, 0, 0, 0.15); }

.wp-core-ui .button-primary:hover, .wp-core-ui .button-primary:focus {
background: <?php echo ub_get_option( 'ucs_primary_button_hover_background_color', '#EFC94C' ); ?>;
border-color: <?php echo ub_get_option( 'ucs_primary_button_hover_background_color', '#EFC94C' ); ?>;
color: <?php echo ub_get_option( 'ucs_primary_button_hover_text_color', '#fff' ); ?>;
-webkit-box-shadow: inset 0 1px 0 <?php echo ub_get_option( 'ucs_primary_button_hover_background_color', '#EFC94C' ); ?>, 0 1px 0 rgba(0, 0, 0, 0.15);
box-shadow: inset 0 1px 0 <?php echo ub_get_option( 'ucs_primary_button_hover_background_color', '#EFC94C' ); ?>, 0 1px 0 rgba(0, 0, 0, 0.15); }

.wp-core-ui .button-primary:active {
background: <?php echo ub_get_option( 'ucs_primary_button_background_color', '#334D5C' ); ?>;
border-color: <?php echo ub_get_option( 'ucs_primary_button_background_color', '#334D5C' ); ?>;
color: <?php echo ub_get_option( 'ucs_primary_button_text_color', '#ffffff' ); ?>;
-webkit-box-shadow: inset 0 2px 5px -3px rgba(0, 0, 0, 0.5);
box-shadow: inset 0 2px 5px -3px rgba(0, 0, 0, 0.5); }

.wp-core-ui .button-primary[disabled], .wp-core-ui .button-primary:disabled, .wp-core-ui .button-primary.button-primary-disabled,
{
color: <?php echo ub_get_option( 'ucs_disabled_button_text_color', '#000' ); ?> !important;
background: <?php echo ub_get_option( 'ucs_disabled_button_background_color', '#cccccc' ); ?> !important;
border-color: <?php echo ub_get_option( 'ucs_disabled_button_background_color', '#cccccc' ); ?> !important;
text-shadow: none !important;
}

/* List tables */

.wrap .add-new-h2:hover, #add-new-comment a:hover, .tablenav .tablenav-pages a:hover, .tablenav .tablenav-pages a:focus {
color: white;
background-color: <?php echo ub_get_option( 'ucs_table_list_hover_color', '#45B29D' ); ?>; }

.view-switch a.current:before {
color: <?php echo ub_get_option( 'ucs_table_view_switch_icon_color', '#45B29D' ); ?>; }

.view-switch a:hover:before {
color: <?php echo ub_get_option( 'ucs_table_view_switch_icon_hover_color', '#d46f15' ); ?>; }

.column-comments .post-com-count-approved:focus:after, .column-comments .post-com-count-approved:hover:after, .column-response .post-com-count-approved:focus:after, .column-response .post-com-count-approved:hover:after {
border-top-color: <?php echo ub_get_option( 'ucs_table_post_comment_icon_color', '#45B29D' ); ?>; }

.column-comments .post-com-count-approved:focus .comment-count-approved, .column-comments .post-com-count-approved:hover .comment-count-approved, .column-response .post-com-count-approved:focus .comment-count-approved, .column-response .post-com-count-approved:hover .comment-count-approved {
color: white;
background-color: <?php echo ub_get_option( 'ucs_table_post_comment_icon_color', '#45B29D' ); ?>; }

.column-comments .post-com-count-approved:after, .column-comments .post-com-count-no-comments:after, .column-response .post-com-count-approved:after, .column-response .post-com-count-no-comments:after:after {
border-top-color: <?php echo ub_get_option( 'ucs_table_post_comment_strong_icon_color', '#d46f15' ); ?>; }

.column-comments .comment-count-approved, .column-comments .comment-count-no-comments, .column-response .comment-count-approved, .column-response .comment-count-no-comments {
background-color: <?php echo ub_get_option( 'ucs_table_post_comment_strong_icon_color', '#d46f15' ); ?>; }

.alt,
.alternate,
.striped>tbody>:nth-child(odd),
ul.striped>:nth-child(odd){
background-color: <?php echo ub_get_option( 'ucs_table_alternate_row_color', '#E5ECF0' ); ?>;
}

/* Admin Menu */

#adminmenuback, #adminmenuwrap, #adminmenu {
background: <?php echo ub_get_option( 'ucs_admin_menu_background_color', '#45B29D' ); ?>; }

#adminmenu a {
color: <?php echo ub_get_option( 'ucs_admin_menu_link_color', '#FFFFFF' ); ?>; }

#adminmenu a:hover, #adminmenu li.menu-top:hover, #adminmenu li.opensub > a.menu-top, #adminmenu li > a.menu-top:focus {
color: <?php echo ub_get_option( 'ucs_admin_menu_link_hover_color', '#FFFFFF' );?>;
background-color: <?php echo ub_get_option( 'ucs_admin_menu_link_hover_background_color', '#334D5C' );?>; }

#adminmenu div.wp-menu-image:before {
color: <?php echo ub_get_option( 'ucs_admin_menu_icons_color', '#FFF' ); ?>; }


#adminmenu li.menu-top:hover div.wp-menu-image:before, #adminmenu li.opensub > a.menu-top div.wp-menu-image:before {
color: <?php echo ub_get_option( 'ucs_admin_menu_icons_color', '#FFF' ); ?>; }

/* Active tabs use a bottom border color that matches the page background color. */

.about-wrap h2 .nav-tab-active, .nav-tab-active, .nav-tab-active:hover {
border-bottom-color: <?php echo ub_get_option( 'ucs_background_color', '#f1f1f1' ); ?>; }

/* Admin Menu: submenu */
#adminmenu .wp-submenu, #adminmenu .wp-has-current-submenu .wp-submenu, #adminmenu .wp-has-current-submenu.opensub .wp-submenu, .folded #adminmenu .wp-has-current-submenu .wp-submenu, #adminmenu a.wp-has-current-submenu:focus + .wp-submenu {
background: <?php echo ub_get_option( 'ucs_admin_menu_submenu_background_color', '#334D5C' );?>; }

#adminmenu li.wp-has-submenu.wp-not-current-submenu.opensub:hover:after {
border-right-color: <?php echo ub_get_option( 'ucs_admin_menu_submenu_background_color', '#334D5C' );?>; }

#adminmenu .wp-submenu a, #adminmenu .wp-has-current-submenu .wp-submenu a, .folded #adminmenu .wp-has-current-submenu .wp-submenu a, #adminmenu a.wp-has-current-submenu:focus + .wp-submenu a, #adminmenu .wp-has-current-submenu.opensub .wp-submenu a, #adminmenu .wp-submenu .wp-submenu-head {
color: <?php echo ub_get_option( 'ucs_admin_menu_submenu_link_color', '#cbc5d3' );?>; }

#adminmenu .wp-submenu a:focus, #adminmenu .wp-submenu a:hover, #adminmenu .wp-has-current-submenu .wp-submenu a:focus, #adminmenu .wp-has-current-submenu .wp-submenu a:hover, .folded #adminmenu .wp-has-current-submenu .wp-submenu a:focus, .folded #adminmenu .wp-has-current-submenu .wp-submenu a:hover, #adminmenu a.wp-has-current-submenu:focus + .wp-submenu a:focus, #adminmenu a.wp-has-current-submenu:focus + .wp-submenu a:hover, #adminmenu .wp-has-current-submenu.opensub .wp-submenu a:focus, #adminmenu .wp-has-current-submenu.opensub .wp-submenu a:hover {
color: <?php echo ub_get_option( 'ucs_admin_menu_submenu_link_hover_color', '#fff' );?>; }

/* Admin Menu: current */
#adminmenu .wp-submenu li.current a, #adminmenu a.wp-has-current-submenu:focus + .wp-submenu li.current a, #adminmenu .wp-has-current-submenu.opensub .wp-submenu li.current a {
color: <?php echo ub_get_option( 'ucs_admin_menu_current_link_color', '#FFFFFF' );?>; }

#adminmenu .wp-submenu li.current a:hover, #adminmenu .wp-submenu li.current a:focus, #adminmenu a.wp-has-current-submenu:focus + .wp-submenu li.current a:hover, #adminmenu a.wp-has-current-submenu:focus + .wp-submenu li.current a:focus, #adminmenu .wp-has-current-submenu.opensub .wp-submenu li.current a:hover, #adminmenu .wp-has-current-submenu.opensub .wp-submenu li.current a:focus {
color: <?php echo ub_get_option( 'ucs_admin_menu_current_link_hover_color', '#FFFFFF' );?>; }

ul#adminmenu a.wp-has-current-submenu:after, ul#adminmenu > li.current > a.current:after {
border-right-color: <?php echo ub_get_option( 'ucs_admin_menu_current_background_color', '#EFC94C' );?>; }

#adminmenu li.current a.menu-top, #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, #adminmenu li.wp-has-current-submenu .wp-submenu .wp-submenu-head, .folded #adminmenu li.current.menu-top {
color: <?php echo ub_get_option( 'ucs_admin_menu_current_link_color', '#FFFFFF' );?>;
background: <?php echo ub_get_option( 'ucs_admin_menu_current_background_color', '#EFC94C' );?>; }

#adminmenu li.wp-has-current-submenu div.wp-menu-image:before {
color: <?php echo ub_get_option( 'ucs_admin_menu_current_icons_color', '#FFF' );?>; }

/* Admin Menu: bubble */

#adminmenu .awaiting-mod, #adminmenu .update-plugins {
color: <?php echo ub_get_option( 'ucs_admin_menu_bubble_text_color', '#fff' );?>;
background: <?php echo ub_get_option( 'ucs_admin_menu_bubble_background_color', '#EFC94C' );?>; }

#adminmenu li.current a .awaiting-mod, #adminmenu li a.wp-has-current-submenu .update-plugins, #adminmenu li:hover a .awaiting-mod, #adminmenu li.menu-top:hover > a .update-plugins {
color: <?php echo ub_get_option( 'ucs_admin_menu_current_link_color', '#FFFFFF' );?>;
background: <?php echo ub_get_option( 'ucs_admin_menu_current_background_color', '#EFC94C' );?>; }

/* Admin Menu: collapse button */

#collapse-menu {
color: <?php echo ub_get_option( 'ucs_admin_menu_current_link_color', '#FFFFFF' );?>; }

#collapse-menu:hover {
color: <?php echo ub_get_option( 'ucs_admin_menu_current_link_hover_color', '#FFFFFF' );?>; }

#collapse-button div:after {
color: <?php echo ub_get_option( 'ucs_admin_menu_current_icons_color', '#FFF' );?>; }

#collapse-menu:hover #collapse-button div:after {
color: <?php echo ub_get_option( 'ucs_admin_menu_current_icons_color', '#FFF' );?>; }

/* Admin Bar */

#wpadminbar {
color: <?php echo ub_get_option( 'ucs_admin_bar_text_color', '#FFF' );?>;
background: <?php echo ub_get_option( 'ucs_admin_bar_background_color', '#45B29D' );?>; }

#wpadminbar .ab-item, #wpadminbar a.ab-item, #wpadminbar > #wp-toolbar span.ab-label, #wpadminbar > #wp-toolbar span.noticon {
color: <?php echo ub_get_option( 'ucs_admin_bar_text_color', '#FFF' );?>; }

#wpadminbar .ab-icon, #wpadminbar .ab-icon:before, #wpadminbar .ab-item:before, #wpadminbar .ab-item:after {
color: <?php echo ub_get_option( 'ucs_admin_bar_icon_color', '#FFF' );?>; }

#wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar-nojs .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item {
color: #fff;
background: <?php echo ub_get_option( 'ucs_admin_bar_item_hover_background_color', '#334D5C' );?>; }

#wpadminbar .ab-top-menu>li.hover>.ab-item, #wpadminbar.nojq .quicklinks .ab-top-menu>li>.ab-item:focus, #wpadminbar:not(.mobile) .ab-top-menu>li:hover>.ab-item, #wpadminbar:not(.mobile) .ab-top-menu>li>.ab-item:focus{
    color: <?php echo ub_get_option( 'ucs_admin_bar_item_hover_focus_color', '#334D5C' ); ?>;
    background: <?php echo ub_get_option( 'ucs_admin_bar_item_hover_focus_background', '#334D5C' ); ?>;
}

#wpadminbar > #wp-toolbar li:hover span.ab-label,
#wpadminbar > #wp-toolbar li.hover span.ab-label,
#wpadminbar > #wp-toolbar a:focus span.ab-label,
#wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar-nojs .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item{
color: <?php echo ub_get_option( 'ucs_admin_bar_item_hover_text_color', '#FFF' );?>; }

#wpadminbar li:hover .ab-icon:before,
#wpadminbar li:hover .ab-item:before,
#wpadminbar li:hover .ab-item:after,
#wpadminbar li:hover #adminbarsearch:before {
color: <?php echo ub_get_option( 'ucs_admin_bar_item_hover_text_color', '#FFF' );?> !important; }

/* Admin Bar: submenu */

#wpadminbar .menupop .ab-sub-wrapper {
background: <?php echo ub_get_option( 'ucs_admin_bar_item_hover_background_color', '#334D5C' );?>; }

#wpadminbar ul.ab-submenu li a:hover,
#wpadminbar .quicklinks .menupop ul.ab-sub-secondary,
#wpadminbar .quicklinks .menupop ul.ab-sub-secondary .ab-submenu {
background: <?php echo ub_get_option( 'ucs_admin_bar_item_hover_background_color', '#334D5C' );?>;
color: <?php echo ub_get_option( 'ucs_admin_bar_item_hover_text_color', '#FFF' );?> !important;}

#wpadminbar .ab-submenu .ab-item, #wpadminbar .quicklinks .menupop ul li a, #wpadminbar .quicklinks .menupop.hover ul li a, #wpadminbar-nojs .quicklinks .menupop:hover ul li a {
color: <?php echo ub_get_option( 'ucs_admin_bar_submenu_icon_color', '#ece6f6' );?>; }

#wpadminbar .quicklinks li .blavatar, #wpadminbar .menupop .menupop > .ab-item:before {
color: <?php echo ub_get_option( 'ucs_admin_bar_submenu_icon_color', '#ece6f6' );?>;
}

#wpadminbar .quicklinks .menupop ul li a:hover, #wpadminbar .quicklinks .menupop ul li a:focus, #wpadminbar .quicklinks .menupop ul li a:hover strong, #wpadminbar .quicklinks .menupop ul li a:focus strong, #wpadminbar .quicklinks .menupop.hover ul li a:hover, #wpadminbar .quicklinks .menupop.hover ul li a:focus, #wpadminbar.nojs .quicklinks .menupop:hover ul li a:hover, #wpadminbar.nojs .quicklinks .menupop:hover ul li a:focus, #wpadminbar li:hover .ab-icon:before, #wpadminbar li:hover .ab-item:before, #wpadminbar li a:focus .ab-icon:before, #wpadminbar li .ab-item:focus:before, #wpadminbar li.hover .ab-icon:before, #wpadminbar li.hover .ab-item:before, #wpadminbar li:hover .ab-item:after, #wpadminbar li.hover .ab-item:after, #wpadminbar li:hover #adminbarsearch:before,
#wpadminbar .quicklinks li a:hover .blavatar, #wpadminbar .menupop .menupop > .ab-item:hover:before{
color: <?php echo ub_get_option( 'ucs_admin_bar_text_color', '#FFF' );?>; }

/* Admin Bar: search */

#wpadminbar #adminbarsearch:before {
color: <?php echo ub_get_option( 'ucs_admin_bar_submenu_icon_color', '#ece6f6' );?>; }

#wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary > #wp-admin-bar-search #adminbarsearch input.adminbar-input:focus {
color: <?php echo ub_get_option( 'ucs_admin_bar_text_color', '#FFF' );?>;
background: <?php echo ub_get_option( 'ucs_admin_bar_item_hover_background_color', '#334D5C' );?>; }

#wpadminbar #adminbarsearch .adminbar-input::-webkit-input-placeholder {
color: white;
opacity: 0.7; }

#wpadminbar #adminbarsearch .adminbar-input:-moz-placeholder {
color: white;
opacity: 0.7; }

#wpadminbar #adminbarsearch .adminbar-input::-moz-placeholder {
color: white;
opacity: 0.7; }

#wpadminbar #adminbarsearch .adminbar-input:-ms-input-placeholder {
color: white;
opacity: 0.7; }

/* Admin Bar: My Account */

#wpadminbar #wp-admin-bar-user-info .display-name {
color: <?php echo ub_get_option( 'ucs_admin_bar_text_color', '#FFF' );?>; }

#wpadminbar #wp-admin-bar-user-info a:hover .display-name {
color: <?php echo ub_get_option( 'ucs_admin_bar_text_color', '#FFF' );?>; }

#wpadminbar #wp-admin-bar-user-info .username {
color: <?php echo ub_get_option( 'ucs_admin_bar_text_color', '#FFF' );?>; }

/* Media Uploader */

.media-item .bar, .media-progress-bar div {
background-color: <?php echo ub_get_option( 'ucs_admin_media_progress_bar_color', '#334D5C' );?>; }

.details.attachment {
box-shadow: 0 0 0 1px white, 0 0 0 5px <?php echo ub_get_option( 'ucs_admin_media_selected_attachment_color', '#334D5C' );?>; }

.attachment.details .check {
background-color: <?php echo ub_get_option( 'ucs_admin_media_selected_attachment_color', '#334D5C' );?>;
box-shadow: 0 0 0 1px white, 0 0 0 2px <?php echo ub_get_option( 'ucs_admin_media_selected_attachment_color', '#334D5C' );?>; }

/* Themes */

.theme-browser .theme.active .theme-name, .theme-browser .theme.add-new-theme:hover:after {
background: <?php echo ub_get_option( 'ucs_admin_active_theme_background_color', '#334D5C' );?>; }

.theme-browser .theme.add-new-theme:hover span:after {
color: <?php echo ub_get_option( 'ucs_admin_active_theme_background_color', '#334D5C' );?>; }

.theme-overlay .theme-header .close:hover, .theme-overlay .theme-header .right:hover, .theme-overlay .theme-header .left:hover {
background: <?php echo ub_get_option( 'ucs_admin_active_theme_background_color', '#334D5C' );?>; }

.theme-browser .theme.active .theme-actions{
background: <?php echo ub_get_option( 'ucs_admin_active_theme_actions_background_color', '#45B29D' );?>;
}

.theme-browser .theme .more-details{
background: <?php echo ub_get_option( 'ucs_admin_active_theme_details_background_color', '#45B29D' );?>;
text-shadow: none;
}

/* Thickbox: Plugin information */

#sidemenu a.current {
background: <?php echo ub_get_option( 'ucs_background_color', '#f1f1f1' ); ?>;
border-bottom-color: <?php echo ub_get_option( 'ucs_background_color', '#f1f1f1' ); ?>; }

#plugin-information .action-button {
background: <?php echo ub_get_option( 'ucs_admin_menu_background_color', '#45B29D' ); ?>; }

.plugins .active th.check-column{
    border-left: 4px solid <?php echo ub_get_option( 'ucs_admin_active_plugin_border_color', '#EFC94C' ); ?>;
}