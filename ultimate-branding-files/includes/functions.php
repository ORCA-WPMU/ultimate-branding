<?php
// We initially need to make sure that this function exists, and if not then include the file that has it.
if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

function ub_get_url_valid_shema( $url ) {
	$valid_url = $url;
	$v_valid_url = parse_url( $url );
	if ( isset( $v_valid_url['scheme'] ) && $v_valid_url['scheme'] === 'https' ) {
		if ( ! is_ssl() ) {
			$valid_url = str_replace( 'https', 'http', $valid_url );
		}
	} else {
		if ( is_ssl() ) {
			$valid_url = str_replace( 'http', 'https', $valid_url );
		}
	}
	return $valid_url;
}

function ub_url( $extended ) {
	global $UB_url;
	return ub_get_url_valid_shema( $UB_url ) . $extended;
}

function ub_dir( $extended ) {
	global $UB_dir;
	return $UB_dir . $extended;
}

function ub_files_url( $extended ) {
	return ub_url( 'ultimate-branding-files/' . $extended );
}

function ub_files_dir( $extended ) {
	return ub_dir( 'ultimate-branding-files/' . $extended );
}

// modules loading code
function ub_is_active_module( $module ) {
	$modules = get_ub_activated_modules();
	return ( in_array( $module, array_keys( $modules ) ) );
}

function ub_get_option( $option, $default = false ) {
	if ( is_multisite() && function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'ultimate-branding/ultimate-branding.php' ) ) {
		return get_site_option( $option, $default );
	} else {
		return get_option( $option, $default );
	}
}

function ub_update_option( $option, $value = null ) {
	if ( is_multisite() && function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'ultimate-branding/ultimate-branding.php' ) ) {
		return update_site_option( $option, $value );
	} else {
		return update_option( $option, $value );
	}
}

function ub_delete_option( $option ) {
	if ( is_multisite() && function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'ultimate-branding/ultimate-branding.php' ) ) {
		return delete_site_option( $option );
	} else {
		return delete_option( $option );
	}
}

function get_ub_activated_modules() {
	$modules = ub_get_option( 'ultimatebranding_activated_modules', array() );
	/**
	 * Filter allow to turn on/off modules.
	 *
	 * @since 1.9.4
	 *
	 * @param array $modules Active modules array.
	 */
	$modules = apply_filters( 'ultimatebranding_activated_modules', $modules );
	return $modules;
}

function update_ub_activated_modules( $data ) {
	ub_update_option( 'ultimatebranding_activated_modules', $data );
}

function ub_load_single_module( $module ) {
	$modules = ub_get_modules_list( 'keys' );
	if ( in_array( $module, $modules ) ) {
		include_once( ub_files_dir( 'modules/' . $module ) );
	}

}

function ub_has_menu( $menuhook ) {
	global $submenu;
	$menu = (isset( $submenu['branding'] )) ? $submenu['branding'] : false;
	if ( is_array( $menu ) ) {
		foreach ( $menu as $key => $m ) {
			if ( $m[2] == $menuhook ) {
				return true;
			}
		}
	}
	// if we are still here then we didn't find anything
	return false;
}

/*
Function based on the function wp_upload_dir, which we can't use here because it insists on creating a directory at the end.
 */
function ub_wp_upload_url() {
	global $switched;

	$siteurl = get_option( 'siteurl' );
	$upload_path = get_option( 'upload_path' );
	$upload_path = trim( $upload_path );

	$main_override = is_multisite() && defined( 'MULTISITE' ) && is_main_site();

	if ( empty( $upload_path ) ) {
		$dir = WP_CONTENT_DIR . '/uploads';
	} else {
		$dir = $upload_path;
		if ( 'wp-content/uploads' == $upload_path ) {
			$dir = WP_CONTENT_DIR . '/uploads';
		} elseif ( 0 !== strpos( $dir, ABSPATH ) ) {
			// $dir is absolute, $upload_path is (maybe) relative to ABSPATH
			$dir = path_join( ABSPATH, $dir );
		}
	}

	if ( ! $url = get_option( 'upload_url_path' ) ) {
		if ( empty( $upload_path ) || ( 'wp-content/uploads' == $upload_path ) || ( $upload_path == $dir ) ) {
			$url = WP_CONTENT_URL . '/uploads'; } else { 			$url = trailingslashit( $siteurl ) . $upload_path; }
	}

	if ( defined( 'UPLOADS' ) && ! $main_override && ( ! isset( $switched ) || $switched === false ) ) {
		$dir = ABSPATH . UPLOADS;
		$url = trailingslashit( $siteurl ) . UPLOADS;
	}

	if ( defined( 'UPLOADS' ) && is_multisite() && ! $main_override && ( ! isset( $switched ) || $switched === false ) ) {
		if ( defined( 'BLOGUPLOADDIR' ) ) {
			$dir = untrailingslashit( BLOGUPLOADDIR ); }
		$url = str_replace( UPLOADS, 'files', $url );
	}

	$bdir = $dir;
	$burl = $url;

	return $burl;
}

function ub_wp_upload_dir() {
	global $switched;

	$siteurl = get_option( 'siteurl' );
	$upload_path = get_option( 'upload_path' );
	$upload_path = trim( $upload_path );

	$main_override = is_multisite() && defined( 'MULTISITE' ) && is_main_site();

	if ( empty( $upload_path ) ) {
		$dir = WP_CONTENT_DIR . '/uploads';
	} else {
		$dir = $upload_path;
		if ( 'wp-content/uploads' == $upload_path ) {
			$dir = WP_CONTENT_DIR . '/uploads';
		} elseif ( 0 !== strpos( $dir, ABSPATH ) ) {
			// $dir is absolute, $upload_path is (maybe) relative to ABSPATH
			$dir = path_join( ABSPATH, $dir );
		}
	}

	if ( ! $url = get_option( 'upload_url_path' ) ) {
		if ( empty( $upload_path ) || ( 'wp-content/uploads' == $upload_path ) || ( $upload_path == $dir ) ) {
			$url = WP_CONTENT_URL . '/uploads'; } else { 			$url = trailingslashit( $siteurl ) . $upload_path; }
	}

	if ( defined( 'UPLOADS' ) && ! $main_override && ( ! isset( $switched ) || $switched === false ) ) {
		$dir = ABSPATH . UPLOADS;
		$url = trailingslashit( $siteurl ) . UPLOADS;
	}

	if ( defined( 'UPLOADS' ) && is_multisite() && ! $main_override && ( ! isset( $switched ) || $switched === false ) ) {
		if ( defined( 'BLOGUPLOADDIR' ) ) {
			$dir = untrailingslashit( BLOGUPLOADDIR ); }
		$url = str_replace( UPLOADS, 'files', $url );
	}

	$bdir = $dir;
	$burl = $url;

	return $bdir;
}

/**
 * Returns option name from module name.
 */
function ub_get_option_name_by_module( $module ) {
	return apply_filters( 'ultimate_branding_get_option_name', 'unknown', $module );
}

/**
 * show deprecated module information
 *
 * @since 1.8.7
 */
function ub_deprecated_module( $deprecated, $substitution, $tab ) {
	$url = is_network_admin()? network_admin_url( 'admin.php' ):admin_url( 'admin.php' );
	$url = add_query_arg(
		array(
			'page' => 'branding',
			'tab' => $tab,
		),
		$url
	);
	echo '<div class="ub-deprecated-module"><p>';
	printf(
		__( '%s module is deprecated. Please use %s module.', 'ub' ),
		sprintf( '<b>%s</b>', esc_html( $deprecated ) ),
		sprintf( '<b><a href="%s">%s</a></b>', esc_url( $url ), esc_html( $substitution ) )
	);
	echo '</p></div>';
}

/**
 * register_activation_hook
 *
 * @since 1.8.8
 */
function ub_register_activation_hook() {
	$version = ub_get_option( 'ub_version' );
	$compare = version_compare( $version, '1.8.8', '<' );
	/**
	 * Turn off plugin "HTML E-mail Template" and turn on module.
	 *
	 * @since 1.8.8
	 */
	if ( 0 < $compare ) {
		/**
		 * Turn off "HTML E-mail Templates" plugin and turn on "HTML E-mail
		 * Templates" module instead.
		 */
		$turn_on_module_htmlemail = false;
		if ( is_network_admin() ) {
			$plugins = get_site_option( 'active_sitewide_plugins' );
			if ( array_key_exists( 'htmlemail/htmlemail.php', $plugins ) ) {
				unset( $plugins['htmlemail/htmlemail.php'] );
				update_site_option( 'active_sitewide_plugins', $plugins );
				$turn_on_module_htmlemail = true;
			}
		} else {
			$plugins = get_option( 'active_plugins' );
			if ( in_array( 'htmlemail/htmlemail.php', $plugins ) ) {
				$new = array();
				foreach ( $plugins as $plugin ) {
					if ( 'htmlemail/htmlemail.php' == $plugin ) {
						$turn_on_module_htmlemail = true;
						continue;
					}
					$new[] = $plugin;
				}
				update_option( 'active_plugins', $new );
			}
		}
		if ( $turn_on_module_htmlemail ) {
			$uba = new UltimateBrandingAdmin();
			$uba->activate_module( 'htmlemail.php' );
		}
	}
	$file = dirname( dirname( dirname( __FILE__ ) ) ).'/ultimate-branding.php';
	$data = get_plugin_data( $file );
	ub_update_option( 'ub_version', $data['Version'] );
}
/**
 * Set required Ultimate Branding defaults.
 *
 * @since 1.9.5
 */
function set_ultimate_branding( $base ) {
	global $UB_dir, $UB_url;
	/**
	 * Set UB_dir
	 */
	$UB_dir = plugin_dir_path( $base );
	if ( defined( 'WPMU_PLUGIN_DIR' ) && file_exists( WPMU_PLUGIN_DIR . '/' . basename( $base ) ) ) {
		$UB_dir = trailingslashit( WPMU_PLUGIN_DIR );
	}
	/**
	 * set $UB_url
	 */
	global $UB_url;
	$UB_url = plugin_dir_url( $base );
	if ( defined( 'WPMU_PLUGIN_URL' ) && defined( 'WPMU_PLUGIN_DIR' ) && file_exists( WPMU_PLUGIN_DIR . '/' . basename( $base ) ) ) {
		$UB_url = trailingslashit( WPMU_PLUGIN_URL );
	}
	/**
	 * include dir
	 */
	$include_dir = $UB_dir.'/ultimate-branding-files/classes';
	/**
	 * load files
	 */
	require_once( $include_dir . '/ubadmin.php' );
	if ( is_admin() ) {
		// Add in the contextual help
		require_once( $include_dir . '/class.help.php' );
		include_once( $include_dir . '/class.simple.options.php' );
		// Include the admin class
		$uba = new UltimateBrandingAdmin();
	} else {
		// Include the public class
		require_once( $include_dir . '/ubpublic.php' );
		$ubp = new UltimateBrandingPublic();
	}

	/**
	 * handle ajax
	 */
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		include_once( $include_dir . '/class.simple.options.php' );
		new simple_options;
	}

}
