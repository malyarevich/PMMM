<?php
	/**
	* Plugin Name: Tracking Script Manager
	* Plugin URI: http://wordpress.org/plugins/tracking-script-manager/
	* Description: A plugin that allows you to add tracking scripts to your site.
	* Version: 1.1.6
	* Author: Red8 Interactive
	* Author URI: http://red8interactive.com
	* License: GPL2
	*/
 
	/*  
		Copyright 2014 Red8 Interactive  (email : james@red8interactive.com) 
	
		This program is free software; you can redistribute it and/or
		modify it under the terms of the GNU General Public License
		as published by the Free Software Foundation; either version 2
		of the License, or (at your option) any later version.
		
		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.
		
		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
	*/
	
	if ( ! defined( 'ABSPATH' ) ) { 
    	exit; // Exit if accessed directly
	}
	
	if( !class_exists('Tracking_Scripts') ) {
		
		class Tracking_Scripts {
		
			function __construct() {
				self::define_constants();
				self::load_hooks();
			}
	
			/**
			* Defines plugin constants
			*/
			public static function define_constants() {
				define('TRACKING_SCRIPT_PATH', plugins_url( ' ', __FILE__ ) ); 
				define('TRACKING_SCRIPT_BASENAME', plugin_basename( __FILE__ ));
				define('TRACKING_SCRIPT_TEXTDOMAIN', 'tracking-scripts-manager');
				
				define('WP_TRACKING_SCRIPTS_OPTION_GROUP', 'tracking_scripts_options' );
				define('WP_HEADER_TRACKING_SCRIPT', 'header_tracking_script_code' );
				define('WP_FOOTER_TRACKING_SCRIPT', 'footer_tracking_script_code' );
				define('WP_PAGE_TRACKING_SCRIPT', 'page_tracking_script_code' );
				define('WP_NEW_HEADER_TRACKING_SCRIPT', 'new_header_tracking_script_code' );
				define('WP_NEW_FOOTER_TRACKING_SCRIPT', 'new_footer_tracking_script_code' );
				define('WP_NEW_PAGE_TRACKING_SCRIPT', 'new_page_tracking_script_code' );
				define('WP_NEW_PAGE_TRACKING_SCRIPT_ID', 'new_page_tracking_script_code_id' );
				define('WP_NEW_PAGE_TRACKING_SCRIPT_LOCATION', 'new_page_tracking_script_code_location' );
				define('WP_NEW_PAGE_TRACKING_SCRIPT_GLOBAL', 'new_page_tracking_script_code_global' );
				define('WP_PAGE_TRACKING_SCRIPT_COUNT', 'page_tracking_script_count');
			}
			
			public static function load_hooks() {
				add_action('wp_head', array(__CLASS__, 'find_header_tracking_codes'));
				
				add_action('wp_footer', array(__CLASS__, 'find_footer_tracking_codes'));
				
				add_action('admin_menu', array(__CLASS__, 'tracking_scripts_create_menu'));
				
				add_action('admin_init', array(__CLASS__, 'initialize_admin_posts'));

				add_action( 'wp_ajax_tracking_scripts_get_posts', array(__CLASS__, 'tracking_scripts_posts_ajax_handler') );
				
				add_action( 'wp_ajax_tracking_scripts_get_post_content', array(__CLASS__, 'tracking_scripts_post_content_ajax_handler') );
			}
	
			/*************************************************
			 * Front End
			**************************************************/
		
			// Header Tracking Codes
			public static function find_header_tracking_codes() {
				$header_scripts = unserialize(get_option(WP_HEADER_TRACKING_SCRIPT));
				
				if($header_scripts) {
					foreach($header_scripts as $script) {
						if($script->active) {
							echo html_entity_decode(esc_attr($script->script_code), ENT_QUOTES, 'cp1252');
						}
					}
				}

				$page_scripts = unserialize(get_option(WP_PAGE_TRACKING_SCRIPT));
				
				if($page_scripts) {
					global $wp_query;
					$post_id = $wp_query->post->ID;
					foreach($page_scripts as $script) {
						if($script->active && $script->location == 'header' && $script->page_id == $post_id) {
							echo html_entity_decode(esc_attr($script->script_code), ENT_QUOTES, 'cp1252');
						}
					}
				}
			}
			
			
			// Footer Tracking Codes
			public static function find_footer_tracking_codes() {
				$footer_scripts = unserialize(get_option(WP_FOOTER_TRACKING_SCRIPT));
				
				if($footer_scripts) {
					foreach($footer_scripts as $script) {
						if($script->active) {
							echo html_entity_decode(esc_attr($script->script_code), ENT_QUOTES, 'cp1252');
						}
					}
				}

				$page_scripts = unserialize(get_option(WP_PAGE_TRACKING_SCRIPT));
				
				if($page_scripts) {
					global $wp_query;
					$post_id = $wp_query->post->ID;
					foreach($page_scripts as $script) {
						if($script->active && $script->location == 'footer' && $script->page_id == $post_id) {
							echo html_entity_decode(esc_attr($script->script_code), ENT_QUOTES, 'cp1252');
						}
					}
				}
			}

			/*************************************************
			 * Admin Area
			**************************************************/
			
			public static function tracking_scripts_create_menu() {
				add_menu_page('Tracking Script Manager', 'Tracking Script Manager', 'manage_options', __FILE__, array(__CLASS__, 'tracking_options'), '');
				add_action('admin_init', array(__CLASS__, 'register_tracking_scripts_settings'));
			}
			
			public static function register_tracking_scripts_settings() {
				register_setting( WP_TRACKING_SCRIPTS_OPTION_GROUP, WP_HEADER_TRACKING_SCRIPT, 'esc_textarea' );
				register_setting( WP_TRACKING_SCRIPTS_OPTION_GROUP, WP_FOOTER_TRACKING_SCRIPT, 'esc_textarea' );
			}
			
			public static function tracking_scripts_admin_tabs( $current = 'add_new' ) {
				$tabs = array('add_new' => 'Add New', 'global' => 'Global', 'pages' => 'Specific Location');
				?><h2><?php _e( 'Tracking Script Manager', TRACKING_SCRIPT_TEXTDOMAIN ); ?></h2>
				<h2 class="nav-tab-wrapper"><?php
				foreach($tabs as $tab => $name) {
					$class = ($tab == $current) ? ' nav-tab-active' : '';
					?><a class="nav-tab<?php echo $class; ?>" href="?page=<?php echo TRACKING_SCRIPT_BASENAME; ?>&tab=<?php echo $tab; ?>"><?php _e( $name, TRACKING_SCRIPT_TEXTDOMAIN ); ?></a><?php
				}
				?></h2><?php
			}
			
			public static function tracking_options() {
				self::tracking_scripts_admin_scripts();
				?>
				<div class="wrap tracking_scripts_wrap">
				<?php 
					global $pagenow;
					$settings = get_option('tracking_scripts_settings');
					
					if(isset($_GET['tab'])) {
						self::tracking_scripts_admin_tabs($_GET['tab']);
						$pagenow = $_GET['tab']; 
					} else {
						self::tracking_scripts_admin_tabs('add_new'); 
						$pagenow = 'add_new';
					}
				?>
					<form method="post" action="<?php echo get_admin_url(); ?>admin-post.php">
						<?php settings_fields(WP_TRACKING_SCRIPTS_OPTION_GROUP); ?>
						<?php do_settings_sections(WP_TRACKING_SCRIPTS_OPTION_GROUP); ?>
						<?php if($pagenow == 'add_new') { ?>
							<?php include_once('templates/tracking-scripts-manager-pro-add-new.php'); ?>
					    <?php } else if($pagenow == 'global') { ?>
					    	<?php include_once('templates/tracking-scripts-manager-pro-edit-global.php'); ?>
					    <?php } else { ?>
					    	<?php include_once('templates/tracking-scripts-manager-pro-edit-single-post.php'); ?>
					    <?php } ?>
					</form>
				</div>
				<?php 
				
			} 
			
			
			// Admin Scripts
			public static function tracking_scripts_admin_scripts() {
				wp_enqueue_script('jquery');
				wp_enqueue_script('jquery-ui-sortable');
				
				wp_register_style('tracking-scripts-main', plugins_url('/css/main.css', __FILE__));
				wp_enqueue_style('tracking-scripts-main');
				
				wp_enqueue_script( 'tracking_script_js', plugin_dir_url(__FILE__) . '/js/built.min.js', array(), '', true );
				wp_localize_script( 'tracking_script_js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
			}


			// Ajax Functions
			public static function tracking_scripts_posts_ajax_handler() {
				$post_type = ($_POST['postType']) ? esc_attr($_POST['postType']) : 'post';

				$args = array(
					'post_type' => $post_type,
					'posts_per_page' => -1,
					'orderby' => 'name',
					'order' => 'ASC'
				);

				ob_start();
				
				$query = new WP_Query($args);
				echo '<option value="none" id="none">Choose '.ucwords($post_type).'</option>';
				while($query->have_posts()) : $query->the_post();
					echo '<option value="'.get_the_ID().'" id="'.get_the_ID().'">'.ucwords(get_the_title()).'</option>';
				endwhile; 
				wp_reset_postdata();
				
				echo ob_get_clean();
				die();
			}
			
			public static function tracking_scripts_post_content_ajax_handler() {
				$current_page_id = ($_POST['postID']) ? esc_attr($_POST['postID']) : null;
				
				if($current_page_id) {
					$page_scripts = unserialize(get_option(WP_PAGE_TRACKING_SCRIPT)); $i = 1;						        
	        		$page_scripts_array = array();
	        		foreach($page_scripts as $script) {
		        		if($script->page_id == $current_page_id) {
	        				$page_scripts_array[$script->location][] = $script;
	        			}
	        		}
	        		
	        		ob_start();
				?>				
					<div id="tracking_scripts_<?php echo $current_page_id; ?>" class="tracking_scripts">
			        	<?php 
			        		$page_header_scripts = (array_key_exists('header', $page_scripts_array)) ? $page_scripts_array['header'] : null;
			        		$page_footer_scripts = (array_key_exists('footer', $page_scripts_array)) ? $page_scripts_array['footer'] : null;
			        	?>
			        	<h3><?php _e( 'Header', TRACKING_SCRIPT_TEXTDOMAIN ); ?></h3>
			        	<?php if($page_header_scripts) { ?>
			        		<ul class="tracking_script_list">
				        	<?php $i = 1; ?>
				        	<?php foreach($page_header_scripts as $script) { ?>
					        	<div class="tracking_script">
					        		<i class="fa fa-sort" title="Drag to Sort"></i>
					        		<p><?php echo $i; ?></p>
					        		<div class="script_info">
					        			<input type="text" name="page_script_<?php echo $script->script_id; ?>_name" value="<?php echo $script->script_name; ?>" readonly="readonly">
										<input type="text" name="page_script_<?php echo $script->script_id; ?>_code" value="<?php echo $script->script_code; ?>" readonly="readonly">
					        		</div>
					        		<i class="active_tracking fa <?php if($script->active === true) { echo 'fa-check-circle'; } else { echo 'fa-circle-o'; } ?>" title="<?php if($script->active === true) { echo 'Deactivate Script'; } else { echo 'Activate Script'; } ?>"></i>
					        		<i class="edit_tracking fa fa-edit" title="Edit Script"></i>
					        		<i class="delete_tracking fa fa-times" title="Delete Script"></i>
					        		<input type="hidden" class="script_order" name="page_script_<?php echo $script->script_id; ?>_order" value="<?php echo $i; ?>">
					        		<input type="hidden" class="script_active" name="page_script_<?php echo $script->script_id; ?>_active" value="<?php if($script->active === true) { echo 'true'; } else { echo 'false'; } ?>">
					        		<input type="hidden" class="script_exists" name="page_script_<?php echo $script->script_id; ?>_exists" value="true">
					        	</div>
								<?php $i++; ?> 
							<?php } ?>
			        		</ul>
						<?php } ?>
			        	<h3><?php _e( 'Footer', TRACKING_SCRIPT_TEXTDOMAIN ); ?></h3>
			        	<?php if($page_footer_scripts) { ?>
			        		<ul class="tracking_script_list">
				        	<?php foreach($page_footer_scripts as $script) { ?>
					        	<div class="tracking_script">
					        		<i class="fa fa-sort" title="Drag to Sort"></i>
					        		<p><?php echo $i; ?></p>
					        		<div class="script_info">
					        			<input type="text" name="page_script_<?php echo $script->script_id; ?>_name" value="<?php echo $script->script_name; ?>" readonly="readonly">
										<input type="text" name="page_script_<?php echo $script->script_id; ?>_code" value="<?php echo $script->script_code; ?>" readonly="readonly">
					        		</div>
					        		<i class="active_tracking fa <?php if($script->active === true) { echo 'fa-check-circle'; } else { echo 'fa-circle-o'; } ?>" title="<?php if($script->active === true) { echo 'Deactivate Script'; } else { echo 'Activate Script'; } ?>"></i>
					        		<i class="edit_tracking fa fa-edit" title="Edit Script"></i>
					        		<i class="delete_tracking fa fa-times" title="Delete Script"></i>
					        		<input type="hidden" class="script_order" name="page_script_<?php echo $script->script_id; ?>_order" value="<?php echo $i; ?>">
					        		<input type="hidden" class="script_active" name="page_script_<?php echo $script->script_id; ?>_active" value="<?php if($script->active === true) { echo 'true'; } else { echo 'false'; } ?>">
					        		<input type="hidden" class="script_exists" name="page_script_<?php echo $script->script_id; ?>_exists" value="true">
					        	</div>
								<?php $i++; ?> 
							<?php } ?>
			        		</ul>
						<?php } ?>
			        </div>
			<?php
					echo ob_get_clean();
					die();
				}
			}
			
			// Admin Hooks
			public static function initialize_admin_posts() {
				add_action('admin_post_save_new_tracking_codes', array(__CLASS__, 'save_new_tracking_codes')); // If the user is logged in
				add_action('admin_post_update_tracking_codes', array(__CLASS__, 'update_tracking_codes')); // If the user is logged in
				add_action('admin_post_update_page_tracking_codes', array(__CLASS__, 'update_page_tracking_codes')); // If the user is logged in
			}
			
			
			
			// Save New Tracking Codes
			public static function save_new_tracking_codes() {
				$header_scripts = unserialize(get_option(WP_HEADER_TRACKING_SCRIPT));
				$footer_scripts = unserialize(get_option(WP_FOOTER_TRACKING_SCRIPT));
				$page_scripts = unserialize(get_option(WP_PAGE_TRACKING_SCRIPT));
				
				if(!$header_scripts) {
					$header_scripts = array();
				}
				
				if(!$footer_scripts) {
					$footer_scripts = array();
				}
				
				if($_POST[WP_NEW_PAGE_TRACKING_SCRIPT_GLOBAL] && $_POST[WP_NEW_PAGE_TRACKING_SCRIPT_GLOBAL] == 'yes') {
					$tracking_location = esc_attr($_POST[WP_NEW_PAGE_TRACKING_SCRIPT_LOCATION]);
					
					if($tracking_location == 'header') {
						$tracking = new Tracking_Script();
						$tracking->script_name = sanitize_text_field($_POST['new_page_script_name']);
						$tracking->script_code = stripslashes(esc_textarea($_POST[WP_NEW_PAGE_TRACKING_SCRIPT]));
						$tracking->active = true;
						$tracking->order = count($header_scripts);
						$tracking->location = 'header';
						$header_scripts[] = $tracking;
						update_option(WP_HEADER_TRACKING_SCRIPT, serialize($header_scripts));
					} else if($tracking_location == 'footer') {
						$tracking = new Tracking_Script();
						$tracking->script_name = sanitize_text_field($_POST['new_page_script_name']);
						$tracking->script_code = stripslashes(esc_textarea($_POST[WP_NEW_PAGE_TRACKING_SCRIPT]));
						$tracking->active = true;
						$tracking->order = count($footer_scripts);
						$tracking->location = 'footer';
						$footer_scripts[] = $tracking;
						update_option(WP_FOOTER_TRACKING_SCRIPT, serialize($footer_scripts));
					}
				} else {
					if($_POST[WP_NEW_PAGE_TRACKING_SCRIPT_ID] && $_POST[WP_NEW_PAGE_TRACKING_SCRIPT]) {
						$tracking_pagecount = unserialize(get_option(WP_PAGE_TRACKING_SCRIPT_COUNT));
						if(!$tracking_pagecount) {
							$tracking_pagecount = 1;
						} else {
							$tracking_pagecount++;
						}
						update_option(WP_PAGE_TRACKING_SCRIPT_COUNT, serialize($tracking_pagecount));
						
						$tracking = new Tracking_Script();
						$tracking->script_name = sanitize_text_field($_POST['new_page_script_name']);
						$tracking->script_code = stripslashes(esc_textarea($_POST[WP_NEW_PAGE_TRACKING_SCRIPT]));
						$tracking->active = true;
						$tracking->page_id = esc_attr($_POST[WP_NEW_PAGE_TRACKING_SCRIPT_ID]);
						$tracking->location = esc_attr($_POST[WP_NEW_PAGE_TRACKING_SCRIPT_LOCATION]);
						$tracking->script_id = $tracking_pagecount;
						$page_scripts[] = $tracking;
						update_option(WP_PAGE_TRACKING_SCRIPT, serialize($page_scripts));
					}
				}

				$redirect_url = get_admin_url().'admin.php?page='.TRACKING_SCRIPT_BASENAME;
				if($_POST[WP_NEW_PAGE_TRACKING_SCRIPT_GLOBAL] == 'yes') {
					$redirect_url = get_admin_url().'admin.php?page='.TRACKING_SCRIPT_BASENAME.'&tab=global';
				} else if($_POST[WP_NEW_PAGE_TRACKING_SCRIPT_ID] && $_POST[WP_NEW_PAGE_TRACKING_SCRIPT]) {
					$redirect_url = get_admin_url().'admin.php?page='.TRACKING_SCRIPT_BASENAME.'&tab=pages';
				}
				
				wp_redirect($redirect_url);
				exit();
			}
			
			
			// Update Global Codes
			public static function update_tracking_codes() {
				$header_scripts = unserialize(get_option(WP_HEADER_TRACKING_SCRIPT));
				$footer_scripts = unserialize(get_option(WP_FOOTER_TRACKING_SCRIPT));
				
				$i = 1;
				foreach($header_scripts as $script) {
					if(isset($_POST['header_script_'.$i.'_name'])) {
						$script->script_name = sanitize_text_field($_POST['header_script_'.$i.'_name']);
					}
					if(isset($_POST['header_script_'.$i.'_code'])) {
						$script->script_code = stripslashes(esc_textarea($_POST['header_script_'.$i.'_code']));
					}
					if(isset($_POST['header_script_'.$i.'_active'])) {
						if($_POST['header_script_'.$i.'_active'] === 'false') {
							$script->active = false;
						} else {
							$script->active = true;
						}
					}
					if(isset($_POST['header_script_'.$i.'_order'])) {
						$order = filter_input(INPUT_POST, 'header_script_'.$i.'_order', FILTER_VALIDATE_INT);
						if(is_int($order)) { 
							$script->order = $order;
						}
					}
					if(isset($_POST['header_script_'.$i.'_exists'])) {
						if($_POST['header_script_'.$i.'_exists'] === 'false') {
							unset($header_scripts[$i-1]);
						}
					}
					
					$i++;
				}
				
				$i = 1;
				foreach($footer_scripts as $script) {
					if(isset($_POST['footer_script_'.$i.'_name'])) {
						$script->script_name = sanitize_text_field($_POST['footer_script_'.$i.'_name']);
					}
					if(isset($_POST['footer_script_'.$i.'_code'])) {
						$script->script_code = stripslashes(esc_textarea($_POST['footer_script_'.$i.'_code']));
					}
					if(isset($_POST['footer_script_'.$i.'_active'])) {
						if($_POST['footer_script_'.$i.'_active'] === 'false') {
							$script->active = false;
						} else {
							$script->active = true;
						}
					}
					if(isset($_POST['footer_script_'.$i.'_order'])) {
						$order = filter_input(INPUT_POST, 'footer_script_'.$i.'_order', FILTER_VALIDATE_INT);
						if(is_int($order)) { 
							$script->order = $order;
						}
					}
					if(isset($_POST['footer_script_'.$i.'_exists'])) {
						if($_POST['footer_script_'.$i.'_exists'] === 'false') {
							unset($footer_scripts[$i-1]);
						}
					}
					
					$i++;
				}
				
				usort($header_scripts, array(__CLASS__, 'compare_order'));
				usort($footer_scripts, array(__CLASS__, 'compare_order'));
				
				
				update_option(WP_HEADER_TRACKING_SCRIPT, serialize($header_scripts));
				update_option(WP_FOOTER_TRACKING_SCRIPT, serialize($footer_scripts));
				
				wp_redirect(get_admin_url().'admin.php?page='.TRACKING_SCRIPT_BASENAME.'&tab=global');
				exit();
			}	


			// Update Page Specific Codes
			public static function update_page_tracking_codes() {
				$page_scripts = unserialize(get_option(WP_PAGE_TRACKING_SCRIPT));

				$index = 0;
				foreach($page_scripts as $script) {
					$script_id = $script->script_id;
					if(isset($_POST['page_script_'.$script_id.'_name'])) {
						$script->script_name = sanitize_text_field($_POST['page_script_'.$script_id.'_name']);
					}
					if(isset($_POST['page_script_'.$script_id.'_code'])) {
						$script->script_code = stripslashes(esc_textarea($_POST['page_script_'.$script_id.'_code']));
					}
					if(isset($_POST['page_script_'.$script_id.'_active'])) {
						if($_POST['page_script_'.$script_id.'_active'] === 'false') {
							$script->active = false;
						} else {
							$script->active = true;
						}
					}
					if(isset($_POST['page_script_'.$script_id.'_order'])) {
						$order = filter_input(INPUT_POST, 'page_script_'.$script_id.'_order', FILTER_VALIDATE_INT);
						if(is_int($order)) { 
							$script->order = $order;
						}
					}
					if(isset($_POST['page_script_'.$script_id.'_exists'])) {
						if($_POST['page_script_'.$script_id.'_exists'] === 'false') {
							unset($page_scripts[$index]);
						}
					}
					$index++;
				}

				usort($page_scripts, array(__CLASS__, 'compare_order'));				

				update_option(WP_PAGE_TRACKING_SCRIPT, serialize($page_scripts));
				
				wp_redirect(get_admin_url().'admin.php?page='.TRACKING_SCRIPT_BASENAME.'&tab=pages');
				exit();
			}
			
			public static function compare_order($a, $b) {
				return ($a->order < $b->order) ? -1 : 1;
			}
		}

		$class['Tracking_Scripts'] = new Tracking_Scripts();
	}
	
	if( !class_exists('Tracking_Script') ) {
		
		class Tracking_Script {
			public $script_name;
			public $script_code;
			public $active;
			public $order;
			public $page_id;
			public $location;
			public $script_id;
		
			function __construct() {
				
			}
		}
	
	}
?>