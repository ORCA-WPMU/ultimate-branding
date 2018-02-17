<?php
// If we are on a campus install then we should be hiding some of the modules
if ( ! defined( 'UB_ON_CAMPUS' ) ) { define( 'UB_ON_CAMPUS', false ); }
// Allows the branding admin menus to be hidden on a single site install
if ( ! defined( 'UB_HIDE_ADMIN_MENU' ) ) { define( 'UB_HIDE_ADMIN_MENU', false ); }
// Allows the main blog to be changed from the default with an id of 1
if ( ! defined( 'UB_MAIN_BLOG_ID' ) ) { define( 'UB_MAIN_BLOG_ID', 1 ); }

/**
 * Modules list
 *
 * @since 1.9.4
 */
function ub_get_modules_list( $mode = 'full' ) {
	$modules = array(
		/*
        'admin-menu.php' => array(
            'module' => 'admin-menu.php',
            'tab' => 'admin-menu',
            'page_title' => __( 'Admin Menu Manager', 'ub' ),
        ),
         */
		'admin-footer-text.php' => array(
			'module' => 'admin-footer-text/admin-footer-text.php',
			'tab' => 'footer',
			'page_title' => __( 'Footer Content', 'ub' ),
			'title' => __( 'Admin Footer Content', 'ub' ),
		),
		'admin-help-content.php' => array(
			'module' => 'admin-help-content/admin-help-content.php',
			'tab' => 'help',
			'page_title' => __( 'Help Content', 'ub' ),
		),
		'custom-admin-bar.php' => array(
			'module' => 'custom-admin-bar/custom-admin-bar.php',
			'tab' => 'adminbar',
			'page_title' => __( 'Admin Bar', 'ub' ),
		),
		'admin-bar-logo.php' => array(
			'module' => 'admin-bar-logo.php',
			'tab' => 'adminbar',
			'page_title' => __( 'Admin Bar', 'ub' ),
			'title' => __( 'Admin Bar Logo', 'ub' ),
		),
		'custom-dashboard-welcome.php' => array(
			'module' => 'custom-dashboard-welcome.php',
			'tab' => 'widgets',
			'page_title' => __( 'Widgets', 'ub' ),
			'title' => __( 'Dashboard Welcome', 'ub' ),
		),
		'custom-email-from.php' => array(
			'module' => 'custom-email-from/custom-email-from.php',
			'tab' => 'from-email',
			'page_title' => __( 'E-mail From', 'ub' ),
		),
		'global-footer-content.php' => array(
			'module' => 'global-footer-content/global-footer-content.php',
			'tab' => 'footer',
			'page_title' => __( 'Footer Content', 'ub' ),
			'title' => __( 'Global Footer Content', 'ub' ),
		),
		'global-header-content.php' => array(
			'module' => 'global-header-content/global-header-content.php',
			'tab' => 'header',
			'page_title' => __( 'Header Content', 'ub' ),
		),
		'rebranded-meta-widget.php' => array(
			'module' => 'rebranded-meta-widget/rebranded-meta-widget.php',
			'tab' => 'widgets',
			'page_title' => __( 'Widgets', 'ub' ),
			'title' => __( 'Rebranded Meta Widget', 'ub' ),
		),
		'remove-dashboard-link-for-users-without-site.php' => array(
			'module' => 'remove-dashboard-link-for-users-without-site/remove-dashboard-link-for-users-without-site.php',
		),
		'remove-permalinks-menu-item.php' => array(
			'module' => 'remove-permalinks-menu-item/remove-permalinks-menu-item.php',
			'tab' => 'permalinks',
			'page_title' => __( 'Permalinks Menu', 'ub' ),
		),
		'remove-wp-dashboard-widgets.php' => array(
			'module' => 'remove-wp-dashboard-widgets/remove-wp-dashboard-widgets.php',
			'tab' => 'widgets',
			'page_title' => __( 'Widgets', 'ub' ),
			'title' => __( 'Remove WP Dashboard Widgets', 'ub' ),
		),
		'site-generator-replacement.php' => array(
			'module' => 'site-generator-replacement/site-generator-replacement.php',
			'tab' => 'sitegenerator',
			'page_title' => __( 'Site Generator', 'ub' ),
		),
		'site-wide-text-change.php' => array(
			'module' => 'site-wide-text-change/site-wide-text-change.php',
			'tab' => 'textchange',
			'page_title' => __( 'Network Wide Text Change', 'ub' ),
			'menu_title' => __( 'Text Change', 'ub' ),
		),
		'custom-admin-css.php' => array(
			'module' => 'custom-admin-css.php',
			'tab' => 'css',
			'menu_title' => __( 'CSS', 'ub' ),
			'page_title' => __( 'Cascading Style Sheets', 'ub' ),
		),
		'custom-login-css.php' => array(
			'module' => 'custom-login-css.php',
			'tab' => 'css',
			'page_title' => __( 'Cascading Style Sheets', 'ub' ),
			'menu_title' => __( 'CSS', 'ub' ),
		),
		'ultimate-color-schemes.php' => array(
			'module' => 'ultimate-color-schemes.php',
			'tab' => 'ultimate-color-schemes',
			'page_title' => __( 'Color Schemes', 'ub' ),
		),
		'admin-message.php' => array(
			'module' => 'admin-message.php',
			'tab' => 'admin-message',
			'page_title' => __( 'Admin Message', 'ub' ),
		),
		/**
		 * Images
		 */
		'favicons.php' => array(
			'module' => 'favicons.php',
			'tab' => 'images',
			'page_title' => __( 'Images', 'ub' ),
			'title' => __( 'Multisite Favicons', 'ub' ),
		),
		'login-image.php' => array(
			'module' => 'login-image/login-image.php',
			'tab' => 'images',
			'page_title' => __( 'Images', 'ub' ),
			'title' => __( 'Login Image', 'ub' ),
		),
		/**
		 * Images: Image upload size
		 *
		 * @since 1.9.2
		 */
		'image-upload-size.php' => array(
			'module' => 'image-upload-size.php',
			'tab' => 'images',
			'page_title' => __( 'Images', 'ub' ),
			'title' => __( 'Limit Image Upload Filesize', 'ub' ),
		),
		/**
		 * Email Template
		 *
		 * @since 1.8.4
		 */
		'htmlemail.php' => array(
			'module' => 'htmlemail',
			'tab' => 'htmlemail',
			'page_title' => __( 'Email Template', 'ub' ),
		),
		/**
		 * Custom Login Screen
		 *
		 * @since 1.8.5
		 */
		'custom-login-screen.php' => array(
			'module' => 'custom-login-screen.php',
			'tab' => 'login-screen',
			'page_title' => __( 'Login screen', 'ub' ),
		),
		/**
		 * Custom MS email content
		 *
		 * @since 1.8.6
		 */
		'custom-ms-register-emails.php' => array(
			'module' => 'custom-ms-register-emails.php',
			'network-only' => true,
			'tab' => 'custom-ms-register-emails',
			'page_title' => __( 'MultiSite Registration emails', 'ub' ),
			'menu_title' => __( 'Registration emails', 'ub' ),
		),
		/**
		 * Export - Import
		 *
		 * @since 1.8.6
		 */
		'export-import.php' => array(
			'module' => 'export-import.php',
			'tab' => 'export-import',
			'page_title' => __( 'Export & Import Ultimate Branding configuration', 'ub' ),
			'menu_title' => __( 'Export/Import', 'ub' ),
		),
		'admin-panel-tips/admin-panel-tips.php' => array(
			'module' => 'admin-panel-tip',
			'show-on-single' => true,
			'tab' => 'admin-panel-tips',
			'page_title' => __( 'Admin Panel Tips', 'ub' ),
			'menu_title' => __( 'Tips', 'ub' ),
		),
		/**
		 * Comments Control
		 *
		 * @since 1.8.6
		 */
		'comments-control.php' => array(
			'module' => 'comments-control.php',
			'network-only' => true,
			'tab' => 'comments-control',
			'page_title' => __( 'Comments Control', 'ub' ),
		),
		/**
		 * Dashboard Feeds
		 *
		 * @since 1.8.6
		 */
		'dashboard-feeds/dashboard-feeds.php' => array(
			'module' => 'dashboard-feeds',
			'network-only' => true,
			'tab' => 'dashboard-feeds',
			'page_title' => __( 'Dashboard Feeds', 'ub' ),
		),
		/**
		 * Link Manager
		 *
		 * @since 1.8.6
		 */
		'link-manager.php' => array(
			'module' => 'link-manager',
			'tab' => 'link-manager',
			'page_title' => __( 'Link Manager', 'ub' ),
		),
		/**
		 * Coming Soon Page & Maintenance Mode
		 *
		 * @since 1.9.1
		 */
		'maintenance/maintenance.php' => array(
			'module' => 'maintenance',
			'tab' => 'maintenance',
			'page_title' => __( 'Coming Soon Page & Maintenance Mode', 'ub' ),
			'menu_title' => __( 'Maintenance', 'ub' ),
		),
		/**
		 * Dashboard widgets
		 *
		 * @since 1.9.1
		 */
		'dashboard-text-widgets/dashboard-text-widgets.php' => array(
			'module' => 'dashboard-text-widgets',
			'tab' => 'dashboard-text-widgets',
			'page_title' => __( 'Dashboard Text Widgets', 'ub' ),
		),
	);
	apply_filters( 'ultimatebranding_available_modules', $modules );
	if ( 'keys' == $mode ) {
		$modules = array_keys( $modules );
		sort( $modules );
	}
	return $modules;
}

?>