<?php
/**
 * Class UB_Blog_Favicons
 * @since 1.8.1
 * @var $wpdb WPDB
 */
if ( ! class_exists( 'UB_Blog_Favicons' ) ) :
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

	class UB_Blog_Favicons extends WP_List_Table{

		/**
	 * Construst the table
	 *
	 * @since 1.8.1
	 */
		function __construct() {
			return parent::__construct(array(
				'plural' => 'ub_site_favicons',
				'singular' => 'ub_site_favicons',
				'ajax' => true,
				'screen' => 'ub_site_favicons',
			));
		}

		/**
	 * Defines columns
	 *
	 * @since 1.8.1
	 *
	 * @return array
	 */
		public function get_columns() {
			return  array(
			'blog_id'    => __( 'Blog ID', 'ub' ),
			'domain'    => __( 'Domain', 'ub' ),
			'favicon'    => __( 'Favicon', 'ub' ),
			);
		}


		/**
	 * Defines sortable columns
	 *
	 * @since 1.8.1
	 *
	 * @return array
	 */
		protected function get_sortable_columns() {
			return array(
			'blog_id' => 'blog_id',
			'domain' => 'domain',
			);
		}

		/**
	 * Fetches records from database.
	 *
	 * @since 1.8.1
	 *
	 * @global wpdb $wpdb The database connection.
	 */
		public function prepare_items() {
			global $wpdb;
			/**
		 * @var $wpdb WPDB
		 */
			$per_page = 10;

			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			$search_term = '';
			if ( isset( $_REQUEST['s'] ) && ! empty( $_REQUEST['s'] ) ) {
				$search_term = $_REQUEST['s'];
			}

			$offset = isset( $_GET['paged'] ) ? ( $_GET['paged'] - 1 )   : 0;
			$offset = $offset * $per_page;

			$order_type = isset( $_GET['order'] ) && strtolower( $_GET['order'] ) === 'desc' ? 'desc' : 'asc';

			$order_columns = isset( $_GET['orderby'] ) && $_GET['orderby'] === 'd' ? 'domain' :  'blog_id';

			$q = $wpdb->prepare( "SELECT *  FROM $wpdb->blogs WHERE `deleted`=0 AND `blog_id` != %d ORDER BY $order_columns $order_type  LIMIT %d offset %d", get_current_blog_id(),  $per_page, $offset );

			$total_items = $wpdb->get_row( "SELECT count(blog_id) as count  FROM $wpdb->blogs WHERE `public`=1 AND `deleted`=0 ", OBJECT );
			$total_items = $total_items->count;

			$total_pages = $total_items % $per_page === 0 ? $total_items / $per_page : ( intval( $total_items / $per_page ) + 1 );

			$this->items = $wpdb->get_results( $q, OBJECT );

			$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page' => $per_page,
				'total_pages' => $total_pages,
				'orderby'	=> 'subsite',
			) );

		}


		/**
	 * Renders blog_id column
	 *
	 * @param $site
	 *
	 * @since 1.8.1
	 *
	 * @return mixed
	 */
		public function column_blog_id( $site ) {
			return $site->blog_id;
		}

		/**
	 * Renders domain column
	 *
	 * @param $site
	 *
	 * @since 1.8.1
	 *
	 * @return string
	 */
		public function column_domain( $site ) {
			$url = ( is_ssl() ? 'https://' : 'http://' ) .  $site->domain . $site->path;
			$label = $site->path === '/' ? $site->domain : str_replace( '/', '', $site->path );
			return sprintf( "<a href='%s'>%s</a>",$url, $label );
		}


		/**
	 * Renders favicon column
	 *
	 * @since 1.8.1
	 *
	 * @param $site
	 */
		public function column_favicon( $site ) {
			$input_prefix = 'ub_favicons[' . $site->blog_id . ']';
			$reset_nonce_name = 'ub_favicons_' . $site->blog_id . '_reset';
			$fav = ub_favicons::get_favicon( $site->blog_id );
			$url = ub_favicons::has_favicon( $site->blog_id ) ? esc_url( ub_favicons::get_favicon( $site->blog_id, false ) ) : '';
		?>
            <ul>
                <li class="ub_favicons_fav_li">
                    <img class="ub_favicons_fav" height="16" width="16" data-default="<?php echo ub_favicons::get_main_favicon(); ?>" src="<?php echo $fav; ?>" alt=""/>
                </li>
                <li class="ub_favicons_text_li">
                    <input class="ub_favicons_fav_url" name="<?php echo $input_prefix ?>[url]" value="<?php  echo $url ; ?>" type="text"/> <button class="button ub_favicons_browse"><?php _e( 'Browse', 'ub' ); ?></button>
                    <input type="hidden" name="<?php echo $input_prefix ?>[id]" class="ub_favicons_fav_id"/>
                    <input type="hidden" name="<?php echo $input_prefix ?>[size]" class="ub_favicons_fav_size"/>
                    <?php wp_nonce_field( 'ub_save_favicon', $input_prefix . '[nonce]' ); ?>
                    <?php wp_nonce_field( 'ub_reset_favicon', $reset_nonce_name ); ?>
                </li>
                <li class="ub_favicons_reset_li">
                    <button class="button ub_favicons_reset" data-id="<?php echo $site->blog_id; ?>">Reset</button>
                </li>
                <li class="ub_favicons_save_li">
                    <button class="button button-primary ub_favicons_save">Save</button>
                </li>
                <li>
                    <span class="spinner ub_favicons_spinner"></span>
                </li>
            </ul>
	<?php
		}

		/**
	 * Returns bulk actions
	 *
	 * @since 1.8.1
	 *
	 * @return array
	 */
		public function get_bulk_actions() {
			return array();
		}

		/**
	 * Renders table nav
	 *
	 * @since 1.8.1
	 *
	 * @param string $which
	 */
		protected function display_tablenav( $which ) {
		?>
            <div class="tablenav <?php echo esc_attr( $which ); ?>">

            <div class="alignleft actions bulkactions">
                <?php $this->bulk_actions( $which ); ?>
            </div>
	<?php
		$this->extra_tablenav( $which );
		$this->pagination( $which );
	?>

            <br class="clear" />
            </div>
	<?php
		}
	}
endif;