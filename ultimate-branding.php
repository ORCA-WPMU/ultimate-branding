<?php
/*
Plugin Name: Ultimate Branding
Plugin URI: https://premium.wpmudev.org/project/ultimate-branding/
Description: A complete white-label and branding solution for multisite. Login images, favicons, remove WordPress links and branding, and much more.
Author: WPMU DEV
Version: 1.9.5.1
Author URI: http://premium.wpmudev.org/
Text_domain: ub
WDP ID: 9135

Copyright 2009-2018 Incsub (http://incsub.com)

Lead Developer - Sam Najian (Incsub)

Contributors - Ve Bailovity (Incsub), Barry (Incsub), Andrew Billits, Ulrich Sossou, Marko Miljus, Joseph Fusco (Incsub), Marcin Pietrzak (Incsub)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Ultimate Branding Version
 */
$ub_version = null;

// Include the configuration library
require_once( 'ultimate-branding-files/includes/config.php' );
// Include the functions library
require_once( 'ultimate-branding-files/includes/functions.php' );
require_once( 'ultimate-branding-files/classes/class.ub.helper.php' );		  	  		 	 		 	 		

// Set up my location
set_ultimate_branding( __FILE__ );

/**
 * set ub Version
 */
function ub_set_ub_version() {
	global $ub_version;
	$data = get_plugin_data( __FILE__ );
	$ub_version = $data['Version'];
}

include_once( 'external/dash-notice/wpmudev-dash-notification.php' );

register_activation_hook( __FILE__, 'ub_register_activation_hook' );
