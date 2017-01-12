jQuery(document).ready(function($) {
	$('.tracking_script .active_tracking').live('click', function(e) {
		var active = $(this).siblings('.script_active').attr('value');
		
		$(this).removeClass('fa-circle-o');
		$(this).removeClass('fa-check-circle');
		
		if(active === 'true') {
			$(this).addClass('fa-circle-o');
			$(this).attr('title', 'Activate Script');
			$(this).siblings('.script_active').attr('value', 'false');
		} else {
			$(this).addClass('fa-check-circle');
			$(this).attr('title', 'Deactive Script');
			$(this).siblings('.script_active').attr('value', 'true');
		}
	});
	
	$('.tracking_script .edit_tracking').live('click', function(e) {
		$(this).removeClass('fa-edit');
		$(this).removeClass('fa-save');	

		if($(this).siblings('.script_info').find('input[type="text"]').attr('readonly') === 'readonly') {
			$(this).siblings('.script_info').find('input[type="text"]').attr('readonly', false);
			$(this).addClass('fa-save');
			$(this).attr('title', 'Save Script');
		} else {
			$('.tracking_scripts_wrap form').submit();
			$(this).siblings('.script_info').find('input[type="text"]').attr('readonly', 'readonly');
			$(this).addClass('fa-edit');
			$(this).attr('title', 'Edit Script');
		}
	});
	
	$('.tracking_script .delete_tracking').live('click', function(e) {
		var confirmed = confirm("Are you sure you want to delete this script?");
		if(confirmed) {
			$(this).parent().fadeOut(400, function() {
				$(this).find('.script_exists').attr('value', 'false');
				
				var index = 1;
				$(this).parent().find('.tracking_script').each(function(i) {
					if($(this).css('display') === 'block') {
						$(this).find('> p').text(index);
						$(this).find('.script_order').attr('value', index);
						index++;
					}
				});
				
				$('.tracking_scripts_wrap form').submit();
			});
		}
	});
	
	$('.tracking_script_list').sortable({
		update: function(event, ui) {
			$(this).find('.tracking_script').each(function(i) {
				i++;
				$(this).find('> p').text(i);
				$(this).find('.script_order').attr('value', i);
			});
		}
	});
	
	
	$('#save_new_tracking_codes').click(function(e) {
		if($('#new_page_tracking_script_code_global').find(':selected').val() == 'no' && ($('#tracking_scripts_new_post').find(':selected').val() == 'none' || $('#tracking_scripts_new_post_type').find(':selected').val() == 'none')) {
			e.preventDefault();
			alert("No Post/Page Selected. Please select one");
		}
	});


	// Pages JS
	var tsm_currentSinglePostID = "none";
	$tracking_pages_post_content = $('.tracking_scripts_page_content');
	$('#tracking_script_single_page').change(function(e) {
		if(tsm_currentSinglePostID != "none") {
			var quitScript = confirm("Unsaved changes will be lost. Continue?");
			if(quitScript == true) {
				var postID = $('#tracking_script_single_page option:selected').val();
				$tracking_pages_post_content.empty();
				tsm_currentSinglePostID = postID;
				if(postID != 'none') {
					tsm_load_post_content();
				}
			} else {
				$('#tracking_script_single_page').val(tsm_currentSinglePostID);
			}
		} else {
			var postID = $('#tracking_script_single_page option:selected').val();
			$tracking_pages_post_content.empty();
			if(postID != 'none') {
				tsm_currentSinglePostID = postID;
				tsm_load_post_content();
			}
		}
	});
	
	var tsm_load_post_content = function() {
		var data = {
			'action': 'tracking_scripts_get_post_content',
			'postID': tsm_currentSinglePostID
		};

		$.post(ajaxurl, data, function(response) {
			$tracking_pages_post_content.empty();
			$tracking_pages_post_content.append(response); 
			$tracking_pages_post_content.find('.tracking_script_list').sortable({
				update: function(event, ui) {
					$(this).find('.tracking_script').each(function(i) {
						i++;
						$(this).find('> p').text(i);
						$(this).find('.script_order').attr('value', i);
					});
				}
			});
		});
	}

	// Single Page JS
	var tsm_postType = 'post';
	$tracking_posts_content = $('#tracking_scripts_new_post');
	var tsm_load_posts = function() {
		var data = {
			'action': 'tracking_scripts_get_posts',
			'postType': tsm_postType
		};

		$.post(ajaxurl, data, function(response) {
			$tracking_posts_content.empty();
			$tracking_posts_content.append(response); 
			//$('#add_page_tracking_script_post > label').text(tsm_postType+":");
			//$('#add_page_tracking_script_post > label').css('textTransform', 'capitalize');
			$('#add_page_tracking_script_post').css('display', 'block');
		});
	};
	
	$('#add_page_tracking_script_global').change(function(e) {
		var tsm_global = $('input[name="new_page_tracking_script_code_global"]:checked').val();
		if(tsm_global == 'yes') {
			$('#add_page_tracking_script_post_type').css('display', 'none');
			$('#add_page_tracking_script_post').css('display', 'none');
			$('#tracking_scripts_new_post_type').val('none');
			$('#new_page_tracking_script_code_id').val('');
			$('#add_page_tracking_script_code').css('display', 'block');
		} else {
			$('#add_page_tracking_script_post_type').css('display', 'block');
			$('#add_page_tracking_script_post').css('display', 'none');
			$('#new_page_tracking_script_code_id').val('');
			$('#add_page_tracking_script_code').css('display', 'none');
		}
	});

	$('#tracking_scripts_new_post_type').change(function(e) {
		tsm_postType = $('#tracking_scripts_new_post_type option:selected').val();
		if(tsm_postType == 'none') {
			$('#add_page_tracking_script_post').css('display', 'none');
			$('#new_page_tracking_script_code_id').val('');
			$('#add_page_tracking_script_code').css('display', 'none');
		} else {
			tsm_load_posts();
		}
	});

	$('#tracking_scripts_new_post').change(function(e) {
		var postID = $('#tracking_scripts_new_post option:selected').val();
		if(postID == 'none') {
			$('#new_page_tracking_script_code_id').val('');
			$('#add_page_tracking_script_code').css('display', 'none');
		} else {
			$('#new_page_tracking_script_code_id').val(postID);
			$('#add_page_tracking_script_code').css('display', 'block');
		}
	});
});