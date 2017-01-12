<?php

    /*
    *
    *	Swift Page Builder - Shortcode Mapper to integrate 3rd Party Shortcodes
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    /* SHORTCODE MAPPER
    ================================================== */
    
    global $sf_opts;

    if( isset($sf_opts['shortcode_mapper_field']) ){
        $shortcode_mapper_options = $sf_opts['shortcode_mapper_field'];    
    }
    

    if( ! empty($shortcode_mapper_options) ){
        foreach ($shortcode_mapper_options as $shortcode) {
            
            $short_params = array();
            $item_count = 0;

            for ( $i = 1; $i<= 20; $i++){

                if( isset( $shortcode['param_name_' . $i] ) &&  $shortcode['param_name_' . $i] != '' ){ 
                   
                    if( $shortcode['param_type_' . $i] == 'dropdown' ) {    
                        $value_list= array();
                        array_push($value_list, 'Choose one option');
                        $value_list = explode(',',  $shortcode['default_value_' . $i]);
                       
                    }
                    else{
                        $value_list =  $shortcode['default_value_' . $i];
                    }             

                    //Check if the Heading is empty and use the Parameter name instead
                    if ( $shortcode['heading_' . $i] == '' ) {
                        $shortcode_heading = __( $shortcode['param_name_' . $i] , 'swift-framework-plugin' );
                    }else{
                        $shortcode_heading = __( $shortcode['heading_' . $i] , 'swift-framework-plugin' );
                    }

                    array_push($short_params ,array(
                        "type"        => $shortcode['param_type_' . $i] ,
                        "heading"     => $shortcode_heading,
                        "param_name"  => $shortcode['param_name_' . $i],
                        "value"       => $value_list ,
                        "description" => __(  $shortcode['param_description_' . $i] , 'swift-framework-plugin' ),
                      

                        )
                    );

                    $item_count ++;
                }
                
            }

            
            if ( $item_count > 0  || ( isset($shortcode['basename']) && $shortcode['basename'] != '' ) ){

                //Check if it includes the content parameter
                if ( $shortcode['inc_content'] == 'yes' ){
                        array_push($short_params ,array(
                                    "type"        => "textarea_html",
                                    "holder"      => "div",
                                    "heading"     => "", 
                                    "param_name"  => "content",
                                    "value"       => "",
                                    "description" => "",
                                    "class"      => "short_content_holder"
                                   )
                                );
                }

                //Set the Visual Name of asset inside the page builder mode  
                array_push($short_params ,array(
                        "type"        => "hidden",
                        "heading"     => "",
                        "param_name"  => "shortcode_label", 
                        "value"       => __( $shortcode['shortcodename'], 'swift-framework-plugin' ),
                        "description" => "",
                        "holder"      => "p",
                        "class"      => "shortcode_holder",      
                        )
                   );
            



                //Add the new Shortcode to the SPB
                SPBMap::map( $shortcode['basename'] , array(
                    "name"          => __( $shortcode['shortcodename'], 'swift-framework-plugin' ),
                    "base"          => $shortcode['basename'],
                    "class"         => "",
                    "icon"          => "spb-icon-text-block",  
                    "wrapper_class" => "clearfix",
                    "controls"      => "full",
                    "params"        => $short_params
                        
                    )
                );

            }         
            
       }
}