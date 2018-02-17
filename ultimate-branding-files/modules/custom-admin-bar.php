<?php

/*
  Plugin Name: Admin Bar
  Plugin URI: http://premium.wpmudev.org/project/custom-admin-bar
  Description: Adds a custom drop-down entry to your admin bar.
  Version: 1.6.1
  Author: Barry (Incsub), Ve Bailovity (Incsub), Marko Miljus (Incsub), Sam Najian (Incsub)
  Author URI: http://premium.wpmudev.org
  WDP ID: 238

  Copyright 2009-2017 Incsub (http://incsub.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
  the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once( ub_files_dir( 'modules/custom-admin-bar-files/inc/UB_Admin_Bar.php' ) );
require_once( ub_files_dir( 'modules/custom-admin-bar-files/inc/UB_Admin_Bar_Menu.php' ) );
require_once( ub_files_dir( 'modules/custom-admin-bar-files/inc/UB_Admin_Bar_Forms.php' ) );
require_once( ub_files_dir( 'modules/custom-admin-bar-files/inc/UB_Admin_Bar_Tab.php' ) );

$ub_wdcab_adminpages = new UB_Admin_Bar_Tab();
