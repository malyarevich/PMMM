/*
 * NOTE: all actions are prefixed by plugin shortnam_action_name
 * Plugin Verion 3.9.1
 */
var boxes		= new Array();	//checking bound connection
var cfom_extra_price = {};
var oAJAXRequest = false;

jQuery(function($){	
	
	
	if( jQuery('form.checkout select').length > 0 )
		jQuery('form.checkout select').select2();
	
	//conditional elements handling
	$("form.checkout").find('select, input[type="checkbox"], input[type="radio"]').on('change', function(){
		
		var element_name 	= $(this).attr("name");
		var element_value	= '';
		if($(this).attr('data-type') === 'radio'){
			element_value	= $(this).filter(':checked').val();
		}else{
			element_value	= $(this).val();
		}
		
		$("form.checkout p, form.checkout div.fileupload-box").each(function(i, p_box){

			var parsed_conditions 	= $.parseJSON ($(p_box).attr('data-rules'));
			var box_id				= $(p_box).attr('id');
			var element_box = new Array();
			//console.log( parsed_conditions );
			
			if(parsed_conditions !== null){
			
				
				var _visiblity		= parsed_conditions.visibility;
				var _bound			= parsed_conditions.bound;
				var _total_rules 	= Object.keys(parsed_conditions.rules).length;
				
				 var matched_rules = {};
				 var last_meched_element = '';
				$.each(parsed_conditions.rules, function(i, rule){
					
					var _element 		= rule.elements;
					var _elementvalues	= rule.element_values;
					var _operator 		= rule.operators;
					
					//console.log('_element ='+_element+' element_name ='+element_name);
					var matched_rules = {};	
					
					if(_element === element_name && last_meched_element !== _element){
						
						//console.log(_element);
						var temp_matched_rules = {};
						
						switch(_operator){
						
							case 'is':
								
								if(_elementvalues === element_value){
									
									last_meched_element = element_name;
									
									if(boxes[box_id]){
					                    jQuery.each(boxes[box_id], function(j, matched){
					                        if(matched !== undefined){
					                            jQuery.each(matched, function(k,v){
					                            	if(k !== _element){
					                            		temp_matched_rules[k]=v;
						                                element_box.push(temp_matched_rules);
					                            	}
					                            });
					                        }
					                    });
					                }
									
									matched_rules[_element]=element_value;
					                element_box.push(matched_rules);
					                boxes[box_id] = element_box;
								}else{
									
									remove_existing_rules_cofm(boxes[box_id], _element);
									//reset value if set before
									jQuery('#'+box_id).find(':input').not(':checkbox, :radio').val('');
									jQuery('#'+box_id).find(':input','select').removeAttr('checked').removeAttr('selected');
									jQuery('#'+box_id).find('select, input[type="checkbox"], input[type="radio"]').change();
									
									
								}		
								break;
								
								
							case 'not':
								
								if(_elementvalues !== element_value){
									
									if(boxes[box_id]){
					                    jQuery.each(boxes[box_id], function(j, matched){
					                        if(matched !== undefined){
					                            jQuery.each(matched, function(k,v){
					                            	if(k !== _element){
					                            		temp_matched_rules[k]=v;
						                                element_box.push(temp_matched_rules);
					                            	}
					                            });
					                        }
					                    });
					                }
									
									matched_rules[_element]=element_value;
					                element_box.push(matched_rules);
					                boxes[box_id] = element_box;
								}else{
									
									remove_existing_rules_cofm(boxes[box_id], _element);
									//reset value if set before
									jQuery('#'+box_id).find(':input').not(':checkbox, :radio').val('');
									jQuery('#'+box_id).find(':input','select').removeAttr('checked').removeAttr('selected');
									jQuery('#'+box_id).find('select, input[type="checkbox"], input[type="radio"]').change();
									
									
								}		
								break;
								
								
								case 'greater then':
									
									if(parseFloat(_elementvalues) < parseFloat(element_value) ){
										
										if(boxes[box_id]){
						                    jQuery.each(boxes[box_id], function(j, matched){
						                        if(matched !== undefined){
						                            jQuery.each(matched, function(k,v){
						                            	if(k !== _element){
						                            		temp_matched_rules[k]=v;
							                                element_box.push(temp_matched_rules);
						                            	}
						                            });
						                        }
						                    });
						                }
										
										matched_rules[_element]=element_value;
						                element_box.push(matched_rules);
						                boxes[box_id] = element_box;
									}else{
										
										remove_existing_rules_cofm(boxes[box_id], _element);
										//reset value if set before
										jQuery('#'+box_id).find(':input').not(':checkbox, :radio').val('');
										jQuery('#'+box_id).find(':input','select').removeAttr('checked').removeAttr('selected');
										jQuery('#'+box_id).find('select, input[type="checkbox"], input[type="radio"]').change();
									
										
									}		
									break;
									
								
								case 'less then':
									
									if(parseFloat(_elementvalues) > parseFloat(element_value) ){
										
										if(boxes[box_id]){
						                    jQuery.each(boxes[box_id], function(j, matched){
						                        if(matched !== undefined){
						                            jQuery.each(matched, function(k,v){
						                            	if(k !== _element){
						                            		temp_matched_rules[k]=v;
							                                element_box.push(temp_matched_rules);
						                            	}
						                            });
						                        }
						                    });
						                }
										
										matched_rules[_element]=element_value;
						                element_box.push(matched_rules);
						                boxes[box_id] = element_box;
									}else{
										
										remove_existing_rules_cofm(boxes[box_id], _element);
										//reset value if set before
										jQuery('#'+box_id).find(':input').not(':checkbox, :radio').val('');
										jQuery('#'+box_id).find(':input','select').removeAttr('checked').removeAttr('selected');
										jQuery('#'+box_id).find('select, input[type="checkbox"], input[type="radio"]').change();
									
										
									}		
									break;
									}
						
						set_visibility_cofm(p_box, _bound, _total_rules, _visiblity);
					}
					
				});
				
			}
			
			
		});
		
	});
	
	setTimeout(function(){
		
		$("form.checkout").find('select, input[type="checkbox"], input[type="radio"]').trigger('change');
	},
	200);

});


function set_visibility_cofm(p_box, _bound, _total_rules, _visiblity){
	
	var box_id				= jQuery(p_box).attr('id');
	var input_id			= jQuery(p_box).closest('div').attr('input-id');
	if( jQuery(p_box).closest('div.woocommerce-billing-fields').length > 0 ){
		input_id = jQuery(p_box).attr('data-fieldid');
	}

		// console.log(box_id+': total rules = '+_total_rules+' rules matched = '+Object.keys(boxes[box_id]).length);
			
	switch(_visiblity){
	
	case 'Show':
		
		if(boxes[box_id] !== undefined && ( (_bound === 'Any' &&  (Object.keys(boxes[box_id]).length > 0)) || _total_rules === Object.keys(boxes[box_id]).length) ){
			jQuery(p_box).show(200, function(){
				var inner_input = jQuery(p_box).find('input, textarea');
				var hidden_name = '_'+input_id+'_';
				jQuery('input:hidden[name="'+hidden_name+'"]').remove();
				inner_input.after('<input type="hidden" name="'+hidden_name+'" value="showing" />');
			});
			
		}else{
			
			jQuery(p_box).hide(200, function(){
				var inner_input = jQuery(p_box).find('input, textarea');
				console.log(inner_input);
				var hidden_name = '_'+input_id+'_';
				jQuery('input:hidden[name="'+hidden_name+'"]').remove();
				inner_input.after('<input type="hidden" name="'+hidden_name+'" value="hidden" />');
			});
      		
      		//update_rule_childs(element_name);
		}
		break;					
	
	case 'Hide':
		
		
		if(boxes[box_id] !== undefined && ( (_bound === 'Any' &&  (Object.keys(boxes[box_id]).length > 0)) || _total_rules === Object.keys(boxes[box_id]).length) ){
			jQuery(p_box).hide(200, function(){
				jQuery(p_box).find('select, input:radio, input:text, textarea').val('');
				var inner_input = jQuery(p_box).find('input, textarea');
				var hidden_name = '_'+input_id+'_';
				jQuery('input:hidden[name="'+hidden_name+'"]').remove();
				inner_input.after('<input type="hidden" name="'+hidden_name+'" value="hidden" />');
			});
			// console.log('hiddedn rule '+box_id);
		}else{
			jQuery(p_box).show(200, function(){
				var inner_input = jQuery(p_box).find('input, textarea');
				var hidden_name = '_'+input_id+'_';
				jQuery('input:hidden[name="'+hidden_name+'"]').remove();
				inner_input.after('<input type="hidden" name="'+hidden_name+'" value="showing" />');
			});
			
		}
		break;
	}
}

function update_rule_childs_cofm(element_name, element_values){
	
	jQuery(".nm-productmeta-box > p, .nm-productmeta-box div.fileupload-box").each(function(i, p_box){

		var parsed_conditions 	= jQuery.parseJSON (jQuery(p_box).attr('data-rules'));
		var box_id				= jQuery(p_box).attr('id');
		
		if(parsed_conditions !== null){
		
			var _visiblity		= parsed_conditions.visibility;
			var _bound			= parsed_conditions.bound;
			var _total_rules 	= Object.keys(parsed_conditions.rules).length;
			
			 var matched_rules = {};
			 var last_meched_element = '';
			jQuery.each(parsed_conditions.rules, function(i, rule){
				
				var _element 		= rule.elements;
				var _elementvalues	= rule.element_values;
				var _operator 		= rule.operators;
				
				//console.log('_element ='+_element+' element_name ='+element_name);
				var matched_rules = {};	
				
				if(element_values === 'child')
					_elementvalues = element_values;
				
				if(_element === element_name && _elementvalues === element_values){
					//console.log('Hiding _element ='+_element+' under box ='+jQuery(p_box).find('select').attr('name'));
					//console.log('hiddedn rule '+element_name+' value ' + element_values + 'under box = ' + jQuery(p_box).attr('id'));
					jQuery(p_box).hide(300, function(){
						update_rule_childs_cofm(jQuery(this).find('select, input:radio').attr('name'), 'child');
					});
					
				}
			});
		}
});
	
}
	
function remove_existing_rules_cofm(box_rules, element){
	
	if(box_rules){
        jQuery.each(box_rules, function(j, matched){
            if(matched !== undefined){
                jQuery.each(matched, function(k,v){
                	if(k === element){
                  		delete box_rules[j];
                  		update_rule_childs_cofm(k, v);
                	}
                });
            }
        });
    }
}

/* Starting ...
** ================= extra options price =============
*/
function get_all_priced_options(){
					
	jQuery('.woocommerce-checkout-review-order-table').block({
                    message: null,
                    overlayCSS: {
                    background: "#fff",
                    opacity: .6
			                    }
	         });
	         
	//Select
	jQuery("form.checkout select").each(function(i, elem){
	
			var thefee = {};
			thefee.title 	= jQuery(elem).attr('data-label') +' - '+jQuery(elem).find(":selected").val();
			thefee.price 	= jQuery(elem).find(":selected").attr('data-price');
			thefee.tax 		= jQuery(elem).attr('data-tax');
			thefee.calc		= jQuery(elem).attr('data-calculate');
			
			cfom_extra_price[jQuery(elem).attr('name')] = thefee;
	});
	
	
	//Radio
	jQuery("form.checkout input:radio").each(function(i, elem){

		var thefee = {};
		if(jQuery(elem).is(':checked')){
			
			if( jQuery(elem).attr('field-type') !== 'image' )
				thefee.title 	= jQuery(elem).attr('data-label') +' - '+jQuery(elem).val();
			else	
				thefee.title 	= jQuery(elem).attr('data-label');
				
			thefee.price 	= jQuery(elem).attr('data-price');
			thefee.tax 		= jQuery(elem).attr('data-tax');
			thefee.calc		= jQuery(elem).attr('data-calculate');
			
			//console.log(thefee);
		}
		cfom_extra_price[jQuery(elem).attr('id')] = thefee;
	});
	
	//Checkbox
	jQuery("form.checkout input:checkbox").each(function(i, elem){

		var thefee = {};
		if(jQuery(elem).is(':checked')){
			thefee.title 	= jQuery(elem).attr('data-label') +' - '+jQuery(elem).val();
			thefee.price 	= jQuery(elem).attr('data-price');
			thefee.tax 		= jQuery(elem).attr('data-tax');
			thefee.calc		= jQuery(elem).attr('data-calculate');
		}
		cfom_extra_price[jQuery(elem).attr('id')] = thefee;
	});
	
	
	cofm_update_extra_options();
}


function cofm_update_extra_options(){
	         
     var data = {action: 'nm_cofm_update_checkout', 
			security:wc_checkout_params.update_order_review_nonce,
			extra_item_fee: cfom_extra_price,
            };
            
    setTimeout(function(){
  			
	  	if( oAJAXRequest != false )
		return;
            
		oAJAXRequest = jQuery.post(nm_cofm_vars.ajaxurl, data, function(resp){
			
			jQuery(document.body).trigger("update_checkout");
			oAJAXRequest = false;
	        
		});
    }, 200);
				
}


/* Ending ...
** ================= extra options price =============
*/

function get_option(key){
	
	/*
	 * TODO: change plugin shortname
	 */
	var keyprefix = 'nm_todolist';
	
	key = keyprefix + key;
	
	var req_option = '';
	
	jQuery.each(googlerabwah_vars.settings, function(k, option){
		
		//console.log(k);
		
		if (k == key)
			req_option = option;		
	});
	
	//console.log(req_option);
	return req_option;
	
}