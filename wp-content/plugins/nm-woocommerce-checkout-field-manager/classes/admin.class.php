<?php
/*
 * working behind the seen
*/


class NM_Checkout_Field_Manager_Admin extends NM_Checkout_Field_Manager{


	var $menu_pages, $plugin_scripts_admin, $plugin_settings;
	var $inputs;


	function __construct(){


		//setting plugin meta saved in config.php
		$this -> plugin_meta = get_plugin_meta_cofm();

		//getting saved settings
		$this -> plugin_settings = get_option($this->plugin_meta['shortname'].'_settings');

		// populating $inputs with NM_Inputs object
		$this -> inputs = $this -> get_all_inputs ();

		/*
		 * [1]
		* TODO: change this for plugin admin pages
		*/
		if(1){
			
			$this -> menu_pages		= array(array('page_title'	=> $this->plugin_meta['name'],
				'menu_title'	=> $this->plugin_meta['name'],
				'cap'			=> 'manage_options',
				'slug'			=> $this->plugin_meta['shortname'],
				'callback'		=> 'main_settings',
				'parent_slug'		=> '',),
			);
		}else{
			
			$this->menu_pages = array (
					array (
							'page_title' => $this->plugin_meta ['name'],
							'menu_title' => $this->plugin_meta ['name'],
							'cap' => 'manage_options',
							'slug' => $this->plugin_meta['shortname'],
							'callback' => 'activate_plugin',
							'parent_slug' => ''
					),
					);
			
		}
		


		/*
		 * [2]
		* TODO: Change this for admin related scripts
		* JS scripts and styles to loaded
		* ADMIN
		*/
		$this -> plugin_scripts_admin =  array(
				array(	'script_name'	=> 'scripts-global',
								'script_source'	=> '/js/nm-global.js',
								'localized'		=> false,
								'type'			=> 'js',
								'page_slug'		=> $this->plugin_meta['shortname']
						),
						
				array(	'script_name'	=> 'scripts-chosen',
								'script_source'	=> '/js/chosen/chosen.jquery.min.js',
								'localized'		=> false,
								'type'			=> 'js',
								'page_slug'		=> $this->plugin_meta['shortname'],
								'depends' => array ('jquery')
						),
						
						array (
								'script_name' => 'chosen-style',
								'script_source' => '/js/chosen/chosen.min.css',
								'localized' => false,
								'type' => 'style',
								'page_slug' => $this->plugin_meta['shortname'],
						),
							
				array (
						'script_name' => 'cofm-scripts-admin',
						'script_source' => '/js/admin.js',
						'localized' => true,
						'type' => 'js',
						'page_slug' => array (
								$this->plugin_meta ['shortname'],
						),
						'depends' => array (
								'jquery',
								'jquery-ui-accordion',
								'jquery-ui-draggable',
								'jquery-ui-droppable',
								'jquery-ui-sortable',
								'jquery-ui-slider',
								'jquery-ui-dialog',
								'jquery-ui-tabs',
								//'media-upload',
								//'thickbox'
						) 
				),
				array (
						'script_name' => 'ui-style',
						'script_source' => '/js/ui/css/smoothness/jquery-ui-1.10.3.custom.min.css',
						'localized' => false,
						'type' => 'style',
						'page_slug' => array (
								'nm_cofm'
						)
				),
				array (
						'script_name' => 'thickbox',
						'script_source' => 'shipped',
						'localized' => false,
						'type' => 'style',
						'page_slug' => array (
								'nm_cofm'
						)
				),
				array (
						'script_name' => 'ui-style',
						'script_source' => '/js/ui/css/smoothness/jquery-ui-1.10.3.custom.min.css',
						'localized' => false,
						'type' => 'style',
						'page_slug' => array (
								'nm_cofm' 
						) 
				),
				
				
					
		);


		add_action('admin_menu', array($this, 'add_menu_pages'));
		
		//Auto update notification
		add_action('in_plugin_update_message-nm-woocommerce-checkout-field-manager/index.php', array($this, 'update_message'), 2, 10);
	}
	
	public function update_message(){
		
		echo '<div style="color:red;font:bold">';
		// echo 'Download your plugin new version from <a target="_blank" href="https://www.wordpresspoets.com/member-area/">Member Area</a>';
		echo 'Download your plugin new version from <a target="_blank" href="https://codecanyon.net/">CodeCanyon</a> and replace with existing';
		echo '</div>';
	}

	function load_scripts_admin() {
		
		// adding media upload scripts (WP 3.5+)
		wp_enqueue_media();
						
		// localized vars in js
		$arrLocalizedVars = array (
				'ajaxurl' => admin_url ( 'admin-ajax.php' ),
				'plugin_url' => $this->plugin_meta ['url'] 
		);
		
		// admin end scripts
		
		if ($this->plugin_scripts_admin) {
			foreach ( $this->plugin_scripts_admin as $script ) {
				
				// checking if it is style
				if ($script ['type'] == 'js') {
					
					$depends = (isset ( $script ['depends'] ) ? $script ['depends'] : NULL);
					wp_enqueue_script ( $this->plugin_meta ['shortname'] . '-' . $script ['script_name'], $this->plugin_meta ['url'] . $script ['script_source'], $depends );
					
					// if localized
					if ($script ['localized'])
						wp_localize_script ( $this->plugin_meta ['shortname'] . '-' . $script ['script_name'], $this->plugin_meta ['shortname'] . '_vars', $arrLocalizedVars );
				} else {
					
					if ($script ['script_source'] == 'shipped')
						wp_enqueue_style ( $script ['script_name'] );
					else
						wp_enqueue_style ( $this->plugin_meta ['shortname'] . '-' . $script ['script_name'], $this->plugin_meta ['url'] . $script ['script_source'] );
				}
			}
		}
	}



	/*
	 * creating menu page for this plugin
	*/

	function add_menu_pages(){

		foreach ($this -> menu_pages as $page){
				
			if ($page['parent_slug'] == ''){

				$menu = add_menu_page(__('Checkout Fields', $this->plugin_meta['shortname']),
						__('Checkout Fields', $this->plugin_meta['shortname']),
						$page['cap'],
						$page['slug'],
						array($this, $page['callback']),
						$this->plugin_meta['logo'],
						$this->plugin_meta['menu_position']);
			}else{

				$menu = add_submenu_page($page['parent_slug'],
						__($page['page_title'].' Settings', $this->plugin_meta['shortname']),
						__($page['menu_title'].' Settings', $this->plugin_meta['shortname']),
						$page['cap'],
						$page['slug'],
						array($this, $page['callback'])
				);

			}
				
			//loading script for only plugin optios pages
			// page_slug is key in $plugin_scripts_admin which determine the page
			foreach ($this -> plugin_scripts_admin as $script){

				if (is_array($script['page_slug'])){
					
					
					if (in_array($page['slug'], $script['page_slug'])){
						
						add_action('admin_print_scripts-'.$menu, array($this, 'load_scripts_admin'));
					}
						
				}else if ($script['page_slug'] == $page['slug']){
					
					add_action('admin_print_scripts-'.$menu, array($this, 'load_scripts_admin'));
				}
			}
		}


	}


	//====================== CALLBACKS =================================
	function main_settings(){

		$this -> load_template('admin/field-manager.php');

	}

	function activate_plugin(){
		
		echo '<div class="wrap">';
		echo '<h2>' . __ ( 'Provide API key below:', 'nm-personalizedproduct' ) . '</h2>';
		echo '<p>' . __ ( 'If you don\'t know your API key, please login into your: <a target="_blank" href="http://wordpresspoets.com/member-area">Member area</a>', 'nm-personalizedproduct' ) . '</p>';
		
		echo '<form onsubmit="return validate_api_cofm(this)">';
			echo '<p><label id="plugin_api_key">'.__('Entery API key', 'nm-personalizedproduct').':</label><br /><input type="text" name="plugin_api_key" id="plugin_api_key" /></p>';
			wp_nonce_field();
			echo '<p><input type="submit" class="button-primary button" name="plugin_api_key" /></p>';
			echo '<p id="nm-sending-api"></p>';
		echo '</form>';
		
		echo '</div>';
		
	}
}