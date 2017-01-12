// Note: I18n is loaded into wjecf_admin_i18n

jQuery( function( $ ) {

	var update_wjecf_products_and = function() { 
		$("#wjecf_products_and_label").html( 
			$("#_wjecf_products_and").val() == 'yes' ? wjecf_admin_i18n.label_and : wjecf_admin_i18n.label_or
		);
	};

	var update_wjecf_categories_and = function() { 
		$("#wjecf_categories_and_label").html( 
			$("#_wjecf_categories_and").val() == 'yes' ? wjecf_admin_i18n.label_and : wjecf_admin_i18n.label_or
		);
	};

	var init = function() {
		$("#woocommerce-coupon-data .form-field:has('#_wjecf_products_and')").detach().insertBefore("#woocommerce-coupon-data .form-field:has('[name=\"product_ids\"]')");
		$("#woocommerce-coupon-data .form-field:has('#_wjecf_categories_and')").detach().insertBefore("#woocommerce-coupon-data .form-field:has('[name=\"product_categories[]\"]')");
		
		//Append AND/OR to the labels
		$("#woocommerce-coupon-data .form-field:has('[name=\"product_ids\"]') label").append( ' <strong><span id="wjecf_products_and_label"></span></strong>' );
		$("#woocommerce-coupon-data .form-field:has('[name=\"product_categories[]\"]') label").append( ' <strong><span id="wjecf_categories_and_label"></span></strong>' );

		//Update AND or OR when checkbox value changes
		$("#_wjecf_products_and").click( update_wjecf_products_and );
		$("#_wjecf_categories_and").click( update_wjecf_categories_and );

		//Update now
		update_wjecf_products_and();
		update_wjecf_categories_and();

		$( 'select#discount_type' ).on( 'change', update_discount_type ).change();
	};


	/** Toggle visibility depending on selected discount type **/
	var update_discount_type = function() {
		// Get value
		var select_val = $( 'select#discount_type' ).val();

		if ( select_val === 'fixed_cart' ) {
			$( '.wjecf_hide_on_fixed_cart_discount' ).hide();
		} else {
			$( '.wjecf_hide_on_fixed_cart_discount' ).show();
		}

		if ( select_val === 'fixed_product' || select_val === 'percent_product' ) {
			$( '.wjecf_hide_on_product_discount' ).hide();
		} else {
			$( '.wjecf_hide_on_product_discount' ).show();
		}
	}

	init();
} );