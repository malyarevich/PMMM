<?php

    /*
    *
    *	Swift Page Builder - Builder Class
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilder extends SFPageBuilderAbstract {
        private $is_theme = true;
        private $postTypes;
        private $layout;
        protected $shortcodes, $images_media_tab;

        public static function getInstance() {
            static $instance = null;
            if ( $instance === null ) {
                $instance = new SwiftPageBuilder();
            }

            return $instance;
        }

        public function get_theme_opts_name() {
            if ( get_option('sf_theme') != "" ) {
                return 'sf_' . get_option('sf_theme') . '_options';
            } else {
                return "";
            }
        }

        public function getPostTypes() {

            global $sf_opts;

            if ( is_array( $this->postTypes ) ) {
                return $this->postTypes;
            }

            if ( isset( $sf_opts['spb-post-types'] ) ) {
                $pt_array = $sf_opts['spb-post-types'];
            }
            $pt_array[] = 'spb-section';

            $pt_array = apply_filters('spb_post_types', $pt_array);

            $this->postTypes = $pt_array ? $pt_array : array(
                'page',
                'post',
                'portfolio',
                'team',
                'ajde_events',
                'product',
                'spb-section'
            );

            return $this->postTypes;
        }

        public function getLayout() {
            if ( $this->layout == null ) {
                $this->layout = new SPBLayout();
            }

            return $this->layout;
        }

        /* Add shortCode to plugin */
        public function addShortCode( $shortcode, $function = false ) {
            $name = 'SwiftPageBuilderShortcode_' . $shortcode['base'];
            if ( class_exists( $name ) && is_subclass_of( $name, 'SwiftPageBuilderShortcode' ) ) {
                $this->shortcodes[ $shortcode['base'] ] = new $name( $shortcode );
            }else if ( is_admin() ){
                
                $name = 'SwiftPageBuilderShortcode_default';
                
                $this->shortcodes[ $shortcode['base'] ] = new $name( $shortcode );
            }
        }

        public function createShortCodes() {
            remove_all_shortcodes();
            foreach ( SPBMap::getShortCodes() as $sc_base => $el ) {
                $name = 'SwiftPageBuilderShortcode_' . $el['base'];
                if ( class_exists( $name ) && is_subclass_of( $name, 'SwiftPageBuilderShortcode' ) ) {
                    $this->shortcodes[ $sc_base ] = new $name( $el );
                }
            }
        }

        /* Save generated shortcodes, html and builder status in post meta
        ---------------------------------------------------------- */
        public function saveMetaBoxes( $post_id ) {
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return $post_id;
            }
            $value = $this->post( 'spb_status' );
            $legacy_value = $this->post( 'spb_js_status' );
            if ( $value !== null ) {
                // Add value
                if ( spb_get_post_meta( $post_id, '_spb_status' ) == '' ) {
                    add_post_meta( $post_id, '_spb_status', $value, true );
                } // Update value
                elseif ( $value != spb_get_post_meta( $post_id, '_spb_status', true ) ) {
                    update_post_meta( $post_id, '_spb_status', $value );
                } // Delete value
                elseif ( $value == '' ) {
                    delete_post_meta( $post_id, '_spb_status', spb_get_post_meta( $post_id, '_spb_status', true ) );
                }
                if ( $value != spb_get_post_meta( $post_id, '_spb_js_status', true ) ) {
                    delete_post_meta( $post_id, '_spb_js_status' );
                }
            }
            if ( $value !== null ) {
                // Add value
                if ( spb_get_post_meta( $post_id, '_spb_js_status' ) == '' ) {
                    add_post_meta( $post_id, '_spb_js_status', $value, true );
                } // Update value
                elseif ( $value != spb_get_post_meta( $post_id, '_spb_js_status', true ) ) {
                    update_post_meta( $post_id, '_spb_js_status', $value );
                } // Delete value
                elseif ( $value == '' ) {
                    delete_post_meta( $post_id, '_spb_js_status', spb_get_post_meta( $post_id, '_spb_js_status', true ) );
                }
            }
        }

        public function elementBackendHtmlJavascript_callback() {
            $output       = '';
            $element      = $this->post( 'element' );
            $data_element = $this->post( 'data_element' );

            if ( $data_element == 'spb_column' && $this->post( 'data_width' ) !== null ) {
                $output = do_shortcode( '[spb_column width="' . $this->post( 'data_width' ) . '"]' );
                echo $output;
            } else if ( $data_element == 'spb_text_block' ) {
                $output = do_shortcode( '[spb_text_block]' . __( "<p>This is a text block. Click the edit button to change this text.</p>", 'swift-framework-plugin' ) . '[/spb_text_block]' );
                echo $output;
            } else {
                $output = do_shortcode( '[' . $data_element . ']' );
                //$output = $data_element .'  -  ' . $element;
                echo $output;
            }
            die();
        }

        public function spbShortcodesJS_callback() {
            $content = $this->post( 'content' );

            $content = stripslashes( $content );
            $output  = spb_format_content( $content );
            echo $output;
           
            die();
        }


        public function showEditFormJavascript_callback() {
            $element   = $this->post( 'element' );
            $shortCode = $this->post( 'shortcode' );
            $shortCode = stripslashes( $shortCode );

            $this->removeShortCode( $element );
            $settings = SPBMap::getShortCode( $element );

            new SwiftPageBuilderShortcode_Settings( $settings );

            echo do_shortcode( $shortCode );

            die();
        }


        public function showSmallEditFormJavascript_callback() {

            $element_name = $this->post( 'element_name' );
            $tab_name     = $this->post( 'tab_name' );
            $icon         = $this->post( 'icon' );

            if ( $element_name == 'Tabs' ) {
                $singular_name = __( "Tab", 'swift-framework-plugin' );
            } else {
                $singular_name = __( "Section", 'swift-framework-plugin' );
            }

            echo '<div class="edit-small-modal"><h2>Edit ' . $element_name . ' Header</h2><div class="edit_form_actions"><a href="#" id="cancel-small-form-background">Cancel</a><a href="#" id="save-small-form" class="spb_save_edit_form button-primary">Save</a></div></div><div class="row-fluid"><div class="span4 spb_element_label">' . $singular_name . ' title</div><div class="span8 edit_form_line"><input name="small_form_title"  value="' . $tab_name . '" class="spb_param_value spb-textinput small_form_title textfield" type="text" value=""><p><span class="description">What text use as Tab title. Leave blank if no title is needed.</span></p></div></div><div class="row-fluid"><div class="span4 spb_element_label">' . $singular_name . ' Icon class</div><div class="span8 edit_form_line"><input name="small_form_icon" class="spb_param_value spb-textinput small_form_icon textfield" type="text" value="' . $icon . '"><p><span class="description">Specify your icon.</span></p></div></div>';

            die();
        }

        /* Save template callback
        ---------------------------------------------------------- */
        public function saveTemplateJavascript_callback() {
            $output        = '';
            $template_name = $this->post( 'template_name' );
            $template      = $this->post( 'template' );

            if ( ! isset( $template_name ) || $template_name == "" || ! isset( $template ) || $template == "" ) {
                echo 'Error: TPL-01';
                die();
            }

            $template_arr = array( "name" => $template_name, "template" => $template );

            $option_name     = 'spb_templates';
            $saved_templates = get_option( $option_name );

            $template_id = sanitize_title( $template_name ) . "_" . rand();
            if ( $saved_templates == false ) {
                $deprecated = '';
                $autoload   = 'no';
                
                $new_template                 = array();
                $new_template[ $template_id ] = $template_arr;
                
                add_option( $option_name, $new_template, $deprecated, $autoload );
            } else {
                $saved_templates[ $template_id ] = $template_arr;
                update_option( $option_name, $saved_templates );
            }
            $prebuilt_templates = spb_get_prebuilt_templates();
            
            foreach ( $prebuilt_templates as $template ) {
                $output .= '<li class="sf_prebuilt_template"><a href="#" data-template_id="' . $template['id'] . '"><span class="icon-swift-template"></span>' . $template['name'] . '</a></li>';
            }

            $output .= $this->getLayout()->getNavBar()->getTemplateMenu();

            echo $output;
            
            die();
        }

        /* Load template callback
        ---------------------------------------------------------- */
        public function loadTemplateJavascript_callback() {
            $output      = '';
            $template_id = $this->post( 'template_id' );

            if ( ! isset( $template_id ) || $template_id == "" ) {
                echo 'Error: TPL-02';
                die();
            }

            $option_name     = 'spb_templates';
            $saved_templates = get_option( $option_name );

            $content = $saved_templates[ $template_id ]['template'];
            $content = str_ireplace( '\"', '"', $content );
         
            echo do_shortcode( $content );

            die();
        }

        /* Delete template callback
        ---------------------------------------------------------- */
        public function deleteTemplateJavascript_callback() {
            $output      = '';
            $template_id = $this->post( 'template_id' );

            if ( ! isset( $template_id ) || $template_id == "" ) {
                echo 'Error: TPL-03';
                die();
            }

            $option_name     = 'spb_templates';
            $saved_templates = get_option( $option_name );

            unset( $saved_templates[ $template_id ] );
            if ( count( $saved_templates ) > 0 ) {
                update_option( $option_name, $saved_templates );
            } else {
                delete_option( $option_name );
            }

            echo $this->getLayout()->getNavBar()->getTemplateMenu();

            die();
        }

        /* Load pre-built template callback
        ---------------------------------------------------------- */
        public function loadSFTemplateJavascript_callback() {
            $output      = $content = '';
            $template_id = $this->post( 'template_id' );

            if ( ! isset( $template_id ) || $template_id == "" ) {
                echo 'Error: TPL-02';
                die();
            }

            $content = spb_get_prebuilt_template_code( $template_id );

            echo do_shortcode( $content );

            die();
        }


        /* Track most used Elements PB 
       ---------------------------------------------------------- */
        public function spb_track_used_elements() {
            
            $asset_count = 1;
            $type = $this->post( 'data_element_type' );
                                    
            if ( get_option( 'spb_most_used_elements' ) ){
                $spb_most_used_elements = get_option( 'spb_most_used_elements' );    
            }else{
                $spb_most_used_elements = array();
            }

            if( isset( $spb_most_used_elements[ $type ] ) ){
               $asset_count = intval( $spb_most_used_elements[ $type ]) + 1;
            }
            
            $spb_most_used_elements[ $type ] = $asset_count;
            update_option( 'spb_most_used_elements', $spb_most_used_elements );

            wp_die();

        }
        

       
       /* Save PB History 
       ---------------------------------------------------------- */
        public function spb_save_pb_history() {
            
            $element_name = $this->post( 'data_element' );        
            $type = $this->post( 'data_type' );
            $pb_content = $this->post( 'data_pb_content' );
            $pb_page_id = $this->post( 'data_page_id' );
            $op_type = __( $type, 'swift-framework-plugin' );
            $spb_history_array = get_post_meta( $pb_page_id, 'spb_history_' . $pb_page_id , true);

            if ( $spb_history_array == '' ) :
                 $spb_history_array = array();
            endif;
            
            if ( count( $spb_history_array ) >= 20 ) : 
                  array_shift( $spb_history_array ); 
            endif;

            $spb_history_array[ date("Y-m-d H:i:s") . ' - ' . $op_type . ' '.$element_name] = $pb_content;
            update_post_meta( $pb_page_id, 'spb_history_'. $pb_page_id, $spb_history_array); 
            $history_html = '<li><a href="#">Page Builder History</a></li><li class="divider"></li>';
            $spb_history = get_post_meta( $pb_page_id, 'spb_history_' . $pb_page_id , true);
            $spb_history = array_reverse($spb_history);

            foreach ($spb_history as $history_item => $value) :
                    $history_html .=  '<li><a href="#" data-revision-value="' .  esc_attr( $value ) . '"><span class="icon-undo"></span>' . $history_item . '</a></li>';
            endforeach;

            echo  $history_html ;
            wp_die();

        }
        

        /* Save element callback
        ---------------------------------------------------------- */
        public function saveElementJavascript_callback() {
            $output       = '';
            $element_name = $this->post( 'element_name' );
            $element      = $this->post( 'element' );

            if ( ! isset( $element_name ) || $element_name == "" || ! isset( $element ) || $element == "" ) {
                echo 'Error: TPL-01';
                die();
            }

            $element_arr = array( "name" => $element_name, "element" => $element );

            $option_name    = 'spb_elements';
            $saved_elements = get_option( $option_name );

            $element_id = sanitize_title( $element_name ) . "_" . rand();
            if ( $saved_elements == false ) {
                $deprecated = '';
                $autoload   = 'no';
                //
                $new_element                = array();
                $new_element[ $element_id ] = $element_arr;
                //
                add_option( $option_name, $new_element, $deprecated, $autoload );
            } else {
                $saved_elements[ $element_id ] = $element_arr;
                update_option( $option_name, $saved_elements );
            }

            echo $this->getLayout()->getNavBar()->getElementsMenu();

            die();
        }

        /* Delete element callback
        ---------------------------------------------------------- */
        public function deleteElementJavascript_callback() {
            $output     = '';
            $element_id = $this->post( 'element_id' );

            if ( ! isset( $element_id ) || $element_id == "" ) {
                echo 'Error: TPL-03';
                die();
            }

            $option_name    = 'spb_elements';
            $saved_elements = get_option( $option_name );

            unset( $saved_elements[ $element_id ] );
            if ( count( $saved_elements ) > 0 ) {
                update_option( $option_name, $saved_elements );
            } else {
                delete_option( $option_name );
            }

            echo $this->getLayout()->getNavBar()->getElementsMenu();

            die();
        }

        /* Load element callback
        ---------------------------------------------------------- */
        public function loadElementJavascript_callback() {
            $output     = '';
            $element_id = $this->post( 'element_id' );

            if ( ! isset( $element_id ) || $element_id == "" ) {
                echo 'Error: TPL-02';
                die();
            }

            $option_name    = 'spb_elements';
            $saved_elements = get_option( $option_name );

            $content = $saved_elements[ $element_id ]['element'];
            $content = str_ireplace( '\"', '"', $content );
            echo do_shortcode( $content );
            
            die(); 
        }
    }

class SwiftPageBuilderShortcode_default extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {
           
        }

}        
?>
