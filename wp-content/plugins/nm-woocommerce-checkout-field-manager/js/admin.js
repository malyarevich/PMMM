jQuery(function($){

	$("#cofm-tabs").tabs({
		activate: function( event, ui ) {
			$(".form-meta-setting ul").accordion( "refresh" );
		}
	});
	
	 /* =========== Chosen plugin =============== */
    var chosen_options = {};
    $(".nm-wooproducts").chosen(chosen_options);
    /* =========== Chosen plugin =============== */
	
var meta_removed;
	
	//attaching hide and delete events for existing meta data
	$(".form-meta-setting ul li").each(function(i, item){
		// for delete box
		$(item).find(".dashicons-trash").click(function(e) {
			e.preventDefault();
			$("#remove-meta-confirm").dialog("open");
			meta_removed = $(item);
		});	
	});

    var active = false,
        sorting = false;
    var icons = {
        header: "dashicons dashicons-plus",
        activeHeader: "dashicons dashicons-minus"
    };        

    $(".form-meta-setting ul")
    .accordion({
    	icons: icons,
    	autoHeight: false,
        header: "> li > div > h3",
        collapsible: true,
        activate: function( event, ui){
            //this fixes any problems with sorting if panel was open 
            //remove to see what I am talking about
            if(sorting)
                $(this).sortable("refresh");   
        }
    })
    .sortable({
    	revert : true,
        handle: "h3",
        placeholder: "nm-state-highlight",
        start: function( event, ui ){
            //change bool to true
            sorting=true;

            //find what tab is open, false if none
            active = $(this).accordion( "option", "active" ); 

            //possibly change animation here (to make the animation instant if you like)
            $(this).accordion( "option", "animate", { easing: 'swing', duration: 0 } );

            //close tab
            $(this).accordion({ active:false });
        },
        stop: function( event, ui ) {
            ui.item.children( "h3" ).triggerHandler( "focusout" );

            //possibly change animation here; { } is default value
            $(this).accordion( "option", "animate", { } );

            //open previously active panel
            $(this).accordion( "option", "active", active );

            //change bool to false
            sorting=false;
			// console.log(ui);

			// only attach click event when dropped from right panel
			if (ui.originalPosition.left > 20) {
				/*$(ui.item).find(".ui-icon-carat-2-n-s").click(function(e) {
					$(this).parent('.postbox').find("table").slideToggle(300);
				});*/

				// for delete box
				$(ui.item).find(".dashicons-trash").click(function(e) {
					$("#remove-meta-confirm").dialog("open");
					meta_removed = $(ui.item);
				});
			}            
        }
    });	

	// =========== remove dialog ===========
	$("#remove-meta-confirm").dialog({
		resizable : false,
		height : 160,
		autoOpen : false,
		modal : true,
		buttons : {
			"Remove" : function() {
				$(this).dialog("close");
				meta_removed.remove();
			},
			Cancel : function() {
				$(this).dialog("close");
			}
		}
	});

	$("#nm-input-types li").draggable(
			{
				connectToSortable : ".form-meta-setting ul",
				helper : "clone",
				start : function(event, ui) {
					     ui.helper.width('100%');
					ui.helper.height('auto');
					 },
				revert : "invalid",
				stop : function(event, ui) {
					

					$('.ui-sortable .ui-draggable').removeClass(
							'input-type-item');

					// now replacing the icons with arrow
					$('.postbox').find('.dashicons-menu').removeClass(
							'dashicons-menu').addClass('dashicons-trash');
					// $('.postbox').find('.ui-icon-placehorder').removeClass(
					// 		'ui-icon-placehorder').addClass(
					// 		'ui-icon ui-icon-trash');

				$(".form-meta-setting ul").accordion( "refresh" );

				}
			});
	//$("ul, li").disableSelection();

	// ================== new meta form creator ===================

	// add validation message if required
	$('input:checkbox[name="meta-required"]').change(function() {

		if ($(this).is(':checked')) {
			$(this).parent().find('span').show();
		} else {
			$(this).parent().find('span').hide();
		}
	});

	// increaing/saming the width of section's element
	$(".the-section").find('input, select, textarea').css({
		'width' : '35%'
	});

	$(".form-meta-setting img.add_rule").live("click", function(){
		
		var $div    = $(this).closest('div');
		var $clone = $div.clone();
		$clone.find('strong').val('Rule just added');
		
		var $td = $div.closest('td');
		$td.append($clone);
	});
	
	$(".form-meta-setting img.remove_rule").live("click", function(){
		
		var $div    = $(this).closest('div');
		var $td = $div.closest('td');
		if($td.find('div').length > 1)
			$div.remove();
		else
			alert('Not allowed');
	});
	
	/* ============= new options / remove options =============== */
	$(".form-meta-setting img.add_option").live("click", function(){
			
			var $div    = $(this).closest('div');
			var $clone = $div.clone();
			// $clone.find('strong').val('Rule just added');
			
			var $td = $div.closest('td');
			$td.append($clone);
	});
	
	$(".form-meta-setting img.remove_option").live("click", function(){
		
		var $div    = $(this).closest('div');
		var $td = $div.closest('td');
		if($td.find('div').length > 1)
			$div.remove();
		else
			alert('Not allowed');
	});
	
	// making table sortable
	// make table rows sortable
	$('#nm-file-meta-admin tbody').sortable(
			{
				start : function(event, ui) {
					// fix firefox position issue when dragging.
					if (navigator.userAgent.toLowerCase().match(/firefox/)
							&& ui.helper !== undefined) {
						ui.helper.css('position', 'absolute').css('margin-top',
								$(window).scrollTop());
						// wire up event that changes the margin whenever the
						// window scrolls.
						$(window).bind(
								'scroll.sortableplaylist',
								function() {
									ui.helper.css('position', 'absolute')
											.css('margin-top',
													$(window).scrollTop());
								});
					}
				},
				beforeStop : function(event, ui) {
					// undo the firefox fix.
					if (navigator.userAgent.toLowerCase().match(/firefox/)
							&& ui.offset !== undefined) {
						$(window).unbind('scroll.sortableplaylist');
						ui.helper.css('margin-top', 0);
					}
				},
				helper : function(e, ui) {
					ui.children().each(function() {
						$(this).width($(this).width());
					});
					return ui;
				},
				scroll : true,
				stop : function(event, ui) {
					// SAVE YOUR SORT ORDER
				}
			}).disableSelection();
	
	
	// condtions handling
	populate_conditional_elements();
	
	
/* ============== pre uploaded images 1- Media uploader launcher ================= */
	
	var $uploaded_image_container;
	
	$('input:button[name="pre_upload_image_button"]').live('click', function(){
		
		$uploaded_image_container = $(this).closest('div');
		
		wp.media.editor.send.attachment = function(props, attachment)
		{
			var existing_images;
			var fileurl = attachment.url;
			var fileid	= attachment.id;
			
			if(fileurl){
	        	var image_box 	 = '<table>';
	        	image_box 		+= '<tr>';
	        	image_box 		+= '<td><img width="75" src="'+fileurl+'"></td>';
	        	image_box 		+= '<input type="hidden" name="pre-upload-link" value="'+fileurl+'">';
	        	image_box 		+= '<input type="hidden" name="pre-upload-id" value="'+fileid+'">';
	        	image_box 		+= '<td><input placeholder="title" style="width:100px" type="text" name="pre-upload-title"><br>';
	        	image_box 		+= '<input placeholder="price" style="width:100px" type="text" name="pre-upload-price"><br>';
	        	image_box 		+= '<input style="width:100px; color:red" name="pre-upload-delete" type="button" class="button" value="Delete"><br>';
	        	image_box 		+= '</td></tr>';
	        	image_box 		+= '</table><br>';
	        	
	        	$uploaded_image_container.append(image_box);
	        	//console.log(image_box);
        }
		}
		
		wp.media.editor.open(this);
		
		return false;
	});
	
	/*$('input:button[name="pre_upload_image_button"]').live('click', function() {
		
		var pre_box_id = $(this).attr('data-upload-for');
		
		$uploaded_image_container = $(this).closest('div');
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		return false;
	});
		 
	// user inserts file into post. only run custom if user started process using the above process
    // window.send_to_editor(html) is how wp would normally handle the received data

    window.original_send_to_editor = window.send_to_editor;
    window.send_to_editor = function(html){
    	
    	var fileurl = $(html).attr('href');
        if (fileurl) {
        	
        	var existing_images;
        	var image_box 	 = '<table>';
        	image_box 		+= '<tr>';
        	image_box 		+= '<td><img width="75" src="'+fileurl+'">';
        	image_box 		+= '<input type="hidden" name="pre-upload-link" value="'+fileurl+'"></td>';
        	image_box 		+= '<td><input placeholder="title" style="width:100px" type="text" name="pre-upload-title"><br>';
        	image_box 		+= '<input placeholder="price" style="width:100px" type="text" name="pre-upload-price"><br>';
        	image_box 		+= '<input style="width:100px; color:red" name="pre-upload-delete" type="button" class="button" value="Delete"><br>';
        	image_box 		+= '</td></tr>';
        	image_box 		+= '</table><br>';
        	
        	$uploaded_image_container.append(image_box);
        	
            tb_remove();
       } else {
    	   window.original_send_to_editor(html);
       }
       
    }*/
    
    $('input:button[name="pre-upload-delete"]').live('click', function(){
    	
    	$(this).closest('table').remove();
    });
});


//conditiona logic for select, radio and checkbox
function populate_conditional_elements() {

	// resetting
	jQuery('select[name="condition_elements"]').html('');

	jQuery("ul#billing-meta-input-holder li, ul#shipping-meta-input-holder li,ul#order-meta-input-holder li").each(
			function(i, item) {

				var input_type = jQuery(item).attr('data-inputtype');
				var conditional_elements = jQuery(item).find(
						'input[name="title"]').val();
				var conditional_elements_value = jQuery(item).find(
						'input[name="data_name"]').val();
				//console.log('conditna value '+conditional_elements_value);

				if (conditional_elements !== '' && (input_type === 'select' || input_type === 'radio')){
					
					jQuery('select[name="condition_elements"]')
					.append(
							'<option value="'
									+ conditional_elements_value + '">'
									+ conditional_elements
									+ '</option>');
					
				}
					
			});
	
	// setting the existing conditional elements
	jQuery("ul#billing-meta-input-holder li, ul#shipping-meta-input-holder li,ul#order-meta-input-holder li").each(
			function(i, item) {
				
				jQuery(item).find('select[name="condition_elements"]').each(function(i, condition_element){
				
					var existing_value1 = jQuery(condition_element).attr("data-existingvalue");
					jQuery(condition_element).val(existing_value1);
					
					// populating element_values, also setting existing option
					load_conditional_values(jQuery(condition_element));
				});
				
					
			});


}

// load conditional values
function load_conditional_values(element) {

	// resetting
	jQuery(element).parent().find('select[name="condition_element_values"]')
			.html('');

	jQuery("ul#billing-meta-input-holder li, ul#shipping-meta-input-holder li,ul#order-meta-input-holder li").each(
			function(i, item) {

				var conditional_elements_value = jQuery(item).find(
						'input[name="data_name"]').val();
				
				if (conditional_elements_value === jQuery(element).val()) {

					var opt = '';
					jQuery(item).find('input:text[name="options[option]"], input:text[name="pre-upload-title"]').each(function(i, item){
						
						//console.log(jQuery(item).val());
						opt = jQuery(item).val();
						var existing_value2 = jQuery(element).parent().find('select[name="condition_element_values"]').attr("data-existingvalue");
						var selected = (opt === existing_value2) ? 'selected = "selected"' : '';

						//console.log(jQuery(element).val() + ' ' +existing_value2);
						jQuery(element).parent().find(
								'select[name="condition_element_values"]')
								.append(
										'<option '+selected+' value="' + opt + '">' + opt
												+ '</option>');
					});
					

				}

			});
}


// =========== load default billing fields ==============
function load_default_fields(type){
	

	jQuery("#"+type+"-fields-updating").html('<img src="'+nm_cofm_vars.plugin_url+'/images/loading.gif">');
	jQuery.post(ajaxurl, {section_type: type, action:'nm_cofm_load_default_fields'}, function(resp){
		
		//console.log(resp);		
		jQuery("#"+type+"-fields-updating").html(resp.message);
		window.location.reload(true);
		
	});
}

function remove_default_fields(type){
	

	jQuery("#"+type+"-fields-updating").html('<img src="'+nm_cofm_vars.plugin_url+'/images/loading.gif">');
	jQuery.post(ajaxurl, {section_type: type, action:'nm_cofm_remove_default_fields'}, function(resp){
		
		//console.log(resp);
		jQuery("#"+type+"-fields-updating").html('');
		window.location.reload(true);
		
	});
}

function save_all_changes(type){
	
	jQuery("#"+type+"-fields-updating").html('<img src="'+nm_cofm_vars.plugin_url+'/images/loading.gif">');	
	var co_meta_values = new Array();		//{};		//Array();	
	
	jQuery("#"+type+"-meta-input-holder li").each(
			function(i, item) {

				var inner_array = {};
				inner_array['type']	= jQuery(item).attr('data-inputtype');
				
				jQuery(this).find('td.table-column-input').each(
						function(i, col) {

							var meta_input_type = jQuery(col).attr('data-type');
							var meta_input_name = jQuery(col).attr('data-name');
							var cb_value = '';

							if(meta_input_type == 'checkbox'){
								if(meta_input_name === 'editing_tools'){
									cb_value = (jQuery(this).find('input:checkbox[name="' + meta_input_name + '[]"]:checked').serialize() === undefined ? '' : jQuery(this).find('input:checkbox[name="' + meta_input_name + '[]"]:checked').serialize());
									inner_array[meta_input_name] = cb_value;
								}else{
									cb_value = (jQuery(this).find('input:checkbox[name="' + meta_input_name + '"]:checked').val() === undefined ? '' : jQuery(this).find('input:checkbox[name="' + meta_input_name + '"]:checked').val());
									inner_array[meta_input_name] = cb_value;
								}
							}else if(meta_input_type == 'textarea'){
								inner_array[meta_input_name] = jQuery(this).find('textarea[name="' + meta_input_name + '"]').val();
							}else if(meta_input_type == 'select'){
								inner_array[meta_input_name] = jQuery(this).find('select[name="' + meta_input_name + '"]').val();
							}else if (meta_input_type == 'html-conditions') {
								
								var all_conditions = {};
								var the_conditions = new Array();	//{};
								
								all_conditions['visibility'] = jQuery(
										this)
										.find(
												'select[name="condition_visibility"]')
										.val();
								all_conditions['bound'] = jQuery(
										this)
										.find(
												'select[name="condition_bound"]')
										.val();
								jQuery(this).find('div.webcontact-rules').each(function(i, div_box){
								
									var the_rule = {};
									
									the_rule['elements'] = jQuery(
											this)
											.find(
													'select[name="condition_elements"]')
											.val();
									the_rule['operators'] = jQuery(
											this)
											.find(
													'select[name="condition_operators"]')
											.val();
									the_rule['element_values'] = jQuery(
											this)
											.find(
													'select[name="condition_element_values"]')
											.val();
									
									the_conditions.push(the_rule);
								});
								
								all_conditions['rules'] = the_conditions;
								inner_array[meta_input_name] = all_conditions;
							}else if (meta_input_type == 'pre-images') {
								
								var all_preuploads = new Array();
								jQuery(this).find('div.pre-upload-box table').each(function(i, preupload_box){
									var pre_upload_obj = {	link: jQuery(preupload_box).find('input[name="pre-upload-link"]').val(),
											title: jQuery(preupload_box).find('input[name="pre-upload-title"]').val(),
											price: jQuery(preupload_box).find('input[name="pre-upload-price"]').val(),};
									
									all_preuploads.push(pre_upload_obj);
								});
								
								inner_array['images'] = all_preuploads;
							}else if (meta_input_type == 'paired') {
								
								var all_options = new Array();
								jQuery(this).find('div.data-options').each(function(i, option_box){
									var option_set = {	option: jQuery(option_box).find('input[name="options[option]"]').val(),
														price: jQuery(option_box).find('input[name="options[price]"]').val(),};
									
									all_options.push(option_set);
								});
								
								inner_array['options'] = all_options;
							} else {
								inner_array[meta_input_name] = jQuery.trim(jQuery(this).find('input[name="'+ meta_input_name+ '"]').val())
								// inner_array.push(temp);
							}
							
						});

				co_meta_values.push( inner_array );

			});
	

	//console.log(co_meta_values); return false;
	// ok data is collected, so send it to server now Huh?

	var productmeta_id = jQuery('input[name="productmeta_id"]').val();

	do_action = 'nm_cofm_update_all_co_fields';
	
	var server_data = {
		action 				: do_action,
		fields_type	: type,		
		checkout_meta 		: co_meta_values
	}
		jQuery.post(ajaxurl, server_data, function(resp) {
			
			console.log(resp);
			jQuery("#"+type+"-fields-updating").html(resp.message);
			
			if(resp.status == 'success'){
				
				// alert(resp.message);
				window.location.reload(true);
			}
			
		}, 'json');
}

function validate_api_cofm(form){
	
	jQuery(form).find("#nm-sending-api").html(
			'<img src="' + nm_cofm_vars.doing + '">');
	
	var data = jQuery(form).serialize();
	data = data + '&action=nm_cofm_validate_api';
	
	jQuery.post(ajaxurl, data, function(resp) {

		//console.log(resp);
		jQuery(form).find("#nm-sending-api").html(resp.message);
		if( resp.status == 'success' ){
			window.location.reload(true);			
		}
	}, 'json');
	
	
	return false;
}