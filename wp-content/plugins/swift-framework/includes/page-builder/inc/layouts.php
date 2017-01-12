<?php

    /*
    *
    *	Swift Page Builder - Shortcode Mapper Class
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    class SPBLayoutButton implements SPBTemplateInterface {
        protected $params = Array();

        public function setup( $params ) {
            if ( empty( $params['id'] ) || empty( $params['title'] ) ) {
                trigger_error( "Wrong layout params" );
            }
            $this->params = (array) $params;

            return $this;
        }

        public function output( $post = null ) {
            if ( empty( $this->params ) ) {
                return '';
            }
            $output = "";
            if ( $this->params['id'] == "row" ) {
                $output = '<li class="row-option"><a id="' . $this->params['id'] . '" data-element="spb_row" data-width="' . $this->params['id'] . '" class="' . $this->params['id'] . ' clickable_layout_action dropable_column" href="#"><span>' . __( $this->params['title'], 'swift-framework-plugin' ) . '</span></a></li>';
            } else {
                $output = '<li><a id="' . $this->params['id'] . '" data-element="spb_column" data-width="' . $this->params['id'] . '" class="' . $this->params['id'] . ' clickable_layout_action dropable_column" href="#"><span>' . __( $this->params['title'], 'swift-framework-plugin' ) . '</span></a></li>';
            }

            return $output;
        }
    }


    class SPBTemplateMenuButton implements SPBTemplateInterface {
        protected $params = Array();
        protected $id;

        public function setID( $id ) {
            $this->id = (string) $id;

            return $this;
        }

        public function setup( $params ) {
            $this->params = (array) $params;

            return $this;
        }

        public function output( $post = null ) {
            if ( empty( $this->params ) ) {
                return '';  
            }
            $output = '<li class="sf_prebuilt_template spb_template_li"><span class="icon-templates"></span><a data-template_id="' . $this->id . '" href="#">' . __( $this->params['name'], 'swift-framework-plugin' ) . '</a> <span class="spb_remove_template"><span class="icon-delete"> </span> </span></li>';

            return $output;
        }
    }

    class SPBSavedElementsMenuButton implements SPBTemplateInterface {
        protected $params = Array();
        protected $id;

        public function setID( $id ) {
            $this->id = (string) $id;

            return $this;
        }

        public function setup( $params ) {
            $this->params = (array) $params;

            return $this;
        }

        public function output( $post = null ) {
            if ( empty( $this->params ) ) {
                return '';
            }
            
            $output = '<li class="spb_elements_li"><a data-element_id="' . $this->id . '" href="#">' . __( $this->params['name'], 'swift-framework-plugin' ) . '</a> <span class="spb_remove_element"><span class="icon-delete"></span> </span></li>';

            return $output;
        }
    }

    class SPBElementButton implements SPBTemplateInterface {
        protected $params = Array();
        protected $base;

        public function setBase( $base ) {
            $this->base = $base;

            return $this;
        }

        public function setup( $params ) {
            $this->params = $params;

            return $this;
        }

        protected function getIcon() {
            return ! empty( $this->params['icon'] ) ? '<span class="el_menu_icon ' . sanitize_title( $this->params['icon'] ) . '"></span> ' : '';
        }

        public function output( $post = null ) {
            if ( empty( $this->params ) ) {
                return '';
            }
            $output = $class = '';
            if ( isset( $this->params["class"] ) && $this->params["class"] != '' ) {
                $class_ar = explode( " ", $this->params["class"] );
                for ( $n = 0; $n < count( $class_ar ); $n ++ ) {
                    $class_ar[ $n ] .= "_nav";
                }
                $class = ' ' . implode( " ", $class_ar );
            }
            $output .= '<li class="' . $this->base . '"><a data-element="' . $this->base . '" id="' . $this->base . '" class="dropable_el clickable_action' . $class . '" href="#">' . $this->getIcon() . '<p>' . __( $this->params["name"], 'swift-framework-plugin' ) . '</p></a></li>';

            return $output;
        }
    }

    class SPBTemplateMenu implements SPBTemplateInterface {
        protected $params = Array();

        public function setup( $params ) {
            $this->params = (array) $params;

            return $this;
        }

        public function output( $post = null ) {
            if ( empty( $this->params ) ) {
                return '';
            }

            $output = '';
         
            $is_empty = true;
            foreach ( $this->params as $id => $template ) {
                if ( is_array( $template ) ) {
                    $template_button = new SPBTemplateMenuButton();
                    $output .= $template_button->setup( $template )->setID( $id )->output();
                    $is_empty = false;
                }
            }
           

            return $output;
        }
    }

    class SPBElementsMenu implements SPBTemplateInterface {
        protected $params = Array();

        public function setup( $params ) {
            $this->params = (array) $params;

            return $this;
        }

        public function output( $post = null ) {
            if ( empty( $this->params ) ) {
                return '';
            }
            
            $output   = '';
            $is_empty = true;
            foreach ( $this->params as $id => $element ) {
                if ( is_array( $element ) ) {
                    $element_button = new SPBSavedElementsMenuButton();
                    $output .= $element_button->setup( $element )->setID( $id )->output();
                    $is_empty = false;
                }
            }
            if ( $is_empty ) {
                $output .= '<li class="spb_no_elements"><span>' . __( 'You have not saved any elements yet.', 'swift-framework-plugin' ) . '</span></li>';
            }

            return $output;
        }
    }

    class SPBTemplate_r extends SFPageBuilderAbstract {

        protected $templates = Array();

        public function getMenu() {
            $template_menu = new SPBTemplateMenu();

            return $template_menu->setup( $this->getTemplatesList() )->output();
        }

        protected function getTemplates() {
            if ( $this->templates == null ) {
                $this->templates = (array) get_option( 'spb_templates' );
            }

            return $this->templates;
        }

        public function getTemplatesList() {
            return $this->getTemplates();
        }
    }

    class SPBElements_r extends SFPageBuilderAbstract {

        protected $elements = Array();

        public function getMenu() {
            $elements_menu = new SPBElementsMenu();

            return $elements_menu->setup( $this->getElementsList() )->output();
        }

        protected function getElements() {
            if ( $this->elements == null ) {
                $this->elements = (array) get_option( 'spb_elements' );
            }

            return $this->elements;
        }

        public function getElementsList() {
            return $this->getElements();
        }
    }

    class SPBNavBar implements SPBTemplateInterface {
        public function __construct() {

        }

        public function getColumnLayouts() {
            $output = '';
            foreach ( SPBMap::getLayouts() as $layout ) {
                $layout_button = new SPBLayoutButton();
                $output .= $layout_button->setup( $layout )->output();
            }

            return $output;
        }

        public function getContentLayouts( $el_type = null ) {

            $output = ''; 
            $element_button = new SPBElementButton();

            if ( $el_type == 'most_used_elements' ) {

                        $most_used_el = array();

                        if ( get_option( 'spb_most_used_elements' ) ){
                        
                             $spb_most_used_elements = get_option( 'spb_most_used_elements' );  
                             arsort( $spb_most_used_elements );
                             $spb_most_used_elements = array_slice($spb_most_used_elements, 0, 24);
                        
                             foreach ( $spb_most_used_elements as $used_element => $value ) {
                                 array_push(  $most_used_el, $used_element );
                             }

                        }
                       
            }

        

            foreach ( SPBMap::getShortCodes() as $sc_base => $el ) {
              
                    if ( $el_type == 'most_used_elements' ) {
                                               
                        if( in_array( $sc_base, $most_used_el ) ) {
                            $output .= $element_button->setBase( $sc_base )->setup( $el )->output();  
                          
                        }

                    }else if ( $el['name'] != 'Map Pin' && $el['name'] != 'Pricing Column' && $el['name'] != 'Pricing Column Feature' && $el['name'] != 'Multilayer Parallax Layer' && $el['name'] != 'Icon Box Grid Element'){

                        $media_pos = strpos( $el['class'], 'spb_tab_media');
                        $ui_pos = strpos( $el['class'], 'spb_tab_ui');
                        $layout_pos = strpos( $el['class'], 'spb_tab_layout');
                      
                        if ( isset($el['class']) &&  $media_pos === false  && $ui_pos === false &&  $layout_pos === false ) {
                            $el['class'] .=  ' spb_tab_misc';  
                        }
                     
                        $output .= $element_button->setBase( $sc_base )->setup( $el )->output();       
                    }
    

            }

            return $output;
        }

        public function getTemplateMenu() {
            $template_r = new SPBTemplate_r();

            return $template_r->getMenu();
        }

        public function getElementsMenu() {
            $elements_r = new SPBElements_r();

            return $elements_r->getMenu();
        }

           public function spb_get_js_translation_text(){

            return '<div class="spb_translated_objects"
                        
                        data-spb-delete-element-header="' . __( 'Delete Element.', 'swift-framework-plugin' ) . '" 
                        data-spb-deleted-element-q1="' . __( 'Do you want to delete this element?', 'swift-framework-plugin' ) . '" 
                        data-spb-deleted-element="' . __( 'Element deleted.', 'swift-framework-plugin' ) . '" 
                        data-spb-delete-pb-content-question="' . __( 'This will clear the contents of the page, are you sure?', 'swift-framework-plugin' ) . '" 
                        data-spb-deleted-pb-content="' . __( 'The content of the page builder was cleared.', 'swift-framework-plugin' ) . '" 
                        data-spb-tab-saved="' . __( 'Tab saved.', 'swift-framework-plugin' ) . '" 
                        data-spb-delete-tabs-section-q1="' . __( 'Do you want to delete this Tab?', 'swift-framework-plugin' ) . '" 
                        data-spb-delete-accordion-section-q1="' . __( 'Do you want to delete this Accordion Section?', 'swift-framework-plugin' ) . '" 
                        data-spb-save-element-header="' . __( 'Save Element', 'swift-framework-plugin' ) . '" 
                        data-spb-saved-template="' . __( 'Template saved.', 'swift-framework-plugin' ) . '" 
                        data-spb-save-template-header="' . __( 'Save Template', 'swift-framework-plugin' ) . '" 
                        data-spb-save-element-msg="' . __( 'Please enter a name to save the element has.', 'swift-framework-plugin' ) . '" 
                        data-spb-save-template-msg="' . __( 'Please enter a name to save the template has.', 'swift-framework-plugin' ) . '" 
                        data-spb-delete-tabs-header="' . __( 'Delete Tab', 'swift-framework-plugin' ) . '" 
                        data-spb-delete-accordion-header="' . __( 'Delete Section', 'swift-framework-plugin' ) . '" 
                        data-spb-clear-pb-header="' . __( 'Clear Page Builder Content', 'swift-framework-plugin' ) . '" 
                        data-spb-same-window="' . __( 'Same Window', 'swift-framework-plugin' ) . '" 
                        data-spb-new-window="' . __( 'New Window', 'swift-framework-plugin' ) . '"             
                        data-spb-right="' . __( 'Right', 'swift-framework-plugin' ) . '" 
                        data-spb-left="' . __( 'Left', 'swift-framework-plugin' ) . '" 
                        data-spb-plan-price="' . __( 'Plan Price', 'swift-framework-plugin' ) . '" 
                        data-spb-yes="' . __( 'Yes', 'swift-framework-plugin' ) . '" 
                        data-spb-no="' . __( 'No', 'swift-framework-plugin' ) . '" 
                        data-spb-extra-class="' . __( 'Extra Class', 'swift-framework-plugin' ) . '" 
                        data-spb-link-url="' . __( 'Button Link URL', 'swift-framework-plugin' ) . '" 
                        data-spb-btn-text-color="' . __( 'Button Text Color', 'swift-framework-plugin' ) . '" 
                        data-spb-btn-text="' . __( 'Button Text', 'swift-framework-plugin' ) . '" 
                        data-spb-plan-period="' . __( 'Plan Period', 'swift-framework-plugin' ) . '" 
                        data-spb-btn-bg-color="' . __( 'Button Background Color', 'swift-framework-plugin' ) . '" 
                        data-spb-header-bg-color="' . __( 'Header Background Color', 'swift-framework-plugin' ) . '" 
                        data-spb-plan-name="' . __( 'Plan Name', 'swift-framework-plugin' ) . '" 
                        data-spb-plan-price-currency-text="' . __( 'Plan Price Currency Text', 'swift-framework-plugin' ) . '" 
                        data-spb-highlight-column="' . __( 'Highlight Column', 'swift-framework-plugin' ) . '" 
                        data-spb-plan-price-currency-position="' . __( 'Plan Price Currency Position', 'swift-framework-plugin' ) . '" 
                        data-spb-link-target="' . __( 'Link Target', 'swift-framework-plugin' ) . '" 
                        data-spb-full-width="' . __( 'Full Width', 'swift-framework-plugin' ) . '" 
                        data-spb-features="' . __( 'Features', 'swift-framework-plugin' ) . '" 
                        data-spb-feature-name="' . __( 'Feature Name', 'swift-framework-plugin' ) . '" 
                        data-spb-bg-color="' . __( 'Background Color', 'swift-framework-plugin' ) . '" 
                        data-spb-delete-saved-element-header="' . __( 'Delete Saved Element', 'swift-framework-plugin' ) . '" 
                        data-spb-delete-page-template-header="' . __( 'Delete Page Template', 'swift-framework-plugin' ) . '" 
                        data-spb-add-feature="' . __( 'Add Feature', 'swift-framework-plugin' ) . '" 
                        data-spb-plan-settings="' . __( 'Plan Settings', 'swift-framework-plugin' ) . '" 
                        data-spb-delete-saved-element-msg1="' . __( 'Do you want to delete this saved Element?', 'swift-framework-plugin' ) . '" 
                        data-spb-delete-saved-template-msg1="' . __( 'Do you want to delete this saved page template?', 'swift-framework-plugin' ) . '" 
                        data-spb-new-plan="' . __( 'New Plan', 'swift-framework-plugin' ) . '" 
                        data-spb-error-row-add="' . __( 'Not possible to add that element here.', 'swift-framework-plugin' ) . '" 
                        data-spb-saved_element="' . __( 'Element saved.', 'swift-framework-plugin' ) . '"     ></div>';

        }

        
        public function output( $post = null ) {

            $prebuilt_templates = spb_get_prebuilt_templates();
            global $sf_opts, $post;
            
            if( !isset( $sf_opts['spb_color_scheme'] ) ){
              $sf_opts['spb_color_scheme'] = 'spb-blue';
            }
            
            $output = $this->spb_get_js_translation_text(); 
            $output .= '
	            <div id="spb-elements" class="navbar" data-color-scheme="' . $sf_opts['spb_color_scheme'] . '">
	                <div class="navbar-inner">
	                    <div class="container">
	                        <div class="nav-collapse">';
                            
            $output .='<ul class="nav pull-left">
	                        <li class="dropdown content-dropdown"><a class="dropdown-toggle spb_content_elements" data-slideout="spb-content-elements" href="#"><span class="icon-add pb-main-icon"></span>' . __( "Add Element", 'swift-framework-plugin' ) . ' <b class="caret"></b></a>';
                           
	        $output .= '</li></ul>';
            $output .= '<ul class="nav pull-left custom-elements-nav">
	                        <li class="dropdown">
	                            <a class="dropdown-toggle spb_custom_elements" data-slideout="spb-custom-elements" href="#"><span class="icon-save pb-main-icon"></span>' . __( 'Saved Items', 'swift-framework-plugin' ) . ' <b class="caret"></b></a>  
                                       
	                        </li>  
	                    </ul>';

            // SPB History code
            $output .= '<ul class="nav pull-left icon-only page-builder-revisions">
                                    <li>
                                        <a id="spb-revisions" href="#" data-activates="dropdown-history" class="dropdown-button"><span class="icon-undo pb-main-icon"></span></a><ul id="dropdown-history"  style="display:none;" class="dropdown-content">';
            $output .= '<li><a href="#">Page Builder History</a></li><li class="divider"></li>';        
                                                                                      
                                            $spb_history = get_post_meta($post->ID, 'spb_history_' . $post->ID , true);

                                            if ( isset( $spb_history ) && is_array( $spb_history ) ) {                                                                                     
                                                $spb_history = array_reverse($spb_history);

                                                if( count(  $spb_history ) > 0 ){
                                                    foreach ($spb_history as $history_item => $value) {
                                                        $output .=  '<li><a href="#" data-revision-value="' .  esc_attr($value) . '"><span class="icon-undo"></span>' . $history_item . '</a></li>';
                                                    }
                                                }
                                            } else{
                                                $output .=  '<li><a href="#">No history.</a>';
                                            }

            $output .= '</ul></li></ul>';

            $output .= '<ul class="nav pull-left icon-only page-builder-preview">
                             <li>
                                 <a id="spb-preview" href="#"><span class="icon-preview pb-main-icon"></span></a>
                             </li>
                        </ul>';

            $output .= '<ul class="nav pull-left icon-only clear-page-builder">
                             <li>
                                 <a id="clear-spb" href="#"><span class="icon-delete pb-main-icon"></span></a>
                             </li>
                        </ul>'; 

            $output .= '<ul class="nav pull-left icon-only save-page-template">
	                       	<li>
	                      		<a id="spb_save_template" href="#"><span class="icon-save pb-main-icon"></span></a>
	                       	</li>
	                    </ul>';

            $output .= '<ul class="nav icon-only pull-right">
	                       	<li>
	                       		<a id="fullscreen-spb" href="#"><span class="icon-fullscreen pb-main-icon"></span></a>
	                       	</li>
	                       	<li>
	                       		<a id="close-fullscreen-spb" href="#"><i class="fa-close"></i></a>
	                       	</li>
	                    </ul>';

            $output .= '<ul class="nav pull-right icon-only custom-elements-nav previewpage-spb" style="display:none;">
	                       	<li>
	                       		<a id="previewpage-spb" href="#" target="wp-preview">Preview</a>
	                       	</li>
	                    </ul>';

            $output .= '</div><!-- /.nav-collapse --> </div></div></div>';
 
            $output .= '<div id="spb-option-slideout"> 
                            <div class="spb_elements_tabs_header"><div class="page_builder_elements_header"><div class="spb_search_tab">All</div><div class="spb_med_tab" >' . __( "Media", 'swift-framework-plugin' ) . '</div><div class="spb_ui_tab">' . __( "U.I.", 'swift-framework-plugin' ) . '</div>
                                <div class="spb_misc_tab">' . __( "Misc.", 'swift-framework-plugin' ) . '</div><div class="spb_layout_tab">' . __( "Layout", 'swift-framework-plugin' ) . '</div><div class="spb_saved_el_tab">' . __( "Saved Elements", 'swift-framework-plugin' ) . '</div><div class="vert-divider-wrapper"><div class="vert-divider-asset"></div></div><div class="spb_most_used"><span class="icon-frequently-used"></span></div><div class="vert-divider-wrapper"><div class="vert-divider-asset"></div></div><div class="elements_controls_tab"><span class="icon-search"></span><input type="text" class="rwmb-text" name="sf_search_elements" id="sf_search_elements" value="" placeholder="' . __( "Search", 'swift-framework-plugin' ) . '" ></span></div><div class="tab_closing"><span class="icon-close"></div></div>';

            /* Saved Elements */ 
            $output .= '<div class="page_builder_saved_elements"><div class="spb_saved_el_tab active_tab" >' . __( "Saved Elements", 'swift-framework-plugin' ) . '</div><div class="spb_saved_pages_el_tab">' . __( "Page Templates", 'swift-framework-plugin' ) . '</div><div class="vert-divider-wrapper"><div class="vert-divider-asset"></div></div><div class="elements_controls_tab"><span class="icon-search"></span><input type="text" class="rwmb-text" name="sf_search_saved_elements" id="sf_search_saved_elements" value="" placeholder="Search" ></div><div class="tab_closing"><span class="icon-close"></div></div></div>';
	        $output .= '<div class="spb-elements-no-results"><h2>' . __( "No results found.", 'swift-framework-plugin' ) . '</h2></div><ul class="spb-content-elements clearfix spb-item-slideout">' . $this->getContentLayouts("media") . '</ul>';
            $output .= '<ul class="spb-most-used-elements clearfix spb-item-slideout">' . $this->getContentLayouts("most_used_elements") . '</ul>';
            $output .= '<ul class="spb-content-elements-ui clearfix spb-item-slideout">' . $this->getContentLayouts("ui") . '</ul>';
            $output .= '<ul class="spb-content-elements-misc clearfix spb-item-slideout">' . $this->getContentLayouts("misc") . '</ul>';
            $output .= '<ul class="spb-content-elements-layout clearfix spb-item-slideout">' . $this->getContentLayouts("layout") . '</ul>';
            $output .= '<ul class="spb-content-elements-search clearfix spb-item-slideout">' . $this->getContentLayouts("all") . '</ul>';
            $output .= '<ul class="spb-content-elements-saved clearfix spb-item-slideout">' . $this->getElementsMenu() . '</ul>';


            /* Saved pages & Pre-Built Pages */ 
            $output .= '<ul class="spb-prebuilt-pages spb-item-slideout clearfix">'; 
                    
            foreach ( $prebuilt_templates as $template ) {
                $output .= '<li class="sf_prebuilt_template"><span class="icon-swift-template"></span><a href="#" data-template_id="' . $template['id'] . '">' . $template['name'] . '</a></li>';
            }

            $output .= $this->getTemplateMenu();
            $output .= '</ul>
	            </div>
	            <style type="text/css">#spb {display: none;}</style>';


            return $output;
        }
    }

    class SPBLayout implements SPBTemplateInterface {
        protected $navBar;

        public function __construct() {

        }

        public function getNavBar() {
            if ( $this->navBar == null ) {
                $this->navBar = new SPBNavBar();
            }

            return $this->navBar;
        }

        public function getContainerHelper() {
            
            $cont_help = "";

            $cont_help .= '<div class="container-helper">';
            $cont_help .= '<a href="#" class="add-element-to-column btn-floating waves-effect waves-light"><span class="icon-add"></span></a>';
            $cont_help .= '</div>';

            return $cont_help;
        }

        public function output( $post = null ) {

            $output = '';

            $output .= $this->getNavBar()->output();

            $id = rand(10000,20000);

            $output .= '<div class="metabox-builder-content">
						<div id="spb_edit_form"></div>
						<div id="spb_content" class="spb_main_sortable main_wrapper row-fluid spb_sortable_container">
                            <div class="spb-loading-message">
                                <div class="sf-svg-loader">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="32px" height="32px" viewBox="0 0 32 32">
                                        <g>
                                            <path opacity="0.4" fill="#444444" d="M16,32C7.17773,32,0,24.82227,0,16S7.17773,0,16,0s16,7.17773,16,16S24.82227,32,16,32z M16,4
                    C9.3833,4,4,9.3833,4,16s5.3833,12,12,12s12-5.3833,12-12S22.6167,4,16,4z"/>
                                            <path class="nc-circle-03-linear-'.$id.'" data-color="color-2" fill="#444444" d="M32,16h-4c0-6.6167-5.3833-12-12-12V0C24.82227,0,32,7.17773,32,16z" transform="rotate(291.40284640448436 16 16)"/>
                                        </g>
                                    </svg>
                                    <script type="text/javascript">function stepCircleThreeLin(t){startxCircle8||(startxCircle8=t);var e=t-startxCircle8,n=Math.min(e/1.4,360);504>e||(startxCircle8+=504),window.requestAnimationFrame(stepCircleThreeLin),pathxCircle8.setAttribute("transform","rotate("+n+" 16 16)")}!function(){var t=0;window.requestAnimationFrame||(window.requestAnimationFrame=function(e){var n=(new Date).getTime(),r=Math.max(0,16-(n-t)),i=window.setTimeout(function(){e(n+r)},r);return t=n+r,i})}();var pathxCircle8=document.getElementsByClassName("nc-circle-03-linear-'.$id.'")[0],startxCircle8=null;window.requestAnimationFrame(stepCircleThreeLin);</script>
                                </div>
                                <div class="loading_text">' . __( "Loading, please wait...", 'swift-framework-plugin' ) . '</div>
                            </div>
						</div>
						<div id="spb-empty">
                            <div class="unhappy-face"></div>
							<h2>' . __( "there's nothing here yet,<br> add something to get started", 'swift-framework-plugin' ) . '</h2> 
							<div class="container-helper"><a href="javascript:open_elements_dropdown();" class="open-dropdown-content-element step-one btn-floating waves-effect waves-light"><span class="icon-add"></span></a></div>
						</div>
					</div><div id="container-helper-block" style="display: none;">' . $this->getContainerHelper() . '</div>';

            $spb_status = spb_get_post_meta( $post->ID, '_spb_status', true );
            if ( $spb_status == "" || ! isset( $spb_status ) ) {
                $spb_status = 'false';
            }
            $output .= '<input type="hidden" id="spb_status" name="spb_status" value="' . $spb_status . '" />';
            $output .= '<input type="hidden" id="spb_loading" name="spb_loading" value="' . __( "Loading, please wait...", 'swift-framework-plugin' ) . '" />';

            $clone_id = rand(20000,30000);

            $output .= '<div class="spb-svg-loader clone hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="32px" height="32px" viewBox="0 0 32 32">
                                        <g>
                                            <path opacity="0.4" fill="#444444" d="M16,32C7.17773,32,0,24.82227,0,16S7.17773,0,16,0s16,7.17773,16,16S24.82227,32,16,32z M16,4
                    C9.3833,4,4,9.3833,4,16s5.3833,12,12,12s12-5.3833,12-12S22.6167,4,16,4z"/>
                                            <path class="nc-circle-03-linear-'.$clone_id.'" data-color="color-2" fill="#444444" d="M32,16h-4c0-6.6167-5.3833-12-12-12V0C24.82227,0,32,7.17773,32,16z" transform="rotate(291.40284640448436 16 16)"/>
                                        </g>
                                    </svg>
                                    <script type="text/javascript">function stepCircleThreeLin(t){startxCircle8||(startxCircle8=t);var e=t-startxCircle8,n=Math.min(e/1.4,360);504>e||(startxCircle8+=504),window.requestAnimationFrame(stepCircleThreeLin),pathxCircle8.setAttribute("transform","rotate("+n+" 16 16)")}!function(){var t=0;window.requestAnimationFrame||(window.requestAnimationFrame=function(e){var n=(new Date).getTime(),r=Math.max(0,16-(n-t)),i=window.setTimeout(function(){e(n+r)},r);return t=n+r,i})}();var pathxCircle8=document.getElementsByClassName("nc-circle-03-linear-'.$clone_id.'")[0],startxCircle8=null;window.requestAnimationFrame(stepCircleThreeLin);</script>
                                </div>';

            echo $output;
        }
    }

?>
