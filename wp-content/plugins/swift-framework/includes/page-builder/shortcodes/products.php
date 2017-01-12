<?php

    /*
    *
    *	Swift Page Builder - Products Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_products_mini extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $title = $asset_type = $width = $el_class = $output = $items = $el_position = '';

            extract( shortcode_atts( array(
                'title'       => '',
                'asset_type'  => 'best-sellers',
                'item_count'  => '4',
                'category'    => '',
                'el_position' => '',
                'width'       => '1/4',
                'el_class'    => ''
            ), $atts ) );


            /* SIDEBAR CONFIG
            ================================================== */
            $sidebar_config = spb_get_post_meta( get_the_ID(), 'sf_sidebar_config', true );

            $sidebars = '';
            if ( ( $sidebar_config == "left-sidebar" ) || ( $sidebar_config == "right-sidebar" ) ) {
                $sidebars = 'one-sidebar';
            } else if ( $sidebar_config == "both-sidebars" ) {
                $sidebars = 'both-sidebars';
            } else {
                $sidebars = 'no-sidebars';
            }

            /* PRODUCT ITEMS
            ================================================== */
            if ( sf_woocommerce_activated() ) {
                $items = sf_mini_product_items( $asset_type, $category, $item_count, $sidebars, $width );
            } else {
                $items = __( "Please install/activate WooCommerce.", 'swift-framework-plugin' );
            }

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="product_list_widget woocommerce spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, '' ) : '';
            $output .= "\n\t\t" . $items;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            global $sf_has_products;
            $sf_has_products = true;

            return $output;

        }
    }

    SPBMap::map( 'spb_products_mini', array(
        "name"   => __( "Products (Mini)", 'swift-framework-plugin' ),
        "base"   => "spb_products_mini",
        "class"  => "spb-products-mini",
        "icon"   => "icon-products-mini",
        "params" => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "value"       => "",
                "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Asset type", 'swift-framework-plugin' ),
                "param_name"  => "asset_type",
                "value"       => array(
                    __( 'Best Sellers', 'swift-framework-plugin' )      => "best-sellers",
                    __( 'Latest Products', 'swift-framework-plugin' )   => "latest-products",
                    __( 'Top Rated', 'swift-framework-plugin' )         => "top-rated",
                    __( 'Sale Products', 'swift-framework-plugin' )     => "sale-products",
                    __( 'Recently Viewed', 'swift-framework-plugin' )   => "recently-viewed",
                    __( 'Featured Products', 'swift-framework-plugin' ) => "featured-products"
                ),
                "description" => __( "Select the order of the products you'd like to show.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Product category", 'swift-framework-plugin' ),
                "param_name"  => "category",
                "value"       => "",
                "description" => __( "Optionally, provide the category slugs for the products you want to show (comma seperated). i.e. trainer,dress,bag.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "class"       => "",
                "heading"     => __( "Number of items", 'swift-framework-plugin' ),
                "param_name"  => "item_count",
                "value"       => "4",
                "description" => __( "The number of products to show.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                "param_name"  => "el_class",
                "value"       => "",
                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
            )
        )
    ) );


    class SwiftPageBuilderShortcode_spb_products extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $title = $asset_type = $carousel = $width = $sidebars = $el_class = $output = $items = $el_position = '';

            extract( shortcode_atts( array(
                'title'          => '',
                'asset_type'     => 'best-sellers',
                'category'       => '',
                'products'		 => '',
                'display_type'	 => '',
                'display_layout' => '',
                'carousel'       => 'no',
                'multi_masonry'	 => 'no',
                'fullwidth'      => 'no',
                'gutters'        => 'yes',
                'columns'        => '4',
                'item_count'     => '8',
                'order_by'		 => '',
                'order'			 => '',
                'button_enabled' => 'no',
                'el_position'    => '',
                'width'          => '1/1',
                'el_class'       => ''
            ), $atts ) );

            $width    = spb_translateColumnWidthToSpan( $width );

            if ( isset($atts['display_layout']) && $atts['display_layout'] == "grid" ) {
                $atts['display_layout'] = "standard";
            }

            $view_all_icon = apply_filters( 'sf_view_all_icon' , '<i class="ss-layergroup"></i>' );


            // Enqueue script
            if ( $multi_masonry == "yes" ) {
                wp_enqueue_script( 'isotope' );
                wp_enqueue_script( 'isotope-packery' );
            }
            

            /* SIDEBAR CONFIG
            ================================================== */
            global $sf_sidebar_config;

            $sidebars = '';
            if ( ( $sf_sidebar_config == "left-sidebar" ) || ( $sf_sidebar_config == "right-sidebar" ) ) {
                $sidebars = 'one-sidebar';
            } else if ( $sf_sidebar_config == "both-sidebars" ) {
                $sidebars = 'both-sidebars';
            } else {
                $sidebars = 'no-sidebars';
            }

            /* FULL WIDTH CONFIG
            ================================================== */
            if ( $fullwidth == "yes" && $sidebars == "no-sidebars" && $width == "col-sm-12" ) {
                $fullwidth = true;
            } else {
                $fullwidth = false;
            }


            /* PRODUCT ITEMS
            ================================================== */
            if ( $asset_type == "categories" ) {
                $atts['multi_masonry'] = "no";
            }
            if ( sf_woocommerce_activated() ) {
                $items = sf_product_items( $atts );
            } else {
                $items = __( "Please install/activate WooCommerce.", 'swift-framework-plugin' );
            }


            /* OUTPUT
            ================================================== */
            global $sf_product_display_layout;
            if ( $display_layout != "" ) {
                $sf_product_display_layout = $display_layout;
            }

            $el_class = $this->getExtraClass( $el_class, $fullwidth );
            
            $page_button   = $title_wrap_class = "";
            $has_button    = true;
            $shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );
            $page_button   = '<a class="sf-button medium white sf-icon-stroke " href="' . $shop_page_url . '">' . $view_all_icon . '<span class="text">' . __( "VIEW ALL PRODUCTS", 'swift-framework-plugin' ) . '</span></a>';

            if ( $has_button && $button_enabled == "yes" ) {
                $title_wrap_class .= 'has-button ';
            }
            if ( $fullwidth ) {
                $title_wrap_class .= 'container ';
            }

            $output .= "\n\t" . '<div class="product_list_widget woocommerce spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= "\n\t\t" . '<div class="title-wrap clearfix ' . $title_wrap_class . '">';
            if ( $title != '' ) {
                if ( $fullwidth == "yes" ) {
                    $output .= '<h2 class="spb-heading"><span>' . $title . '</span></h2>';
                } else {
                    $output .= '<h3 class="spb-heading"><span>' . $title . '</span></h3>';
                }
            }
            if ( $carousel == "yes" ) {
                $output .= spb_carousel_arrows();
            }
            if ( $has_button && $button_enabled == "yes" ) {
                $output .= $page_button;
            }
            $output .= '</div>';

            $output .= "\n\t\t" . $items;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', $fullwidth ) . $output . $this->endRow( $el_position, '', $fullwidth );

            if ( $carousel == "yes" ) {
                global $sf_include_carousel;
                $sf_include_carousel = true;

            }
            global $sf_include_isotope, $sf_has_products;
            $sf_include_isotope = true;
            $sf_has_products    = true;

            return $output;

        }
    }

    /* PARAMS
    ================================================== */
    $product_display_type = array(
                __( 'Standard', 'swift-framework-plugin' )  => "standard",
                __( 'Gallery', 'swift-framework-plugin' )   => "gallery",
                __( 'Gallery Bordered', 'swift-framework-plugin' )   => "gallery-bordered",
            );

    if ( spb_theme_supports('product-preview-slider') ) {
        $product_display_type = array(
                __( 'Standard', 'swift-framework-plugin' )  => "standard",
                __( 'Gallery', 'swift-framework-plugin' )   => "gallery",
                __( 'Gallery Bordered', 'swift-framework-plugin' )   => "gallery-bordered",
                __( 'Preview Slider', 'swift-framework-plugin' )   => "preview-slider",
            );
    }

    $product_display_layout = array(
                __( 'Standard', 'swift-framework-plugin' )  => "standard",
                __( 'List', 'swift-framework-plugin' )   => "list",
            );

    $product_display_type = apply_filters( 'spb_product_display_types', $product_display_type );
    $product_display_layout = apply_filters( 'spb_product_display_layouts', $product_display_layout );

    $params = array(
        array(
            "type"        => "textfield",
            "heading"     => __( "Widget title", 'swift-framework-plugin' ),
            "param_name"  => "title",
            "value"       => "",
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Asset type", 'swift-framework-plugin' ),
            "param_name"  => "asset_type",
            "value"       => array(
                __( 'Best Sellers', 'swift-framework-plugin' )      => "best-sellers",
                __( 'Latest Products', 'swift-framework-plugin' )   => "latest-products",
                __( 'Top Rated', 'swift-framework-plugin' )         => "top-rated",
                __( 'Sale Products', 'swift-framework-plugin' )     => "sale-products",
                __( 'Recently Viewed', 'swift-framework-plugin' )   => "recently-viewed",
                __( 'Featured Products', 'swift-framework-plugin' ) => "featured-products",
                __( 'Categories', 'swift-framework-plugin' )		=> "categories",
                __( 'Selected Products', 'swift-framework-plugin' ) => "selected-products"
            ),
        ),
        array(
            "type"        => "select-multiple-id",
            "heading"     => __( "Product category", 'swift-framework-plugin' ),
            "param_name"  => "category",
            "value"       => spb_get_product_categories(),
            "required"       => array("asset_type", "not", "selected-products"),
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Products", 'swift-framework-plugin' ),
            "param_name"  => "products",
            "value"       => "",
            "required"       => array("asset_type", "=", "selected-products"),
            "description" => __( "Select specific products to show here, providing the Product ID in comma delimited format.", 'swift-framework-plugin' )
        ),
    );

    $params[] = array(
        "type"        => "dropdown",
        "heading"     => __( "Product display type", 'swift-framework-plugin' ),
        "param_name"  => "display_type",
        "required"       => array("asset_type", "!=", "categories"),
        "value"       => $product_display_type
    );

    if ( spb_theme_supports('product-layout-opts') ) {
        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Product display layout", 'swift-framework-plugin' ),
            "param_name"  => "display_layout",
            "required"       => array("asset_type", "!=", "categories"),
            "value"       => $product_display_layout
        );
    }

    if ( spb_theme_supports('product-multi-masonry') ) {
    	$params[] = array(
    		    "type"        => "buttonset",
    		    "heading"     => __( "Multi-Masonry display", 'swift-framework-plugin' ),
    		    "param_name"  => "multi_masonry",
    		    "value"       => array(
    		        __( 'Yes', 'swift-framework-plugin' ) => "yes",
                    __( 'No', 'swift-framework-plugin' )  => "no"
    		    ),
                "std"         => "no",
                "required"       => array("asset_type", "!=", "categories"),
                "buttonset_on"  => "yes",
    		    "description" => __( "Select if you'd like the asset to show products in a Multi-Masonry layout. NOTE: Not supported with Preview Slider display type.", 'swift-framework-plugin' )
    	);
    }

    $params[] = array(
    	    "type"        => "buttonset",
    	    "heading"     => __( "Carousel", 'swift-framework-plugin' ),
    	    "param_name"  => "carousel",
    	    "value"       => array(
    	        __( 'No', 'swift-framework-plugin' )  => "no",
    	        __( 'Yes', 'swift-framework-plugin' ) => "yes",
    	    ),
            "buttonset_on"  => "yes",
    	    "required"       => array("multi_masonry", "!=", "yes"),
    	);
   $params[] = array(
    	    "type"        => "buttonset",
    	    "heading"     => __( "Full Width", 'swift-framework-plugin' ),
    	    "param_name"  => "fullwidth",
    	    "value"       => array(
    	        __( 'No', 'swift-framework-plugin' )  => "no",
    	        __( 'Yes', 'swift-framework-plugin' ) => "yes"
    	    ),
            "buttonset_on"  => "yes",
            "std" => "no",
    	);
    $params[] = array(
    	    "type"        => "dropdown",
    	    "heading"     => __( "Column count", 'swift-framework-plugin' ),
    	    "param_name"  => "columns",
    	    "value"       => array( "1", "2", "3", "4", "5", "6" ),
    	    "std"         => "4",
            "required"    => array("multi_masonry", "=", "no"),
    	);
   $params[] = array(
    	    "type"        => "textfield",
    	    "class"       => "",
    	    "heading"     => __( "Number of items", 'swift-framework-plugin' ),
    	    "param_name"  => "item_count",
    	    "value"       => "8",
    	    "required"       => array("asset_type", "!=", "selected-products"),
    	);
    $params[] = array(
    	    "type"        => "dropdown",
    	    "heading"     => __( "Order by", 'swift-framework-plugin' ),
    	    "param_name"  => "order_by",
    	    "value"       => array(
    	        __( 'Default', 'swift-framework-plugin' ) => "",
    	        __( 'ID', 'swift-framework-plugin' )  => "ID",
    	        __( 'Title', 'swift-framework-plugin' )  => "title",
    	        __( 'Date', 'swift-framework-plugin' )  => "date",
    	        __( 'Random', 'swift-framework-plugin' )  => "rand",
    	    ),
    	    "required"       => array("multi_masonry", "=", "no"),
    	);
    $params[] = array(
    	    "type"        => "dropdown",
    	    "heading"     => __( "Order", 'swift-framework-plugin' ),
    	    "param_name"  => "order",
    	    "value"       => array(
    	        __( "Descending", 'swift-framework-plugin' ) => "DESC",
    	        __( "Ascending", 'swift-framework-plugin' )  => "ASC"
    	    ),
    	);
    $params[] = array(
    	    "type"        => "buttonset",
    	    "heading"     => __( "Shop Page Button", 'swift-framework-plugin' ),
    	    "param_name"  => "button_enabled",
    	    "value"       => array(
    	        __( 'No', 'swift-framework-plugin' )  => "no",
    	        __( 'Yes', 'swift-framework-plugin' ) => "yes"
    	    ),
            "buttonset_on"  => "yes",
    	    "description" => __( "Select if you'd like to include a button in the title bar to link through to all shop items.", 'swift-framework-plugin' )
   		);
    $params[] = array(
    	    "type"        => "textfield",
    	    "heading"     => __( "Extra class", 'swift-framework-plugin' ),
    	    "param_name"  => "el_class",
    	    "value"       => "",
    	    "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
    	);


	/* SHORTCODE MAP
	================================================== */
	SPBMap::map( 'spb_products', array(
	    "name"   => __( "Products", 'swift-framework-plugin' ),
	    "base"   => "spb_products",
	    "class"  => "spb-products",
	    "icon"   => "icon-products",
	    "params" => $params
	) );
