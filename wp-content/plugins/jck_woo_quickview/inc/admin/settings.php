<?php
add_filter( 'wpsf_register_settings_jckqv', 'jckqv_settings' );

/**
 * Quickview Settings
 *
 * @param arr $wpsf_settings
 * @return arr
 */
function jckqv_settings( $wpsf_settings ) {

    $wpsf_settings = array(

        'tabs' => array(
            array(
                'id' => 'trigger',
                'title' => __('Quickview Trigger Settings', 'jckqv')
            ),
            array(
                'id' => 'popup',
                'title' => __('Popup Settings', 'jckqv')
            )
        ),

        'sections' => array(
            array(
                'tab_id' => 'trigger',
                'section_id' => 'general',
                'section_title' => 'General',
                'section_description' => '',
                'fields' => array(
                    array(
                        'id' => 'method',
                        'title' => __('Quickview Method', 'jckqv'),
                        'subtitle' => '',
                        'type' => 'select',
                        'default' => 'click',
                        'placeholder' => '',
                        'choices' => array(
                            'mouseover' => __('Hover Quickview Button', 'jckqv'),
                            'click' => __('Click Quickview Button', 'jckqv')
                        )
                    )
                )
            ),


            array(
                'tab_id' => 'trigger',
                'section_id' => 'position',
                'section_title' => 'Positioning',
                'section_description' => '',
                'fields' => array(
                    array(
                        'id' => 'autoinsert',
                        'title' => __('Automatically insert Button?', 'jckqv'),
                        'subtitle' => __('Would you like Quickview to attempt to automatically insert the Quickview button?<br><br> For alternative insertion options, please see <a href="http://www.jckemp.com/plugins/woocommerce-quickview/#manually_insert_the_quickview_button" target="_blank">the documentation</a>.', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 1,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'position',
                        'title' => __('Button Position', 'jckqv'),
                        'subtitle' => __('If you chose to automatically insert the button, where should it be displayed?', 'jckqv'),
                        'type' => 'select',
                        'default' => 'beforetitle',
                        'placeholder' => '',
                        'choices' => array(
                            'beforeitem' => __('Before Item', 'jckqv'),
                            'beforetitle' => __('Before Title', 'jckqv'),
                            'aftertitle' => __('After Title', 'jckqv'),
                            'afteritem' => __('After Item', 'jckqv')
                        )
                    ),
                    array(
                        'id' => 'align',
                        'title' => __('Button Align', 'jckqv'),
                        'type' => 'select',
                        'default' => 'left',
                        'placeholder' => '',
                        'choices' => array(
                            'left' => __('Left', 'jckqv'),
                            'center' => __('Centre', 'jckqv'),
                            'right' => __('Right', 'jckqv'),
                            'none' => __('None', 'jckqv')
                        )
                    ),
                    array(
                        'id' => 'margins',
                        'title' => __('Margins', 'jckqv'),
                        'type' => 'multiinputs',
                        'subtitle' => __('Enter a pixel value (positive/negative) to offset the quickview button.', 'jckqv'),
                        'default' => array(
                            'Top' => 0,
                            'Right' => 0,
                            'Bottom' => 10,
                            'Left' => 0
                        ),
                        'placeholder' => '',
                    )
                )
            ),
            array(
                'tab_id' => 'trigger',
                'section_id' => 'styling',
                'section_title' => 'Styling',
                'section_description' => '',
                'fields' => array(
                    array(
                        'id' => 'autohide',
                        'title' => __('Auto-hide Button?', 'jckqv'),
                        'subtitle' => __('Should the quickview button only show when the product is hovered?', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 0,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'hoverel',
                        'title' => __('Button Parent', 'jckqv'),
                        'type' => 'text',
                        'subtitle' => __('If the button is set to autohide, enter a parent class, id, or element that should display the quickview button when hovered.', 'jckqv'),
                        'default' => __('.product', 'jckqv'),
                        'placeholder' => '',
                    ),
                    array(
                        'id' => 'icon',
                        'title' => __('Icon', 'jckqv'),
                        'subtitle' => __('Choose the Icon to use on the Quickview button', 'jckqv'),
                        'type' => 'select',
                        'default' => 'eye',
                        'placeholder' => '',
                        'choices' => array(
                            'none' => __('No Icon', 'jckqv'),
                            'search' => __('Magnifier', 'jckqv'),
                            'eye' => __('Eye', 'jckqv'),
                            'plus' => __('Plus Symbol', 'jckqv')
                        )
                    ),
                    array(
                        'id' => 'text',
                        'title' => __('Quickview Text', 'jckqv'),
                        'type' => 'text',
                        'default' => __('Quickview', 'jckqv'),
                        'placeholder' => '',
                    ),
                    array(
                        'id' => 'btnstyle',
                        'title' => __('Button Style', 'jckqv'),
                        'type' => 'select',
                        'default' => 'flat',
                        'placeholder' => '',
                        'choices' => array(
                            'none' => __('None', 'jckqv'),
                            'border' => __('Border', 'jckqv'),
                            'flat' => __('Flat', 'jckqv')
                        )
                    ),
                    array(
                        'id' => 'padding',
                        'title' => __('Padding', 'jckqv'),
                        'type' => 'multiinputs',
                        'subtitle' => __('Enter a pixel value (positive/negative) to pad the quickview button.', 'jckqv'),
                        'default' => array(
                            'Top' => 8,
                            'Right' => 10,
                            'Bottom' => 8,
                            'Left' => 10
                        ),
                        'placeholder' => '',
                    ),
                    array(
                        'id' => 'btncolour',
                        'title' => __('Button Colour', 'jckqv'),
                        'subtitle' => __('If your button is "Flat" style, this is the background colour. If it\'s "Border" style, this is the border colour.', 'jckqv'),
                        'type' => 'color',
                        'default' => '#66cc99',
                        'placeholder' => '',
                    ),
                    array(
                        'id' => 'btnhovcolour',
                        'title' => __('Button Hover Colour', 'jckqv'),
                        'subtitle' => __('If your button is "Flat" style, this is the background colour on hover. If it\'s "Border" style, this is the border colour on hover.', 'jckqv'),
                        'type' => 'color',
                        'default' => '#47C285',
                        'placeholder' => '',
                    ),
                    array(
                        'id' => 'btntextcolour',
                        'title' => __('Button Text Colour', 'jckqv'),
                        'type' => 'color',
                        'default' => '#ffffff',
                        'placeholder' => '',
                    ),
                    array(
                        'id' => 'btntexthovcolour',
                        'title' => __('Button Text Hover Colour', 'jckqv'),
                        'type' => 'color',
                        'default' => '#ffffff',
                        'placeholder' => '',
                    ),
                    array(
                        'id' => 'borderradius',
                        'title' => __('Border Radius', 'jckqv'),
                        'type' => 'multiinputs',
                        'subtitle' => __('Enter a border radius in pixels.', 'jckqv'),
                        'default' => array(
                            'Top Left' => 4,
                            'Top Right' => 4,
                            'Btm Right' => 4,
                            'Btm Left' => 4
                        ),
                        'placeholder' => '',
                    ),
                )
            ),
            array(
                'tab_id' => 'popup',
                'section_id' => 'general',
                'section_title' => 'General',
                'section_description' => '',
                'fields' => array(
                    array(
                        'id' => 'gallery',
                        'title' => __('Enable Gallery?', 'jckqv'),
                        'subtitle' => __('Should the popup allow you to navigate between the other products on the page?', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 1,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'overlaycolour',
                        'title' => __('Overlay Colour', 'jckqv'),
                        'type' => 'color',
                        'default' => '#000000',
                        'placeholder' => '',
                    ),
                    array(
                        'id' => 'overlayopacity',
                        'title' => __('Overlay Opacity', 'jckqv'),
                        'type' => 'text',
                        'subtitle' => __('Enter a value where 0 = transparent, and 1 = opaque.', 'jckqv'),
                        'default' => '0.8',
                        'placeholder' => '',
                    )
                )
            ),
            array(
                'tab_id' => 'popup',
                'section_id' => 'imagery',
                'section_title' => 'Imagery',
                'section_description' => '',
                'fields' => array(
                    array(
                        'id' => 'imgtransition',
                        'title' => __('Image Transition', 'jckqv'),
                        'type' => 'select',
                        'default' => 'horizontal',
                        'placeholder' => '',
                        'choices' => array(
                            'horizontal' => __('Horizontal Slide', 'jckqv'),
                            'fade' => __('Fade', 'jckqv')
                        )
                    ),
                    array(
                        'id' => 'transitionspeed',
                        'title' => __('Transition Speed', 'jckqv'),
                        'type' => 'text',
                        'subtitle' => __('The speed in milliseconds at which the image gallery transition occurs.', 'jckqv'),
                        'default' => '600',
                        'placeholder' => '',
                    ),
                    array(
                        'id' => 'autoplay',
                        'title' => __('Autoplay?', 'jckqv'),
                        'subtitle' => __('Automatically scroll through the product imagery?', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 0,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'autoplayspeed',
                        'title' => __('Autoplay Speed', 'jckqv'),
                        'type' => 'text',
                        'subtitle' => __('The duration in milliseconds that each image is displayed.', 'jckqv'),
                        'default' => '3000',
                        'placeholder' => '',
                    ),
                    array(
                        'id' => 'infinite',
                        'title' => __('Infinite Scroll?', 'jckqv'),
                        'subtitle' => __('Infnitely scroll through the product imagery?', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 1,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'navarr',
                        'title' => __('Show Navigation Arrows?', 'jckqv'),
                        'subtitle' => __('Show previous/next arrows on the image gallery?', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 1,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'thumbnails',
                        'title' => __('Thumbnail type', 'jckqv'),
                        'type' => 'select',
                        'default' => 'thumbnails',
                        'placeholder' => '',
                        'choices' => array(
                            'thumbnails' => __('Sliding Thumbnails', 'jckqv'),
                            'bullets' => __('Bullets', 'jckqv'),
                            'none' => __('None', 'jckqv')
                        )
                    )
                )
            ),
            array(
                'tab_id' => 'popup',
                'section_id' => 'content',
                'section_title' => 'Content',
                'section_description' => '',
                'fields' => array(
                    array(
                        'id' => 'showtitle',
                        'title' => __('Show Title?', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 1,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'showprice',
                        'title' => __('Show Price?', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 1,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'showrating',
                        'title' => __('Show Rating?', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 1,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'showbanner',
                        'title' => __('Show Banner?', 'jckqv'),
                        'subtitle' => __('E.g: "Sale!"', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 1,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'showdesc',
                        'title' => __('Show Description?', 'jckqv'),
                        'type' => 'select',
                        'default' => 'full',
                        'placeholder' => '',
                        'choices' => array(
                            'no' => __('No', 'jckqv'),
                            'full' => __('Full', 'jckqv'),
                            'short' => __('Short', 'jckqv')
                        )
                    ),
                    array(
                        'id' => 'showatc',
                        'title' => __('Show Product Options / Add to Cart?', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 1,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'ajaxcart',
                        'title' => __('Enable AJAX Add to Cart?', 'jckqv'),
                        'subtitle' => __('Add products to cart from the quickview without reloading the page.', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 1,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'autohidepopup',
                        'title' => __('Hide Popup After Add to Cart?', 'jckqv'),
                        'subtitle' => __('Once a product has been added to the cart via ajax, hide the modal popup..', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 1,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'showqty',
                        'title' => __('Show Qty Field?', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 1,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'showmeta',
                        'title' => __('Show Product Meta?', 'jckqv'),
                        'subtitle' => __('E.g: Categories, Tags, etc.', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 1,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'themebtn',
                        'title' => __('Try to use your Theme\'s Button Styling?', 'jckqv'),
                        'subtitle' => __('Check this if you\'d like to attempt to use the button styling from your theme. If this fails, use the styling tools below, or add your own CSS.', 'jckqv'),
                        'type' => 'checkbox',
                        'default' => 0,
                        'placeholder' => ''
                    ),
                    array(
                        'id' => 'btncolour',
                        'title' => __('Button Colour', 'jckqv'),
                        'type' => 'color',
                        'default' => '#66cc99',
                        'placeholder' => '',
                    ),
                    array(
                        'id' => 'btnhovcolour',
                        'title' => __('Button Hover Colour', 'jckqv'),
                        'type' => 'color',
                        'default' => '#47C285',
                        'placeholder' => '',
                    ),
                    array(
                        'id' => 'btntextcolour',
                        'title' => __('Button Text Colour', 'jckqv'),
                        'type' => 'color',
                        'default' => '#ffffff',
                        'placeholder' => '',
                    ),
                    array(
                        'id' => 'btntexthovcolour',
                        'title' => __('Button Text Hover Colour', 'jckqv'),
                        'type' => 'color',
                        'default' => '#ffffff',
                        'placeholder' => '',
                    ),
                )
            )
        )

    );

    return $wpsf_settings;

}