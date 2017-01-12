<?php

    if ( ! defined( 'ABSPATH' ) ) {
        die( '-1' );
    }
 
    $row_el_class = $el_class = $minimize_row = $width = $row_bg_color = $row_top_style = $row_bottom_style = $row_padding_vertical = $row_padding_horizontal = $row_margin_vertical = $remove_element_spacing = $el_position = $animation_output = $custom_css = $rowId = '';
    $row_classes    = array();
    $styles         = array();
    $inner_styles   = array();
    $parallax_layer_styles = array();

    extract( shortcode_atts( array(
        'wrap_type'               => 'content-width',
        'content_stretch'         => 'false',
        'row_bg_color'            => '',
        'color_row_height'        => '',
        'inner_column_height'     => '',
        'row_style'               => '',
        'row_id'                  => '',
        'row_name'                => '',
        'row_header_style'        => '',
        'row_top_style'           => '',
        'row_bottom_style'        => '',
        'row_padding_vertical'    => '',
        'row_padding_horizontal'  => '',
        'row_margin_vertical'     => '30',
        'row_overlay_opacity'     => '0',
        'remove_element_spacing'  => '',
        'vertical_center'         => 'true',
        'row_bg_type'             => '',
        'bg_image'                => '',
        'bg_video_mp4'            => '',
        'bg_video_webm'           => '',
        'bg_video_ogg'            => '',
        'bg_video_loop'           => 'yes',
        'parallax_video_height'   => 'window-height',
        'parallax_image_height'   => 'content-height',
        'parallax_video_overlay'  => 'none',
        'parallax_image_movement' => 'fixed',
        'parallax_image_speed'    => '0.5',
        'bg_type'                 => '',
        'row_expanding'           => 'no',
        'row_expading_text_closed' => '',
        'row_expading_text_open'  => '',
        'row_animation'           => '',
        'row_animation_delay'     => '',
        'responsive_vis'          => '',
        'row_responsive_vis'      => '',
        'row_el_class'            => '',
        'el_position'             => '',
        'width'                   => '1/1',
        'custom_css'              => '',
        'el_class'                => ''
    ), $atts ) );


    // Enqueue scripts 
    if ( $parallax_image_movement == "parallax" || $parallax_image_movement == "stellar" ) {
        wp_enqueue_script( 'parallax' );
    }

    // legacy checks
    if ($row_responsive_vis == "" && $responsive_vis != "") {
        $row_responsive_vis = $responsive_vis;
    }
    $responsive_vis = str_replace( "_", " ", $row_responsive_vis );
    if ( $el_class != '' ) {  
        $row_el_class = $el_class;
    }

    $row_classes[]  = 'spb-row';
    $row_classes[]  = $this->getExtraClass( $row_el_class );
    $row_classes[]  = $responsive_vis;
    $orig_width     = $width;
    $width          = spb_translateColumnWidthToSpan( $width );
    $img_url        = wp_get_attachment_image_src( $bg_image, 'full' );

    if ( $remove_element_spacing == "yes" ) {
        $row_classes[] = 'remove-element-spacing';
    }

    // Row background colour
    if ( $row_bg_color != "" ) {
        $styles[] = 'background-color:' . $row_bg_color . ';';
    }
    if ( $custom_css != "" ) {
        $styles[] = $custom_css;
        // Row background image
        if ( $row_bg_type != "color" && isset( $img_url ) && $img_url[0] != "" && !( $parallax_image_movement == "parallax" || $parallax_image_movement == "stellar" ) ) {
            $styles[] = 'background-image: url(' . $img_url[0] . ');';
            if ( $bg_type == "cover" ) {
                $styles[] = 'background-size: cover;';  
            }
        }    
    } else {
        // Row padding/margin
        if ( $row_padding_vertical != "" ) {
            $inner_styles[] = 'padding-top:' . $row_padding_vertical . 'px;';
            $inner_styles[] = 'padding-bottom:' . $row_padding_vertical . 'px;';
        }
        if ( $row_padding_horizontal != "" ) {
            $styles[] = 'padding-left:' . $row_padding_horizontal . '%;';
            $styles[] = 'padding-right:' . $row_padding_horizontal . '%;';
        }
        if ( $row_margin_vertical != "" ) {
            $styles[] = 'margin-top:' . $row_margin_vertical . 'px;';
            $styles[] = 'margin-bottom:' . $row_margin_vertical . 'px;';
        }

        // Row background image
        if ( $row_bg_type != "color" && isset( $img_url ) && $img_url[0] != "" && !( $parallax_image_movement == "parallax" || $parallax_image_movement == "stellar" ) ) {
            $styles[] = 'background-image: url(' . $img_url[0] . ');';
        }    
    }

    // Row Parallax
    if ( $parallax_image_movement == "parallax" || $parallax_image_movement == "stellar" ) {
        $row_classes[] = 'spb-row-parallax';
        $parallax_layer_styles[] = 'background-image: url(' . $img_url[0] . ');';
    }

    // Row animation
    if ( $row_animation != "" && $row_animation != "none" ) {
        $row_classes[] = 'spb-animation';
    }

    // Expanding Row
    if ( $row_expanding == "yes" ) {
        $row_classes[] = 'spb-row-expanding';
    ?>
        <a href="#" class="spb-row-expand-text container" data-closed-text="<?php echo $row_expading_text_closed; ?>" data-open-text="<?php echo $row_expading_text_open; ?>">
            <span><?php echo $row_expading_text_closed; ?></span>
        </a>
    <?php }

    // Start Row
    if ( $row_id != "" ) {
        $rowId = 'id="' . $row_id . '" data-rowname="' . $row_name . '" data-header-style="' . $row_header_style . '"';
    } else {
        $rowId = 'data-header-style="' . $row_header_style . '"';
    }
    echo $this->startRow( $el_position, '', true, $rowId ); ?>

    <div class="<?php echo implode(' ', $row_classes); ?>" data-wrap="<?php echo $wrap_type; ?>" data-content-stretch="<?php echo $content_stretch; ?>" data-type="<?php echo $row_bg_type; ?>" data-row-style="<?php echo $row_style; ?>" data-inner-col-height="<?php echo $inner_column_height; ?>" data-v-center="<?php echo  $vertical_center; ?>" data-top-style="<?php echo $row_top_style; ?>" data-bottom-style="<?php echo $row_bottom_style; ?>" data-animation="<?php echo $row_animation ; ?>" data-delay="<?php echo $row_animation_delay; ?>" style="<?php echo implode('', $styles); ?>">

        <?php if ( $row_top_style == "slant-ltr" || $row_top_style == "slant-rtl" ) { ?>
            <div class="spb_row_slant_spacer"></div>
        <?php } ?>

        <div class="spb_content_element" style="' . $inner_inline_style . '">
            <?php echo spb_format_content( $content ); ?>
        </div><?php echo $this->endBlockComment( $width ); ?>

        <?php if ( $row_bg_type == "video" ) : 
            $loop = 'loop';
            if ( $bg_video_loop == "no" ) {
                $loop = '';
            }
        ?>
            
            <?php if ( $img_url ) : ?>
                <video class="parallax-video" poster="<?php echo $img_url[0]; ?>" preload="auto" autoplay muted <?php echo $loop; ?>>
            <?php else : ?>
                <video class="parallax-video" preload="auto" autoplay muted <?php echo $loop; ?>>
            <?php endif; ?>

                <?php if ( $bg_video_mp4 != "" ) : ?>
                    <source src="<?php echo $bg_video_mp4; ?>" type="video/mp4">
                <?php endif; ?>
                <?php if ( $bg_video_webm != "" ) : ?>
                    <source src="<?php echo $bg_video_webm; ?>" type="video/webm">
                <?php endif; ?>
                <?php if ( $bg_video_ogg != "" ) : ?>
                    <source src="<?php echo $bg_video_ogg; ?>" type="video/ogg">
                <?php endif; ?>

            </video>
            
            <?php if ( $parallax_video_overlay != "color" ) { ?>
                <div class="video-overlay overlay-<?php echo $parallax_video_overlay; ?>"></div>
            <?php }

        endif;

        if ( $row_overlay_opacity != "0" && $parallax_video_overlay == "color" ) :
            $opacity = intval( $row_overlay_opacity, 10 ) / 100;
        ?>
            <div class="row-overlay" style="<?php printf( 'background-color:%1$s;opacity:%2$s', $row_bg_color, $opacity ) ?>"></div>
        <?php elseif ( $row_overlay_opacity != "0" ) : ?>
            <div class="row-overlay overlay-<?php echo $parallax_video_overlay; ?>"></div>
        <?php endif;

        if ( $row_bottom_style == "slant-ltr" || $row_bottom_style == "slant-rtl" ) { ?>
            <div class="spb_row_slant_spacer"></div>
        <?php } ?>


        <?php if ( $parallax_image_movement == "parallax" || $parallax_image_movement == "stellar" ) : ?>
            <div class="spb-row-parallax-layer-wrap">
                <div class="spb-row-parallax-layer" style="<?php echo implode('', $parallax_layer_styles); ?>"></div>
            </div>
        <?php endif; ?>

        

    </div><!-- .sb-row -->

<?php
    // End row
    echo $this->endRow( $el_position, '', true );
?>

<div class="spb-row-sizer"></div>
