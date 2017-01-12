jQuery( function( $ ) {

	/**
	 * Object to handle Stripe payment forms.
	 */
	var wc_gzdp_multistep_stripe_helper = {

		/**
		 * Initialize e handlers and UI state.
		 */
		init: function() {
			this.isSelected          = false;

			this.update();

			$( document.body ).bind( 'updated_checkout', this.update );
			$( document.body ).bind( 'wc_gzdp_step_changed', this.update );
		},

		update: function() {

			if ( $( '.step-wrapper-active' ).hasClass( 'step-wrapper-1' ) ) {
				
				if ( $( '#payment_method_stripe' ).is( ':checked' ) ) {
					wc_gzdp_multistep_stripe_helper.isSelected = true;
				}
				
				$( '#payment_method_stripe' ).attr( 'checked', false );
			
			} else {
				
				if ( wc_gzdp_multistep_stripe_helper.isSelected ) {
					$( '#payment_method_stripe' ).attr( 'checked', true );
					wc_gzdp_multistep_stripe_helper.isSelected = false;
				}
			}
		},

	};

	wc_gzdp_multistep_stripe_helper.init();

});