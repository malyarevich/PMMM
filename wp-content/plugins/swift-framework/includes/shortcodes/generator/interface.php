<?php 

    $icon_list = sf_get_icons_list();
    $animatons_list = spb_animations_list();
    $upload_dir = wp_upload_dir();     
    $fontello_icons_path = $upload_dir['baseurl'] . '/redux/custom-icon-fonts/fontello_css/fontello-embedded.css';  
    
    $button_types = array(
        'standard' => __('Standard', 'nota'),
        'bordered' => __('Bordered', 'nota'),
        'rotate-3d' => __('3D Rotate', 'nota'),
        'stroke-to-fill' => __('Stroke To Fill', 'nota'),
        'sf-icon-reveal' => __('Icon Reveal', 'nota'),
        'sf-icon-stroke' => __('Icon', 'nota'),
    );
    
    if (!spb_theme_supports( '3drotate-button' )) {
        unset($button_types['rotate-3d']);
    }
    if (!spb_theme_supports( 'bordered-button' )) {
        unset($button_types['bordered']);
    }
    
    $button_sizes = array(
        'standard' => __('Standard', 'nota'),
        'large' => __('Large', 'nota'),
    );
    
    $button_colours = array(
        'accent' => __('Accent', 'nota'),
        'black' => __('Black', 'nota'),
        'white' => __('White', 'nota'),
        'blue'  => __('Blue', 'nota'),
        'grey'  => __('Grey', 'nota'),
        'lightgrey' => __('Light Grey', 'nota'),
        'orange'    => __('Orange', 'nota'),
        'green' => __('Green', 'nota'),
        'pink'  => __('Pink', 'nota'),
        'gold'  => __('Gold', 'nota'),
        'transparent-light' => __('Transparent - Light', 'nota'),
        'transparent-dark'  => __('Transparent - Dark', 'nota'),
    );
    
    $button_types = apply_filters( 'sf_shortcode_button_types', $button_types );
    $button_sizes = apply_filters( 'sf_shortcode_button_sizes', $button_sizes );
    $button_colours = apply_filters( 'sf_shortcode_button_colours', $button_colours );
         
?>

<!-- Swift Framework Shortcode Panel -->

<!-- OPEN html -->
<html xmlns="http://www.w3.org/1999/xhtml">

<!-- OPEN head -->
<head>

    <!-- Title & Meta -->
    <!--<title><?php wp_title( '|', true, 'right' ); ?></title>
    <meta http-equiv="Content-Type"
          content="<?php bloginfo( 'html_type' ); ?>; charset=<?php echo get_option( 'blog_charset' ); ?>"/>
-->
    <!-- LOAD scripts -->
    <script language="javascript" type="text/javascript"
            src="<?php echo get_option( 'siteurl' ) ?>/wp-includes/js/jquery/jquery.js"></script>
    <script language="javascript" type="text/javascript"
            src="<?php echo plugin_dir_url( __FILE__ ); ?>sf.shortcodes.js"></script>
    <script language="javascript" type="text/javascript"
            src="<?php echo plugin_dir_url( __FILE__ ); ?>/sf.shortcode.embed.js"></script>
    <script language="javascript" type="text/javascript"
            src="<?php echo get_option( 'siteurl' ) ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>

    <base target="_self"/>


    <?php if ( spb_theme_supports('nucleo-interface-font') || spb_theme_supports('nucleo-general-font') ) { ?>
    <link href="<?php echo get_template_directory_uri(); ?>/css/iconfont.css" rel="stylesheet" type="text/css"/>
    <?php } ?>
    <?php if ( spb_theme_supports('icon-mind-font') ) { ?>
    <link href="<?php echo get_template_directory_uri(); ?>/css/iconmind.css" rel="stylesheet" type="text/css"/>
    <?php } ?>
    <?php if ( spb_theme_supports('gizmo-icon-font') ) { ?>
    <link href="<?php echo get_template_directory_uri(); ?>/css/ss-gizmo.css" rel="stylesheet" type="text/css"/>
    <?php } ?>
    <?php if ( spb_theme_supports('simple-line-icons-font') ) { ?>
    <link href="<?php echo get_template_directory_uri(); ?>/css/simple-line-icons.css" rel="stylesheet" type="text/css"/>
    <?php } ?>
    <link href="<?php echo get_template_directory_uri(); ?>/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <?php if( file_exists( $fontello_icons_path ) ){ ?>
    <link href="<?php echo esc_url($fontello_icons_path); ?>" rel="stylesheet" type="text/css"/>    
    <?php } ?>
   
    <link href="<?php echo plugin_dir_url( __FILE__ ); ?>base.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?php echo plugin_dir_url( __FILE__ ); ?>sf-shortcodes-style.css"
          rel="stylesheet" type="text/css"/>

    <!-- CLOSE head -->
</head>

<!-- OPEN body -->
<body onLoad="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" id="link">

<!-- OPEN swiftframework_shortcode_form -->
<form name="swiftframework_shortcode_form" action="#">

<!-- OPEN #shortcode_wrap -->
<div id="shortcode_wrap">

<!-- CLOSE #shortcode_panel -->
<div id="shortcode_panel" class="current">

<fieldset>

<h4><?php _e( 'Select a shortcode', 'nota' ); ?></h4>

<div class="option">
    <label for="shortcode-select"><?php _e( 'Shortcode', 'nota' ); ?></label>
    <select id="shortcode-select" name="shortcode-select">
        <option value="0"></option>
        <?php if ( sf_woocommerce_activated() ) { ?>
        <option value="shortcode-addtocart-button"><?php _e( 'Add to Cart Button', 'nota' ); ?></option>
        <?php } ?>
        <option value="shortcode-buttons"><?php _e( 'Button', 'nota' ); ?></option>
        <option value="shortcode-chart"><?php _e( 'Chart', 'nota' ); ?></option>
        <option value="shortcode-columns"><?php _e( 'Columns', 'nota' ); ?></option>
        <option value="shortcode-counters"><?php _e( 'Counters', 'nota' ); ?></option>
        <option value="shortcode-countdown"><?php _e( 'Countdown', 'nota' ); ?></option>
        <option value="shortcode-icons"><?php _e( 'Icons', 'nota' ); ?></option>
        <option value="shortcode-iconbox"><?php _e( 'Icon Box', 'nota' ); ?></option>
        <option value="shortcode-imagebanner"><?php _e( 'Image Banner', 'nota' ); ?></option>
        <option value="shortcode-lists"><?php _e( 'Lists', 'nota' ); ?></option>
        <option value="shortcode-modal"><?php _e( 'Modal', 'nota' ); ?></option>
        <option value="shortcode-progressbar"><?php _e( 'Progress Bar', 'nota' ); ?></option>
        <option value="shortcode-fwvideo"><?php _e( 'Fullscreen Video', 'nota' ); ?></option>
        <option value="shortcode-responsivevis"><?php _e( 'Responsive Visiblity', 'nota' ); ?></option>
        <option value="shortcode-social"><?php _e( 'Social', 'nota' ); ?></option>
        <option value="shortcode-social-share"><?php _e( 'Social share', 'nota' ); ?></option>
        <option value="shortcode-tables"><?php _e( 'Table', 'nota' ); ?></option>
        <?php if ( sf_theme_opts_name() != "sf_atelier_options" ) { ?>
        <option value="shortcode-tooltip"><?php _e( 'Tooltip', 'nota' ); ?></option>
        <?php } ?>
        <option value="shortcode-typography"><?php _e( 'Typography', 'nota' ); ?></option>
    </select>
</div>


<!--//////////////////////////////
////    ADD TO CART BUTTON
//////////////////////////////-->

<div id="shortcode-addtocart-button" class="shortcode-option">
    <h5><?php _e( 'Add to Cart Button', 'nota' ); ?></h5>

    <div class="option">
        <label for="addtocart-button-productid"><?php _e( 'Product ID', 'nota' ); ?></label>
        <input id="addtocart-button-productid" name="button-productid" type="text"
               value=""/>
        <p class="info">Provide the ID for the product here, you can find this in the Products admin area when you hover the product.</p>
        </p>
    </div>
    <div class="option">
        <label for="addtocart-button-colour"><?php _e( 'Button colour', 'nota' ); ?></label>
        <select id="addtocart-button-colour" name="button-colour">
            <option value="default"><?php _e( 'Default', 'nota' ); ?></option>
            <option value="accent"><?php _e( 'Accent', 'nota' ); ?></option>
            <option value="black"><?php _e( 'Black', 'nota' ); ?></option>
            <option value="white"><?php _e( 'White', 'nota' ); ?></option>
            <option value="blue"><?php _e( 'Blue', 'nota' ); ?></option>
            <option value="grey"><?php _e( 'Grey', 'nota' ); ?></option>
            <option value="lightgrey"><?php _e( 'Light Grey', 'nota' ); ?></option>
            <option value="orange"><?php _e( 'Orange', 'nota' ); ?></option>
            <option value="turquoise"><?php _e( 'Turquoise', 'nota' ); ?></option>
            <option value="green"><?php _e( 'Green', 'nota' ); ?></option>
            <option value="pink"><?php _e( 'Pink', 'nota' ); ?></option>
            <option value="gold"><?php _e( 'Gold', 'nota' ); ?></option>
            <option
                value="transparent-light"><?php _e( 'Transparent - Light (For use on images/dark backgrounds)', 'nota' ); ?></option>
            <option value="transparent-dark"><?php _e( 'Transparent - Dark', 'nota' ); ?></option>
        </select>
    </div>
    <div class="option">
        <label for="addtocart-button-extraclass"><?php _e( 'Button Extra Class', 'nota' ); ?></label>
        <input id="addtocart-button-extraclass" name="button-extraclass" type="text" value=""/>

        <p class="info">Optional, for extra styling/custom colour control.</a></p>
    </div>
</div>


<!--//////////////////////////////
////    BUTTONS
//////////////////////////////-->

<div id="shortcode-buttons" class="shortcode-option">
    <h5><?php _e( 'Buttons', 'nota' ); ?></h5>

    <div class="option">
        <label for="button-size"><?php _e( 'Button size', 'nota' ); ?></label>
        <select id="button-size" name="button-size">
            <?php foreach ($button_sizes as $size_val => $size_name ) {
                echo '<option value="' . $size_val . '">' . $size_name . '</option>';
            } ?>
        </select>
    </div>
    <div class="option">
        <label for="button-colour"><?php _e( 'Button colour', 'nota' ); ?></label>
        <select id="button-colour" name="button-colour">
            <?php foreach ($button_colours as $colour_val => $colour_name ) {
                echo '<option value="' . $colour_val . '">' . $colour_name . '</option>';
            } ?>
        </select>
    </div>
    <div class="option">
        <label for="button-type"><?php _e( 'Button type', 'nota' ); ?></label>
        <select id="button-type" name="button-type">
            <?php foreach ($button_types as $button_val => $button_name ) {
                echo '<option value="' . $button_val . '">' . $button_name . '</option>';
            } ?>
        </select>
    </div>
    <div class="option">
        <label for="button-rounded"
               class="for-checkbox"><?php _e( 'Button rounded', 'nota' ); ?></label>
        <input id="button-rounded" class="checkbox" name="button-rounded" type="checkbox"/>
    </div>
    <div class="option">
        <label for="button-dropshadow"
               class="for-checkbox"><?php _e( 'Button drop shadow', 'nota' ); ?></label>
        <input id="button-dropshadow" class="checkbox" name="button-dropshadow" type="checkbox"/>
    </div>
    <div class="option">
        <label
            for="button-icon"><?php _e( 'Button icon (for button types with icon)', 'nota' ); ?></label>
        <input type="text" class="search-icon-grid textfield" placeholder="Search Icon">
        <input id="button-icon" name="icon-icon" type="text" value="" style="visibility: hidden;"/>
        <ul class="font-icon-grid"><?php echo $icon_list; ?></ul>
    </div>
    <div class="option">
        <label for="button-text"><?php _e( 'Button text', 'nota' ); ?></label>
        <input id="button-text" name="button-text" type="text"
               value="<?php _e( 'Button text', 'nota' ); ?>"/>
    </div>
    <div class="option">
        <label for="button-url"><?php _e( 'Button URL', 'nota' ); ?></label>
        <input id="button-url" name="button-url" type="text" value="http://"/>
    </div>
    <div class="option">
        <label for="button-target"
               class="for-checkbox"><?php _e( 'Open link in a new window?', 'nota' ); ?></label>
        <input id="button-target" class="checkbox" name="button-target" type="checkbox"/>
    </div>
    <div class="option">
        <label for="button-extraclass"><?php _e( 'Button Extra Class', 'nota' ); ?></label>
        <input id="button-extraclass" name="button-extraclass" type="text" value=""/>

        <p class="info">Optional, for extra styling/custom colour control.</a></p>
    </div>
</div>


<!--//////////////////////////////
////    ICONS
//////////////////////////////-->

<div id="shortcode-icons" class="shortcode-option">
    <h5><?php _e( 'Icons', 'nota' ); ?></h5>

    <div class="option">
        <label for="icon-size"><?php _e( 'Icon size', 'nota' ); ?></label>
        <select id="icon-size" name="icon-size">
            <option value="small"><?php _e( 'Small', 'nota' ); ?></option>
            <option value="medium"><?php _e( 'Medium', 'nota' ); ?></option>
            <option value="large"><?php _e( 'Large', 'nota' ); ?></option>
        </select>
    </div>
    <div class="option">
        <label for="icon-image"><?php _e( 'Icon image', 'nota' ); ?></label>
        <input type="text" class="search-icon-grid textfield" placeholder="Search Icon">
        <input id="icon-image" name="icon-image" type="text" value="" style="visibility: hidden;"/>
        <ul class="font-icon-grid"><?php echo $icon_list; ?></ul>
    </div>
    <div class="option">
        <label for="icon-character"><?php _e( 'Icon Character', 'nota' ); ?></label>
        <input id="icon-character" name="icon-character" type="text" value=""/>

        <p class="info">Instead of an icon, you can optionally provide a single letter/digit here. NOTE: This will
            override the icon selection.</p>
    </div>
    <div class="option">
        <label for="icon-cont"><?php _e( 'Circular container', 'nota' ); ?></label>
        <select id="icon-cont" name="icon-cont">
            <option value="no"><?php _e( 'No', 'nota' ); ?></option>
            <option value="yes"><?php _e( 'Yes', 'nota' ); ?></option>
        </select>
    </div>
    <div class="option">
        <label for="icon-float"><?php _e( 'Icon float', 'nota' ); ?></label>
        <select id="icon-float" name="icon-float">
            <option value="left"><?php _e( 'Left', 'nota' ); ?></option>
            <option value="right"><?php _e( 'Right', 'nota' ); ?></option>
            <option value="none"><?php _e( 'None', 'nota' ); ?></option>
        </select>
    </div>
    <div class="option">
        <label for="icon-color"><?php _e( 'Icon Color', 'nota' ); ?></label>
        <input id="icon-color" name="icon-color" type="text" value=""/>

        <p class="info">If you'd like to override the default color customiser value (link in the WP Admin Bar), then
            please enter a hex colour value (including #).</p>
    </div>
</div>


<!--//////////////////////////////
////    ICON BOX
//////////////////////////////-->

<div id="shortcode-iconbox" class="shortcode-option">
    <h5><?php _e( 'Icon Box', 'nota' ); ?></h5>

    <div class="option">
        <label for="iconbox-type"><?php _e( 'Icon Box Type', 'nota' ); ?></label>
        <select id="iconbox-type" name="iconbox-type">
            <option value="standard"><?php _e( 'Standard', 'nota' ); ?></option>
            <option value="standard-title"><?php _e( 'Standard Title Icon', 'nota' ); ?></option>
            <option value="bold"><?php _e( 'Bold', 'nota' ); ?></option>
            <option value="left-icon"><?php _e( 'Left Icon', 'nota' ); ?></option>
            <option value="boxed-one"><?php _e( 'Boxed Icon Box', 'nota' ); ?></option>
            <option value="boxed-two"><?php _e( 'Boxed Icon Box Type 2', 'nota' ); ?></option>
            <option value="boxed-three"><?php _e( 'Boxed Icon Box Type 3', 'nota' ); ?></option>
            <option value="boxed-four"><?php _e( 'Boxed Icon Box Type 4', 'nota' ); ?></option>
            <option value="animated"><?php _e( 'Animated', 'nota' ); ?></option>
        </select>
    </div>
    <div class="option">
        <label for="iconbox-image"><?php _e( 'Icon Box Image', 'nota' ); ?></label>
        <input type="text" class="search-icon-grid textfield" placeholder="Search Icon">
        <input id="iconbox-image" name="iconbox-image" type="text" value="" style="visibility: hidden;"/>
        <ul class="font-icon-grid"><?php echo $icon_list; ?></ul>
    </div>
    <div class="option">
        <label for="iconbox-character"><?php _e( 'Icon Character', 'nota' ); ?></label>
        <input id="iconbox-character" name="iconbox-character" type="text" value=""/>

        <p class="info">Instead of an icon, you can optionally provide a single letter/digit here. NOTE: This will
            override the icon selection.</p>
    </div>
    <div class="option">
        <label for="iconbox-color"><?php _e( 'Icon Color', 'nota' ); ?></label>
        <select id="iconbox-color" name="iconbox-color">
            <option value="standard"><?php _e( 'Standard', 'nota' ); ?></option>
            <option value="accent"><?php _e( 'Accent', 'nota' ); ?></option>
            <option value="secondary-accent"><?php _e( 'Secondary Accent', 'nota' ); ?></option>
            <option value="icon-one"><?php _e( 'Icon One', 'nota' ); ?></option>
            <option value="icon-two"><?php _e( 'Icon Two', 'nota' ); ?></option>
            <option value="icon-three"><?php _e( 'Icon Three', 'nota' ); ?></option>
            <option value="icon-four"><?php _e( 'Icon Four', 'nota' ); ?></option>
            <p class="info">These colours are all set in the Color Customiser (link in the WP Admin Bar).</p>
        </select>
    </div>
    <div class="option">
        <label for="iconbox-title"><?php _e( 'Icon Box Title', 'nota' ); ?></label>
        <input id="iconbox-title" name="iconbox-title" type="text" value=""/>
    </div>
    <div class="option">
        <label for="iconbox-link"><?php _e( 'Icon Box Link', 'nota' ); ?></label>
        <input id="iconbox-link" name="iconbox-link" type="text" value=""/>

        <p class="info">This is optional, only provide if you'd like the icon box to link on click.</p>
    </div>
    <div class="option">
        <label for="iconbox-target"
               class="for-checkbox"><?php _e( 'Open link in a new window?', 'nota' ); ?></label>
        <input id="iconbox-target" class="checkbox" name="iconbox-target" type="checkbox"/>
    </div>
    <div class="option">
        <label for="iconbox-animation"><?php _e( 'Icon Box Animation', 'nota' ); ?></label>
        <select id="iconbox-animation" name="iconbox-animation">
            <?php echo $animatons_list; ?>
        </select>
    </div>
    <div class="option">
        <label for="iconbox-animation-delay"><?php _e( 'Icon Box Animation Delay', 'nota' ); ?></label>
        <input id="iconbox-animation-delay" name="iconbox-animation-delay" type="text" value="200"/>

        <p class="info">This value determines the delay to which the animation starts once it's visible on the
            screen.</p>
    </div>
</div>


<!--//////////////////////////////
////    SOCIAL
//////////////////////////////-->

<div id="shortcode-social" class="shortcode-option">
    <h5><?php _e( 'Social', 'nota' ); ?></h5>

    <div class="option">
        <label for="social-size"><?php _e( 'Social Icon Size', 'nota' ); ?></label>
        <select id="social-size" name="social-size">
            <option value="standard"><?php _e( 'Standard', 'nota' ); ?></option>
            <option value="large"><?php _e( 'Large', 'nota' ); ?></option>
        </select>
    </div>
</div>


<!--//////////////////////////////
////    SOCIAL SHARE
//////////////////////////////-->

<div id="shortcode-social-share" class="shortcode-option">
    <h5><?php _e( 'Social share', 'nota' ); ?></h5>

    <div class="option">
        <p class="info">This shortcode will embed the social share links asset, for sharing the current post/page on
            social media.</p>
    </div>
</div>


<!--//////////////////////////////
////    TYPOGRAPHY
//////////////////////////////-->

<div id="shortcode-typography" class="shortcode-option">
    <h5><?php _e( 'Typography', 'nota' ); ?></h5>

    <div class="option">
        <label for="typography-type"><?php _e( 'Type', 'nota' ); ?></label>
        <select id="typography-type" name="typography-type">
            <option value="0"></option>
            <option value="highlight"><?php _e( 'Highlight', 'nota' ); ?></option>
            <option
                value="decorative_ampersand"><?php _e( 'Decorative Ampersand', 'nota' ); ?></option>
            <option value="blockquote1"><?php _e( 'Blockquote Standard', 'nota' ); ?></option>
            <option value="blockquote2"><?php _e( 'Blockquote Medium', 'nota' ); ?></option>
            <option value="blockquote3"><?php _e( 'Blockquote Big', 'nota' ); ?></option>
            <option value="pullquote"><?php _e( 'Pull Quote', 'nota' ); ?></option>
            <option value="dropcap1"><?php _e( 'Dropcap Type 1', 'nota' ); ?></option>
            <option value="dropcap2"><?php _e( 'Dropcap Type 2', 'nota' ); ?></option>
            <option value="dropcap3"><?php _e( 'Dropcap Type 3', 'nota' ); ?></option>
            <option value="dropcap4"><?php _e( 'Dropcap Type 4', 'nota' ); ?></option>
        </select>
    </div>
</div>


<!--//////////////////////////////
////    COLUMNS
//////////////////////////////-->

<div id="shortcode-columns" class="shortcode-option">
    <h5><?php _e( 'Columns', 'nota' ); ?></h5>

    <div class="option">
        <label for="column-options"><?php _e( 'Layout', 'nota' ); ?></label>
        <select id="column-options" name="column-options">
            <option value="0"></option>
            <option value="two_halves"><?php _e( '1/2 + 1/2', 'nota' ); ?></option>
            <option value="three_thirds"><?php _e( '1/3 + 1/3 + 1/3', 'nota' ); ?></option>
            <option value="one_third_two_thirds"><?php _e( '1/3 + 2/3', 'nota' ); ?></option>
            <option value="two_thirds_one_third"><?php _e( '2/3 + 1/3', 'nota' ); ?></option>
            <option value="four_quarters"><?php _e( '1/4 + 1/4 + 1/4 + 1/4', 'nota' ); ?></option>
            <option value="one_quarter_three_quarters"><?php _e( '1/4 + 3/4', 'nota' ); ?></option>
            <option value="three_quarters_one_quarter"><?php _e( '3/4 + 1/4', 'nota' ); ?></option>
            <option
                value="one_quarter_one_quarter_one_half"><?php _e( '1/4 + 1/4 + 1/2', 'nota' ); ?></option>
            <option
                value="one_quarter_one_half_one_quarter"><?php _e( '1/4 + 1/2 + 1/4', 'nota' ); ?></option>
            <option
                value="one_half_one_quarter_one_quarter"><?php _e( '1/2 + 1/4 + 1/4', 'nota' ); ?></option>
        </select>
    </div>
</div>

<!--//////////////////////////////
////    PROGRESS BAR
//////////////////////////////-->

<div id="shortcode-progressbar" class="shortcode-option">
    <h5><?php _e( 'Progress Bar', 'nota' ); ?></h5>

    <div class="option">
        <label for="progressbar-percentage"><?php _e( 'Percentage', 'nota' ); ?></label>
        <input id="progressbar-percentage" name="progressbar-percentage" type="text" value=""/>

        <p class="info">Enter the percentage of the progress bar.</p>
    </div>
    <div class="option">
        <label for="progressbar-text"><?php _e( 'Progress Text', 'nota' ); ?></label>
        <input id="progressbar-text" name="progressbar-text" type="text" value=""/>

        <p class="info">Enter the text that you'd like shown above the bar, i.e. "COMPLETED".</p>
    </div>
    <div class="option">
        <label for="progressbar-value"><?php _e( 'Progress Value', 'nota' ); ?></label>
        <input id="progressbar-value" name="progressbar-value" type="text" value=""/>

        <p class="info">Enter value that you'd like shown at the end of the bar on completion, i.e. "90%".</p>
    </div>
    <div class="option">
        <label for="progressbar-type"><?php _e( 'Progress Bar Type', 'nota' ); ?></label>
        <select id="progressbar-type" name="progressbar-type">
            <option value=""><?php _e( 'Standard', 'nota' ); ?></option>
            <option value="progress-striped"><?php _e( 'Striped', 'nota' ); ?></option>
            <option
                value="progress-striped active"><?php _e( 'Striped - Animated', 'nota' ); ?></option>
        </select>
    </div>
    <div class="option">
        <label for="progressbar-colour"><?php _e( 'Progress Bar Colour', 'nota' ); ?></label>
        <input id="progressbar-colour" name="progressbar-colour" type="text" value=""/>

        <p class="info">Enter the hex value (with the #) for the progress bar colour, or it will default to accent
            colour.</p>
    </div>
</div>


<!--//////////////////////////////
////    FULLSCREEN VIDEO
//////////////////////////////-->

<div id="shortcode-fwvideo" class="shortcode-option">
    <h5><?php _e( 'Fullscreen Video', 'nota' ); ?></h5>

    <div class="option">
        <label for="fwvideo-type"><?php _e( 'Button type', 'nota' ); ?></label>
        <select id="fwvideo-type" name="fwvideo-type">
            <option value="image-button"><?php _e( 'Image Button', 'nota' ); ?></option>
            <option value="image-button2"><?php _e( 'Image Button Alt', 'nota' ); ?></option>
            <option value="image-button3"><?php _e( 'Image Button Bottom Left', 'nota' ); ?></option>
            <option value="icon-button"><?php _e( 'Icon Button', 'nota' ); ?></option>
            <option value="text-button"><?php _e( 'Text Button', 'nota' ); ?></option>
        </select>

        <p class="info">Choose the button type you'd like to link to the fullscreen video.</p>
    </div>
    <div class="option">
        <label for="fwvideo-imageurl"><?php _e( 'Image URL (for image button)', 'nota' ); ?></label>
        <input id="fwvideo-imageurl" name="fwvideo-imageurl" type="text" value=""/>

        <p class="info">If you've chosen the image button above, then please enter the full path for the image that you
            wish the fullscreen video to be linked from.</p>
    </div>
    <div class="option">
        <label for="fwvideo-imageheight"><?php _e( 'Image Height', 'nota' ); ?></label>
        <input id="fwvideo-imageheight" name="fwvideo-imageheight" type="text" value=""/>
        <p class="info">Enter the height of the image, numeric only (no px).</p>
    </div>
    <div class="option">
        <label for="fwvideo-imagewidth"><?php _e( 'Image Width', 'nota' ); ?></label>
        <input id="fwvideo-imagewidth" name="fwvideo-imagewidth" type="text" value=""/>
        <p class="info">Enter the height of the image, numeric only (no px).</p>
    </div>
    <div class="option">
        <label for="fwvideo-btntext"><?php _e( 'Button Text (for text button)', 'nota' ); ?></label>
        <input id="fwvideo-btntext" name="fwvideo-btntext" type="text" value=""/>

        <p class="info">If you've chosen the text button above, then please enter the text you'd like to show on the
            button. This also functions as the alt text for an image button.</p>
    </div>
    <div class="option">
        <label for="fwvideo-videourl"><?php _e( 'Video URL', 'nota' ); ?></label>
        <input id="fwvideo-videourl" name="fwvideo-videourl" type="text" value=""/>

        <p class="info">Enter the video URL here. Vimeo/YouTube are supported, and please make sure you enter the full
            video URL, not shortened, and HTTP only.</p>
    </div>
    <div class="option">
        <label for="fwvideo-extraclass"><?php _e( 'Button Extra class', 'nota' ); ?></label>
        <input id="fwvideo-extraclass" name="fwvideo-extraclass" type="text" value=""/>

        <p class="info">Provide any extra classes you'd like to add here (optional).</p>
    </div>
</div>


<!--//////////////////////////////
////    RESPONSIVE VISIBILITY
//////////////////////////////-->

<div id="shortcode-responsivevis" class="shortcode-option">
    <h5><?php _e( 'Responsive Visibility', 'nota' ); ?></h5>

    <div class="option">
        <label for="responsivevis-config"><?php _e( 'Device Visiblity', 'nota' ); ?></label>
        <select id="responsivevis-config" name="responsivevis-config">
            <option value="visible-xs"><?php _e( 'Visible - Phone', 'nota' ); ?></option>
            <option value="visible-md visible-sm"><?php _e( 'Visible - Tablet', 'nota' ); ?></option>
            <option value="visible-lg"><?php _e( 'Visible - Desktop', 'nota' ); ?></option>
            <option value="hidden-xs"><?php _e( 'Hidden - Phone', 'nota' ); ?></option>
            <option value="hidden-md hidden-sm"><?php _e( 'Hidden - Tablet', 'nota' ); ?></option>
            <option value="hidden-lg"><?php _e( 'Hidden - Desktop', 'nota' ); ?></option>
        </select>

        <p class="info">Choose the responsive visibility for the content within the shortcode.</p>
    </div>
</div>


<!--//////////////////////////////
////    TOOLTIP
//////////////////////////////-->

<div id="shortcode-tooltip" class="shortcode-option">
    <h5><?php _e( 'Tooltip', 'nota' ); ?></h5>

    <div class="option">
        <label for="tooltip-text"><?php _e( 'Text', 'nota' ); ?></label>
        <input id="tooltip-text" name="tooltip-text" type="text" value=''/>

        <p class="info">Enter the text for the tooltip.</p>
    </div>
    <div class="option">
        <label for="tooltip-link"><?php _e( 'Link', 'nota' ); ?></label>
        <input id="tooltip-link" name="tooltip-link" type="text" value=""/>

        <p class="info">Enter the link that the tooltip text links to.</p>
    </div>
    <div class="option">
        <label for="tooltip-direction"><?php _e( 'Direction', 'nota' ); ?></label>
        <select id="tooltip-direction" name="tooltip-direction">
            <option value="top"><?php _e( 'Top', 'nota' ); ?></option>
            <option value="bottom"><?php _e( 'Bottom', 'nota' ); ?></option>
            <option value="left"><?php _e( 'Left', 'nota' ); ?></option>
            <option value="right"><?php _e( 'Right', 'nota' ); ?></option>
        </select>

        <p class="info">Choose the direction in which the tooltip appears.</p>
    </div>
</div>


<!--//////////////////////////////
////    MODAL
//////////////////////////////-->

<div id="shortcode-modal" class="shortcode-option">
    <h5><?php _e( 'Modal', 'nota' ); ?></h5>
    	
    <div class="option">
        <label for="modal-link-type"><?php _e( 'Link Type', 'nota' ); ?></label>
        <select id="modal-link-type" name="modal-link-type">
            <option value="button"><?php _e( 'Button', 'nota' ); ?></option>
            <option value="text"><?php _e( 'Text Link', 'nota' ); ?></option>
        </select>
    </div>
    
    <div class="option">
        <label for="modal-link-text"><?php _e( 'Modal link text', 'nota' ); ?></label>
        <input id="modal-link-text" name="modal-link-text" type="text" value="<?php _e( 'Modal link', 'nota' ); ?>"/>
        <p class="info">If using the Text Link type, then enter the link text here.</p>
    </div>

    <div class="option">
        <label for="modal-button-size"><?php _e( 'Modal Button size', 'nota' ); ?></label>
        <select id="modal-button-size" name="modal-button-size">
            <?php foreach ($button_sizes as $size_val => $size_name ) {
                echo '<option value="' . $size_val . '">' . $size_name . '</option>';
            } ?>
        </select>
    </div>
    <div class="option">
        <label for="modal-button-colour"><?php _e( 'Modal Button colour', 'nota' ); ?></label>
        <select id="modal-button-colour" name="modal-button-colour">
            <?php foreach ($button_colours as $colour_val => $colour_name ) {
                echo '<option value="' . $colour_val . '">' . $colour_name . '</option>';
            } ?>
        </select>
    </div>
    <div class="option">
        <label for="modal-button-type"><?php _e( 'Modal Button type', 'nota' ); ?></label>
        <select id="modal-button-type" name="modal-button-type">
            <?php foreach ($button_types as $button_val => $button_name ) {
                echo '<option value="' . $button_val . '">' . $button_name . '</option>';
            } ?>
        </select>
    </div>
    
    <div class="option">
        <label
            for="modal-button-icon"><?php _e( 'Modal Button Icon (Icon Reveal Only)', 'nota' ); ?></label>
        <input id="modal-button-icon" name="modal-button-icon" type="text" value="ss-star"/>
    </div>
    <div class="option">
        <label for="modal-button-text"><?php _e( 'Modal Button text', 'nota' ); ?></label>
        <input id="modal-button-text" name="modal-button-text" type="text" value="<?php _e( 'Button text', 'nota' ); ?>"/>
    </div>
    <div class="option">
        <label for="modal-header"><?php _e( 'Header', 'nota' ); ?></label>
        <input id="modal-header" name="modal-header" type="text" value=''/>

        <p class="info">Enter the heading for the modal popup.</p>
    </div>
</div>


<!--//////////////////////////////
////    CHART
//////////////////////////////-->

<div id="shortcode-chart" class="shortcode-option">
    <h5><?php _e( 'Chart', 'nota' ); ?></h5>

    <div class="option">
        <label for="chart-percentage"><?php _e( 'Percentage', 'nota' ); ?></label>
        <input id="chart-percentage" name="chart-percentage" type="text" value=""/>

        <p class="info">Enter the percentage of the chart value. NOTE: This must be between 0-100, numeric only.</p>
    </div>
    <div class="option">
        <label for="chart-content"><?php _e( 'Content', 'nota' ); ?></label>
        <input id="chart-content" name="chart-content" type="text" value=''/>

        <p class="info">Enter the content for the center of the chart, i.e. a number or percentage. NOTE: if you'd like
            to include a font awesome icon or Gizmo icon here, just enter the icon name, i.e. "fa-magic".</p>
    </div>
    <div class="option">
        <label for="chart-size"><?php _e( 'Chart Size', 'nota' ); ?></label>
        <select id="chart-size" name="chart-size">
            <option value="70"><?php _e( 'Standard', 'nota' ); ?></option>
            <option value="170"><?php _e( 'Large', 'nota' ); ?></option>
        </select>
    </div>
    <div class="option">
        <label for="chart-barcolour"><?php _e( 'Chart Bar Colour', 'nota' ); ?></label>
        <input id="chart-barcolour" name="chart-barcolour" type="text" value=""/>

        <p class="info">Enter the hex value (with the #) for the chart bar colour.</p>
    </div>
    <div class="option">
        <label for="chart-trackcolour"><?php _e( 'Chart Track Colour', 'nota' ); ?></label>
        <input id="chart-trackcolour" name="chart-trackcolour" type="text" value=""/>

        <p class="info">Enter the hex value (with the #) for the chart track colour (the path the bar follows).</p>
    </div>
    <div class="option">
        <label for="chart-align"><?php _e( 'Chart Align', 'nota' ); ?></label>
        <select id="chart-align" name="chart-align">
            <option value="left"><?php _e( 'Left', 'nota' ); ?></option>
            <option value="center"><?php _e( 'Center', 'nota' ); ?></option>
        </select>
    </div>
</div>


<!--//////////////////////////////
////    COUNTERS
//////////////////////////////-->

<div id="shortcode-counters" class="shortcode-option">
    <h5><?php _e( 'Counters', 'nota' ); ?></h5>

    <div class="option">
        <label for="count-from"><?php _e( 'From Value', 'nota' ); ?></label>
        <input id="count-from" name="count-from" type="text" value=""/>

        <p class="info">Enter the number from which the counter starts at.</p>
    </div>
    <div class="option">
        <label for="count-to"><?php _e( 'To Value', 'nota' ); ?></label>
        <input id="count-to" name="count-to" type="text" value=""/>

        <p class="info">Enter the number from which the counter counts up to.</p>
    </div>
    <div class="option">
        <label for="count-prefix"><?php _e( 'Prefix Text', 'nota' ); ?></label>
        <input id="count-prefix" name="count-prefix" type="text" value=""/>

        <p class="info">Enter the text which you would like to show before the count number (optional).</p>
    </div>
    <div class="option">
        <label for="count-suffix"><?php _e( 'Suffix Text', 'nota' ); ?></label>
        <input id="count-suffix" name="count-suffix" type="text" value=""/>

        <p class="info">Enter the text which you would like to show after the count number (optional).</p>
    </div>
    <div class="option">
        <label for="count-commas"
               class="for-checkbox"><?php _e( 'Comma Seperated', 'nota' ); ?></label>
        <input id="count-commas" class="checkbox" name="count-commas" type="checkbox"/>

        <p class="info">Include comma separators in the numbers after every 3rd digit.</p>
    </div>
    <div class="option">
        <label for="count-subject"><?php _e( 'Subject Text', 'nota' ); ?></label>
        <input id="count-subject" name="count-subject" type="text" value=""/>

        <p class="info">Enter the text which you would like to show below the counter.</p>
    </div>
    <div class="option">
        <label for="count-speed"><?php _e( 'Speed', 'nota' ); ?></label>
        <input id="count-speed" name="count-speed" type="text" value=""/>

        <p class="info">Enter the time you want the counter to take to complete, this is in milliseconds and optional.
            The default is 2000.</p>
    </div>
    <div class="option">
        <label for="count-refresh"><?php _e( 'Refresh Interval', 'nota' ); ?></label>
        <input id="count-refresh" name="count-refresh" type="text" value=""/>

        <p class="info">Enter the time to wait between refreshing the counter. This is in milliseconds and optional. The
            default is 25.</p>
    </div>
    <div class="option">
        <label for="count-textstyle"><?php _e( 'Text style', 'nota' ); ?></label>
        <select id="count-textstyle" name="count-textstyle">
            <option value="h3"><?php _e( 'H3', 'nota' ); ?></option>
            <option value="h6"><?php _e( 'H6', 'nota' ); ?></option>
            <option value="div"><?php _e( 'Body', 'nota' ); ?></option>
        </select>
    </div>
    <div class="option">
        <label for="count-textcolor"><?php _e( 'Text Color', 'nota' ); ?></label>
        <input id="count-textcolor" name="count-textcolor" type="text" value=""/>

        <p class="info">Enter the hex colour code here for a custom colour (e.g. #ff9900).</p>
    </div>
</div>


<!--//////////////////////////////
////    COUNTDOWN
//////////////////////////////-->

<div id="shortcode-countdown" class="shortcode-option">
    <h5><?php _e( 'Countdown', 'nota' ); ?></h5>

    <div class="option">
        <label for="countdown-year"><?php _e( 'Year', 'nota' ); ?></label>
        <input id="countdown-year" name="countdown-year" type="text" value=""/>

        <p class="info">Enter the year for which you want the countdown to count to (e.g. 2020).</p>
    </div>
    <div class="option">
        <label for="countdown-month"><?php _e( 'Month', 'nota' ); ?></label>
        <input id="countdown-month" name="countdown-month" type="text" value=""/>

        <p class="info">Enter the month for which you want the countdown to count to (e.g. 10).</p>
    </div>
    <div class="option">
        <label for="countdown-day"><?php _e( 'Day', 'nota' ); ?></label>
        <input id="countdown-day" name="countdown-day" type="text" value=""/>

        <p class="info">Enter the day for which you want the countdown to count to (e.g. 24).</p>
    </div>
    <div class="option">
        <label for="countdown-displaytext"><?php _e( 'Display Text', 'nota' ); ?></label>
        <input id="countdown-displaytext" name="countdown-displaytext" type="text" value=""/>

        <p class="info">Enter the text that you want to show below the countdown (optional).</p>
    </div>
</div>


<!--//////////////////////////////
////    IMAGE BANNER
//////////////////////////////-->

<div id="shortcode-imagebanner" class="shortcode-option">
    <h5><?php _e( 'Image Banner', 'nota' ); ?></h5>

    <div class="option">
        <label for="imagebanner-image"><?php _e( 'Background Image', 'nota' ); ?></label>
        <input id="imagebanner-image" name="imagebanner-image" type="text" value=""/>

        <p class="info">Provide the URL here for the background image that you would like to use.</p>
    </div>
    <div class="option">
        <label for="imagebanner-animation"><?php _e( 'Content Animation', 'nota' ); ?></label>
        <select id="imagebanner-animation" name="imagebanner-animation">
            <?php echo $animatons_list; ?>
        </select>

        <p class="info">Choose the intro animation for the content.</p>
    </div>
    <div class="option">
        <label for="imagebanner-contentpos"><?php _e( 'Content Position', 'nota' ); ?></label>
        <select id="imagebanner-contentpos" name="imagebanner-contentpos">
            <option value="left"><?php _e( 'Left', 'nota' ); ?></option>
            <option value="center"><?php _e( 'Center', 'nota' ); ?></option>
            <option value="right"><?php _e( 'Right', 'nota' ); ?></option>
        </select>

        <p class="info">Choose the alignment for the content.</p>
    </div>
    <div class="option">
        <label for="imagebanner-textalign"><?php _e( 'Text Align', 'nota' ); ?></label>
        <select id="imagebanner-textalign" name="imagebanner-textalign">
            <option value="left"><?php _e( 'Left', 'nota' ); ?></option>
            <option value="center"><?php _e( 'Center', 'nota' ); ?></option>
            <option value="right"><?php _e( 'Right', 'nota' ); ?></option>
        </select>

        <p class="info">Choose the alignment for the text within the content.</p>
    </div>
    <div class="option">
        <label for="imagebanner-link"><?php _e( 'Image Banner Link', 'nota' ); ?></label>
        <input id="imagebanner-link" name="imagebanner-link" type="text" value=""/>

        <p class="info">This is optional, only provide if you'd like the entire image banner to link on click.</p>
    </div>
    <div class="option">
        <label for="imagebanner-target"
               class="for-checkbox"><?php _e( 'Open link in a new window?', 'nota' ); ?></label>
        <input id="imagebanner-target" class="checkbox" name="imagebanner-target" type="checkbox"/>
    </div>
    <div class="option">
        <label for="imagebanner-extraclass"><?php _e( 'Extra class', 'nota' ); ?></label>
        <input id="imagebanner-extraclass" name="imagebanner-extraclass" type="text" value=""/>

        <p class="info">Provide any extra classes you'd like to add here (optional).</p>
    </div>
</div>


<!--//////////////////////////////
////    TABLE
//////////////////////////////-->

<div id="shortcode-tables" class="shortcode-option">
    <h5><?php _e( 'Tables', 'nota' ); ?></h5>

    <div class="option">
        <label for="table-type"><?php _e( 'Table style', 'nota' ); ?></label>
        <select id="table-type" name="table-type">
            <option value="standard_minimal"><?php _e( 'Standard minimal table', 'nota' ); ?></option>
            <option value="striped_minimal"><?php _e( 'Striped minimal table', 'nota' ); ?></option>
            <option
                value="standard_bordered"><?php _e( 'Standard bordered table', 'nota' ); ?></option>
            <option value="striped_bordered"><?php _e( 'Striped bordered table', 'nota' ); ?></option>
        </select>
    </div>
    <div class="option">
        <label for="table-head"><?php _e( 'Table Head', 'nota' ); ?></label>
        <select id="table-head" name="table-head">
            <option value="yes"><?php _e( 'Yes', 'nota' ); ?></option>
            <option value="no"><?php _e( 'No', 'nota' ); ?></option>
            <p class="info">Include a heading row in the table</p>
        </select>
    </div>
    <div class="option">
        <label for="table-columns"><?php _e( 'Number of columns', 'nota' ); ?></label>
        <select id="table-columns" name="table-columns">
            <option value="1"><?php _e( '1', 'nota' ); ?></option>
            <option value="2"><?php _e( '2', 'nota' ); ?></option>
            <option value="3"><?php _e( '3', 'nota' ); ?></option>
            <option value="4"><?php _e( '4', 'nota' ); ?></option>
            <option value="5"><?php _e( '5', 'nota' ); ?></option>
            <option value="6"><?php _e( '6', 'nota' ); ?></option>
        </select>
    </div>

    <div class="option">
        <label for="table-rows"><?php _e( 'Number of rows', 'nota' ); ?></label>
        <select id="table-rows" name="table-rows">
            <option value="1"><?php _e( '1', 'nota' ); ?></option>
            <option value="2"><?php _e( '2', 'nota' ); ?></option>
            <option value="3"><?php _e( '3', 'nota' ); ?></option>
            <option value="4"><?php _e( '4', 'nota' ); ?></option>
            <option value="5"><?php _e( '5', 'nota' ); ?></option>
            <option value="6"><?php _e( '6', 'nota' ); ?></option>
            <option value="7"><?php _e( '7', 'nota' ); ?></option>
            <option value="8"><?php _e( '8', 'nota' ); ?></option>
            <option value="9"><?php _e( '9', 'nota' ); ?></option>
            <option value="10"><?php _e( '10', 'nota' ); ?></option>
        </select>
    </div>
</div>

<!--//////////////////////////////
////    LISTS
//////////////////////////////-->

<div id="shortcode-lists" class="shortcode-option">
    <h5><?php _e( 'Lists', 'nota' ); ?></h5>

    <div class="option">
        <label for="list-icon"><?php _e( 'List icon', 'nota' ); ?></label>
        <input type="text" class="search-icon-grid textfield" placeholder="Search Icon">
        <input id="list-icon" name="list-icon" type="text" value="" style="visibility: hidden;"/>
        <ul class="font-icon-grid"><?php echo $icon_list; ?></ul>
    </div>
    <div class="option">
        <label for="list-items"><?php _e( 'Number of list items', 'nota' ); ?></label>
        <select id="list-items" name="list-items">
            <option value="1"><?php _e( '1', 'nota' ); ?></option>
            <option value="2"><?php _e( '2', 'nota' ); ?></option>
            <option value="3"><?php _e( '3', 'nota' ); ?></option>
            <option value="4"><?php _e( '4', 'nota' ); ?></option>
            <option value="5"><?php _e( '5', 'nota' ); ?></option>
            <option value="6"><?php _e( '6', 'nota' ); ?></option>
            <option value="7"><?php _e( '7', 'nota' ); ?></option>
            <option value="8"><?php _e( '8', 'nota' ); ?></option>
            <option value="9"><?php _e( '9', 'nota' ); ?></option>
            <option value="10"><?php _e( '10', 'nota' ); ?></option>
            <p class="info">You can easily add more by duplicating the code after.</p>
        </select>
    </div>
    <div class="option">
        <label for="list-extraclass"><?php _e( 'List Extra Class', 'nota' ); ?></label>
        <input id="list-extraclass" name="list-extraclass" type="text" value=""/>

        <p class="info">Optional, for extra styling/custom colour control.</a></p>
    </div>
</div>

</fieldset>

<!-- CLOSE #shortcode_panel -->
</div>

<div class="buttons clearfix">
    <input type="submit" id="insert" name="insert" value="<?php _e( 'Insert Shortcode', 'nota' ); ?>"
           onClick="embedSelectedShortcode();"/>
</div>

<!-- CLOSE #shortcode_wrap -->
</div>

<!-- CLOSE swiftframework_shortcode_form -->
</form>

<!-- CLOSE body -->
</body>

<!-- CLOSE html -->
</html>