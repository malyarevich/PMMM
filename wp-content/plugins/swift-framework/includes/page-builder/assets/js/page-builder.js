/* global jQuery,google,console,wp,wNumb,tinyMCE,tinyMCEPreInit,quicktags,QTags,tinymce,Materialize,alert,switchEditors,ajaxurl,confirm,tb_remove,_ */

// USE STRICT
"use strict";

//Global Var
var sfTranslatedText = jQuery( '.spb_translated_objects' );
var deletedElement;
var draggedItem;
var percentageClass;
var elements_values_array = [];
var currentAsset = "";

(function( $ ) {    
    
    $.log = function( text ) {
        if ( typeof(window.console) != 'undefined' ) console.log( text );
    };

    $.swift_page_builder = {
        isMainContainerEmpty: function() {
            if ( !jQuery( '.spb_main_sortable > div' ).length ) {
                $( '.metabox-builder-content' ).addClass( 'empty-builder' );
            } else {
                $( '.metabox-builder-content' ).removeClass( 'empty-builder' );
            }
        },
        cloneSelectedImagesFromMediaTab: function( html ) {
            var $button = $( '.spb_current_active_media_button' ).removeClass( '.spb_current_active_media_button' );

            var attached_img_div = $button.next();

            var hidden_ids = attached_img_div.prev().prev(),
                img_ul = attached_img_div.find( '.sf_gallery_widget_attached_images_list' );

            img_ul.html( html );

            var hidden_ids_value = '';
            img_ul.find( 'li' ).each(
                function() {
                    hidden_ids_value += (hidden_ids_value.length > 0 ? ',' : '') + $( this ).attr( 'media_id' );
                }
            );

            hidden_ids.val( hidden_ids_value );

            attachedImgSortable( img_ul );

            tb_remove();

        },
        galleryImagesControls: function() {

            $( document ).on( 'click', '#spb .sf_gallery_widget_add_images', function( e ) {

                    e.preventDefault();

                    var file_frame = "",
                        parentField = $( this ).parent().find( '.attach_image' ),
                        attachedImages = $( this ).parent().find( '.sf_gallery_widget_attached_images_list' );

                    // If the media frame already exists, reopen it.
                    if ( file_frame ) {
                        file_frame.open();
                        return;
                    }

                    // Create the media frame.
                    file_frame = wp.media.frames.file_frame = wp.media({
                        title: jQuery( this ).data( 'uploader_title' ),
                        button: {
                            text: jQuery( this ).data( 'uploader_button_text' ),
                        },
                        multiple: false  // Set to true to allow multiple files to be selected
                    });

                    // When an image is selected, run a callback.
                    file_frame.on( 'select', function( ) {
                      
                            // We set multiple to false so only get one image from the uploader
                            var attachment = file_frame.state().get( 'selection' ).first().toJSON();

                            parentField.val( attachment.id );
                            attachedImages.empty();   
                            attachedImages.append('<li class="added" media_id="'+attachment.id+'"><img src="'+attachment.sizes.thumbnail.url+'" alt="" rel="'+attachment.id+'"><div class="sf-close-image-bar"><a title="Deselect" class="sf-close-delete-file" href="#">×</a></div></li>');
 
                            jQuery('.sf-close-delete-file').click(function(e) {
                                e.preventDefault();
                                jQuery(this).parent().parent().remove();
                                jQuery('.sf_gallery_widget_attached_images_ids').val("");
                                return false;

                            });
                            return false;
                        }
                    );

                    // Finally, open the modal
                    file_frame.open();
                }
            );

            $( '.gallery_widget_img_select li' ).live("click", function() {
                    $( this ).toggleClass( 'added' );

                    var hidden_ids = $( this ).parent().parent().prev().prev().prev(),
                        ids_array = (hidden_ids.val().length > 0) ? hidden_ids.val().split( "," ) : [],
                        img_rel = $( this ).find( "img" ).attr( "rel" ),
                        id_pos = $.inArray( img_rel, ids_array );

                    /* if not found */
                    if ( id_pos == -1 ) {
                        ids_array.push( img_rel );
                    }
                    else {
                        ids_array.splice( id_pos, 1 );
                    }

                    hidden_ids.val( ids_array.join( "," ) );

                }
            );
        },initializeFormEditing: function( element ) {
            //

            removeClassProcessedElements();
            $( '#spb_edit_form .wp-editor-wrap .textarea_html' ).each(
                function() {
                    initTinyMce( $( this ) );
                }
            );
            
            var max_value = 800;
            
            if ( jQuery( '#spb .custom_css_percentage' ).attr('checked') == 'checked' ) {
                max_value = 20;
            }

            jQuery( '#padding_vertical, #padding_vertical_val, #padding_horizontal, #padding_horizontal_val, #margin_vertical, #margin_vertical_val ' ).attr( 'max' , max_value );     
                           
                    
            if ( element.attr( "data-element_type" ) == 'spb_text_block' ) {

                var el_vertical_pad = element.find('.padding_vertical');
                var el_horizontal_pad = element.find('.padding_horizontal');

                if( el_vertical_pad.val() > 0 ) {

                    if ( jQuery('.spb_edit_form_elements .custom-padding-top').val() == '' ) {
                        jQuery('.spb_edit_form_elements .custom-padding-top').val( el_vertical_pad.val() );    
                    }
                    
                    if ( jQuery('.spb_edit_form_elements .custom-padding-bottom').val() == '' ) {
                        jQuery('.spb_edit_form_elements .custom-padding-bottom').val( el_vertical_pad.val() );                  
                    }
                }

                if( el_horizontal_pad.val() > 0 && jQuery('.spb_edit_form_elements .custom-padding-left').val() == '' ) {

                    if ( jQuery('.spb_edit_form_elements .custom-padding-left').val() == '' ) {
                        jQuery('.spb_edit_form_elements .custom-padding-left').val( el_horizontal_pad.val() );
                    }

                    if ( jQuery('.spb_edit_form_elements .custom-padding-right').val() == '' ) {
                        jQuery('.spb_edit_form_elements .custom-padding-right').val( el_horizontal_pad.val() );                    
                    }
                }
            }  

            if ( element.attr( "data-element_type" ) == 'spb_column' ) {
                var el_horizontal_pad = element.find('.padding_horizontal').last();
                var el_vertical_pad = element.find('.padding_vertical').last();
                var max_value = 20;
                jQuery( '#col_el_class' ).parent().parent().parent().hide();
                jQuery('.col_sm').attr('disabled','disabled');
                
                setTimeout(function(){  

                    jQuery( '#col_padding_val' ).parent().parent().parent().hide();    


                    if ( parseInt( jQuery( '#col_padding_val' ).val() ) > 0 ){
                    
                        el_horizontal_pad.val( parseInt( jQuery( '#col_padding_val' ).val() ));
                        el_vertical_pad.val( parseInt( jQuery( '#col_padding_val' ).val() ));

                        jQuery( '#col_padding_val' ).val(0);
                        jQuery( '#col_padding' ).val(0);
                        jQuery( '#padding_horizontal' ).attr( 'max' , max_value );    
                        jQuery( '#padding_horizontal_val' ).attr( 'max' , max_value );   
                        jQuery( '#padding_vertical' ).attr( 'max' , max_value );    
                        jQuery( '#padding_vertical_val' ).attr( 'max' , max_value );   
                        jQuery( '#spb_edit_form .custom_css_percentage' ).attr('checked', 'checked');
                        jQuery('.custom-padding-bottom').val( el_horizontal_pad.val() );          
                        jQuery('.custom-padding-top').val( el_horizontal_pad.val() );  
                        jQuery('.custom-padding-left').val( el_horizontal_pad.val() );              
                        jQuery('.custom-padding-right').val( el_horizontal_pad.val() );  
                        
                    }

                    
                    jQuery('#col_padding').attr('disabled','disabled');
                    jQuery('#padding_horizontal_val').attr('value',  el_horizontal_pad.val() );
                    jQuery('#padding_horizontal').attr('value',   el_horizontal_pad.val() );
                    jQuery('#padding_vertical_val').attr('value',  el_vertical_pad.val() );
                    jQuery('#padding_vertical').attr('value',   el_vertical_pad.val() );

                }, 100);

                
            }

            if ( element.attr( "data-element_type" ) == 'spb_row' ) {

                jQuery( '#row_el_class' ).parent().parent().parent().hide();
                var el_horizontal_pad = element.find('.row_padding_horizontal');
                var el_vertical_pad = element.find('.row_padding_vertical');
                var el_vertical_margin = element.find('.row_margin_vertical');
                var max_value = 800;
                var percentage_value;
                var element_value;
                var row_el_class = element.find( '.row_el_class' ).val(); 


                el_horizontal_pad.val( Math.round( el_horizontal_pad.val() ) );
                el_vertical_pad.val( Math.round( el_vertical_pad.val() ) );
                el_vertical_margin.val( Math.round( el_vertical_margin.val() ) );

                if( row_el_class != '' ){
                    jQuery( '#row_el_class' ).val( '' );
                    jQuery( '#el_class' ).val( row_el_class ); 
                }
                
                jQuery('#row_margin_vertical_val').attr('disabled','disabled');
                jQuery('#row_padding_horizontal_val').attr('disabled','disabled');
                jQuery('#row_padding_vertical_val').attr('disabled','disabled');
                jQuery('#row_margin_vertical').attr('disabled','disabled');
                jQuery('#row_padding_horizontal').attr('disabled','disabled');
                jQuery('#row_padding_vertical').attr('disabled','disabled');
                jQuery('#row_padding_vertical').prev().css('background-color','#ccc');
                jQuery('#row_padding_horizontal').prev().css('background-color','#ccc');
                jQuery('#row_margin_vertical').prev().css('background-color','#ccc');


                if( el_horizontal_pad.val() > 0 ) {

                    if ( jQuery('.spb_edit_form_elements .custom-padding-left').val() == '' &&  jQuery('.spb_edit_form_elements .custom-padding-right').val() == '' ) {
                    
                        percentage_value = el_horizontal_pad.val() / 20 * 100;
                        element_value = max_value * percentage_value / 100;
                        jQuery('#padding_horizontal_val').val( element_value );
                        jQuery('#padding_horizontal').val( element_value );
                        el_horizontal_pad = jQuery('#padding_horizontal_val');

                    }


                    if ( jQuery('.spb_edit_form_elements .custom-padding-left').val() == '' ) {
                        jQuery('.spb_edit_form_elements .custom-padding-left').val( el_horizontal_pad.val() );
                    }

                    if ( jQuery('.spb_edit_form_elements .custom-padding-right').val() == '' ) {
                        jQuery('.spb_edit_form_elements .custom-padding-right').val( el_horizontal_pad.val() );                    
                    }
                }

                if( el_vertical_pad.val() > 0 ) {

                    jQuery('#padding_vertical_val').val( el_vertical_pad.val() );
                    jQuery('#padding_vertical').val( el_vertical_pad.val() );

                    if ( jQuery('.spb_edit_form_elements .custom-padding-bottom').val() == '' ) {
                        jQuery('.spb_edit_form_elements .custom-padding-bottom').val( el_vertical_pad.val() );
                    }

                    if ( jQuery('.spb_edit_form_elements .custom-padding-top').val() == '' ) {
                        jQuery('.spb_edit_form_elements .custom-padding-top').val( el_vertical_pad.val() );                    
                    }
                }

                 if( el_vertical_margin.val() > 0 ) {

                    jQuery('#margin_vertical_val').val( el_vertical_margin.val() );
                    jQuery('#margin_vertical').val( el_vertical_margin.val() );

                    if ( jQuery('.spb_edit_form_elements .custom-margin-bottom').val() == '' ) {
                        jQuery('.spb_edit_form_elements .custom-margin-bottom').val( el_vertical_margin.val() );
                    }

                    if ( jQuery('.spb_edit_form_elements .custom-margin-top').val() == '' ) {
                        jQuery('.spb_edit_form_elements .custom-margin-top').val( el_vertical_margin.val() );                    
                    }
                }

            }


          

            // Colorpicker
            $( '.spb-colorpicker' ).minicolors();

            // Ui Slider
            if ( $( '.noUiSlider' ).length > 0 ) {
                $( '.noUiSlider' ).each(
                    function() {
                        var uislider = $( this ),
                            sliderInput = uislider.next( 'input.spb-uislider' ),
                            value = parseInt( sliderInput.val(), 10 ),
                            step = parseFloat( sliderInput.data( 'step' ) ),
                            min = parseFloat( sliderInput.data( 'min' ) ),
                            max = parseFloat( sliderInput.data( 'max' ) );


                        uislider.noUiSlider(
                            {
                                range: {
                                    'min' : min,
                                    'max' : max
                                },
                                start: [value],
                                handles: 1,
                                step: step,
                                format: wNumb({
                                    decimals: 0,
                                    thousand: '',
                                    postfix: '',
                                })
                            }
                        );
                        uislider.Link('lower').to(sliderInput);
                    }
                );
            }

            // Buttonset
            if ( $( '.spb-buttonset' ).length > 0 ) {
                $( '.spb-buttonset' ).each(
                    function() {
                        var buttonset = $(this);
                        buttonset.find( '.buttonset-item,.ui-button' ).button();
                        buttonset.buttonset();
                    }
                );
            }

            // Icon picker
            if ( $( '.icon-picker' ).length > 0 ) {
                $( '.icon-picker' ).each(function() {
                    var selectedIcon = $(this).val(),
                        grid = $(this).parent().find('.font-icon-grid');
                    
                    if ( selectedIcon !== "" ) {
                        //grid.find( '.' + selectedIcon ).parent().addClass( 'selected' );
                    }
                });
            }

            // Icon grid
            $( '.font-icon-grid.std-grid' ).on('click', 'li', function() {
                    var selection = $( this ),
                        iconName = "";

                    if ( selection.hasClass('svg-icon') ) {
                        iconName = selection.data('path');
                    } else {
                        iconName = selection.find( 'i' ).attr( 'class' );
                    }

                    if ( iconName === "" ) {
                        iconName = selection.find( 'i' ).attr( 'class' );
                    }

                    if ( !iconName ) {
                        iconName = selection.data( 'icon' );
                    }

                    $( '.font-icon-grid li' ).removeClass( 'selected' );
                    selection.addClass( 'selected' );
                    selection.parent().parent().find( 'input' ).val( iconName );
                    selection.parent().parent().find('input').not('.search-icon-grid').val(iconName);

                }
            );

            $( document ).on( 'click', '.section_add_icon', function(){
                jQuery( this ).parent().parent().find('.font-icon-grid').show();
            });

            $( document ).on( 'click', '.section_icon_image',  function(){
                jQuery( this ).parent().parent().find('.font-icon-grid').show();
            });

            // Icon grid
            $( document ).on('click', '.font-icon-grid.repeater-grid li', function() {
                    var selection = $( this ),
                        iconName = "";
                    
                    if ( selection.hasClass('svg-icon') ) {
                        iconName = selection.data('icon');
                    } else {
                        iconName = selection.find( 'i' ).attr( 'class' );
                    }

                    if ( iconName === "" ) {
                        iconName = selection.find( 'i' ).attr( 'class' );
                    }

                    if ( iconName === "" ) {
                        iconName = selection.find( 'i' ).data( 'icon' );
                    }

                    //$( '.font-icon-grid-repeater li' ).removeClass( 'selected' );   
                   // selection.addClass( 'selected' );
                    selection.parent().parent().find( '.icon_field_value' ).val( iconName );
                    var clonedIcon = jQuery(this).find('i').clone();
                    selection.parent().parent().find('.section_icon_image').html( clonedIcon );
                    //selection.parent().parent().find('input').not('.search-icon-grid').val(iconName);
                    jQuery(this).parent().hide();
                    selection.parent().parent().find('.section_icon_image').show();

                }
            );
           

            // Get callback function name
            var cb = element.children( ".spb_edit_callback" );
            //
            if ( cb.length == 1 ) {
                var fn = window[cb.attr( "value" )];
                if ( typeof fn === 'function' ) {
                    //var tmp_output = fn( element );
                }
            }

            $( '.spb_save_edit_form' ).unbind( 'click' ).click(
                function( e ) {
                    e.preventDefault();
                    removeClassProcessedElements();
                    saveFormEditing( element );//(element);

                }
            );

            $( '#cancel-background-options' ).unbind( 'click' ).click(
                function( e ) {
                    e.preventDefault();
                    jQuery( 'body' ).css( 'overflow', '' );
                    //$('.spb_main_sortable, #spb-elements, .spb_switch-to-builder').show();
                    $( '.spb_tinymce' ).each(
                        function() {

                            if ( tinyMCE.majorVersion >= 4 ) {
                                tinyMCE.execCommand( "mceRemoveEditor", true, $( this ).attr( 'id' ) );
                            } else {
                                tinyMCE.execCommand( "mceRemoveControl", true, $( this ).attr( 'id' ) );
                            }

                        }
                    );
                    $( '#spb_edit_form' ).html( '' ).fadeOut();
                    //$('body, html').scrollTop(current_scroll_pos);
                    $( "#publish" ).show();
                }
            );
            jQuery( '#spb_edit_form').find('.spb_param_value.caption').parent().parent().hide();
         
        },
        onDragPlaceholder: function() {
            return $( '<div id="drag_placeholder"></div>' );
        },
        addLastClass: function( dom_tree ) {
            var total_width, width, next_width, $dom_tree;
            total_width = 0;
            width = 0;
            next_width = 0;
            $dom_tree = $( dom_tree );

            $dom_tree.children( ".spb_sortable" ).removeClass( "spb_first spb_last" );
            if ( $dom_tree.hasClass( "spb_main_sortable" ) ) {
                $dom_tree.find( ".spb_sortable .spb_sortable" ).removeClass( "sortable_1st_level" );
                $dom_tree.children( ".spb_sortable" ).addClass( "sortable_1st_level" );
                $dom_tree.children( ".spb_sortable:eq(0)" ).addClass( "spb_first" );
                $dom_tree.children( ".spb_sortable:last" ).addClass( "spb_last" );
            }

            if ( $dom_tree.hasClass( "spb_column_container" ) ) {
                $dom_tree.children( ".spb_sortable:eq(0)" ).addClass( "spb_first" );
                $dom_tree.children( ".spb_sortable:last" ).addClass( "spb_last" );
            }

            $dom_tree.children( ".spb_sortable" ).each( function() {

                    var cur_el = $( this );

                    // Width of current element
                    if ( cur_el.hasClass( "span12" ) || cur_el.hasClass( "spb_widget" ) ) {
                        width = 12;
                    }
                    else if ( cur_el.hasClass( "span10" ) ) {
                        width = 10;
                    }
                    else if ( cur_el.hasClass( "span9" ) ) {
                        width = 9;
                    }
                    else if ( cur_el.hasClass( "span8" ) ) {
                        width = 8;
                    }
                    else if ( cur_el.hasClass( "span6" ) ) {
                        width = 6;
                    }
                    else if ( cur_el.hasClass( "span4" ) ) {
                        width = 4;
                    }
                    else if ( cur_el.hasClass( "span3" ) ) {
                        width = 3;
                    }
                    else if ( cur_el.hasClass( "span2" ) ) {
                        width = 2;
                    }
                    total_width += width;

                    if ( total_width > 10 && total_width <= 12 ) {
                        cur_el.addClass( "spb_last" );
                        cur_el.next( '.spb_sortable' ).addClass( "spb_first" );
                        total_width = 0;
                    }
                    if ( total_width > 12 ) {
                        cur_el.addClass( 'spb_first' );
                        cur_el.prev( '.spb_sortable' ).addClass( "spb_last" );
                        total_width = width;
                    }

                    if ( cur_el.hasClass( 'spb_column' ) || cur_el.hasClass( 'spb_row' ) || cur_el.hasClass( 'spb_tabs' ) || cur_el.hasClass( 'spb_tour' ) || cur_el.hasClass( 'spb_gmaps' ) || cur_el.hasClass( 'spb_accordion' ) ) {


                        if ( cur_el.find( '.spb_element_wrapper .spb_column_container' ).length > 0 ) {
                            cur_el.removeClass( 'empty_column' );
                            cur_el.addClass( 'not_empty_column' );
                            //addLastClass(cur_el.find('.spb_element_wrapper .spb_column_container'));
                            cur_el.find( '.spb_element_wrapper .spb_column_container' ).each( function() {
                                    $.swift_page_builder.addLastClass( $( this ) ); // Seems it does nothing

                                    if ( $( this ).find( 'div:not(.container-helper)' ).length === 0 ) {
                                        $( this ).addClass( 'empty_column' );
                                        $( this ).html( $( '#container-helper-block' ).html() );
                                    } else {
                                        $( this ).find( '.container-helper' ).each(
                                            function() {
                                                var helper = jQuery( this );
                                                helper.appendTo( helper.parent() );
                                            }
                                        );
                                    }
                                }
                            );
                        }
                        else if ( cur_el.find( '.spb_element_wrapper .spb_column_container' ).length === 0 ) {
                            cur_el.removeClass( 'not_empty_column' );
                            cur_el.addClass( 'empty_column' );
                        }
                        else {
                            cur_el.removeClass( 'empty_column not_empty_column' );
                        }
                    }
                }
            );
        }, // endjQuery.swift_page_builder.addLastClass()
        save_spb_html: function() {
            this.addLastClass( $( ".spb_main_sortable" ) );

            var shortcodes = generateShortcodesFromHtml( $( ".spb_main_sortable" ) );

            removeClassProcessedElements();

            if ( isTinyMceActive() !== true ) {
                $( '#content' ).val( shortcodes );
            } else {
                //tinyMCE.activeEditor.setContent(shortcodes, {format : 'html'});
                tinyMCE.get( 'content' ).setContent( shortcodes, {format: 'html'} );

            }
        }
    };
})( jQuery );

jQuery( document ).ready(
    function( $ ) {   

 
        spb_reorder_saved_templates();
    
        /*** Template System ***/
        spb_templateSystem();

        /*** Element System ***/
        spb_customElementSystem();

        //Tabs - Edit Tab Action
        jQuery( document ).on( 'click', '.ui-tabs-nav .edit_tab', function() {
            showEditSmallForm( jQuery( this ) );
            return false;
        });

        jQuery( document ).on( 'change', '#padding_vertical', function() {
            
              jQuery('.spb_edit_form_elements .custom-padding-top').val( jQuery( this ).val() );
              jQuery('.spb_edit_form_elements .custom-padding-bottom').val( jQuery( this ).val() );                  

        });

        jQuery( document ).on( 'change', '#padding_horizontal', function() {
          
                jQuery('.spb_edit_form_elements .custom-padding-left').val( jQuery( this ).val() );
                jQuery('.spb_edit_form_elements .custom-padding-right').val( jQuery( this ).val() );   
        });

        jQuery( document ).on( 'change', '#margin_vertical', function() {
          
                jQuery('.spb_edit_form_elements .custom-margin-top').val( jQuery( this ).val() );
                jQuery('.spb_edit_form_elements .custom-margin-bottom').val( jQuery( this ).val() );   
        });

        jQuery( document ).on( 'change', '#border_size', function() {
          
                jQuery( '.spb_edit_form_elements .custom-border-top' ).val( jQuery( this ).val() );
                jQuery( '.spb_edit_form_elements .custom-border-bottom' ).val( jQuery( this ).val() );   
                jQuery( '.spb_edit_form_elements .custom-border-left' ).val( jQuery( this ).val() );   
                jQuery( '.spb_edit_form_elements .custom-border-right' ).val( jQuery( this ).val() );   
        });
        
        jQuery( document ).on( 'change', '#spb .custom_css_percentage' , function() {
            
            var max_value;
            var percentage_value;  
            var element_value;

            if( jQuery( this ).attr('checked') == 'checked' ) {
                max_value = '20';               
            } else {
                max_value = '800';
            }  
                        
            percentage_value = jQuery( '#padding_vertical_val' ).val() / jQuery( '#padding_vertical_val' ).attr( 'max' ) * 100;
            element_value = max_value * percentage_value / 100;

            jQuery( '#padding_vertical' ).attr( 'max' , max_value );     
            jQuery( '#padding_vertical_val' ).attr( 'max' , max_value );
            jQuery( '#padding_vertical' ).val( element_value );
            jQuery( '#padding_vertical_val' ).val( element_value );  
            jQuery( '#padding_vertical' ).trigger( 'change' ); 

            percentage_value = jQuery( '#padding_horizontal_val' ).val() / jQuery( '#padding_horizontal_val' ).attr( 'max' )*100;
            element_value = max_value * percentage_value / 100;

            jQuery( '#padding_horizontal' ).attr('max', max_value );
            jQuery( '#padding_horizontal_val' ).attr('max', max_value );
            jQuery( '#padding_horizontal' ).val( element_value );
            jQuery( '#padding_horizontal_val' ).val( element_value );  
            jQuery( '#padding_horizontal' ).trigger( 'change' );

            percentage_value = jQuery( '#margin_vertical_val' ).val() / jQuery( '#margin_vertical_val' ).attr( 'max' )*100;
            element_value = max_value * percentage_value / 100;
            element_value = Math.round( element_value );
            jQuery( '#margin_vertical' ).attr('max', max_value );
            jQuery( '#margin_vertical_val' ).attr('max', max_value );
            jQuery( '#margin_vertical' ).val( element_value );
            jQuery( '#margin_vertical_val' ).val( element_value );  
            jQuery( '#margin_vertical' ).trigger( 'change' );

            percentage_value = jQuery('.custom-padding-bottom').val() / jQuery( '#padding_vertical_val' ).attr( 'max' ) * 100;
            element_value = max_value * percentage_value / 100;
            jQuery('.custom-padding-bottom').val(  Math.round( element_value ) );  

            percentage_value = jQuery('.custom-padding-top').val() / jQuery( '#padding_vertical_val' ).attr( 'max' ) * 100;
            element_value = max_value * percentage_value / 100;
            jQuery('.custom-padding-top').val(  Math.round( element_value ) );  

            percentage_value = jQuery('.custom-padding-left').val() / jQuery( '#padding_horizontal_val' ).attr( 'max' ) * 100;
            element_value = max_value * percentage_value / 100;
            jQuery('.custom-padding-left').val(  Math.round( element_value ) );  

            percentage_value = jQuery('.custom-padding-right').val() / jQuery( '#padding_horizontal_val' ).attr( 'max' ) * 100;
            element_value = max_value * percentage_value / 100;
            jQuery('.custom-padding-right').val(  Math.round( element_value ) );  
            
            percentage_value = jQuery('.custom-margin-bottom').val() / jQuery( '#margin_vertical_val' ).attr( 'max' ) * 100;
            element_value = max_value * percentage_value / 100;
            jQuery('.custom-margin-bottom').val( Math.round( element_value ) );  

            percentage_value = jQuery('.custom-margin-top').val() / jQuery( '#margin_vertical_val' ).attr( 'max' ) * 100;
            element_value = max_value * percentage_value / 100;
            jQuery('.custom-margin-top').val( Math.round( element_value ) );  

        });


        jQuery( document ).on( 'change', '#spb .simplified_controls' , function() {
                detailedControls( jQuery( this ) );
        });
         
        jQuery( document ).on( 'input' , '.custom-margin', function ( ) {
                jQuery( '.lb_css_field .cm-global' ).val( jQuery( this ).val() );
        });

        jQuery( document ).on( 'input' , '.custom-border', function ( ) {
                jQuery( '.lb_css_field .cb-global' ).val( jQuery( this ).val() );
        });

        jQuery( document ).on( 'input' , '.custom-padding', function ( ) {
                jQuery( '.lb_css_field .cp-global' ).val( jQuery( this ).val() );
        });

        //Tabs - Delete Tab Action
        jQuery( document ).on( 'click', '.ui-tabs-nav .delete_tab' , function( e ) {
            e.preventDefault();

            var modal_msg = sfTranslatedText.attr( 'data-spb-delete-tabs-section-q1' );   
            var modal_header_msg =  sfTranslatedText.attr( 'data-spb-delete-tabs-header' );
            var elementSaveModal = '<div class="modal_wrapper"><a class="waves-effect waves-light btn modal-trigger" href="#modal-delete"></a><div id="modal-delete-tab" class="modal modal_spb"><div class="modal-content">';
            elementSaveModal += '<h4>' + modal_header_msg + '</h4><div class="row"> <div class="input-field delete-block col s12"><label>' + modal_msg + '</label>';
            elementSaveModal += '</div></div><div class="modal-footer modal-delete-tab"><a href="#!" class=" modal-action spb-modal-close waves-effect modal-ok-button btn-flat">OK</a><a href="#!" class=" modal-action spb-modal-close waves-effect modal-cancel-button btn-flat">Cancel</a></div></div></div></div>';
            jQuery( '#spb' ).append( elementSaveModal );   
            jQuery('#modal-delete-tab').openModal();            
            deletedElement = jQuery( this );

            return false;
        });

        //Tabs - Add Tab Action 
        jQuery( document ).on('click', '.tabs_expanded_helper .add_tab, .add-pin-to-map, .add-parallax-layer', function(e) {                       
            e.preventDefault();
            
            var tab_title = ( jQuery( this ).closest( '.spb_sortable' ).hasClass( 'spb_tour' )) ? 'Slide' : 'Tab',
                tabs_count = jQuery( this ).parent().parent().parent().find( '.ui-tabs-nav li' ).length + 1,
                tabs_asset = jQuery( this ).parent().parent().parent().parent().find( '.spb_tabs_holder' ),
                tabs_nav = tabs_asset.find( '.ui-tabs-nav' ),
                el_name = '';

                //Hide the expanded action container
                jQuery( this ).parent().hide();

                //Show the closed action container 
                jQuery( this ).parent().parent().find('.container-helper').show();
                
            if ( jQuery( this ).closest( '.spb_sortable' ).hasClass( 'spb_gmaps' ) ) {
                tab_title = 'Pin';
                tabs_count = jQuery( this ).parent().parent().find( '.spb_map_pin' ).length + 1;

            }

            if ( jQuery( this ).closest( '.spb_sortable' ).hasClass( 'spb_multilayer_parallax' ) ) {
                tab_title = 'Layer';
                tabs_count = jQuery( this ).parent().parent().find( '.spb_multilayer_parallax_layer' ).length + 1;

            }


            if ( jQuery( this ).closest( '.spb_sortable' ).hasClass( 'spb_tour' ) ) {
                el_name = 'Tour Tab';
                tabs_nav.append( '<li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tab-' + tabs_count + '" aria-labelledby="ui-id-' + tabs_count + '" aria-selected="false" style="top: 0px;"><a href="#tab-' + tabs_count + '" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-' + tabs_count + '"><span>' + tab_title + ' ' + tabs_count + '</span></a><a class="delete_tab"></a><a class="edit_tab"></a></li>' );

                tabs_asset.append( '<div id="tab-' + tabs_count + '" class="row-fluid spb_column_container spb_sortable_container not-column-inherit ui-sortable ui-droppable ui-tabs-panel ui-widget-content ui-corner-bottom spb-dont-resize" aria-labelledby="ui-id-' + tabs_count + '" role="tabpanel" aria-expanded="true" aria-hidden="false"><div data-element_type="spb_text_block" class="spb_text_block spb_content_element spb_sortable span12 spb_first spb_last ui-sortable-helper"><input type="hidden" class="spb_sc_base" name="element_name-spb_text_block" value="spb_text_block"><div class="controls sidebar-name"> <div class="column_size_wrapper"> <a class="column_decrease" href="#" title="Decrease width"></a> <span class="column_size">1/1</span> <a class="column_increase" href="#" title="Increase width"></a> </div><div class="controls_right"> <a class="column_popup" href="#" title="Pop up"></a><a class="column_edit" href="#" title="Edit"><span class="icon-edit"></span></a> <a class="column_clone" href="#" title="Clone"></a> <a class="column_delete" href="#" title="Delete"></a></div></div><div class="spb_element_wrapper clearfix"><input type="hidden" class="spb_param_value title textfield " name="title" value=""><input type="hidden" class="spb_param_value icon textfield " name="icon" value=""><div class="spb_param_value holder content textarea_html " name="content"><p> This is a text block. Click the edit button to change this text. </p></div><input type="hidden" class="spb_param_value pb_margin_bottom dropdown " name="pb_margin_bottom" value="no"><input type="hidden" class="spb_param_value pb_border_bottom dropdown " name="pb_border_bottom" value="no"><input type="hidden" class="spb_param_value el_class textfield " name="el_class" value=""></div> <!-- end .spb_element_wrapper --></div> <!-- end #element-spb_text_block --></div>' );
            }
            else if ( jQuery( this ).closest( '.spb_sortable' ).hasClass( 'spb_gmaps' ) ) {
                el_name = 'Map Pin';
                tabs_asset = jQuery( this ).parent().parent().parent().find( '.spb_tabs_holder' );
                tabs_nav.append( '<li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tab-' + tabs_count + '" aria-labelledby="ui-id-' + tabs_count + '" aria-selected="false" style="top: 0px;"><a href="#tab-' + tabs_count + '" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-' + tabs_count + '"><span>' + tab_title + ' ' + tabs_count + '</span></a></li>' );
                tabs_asset.append( '<div class="row-fluid spb_column_container map_pin_wrapper not-column-inherit not-sortable"><div data-element_type="spb_map_pin" class="spb_map_pin spb_content_element spb_sortable span12 spb_first spb_last"><input type="hidden" class="spb_sc_base" name="element_name-spb_map_pin" value="spb_map_pin"><div class="spb_element_wrapper  over_element_wrapper"><div class="spb_elem_controls" style="display: none;"><a class="column_delete" href="#" title="Delete"><span class="icon-delete"></span></a><a class="element-save" href="#" title="Save"><span class="icon-save"></span></a><a class="column_clone" href="#" title="Duplicate"><span class="icon-duplicate"></span></a><a class="column_edit" href="#" title="Edit"><span class="icon-edit"></span></a></div><div class="icon_holder"><span class="icon-map"></span></div><div class="el_name_holder" data_default_name="' + el_name + '">' + el_name + '</div><div class="el_name_editor"><input name="el_name_editor" id="el_name_editor" type="text" class="validate textfield" value="Map Pin"><a class="el-name-save" href="#" title="Save"><span class="icon-save"></span></a></div><input type="hidden" class="spb_param_value general_tab section_tab " name="general_tab" value=""><input type="hidden" class="spb_param_value element_name textfield " name="element_name" value=""><input type="hidden" class="spb_param_value pin_title textfield " name="pin_title" value="First Pin"><input type="hidden" class="spb_param_value address textfield " name="address" value="Click the edit button to change the map pin details."><input type="hidden" class="spb_param_value pin_latitude textfield " name="pin_latitude" value=""><input type="hidden" class="spb_param_value pin_longitude textfield " name="pin_longitude" value=""><input type="hidden" class="spb_param_value pin_image attach_image " name="pin_image" value=""><input type="hidden" class="spb_param_value pin_link textfield " name="pin_link" value=""><input type="hidden" class="spb_param_value pin_button_text textfield " name="pin_button_text" value=""><div class="spb_param_value holder content textarea_html hide-shortcode" name="content">This is a map pin. Click the edit button to change it.</div></div> <!-- end .spb_element_wrapper --> <!-- end #element-spb_map_pin --></div></div>' );
                //tabs_asset.append( '<div data-element_type="spb_map_pin" class="spb_map_pin spb_content_element spb_sortable span12 spb_first spb_last"><input type="hidden" class="spb_sc_base" name="element_name-spb_map_pin" value="spb_map_pin"><div class="spb_element_wrapper  over_element_wrapper"><div class="spb_elem_controls" style="display: none;"><a class="column_delete" href="#" title="Delete"><span class="icon-delete"></span></a><a class="element-save" href="#" title="Save"><span class="icon-save"></span></a><a class="column_clone" href="#" title="Duplicate"><span class="icon-duplicate"></span></a><a class="column_edit" href="#" title="Edit"><span class="icon-edit"></span></a></div><div class="icon_holder"><span class="icon-map"></span></div><div class="el_name_holder" data_default_name="Map Pin">Map Pin</div><div class="el_name_editor"><input name="el_name_editor" id="el_name_editor" type="text" class="validate textfield" value="Map Pin"><a class="el-name-save" href="#" title="Save"><span class="icon-save"></span></a></div><input type="hidden" class="spb_param_value general_tab section_tab " name="general_tab" value=""><input type="hidden" class="spb_param_value element_name textfield " name="element_name" value=""><input type="hidden" class="spb_param_value pin_title textfield " name="pin_title" value="First Pin"><input type="hidden" class="spb_param_value address textfield " name="address" value="Click the edit button to change the map pin details."><input type="hidden" class="spb_param_value pin_latitude textfield " name="pin_latitude" value=""><input type="hidden" class="spb_param_value pin_longitude textfield " name="pin_longitude" value=""><input type="hidden" class="spb_param_value pin_image attach_image " name="pin_image" value=""><input type="hidden" class="spb_param_value pin_link textfield " name="pin_link" value=""><input type="hidden" class="spb_param_value pin_button_text textfield " name="pin_button_text" value=""><div class="spb_param_value holder content textarea_html hide-shortcode" name="content">This is a map pin. Click the edit button to change it.</div></div> <!-- end .spb_element_wrapper --> <!-- end #element-spb_map_pin --></div>' );
            }
            else if ( jQuery( this ).closest( '.spb_sortable' ).hasClass( 'spb_multilayer_parallax' ) ) {
                el_name = 'Multilayer Parallax Layer';
                tabs_nav.append( '<li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tab-' + tabs_count + '" aria-labelledby="ui-id-' + tabs_count + '" aria-selected="false" style="top: 0px;"><a href="#tab-' + tabs_count + '" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-' + tabs_count + '"><span>' + tab_title + ' ' + tabs_count + '</span></a></li>' );
                tabs_asset.append( '<div class="row-fluid spb_column_container not-column-inherit not-sortable"><div data-element_type="spb_multilayer_parallax_layer" class="spb_multilayer_parallax_layer spb_content_element spb_sortable span12"><input type="hidden" class="spb_sc_base" name="element_name-spb_multilayer_parallax_layer" value="spb_multilayer_parallax_layer"><div class="controls sidebar-name"><div class="controls_right" style="display: block;"> <a class="column_edit" href="#" title="Edit"><span class="icon-edit"></span></a><a class="column_delete" href="#" title="Delete"><span class="icon-delete"></span></a></div></div><div class="spb_element_wrapper over_element_wrapper"><div class="spb_elem_controls" style="display: none;"><a class="column_delete" href="#" title="Delete"><span class="icon-delete"></span></a><a class="element-save" href="#" title="Save"><span class="icon-save"></span></a><a class="column_clone" href="#" title="Duplicate"><span class="icon-duplicate"></span></a><a class="column_edit" href="#" title="Edit"><span class="icon-edit"></span></a></div><div class="icon_holder"><span class="spb_multilayer_parallax_layer"></span></div><div class="el_name_holder" data_default_name="' + el_name + '">' + el_name + '</div><div class="el_name_editor"><input type="hidden" class="spb_param_value layer_title textfield " name="layer_title" value=""><input name="el_name_editor" id="el_name_editor" type="text" class="validate textfield" value="' + el_name + ' "><a class="el-name-save" href="#" title="Save"><span class="icon-save"></span></a></div><input type="hidden" class="spb_param_value general_tab section_tab " name="general_tab" value=""><input type="hidden" class="spb_param_value element_name textfield " name="element_name" value=""><input type="hidden" class="spb_param_value layer_title textfield " name="layer_title" value=""><input type="hidden" class="spb_param_value layer_image attach_image " name="layer_image" value=""><img width="66" height="66" src="' +   jQuery( this ).closest( '.spb_sortable' ).attr('data-content-url') +  '" class="attachment-thumbnail" alt="" title=""><a href="#" class="column_edit_trigger image-exists"><i class="spb-icon-single-image"></i>No image yet! Click here to select it now.</a><input type="hidden" class="spb_param_value layer_type dropdown " name="layer_type" value="original"><input type="hidden" class="spb_param_value layer_bg_pos dropdown " name="layer_bg_pos" value="center_center"><input type="hidden" class="spb_param_value layer_bg_repeat dropdown " name="layer_bg_repeat" value="no-repeat"><input type="hidden" class="spb_param_value layer_depth dropdown " name="layer_depth" value="0.00"><input type="hidden" class="spb_param_value section_misc_options section " name="section_misc_options" value=""><input type="hidden" class="spb_param_value text_layer buttonset " name="text_layer" value="no"><input type="hidden" class="spb_param_value content textarea_html " name="content" value=""></div> <!-- end .spb_element_wrapper --></div> <!-- end #element-spb_multilayer_parallax_layer --></div></div>' );
                
            } else {
                el_name ='Tab';
                tabs_nav.append( '<li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tab-' + tabs_count + '" aria-labelledby="ui-id-' + tabs_count + '" aria-selected="false" style="top: 0px;"><a href="#tab-' + tabs_count + '" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-' + tabs_count + '"><span>' + tab_title + ' ' + tabs_count + '</span></a><a class="edit_tab"><span class="icon-edit"></span></a></a><a class="delete_tab"><span class="icon-delete"></span></a></li>' );
                tabs_asset.append( '<div id="tab-' + tabs_count + '" class="row-fluid spb_column_container spb_sortable not-column-inherit not-sortable ui-sortable ui-droppable ui-tabs-panel ui-widget-content ui-corner-bottom spb-dont-resize" aria-labelledby="ui-id-' + tabs_count + '" role="tabpanel" aria-expanded="false" aria-hidden="true" style="display: none;"><div data-element_type="spb_text_block" class="spb_text_block spb_content_element spb_sortable span12 sortable_1st_level spb_last spb_first"><input type="hidden" class="spb_sc_base" name="element_name-spb_text_block" value="spb_text_block"><div class="spb_element_wrapper clearfix over_element_wrapper"><div class="spb_elem_controls" style="display: none;"><a class="column_delete" href="#" title="Delete"><span class="icon-delete"></span></a><a class="element-save" href="#" title="Save"><span class="icon-save"></span></a><a class="column_clone" href="#" title="Duplicate"><span class="icon-duplicate"></span></a><a class="column_edit" href="#" title="Edit"><span class="icon-edit"></span></a></div><div class="controls sidebar-name"> <div class="column_size_wrapper" style="display: none;"> <a class="column_decrease" href="#" title="Decrease width"></a> <span class="column_size">1/1</span> <a class="column_increase" href="#" title="Increase width"></a> </div><div class="controls_right" style="display: block;"> <a class="element-save" href="#" title="Save"><span class="icon-save"></span></a> <a class="column_edit" href="#" title="Edit"><span class="icon-edit"></span></a> <a class="column_clone" href="#" title="Clone"><span class="icon-duplicate"></span></a> <a class="column_delete" href="#" title="Delete"></a></div></div><div class="spb_elem_handles"><i class="fa-chevron-left"></i><i class="fa-chevron-right"></i></div><div class="icon_holder"><span class="icon-text-block"></span></div><div class="el_name_holder" data_default_name="Text Block">Text Block</div><div class="el_name_editor"><input name="el_name_editor" id="el_name_editor" type="text" value="Text Block "><a class="el-name-save" href="#" title="Save"><span class="icon-save"></span></a></div><input type="hidden" class="spb_param_value general_tab section_tab " name="general_tab" value=""><input type="hidden" class="spb_param_value element_name textfield " name="element_name" value=""><div class="spb_param_value holder title textfield " name="title"></div><input type="hidden" class="spb_param_value icon textfield " name="icon" value=""><div class="spb_param_value holder content textarea_html " name="content"><p>This is a text block. Click the edit button to change this text.</p></div><input type="hidden" class="spb_param_value tb_animation_options section " name="tb_animation_options" value=""><input type="hidden" class="spb_param_value animation dropdown " name="animation" value="none"><input type="hidden" class="spb_param_value animation_delay textfield " name="animation_delay" value="0"><input type="hidden" class="spb_param_value tb_styling_options section " name="tb_styling_options" value=""><input type="hidden" class="spb_param_value padding_vertical uislider " name="padding_vertical" value="0"><input type="hidden" class="spb_param_value padding_horizontal uislider " name="padding_horizontal" value="0"><input type="hidden" class="spb_param_value form_content textfield " name="form_content" value=""><input type="hidden" class="spb_param_value el_class textfield " name="el_class" value=""></div> <!-- end .spb_element_wrapper --></div><div class="tabs_expanded_helper"><a href="#" class="add_element"><span class="icon-add"></span>Add Element</a><a href="#" class="add_tab"><span class="icon-add-tab"></span>Add Tab</a></div><div class="container-helper"><a href="#" class="add-element-to-column btn-floating waves-effect waves-light"><span class="icon-add"></span></a></div>' );

            }

            if ( !jQuery( this ).closest( '.spb_sortable' ).hasClass( 'spb_gmaps' ) && !jQuery( this ).closest( '.spb_sortable' ).hasClass( 'spb_multilayer_parallax' ) ) {
                //Refresh the Tabs object
                jQuery( this ).parent().parent().parent().tabs( 'refresh' );
            }
                             
         
            initDroppable();
            save_spb_html();
            savePbHistory( '' , el_name , 'Added' );

            return false;
        });

        jQuery( document ).on( 'click', '#dropdown-history li a', function( e ) {

            e.preventDefault();

            if ( isTinyMceActive() !== true ) {  
                jQuery( '#content' ).val( stripslashes( jQuery(this).attr('data-revision-value') ) );
            } else {
                tinyMCE.activeEditor.setContent( stripslashes( jQuery(this).attr('data-revision-value') ) ) ;
            }

            jQuery( '#spb-empty' ).hide();
            spb_shortcodesToBuilder( true );        
  
        });

        // Accordion Add section action
        jQuery( document ).on( 'click', '.tabs_expanded_helper .add_section', function(e){
            e.preventDefault();
            
            //Find the Accordion object
            var $tabs = jQuery( this ).parent().parent().parent().parent().parent();

            //Tabs Count
            var tabs_count = $tabs.find( '.group' ).length + 1;            
            
            var tab_title = 'Section'; 
            var section_template = '<div class="group"><h3 class="ui-accordion-header ui-state-default ui-corner-all  ui-sortable-handle" role="tab" id="ui-id-9" aria-controls="ui-id-' + tabs_count + '" aria-selected="false" aria-expanded="false" tabindex="-1"><a class="title-text" href="#section-' + tabs_count + '" id="section-' + tabs_count + '">' + tab_title + ' ' + tabs_count + '</a><div class="accordion_holder"><a class="delete_tab"><span class="icon-delete"></span></a><a class="edit_tab"><span class="icon-edit"></span></a></h3><div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" id="ui-id-' + tabs_count + '" aria-labelledby="ui-id-' + tabs_count + '" role="tabpanel" style="display: block;" aria-hidden="false">';
            section_template += '<div class="row-fluid spb_column_container spb_sortable_container not-column-inherit ui-sortable ui-droppable"><div data-element_type="spb_text_block"class="spb_text_block spb_content_element spb_sortable span12 ui-resizable ui-sortable-handle  spb_first spb_last"><input type="hidden" class="spb_sc_base" name="element_name-spb_text_block" value="spb_text_block"><div class="spb_element_wrapper clearfix over_element_wrapper"><div class="spb_elem_controls" style="display: none;"><a class="column_delete" href="#" title="Delete"><span class="icon-delete"></span></a><a class="element-save" href="#" title="Save"><span class="icon-save"></span></a><a class="column_clone" href="#" title="Duplicate"><span class="icon-duplicate"></span></a><a class="column_edit" href="#" title="Edit"><span class="icon-edit"></span></a></div><div class="controls sidebar-name"> <div class="column_size_wrapper" style="display: none;"> <a class="column_decrease" href="#" title="Decrease width"></a> <span class="column_size">1/1</span> <a class="column_increase" href="#" title="Increase width"></a> </div><div class="controls_right" style="display: block;"> <a class="element-save" href="#" title="Save"><span class="icon-save"></span></a> <a class="column_edit" href="#" title="Edit"><span class="icon-edit"></span></a> <a class="column_clone" href="#" title="Clone"><span class="icon-duplicate"></span></a> <a class="column_delete" href="#" title="Delete"></a></div></div><div class="spb_elem_handles"><i class="fa-chevron-left"></i><i class="fa-chevron-right"></i></div><div class="icon_holder"><span class="icon-text-block"></span></div><div class="el_name_holder" data_default_name="Text Block">Text Block</div><div class="el_name_editor"><input name="el_name_editor" id="el_name_editor" type="text" value="Text Block "><a class="el-name-save" href="#" title="Save"><span class="icon-save"></span></a></div><input type="hidden" class="spb_param_value general_tab section_tab " name="general_tab" value=""><input type="hidden" class="spb_param_value element_name textfield " name="element_name" value=""><div class="spb_param_value holder title textfield " name="title"></div><input type="hidden" class="spb_param_value icon textfield " name="icon" value=""><div class="spb_param_value holder content textarea_html " name="content"><p> This is a text block. Click the edit button to change this text. </p></div><input type="hidden" class="spb_param_value tb_animation_options section " name="tb_animation_options" value=""><input type="hidden" class="spb_param_value animation dropdown" name="animation" value="none"><input type="hidden" class="spb_param_value animation_delay textfield " name="animation_delay" value="0"><input type="hidden" class="spb_param_value tb_styling_options section " name="tb_styling_options" value=""><input type="hidden" class="spb_param_value padding_vertical uislider " name="padding_vertical" value="0"><input type="hidden" class="spb_param_value padding_horizontal uislider " name="padding_horizontal" value="0"><input type="hidden" class="spb_param_value form_content textfield " name="form_content" value=""><input type="hidden" class="spb_param_value el_class textfield " name="el_class" value=""></div> <!-- end .spb_element_wrapper --></div> <!-- end #element-spb_text_block --><div class="tabs_expanded_helper" style="display: none;"><a href="#" class="add_element"><span class="icon-add"></span>Add Element</a><a href="#" class="add_section"><span class="icon-add-tab"></span>Add Section</a></div><div class="container-helper ui-sortable-handle" style="display: block;"><a href="#" class="add-element-to-column btn-floating waves-effect waves-light"><span class="icon-add"></span></a></div></div></div></div>';

            //Hide the expanded action container
            jQuery( this ).parent().hide();

            //Show the closed action container 
            jQuery( this ).parent().parent().find('.container-helper').show();                 

            $tabs.append( section_template );
            $tabs.accordion( "destroy" ).accordion({
                header: "> div > h3",
                autoHeight: true,
                heightStyle: "content"
            }).sortable({
                axis: "y",
                handle: "h3",
                stop: function( event, ui ) {
                    // IE doesn't register the blur when sorting
                    // so trigger focusout handlers to remove .ui-state-focus
                    ui.item.children( "h3" ).triggerHandler( "focusout" );
                    //
                    save_spb_html();
                }
            });

            initDroppable();
            save_spb_html();
            jQuery( this ).parent().parent().parent().parent().find('.title-text').click();
            savePbHistory( '' , 'Accordion Section' , 'Added' );

            return false;
        });
        
        //Accordions Delete Section Action
        jQuery( document ).on( 'click', '.spb_accordion_holder .delete_tab' , function(e) {                
            e.preventDefault();
            var modal_msg =  sfTranslatedText.attr( 'data-spb-delete-accordion-section-q1' ); 
            var modal_header_msg = sfTranslatedText.attr( 'data-spb-delete-accordion-header' );   
            var elementSaveModal = '<div class="modal_wrapper"><a class="waves-effect waves-light btn modal-trigger" href="#modal-delete"></a><div id="modal-delete-section" class="modal modal_spb"><div class="modal-content">';
            elementSaveModal += '<h4>' + modal_header_msg + '</h4><div class="row"> <div class="input-field delete-block col s12"><label>' + modal_msg + '</label>';
            elementSaveModal += '</div></div><div class="modal-footer modal-delete-section"><a href="#!" class=" modal-action spb-modal-close waves-effect modal-ok-button btn-flat">OK</a><a href="#!" class=" modal-action spb-modal-close waves-effect modal-cancel-button btn-flat">Cancel</a></div></div></div></div>';
            jQuery( '#spb' ).append( elementSaveModal );   
            jQuery('#modal-delete-section').openModal();            
            deletedElement = jQuery( this );
            return false;
        });

        //Accordions Delete Tab Confirmation
        jQuery( document ).on( 'click', '.modal-delete-section .modal-ok-button', function( e ) {          
            e.preventDefault();
            deletedElement.parent().closest( '.group' ).remove();
            Materialize.toast( sfTranslatedText.attr( 'data-spb-deleted-element' ) , 2000);
            save_spb_html();
        });

        //Saved Templates - Delete Template Confirmation
        jQuery( document ).on( 'click', '.modal-remove-template .modal-ok-button', function( e ) {          
            e.preventDefault();    
            
              var data = {
                    action: 'spb_delete_template',
                    template_id: deletedElement
                };

                jQuery.post(
                    ajaxurl, data, function( response ) {
                        jQuery( '.spb-prebuilt-pages' ).html( response );
                        spb_reorder_saved_templates();
                        Materialize.toast( sfTranslatedText.attr( 'data-spb-deleted-element' ) , 2000);
                    }
                );            

        });

        //Accordions Delete Tab Confirmation
        jQuery( document ).on( 'click', '.modal-delete-tab .modal-ok-button', function( e ) {          
            e.preventDefault();    
            var tab_pos = deletedElement.closest( 'li' ).index(),
                alt_tab_pos = tab_pos + 1;

            if ( tab_pos < 0 ) {
                return false;
            }
                    
            var deletedTabsHolder = deletedElement.parent().parent().parent();
            var tab_id = deletedTabsHolder.find( '.ui-tabs-nav li:eq(' + tab_pos + ')' ).attr( 'aria-controls' );
            deletedTabsHolder.find( '.ui-tabs-nav li:eq(' + tab_pos + ')' ).remove();
                    
            if ( deletedTabsHolder.closest( '.spb_sortable' ).hasClass( 'spb_tour' ) ) {
                    deletedTabsHolder.find( '#tab-' + alt_tab_pos ).remove();
                    deletedTabsHolder.find( '#tab-slide-' + alt_tab_pos ).remove();
            }
            else {
                    deletedTabsHolder.find( '#' + tab_id ).remove();
                    deletedTabsHolder.find( '#tab-' + tab_id ).remove();
            }
                        
            deletedTabsHolder.tabs( 'refresh' );    
            save_spb_html();
            Materialize.toast( sfTranslatedText.attr( 'data-spb-deleted-element' ) , 2000);
            jQuery( '.modal_wrapper' ).remove(); 
            return false;
        });


        //Force the focus of the click on the textfield Placeholder
        jQuery ( document ).on( 'click', '.input-field label', function () {
            if ( jQuery( this ).prev().hasClass( 'validate' ) ) {
                jQuery( this ).prev().focus();
            }
        });

        jQuery('.tooltipped').tooltip({delay: 0});
  
        jQuery( document ).on( 'click', '.right_top_section .icon-delete', function() {
            jQuery( this ).parent().parent().parent().remove();
        });
        
        jQuery( document ).on( 'click', '.pricing_column_holder .icon-delete', function() {
            jQuery( this ).parent().parent().parent().remove();
        });


        // Add new icon Section - Icon Box Grid
        jQuery( document ).on( 'click', '.spb_add_new_icon_section', function(e) {
            e.preventDefault();
            var element_count = jQuery('.section_repeater').size() +1;
            var param_line = '';
            
            param_line += '<li><div class="section_repeater"><div class="left_section"><div class="section_icon_image" style="display:none;"></div><a href="#" class="section_add_icon"><span class="icon-add"></span></a></div><div class="right_section"><div class="right_top_section">';
            param_line += '<input name="icon_title_' + element_count + '" id="icon_title_' + element_count + '" class="textfield validate active icon_title " placeholder="Icon Box Title" type="text" value="" /><span class="icon-drag-handle"></span><span class="icon-delete"></span></div>';
            param_line += '<div class="right_bottom_section"><input name="icon_link_' + element_count + '" id="icon_link_' + element_count + '" class="textfield validate active icon_link" placeholder="Icon Box Link" type="text" value="" />';
            param_line += '<select id="select_section_' + element_count + '"  class="icon_target"><option value="_self" selected>' + sfTranslatedText.attr( 'data-spb-same-window' ) + '</option><option value="_blank">' + sfTranslatedText.attr( 'data-spb-new-window' ) + '</option></select></div>';
            param_line += '</div><ul class="font-icon-grid repeater-grid svg-grid">'+ jQuery('.section_repeater').find('.font-icon-grid').first().html() + '</ul></div></li>';                        
            jQuery( '.section_repeater' ).last().parent().after(param_line);  
            jQuery( '.icon_section_holder' ).find( 'select' ).not( '.disabled' ).material_select();   
        });

        // Add new feature Row - Pricing Table
        jQuery( document ).on( 'click', '.spb_add_new_column_feature', function(e) {
            e.preventDefault();
            var element_count = jQuery('.section_repeater_features').size() +1;
            var param_line = '';

            param_line += '<li><div class="collapsible-header">' + sfTranslatedText.attr( 'data-spb-feature-name' ) + '<div class="pricing_col_actions"><span class="icon-drag-handle"></span><span class="icon-delete"></span></div></div><div class="collapsible-body"><div class="section_repeater_features">';
            param_line += '<div class="row"><div class="input-field col s12"><input name="plan_feature_name_' + element_count +  '" id="plan_feature_name_'  + element_count + '" class="textfield validate active plan_feature_name" type="text"  value="' + sfTranslatedText.attr( 'data-spb-feature-name' ) + '" /><label class="active" for="plan_feature_name_' + element_count + '">' + sfTranslatedText.attr( 'data-spb-feature-name' ) + '</label></div></div>';
            param_line += '<div class="row"><div class="input-field col s12"><input name="plan_feature_name_el_class_' + element_count + '" id="plan_feature_name_el_class_' + element_count + '" class="textfield validate active feature_el_class" type="text"  value="" /><label for="plan_feature_name_el_class_' + element_count + '">' + sfTranslatedText.attr( 'data-spb-extra-class' ) + '</label></div></div>';
            param_line += ' </div></div></li>';
                                     
            //jQuery( '.section_repeater_features' ).last().parent().parent().after( param_line );  
            jQuery(this).parent().parent().find('.pricing_column_feature_holder').append( param_line );
            jQuery('.collapsible').collapsible( { accordion : false  });
        });

        // Add new pricing column - Pricing Table
        jQuery( document ).on( 'click', '.spb_add_new_pricing_column', function(e) {
            e.preventDefault();
            var element_count = jQuery('.section_repeater').size() +1;
            var param_line = '';
            var param_feature_line = '';

            //Default Feature Row 
            param_feature_line  += '<ul class="collapsible pricing_column_feature_holder" data-collapsible="accordion">';  
            param_feature_line  += '<li><div class="collapsible-header">' + sfTranslatedText.attr( 'data-spb-feature-name' ) + '<div class="pricing_col_actions"><span class="icon-drag-handle"></span><span class="icon-delete"></span></div></div><div class="collapsible-body"><div class="section_repeater_features">';
            param_feature_line  += '<div class="row"><div class="input-field col s12"><input name="plan_feature_name_' + element_count + '_1" id="plan_feature_name_' + element_count +'_1" class="textfield validate active plan_feature_name" type="text"  value="' + sfTranslatedText.attr( 'data-spb-feature-name' ) + '" /><label class="active" for="plan_feature_name_' + element_count + '_1">' + sfTranslatedText.attr( 'data-spb-feature-name' ) + '</label></div></div>';
            //param_feature_line  += '<div class="row"><div class="input-field col s12"><input name="bg_color_feature' + element_count + '_1" id="bg_color_feature' + element_count + '_1" class="textfield validate active bg_color_feature" type="text"  value="" /><label class="active" for="bg_color_feature' + element_count + '_1">' + sfTranslatedText.attr( 'data-spb-bg-color' ) + '</label></div></div>';
            //param_feature_line  += '<div class="row"><div class="input-field col s12"><input name="text_color_feature' + element_count + '_1" id="text_color_feature' + element_count + '_1" class="textfield validate active text_color_feature" type="text"  value="" /><label class="active" for="text_color_feature' + element_count + '_1">' + sfTranslatedText.attr( 'data-spb-text-color' ) + '</label></div></div>';
            param_feature_line  += '<div class="row"><div class="input-field col s12"><input name="plan_feature_name_el_class_' + element_count + '_1" id="plan_feature_name_el_class_' + element_count + '_1" class="textfield validate active feature_el_class" type="text"  value="" /><label class="active" for="plan_feature_name_el_class_' + element_count + '_1">' + sfTranslatedText.attr( 'data-spb-extra-class' ) + '</label></div></div>';
            param_feature_line  += '</div></div></li></ul>';
            param_feature_line  += '<div class="bottom_action"><a href="#" class="spb_add_new_column_feature">' + sfTranslatedText.attr( 'data-spb-add-feature' ) + '</a></div>';            

            param_line += '<li><div class="collapsible-header"> ' + sfTranslatedText.attr( 'data-spb-new-plan' ) + '<div class="pricing_col_actions"><span class="icon-drag-handle"></span><span class="icon-delete"></span></div></div><div class="collapsible-body">';
            param_line += '<div class="section_repeater">';
            param_line += '<div class="price_column_holder">';
            param_line += '<div class="row"><div class="input-field col s12"><input name="plan_name_' + element_count +  '" id="plan_name_' + element_count +  '" class="textfield validate active plan_name" type="text"  value="' + sfTranslatedText.attr( 'data-spb-new-plan' ) + '" /><label class="active" for="plan_name_' + element_count +  '">' + sfTranslatedText.attr( 'data-spb-plan-name' ) + '</label></div></div>';
            param_line += '<div class="row"><div class="input-field col s12 plan_highlight_column"><select id="select_highlight_column_' + element_count +  '" ><option value="no" selected>' + sfTranslatedText.attr( 'data-spb-no' ) + '</option><option value="yes">' + sfTranslatedText.attr( 'data-spb-yes' ) + '</option></select><label for="select_highlight_column_' + element_count +  '">' + sfTranslatedText.attr( 'data-spb-highlight-column' ) + '</label></div></div>';
            param_line += '<div class="row"><div class="input-field col s12"><input name="plan_price_' + element_count +  '" id="plan_price_' + element_count +  '" class="textfield validate active plan_price" type="text" value="" /><label for="plan_price_' + element_count +  '">' + sfTranslatedText.attr( 'data-spb-plan-price' ) + '</label></div></div>';
            param_line += '<div class="row"><div class="input-field col s12"><input name="plan_period_' + element_count +  '" id="plan_period_' + element_count +  '" class="textfield validate active plan_period" type="text" value="" /><label for="plan_period_' + element_count +  '">' + sfTranslatedText.attr( 'data-spb-plan-period' ) + '</label></div></div>';
            param_line += '<div class="features_row_holder"><label>' + sfTranslatedText.attr( 'data-spb-features' ) + '</label>' + param_feature_line + '</div>';
            param_line += '<div class="row"><div class="input-field col s12"><input name="plan_button_text_' + element_count +  '" id="plan_button_text_' + element_count +  '" class="textfield validate active plan_button_text" type="text" value="" /><label for="plan_button_text_' + element_count +  '">' + sfTranslatedText.attr( 'data-spb-btn-text' ) + '</label></div></div>';
            param_line += '<div class="row"><div class="input-field col s12"><input name="plan_link_url_' + element_count +  '" id="plan_link_url_' + element_count +  '" class="textfield validate active plan_link_url" type="text" value="" /><label for="plan_link_url_' + element_count +  '">' + sfTranslatedText.attr( 'data-spb-link-url' ) + '</label></div></div>';
            param_line += '<div class="row"><div class="input-field col s12 plan_link_target"> <select id="select_link_target_' + element_count +  '"><option value="_self" selected>' + sfTranslatedText.attr( 'data-spb-same-window' ) + '</option><option value="_blank" >' + sfTranslatedText.attr( 'data-spb-new-window' ) + '</option></select><label for="select_link_target_' + element_count +  '">' + sfTranslatedText.attr( 'data-spb-link-target' ) + '</label></div></div>';
            param_line += '<div class="row"><div class="input-field col s12"><input name="plan_extra_class_' + element_count +  '" id="plan_extra_class_' + element_count +  '" class="textfield validate active plan_extra_class" type="text" value="" /><label for="plan_extra_class_' + element_count +  '">' + sfTranslatedText.attr( 'data-spb-extra-class' ) + '</label></div></div>';
            param_line += '</div></div></div></li>';

            jQuery( '.section_repeater' ).last().parent().parent().after(param_line);
            jQuery( '.pricing_column_holder' ).find( 'select' ).not( '.disabled' ).material_select();   
            jQuery('.collapsible').collapsible( { accordion : false  });
        });

        //set the focus on repeater input fields
        jQuery ( document ).on( 'click', '.section_repeater input', function () {
            jQuery( this ).focus();
        });

        jQuery('.dropdown-history').dropdown({
            inDuration: 300,
            outDuration: 225,
            constrain_width: false, // Does not change width of dropdown to that of the activator
            hover: true, // Activate on hover
            gutter: 0, // Spacing from edge
            belowOrigin: true, // Displays dropdown below the button
            alignment: 'left' // Displays dropdown with edge aligned to the left of button
        });
        
        $('.tooltipped').tooltip({delay: 0});


        // Detect Changes in Asset Modal Form
        jQuery( "body" ).on('change', '#spb_edit_form select, #spb_edit_form radio, #spb_edit_form input[type=checkbox], #spb_edit_form input[type=hidden], .spb-buttonset',function() {

            if ( jQuery(this).hasClass('spb-buttonset') ){
                var new_radio_value = jQuery( this ).parent().find(':radio:checked').data('id');
                jQuery(this).find('.buttonset').val(new_radio_value);
            }

            check_form_dependency_fields();
        });


        jQuery( document ).on( 'hover', '.spb-content-elements li', function(){
            jQuery(this).addClass('z-depth-4');
        });

        jQuery( document ).on( 'mouseout', '.spb-content-elements li', function(){
            jQuery(this).removeClass('z-depth-4'); 
        });

        jQuery('.sf-close-delete-file').live('click', function(e) {
            e.preventDefault();
            jQuery(this).parent().parent().remove();
            jQuery('.sf_gallery_widget_attached_images_ids').val("");

            return false;
        });

        // Append modal tabs
        jQuery( 'body' ).append( '<div class="spb-modal-tabs"></div>' );
        
        // Range field slider
        jQuery( document ).on("change",  '.range-field .uislider', function() {        
            jQuery( '#' + jQuery(this).attr('name') + '_val' ).val(jQuery( this ).val());
            var changedInput = jQuery(this);
            if (parseInt( jQuery( this ).val() ) > parseInt( changedInput.attr('max') ) ){
                changedInput.prev().width('100%');
                jQuery( this ).val( changedInput.attr('max') );
            }else{
                changedInput.prev().width(''+ jQuery( this ).val() / changedInput.attr('max') * 100 + '%');
            }
        });
       
        // Range field slider input
        jQuery( document ).on('keydown', '.uisliderinput', function(e) {  
            var changedInput = jQuery(this).parent().prev().find('input');    
            if (e.which == 13 || e.keyCode == 13) {
                e.preventDefault();
                if ( parseInt( jQuery( this ).val() ) > parseInt( changedInput.attr('max') ) ) {
                    changedInput.prev().width('100%');
                    changedInput.val( changedInput.attr('max') );
                    jQuery(this).val( changedInput.attr('max') );
                } else {
                    changedInput.prev().width(''+ jQuery( this ).val() / changedInput.attr('max') * 100 + '%');
                    jQuery( this ).parent().prev().find('input').val( jQuery( this ).val() );
                }  
            
               jQuery('.spb_edit_form_elements .custom-padding-top').val( jQuery( '#padding_vertical' ).val() );
               jQuery('.spb_edit_form_elements .custom-padding-bottom').val( jQuery( '#padding_vertical' ).val() );                       
               jQuery('.spb_edit_form_elements .custom-padding-left').val( jQuery( '#padding_horizontal' ).val() );
               jQuery('.spb_edit_form_elements .custom-padding-right').val( jQuery( '#padding_horizontal' ).val() );     
               jQuery('.spb_edit_form_elements .custom-margin-top').val( jQuery( '#margin_vertical' ).val() );                
               jQuery('.spb_edit_form_elements .custom-margin-bottom').val( jQuery( '#margin_vertical' ).val() );   

            }
        });

        
         $( document ).on( 'click', '.tab_features_row', function() {
                jQuery( this ).addClass( 'active' );
                jQuery( this ).prev().removeClass( 'active' );
                jQuery( this ).parent().parent().find('.features_row_holder').show();
                jQuery( this ).parent().parent().find('.section_repeater').hide();

         });

        $( document ).on( 'click', '.tab_price_column', function() {
                jQuery( this ).addClass( 'active' );
                jQuery( this ).next().removeClass( 'active' );
                jQuery( this ).parent().parent().find('.features_row_holder').hide();
                jQuery( this ).parent().parent().find('.section_repeater').show();

         });
         

        // Range field slider input focus
        $( document ).on( 'focusin', '.uisliderinput', function( e ) {
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
    
        $( document ).on( 'mouseover', '.spb_accordion',  function() {
            $( this ).find('.spb_accordion_controls').fadeIn();  
        });
        
        $( document ).on( 'mouseleave', '.spb_accordion',  function() {
            $( this ).find('.spb_accordion_controls').fadeOut();  
        });

        $( document ).on( 'mouseover', '.spb_tabs, .spb_tour, .spb_gmaps, .spb_multilayer_parallax',  function() {
            $( this ).find('.spb_tab_controls').fadeIn();  
        });
        
        $( document ).on( 'mouseleave', '.spb_tabs, .spb_tour, .spb_gmaps, .spb_multilayer_parallax',  function() {
            $( this ).find('.spb_tab_controls').fadeOut();  
        });

        $( document ).on( 'mouseover', '.spb-content-elements-saved .spb_elements_li', function () {
            $( this ).find( '.spb_remove_element' ).show();
        });

        $( document ).on( 'mouseleave', '.spb-content-elements-saved .spb_elements_li', function () {
            $( this ).find( '.spb_remove_element' ).hide();
        });

        $( document ).on( 'mouseenter', '.spb_content_element:not(.spb_tabs, .spb_accordion, .spb_gmaps, .spb_multilayer_parallax, .spb_tour)',  function() {
            var element_obj = $(this); 
            
            setTimeout(function(){
                 if (element_obj.is(':hover')) {
                     element_obj.find('.spb_elem_controls').fadeIn();
                 }
            }, 100);
                    
            return false;         
        });

        $( document ).on( 'mouseleave', '.spb_content_element',  function( e ) {
            $(this).find('.spb_elem_controls').hide();
            
            if ( $(this).find( '.el_name_editor').css( 'display' ) == 'block' ){
                $(this).find( '.el_name_editor').hide();
                 $(this).find( '.el_name_editor').prev().show();
            }
            e.stopImmediatePropagation();
        });

        $( document ).on( 'click', '.el_name_holder',  function() {
            $(this).parent().find('.spb_elem_controls').fadeOut();
            var input_el = $(this).next().find('#el_name_editor').first();    
            $(this).hide();  
            $(this).next().show();
            input_el.focus();

        });

        $( document ).on( 'click', '.el-name-save',  function( e ) {
            
            var temp_name_holder;

            e.preventDefault();

            if ( $(this).parent().prev().hasClass( 'el_name_holder' ) ) {
                temp_name_holder = $(this).parent();
            } else {
                temp_name_holder = $(this).parent().parent();
            }

            temp_name_holder.prev().html( $(this).prev().val() );
            temp_name_holder.parent().parent().find('.element_name').first().val( $(this).prev().val() );
            temp_name_holder.prev().show();
            temp_name_holder.hide();
            save_spb_html();

        });
  
        //Save element name on enter
        $( document ).keypress(  function(e) {
                    
             if( e.which == 13 && jQuery(e.target).attr('id') == 'el_name_editor' ) {
                 e.preventDefault();
                 $( '.el-name-save' ).trigger( 'click' );
             }

        });

        $( document ).on( 'click', '#el_name_editor',  function( e ) {    
            e.preventDefault();
        });
        
        $( document ).on( 'click', '.tab_closing',  function() {
            cleaningAfterClosingElementsModal();
            return false;
        });

        $( document ).on( 'click', '#cancel-small-form-background', function() {
            jQuery( '.spb-modal-tabs' ).html( '' );
            jQuery( '.spb-modal-tabs' ).hide();
            jQuery( '#spb_edit_form' ).hide();
            jQuery( 'body' ).css( 'overflow', '' );

            return false;
        });

        $( document ).on( 'click', '#save-small-form', function() {
            saveSmallFormEditing();
            return false;
        });

        // Control Elements Tabs Menu        
         $( '.spb_most_used, .spb_ui_tab, .spb_med_tab, .spb_layout_tab, .spb_misc_tab, .spb_search_tab, .spb_saved_el_tab, .spb_saved_pages_el_tab').on('click', function() {
            $( '#sf_search_elements' ).val('');
            $( '.hide-modal_elements' ).removeClass( 'hide-modal_elements' );
            $( '.spb_elements_tabs_header' ).find( '.active_tab' ).removeClass( 'active_tab' );
            $(this).addClass( 'active_tab' );
            
            if ( !jQuery( this ).hasClass('spb_saved_el_tab') ){
                $( '.spb-content-elements' ).show();    
            }     

            //Media Tab action
            if ( jQuery( this ).hasClass('spb_med_tab') ){ 
                $('.spb-item-slideout li').css('display', 'none');
                $('.spb_tab_media_nav').parent().css('display', 'inline-block');
                $('.spb-content-elements').fadeIn( 300 );
            }

            // Search Tab action
            if ( jQuery( this ).hasClass('spb_search_tab') ) { 
                $('.spb-item-slideout li').css('display', 'inline-block');
                //$('.spb-content-elements-search').fadeIn( 300 );
                $('.spb-content-elements').fadeIn( 300 );
            }

            //Saved Template Pages Tab action
            if ( jQuery( this ).hasClass('spb_saved_pages_el_tab') ){                
                $( '.spb-item-slideout' ).css('display', 'none');
                $( '.spb-prebuilt-pages .spb_template_li' ).css('display', 'inline-block');
                $( '.spb-prebuilt-pages .sf_prebuilt_template' ).css('display', 'inline-block');
                $( '.spb-prebuilt-pages' ).fadeIn( 300 );        
            }

            //UI Tab action
            if ( jQuery( this ).hasClass('spb_ui_tab') ){   
                $('.spb-item-slideout li').css('display', 'none');
                $('.spb_tab_ui_nav').parent().css('display', 'inline-block');
                $('.spb-content-elements-ui').fadeIn( 300 );
            }

            //Layout Tab action
            if ( jQuery( this ).hasClass('spb_layout_tab') ){                
                $('.spb-item-slideout li').css('display', 'none');
                $('.spb_tab_layout_nav').parent().css('display', 'inline-block');
                $('.spb-content-elements-layout').fadeIn( 300 );
            }

            //Saved Elements Tab action
            if ( jQuery( this ).hasClass('spb_saved_el_tab') ) {                
                $( '.spb-item-slideout').css('display', 'none');               
                $( '.spb-item-slideout li' ).css('display', 'none');
                $( '.spb-content-elements' ).css('display', 'none');
                $( '.spb-content-elements-saved .spb_elements_li' ).css('display', 'inline-block');
                $( '.spb-content-elements-saved' ).fadeIn( 300 );        
            }     

            //Misc Tab action
            if ( jQuery( this ).hasClass('spb_misc_tab') ) {                
                $('.spb-item-slideout li').css('display', 'none');
                $('.spb_tab_misc_nav').parent().css('display', 'inline-block');
                $('.spb-content-elements-misc').fadeIn( 300 );
            } 

            //Most used
            if ( jQuery( this ).hasClass('spb_most_used') ) {     
                $('.spb-content-elements').css('display', 'none');
                $('.spb-item-slideout li').css('display', 'none');
                $('.spb-most-used-elements li').css('display', 'inline-block');
                $('.spb-most-used-elements').fadeIn( 300 );
            }

        }); 

        $( document ).on( 'click', '.spb_element_section_tab', function() {
            jQuery( '.spb_element_section_tab' ).removeClass('active_tab');
            jQuery( this ).addClass('active_tab');
            jQuery( '.element_tab_content').hide();
            jQuery( '.' + jQuery( this ).attr('data-content-name') ).show();
        });       

        $( document ).on( 'click', '#sf_directory_calculate_coordinates', function( e ) {                
            e.preventDefault();
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({'address': jQuery( '#sf_directory_address' ).val()}, function( results ) {
                    jQuery( '#sf_directory_lat_coord' ).val( results[0].geometry.location.lat() );
                    jQuery( '#sf_directory_lng_coord' ).val( results[0].geometry.location.lng() );
                }
            );
        });

        $( document ).on( 'mouseenter', '.spb_sortable',  function( e ) {
            e.preventDefault();
            if ( !$( 'body' ).hasClass( 'startedDragging' ) ) {
                $( this ).find( '.controls_right:first' ).fadeIn( 100 );
                $( this ).find( '.spb_element_wrapper' ).addClass( 'over_element_wrapper' );
            }
        });

        $( document ).on( 'mouseleave', '.spb_sortable', function( e ) {
            e.preventDefault();
            $( this ).find( '.controls_right:first , .column_size_wrapper:first' ).hide();
            $( '.spb_map_pin .controls_right , .spb_map_pin .column_size_wrapper' ).show();
            $( this ).removeClass( 'over_element_wrapper' );
        });


        $( document ).on( 'click', '#close-fullscreen-preview',  function( e ) {
            e.preventDefault();
            jQuery( 'body' ).css( 'overflow', '' );
            $( '#spb_edit_form' ).html( '' ).fadeOut();                
        });


        $( "#previewpage-spb" ).click( function( e ) {
            e.preventDefault();

            if ( !jQuery( '.spb_main_sortable > div' ).length ) {
                alert( "Please add some content before previewing the page." );
            } else {

                jQuery( 'body' ).css( 'overflow', 'hidden' );
                jQuery( '#spb_edit_form' ).html( '<div class="spb_preview_fmodal"><div class="spb_preview_fmodal_top_bar"><span class="spb_smodal_header_text">Previewing Page</span><a href="#" id="close-fullscreen-preview" style="float:right;color: #FFFEFE;padding: 15px;">Close Preview</a></div><div class="spb_preview_lockdiv"></div><div class="spb_preview_fmodal_content"><div class="spinnerholder"><div class="spinnermessage"><h4>' + jQuery( '#spb_loading' ).val() + '</h4></div><div class="importspinner">  <div class="bounce1"></div> <div class="bounce2"></div> <div class="bounce3"></div> </div></div></div></div>' ).show().css( {"padding-top": 60} );
                jQuery( '.spb_preview_fmodal_content' ).load( $( "#post-preview" ).attr( 'href' ) );

            }
        });

        $( document ).on( 'click' , '#sf_search_elements',  function() {
            $( '.spb_search_tab' ).trigger( 'click' );
        });

        $( document ).on('input', '#sf_search_saved_elements',  function() {

            var foundResults = false;
            var parNameObj;
            
            if ( $( '.page_builder_saved_elements .spb_saved_el_tab' ).hasClass( 'active_tab' ) ){

                parNameObj = '.spb-content-elements-saved';    
            } else {

                parNameObj = '.spb-prebuilt-pages';    
            }

            if ( $( this ).val().length > 1 ) {

                var str = $( this ).val();
                str = str.toLowerCase().replace(
                    /\b[a-z]/g, function( letter ) {
                        return letter.toUpperCase();
                    } 
                );

                var txAux = str.toLowerCase(); 
                
                $( parNameObj + ' > li' ).find( 'a' ).each(
                    function() {

                        if ( $( this ).html().toLowerCase().indexOf( txAux ) < 0 ) {
                            $( this ).parent().addClass( 'hide-modal_elements' );
                        } else {
                            $( this ).parent().removeClass( 'hide-modal_elements');
                            foundResults = true;
                        }

                    }
                );
            } else {
                $( parNameObj + ' > li' ).find( 'a' ).parent().removeClass( 'hide-modal_elements' );
            }

            if ( $( this ).val() === '' || !foundResults ) {
                $( parNameObj + ' > li' ).find( 'a' ).parent().removeClass( 'hide-modal_elements' );
            }

        });

        $( document ).on('input', '#sf_search_elements',  function() {

            var foundResults = false;

            if ( $( this ).val().length > 1 ) {

                var str = $( this ).val();
                str = str.toLowerCase().replace(
                    /\b[a-z]/g, function( letter ) {
                        return letter.toUpperCase();
                    } 
                );

                var txAux = str; 
                
                $( '.spb-content-elements > li' ).find( 'a' ).each(
                    function() {

                        if ( $( this ).html().indexOf( txAux ) < 0 ) {
                            $( this ).parent().addClass( 'hide-modal_elements' );
                        } else {
                            $( this ).parent().removeClass( 'hide-modal_elements');
                            foundResults = true;
                            jQuery( '.spb-elements-no-results' ).hide();
                        }

                    }
                );
            } else {
                $( '.spb-content-elements > li' ).find( 'a' ).parent().removeClass( 'hide-modal_elements' );
                jQuery( '.spb-elements-no-results' ).hide();
            }

            if ( $( this ).val() === '' || !foundResults ) {
                $( '.spb-content-elements > li' ).find( 'a' ).parent().removeClass( 'hide-modal_elements' );
                jQuery( '.spb-elements-no-results' ).hide();
            }

             if ( $( this ).val() !== '' &&  $( this ).val().length >= 3  && !foundResults ) {
                $( '.spb-content-elements > li' ).find( 'a' ).parent().addClass( 'hide-modal_elements' );
                jQuery( '.spb-elements-no-results' ).show();

            }

        });

        $( document ).on('input', '.asset-auto-complete',  function() {

                var foundResults = false;

                if ( $( this ).val().length > 1 ) {

                    var str = $( this ).val();
                    str = str.toLowerCase().replace(
                        /\b[a-z]/g, function( letter ) {
                            return letter.toUpperCase();
                        }
                    );

                    var txAux = str;

                    $( '.spb-content-elements > li' ).find( 'a' ).each( function() {
                        if ( $( this ).html().indexOf( txAux ) < 0 )
                            $( this ).hide();
                        else {
                            $( this ).show();
                            foundResults = true;
                        }
                    });
                } else {
                    $( '.spb-content-elements > li' ).find( 'a' ).show();
                }

                if ( $( this ).val() === '' || !foundResults ) {
                    $( '.spb-content-elements > li' ).find( 'a' ).show();
                }

            }
        );

        $( document ).on('input', '.search-icon-grid',  function() {
            if ( $(this).val().length > 1 ) {
                var str = $(this).val();
                str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                     return letter.toUpperCase();
                });

                var txAux = str.toLowerCase();
                var foundResults = false;

                $('.font-icon-grid > li').find('span').each(function() {

                    if ($(this).html().indexOf(txAux) < 0) {
                        $(this).parent().hide();
                    } else {
                        $(this).parent().show();
                        foundResults = true;
                    }

                });
            } else {
                $('.font-icon-grid > li').show();
            }
        });

        
        $( "#spb .dropable_el, #spb .dropable_column" ).draggable({
                helper: function() {
                    return $( '<div id="drag_placeholder">' + $( this ).html() + '</div>' ).appendTo( 'body' );
                },
                zIndex: 999999999999,
                //cursorAt: { right: 1, top : 15 },
                //cursor: "pointer",
                revert: "invalid",
                distance: 2,
                start: function() {  

                    cleaningAfterClosingElementsModal();                    
                    jQuery( 'body' ).addClass( 'startedDragging' );
                    jQuery( '.metabox-builder-content' ).removeClass( 'empty-builder' );
                    jQuery( '.spb-item-slideout' ).hide();
                    jQuery( '.dropdown-toggle' ).removeClass( 'selected' );
                    jQuery( '.dropdown' ).removeClass( 'open' );

                    $( '.main_wrapper' ).prepend( '<div class="newtop_sortable_element spb_first spb_last"><div class="new_element_col1"><div class="new_element_plus_sign">+</div></div><div class="new_element_col2"> <div class="newtop_element_text"> </div></div></div>' );

                    //New code for droppable areas between elements
                    var elementpos = 0;
                    $( '.sortable_1st_level' ).each( function() {
                        if ( $( this ).parent().hasClass( 'spb_main_sortable' ) && $( this ).hasClass( 'spb_last' ) ) {
                            elementpos++;
                            $( this ).after( '<div class="new_sortable_element spb_first spb_last" datael-position="' + elementpos + '"><div class="new_element_col1"><div class="new_element_plus_sign">+</div></div><div class="new_element_col2"> <div class="newtop_element_text"> </div></div></div>' );
                        }
                    });

                    var rowtop = 1;
                    $( '.spb_row .spb_column_container' ).each( function() {
                        jQuery( this ).prepend( '<div datael-position="1" datael-class="row_top_' + rowtop + '" class="newrowtop_sortable_element spb_first spb_last"><div class="new_element_col1"><div class="new_element_plus_sign">+</div></div><div class="new_element_col2"> <div class="newtop_element_text"></div></div></div>' );
                        jQuery( this ).addClass( 'row_top_' + rowtop );
                        rowtop++;
                    });

                    elementpos = 0;

                    $( '.spb_row .spb_sortable' ).each( function() {
                        if ( $( this ).parent().hasClass( 'spb_column_container' ) && $( this ).hasClass( 'spb_last' ) ) {
                            elementpos++;
                            $( this ).addClass( 'spb_row_' + elementpos );
                            $( this ).after( '<div class="newrow_sortable_element spb_first spb_last" datael-position="' + elementpos + '"><div class="new_element_col1"><div class="new_element_plus_sign">+</div></div><div class="new_element_col2"> <div class="newtop_element_text"> </div></div></div>' );

                        }
                    });                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      

                    initDroppable();

                },
                stop: function() {
                    jQuery( 'body' ).removeClass( 'startedDragging' );
                    //Hide new Drop Areas
                    jQuery( '.newtop_sortable_element' ).remove();
                    jQuery( '.new_sortable_element' ).remove();
                    jQuery( '.newrowtop_sortable_element' ).remove();
                    jQuery( '.newrow_sortable_element' ).remove();

                }

            }
        );
        initDroppable();

        /* Make menu elements dropable */

        $( '.dropdown-toggle' ).on( 'click', function( e ) {

                e.preventDefault();

                $( '.dropdown-toggle' ).removeClass( 'selected' );
                $( '.spb-elements-no-results' ).hide();

                if ( !$( this ).hasClass( 'spb_custom_elements' ) ) {
                    
                    $('.spb_tab_media_nav').parent().show();
                    $( ' .spb-item-slideout').hide();
                    $( '.page_builder_saved_elements').hide();
                    $( '.page_builder_elements_header').show();
                    $( '.spb_elements_tabs_header' ).show();
                    $( '.spb_search_tab').addClass('active_tab');
                    $( '#spb_edit_form').show().css({ opacity: 0.8 });
                    $( 'body').css({ overflow: "hidden" });
                    $( '#spb-option-slideout').fadeIn();
                    $( '.spb-content-elements.spb-item-slideout').show();
                    $( '.spb-prebuilt-pages-fullscreen' ).removeClass( 'selected' );
                    $( '.spb-content-elements' ).removeClass( 'selected' );
                    $( '.spb-item-slideout' ).removeClass( 'selected' );

                }else{

                    $( '.page_builder_saved_elements .spb_saved_el_tab').click();
                    $( '.page_builder_saved_elements').show();
                    $( '.spb_elements_tabs_header' ).show();
                    $( '.page_builder_elements_header').hide();
                    $( '#spb_edit_form').show().css({ opacity: 0.8 });
                    $( 'body').css({ overflow: "hidden" });
                    $( '#spb-option-slideout').fadeIn();
                
                 
                }

                if ( $( this ).hasClass( 'spb_prebuilt_pages' ) ) {

                    if ( $( '.spb-prebuilt-pages' ).hasClass( 'selected' ) ) {
                       
                        $( '.spb-item-slideout' ).removeClass( 'selected' );
                        return;
                    }

                }

                if ( $( this ).hasClass( 'spb_prebuilt_pages' ) ) {

                    $( '.spb-item-slideout' ).removeClass( 'selected' );
                    if ( !$( 'body' ).hasClass( 'spb-fullscreen-mode' ) ) {
                        $( '.spb-content-elements' ).removeClass( 'selected' );
                        $( '.spb-content-elements' ).hide();
                    }
                }

                if ( $( this ).hasClass( 'spb_content_elements' ) ) {
                    $( '.spb-prebuilt-pages' ).removeClass( 'selected' );
                    $( '.spb-prebuilt-pages' ).hide();
                    $( '.spb_columns' ).parent().removeClass( 'open' );


                }

                if ( $( this ).hasClass( 'spb-prebuilt-pages' ) ) {
                    $( '.spb-content-elements' ).removeClass( 'selected' );
                    $( '.spb-content-elements' ).hide();

                }

                if ( $( '#spb-elements' ).hasClass( 'subnav-fixed' ) || $( this ).hasClass( 'spb_templates' ) || $( this ).hasClass( 'spb_custom_elements' ) || $( this ).hasClass( 'spb_columns' ) ) {

                    
                    if ( $( this ).parent().hasClass( 'open' ) ) {
                        $( this ).parent().removeClass( 'open' );
                        return;
                    } else {
                        $( '#spb-elements .dropdown' ).removeClass( 'open' );
                        $( this ).parent().addClass( 'open' );
                    }

                } else {

                    $( '.dropdown' ).removeClass( 'open' );

                    var slideOutWrap = $( '#spb-option-slideout' );
                    var thisSlideout = $( this ).attr( 'data-slideout' );
                    var thisSlideoutElement = $( '.' + thisSlideout );

                    
                    if ( $( this ).hasClass( 'spb-prebuilt-pages-fullscreen' ) )
                        slideOutWrap = $( '.spb-prebuilt-pages-fullscreen' );

                    if ( thisSlideoutElement.hasClass( 'selected' ) ) {
                        $( '.spb-item-slideout' ).removeClass( 'selected' );
                        $( '.spb-prebuilt-pages-fullscreen' ).removeClass( 'selected' );

                        return;
                    }

                    $( this ).addClass( 'selected' );


                }

            }
        );

        $('#publish').click(function() {
            $('#spb_content').html("");
        });

        $( ".clickable_layout_action" ).click( function( ) {

                $( '.dropdown-toggle' ).removeClass( 'selected' );
                $( '.dropdown' ).removeClass( 'open' );

            }
        );

        /* Add action for menu buttons with 'clickable_action' class name */
        $( "#spb-elements .clickable_action" ).click(
            function( e ) {
                e.preventDefault();

                if ( currentAsset === "" ) {
                    currentAsset = $( '.main_wrapper' );
                }

                if ( (jQuery( this ).hasClass( 'spb_accordion_nav' ) && currentAsset.parents( '.spb_tabs' ).length > 0) || (jQuery( this ).hasClass( 'spb_tabs_nav' ) && currentAsset.parents( '.spb_accordion' ).length > 0 ) ) {
                    currentAsset = "";
                    close_elements_dropdown();
                } else {
                    getElementMarkup( currentAsset, $( this ), "initDroppable" );
                    currentAsset = "";
                    close_elements_dropdown();
                }

            }
        );

        $( ".spb-content-elements .clickable_action, .spb-most-used-elements  .clickable_action" ).click(
            function( e ) {
                var new_added_el = jQuery( this );
                e.preventDefault();

                if ( currentAsset === "" ) {
                    currentAsset = $( '.main_wrapper' );
                }
                
                getElementMarkup( currentAsset, $( this ), "initDroppable" );
                currentAsset = "";
                jQuery( '.tab_closing .icon-close' ).trigger( "click" );
                save_spb_html();

                 setTimeout(function(){
                    savePbHistory( new_added_el.attr('data-element') , new_added_el.find('p').text(), 'Added' );
                    trackMostusedElements( new_added_el.attr('data-element') );
                }, 2000);

         
            }
        );

        $( "#spb-elements .clickable_layout_action" ).click( function( e ) {
                e.preventDefault();
                getElementMarkup( $( '.main_wrapper' ), $( this ), "initDroppable" );

            }
        );

        $( document ).on( 'click', '.spb_tabs_holder .add_element, .spb_accordion_holder .add_element', function(e){

            e.preventDefault(); 
            currentAsset = $( this ).parent().parent();
            open_elements_dropdown();
            $( this ).parent().hide();
            $( this ).parent().parent().find('.container-helper').show();

        });

        $( document ).on( 'click', '.add-element-to-column',  function( e ) {
                
                e.preventDefault(); 

                if ( $( this ).parent().parent().parent().hasClass('spb_tabs_holder') || $( this ).parent().parent().parent().hasClass('spb_accordion_helper') ) {
                    
                    $( this ).parent().fadeOut();  
                    $( this ).parent().parent().find('.tabs_expanded_helper').fadeIn();

                } else if ( $( this ).parent().parent().parent().parent().parent().hasClass( 'spb_accordion_holder' ) ) {
                      
                    $( this ).parent().hide();  
                    $( this ).parent().parent().find('.tabs_expanded_helper').show();

                } else {

                    currentAsset = $( this ).parent().parent();
                    open_elements_dropdown();

                }
            }
        );

        columnControls();
        /* Set action for column sizes and delete buttons */

        if ( $( "#spb" ).length == 1 ) {
            

            $( 'div#postdivrich' ).before( '<p class="page-builder-switch"><a class="spb_switch-to-builder button-primary" style="display:none;" href="#">Use Page Builder</a></p>' );
            
            $( '#spb' ).children().first().append('<span class="icon-expand-less pb-main-icon"></span><span class="icon-expand-more pb-main-icon"></span>');
            $( '#spb h2' ).first().prepend('<span class="icon-icon-page-builder pb-main-icon"></span>');

            var spb_color_scheme = $('#spb-elements').attr('data-color-scheme');
            jQuery( 'body' ).addClass(spb_color_scheme);

            var postdivrich = $( '#postdivrich' ),
                swiftPageBuilder = $( '#spb' ),
                pageVars = getURLVars();

            $( '.spb_switch-to-builder' ).click(
                function( e ) {
                    e.preventDefault();
                    if ( postdivrich.is( ":visible" ) ) {

                        if ( !isTinyMceActive() ) {
                            if ( switchEditors !== undefined )  $( '#content-tmce' ).get( 0 ).click();
                        }
                        postdivrich.hide();
                        swiftPageBuilder.show();
                        $( '#spb_status' ).val( "true" );
                        $( this ).html( 'Use Default editor' ).addClass('default');

                        spb_shortcodesToBuilder( false );
                        spb_navOnScroll();

                        // } else {
                        //  alert("Please switch default WordPress editor to 'Visual' mode first.");
                        // }
                    }
                    else {
                        jQuery.swift_page_builder.save_spb_html();
                        postdivrich.show();
                        swiftPageBuilder.hide();
                        $( '#spb_status' ).val( "false" );
                        $(window).trigger('resize');
                        $( this ).html( 'Use Page Builder' ).removeClass('default');

                    }
                }
            );

            if ( pageVars.spb_enabled && $( '#spb_status' ).val() === "false" ) {
                $( '.spb_switch-to-builder' ).trigger( 'click' );
            }

            /* Decide what editor to show on load
             ---------------------------------------------------------- */
            if ( $( '#spb_status' ).val() == 'true' && jQuery( '#wp-content-wrap' ).hasClass( 'tmce-active' ) ) {
                //if ( isTinyMceActive() == true ) {
                postdivrich.hide();
                swiftPageBuilder.show();
                $( '.spb_switch-to-builder' ).html( 'Use Default editor' ).addClass('default');

                //} else {
                //  alert("Please switch default WordPress editor to 'Visual' mode first.");
                //}

                //spb_shortcodesToBuilder();
            } else {
                postdivrich.show();
                swiftPageBuilder.hide();
                $( '.spb_switch-to-builder' ).html( 'Swift Page Builder' );
                $( '.spb_switch-to-builder' ).show();
            }
        }
        jQuery( window ).load( function() {
                if ( $( '#spb_status' ).val() == 'true' && jQuery( '#wp-content-wrap' ).hasClass( 'tmce-active' ) ) {
                    //spb_shortcodesToBuilder();
                    setTimeout( function() {
                        spb_shortcodesToBuilder( false );
                    }, 50 );
                    spb_navOnScroll();
                }
            }
        );

        /*** Toggle click (FAQ) ***/
        jQuery( ".toggle_title" ).live( "click", function() {
            if ( jQuery( this ).hasClass( 'toggle_title_active' ) ) {
                jQuery( this ).removeClass( 'toggle_title_active' ).next().hide();
            } else {
                jQuery( this ).addClass( 'toggle_title_active' ).next().show();
            }
        });

        /*** Gallery Controls / Site attached images ***/
        $.swift_page_builder.galleryImagesControls();

        $( '#spb' ).on('click', '.add-text-block-to-content', function( e ) {
                e.preventDefault();
                if ( $( this ).attr( 'parent-container' ) ) {
                    if ( $( this ).parent().parent().hasClass( 'ui-accordion-content' ) || $( this ).parent().parent().hasClass( 'ui-tabs-panel' ) ) {
                        getElementMarkup( $( this ).parent().parent(), $( '#spb_text_block' ) );
                    } else if ( $( this ).parent().parent().parent().hasClass( 'ui-accordion-content' ) || $( this ).parent().parent().parent().hasClass( 'ui-tabs-panel' ) ) {
                        getElementMarkup( $( this ).parent().parent(), $( '#spb_text_block' ) );
                    } else if ( $( this ).parent().parent().hasClass( 'spb_column_container' ) ) {
                        getElementMarkup( $( this ).parent().parent(), $( '#spb_text_block' ) );
                    } else {
                        getElementMarkup( $( $( this ).attr( 'parent-container' ) ), $( '#spb_text_block' ) );
                    }
                } else {
                    getElementMarkup( $( this ).parent().parent().parent(), $( '#spb_text_block' ) );
                }
            }
        );

        function sortElementsDropdown() {

            var mylist = $( '.spb_content_elements' ).parent().find( 'ul' );
            var listitems = mylist.children( 'li' ).get();
            listitems.sort(
                function( a, b ) {
                    var compA = $( a ).text().toUpperCase();
                    var compB = $( b ).text().toUpperCase();
                    return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
                }
            );
            $.each(
                listitems, function( idx, itm ) {
                    mylist.append( itm );
                }
            );

        }

        sortElementsDropdown();

        function sortElementsSlideout( sort_list ) {

            var mylist = $( '.' + sort_list );
            var listitems = mylist.children( 'li' ).get();
            listitems.sort(
                function( a, b ) {
                    var compA = $( a ).text().toUpperCase();
                    var compB = $( b ).text().toUpperCase();
                    return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
                }
            );
            $.each(
                listitems, function( idx, itm ) {
                    mylist.append( itm );
                }
            );

        }

        sortElementsSlideout( 'spb-content-elements' );
        sortElementsSlideout( 'spb-most-used-elements' );

        $( '.alt_background' ).live(
            'change', function() {
                $( '.altbg-preview' ).attr( 'class', 'altbg-preview' );
                $( '.altbg-preview' ).addClass( jQuery( this ).val() );
            }
        );

        $( document ).on( 'click', '#clear-spb', function( e ) {
                e.preventDefault();
                clear_page_builder_content();
            }
        ); 

        $( '.columns-dropdown' ).before( '<ul class="nav assetsearch" style="display:none;"><input type="text" class="asset-auto-complete" placeholder="Search Element"></ul>' );

        $( document ).on( 'click', '#fullscreen-spb', function( e ) {

                e.preventDefault();
                
                //If the Elements Dropdown was open when we entered in fullscreen, let's close it all
                
                $( '.spb-item-slideout' ).hide();
                $( '.spb-item-slideout' ).removeClass( 'selected' );
                $( '.dropdown-toggle' ).removeClass( 'selected' );
                $( '.dropdown' ).removeClass( 'open' );
                $( 'body' ).addClass( 'spb-fullscreen-mode' );
                $( '.normalscreen-step1' ).hide();
                $( '#spb-empty' ).addClass( 'spb-empty-fullscreen' );
                $( '.assetsearch' ).show();
                $( '.fullscreen-step1' ).show();
                $( '.spb-prebuilt-pages' ).hide();
                $( '#spb-option-slideout' ).addClass( 'spb-option-slideout-fullscreen-aux' );

                initDroppable();


            }
        );

        $( '#close-fullscreen-spb' ).on('click', function( e ) {
            e.preventDefault();
            $( '#spb-option-slideout' ).removeClass( 'spb-option-slideout-fullscreen-aux' );
            $( '.spb-item-slideout-fullscreen' ).addClass( 'spb-item-slideout' );
            $( '.spb-item-slideout' ).removeClass( 'spb-item-slideout-fullscreen' );
            $( '.spb-item-slideout' ).hide();
            $( '.spb-content-elements > li' ).find( 'a' ).show();
            $( '.asset-auto-complete' ).val( '' );
            $( ".previewpage-spb" ).hide();
            $( '.assetsearch' ).hide();
            $( '.fullscreen-step1' ).hide();
            $( '.normalscreen-step1' ).show();
            $( '.spb-prebuilt-pages-fullscreen' ).removeClass( 'selected' );
            $( '.spb-prebuilt-pages-fullscreen' ).removeClass( 'open' );
            $( '.spb-item-slideout-fullscreen' ).hide();
            $( 'body' ).removeClass( 'spb-fullscreen-mode' );
            $( '#spb-empty' ).removeClass( 'spb-empty-fullscreen' );
            $( '.spb-prebuilt-pages' ).removeClass( 'spb-prebuilt-pages-fullscreen' );
            $( '.spb-prebuilt-pages' ).addClass( 'spb-item-slideout' );
            $( '.spb_content_elements' ).show();
            $( '.spb-item-slideout' ).hide();
            $( '.dropdown-toggle' ).removeClass( 'selected' );
            $( '.spb_templates' ).removeClass( 'open' );
        });

        // Check if we are in Fullscreen Page Builder mode
        function spb_is_fullscreen() {
            if ( $( 'body' ).hasClass( 'spb-fullscreen-mode' ) ) {
                return true;
            }
            else {
                return false;
            }
        }

    }
);


 function spb_reorder_saved_templates(){
    var mylist = jQuery( '.spb-prebuilt-pages' );
    var listitems = mylist.children('li').get();

    listitems.sort( function( a, b ) {
                var compA = jQuery( a ).text().toUpperCase();
                var compB = jQuery( b ).text().toUpperCase();
                return (compA < compB) ? -1 : ( compA > compB ) ? 1 : 0;
    });

    jQuery.each(listitems, function( idx , itm ) { 
           mylist.append(itm); 
    });
}


function trackMostusedElements( element_type ){



        var data = {
            action: 'spb_track_used_elements',
            data_element_type: element_type
            
        };

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post( ajaxurl, data, function( response ) {});


}

function savePbHistory( element_type, element_name, type) {
        
        var page_id = jQuery( '#post' ).find( '#post_ID' ).val();
        //var pb_content = jQuery("#content").val();
        var pb_content = spb_getContentFromTinyMCE();


        var data = {
            action: 'spb_save_pb_history',
            data_element: element_name,
            data_element_type: element_type,
            data_pb_content: pb_content,
            data_type: type,
            data_page_id: page_id
        };

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post( ajaxurl, data, function( response ) {
            jQuery('#dropdown-history').html( response );
        });

}

function cleaningAfterClosingElementsModal() {
    jQuery( '.spb-elements-no-results' ).hide();
    jQuery( '#spb-option-slideout' ).fadeOut(300);
    jQuery( '#spb_edit_form' ).fadeOut();    
    jQuery( '#spb_edit_form' ).css({ opacity: 0.8 }).hide();
    jQuery('.spb_elements_tabs_header').find('.active_tab').removeClass('active_tab');
    jQuery( 'body' ).css( 'overflow', '' );
    jQuery( '.spb-content-elements' ).removeClass( 'selected' );
    jQuery( '.spb-item-slideout' ).removeClass( 'selected' );
    jQuery( '.spb-content-elements > li' ).find( 'a' ).parent().removeClass( 'hide-modal_elements' );
    jQuery( '#sf_search_elements').val( '' );
}

function open_elements_dropdown() {
    jQuery( '.spb_content_elements:first' ).trigger( 'click' );
}

function close_elements_dropdown() {
    jQuery( '.spb_content_elements' ).parents( '.content-dropdown' ).removeClass( 'open' );
    jQuery( '.spb_content_elements' ).removeClass( 'selected' );
}

// function open_layouts_dropdown() {
//     jQuery( '.spb_popular_layouts:first' ).trigger( 'click' );
// }

// function open_custom_elements_dropdown() {
//     jQuery( '.spb_custom_elements:first' ).trigger( 'click' );
// }

/**
 * Swift Page Builder class
 */

function spb_templateSystem() {

   jQuery( document ).on( 'click', '#spb_save_template', function( e ) {
        e.preventDefault();  
        var modal_header_msg = sfTranslatedText.attr( 'data-spb-save-template-header' );
        var modal_element_msg = sfTranslatedText.attr( 'data-spb-save-template-msg' );
        var elementSaveModal = '<div class="modal_wrapper"><a class="waves-effect waves-light btn modal-trigger" href="#modal-save-template"></a><div id="modal-save-template" class="modal modal_spb"><div class="modal-content">';
        elementSaveModal += '<h4>' + modal_header_msg + '</h4><div class="row"> <div class="input-field col s12"><input id="saved_template_name"  type="text" class="validate"><label for="saved_template_name">' + modal_element_msg + '</label>';
        elementSaveModal += '</div></div><div class="modal-footer modal-save-template"><a href="#!" class=" modal-action spb-modal-close waves-effect modal-ok-button btn-flat">OK</a><a href="#!" class=" modal-action spb-modal-close waves-effect modal-cancel-button btn-flat">Cancel</a></div></div></div></div>';
        jQuery( '#spb' ).append(elementSaveModal); 
        jQuery('#modal-save-template').openModal();            
        var element = generateShortcodesFromHtml( jQuery( this ).closest( '.spb_sortable' ), true );
        removeClassProcessedElements();
        jQuery('#modal-save-template').attr('data-element', element );
    });

 
    jQuery( document ).on( 'mouseenter', '.sf_prebuilt_template', function() {    
        jQuery( this ).find( 'a' ).next().show();    

    });

    jQuery( document ).on( 'mouseleave', '.sf_prebuilt_template', function() {    
        jQuery( this ).find( 'a' ).next().hide();    

    });

    jQuery( document ).on( 'click', '.sf_prebuilt_template a', function( e ) {    
        e.preventDefault();

        var data = {
            action: 'sf_load_template',
            template_id: jQuery( this ).attr( 'data-template_id' )
        };

        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '.spb_main_sortable' ).append( response ).find( ".spb_init_callback" ).each( function() {
                var fn = window[jQuery( this ).attr( "value" )];
                if ( typeof fn === 'function' ) {
                    fn( jQuery( this ).closest( '.spb_content_element' ) );
                }
            });

            initDroppable();
            save_spb_html();
        });
        
        cleaningAfterClosingElementsModal();

    });

    jQuery( document ).on( 'click', '.spb_template_li a', function( e ) {
            
            e.preventDefault();
            var data = {
                action: 'spb_load_template',
                template_id: jQuery( this ).attr( 'data-template_id' )
            };

            jQuery.post( ajaxurl, data, function( response ) {
                jQuery( '.spb_main_sortable' ).append( response ).find( ".spb_init_callback" ).each( function() {
                    var fn = window[jQuery( this ).attr( "value" )];
                    if ( typeof fn === 'function' ) {
                        fn( jQuery( this ).closest( '.spb_content_element' ) );
                    }
                });
                //
                initDroppable();
                save_spb_html();
            });

            jQuery( this ).parents( '.custom-templates-nav' ).find( '.dropdown' ).removeClass( 'open' );
        }
    );

    jQuery( document ).on( 'click', '.spb_remove_template', function( e ) {            
        
        e.preventDefault();

        var modal_header_msg = sfTranslatedText.attr( 'data-spb-delete-page-template-header' );
        var modal_element_msg = sfTranslatedText.attr( 'data-spb-delete-saved-template-msg1' );
        var elementSaveModal = '<div class="modal_wrapper"><a class="waves-effect waves-light btn modal-trigger" href="#modal-delete-saved-element"></a><div id="modal-delete-saved-element" class="modal modal_spb"><div class="modal-content">';
        elementSaveModal += '<h4>' + modal_header_msg + '</h4><div class="row"> <div class="input-field delete-block col s12"><label>' + modal_element_msg + '</label>';
        elementSaveModal += '</div></div><div class="modal-footer modal-remove-template"><a href="#!" class=" modal-action spb-modal-close waves-effect modal-ok-button btn-flat">OK</a><a href="#!" class=" modal-action spb-modal-close waves-effect modal-cancel-button btn-flat">Cancel</a></div></div></div></div>';
        jQuery( '#spb' ).append(elementSaveModal); 
        jQuery('#modal-delete-saved-element').openModal();  
        deletedElement = jQuery( this ).closest( '.spb_template_li' ).find( 'a' ).attr( 'data-template_id' );

    });
}


function spb_customElementSystem() {

    // Save Template Cancel Confirmation
    jQuery( document ).on( 'click', '.modal-cancel-button', function( e ) {
        e.preventDefault();
        jQuery( '.modal_wrapper' ).remove(); 
    });
     
    // Save Template Confirmation
    jQuery( document ).on( 'click', '.modal-save-template .modal-ok-button' , function() {          
        var template_name = jQuery('.modal-content #saved_template_name').val();
        var template = generateShortcodesFromHtml( jQuery( ".spb_main_sortable" ) );
        
        if ( template_name !== null && template_name !== "" ) {
                        
            //var element = jQuery('#modal-save-template').attr('data-element');
            var data = {
                action: 'spb_save_template',
                template: template,
                template_name: template_name
            };

            jQuery.post( ajaxurl, data, function( response ) {
                jQuery( '.spb-prebuilt-pages' ).html( response );   
                spb_reorder_saved_templates();
                jQuery( '.spb-prebuilt-pages .spb_template_li' ).show(); 
            });
        }

    Materialize.toast( sfTranslatedText.attr( 'data-spb-saved-template' ) , 2000);
    jQuery( '.modal_wrapper' ).remove(); 
    });
     
    
    jQuery( document ).on( 'click', '.modal-delete-element .modal-ok-button' , function() {                

        var $parent = deletedElement.closest( ".spb_sortable" );
        var delete_el_name;

        if ( $parent.parents( '.spb_gmaps' ).length > 0 ) {
            deletedElement.closest( ".spb_sortable" ).parent().remove();
        }

        deletedElement.closest( ".spb_sortable" ).remove();
        $parent.addClass( 'empty_column' );
        save_spb_html();
 
        deletedElement = deletedElement.parent().parent().parent();
        delete_el_name = deletedElement.find( '.el_name_holder' ).attr( 'data_default_name' );
        
        if ( deletedElement.hasClass('spb_row') || deletedElement.hasClass('spb_column')){
            delete_el_name = deletedElement.find( '.asset-name' ).text();
        }
      /*
        //In Accordions/tabs/tours we should go 1 level up
        if ( deletedElement.parent().hasClass( 'spb_accordion222') ){
            deletedElement = deletedElement.parent();
        }*/

        savePbHistory( deletedElement.attr( 'data-element_type' ) , delete_el_name , 'Deleted' );
        Materialize.toast( sfTranslatedText.attr( 'data-spb-deleted-element' ) , 2000);
        jQuery( '.modal_wrapper' ).remove(); 
        deletedElement = null;
    });

    jQuery( document ).on( 'click', '.modal-delete-page-builder-content .modal-ok-button', function() {
        if ( isTinyMceActive() !== true ) {
            jQuery( '#content' ).val( '' );
        } else {
            tinyMCE.activeEditor.setContent( '' );
        }

        jQuery( '#spb_content' ).find( ".spb_sortable" ).remove();
        jQuery( '#spb_content' ).find( ".new_sortable_element" ).remove();
        jQuery( '#spb_content' ).find( ".newtop_sortable_element" ).remove();
        save_spb_html();
        jQuery('.modal-delete-page-builder-content').remove();
        Materialize.toast( sfTranslatedText.attr( 'data-spb-deleted-pb-content' ) , 2000);
        jQuery( '.modal_wrapper' ).remove(); 
    });

    jQuery( document ).on( 'click', '.modal-delete-saved-element .modal-ok-button', function() {
        var data = {
            action: 'spb_delete_element',
            element_id: deletedElement.closest( '.spb_elements_li' ).find( 'a' ).attr( 'data-element_id' )
        };

        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '.spb-content-elements-saved' ).html( response );
            setTimeout( Materialize.toast( sfTranslatedText.attr( 'data-spb-deleted-element' ) , 1500) , 2000);
        });
    });

    // Save Element Confirmation
    jQuery( document ).on( 'click', '.modal-save-element .modal-ok-button', function() {
        var element_name = jQuery('.modal-content #saved_element_name').val();
        if ( element_name !== null && element_name !== "" ) {   
            var element = jQuery('#modal-save-element').attr('data-element');
            var data = {
                action: 'spb_save_element',
                element: element,
                element_name: element_name
            };
            jQuery.post( ajaxurl, data, function( response ) {
                jQuery( '.spb-content-elements-saved' ).html( response );
            });
        }
        jQuery('#modal-save-element').remove();
        Materialize.toast( sfTranslatedText.attr( 'data-spb-saved-element' ) , 2000);
        jQuery( '.modal_wrapper' ).remove(); 
    });

    // Save Element Cancel Confirmation
    jQuery( document ).on( 'click', '.modal-save-element .modal-cancel-button', function( e ) {
        e.preventDefault();
        jQuery('.modal-content #saved_element_name').val('');
        jQuery('.modal-content #saved_element_name').focusout();
    });

    //Save Element Modal
    jQuery( document ).on( 'click', '.element-save', function( e ) {
        e.preventDefault();
        var modal_header_msg = sfTranslatedText.attr( 'data-spb-save-element-header' );
        var modal_element_msg = sfTranslatedText.attr( 'data-spb-save-element-msg' );
        var elementSaveModal = '<div class="modal_wrapper"><a class="waves-effect waves-light btn modal-trigger" href="#modal-save-element"></a><div id="modal-save-element" class="modal modal_spb"><div class="modal-content">';
        elementSaveModal += '<h4>' + modal_header_msg + '</h4><div class="row"> <div class="input-field col s12"><input id="saved_element_name"  type="text" class="validate"><label for="first_name">' + modal_element_msg + '</label>';
        elementSaveModal += '</div></div><div class="modal-footer modal-save-element"><a href="#!" class=" modal-action spb-modal-close waves-effect modal-ok-button btn-flat">OK</a><a href="#!" class=" modal-action spb-modal-close waves-effect modal-cancel-button btn-flat">Cancel</a></div></div></div></div>';
        jQuery( '#spb' ).append(elementSaveModal); 
        jQuery('#modal-save-element').openModal();            
        var element = generateShortcodesFromHtml( jQuery( this ).closest( '.spb_sortable' ), true );
        removeClassProcessedElements();
        jQuery('#modal-save-element').attr('data-element', element );
    });


    jQuery( document ).on( 'click', '.spb_elements_li a', function( e ) {
        e.preventDefault();
        var data = {
            action: 'spb_load_element',
            element_id: jQuery( this ).attr( 'data-element_id' )
        };



        jQuery.post( ajaxurl, data, function( response ) {

            if ( currentAsset === "" ) {
                 currentAsset = '.spb_main_sortable';
            }

               
                jQuery( currentAsset ).append( response ).find( ".spb_init_callback" ).each( function() {
                    var fn = window[jQuery( this ).attr( "value" )];
                    if ( typeof fn === 'function' ) {
                        fn( jQuery( this ).closest( '.spb_content_element' ) );
                    }
                });
            currentAsset = '';
            
            save_spb_html();
        });

        cleaningAfterClosingElementsModal();
    });

    jQuery( document ).on( 'click', '.spb_remove_element', function( e ) {
        e.preventDefault();
        var modal_header_msg = sfTranslatedText.attr( 'data-spb-delete-saved-element-header' );
        var modal_element_msg = sfTranslatedText.attr( 'data-spb-delete-saved-element-msg1' );
        var elementSaveModal = '<div class="modal_wrapper"><a class="waves-effect waves-light btn modal-trigger" href="#modal-delete-saved-element">Modal</a><div id="modal-delete-saved-element" class="modal modal_spb"><div class="modal-content">';
        elementSaveModal += '<h4>' + modal_header_msg + '</h4><div class="row"> <div class="input-field delete-block col s12"><label>' + modal_element_msg + '</label>';
        elementSaveModal += '</div></div><div class="modal-footer modal-delete-saved-element"><a href="#!" class=" modal-action spb-modal-close waves-effect modal-ok-button btn-flat">OK</a><a href="#!" class=" modal-action spb-modal-close waves-effect modal-cancel-button btn-flat">Cancel</a></div></div></div></div>';
        jQuery( '#spb' ).append(elementSaveModal); 
        jQuery('#modal-delete-saved-element').openModal();  
        deletedElement = jQuery( this );
    });
}

// fix sub nav on scroll
var $win, $nav, navTop, isFixed = 0;
function spb_navOnScroll() {
    $win = jQuery( window );
    $nav = jQuery( '#spb-elements' );

    if ( jQuery( '#spb-elements' ).is( ":visible" ) ) {
        navTop = jQuery( '#spb-elements' ).length && jQuery( '#spb-elements' ).offset().top -28;
    } else {
 
        navTop = jQuery( '#spb' ).offset().top-28;
    }

    isFixed = 0;

    spb_processScroll();
    $win.on( 'scroll', spb_processScroll );
}
function spb_processScroll() {
    var scrollTop = $win.scrollTop();

    if ( scrollTop >= navTop && !isFixed ) {
        isFixed = 1;
        $nav.addClass( 'subnav-fixed' );
    } else if ( scrollTop <= navTop && isFixed ) {
        isFixed = 0;
        $nav.removeClass( 'subnav-fixed' );
        jQuery( '#spb-elements .dropdown' ).removeClass( 'open' );
    }
}

// function hideEditFormSaveButton() {
//     jQuery( '#spb_edit_form .edit_form_actions' ).hide();
// }
// function showEditFormSaveButton() {
//     jQuery( '#spb_edit_form .edit_form_actions' ).show();
// }

function check_form_dependency_fields() {

    jQuery("#spb_edit_form .dependency-field").each( function() {

            var field_operator = jQuery(this).attr('data-parent-operator');
            var field_value = jQuery(this).attr('data-parent-value');
            var changed_field_value;
            var field_values_array;
            var current_field;
            var valid_counter = 0;
            var temp_object;
            
            switch ( field_operator ) {
                case '=':
                case 'equals':

                    temp_object = jQuery('#spb_edit_form  .' + jQuery(this).attr('data-parent-id') );

                    if ( temp_object.length > 1 ) {

                        changed_field_value = temp_object.find( 'select' ).val();
                    
                    } else {
                        if( temp_object.hasClass( 'buttonset') ){
                                         
                            if( temp_object.attr('checked') == 'checked' ){
                                changed_field_value = temp_object.attr('data-value-on');
                            }else{
                                changed_field_value = temp_object.attr('data-value-off');
                            }

                        }else{
                            changed_field_value = jQuery('#spb_edit_form  .'+jQuery(this).attr('data-parent-id')).val();
                        }
                    }

                    if( changed_field_value == field_value ){
                        //Show the depency field
                        jQuery(this).removeClass("hide");

                    }else{
                        //Hide the depency field
                        jQuery(this).addClass("hide");
                    }

                    break;
                case '!=':
                case 'not':
                    field_values_array = jQuery(this).attr('data-parent-value').split(',');
                    current_field = jQuery(this);
                    valid_counter = 0;

                    jQuery.each(field_values_array, function(index, value) {
                        
                        temp_object = jQuery('#spb_edit_form  .'+current_field.attr('data-parent-id'));
                        
                        if ( temp_object.length > 1 ) {

                            changed_field_value = temp_object.find( 'select' ).val();
                    
                        } else {
                            if( temp_object.hasClass( 'buttonset') ){
                                         
                                if( temp_object.attr('checked') == 'checked' ){
                                    changed_field_value = temp_object.attr('data-value-on');
                                }else{
                                    changed_field_value = temp_object.attr('data-value-off');
                                }

                            }else{
                                changed_field_value = jQuery('#spb_edit_form  .'+jQuery(this).attr('data-parent-id')).val();
                            }
                        }

                        if( changed_field_value != value.trim() ){
                            valid_counter++;
                        }

                    });

                    if ( valid_counter == field_values_array.length ) {
                        //Show the depency field
                        jQuery(this).removeClass("hide");
                    } else {
                        //Hide the depency field
                        jQuery(this).addClass("hide");
                    }
                    break;
                case 'or':
                    field_values_array = jQuery(this).attr('data-parent-value').split(',');
                    current_field = jQuery(this);
                    valid_counter = 0;

                    jQuery.each(field_values_array, function(index, value) {
                        temp_object = jQuery('#spb_edit_form  .'+current_field.attr('data-parent-id'));

                     if ( temp_object.length > 1 ) {

                        changed_field_value = temp_object.find( 'select' ).val();
                    
                    } else {
                        if( temp_object.hasClass( 'buttonset') ){
                                         
                            if( temp_object.attr('checked') == 'checked' ){
                                changed_field_value = temp_object.attr('data-value-on');
                            }else{
                                changed_field_value = temp_object.attr('data-value-off');
                            }

                        }else{
                            changed_field_value = jQuery('#spb_edit_form  .'+jQuery(this).attr('data-parent-id')).val();
                        }
                    }

                    if( changed_field_value != value.trim() ){
                            valid_counter++;
                    }

                    });

                    if ( valid_counter == field_values_array.length ) {
                        //Hide the depency field
                        jQuery(this).addClass("hide");
                    } else {
                        //Show the depency field
                        jQuery(this).removeClass("hide");
                    }
                    break;
            }

     });
}


/* Updates ids order in hidden input field, on drag-n-drop reorder */
function updateSelectedImagesOrderIds( img_ul ) {
    var img_ids = [];

    jQuery( img_ul ).find( '.added img' ).each(
        function() {
            img_ids.push( jQuery( this ).attr( "rel" ) );
        }
    );

    jQuery( img_ul ).parent().prev().prev().val( img_ids.join( ',' ) );
}

// /* Takes ids from hidden field and clone li's */
// function cloneSelectedImages( site_img_div, attached_img_div ) {
//     var hidden_ids = jQuery( attached_img_div ).prev().prev(),
//         ids_array = (hidden_ids.val().length > 0) ? hidden_ids.val().split( "," ) : [],
//         img_ul = attached_img_div.find( '.sf_gallery_widget_attached_images_list' );

//     img_ul.html( '' );

//     jQuery.each(
//         ids_array, function( index, value ) {
//             jQuery( site_img_div ).find( 'img[rel=' + value + ']' ).parent().clone().appendTo( img_ul );
//         }
//     );
//     attachedImgSortable( img_ul );
// }

function attachedImgSortable( img_ul ) {
    jQuery( img_ul ).sortable(
        {
            forcePlaceholderSize: true,
            placeholder: "widgets-placeholder",
            cursor: "move",
            items: "li",
            update: function() {
                updateSelectedImagesOrderIds( img_ul );
            }
        }
    );
}


/* Get content from tinyMCE editor and convert it to Page Builder Assets
 ---------------------------------------------------------- */
function spb_shortcodesToBuilder( history_click ) {
    var content = spb_getContentFromTinyMCE();

    var form_index_ini;
    var form_index_end;
  

    if ( jQuery.trim( content ).length > 0 && jQuery.trim( content ).substr(
            0, 1
        ) != "[" && jQuery.trim( content ).substr( 0, 5 ) != "<span"  && !history_click) {
        alert( "By switching to the page builder, any content not in page builder assets will be removed for consistency." );
        //content = '[spb_text_block pb_margin_bottom="no" pb_border_bottom="no" width="1/1" el_position="first last"]' + content + '[/spb_text_block]';
        if ( isTinyMceActive() ) {
            tinyMCE.get( 'content' ).setContent( content );
        } else {
            content = jQuery( '#content' ).text( content );
        } 
    }

//    jQuery( '.spb_main_sortable' ).html( jQuery( '#spb_loading' ).val() );
    var loader = jQuery('.spb-svg-loader.clone').html();
    jQuery( '.spb_main_sortable'  ).html( '<div class="spb-loading-message">' + loader + '<div class="loading_text">' + jQuery( '#spb_loading' ).val() + '</div></div>' ).show().css( {"padding-top": 60} );
    
    if ( !history_click ){
        jQuery( '.spb_switch-to-builder' ).hide();
    }


    if ( content !== undefined ) {
        
        form_index_ini = content.indexOf('<form');
        form_index_end = content.indexOf('</form>');  

        if ( form_index_ini >= 0 ) {
            form_index_end += 7 - form_index_ini;
            var final_form_text = content.substr(form_index_ini, form_index_end).replace(/"/g, "'");
            content = content.replace(content.substr(form_index_ini, form_index_end), final_form_text);
        }
    }
    
    
    
    var data = {
        action: 'spb_shortcodes_to_builder',
        content: content
    };

    jQuery.post( ajaxurl, data, function( response ) {
        jQuery( '.spb_main_sortable' ).html( response );
        spb_elements_hide_controls( true );
        jQuery.swift_page_builder.isMainContainerEmpty();
        jQuery( '.spb_switch-to-builder' ).show();
        jQuery('.tooltipped').tooltip({delay: 50});
        //
        //console.log(response);
        jQuery.swift_page_builder.addLastClass( jQuery( ".spb_main_sortable" ) );
        initDroppable();

        //Fire INIT callback if it is defined
        jQuery( '.spb_main_sortable' ).find( ".spb_init_callback" ).each( function() {
            var fn = window[jQuery( this ).attr( "value" )];
            if ( typeof fn === 'function' ) {
                fn( jQuery( this ).closest( '.spb_sortable' ) );
            }
        });


    });


}

/* get content from tinyMCE editor
 ---------------------------------------------------------- */
function spb_getContentFromTinyMCE() {
    var content = '';

    //if ( tinyMCE.activeEditor ) {
    if ( isTinyMceActive() ) {
        var spb_ed = tinyMCE.get( 'content' ); // get editor instance
        content = spb_ed.save();
    } else {
        content = jQuery( '#content' ).text();
    }
    return content;
}


function moved() {

    var sortables = jQuery( ".spb_sortable" );

    //Dragged item's position++
    var dpos = draggedItem.position();
    var d = {
        top: dpos.top,
        bottom: dpos.top + draggedItem.height(),
        left: dpos.left,
        right: dpos.left + draggedItem.width()
    };

    //Find sortable elements covered by draggedItem
    var hoveredOver = sortables.not( draggedItem ).filter(
        function() {
            var t = jQuery( this );
            var pos = t.position();

            //This spb_sortable's position++
            var p = {
                top: pos.top,
                bottom: pos.top + t.height(),
                left: pos.left,
                right: pos.left + t.width()
            };

            //itc = intersect
            var itcTop = p.top <= d.bottom;
            var itcBtm = d.top <= p.bottom;
            var itcLeft = p.left <= d.right;
            var itcRight = d.left <= p.right;
            var itcDivx = false;
            var itcDivy = false;

            var difx = d.left - p.left;
            var dify = p.bottom - d.bottom;
            dify = Math.abs( dify );

            if ( difx < (t.width()) && difx > 0 ) {
                itcDivx = true;
            }

            if ( dify < (t.height()) && dify > 0 ) {
                itcDivy = true;
            }

            return itcTop && itcBtm && itcLeft && itcRight && itcDivx && itcDivy;
        }
    );
    hoveredOver.each( function() {
        if ( jQuery( this ).hasClass( 'spb_first' ) ) {
            jQuery( this ).removeClass( 'spb_first' );
        }
    });
}

function spb_load_top_preloader() {
    jQuery('.page-builder-switch').after( '<div class="spb_top_loader">' + jQuery('.spb-svg-loader.clone').html() + '</div>' );
}

function spb_elements_hide_controls( display ) {
    if ( display ) {
        jQuery( '.controls_right , .column_size_wrapper' ).hide();
        jQuery( '.spb_map_pin .controls_right , .spb_map_pin .column_size_wrapper' ).show();
    } else {
        jQuery( '.controls_right , .column_size_wrapper' ).show();
    }
    jQuery( '.spb_top_loader').remove();

}

function startInitDragging() {   
    var counter = 0;
    elements_values_array = [];

    jQuery('.spb_element_wrapper').each(function (){
         
            jQuery( this ).attr( 'data-values-id',  counter );
            counter ++;
            elements_values_array.push( jQuery( this ).children('input[type="hidden"]').detach() );
       
 
    });

    jQuery( 'body' ).addClass( 'startedDragging' ); 
}

function endInitDragging() {
 
    var data_id = 0;

    jQuery('.spb_element_wrapper').each(function (){
 
        data_id = jQuery( this ).attr( 'data-values-id');
        jQuery( this ).append( elements_values_array[data_id] );
 
    });

    save_spb_html();
    jQuery( 'body' ).addClass( 'startedDragging' ); 
}

/* This makes layout elements droppable, so user can drag
 them from on column to another and sort them (re-order)
 within the current column
 ---------------------------------------------------------- */
function initDroppable() {
    
    
    jQuery( '.spb_sortable_container:not(.not-sortable)' ).sortable(
        {  
            opacity: 0.7,
            connectWith: ".spb_sortable_container",  
            placeholder: "widgets-placeholder", 
            delay: 200,
            refreshPositions: true,
            tolerance: 'intersect',
            scrollSensitivity: 10,
            scrollspeed: 10,
            cancel: '.spb_map_pin, .spb_elem_handles, .el_name_editor',
           
            change: function( event, ui){
   
                 if ( !ui.item.hasClass( 'spb_row' ) ) {
                   ui.placeholder.stop().height(24).animate({
                   height: 54,                    
                   duration: "fast",
                   easing: "easeout",                    
                   opacity: 0
                   }, 300);
               }
                              
            },                
            start: function( event, ui ) {

                 startInitDragging();
                 jQuery( '.spb_elem_controls' ).hide();
                 ui.placeholder.css("opacity", "0");
                 ui.placeholder.css("width", ui.item.width());
                 ui.placeholder.height( ui.item.height() ); 
                 
                 if ( ui.item.hasClass( 'spb_row' ) ) {
                   
                    var  row_object = ui.item.find('.spb_sortable_container');
                    row_object.parent().hide();        
                    ui.item.find('.controls_right, .spb_elem_controls').hide();
                    ui.placeholder.width( ui.item.width()-20 );
                    ui.placeholder.height( 66 );
                    ui.placeholder.addClass('widgets-placeholder');
                    row_object.parent().parent().css('height', 66 );
                    ui.item.parent().find('.spb_row').addClass('spb_sortable_container_dragging');
                    
                }
                ui.item.find('.controls_right, .spb_elem_controls, .spb_tab_controls').hide();

                if ( ui.item.hasClass( 'span12' ) ) {
                    ui.placeholder.css( {maxWidth: ui.item.width()} );
                }
                else {
                    
                    if ( ui.item.hasClass( 'spb_last' ) ) {
                        ui.placeholder.removeClass( 'widgets-placeholder' );
                        ui.placeholder.addClass( 'widgets-placeholder-last' );
                    }

                    ui.placeholder.css( {maxWidth: ui.item.width() } );
                }

                jQuery( '#spb_content' ).addClass( 'sorting-started' );
                draggedItem = ui.item;

            },
            
            receive: function( event, ui ) {

                jQuery( event.target ).parent().parent().hasClass('spb_row')

                if ( ui.item.hasClass( 'spb_column' ) && ui.item.parents( '.spb_column' ).length > 0 ) {
                    jQuery( ui.sender ).sortable( 'cancel' );
                }

                if ( ui.item.hasClass( 'spb_row' ) && ui.item.parents( '.spb_row' ).length > 0 ) {
                    jQuery( this ).sortable( 'cancel' );
                    jQuery( ui.sender ).sortable( 'cancel' );
                }

                if ( ui.item.hasClass( 'spb_accordion' ) && ui.item.parents( '.spb_tabs' ).length > 0 ) {
                    jQuery( ui.sender ).sortable( 'cancel' );
                }

                if ( ui.item.hasClass( 'spb_tabs' ) && ui.item.parents( '.spb_accordion' ).length > 0 ) {
                    jQuery( ui.sender ).sortable( 'cancel' );
                }
            },
            stop: function( event, ui ) {
                endInitDragging();
                var  row_object = ui.item.find('.spb_sortable_container');
                jQuery( 'body' ).removeClass( 'startedDragging' );
                jQuery( '#spb_content' ).removeClass( 'sorting-started' );
                jQuery( window ).unbind( "mousemove", moved );
                jQuery.swift_page_builder.addLastClass( ".spb_main_sortable" );
                ui.item.parent().find('.spb_row').removeClass('spb_sortable_container_dragging');
                row_object.parent().show();
                jQuery('.spb_sortable_container_dragging').addClass('spb_sortable_container').removeClass('spb_sortable_container_dragging');
                //jQuery( '.spb_sortable_container:not(.not-sortable)' ).sortable( "refresh" ); 
                

                
                if ( ui.item.hasClass( 'spb_row' ) ) {  
                    row_object = ui.item.find('.spb_sortable_container');
                    row_object.parent().parent().css('height', '100%');
                }

                jQuery( '.newrowbottom_sortable_element').remove();
  

            },
            update: function() {

                jQuery.swift_page_builder.save_spb_html();
                removeClassProcessedElements();

            },

            beforeStop: function( event, ui ) {

                if ( ui.item.hasClass( 'not-column-inherit' ) && ui.placeholder.parent().hasClass( 'not-column-inherit' ) && ui.placeholder.parents( '.spb_row' ).length <= 0 ) {
                    return false;
                }

            }
        }
    );
   //).disableSelection();


    jQuery( '.spb_column_container' ).sortable(
        {
            connectWith: ".spb_column_container, .spb_main_sortable",
            forcePlaceholderSize: true,
            placeholder: "widgets-placeholder",
            cursor: 'move',
            items: "div.spb_sortable",
            cancel: '.spb_gmaps, .el_name_editor',
            refreshPositions: true,
            change: function( event, ui){
   
                 if ( !ui.item.hasClass( 'spb_row' ) ) {
                   ui.placeholder.stop().height(24).animate({
                   height: 54,                    
                   duration: "fast",
                   easing: "easeout",                    
                   opacity: 0
                   }, 300);
               }
                              
            }, 
            start: function( event, ui ) {

                startInitDragging();

                if( ui.item.parent().parent().hasClass( 'spb_tabs_holder' ) || ui.item.parent().parent().parent().parent().hasClass( 'spb_accordion_holder' ) ){
                    ui.item.parent().append( '<div  class="newrowbottom_sortable_element spb_content_element spb_sortable span12  spb_last spb_first ui-sortable-handle" style="position: relative; opacity: 1; z-index: 0;"></div>');
                }  
                                       
                
                ui.item.closest('.spb_row').find( '.spb_element_wrapper').first().find( '.spb_column_container' ).first().append( '<div  class="newrowbottom_sortable_element spb_content_element spb_sortable span12  spb_last spb_first ui-sortable-handle" style="position: relative; opacity: 1; z-index: 0;"></div>');
                jQuery( '.spb_elem_controls' ).hide();
                ui.placeholder.css("opacity", "0");
                ui.placeholder.css("width", ui.item.width());
                
                if ( ui.item.hasClass( 'span12' ) ) {
                    ui.placeholder.css( {maxWidth: ui.item.width()} );
                }
                else {
                    
                    if ( ui.item.hasClass( 'spb_last' ) ) {
                        ui.placeholder.removeClass( 'widgets-placeholder' );
                        ui.placeholder.addClass( 'widgets-placeholder-last' );
                    }

                    ui.placeholder.css( {maxWidth: ui.item.width()  } );
                }
            },
            stop: function() {
                endInitDragging();
                jQuery('.newrowbottom_sortable_element').remove();
                jQuery( 'body' ).removeClass( 'startedDragging' );
                jQuery.swift_page_builder.save_spb_html();
                
            }
        }
    );

    jQuery( '.spb_main_sortable' ).droppable(
        {
            greedy: true,
            accept: ".dropable_el, .dropable_column",
            drop: function( event, ui ) {
                    
                    getElementMarkup( jQuery( this ), ui.draggable, "addLastClass" );
                    jQuery( 'body' ).removeClass( 'startedDragging' );

            }
        }
    );

    jQuery( '.spb_row' ).droppable(
        {

            greedy: true,

            accept: function( dropable_el ) {

                if ( dropable_el.hasClass( 'spb_row' ) || ( dropable_el.hasClass( 'dropable_el' ) && jQuery( this ).hasClass( 'ui-droppable' ) && dropable_el.hasClass( 'not_dropable_in_third_level_nav' ) ) ) {
                    return false;
                } else if ( dropable_el.hasClass( 'dropable_el' )  || dropable_el.hasClass( 'dropable_column' )  ) {
                    return true;
                }

            },
            drop: function( event, ui ) {

                
                jQuery( this ).parent().removeClass( "spb_ui-state-active" );
                getElementMarkup( jQuery( this ), ui.draggable, "addLastClass" );
                spb_elements_hide_controls( false );
                jQuery( ui.item ).removeClass( 'controls_back' );
                jQuery( 'body' ).removeClass( 'startedDragging' );
            }
        }
    );


    jQuery( '.spb_row' ).sortable(
        {
            
            connectWith: ".spb_main_sortable",
            cancel: ".spb_row, .el_name_editor",
            placeholder: "widgets-placeholder",
            helper: function() {
                return jQuery( '<div id="itemHelper" class="spb_content_element spb_sortable sortable_1st_level span4 spb_first spb_last"><div class="controls sidebar-name"> <div class="column_size_wrapper" style="display: none;"></div></div><div class="spb_element_wrapper"></div></div>' ).appendTo( 'body' );
            },
            cursor: 'move',
            start: function( event, ui ) {

                ui.helper.addClass( ui.item.attr( "data-element_type" ) );
                ui.helper.width( 230 );
                ui.helper.height( 104 );
                ui.helper.css( {minHeight: 104} );
                ui.helper.find( '.spb_element_wrapper' ).addClass( ui.item.attr( "data-element_type" ) + '_helper' );
                
                if ( ui.item.hasClass( 'span12' ) ) {
                    ui.placeholder.css( {maxWidth: ui.item.width()} );
                }
                else {
                    ui.placeholder.css( {maxWidth: ui.item.width() / 100 * 85} );
                }

                jQuery( '#spb_content' ).addClass( 'sorting-started' );
                jQuery( '.spb_row' ).addClass( 'sorting-started' );
                draggedItem = ui.item;
                jQuery( 'body' ).addClass( 'startedDragging' );


            },
            stop: function() {
                
                endInitDragging();
                jQuery( '#spb_content' ).removeClass( 'sorting-started' );
                jQuery( '.spb_row' ).removeClass( 'sorting-started' );
                jQuery.swift_page_builder.addLastClass( ".spb_main_sortable" );
                jQuery( 'body' ).removeClass( 'startedDragging' );

            },

            items: "div.spb_sortable:not(.spb_row)",

            update: function() {

                jQuery.swift_page_builder.save_spb_html();
                removeClassProcessedElements();

            },
        }
    );

    jQuery( '.spb_column_container' ).droppable({
            greedy: true,
            accept: function( dropable_el ) {

                if ( dropable_el.hasClass( 'spb_accordion_nav' ) && jQuery( this ).parents( '.spb_tabs' ).length > 0 ) {
                    return false;
                }

                if ( dropable_el.hasClass( 'dropable_el' ) && jQuery( this ).hasClass( 'ui-droppable' ) && dropable_el.hasClass( 'not_dropable_in_third_level_nav' ) ) {
                    return false;
                } else if ( dropable_el.hasClass( 'dropable_el' ) === true ) {
                    return true;
                }

                if ( jQuery( this ).parents( '.spb_column' ).length > 0 && dropable_el.hasClass( 'dropable_column' ) ) {
                    return false;
                }

                if ( jQuery( this ).parents( '.spb_row' ).length > 0 && dropable_el.hasClass( 'dropable_column' ) ) {
                    return true;
                }

            },
            drop: function( event, ui ) {
                
                jQuery( this ).parent().removeClass( "spb_ui-state-active" );
                getElementMarkup( jQuery( this ), ui.draggable, "addLastClass" );
                spb_elements_hide_controls( false );
                jQuery( ui.item ).removeClass( 'controls_back' );
                jQuery( 'body' ).removeClass( 'startedDragging' );
            }
        }
    );


    jQuery( '.newtop_sortable_element' ).droppable(
        {
            greedy: true,
            accept: ".dropable_el, .dropable_column, .spb_sortable",
            hoverClass: "topelementover",
            zIndex: 99999999,
            drop: function( event, ui ) {
                
                getElementMarkup( jQuery( '.main_wrapper' ), ui.draggable, "topElement" );
                jQuery( ui.item ).removeClass( 'controls_back' );

                return false;

            }
        }
    );

    jQuery( '.new_sortable_element' ).droppable(
        {
            greedy: true,
            accept: ".dropable_el, .dropable_column",
            hoverClass: "topelementover",
            zIndex: 99999999,
            drop: function( event, ui ) {

                if ( jQuery( this ).hasClass( 'new_sortable_element' ) ) {
                    getElementMarkup( jQuery( this ), ui.draggable, "newElementDrop" );
                } else {
                    getElementMarkup( jQuery( this ), ui.draggable, "newRowElementDrop" );
                }

                jQuery( '.newtop_sortable_element' ).remove();
                jQuery( '.new_sortable_element' ).remove();
                jQuery( '.newrowtop_sortable_element' ).remove();
                jQuery( '.newrow_sortable_element' ).remove();
                jQuery.swift_page_builder.save_spb_html();

            }
        }
    );

    jQuery( ".spb_sortable" ).not('.spb_row, .spb_map_pin, .spb_multilayer_parallax_layer, .spb-dont-resize').resizable({
        
        helper: "marker",
        handles:  'e',         
       
        start:  function(event, ui){
                //jQuery(ui.helper).height( 66 );
                jQuery(ui.helper).width(ui.originalSize.width);
        },
        resize: function( event, ui ) {  


              var el_step_size = 0;
              var check_column = ui.element.parent().parent().parent();
              var percentage = 0;
              var col_width = 0;
              var offset_el = 0;
              var offset_step_el = 0;
              var percentage_step_el = 0;
              var resized_el = jQuery('.metabox-builder-content');
              
              if ( check_column.hasClass('spb_row') && !check_column.hasClass('spb_column') ){

                  offset_step_el = 28;
                  percentage_step_el = 28; 
                  offset_el = 38;

              }
              else if ( check_column.hasClass('spb_column') ) {
            
                        offset_step_el = 25;
                        percentage_step_el = 25;
                        resized_el = check_column;
                        offset_el = 50;

              } else if ( check_column.parent().parent().parent().hasClass('spb_accordion') ) {

                        offset_step_el = 25;
                        percentage_step_el = 25;
                        resized_el = check_column;
                        offset_el = 25;
                 
              } else if ( check_column.hasClass('spb_tabs') || check_column.hasClass('spb_tour')  ) {

                        offset_step_el = 40;
                        jQuery(ui.helper).height( 64 );

              }else if ( check_column.parent().hasClass('spb_tabs') || check_column.parent().hasClass('spb_tour')  ) {

                        offset_step_el = 25;
                        resized_el = check_column;
                        offset_el = 0;
              }   

              //Determine the Step size(the minimun width that the element can assume)
              el_step_size = Math.round( (resized_el.width() - offset_step_el) / 6 );

              //Determine the actual size width 
              percentage = parseInt( ui.helper.width() / ( resized_el.width() - percentage_step_el) * 100).toString();

              //Set the max width of the resized element according to the size of the holder div
              jQuery(this).resizable( "option", "maxWidth", resized_el.width() - offset_el );
            
              //Remove the helper
              jQuery(ui.helper).find('.width-helper').remove();    

              if ( percentage >= 88 && percentage <= 100 ) {
                  percentage = '1/1 ';
                  percentageClass = 'span12';
                  col_width = '12';
              }  

              if ( percentage >= 70 && percentage < 88 ) {
                  percentage = '3/4 ';
                  percentageClass = 'span9';
                  col_width = '9';
              }
            
              if ( percentage >= 60 && percentage < 70 ) {
                  percentage = "2/3 ";
                  percentageClass = 'span8';
                  col_width = '8';
              }

              if ( percentage >= 42 && percentage < 60 ) {
                  percentage = '1/2 ';
                  percentageClass = 'span6';
                  col_width = '6';
              }

              if ( percentage >= 29 && percentage < 42 ) {
                  percentage = "1/3 ";
                  percentageClass = 'span4';
                  col_width = '4';
              }

              if ( percentage >= 20 && percentage < 29 ) {
                  percentage = "1/4 ";
                  percentageClass = 'span3';
                  col_width = '3';
              }

              if ( percentage < 20 ) {
                  percentage = "1/6 ";
                  percentageClass = 'span2';
                  col_width = '2';
              }

              if( ui.element.hasClass( 'spb_column') ){
                  ui.element.find('.col_sm').attr('value', col_width);
              }


              jQuery(ui.helper).append('<div  class="width-helper" style="font-size:25px;width:100%;height:100%;text-align:right;float:right;margin-right:30px;margin-top:20px;"> ' + percentage + ' Width </div>');
  
              // Set the minimum width
              jQuery(this).resizable( "option", "minWidth", el_step_size );

 
        },   
        stop: function( event, ui ) {  
               jQuery( 'body' ).removeClass( 'startedDragging' );


               if ( ui.element.hasClass('spb_text_block')  ){
                setTimeout(function(){  ui.element.css( 'height' ,  ui.element.find('.spb_element_wrapper').height() + 30); }, 50);
                  
               }else if ( ui.element.hasClass('spb_column') ) {               
                      setTimeout(function(){  ui.element.css( 'height' , '100%');}, 50);
                  
                }else{

                    ui.element.css( 'height' , ui.originalSize.height );                  
                  
               }
             
               var column = jQuery( this ).closest( ".spb_sortable" );
               var resized = jQuery(this);

               resized.queue(function() {


                  var sizes = getColumnSize( column );
                  ui.element.css({ width: '' });
                  save_spb_html();
                  column.removeClass( sizes[0] ).addClass( percentageClass );
                  save_spb_html();
                  jQuery( this ).dequeue();

               });

        }
       
    });
    
    jQuery( '#spb-option-slideout' ).draggable({
        greedy: true,
        hoverClass: "",
        //hoverClass: "topelementover",
        zIndex: 99999999
    });

    jQuery( '.newrowtop_sortable_element,  .newrow_sortable_element' ).droppable({
        greedy: true,
        accept: function( dropable_el ) {

            if ( dropable_el.hasClass( 'dropable_el' ) ) {

                return true;
            }
            if ( jQuery( this ).parents( '.spb_column' ).length > 0 && dropable_el.hasClass( 'dropable_column' ) ) {
                return false;
            }

            if ( jQuery( this ).parents( '.spb_row' ).length > 0 && dropable_el.hasClass( 'dropable_column' ) ) {
                return true;
            }

        },
        hoverClass: "topelementover",
        zIndex: 99999999,
        drop: function( event, ui ) {

            if ( jQuery( this ).hasClass( 'new_sortable_element' ) ) {
                getElementMarkup( jQuery( this ), ui.draggable, "newElementDrop" );
            } else {
                getElementMarkup( jQuery( this ), ui.draggable, "newRowElementDrop" );
            }

            jQuery( '.newtop_sortable_element' ).remove();
            jQuery( '.new_sortable_element' ).remove();
            jQuery( '.newrowtop_sortable_element' ).remove();
            jQuery( '.newrow_sortable_element' ).remove();
            jQuery.swift_page_builder.save_spb_html();      

        }
    });


}


/* Get initial html markup for content element. This function
 use AJAX to run do_shortcode and then place output code into
 main content holder
 ---------------------------------------------------------- */
function getElementMarkup( target, element, action ) {
    
    var data = {
        action: 'spb_get_element_backend_html',
        //column_index: jQuery(".spb_main_sortable .spb_sortable").length + 1,
        element: element.attr( 'id' ),
        data_element: element.attr( 'data-element' ),
        data_width: element.attr( 'data-width' )
    };

    spb_load_top_preloader();

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post( ajaxurl, data, function( response ) {
        //alert('Got this from the server: ' + response);
        //jQuery(target).append(response);

        //Fire INIT callback if it is defined
        //jQuery(response).find(".spb_init_callback").each(function(index) {
        target.removeClass( 'empty_column' );
        var elementpos = 1;

        if ( action == 'topElement' ) {

            jQuery( target ).prepend( response ).find( ".spb_init_callback" ).each( function() {
                var fn = window[jQuery( this ).attr( "value" )];
                if ( typeof fn === 'function' ) {
                    fn( jQuery( this ).closest( '.spb_content_element' ).removeClass( 'empty_column' ) );
                }
            });
        
        } else if ( action == 'newElementDrop' ) {

            jQuery( '.sortable_1st_level' ).each( function() {
                if ( jQuery( this ).parent().hasClass( 'spb_main_sortable' ) && jQuery( this ).hasClass( 'spb_last' ) ) {
                    if ( parseInt( jQuery( target ).attr( "datael-position" ) ) == elementpos ) {
                        if ( jQuery( this ).hasClass( 'spb_row' ) ) {
                            if ( jQuery( target ).hasClass( 'newrowtop_sortable_element' ) ) {
                                jQuery( this ).find( '.spb_column_container' ).prepend( response );
                            } else {
                                if ( jQuery( target ).hasClass( 'new_sortable_element' ) ) {
                                    jQuery( this ).after( response );
                                }
                                else {
                                    jQuery( this ).find( '.spb_column_container' ).append( response );
                                }
                            }
                        } else {
                            jQuery( this ).after( response );
                        }

                        jQuery( '.main_wrapper' ).find( ".spb_init_callback" ).each( function() {
                            var fn = window[jQuery( this ).attr( "value" )];

                            if ( typeof fn === 'function' ) {
                                fn( jQuery( this ).closest( '.spb_content_element' ).removeClass( 'empty_column' ) );
                            }
                        });
                    }
                    elementpos++;
                }
            });
        
        } else if ( action == 'newRowElementDrop' ) {

            if ( jQuery( target ).hasClass( 'newrowtop_sortable_element' ) ) {
                
                jQuery( '.spb_row .spb_column_container' ).each( function() {
                    if ( jQuery( this ).hasClass( jQuery( target ).attr( "datael-class" ) ) ) {
                        jQuery( this ).prepend( response );
                    }
                });

            } else {

                jQuery( '.spb_row' ).find( '.spb_sortable' ).each( function() {
                    if ( parseInt( jQuery( target ).attr( "datael-position" ) ) == elementpos ) {
                        jQuery( '.spb_row_' + elementpos ).after( response );
                    }
                    elementpos++;
                });
            }
        
        } else {

            var append_response = true;
            
            if ( target.parent().hasClass('spb_tabs_holder') || target.parent().parent().parent().hasClass('spb_accordion_holder') ){
                    if ( response.indexOf('spb_row') > 0 || response.indexOf('spb_tabs') > 0 || response.indexOf('spb_accordion') > 0 ){
                        append_response = false;
                        Materialize.toast( sfTranslatedText.attr( 'data-spb-error-row-add' ) , 2000);
                    }
            }

            if( append_response == true ) {

                jQuery( target ).append( response ).find( ".spb_init_callback" ).each( function() {
                    var fn = window[jQuery( this ).attr( "value" )];
                    if ( typeof fn === 'function' ) {
                        fn( jQuery( this ).closest( '.spb_content_element' ).removeClass( 'empty_column' ) );
                    }
                });

                //Move the Container Helper at the end to don't mess the spb_first and spb_last assignment 
                if ( target.parent().hasClass('spb_tabs_holder') || target.parent().parent().parent().hasClass('spb_accordion_holder') ){
                    jQuery( target ).find('.container-helper, .tabs_expanded_helper, .ui-resizable-handle').last().detach().appendTo( target );
                }

            }

        }

        jQuery.swift_page_builder.isMainContainerEmpty();
        ////

        jQuery( ".spb_sortable" ).removeClass( "spb_row_1" );
        jQuery( ".spb_sortable" ).removeClass( "spb_row_2" );
        jQuery( ".spb_sortable" ).removeClass( "spb_row_3" );
        jQuery( ".spb_sortable" ).removeClass( "spb_row_4" );
        jQuery( ".spb_sortable" ).removeClass( "spb_row_5" );
        jQuery( ".spb_sortable" ).removeClass( "spb_row_6" );
        jQuery( ".spb_sortable" ).removeClass( "spb_row_7" );
        jQuery( ".spb_sortable" ).removeClass( "spb_row_8" );
        jQuery( ".spb_sortable" ).removeClass( "spb_row_9" );
        jQuery( ".spb_sortable" ).removeClass( "spb_row_10" );
        jQuery( ".spb_sortable" ).removeClass( "row_top_1" );
        jQuery( ".spb_sortable" ).removeClass( "row_top_2" );
        jQuery( ".spb_sortable" ).removeClass( "row_top_3" );
        jQuery( ".spb_sortable" ).removeClass( "row_top_4" );
        jQuery( ".spb_sortable" ).removeClass( "row_top_5" );

        //initTabs();
        //if (action == 'initDroppable') { initDroppable(); }
        initDroppable();
        save_spb_html();
        spb_elements_hide_controls( true );

    });

} // end getElementMarkup()


/* Set action for column size and delete buttons
 ---------------------------------------------------------- */
function columnControls() {
    jQuery( document ).on('click', '#spb .column_delete', function( e ) {   
                 
        e.preventDefault();
        
        var elementSaveModal = '<div class="modal_wrapper"><a class="waves-effect waves-light btn modal-trigger" href="#modal-delete">Modal</a><div id="modal1" class="modal modal_spb"><div class="modal-content">';
        elementSaveModal += '<h4>' + sfTranslatedText.attr( 'data-spb-delete-element-header' ) + '</h4><div class="row"> <div class="input-field delete-block col s12"><label>' + sfTranslatedText.attr( 'data-spb-delete-element-header' ) + '</label>';
        elementSaveModal += '</div></div><div class="modal-footer modal-delete-element"><a href="#!" class=" modal-action spb-modal-close waves-effect modal-ok-button btn-flat">OK</a><a href="#!" class=" modal-action spb-modal-close waves-effect modal-cancel-button btn-flat">Cancel</a></div></div></div></div>';
        jQuery( '#spb' ).append( elementSaveModal );  
        jQuery('#modal1').openModal();            
        deletedElement = jQuery( this ); 
    });
  
    jQuery( document ).on( 'click', '.controls_right .column_minimize', function( e ) {
        e.preventDefault();
        jQuery(this).parent().parent().next().hide();
        jQuery(this).next().show();
        jQuery(this).hide();
        jQuery(this).parent().parent().next().find('.minimize_row').val('yes');
        save_spb_html();
    });

    jQuery( document ).on( 'click', '.controls_right .column_maximize', function( e ) {
        e.preventDefault();
        jQuery(this).parent().parent().next().show();
        jQuery(this).prev().show();
        jQuery(this).hide(); 
        jQuery(this).parent().parent().next().find('.minimize_row').val('no');
        save_spb_html();
    });

    jQuery( document ).on( 'click', '.controls_right .column_clone, .spb_elem_controls .column_clone, .spb_accordion .column_clone, .spb_tab_controls .column_clone, .row_controls .column_clone', function( e ) {
        e.preventDefault();
        var closest_el = jQuery( this ).closest( ".spb_sortable" ),
            cloned = closest_el.clone();
        cloned.find( '.spb_element_wrapper' ).removeClass( 'over_element_wrapper' );
        cloned.insertAfter( closest_el ).hide().fadeIn();

        if ( cloned.hasClass( 'spb_tabs' ) ) {

            cloned.find( '.ui-tabs-nav span' ).each( function() {
                updateTabTitleIds( jQuery( this ).text() );
            });
        }

        cloned.find( '.controls_right , .column_size_wrapper, .spb_elem_controls' ).hide();
        cloned.find('.ui-resizable-handle').remove(); 

        //Fire INIT callback if it is defined
        cloned.find( '.spb_initialized' ).removeClass( 'spb_initialized' );
        cloned.find( ".spb_init_callback" ).each( function() {
            var fn = window[jQuery( this ).attr( "value" )];
            if ( typeof fn === 'function' ) {
                fn( cloned );
            }
        });

        save_spb_html();
        initDroppable();
        savePbHistory( cloned.attr( 'data-element_type' ) , cloned.find( '.el_name_holder' ).attr( 'data_default_name' ) , 'Cloned' );

    });

    jQuery(document).on( "click", ".spb_sortable .spb_sortable .column_popup", function( e ) {
        e.preventDefault();
        var answer = confirm( "Press OK to pop (move) section to the top level, Cancel to leave" );
        if ( answer ) {
            jQuery( this ).closest( ".spb_sortable" ).appendTo( '.spb_main_sortable' );//insertBefore('.spb_main_sortable div.spb_clear:last');
            initDroppable();
            save_spb_html();
        }
    });
  
    jQuery(document).on("click", "#spb .column_edit, #spb .column_edit_trigger", function(e) {
        
        e.preventDefault();
        var element = jQuery( this ).closest( '.spb_sortable' );

        // show edit form
        showEditForm( element );          
    });

    jQuery( ".column_increase" ).live("click", function(e) {
        e.preventDefault();
        var column = jQuery( this ).closest( ".spb_sortable" ),
            sizes = getColumnSize( column );
        if ( sizes[1] ) {
            column.removeClass( sizes[0] ).addClass( sizes[1] );
            /* get updated column size */
            sizes = getColumnSize( column );
            jQuery( column ).find( ".column_size:first" ).html( sizes[3] );
            save_spb_html();
        }
    });

    jQuery( ".column_decrease" ).live("click", function(e) {
        e.preventDefault();
        var column = jQuery( this ).closest( ".spb_sortable" ),
            sizes = getColumnSize( column );

        if ( sizes[2] ) {
            column.removeClass( sizes[0] ).addClass( sizes[2] );
            /* get updated column size */
            sizes = getColumnSize( column );
            jQuery( column ).find( ".column_size:first" ).html( sizes[3] );
            save_spb_html();
        }
    });
} // end columnControls()


// Function to avoid that the same spb_element is processed more than once
function removeClassProcessedElements() {
    jQuery( ".spb_sortable" ).removeClass( "spb_element_processed" );
}


/* Show widget small form(used for tabs, accordion, tour)
 ---------------------------------------------------------- */

function showEditSmallForm( element ) {

    var tab_name = '';
    var element_name = '';
    var icon = '';

    //Just for precaution to avoid duplicated elements
    if ( !jQuery( 'body' ).hasClass( 'edited-form-element' ) ) {

        element.parent().addClass( 'edited-form-element' );

        //if it's a Tab Element
        if ( element.parent().parent().hasClass( 'ui-tabs-nav' ) ) {
            tab_name = element.parent().find( 'span' ).text();
            element_name = 'Tabs';
            if ( element.parent().attr( 'data-title-icon' ) ) {
                icon = element.parent().attr( 'data-title-icon' );
            }
        } else {
            tab_name = element.parent().find( '.title-text' ).text();
            element_name = 'Accordion';
            if ( element.parent().attr( 'data-title-icon' ) ) {
                icon = element.parent().attr( 'data-title-icon' );
            }
        }

        if ( jQuery( 'body' ).find( 'edit-small-modal' ).length <= 0 ) {
            //jQuery( 'body' ).css( 'overflow', 'hidden' );
            var loader = jQuery('.spb-svg-loader.clone').html();
            jQuery( '#spb_edit_form' ).html( '<div class="spb-loading-message">' + loader + '<div class="loading_text">' + jQuery( '#spb_loading' ).val() + '</div></div>' ).show().css( {"padding-top": 60} );

            var data = {
                action: 'spb_show_small_edit_form',
                element_name: element_name,
                tab_name: tab_name,
                icon: icon
            };

            jQuery.post(
                ajaxurl, data, function( response ) {
                    jQuery( '.spb-modal-tabs' ).html( response );
                    jQuery( '.spb-modal-tabs' ).show();
                    jQuery( '#spb_edit_form' ).html( '' );
                }
            );

        }
    }

    //jQuery( 'body' ).css( 'overflow', 'hidden' );

    return false;
}

function updateTabTitleIds( title ) {

    var id_title = title.replace(/[^A-Za-z0-9\-_]/g, '');
    var new_tab_id;

    if ( jQuery( '.ui-tabs-nav span:contains("' + title + '")' ).length > 1 ) {
        jQuery( '.ui-tabs-nav span:contains("' + title + '")' ).each( function( index ) {
            if ( index === 0 ) {
                new_tab_id = jQuery.trim( id_title );
            } else {
                new_tab_id = jQuery.trim( id_title ) + '-' + index;
            }
            jQuery( this ).parent().parent().attr( 'id', new_tab_id );
        });
    } else {
        jQuery( '.ui-tabs-nav span:contains("' + title + '")' ).parent().parent().attr( 'id', id_title );
    }
}

function updateAccordionTitleIds( title ) {

    var id_title = title.toLowerCase().replace(/[^A-Za-z0-9\-_]/g, '-');
    var new_tab_id;

    if ( jQuery( '.ui-accordion-header .title-text:contains("' + title + '")' ).length > 1 ) {
        jQuery( '.ui-accordion-header .title-text:contains("' + title + '")' ).each( function( index ) {
            if ( index === 0 ) {
                new_tab_id = jQuery.trim( id_title );
            } else {
                new_tab_id = jQuery.trim( id_title ) + '-' + index;
            }
            jQuery( this ).parent().attr( 'accordion_id', new_tab_id );
        });
    } else {
        jQuery( '.ui-accordion-header .title-text:contains("' + title + '")' ).parent().attr( 'accordion_id', id_title );
    }
}

function saveSmallFormEditing() {

    var element = jQuery( '.edited-form-element' );
    var tab_title = jQuery( '.small_form_title' ).val();
    var new_tab_id;

    if ( element.parent().hasClass( 'ui-tabs-nav' ) ) {
        element.find( 'span' ).first().text( tab_title );

    } else {
        element.find( '.title-text' ).text( tab_title );
    }

    element.attr( 'data-title-icon', jQuery( '.small_form_icon' ).val() );

    if ( element.hasClass('ui-accordion-header') ) {
        new_tab_id = updateAccordionTitleIds( tab_title );
    } else {
        new_tab_id = updateTabTitleIds( tab_title );
    }

    element.removeClass( 'edited-form-element' );
    jQuery( '.spb-modal-tabs' ).html( '' );
    jQuery( '.spb-modal-tabs' ).hide();
    jQuery( '#spb_edit_form' ).hide();
    jQuery( 'body' ).css( 'overflow', '' );
    Materialize.toast( sfTranslatedText.attr( 'data-spb-saved-element' ) , 2000);

    save_spb_html();

    return false;
}

function detailedControls ( element ) {
    
     if ( element.is( ":checked" ) ) {
        jQuery( ' #spb .css-detailed-controls, .spb_edit_wrap .lb_css_field' ).hide();
        jQuery( ' #spb .design_tab .lb_uislider' ).parent().show();
        
        if ( jQuery('#spb_edit_form .custom-padding-top').val() == jQuery('#spb_edit_form .custom-padding-bottom').val() ){
             jQuery( '#padding_vertical_val' ).val( Math.round( jQuery('#spb_edit_form .custom-padding-bottom').val() ) );
        } 

        if ( jQuery('#spb_edit_form .custom-padding-left').val() == jQuery('#spb_edit_form .custom-padding-right').val() ){
             jQuery( '#padding_horizontal_val' ).val( Math.round( jQuery('#spb_edit_form .custom-padding-left').val() ) );
        } 

        if ( jQuery('#spb_edit_form .custom-margin-top').val() == jQuery('#spb_edit_form .custom-margin-bottom').val() ){
             jQuery( '#margin_vertical_val' ).val( Math.round( jQuery('#spb_edit_form .custom-margin-bottom').val() ) );
        } 

        jQuery( '#spb_edit_form .custom_css_percentage' ).trigger( 'change' );  
        
               
    } else {
        jQuery( ' #spb .css-detailed-controls, .spb_edit_wrap .lb_css_field').show(); 
        jQuery( ' #spb .design_tab .lb_uislider' ).parent().hide();

    }

}

/* Show widget edit form
 ---------------------------------------------------------- */
//var current_scroll_pos = 0;
function showEditForm( element ) {
    //current_scroll_pos = jQuery('body, html').scrollTop();
    var element_shortcode = generateShortcodesFromHtml( element, true ),
        element_type = element.attr( "data-element_type" );

    jQuery( 'body' ).css( 'overflow', 'hidden');
    jQuery( '#spb_edit_form' ).css( {"opacity": 1} );
    var loader = jQuery('.spb-svg-loader.clone').html();
    jQuery( '#spb_edit_form' ).html( '<div class="spb-loading-message">' + loader + '<div class="loading_text">' + jQuery( '#spb_loading' ).val() + '</div></div>' ).show().css( {"padding-top": 60} );
    
    var data = {
        action: 'spb_show_edit_form',
        element: element_type,
        shortcode: element_shortcode
    };

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post( ajaxurl, data, function( response ) {   
        jQuery( '#spb_edit_form' ).html( response ).css( {"padding-top": 0, "opacity": 1} );
        jQuery.swift_page_builder.initializeFormEditing( element );      
        jQuery('.tooltipped').tooltip({delay: 0});
                
        jQuery( '.spb_edit_form_elements' ).resizable(  { 
            handles: "e",
            create: function() {
                 // Prefers an another cursor with two arrows
                jQuery(".ui-resizable-e").css("cursor","ew-resize");
            }
        }).draggable({
            cancel: '.spb_edit_wrap',
            delay: 300
        });
 
        jQuery('.textfield').each(function() {
            var $this = jQuery(this),
                thisLabel = $this.next('label');

            if ( $this.val() && !thisLabel.hasClass('active') ) {
                thisLabel.addClass('active');
            }
        });

        jQuery('#spb .edit_form_line').find( '.simplified_controls' ).each( function(){
           detailedControls( jQuery( this ) );
        });

        jQuery('#spb .edit_form_line').find('select').not('.disabled').material_select();  

        jQuery('.icon_section_holder, .pricing_column_holder').find('select').not('.disabled').material_select();  
        jQuery('.collapsible').collapsible( { accordion : false  });
        check_form_dependency_fields();
        
        jQuery('.icon_section_holder, .pricing_column_holder, .pricing_column_feature_holder').sortable({
            connectWith: ".section_repeater",
            placeholder: "widgets-placeholder",
            opacity: 0.8,           
            tolerance: 'intersect',
            cursor: 'move',
            handle: '.icon-drag-handle',
            cancel: '.right_bottom_section',
            start: function( event, ui){
                ui.placeholder.css("opacity", "0");
            },
            change: function( event, ui) {
                ui.placeholder.stop().height(24).animate({
                height: 74,                    
                duration: "fast",
                easing: "easeout",                    
                opacity: 0
                }, 200);
            },
        });

        var form_content;
        if ( element.attr( "data-element_type" ) == 'spb_text_block' ) {

            if ( element.find('.content').is("[data-form-content]") ) {

                form_content = element.find('.content').attr('data-form-content');    
            } else {
                form_content = jQuery('.spb_edit_form_elements .form_content').val();
            }
            
            jQuery('.spb_edit_form_elements #form_content').parent().parent().parent().hide();

            if ( form_content !== undefined && form_content.indexOf('</form>') >= 0 ) {
               tinyMCE.activeEditor.setContent( form_content );
            }
        } 

        jQuery('#spb .edit_form_line').find('select').not('.disabled').trigger('contentChanged');  
        
    });
}


function saveFormEditing( element ) {

    jQuery( "#publish" ).show(); // show main publish button
    jQuery( '.spb_main_sortable, #spb-elements, .spb_switch-to-builder' ).show();
    removeClassProcessedElements();
    
    var el_name = element.find('.el_name_holder').text();
    var el_name_history = element.find('.el_name_holder').first().text();
    if ( el_name === '' ) {
        el_name = element.find('.element_name').val();
    }

    var isMapPin = element.hasClass( "spb_map_pin" ) ? true : false;

    if ( element.hasClass('spb_section') ) {
        element.find('.page-name-holder').empty();
    }

    if ( element.attr( "data-element_type" ) == 'spb_text_block' ) {           
        var content = tinyMCE.activeEditor.getContent();
        var form_index_ini = content.indexOf('<form');
        var form_index_end = content.indexOf('</form>'); 
        
        if ( form_index_ini >= 0 ) {
            form_index_end += 7 - form_index_ini;
            var final_form_text = content.substr(form_index_ini, form_index_end).replace(/"/g, "'");
            var text_ini_part = content.substr(0, form_index_ini); 
            var text_end_part = content.substr(form_index_ini + form_index_end, content.length); 
            content = text_ini_part + final_form_text + text_end_part;
            jQuery('.spb_edit_form_elements .form_content').val(content);
        }
    }
   
    // save data
    jQuery( "#spb_edit_form .spb_param_value" ).each( function() {
        var element_to_update = jQuery( this ).attr( "name" ),
            new_value = '',
            shortcode_value = '';
          
        if ( jQuery( this ).hasClass('icon_section_holder') ){
            element_to_update = 'content';
            jQuery( this ).find( '.section_repeater').each(function() {
                var icon =  jQuery(this).find('.section_icon_image i').attr("class");
                var title = jQuery(this).find('.icon_title').val();
                var link =  jQuery(this).find('.icon_link').val();
                var target =  jQuery(this).find('.icon_target').val();
                shortcode_value += '[spb_icon_box_grid_element title="' + title + '" target="' + target + '" link="' + link + '" icon="' + icon + '"][/spb_icon_box_grid_element]';
            });
            new_value = shortcode_value;         
        }

        if ( element_to_update == 'custom_css' ) {

            var mt, ml, mb, mr, pt, pl, pr, pb, bt, bl, br, bb, css_value, border_color, border_style, bg_color, bg_image, css_unit;
            var styles_output = [];
            
            if( jQuery('#spb_edit_form .custom_css_percentage').attr('checked') == 'checked' ){
                css_unit = '%';
            } else{
                css_unit = 'px';
            }  
             
            mt = jQuery('#spb_edit_form .custom-margin-top').val();
            ml = jQuery('#spb_edit_form .custom-margin-left').val();
            mr = jQuery('#spb_edit_form .custom-margin-right').val();
            mb = jQuery('#spb_edit_form .custom-margin-bottom').val();
            bt = jQuery('#spb_edit_form .custom-border-top').val();
            bl = jQuery('#spb_edit_form .custom-border-left').val();
            br = jQuery('#spb_edit_form .custom-border-right').val();
            bb = jQuery('#spb_edit_form .custom-border-bottom').val();
            pt = jQuery('#spb_edit_form .custom-padding-top').val();
            pl = jQuery('#spb_edit_form .custom-padding-left').val();
            pr = jQuery('#spb_edit_form .custom-padding-right').val();
            pb = jQuery('#spb_edit_form .custom-padding-bottom').val();
            border_color = jQuery('#spb_edit_form .border_color_global').val();
            border_style = jQuery('#spb_edit_form .border_styling_global').find( 'select' ).val();
            bg_color = jQuery('#spb_edit_form .back_color_global').val();
            
            if ( border_style == 'default' ) {
                border_style = '';
            }

            if ( typeof mt != 'undefined' && mt != '' ) {
                styles_output.push('margin-top: '+ mt + css_unit + ';');
            }
            if ( typeof ml != 'undefined' && ml != '' ) {
                styles_output.push('margin-left: '+ ml + css_unit + ';');
            }
            if ( typeof mr != 'undefined' && mr != '' ) {
                styles_output.push('margin-right: '+ mr + css_unit + ';');
            }
            if ( typeof mb != 'undefined' && mb != '' ) {
                styles_output.push('margin-bottom: '+ mb + css_unit + ';');
            }
            if ( typeof bt != 'undefined' && bt != '' && bt > 0 ) {
                styles_output.push('border-top: '+ bt + 'px ' + border_style + ' ' + border_color + ';');
            }
            if ( typeof bl != 'undefined' && bl != '' && bl > 0 ) {
                styles_output.push('border-left: '+ bl + 'px ' + border_style + ' ' + border_color + ';');
            }
            if ( typeof br != 'undefined' && br != '' && br > 0 ) {
                styles_output.push('border-right: '+ br + 'px ' + border_style + ' ' + border_color + ';');
            }
            if ( typeof bb != 'undefined' && bb != '' && bb > 0 ) {
                styles_output.push('border-bottom: '+ bb + 'px ' + border_style + ' ' + border_color + ';');
            }
            if ( typeof pt != 'undefined' && pt != '' && pt > 0 ) {
                styles_output.push('padding-top: '+ pt + css_unit + ';');
            }
            if ( typeof pl != 'undefined' && pl != '' && pl > 0 ) {
                styles_output.push('padding-left: '+ pl + css_unit + ';');
            }
            if ( typeof pr != 'undefined' && pr != '' && pr > 0 ) {
                styles_output.push('padding-right: '+ pr + css_unit + ';');
            }
            if ( typeof pb != 'undefined' && pb != '' && pb > 0 ) {
                styles_output.push('padding-bottom: '+ pb + css_unit + ';');
            }

            css_value = styles_output.join('');

            if ( bg_color != ''  && bg_color != undefined ){
                css_value += 'background-color:' + bg_color + ';';
            }

            jQuery( this ).val( css_value );


        }

        if ( jQuery( this ).hasClass('pricing_column_holder') ) {
            shortcode_value = '';
            var feature_shortcode_value = '';  
            element_to_update = 'content';

            jQuery( this ).find( '.section_repeater').each(function() {
                
                var plan_name = jQuery(this).find('.plan_name').val();
                var highlight_column = jQuery(this).find('.plan_highlight_column').find( 'select' ).val();
                var price = jQuery(this).find('.plan_price').val();
                var plan_period = jQuery(this).find('.plan_period').val();
                var plan_button_text = jQuery(this).find('.plan_button_text').val();
                var plan_link_url = jQuery(this).find('.plan_link_url').val();
                var plan_link_target = jQuery(this).find('.plan_link_target').find( 'select' ).val();
                var plan_el_class = jQuery(this).find('.plan_extra_class').val();
                
                feature_shortcode_value = '';

                //Loop to grab the Column Features
                jQuery(this).parent().find('.section_repeater_features').each(function() {

                        var plan_feature_name = jQuery(this).find('.plan_feature_name').val();
                        //var bg_color_feature = jQuery(this).find('.bg_color_feature').val();
                        //var text_color_feature = jQuery(this).find('.text_color_feature').val();
                        var feature_el_class = jQuery(this).find('.feature_el_class').val();

                        //feature_shortcode_value  += '[spb_pricing_column_feature  feature_name="' + plan_feature_name + '" bg_color_feature="' + bg_color_feature + '" text_color_feature="' + text_color_feature + '" el_class="' + feature_el_class + '"] [/spb_pricing_column_feature]';
                        feature_shortcode_value  += '[spb_pricing_column_feature feature_name="' + plan_feature_name + '" el_class="' + feature_el_class + '"] [/spb_pricing_column_feature]';
                });
                
                shortcode_value += '[spb_pricing_column name="' + plan_name + '" highlight_column="' + highlight_column + '" price="' + price + '"';
                shortcode_value += ' period="' + plan_period + '" btn_text="' + plan_button_text + '" href="' + plan_link_url + '" target="' + plan_link_target + '" el_class="' + plan_el_class + '" ]' + feature_shortcode_value + '[/spb_pricing_column]';
            });

            new_value = shortcode_value;         
        }


        if ( element.attr('data-element_type') != "spb_row" && element.attr('data-element_type') != "spb_column"){
            if ( element_to_update === 'element_name' && jQuery( this ).val() !== '' ) {
                element.find('.el_name_holder').first().html(jQuery( this ).val());
            }
            if ( element_to_update === 'element_name' && jQuery( this ).val() === '' ) {
                element.find('.el_name_holder').first().html(element.find('.el_name_holder').attr('data_default_name'));
            }
        } else {
            if ( element_to_update === 'element_name' && jQuery( this ).val() !== '' ) {
               element.find('.asset-name').first().html(jQuery( this ).val());
            }
        }

        // Textfield - input
        if ( jQuery( this ).hasClass( "textfield" ) ) {
            new_value = jQuery( this ).val();
        }
        // Textfield - input
        if ( jQuery( this ).hasClass( "textfield_html" ) ) {
            new_value = jQuery( this ).val();
            element.find( '[name=' + element_to_update + '_code]' ).val( base64_encode( rawurlencode( new_value ) ) );
            new_value = jQuery( "<div/>" ).text( new_value ).html();
        }
        // Color - input
        else if ( jQuery( this ).hasClass( "colorpicker" ) ) {
            new_value = jQuery( this ).val();
        }
        // Slider - input
        else if ( jQuery( this ).hasClass( "uislider" ) ) {
            new_value = jQuery( this ).val();
        }
        else if ( jQuery( this ).hasClass( "icon-picker" ) ) {
            new_value = jQuery( this ).val();
        }
        else if ( jQuery( this ).hasClass( "buttonset" ) ) {
            new_value = jQuery( this ).val();  
            if ( jQuery( this ).attr('checked') == 'checked' ) {
                new_value = jQuery( this ).attr('data-value-on');
            } else {
                new_value = jQuery( this ).attr('data-value-off');
            }
        }
        // Dropdown - select
        else if ( jQuery( this ).hasClass( "dropdown" ) || jQuery( this ).hasClass( "dropdown-id" ) ) {
            //new_value = jQuery( this ).val(); // get selected element
             
            if ( element_to_update === undefined ) {
                new_value = jQuery( this ).find('select').val(); // get selected element
                element_to_update = jQuery( this ).find('select').attr( "name" );

            if ( jQuery( this ).find( '.responsive_vis' ).length > 0 || jQuery( this ).find( '.row_responsive_vis' ).length > 0 || jQuery( this ).find( '.col_responsive_vis' ).length > 0 ) {
                
                var responsive_vis_icons;

                if( element.hasClass( 'spb_blank_spacer' ) ||  element.hasClass( 'spb_column' ) ){
                    responsive_vis_icons = element.find('.responsive-vis-indicator > .icons' ).first();
                } else {
                    responsive_vis_icons = element.parent().parent().find('.responsive-vis-indicator > .icons' ).first();
                }

                if ( new_value === "hidden-lg_hidden-md" ) { 
                    responsive_vis_icons.html( '<a href="#" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Hidden on Desktop"><i class="icon-vis-desktop hidden_vis"></i><i class="icon-vis-tablet"></i><i class="icon-vis-phone"></i></a>' );
                } else if ( new_value === "hidden-sm" ) {
                    responsive_vis_icons.html( '<a href="#" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Hidden on Tablet"><i class="icon-vis-desktop"></i><i class="icon-vis-tablet hidden_vis"></i><i class="icon-vis-phone"></i></a>' );
                } else if ( new_value === "hidden-lg_hidden-md_hidden-sm" ) {
                    responsive_vis_icons.html( '<a href="#" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Hidden on Desktop + Tablet"><i class="icon-vis-desktop hidden_vis"></i><i class="icon-vis-tablet hidden_vis"></i><i class="icon-vis-phone"></i></a>' );
                } else if ( new_value === "hidden-lg_hidden-md_hidden-xs" ) {
                    responsive_vis_icons.html( '<a href="#" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Hidden on Desktop + Phone"><i class="icon-vis-desktop hidden_vis"></i><i class="icon-vis-tablet"></i><i class="icon-vis-phone hidden_vis"></i></a>' );    
                } else if ( new_value === "hidden-xs_hidden-sm" ) {
                    responsive_vis_icons.html( '<a href="#" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Hidden on Tablet + Phone"><i class="icon-vis-desktop"></i><i class="icon-vis-tablet hidden_vis"></i><i class="icon-vis-phone hidden_vis"></i></a>' );
                } else if ( new_value === "hidden-xs" ) {
                    responsive_vis_icons.html( '<a href="#" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Hidden on Phone"><i class="icon-vis-desktop"></i><i class="icon-vis-tablet"></i><i class="icon-vis-phone hidden_vis"></i></a>' );
                } else {
                     responsive_vis_icons.html( '' );
                }

                jQuery('.tooltipped').tooltip({delay: 50});

            }
  

            var all_classes_ar = [],
                all_classes = '';
            jQuery( this ).find( 'option' ).each(
                function() {
                    var val = jQuery( this ).attr( 'value' );
                    all_classes_ar.push( val ); //populate all posible dropdown values
                }
            );

            all_classes = all_classes_ar.join( " " ); // convert array to string

            //element.removeClass(all_classes).addClass(new_value); // remove all possible class names and add only selected one
            if( !element.hasClass('spb_gallery') ){
                element.find( '.spb_element_wrapper' ).removeClass( all_classes ).addClass( new_value ); // remove all possible class names and add only selected one
            }
            }
        }
        else if ( jQuery( this ).hasClass( "select-multiple" ) ) {

            if ( element_to_update === undefined ) {
                element_to_update = jQuery( this ).find('select').attr( "name" );
                new_value  = jQuery( this ).find('input').val();
            }

            /*var selected =  jQuery( this ).find('input').val();
            if ( selected ) {
                all_selected = selected.join( "," ); // convert array to string
                new_value = all_selected; // get selected element
            }*/
            //element.removeClass(all_classes).addClass(new_value); // remove all possible class names and add only selected one
            //element.find('.wpb_element_wrapper').removeClass(all_classes).addClass(new_value); // remove all possible class names and add only selected one
        }
        else if ( jQuery( this ).hasClass( "select-multiple-id" ) ) {
                if ( element_to_update === undefined ) {
                    var selected = jQuery( this ).find( 'select' ).val();
                    element_to_update = jQuery( this ).find('select').attr( "name" );
                }

            if ( selected ) {
                var all_selected = selected.join( "," ); // convert array to string
                new_value = all_selected; // get selected element
            }
            //element.removeClass(all_classes).addClass(new_value); // remove all possible class names and add only selected one
            //element.find('.wpb_element_wrapper').removeClass(all_classes).addClass(new_value); // remove all possible class names and add only selected one
        }
        // WYSIWYG field
        else if ( jQuery( this ).hasClass( "textarea_html" ) ) {
            new_value = getTinyMceHtml( jQuery( this ) );
            //Hide Multi Layer Parallax Image if added Parallax Text
            if ( new_value !== '' && jQuery( this ).hasClass( "content" ) && jQuery( this ).closest( '.spb_edit_wrap' ).find( '.layer_image' ).length > 0 ) {
                element.find( '.column_edit_trigger' ).addClass( 'hide-layer-image' );
                element.find( '.attachment-medium' ).addClass( 'hide-layer-img' );
            }
            if ( new_value === '' && jQuery( this ).hasClass( "content" ) && jQuery( this ).closest( '.spb_edit_wrap' ).find( '.layer_image' ).length > 0 ) {
                element.find( '.column_edit_trigger' ).removeClass( 'hide-layer-image' );
                element.find( '.attachment-medium' ).removeClass( 'hide-layer-img' );

            }

        }
        // Check boxes
        else if ( jQuery( this ).hasClass( "spb-checkboxes" ) ) {
            var posstypes_arr = [];
            jQuery( this ).closest( '.edit_form_line' ).find( 'input' ).each( function() {
                var self = jQuery( this );
                element_to_update = self.attr( "name" );
                if ( self.is( ':checked' ) ) {
                    posstypes_arr.push( self.attr( "id" ) );
                }
            });
            if ( posstypes_arr.length > 0 ) {
                new_value = posstypes_arr.join( ',' );
            }
        }
        // Exploded textarea
        else if ( jQuery( this ).hasClass( "exploded_textarea" ) ) {
            new_value = jQuery( this ).val().replace( /\n/g, "," );
        }
        // Regular textarea
        else if ( jQuery( this ).hasClass( "textarea" ) ) {
            new_value = jQuery( this ).val();
        }
        // Encoded textarea
        else if ( jQuery( this ).hasClass( "textarea_encoded" ) ) {
            new_value = base64_encode( rawurlencode( jQuery( this ).val() ) );
            element.find( '[name=' + element_to_update + '_code]' ).val(  new_value );
            new_value = jQuery( "<div/>" ).text( new_value ).html();
        }
        else if ( jQuery( this ).hasClass( "textarea_raw_html" ) ) {
            new_value = jQuery( this ).val();
            element.find( '[name=' + element_to_update + '_code]' ).val( base64_encode( rawurlencode( new_value ) ) );
            new_value = jQuery( "<div/>" ).text( new_value ).html();
        }
        // Attach images
        else if ( jQuery( this ).hasClass( "attach_images" ) ) {
            new_value = jQuery( this ).val();
        }
        else if ( jQuery( this ).hasClass( "attach_image" ) ) {
            new_value = jQuery( this ).val();
            var $thumbnail;
            /* KLUDGE: to change image */
            //if ( jQuery( this ).hasClass( "layer_image" ) ) {
            //    $thumbnail = element.find( '[name=' + element_to_update + ']' ).next( '.attachment-medium' );
            //} else {
                $thumbnail = element.find( '[name=' + element_to_update + ']' ).next( '.attachment-thumbnail' );
            //}


            $thumbnail.attr( 'src', jQuery( this ).parent().find( 'li.added img' ).attr( 'src' ) );
            $thumbnail.attr( 'srcset', '' ); 
            if ( new_value !== '' ) {
                $thumbnail.next().addClass( 'image-exists' );
                $thumbnail.show();
            } else {
                $thumbnail.next().removeClass('image-exists');
                $thumbnail.hide();
            }

        }

        element_to_update = element_to_update.replace( 'spb_tinymce_', '' );
        if ( element.find( '.' + element_to_update ).not('.spb_element_wrapper').is( 'div, h1,h2,h3,h4,h5,h6, span, i, b, strong, button' ) ) {

            //element.find('.'+element_to_update).html(new_value);
            element.find( '[name=' + element_to_update + ']' ).html( new_value );
             
             if ( new_value.indexOf('</form>') >= 0 ){
                element.find( '[name=' + element_to_update + ']' ).attr('data-form-content', new_value);
            }else{  
               
                if ( element.find( '[name=' + element_to_update + ']' ).is("[data-form-content]") ){
                    element.find( '[name=' + element_to_update + ']' ).removeAttr("data-form-content");
                }
            }

        } else {
            //element.find('.'+element_to_update).val(new_value);
            if( element.hasClass( 'spb_row') && element_to_update == 'element_name' || element_to_update == 'custom_css' || element_to_update == 'back_color_global' || element_to_update == 'border_styling_global' || element_to_update == 'bk_image_global'  || element_to_update == 'border_color_global' ){
                element.find( '[name=' + element_to_update + ']' ).last().val( new_value );
            }else {
                element.find( '[name=' + element_to_update + ']' ).last().val( new_value );
            }
        }
    });

    // Map Asset Functions
    if ( isMapPin ) {
        var address = element.find( '[name=address]' ).val(),
            pintitle = element.find( '[name=pin_title]' ).val(),
            pinLatitude = element.find( '[name=pin_latitude]' ),
            pinLongitude = element.find( '[name=pin_longitude]' );

        // Set the lat & long
        if ( address !== "" ) {
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({'address': address}, function( results ) {
                pinLatitude.val( results[0].geometry.location.lat() );
                pinLongitude.val( results[0].geometry.location.lng() );
            });
        } else {
            element.find( '[name=address]' ).val( jQuery( "[name=address]" ).attr( 'data-previous-text' ) );
        }

        // Set the pin title
        if ( !pintitle || pintitle === "" ) {
            pintitle = address;
            pintitle = pintitle.length > 15 ? pintitle.substring( 0, 12 ) + '...' : pintitle;
        }
        jQuery( '#' + jQuery( element ).parent().attr( "aria-labelledby" ) ).find( 'span' ).html( pintitle );
    }

    // Get callback function name
    var cb = element.children( ".spb_save_callback" );
    //
    if ( cb.length == 1 ) {
        var fn = window[cb.attr( "value" )];
        if ( typeof fn === 'function' ) {
            //var tmp_output = fn( element );
        }
    }

    save_spb_html();
    savePbHistory( element.attr('data-element_type') , el_name_history, 'Edited' );
    jQuery( '#spb_edit_form' ).html( '' ).hide();
    jQuery( 'body' ).css( 'overflow', '' );
    //jQuery('body, html').scrollTop(current_scroll_pos);
}

function getTinyMceHtml( obj ) {

    var mce_id = obj.attr( 'id' ),
        html_back;

    // Switch back to visual editor
    window.switchEditors.go( mce_id, 'tmce' );

    try {
        html_back = tinyMCE.get( mce_id ).getContent();
        if ( tinyMCE.majorVersion >= 4 ) {
            tinyMCE.execCommand( "mceRemoveEditor", true, mce_id );
        } else {
            tinyMCE.execCommand( "mceRemoveControl", true, mce_id );
        }
    }
    catch ( err ) {
        html_back = switchEditors.wpautop( obj.val() );
    }

    return html_back;
}

function initTinyMce( element ) {
    var qt, textfield_id = element.attr( "id" ),
        form_line = element.closest( '#spb .edit_form_line' ),
        content_holder = form_line.find( '.spb-textarea.textarea_html' );
    
    //var content = content_holder.val();

    window.tinyMCEPreInit.mceInit[textfield_id] = _.extend(
        {}, tinyMCEPreInit.mceInit.content, {
            resize: 'vertical',
            height: 200
        }
    );

    if ( _.isUndefined( tinyMCEPreInit.qtInit[textfield_id] ) ) {
        window.tinyMCEPreInit.qtInit[textfield_id] = _.extend(
            {}, tinyMCEPreInit.qtInit.replycontent, {
                id: textfield_id
            }
        );
    }

    element.val( content_holder.val() );
    qt = quicktags( window.tinyMCEPreInit.qtInit[textfield_id] );
    QTags._buttonsInit();
    window.switchEditors.go( textfield_id, 'tmce' );

    if ( tinymce.majorVersion >= "4" ) {
        tinymce.execCommand( 'mceAddEditor', true, textfield_id );
    }
}

function isTinyMceActive() {
    var rich = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
    return rich;
}

/* This function helps when you need to determine current
 column size.

 Returns Array("current size", "larger size", "smaller size", "size string");
 ---------------------------------------------------------- */
function getColumnSize( column ) {
    if ( column.hasClass( "span12" ) ) //full-width
        return new Array( "span12", "span12", "span9", "1/1" );

    else if ( column.hasClass( "span9" ) ) //three-fourth
        return new Array( "span9", "span12", "span8", "3/4" );

    else if ( column.hasClass( "span8" ) ) //two-third
        return new Array( "span8", "span9", "span6", "2/3" );

    else if ( column.hasClass( "span6" ) ) //one-half
        return new Array( "span6", "span8", "span4", "1/2" );

    else if ( column.hasClass( "span4" ) ) // one-third
        return new Array( "span4", "span6", "span3", "1/3" );

    else if ( column.hasClass( "span3" ) ) // one-fourth
        return new Array( "span3", "span4", "span2", "1/4" );
    else if ( column.hasClass( "span2" ) ) // one-fourth
        return new Array( "span2", "span3", "span2", "1/6" );
    else
        return false;
} // end getColumnSize()

// function getAltColumnSize( column ) {
//     if ( column.hasClass( "span12" ) ) //full-width
//         return new Array( "span12", "span12", "span9", "1/1" );

//     else if ( column.hasClass( "span9" ) ) //three-fourth
//         return new Array( "span9", "span12", "span6", "3/4" );

//     else if ( column.hasClass( "span6" ) ) //one-half
//         return new Array( "span6", "span9", "span3", "1/2" );

//     else if ( column.hasClass( "span3" ) ) // one-fourth
//         return new Array( "span3", "span6", "span2", "1/4" );
//     else if ( column.hasClass( "span2" ) ) // one-fourth
//         return new Array( "span2", "span3", "span2", "1/6" );
//     else
//         return false;
// } // end getAltColumnSize()

/* This functions goes throw the dom tree and automatically
 adds 'last' class name to the columns elements.
 ---------------------------------------------------------- */
// function addLastClass( dom_tree ) {
//     return jQuery.swift_page_builder.addLastClass( dom_tree );
//     //jQuery(dom_tree).children(".column:first").addClass("first");
//     //jQuery(dom_tree).children(".column:last").addClass("last");
// } // endjQuery.swift_page_builder.addLastClass()

/* This functions copies html code into custom field and
 then on page reload/refresh it is used to build the
 initial layout.
 ---------------------------------------------------------- */
function save_spb_html() {

    jQuery.swift_page_builder.addLastClass( jQuery( ".spb_main_sortable" ) );

    var shortcodes = generateShortcodesFromHtml( jQuery( ".spb_main_sortable" ) );
    removeClassProcessedElements();

    if ( isTinyMceActive() !== true ) {
        jQuery( '#content' ).val( shortcodes );
    } else {
        //tinyMCE.activeEditor.setContent(shortcodes, {format : 'html'});
        //      if (tinyMCE.get('excerpt')) {
        //      tinyMCE.get('excerpt').setContent(shortcodes);
        //      }
        if ( tinyMCE.get( 'content' ) ) {
            tinyMCE.get( 'content' ).setContent( shortcodes );
        }
    }

    jQuery.swift_page_builder.isMainContainerEmpty();

}

function clear_page_builder_content() {
    
    var modal_header_msg = sfTranslatedText.attr( 'data-spb-clear-pb-header' );
    var modal_clear_pb__msg = sfTranslatedText.attr( 'data-spb-delete-pb-content-question' );
        
    var elementSaveModal = '<div class="modal_wrapper"><a class="waves-effect waves-light btn modal-trigger" href="#modal-delete">Modal</a><div id="modal-delete-pb-content" class="modal modal_spb"><div class="modal-content">';
    elementSaveModal += '<h4>' + modal_header_msg + '</h4><div class="row"> <div class="input-field delete-block col s12"><label>' + modal_clear_pb__msg + '</label>';
    elementSaveModal += '</div></div><div class="modal-footer modal-delete-page-builder-content"><a href="#!" class=" modal-action spb-modal-close waves-effect modal-ok-button btn-flat">Yes</a><a href="#!" class=" modal-action spb-modal-close waves-effect modal-cancel-button btn-flat">No</a></div></div></div></div>';
    jQuery( '#spb' ).append( elementSaveModal );  
    jQuery('#modal-delete-pb-content').openModal();   
}

/* Generates shortcode values
 ---------------------------------------------------------- */
function generateShortcodesFromHtml( dom_tree, single_element ) {
    var output = '';
    var selector_to_go_through = '';
    var tmp_output;

    if ( single_element ) {
        // this is used to generate shortcode for a single content element
        selector_to_go_through = jQuery( dom_tree );
    } else {
        selector_to_go_through = jQuery( dom_tree ).children( ".spb_sortable" );
    }

    selector_to_go_through.each( function() {
        //jQuery(dom_tree.selector+" > .spb_sortable").each(function(index) {
        var element = jQuery( this ),
            current_top_level = element,
            sc_base = element.find( '.spb_sc_base' ).val(),
            column_el_width = getColumnSize( element ),
            params = '',
            sc_ending = ']';
        //New Validation to avoid duplicated text
        if ( !element.hasClass( "spb_element_processed" ) ) {

            if ( element.parent().hasClass( 'spb_column_container' ) ) {
                element.addClass( "spb_element_processed" );
            }

            element.children( '.spb_element_wrapper' ).children( '.spb_param_value' ).each( function() {
                var param_name = jQuery( this ).attr( "name" ),
                    content_value = '',
                    new_value = '';

                if ( jQuery( this ).hasClass( "textfield" ) ) {
                    if ( jQuery( this ).is( 'div, h1,h2,h3,h4,h5,h6, span, i, b, strong' ) ) {
                        new_value = jQuery( this ).html();
                    } else if ( jQuery( this ).is( 'button' ) ) {
                        new_value = jQuery( this ).text();
                    } else {
                        new_value = jQuery( this ).val();
                    }
                }
                else if ( jQuery( this ).hasClass( "textfield_html" ) ) {
                    new_value = jQuery( this ).val();
                }
                else if ( jQuery( this ).hasClass( "colorpicker" ) ) {
                    new_value = jQuery( this ).val();
                }
                else if ( jQuery( this ).hasClass( "uislider" ) ) {
                    new_value = jQuery( this ).val();
                }
                else if ( jQuery( this ).hasClass( "buttonset" ) ) {
                    new_value = jQuery( this ).val();
                }
                else if ( jQuery( this ).hasClass( "icon-picker" ) ) {
                    new_value = jQuery( this ).val();
                }
                else if ( jQuery( this ).hasClass( "dropdown" ) ) {
                    new_value = jQuery( this ).val();
                }
                else if ( jQuery( this ).hasClass( "dropdown-id" ) ) {
                    new_value = jQuery( this ).val();
                }
                else if ( jQuery( this ).hasClass( "select-multiple" ) ) {
                    new_value = jQuery( this ).val();
                }
                else if ( jQuery( this ).hasClass( "select-multiple-id" ) ) {
                    new_value = jQuery( this ).val();
                }
                else if ( jQuery( this ).hasClass( "textarea_encoded" ) ) {
                    new_value = jQuery( this ).val();
                }
                else if ( jQuery( this ).hasClass( "textarea_raw_html" ) && element.children( '.spb_sortable' ).length === 0 ) {
                    //content_value = jQuery( this ).next( '.' + param_name + '_code' ).val();
                    content_value = jQuery( this ).parent().find( '.' + param_name + '_code' ).val();
                    sc_ending = ']' + content_value + '[/' + sc_base + ']';
                }
                else if ( jQuery( this ).hasClass( "textarea_html" ) && element.children( '.spb_sortable' ).length === 0 ) {
                    content_value = jQuery( this ).html();
                    sc_ending = ']' + content_value + '[/' + sc_base + ']';
                }
                else if ( jQuery( this ).hasClass( "posttypes" ) ) {
                    new_value = jQuery( this ).val();
                }
                else if ( jQuery( this ).hasClass( "exploded_textarea" ) ) {
                    new_value = jQuery( this ).val();
                }
                else if ( jQuery( this ).hasClass( "textarea" ) ) {
                    if ( jQuery( this ).is( 'div, h1,h2,h3,h4,h5,h6, span, i, b, strong' ) ) {
                        new_value = jQuery( this ).html();
                    } else {
                        new_value = jQuery( this ).val();
                    }
                }
                else if ( jQuery( this ).hasClass( "attach_images" ) ) {
                    new_value = jQuery( this ).val();
                }
                else if ( jQuery( this ).hasClass( "attach_image" ) ) {
                    new_value = jQuery( this ).val();
                }
                else if ( jQuery( this ).hasClass( "widgetised_sidebars" ) ) {
                    new_value = jQuery( this ).val();
                }

                new_value = jQuery.trim( new_value );
                if ( new_value !== '' ) {
                    params += ' ' + param_name + '="' + new_value + '"';
                }
            });

            params += ' width="' + column_el_width[3] + '"';

            if ( element.hasClass( "spb_first" ) || element.hasClass( "spb_last" ) ) {
                var spb_first = (element.hasClass( "spb_first" )) ? 'first' : '';
                var spb_last = (element.hasClass( "spb_last" )) ? 'last' : '';
                var pos_space = (element.hasClass( "spb_last" ) && element.hasClass( "spb_first" )) ? ' ' : '';
                params += ' el_position="' + spb_first + pos_space + spb_last + '"';
            }

            // Get callback function name
            var cb = element.children( ".spb_shortcode_callback" );
            //
            if ( cb.length == 1 ) {
                var fn = window[cb.attr( "value" )];
                if ( typeof fn === 'function' ) {
                    tmp_output = fn( element );
                }
            }


            output += '[' + sc_base + params + sc_ending + ' ';

            //deeper
            //if ( element.children('.spb_element_wrapper').children('.spb_column_container').children('.spb_sortable').length > 0 ) {
            if ( element.children( '.spb_element_wrapper' ).find( '.spb_column_container' ).length > 0 ) {
                //output += generateShortcodesFromHtml(element.children('.spb_element_wrapper').children('.spb_column_container'));

                // Get callback function name
                cb = element.children( ".spb_shortcode_callback" );
                var inner_element_count = 0;
                //
                element.children( '.spb_element_wrapper' ).find( '.spb_column_container' ).each( function() {
                    var sc = generateShortcodesFromHtml( jQuery( this ) );
                    //Fire SHORTCODE GENERATION callback if it is defined
                    if ( cb.length == 1 && !jQuery( this ).hasClass( 'map_pin_wrapper' ) ) {
                        var fn = window[cb.attr( "value" )];
                        if ( typeof fn === 'function' ) {
                            tmp_output = fn( current_top_level, inner_element_count );
                        }
                        if ( tmp_output ) {
                            sc = " " + tmp_output.replace( "%inner_shortcodes", sc ) + " ";
                        }

                        //sc = " " + tmp_output.replace("%inner_shortcodes", sc);
                        inner_element_count++;
                    }

                    if ( sc.trim() == '[spb_accordion_tab title=""]  [/spb_accordion_tab]' || sc.trim() == '[spb_accordion_tab title="" icon=""]  [/spb_accordion_tab]' || sc.trim() == '[spb_accordion_tab title=""  accordion_id="" icon=""]  [/spb_accordion_tab]' ){
                        sc = "";
                    }

                    output += sc;
                });

                output += '[/' + sc_base + '] ';
            }
            jQuery( '.spb_column_container' ).removeClass( 'converted' );
        }
    });

    return output;
} // end generateShortcodesFromHtml()

/* This function adds a class name to the div#drag_placeholder,
 and this helps us to give a style to the draging placeholder
 ---------------------------------------------------------- */
// function renderCorrectPlaceholder() {
//     jQuery( "#drag_placeholder" ).addClass( "column_placeholder" ).html( "Drag and drop me into the editor" );
// }


/* Custom Callbacks
 ---------------------------------------------------------- */


/* Tabs Callbacks
 ---------------------------------------------------------- */
function spbTabsInitCallBack( element ) {
    element.find( '.spb_tabs_holder' ).not( '.spb_initialized' ).each( function() {
        jQuery( this ).addClass( 'spb_initialized' );
                  
        var $tabs;

        $tabs = jQuery( this ).tabs({
            panelTemplate: '<div class="row-fluid spb_column_container empty_column spb_sortable_container not-column-inherit">' + jQuery( '#container-helper-block' ).html() + '</div>',
            add: function() {
                var tabs_count = jQuery( this ).tabs( "length" ) - 1;
                jQuery( this ).tabs( "select", tabs_count );
                save_spb_html();
            }
        });

        
        $tabs.find( ".ui-tabs-nav" ).sortable({
            axis: 'x',                    
            tolerance: 'pointer',
            scrollSensitivity: 10,
            scrollspeed: 10,
            helper: 'clone',
            opacity: 0.9,
            start: function( event, ui ) {
                ui.placeholder.width(ui.item.width() );               
                ui.helper.width( ui.item.width()  + 20 );   
                ui.helper.css( {"background-color": "#E7E7E7"} );                           
            },
            stop: function() {
                $tabs.find( 'ul li' ).each( function() {
                    var href = jQuery( this ).find( 'a' ).attr( 'href' ).replace( "#", "" );
                    $tabs.find( 'div.spb_column_container#' + href ).appendTo( $tabs );
                });
                save_spb_html();
            }
        });  
    });

    initDroppable();
}


function spbTabsGenerateShortcodeCallBack( current_top_level, inner_element_count ) {
    var tab_title = current_top_level.find( ".ui-tabs-nav li:eq(" + inner_element_count + ") a" ).text();
    var tab_title_icon = '', tab_title_id = '';

    //Grab the icon value
    if ( current_top_level.find( ".ui-tabs-nav li:eq(" + inner_element_count + ")" ).attr( 'data-title-icon' ) ) {
        tab_title_icon = current_top_level.find( ".ui-tabs-nav li:eq(" + inner_element_count + ")" ).attr( 'data-title-icon' );
    }
    //Grab the id value
    if ( current_top_level.find( ".ui-tabs-nav li:eq(" + inner_element_count + ")" ).attr( 'id' ) ) {
        tab_title_id = current_top_level.find( ".ui-tabs-nav li:eq(" + inner_element_count + ")" ).attr( 'id' );
    }

    var output = '[spb_tab title="' + tab_title + '" icon="' + tab_title_icon + '" id="' + tab_title_id + '"] %inner_shortcodes [/spb_tab]';
    if ( inner_element_count === null ) {
        output = '';
    }
    return output;
}

/* Accordion Callback
 ---------------------------------------------------------- */
function spbAccordionInitCallBack( element ) {

    element.find( '.spb_accordion_holder' ).not( '.spb_initialized' ).each( function() {            
        jQuery( this ).addClass( 'spb_initialized' );
        var $tabs;
        $tabs = jQuery( this ).accordion( {
            header: "> div > h3",
            autoHeight: true,
            heightStyle: "content"
        }).sortable({
            axis: "y",
            handle: "h3",
            stop: function( event, ui ) {
                // IE doesn't register the blur when sorting
                // so trigger focusout handlers to remove .ui-state-focus
                ui.item.children( "h3" ).triggerHandler( "focusout" );
                //
                save_spb_html();
            }
        });

        setAccordionButtons();

        function setAccordionButtons() {
            jQuery( document ).on( 'click', '.spb_accordion_holder .edit_tab', function() {
                showEditSmallForm( jQuery( this ) );
                return false;
            });
        }
    });
    initDroppable();
}


/* Accordion Callback
 ---------------------------------------------------------- */

function spbAccordionGenerateShortcodeCallBack( current_top_level, inner_element_count ) {

    var tab_title_icon = '', accordion_title_id = '';
    var tab_title = current_top_level.find( ".group:eq(" + inner_element_count + ") > h3" ).text();

    if ( current_top_level.find( ".group:eq(" + inner_element_count + ") > h3" ).attr( 'data-title-icon' ) ) {
        tab_title_icon = current_top_level.find( ".group:eq(" + inner_element_count + ") > h3" ).attr( 'data-title-icon' );
    }

    //Grab the id value
    if (current_top_level.find( ".group:eq(" + inner_element_count + ") > h3" ).attr( 'accordion_id' ) ) {
        accordion_title_id =  current_top_level.find( ".group:eq(" + inner_element_count + ") > h3" ).attr( 'accordion_id' );
    }

    var output = '[spb_accordion_tab title="' + tab_title + '" accordion_id="' + accordion_title_id + '" icon="' + tab_title_icon + '"] %inner_shortcodes [/spb_accordion_tab]';

    return output;
}

/* Message box Callbacks
 ---------------------------------------------------------- */
function spbMessageInitCallBack( element ) {
    var el = element.find( '.spb_param_value.color' );
    var class_to_set = el.val();
    el.closest( '.spb_element_wrapper' ).addClass( class_to_set );
}

/* Text Separator Callbacks
 ---------------------------------------------------------- */
function spbTextSeparatorInitCallBack( element ) {
    var el = element.find( '.spb_param_value.title_align' );
    var class_to_set = el.val();
    el.closest( '.spb_element_wrapper' ).addClass( class_to_set );
}

/* Get URL Vars
 ---------------------------------------------------------- */
function getURLVars() {
    var vars = [], hash;
    var hashes = window.location.href.slice( window.location.href.indexOf( '?' ) + 1 ).split( '&' );
    for ( var i = 0; i < hashes.length; i++ ) {
        hash = hashes[i].split( '=' );
        vars.push( hash[0] );
        vars[hash[0]] = hash[1];
    }
    return vars;
}

/* URL Encoding
 ---------------------------------------------------------- */
function rawurldecode( str ) {
    return decodeURIComponent( str + '' );
}
function rawurlencode( str ) {
    str = (str + '').toString();
    return encodeURIComponent( str ).replace( /!/g, '%21' ).replace( /'/g, '%27' ).replace(
        /\(/g, '%28'
    ).replace( /\)/g, '%29' ).replace( /\*/g, '%2A' );
}
function base64_decode( data ) {
    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0, ac = 0, dec = "", tmp_arr = [];
    if ( !data ) {
        return data;
    }
    data += '';
    do {
        h1 = b64.indexOf( data.charAt( i++ ) );
        h2 = b64.indexOf( data.charAt( i++ ) );
        h3 = b64.indexOf( data.charAt( i++ ) );
        h4 = b64.indexOf( data.charAt( i++ ) );
        bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;
        o1 = bits >> 16 & 0xff;
        o2 = bits >> 8 & 0xff;
        o3 = bits & 0xff;
        if ( h3 == 64 ) {
            tmp_arr[ac++] = String.fromCharCode( o1 );
        } else if ( h4 == 64 ) {
            tmp_arr[ac++] = String.fromCharCode( o1, o2 );
        } else {
            tmp_arr[ac++] = String.fromCharCode( o1, o2, o3 );
        }
    } while ( i < data.length );
    dec = tmp_arr.join( '' );
    dec = utf8_decode( dec );
    return dec;
}
function base64_encode( data ) {
    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0, ac = 0, enc = "", tmp_arr = [];
    if ( !data ) {
        return data;
    }
    data = utf8_encode( data + '' );
    do {
        o1 = data.charCodeAt( i++ );
        o2 = data.charCodeAt( i++ );
        o3 = data.charCodeAt( i++ );
        bits = o1 << 16 | o2 << 8 | o3;
        h1 = bits >> 18 & 0x3f;
        h2 = bits >> 12 & 0x3f;
        h3 = bits >> 6 & 0x3f;
        h4 = bits & 0x3f;
        tmp_arr[ac++] = b64.charAt( h1 ) + b64.charAt( h2 ) + b64.charAt( h3 ) + b64.charAt( h4 );
    } while ( i < data.length );
    enc = tmp_arr.join( '' );
    var r = data.length % 3;
    return (r ? enc.slice( 0, r - 3 ) : enc) + '==='.slice( r || 3 );
}
function utf8_decode( str_data ) {
    var tmp_arr = [], i = 0, ac = 0, c1 = 0, c2 = 0, c3 = 0;
    str_data += '';
    while ( i < str_data.length ) {
        c1 = str_data.charCodeAt( i );
        if ( c1 < 128 ) {
            tmp_arr[ac++] = String.fromCharCode( c1 );
            i++;
        } else if ( c1 > 191 && c1 < 224 ) {
            c2 = str_data.charCodeAt( i + 1 );
            tmp_arr[ac++] = String.fromCharCode( ((c1 & 31) << 6) | (c2 & 63) );
            i += 2;
        } else {
            c2 = str_data.charCodeAt( i + 1 );
            c3 = str_data.charCodeAt( i + 2 );
            tmp_arr[ac++] = String.fromCharCode( ((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63) );
            i += 3;
        }
    }
    return tmp_arr.join( '' );
}
function stripslashes (str) {

  return (str + '').replace(/\\(.?)/g, function (s, n1) {
    switch (n1) {
    case '\\':
      return '\\';
    case '0':
      return '\u0000';
    case '':
      return '';
    default:
      return n1;
    }
  });
}
function utf8_encode( argString ) {
    if ( argString === null || typeof argString === "undefined" ) {
        return "";
    }
    var string = (argString + '');
    var utftext = "", start, end, stringl = 0;
    start = end = 0;
    stringl = string.length;
    for ( var n = 0; n < stringl; n++ ) {
        var c1 = string.charCodeAt( n );
        var enc = null;
        if ( c1 < 128 ) {
            end++;
        } else if ( c1 > 127 && c1 < 2048 ) {
            enc = String.fromCharCode( (c1 >> 6) | 192 ) + String.fromCharCode( (c1 & 63) | 128 );
        } else {
            enc = String.fromCharCode( (c1 >> 12) | 224 ) + String.fromCharCode( ((c1 >> 6) & 63) | 128 ) + String.fromCharCode( (c1 & 63) | 128 );
        }
        if ( enc !== null ) {
            if ( end > start ) {
                utftext += string.slice( start, end );
            }
            utftext += enc;
            start = end = n + 1;
        }
    }
    if ( end > start ) {
        utftext += string.slice( start, stringl );
    }
    return utftext;
}
