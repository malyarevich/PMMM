(function($){
"use strict";

	var pf_singlesc = false;
	if ( $('.prdctfltr_sc_products.prdctfltr_ajax').length == 1 && !$('.prdctfltr_sc_products .prdctfltr_wc').hasClass('prdctfltr_step_filter') ) {
		$('body').addClass('prdctfltr-ajax');
		pf_singlesc = 1;
	}

	function prdctfltr_sort_classes() {
		if ( prdctfltr.ajax_class == '' ) {
			prdctfltr.ajax_class = '.products';
		}
		if ( prdctfltr.ajax_category_class == '' ) {
			prdctfltr.ajax_category_class = '.product-category';
		}
		if ( prdctfltr.ajax_product_class == '' ) {
			prdctfltr.ajax_product_class = '.type-product';
		}
		if ( prdctfltr.ajax_pagination_class == '' ) {
			prdctfltr.ajax_pagination_class = '.woocommerce-pagination';
		}
		if ( prdctfltr.ajax_count_class == '' ) {
			prdctfltr.ajax_count_class = '.woocommerce-result-count';
		}
		if ( prdctfltr.ajax_orderby_class == '' ) {
			prdctfltr.ajax_orderby_class = '.woocommerce-ordering';
		}
	}
	prdctfltr_sort_classes();

	var pf_failsafe = false;
	function ajax_failsafe() {
		if ( prdctfltr.ajax_failsafe.length == 0 ) {
			return false;
		}
		if ( $('.prdctfltr_sc_products').length > 0 ) {
			return false;
		}
		if ( $('body').hasClass('prdctfltr-ajax') ) {
			pf_failsafe = false;
			if( $.inArray('wrapper', prdctfltr.ajax_failsafe) !== -1 ) {
				if ( $(prdctfltr.ajax_class).length < 1 ) {
					pf_failsafe = true;
				}
			}
			if( $.inArray('product', prdctfltr.ajax_failsafe) !== -1 ) {
				if ( $(prdctfltr.ajax_class+' '+prdctfltr.ajax_product_class).length < 1 ) {
					pf_failsafe = true;
				}
			}

			if( $.inArray('pagination', prdctfltr.ajax_failsafe) !== -1 ) {
				if ( $(prdctfltr.ajax_pagination_class).length < 1 ) {
					pf_failsafe = true;
				}
			}

			if ( pf_failsafe === true ) {
				console.log('PF: AJAX Failsafe active.');
			}
		}
	}
	ajax_failsafe();

	prdctfltr.clearall = ( $.isArray(prdctfltr.clearall) === true ? prdctfltr.clearall : false );

	var archiveAjax = false;
	if ( $('body').hasClass('prdctfltr-ajax') && pf_failsafe === false ) {
		archiveAjax = true;
	}

	if ( archiveAjax === true ) {
		var pageFilters = {};

		$('.prdctfltr_wc').each( function() {
			pageFilters[$(this).attr('data-id')] = $("<div />").append($(this).clone()).html();
		});

		if ( prdctfltr.rangefilters ) {
			pageFilters.ranges = prdctfltr.rangefilters;
		}

		if ( $('body').hasClass('prdctfltr-ajax') ) {
			pageFilters.products = $("<div />").append($(prdctfltr.ajax_class).clone()).html();
			pageFilters.pagination = $("<div />").append($(prdctfltr.ajax_pagination_class).clone()).html();
			pageFilters.count = $("<div />").append($(prdctfltr.ajax_count_class).clone()).html();
			pageFilters.orderby = $("<div />").append($(prdctfltr.ajax_orderby_class).clone()).html();
		}

		History.replaceState({filters:pageFilters}, document.title, '');
	}

	var curr_data = {};
	var ajaxActive = false;
	var priceRatio = prdctfltr.priceratio;

	$('.prdctfltr_subonly').each( function() {
		prdctfltr_show_sub_cats($(this).closest('.prdctfltr_wc:not(.prdctfltr_tabbed_selection)'));
	});

	$.expr[':'].Contains = function(a,i,m){
		return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
	};

	String.prototype.getValueByKey = function (k) {
		var p = new RegExp('\\b' + k + '\\b', 'gi');
		return this.search(p) != -1 ? decodeURIComponent(this.substr(this.search(p) + k.length + 1).substr(0, this.substr(this.search(p) + k.length + 1).search(/(&|;|$)/))) : "";
	};

	function init_ranges() {
		$.each( prdctfltr.rangefilters, function(i, obj3) {
			obj3.onFinish = function (data) {

				if ( $('#'+i).hasClass('pf_rng_price') ) {
					if ( data.min == data.from && data.max == data.to ) {$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_min_"]:first').val( '' );$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_max_"]:first').val( '' ).trigger('change');}else {$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_min_"]:first').val( ( data.from_value == null ? Math.round( parseInt(data.from)*priceRatio ) : Math.round( parseInt($(data.from_value).text())*priceRatio ) ) );$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_max_"]:first').val( ( data.to_value == null ? Math.round( parseInt(data.to)*priceRatio ) : Math.round( parseInt($(data.to_value).text())*priceRatio ) ) ).trigger('change');}
				}
				else {
					if ( data.min == data.from && data.max == data.to ) {$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_min_"]:first').val( '' );$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_max_"]:first').val( '' ).trigger('change');} else {$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_min_"]:first').val( ( data.from_value == null ? data.from : $(data.from_value).text() ) );$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_max_"]:first').val( ( data.to_value == null ? data.to : $(data.to_value).text() ) ).trigger('change');}
				}

				var curr_filter = $('#'+i).closest('.prdctfltr_wc');
				if ( curr_filter.hasClass('prdctfltr_tabbed_selection') && curr_filter.hasClass('prdctfltr_click') ) {
					curr_filter.find('.prdctfltr_filter').each( function() {
						if ( $(this).find('input[type="hidden"]:first').length > 0 && $(this).find('input[type="hidden"]:first').val() !== '' ) {
							if ( !$(this).hasClass('prdctfltr_has_selection') ) {
								$(this).addClass('prdctfltr_has_selection');
							}
							
						}
						else {
							if ( $(this).hasClass('prdctfltr_has_selection') ) {
								$(this).removeClass('prdctfltr_has_selection');
							}
						}
					});
				}
			};
			$('#'+i).ionRangeSlider(obj3);
		});
	}
	init_ranges();

	function reorder_selected(curr) {

		curr = ( curr == null ? $('.prdctfltr_wc') : curr );

		curr.each( function() {

			var currEl = $(this);

			if ( $(this).hasClass('prdctfltr_selected_reorder') ) {

				currEl.find('.prdctfltr_filter.prdctfltr_attributes:not(.prdctfltr_hierarchy) .prdctfltr_checkboxes, .prdctfltr_filter.prdctfltr_vendor .prdctfltr_checkboxes, .prdctfltr_filter.prdctfltr_byprice .prdctfltr_checkboxes, .prdctfltr_filter.prdctfltr_orderby .prdctfltr_checkboxes').each( function() {
					var checkboxes = $(this);
					if ( checkboxes.find('label.prdctfltr_active').length > 0 ) {
						if ( checkboxes.find('label.prdctfltr_ft_none').length > 0 || checkboxes.find('label.prdctfltr_ft_').length > 0 ) {
							checkboxes.find('label.prdctfltr_active').each( function () {
								var addThis = $(this);
								$(this).remove();
								checkboxes.find('label:not(.prdctfltr_active):not(.prdctfltr_ft_none):not(.prdctfltr_ft_):first').before(addThis);
							});

						}
						else {
							checkboxes.find('label.prdctfltr_active').each( function () {
								var addThis = $(this);
								$(this).remove();
								checkboxes.prepend(addThis);
							});
						}
					}
				});
			}
		});

	}
	reorder_selected();

	function reorder_adoptive(curr) {

		curr = ( curr == null ? $('.prdctfltr_wc') : curr );

		curr.each( function() {

			var currEl = $(this);
			if ( $(this).hasClass('prdctfltr_adoptive_reorder') ) {
				currEl.find('.prdctfltr_adoptive').each( function() {
					var filter = $(this);
					if ( filter.find('.pf_adoptive_hide').length > 0 ) {
						var checkboxes = filter.find('.prdctfltr_checkboxes');
						filter.find('.pf_adoptive_hide').each( function() {
							var addThis = $(this);
							$(this).remove();
							checkboxes.append(addThis);
						});
					}
				});
			}


		});

	}
	reorder_adoptive();

	$(document).on('click', '.pf_more:not(.pf_activated)', function() {
		var filter = $(this).closest('.prdctfltr_attributes, .prdctfltr_meta');
		var checkboxes = filter.find('.prdctfltr_checkboxes');
		var curr = filter.closest('.prdctfltr_wc');

		if ( curr.hasClass('pf_adptv_default') ) {
			var searchIn = '> label:not(.pf_adoptive_hide)';
		}
		else {
			var searchIn = '> label';
		}

		var displayType = checkboxes.find(searchIn+':first').css('display');

		checkboxes.find(searchIn).attr('style', 'display:'+displayType+' !important');
		checkboxes.find('.pf_more').html('<span>'+prdctfltr.localization.show_less+'</span>');
		checkboxes.find('.pf_more').addClass('pf_activated');

		if ( filter.closest('.prdctfltr_wc').hasClass('pf_mod_masonry') ) {
			filter.closest('.prdctfltr_filter_inner').isotope('layout');
		}
	});

	$(document).on('click', '.pf_more.pf_activated', function() {
		var filter = $(this).closest('.prdctfltr_attributes, .prdctfltr_meta');
		var checkboxes = filter.find('.prdctfltr_checkboxes');
		var curr = filter.closest('.prdctfltr_wc');
		if ( curr.hasClass('pf_adptv_default') ) {
			var searchIn = '> label:not(.pf_adoptive_hide)';
		}
		else {
			var searchIn = '> label';
		}
		checkboxes.each(function(){
			var max = parseInt(filter.attr('data-limit'));
			if (max != 0 && $(this).find(searchIn).length > max+1) {

				$(this).find(searchIn+':gt('+max+')').attr('style', 'display:none !important');
				$(this).find('.pf_more').html('<span>'+prdctfltr.localization.show_more+'</span>').removeClass('pf_activated');

				if ( filter.closest('.prdctfltr_wc').hasClass('pf_mod_masonry') ) {
					filter.closest('.prdctfltr_filter_inner').isotope('layout');
				}
			}
		});
	});

	function set_select_index(curr) {

		curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

		curr.each( function() {

			var curr_el = $(this);

			var selects = curr_el.find('.pf_select .prdctfltr_filter');
			if ( selects.length > 0 ) {
				var zIndex = selects.length;
				selects.each( function() {
					$(this).css({'z-index':zIndex});
					zIndex--;
				});
			}
		});

	}
	set_select_index();

	function init_search(curr) {

		var curr = $('.prdctfltr_wc');

		curr.each( function() {

			var curr_el = $(this);

			curr_el.find('input.pf_search').each( function() {
				if ( curr_el.hasClass('prdctfltr_click_filter') ) {
					$(this).keyup( function () {
						if ($(this).next().is(':hidden')) {
							$(this).next().show();
						}
						if ($(this).val()==''){
							$(this).next().hide();
						}
					});
				}
			});
		});
	}
	init_search();


	$(document).on( 'keydown', '.pf_search', function() {
		if(event.which==13) {
			$(this).next().trigger('click');
			return false;
		}
	});

	$(document).on( 'click', '.pf_search_trigger', function() {
		var wc = $(this).closest('.prdctfltr_wc');

		if ( !wc.hasClass('prdctfltr_click_filter') ) {
			wc.find('.prdctfltr_woocommerce_filter_submit').trigger('click');
		}
		else {
			var obj = wc.find('.prdctfltr_woocommerce_ordering');
			prdctfltr_respond_550(obj);
		}

		return false;
	});

	function prdctfltr_filter_terms_init(curr) {
		curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

		curr.each( function() {
			var curr_el = $(this);
			if ( curr_el.hasClass('prdctfltr_search_fields') ) {
				curr_el.find('.prdctfltr_filter.prdctfltr_attributes .prdctfltr_add_scroll, .prdctfltr_filter.prdctfltr_vendor .prdctfltr_add_scroll, .prdctfltr_filter.prdctfltr_meta .prdctfltr_add_scroll').each( function() {
					var curr_list = $(this);
					prdctfltr_filter_terms(curr_list);
				});
			}
		});

	}
	prdctfltr_filter_terms_init();

	function is_touch_device() {
		return 'ontouchstart' in window || navigator.maxTouchPoints;
	}


	function prdctfltr_init_tooltips(curr) {
		if (is_touch_device()!==true) {
			curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

			curr.each( function() {
				var curr_el = $(this);

				var $pf_tooltips = curr_el.find('.prdctfltr_filter.pf_attr_img label, .prdctfltr_terms_customized:not(.prdctfltr_terms_customized_select) label');

				$pf_tooltips
				.on('mouseover', function()
				{
					var $this = $(this);

					if ($this.prop('hoverTimeout'))
					{
						$this.prop('hoverTimeout', clearTimeout($this.prop('hoverTimeout')));
					}

					$this.prop('hoverIntent', setTimeout(function()
					{
						$this.addClass('prdctfltr_hover');
					}, 250));
					})
				.on('mouseleave', function()
					{
					var $this = $(this);

					if ($this.prop('hoverIntent'))
					{
						$this.prop('hoverIntent', clearTimeout($this.prop('hoverIntent')));
					}

					$this.prop('hoverTimeout', setTimeout(function()
					{
						$this.removeClass('prdctfltr_hover');
					}, 250));
				});
			});
		}
	}
	prdctfltr_init_tooltips();

	function reorder_limit(curr) {

		curr = ( typeof curr == 'undefined' ? $('.prdctfltr_wc') : curr );

		curr.each( function() {

			var curr_el = $(this);

			if ( curr_el.hasClass('pf_adptv_default') ) {
				var searchIn = '> label:not(.pf_adoptive_hide)';
			}
			else {
				var searchIn = '> label';
			}

			curr_el.find('.prdctfltr_attributes, .prdctfltr_meta').each( function() {
				var filter = $(this);
				var checkboxes = filter.find('.prdctfltr_checkboxes');
				checkboxes.each(function(){
					var max = parseInt(filter.attr('data-limit'));
					if (max != 0 && $(this).find(searchIn).length > max+1) {
						$(this).find(searchIn+':gt('+max+')').attr('style', 'display:none !important').end().append($('<div class="pf_more"><span>'+prdctfltr.localization.show_more+'</span></div>'));
					}
				});
			});
		});

	}
	reorder_limit();

	function prdctfltr_init_scroll(curr) {

		curr = ( curr == null ? $('.prdctfltr_wc') : curr );

		if ( curr.hasClass('prdctfltr_scroll_active') && curr.hasClass('prdctfltr_maxheight') ) {

			var wrapper = curr.find('.prdctfltr_filter:not(.prdctfltr_range) .prdctfltr_add_scroll');

			wrapper.mCustomScrollbar({
				axis:'y',
				scrollInertia:550,
				autoExpandScrollbar:true,
				advanced:{
					updateOnBrowserResize:true,
					updateOnContentResize:true
				}
			});

			if ( curr.hasClass('pf_mod_row') && ( curr.find('.prdctfltr_checkboxes').length > $('.prdctfltr_filter_wrapper:first').attr('data-columns') ) ) {
				if ( $('.prdctfltr-widget').length == 0 || $('.prdctfltr-widget').length == 1 && $('.prdctfltr-widget .prdctfltr_error').length == 1 ) {

					if ( curr.hasClass('prdctfltr_slide') ) {
						curr.find('.prdctfltr_woocommerce_ordering').show();
					}

					var curr_scroll_column = curr.find('.prdctfltr_filter:first').width();
					var curr_columns = curr.find('.prdctfltr_filter').length;

					curr.find('.prdctfltr_filter_inner').css('width', curr_columns*curr_scroll_column);
					curr.find('.prdctfltr_filter').css('width', curr_scroll_column);
					
					curr.find('.prdctfltr_filter_wrapper').mCustomScrollbar({
						axis:'x',
						scrollInertia:550,
						scrollbarPosition:'outside',
						autoExpandScrollbar:true,
						advanced:{
							updateOnBrowserResize:true,
							updateOnContentResize:false
						}
					});

					if ( curr.hasClass('prdctfltr_slide') ) {
						curr.find('.prdctfltr_woocommerce_ordering').hide();
					}
				}
			}

			if ( $('.prdctfltr-widget').length == 0 || $('.prdctfltr-widget .prdctfltr_error').length == 1 ) {
				curr.find('.prdctfltr_slide .prdctfltr_woocommerce_ordering').hide();
			}

		}
		else if ( curr.hasClass('prdctfltr_scroll_default') && curr.hasClass('prdctfltr_maxheight') ) {
		}
		else {
		}
	}

	function prdctfltr_show_sub_cats(curr) {

		curr = ( curr == null ? $('.prdctfltr_woocommerce:not(.prdctfltr_tabbed_selection)') : curr );
		var ourEl = curr.find('.prdctfltr_subonly label.prdctfltr_active');

		var doIt = true;
		var checkCheckboxes = curr.find('.prdctfltr_subonly .prdctfltr_checkboxes');

		if ( checkCheckboxes.find('label.prdctfltr_active').length > 1 ) {
			if ( checkCheckboxes.find('> label.prdctfltr_active').length > 1 ) {
				doIt = false;
			}
			var checkParents = '';
			checkCheckboxes.find('label.prdctfltr_active input[type="checkbox"]').each( function() {
				if ( checkParents == '' ) {
					checkParents = ( $(this).attr('data-parent') ? $(this).attr('data-parent') : '%toplevel' );
				}
				else {
					if ( $(this).attr('data-parent') !== checkParents ) {
						doIt = false;
					}
				}
			});

		}

		if ( doIt === false ) {
			return;
		}

		ourEl.each( function() {
			var subParent = $(this).closest('.prdctfltr_sub');

			if ( subParent.length > 0 ) {
				var subParentCon = $('<div></div>').append(subParent.clone()).html();
				if ( subParent.prev().is('label') ) {
					subParentCon += $('<div></div>').append(subParent.prev().clone()).html();
				}
			}
			else {
				var currSubParent = $(this).next();

				if ( currSubParent.length > 0 && currSubParent.hasClass('prdctfltr_sub') ) {
					var subParentCon = $('<div></div>').append(currSubParent.clone()).html();

					if ( $(this).is('label') ) {
						subParentCon += $('<div></div>').append($(this).clone()).html();
					}

				}
			}

			if ( typeof subParentCon != 'undefined' ) {
				var checkboxesWrap = $(this).closest('.prdctfltr_checkboxes');
				checkboxesWrap.empty();
				checkboxesWrap.append(subParentCon);
			}

		});

	}

	function prdctfltr_show_opened_cats(curr) {

		curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

		curr.find('label.prdctfltr_active').each( function() {
			$(this).next().show();
			$(this).parents('.prdctfltr_sub').each( function() {
				$(this).show();
				if ( !$(this).prev().hasClass('prdctfltr_clicked') ) {
					$(this).prev().addClass('prdctfltr_clicked');
				}
			});
		});

	}

	function prdctfltr_all_cats(curr) {

		curr = ( curr == null ? $('.prdctfltr_wc') : curr );

		curr.find('.prdctfltr_filter.prdctfltr_attributes.prdctfltr_expand_parents .prdctfltr_sub').each( function() {
			var curr = $(this);
			if ( !curr.is(':visible') ) {
				curr.show();
				if ( !curr.prev().hasClass('prdctfltr_clicked') ) {
					curr.prev().addClass('prdctfltr_clicked');
				}
			}
		});

	}

	function prdctfltr_make_clears(curr) {

		curr = ( curr == null ? $('.prdctfltr_wc') : curr );
		if ( curr.hasClass('pf_remove_clearall') ) {
			return false;
		}

		var clearActive = false;
		var currEls = curr.find('label.prdctfltr_active');
		var currElLength = currEls.length;

		var rangeEl = curr.find('input[name^="rng_m"]').filter(function() { return this.value !== ''; });

		if ( rangeEl.length > 0 ) {
			curr.each( function() {
				var currPf = $(this);
				currPf.find('.prdctfltr_buttons').append('<span class="prdctfltr_reset"><label><input name="reset_filter" type="checkbox" /><span>'+prdctfltr.localization.clearall+'</span></label></span>');
			});
		}
		else if ( currElLength>0 ) {
			currEls.each( function() {

				var currEl = $(this);
				var currElPrnt = currEl.closest('.prdctfltr_filter');
				var currElFilter = currElPrnt.attr('data-filter');

				if ( prdctfltr.clearall[0] != null) {
					if ( $.inArray( currElFilter, prdctfltr.clearall ) > -1 ) {
						
					}
					else {
						clearActive = true;
					}
				}
				else {
					clearActive = true;
				}

				if ( !--currElLength ) {
					if ( clearActive === true ) {
						curr.each( function() {
							var currPf = $(this);
							currPf.find('.prdctfltr_buttons').append('<span class="prdctfltr_reset"><label><input name="reset_filter" type="checkbox" /><span>'+prdctfltr.localization.clearall+'</span></label></span>');
						});
					}
				}

			});
		}

	}
	prdctfltr_make_clears();

	function prdctfltr_submit_form(curr_filter) {

		if ( curr_filter.hasClass('prdctfltr_click_filter') || curr_filter.find('input[name="reset_filter"]:checked').length > 0 ) {

			prdctfltr_respond_550(curr_filter.find('form'));

		}

	}

	$('.prdctfltr_wc').each( function() {

		var curr = $(this);

		prdctfltr_init_scroll(curr);

		if ( curr.find('.prdctfltr_filter.prdctfltr_attributes.prdctfltr_expand_parents').length > 0 ) {
			prdctfltr_all_cats(curr);
		}
		else {
			prdctfltr_show_opened_cats(curr);
		}

		if ( curr.hasClass('pf_mod_masonry') ) {
			curr.find('.prdctfltr_filter_inner').isotope({
				resizable: false,
				masonry: { }
			});
			if ( !curr.hasClass('prdctfltr_always_visible') ) {
				curr.find('.prdctfltr_woocommerce_ordering').hide();
			}
		}

		if ( curr.attr('class').indexOf('pf_sidebar_css') > 0 ) {
			if ( curr.hasClass('pf_sidebar_css_right') ) {
				$('body').css('right', '0px');
			}
			else {
				$('body').css('left', '0px');
			}
			if ( !$('body').hasClass('wc-prdctfltr-active-overlay') ) {
				$('body').addClass('wc-prdctfltr-active-overlay');
			}
		}

		if ( curr.hasClass('prdctfltr_step_filter') ) {
			curr.find('.prdctfltr_buttons').prepend('<a class="button prdctfltr_woocommerce_filter_submit pf_stopajax" href="#">'+prdctfltr.localization.getproducts+'</a>');
		}

		pf_preload_image(prdctfltr.url+'lib/images/svg-loaders/'+$(this).attr('data-loader')+'.svg');

	});

	function pf_preload_image(url) {
		var img = new Image();
		img.src = url;
	}

	$(document).on( 'change', 'input[name^="rng_"]', function() {
		var curr = $(this).closest('.prdctfltr_woocommerce');

		if ( curr.hasClass('prdctfltr_click_filter') ) {
			prdctfltr_respond_550(curr.find('.prdctfltr_woocommerce_ordering'));
		}
	});

	var stopAjax = false;
	$(document).on('click', '.prdctfltr_woocommerce_filter_submit', function() {

		if ( $(this).hasClass('pf_stopajax') ) {
			stopAjax = true;
		}

		var curr = $(this).closest('.prdctfltr_woocommerce_ordering');

		prdctfltr_respond_550(curr);

		return false;

	});

	$(document).on('click', '.prdctfltr_woocommerce_filter', function() {

		var curr_filter = $(this).closest('.prdctfltr_woocommerce');

		if (curr_filter.hasClass('pf_mod_masonry') && curr_filter.find('.prdctfltr_woocommerce_ordering:hidden').length > 0 ) {
			if (curr_filter.hasClass('prdctfltr_active')===false) {
				var curr_check = curr_filter.find('.prdctfltr_woocommerce_ordering');
				curr_check.show().find('.prdctfltr_filter_inner').isotope('layout');
				curr_check.hide();
			}
		}
		if ( !curr_filter.hasClass('prdctfltr_always_visible') ) {
			var curr = $(this).closest('.prdctfltr_woocommerce').find('.prdctfltr_woocommerce_ordering');

			if( $(this).hasClass('prdctfltr_active') ) {
				if ( curr_filter.attr('class').indexOf( 'pf_sidebar' ) == -1 ) {
					if ( curr_filter.hasClass( 'pf_fullscreen' ) ) {
						curr.stop(true,true).fadeOut(200, function() {
							curr.find('.prdctfltr_close_sidebar').remove();
						});
					}
					else {
						curr.stop(true,true).slideUp(200);
					}
				}
				else {
					curr.stop(true,true).fadeOut(200, function() {
						curr.find('.prdctfltr_close_sidebar').remove();
					});
					if ( curr_filter.attr('class').indexOf( 'pf_sidebar_css' ) > 0 ) {
						if ( curr_filter.hasClass('pf_sidebar_css_right') ) {
							$('body').css({'right':'0px','bottom':'auto','top':'auto','left':'auto'});
						}
						else {
							$('body').css({'right':'auto','bottom':'auto','top':'auto','left':'0px'});
						}
						$('.prdctfltr_overlay').remove();
					}
				}
				$(this).removeClass('prdctfltr_active');
				$('body').removeClass('wc-prdctfltr-active');
			}
			else {
				$(this).addClass('prdctfltr_active');
				if ( curr_filter.attr('class').indexOf( 'pf_sidebar' ) == -1 ) {
					$('body').addClass('wc-prdctfltr-active');
					if ( curr_filter.hasClass( 'pf_fullscreen' ) ) {
						curr.prepend('<div class="prdctfltr_close_sidebar"><i class="prdctfltr-delete"></i> '+prdctfltr.localization.close_filter+'</div>');
						curr.stop(true,true).fadeIn(200);

						var curr_height = $(window).height() - curr.find('.prdctfltr_filter_inner').outerHeight() - curr.find('.prdctfltr_close_sidebar').outerHeight() - curr.find('.prdctfltr_buttons').outerHeight();

						if ( curr_height > 128 ) {
							var curr_diff = curr_height/2;
							curr_height = curr.outerHeight();
							curr.css({'padding-top':curr_diff+'px'});
						}
						else {
							curr_height = $(window).height() - curr.find('.prdctfltr_close_sidebar').outerHeight() - curr.find('.prdctfltr_buttons').outerHeight() -128;
						}
						curr_filter.find('.prdctfltr_filter_wrapper').css({'max-height':curr_height});
					}
					else {
						curr.stop(true,true).slideDown(200);
					}
				}
				else {
					curr.prepend('<div class="prdctfltr_close_sidebar"><i class="prdctfltr-delete"></i> '+prdctfltr.localization.close_filter+'</div>');
					curr.stop(true,true).fadeIn(200);
					if ( curr_filter.attr('class').indexOf( 'pf_sidebar_css' ) > 0 ) {
						$('body').append('<div class="prdctfltr_overlay"></div>');
						if ( curr_filter.hasClass('pf_sidebar_css_right') ) {
							$('body').css({'right':'160px','bottom':'auto','top':'auto','left':'auto'});
							$('.prdctfltr_overlay').css({'right':'310px'}).delay(200).animate({'opacity':0.33},200,'linear');
						}
						else {
							$('body').css({'right':'auto','bottom':'auto','top':'auto','left':'160px'});
							$('.prdctfltr_overlay').css({'left':'310px'}).delay(200).animate({'opacity':0.33},200,'linear');
						}
					}
					$('body').addClass('wc-prdctfltr-active');
				}

			}
		}

		return false;
	});

	$(document).on('click', '.prdctfltr_overlay, .prdctfltr_close_sidebar', function() {

		if ( $(this).closest('.prdctfltr_woocommerce').length > 0 ) {
			$(this).closest('.prdctfltr_woocommerce').find('.prdctfltr_woocommerce_filter.prdctfltr_active').trigger('click');
		}
		else {
			$('.prdctfltr_woocommerce_filter.prdctfltr_active:first').trigger('click');
		}

	});

	$(document).on('click', '.pf_default_select .prdctfltr_widget_title, .prdctfltr_terms_customized_select .prdctfltr_widget_title', function() {

		var curr = $(this).closest('.prdctfltr_filter').find('.prdctfltr_add_scroll');

		if ( !curr.hasClass('prdctfltr_down') ) {
			$(this).find('.prdctfltr-down').attr('class', 'prdctfltr-up');
			curr.addClass('prdctfltr_down');
			curr.slideDown(100);
		}
		else {
			curr.slideUp(100);
			curr.removeClass('prdctfltr_down');
			$(this).find('.prdctfltr-up').attr('class', 'prdctfltr-down');
		}

	});

	var pf_select_opened = false;
	$(document).on('click', '.pf_select .prdctfltr_filter > span, .prdctfltr_terms_customized_select.prdctfltr_filter > span', function() {
		pf_select_opened = true;
		var curr = $(this).closest('.prdctfltr_filter').find('.prdctfltr_add_scroll');

		if ( !curr.hasClass('prdctfltr_down') ) {
			$(this).find('.prdctfltr-down').attr('class', 'prdctfltr-up');
			curr.addClass('prdctfltr_down');
			curr.slideDown(100, function() {
				pf_select_opened = false;
			});

			if ( !$('body').hasClass('wc-prdctfltr-select') ) {
				$('body').addClass('wc-prdctfltr-select');
			}
		}
		else {
			curr.slideUp(100, function() {
				pf_select_opened = false;

			});
			curr.removeClass('prdctfltr_down');
			$(this).find('.prdctfltr-up').attr('class', 'prdctfltr-down');
			if ( curr.closest('.prdctfltr_woocommerce').find('.prdctfltr_down').length == 0 ) {
				$('body').removeClass('wc-prdctfltr-select');
			}
		}

	});

	$(document).on( 'click', 'body.wc-prdctfltr-select', function(e) {

		var curr_target = $(e.target);

		if ( $('.prdctfltr_wc.pf_select .prdctfltr_down, .prdctfltr_terms_customized_select .prdctfltr_down').length > 0 && pf_select_opened === false && !curr_target.is('span, input, i') ) {
			$('.prdctfltr_wc.pf_select .prdctfltr_down, .prdctfltr_wc:not(.prdctfltr_wc_widget.pf_default_select) .prdctfltr_terms_customized_select .prdctfltr_down').each( function() {
				var curr = $(this);
				if ( curr.is(':visible') ) {
					curr.slideUp(100);
					curr.removeClass('prdctfltr_down');
					curr.closest('.prdctfltr_filter').find('span .prdctfltr-up').attr('class', 'prdctfltr-down');
				}
			});
			$('body').removeClass('wc-prdctfltr-select');
		}
	});

	$(document).on('click', 'span.prdctfltr_sale label, span.prdctfltr_instock label, span.prdctfltr_reset label', function() {

		var field = $(this).children('input:first');

		var curr_name = field.attr('name');
		var curr_filter = $(this).closest('.prdctfltr_wc');

		var ourObj = prdctfltr_get_obj_580(curr_filter);
		var pf_length = prdctfltr_count_obj_580(ourObj);

		$.each( ourObj, function(i, obj) {

			obj = $(obj);

			var curr_obj = obj.find('.prdctfltr_buttons input[name="'+curr_name+'"]');

			if ( !curr_obj.parent().hasClass('prdctfltr_active') ) {
				curr_obj.prop('checked', true).attr('checked', true).parent().addClass('prdctfltr_active');
			}
			else {
				curr_obj.prop('checked', false).attr('checked', false).parent().removeClass('prdctfltr_active');
			}

			if ( !--pf_length ) {
				prdctfltr_submit_form(curr_filter);
			}

		});

		//return false;

	});

	$(document).on('click', '.prdctfltr_byprice label', function() {

		var curr_chckbx = $(this).find('input[type="checkbox"]');
		var curr = curr_chckbx.closest('.prdctfltr_filter');
		var curr_var = curr_chckbx.val().split('-');
		var curr_filter = curr_chckbx.closest('.prdctfltr_wc');

		var ourObj = prdctfltr_get_obj_580(curr_filter);
		var pf_length = prdctfltr_count_obj_580(ourObj);

		if ( curr_var[0] == '' && curr_var[1] == '' || curr_chckbx.closest('label').hasClass('prdctfltr_active') ) {

			$.each( ourObj, function(i, obj) {
				var pfObj = $(obj).find('.prdctfltr_filter.prdctfltr_byprice');
				pfObj.find('.prdctfltr_active input[type="checkbox"]').prop('checked',false).attr('checked',false).closest('label').removeClass('prdctfltr_active');
				pfObj.find('input[name="min_price"]').val('');
				pfObj.find('input[name="max_price"]').val('');
				if ( !--pf_length ) {
					prdctfltr_submit_form(curr_filter);
				}
			});

		}
		else {

			$.each( ourObj, function(i, obj) {
				var pfObj = $(obj).find('.prdctfltr_filter.prdctfltr_byprice');
				pfObj.find('.prdctfltr_active input[type="checkbox"]').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');
				pfObj.find('input[name="min_price"]').val(curr_var[0]);
				pfObj.find('input[name="max_price"]').val(curr_var[1]);
				pfObj.find('input[value="'+curr_var[0]+'-'+curr_var[1]+'"][type="checkbox"]').prop('checked',true).attr('checked',true).change().closest('label').addClass('prdctfltr_active');
				if ( !--pf_length ) {
					prdctfltr_submit_form(curr_filter);
				}
			});

		}

		if ( curr_filter.hasClass('prdctfltr_tabbed_selection') && curr_filter.hasClass('prdctfltr_click') ) {
			curr_filter.find('.prdctfltr_filter').each( function() {
				if ( $(this).find('input[type="hidden"]:first').length > 0 && $(this).find('input[type="hidden"]:first').val() !== '' ) {
					if ( !$(this).hasClass('prdctfltr_has_selection') ) {
						$(this).addClass('prdctfltr_has_selection');
					}
					
				}
				else {
					if ( $(this).hasClass('prdctfltr_has_selection') ) {
						$(this).removeClass('prdctfltr_has_selection');
					}
				}
			});
		}

		if ( curr_chckbx.closest('.prdctfltr_wc').hasClass('pf_select') || curr.hasClass('prdctfltr_terms_customized_select') ) {
			if ( curr.hasClass('prdctfltr_terms_customized_select') && curr_chckbx.closest('.prdctfltr_wc').hasClass('prdctfltr_wc_widget') && curr_chckbx.closest('.prdctfltr_wc').hasClass('pf_default_select') ) {
				return false;
			}
			curr_chckbx.closest('.prdctfltr_filter').find('.prdctfltr_add_scroll').slideUp(250).removeClass('prdctfltr_down');
			curr_chckbx.closest('.prdctfltr_filter').find('.prdctfltr_regular_title i.prdctfltr-up').removeClass('prdctfltr-up').addClass('prdctfltr-down');

			if ( curr.find('.prdctfltr_title_selected').length > 0 ) {
				var add = '';
				if ( curr.find('label.prdctfltr_active .prdctfltr_customize_name').length > 0 ) {
					curr.find('label.prdctfltr_active .prdctfltr_customize_name').each( function() {
						add += add == '' ? $(this).text() : ', '+$(this).text();
					});
				}
				else {
					curr.find('label.prdctfltr_active span').each( function() {
						add += add == '' ? $(this).text() : ', '+$(this).text();
					});
				}

				curr.find('.prdctfltr_title_remove').replaceWith('<span class="prdctfltr_title_added"><i class="prdctfltr-check"></i></span>');
				curr.find('.prdctfltr_title_selected span.prdctfltr_selected_title').text(add);
			}
			else {
				var add = '';
				if ( curr.find('label.prdctfltr_active .prdctfltr_customize_name').length > 0 ) {
					curr.find('label.prdctfltr_active .prdctfltr_customize_name').each( function() {
						add += add == '' ? $(this).text() : ', '+$(this).text();
					});
				}
				else {
					curr.find('label.prdctfltr_active span').each( function() {
						add += add == '' ? $(this).text() : ', '+$(this).text();
					});
				}

				curr.find('.prdctfltr_regular_title, .prdctfltr_widget_title').prepend('<span class="prdctfltr_title_selected"><span class="prdctfltr_title_added"><i class="prdctfltr-check"></i></span> <span class="prdctfltr_selected_title">'+add+'</span><span class="prdctfltr_title_selected_separator"> / </span></span>');
			}
			if ( add == '' ) {
				curr.find('.prdctfltr_title_selected').remove();
			}

		}

		return false;

	});


	$(document).on('click', '.prdctfltr_filter:not(.prdctfltr_byprice) label', function(event) {

		if( $(event.target).is('input') ) {
			return false;
		}

		var curr_chckbx = $(this).find('input[type="checkbox"]');
		var curr = curr_chckbx.closest('.prdctfltr_filter');
		var curr_var = curr_chckbx.attr('value');
		var curr_filter = curr.closest('.prdctfltr_wc');

		if ( curr_filter.hasClass('pf_adptv_unclick') ) {
			if ( curr_chckbx.parent().hasClass( 'pf_adoptive_hide' ) ) {
				return false;
			}
		}

		prdctfltr_check_580(curr, curr_chckbx, curr_var, curr_filter);

		return false;

	});

	function prdctfltr_get_obj_580(curr_filter) {
		var ourObj = {};

		if ( curr_filter.closest('.prdctfltr_sc_products').length > 0 && $('.prdctfltr_wc_widget').length == 0 && $('.prdctfltr_sc_filter').length == 0 ) {
			ourObj[curr_filter.attr('data-id')] = curr_filter;
		}
		else {
			if ( curr_filter.hasClass('prdctfltr_step_filter') ) {
				ourObj[curr_filter.attr('data-id')] = curr_filter;
			}
			else {
				$('.prdctfltr_wc:not([data-id="'+curr_filter.attr('data-id')+'"])').each( function() {
					ourObj[$(this).attr('data-id')] = $(this);
				});
				ourObj[curr_filter.attr('data-id')] = $('.prdctfltr_wc[data-id="'+curr_filter.attr('data-id')+'"]');
			}
		}
		return ourObj;
	}

	function prdctfltr_count_obj_580(ourObj) {
		var pf_length = 0;
		var i;
		for (i in ourObj) {
			if (ourObj.hasOwnProperty(i)) {
				pf_length++;
			}
		}
		return pf_length;
	}

	function prdctfltr_check_parent_helper_590(termParent, pfObj) {
		if ( termParent ) {
			var found = pfObj.find('input[value="'+termParent+'"]');
			if ( found.length > 0 ) {
				pfObj.find('input[value="'+termParent+'"][type="checkbox"]').prop('checked',true).attr('checked',true).change().closest('label').addClass('prdctfltr_active');
			}
			else {
				//pfObj.find('label:first').insertBefore('<label style="display:none;"><input type="checkbox" value="'+termParent+'" checked /></label>');
			}
		}
	}

	function prdctfltr_check_580(curr, curr_chckbx, curr_var, curr_filter) {

		var ourObj = prdctfltr_get_obj_580(curr_filter);
		var pf_length = prdctfltr_count_obj_580(ourObj);

		var field = curr.children('input[type="hidden"]:first');

		var curr_name = field.attr('name');
		var curr_val = typeof field.attr('value') == 'undefined' ? '' : field.attr('value');
		
		if ( curr_filter.hasClass('prdctfltr_tabbed_selection') ) {
			if ( curr_val == curr_chckbx.val() ) {
				return false;
			}
		}

		if ( !curr.hasClass('prdctfltr_multi') ) {

			if ( curr_var == '' || curr_chckbx.closest('label').hasClass('prdctfltr_active') ) {

				var termParent = curr_chckbx.attr('data-parent');

				$.each( ourObj, function(i, obj) {
					var pfObj = $(obj).find('.prdctfltr_filter[data-filter="'+curr_name+'"]');
					pfObj.find('.prdctfltr_active input[type="checkbox"]').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');

					if ( termParent ) {
						prdctfltr_check_parent_helper_590(termParent, pfObj);
						pfObj.find('input[name="'+curr_name+'"]').val(termParent);
					}
					else {
						pfObj.find('input[name="'+curr_name+'"]').val('');
					}

					if ( !--pf_length ) {
						pfClearSure = curr_name;
						prdctfltr_submit_form(curr_filter);
					}
				});

			}
			else {

				$.each( ourObj, function(i, obj) {
					var pfObj = $(obj).find('.prdctfltr_filter[data-filter="'+curr_name+'"]');
					pfObj.find('.prdctfltr_active input[type="checkbox"]').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');
					pfObj.find('input[name="'+curr_name+'"]').val(curr_var);
					pfObj.find('input[value="'+curr_var+'"][type="checkbox"]').prop('checked',true).attr('checked',true).change().closest('label').addClass('prdctfltr_active');
					if ( !--pf_length ) {
						prdctfltr_submit_form(curr_filter);
					}
				});

			}

			if ( curr_chckbx.closest('.prdctfltr_wc').hasClass('pf_select') || curr.hasClass('prdctfltr_terms_customized_select') ) {
				if ( curr.hasClass('prdctfltr_terms_customized_select') && curr_chckbx.closest('.prdctfltr_wc').hasClass('prdctfltr_wc_widget') && curr_chckbx.closest('.prdctfltr_wc').hasClass('pf_default_select') ) {
					return false;
				}
				curr_chckbx.closest('.prdctfltr_filter').find('.prdctfltr_add_scroll').slideUp(250).removeClass('prdctfltr_down');
				curr_chckbx.closest('.prdctfltr_filter').find('.prdctfltr_regular_title i.prdctfltr-up').removeClass('prdctfltr-up').addClass('prdctfltr-down');
			}

		}
		else {

			if ( curr_chckbx.val() !== '' ) {

				if ( curr_chckbx.closest('label').hasClass('prdctfltr_active') ) {

					if ( curr.hasClass('prdctfltr_merge_terms') ) {
						var curr_settings = ( curr_val.indexOf('+') > 0 ? curr_val.replace('+' + curr_var, '').replace(curr_var + '+', '') : '' );

						$.each(prdctfltr.js_filters, function(n18,obj43){
							if ( obj43.adds[curr_name] != null ) {
								var check = prdctfltr.js_filters[n18].adds[curr_name];
								prdctfltr.js_filters[n18].adds[curr_name] = ( check.indexOf('+') > 0 ? check.replace('+' + curr_var, '').replace(curr_var + '+', '') : '' );
							}
						});
					}
					else {
						var curr_settings = ( curr_val.indexOf(',') > 0 ? curr_val.replace(',' + curr_var, '').replace(curr_var + ',', '') : '' );

						$.each(prdctfltr.js_filters, function(n18,obj43){
							if ( obj43.adds[curr_name] != null ) {
								var check = prdctfltr.js_filters[n18].adds[curr_name];
								prdctfltr.js_filters[n18].adds[curr_name] = ( check.indexOf(',') > 0 ? check.replace(',' + curr_var, '').replace(curr_var + ',', '') : '' );
							}
						});
					}

					var termParent = curr_chckbx.attr('data-parent');

					$.each( ourObj, function(i, obj) {
						var pfObj = $(obj).find('.prdctfltr_filter[data-filter="'+curr_name+'"]');
						pfObj.find('input[name="'+curr_name+'"]').val(curr_settings);
						pfObj.find('input[value="'+curr_var+'"][type="checkbox"]').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');

						if ( termParent ) {
							if ( curr_settings == '' ) {
								prdctfltr_check_parent_helper_590(termParent, pfObj);
								pfObj.find('input[name="'+curr_name+'"]').val(termParent);
							}
						}

						if ( !--pf_length ) {
							prdctfltr_submit_form(curr_filter);
						}

					});

				}
				else {

					$('.prdctfltr_filter[data-filter="'+curr_name+'"] .prdctfltr_sub[data-sub="'+curr_var+'"]').find('.prdctfltr_active input[type="checkbox"]').each( function() {

						var checkVal = $(this).attr('value');
						if ( curr.hasClass('prdctfltr_merge_terms') ) {
							if ( curr_val.indexOf('+') > 0 ) {
								curr_val = curr_val.replace('+' + checkVal, '').replace(checkVal + '+', '');
							}
							else {
								curr_val = curr_val.replace(checkVal, '');
							}
						}
						else {
							if ( curr_val.indexOf(',') > 0 ) {
								curr_val = curr_val.replace(',' + checkVal, '').replace(checkVal + ',', '');
							}
							else {
								curr_val = curr_val.replace(checkVal, '');
							}
						}
						$(this).prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');
					});

					if ( curr.hasClass('prdctfltr_merge_terms') ) {
						var curr_settings = ( curr_val == '' ? curr_var : curr_val + '+' + curr_var );
					}
					else {
						var curr_settings = ( curr_val == '' ? curr_var : curr_val + ',' + curr_var );
					}

					var termParent = curr_chckbx.attr('data-parent');

					$.each( ourObj, function(i, obj) {
						var pfObj = $(obj).find('.prdctfltr_filter[data-filter="'+curr_name+'"]');
						pfObj.find('input[name="'+curr_name+'"]').val(curr_settings);
						pfObj.find('input[value="'+curr_var+'"][type="checkbox"]').prop('checked',true).attr('checked',true).change().closest('label').addClass('prdctfltr_active');

						if ( termParent ) {
							if ( pfObj.find('input[value="'+termParent+'"][type="checkbox"]:checked').length > 0 ) {
								pfObj.find('input[value="'+termParent+'"][type="checkbox"]:checked').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');
								if ( curr_settings.indexOf(termParent) > -1 ) {
									if ( curr.hasClass('prdctfltr_merge_terms') ) {
										var makeNew = ( curr_settings.indexOf('+') > 0 ? curr_settings.replace('+' + termParent, '').replace(termParent + '+', '') : '' );
									}
									else {
										var makeNew = ( curr_settings.indexOf(',') > 0 ? curr_settings.replace(',' + termParent, '').replace(termParent + ',', '') : '' );
									}
									pfObj.find('input[name="'+curr_name+'"]').val(makeNew);
								}
							}
							else {
								var remTermParent = pfObj.find('input[value="'+termParent+'"][type="checkbox"]').attr('data-parent');
								if ( remTermParent ) {
									while ( remTermParent !== false ) {
										pfObj.find('input[value="'+remTermParent+'"][type="checkbox"]:checked').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');
										if ( curr_settings.indexOf(remTermParent) > -1 ) {
											if ( curr.hasClass('prdctfltr_merge_terms') ) {
												var makeNew = ( curr_settings.indexOf('+') > 0 ? curr_settings.replace('+' + remTermParent, '').replace(remTermParent + '+', '') : '' );
											}
											else {
												var makeNew = ( curr_settings.indexOf(',') > 0 ? curr_settings.replace(',' + remTermParent, '').replace(remTermParent + ',', '') : '' );
											}
											pfObj.find('input[name="'+curr_name+'"]').val(makeNew);
										}
										remTermParent = ( pfObj.find('input[value="'+remTermParent+'"][type="checkbox"]').attr('data-parent') ? pfObj.find('input[value="'+remTermParent+'"][type="checkbox"]').attr('data-parent') : false );
									}
								}
							}
						}

						if ( !--pf_length ) {
							prdctfltr_submit_form(curr_filter);
						}
					});

				}
			}
			else {

				$.each( ourObj, function(i, obj) {
					var pfObj = $(obj).find('.prdctfltr_filter[data-filter="'+curr_name+'"]');
					pfObj.find('input[name="'+curr_name+'"]').val('');
					pfObj.find('input[type="checkbox"]').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');
					if ( !--pf_length ) {
						prdctfltr_submit_form(curr_filter);
					}
				});

			}

		}

		if ( curr_filter.hasClass('prdctfltr_tabbed_selection') && curr_filter.hasClass('prdctfltr_click') ) {
			curr_filter.find('.prdctfltr_filter').each( function() {
				if ( $(this).find('input[type="hidden"]:first').length > 0 && $(this).find('input[type="hidden"]:first').val() !== '' ) {
					if ( !$(this).hasClass('prdctfltr_has_selection') ) {
						$(this).addClass('prdctfltr_has_selection');
					}
					
				}
				else {
					if ( $(this).hasClass('prdctfltr_has_selection') ) {
						$(this).removeClass('prdctfltr_has_selection');
					}
				}
			});
		}

		if ( curr_chckbx.closest('.prdctfltr_wc').hasClass('pf_select') || curr.hasClass('prdctfltr_terms_customized_select') ) {
			if ( curr.hasClass('prdctfltr_terms_customized_select') && curr_chckbx.closest('.prdctfltr_wc').hasClass('prdctfltr_wc_widget') && curr_chckbx.closest('.prdctfltr_wc').hasClass('pf_default_select') ) {
				return false;
			}
			if ( curr.find('.prdctfltr_title_selected').length > 0 ) {
				var add = '';
				if ( curr.find('label.prdctfltr_active .prdctfltr_customize_name').length > 0 ) {
					curr.find('label.prdctfltr_active .prdctfltr_customize_name').each( function() {
						add += add == '' ? $(this).text() : ', '+$(this).text();
					});
				}
				else {
					curr.find('label.prdctfltr_active span:first').each( function() {
						add += add == '' ? $(this).contents().filter(function(){return 3==this.nodeType;}).text() : ', '+$(this).contents().filter(function(){return 3==this.nodeType;}).text();
					});
				}
				curr.find('.prdctfltr_title_remove').replaceWith('<span class="prdctfltr_title_added"><i class="prdctfltr-check"></i></span>');
				curr.find('.prdctfltr_title_selected span.prdctfltr_selected_title').text(add);
			}
			else {
				var add = '';
				if ( curr.find('label.prdctfltr_active .prdctfltr_customize_name').length > 0 ) {
					curr.find('label.prdctfltr_active .prdctfltr_customize_name').each( function() {
						add += add == '' ? $(this).text() : ', '+$(this).text();
					});
				}
				else {
					curr.find('label.prdctfltr_active span:first').each( function() {
						add += add == '' ? $(this).contents().filter(function(){return 3==this.nodeType;}).text() : ', '+$(this).contents().filter(function(){return 3==this.nodeType;}).text();
					});
				}
				curr.find('.prdctfltr_regular_title, .prdctfltr_widget_title').prepend('<span class="prdctfltr_title_selected"><span class="prdctfltr_title_added"><i class="prdctfltr-check"></i></span> <span class="prdctfltr_selected_title">'+add+'</span><span class="prdctfltr_title_selected_separator"> / </span></span>');
			}
			if ( add == '' ) {
				curr.find('.prdctfltr_title_selected').remove();
			}
		}

		if ( $('.pf_added_input[name="'+curr_name+'"]').length > 0 ) {
			$('.pf_added_input[name="'+curr_name+'"]').remove();
		}

	}

	var pfClearSure = false;

	$(document).on('click', 'a.prdctfltr_title_remove', function() {

		var filter = $(this).attr('data-key');
		pfClearSure = filter;

		if ( filter == 's' || filter == 'search_products' ) {
			if ( $(this).closest('.prdctfltr_sc_products').length>0 ) {
				$(this).closest('.prdctfltr_sc_products').find('.prdctfltr_filter').find('input[name="s"],input[name="search_products"]').remove();
			}
			else {
				$('.prdctfltr_filter').find('input[name="s"]').remove();
			}
		}
		else if ( filter == 'byprice' ) {
			if ( $(this).closest('.prdctfltr_sc_products').length>0 ) {
				$(this).closest('.prdctfltr_sc_products').find('.prdctfltr_filter').find('input[name="min_price"],input[name="max_price"]').remove();
			}
			else {
				$('.prdctfltr_filter').find('input[name="min_price"],input[name="max_price"]').remove();
			}
		}
		else if ( filter == 'orderby' || filter == 'sale_products' || filter == 's' || filter == 'search_products' || filter == 'instock_products' ) {
			if ( $(this).closest('.prdctfltr_sc_products').length>0 ) {
				$(this).closest('.prdctfltr_sc_products').find('.prdctfltr_wc').find('input[name="'+filter+'"]').remove();
			}
			else {
				$('.prdctfltr_wc').find('input[name="'+filter+'"]').remove();
			}
		}
		else if ( filter == 'vendor' || filter == 'instock' || filter == 'products_per_page' ) {
			if ( $(this).closest('.prdctfltr_sc_products').length>0 ) {
				$(this).closest('.prdctfltr_sc_products').find('.prdctfltr_filter').find('input[name="'+filter+'"]').remove();
			}
			else {
				$('.prdctfltr_filter').find('input[name="'+filter+'"]').remove();
			}
		}
		else if ( filter.substr(0,4) !== 'rng_' ) {

			if ( $(this).closest('.prdctfltr_sc_products').length > 0 ) {
				var curr_els = $(this).closest('.prdctfltr_wc').find('input[name="'+filter+'"]');
			}
			else {
				var curr_els = $('.prdctfltr_filter').find('input[name="'+filter+'"]');
			}


			var selectedString = $(this).attr('data-slug');
			if ( selectedString.indexOf( '>' ) > 0 ) {
				var termParent = selectedString.substr(0, selectedString.indexOf( '>' ));
				selectedString = selectedString.substr(selectedString.indexOf( '>' )+1);
			}

			var cur_vals = [];
			if ( selectedString.indexOf( ',') > 0 ) {
				cur_vals = selectedString.split(',');
			}
			else {
				cur_vals[0] = selectedString;
			}

			var cv_lenght = cur_vals.length;

			$.each(cur_vals, function(i, val23) {

				var curr_value = val23;

				curr_els.each( function() {

					var curr_chckd = $(this);
					var curr_chckdval = $(this).val();

					if ( curr_chckdval.indexOf( ',' ) > 0 ) {
						curr_chckd.val(curr_chckdval.replace(',' + curr_value, '').replace(curr_value + ',', ''));
					}
					else if ( curr_chckdval.indexOf( '+' ) > 0 ) {
						curr_chckd.val(curr_chckdval.replace('+' + curr_value, '').replace(curr_value + '+', ''));
					}
					else {
						curr_chckd.val(curr_chckdval.replace(curr_value, '').replace(curr_value, ''));
					}

				});

				if ( !--cv_lenght ) {

					curr_els.each( function() {

						var curr_chckd = $(this);

						if ( termParent ) {
							curr_chckd.val(termParent);
							if ( curr_chckd.val() == '' ) {
								curr_chckd.val(termParent);
							}
							
						}

					});

				}

			});

		}
		else {
			if ( $(this).closest('.prdctfltr_sc_products').length>0 ) {
				if ( filter == 'rng_price' ) {
					$(this).closest('.prdctfltr_sc_products').find('.prdctfltr_range.prdctfltr_price input[type="hidden"]').each(function() {
						$(this).remove();
					});
				}
				else {
					$(this).closest('.prdctfltr_sc_products').find('.prdctfltr_range input[type="hidden"][name$="'+filter.substr(4, filter.length)+'"]').each(function() {
						$(this).remove();
					});
				}

			}
			else {
				if ( filter == 'rng_price' ) {
					$('.prdctfltr_wc').find('.prdctfltr_range.prdctfltr_price input[type="hidden"]').each(function() {
						$(this).remove();
					});
				}
				else {
					$('.prdctfltr_wc').find('.prdctfltr_range input[type="hidden"][name$="'+filter.substr(4, filter.length)+'"]').each(function() {
						$(this).remove();
					});
				}
			}
		}


		prdctfltr_respond_550($(this).closest('.prdctfltr_wc').find('form.prdctfltr_woocommerce_ordering'));

		pfClearSure = false;

		return false;

	});

	$(document).on('click', '.prdctfltr_checkboxes label > i', function() {

		var curr = $(this).parent().next();

		$(this).parent().toggleClass('prdctfltr_clicked');

		if ( curr.hasClass('prdctfltr_sub') ) {
			curr.slideToggle(100, function() {
				if ( curr.closest('.prdctfltr_woocommerce').hasClass('pf_mod_masonry') ) {
					curr.closest('.prdctfltr_woocommerce').find('.prdctfltr_filter_inner').isotope('layout');
				}
			});

		}

		return false;

	});

	function prdctfltr_get_loader(curr) {
		var curr_loader = curr.closest('.prdctfltr_wc').attr('data-loader');
		if ( curr.closest('.prdctfltr_wc').find('.prdctfltr_woocommerce_filter i').length > 0 && curr.closest('.prdctfltr_wc').find('.prdctfltr_woocommerce_filter img').length == 0 ) {
			curr.closest('.prdctfltr_wc').find('.prdctfltr_woocommerce_filter').addClass('pf_ajax_loading');
			curr.closest('.prdctfltr_wc').find('.prdctfltr_woocommerce_filter i').replaceWith('<img src="'+prdctfltr.url+'lib/images/svg-loaders/'+curr_loader+'.svg" class="prdctfltr_reset_this prdctfltr_loader" />');
		}
		else {
			curr.closest('.prdctfltr_wc').prepend('<div class="prdctfltr_added_loader"><img src="'+prdctfltr.url+'lib/images/svg-loaders/'+curr_loader+'.svg" class="prdctfltr_reset_this prdctfltr_loader" /></div>');
		}
		return false;
	}

	function prdctfltr_reset_filters_550(obj) {

		obj.find('input[type="hidden"]:not([name="post_type"])').each( function() {
			if ( prdctfltr.clearall[0] != null) {
				if ( $.inArray( this.name, prdctfltr.clearall ) > -1 ) {
					if ( !$(this).val() ) {
						if ( $(this).attr('data-parent') ) {
							$(this).val($(this).attr('data-parent'));
						}
						else {
							$(this).remove();
						}
					}
				}
				else {
					if ( $(this).attr('data-parent') ) {
						$(this).val($(this).attr('data-parent'));
					}
					else {
						$(this).remove();
					}
				}
			}
			else {
				if ( $(this).attr('data-parent') ) {
					$(this).val($(this).attr('data-parent'));
				}
				else {
					$(this).remove();
				}
			}
		});

		obj.find('.prdctfltr_filter input.pf_search').val('').prop('disabled',true).attr('disabled','true');

		if ( $('.prdctfltr_wc .prdctfltr_buttons input[name="sale_products"]').length>0 ) {
			$('.prdctfltr_wc .prdctfltr_buttons input[name="sale_products"]').remove();
		}
		if ( $('.prdctfltr_wc .prdctfltr_buttons input[name="instock_products"]').length>0 ) {
			$('.prdctfltr_wc .prdctfltr_buttons input[name="instock_products"]').remove();
		}
		if ( $('.prdctfltr_wc .prdctfltr_add_inputs input[name="orderby"]').length>0 ) {
			$('.prdctfltr_wc .prdctfltr_add_inputs input[name="orderby"]').remove();
		}

		obj.find('input[name="reset_filter"]').remove();
	}

	function prdctfltr_remove_empty_inputs_550(obj) {

		obj.find('.prdctfltr_filter input[type="hidden"], .prdctfltr_filter input.pf_search, .prdctfltr_add_inputs input[type="hidden"]').each(function() { //, .prdctfltr_add_inputs input[type="hidden"]:not([name="post_type"])

			var curr_val = $(this).val();

			if ( curr_val == '' ) {
				if ( $(this).is(':visible') ) {
					$(this).prop('disabled',true).attr('disabled','true');
				}
				else {
					$(this).remove();
				}
			}

		});

	}

	function prdctfltr_remove_ranges_550(obj) {
		obj.find('.prdctfltr_filter.prdctfltr_range').each( function() {
			var curr_rng = $(this);
			if ( curr_rng.find('[name^="rng_min_"]').val() == undefined || curr_rng.find('[name^="rng_max_"]').val() == undefined ) {
				curr_rng.find('input').remove();
			}
		});
	}

	function prdctfltr_check_display_550(obj) {

		if ( $('body').hasClass('wc-prdctfltr-active') ) {

			if ( obj.attr('class').indexOf( 'pf_sidebar' ) == -1 ) {
				if ( obj.hasClass( 'pf_fullscreen' ) ) {
					obj.find('form').stop(true,true).fadeOut(200, function() {
						obj.find('.prdctfltr_close_sidebar').remove();
					});
				}
				else {
					if ( !obj.hasClass('prdctfltr_wc_widget') ) {
						obj.find('form').stop(true,true).slideUp(200);
					}
				}
			}
			else {
				obj.find('form').fadeOut(200);

				if ( obj.attr('class').indexOf( 'pf_sidebar_css' ) > 0 ) {
					if ( obj.hasClass('pf_sidebar_css_right') ) {
						$('body').css({'right':'0px','bottom':'auto','top':'auto','left':'auto'});
					}
					else {
						$('body').css({'right':'auto','bottom':'auto','top':'auto','left':'0px'});
					}
					$('.prdctfltr_overlay').remove();
				}
				obj.find('form').removeClass('prdctfltr_active');
				$('body').removeClass('wc-prdctfltr-active');

			}

		}

	}

	function prdctfltr_get_fields_550(obj) {

		var curr_fields = {};
		var lookAt = ( pf_restrict == 'pagination' ? '.prdctfltr_filter input[type="hidden"], .prdctfltr_filter input.pf_search, .prdctfltr_add_inputs input[name="orderby"], .prdctfltr_add_inputs input[name="s"], .prdctfltr_add_inputs input.pf_added_input' : '.prdctfltr_filter input[type="hidden"], .prdctfltr_filter input.pf_search, .prdctfltr_add_inputs input[name="orderby"], .prdctfltr_add_inputs input[name="s"]' );

		obj.find(lookAt).each( function() {
			if ( $(this).attr('value') !== '' ) {
				curr_fields[$(this).attr('name')] = $(this).attr('value');
			}
		});

		if ( obj.find('.prdctfltr_buttons input[name="sale_products"]:checked').length > 0 ) {
			curr_fields.sale_products = 'on';
		}
		if ( obj.find('.prdctfltr_buttons input[name="instock_products"]:checked').length > 0 ) {
			curr_fields.instock_products = 'in';
		}

		if ( $.isEmptyObject( curr_fields ) === false ) {
			if ( prdctfltr.analytics == 'yes' ) {

				var analyticsData = {
					action: 'prdctfltr_analytics',
					pf_filters: curr_fields,
					pf_nonce: obj.attr('data-nonce')
				};

				$.post(prdctfltr.ajax, analyticsData, function(response) {

				});

			}
		}

		return curr_fields;

	}

	function after_ajax(curr_next) {

		if ( curr_next.find('.prdctfltr_filter.prdctfltr_attributes.prdctfltr_expand_parents').length > 0 ) {
			prdctfltr_all_cats(curr_next);
		}
		else {
			prdctfltr_show_opened_cats(curr_next);
		}
		$('.prdctfltr_subonly').each( function() {
			prdctfltr_show_sub_cats($(this).closest('.prdctfltr_wc:not(.prdctfltr_tabbed_selection)'));
		});
		prdctfltr_init_scroll(curr_next);
		prdctfltr_init_tooltips(curr_next);
		reorder_selected(curr_next);
		reorder_adoptive(curr_next);
		set_select_index(curr_next);
		init_search(curr_next);
		init_ranges(curr_next);
		prdctfltr_make_clears(curr_next);
		do_zindexes(curr_next);
		reorder_limit(curr_next);
		prdctfltr_tabbed_selection(curr_next);
		//ajax_failsafe();

		if ( curr_next !== undefined ) {
			if ( curr_next.hasClass('pf_mod_masonry') ) {

				curr_next.find('.prdctfltr_woocommerce_ordering').show();
				curr_next.find('.prdctfltr_filter_inner').isotope({
					resizable: false,
					masonry: { }
				});
				if ( !curr_next.hasClass('prdctfltr_always_visible') ) {
					curr_next.find('.prdctfltr_woocommerce_ordering').hide();
				}
			}
			if ( curr_next.hasClass('prdctfltr_step_filter') ) {
				curr_next.find('.prdctfltr_buttons').prepend('<a class="button prdctfltr_woocommerce_filter_submit pf_stopajax" href="#">'+prdctfltr.localization.getproducts+'</a>');
			}

		}

		if ( $(prdctfltr.ajax_orderby_class).length<1 ) {
			$('.prdctfltr_add_inputs input[name="orderby"]').remove();
		}

		prdctfltr_filter_terms_init(curr_next);
		prdctfltr_show_opened_widgets(curr_next);
		if ( curr_next !== undefined && curr_next.hasClass('pf_mod_masonry') ) {
			curr_next.find('.prdctfltr_filter_inner').isotope('layout');
		}
	}

	var pf_paged = 1;
	var pf_offset = 0;
	var pf_restrict = '';

	$(document).on('click', '.prdctfltr_sc_products.prdctfltr_ajax '+prdctfltr.ajax_pagination_class+' a, body.prdctfltr-ajax.prdctfltr-shop '+prdctfltr.ajax_pagination_class+' a, .prdctfltr-pagination-default a, .prdctfltr-pagination-load-more a', function() {

		if (ajaxActive===true) {
			return false;
		}

		ajaxActive = true;

		var loadMore = ( $(this).closest('.prdctfltr-pagination-load-more').length > 0 ? true : false );
		var curr_link = $(this);

		var shortcodeAjax = false;
		var checkShortcode = curr_link.closest('.prdctfltr_sc_products');
		if ( archiveAjax===false && checkShortcode.length > 0 && checkShortcode.hasClass('prdctfltr_ajax') ) {
			shortcodeAjax = true;
			var obj = checkShortcode.find('form');
		}
		else {
			var obj = $('.prdctfltr_wc:first form');
		}

		var curr_href = curr_link.attr('href');

		if ( loadMore === true ) {
			if ( shortcodeAjax===false ) {
				pf_offset = parseInt( $(prdctfltr.ajax_class).find(prdctfltr.ajax_product_class).length, 10 );
			}
			else {
				pf_offset = parseInt( checkShortcode.find(prdctfltr.ajax_product_class).length, 10 );
			}
		}
		else {
			if ( curr_href.indexOf('paged=') >= 0 ) {
				pf_paged = parseInt( curr_href.getValueByKey('paged'), 10 );
			}
			else {
				var arrUrl = curr_href.split('/'+prdctfltr.page_rewrite+'/');
				if ( typeof arrUrl[1] !== 'undefined' ) {
					pf_paged = arrUrl[1].substring( 0, 1 );
				}
			}
		}

		pf_restrict = 'pagination';

		ajaxActive = false;
		prdctfltr_respond_550(obj);

		return false;

	});

	function prdctfltr_respond_550(curr) {

		if (ajaxActive===true) {
			return false;
		}
		ajaxActive = true;

		//prdctfltr_get_loader(curr);

		if ( archiveAjax === true ) {
			$(prdctfltr.ajax_class).fadeTo(200,0.5).addClass('prdctfltr_faded');
		}

		var shortcodeAjax = false;

		if ( archiveAjax === false ) {

			var checkShortcode = $('.prdctfltr_sc_products.prdctfltr_ajax');
			var checkWidget = $('.prdctfltr_wc_widget');

			if ( checkShortcode.length > 0 ) {

				if ( checkWidget.length > 0 ) {
					checkShortcode = $('.prdctfltr_sc_products.prdctfltr_ajax:first');
					shortcodeAjax = true;
					var multiAjax = true;
				}
				else {
					checkShortcode = curr.closest('.prdctfltr_sc_products.prdctfltr_ajax');
					shortcodeAjax = true;
					if ( stopAjax === true ) {
						shortcodeAjax = false;
						stopAjax = false;
					}
				}
				checkShortcode.find(prdctfltr.ajax_class).fadeTo(200,0.5).addClass('prdctfltr_faded');
			}
		}

		var curr_filter = curr.closest('.prdctfltr_wc');

		var ourObj = prdctfltr_get_obj_580(curr_filter);
		var pf_length = prdctfltr_count_obj_580(ourObj);

		var curr_fields = {};
		var requested_filters = {};


		$.each( ourObj, function(i, obj) {

			obj=$(obj);

			prdctfltr_get_loader(obj);

			var pf_id = obj.attr('data-id');

			var wasReset = false;
			if ( obj.find('input[name="reset_filter"]:checked').length > 0 ) {
				wasReset = true;
				prdctfltr_reset_filters_550(obj);
			}
			else {
				prdctfltr_remove_empty_inputs_550(obj);
			}

			prdctfltr_remove_ranges_550(obj);

			prdctfltr_check_display_550(obj);

			requested_filters[pf_id] = pf_id;

			curr_fields[pf_id] = prdctfltr_get_fields_550(obj);

			if ( !--pf_length ) {

				if (archiveAjax===true||shortcodeAjax===true) {

					var pf_set = 'archive';
					if ( archiveAjax===true && !$('body').hasClass('prdctfltr-shop') ) {
						pf_set = 'shortcode';
					}
					else {
						pf_set = ( archiveAjax === true ? 'archive' : 'shortcode' );
					}

					var data = {
						action: 'prdctfltr_respond_550',
						pf_url: location.protocol + '//' + location.host + location.pathname,
						pf_request: prdctfltr.js_filters,
						pf_requested: requested_filters,

						pf_query: prdctfltr.js_filters[pf_id].args,
						pf_shortcode: prdctfltr.js_filters[pf_id].atts,
						pf_atts: prdctfltr.js_filters[pf_id].atts_sc,
						pf_adds: prdctfltr.js_filters[pf_id].adds,
						pf_ajax_query_vars: prdctfltr.ajax_query_vars,

						pf_filters: curr_fields,
						pf_mode: 'archive',
						pf_set: pf_set,
						pf_id: pf_id,
						pf_paged: pf_paged,
						pf_pagefilters: prdctfltr.pagefilters,
						pf_restrict: pf_restrict
					};

					if ( wasReset===true ) {

						/*if ( prdctfltr.clearall[0] != null) {
							$.each( data.pf_filters, function(n2,obj10) {
								$.each(obj10, function(n5,obj23){
									if ( $.inArray( obj23, prdctfltr.clearall ) > -1 ) {
										
									}
									else {
										delete data.pf_filters[n2][obj23];

										if ( data.pf_adds[n2] != null ) {
											delete data.pf_adds[n2];
											$.each(prdctfltr.js_filters, function(n18,obj43){
												if ( obj43['adds'][pfClearSure] != null ) {
													delete prdctfltr.js_filters[n18]['adds'][pfClearSure];
												}
											});
										}
									}
								});

							});
						}
						else {
							data.pf_filters = [];
							data.pf_adds = [];
							$.each(prdctfltr.js_filters, function(n19,obj47){
								if ( obj47['adds'][pfClearSure] != null ) {
									prdctfltr.js_filters[n19]['adds'] = [];
								}
							});
						}*/

					}

					if ( pfClearSure!==false ) {
						if ( data.pf_adds[pfClearSure] != null ) {
							delete data.pf_adds[pfClearSure];
							$.each(prdctfltr.js_filters, function(n18,obj43){
								if ( obj43.adds[pfClearSure] != null ) {
									delete prdctfltr.js_filters[n18].adds[pfClearSure];
								}
							});
						}
					}

					if ( $('.prdctfltr_wc_widget').length > 0 ) {

						var widget = $('.prdctfltr_wc_widget:first');

						var rpl = $('<div></div>').append(widget.find('.prdctfltr_filter:first').children(':not(input):first').clone()).html().toString().replace(/\t/g, '');
						var rpl_off = $('<div></div>').append(widget.find('.prdctfltr_filter:first').children(':not(input):first').find('.prdctfltr_widget_title').clone()).html().toString().replace(/\t/g, '');
						
						rpl = rpl.replace(rpl_off, '%%%');

						data.pf_widget_title = $.trim(rpl);

					}

					if ( obj.attr('data-lang') !== undefined ) {
						data.lang = obj.attr('data-lang');
					}

					if ( pf_offset>0 ) {
						data.pf_offset = pf_offset;
					}

					if ( $(prdctfltr.ajax_orderby_class).length>0 ) {
						data.pf_orderby_template = 'set';
					}

					if ( $(prdctfltr.ajax_count_class).length>0 ) {
						data.pf_count_template = 'set';
					}

					if ( pf_singlesc==1 ) {
						data.pf_singlesc = 1;
					}

					$.post(prdctfltr.ajax, data, function(response) {
						if (response) {
							var getElement = shortcodeAjax === true ? checkShortcode : false;
							prdctfltr_handle_response_580(response, archiveAjax, shortcodeAjax, getElement);
						}
					});

				}
				else {

					obj.find('input[type="hidden"], input[name="s"]').each(function () {
						obj.find('input[name="'+this.name+'"]:gt(0)').remove();
					});

					var cf_length = $.pfcount(curr_fields);

					if ( cf_length > 1 ) {
						$.each( curr_fields, function(e1,w1) {
							$.each( w1, function( k02, s02 ) {
								if ( k02 != 's' && obj.find('input[name="'+k02+'"]').length == 0 ) {
									
									obj.find('.prdctfltr_add_inputs').append('<input type="hidden" name="'+k02+'" value="'+s02+'" class="pf_added_input" />');
								}
								else if ( k02 != 's' && obj.find('input[name="'+k02+'"]').length > 0 ) {
									obj.find('input[type="hidden"][name="'+k02+'"]').val(s02);
								}
								if ( k02 == 's' && obj.find('input[name="s"]').length == 0 ) {
									obj.find('.prdctfltr_add_inputs').append('<input type="hidden" name="s" value="'+s02+'" class="pf_added_input" />');
								}
							});
						});
					}

					if ( $('.prdctfltr_wc input[name="orderby"][value="'+prdctfltr.orderby+'"]').length > 0 ) {
						$('.prdctfltr_wc input[name="orderby"][value="'+prdctfltr.orderby+'"]').remove();
					}

					obj.find('.prdctfltr_woocommerce_ordering').submit();

				}

			}

		});

	}

	function prdctfltr_handle_response_580(response, archiveAjax, shortcodeAjax, getElement) {

		var ajax_length = prdctfltr_count_obj_580(response);
		var ajaxRefresh = {};
		var query = '';

		$.each(response, function(n,obj2) {


			if ( n == 'products' ) {
				obj2 = ( $(obj2).find(prdctfltr.ajax_class).length > 0 ? $(obj2).find(prdctfltr.ajax_class) : $(obj2) );

				if (archiveAjax===true) {
					var products =$(prdctfltr.ajax_class);
				}
				else if ( shortcodeAjax===true ) {
					var products = getElement.find(prdctfltr.ajax_class);
				}
				else {
					var products =$(prdctfltr.ajax_class);
				}

				if ( obj2.length<1 ) {
					products.empty();
				}
				else {
					if (pf_offset<1) {
						if ( obj2.find(prdctfltr.ajax_product_class).length > 0 ) {
							if ( pf_restrict == 'pagination' ) {
								pf_get_scroll(products, 0);
							}
							pf_animate_products( products, obj2, 'replace' );
						}
						else {
							products.replaceWith(obj2);
						}
					}
					else {
						if ( obj2.find(prdctfltr.ajax_product_class).length > 0 ) {
							pf_animate_products( products, obj2, 'append' );
							$('.prdctfltr_faded').fadeTo(200,1).removeClass('prdctfltr_faded');
							if ( pf_restrict == 'pagination' ) {
								pf_get_scroll(products, pf_offset);
							}
						}
						else {
							$('.prdctfltr_faded').fadeTo(200,1).removeClass('prdctfltr_faded');
						}
					}
				}
			}
			else if ( n == 'pagination' ) {

				obj2 = $(obj2);

				if (archiveAjax===true&&$('body').hasClass('prdctfltr-shop')) {
					var pagination = ( prdctfltr.ajax_pagination_type=='default' ? $(prdctfltr.ajax_pagination_class) : $('.'+prdctfltr.ajax_pagination_type) );
				}
				else if ( shortcodeAjax===true ) {
					var pagination = getElement.find(prdctfltr.ajax_pagination_class);
					if ( pagination.length < 1 ) {
						var pagination = getElement.find('.prdctfltr-pagination-default');
					}
					if ( pagination.length < 1 ) {
						var pagination = getElement.find('.prdctfltr-pagination-load-more');
					}
				}
				else if ( shortcodeAjax===false ) {
					var pagination = $(prdctfltr.ajax_pagination_class);
					if ( pagination.length < 1 ) {
						var pagination = $('.prdctfltr-pagination-default');
					}
					if ( pagination.length < 1 ) {
						var pagination = $('.prdctfltr-pagination-load-more');
					}
				}

				if ( obj2 !== '' ) {
					if ( pagination.length < 1 ) {
						if ( $('.pf_pagination_dummy').length == 0 )  {
							$(prdctfltr.ajax_class).after('<div class="pf_pagination_dummy"></div>');
						}

						pagination = $('.pf_pagination_dummy');
					}
				}

				if ( obj2.length<1 ) {
					pagination.empty();
				}
				else {
					pagination.replaceWith(obj2);
				}

			}
			else if ( n == 'ranges' ) {
				obj2 = $(obj2);
				prdctfltr.rangefilters = obj2[0];
			}
			else if ( n == 'orderby' ) {
				obj2 = $(obj2);
				$(prdctfltr.ajax_orderby_class).replaceWith(obj2);
			}
			else if ( n == 'count' ) {
				obj2 = $(obj2);
				if ( obj2.length<1 ) {
					$(prdctfltr.ajax_count_class).html(prdctfltr.localization.noproducts);
				}
				else {
					$(prdctfltr.ajax_count_class).replaceWith(obj2);
				}
			}
			else if ( n == 'query' ) {
				if ( prdctfltr.permalinks !== 'yes' ) {
					query = ( obj2 == '' ? location.protocol + '//' + location.host + location.pathname : obj2 );
				}
				else {
					query = location.protocol + '//' + location.host + location.pathname;
				}
			}
			else if ( n.substring(0, 9) == 'prdctfltr' ){
				obj2 = $(obj2);

				if ( obj2.hasClass('prdctfltr_wc') ) {
					if ( pf_offset>0&&$(response.products).find(prdctfltr.ajax_product_class).length>0 || pf_offset==0 ) {
						if ( $('.prdctfltr_wc[data-id="'+n+'"]').length > 0 ) {
							$('.prdctfltr_wc[data-id="'+n+'"]').replaceWith(obj2);
							ajaxRefresh[n] = n;
						}
					}
					else {
						$('.prdctfltr_wc[data-id="'+n+'"]').find('.prdctfltr_woocommerce_filter').replaceWith(obj2.find('.prdctfltr_woocommerce_filter'));
					}
				}
				else if ( obj2.hasClass('prdctfltr-widget') ) {
					if ( $('.prdctfltr_wc[data-id="'+n+'"]').length > 0 ) {
						$('.prdctfltr_wc[data-id="'+n+'"]').closest('.prdctfltr-widget').replaceWith(obj2);
						ajaxRefresh[n] = n;
					}
				}

			}

			if ( !--ajax_length ) {

				if ( !$.isEmptyObject( ajaxRefresh ) ) {
					$.each(ajaxRefresh, function(m,obj4) {
						after_ajax($('.prdctfltr_wc[data-id="'+m+'"]'));
					});
				}

				$(document.body).trigger( 'post-load' );
				if ( prdctfltr.js !== '' ) {
					eval(prdctfltr.js);
				}

				if ( archiveAjax === true && pf_offset == 0 ) {
					History.pushState({response:response, archiveAjax:archiveAjax, shortcodeAjax:shortcodeAjax}, document.title, query);
				}

				initChange = true;

				ajaxActive = false;
				pf_paged = 1;
				pf_offset = 0;
				pf_restrict = '';

			}

		});
	}

	if ( archiveAjax === true ) {
		var initChange = true;

		History.Adapter.bind(window,'statechange',function(){

			if ( initChange === false ) {
				var state = History.getState();
				if ( typeof state.data.response !== 'undefined' ) {
					prdctfltr_handle_response_580(state.data.response, state.data.archiveAjax, state.data.shortcodeAjax, false);
				}
				else if ( typeof state.data.filters !== 'undefined' ) {
					prdctfltr_handle_response_580(state.data.filters, ( $('body').hasClass('prdctfltr-ajax') ? true : false ), false, false);
				}
			}
			initChange = false;
		});
	}


	$(window).load( function() {
		$('.pf_mod_masonry .prdctfltr_filter_inner').each( function() {
			$(this).isotope('layout');
		});
	});

	if ( $('.prdctfltr-widget').length == 0 || $('.prdctfltr-widget .prdctfltr_error').length == 1 ) {

		$(window).on('resize', function() {

			$('.prdctfltr_woocommerce').each( function() {

				var curr = $(this);
		
				if ( curr.hasClass('pf_mod_row') ) {

					if ( window.matchMedia('(max-width: 768px)').matches ) {
						curr.find('.prdctfltr_filter_inner').css('width', 'auto');
					}
					else {
						var curr_columns = curr.find('.prdctfltr_filter_wrapper:first').attr('data-columns');

						var curr_scroll_column = curr.find('.prdctfltr_woocommerce_ordering').width();
						var curr_columns_length = curr.find('.prdctfltr_filter').length;

						curr.find('.prdctfltr_filter_inner').css('width', curr_columns_length*curr_scroll_column/curr_columns);
						curr.find('.prdctfltr_filter').css('width', curr_scroll_column/curr_columns);
					}
				}
			});
		});
	}

	if ((/Trident\/7\./).test(navigator.userAgent)) {
		$(document).on('click', '.prdctfltr_checkboxes label img', function() {
			$(this).parents('label').children('input:first').change().click();
		});
	}

	if ((/Trident\/4\./).test(navigator.userAgent)) {
		$(document).on('click', '.prdctfltr_checkboxes label > span > img, .prdctfltr_checkboxes label > span', function() {
			$(this).parents('label').children('input:first').change().click();
		});
	}

	function prdctfltr_filter_results(currThis,list,searchIn,curr_filter) {
		var filter = currThis.val();

		if(filter) {
			var curr = currThis.closest('.prdctfltr_filter');
			if ( curr.find('div.prdctfltr_sub').length > 0 ) {
				$(list).find(".prdctfltr_sub:not(:visible)").css({'margin-left':0}).show().prev().addClass('prdctfltr_clicked');
				if ( curr.hasClass('prdctfltr_searching') === false ) {
					curr.addClass('prdctfltr_searching');
				}
			}
			$(list).find(searchIn+" > span:not(:Contains(" + filter + "))").closest('label').attr('style', 'display:none !important');
			$(list).find(searchIn+" > span:Contains(" + filter + ")").closest('label').show();
			curr.find('.pf_more').hide();
		}
		else {
			var curr = currThis.closest('.prdctfltr_filter');
			if ( curr.find('div.prdctfltr_sub').length > 0 ) {
				$(list).find(".prdctfltr_sub:visible").css({'margin-left':'22px'}).hide().prev().removeClass('prdctfltr_clicked');
			}
			curr.removeClass('prdctfltr_searching');
			$(list).find(searchIn+" > span").closest('label').show();

			var checkboxes = curr.find('.prdctfltr_checkboxes');

			checkboxes.each(function(){
				var max = parseInt(curr.attr('data-limit'));
				if (max != 0 && currThis.find(searchIn).length > max+1) {

					currThis.find(searchIn+':gt('+max+')').attr('style', 'display:none !important');
					currThis.find(".pf_more").html('<span>'+prdctfltr.localization.show_more+'</span>').removeClass('pf_activated');
				}
			});
			curr.find('.pf_more').show();
		}

		if ( curr_filter.hasClass('pf_mod_masonry') ) {
			curr_filter.find('.prdctfltr_filter_inner').isotope('layout');
		}

		return false;
	}

	function prdctfltr_filter_terms(list) {

		var curr_filter = list.closest('.prdctfltr_wc');
		var form = $("<div>").attr({"class":"prdctfltr_search_terms","action":"#"}),
		input = $("<input>").attr({"class":"prdctfltr_search_terms_input prdctfltr_reset_this","type":"text","placeholder":prdctfltr.localization.filter_terms});
		

		if ( curr_filter.hasClass('pf_select') || curr_filter.hasClass('pf_default_select') || list.closest('.prdctfltr_filter').hasClass('prdctfltr_terms_customized_select') ) {
			$(form).append("<i class='prdctfltr-search'></i>").append(input).prependTo(list);
		}
		else{
			$(form).append("<i class='prdctfltr-search'></i>").append(input).insertBefore(list);
		}

		if ( curr_filter.hasClass('pf_adptv_default') ) {
			var searchIn = 'label:not(.pf_adoptive_hide)';
		}
		else {
			var searchIn = 'label';
		}

		var timeoutId = 0;

		$(input)
		.change( function () {

			var filter = $(this);

			clearTimeout(timeoutId);
			timeoutId = setTimeout(function() {prdctfltr_filter_results(filter,list,searchIn,curr_filter);}, 500);

		})
		.keyup( function () {
			$(this).change();
		});

	}

	$(document).on('click', '.prdctfltr_sc_products.prdctfltr_ajax '+prdctfltr.ajax_class+' '+prdctfltr.ajax_category_class+' a, .prdctfltr-shop.prdctfltr-ajax '+prdctfltr.ajax_class+' '+prdctfltr.ajax_category_class+' a', function() {

		var curr = $(this).closest(prdctfltr.ajax_category_class);

		var curr_sc = ( curr.closest('.prdctfltr_sc_products').length > 0 ? curr.closest('.prdctfltr_sc_products') : $('.prdctfltr_sc_products:first').length > 0 ? $('.prdctfltr_sc_products:first') : $('.prdctfltr_woocommerce:first').length > 0 ? $('.prdctfltr_woocommerce:first') : 'none' );

		if ( curr_sc == 'none' ) {
			return;
		}

		if ( curr_sc.hasClass('prdctfltr_sc_products') ) {
			var curr_filter = ( curr_sc.find('.prdctfltr_woocommerce').length > 0 ? curr_sc.find('.prdctfltr_woocommerce') : $('.prdctfltr-widget').find('.prdctfltr_woocommerce') );
		}
		else if ( $('.prdctfltr_sc_products').length == 0 ) {
			var curr_filter = curr_sc;
		}
		else {
			return;
		}

		var cat = curr.find('.prdctfltr_cat_support').data('slug');

		var hasFilter = curr_filter.find('.prdctfltr_filter[data-filter="product_cat"] input[value="'+cat+'"]:first');

		if ( hasFilter.length > 0 ) {
			hasFilter.closest('label').trigger('click');
			if ( !curr_filter.hasClass('prdctfltr_click_filter') ) {
				curr_filter.find('.prdctfltr_woocommerce_filter_submit').trigger('click');
			}
		}
		else {
			var hasField = curr_filter.find('.prdctfltr_filter[data-filter="product_cat"]');

			if ( hasField.length > 0 ) {
				hasField.find('input[name="product_cat"]').val(cat);
			}
			else {
				var append = $('<input name="product_cat" type="hidden" value="'+cat+'" />');
				curr_filter.find('.prdctfltr_add_inputs').append(append);
			}

			if ( !curr_filter.hasClass('prdctfltr_click_filter') ) {
				curr_filter.find('.prdctfltr_woocommerce_filter_submit').trigger('click');
			}
			else {
				prdctfltr_respond_550(curr_filter.find('form'));
			}
		}

		return false;

	});

	if ( $('body').hasClass('prdctfltr-ajax') ) {
		if ( $('body.prdctfltr-ajax '+prdctfltr.ajax_orderby_class).length>0 ) {

			$(document).on('submit', 'body.prdctfltr-ajax '+prdctfltr.ajax_orderby_class, function() {
				return false;
			});

			$(document).on('change', 'body.prdctfltr-ajax '+prdctfltr.ajax_orderby_class+' select', function() {

				var orderVal = this.value;
				$('.prdctfltr_wc .prdctfltr_add_inputs').each( function() {
					if ( $(this).find('input[name="orderby"]').length > 0 ) {
						$(this).find('input[name="orderby"]').val(orderVal);
						prdctfltr_respond_550($('.prdctfltr_wc:first form'));
					}
					else {
						$(this).append('<input name="orderby" value="'+orderVal+'" />');
						prdctfltr_respond_550($('.prdctfltr_wc:first form'));
					}
				});

			});

		}

	}

	function pf_get_scroll( products, offset ) {

		if ( prdctfltr.ajax_scroll == 'products' ) {
			if ( offset>0 ) {
				var objOffset = products.find(prdctfltr.ajax_product_class+':gt('+offset+')').offset().top;
			}
			else {
				if ( products.closest('.prdctfltr_sc_products') > 0 ) {
					var objOffset = products.offset().top;
				}
				else {
					var objOffset = $('.prdctfltr_wc.prdctfltr_wc_widget').length > 0 ? $('.prdctfltr_wc_widget:first').offset().top : $('.prdctfltr_wc:first').offset().top;
					if ( objOffset > 500 ) {
						objOffset = 500;
					}
				}
			}

			$('html, body').animate({
				scrollTop: objOffset-100
			}, 500);
		}
		else if ( prdctfltr.ajax_scroll == 'top' ) {
			$('html, body').animate({
				scrollTop: 0
			}, 500);
		}

	}

	function pf_animate_products( products, obj2, type ) {
		if ( type=='append' ) {
			if ( prdctfltr.ajax_animation == 'none' ) {
				products.append(obj2.contents().unwrap());
			}
			else if ( prdctfltr.ajax_animation == 'slide' ) {

				var beforeLength = products.find(prdctfltr.ajax_product_class).length;

				products.append(obj2.contents().unwrap());
				var curr_products = products.find(prdctfltr.ajax_product_class+':gt('+beforeLength+')');

				curr_products.hide();
				if ( typeof curr_products !== 'undefined' ) {
					curr_products.each(function(i) {
						$(this).delay((i++) * 100).slideDown({duration: 200,easing: 'linear'});
					});
				}

			}
			else if ( prdctfltr.ajax_animation == 'random' ) {

				var beforeLength = products.find(prdctfltr.ajax_product_class).length;

				products.append(obj2.contents().unwrap());
				var curr_products = products.find(prdctfltr.ajax_product_class+':gt('+beforeLength+')');

				curr_products.css('visibility', 'hidden');
				if ( typeof curr_products !== 'undefined' ) {
					curr_products.css('visibility', 'hidden');

					var interval = setInterval(function () {
					var $ds = curr_products.not('.pf_faded');
					$ds.eq(Math.floor(Math.random() * $ds.length)).css('visibility','visible').hide().fadeTo(100, 1).addClass('pf_faded');
						if ($ds.length == 1) {
							clearInterval(interval);
						}
					}, 50);
				}

			}
			else {

				var beforeLength = products.find(prdctfltr.ajax_product_class).length;

				products.append(obj2.contents().unwrap());
				var curr_products = products.find(prdctfltr.ajax_product_class+':gt('+beforeLength+')');

				curr_products.hide();
				if ( typeof curr_products !== 'undefined' ) {
					curr_products.each(function(i) {
						$(this).delay((i++) * 100).fadeTo(100, 1);
					});
				}
			}
			
		}
		else {

			if ( prdctfltr.ajax_animation == 'none' ) {
				products.replaceWith(obj2);
			}
			else if ( prdctfltr.ajax_animation == 'slide' ) {
				products.replaceWith(obj2);
				var curr_products = obj2.find(prdctfltr.ajax_product_class);

				curr_products.hide();
				if ( typeof curr_products !== 'undefined' ) {
					curr_products.each(function(i) {
						$(this).delay((i++) * 100).slideDown({duration: 200,easing: 'linear'});
					});
				}
			}
			else if ( prdctfltr.ajax_animation == 'random' ) {
				products.replaceWith(obj2);
				var curr_products = obj2.find(prdctfltr.ajax_product_class);

				curr_products.css('visibility', 'hidden');
				if ( typeof curr_products !== 'undefined' ) {
					curr_products.css('visibility', 'hidden');

					var interval = setInterval(function () {
					var $ds = curr_products.not('.pf_faded');
					$ds.eq(Math.floor(Math.random() * $ds.length)).css('visibility','visible').hide().fadeTo(100, 1).addClass('pf_faded');
						if ($ds.length == 1) {
							clearInterval(interval);
						}
					}, 50);
				}
			}
			else {
				products.replaceWith(obj2);
				var curr_products = obj2.find(prdctfltr.ajax_product_class);

				curr_products.hide();
				if ( typeof curr_products !== 'undefined' ) {
					curr_products.each(function(i) {
						$(this).delay((i++) * 100).fadeTo(100, 1);
					});
				}
			}
		}
	}

	if ( $(prdctfltr.ajax_orderby_class).length<1 ) {
		$('.prdctfltr_add_inputs input[name="orderby"]').remove();
	}

	function do_zindexes(curr) {
		curr = ( curr == null ? $('.prdctfltr_wc') : curr );

		curr.each( function() {
			if ( $(this).hasClass('pf_select')) {
				var objCount = $(this).find('.prdctfltr_filter');
			}
			else {
				var objCount = $(this).find('.prdctfltr_terms_customized_select');
			}
			

			var c = objCount.length;
			objCount.css('z-index', function(i) {
				return c - i + 10;
			});

		});
	}
	do_zindexes();

	function prdctfltr_show_opened_widgets() {

		if ( $('.prdctfltr-widget').length > 0 && $('.prdctfltr-widget .prdctfltr_error').length !== 1 ) {
			$('.prdctfltr-widget .prdctfltr_filter').each( function() {

				var curr = $(this);

				if ( curr.find('input[type="checkbox"]:checked').length > 0 ) {

					curr.find('.prdctfltr_widget_title .prdctfltr-down').removeClass('prdctfltr-down').addClass('prdctfltr-up');
					curr.find('.prdctfltr_add_scroll').addClass('prdctfltr_down').css({'display':'block'});

				}
				else if ( curr.find('input[type="hidden"]:first').length == 1 && curr.find('input[type="hidden"]:first').val() !== '' ) {

					curr.find('.prdctfltr_widget_title .prdctfltr-down').removeClass('prdctfltr-down').addClass('prdctfltr-up');
					curr.find('.prdctfltr_add_scroll').addClass('prdctfltr_down').css({'display':'block'});

				}
				else if ( curr.find('input[type="text"]').length > 0 && curr.find('input[type="text"]').val() !== '' ) {

					curr.find('.prdctfltr_widget_title .prdctfltr-down').removeClass('prdctfltr-down').addClass('prdctfltr-up');
					curr.find('.prdctfltr_add_scroll').addClass('prdctfltr_down').css({'display':'block'});

				}

			});
		}

	}
	prdctfltr_show_opened_widgets();


	function prdctfltr_tabbed_selection(curr) {
		curr = ( curr == null ? $('.prdctfltr_wc') : curr );

		curr.each( function() {
			if ( $(this).hasClass('prdctfltr_tabbed_selection') ) {

				$(this).find('label:first-child').each( function() {
					if ( $(this).find('input[type="checkbox"][value=""]').length > 0 ) {
						$(this).remove();
					}
				});

				var checkLength = $(this).find('.prdctfltr_filter').length;
				var checkObj = $(this);
				$(this).find('.prdctfltr_filter').each( function() {
					if ( $(this).find('input[type="hidden"]:first').length > 0 && $(this).find('input[type="hidden"]').val() !== '' ) {
						$(this).addClass('prdctfltr_has_selection');
					}
					if ( !--checkLength ) {
						var newLength = checkObj.find('.prdctfltr_has_selection').length;
						var count = 0;
						checkObj.find('.prdctfltr_has_selection').each( function() {
							count++;
							if ( newLength !== count ) {
								checkObj.find('a[data-key="'+$(this).attr('data-filter')+'"]').remove();
							}
						});
					}
				});

			}
		});
	}
	prdctfltr_tabbed_selection();

	$.pfcount = function (array) {
		if(array.length) {
			return array.length;
		}
		else {
			var length = 0;
			for ( var p in array ){
				if(array.hasOwnProperty(p)) length++;
			}
			return length;
		}
	};

})(jQuery);