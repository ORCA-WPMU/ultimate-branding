<?php

/**
 * Class UB_Admin_Bar
 * @since 1.5
 * @var $wpdb WPDB
 */
global $wpdb;
class UB_Admin_Bar {

	/**
	 * @const module name
	 */
	const NAME = 'custom-admin-bar';

	const OPTION_KEY = 'ub_admin_bar_menus';

	const MENU_OPTION_KEY = 'ub_admin_bar_menu_';
	/**
	 * @const style key for saving to options table
	 */
	const STYLE = 'ub_admin_bar_style';

	/**
	 * @const order key for saving admin bar order to options table
	 */
	const ORDER = 'ub_admin_bar_order';
	/**
	 * Constructs the class and hooks to action/filter hooks
	 *
	 * @since 1.5
	 * @access public
	 *
	 */
	function __construct() {
		add_filter( 'ultimatebranding_settings_adminbar_process', array( $this, 'update_data' ), 10, 1 );
		add_action( 'admin_bar_menu', array( $this, 'reorder_menus' ), 99999 );
		add_action( 'admin_bar_menu', array( $this, 'add_custom_menus' ), 1 );
		add_action( 'admin_bar_menu', array( $this, 'remove_menus_from_admin_bar' ), 999 );
		add_action( 'wp_head', array( $this, 'print_style_tag' ) );
		add_action( 'admin_head', array( $this, 'print_style_tag' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'before_admin_bar_render' ) );
		add_action( 'wp_after_admin_bar_render', array( $this, 'after_admin_bar_render' ) );
		add_action( 'wp_ajax_ub_save_menu_ordering', array( $this, 'ajax_save_menu_ordering' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enque_general_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enque_general_scripts' ) );
		add_action( 'init', array( $this, 'try_to_show_admin_bar' ) );
		add_filter( 'ultimate_branding_export_data', array( $this, 'export' ) );
		add_action( 'ultimate_branding_import', array( $this, 'import' ) );
	}

	public function try_to_show_admin_bar() {
		// Show the Toolbar for logged out users.
		if ( is_user_logged_in() ) {
			return;
		}
		if ( 1 == (int) UB_Admin_Bar_Forms::get_option( 'show_toolbar_for_non_logged' ) ) {
			show_admin_bar( true );
		}
	}

	/**
	 * Updates data
	 *
	 * @since 1.5
	 * @access public
	 *
	 * @hook ultimatebranding_settings_adminbar_process
	 *
	 * @param bool $status
	 * @return bool true on successful update, false on update failure
	 */
	function update_data( $status ) {
		if ( isset( $_POST['ub_admin_bar_restore_default_order'] ) ) {
			ub_update_option( self::ORDER, '' );
			return true;
		}

		$style = strip_tags( $_POST['ub_admin_bar_style'] );

		ub_update_option( 'wdcab', $_POST['wdcab'] );
		ub_update_option( self::STYLE, $style );

		$cache_id = self::STYLE;
		$style = self::style_normilize( $style );
		wp_cache_set( $cache_id, $style );

		$save_result = $this->_save_new_menus();
		$update_result = $this->_update_prev_menus();
		$remove_result = $this->_remove_menus();
		return $save_result && $status && $update_result && $remove_result;
	}

	/**
	 * Saves newly added menus
	 *
	 * @since 1.5
	 * @access private
	 *
	 * @return bool
	 */
	private function _save_new_menus() {
		if ( isset( $_POST['ub_ab_new'] ) ) {
			$news = $_POST['ub_ab_new'];
			foreach ( $news as $key => $new ) {
				$new_sub = $new['links']['_last_'];
				unset( $new['links']['_last_'] );
				$new['title'] = empty( $new['title'] ) ? __( 'New Menu Title', 'ub' ) : wp_unslash( $new['title'] );

				$parent_id = $this->_insert_menu( $new );
				$sub_insert = true;
				// save new sub menu if any
				if ( is_numeric( $parent_id ) && isset( $new_sub['title'] ) && ! empty( $new_sub['title'] ) ) {
					$sub_insert = $this->_insert_sub_menu( $new_sub, $parent_id );
				}
			}

			$this->_update_menus_record( $parent_id );
			return $parent_id && $sub_insert;
		}
		return true;
	}

	private static function _get_menu_composite_id( $id ) {
		return self::MENU_OPTION_KEY . $id;
	}
	/**
	 * Updates row that keeps id of the menus
	 *
	 * @param $new_id
	 */
	private function _update_menus_record( $new_id ) {
		$prev_ids = ub_get_option( self::OPTION_KEY );
		$prev_ids[] = $new_id;
		ub_update_option( self::OPTION_KEY, $prev_ids );
	}

	/**
	 * Returns last menu id
	 *
	 * @since 1.7.3
	 * @access private
	 *
	 * @return int
	 */
	private function _get_last_menu_id() {
		global $wpdb;
		if ( is_multisite() && function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'ultimate-branding/ultimate-branding.php' ) ) {
			return (int) $wpdb->get_var( "SELECT MAX(meta_id) FROM $wpdb->sitemeta WHERE `meta_key` LIKE '". self::MENU_OPTION_KEY ."%'" );
		} else {
			return (int) $wpdb->get_var( "SELECT MAX(option_id) FROM $wpdb->options WHERE `option_name` LIKE '". self::MENU_OPTION_KEY ."%'" );
		}
	}
	/**
	 * Updates previous menus
	 *
	 * @since 1.5
	 * @access private
	 *
	 * @return bool
	 */
	private function _update_prev_menus() {
		$result = array();
		if ( isset( $_POST['ub_ab_prev'] ) ) {
			$menus = $_POST['ub_ab_prev'];

			foreach ( $menus  as $menu_id => $menu ) {
				$new = $menu['links']['_last_'];
				unset( $menu['links']['_last_'] );
				$links = (array) $menu['links'];

				// add new submenu
				if ( isset( $new['title'] ) && ! empty( $new['title'] ) ) {
					$links[] = $new;
				}

				$menu['links'] = $links;

				$menu['title'] = wp_unslash( $menu['title'] );
				// update parent menu
				$result[] = $this->_update_menu( $menu_id, $menu );
			}
		}

		return true;
	}

	/**
	 * Removes menus
	 *
	 * @since 1.5
	 * @access private
	 *
	 * @return bool
	 */
	private function _remove_menus() {
		$result = array();
		if ( isset( $_POST['ub_ab_delete_links'] ) ) {
			$links = $_POST['ub_ab_delete_links'];
			$links = explode( ',', $links );
			$saved_ids = (array) maybe_unserialize( ub_get_option( self::OPTION_KEY ) );
			$links = array_map( 'trim', $links );
			$saved_ids = array_diff( $saved_ids, $links );
			foreach ( $links as $key => $link_id ) {
				ub_delete_option( self::_get_menu_composite_id( $link_id ) );
			}
			ub_update_option( self::OPTION_KEY, $saved_ids );
		}
		return ! in_array( false, $result );
	}

	/**
	 * Inserts new menu
	 *
	 * @since 1.5
	 * @access private
	 *
	 * @param $menu
	 * @return int|WP_Error
	 */
	private function _insert_menu( $menu ) {
		$parent_id = $this->_get_last_menu_id();
		$composite_id  = $this->_get_menu_composite_id( $parent_id );
		return ub_update_option( $composite_id, serialize( $menu ) ) ? $parent_id : false;
	}

	/**
	 * Inserts sub menu
	 *
	 * @since 1.5
	 * @access private
	 *
	 * @param $sub_menu
	 * @param $parent_id
	 * @return int|WP_Error
	 */
	private function _insert_sub_menu( $sub_menu, $parent_id ) {

		$composite_id = self::_get_menu_composite_id( $parent_id );
		$menu = unserialize( ub_get_option( $composite_id ) );
		$links = isset( $menu['links'] ) ? (array) $menu['links'] : array();
		$links[] = $sub_menu;
		$menu['links'] = $links;
		return ub_update_option( $composite_id, serialize( $menu ) );
	}

	/**
	 * Updates a single menu
	 *
	 * @since 1.5
	 * @access private
	 *
	 * @param $id
	 * @param $menu
	 * @return int|WP_Error
	 */
	private function _update_menu( $id, $menu ) {

		return ub_update_option( self::_get_menu_composite_id( $id ), $menu );
	}


	/**
	 * Retrieves menus from database
	 *
	 * @since 1.5
	 * @access public
	 *
	 * @return array UB_Admin_Bar_Menu|bool false
	 */
	public static  function menus() {
		global $wpdb;
		$ids = maybe_unserialize( ub_get_option( self::OPTION_KEY ) );
		$menus = array();
		if ( $ids ) {
			foreach ( $ids as $id => $data ) {
				if ( ! is_array( $data ) ) {
					$id = $data;
				}
				$composite_id = self::_get_menu_composite_id( $id );
				if ( $m = ub_get_option( $composite_id )  ) {
					$m = maybe_unserialize( $m );
					$menus[]  = new UB_Admin_Bar_Menu( $id , $m );
				}
			}
		}
		return $menus;
	}

	/**
	 * Returns menus style
	 *
	 * @since 1.6
	 * @access public
	 *
	 * @param bool $editor, true, it's in editor mode
	 * @return array|mixed|string|void
	 */
	public static function styles( $editor = false ) {
		$style = <<<UBSTYLE
.ub_admin_bar_image{
    max-width: 100%;
    max-height: 28px;
    padding: 2px 0;
}
UBSTYLE;

		$save_style = stripslashes( ub_get_option( self::STYLE ) );

		if ( $editor ) {
			return $save_style;
		}

		$styles = empty( $save_style ) ? $style : $save_style;
		return self::_prefix_styles( $styles );
	}

	/**
	 * Adds #ub_admin_bar_wrap prefix to the define styles
	 *
	 * @since 1.6
	 * @access private
	 *
	 * @param $styles
	 * @return array|string
	 */
	private static function _prefix_styles( $styles ) {

		$cache_id = self::STYLE;
		$style = wp_cache_get( $cache_id );

		if ( ! $style ) {
			$style = self::style_normilize( $styles );
			wp_cache_set( $cache_id, $style );
		}

		return $style;

	}

	/**
	 * Add prefix to CSS rules. Supports media queries.
	 *
	 * @since 1.8.3.2
	 * @access public
	 *
	 * @param string css
	 * @return string style
	 */
	public static function style_normilize( $css ) {

		if ( empty( $css ) ) { return $css; }

		$pattern = '~@media\b[^{]*({((?:[^{}]+|(?1))*)})~';
		preg_match_all( $pattern, $css, $matches, PREG_PATTERN_ORDER );

		$style_normilized = '';
		$media_wraps = $matches[0];
		$media_chunks = $matches[2];

		foreach ( $media_chunks as $key => $media_chunk ) {

			$whole_chunk = $media_wraps[ $key ];
			$css = str_replace( $whole_chunk, '', $css );
			$wrap = explode( '{', $whole_chunk );
			$wrap = $wrap[0];

			if ( ! empty( $media_chunk ) ) {
				$styles = array_filter( explode( '}', $media_chunk ) );
				$output = array();
				foreach ( $styles as $style ) {
					if ( trim( $style ) != '' ) {
						$output[] = '#ub_admin_bar_wrap ' . $style . '}';
					}
				}
				$media_chunk = implode( '', $output );
			}

			$style_normilized .= $wrap . '{' . $media_chunk . '}';

		}

		if ( ! empty( $css ) ) {
			$styles = array_filter( explode( '}', $css ) );
			$output = array();
			foreach ( $styles as $style ) {
				if ( trim( $style ) != '' ) {
					$output[] = '#ub_admin_bar_wrap ' . $style . '}';
				}
			}
			$css = implode( '', $output );
			$style_normilized = $css . $style_normilized;
		}

		return $style_normilized;

	}

	/**
	 * Saves new menu order into database
	 *
	 * @since 1.6
	 * @access public
	 *
	 * @return void
	 */
	public function ajax_save_menu_ordering() {
		$order = $_POST['order'];

		$result = array(
			'status' => false,
		);

		if ( is_array( $order ) && count( $order ) > 0 ) {

			ub_update_option( self::ORDER, $order );
			$result = array(
				'status' => true,
			) ;
		}

		header( 'Content-Type: application/json' );
		echo json_encode( $result );
		wp_die();
	}

	/**
	 * Returns menus' order
	 *
	 * @since 1.6
	 * @access public
	 *
	 * @return mixed|void
	 */
	public static function order() {
		return ub_get_option( self::ORDER );
	}

	/**
	 * Renders before admin bad renderer
	 *
	 * @hook wp_before_admin_bar_render
	 *
	 * @since 1.6
	 * @access public
	 */
	function before_admin_bar_render() {
		echo '<div id="ub_admin_bar_wrap">';
	}

	/**
	 * Renders after admin bad renderer
	 *
	 * @hook wp_after_admin_bar_render
	 *
	 * @since 1.6
	 * @access public
	 */
	function after_admin_bar_render() {
		echo '</div>';
	}

	/**
	 * Keeps the menus in order based on saved orderings
	 *
	 * @since 1.6
	 * @access public
	 *
	 * @param $wp_admin_bar instance of WP_Admin_Bar passed by refrence
	 * @hook admin_bar_menu
	 *
	 * @return void
	 */
	public function reorder_menus() {
		/**
		 * @var $wp_admin_bar WP_Admin_Bar
		 */
		global $wp_admin_bar;
		$order = UB_Admin_Bar::order();
		if ( ! $order || ! is_array( $order ) ) { return; }

		$nodes = $wp_admin_bar->get_nodes();
		// remove all nodes
		foreach ( $nodes as $node_id => $node ) {
			$wp_admin_bar->remove_node( $node_id );
		}

		// add ordered nodes
		foreach ( $order as $o ) {
			if ( isset( $nodes[ $o ] ) ) {
				$wp_admin_bar->add_node( $nodes[ $o ] );
				unset( $nodes[ $o ] );
			}
		}

		// add rest of the nodes
		if ( count( $nodes ) > 0 ) {
			foreach ( $nodes as $node ) {
				$wp_admin_bar->add_node( $node );
			}
		}

	}

	/**
	 * Adds custom menus to the admin bar
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @param $wp_admin_bar WP_Admin_Bar passed by refrence
	 * @hook admin_bar_menu
	 *
	 * @return void
	 */
	function add_custom_menus() {
		/**
		 * @var $wp_admin_bar WP_Admin_Bar
		 */
		global $wp_admin_bar, $current_user;

		$enabled = ub_get_option( 'wdcab' );
		$enabled = (bool) $enabled['enabled'];

		if ( ! $enabled ) { return; }

		/**
		 * @var $menu UB_Admin_Bar_Menu
		 * @var $sub UB_Admin_Bar_Menu
		 */
		$menus = UB_Admin_Bar::menus();
		if ( is_array( $menus ) && ! empty( $menus ) ) {
			foreach ( $menus as $menu ) {
				$menu_roles = isset( $menu->menu->menu_roles ) ? $menu->menu->menu_roles : array();
				if ( ! is_array( $menu_roles ) ) {
					$menu_roles = array();
				}
				if (
					( is_user_logged_in() && self::user_has_access( $menu_roles, true ) )
					||
					( ! is_user_logged_in() && isset( $menu_roles['guest'] ) && 'on' == $menu_roles['guest'] )
				) {
					$wp_admin_bar->add_menu(array(
						'id' => 'ub_admin_bar_' . $menu->id,
						'title' => $menu->title_image,
						'href' => $menu->link_url,
						'meta' => array(
						'target' => $menu->target,
						),
						)
					);
					$submenus = $menu->subs;
					if ( $submenus ) {
						foreach ( $submenus as $sub ) {
							$wp_admin_bar->add_menu(array(
								'parent' => 'ub_admin_bar_' . $menu->id,
								'id' => $sub->external_id,
								'title' => $sub->title_image,
								'href' => $sub->link_url,
								'meta' => array(
								'target' => $sub->target,
								),
								)
							);
						}
					}
				}
			}
		}

	}

	/**
	 * Removes selected default menus from admin bar
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @return void
	 */
	function remove_menus_from_admin_bar() {
		global $wp_version, $current_user;
		$version = preg_replace( '/-.*$/', '', $wp_version );
		if ( version_compare( $version, '3.3', '>=' ) ) {
			global $wp_admin_bar;
			$wproles = ub_get_option( 'wdcab' );

			/**
			 * sanitize
			 */
			if ( ! isset( $wproles['wp_menu_roles'] ) || ! is_array( $wproles['wp_menu_roles'] ) ) {
				$wproles['wp_menu_roles'] = array();
			}
			if ( ! isset( $wproles['disabled_menus'] ) || ! is_array( $wproles['disabled_menus'] ) ) {
				$wproles['disabled_menus'] = array();
			}

			$hide_from_subscriber = count( $current_user->roles ) === 0 && isset( $wproles['wp_menu_roles'] ) && in_array( 'subscriber', (array) $wproles['wp_menu_roles'] );
			if (
				/**
				 * not loged, remove
				 */
				( ! is_user_logged_in() && in_array( 'guest', $wproles['wp_menu_roles'] ) )
				||
				/**
				 * logged, check roles
				 */
				(
					is_user_logged_in() && (
						! isset( $wproles['wp_menu_roles'] ) || ( isset( $wproles['wp_menu_roles'], $current_user )
						&& is_array( $wproles['wp_menu_roles'] )
						&& ( ! current_user_can( 'manage_network' ) && ( $hide_from_subscriber || count( array_intersect( $wproles['wp_menu_roles'], (array) $current_user->roles ) ) ) )
						|| (current_user_can( 'manage_network' ) && in_array( 'super', $wproles['wp_menu_roles'] ) ) )
					)
				)
			) {
				$opts = ub_get_option( 'wdcab' );
				$disabled = is_array( $opts['disabled_menus'] ) ? $opts['disabled_menus'] : array();
				foreach ( $disabled as $id ) {
					$wp_admin_bar->remove_node( $id );
				}
			}
		}
	}

	/**
	 * Checks to see if user has access to the custom menu based on his roles
	 *
	 * @param $roles
	 * @param bool $keys
	 *
	 * @return bool
	 */
	function user_has_access( $roles, $keys = false ) {
		$user = wp_get_current_user();
		if ( empty( $user ) || ! is_array( $roles ) ) {
			return false;
		}
		if ( ! $keys && ( ! current_user_can( 'manage_network' ) && array_intersect( $roles, $user->roles ))
			|| current_user_can( 'manage_network' ) && in_array( 'super', $roles ) ) {
			return true;
		} elseif ( $keys ) {
			foreach ( $roles as $key => $val ) {
				$val = $key;
				$roles[ $key ] = $val;
			}
			if ( ( ! current_user_can( 'manage_network' ) && array_intersect( $roles, $user->roles ))
				|| current_user_can( 'manage_network' ) && in_array( 'super', $roles ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Enqueues general scripts
	 */
	public function enque_general_scripts() {
		/**
		 * Avoid to load when we do not need it.
		 */
		if ( ! is_admin() && 1 !== (int) UB_Admin_Bar_Forms::get_option( 'show_toolbar_for_non_logged' ) ) {
			return;
		}
		global $ub_version;
		wp_enqueue_style( 'ub_adminbar_general_styles',  ub_files_url( 'modules/custom-admin-bar-files/css/general.css' ), array(), $ub_version );
	}

	/**
	 * Add custom styles to html head section
	 *
	 * @hook wp_head
	 * @hook admin_head
	 *
	 * @since 1.8.5
	 * @access public
	 */
	function print_style_tag() {
		if ( ! is_user_logged_in() ) {
			$show = (int) UB_Admin_Bar_Forms::get_option( 'show_toolbar_for_non_logged' );
			if ( 1 !== $show ) {
				return;
			}
		}
?>
        <style type="text/css" id="custom-admin-bar-css">
            <?php echo self::styles();?>
        </style>
<?php
	}

	/**
	 * Export data.
	 *
	 * @since 1.8.6
	 */
	public function export( $data ) {
		$options = array(
			'wdcab',
			self::STYLE,
			self::ORDER,
		);
		foreach ( $options as $key ) {
			$data['modules'][ $key ] = ub_get_option( $key );
		}
		$menus = ub_get_option( self::OPTION_KEY );
		foreach ( $menus as $menu_id ) {
			$id = self::_get_menu_composite_id( $menu_id );
			$data['modules'][ self::OPTION_KEY ][ $menu_id ] = ub_get_option( $id );
		}
		return $data;
	}

	/**
	 * Handle custom import.
	 *
	 * @since 1.9.2
	 *
	 * @param array $data Import array.
	 */
	public function import( $data ) {
		if ( isset( $data['ub_admin_bar_menus'] ) && is_array( $data['ub_admin_bar_menus'] ) ) {
			$menus = $data['ub_admin_bar_menus'];
			$ub_admin_bar_menus = array();
			foreach ( $menus as $id => $menu ) {
				$key = sprintf( 'ub_admin_bar_menu_%d', $id );
				ub_update_option( $key, $menu );
				$ub_admin_bar_menus[] = $id;
			}
			ub_update_option( 'ub_admin_bar_menus', $ub_admin_bar_menus );
		}
	}
}

new UB_Admin_Bar;