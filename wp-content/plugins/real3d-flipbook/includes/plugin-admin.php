<?php

	function real3d_flipbook_admin_menu(){
		add_options_page("Real 3D Flipbook Admin", "Real3D Flipbook", "publish_posts", "real3d_flipbook_admin", "real3d_flipbook_admin"); 

		add_menu_page("Real 3D Flipbook Admin", "Real3D Flipbook", "publish_posts", "real3d_flipbook_menu", "real3d_flipbook_admin",'dashicons-book'); 
		// Add a new top-level menu (ill-advised):
	    // add_menu_page(__('Real3D Flipbook','menu-test'), __('Real3D Flipbook','menu-test'), 'publish_posts', 'mt-top-level-handle', 'real3d_flipbook_admin','dashicons-book' );

	    // Add a submenu to the custom top-level menu:
	    //add_submenu_page('mt-top-level-handle', __('Categories','menu-test'), __('Categories','menu-test'), 'publish_posts', 'real3d_flipbook_admin', 'mt_sublevel_page');
	}
	add_action("admin_menu", "real3d_flipbook_admin_menu");

	add_action( 'wp_ajax_real3dflipbook_update_option', 'real3dflipbook_update_option_callback' );
	add_action( 'wp_ajax_nopriv_real3dflipbook_update_option', 'real3dflipbook_update_option_callback' );
	function real3dflipbook_update_option_callback() {
		update_option($_POST['name'],$_POST['data']);
		die;
	}

	add_action( 'wp_ajax_real3dflipbook_get_option', 'real3dflipbook_get_option_callback' );
	add_action( 'wp_ajax_nopriv_real3dflipbook_get_option', 'real3dflipbook_get_option_callback' );
	function real3dflipbook_get_option_callback() {
		echo wp_json_encode( get_option($_POST['name']) );
	    die;
	}
	//options page
	function real3d_flipbook_admin()
    {

		$current_action = $current_id = $page_id = '';
		// handle action from url
		if (isset($_GET['action']) ) {
			$current_action = $_GET['action'];
		}

		if (isset($_GET['bookId']) ) {
			$current_id = $_GET['bookId'];
		}
		
		if (isset($_GET['pageId']) ) {
			$page_id = $_GET['pageId'];
		}

		$url=admin_url( "admin.php?page=real3d_flipbook_admin" );

		$reak3dflipbooks_converted = get_option("reak3dflipbooks_converted");

		if(!$reak3dflipbooks_converted){

			$flipbooks = get_option("flipbooks");
			if(!$flipbooks){
				$flipbooks = array();
			}

			add_option('reak3dflipbooks_converted', true);
			$real3dflipbooks_ids = array();
			//trace('converting flipbooks...');
			foreach ($flipbooks as $b) {
				$id = $b['id'];
				//trace($id);
				delete_option('real3dflipbook_'.(string)$id);
				add_option('real3dflipbook_'.(string)$id, $b);
				array_push($real3dflipbooks_ids,(string)$id);
			}
			// trace($real3dflipbooks_ids);
		}else{
			// trace($real3dflipbooks_ids);
			$real3dflipbooks_ids = get_option('real3dflipbooks_ids');
			if(!$real3dflipbooks_ids){
				$real3dflipbooks_ids = array();
			}
			$flipbooks = array();
			foreach ($real3dflipbooks_ids as $id) {
				// trace($id);
				$book = get_option('real3dflipbook_'.$id);
				if($book){
					$flipbooks[$id] = $book;
					// array_push($flipbooks,$book);
				}else{
					//remove id from array
					$real3dflipbooks_ids = array_diff($real3dflipbooks_ids, array($id));
				}
			}
		}
		
		update_option('real3dflipbooks_ids', $real3dflipbooks_ids);

		switch( $current_action ) {
		
			case 'edit':
				include("edit-flipbook.php");
				break;
				
			case 'delete':
				//backup
				delete_option('real3dflipbooks_ids_back');
				add_option('real3dflipbooks_ids_back',$real3dflipbooks_ids);
				foreach ($real3dflipbooks_ids as $id) {
					update_option("real3dflipbooks_ids",array());
				}
				
				
				$ids = explode(',', $current_id);
				
				foreach ($ids as $id) {
					unset($flipbooks[$id]);
				}
				$real3dflipbooks_ids = array_diff($real3dflipbooks_ids, $ids);
				update_option('real3dflipbooks_ids', $real3dflipbooks_ids);
				
				//delete flipbook with id from url
				
				include("flipbooks.php");
				// trace($flipbooks[$current_id]['name']);
				// trace(REAL3D_FLIPBOOK_DIR);
				////delete folder books/$flipbooks[$current_id]['name']
				// $bookFolder = $flipbooks[$current_id]['name'];
				// trace(REAL3D_FLIPBOOK_DIR.'books/'.$bookFolder);
				// rrmdir(REAL3D_FLIPBOOK_DIR.'books/'.$bookFolder);
				break;
				
			case 'delete_all':
				//backup
				delete_option('real3dflipbooks_ids_back');
				add_option('real3dflipbooks_ids_back',$real3dflipbooks_ids);
				foreach ($real3dflipbooks_ids as $id) {
					delete_option('real3dflipbook_'.(string)$id);
				}
				$flipbooks = array();
				include("flipbooks.php");
				break;
				
			case 'duplicate':
				$new_id = 0;
				$highest_id = 0;

				foreach ($real3dflipbooks_ids as $id) {
					if((int)$id > $highest_id) {
						$highest_id = (int)$id;
					}
				}
				$new_id = $highest_id + 1;
				$flipbooks[$new_id] = $flipbooks[$current_id];
				$flipbooks[$new_id]["id"] = $new_id;
				$flipbooks[$new_id]["name"] = $flipbooks[$current_id]["name"]." (copy)";
				
				$flipbooks[$new_id]["date"] = current_time( 'mysql' );

				delete_option('real3dflipbook_'.(string)$new_id);
				add_option('real3dflipbook_'.(string)$new_id,$flipbooks[$new_id]);

				array_push($real3dflipbooks_ids,$new_id);
				update_option('real3dflipbooks_ids',$real3dflipbooks_ids);


				include("flipbooks.php");
				break;
				
			case 'add_new':
				//generate ID 
				$new_id = 0;
				$highest_id = 0;

				foreach ($real3dflipbooks_ids as $id) {
					if((int)$id > $highest_id) {
						$highest_id = (int)$id;
					}
				}

				$current_id = $highest_id + 1;
				//create new book 
				$book = array(	"id" => $current_id, 
								"name" => "flipbook " . $current_id,
								"pages" => array(),
								"date" => current_time( 'mysql' )
							);
				//save new book to database
				delete_option('real3dflipbook_'.(string)$current_id);
				add_option('real3dflipbook_'.(string)$current_id,$book);
				//add new book to books
				array_push($flipbooks,$book);
				//save new id to array of id-s
				array_push($real3dflipbooks_ids,$current_id);
				update_option('real3dflipbooks_ids',$real3dflipbooks_ids);

				include("edit-flipbook.php");
				break;
				
			case 'add_new_cat':
				
				break;
				
			case 'save_settings':

				if(count($_POST) == 0){
					include("edit-flipbook.php");
					break;
				}
				
				//clear pages array if delete all pages
				if (!isset($_POST['pages']) ) {
					$_POST['pages'] = array();
				}
				if($flipbooks && $current_id != ''){
					$flipbook = $flipbooks[$current_id];
					if($flipbook){
						$pages = $flipbook["pages"];
					}else{
						$flipbook = array();
					}
				}

				$new = array_merge($flipbook, $_POST);
				$flipbooks[$current_id] = $new;
				//reset indexes because of sortable pages can be rearranged
				$oldPages = $flipbooks[$current_id]["pages"];
				$newPages = array();
				$index = 0;
				foreach($oldPages as $p){
					$newPages[$index] = $p;
					$index++;
				}
				$flipbooks[$current_id]["pages"] = $newPages;
								
				//for each page
				/*for($i = 0; $i < count($flipbooks[$current_id]["pages"]); $i++){
					$p = $flipbooks[$current_id]["pages"][$i];

					if(isset($p["links"])){
						//reset links 
						$oldLinks = $p["links"];
						if($oldLinks){
							$newLinks = array();
							$index = 0;
							foreach($oldLinks as $lnk){
								$newLinks[$index] = $lnk;
								$index++;
							}
							$flipbooks[$current_id]["pages"][$i]["links"] = $newLinks;
							$p = $flipbooks[$current_id]["pages"][$i];
							//for each link in links
							$formattedLinks = array();
							for($j = 0; $j < count($p["links"]); $j++){
								$l = $p["links"][$j];
								$formattedLink = array_map("cast", $l);
								$formattedLinks[$j] = $formattedLink;
							}
							$flipbooks[$current_id]["pages"][$i]["links"] = $formattedLinks;
						}
					}	
				}*/
				update_option('real3dflipbook_'.$current_id, $flipbooks[$current_id]);
				include("edit-flipbook.php");
				break;
				
			case 'generate_json':
				// trace("generate_json");
				// trace($_POST);
				include("flipbooks.php");
				break;
			
			case 'import_from_json':
				// trace("import_from_json");
				// trace($_POST);
				include("flipbooks.php");
				break;
			
			case 'import_from_json_confirm':

				//backup
				delete_option('real3dflipbooks_ids_back');
				add_option('real3dflipbooks_ids_back',$real3dflipbooks_ids);

				//delete all flipbooks
				foreach ($real3dflipbooks_ids as $id) {
					delete_option('real3dflipbook_'.(string)$id);
				}

				
				// trace("import_from_json_confirm");
				// trace($_POST['flipbooks']);
				$json = stripslashes($_POST['flipbooks']);
				// trace(($json));
				// trace(json_decode($json));
				// trace(json_decode($_POST['flipbooks']));

				//trace($_POST['flipbooks']);
				$newFlipbooks = real3dflipbook_objectToArray(json_decode($json));
				// trace($newFlipbooks);
				if((string)$json != "" && is_array($newFlipbooks)){
					$real3dflipbooks_ids = array();
					//trace('converting flipbooks...');
					foreach ($newFlipbooks as $b) {
						$id = $b['id'];
						//trace($id);
						// delete_option('real3dflipbook_'.(string)$id);
						add_option('real3dflipbook_'.(string)$id, $b);
						array_push($real3dflipbooks_ids,(string)$id);
					}
					// trace($real3dflipbooks_ids);
					update_option('real3dflipbooks_ids', $real3dflipbooks_ids);
					$flipbooks = $newFlipbooks;
				}
				
				// trace($_POST['flipbooks'] !== "");
				// trace($_POST['flipbooks']);
				// trace(json_decode(stripslashes($_POST['flipbooks'])));
				include("flipbooks.php");
				break;
				
			case 'undo':

				$real3dflipbooks_ids = get_option('real3dflipbooks_ids_back');

				$flipbooks = array();
				foreach ($real3dflipbooks_ids as $id) {
					// trace($id);
					$book = get_option('real3dflipbook_'.$id);
					if($book){
						$flipbooks[$id] = $book;
						// array_push($flipbooks,$book);
					}else{
						//remove id from array
						$real3dflipbooks_ids = array_diff($real3dflipbooks_ids, array($id));
					}
				}
				update_option('real3dflipbooks_ids', $real3dflipbooks_ids);


				include("flipbooks.php");
				break;
			
			default:
				include("flipbooks.php");
				break;
				
		}
    }
	
	
	function real3dflipbook_objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}

		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(__FUNCTION__, $d);
		}
		else {
			// Return array
			return $d;
		}
	}				
	
	function cast($n)
	{
		if($n === "true") {
			return true;
		}else if ($n === "false"){
			return false;
		}else if(is_numeric($n)){
			// return (int)$n;
			return floatval($n);
		}else{
			return $n;
		}
	}