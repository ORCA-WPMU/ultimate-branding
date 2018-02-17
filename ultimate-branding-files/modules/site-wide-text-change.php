<?php
/*
Plugin Name: Network Wide Text Change
Plugin URI: http://premium.wpmudev.org/project/site-wide-text-change
Description: Would you like to be able to change any wording, anywhere in the entire admin area on your whole site? Without a single hack? Well, if that's the case then this plugin is for you!
Author: Barry (Incsub), Ulrich Sossou (incsub)
Network: true
 */
/*
Copyright 2007-2017 Incsub (http://incsub.com)
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
// Un comment for full belt and braces replacements, warning:
// 1. TEST TEST TEST
// define( 'SWTC-BELTANDBRACES', 'yes' );
/**
 * Plugin main class
 **/
class ub_Site_Wide_Text_Change {
	/**
	 * Current version of the plugin
	 **/
	var $build = '2.0.2';
	/**
	 * Stores translation tables
	 **/
	var $translationtable = false;
	/**
	 * Stores translations
	 **/
	var $translationops = false;
	/**
	 * PHP 5 constructor
	 **/
    function __construct() {
        global $ub_version;
        $this->build = $ub_version;
		add_action('admin_init', array(&$this, 'add_admin_header_sitewide'));
		add_filter('gettext', array($this, 'replace_text'), 10, 3);
		add_filter('gettext_with_context', array($this, 'replace_gettext_with_context'), 10, 4);
		if( defined('SWTC-BELTANDBRACES') ) {
			add_action('init', array(&$this, 'start_cache'), 1);
			add_action('admin_print_footer_scripts', array(&$this, 'end_cache'), 9999);
		}
		add_action('ultimatebranding_settings_textchange', array(&$this, 'handle_admin_page') );
		add_filter('ultimatebranding_settings_textchange_process', array(&$this, 'update_admin_page') );
		/**
		 * export
		 */
        add_filter( 'ultimate_branding_export_data', array( $this, 'export' ) );
        add_action( 'ultimatebranding_settings_textchange_after_title', array( $this, 'add_new_button' ) );
    }

	/**
	 * Show admin warning
	 **/
	function warning() {
		echo '<div id="update-nag">' . __('Warning, this page is not loaded with the full replacements processed.','ub') . '</div>';
	}
	/**
	 * Run before admin page display
	 *
	 * Enqueue scripts, remove output buffer and save settings
	 **/
	function add_admin_header_sitewide() {
		global $plugin_page;
		if( 'branding' !== $plugin_page  )
			return;
		if((isset($_GET['tab']) && $_GET['tab'] == 'textchange')) {
			wp_enqueue_style('sitewidecss', ub_files_url('modules/site-wide-text-change-files/sitewidetextincludes/styles/sitewide.css'), array(), $this->build);
			wp_enqueue_script('sitewidejs', ub_files_url('modules/site-wide-text-change-files/sitewidetextincludes/js/sitewideadmin.js'), array('jquery', 'jquery-form', 'jquery-ui-sortable'), $this->build);
			$this->update_admin_page();
		}
		if(defined('SWTC-BELTANDBRACES')) {
			add_action('admin_notices', array(&$this, 'warning'));
			//remove other actions
			remove_action('init', array(&$this, 'start_cache'));
			remove_action('admin_print_footer_scripts', array(&$this, 'end_cache'));
		}
	}
	/**
	 * Individual replace table output
	 **/
	function show_table($key, $table) {
		echo '<div class="postbox " id="swtc-' . $key . '">';
		echo '<div title="Click to toggle" class="handlediv"><br/></div><h3 class="hndle"><input type="checkbox" name="deletecheck[]" class="deletecheck" value="' . $key . '" /><span>' . $table['title'] . '</span></h3>';
		echo '<div class="inside">';
		echo "<table width='100%'>";
		echo "<tr>";
		echo "<td valign='top' class='heading'>";
		echo __('Find this text','ub');
		echo "</td>";
		echo "<td valign='top' class=''>";
		echo "<input type='text' name='swtble[$key][find]' value='" . esc_attr(stripslashes($table['find'])) . "' class='long find' />";
		echo "<br/>";
		echo "<input type='checkbox' name='swtble[$key][ignorecase]' class='case' value='1' ";
		if($table['ignorecase'] == '1') echo "checked='checked' ";
		echo "/>&nbsp;<span>" . __('Ignore case when replacing text.','ub') . "</span>";
		echo "<br />";
		echo "<input type='checkbox' name='swtble[$key][exclude_url]' class='case' value='1' ";
		if(isset($table['exclude_url']) && ($table['exclude_url'] == '1')) echo "checked='checked' ";
		echo "/>&nbsp;<span>" . __('Exclude urls when replacing text.','ub') . "</span>";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td valign='top' class='heading'>";
		echo __('in this text domain','ub');
		echo "</td>";
		echo "<td valign='top' class=''>";
		echo "<input type='text' name='swtble[$key][domain]' value='" . esc_attr(stripslashes($table['domain'])) . "' class='short domain' />";
		echo "&nbsp;<span>" . __('( leave blank for global changes )','ub') , '</span>';
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td valign='top' class='heading'>";
		echo __('and replace it with','ub');
		echo "</td>";
		echo "<td valign='top' class=''>";
		echo "<input type='text' name='swtble[$key][replace]' value='" . esc_attr(stripslashes($table['replace'])) . "' class='long replace' />";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td valign='top' class='heading'>";
		echo __('Admin/Front-end only?','ub');
		echo "</td>";
		echo "<td valign='top' class=''>";
?>
		<select  name='<?php echo "swtble[$key][admin_front]" ?>' class="admin_front">
			<option <?php selected("both", $table['admin_front']) ?> value="both"><?php _e("Both", "ub"); ?></option>
			<option <?php selected("admin", $table['admin_front']) ?> value="admin"><?php _e("Admin pages only", "ub"); ?></option>
			<option <?php selected("front", $table['admin_front']) ?> value="front"><?php _e("Front-end pages only", "ub"); ?></option>
		</select>
<?php
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo '</div>';
		echo '</div>';
	}
	/**
	 * Individual replace table output for javascript use
	 **/
	function show_table_template( $dt = '') {
		if(!empty($dt)) {
			echo '<div class="postbox blanktable" id="swtc-' . $dt . '" style="display: block;">';
		} else {
			echo '<div class="postbox blanktable" id="blanktable" style="display: none;">';
		}
		echo '<div title="Click to toggle" class="handlediv"><br/></div><h3 class="hndle"><input type="checkbox" name="deletecheck[{$dt}]" class="deletecheck" value="" /><span>' . __('New Text Change Rule','ub') . '</span></h3>';
		echo '<div class="inside">';
		echo "<table width='100%'>";
		echo "<tr>";
		echo "<td valign='top' class='heading'>";
		echo __('Find this text','ub');
		echo "</td>";
		echo "<td valign='top' class=''>";
		echo "<input type='text' name='swtble[{$dt}][find]' value='' class='long find' />";
		echo "<br/>";
		echo "<input type='checkbox' name='swtble[{$dt}][ignorecase]' class='case' value='1' ";
		echo "/>&nbsp;<span>" . __('Ignore case when finding text.','ub') . "</span>";
		echo "<br/>";
		echo "<input type='checkbox' name='swtble[{$dt}][exclude_url]' class='case' value='1' ";
		echo "/>&nbsp;<span>" . __('Exclude urls when replacing text.','ub') . "</span>";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td valign='top' class='heading'>";
		echo __('in this text <abbr title="A text domain is related to the internationisation of the text, you should leave this blank unless you know what it means.">domain</abbr>','ub');
		echo "</td>";
		echo "<td valign='top' class=''>";
		echo "<input type='text' name='swtble[{$dt}][domain]' value='' class='short domain' />";
		echo "&nbsp;<span>" . __('( leave blank for global changes )','ub') , '</span>';
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td valign='top' class='heading'>";
		echo __('and replace it with','ub');
		echo "</td>";
		echo "<td valign='top' class=''>";
		echo "<input type='text' name='swtble[{$dt}][replace]' value='' class='long replace' />";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td valign='top' class='heading'>";
		echo __('Admin only','ub');
		echo "</td>";
		echo "<td valign='top' class=''>";
?>
		<select  name='<?php echo "swtble[$dt][admin_front]" ?>' class="admin_front">
			<option value="both"><?php _e("Both", "ub"); ?></option>
			<option value="admin"><?php _e("Admin pages only", "ub"); ?></option>
			<option value="front"><?php _e("Front pages only", "ub"); ?></option>
		</select>
<?php
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo '</div>';
		echo '</div>';
	}
	/**
	 * Save admin settings
	 **/
	function update_admin_page( $status = false ) {
		if(!empty($_POST['delete'])) {
			$deletekeys = (array) $_POST['deletecheck'];
		} else {
			$deletekeys = array();
		}
		if(!empty($_POST['swtble'])) {
			$save = array();
			$op = array();
			foreach($_POST['swtble'] as $key => $table) {
				//				htmlentities("what's", ENT_QUOTES, 'UTF-8')
				if(!in_array($key, $deletekeys) && !empty($table['find'])) {
					$save[addslashes($key)]['title'] = 'Text Change : ' . esc_attr(stripslashes($table['find']));
					//					$save[addslashes($key)]['find'] = htmlentities($table['find'], CREDITS_ALL, 'UTF-8');
					$save[addslashes($key)]['find'] = $table['find'];
					$save[addslashes($key)]['ignorecase'] = isset($table['ignorecase']) ? $table['ignorecase'] : "0";
					$save[addslashes($key)]['exclude_url'] = isset($table['exclude_url']) ? $table['exclude_url'] : "0";
					$save[addslashes($key)]['domain'] = $table['domain'];
					$save[addslashes($key)]['replace'] = $table['replace'];
					$save[addslashes($key)]['admin_front'] = $table['admin_front'];
					//If exclude_url is set we define our filter. Else we use the normal one
					if(isset($table['exclude_url']) && $table['exclude_url'] == '1') {
						if(isset($table['ignorecase']) && $table['ignorecase'] == '1') {
							$op['domain-' . $table['domain']]['find'][] = '~(?i)<a.*?</a>(*SKIP)(*F)|\b' . stripslashes($table['find']) . '\b/i~';
						}
						else{
							$op['domain-' . $table['domain']]['find'][] = '~(?i)<a.*?</a>(*SKIP)(*F)|\b' . stripslashes($table['find']) . '\b~';
						}
						$op['domain-' . $table['domain']]['exclude_url'] = '1';
					}else{
						if(isset($table['ignorecase']) && $table['ignorecase'] == '1') {
							$op['domain-' . $table['domain']]['find'][] = '/' . str_replace('/','\/', stripslashes($table['find'])) . '/i';
						} else {
							$op['domain-' . $table['domain']]['find'][] = '/' . str_replace('/','\/', stripslashes($table['find'])) . '/';
						}
					}
					$op['domain-' . $table['domain']]['replace'][] = str_replace('/','\/', stripslashes($table['replace']));
					$op['domain-' . $table['domain']]['admin_front'] = $table['admin_front'];
				}
			}
			if(!empty($op)) {
				ub_update_option('translation_ops',$op);
				ub_update_option('translation_table',$save);
			} else {
				ub_update_option('translation_ops', 'none');
				ub_update_option('translation_table', 'none');
			}
		}
		if($status === false) {
			return $status;
		} else {
			return true;
		}
	}
	/**
	 * Admin page output
	 **/
	function handle_admin_page() {
		$translations = $this->get_translation_table(true);
		echo '<div class="tablenav">';
		echo '<div class="alignleft">';
		echo '<input class="button-secondary del" type="submit" name="delete" value="' . __('Delete selected', 'ub') . '" />';
		echo '</div>';
		echo '<div class="alignright">';
		echo '</div>';
		echo '</div>';
		echo "<div id='entryholder'>";
		if($translations && is_array($translations)) {
			foreach($translations as $key => $table) {
				$this->show_table($key, $table);
			}
		} else {
			$this->show_table_template( time() );
		}
		echo "</div>";	// Entry holder
		echo '<div class="tablenav">';
		echo '<div class="alignleft">';
		echo '<input class="button-secondary del" type="submit" name="delete" value="' . __('Delete selected', 'ub') . '" />';
		echo '</div>';
		echo '<div class="alignright">';
		echo '</div>';
		echo '</div>';
		$this->show_table_template();
	}
	/**
	 * Cache translation tables
	 **/
	function get_translation_table($reload = false) {
		if($this->translationtable && !$reload) {
			return $this->translationtable;
		} else {
			$this->translationtable = ub_get_option('translation_table', array());
			return $this->translationtable;
		}
	}
	/**
	 * Cache translations
	 **/
	function get_translation_ops($reload = false) {
		if($this->translationops && !$reload) {
			return $this->translationops;
		} else {
			$this->translationops = ub_get_option( 'translation_ops', array() );
			return $this->translationops;
		}
	}
	/**
	 * Finds-out where to the text change should be applied
	 *
	 * @param $prefix
	 *
	 * @return bool
	 */
	private function _get_admin_front($prefix){
		if( isset( $prefix['admin_front'] ) && $prefix['admin_front'] !== "both" ){
			return $admin_front = $prefix['admin_front'] === "admin" ? is_admin() : !is_admin();
		}
		return true;
	}
	/**
	 * Filters gettext
	 *
	 * @param $transtext
	 * @param $normtext
	 * @param $domain
	 * @return mixed
	 */
	function replace_text( $transtext, $normtext, $domain ) {
		$tt = $this->get_translation_ops();
		$admin_front = true;
		if( !is_array( $tt ) ) {
            return $transtext;
        }
		$toprocess = array();
		if( isset( $tt['domain-' . $domain]['find'] ) && isset( $tt['domain-']['find'] ) ){
			$toprocess =  (array) $tt['domain-' . $domain]['find'] + (array) $tt['domain-']['find'];
			$admin_front = $this->_get_admin_front( $tt['domain-' . $domain] );
		}elseif( isset( $tt['domain-' . $domain]['find'] ) ){
			$toprocess =  (array) $tt['domain-' . $domain]['find'];
			$admin_front = $this->_get_admin_front( $tt['domain-' . $domain] );
		}elseif( isset( $tt['domain-']['find'] ) ){
			$toprocess =  (array) $tt['domain-']['find'];
			$admin_front = $this->_get_admin_front( $tt['domain-'] );
		}
		$toreplace = array();
		if( isset( $tt['domain-' . $domain]['replace'] ) && isset( $tt['domain-']['replace'] ) )
			$toreplace =  (array) $tt['domain-' . $domain]['replace'] + (array) $tt['domain-']['replace'];
		elseif( isset( $tt['domain-' . $domain]['replace'] ) )
			$toreplace =  (array) $tt['domain-' . $domain]['replace'];
		elseif( isset( $tt['domain-']['replace'] ) )
			$toreplace =  (array) $tt['domain-']['replace'];
		if( $admin_front ){
			$transtext =  str_replace("&#8217;", "’", $transtext);
			//Check if exclude url is enabled
			if( isset( $tt['domain-']['exclude_url'] ) && $tt['domain-']['exclude_url'] == '1' ){
				//Replacing dots with a random valid text, as '/b' will fail in http://text.text_to_replace.com. The "text_to_replace" will be replaced if '.'
				$str_key = '_WPMUDEV_UB_' . wp_generate_password( 12, false , false );
				$transtext = str_replace( '.', $str_key, $transtext );
				foreach( $toprocess as $key => $processee ){
					$processee = $this->_escape_punctuations( $processee );
				}
				$replaced_transtext = preg_replace( $toprocess, $toreplace, $transtext  );
				$replaced_transtext = str_replace( $str_key, '.', $replaced_transtext );
				$transtext = empty($replaced_transtext) ? $transtext : $replaced_transtext;
			}
			else {
				/**
				 * Escape punctuations
				 */
				foreach( $toprocess as &$processee ){
					$processee = $this->_escape_punctuations( $processee );
				}
				$replaced_transtext = preg_replace( $toprocess, $toreplace, $transtext  );
				$transtext = empty($replaced_transtext) ? $transtext : $replaced_transtext;
			}
		}
		return $transtext;
	}
	/**
	 * Filters gettext_with_context
	 *
	 * @param $translations
	 * @param $text
	 * @param $context
	 * @param $domain
	 * @return mixed
	 */
	function replace_gettext_with_context(  $translations, $text, $context, $domain ){
		return $this->replace_text( $translations, $text, $domain );
	}
	/**
	 * Start output buffer
	 **/
	function start_cache() {
		ob_start();
	}
	/**
	 * End output buffer
	 **/
	function end_cache() {
		$tt = $this->get_translation_ops();
		if( !is_array( $tt ) ) {
			ob_end_flush();
		} else {
			$content = ob_get_contents();
			$toprocess = (array) $tt['domain-']['find'];
			$toreplace = (array) $tt['domain-']['replace'];
			$content = preg_replace( $toprocess, $toreplace, $content );
			ob_end_clean();
			echo $content;
		}
	}
	/**
	 * Escapes punctuations in $string
	 * @param $string
	 *
	 * @return mixed
	 */
	private function _escape_punctuations( $string ) {
		$string = str_replace( array(
			"?",
			"!",
			"*",
			"$",
			"€",
            "£",
            '$',
            '%',
		), array(
			"\?",
			"\!",
			"\*",
			"\$",
			"\€",
			"\£",
            '\$',
            '\%',
		), $string );
		return $string;
	}
	/**
	 * Export data.
	 *
	 * @since 1.8.6
	 */
	public function export( $data ) {
		$options = array(
			'translation_table',
			'translation_ops',
		);
		foreach ( $options as $key ) {
			$data['modules'][ $key ] = ub_get_option( $key );
		}
		return $data;
    }

    /**
     * Add new button
	 *
	 * @since 1.8.6
     */
    public function add_new_button() {
        printf(
            '<a class="add-new-h2" href="#addnew" id="addnewtextchange">%s</a>',
            esc_html__( 'Add New', 'ub' )
        );
    }
}
new ub_Site_Wide_Text_Change();
