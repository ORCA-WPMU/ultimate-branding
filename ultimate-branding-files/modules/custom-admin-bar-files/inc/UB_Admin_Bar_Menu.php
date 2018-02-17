<?php

/**
 * Class UB_Admin_Bar_Menu
 * @property string $link_url
 * @property string $link_type
 * @property string $target
 * @property WP_Post[] $subs
 * @property bool $is_submenu
 * @property bool $is_image
 * @property string $title_image
 * @property int $external_id
 * @property string $icon
 * @property bool $use_icon
 */
class UB_Admin_Bar_Menu {

	/**
	 * @since 1.5
	 * @access public
	 *
	 * @var int
	 */
	var $id;

	/**
	 * @since 1.5
	 * @access public
	 *
	 * @var stdClass
	 */
	var $menu;

	/**
	 * Constructs the class
	 *
	 * @since 1.5
	 *
	 * @param $id
	 * @param array $menu
	 */
	function __construct( $id, array $menu ) {
		$this->id = $id;
		$this->menu = (object) $menu;
	}

	function load_sub( $menu ) {
		return new UB_Admin_Bar_Menu( '', $menu );
	}

	/**
	 * Implements getter for properties
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param $property
	 * @return mixed
	 */
	public function __get( $property ) {
		$method_name = 'get_' . $property;
		if ( property_exists( $this, $property ) ) {
			return $this->$property;
		} elseif ( method_exists( $this, $method_name ) ) {
			return $this->$method_name();
		} elseif ( isset( $this->menu->{$property} ) ) {
			return $this->menu->{$property};
		}
	}

	/**
	 * Return menu url
	 *
	 * @since 1.5
	 * @access public
	 *
	 * @return string url to for menu
	 */
	function get_link_url( $config_screen = false ) {
		$switcher = $this->is_submenu ? $this->link_type : $this->menu->url;
		if ( $config_screen ) { return $this->menu->url; }
		switch ( $switcher ) {
			case 'network_site_url' :
				$url = network_site_url();
			break;
			case 'admin_url' :
				$url = network_admin_url();
			break;
			case 'site_url' :
				$url = trailingslashit( get_site_url() );
			break;
			case '#':
				$url = '#';
			break;
			case 'admin':
				$url = admin_url( $this->menu->url );
			break;
			case 'site':
				$url = site_url( $this->menu->url );
			break;
			default:
				$url = $this->menu->url;
			break;
		}
		return $url === 'url' ? '' : $url  ;
	}

	/**
	 * Returns target type for menu link
	 *
	 * @since 1.5
	 * @access public
	 *
	 * @return string menu url target
	 */
	function get_target() {
		return ( isset( $this->menu->target ) && $this->menu->target === 'on' ) ? '_blank' : '' ;
	}

	/**
	 * Retrieves type of link/menu
	 * Values are admin_url, site_url, # or url
	 *
	 * @since 1.5
	 * @access public
	 *
	 * @return string
	 */
	function get_link_type() {
		if ( $this->is_submenu ) {
			return $this->menu->url_type;
		}

		if (
			$this->menu->url === network_admin_url( '/' )
			|| $this->menu->url === 'admin_url'
		) {
			return 'admin_url';
		} elseif ( ! (
			$this->menu->url === 'admin_ur'
			|| $this->menu->url === 'site_url'
			|| $this->menu->url === '#'
			|| $this->menu->url === 'network_site_url'
		)  ) {
			return 'url';
		}

		return $this->menu->url;
	}

	/**
	 * Retrieves sub menus
	 *
	 * @since 1.5
	 * @access public
	 *
	 * @return array UB_Admin_Bar_Menu |bool false
	 */
	function get_subs() {
		if ( $this->is_submenu ) { return false; }
		$subs = array();
		foreach ( $this->menu->links  as $id => $link ) {
			$subs[] = new UB_Admin_Bar_Menu( $id, $link );
		}
		return $subs;
	}

	/**
	 * Retrieves image used as title if it's and image, returns url if it's not an image
	 *
	 * @since 1.5
	 * @access public
	 *
	 * @return string
	 */
	public function get_title_image() {
		if ( $this->is_image ) {
			return sprintf(
				'<span class="ab-item"><img  class="ub_admin_bar_image" src="%s" /></span>',
				esc_url_raw( $this->menu->title )
			);
		}
		$icon = $this->use_icon ? "<span class='dashicons dashicons-{$this->icon}'></span>" : '';
		$title_class = $this->use_icon ? 'ub_adminbar_text has_icon' : 'ub_adminbar_text';
		return $icon . "<span class='{$title_class}'>" . $this->menu->title . '<span>';
	}

	/**
	 * Checks if link url is an image
	 *
	 * @since 1.5
	 * @access public
	 *
	 * @return bool
	 */
	private function get_is_image() {
		$image_extentions = array(
			'jpg',
			'jpeg',
			'gif',
			'svg',
			'png',
		);
		$extension = pathinfo( preg_replace( '/\s+/', '', $this->menu->title ) );
		$extension = isset( $extension['extension'] ) ? strtolower( $extension['extension'] ) : false;
		if ( $extension ) {
			return in_array( $extension,  $image_extentions );
		} else {
			return false;
		}

	}

	function get_external_id( $prefix = 'ub_admin_bar_sub_' ) {
		return uniqid( $prefix );
	}

	/**
	 * Checks if it's a submenu
	 *
	 * @since 1.5
	 * @access public
	 *
	 * @return bool
	 */
	function get_is_submenu() {
		return ! isset( $this->menu->links );
	}

	public function get_field_id( $field ) {
		return $field . '-' . $this->id;
	}

	function get_icon() {
		return  isset( $this->menu->dashicons ) ? $this->menu->dashicons : 'media-default';
	}

	function get_use_icon() {
		return (bool) ( isset( $this->menu->use_icon ) ? $this->menu->use_icon : false );
	}
}