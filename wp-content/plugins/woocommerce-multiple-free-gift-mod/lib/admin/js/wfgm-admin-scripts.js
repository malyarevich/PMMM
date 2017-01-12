/**
 * @file wfgm-admin-scripts.js
 *
 * Script for Woocommerce Free Gift Mod plugin.
 *
 * Copyright (c) 2016, Yevgen <yevgen.slyuzkin@gmail.com>
 */
jQuery(document).ready(function($) {

	if ($('.wfgm-ajax-select').length) {
		initialize_selectize($('.wfgm-ajax-select'));
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
		loadingClass: 'wfgm-selectize-loading',
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