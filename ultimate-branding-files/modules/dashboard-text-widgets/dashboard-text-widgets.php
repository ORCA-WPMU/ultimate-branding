<?php
/*
  Plugin Name:  Dashboard Text Widgets
  Description: Enables the Dashboard text widgets.
 */

if ( ! class_exists( 'ub_dashboard_text_widgets' ) ) {
	class ub_dashboard_text_widgets extends ub_helper {
		private $list_table;
		public function __construct() {
			parent::__construct();
			add_action( 'ultimatebranding_settings_dashboard_text_widgets', array( $this, 'admin_options_page' ) );
			add_action( 'ultimatebranding_settings_dashboard_text_widgets_process', array( $this, 'update' ) );
			add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ), 99 );
			add_action( 'wp_network_dashboard_setup', array( $this, 'add_dashboard_widgets' ), 99 );
			add_action( 'wp_user_dashboard_setup', array( $this, 'add_dashboard_widgets' ), 99 );
			$this->option_name = 'wpmudev_dashboard_text_widgets_options';
		}

		public function update( $status ) {
			if ( isset( $_POST['widget'] ) && is_array( $_POST['widget'] ) && isset( $_POST['widget']['number'] ) ) {
				$args = array();
				$widgets = $this->get_items();
				$id = intval( $_POST['widget']['number'] );
				if ( ! isset( $widgets[ $id ] ) ) {
					$id = ( is_array( $widgets )? max( array_keys( $widgets ) ):0 ) + 1;
					$args = array(
						'msg' => 'success',
						'number' => $id,
						'act' => 'edit',
					);
				}
				$widgets[ $id ] = array(
					'title' => isset( $_POST['widget']['title'] )? $_POST['widget']['title']:'',
					'content' => isset( $_POST['widget']['content'] )? $_POST['widget']['content']:'',
					'content_parse' => apply_filters( 'the_content', isset( $_POST['widget']['content'] )? $_POST['widget']['content']:'' ),
					'show-on' => array(
						'site' => isset( $_POST['widget']['show-on']['site'] )? $_POST['widget']['show-on']['site']:'off',
						'network' => isset( $_POST['widget']['show-on']['network'] )? $_POST['widget']['show-on']['network']:'off',
					),
				);
				ub_update_option( $this->option_name, $widgets );
				if ( empty( $args ) ) {
					return true;
				}
				wp_safe_redirect( UB_Help::add_query_arg_raw( $args, wp_get_referer() ) );
				exit;
			}
		}

		public function admin_options_page() {
			global $wp_version, $ub_version;
			add_filter( 'ultimatebranding_settings_panel_show_submit', '__return_false' );
			$version_compare = version_compare( $wp_version, '3.7.1' );
			$_SHOW_LISTING = true;

			if ( ! isset( $_GET['subpage'] ) ) {
				$this->list_table = new WPMUDEV_Dashboard_Texts_List_Table();
			}

			if ( (isset( $_GET['act'] )) && ($_GET['act'] == 'add') ) {
?>
                    <div id="wpmudev-dashvboard-feeds-panel" class="wrap wpmudev-dashvboard-feeds-wrap">
                        <h2><?php _ex( 'Add New Dashboard Text Widget', 'New Page Title', 'ub' ); ?></h2>
                        <?php $this->form(); ?>
                    </div>
<?php
				$_SHOW_LISTING = false;
			} else if ( (isset( $_GET['act'] )) && ($_GET['act'] == 'edit') ) {
				if ( (isset( $_GET['number'] )) && ( ! empty( $_GET['number'] )) ) {
					$df_widgets = $this->get_items();
					$df_item_number = esc_attr( $_GET['number'] );
					if ( isset( $df_widgets[ $df_item_number ] ) ) {
						$df_widgets[ $df_item_number ]['number'] = $df_item_number;

?>
                            <div id="wpmudev-dashvboard-feeds-panel" class="wrap wpmudev-dashvboard-feeds-wrap">
                                <h2><?php _ex( 'Edit Dashboard Text Widget', 'Edit Page Title', 'ub' ); ?></h2>
                                <?php $this->form( $df_widgets[ $df_item_number ] ); ?>
                            </div>
<?php
						$_SHOW_LISTING = false;
					}
				}
			} else if ( (isset( $_GET['act'] )) && ($_GET['act'] == 'delete') ) {
				if ( ! empty( $_GET ) && (wp_verify_nonce( $_GET['nonce'],'nonce' )) ) {
					if ( (isset( $_GET['number'] )) && ( ! empty( $_GET['number'] )) ) {
						$widgets = $this->get_items();
						$id = esc_attr( $_GET['number'] );
						if ( isset( $widgets[ $id ] ) ) {
							unset( $widgets[ $id ] );
							ub_update_option( $this->option_name, $widgets );
						}
					}
				}
			}

			if ( $_SHOW_LISTING == true ) {

?>
                    <div id="wpmudev-dashvboard-feeds-panel" class="wrap wpmudev-dashvboard-feeds-wrap">
<?php
				$action_url = remove_query_arg( array( 'nonce', 'msg', 'number' ) );
				$action_url = add_query_arg( 'act', 'add', $action_url );
?>
<a class="add-new-h2" href="<?php echo $action_url; ?>">Add New</a>
<?php
				$df_widgets = $this->get_items(); //get_option($this->option_name);
if ( ( ! $df_widgets) || ( ! is_array( $df_widgets )) ) {
	$df_widgets = array(); }

				$this->show_dashboard_list_table( $df_widgets );
?>
                    </div>
<?php
			}
		}

		private function get_items() {
			return ub_get_option( $this->option_name );
		}

		public function show_dashboard_list_table( $df_items = array() ) {
			$this->list_table->prepare_items( $df_items );
			$this->list_table->display();
		}

		private function form( $widget_options = array() ) {
			if ( (isset( $widget_options['number'] )) && ( ! empty( $widget_options['number'] )) ) {
				$widget_options['number'] = esc_attr( $widget_options['number'] );
			} else {
				$widget_options['number'] = '0';
			}
			$widget_options['title'] = stripslashes( isset( $widget_options['title'] )? $widget_options['title']:'' );
			$widget_options['content'] = isset( $widget_options['content'] )? $widget_options['content']:'';
			$widget_options['show-on'] = isset( $widget_options['show-on'] )? $widget_options['show-on']:array();
			$widget_options['show-on']['site'] = isset( $widget_options['show-on']['site'] )? $widget_options['show-on']['site']:'on';
			$widget_options['show-on']['network'] = isset( $widget_options['show-on']['network'] )? $widget_options['show-on']['network']:'on';

?>
                <input type="hidden" name="widget[number]" value="<?php echo $widget_options['number']; ?>" />

                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content" style="position: relative;">
                        <div id="titlediv">
                            <div id="titlewrap">
                                <input type="text" name="widget[title]" size="30" value="<?php echo esc_attr( $widget_options['title'] ); ?>" id="title" spellcheck="true" autocomplete="off" placeholder="<?php esc_attr_e( 'Enter title here', 'ub' ); ?>">
                            </div>
                            <div id="postdivrich" class="postarea wp-editor-expand">
                                <?php wp_editor( stripslashes( $widget_options['content'] ), 'ultimatebranding_dashboard_text_widget_content', array( 'textarea_name' => 'widget[content]' ) ); ?>
                            </div>
                        </div>
                    </div>
                    <div id="postbox-container-1" class="postbox-container">
                        <div id="formatdiv" class="postbox " style="display: block;">
                            <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Format</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span><?php _e( 'Visibility', 'ub' ); ?></span></h2>
                            <div class="inside">
                                <div id="post-formats-select">
                                    <fieldset>
                                        <legend class="screen-reader-text"><?php _e( ' You can now control the visibility of the Feed widget on the Dashboard. Unchecked - Hide the feed widget.', 'ub' ); ?></legend>
<ul>
    <li><label><input type="checkbox" name="widget[show-on][site]" <?php checked( $widget_options['show-on']['site'], 'on' ); ?> /> <?php _e( 'Show this feed on Site Dashboard.', 'ub' ); ?></label></li>
                        <?php if ( is_multisite() ) { ?>
    <li><label><input type="checkbox" name="widget[show-on][network]" <?php checked( $widget_options['show-on']['network'], 'on' );?> /> <?php _e( 'Show this feed on Network Dashboard.', 'ub' ); ?></label></li>
<?php } ?>
</ul>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>

<p class="submit">

                <input class="button-primary" type="submit" value="<?php _e( 'Submit', 'ub' ); ?>" class="primary-button"/>
                <a class="button-secondary" href="<?php echo $this->url; ?>"><?php _e( 'Cancel', 'ub' ); ?></a>
</p>
<?php
		}

		public function add_dashboard_widgets() {
			global $wp_version;
			$version_compare = version_compare( $wp_version, '3.7.1' );

			$widget_items = array();

			$widgets = $this->get_items();

			if ( ( ! $widgets) || ( ! is_array( $widgets )) ) {
				return;
			}

			foreach ( $widgets as $widget_id => $widget_options ) {
				// IF we still have them, ignore.
				if ( 0 >= $version_compare ) {
					if ( ($widget_id == 'df-dashboard_primary') || ($widget_id == 'df-dashboard_secondary') ) {
						continue;
					}
				}

				if ( (is_multisite()) && (is_network_admin()) ) {
					if ( (isset( $widget_options['show-on']['network'] )) && ($widget_options['show-on']['network'] == 'on') ) {
						$widget_items[ $widget_id ] = new WPMUDEV_Dashboard_Text_Widget();
						$widget_items[ $widget_id ]->init( $widget_id, $widget_options );
					}
				} else {
					if ( (isset( $widget_options['show-on']['site'] )) && ($widget_options['show-on']['site'] == 'on') ) {
						$widget_items[ $widget_id ] = new WPMUDEV_Dashboard_Text_Widget();
						$widget_items[ $widget_id ]->init( $widget_id, $widget_options );
					}
				}
			}
		}
	}
}
new ub_dashboard_text_widgets();

if ( ! class_exists( 'WPMUDEV_Dashboard_Texts_List_Table' ) ) {

	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}
	class WPMUDEV_Dashboard_Texts_List_Table extends WP_List_Table {

		private $url;

		function __construct() {
			global $status, $page;

			//Set parent defaults
			parent::__construct(
				array(
					'singular'  => 'Archive',     //singular name of the listed records
					'plural'    => 'Archive',    //plural name of the listed records
					'ajax'      => false,//does this table support ajax?
				)
			);
			$this->url = add_query_arg(
				array(
					'page' => 'branding',
					'tab' => 'dashboard-text-widgets',
				),
				is_network_admin()? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' )
			);
		}

		function get_table_classes() {
			return array( 'widefat', 'fixed', 'df-list-table' );
		}

		function get_columns() {
			$columns = array();
			$columns['title'] = __( 'Title', 'ub' );
			return $columns;
		}

		function get_hidden_columns() {
			$screen = get_current_screen();
			$hidden = get_hidden_columns( $screen );
			return $hidden;
		}

		function column_default( $item, $column_name ) {
			echo '&nbsp;';
		}

		function column_cb( $item ) {
			?><input type="checkbox" name="delete-bulk[]" value="<?php echo $item['timestamp']; ?>" /><?php
		}

		function column_title( $item ) {
			$edit_url 			= add_query_arg( 'number', $item['number'], $this->url );
			$action_edit_url 	= add_query_arg( 'act', 'edit', $edit_url );
			?><a href="<?php echo $action_edit_url ?>"><?php echo stripslashes( $item['title'] ) ?></a> <?php

			$row_actions = array();
			$row_actions['edit'] = '<span class="edit"><a href="'. $action_edit_url .'">' . __( 'Edit', 'ub' ) . '</a></span>';

			$action_delete_url = add_query_arg( 'act', 'delete', $edit_url );
			$action_delete_url = add_query_arg( 'nonce', wp_create_nonce( 'nonce' ), $action_delete_url );

			$row_actions['delete'] = '<span class="delete"><a href="'. $action_delete_url .'">'. __( 'Delete', 'ub' ) .'</a></span>';

if ( count( $row_actions ) ) {
	?><br /><div class="row-actions"><?php echo implode( ' | ', $row_actions ); ?></div><?php
}
		}

		function prepare_items( $df_items = array() ) {
			$columns 	= $this->get_columns();
			$hidden 	= $this->get_hidden_columns();
			$sortable	= array();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			foreach ( $df_items as $df_key => $df_item ) {
				if ( ! isset( $df_items[ $df_key ]['number'] ) ) {
					$df_items[ $df_key ]['number'] = $df_key;
				}
			}
			$per_page = get_user_meta( get_current_user_id(), 'wpmudev_dashboard_feeds_items_per_page', true );
			if ( ( ! $per_page) || ($per_page < 1) ) {
				$per_page = 15;
			}
			$current_page = $this->get_pagenum();
			if ( count( $df_items ) > $per_page ) {
				$this->items = array_slice( $df_items, (($current_page - 1) * intval( $per_page )), intval( $per_page ), true );
			} else {
				$this->items = $df_items;
			}
			$this->set_pagination_args(
				array(
					'total_items' => count( $df_items ),
					'per_page' => intval( $per_page ),
					'total_pages' => ceil( intval( count( $df_items ) ) / intval( $per_page ) ),
				)
			);
		}
	}
}

if ( ! class_exists( 'WPMUDEV_Dashboard_Text_Widget' ) ) {
	class WPMUDEV_Dashboard_Text_Widget {
		var $widget_id;
		var $widget_options;
		function init( $options_set = '', $options = array() ) {
			if ( empty( $options_set ) ) { return; }
			if ( empty( $options ) ) { return; }
			if ( strlen( $options_set ) ) {
				$this->widget_id = 'wpmudev_dashboard_text_item_'. $options_set;
				$options['number'] = $options_set;
			}
			$this->widget_options = $options;
			wp_add_dashboard_widget( $this->widget_id,
				stripslashes( $this->widget_options['title'] ),
				array( &$this, 'wp_dashboard_widget_display' )
			);
			add_action( 'admin_enqueue_scripts', array( $this, 'load_style' ) );
		}

		/**
		 * Load styles.
		 *
		 * @since 1.9.2
		 */
		public function load_style() {
			global $ub_version;
			wp_register_style( __CLASS__, plugins_url( 'dashboard-text-widgets-admin.css', __FILE__ ), false, $ub_version );
			wp_enqueue_style( __CLASS__ );
		}

		function wp_dashboard_widget_display() {
			$content = $this->widget_options['content'];
			if ( isset( $this->widget_options['content_parse'] ) ) {
				$content = $this->widget_options['content_parse'];
			}
			printf( '<div class="ub-widget">%s</div>', stripslashes( $content ) );
		}

		function wp_dashboard_widget_controls() {
			wp_widget_rss_form( $this->widget_options );
		}
	}
}