<?php
/*
Plugin Name: Coming Soon Page & Maintenance Mode
Description: Customize the Maintenance Mode page and create Coming Soon Page.
License: GNU General Public License (Version 2 - GPLv2)
Copyright 2017 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( ! class_exists( 'ub_maintenance' ) ) {

	class ub_maintenance extends ub_helper {
		protected $option_name = 'ub_maintenance';
		private $current_sites = array();

		public function __construct() {
			parent::__construct();
			$this->module = 'maintenance';
			$this->set_options();
			add_action( 'ultimatebranding_settings_maintenance', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_maintenance_process', array( $this, 'update' ), 10, 1 );
			add_action( 'template_redirect', array( $this, 'render' ), 0 );
			add_filter( 'rest_authentication_errors', array( $this, 'only_allow_logged_in_rest_access' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_ajax_ultimatebranding_maintenance_search_sites', array( $this, 'search_sites' ) );
		}

		/**
		 * modify option name
		 *
		 * @since 1.9.2
		 */
		public function get_module_option_name( $option_name, $module ) {
			if ( is_string( $module ) && $this->module == $module ) {
				return $this->option_name;
			}
			return $option_name;
		}

		protected function set_options() {
			$description = array(
				__( 'A Coming Soon should be used when a domain is new and you are building out the site.', 'ub' ),
				__( 'Maintenance should only be used when your established site is truly down for maintenance.', 'ub' ),
				__( 'Maintenance Mode returns a special header code (503) to notify search engines that your site is currently down so it does not negatively affect your site’s reputation.', 'ub' ),
			);

			$options = array(
				'mode' => array(
					'title' => __( 'Working mode', 'ub' ),
					'fields' => array(
						'mode' => array(
							'type' => 'radio',
							'label' => __( 'Mode', 'ub' ),
							'options' => array(
								'off' => __( 'Off', 'ub' ),
								'coming-soon' => __( 'Coming Soon', 'ub' ),
								'maintenance' => __( 'Maintenance', 'ub' ),
							),
							'default' => 'off',
							'description' => implode( ' ', $description ),
						),
					),
				),
				'document' => array(
					'title' => __( 'Document', 'ub' ),
					'fields' => array(
						'title' => array(
							'label' => __( 'Title', 'ub' ),
							'description' => __( 'Enter a headline for your page.', 'ub' ),
						),
						'content' => array(
							'type' => 'wp_editor',
							'label' => __( 'content', 'ub' ),
						),
						'background' => array(
							'type' => 'color',
							'label' => __( 'Background color', 'ub' ),
							'default' => '#f1f1f1',
						),
						'width' => array(
							'type' => 'number',
							'label' => __( 'Content width', 'ub' ),
							'default' => 600,
							'min' => 0,
							'max' => 2000,
							'classes' => array( 'ui-slider' ),
						),
					),
				),
				'logo' => array(
					'title' => __( 'Logo', 'ub' ),
					'fields' => array(
						'show' => array(
							'type' => 'checkbox',
							'label' => __( 'Logo', 'ub' ),
							'description' => __( 'Would you like to show the logo?', 'ub' ),
							'options' => array(
								'on' => __( 'Show', 'ub' ),
								'off' => __( 'Hide', 'ub' ),
							),
							'default' => 'on',
							'classes' => array( 'switch-button' ),
							'slave-class' => 'logo-related',
						),
						'image' => array(
							'type' => 'media',
							'label' => __( 'Logo image', 'ub' ),
							'description' => __( 'Upload your own logo.', 'ub' ),
							'master' => 'logo-related',
						),
						'width' => array(
							'type' => 'number',
							'label' => __( 'Logo width', 'ub' ),
							'default' => 84,
							'min' => 0,
							'classes' => array( 'ui-slider' ),
							'master' => 'logo-related',
						),
						'position' => array(
							'type' => 'radio',
							'label' => __( 'Logo Position', 'ub' ),
							'options' => array(
								'left' => __( 'Left', 'ub' ),
								'center' => __( 'Center', 'ub' ),
								'right' => __( 'Right', 'ub' ),
							),
							'default' => 'center',
							'master' => 'logo-related',
						),
					),
				),
				'background' => array(
					'title' => __( 'Background', 'ub' ),
					'fields' => array(
						'color' => array(
							'type' => 'color',
							'label' => __( 'Background color', 'ub' ),
							'default' => '#210101',
						),
						'image' => array(
							'type' => 'media',
							'label' => __( 'Background Image', 'ub' ),
							'description' => __( 'You can upload a background image here. The image will stretch to fit the page, and will automatically resize as the window size changes. You\'ll have the best results by using images with a minimum width of 1024px.', 'ub' ),
						),
					),
				),
				'timer' => array(
					'title' => __( 'Timer', 'ub' ),
					'fields' => array(
						'use' => array(
							'type' => 'checkbox',
							'label' => __( 'Use Timer', 'ub' ),
							'description' => __( 'Would you like to use timer?', 'ub' ),
							'options' => array(
								'on' => __( 'On', 'ub' ),
								'off' => __( 'Off', 'ub' ),
							),
							'default' => 'off',
							'classes' => array( 'switch-button' ),
							'slave-class' => 'timer-related',
						),
						'show' => array(
							'type' => 'checkbox',
							'label' => __( 'Show on front-end', 'ub' ),
							'description' => __( 'Would you like to show the timer?', 'ub' ),
							'options' => array(
								'on' => __( 'Show', 'ub' ),
								'off' => __( 'Hide', 'ub' ),
							),
							'default' => 'on',
							'classes' => array( 'switch-button' ),
							'master' => 'timer-related',
						),
						'till_date' => array(
							'type' => 'date',
							'label' => __( 'Till date', 'ub' ),
							'master' => 'timer-related',
						),
						'till_time' => array(
							'type' => 'time',
							'label' => __( 'Till time', 'ub' ),
							'master' => 'timer-related',
						),
					),
				),
				'social_media' => array(
					'title' => __( 'Social Media', 'ub' ),
					'fields' => array(
						'facebook' => array( 'label' => __( 'Facebook', 'ub' ) ),
						'googleplus' => array( 'label' => __( 'G+', 'ub' ) ),
						'twitter' => array( 'label' => __( 'Twitter', 'ub' ) ),
					),
				),
			);
			/**
			 * multisite options
			 */
			if ( is_multisite() ) {
				$options['mode']['fields']['sites'] = array(
					'type' => 'radio',
					'label' => __( 'Apply to', 'ub' ),
					'options' => array(
						'all' => __( 'All sites', 'ub' ),
						'selected' => __( 'Selected sites', 'ub' ),
					),
					'default' => 'all',
				);
				$nonce_action = $this->get_nonce_action_name( $this->module );
				$args = array();
				$sites = $this->get_current_sites();
				if ( ! empty( $sites ) ) {
					$args = array( 'site__not_in' => $sites );
				}
				$options['sites'] = array(
					'title' => __( 'Sites', 'ub' ),
					'master' => array(
						'section' => 'mode',
						'field' => 'sites',
						'value' => 'selected',
					),
					'fields' => array(
						'sites_html' => array(
							'type' => 'description',
							'label' => __( 'Sites added', 'ub' ),
							'value' => $this->get_current_set_sites(),
						),
						'sites' => array(
							'type' => 'select',
							'label' => __( 'Add a site', 'ub' ),
							'multiple' => 'multiple',
							'options' => $this->get_sites( $args ),
							'classes' => array( 'ub-select2' ),
							'after' => sprintf( ' <button class="ub-button ub-add-site">%s</button>', esc_html__( 'Add site', 'ub' ) ),
						),
						'list' => array(
							'type' => 'hidden',
							'multiple' => true,
							'skip_value' => true,
						),
					),
				);
			}
			$this->options = $options;
		}

		/**
		 * get current sites html
		 */
		private function get_current_set_sites() {
			$content = '<ul id="ub_maintenance_selcted_sites">';
			$sites = $this->get_current_sites();
			if ( ! empty( $sites ) ) {
				$sites = get_sites( array( 'site__in' => $sites ) );
				foreach ( $sites as $site ) {
					$content .= sprintf( '<li id="site-%d">', esc_attr( $site->blog_id ) );
					$content .= sprintf( '<input type="hidden" name="simple_options[sites][list][]" value="%d" />', esc_attr( $site->blog_id ) );
					$blog = get_blog_details( $site->blog_id );
					$content .= esc_html( sprintf( '%s (%s)', $blog->blogname, $blog->siteurl ) );
					$content .= sprintf( ' <a href="#">%s</a>', esc_html__( 'remove site', 'ub' ) );
					$content .= '</li>';
				}
			}
			$content .= '</ul>';
			return $content;
		}
		/**
		 * get and set current sites
		 */
		private function get_current_sites() {
			if ( empty( $this->current_sites ) ) {
				$sites = $this->get_value( 'sites', 'list' );
				if ( ! empty( $sites ) ) {
					$this->current_sites = array_filter( $sites );
				}
			}
			return $this->current_sites;
		}
		/**
		 * Wraper for get_sites() wp-ms-function function.
		 */
		private function get_sites( $args = array() ) {
			$results = array(
				'-1' => esc_html__( 'Select a site', 'ub' ),
			);
			$sites = get_sites( $args );
			if ( empty( $sites ) ) {
				return array();
			}
			foreach ( $sites as $site ) {
				$blog = get_blog_details( $site->blog_id );
				$results[ $blog->blog_id ] = esc_html( sprintf( '%s (%s)', $blog->blogname, $blog->siteurl ) );
			}
			return $results;
		}
		/**
		 * Display the default template
		 */
		public function get_default_template() {
			$file = file_get_contents( dirname( __FILE__ ).'/template.html' );
			return $file;
		}
		/**
		 * Display the coming soon page
		 */
		public function render() {
			/**
			 * do not render for logged users
			 */
			$logged = is_user_logged_in();
			if ( $logged ) {
				return;
			}
			/**
			 * check sites options
			 */
			$sites = $this->get_value( 'mode', 'sites' );
			if ( 'selected' == $sites ) {
				$sites = $this->get_current_sites();
				if ( empty( $sites ) ) {
					return;
				}
				$blog_id = get_current_blog_id();
				if ( ! in_array( $blog_id, $sites ) ) {
					return;
				}
			}
			/**
			 * check status
			 */
			$status = $this->get_value( 'mode', 'mode' );
			if ( 'off' == $status ) {
				return;
			}
			/**
			 * check timer
			 */
			$v = $this->get_value( 'timer' );
			if (
				isset( $v['use'] )
				&& 'on' == $v['use']
				&& isset( $v['show'] )
				&& 'on' == $v['show']
				&& isset( $v['till_date'] )
				&& isset( $v['till_date']['alt'] )
			) {
				$date = $v['till_date']['alt'].' '.(isset( $v['till_time'] )? $v['till_time']:'00:00');
				$distance = strtotime( $date ) - time();
				if ( 0 > $distance ) {
					$value = $this->get_value();
					$value['mode']['mode'] = 'off';
					$this->update_value( $value );
					return;
				}
			}

			/**
			 *  set headers
			 */
			if ( 'maintenance' == $status ) {
				header( 'HTTP/1.1 503 Service Temporarily Unavailable' );
				header( 'Status: 503 Service Temporarily Unavailable' );
				header( 'Retry-After: 86400' ); // retry in a day
				$maintenance_file = WP_CONTENT_DIR.'/maintenance.php';
				if ( ! empty( $enable_maintenance_php ) and file_exists( $maintenance_file ) ) {
					include_once( $maintenance_file );
					exit();
				}
			}
			// Prevetn Plugins from caching
			// Disable caching plugins. This should take care of:
			//   - W3 Total Cache
			//   - WP Super Cache
			//   - ZenCache (Previously QuickCache)
			if ( ! defined( 'DONOTCACHEPAGE' ) ) {
				define( 'DONOTCACHEPAGE', true );
			}
			if ( ! defined( 'DONOTCDN' ) ) {
				define( 'DONOTCDN', true );
			}
			if ( ! defined( 'DONOTCACHEDB' ) ) {
				define( 'DONOTCACHEDB', true );
			}
			if ( ! defined( 'DONOTMINIFY' ) ) {
				define( 'DONOTMINIFY', true );
			}
			if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
				define( 'DONOTCACHEOBJECT', true );
			}
			header( 'Cache-Control: max-age=0; private' );

			$template = $this->get_default_template();

			$this->set_data();

			$body_classes = array();

			/**
			 * Add defaults.
			 */
			if ( empty( $this->data['document']['title'] ) && empty( $this->data['document']['content'] ) ) {
				$this->data['document']['title'] = __( 'We&rsquo;ll be back soon!', 'ub' );
				$this->data['document']['content'] = __( 'Sorry for the inconvenience but we&rsquo;re performing some maintenance at the moment. We&rsquo;ll be back online shortly!', 'ub' );
				if ( 'coming-soon' == $status ) {
					$this->data['document']['title'] = __( 'Coming Soon', 'ub' );
					$this->data['document']['content'] = __( 'Stay tuned!', 'ub' );
				}
				$this->data['document']['content_meta'] = $this->data['document']['content'];
			}

			foreach ( $this->data as $section => $data ) {
				foreach ( $data as $name => $value ) {
					if ( empty( $value ) ) {
						$value = '';
					}
					if ( ! is_string( $value ) ) {
						continue;
					}
					$re = sprintf( '/{%s_%s}/', $section, $name );
					$template = preg_replace( $re, stripcslashes( $value ), $template );
				}
			}
			/**
			 * javascript
			 */
			$head = '';
			$v = $this->get_value( 'timer' );
			if (
				isset( $v['use'] )
				&& 'on' == $v['use']
				&& isset( $v['show'] )
				&& 'on' == $v['show']
				&& isset( $v['till_date'] )
				&& isset( $v['till_date']['alt'] )
			) {
				$date = $v['till_date']['alt'].' '.(isset( $v['till_time'] )? $v['till_time']:'00:00');
				$distance = strtotime( $date ) - time();
				$body_classes[] = 'has-counter';
				$head .= '
<script type="text/javascript">
var distance = '.$distance.';
var ultimate_branding_counter = setInterval(function() {
    var days = Math.floor( distance / ( 60 * 60 * 24));
    var hours = Math.floor((distance % ( 60 * 60 * 24)) / ( 60 * 60));
    var minutes = Math.floor((distance % ( 60 * 60)) / ( 60));
    var seconds = Math.floor((distance % ( 60)));
    var value = "";
    if ( 0 < days ) {
        value += days + "'._x( 'd', 'day letter of timer', 'ub' ).'" + " ";
    }
    if ( 0 < hours ) {
        value += hours + "'._x( 'h', 'hour letter of timer', 'ub' ).'" + " ";
    }
    if ( 0 < minutes ) {
        value += minutes + "'._x( 'm', 'minute letter of timer', 'ub' ).'" + " ";
    }
    if ( 0 < seconds ) {
        value += seconds + "'._x( 's', 'second letter of timer', 'ub' ).'";
    }
    if ( "" == value ) {
        value = "'.__( 'We are back now!', 'ub' ).'";
    }
    document.getElementById("counter").innerHTML = value;
    if (distance < 0) {
        window.location.reload();
    }
    distance--;
}, 1000);
</script>';
			}
			/**
			 * social_media
			 */
			$social_media = '';
			$v = $this->get_value( 'social_media' );
			if ( ! empty( $v ) ) {
				foreach ( $v as $key => $url ) {
					if ( empty( $url ) ) {
						continue;
					}
					$social_media .= sprintf(
						'<li><a href="%s"><span class="dashicons dashicons-%s"></span>',
						esc_url( $url ),
						esc_attr( $key )
					);
				}
				if ( ! empty( $social_media ) ) {
					$body_classes[] = 'has-social';
					$social_media = '<ul>'.$social_media.'</ul>';
					$head .= '<link rel="stylesheet" id="dashicons-css" href="/wp-includes/css/dashicons.css" type="text/css" media="all" />';
				}
			}
			$template = preg_replace( '/{social_media}/', $social_media, $template );

			/**
			 * head
			 */
			$template = preg_replace( '/{head}/', $head, $template );
			/**
			 * css
			 */
			$css = '';
			/**
			 * Background
			 */

			$v = $this->get_value( 'background', 'image_meta' );
			if ( isset( $v[0] ) ) {
				$css .= sprintf('
html {
    background: url(%s) no-repeat center center fixed;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
}
body {
    background-color: transparent;
}', esc_url( $v[0] ) );
			}
			/**
			 * Logo
			 */
			$logo = '';
			$show = $this->get_value( 'logo', 'show', false );
			if ( 'on' == $show ) {
				/**
				 * Logo position
				 */
				$position = $this->get_value( 'logo', 'position', false );
				$margin = '0 auto';
				switch ( $position ) {
					case 'left':
						$margin = '0 auto 0 0';
					break;
					case 'right':
						$margin = '0 0 0 auto';
					break;
				}
				$image_meta = $this->get_value( 'logo', 'image_meta' );
				if ( is_array( $image_meta ) && 4 == count( $image_meta ) ) {
					$width = $this->get_value( 'logo', 'width' );
					$height = $image_meta[2] * $width / $image_meta[1];
					$css .= sprintf('
#logo {
    background: url(%s) no-repeat center center;
    -webkit-background-size: contain;
    -moz-background-size: contain;
    -o-background-size: contain;
    background-size: contain;
    width: %dpx;
    height: %dpx;
    display: block;
    margin: %s;
}
', esc_url( $image_meta[0] ), $width, $height, $margin );
					$logo = '<div id="logo"></div>';
				}
			}
			$template = preg_replace( '/{logo}/', $logo, $template );
			/**
			 * replace css
			 */
			$template = preg_replace( '/{css}/', $css, $template );
			/**
			 * body classes
			 */
			$template = preg_replace( '/{body_class}/', implode( ' ', $body_classes ), $template );

			echo $template;
			exit();

		}

		function only_allow_logged_in_rest_access( $access ) {
			$current_WP_version = get_bloginfo( 'version' );
			if ( version_compare( $current_WP_version, '4.7', '>=' ) ) {
				if ( ! is_user_logged_in() ) {
					return new WP_Error( 'rest_cannot_access', __( 'Only authenticated users can access the REST API.', 'coming-soon' ), array( 'status' => rest_authorization_required_code() ) );
				}
			}
			return $access;
		}

		/**
		 * enqueue_scripts
		 */
		public function enqueue_scripts() {
			$tab = get_query_var( 'ultimate_branding_tab' );
			if ( $this->module != $tab ) {
				return;
			}
			/**
			 * module js
			 */
			$file = ub_files_url( 'modules/maintenance/maintenance.js' );
			wp_register_script( __CLASS__, $file, array( 'jquery' ), $this->build, true );
			$localize = array(
				'remove' => __( 'remove site', 'ub' ),
			);
			wp_localize_script( __CLASS__, __CLASS__, $localize );
			/**
			 * jQuery select2
			 */
			$version = '4.0.4';
			$file = ub_url( 'external/select2/select2.min.js' );
			wp_enqueue_script( 'select2', $file, array( __CLASS__, 'jquery' ), $version, true );
			$file = ub_url( 'external/select2/select2.min.css' );
			wp_enqueue_style( 'select2', $file, array(), $version );
		}

		public function search_sites() {
			if ( ! is_multisite() ) {
				wp_send_json_error();
			}
			$user_id = isset( $_REQUEST['user_id'] )? $_REQUEST['user_id']:0;
			$search = isset( $_REQUEST['q'] )? $_REQUEST['q']:null;
			$nonce = isset( $_REQUEST['_wpnonce'] )? $_REQUEST['_wpnonce']:null;
			/**
			 * check values
			 */
			if ( empty( $search ) || empty( $nonce ) ) {
				wp_send_json_error();
			}
			/**
			 * Check nonce
			 */
			$nonce_action = $this->get_nonce_action_name( $this->module, $user_id );
			$verify = wp_verify_nonce( $nonce, $nonce_action );
			if ( ! $verify ) {
				wp_send_json_error();
			}
			$args = array(
				'search' => $search,
			);
			$results = array();
			$sites = get_sites( $args );
			foreach ( $sites as $site ) {
				$blog = get_blog_details( $site->blog_id );
				$results[] = array(
					'blog_id' => $blog->blog_id,
					'blogname' => $blog->blogname,
					'siteurl' => $blog->siteurl,
				);
			}
			wp_send_json_success( $results );
		}
	}
}
new ub_maintenance();
