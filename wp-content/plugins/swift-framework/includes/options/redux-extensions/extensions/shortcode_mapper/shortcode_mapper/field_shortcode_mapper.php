<?php
/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @author      Dovy Paukstys
 * @version     3.1.5
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'ReduxFramework_shortcode_mapper' ) ) {

    /**
     * Main ReduxFramework_shortcode_mapper class
     *
     * @since       1.0.0
     */
    class ReduxFramework_shortcode_mapper extends ReduxFramework {
    
        /**
         * Field Constructor.
         *
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct( $field = array(), $value ='', $parent ) {
         
            $this->parent = $parent;
            $this->field = $field;
            $this->value = $value;

            if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
                $this->extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->extension_dir ) );
            }    

            // Set default args for this field to avoid bad indexes. Change this to anything you use.
            $defaults = array(
                'options'           => array(),
                'stylesheet'        => '',
                'output'            => true,
                'enqueue'           => true,
                'enqueue_frontend'  => true
            );
            $this->field = wp_parse_args( $this->field, $defaults );
            
        }

        /**
         * Field Render Function.
         *
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
   
       public function render () {

            $defaults = array(
                'show' => array(
                    'title' => true,
                    'description' => true,
                    'url' => true,
                ),
                'content_title' => __ ( 'Shortcode', 'redux-framework' )
            );

            $this->field = wp_parse_args ( $this->field, $defaults );
            

            echo '<div class="redux-shortcodemapper-accordion" data-new-content-title="' . esc_attr ( sprintf ( __ ( 'New %s', 'redux-framework' ), $this->field[ 'content_title' ] ) ) . '">';

             $x = 0;
             $z = 0;
             $y = 0;


            $multi = ( isset ( $this->field[ 'multi' ] ) && $this->field[ 'multi' ] ) ? ' multiple="multiple"' : "";

            if ( isset ( $this->value ) && is_array ( $this->value ) && !empty ( $this->value ) ) {

                $slides = $this->value;             

                foreach ( $slides as $slide ) {

                //  $x = 0;
                 //   $z = 0;
                    $y = 0;

                    if ( empty ( $slide ) || key ($slide) != 'shortcodename' ) {
                        $key = array_search('shortcodename', $slide);
                        
                        continue;
                    }
			
					$defaults = array(
                        'shortcodetext' => '',  
                        'shortcodename' => '',
                        'basename' => '',
                        'description' => '',
                        'inc_content' => '',
                        
                        'heading_1' => '',
                        'default_value_1' => '',
                        'param_description_1' => '',
                        'param_type_1' => '',
                        'param_name_1' => '',
                        
                        'heading_2' => '',
                        'default_value_2' => '',
                        'param_description_2' => '',
                        'param_type_2' => '',
                        'param_name_2' => '',
                        
                        'heading_3' => '',
                        'default_value_3' => '',
                        'param_description_3' => '',
                        'param_type_3' => '',
                        'param_name_3' => '',
                        
                        'heading_4' => '',
                        'default_value_4' => '',
                        'param_description_4' => '',
                        'param_type_4' => '',
                        'param_name_4' => '',

                        'heading_5' => '',
                        'default_value_5' => '',
                        'param_description_5' => '',
                        'param_type_5' => '',
                        'param_name_5' => '',

                        'heading_6' => '',
                        'default_value_6' => '',
                        'param_description_6' => '',
                        'param_type_6' => '',
                        'param_name_6' => '',
                        
                        'heading_7' => '',
                        'default_value_7' => '',
                        'param_description_7' => '',
                        'param_type_7' => '',
                        'param_name_7' => '',
                        
                        'heading_8' => '',
                        'default_value_8' => '',
                        'param_description_8' => '',
                        'param_type_8' => '',
                        'param_name_8' => '',
                        
                        'heading_9' => '',
                        'default_value_9' => '',
                        'param_description_9' => '',
                        'param_type_9' => '',
                        'param_name_9' => '',
                        
                        'heading_10' => '',
                        'default_value_10' => '',
                        'param_description_10' => '',
                        'param_type_10' => '',
                        'param_name_10' => '',

                        'heading_11' => '',
                        'default_value_11' => '',
                        'param_description_11' => '',
                        'param_type_11' => '',
                        'param_name_11' => '',
                        
                        'heading_12' => '',
                        'default_value_12' => '',
                        'param_description_12' => '',
                        'param_type_12' => '',
                        'param_name_12' => '',
                        
                        'heading_13' => '',
                        'default_value_13' => '',
                        'param_description_13' => '',
                        'param_type_13' => '',
                        'param_name_13' => '',
                        
                        'heading_14' => '',
                        'default_value_14' => '',
                        'param_description_14' => '',
                        'param_type_14' => '',
                        'param_name_14' => '',

                        'heading_15' => '',
                        'default_value_15' => '',
                        'param_description_15' => '',
                        'param_type_15' => '',
                        'param_name_15' => '',

                        'heading_16' => '',
                        'default_value_16' => '',
                        'param_description_16' => '',
                        'param_type_16' => '',
                        'param_name_16' => '',
                        
                        'heading_17' => '',
                        'default_value_17' => '',
                        'param_description_17' => '',
                        'param_type_17' => '',
                        'param_name_17' => '',
                        
                        'heading_18' => '',
                        'default_value_18' => '',
                        'param_description_18' => '',
                        'param_type_18' => '',
                        'param_name_18' => '',
                        
                        'heading_19' => '',
                        'default_value_19' => '',
                        'param_description_19' => '',
                        'param_type_19' => '',
                        'param_name_19' => '',
                        
                        'heading_20' => '',
                        'default_value_20' => '',
                        'param_description_20' => '',
                        'param_type_20' => '',
                        'param_name_20' => ''

                        );
                 

                    $slide = wp_parse_args ( $slide, $defaults );

                
                    /* Set the accordion title */
                    //if  ( $slide[ 'shortcodename' ] == '' ){
                    
                    if  ( $slide[ 'shortcodename' ] != '' ){
                         
                        $accordion_title =  $slide[ 'shortcodename' ];
                                           
                        echo '<div class="redux-shortcodemapper-accordion-group"><fieldset class="redux-field" data-id="' . $this->field[ 'id' ] . '"><h3><span class="redux-shortcodemapper-header">' . $accordion_title . '</span></h3><div>';                
                        echo '<div class="redux_slides_add_remove">';

                        echo '<ul id="' . $this->field[ 'id' ] . '-ul" class="redux-shortcodemapper-list">'; 
                      
                        /* Shortcode Name Field */
                        $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'shortcodename' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'shortcodename' ] ) : __ ( 'Enter valid shortcode', 'redux-framework' );
                        echo '<li><p>Name</p></li>';
                        echo '<li><input type="text" size="100" id="' . $this->field[ 'id' ] . '-shortcode_name_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][shortcodename]' . $this->field['name_suffix'] .'" value="' . esc_attr ( $slide[ 'shortcodename' ] ) . '" class="full-text slide-title shortcode_parse_text" /></li>';
                        
                        /* Basename Field */
                        $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'basename' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'basename' ] ) : __ ( 'Basename', 'redux-framework' );
                        echo '<li><p>Basename</p></li>';
                        echo '<li><input type="text" id="' . $this->field[ 'id' ] . '-basename_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][basename]' . $this->field['name_suffix'] .'" value="' . esc_attr ( $slide[ 'basename' ] ) . '" class="full-text" placeholder="' . $placeholder . '" /></li>';
                        
                        /*   Description Field */
                        $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'description' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'description' ] ) : __ ( 'Description', 'redux-framework' );
                        echo '<li><p>Description</p></li>';
                        echo '<li><input type="text" id="' . $this->field[ 'id' ] . '-description_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][description]' . $this->field['name_suffix'] .'" value="' . esc_attr ( $slide[ 'description' ] ) . '" class="full-text" placeholder="' . $placeholder . '" /></li>';             

                        /*   Include Content Parameter  */
                        $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'inc_content' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'inc_content' ] ) : __ ( 'Filter', 'redux-framework' );
                         
                        echo '<li><p>Include content param into shortcode</p></li>';
                        echo '<li><select id="' . $this->field[ 'id' ] . '-inc_content_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][inc_content]' . $this->field['name_suffix'] .'" value=""  class="ss-select full-text" placeholder="' . $placeholder . '" />';                  
                        
                        if ( $slide[ 'inc_content' ] == 'yes') {
                            echo '<option value="no">'. __ ( 'No', 'redux-framework' ) .'</option>';
                            echo '<option value="yes" selected>'. __ ( 'Yes', 'redux-framework' ) .'</option>';
                        }
                        else{
                            echo '<option value="no" selected>'. __ ( 'No', 'redux-framework' ) .'</option>';
                            echo '<option value="yes">'. __ ( 'Yes', 'redux-framework' ) .'</option>';
                        }
                        
                        echo '</select></li>';
                        echo '<li><br/><h4>' . __ ( 'Shortcode Parameters', 'redux-framework' ) . '</h4><hr><br/></li>'; 
                        
                        /* Add Parameter button */
                        echo '<li><a href="javascript:void(0);" class="button redux-shortcodemapper-add-parameter button-secondary" rel-id="' . $this->field[ 'id' ] . '-ul" rel-name="' . $this->field[ 'name' ] . '[title][]' . $this->field['name_suffix'] .'">' .  __ ( 'Add Parameter', 'redux-framework' ) . '</a><br/>';
            
                        /* Shortcodes List */
                        
                        for ($y = 1; $y <= 20; $y++){

                            if ($slide[ 'param_name_'.$y ] == '' ){
                                continue;
                            }

                            $drop_select = "";
                            $textfield_select = "";
                            $textarea_select = "";
                            $hidden_select = "";
                            
                            switch ( $slide[ 'param_type_'.$y ] ){

                                case 'dropdown':
                                    $drop_select = "selected";
                                    break;
                                case 'textfield':
                                    $textfield_select = "selected";
                                    break;
                                case 'textarea':
                                    $textarea_select = "selected";
                                    break;
                                case 'hidden':
                                    $hidden_select = "selected";
                                    break;
                            }

                            echo '<ul class="shortcode_parameter_row" data-row="' . $y .'">';
                            
                            /* Headers 1st Row */
                            echo '<li><div class="short_param_row"> <div class="short_param_col"><label>' . __ ( 'Param Name', 'redux-framework' ) .'</label></div>';
                            echo '<div class="short_param_col"> <label>' . __ ( 'Heading', 'redux-framework' ) .'</label></div>';
                            echo '<div class="short_param_col"><label>' . __ ( 'Field Type', 'redux-framework' ) .'</label></div>';

                            /* Input Values 1st Row */
                            echo ' <div class="short_param_col"><input type="text" id="' . $this->field[ 'id' ] . '-param_name_p' . $x . '_' . $y . '" name="' . $this->field[ 'name' ] . '[' . $x . '][param_name_' . $y . ']' . $this->field['name_suffix'] .'" value="' . esc_attr ( $slide[ 'param_name_'.$y ] ) . '" class="" placeholder="' . $placeholder . '" /></div>';
                            echo '<div class="short_param_col"> <input type="text" id="' . $this->field[ 'id' ] . '-heading_p' . $x . '_' . $y . '" name="' . $this->field[ 'name' ] .'[' . $x.'][heading_' . $y . ']' . $this->field['name_suffix'] .'"  value="' . esc_attr ( $slide[ 'heading_'.$y ] ) . '" class="" placeholder="' . $placeholder . '" /></div>';
                            echo '<div class="short_param_col"><select id="' . $this->field[ 'id' ] . '-param_type_p' . $x . '_' . $y . '"  class="ss-select" name="' . $this->field[ 'name' ] .'[' . $x.'][param_type_' . $y . ']' . $this->field['name_suffix'] .'"><option value="textfield" ' . $textfield_select . '>Textfield</option><option value="dropdown" ' . $drop_select . '>Dropdown</option><option value="textarea" ' . $textarea_select . '>Textarea</option><option value="hidden" ' . $hidden_select . '>Hidden</option></select></div>';

                            /* Headers 2nd Row */
                            echo '<div class="short_param_col"> <label>' . __ ( 'Default Value', 'redux-framework' ) .'</label></div>';
                            echo '<div class="short_param_col"> <label>' . __ ( 'Description', 'redux-framework' ) .'</label></div>';
                            echo '<div class="short_param_col"> <label>&nbsp </label></div>';
                            
                            /* Input Values 2nd Row */
                            echo '<div class="short_param_col"> <input type="text" id="' . $this->field[ 'id' ] . '-default_value_p' . $x . '_' . $y . '" name="' . $this->field[ 'name' ] . '[' . $x . '][default_value_' . $y . ']' . $this->field['name_suffix'] .'" value="' . esc_attr ( $slide[ 'default_value_'.$y ] ) . '" class="" placeholder="' . $placeholder . '" /></div>';
                            echo '<div class="short_param_col"> <input type="text" id="' . $this->field[ 'id' ] . '-param_description_p' . $x . '_' . $y . '" name="' . $this->field[ 'name' ] . '[' . $x . '][param_description_' . $y . ']' . $this->field['name_suffix'] .'" value="' . esc_attr ( $slide[ 'param_description_'.$y ] ) . '" class="" placeholder="' . $placeholder . '" /></div>';
                            
                            /* Delete Parameter button */
                            echo '<div class="short_param_col"> <label><a href="javascript:void(0);" class="button deletion redux-shortcodemapper-remove-parameter">' .  __ ( 'Delete Parameter', 'redux-framework' ) . '</a></li></ul>';
                            //echo '<div class="short_param_col"> <label><a href="javascript:void(0);" class="button deletion redux-shortcodemapper-remove-parameter">' .  __ ( 'Delete Parameter', 'redux-framework' ) . '</a></label></div></li></ul></li></ul>';
                            
                            $z ++;

                        }

                        echo '<a href="javascript:void(0);" class="button deletion redux-shortcodemapper-remove">' . sprintf ( __ ( 'Delete %s', 'redux-framework' ), $this->field[ 'content_title' ] ) . '</a>';
                        echo '</li></div></fieldset></div>';

                        $x ++; 

                    }
                }
            }
            
            if ( $z == 0 ) {
                
                $x = 0;
                //$y = 0; 
                
                if ( $y <= 0 ){

                    echo '<div class="redux-shortcodemapper-accordion-group"><fieldset class="redux-field" data-id="' . $this->field[ 'id' ] . '"><h3><span class="redux-shortcodemapper-header">New ' . $this->field[ 'content_title' ] . '</span></h3><div>';
                    
                    $hide = ' hide';

                    echo '<div class="screenshot' . $hide . '">';
                    echo '<a class="of-uploaded-image" href="">';
                    echo '<img class="redux-shortcodemapper-image" id="image_image_id_' . $x . '" src="" alt="" target="_blank" rel="external" />';
                    echo '</a>';
                    echo '</div>';

                    echo '<ul id="' . $this->field[ 'id' ] . '-ul" class="redux-shortcodemapper-list">'; 
                  
                  
                    /* Shortcode Name Field */
                    $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'shortcodename' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'shortcodename' ] ) : __ ( 'Enter valid shortcode', 'redux-framework' );
                    echo '<li><p>Name</p></li>';
                    echo '<li><input type="text" size="100" id="' . $this->field[ 'id' ] . '-shortcode_name_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][shortcodename]' . $this->field['name_suffix'] .'" value="" class="full-text slide-title shortcode_parse_text" /></li>';
                    
                    /* Basename Field */
                    $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'basename' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'basename' ] ) : __ ( 'Basename', 'redux-framework' );
                    echo '<li><p>Basename</p></li>';
                    echo '<li><input type="text" id="' . $this->field[ 'id' ] . '-basename_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][basename]' . $this->field['name_suffix'] .'" value="" class="full-text" placeholder="' . $placeholder . '" /></li>';
                    
                    /*   Description Field */
                    $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'description' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'description' ] ) : __ ( 'Description', 'redux-framework' );
                    echo '<li><p>Description</p></li>';
                    echo '<li><input type="text" id="' . $this->field[ 'id' ] . '-description_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][description]' . $this->field['name_suffix'] .'" value="" class="full-text" placeholder="' . $placeholder . '" /></li>';              


                    /*   Include Content Parameter  */
                    $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'inc_content' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'inc_content' ] ) : __ ( 'Filter', 'redux-framework' );
                     
                    echo '<li><p>Include content param into shortcode</p></li>';
                    echo '<li><select id="' . $this->field[ 'id' ] . '-inc_content_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][inc_content]' . $this->field['name_suffix'] .'" value=""  class="ss-select full-text" placeholder="' . $placeholder . '" />';                  
                    echo '<option value="no">'. __ ( 'No', 'redux-framework' ) .'</option>';
                    echo '<option value="yes">'. __ ( 'Yes', 'redux-framework' ) .'</option>';
                    echo '</select></li>';
                    echo '<li><br/><h4>' . __ ( 'Shortcode Parameters', 'redux-framework' ) . '</h4><hr><br/></li>'; 
                    echo '<li><a href="javascript:void(0);" class="button redux-shortcodemapper-add-parameter button-secondary" rel-id="' . $this->field[ 'id' ] . '-ul" rel-name="' . $this->field[ 'name' ] . '[title][]' . $this->field['name_suffix'] .'">' .  __ ( 'Add Parameter', 'redux-framework' ) . '</a><br/></li>';
                  


                }
                
                    $y = 1; 
                    /* Shortcodes List */
                    echo '<ul class="shortcode_parameter_row" data-row="' . $y .'">';
                    
                    /* Headers 1st Row */
                    echo '<li><div class="short_param_row"> <div class="short_param_col"><label>' . __ ( 'Param Name', 'redux-framework' ) .'</label></div>';
                    echo '<div class="short_param_col"> <label>' . __ ( 'Heading', 'redux-framework' ) .'</label></div>';
                    echo '<div class="short_param_col"><label>' . __ ( 'Field Type', 'redux-framework' ) .'</label></div>';

                    /* Input Values 1st Row */

                    echo ' <div class="short_param_col"><input type="text" id="' . $this->field[ 'id' ] . '-param_name_p' . $x . '_' . $y . '" name="' . $this->field[ 'name' ] . '[' . $x . '][param_name_' . $y . ']' . $this->field['name_suffix'] .'" value="" class="" /></div>';
                    echo '<div class="short_param_col"> <input type="text" id="' . $this->field[ 'id' ] . '-heading_p' . $x . '_' . $y . '" name="' . $this->field[ 'name' ] .'[' . $x.'][heading_' . $y . ']' . $this->field['name_suffix'] .'"  value="" class="" /></div>';
                    echo '<div class="short_param_col"><select id="' . $this->field[ 'id' ] . '-param_type_p' . $x . '_' . $y . '" name="' . $this->field[ 'name' ] .'[' . $x.'][param_type_' . $y . ']' . $this->field['name_suffix'] .'" class="ss-select" ><option value="textfield" >' . __ ( 'Textfield', 'redux-framework' ) . '</option><option value="dropdown" >' . __ ( 'Dropdown', 'redux-framework' ) . '</option><option value="textarea" >' . __ ( 'Textarea', 'redux-framework' ) . '</option><option value="hidden">' . __ ( 'Hidden', 'redux-framework' ) . '</option></select></div>';

                    /* Headers 2nd Row */
                    echo '<div class="short_param_col"> <label>' . __ ( 'Default Value', 'redux-framework' ) .'</label></div>';
                    echo '<div class="short_param_col"> <label>' . __ ( 'Description', 'redux-framework' ) .'</label></div>';
                    echo '<div class="short_param_col"> <label> &nbsp</label></div>';
                    
                    /* Input Values 2nd Row */
                    echo '<div class="short_param_col"> <input type="text" id="' . $this->field[ 'id' ] . '-default_value_p' . $x . '_' . $y . '" name="' . $this->field[ 'name' ] . '[' . $x . '][default_value_' . $y . ']' . $this->field['name_suffix'] .'" value="" class="" /></div>';
                    echo '<div class="short_param_col"> <input type="text" id="' . $this->field[ 'id' ] . '-param_description_p' . $x . '_' . $y . '" name="' . $this->field[ 'name' ] . '[' . $x . '][param_description_' . $y . ']' . $this->field['name_suffix'] .'" value="" class="" /></div>';
                    
                    /* Delete Parameter button */
                    echo '<div class="short_param_col"> <label><a href="javascript:void(0);" class="button deletion redux-shortcodemapper-remove-parameter">' .  __ ( 'Delete Parameter', 'redux-framework' ) . '</a></li></ul>';
                    


                echo '<a href="javascript:void(0);" class="button deletion redux-shortcodemapper-remove">' . sprintf ( __ ( 'Delete %s', 'redux-framework' ), $this->field[ 'content_title' ] ) . '</a>';
                echo '</li></div></fieldset></div>';

            }
            echo '</div></div><a href="javascript:void(0);" class="button redux-shortcodemapper-add button-secondary" rel-id="' . $this->field[ 'id' ] . '-ul" rel-name="' . $this->field[ 'name' ] . '[title][]' . $this->field['name_suffix'] .'">' . sprintf ( __ ( 'Add %s', 'redux-framework' ), $this->field[ 'content_title' ] ) . '</a><br/>';
        }

        
        /**
         * Enqueue Function.
         *
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        
		
		   public function enqueue () {
		   
		   
		   $min = Redux_Functions::isMin();
		   
		   	wp_enqueue_script(
                'redux-field-media-js',
                ReduxFramework::$_url . 'assets/js/media/media' . $min . '.js',
                array( 'jquery', 'redux-js' ),
                time(),
                true  
            );

            wp_enqueue_style (
                'redux-field-media-css', 
                ReduxFramework::$_url . 'inc/fields/media/field_media.css', 
                time (), 
                true
            );

            wp_enqueue_script (
                'redux-field-shortcode-mapper-js', 
                $this->extension_url . 'field_shortcode_mapper.js',  
                array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion', 'wp-color-picker', 'redux-field-media-js' ), 
                time (), 
                true
            );
			
            wp_enqueue_style (
                'redux-field-shortcode-mapper-css', 
                $this->extension_url . 'field_shortcode_mapper.css',  
                time (), 
                true
            );
        }
        
        /**
         * Output Function.
         *
         * Used to enqueue to the front-end
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */        
        public function output() {

            if ( $this->field['enqueue_frontend'] ) {

            }
            
        }        
        
    }
}
