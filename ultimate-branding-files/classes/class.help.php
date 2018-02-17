<?php
if ( ! class_exists( 'UB_Help' ) ) {

	class UB_Help {
		// The screen we want to access help for
		var $screen = false;

		function __construct( &$screen = false ) {
			$this->screen = $screen;
			//$this->set_global_sidebar_content();
		}

		function attach() {
			if ( preg_match( '/^toplevel_page_branding/', $this->screen->id ) ) {
				if ( ! isset( $_GET['tab'] ) ) {
					$tab = 'dashboard';
				} else {
					$tab = stripslashes( $_GET['tab'] );
				}
				switch ( $tab ) {
					case 'dashboard':
						$this->dashboard_help();
					break;

					case 'sitegenerator':
						$this->sitegenerator_help();
					break;

					case 'footer':
						$this->footer_help();
					break;

					case 'permalinks':
						$this->permalinks_help();
					break;

					case 'signuppassword':
						$this->signuppassword_help();
					break;

					case 'textchange':
						$this->textchange_help();
					break;

					case 'widgets':
						$this->widgets_help();
					break;

					case 'images':
						$this->images_help();
					break;

					case 'help':
						$this->help_help();
					break;

					case 'adminbar':
						$this->adminbar_help();
					break;

					case 'css':
						$this->css_help();
					break;
				}
			}
		}

		// Specific help content creation functions
		function set_global_sidebar_content() {
			ob_start();
			include_once( membership_dir( 'membershipincludes/help/help.sidebar.php' ) );
			$help = ob_get_clean();
			$this->screen->set_help_sidebar( $help );
		}

		function dashboard_help() {
			ob_start();
			include_once( ub_files_dir( 'help/contextual.dashboard.php' ) );
			$help = ob_get_clean();
			$this->screen->add_help_tab( array(
				'id'      => 'dashboard',
				'title'   => __( 'Dashboard', 'ub' ),
				'content' => $help,
			) );
		}

		function sitegenerator_help() {
			ob_start();
			include_once( ub_files_dir( 'help/contextual.sitegenerator.php' ) );
			$help = ob_get_clean();
			$this->screen->add_help_tab( array(
				'id'      => 'sitegenerator',
				'title'   => __( 'Custom Site Generator', 'ub' ),
				'content' => $help,
			) );
		}

		function footer_help() {
			ob_start();
			include_once( ub_files_dir( 'help/contextual.footer.php' ) );
			$help = ob_get_clean();
			$this->screen->add_help_tab( array(
				'id'      => 'footer',
				'title'   => __( 'Custom Footer Content', 'ub' ),
				'content' => $help,
			) );
		}

		function permalinks_help() {
			ob_start();
			include_once( ub_files_dir( 'help/contextual.permalinks.php' ) );
			$help = ob_get_clean();
			$this->screen->add_help_tab( array(
				'id'      => 'permalinks',
				'title'   => __( 'Permalinks Menu' , 'ub' ),
				'content' => $help,
			) );
		}

		function signuppassword_help() {
			ob_start();
			include_once( ub_files_dir( 'help/contextual.signuppassword.php' ) );
			$help = ob_get_clean();
			$this->screen->add_help_tab( array(
				'id'      => 'signuppassword',
				'title'   => __( 'Signup Password Module' , 'ub' ),
				'content' => $help,
			) );
		}

		function textchange_help() {
			ob_start();
			include_once( ub_files_dir( 'help/contextual.textchange.php' ) );
			$help = ob_get_clean();
			$this->screen->add_help_tab( array(
				'id'      => 'textchange',
				'title'   => __( 'Network Text Change' , 'ub' ),
				'content' => $help,
			) );
		}

		function widgets_help() {
			ob_start();
			include_once( ub_files_dir( 'help/contextual.widgets.php' ) );
			$help = ob_get_clean();
			$this->screen->add_help_tab( array(
				'id'      => 'widgets',
				'title'   => __( 'Widgets' , 'ub' ),
				'content' => $help,
			) );
		}

		function images_help() {
			ob_start();
			include_once( ub_files_dir( 'help/contextual.images.php' ) );
			$help = ob_get_clean();
			$this->screen->add_help_tab( array(
				'id'      => 'images',
				'title'   => __( 'Custom Images' , 'ub' ),
				'content' => $help,
			) );
		}

		function help_help() {
			ob_start();
			include_once( ub_files_dir( 'help/contextual.help.php' ) );
			$help = ob_get_clean();
			$this->screen->add_help_tab( array(
				'id'      => 'help',
				'title'   => __( 'Custom Help Content' , 'ub' ),
				'content' => $help,
			) );
		}

		function adminbar_help() {
			ob_start();
			include_once( ub_files_dir( 'help/contextual.adminbar.php' ) );
			$help = ob_get_clean();
			$this->screen->add_help_tab( array(
				'id'      => 'adminbar',
				'title'   => __( 'Custom Admin Bar' , 'ub' ),
				'content' => $help,
			) );
		}

		function css_help() {
			ob_start();
			include_once( ub_files_dir( 'help/contextual.css.php' ) );
			$help = ob_get_clean();
			$this->screen->add_help_tab( array(
				'id'      => 'css',
				'title'   => __( 'Custom CSS' , 'ub' ),
				'content' => $help,
			) );
		}


		/**
		 * Escapes output of native add_query_arg
		 * @return string
		 */
		public static function add_query_arg() {
			$args = func_get_args();
			return esc_url( call_user_func_array( 'add_query_arg', $args ) );
		}

		/**
		 * Raw escapes output of native add_query_arg
		 * @return string
		 */
		public static function add_query_arg_raw() {
			$args = func_get_args();
			return esc_url_raw( call_user_func_array( 'add_query_arg', $args ) );
		}

		/**
		 * Escapes output of native remove_query_arg
		 * @return string
		 */
		public static function remove_query_arg() {
			$args = func_get_args();
			return esc_url( call_user_func_array( 'remove_query_arg', $args ) );
		}

		/**
		 * Raw escapes output of native remove_query_arg
		 * @return string
		 */
		public static function remove_query_arg_raw() {
			$args = func_get_args();
			return esc_url_raw( call_user_func_array( 'remove_query_arg', $args ) );
		}
	}

}