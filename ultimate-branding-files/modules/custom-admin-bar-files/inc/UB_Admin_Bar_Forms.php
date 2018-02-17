<?php

class UB_Admin_Bar_Forms {

	private static $_dashicons = array(
		'Admin Menu Icons' => array(
			'menu',
			'admin-site',
			'dashboard',
			'admin-media',
			'admin-page',
			'admin-comments',
			'admin-appearance',
			'admin-plugins',
			'admin-users',
			'admin-tools',
			'admin-settings',
			'admin-network',
			'admin-generic',
			'admin-home',
			'admin-collapse',
			'filter',
			'admin-customizer',
			'admin-multisite',
		),
		'Both Admin Menu and Post Formats' => array(
			'admin-links',
			'format-links',
			'admin-post',
			'format-standard',
		),
		'Post Format Icons' => array(
			'format-image',
			'format-gallery',
			'format-audio',
			'format-video',
			'format-chat',
			'format-status',
			'format-aside',
			'format-quote',
		),
		'Welcome Screen Icons' => array(
			'welcome-write-blog',
			'welcome-edit-page',
			'welcome-add-page',
			'welcome-view-site',
			'welcome-widgets-menus',
			'welcome-comments',
			'welcome-learn-more',
		),
		'Image Editing Icons' => array(
			'image-crop',
			'image-rotate',
			'image-rotate-left',
			'image-rotate-right',
			'image-flip-vertical',
			'image-flip-horizontal',
			'image-filter',
		),
		'Both Image Editing and TinyMCE' => array(
			'undo',
			'redo',
		),
		'TinyMCE Icons' => array(
			'editor-bold',
			'editor-italic',
			'editor-ul',
			'editor-ol',
			'editor-quote',
			'editor-alignleft',
			'editor-aligncenter',
			'editor-alignright',
			'editor-insertmore',
			'editor-spellcheck',
			'editor-distractionfree',
			'editor-expand',
			'editor-contract',
			'editor-kitchensink',
			'editor-underline',
			'editor-justify',
			'editor-textcolor',
			'editor-paste-word',
			'editor-paste-text',
			'editor-removeformatting',
			'editor-video',
			'editor-customchar',
			'editor-outdent',
			'editor-indent',
			'editor-help',
			'editor-strikethrough',
			'editor-unlink',
			'editor-rtl',
			'editor-break',
			'editor-code',
			'editor-paragraph',
			'editor-table',
		),
		'Post Icons' => array(
			'align-left',
			'align-right',
			'align-center',
			'align-none',
			'lock',
			'unlock',
			'calendar',
			'calendar-alt',
			'visibility',
			'hidden',
			'post-status',
			'edit',
			'post-trash',
			'trash',
			'sticky',
		),
		'Sorting' => array(
			'external',
			'arrow-up',
			'arrow-down',
			'arrow-left',
			'arrow-right',
			'arrow-up-alt',
			'arrow-down-alt',
			'arrow-left-alt',
			'arrow-right-alt',
			'arrow-up-alt2',
			'arrow-down-alt2',
			'arrow-left-alt2',
			'arrow-right-alt2',
			'leftright',
			'sort',
			'randomize',
			'list-view',
			'exerpt-view',
			'excerpt-view',
			'grid-view',
			'move',
		),
		'WPorg specific icons' => array(
			'hammer',
			'art',
			'migrate',
			'performance',
			'universal-access',
			'universal-access-alt',
			'tickets',
			'nametag',
			'clipboard',
			'heart',
			'megaphone',
			'schedule',
		),
		'Internal/Products' => array(
			'wordpress',
			'wordpress-alt',
			'pressthis',
			'update',
			'screenoptions',
			'cart',
			'feedback',
			'cloud',
			'translation',
		),
		'Taxonomies' => array(
			'tag',
			'category',
		),
		'Widget icons' => array(
			'archive',
			'tagcloud',
			'text',
		),
		'Media icons' => array(
			'media-archive',
			'media-audio',
			'media-code',
			'media-default',
			'media-document',
			'media-interactive',
			'media-spreadsheet',
			'media-text',
			'media-video',
			'playlist-audio',
			'playlist-video',
			'controls-play',
			'controls-pause',
			'controls-forward',
			'controls-skipforward',
			'controls-back',
			'controls-skipback',
			'controls-repeat',
			'controls-volumeon',
			'controls-volumeoff',
		),
		'Alerts/Notifications/Flags' => array(
			'yes',
			'no',
			'no-alt',
			'plus',
			'plus-alt',
			'plus-alt2',
			'minus',
			'dismiss',
			'marker',
			'star-filled',
			'star-half',
			'star-empty',
			'flag',
			'info',
			'warning',
		),
		'Social Icons' => array(
			'share',
			'share1',
			'share-alt',
			'share-alt2',
			'twitter',
			'rss',
			'email',
			'email-alt',
			'facebook',
			'facebook-alt',
			'networking',
			'googleplus',
		),
		'Misc/CPT' => array(
			'location',
			'location-alt',
			'camera',
			'images-alt',
			'images-alt2',
			'video-alt',
			'video-alt2',
			'video-alt3',
			'vault',
			'shield',
			'shield-alt',
			'sos',
			'search',
			'slides',
			'analytics',
			'chart-pie',
			'chart-bar',
			'chart-line',
			'chart-area',
			'groups',
			'businessman',
			'id',
			'id-alt',
			'products',
			'awards',
			'forms',
			'testimonial',
			'portfolio',
			'book',
			'book-alt',
			'download',
			'upload',
			'backup',
			'clock',
			'lightbulb',
			'microphone',
			'desktop',
			'laptop',
			'tablet',
			'smartphone',
			'phone',
			'smiley',
			'index-card',
			'carrot',
			'building',
			'store',
			'album',
			'palmtree',
			'tickets-alt',
			'money',
			'thumbs-up',
			'thumbs-down',
			'layout',
			'paperclip',
		),
	);
	/**
	 * Retrieves options
	 *
	 * @param bool $key
	 * @param string $pfx
	 *
	 * @return mixed|void
	 */
	public static function get_option( $key = false, $pfx = 'wdcab' ) {
		$opts = ub_get_option( $pfx );
		if ( ! $key ) {
			return $opts;
		}

		if ( isset( $opts[ $key ] ) ) {
			return $opts[ $key ];
		}

		return null;
	}

	/**
	 * Renders checkbox
	 *
	 * @param $name
	 * @param string $pfx
	 *
	 * @return string
	 */
	public static function create_checkbox( $name, $pfx = 'wdcab', $labels = array() ) {
		$value = intval( self::get_option( $name, $pfx ) );
		$defaults = array(
			'yes' => __( 'Yes', 'ub' ),
			'no' => __( 'No', 'ub' ),
		);
		$labels = wp_parse_args( $labels, $defaults );
		$content = '<ul>';
		$content .= sprintf(
			'<li><label><input type="radio" name="%s[%s]" id="%s-%s" value="%d" %s /> %s</label></li>',
			esc_attr( $pfx ),
			esc_attr( $name ),
			esc_attr( $name ),
			esc_attr( 'yes' ),
			1,
			checked( 1, $value, false ),
			$labels['yes']
		);
		$content .= sprintf(
			'<li><label><input type="radio" name="%s[%s]" id="%s-%s" value="%d" %s /> %s</label></li>',
			esc_attr( $pfx ),
			esc_attr( $name ),
			esc_attr( $name ),
			esc_attr( 'no' ),
			0,
			checked( 0, $value, false ),
			$labels['no']
		);
		$content .= '</ul>';
		return $content;
	}

	/**
	 * Creates enable box
	 */
	public static function create_enabled_box() {
		$labels = array(
			'yes' => __( 'Show custom entries', 'ub' ),
			'no' => __( 'Hide custom entries', 'ub' ),
		);
		echo self::create_checkbox( 'enabled', 'wdcab', $labels );
	}

	/**
	 * Creates enable box
	 */
	public static function create_show_box() {
		$labels = array(
			'yes' => __( 'Show to logged out', 'ub' ),
			'no' => __( 'Hide from logged out', 'ub' ),
		);
		echo self::create_checkbox( 'show_toolbar_for_non_logged', 'wdcab', $labels );
	}

	/**
	 * Renders disable/enable setting
	 */
	public static function create_disable_box() {
		$_menus   = array(
			'wp-logo'     => __( 'WordPress menu', 'ub' ),
			'site-name'   => __( 'Site menu', 'ub' ),
			'my-sites'    => __( 'My Sites', 'ub' ),
			'new-content' => __( 'Add New', 'ub' ),
			'comments'    => __( 'Comments', 'ub' ),
			'updates'     => __( 'Updates', 'ub' ),
		);
		$disabled = self::get_option( 'disabled_menus' );
		$disabled = is_array( $disabled ) ? $disabled : array();

		echo '<input type="hidden" name="wdcab[disabled_menus]" value="" />';
		foreach ( $_menus as $id => $lbl ) {
			$checked = in_array( $id, $disabled ) ? 'checked="checked"' : '';
			echo '' .
				"<input type='checkbox' name='wdcab[disabled_menus][]' id='wdcab-disabled_menus-{$id}' value='{$id}' {$checked}>" .
				'&nbsp;' .
				"<label for='wdcab-disabled_menus-{$id}'>{$lbl}</label>" .
				'<br />';
		}
	}

	/**
	 * Renders disable/enable setting
	 *
	 * @param string $name roles key
	 * @param string $pfx
	 */
	public static function create_roles_box( $name, $pfx = 'wdcab' ) {
		$roles = self::get_roles();

		$opt = self::get_option( $name, $pfx );

		$opt = is_array( $opt ) ? $opt : ( $opt === '' ?  array() : array_keys( $roles ) ) ;
		echo "<input type='hidden' name='{$pfx}[{$name}]' value='' />";
		if ( is_multisite() ) {
			$is_super_admin = current_user_can( 'manage_network' ) ? 'ub_adminbar_is_current_user' : '';
			$supericon = current_user_can( 'manage_network' ) ? sprintf( "<span class='dashicons dashicons-info' title='%s'></span", __( 'Current user has this role', 'ub' ) ) : '';
			$checked = in_array( 'super', $opt ) ? 'checked="checked"' : '';
			echo "<p><input type='checkbox' name='{$pfx}[{$name}][Super-Admin]' id='{$pfx}-{$name}-super' value='super' {$checked}>"
				. '&nbsp;'
				. "<label class='{$is_super_admin}' for='{$pfx}-{$name}-super'>Super-Admin&nbsp;{$supericon}</label></p>";
		}
		foreach ( $roles as $role_value => $role_name ) {
			$checked = in_array( $role_value, $opt ) ? 'checked="checked"' : '';
			$current_user_has_role = self::_current_user_has_role( $role_value );
			$is_current_user = $current_user_has_role ? 'ub_adminbar_is_current_user' : '';
			$icon = $current_user_has_role ? sprintf( "<span class='dashicons dashicons-info' title='%s'></span", __( 'Current user has this role', 'ub' ) ) : '';
			echo "<p><input type='checkbox' name='{$pfx}[{$name}][{$role_name}]' id='{$pfx}-{$name}-{$role_value}' value='{$role_value}' {$checked}>"
				. '&nbsp;'
				. "<label class='{$is_current_user}' for='{$pfx}-{$name}-{$role_value}'>{$role_name}&nbsp;{$icon}</label></p>";
		}
	}

	/**
	 * Checks if current user has $role
	 *
	 * @param $role
	 *
	 * @since 1.8.1.2
	 *
	 * @return bool
	 */
	private static function _current_user_has_role( $role ) {
		global $current_user;
		return in_array( $role, (array) $current_user->roles );
	}

	/**
	 * Renders submenu roles
	 *
	 * @param UB_Admin_Bar_Menu $menu |null
	 */
	public static function create_submenu_roles( $menu = null ) {
		$roles = self::get_roles();

		if ( $menu instanceof UB_Admin_Bar_Menu ) {
			$opts = $menu->menu->menu_roles;
			$opts = is_array( $opts ) ? $opts : $roles;
			if ( is_multisite() ) {
				$sname    = "ub_ab_prev[{$menu->id}][menu_roles][super]";
				$sid      = "ub_ab_prev_{$menu->id}_menu_roles_super";
				$is_super_admin = current_user_can( 'manage_network' ) ? 'ub_adminbar_is_current_user' : '';
				$supericon = current_user_can( 'manage_network' ) ? sprintf( "&nbsp;<span class='dashicons dashicons-info' title='%s'></span", __( 'Current user has this role', 'ub' ) ) : '';
				$schecked = array_key_exists( 'super', $opts ) ? 'checked="checked"' : '';
				$label_class = current_user_can( 'manage_network' ) ? 'ub_adminbar_is_current_user' : '';
?>
                <p>
                    <input id="<?php echo $sid ?>" type='checkbox' name='<?php echo $sname ?>' <?php echo $schecked; ?> >
                    <label class="<?php echo $label_class; ?>" for="<?php echo $sid ?>"><?php echo 'Super-Admin' . $supericon?></label>
                </p>
<?php
			}
			foreach ( $roles as $role_value => $role_name ) {
				$checked = array_key_exists( $role_value, $opts ) ? 'checked="checked"' : '';
				$name    = "ub_ab_prev[{$menu->id}][menu_roles][{$role_value}]";
				$id      = "ub_ab_prev_{$menu->id}_menu_roles_{$role_value}";
				$current_user_has_role = self::_current_user_has_role( $role_value );
				$icon = $current_user_has_role ? sprintf( "&nbsp;<span class='dashicons dashicons-info' title='%s'></span", __( 'Current user has this role', 'ub' ) ) : '';
				$label_class = $current_user_has_role ? 'ub_adminbar_is_current_user' : '';
?>
                <p>
                    <input id="<?php echo $id ?>" type='checkbox' name='<?php echo $name ?>' <?php echo $checked; ?> >
                    <label class="<?php echo $label_class; ?>" for="<?php echo $id ?>"><?php echo $role_name . $icon?></label>
                </p>
<?php
			}
		} else {
			if ( is_multisite() ) {
				$sname    = 'ub_ab_tmp[][menu_roles][super]';
				$sid      = 'ub_ab_tmp__menu_roles_super';
?>
                <p>
                    <input type="checkbox" checked="checked" name="<?php echo $sname ?>" id="<?php $sid ?>"/>
                    <label for="<?php echo $sid ?>">Super-Admin</label>
                </p>
<?php
			}
			foreach ( $roles as $role_value => $role_name ) {
				$name = "ub_ab_tmp[][menu_roles][{$role_value}]";
				$id   = "ub_ab_tmp__menu_roles_{$role_value}";
?>
                <p>
                    <input type="checkbox" checked="checked" name="<?php echo $name ?>" id="<?php $id ?>"/>
                    <label for="<?php echo $id ?>"><?php echo $role_name ?></label>
                </p>
<?php
			}
		}

	}

	/**
	 * Renders dashicons radio inputs
	 *
	 * @param UB_Admin_Bar_Menu $menu|null
	 */
	public static function render_dashicons_radios( $menu = null ) {
		foreach (  self::$_dashicons  as $index => $icons ) {
			printf( '<h5>%s</h5>', $index );
?>
        <ul class="ub_adminbar_dashicons">
<?php
if ( $menu instanceof UB_Admin_Bar_Menu ) {
	foreach (  $icons  as $icon_name ) {
		$name = "ub_ab_prev[{$menu->id}][dashicons]";
		$id   = "ub_ab_prev_{$menu->id}_dashicons_{$icon_name}";
		$title = str_replace( '-', ' ', ucfirst( $icon_name ) );
?>
<li class="<?php echo isset( $menu->menu->dashicons ) && $menu->menu->dashicons === $icon_name ? 'selected' : ''; ?>" >
<input <?php echo isset( $menu->menu->dashicons ) ?  checked( $menu->menu->dashicons, $icon_name, false ) : ''; ?> title="<?php echo $title ?>" type="radio"  value="<?php echo $icon_name ?>" name="<?php echo $name ?>" id="<?php echo $id ?>"/>
<label title="<?php echo $title ?>" for="<?php echo $id ?>">
<span class="dashicons dashicons-<?php echo $icon_name ?>"></span>
</label>
</li>
<?php
	}
} else {
	foreach ( $icons as $icon_name ) {
		$name = 'ub_ab_tmp[][dashicons]';
		$id   = "ub_ab_tmp_dashicons_{$icon_name}";
		$title = str_replace( '-', ' ', ucfirst( $icon_name ) );
?>
<li>
<input title="<?php echo $title ?>" type="radio"  value="<?php echo $icon_name ?>" name="<?php echo $name ?>" id="<?php echo $id ?>"/>
<label title="<?php echo $title ?>" for="<?php echo $id ?>">
<span class="dashicons dashicons-<?php echo $icon_name ?>"></span>
</label>
</li>
<?php
	}
}

?>
        </ul>
<?php
		}
	}

	/**
	 * Get roles list.
	 *
	 * @since 1,8,4
	 *
	 * @return array $roles Array of user roles.
	 */
	private static function get_roles() {
		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$roles = $wp_roles->get_names();
		/**
		 * add guest
		 */
		$roles['guest'] = __( 'Guest (non loged user)', 'ub' );
		return $roles;
	}
}