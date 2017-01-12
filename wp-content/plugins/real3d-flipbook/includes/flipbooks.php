<div class="wrap">

	<h2>Flipbooks
		<a href='<?php echo $url .'&action=add_new'; ?>' class='add-new-h2'>Add New</a>
	</h2>
	
	<?php

		if (isset($_GET['action'])){
	
		if($_GET['action'] == "delete"){

		$names = '';
		$ids = explode(',', $_GET['bookId']);
		if(count($ids) == 1)
			$prefix = 'Flipbook';
		else
			$prefix = 'Flipbooks';
		foreach ($ids as $id) {
			if($names != '')
				$names = $names . ', ';
			$names = $names . $id;
		}
		
			echo '<div id="message" class="updated notice is-dismissible below-h2">
					<p><strong>'.$prefix .' </strong><i>' . $names.'</i> <strong>deleted</strong>. <a class="undo" href="#">Undo		</a></p>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>';
		
		}elseif($_GET['action'] == "delete_all"){
		
			echo '<div id="message" class="updated notice is-dismissible below-h2">
					<p>All Flipbooks deleted. <a class="undo" href="#">Undo		</a></p>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>';
		
		}elseif($_GET['action'] == "import_from_json_confirm" ) {
					
			echo '<div id="message" class="updated notice is-dismissible below-h2">
					<p>Flipbooks imported from JSON. <a class="undo" href="#">Undo		</a></p>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>';
		}	
	}		
	
	?>			
				

	<div class="tablenav top">
		<div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
			<select name="action" id="bulk-action-selector-top">
				<option value="-1" selected="selected">Bulk Actions</option>
				<option value="trash">Trash</option>
			</select>

			<input type="submit" id="doaction" class="button action bulkactions-apply" value="Apply">
		</div>


<!-- <div class="alignleft actions">

<label class="screen-reader-text" for="cat">Filter by category</label><select name="cat" id="cat" class="postform">
	<option value="0">All categories</option>
	<option class="level-0" value="1">Uncategorized</option>
</select> 
<input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">		
</div>-->

		<div class="tablenav-pages">
        <span class="displaying-num"></span>
		<span class="pagination-links"><a class="first-page" title="Go to the first page" href="#">«</a>
		<a class="prev-page" title="Go to the previous page" href="#">‹</a>
		<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Select Page</label><input class="current-page" id="current-page-selector" title="Current page" type="text" name="paged" value="1" size="1"> of <span class="total-pages"></span></span>
		<a class="next-page" title="Go to the next page" href="#">›</a>
		<a class="last-page" title="Go to the last page" href="#">»</a></span>
        </div>

	</div>	
	
	
	
	<table class='flipbooks-table wp-list-table widefat fixed striped pages'>
<thead>
			<tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column" style="">
					<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
					<input id="cb-select-all-1" type="checkbox">
				</th>
			
				<th scope="col" id="name" class="manage-column column-title sorted desc"><a href="#"><span>Name</span><span class="sorting-indicator"></span></a></th>
				
				<th style="width:150px">Shortcode</th>
				
				<th style="width:100px" scope="col" id="name" class="manage-column column-title sorted desc"><a href="#"><span>Date</span><span class="sorting-indicator"></span></a></th>
			</tr>
			</thead>
			<tbody id="flipbooks-table">
		</tbody>		 
	</table>
	
	<div class="tablenav bottom">
		<div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label>
			<select name="action" id="bulk-action-selector-bottom">
				<option value="-1" selected="selected">Bulk Actions</option>
				<option value="trash">Trash</option>
			</select>

			<input type="submit" id="doaction" class="button action bulkactions-apply" value="Apply">
		</div>


<!--<div class="alignleft actions">

<label class="screen-reader-text" for="cat">Filter by category</label><select name="cat" id="cat" class="postform">
	<option value="0">All categories</option>
	<option class="level-0" value="1">Uncategorized</option>
</select>
<input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">		</div>-->

		<div class="tablenav-pages"><span class="displaying-num"></span>
		<span class="pagination-links"><a class="first-page" title="Go to the first page" href="#">«</a>
		<a class="prev-page" title="Go to the previous page" href="#">‹</a>
		<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Select Page</label><input class="current-page" id="current-page-selector" title="Current page" type="text" name="paged" value="1" size="1"> of <span class="total-pages"></span></span>
		<a class="next-page" title="Go to the next page" href="#">›</a>
		<a class="last-page" title="Go to the last page" href="#">»</a></span></div>
	</div>	
			
	
	<br/>
	<br/>
	<br/>
	<h3>Import / Export</h3>
	<div>
		<a class='button-secondary' href='<?php echo admin_url( "admin.php?page=real3d_flipbook_admin&action=generate_json" ); ?>'>Export (Generate JSON)</a>
	</div>

	</p>    
	
	<form method="post" enctype="multipart/form-data" action="admin.php?page=real3d_flipbook_admin&amp;action=import_from_json_confirm">
	
		<?php 
				if (isset($_GET['action']) && $_GET['action'] == "generate_json") {
					echo '<textarea id="flipbook-admin-json" rows="20" cols="100" >' . json_encode($flipbooks) . '</textarea>';
				}
			?>
			<br/>
			<br/>
			<br/>
			<p>Import flipbooks from JSON( overwrite existing flipbooks)</p>
			
			<textarea name="flipbooks" id="flipbook-admin-json" rows="20" cols="100" placeholder="Paste JSON here"></textarea>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button save-button button-secondary" value="Import"></p>
	
	</form>
	<br/>
	<br/>
	<br/>
	<span class="submitbox"><a class="submitdelete" href='<?php echo $url .'&action=delete_all'; ?>'>Delete all flipbooks</a></span>
	
	
	
</div>
<?php


wp_enqueue_script("real3d_flipbook_admin", plugins_url()."/real3d-flipbook/js/flipbooks.js", array('jquery' ),REAL3D_FLIPBOOK_VERSION);

//wp_localize_script( 'read3d_flipbook_admin', 'flipbooks', json_encode($flipbooks) );


// wp_enqueue_script("read3d_flipbook_admin", plugins_url()."/real3d-flipbook/js/plugin_admin.js", array('jquery','jquery-ui-sortable','jquery-ui-resizable','jquery-ui-selectable','jquery-ui-tabs' ),REAL3D_FLIPBOOK_VERSION);
// wp_enqueue_style( 'read3d_flipbook_admin_css', plugins_url()."/real3d-flipbook/css/flipbook-admin.css",array(), REAL3D_FLIPBOOK_VERSION );
//pass $flipbooks to javascript
wp_localize_script( 'real3d_flipbook_admin', 'flipbooks', json_encode($flipbooks) );