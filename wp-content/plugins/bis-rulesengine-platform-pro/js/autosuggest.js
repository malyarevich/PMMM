jQuery(function() {
	
	function split(val) {
		return val.split(/,\s*/);
	}
	
	function extractLast(term) {
		return split(term).pop();
	}
	
	function getCriteria(val) {
		var ele = jQuery(val).closest("td").prevAll();
		return jQuery(ele[2]).children()[0].value;
	}
 	
	function getSubCriteria(val) {
		var ele = jQuery(val).closest("td").prevAll();
		return  jQuery(ele[1]).children().children().children()[1].value;
	}
	
	function getCondition(val) {
		
		var ele = jQuery(val).closest("td").prevAll();
		return  jQuery(ele[0]).children().children().children()[1].value;
	}
	
	 var bis_nonce = BISAjax.bis_rules_engine_nonce;

	 jQuery("#bis_re_rule_value").tokenInput(ajaxurl + "?action=bis_re_get_value&bis_nonce="+bis_nonce, {
         theme: "facebook"
     });
});