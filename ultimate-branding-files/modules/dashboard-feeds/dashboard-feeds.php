<?php
/*
Plugin Name: Dashboard Feeds
Description: Customize the dashboard for every user in a flash with this straightforward dashboard feed replacement widget... no more WP development news or Matt's latest photo set :)

License: GNU General Public License (Version 2 - GPLv2)

Copyright 2012-2017 Incsub (http://incsub.com)

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

if ( ! class_exists( 'WPMUDEV_Dashboard_Feeds' ) ) {

	class WPMUDEV_Dashboard_Feeds {
		private $_settings;
		private $_pagehooks = array();
		private $_messages = array();
		private $url;

		var $wpmudev_dashboard_feeds_list_table;

		function __construct() {
			$this->url = add_query_arg(
				array(
					'page' => 'branding',
					'tab' => 'dashboard-feeds',
				),
				is_network_admin()? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' )
			);

			add_action( 'admin_footer', 					array( $this, 'admin_footer_proc' ), 1 );

			add_action( 'wp_dashboard_setup', 				array( $this, 'add_dashboard_widgets' ), 99 );
			add_action( 'wp_network_dashboard_setup', 		array( $this, 'add_dashboard_widgets' ), 99 );
			add_action( 'wp_user_dashboard_setup', 			array( $this, 'add_dashboard_widgets' ), 99 );

			add_action( 'ultimatebranding_settings_dashboard_feeds', array( $this, 'admin_options_page' ) );
			add_action( 'ultimatebranding_settings_dashboard_feeds_process', array( $this, 'update' ) );
			add_filter( 'ultimatebranding_settings_dashboard_feeds_messages', array( $this, 'messages' ) );
		}

		public function messages( $messages ) {
			$messages['success-add'] 				= __( 'The Feed item has been added.', 'ub' );
			$messages['success-update'] 				= __( 'The Feed item has been updated.', 'ub' );
			$messages['success-delete'] 				= __( 'The Feed item has been deleted.', 'ub' );
			$messages['success-settings'] 			= __( 'Settings have been update.', 'ub' );
			return $messages;
		}

		function admin_footer_proc() {
			$js_commands = '';

			// We only want to add this for the Dashboard and/or Network Dashboard screens
			$screen = get_current_screen();
			if ( ($screen->id != 'dashboard') && ($screen->id != 'dashboard-network') ) { return; }

			$df_settings = $this->get_df_widget_settings();

			if ( is_multisite() ) {
				$df_widgets = $this->get_df_feed_widgets_items();
				//echo "df_widgets<pre>"; print_r($df_widgets); echo "</pre>";
				if ( is_network_admin() ) {
					if ( (isset( $df_settings['wordpress-feed-widget'] )) && ($df_settings['wordpress-feed-widget']['network'] == 'on') ) {
						$js_commands .= "jQuery('div#dashboard_primary').hide(); ";
						$js_commands .= "var input_hide = jQuery('div.metabox-prefs input#dashboard_primary-hide'); jQuery(input_hide).hide(); ";
						$js_commands .= "var label_hide = jQuery(input_hide).parent('label'); jQuery(label_hide).hide();";
					}

					if ( isset( $df_widgets['df-dashboard_primary'] ) ) {
						if ( ( ! isset( $df_widgets['df-dashboard_primary']['show-on']['network'] ))
						 || ($df_widgets['df-dashboard_primary']['show-on']['network'] != 'on') ) {

			 				$js_commands .= "jQuery('div#dashboard_primary').hide(); ";
			 				$js_commands .= "var input_hide_primary = jQuery('div.metabox-prefs input#dashboard_primary-hide'); jQuery(input_hide_primary).hide(); ";
			 				$js_commands .= "var label_hide_primary = jQuery(input_hide_primary).parent('label'); jQuery(label_hide_primary).hide();";
						}
					}

					if ( isset( $df_widgets['df-dashboard_secondary'] ) ) {
						if ( ( ! isset( $df_widgets['df-dashboard_secondary']['show-on']['network'] ))
						 || ($df_widgets['df-dashboard_secondary']['show-on']['network'] != 'on') ) {

	 		 				$js_commands .= "jQuery('div#dashboard_secondary').hide(); ";
	 		 				$js_commands .= "var input_hide_secondary = jQuery('div.metabox-prefs input#dashboard_secondary-hide'); jQuery(input_hide_secondary).hide(); ";
	 		 				$js_commands .= "var label_hide_secondary = jQuery(input_hide_secondary).parent('label'); jQuery(label_hide_secondary).hide();";
						}
					}
				} else {
					if ( (isset( $df_settings['wordpress-feed-widget'] )) && ($df_settings['wordpress-feed-widget']['site'] == 'on') ) {
						$js_commands .= "jQuery('div#dashboard_primary').hide(); ";
						$js_commands .= "var input_hide = jQuery('div.metabox-prefs input#dashboard_primary-hide'); jQuery(input_hide).hide(); ";
						$js_commands .= "var label_hide = jQuery(input_hide).parent('label'); jQuery(label_hide).hide();";
					}

					if ( isset( $df_widgets['df-dashboard_primary'] ) ) {
						if ( ( ! isset( $df_widgets['df-dashboard_primary']['show-on']['site'] ))
						 || ($df_widgets['df-dashboard_primary']['show-on']['site'] != 'on') ) {

			 				$js_commands .= "jQuery('div#dashboard_primary').hide(); ";
			 				$js_commands .= "var input_hide_primary = jQuery('div.metabox-prefs input#dashboard_primary-hide'); jQuery(input_hide_primary).hide(); ";
			 				$js_commands .= "var label_hide_primary = jQuery(input_hide_primary).parent('label'); jQuery(label_hide_primary).hide();";

						}
					}

					if ( isset( $df_widgets['df-dashboard_secondary'] ) ) {
						if ( ( ! isset( $df_widgets['df-dashboard_secondary']['show-on']['site'] ))
						 || ($df_widgets['df-dashboard_secondary']['show-on']['site'] != 'on') ) {

	 		 				$js_commands .= "jQuery('div#dashboard_secondary').hide(); ";
	 		 				$js_commands .= "var input_hide_secondary = jQuery('div.metabox-prefs input#dashboard_secondary-hide'); jQuery(input_hide_secondary).hide(); ";
	 		 				$js_commands .= "var label_hide_secondary = jQuery(input_hide_secondary).parent('label'); jQuery(label_hide_secondary).hide();";
						}
					}
				}
			}

			if ( isset( $df_settings['force-dashboard_primary'] ) ) {
				$js_commands .= "jQuery('div#dashboard_primary span.postbox-title-action').hide(); ";
			}
			if ( isset( $df_settings['force-dashboard_secondary'] ) ) {
				$js_commands .= "jQuery('div#dashboard_secondary span.postbox-title-action').hide(); ";
			}
			if ( strlen( $js_commands ) ) {
				?>
                <script type="text/javascript">
                /* WPMUDEV Dashboard Feeds begin */
                jQuery(document).ready( function($) {
					<?php echo $js_commands; ?>
                });
                /* WPMUDEV Dashboard Feeds end */
                </script>
				<?php
			}
		}

		function update() {

			if ( isset( $_POST['df-form-submit'] ) ) {

				if ( (isset( $_GET['subpage'] )) && ($_GET['subpage'] == 'general-settings') ) {

					if ( isset( $_POST['df_settings'] ) ) {
						$df_settings = $this->get_df_widget_settings();

						if ( isset( $_POST['df_settings']['wordpress-feed-widget']['site'] ) ) {
							$df_settings['wordpress-feed-widget']['site'] = 'on';
						} else {
							$df_settings['wordpress-feed-widget']['site'] = false;
						}
						if ( isset( $_POST['df_settings']['wordpress-feed-widget']['network'] ) ) {
							$df_settings['wordpress-feed-widget']['network'] = 'on';
						} else {
							$df_settings['wordpress-feed-widget']['network'] = false;
						}
						$this->set_df_widget_settings( $df_settings );
					}

					$return_url = remove_query_arg( array( 'subpage', 'nonce', 'action', 'number' ) );
					$return_url = add_query_arg( 'msg', 'success-settings', $return_url );
					wp_redirect( $return_url );
					die();

				} else if ( isset( $_POST['widget-rss'] ) ) {
					$df_widgets = $this->get_df_feed_widgets_items();
					if ( ( ! $df_widgets) || ( ! is_array( $df_widgets )) ) {
						$df_widgets = array(); }

					if ( isset( $_POST['widget-rss']['df-new'] ) ) {
						$widget_count = 1;
						while ( true ) {
							$widget_id = sprintf( 'df-%d', $widget_count );
							if ( ! isset( $df_widgets[ $widget_id ] ) ) {
								break;
							} else {
								$widget_count += 1;
							}
						}

						$df_widgets[ $widget_id ] = array();

						if ( isset( $_POST['widget-rss']['df-new']['link'] ) ) {
							$df_widgets[ $widget_id ]['link'] = esc_url( $_POST['widget-rss']['df-new']['link'] ); } else {
							$df_widgets[ $widget_id ]['link'] = ''; }

							if ( isset( $_POST['widget-rss']['df-new']['url'] ) ) {
								$df_widgets[ $widget_id ]['url'] = esc_url( $_POST['widget-rss']['df-new']['url'] ); } else {
								$df_widgets[ $widget_id ]['url'] = ''; }

								if ( isset( $_POST['widget-rss']['df-new']['title'] ) ) {
									$df_widgets[ $widget_id ]['title'] = esc_attr( $_POST['widget-rss']['df-new']['title'] ); } else {
									$df_widgets[ $widget_id ]['title'] = ''; }

									if ( isset( $_POST['widget-rss']['df-new']['items'] ) ) {
										$df_widgets[ $widget_id ]['items'] = intval( $_POST['widget-rss']['df-new']['items'] ); } else {
										$df_widgets[ $widget_id ]['items'] = 10; }

										if ( isset( $_POST['widget-rss']['df-new']['show_summary'] ) ) {
											$df_widgets[ $widget_id ]['show_summary'] = intval( $_POST['widget-rss']['df-new']['show_summary'] ); } else {
											$df_widgets[ $widget_id ]['show_summary'] = false; }

											if ( isset( $_POST['widget-rss']['df-new']['show_author'] ) ) {
												$df_widgets[ $widget_id ]['show_author'] = intval( $_POST['widget-rss']['df-new']['show_author'] ); } else {
												$df_widgets[ $widget_id ]['show_author'] = false; }

												if ( isset( $_POST['widget-rss']['df-new']['show_date'] ) ) {
													$df_widgets[ $widget_id ]['show_date'] = intval( $_POST['widget-rss']['df-new']['show_date'] ); } else {
													$df_widgets[ $widget_id ]['show_date'] = false; }

													if ( isset( $_POST['widget-rss']['df-new']['show-on'] ) ) {
														if ( isset( $_POST['widget-rss']['df-new']['show-on']['network'] ) ) {
															$df_widgets[ $widget_id ]['show-on']['network'] = esc_attr( $_POST['widget-rss']['df-new']['show-on']['network'] ); } else {
															$df_widgets[ $widget_id ]['show-on']['network'] = false; }

															if ( isset( $_POST['widget-rss']['df-new']['show-on']['site'] ) ) {
																$df_widgets[ $widget_id ]['show-on']['site'] = esc_attr( $_POST['widget-rss']['df-new']['show-on']['site'] ); } else {
																$df_widgets[ $widget_id ]['show-on']['site'] = false; }
													} else {
																				$df_widgets[ $widget_id ]['show-on'] = array();
																				$df_widgets[ $widget_id ]['show-on']['network'] = false;
																				$df_widgets[ $widget_id ]['show-on']['site'] = false;
													}
													$this->set_df_feed_widgets_items( $df_widgets );

													$return_url = add_query_arg( 'msg', 'success-add', $this->url );
													wp_redirect( $return_url );
													die();

					} else {

						foreach ( $_POST['widget-rss'] as $widget_id => $widget_options ) {
							// We want to remove the existing widget item. Then readd.
							if ( ! isset( $df_widgets[ $widget_id ] ) ) { continue; }

							$df_widgets[ $widget_id ] = array();

							if ( isset( $_POST['widget-rss'][ $widget_id ]['link'] ) ) {
								$df_widgets[ $widget_id ]['link'] 	= esc_url( $_POST['widget-rss'][ $widget_id ]['link'] ); } else {
								$df_widgets[ $widget_id ]['link'] 	= ''; }

								if ( isset( $_POST['widget-rss'][ $widget_id ]['url'] ) ) {
									$df_widgets[ $widget_id ]['url'] 		= esc_url( $_POST['widget-rss'][ $widget_id ]['url'] ); } else {
									$df_widgets[ $widget_id ]['url']		= ''; }

									if ( isset( $_POST['widget-rss'][ $widget_id ]['title'] ) ) {
										$df_widgets[ $widget_id ]['title'] 	= esc_attr( $_POST['widget-rss'][ $widget_id ]['title'] ); } else {
										$df_widgets[ $widget_id ]['title']	= ''; }

										if ( isset( $_POST['widget-rss'][ $widget_id ]['items'] ) ) {
											$df_widgets[ $widget_id ]['items'] 	= intval( $_POST['widget-rss'][ $widget_id ]['items'] ); } else {
											$df_widgets[ $widget_id ]['items']	= 10; }

											if ( isset( $_POST['widget-rss'][ $widget_id ]['show_summary'] ) ) {
												$df_widgets[ $widget_id ]['show_summary'] = intval( $_POST['widget-rss'][ $widget_id ]['show_summary'] ); } else {
												$df_widgets[ $widget_id ]['show_summary'] = false; }

												if ( isset( $_POST['widget-rss'][ $widget_id ]['show_author'] ) ) {
													$df_widgets[ $widget_id ]['show_author'] = intval( $_POST['widget-rss'][ $widget_id ]['show_author'] ); } else {
													$df_widgets[ $widget_id ]['show_author'] = false; }

													if ( isset( $_POST['widget-rss'][ $widget_id ]['show_date'] ) ) {
														$df_widgets[ $widget_id ]['show_date'] = intval( $_POST['widget-rss'][ $widget_id ]['show_date'] ); } else {
														$df_widgets[ $widget_id ]['show_date'] = false; }

														if ( isset( $_POST['widget-rss'][ $widget_id ]['show-on'] ) ) {
															if ( isset( $_POST['widget-rss'][ $widget_id ]['show-on']['network'] ) ) {
																$df_widgets[ $widget_id ]['show-on']['network'] = esc_attr( $_POST['widget-rss'][ $widget_id ]['show-on']['network'] ); } else {
																$df_widgets[ $widget_id ]['show-on']['network'] = false; }

																if ( isset( $_POST['widget-rss'][ $widget_id ]['show-on']['site'] ) ) {
																	$df_widgets[ $widget_id ]['show-on']['site'] = esc_attr( $_POST['widget-rss'][ $widget_id ]['show-on']['site'] ); } else {
																	$df_widgets[ $widget_id ]['show-on']['site'] = false; }
														} else {
															$df_widgets[ $widget_id ]['show-on'] = array();
															$df_widgets[ $widget_id ]['show-on']['network'] = false;
															$df_widgets[ $widget_id ]['show-on']['site'] = false;
														}

														$this->set_df_feed_widgets_items( $df_widgets );

														if ( ($widget_id == 'df-dashboard_primary') || ($widget_id == 'df-dashboard_secondary') ) {
															$wp_widgets = get_option( 'dashboard_widget_options' );
															if ( $widget_id == 'df-dashboard_primary' ) {
																$wp_widgets['dashboard_primary'] = $df_widgets[ $widget_id ];

																$cache_key = 'dash_' . md5( 'dashboard_primary' );
																delete_transient( $cache_key );
																$feed_name = 'feed_' . $cache_key;
																delete_transient( $feed_name );
																$feed_mod_name = 'feed_mod_' . $cache_key;
																delete_transient( $feed_mod_name );

																$df_settings = $this->get_df_widget_settings();
																if ( isset( $_POST['df_settings']['force-dashboard_primary'] ) ) {
																	$df_settings['force-dashboard_primary'] = 'on';
																} else {
																	if ( isset( $df_settings['force-dashboard_primary'] ) ) {
																		unset( $df_settings['force-dashboard_primary'] );
																	}
																}
																$this->set_df_widget_settings( $df_settings );
															}
															if ( $widget_id == 'df-dashboard_secondary' ) {
																$wp_widgets['dashboard_secondary'] = $df_widgets[ $widget_id ];

																$cache_key = 'dash_' . md5( 'dashboard_secondary' );
																delete_transient( $cache_key );
																$feed_name = 'feed_' . $cache_key;
																delete_transient( $feed_name );
																$feed_mod_name = 'feed_mod_' . $cache_key;
																delete_transient( $feed_mod_name );

																$df_settings = $this->get_df_widget_settings();
																if ( isset( $_POST['df_settings']['force-dashboard_secondary'] ) ) {
																	$df_settings['force-dashboard_secondary'] = 'on';
																} else {
																	if ( isset( $df_settings['force-dashboard_secondary'] ) ) {
																		unset( $df_settings['force-dashboard_secondary'] );
																	}
																}
																$this->set_df_widget_settings( $df_settings );
															}
															update_option( 'dashboard_widget_options', $wp_widgets );
														}

														$cache_key = 'dash_' . md5( $widget_id );
														delete_transient( $cache_key );
														$feed_name = 'feed_' . $cache_key;
														delete_transient( $feed_name );
														$feed_mod_name = 'feed_mod_' . $cache_key;
														delete_transient( $feed_mod_name );

														$return_url = remove_query_arg( array( 'nonce', 'action', 'number' ) );
														$return_url = add_query_arg( 'msg', 'success-update', $return_url );
														wp_redirect( $return_url );
														die();
						}
					}
				}
			}

			if ( (isset( $_POST['wp_screen_options']['option'] ))
			 && ($_POST['wp_screen_options']['option'] == 'settings_page_dashboard_feeds_per_page') ) {

				if ( isset( $_POST['wp_screen_options']['value'] ) ) {
					$per_page = intval( $_POST['wp_screen_options']['value'] );
					if ( ( ! $per_page) || ($per_page < 1) ) {
						$per_page = 20;
					}
					update_user_meta( get_current_user_id(), 'wpmudev_dashboard_feeds_items_per_page', $per_page );
				}
			} else {
				$per_page = get_user_meta( get_current_user_id(), 'wpmudev_dashboard_feeds_items_per_page', true );
				if ( ! $per_page ) {
					$per_page = 15; }
			}

			add_screen_option( 'per_page', array( 'label' => __( 'per Page', 'ub' ), 'default' => $per_page ) );
		}

		public function admin_options_page() {
			global $wp_version, $ub_version;
			add_filter( 'ultimatebranding_settings_panel_show_submit', '__return_false' );
			$version_compare = version_compare( $wp_version, '3.7.1' );

			$_SHOW_LISTING = true;
			$_ACTION_MESSAGE = '';

			wp_enqueue_style( __CLASS__, plugins_url( '/dashboard-feeds.css', __FILE__ ), false, $ub_version );

			if ( ! isset( $_GET['subpage'] ) ) {
				$this->wpmudev_dashboard_feeds_list_table = new WPMUDEV_Dashboard_Feeds_List_Table();
			}

			if ( (isset( $_GET['subpage'] )) && ($_GET['subpage'] == 'general-settings') ) {
				$this->show_dashboard_feed_settings_form();
			} else {
				if ( (isset( $_GET['act'] )) && ($_GET['act'] == 'add') ) {
					?>
                    <div id="wpmudev-dashvboard-feeds-panel" class="wrap wpmudev-dashvboard-feeds-wrap">
						<h2><?php _ex( 'Add New Dashboard Feed', 'New Page Title', 'ub' ); ?></h2>
						<?php $this->show_dashboard_feed_form(); ?>
                    </div>
					<?php
					$_SHOW_LISTING = false;
				} else if ( (isset( $_GET['act'] )) && ($_GET['act'] == 'edit') ) {
					if ( (isset( $_GET['number'] )) && ( ! empty( $_GET['number'] )) ) {
						$df_widgets = $this->get_df_feed_widgets_items();
						$df_item_number 		= esc_attr( $_GET['number'] );
						if ( isset( $df_widgets[ $df_item_number ] ) ) {
							$df_widgets[ $df_item_number ]['number'] = $df_item_number;

?>
                            <div id="wpmudev-dashvboard-feeds-panel" class="wrap wpmudev-dashvboard-feeds-wrap">
                                <h2><?php _ex( 'Edit Dashboard Feed', 'New Page Title', 'ub' ); ?></h2>
                                <?php $this->show_dashboard_feed_form( $df_widgets[ $df_item_number ] ); ?>
                            </div>
<?php
							$_SHOW_LISTING = false;
						}
					}
				} else if ( (isset( $_GET['act'] )) && ($_GET['act'] == 'delete') ) {
					if ( ! empty( $_GET ) && (wp_verify_nonce( $_GET['nonce'],'nonce' )) ) {
						if ( (isset( $_GET['number'] )) && ( ! empty( $_GET['number'] )) ) {
							$df_widgets = $this->get_df_feed_widgets_items();
							$df_item_number = esc_attr( $_GET['number'] );
							if ( isset( $df_widgets[ $df_item_number ] ) ) {
								unset( $df_widgets[ $df_item_number ] );
								$this->set_df_feed_widgets_items( $df_widgets );
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
							// We only show the general settings for WP 3.8 and higher.
						if ( $version_compare >= 1 ) {
							if ( ! (isset( $_GET['act'] )) ) {
								$action_url = add_query_arg( 'subpage', 'general-settings', $this->url );
								?><a class="button-dashboard-feeds-extra button-secondary" href="<?php echo $action_url; ?>"><?php _e( 'General Settings', 'ub' ); ?></a><?php
							}
						}
						?>
				 		<?php
							$df_widgets = $this->get_df_feed_widgets_items();
						if ( ( ! $df_widgets) || ( ! is_array( $df_widgets )) ) {
							$df_widgets = array(); }

							$this->show_dashboard_feed_list_table( $df_widgets );
						?>
                    </div>
					<?php
				}
			}
		}

		function show_dashboard_feed_form( $widget_options = array() ) {
			if ( (isset( $widget_options['number'] )) && ( ! empty( $widget_options['number'] )) ) {
				$widget_options['number'] 			= esc_attr( $widget_options['number'] ); } else {
				$widget_options['number']			= 'df-new'; }

			?>
            <form id="dashboard-feeds-form" method="post" action="">
                <input name="df-form-submit" value="1" type="hidden" />
				<input name="widget-rss[<?php echo $widget_options['number']; ?>][number]" value="<?php echo $widget_options['number'] ?>" type="hidden" />
                <table class="df_dashboard_widgets">
                <tr>
					<td><?php $this->wp_widget_rss_form( $widget_options ); ?></td>
                </tr>
                </table>
				<input class="button-primary" type="submit" value="<?php _e( 'Submit', 'ub' ); ?>" class="primary-button"/>
				<a class="button-secondary" href="<?php echo $this->url; ?>"><?php _e( 'Cancel', 'ub' ); ?></a>
            </form>
			<?php
		}

		/* Copied from wp-includes/default-widgets.php becase I didn't like the main doem */
		function wp_widget_rss_form( $widget_options, $inputs = null ) {

			global $wp_version;
			$version_compare = version_compare( $wp_version, '3.7.1' );

			$df_settings = $this->get_df_widget_settings();

			//echo "widget_options<pre>"; print_r($widget_options); echo "</pre>";

			if ( (isset( $widget_options['number'] )) && ( ! empty( $widget_options['number'] )) ) {
				$widget_options['number'] 			= esc_attr( $widget_options['number'] ); } else {
				$widget_options['number']			= 'df-new'; }

				if ( (isset( $widget_options['title'] )) && ( ! empty( $widget_options['title'] )) ) {
					$widget_options['title']  			= esc_attr( $widget_options['title'] ); } else {
					$widget_options['title']			= ''; }

					if ( (isset( $widget_options['url'] )) && ( ! empty( $widget_options['url'] )) ) {
						$widget_options['url']    			= esc_url( $widget_options['url'] ); } else {
						$widget_options['url']				= ''; }

						if ( (isset( $widget_options['link'] )) && ( ! empty( $widget_options['link'] )) ) {
							$widget_options['link']    			= esc_url( $widget_options['link'] ); } else {
							$widget_options['link']				= ''; }

							if ( isset( $widget_options['items'] ) ) {
								$widget_options['items']  			= (int) $widget_options['items']; } else {
								$widget_options['items']			= 10; }

								if ( $widget_options['items'] < 1 || 20 < $widget_options['items'] ) {
									$widget_options['items']  = 10; }

								if ( isset( $widget_options['show_summary'] ) ) {
									$widget_options['show_summary']   	= (int) $widget_options['show_summary']; } else {
									$widget_options['show_summary']		= false; }

									if ( isset( $widget_options['show_author'] ) ) {
										$widget_options['show_author']    	= (int) $widget_options['show_author']; } else {
										$widget_options['show_author']		= false; }

										if ( isset( $widget_options['show_date'] ) ) {
											$widget_options['show_date']      	= (int) $widget_options['show_date']; } else {
											$widget_options['show_date']		= false; }

											if ( isset( $widget_options['show-on'] ) ) {
												if ( isset( $widget_options['show-on']['network'] ) ) {
													$widget_options['show-on']['network'] = esc_attr( $widget_options['show-on']['network'] ); } else {
													$widget_options['show-on']['network'] = false; }

													if ( isset( $widget_options['show-on']['site'] ) ) {
														$widget_options['show-on']['site'] = esc_attr( $widget_options['show-on']['site'] ); } else {
														$widget_options['show-on']['site'] = false; }
											} else {
												$widget_options['show-on'] = array();
												$widget_options['show-on']['network'] = 'on';
												$widget_options['show-on']['site'] = 'on';
											}
			?>

			<?php
			if ( 0 >= $version_compare ) {
				$label_description = __( 'Checked - This will remove the "configure" link on the widget header.(Recommended)<br />Unchecked - allow individual users to control this widget on their own Dashboards. When the feed is saved here again it will replace the custom settings.', 'ub' );

				if ( $widget_options['number'] == 'df-dashboard_primary' ) {
					?>
					<p><input type="checkbox" name="df_settings[force-dashboard_primary]" id="df-settings-force-dashboard-primary" <?php
					if ( isset( $df_settings['force-dashboard_primary'] ) ) { echo ' checked="checked" '; } ?> /> <label
							for="df-settings-force-dashboard-primary"><?php echo $label_description; ?></label></p>
						<?php
				} else if ( $widget_options['number'] == 'df-dashboard_secondary' ) {
					?>
					<p><input type="checkbox" name="df_settings[force-dashboard_secondary]" id="df-settings-force-dashboard-secondary" <?php
					if ( isset( $df_settings['force-dashboard_secondary'] ) ) { echo ' checked="checked" '; } ?> /> <label
						for="df-settings-force-dashboard-secondary"><?php echo $label_description; ?></label></p>
						<?php
				}
			}
			?>

			<p><label for="rss-title-<?php echo $widget_options['number']; ?>"><?php _e( 'Give the feed a title (optional):', 'ub' ); ?></label>
			<input class="widefat" id="rss-title-<?php echo $widget_options['number']; ?>" name="widget-rss[<?php echo $widget_options['number']; ?>][title]"
				type="text" value="<?php echo $widget_options['title']; ?>" /></p>

			<p><label for="rss-link-<?php echo $widget_options['number']; ?>"><?php _e( 'Enter Site URL:', 'ub' ); ?></label>
			<input class="widefat" id="rss-link-<?php echo $widget_options['number']; ?>"
				name="widget-rss[<?php echo $widget_options['number']; ?>][link]" type="text"
				value="<?php echo $widget_options['link']; ?>" /></p>

			<p><label for="rss-url-<?php echo $widget_options['number']; ?>"><?php _e( 'Enter the RSS feed URL:', 'ub' ); ?></label>
			<input class="widefat" id="rss-url-<?php echo $widget_options['number']; ?>"
				name="widget-rss[<?php echo $widget_options['number']; ?>][url]" type="text"
				value="<?php echo $widget_options['url']; ?>" /></p>

			<p><select id="rss-items-<?php echo $widget_options['number']; ?>" name="widget-rss[<?php echo $widget_options['number']; ?>][items]">
			<?php
			for ( $i = 1; $i <= 20; ++$i ) {
				echo "<option value='$i' " . selected( $widget_options['items'], $i, false ) . ">$i</option>"; }
			?>
			</select> <label for="rss-items-<?php echo $widget_options['number']; ?>"><?php _e( 'How many feed items would you like to display?', 'ub' ); ?></label>
            </p>

			<p><input id="rss-show-summary-<?php echo $widget_options['number']; ?>"
				name="widget-rss[<?php echo $widget_options['number']; ?>][show_summary]" type="checkbox"
				value="1" <?php if ( $widget_options['show_summary'] ) { echo 'checked="checked"'; } ?>/>
			<label for="rss-show-summary-<?php echo $widget_options['number']; ?>"><?php _e( 'Display item full content? Unchecked - Will show only excerpt.', 'ub' ); ?></label></p>

			<p><input id="rss-show-author-<?php echo $widget_options['number']; ?>"
				name="widget-rss[<?php echo $widget_options['number']; ?>][show_author]" type="checkbox"
				value="1" <?php if ( $widget_options['show_author'] ) { echo 'checked="checked"'; } ?>/>
			<label for="rss-show-author-<?php echo $widget_options['number']; ?>"><?php _e( 'Display item author if available?', 'ub' ); ?></label></p>

			<p><input id="rss-show-date-<?php echo $widget_options['number']; ?>"
				name="widget-rss[<?php echo $widget_options['number']; ?>][show_date]" type="checkbox"
				value="1" <?php if ( $widget_options['show_date'] ) { echo 'checked="checked"'; } ?>/>
			<label for="rss-show-date-<?php echo $widget_options['number']; ?>"><?php _e( 'Display item date?', 'ub' ); ?></label></p>

			<p><?php _e( ' You can now control the visibility of the Feed widget on the Dashboard. Unchecked - Hide the feed widget.', 'ub' ); ?><br />

				<input type="checkbox" name="widget-rss[<?php echo $widget_options['number'] ?>][show-on][site]" id="df-settings-show-on-site-<?php echo $widget_options['number']; ?>" <?php if ( $widget_options['show-on']['site'] == 'on' ) { echo ' checked="checked" '; } ?> /> <label for="df-settings-show-on-site-<?php echo $widget_options['number']; ?>"><?php _e( 'Checked - Show this feed on Site Dashboard.', 'ub' ); ?></label>

			<?php if ( is_multisite() ) { ?>
				<br /><input type="checkbox" name="widget-rss[<?php echo $widget_options['number'] ?>][show-on][network]" id="df-settings-show-on-network-<?php echo $widget_options['number']; ?>" <?php if ( $widget_options['show-on']['network'] == 'on' ) { echo ' checked="checked" '; } ?> /> <label for="df-settings-show-on-network-<?php echo $widget_options['number']; ?>"><?php _e( 'Checked - Show this feed on Network Dashboard.', 'ub' ); ?></label>
			<?php } ?></p><?php
		}

		function show_dashboard_feed_settings_form() {
			$df_settings = $this->get_df_widget_settings();
			?>
            <form id="dashboard-feeds-settings-form" method="post" action="">
                <input name="df-form-submit" value="1" type="hidden" />
				<h3><?php _e( 'WordPress Primary & Secondary Feed Widgets', 'ub' ); ?></h3>

				<p><?php _e( 'WordPress 3.8 introduced some changes in the handling of the Dashboard Primary and Secondary Feed Widgets. Prior to WordPress 3.8 these were two separate widgets. In WordPress 3.8 these are now combined into a single widget. You can set the checkbox below to hide this combined widget from view. If you had previously defined Primary and Secondary feeds in this plugin, they will be automatically converted to individual widget items on your Dashboard.', 'ub' ); ?></p>

                <p>
					<input type="checkbox" name="df_settings[wordpress-feed-widget][site]" id="df-settings-wordpress-feed-widget-site" <?php if ( (isset( $df_settings['wordpress-feed-widget']['site'] )) && ($df_settings['wordpress-feed-widget']['site'] == 'on') ) { echo ' checked="checked" '; } ?> /> <label for="df-settings-wordpress-feed-widget-site"><?php _e( 'Check - Hide Combined Primary and Secondary Dashboard Feed Widget on Site.' )?></label>
					<?php if ( is_multisite() ) {?>
						<br /><input type="checkbox" name="df_settings[wordpress-feed-widget][network]" id="df-settings-wordpress-feed-widget-network" <?php if ( (isset( $df_settings['wordpress-feed-widget']['network'] )) && ($df_settings['wordpress-feed-widget']['network'] == 'on') ) { echo ' checked="checked" '; } ?> /> <label for="df-settings-wordpress-feed-widget-network"><?php _e( 'Check - Hide Combined Primary and Secondary Dashboard Feed Widget on Network.' )?></label>
				<?php } ?>
                </p>

				<input class="button-primary" type="submit" value="<?php _e( 'Submit', 'ub' ); ?>" class="primary-button"/>
				<a class="button-secondary" href="<?php echo $this->url; ?>"><?php _e( 'Cancel', 'ub' ); ?></a>
            </form>
			<?php
		}

		function add_dashboard_widgets() {
			global $wp_version;
			$version_compare = version_compare( $wp_version, '3.7.1' );

			$widget_items = array();

			$df_widgets = $this->get_df_feed_widgets_items();

			if ( ( ! $df_widgets) || ( ! is_array( $df_widgets )) ) {
				$df_widgets = array(); }

			foreach ( $df_widgets as $widget_id => $widget_options ) {
				// IF we still have them, ignore.
				if ( 0 >= $version_compare ) {
					if ( ($widget_id == 'df-dashboard_primary') || ($widget_id == 'df-dashboard_secondary') ) {
						continue;
					}
				}

				if ( (is_multisite()) && (is_network_admin()) ) {
					if ( (isset( $widget_options['show-on']['network'] )) && ($widget_options['show-on']['network'] == 'on') ) {
						$widget_items[ $widget_id ] = new WPMUDEV_Dashboard_Feed_Widget();
						$widget_items[ $widget_id ]->init( $widget_id, $widget_options );
					}
				} else {
					if ( (isset( $widget_options['show-on']['site'] )) && ($widget_options['show-on']['site'] == 'on') ) {
						$widget_items[ $widget_id ] = new WPMUDEV_Dashboard_Feed_Widget();
						$widget_items[ $widget_id ]->init( $widget_id, $widget_options );
					}
				}
			}
		}

		function convert_legacy_wordpress_widgets( $df_widgets = array() ) {
			if ( isset( $df_widgets['df-dashboard_primary'] ) ) {
				$count = 0;
				foreach ( $df_widgets as $widget_id => $widget_options ) {
					if ( ($widget_id != 'df-dashboard_primary') && ($widget_id != 'df-dashboard_secondary') ) {
						$count += 1;
					}
				}
				$new_widget_id = sprintf( 'df-%d', intval( $count ) + 1 );
				$df_widgets[ $new_widget_id ] = $df_widgets['df-dashboard_primary'];
				unset( $df_widgets['df-dashboard_primary'] );
			}

			if ( isset( $df_widgets['df-dashboard_secondary'] ) ) {
				$count = 0;
				foreach ( $df_widgets as $widget_id => $widget_options ) {
					if ( ($widget_id != 'df-dashboard_primary') && ($widget_id != 'df-dashboard_secondary') ) {
						$count += 1;
					}
				}
				$new_widget_id = sprintf( 'df-%d', intval( $count ) + 1 );
				$df_widgets[ $new_widget_id ] = $df_widgets['df-dashboard_secondary'];
				unset( $df_widgets['df-dashboard_secondary'] );
			}
			return $df_widgets;
		}

		function get_df_feed_widgets_items() {
			if ( is_multisite() ) {
				global $current_blog;
				if ( $current_blog->site_id == $current_blog->blog_id ) {
					$df_widgets = get_blog_option( $current_blog->site_id, 'wpmudev_df_widget_options' );
					if ( ! is_array( $df_widgets ) ) {
						$df_widgets = get_option( 'wpmudev_df_widget_options' );
					}
				} else {
					$df_widgets = get_blog_option( $current_blog->site_id, 'wpmudev_df_widget_options' );
				}
			} else {
				$df_widgets = get_option( 'wpmudev_df_widget_options' );
			}
			return $df_widgets;
		}

		function set_df_feed_widgets_items( $df_widgets ) {
			if ( is_multisite() ) {
				global $current_blog;

				if ( is_array( $df_widgets ) ) {
					update_blog_option( $current_blog->site_id, 'wpmudev_df_widget_options', $df_widgets );
				} else {
					delete_blog_option( $current_blog->site_id, 'wpmudev_df_widget_options' );
				}
			} else {
				if ( is_array( $df_widgets ) ) {
					update_option( 'wpmudev_df_widget_options', $df_widgets );
				} else {
					delete_option( 'wpmudev_df_widget_options' );
				}
			}
		}

		function get_df_widget_settings() {
			if ( is_multisite() ) {
				global $current_blog;

				if ( $current_blog->site_id == $current_blog->blog_id ) {
					$df_settings = get_blog_option( $current_blog->site_id, 'dashboard_widget_settings' );
					if ( ! is_array( $df_settings ) ) {
						$df_settings = get_option( 'dashboard_widget_settings' );
					}
				} else {
					$df_settings = get_blog_option( $current_blog->site_id, 'dashboard_widget_settings' );
				}
			} else {
				$df_settings = get_option( 'dashboard_widget_settings', array() );
			}

			if ( ! isset( $df_settings['wordpress-feed-widget'] ) ) {
				$df_settings['wordpress-feed-widget'] = array();
			}
			if ( ! isset( $df_settings['wordpress-feed-widget']['site'] ) ) {
				$df_settings['wordpress-feed-widget']['site'] = 'on';
			}
			if ( ! isset( $df_settings['wordpress-feed-widget']['network'] ) ) {
				$df_settings['wordpress-feed-widget']['network'] = 'on';
			}

			return $df_settings;
		}

		function set_df_widget_settings( $df_settings ) {
			if ( is_multisite() ) {
				global $current_blog;
				if ( is_array( $df_settings ) ) {
					update_blog_option( $current_blog->site_id, 'dashboard_widget_settings', $df_settings );
				} else {
					delete_blog_option( $current_blog->site_id, 'dashboard_widget_settings' );
				}
			} else {
				if ( is_array( $df_settings ) ) {
					update_option( 'dashboard_widget_settings', $df_settings );
				} else {
					delete_option( 'dashboard_widget_settings' );
				}
			}
		}

		function show_dashboard_feed_list_table( $df_items = array() ) {
			$this->wpmudev_dashboard_feeds_list_table->prepare_items( $df_items );
			$this->wpmudev_dashboard_feeds_list_table->display();
		}
	}
}
$wpmudev_dashboard_feeds = new WPMUDEV_Dashboard_Feeds();

if ( ! class_exists( 'WPMUDEV_Dashboard_Feeds_List_Table' ) ) {

	if ( ! class_exists( 'WP_List_Table' ) ) {
	    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}
	class WPMUDEV_Dashboard_Feeds_List_Table extends WP_List_Table {

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
					'tab' => 'dashboard-feeds',
				),
				is_network_admin()? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' )
			);
	    }

		function WPMUDEV_Dashboard_Feeds_List_Table() {
	        $this->__construct();
		}

		function get_table_classes() {
			return array( 'widefat', 'fixed', 'df-list-table' );
		}

	    function get_columns() {

			$columns = array();

			$columns['title']		= __( 'Title', 		'ub' );
			//$columns['number']        =   __('ID',            'ub');
			$columns['feedurl']		= __( 'Feed URL', 		'ub' );
			$columns['siteurl']		= __( 'Site URL', 		'ub' );
			$columns['meta']		= __( 'Meta', 			'ub' );

	        return $columns;
	    }

		function get_hidden_columns() {
			$screen 	= get_current_screen();
			$hidden 	= get_hidden_columns( $screen );

			return $hidden;
		}

	    function column_default( $item, $column_name ) {
			//echo "column_name=[". $column_name ."]<br />";
			//echo "item<pre>"; print_r($item); echo "</pre>";
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

		function column_number( $item ) {
			echo $item['number'];
		}

		function column_feedurl( $item ) {
			echo esc_url( $item['url'] );
		}

		function column_siteurl( $item ) {
			echo esc_url( $item['link'] );
		}

		function column_meta( $item ) {
			//echo "item<pre>"; print_r($item); echo "</pre>";

			if ( isset( $item['items'] ) ) {
				echo __( 'Count', 'ub' ). ': '. intval( $item['items'] );
				echo '<br />';
			}

			if ( isset( $item['show_summary'] ) ) {
				echo __( 'Summary', 'ub' ). ': ';
				($item['show_summary'] == 1) ? _e( 'Yes', 'ub' ) : _e( 'No', 'ub' );
				echo '<br />';
			}

			if ( isset( $item['show_author'] ) ) {
				echo __( 'Author', 'ub' ). ': ';
				($item['show_author'] == 1) ? _e( 'Yes', 'ub' ) : _e( 'No', 'ub' );
				echo '<br />';
			}

			if ( isset( $item['show_date'] ) ) {
				echo __( 'Date', 'ub' ). ': ';
				($item['show_date'] == 1) ? _e( 'Yes', 'ub' ) : _e( 'No', 'ub' );
				echo '<br />';
			}

			if ( is_multisite() ) {
				if ( (isset( $item['show-on'] )) && is_array( $item['show-on'] ) && (count( $item['show-on'] )) ) {
					echo __( 'Show on', 'ub' ). ': ';
					$show_on_str = '';
					if ( isset( $item['show-on']['site'] ) ) {
						if ( $item['show-on']['site'] == 'on' ) {
							if ( ! empty( $show_on_str ) ) { $show_on_str .= ', '; }
							$show_on_str .= __( 'Site', 'ub' );
						}
					}
					if ( isset( $item['show-on']['network'] ) ) {
						if ( $item['show-on']['network'] == 'on' ) {
							if ( ! empty( $show_on_str ) ) { $show_on_str .= ', '; }
							$show_on_str .= __( 'Network', 'ub' );
						}
					}
					if ( empty( $show_on_str ) ) { $show_on_str = __( 'None', 'ub' ); }
					echo $show_on_str;
				}
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
					'total_items' => count( $df_items ),                  			// WE have to calculate the total number of items
					'per_page'    => intval( $per_page ),                     			// WE have to determine how many items to show on a page
					'total_pages' => ceil( intval( count( $df_items ) ) / intval( $per_page ) ),// WE have to calculate the total number of pages
				)
			);
		}
	}
}


if ( ! class_exists( 'WPMUDEV_Dashboard_Feed_Widget' ) ) {

	class WPMUDEV_Dashboard_Feed_Widget {
		var $widget_id;
		var $widget_options;

		function __construct() {
		}

		function WPMUDEV_Dashboard_Feed_Widget() {
			$this->__construct();
		}

		function init( $options_set = '', $options = array() ) {
			if ( empty( $options_set ) ) { return; }
			if ( empty( $options ) ) { return; }
			if ( strlen( $options_set ) ) {
				$this->widget_id = 'wpmudev_dashboard_item_'. $options_set;
				$options['number'] = $options_set;
			}
			$this->widget_options = $options;
			/**
			 * setup widget title if is not defined
			 */
			$title = $this->widget_options['title'];
			if ( empty( $title ) ) {
				if ( ! empty( $this->widget_options['link'] ) ) {
					$title = $this->widget_options['link'];
				}
				if ( empty( $title ) ) {
					$title = $this->widget_options['url'];
				}
				$title = preg_replace( '/^[^\/]+\/\//', '', $title );
				$title = preg_replace( '/\/.*/', '', $title );
			}
			if ( empty( $title ) ) {
				$title = __( '[no title]', 'ub' );
			}
			/**
			 * Decode url
			 */
			$this->widget_options['url'] = htmlspecialchars_decode( $this->widget_options['url'] );
			wp_add_dashboard_widget(
				$this->widget_id,
				$title,
				array( &$this, 'wp_dashboard_widget_display' )
			);
		}

		function wp_dashboard_widget_display() {

			$rss = @fetch_feed( $this->widget_options['url'] );

			if ( is_wp_error( $rss ) ) {
				if ( is_admin() || current_user_can( 'manage_options' ) ) {
					echo '<div class="rss-widget"><p>';
					printf( __( '<strong>RSS Error</strong>: %s' ), $rss->get_error_message() );
					echo '</p></div>';
				}
			} elseif ( ! $rss->get_item_quantity() ) {
				$rss->__destruct();
				unset( $rss );
				return false;

			} else {
				echo '<div class="rss-widget">';
				wp_widget_rss_output( $rss, $this->widget_options );
				echo '</div>';
				$rss->__destruct();
				unset( $rss );
			}
		}

		function wp_dashboard_widget_controls() {
			wp_widget_rss_form( $this->widget_options );
		}
	}
}