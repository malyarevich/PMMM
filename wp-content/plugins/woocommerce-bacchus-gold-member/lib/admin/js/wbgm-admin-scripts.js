/**
 * @file wbgm-admin-scripts.js
 *
 * Script for Woocommerce Bacchus Gold Member.
 *
 * Copyright (c) 2015, Yevgen <yevgen.slyuzkin@gmail.com>
 */
jQuery(document).ready(function($) {

	//activate chosen
	if ($('.wbgm-ajax-select').length) {
		initialize_selectize($('.wbgm-ajax-select'));
	}

});

/**
 * Initialize selectize js in a element.
 * 
 * @param elm
 */
function initialize_selectize(elm) {
	elm.selectize({
		valueField: 'id',
		labelField: 'text',
		searchField: 'text',
		highlight: true,
		create: false,
		loadingClass: 'wbgm-selectize-loading',
		load: function (query, callback) {
			if (!query.length) return callback();
			jQuery.ajax({
				url: 'admin-ajax.php',
				dataType: 'json',
				method: 'GET',
				data: {
					action: 'product_list_callback',
					q: query
				},
				success: function (res) {
					callback(res.options);
				}
			});
		}
	});
}