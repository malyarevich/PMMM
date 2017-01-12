
/*global redux_change, wp, redux*/

(function( $ ) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.shortcodemapper = redux.field_objects.shortcodemapper || {};

    $( document ).ready(
        function() {
        
            redux.field_objects.shortcodemapper.init();
        }
    );

    redux.field_objects.shortcodemapper.init = function( selector ) {

        
        if ( !selector ) {
            selector = $( document ).find( ".redux-group-tab:visible" ).find( '.redux-container-shortcode_mapper:visible' );
        }
          

        $( selector ).each( 
            function() {
                var el = $( this );

                redux.field_objects.media.init(el);
                
                var parent = el;
                if ( !el.hasClass( 'redux-field-container' ) ) {
                    parent = el.parents( '.redux-field-container:first' );
                }
                if ( parent.is( ":hidden" ) ) { // Skip hidden fields
                    return;
                }
                
                if ( parent.hasClass( 'redux-container-shortcode_mapper' ) ) {
                    parent.addClass( 'redux-field-init' );    
                }
                
                if ( parent.hasClass( 'redux-field-init' ) ) {
                    parent.removeClass( 'redux-field-init' );
                } else {
                    return;
                }

                el.find( '.redux-shortcodemapper-remove-parameter' ).live(
                            'click', function() {

                        var element = $( this ).parent().parent().parent().parent().parent().parent();
                        
                        if  ( element.find('.shortcode_parameter_row').length > 1 ){
                                                   
                            $( this ).parents('.shortcode_parameter_row').remove();

                        }else{

                            element.find('.shortcode_parameter_row').find( 'input[type="text"]' ).val( '' );
                            element.find('.shortcode_parameter_row').find( 'textarea' ).val( '' );
                            element.find('.shortcode_parameter_row').find( 'input[type="hidden"]' ).val( '' );
                            element.find('.shortcode_parameter_row').find( '.ss-select' ).val( 'no' );
                            element.find('.shortcode_parameter_row').find( 'input[type="text"], input[type="hidden"], textarea, .ss-select' ).each(
                                function() {
       
                                $( this ).attr(
                                    "name", jQuery( this ).attr( "name" ).replace( /[0-9]+(?!.*[0-9])/, 1 )
                                ).attr( "id", $( this ).attr( "id" ).replace( /[0-9]+(?!.*[0-9])/, 1 ) );
                                  
                                }
  
                        )};
                        
                        return false;

                });
                                    

                el.find( '.redux-shortcodemapper-remove' ).live(
                    'click', function() {
                        redux_change( $( this ) );

                        $( this ).parent().siblings().find( 'input[type="text"]' ).val( '' );
                        $( this ).parent().siblings().find( 'textarea' ).val( '' );
                        $( this ).parent().siblings().find( 'input[type="hidden"]' ).val( '' );
                        $( this ).parent().siblings().find( '.ss-select' ).val( 'no' );
						$( this ).parent().siblings().find('.shortcode_parameter_row').not(':first').remove();
						  
                        var slideCount = $( this ).parents( '.redux-container-shortcode_mapper:first' ).find( '.redux-shortcodemapper-accordion-group' ).length;

                        if ( slideCount > 1 ) {
                            $( this ).parents( '.redux-shortcodemapper-accordion-group:first' ).slideUp(
                                'medium', function() {
                                    $( this ).remove();
                                }
                            );
                        } else {   
                        
                            var content_new_title = $('.redux-shortcodemapper-accordion' ).first().data( 'new-content-title' );
                            
                            $( this ).parents( '.redux-shortcodemapper-accordion-group:first' ).find( '.remove-image' ).click();
                            $( this ).parents( '.redux-container-shortcode_mapper:first' ).find( '.redux-shortcodemapper-accordion-group:last' ).find( '.redux-shortcodemapper-header' ).text( content_new_title );
                            
                        }
                    }
                );


                 el.find( '.redux-shortcodemapper-add-parameter' ).click(
                
                    function() {
                        
                        var newSlide = $( this ).parent().find( '.shortcode_parameter_row:last' ).clone( true );
                        var slideCount = $( newSlide ).attr( "data-row" );
                        var slideCount1 = slideCount * 1 + 1;

                        $( newSlide ).attr( "data-row", slideCount1 );

                        $( newSlide ).find( 'input[type="text"], input[type="hidden"], textarea, .ss-select' ).each(
                            function() {
  
                                $( this ).attr(
                                    "name", jQuery( this ).attr( "name" ).replace( /[0-9]+(?!.*[0-9])/, slideCount1 )
                                ).attr( "id", $( this ).attr( "id" ).replace( /[0-9]+(?!.*[0-9])/, slideCount1 ) );
                                
                                $( this ).val( '' );
                                if ( $( this ).hasClass( 'slide-sort' ) ) {
                                    $( this ).val( slideCount1 );
                                }
                            }
                        );

                        var content_new_title = $( this ).prev().data( 'new-content-title' );

                        $( newSlide ).find( '.screenshot' ).removeAttr( 'style' );
                        $( newSlide ).find( '.screenshot' ).addClass( 'hide' );
                        $( newSlide ).find( '.screenshot a' ).attr( 'href', '' );
                        $( newSlide ).find( 'h3' ).text( '' ).append( '<span class="redux-shortcodemapper-header">' + content_new_title + '</span><span class="ui-accordion-header-icon ui-icon ui-icon-plus"></span>' );   
                        $( this ).parent().find( '.shortcode_parameter_row:last' ).after( newSlide );
                    }
                );

                el.find( '.redux-shortcodemapper-add' ).click(
                
                    function() {
                    	
                        var newSlide = $( this ).prev().find( '.redux-shortcodemapper-accordion-group:last' ).clone( true );
                        var slideCount = $( newSlide ).find( '.slide-title' ).attr( "name" ).match( /[0-9]+(?!.*[0-9])/ );
                        var slideCount1 = slideCount * 1 + 1;
                        
                        $( newSlide ).find('.shortcode_parameter_row').not(':first').remove()
                        $( newSlide ).find( 'input[type="text"], input[type="hidden"], textarea, .ss-select' ).each(
                            function() {
 
                                 if ( $( this).parent().hasClass('short_param_col') ){
                                    var ini_index = jQuery( this ).attr( "id" ).indexOf("_p")+2;
                                    var end_index = jQuery( this ).attr( "id" ).substr(ini_index).indexOf("_");
                                    var end_str = jQuery( this ).attr( "id" ).substr(ini_index+end_index);
                                    var final_id = jQuery( this ).attr( "id" ).substr(0, ini_index) +'' + slideCount1+end_str;
                                    
                                     $( this ).attr( "id", final_id);

                                    ini_index = jQuery( this ).attr( "name" ).indexOf("][")+2;
                                    end_index = jQuery( this ).attr( "name" ).substr(ini_index).indexOf("]");
                                    end_str = jQuery( this ).attr( "name" ).substr(ini_index+end_index);
                                    final_id = jQuery( this ).attr( "name" ).substr(0, ini_index) +'' + slideCount1+end_str;
                                    
                                    $( this ).attr( "name", final_id);

                                }else{

                                    $( this ).attr(
                                        "name", jQuery( this ).attr( "name" ).replace( /[0-9]+(?!.*[0-9])/, slideCount1 )                                    
                                    ).attr( "id", $( this ).attr( "id" ).replace( /[0-9]+(?!.*[0-9])/, slideCount1 ) );   

                                   
                                }

                                $( this ).val( '' );
                                
                                if ( $( this ).hasClass( 'slide-sort' ) ) {
                                        $( this ).val( slideCount1 );
                                }    
                                
                            }
                        );

                       

                        var content_new_title = $( this ).prev().data( 'new-content-title' );

                        $( newSlide ).find( '.screenshot' ).removeAttr( 'style' );
                        $( newSlide ).find( '.screenshot' ).addClass( 'hide' );
                        $( newSlide ).find( '.screenshot a' ).attr( 'href', '' );
                        $( newSlide ).find( 'h3' ).text( '' ).append( '<span class="redux-shortcodemapper-header">' + content_new_title + '</span><span class="ui-accordion-header-icon ui-icon ui-icon-plus"></span>' );
                        $( this ).prev().append( newSlide );
                    }
                );

                el.find( '.slide-title' ).keyup(
                    function( event ) {
                        var newTitle = event.target.value;

                        $( this ).parents().eq( 4 ).find( '.redux-shortcodemapper-header' ).text( newTitle );
                    }
                );


                el.find( ".redux-shortcodemapper-accordion" )
                    .accordion(
                    {
                        header: "> div > fieldset > h3",
                        collapsible: true,
                        active: false,
                        heightStyle: "content",
                        icons: {
                            "header": "ui-icon-plus",
                            "activeHeader": "ui-icon-minus"
                        }
                    }
                )
                   
            }
        );
    };
})( jQuery );
