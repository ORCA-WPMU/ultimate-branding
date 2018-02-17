<?php
/*
  Plugin Name: Remove WP Dashboard Link for users without site
  Plugin URI:
  Description: Removes "Dashboard" link from admin panel for users without site (in WP Multisite) and redirect page to the "Profile".
  Author: Marko Miljus (Incsub)
  Version: 1.0.1
  Author URI: http://premium.wpmudev.org/
 */
if ( is_multisite() ) {
	add_action( 'admin_menu', 'ub_rdluws_remove_wpms_dashboard_link' );

	function ub_rdluws_remove_wpms_dashboard_link() {

		$user_blogs = get_blogs_of_user( get_current_user_id() );

		if ( count( $user_blogs ) == 0 ) {
			remove_menu_page( 'index.php' );
			$current_url = ub_rdluws_get_admin_current_page_url();
			if ( preg_match( '/user\//', $current_url ) && ! preg_match( '/profile.php/', $current_url ) ) {
				wp_redirect( 'profile.php' );
			}
		}
	}

	function ub_rdluws_get_admin_current_page_url() {
		$pageURL = 'http';
		if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
			$pageURL .= 's';
		}
		$pageURL .= '://';
		if ( isset( $_SERVER['SERVER_PORT'] ) && $_SERVER['SERVER_PORT'] != '80' ) {
			$pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		} else {
			$pageURL .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}
		return $pageURL;
	}
}
?>