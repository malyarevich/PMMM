<?php

    /*
    *
    *	Swift Page Builder - Templates Return Class
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    function spb_get_prebuilt_template_code( $template_id ) {

        $template_code = "";

        $prebuilt_templates = spb_get_prebuilt_templates();

        if ( array_key_exists( $template_id, $prebuilt_templates ) ) {
            $template_code = $prebuilt_templates[ $template_id ]['code'];
        }

        return $template_code;

    }


    function spb_get_prebuilt_templates() {

        // create array
        $prebuilt_templates = array();

//		$prebuilt_templates["agency-two-home"] = array(
//			'id' => "agency-two-home",
//			'name' => 'Home (Agency Two)',
//			'code' => '[cool]'
//		);	

        $prebuilt_templates["features-tour"] = array(
            'id'   => "features-tour",
            'name' => 'Features Tour',
            'code' => '[spb_row wrap_type="full-width" row_bg_type="image" bg_image="59" bg_type="cover" parallax_image_height="window-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" responsive_vis="hidden-xs_hidden-sm" row_id="intro" row_name="INTRO" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="370px" responsive_vis="hidden-xs_hidden-sm" width="1/1" el_position="first last"] [spb_text_block animation="fade-in" animation_delay="100" padding_vertical="0" padding_horizontal="8" el_class="mb0 pb0" width="1/1" el_position="first last"]
			<h1 style="text-align: center;"><span style="color: #ffffff;">Build an incredible website in minutes.</span></h1>
			<h6 style="text-align: center;"><span style="color: #999999;"><span style="color: #666666;">DESIGNED FOR ANY PURPOSE:</span> BUSINESSES • PORTFOLIOS • SHOPS • RESTAURANTS • MAGAZINES • AGENCIES • PERSONAL. </span></h6>
			&nbsp;
			<div style="text-align: center;"><a class="hero-continue smooth-scroll-link" href="#right">[icon image="ss-navigatedown" character="" size="small" cont="no" float="none" color="#ffffff"]</a></div>
			&nbsp;
			
			&nbsp;
			
			&nbsp;
			
			<img class="aligncenter size-full wp-image-254" src="http://c-media.swiftideas.com/wp-content/uploads/2014/05/intro_tablet_new.png" alt="intro_tablet_new" width="1167" height="678" />
			
			[/spb_text_block] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="image" bg_image="59" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" responsive_vis="hidden-lg_hidden-md" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="60px" responsive_vis="hidden-lg_hidden-md" width="1/1" el_position="first last"] [spb_text_block animation="fade-in" animation_delay="100" padding_vertical="0" padding_horizontal="8" el_class="mb0 pb0" width="1/1" el_position="first last"]
			<h1 style="text-align: center;"><span style="color: #ffffff;">Build an incredible website in minutes.</span></h1>
			<h6 style="text-align: center;"><span style="color: #999999;"><span style="color: #666666;">DESIGNED FOR ANY PURPOSE:</span> BUSINESSES • PORTFOLIOS • SHOPS • RESTAURANTS • MAGAZINES • AGENCIES • PERSONAL. </span></h6>
			&nbsp;
			<div style="text-align: center;"><a class="hero-continue smooth-scroll-link" href="#right">[icon image="ss-navigatedown" character="" size="small" cont="no" float="none" color="#ffffff"]</a></div>
			[/spb_text_block] [spb_blank_spacer height="60px" responsive_vis="hidden-lg_hidden-md" width="1/1" el_position="first last"] [spb_image image="254" image_size="full" frame="noframe" intro_animation="none" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#ffffff" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" row_id="right" row_name="THE RIGHT CHOICE" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_text_block animation="fade-in" animation_delay="0" padding_vertical="8" padding_horizontal="15" width="1/1" el_position="first last"]
			<h1 style="text-align: center;">The best of its kind.</h1>
			&nbsp;
			<p style="text-align: center;">Beautifully designed, precision built &amp; supported with dedication. Cardinal is the most flexible, feature-rich &amp; robust WordPress theme on the market. Whether you are a novice or an expert, building a clients website or your own;</p>
			
			<h3 style="text-align: center;">Cardinal is the right choice for you.</h3>
			&nbsp;
			<p style="text-align: center;">[sf_fullscreenvideo type="text-button" btntext="WATCH THE VIDEO" imageurl="" videourl="https://vimeo.com/98591307" extraclass=""]</p>
			[/spb_text_block] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#f7f7f7" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" row_id="styles" row_name="STYLES" row_el_class="no-shadow pb0" width="1/1" el_position="first last"] [spb_blank_spacer height="120px" responsive_vis="hidden-xs" width="1/1" el_position="first last"] [spb_blank_spacer height="60px" responsive_vis="hidden-lg_hidden-md_hidden-sm" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="18" el_class="mb0 pb0" width="1/1" el_position="first last"]
			<h1 style="text-align: center;">3 Unique Styles.</h1>
			[/spb_text_block] [spb_swift_slider fullscreen="false" maxheight="500" slidecount="3" category="styles" loop="true" nav="true" pagination="true" fullwidth="yes" width="1/1" el_position="first last"] [spb_blank_spacer height="120px" responsive_vis="hidden-xs" width="1/1" el_position="first last"] [spb_blank_spacer height="30px" responsive_vis="hidden-lg_hidden-md_hidden-sm" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" row_id="demos" row_name="DEMOS" row_el_class="no-shadow pb0" width="1/1" el_position="first last"] [spb_blank_spacer height="120px" responsive_vis="hidden-xs" width="1/1" el_position="first last"] [spb_blank_spacer height="60px" responsive_vis="hidden-lg_hidden-md_hidden-sm" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="18" el_class="mb0 pb0" width="1/1" el_position="first last"]
			<h1 style="text-align: center;"><span style="color: #ffffff;">30 Stunning Demos.</span></h1>
			[/spb_text_block] [spb_blank_spacer height="30px" width="1/1" el_position="first last"] [spb_portfolio display_type="gallery" multi_size_ratio="1/1" fullwidth="no" gutters="yes" columns="3" show_title="yes" show_subtitle="no" show_excerpt="no" hover_show_excerpt="no" excerpt_length="20" item_count="30" category="All" portfolio_filter="yes" pagination="no" button_enabled="no" width="1/1" el_position="first last"] [spb_blank_spacer height="128px" responsive_vis="hidden-xs" width="1/1" el_position="first last"] [spb_blank_spacer height="68px" responsive_vis="hidden-lg_hidden-md_hidden-sm" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#252525" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_id="gettingstarted" row_name="GETTING STARTED" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="32px" width="1/1" el_position="first last"] [spb_blank_spacer height="1px" width="1/6" el_position="first"] [spb_text_block animation="fade-in" animation_delay="0" padding_vertical="8" padding_horizontal="16" width="2/3"]
			<h3 style="text-align: center;"><span style="color: #ffffff;">Getting started has never been easier or quicker - simply follow our three step process.</span></h3>
			[/spb_text_block] [spb_blank_spacer height="1px" width="1/6" el_position="last"] [spb_text_block animation="fade-in" animation_delay="0" padding_vertical="5" padding_horizontal="5" width="1/3" el_position="first"]
			<p style="text-align: center;"><img class="alignnone size-full wp-image-64" src="http://cardinal.swiftideas.com/wp-content/uploads/2014/05/demo_icon.png" alt="demo_icon" width="70" height="70" /></p>
			
			<h4 style="text-align: center;"><span style="color: #ffffff;">1. CHOOSE A DEMO</span></h4>
			<p style="text-align: center;"><span style="color: #999999;">Once you have purchased, you will have a choice of 30 beautiful demos at your fingertips. All of them contain unique pages, with industry specific colours, fonts and styling.</span></p>
			[/spb_text_block] [spb_text_block animation="fade-in" animation_delay="200" padding_vertical="5" padding_horizontal="5" width="1/3"]
			<p style="text-align: center;"><img class="alignnone size-full wp-image-67" src="http://cardinal.swiftideas.com/wp-content/uploads/2014/05/import_icon_2.png" alt="import_icon_2" width="70" height="70" /></p>
			
			<h4 style="text-align: center;"><span style="color: #ffffff;">2. USE OUR CONTENT IMPORTER</span></h4>
			<p style="text-align: center;"><span style="color: #999999;">Let our custom Demo Content Importer do the heavy lifting. Your chosen demo will be painlessly imported along with all the settings, colours, fonts and content you need.</span></p>
			[/spb_text_block] [spb_text_block animation="fade-in" animation_delay="400" padding_vertical="5" padding_horizontal="5" width="1/3" el_position="last"]
			<p style="text-align: center;"><img class="alignnone size-full wp-image-66" src="http://cardinal.swiftideas.com/wp-content/uploads/2014/05/customise_icon.png" alt="customise_icon" width="70" height="70" /></p>
			
			<h4 style="text-align: center;"><span style="color: #ffffff;">3. CUSTOMISE AND GO!</span></h4>
			<p style="text-align: center;"><span style="color: #999999;">Now all the demo content is uploaded it is time get customising. Change anything you want, the design style, the fonts, the colours, the settings and the content.</span></p>
			[/spb_text_block] [spb_blank_spacer height="120px" responsive_vis="hidden-xs" width="1/1" el_position="first last"] [spb_blank_spacer height="60px" responsive_vis="hidden-lg_hidden-md_hidden-sm" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#ffffff" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_id="everything" row_name="EVERYTHING YOU NEED" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="120px" responsive_vis="hidden-xs" width="1/1" el_position="first last"] [spb_blank_spacer height="60px" responsive_vis="hidden-lg_hidden-md_hidden-sm" width="1/1" el_position="first last"] [spb_text_block animation="fade-in" animation_delay="0" padding_vertical="0" padding_horizontal="18" width="1/1" el_position="first last"]
			<h1 style="text-align: center;">Everything you need.</h1>
			[/spb_text_block] [spb_blank_spacer height="50px" responsive_vis="hidden-xs" width="1/1" el_position="first last"] [spb_blank_spacer height="25px" responsive_vis="hidden-lg_hidden-md_hidden-sm" width="1/1" el_position="first last"] [spb_text_block animation="fade-in" animation_delay="100" padding_vertical="5" padding_horizontal="5" width="1/3" el_position="first"]
			<p style="text-align: center;"><img class="alignnone size-full wp-image-147" src="http://cardinal.swiftideas.com/wp-content/uploads/2014/05/pagebuilder5.png" alt="pagebuilder5" width="324" height="333" /></p>
			
			<h4 style="text-align: center;">SWIFT PAGE BUILDER</h4>
			<p style="text-align: center;">Powerful yet simple, our Page Builder comes with 45 Elements, 14 Pre-set layouts and the ability to save your favourite pages &amp; elements.</p>
			[/spb_text_block] [spb_text_block animation="fade-in" animation_delay="300" padding_vertical="5" padding_horizontal="5" width="1/3"]
			<p style="text-align: center;"><img class="alignnone size-full wp-image-149" src="http://cardinal.swiftideas.com/wp-content/uploads/2014/05/shop_till_you_drop_img.png" alt="shop_till_you_drop_img" width="324" height="333" /></p>
			
			<h4 style="text-align: center;">SHOP TILL YOU DROP</h4>
			<p style="text-align: center;">Built for WooCommerce 2.0+, Cardinal comes loaded with 5 different FULL Shop demos. Everything you need to start selling today.</p>
			[/spb_text_block] [spb_text_block animation="fade-in" animation_delay="500" padding_vertical="5" padding_horizontal="5" width="1/3" el_position="last"]
			<p style="text-align: center;"><img class="alignnone size-full wp-image-150" src="http://cardinal.swiftideas.com/wp-content/uploads/2014/05/shortcodes.png" alt="shortcodes" width="324" height="333" /></p>
			
			<h4 style="text-align: center;">LOADED WITH SHORTCODES</h4>
			<p style="text-align: center;">Our selection of beautifully designed and cleverly developed shortcodes work seamlessly with our Page Builder &amp; in Classic mode.</p>
			[/spb_text_block] [spb_blank_spacer height="100px" responsive_vis="hidden-xs" width="1/1" el_position="first last"] [spb_blank_spacer height="50px" responsive_vis="hidden-lg_hidden-md_hidden-sm" width="1/1" el_position="first last"] [spb_promo_bar display_type="promo-arrow" promo_bar_text="Check out the Cardinal Asset Reference site for more info on Page Builder Assets &amp; Shortcodes" btn_text="Button Text" btn_color="accent" href="http://cardinal.swiftideas.com/asset-reference/" target="_self" bg_color="#40d0e6" text_color="#ffffff" fullwidth="yes" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" row_id="mobile" row_name="MOBILE-READY" row_el_class="mb0 pb0" width="1/1" el_position="first last"] [spb_blank_spacer height="1px" width="1/4" el_position="first"] [spb_text_block animation="none" animation_delay="0" padding_vertical="18" padding_horizontal="8" width="1/2"]
			<h2 style="text-align: center;"><span style="color: #ffffff;">MOBILE-READY</span></h2>
			<p style="text-align: center;"><span style="color: #ffffff;">Cardinal is mobile ready right from the start. Its responsive, mobile-first fluid grid system scales up or down as the device or viewport size increases or decreases. So your content will always look its best.</span></p>
			[/spb_text_block] [spb_blank_spacer height="1px" width="1/4" el_position="last"] [spb_image image="253" image_size="full" image_width="993" frame="noframe" intro_animation="none" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" el_class="mb0 pb0" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#f7f7f7" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_id="unique" row_name="UNIQUE IN A FEW CLICKS" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="1px" width="1/6" el_position="first"] [spb_text_block animation="none" animation_delay="0" padding_vertical="12" padding_horizontal="18" el_class="mb0" width="2/3"]
			<h3 style="text-align: center;">Every Cardinal website can be made to look unique with just a few clicks.</h3>
			&nbsp;
			<p style="text-align: center;">[sf_button colour="black" type="standard" size="standard" link="http://themedemo.swiftideas.com/cardinal/?preview" target="_self" icon="" dropshadow="no" extraclass=""]TRY OUT OUR SANDBOX DEMO[/sf_button] [sf_button colour="accent" type="standard" size="standard" link="http://cardinal.swiftideas.com/page-builder/" target="_self" icon="" dropshadow="no" extraclass=""]TRY PAGEBUILDER DEMO[/sf_button]</p>
			[/spb_text_block] [spb_blank_spacer height="1px" width="1/6" el_position="last"] [spb_text_block animation="fade-in" animation_delay="0" padding_vertical="5" padding_horizontal="5" width="1/4" el_position="first"]
			<p style="text-align: center;"><img class="alignnone size-full wp-image-71" src="http://cardinal.swiftideas.com/wp-content/uploads/2014/05/headers_top.png" alt="headers_top" width="70" height="70" /></p>
			
			<h4 style="text-align: center;">TOP HEADERS</h4>
			<p style="text-align: center;">9 Different horizontal Headers, 3 of which can be completely Full-width. And include Subscribe, Account, Translation, cart + Wishlist and Search functionality.</p>
			[/spb_text_block] [spb_text_block animation="fade-in" animation_delay="200" padding_vertical="5" padding_horizontal="5" width="1/4"]
			<p style="text-align: center;"><img class="alignnone size-full wp-image-70" src="http://cardinal.swiftideas.com/wp-content/uploads/2014/05/headers_left_vert.png" alt="headers_left_vert" width="70" height="70" /></p>
			
			<h4 style="text-align: center;">VERTICAL LEFT</h4>
			<p style="text-align: center;">You can also choose a vertical Header and navigation combo. This option allows for all the functionality of a horizontal Header, so there is no missing out.</p>
			[/spb_text_block] [spb_text_block animation="fade-in" animation_delay="400" padding_vertical="5" padding_horizontal="5" width="1/4"]
			<p style="text-align: center;"><img class="alignnone size-full wp-image-69" src="http://cardinal.swiftideas.com/wp-content/uploads/2014/05/headers_right_vert.png" alt="headers_right_vert" width="70" height="70" /></p>
			
			<h4 style="text-align: center;">VERTICAL RIGHT</h4>
			<p style="text-align: center;">If desired, you can also have the vertical Header and navigation aligned to the right. Like the left aligned version, you will not miss out on any functionality.</p>
			[/spb_text_block] [spb_text_block animation="fade-in" animation_delay="600" padding_vertical="5" padding_horizontal="5" width="1/4" el_position="last"]
			<p style="text-align: center;"><img class="alignnone size-full wp-image-68" src="http://cardinal.swiftideas.com/wp-content/uploads/2014/05/header_unlimited.png" alt="header_unlimited" width="70" height="70" /></p>
			
			<h4 style="text-align: center;">NAVIGATION</h4>
			<p style="text-align: center;">Cardinal has an almost infinite number of navigation possibilities. From centred logos, mobile menus to overlay style navigation and everything in-between.</p>
			[/spb_text_block] [spb_blank_spacer height="100px" responsive_vis="hidden-xs" width="1/1" el_position="first last"] [spb_blank_spacer height="5px" responsive_vis="hidden-lg_hidden-md_hidden-sm" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#ffffff" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_id="features" row_name="FEATURES" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="100px" responsive_vis="hidden-xs" width="1/1" el_position="first last"] [spb_blank_spacer height="50px" responsive_vis="hidden-lg_hidden-md_hidden-sm" width="1/1" el_position="first last"] [spb_divider type="heading" heading_text="Full Features List" text="Go to top" top_margin="0px" bottom_margin="0px" fullwidth="no" width="1/1" el_position="first last"] [spb_icon_box box_type="standard-title" icon="ss-touchtonephone" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3" el_position="first"]
			<h4>FREE SUPPORT &amp; UPDATES</h4>
			We pride ourselves on professional and timely support. If you have any pre-sales questions or need any help, feel free to ask.
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="ss-view" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3"]
			<h4>RETINA-READY</h4>
			We’ve taken great care to ensure that Cardinal is fully retina-ready. So now matter what device it is being viewed on, it’ll always look great.
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="ss-layergroup" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3" el_position="last"]
			<h4>4 PREMIUM SLIDERS</h4>
			Choose from 4 amazing sliders; Master Slider, Revolution Slider, LayerSlider and of course our new and improved Swift Slider.
			
			[/spb_icon_box] [spb_divider type="standard" text="Go to top" top_margin="20px" bottom_margin="20px" fullwidth="no" width="1/1" el_position="first last"] [spb_icon_box box_type="standard-title" icon="ss-usergroup" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3" el_position="first"]
			<h4>100% TRANSLATABLE</h4>
			Cardinal is fully translatable using the WPML plugin, so you can truly connect with a global audience, <em>et voilà!</em>
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="ss-erase" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3"]
			<h4>EXTENSIVE THEME OPTIONS</h4>
			Customise Cardinal even further with a plethora of advanced options, settings &amp; powerful page meta.
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="ss-globe" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3" el_position="last"]
			<h4>SEO OPTIMISED</h4>
			Search engines love Cardinal. It is built with clean, semantic HTML5 code, and has been thoroughly tested with SEO in mind.
			
			[/spb_icon_box] [spb_divider type="standard" text="Go to top" top_margin="20px" bottom_margin="20px" fullwidth="no" width="1/1" el_position="first last"] [spb_icon_box box_type="standard-title" icon="ss-chat" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3" el_position="first"]
			<h4>SOCIAL INTEGRATION</h4>
			Social buttons, social sharing and social integration. Now you can show off your content from Twitter, Flickr &amp; Instagram.
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="ss-droplet" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3"]
			<h4>CUSTOM COLORS</h4>
			Unparalleled access to color properties, over 60 different elements that can be customised using the Colorizer to create a unique look.
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="ss-crop" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3" el_position="last"]
			<h4>FLEXIBLE LAYOUTS</h4>
			Choose from boxed or full-width layouts, and if you wish you can also adjust the site max-width (default is 1170px.)
			
			[/spb_icon_box] [spb_divider type="standard" text="Go to top" top_margin="20px" bottom_margin="20px" fullwidth="no" width="1/1" el_position="first last"] [spb_icon_box box_type="standard-title" icon="fa-font" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3" el_position="first"]
			<h4>600+ GOOGLE FONTS</h4>
			Choose from a huge selection of FREE Google fonts. Preview them, then adjust the weight, size, line-height, letter-spacing and colour.
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="fa-align-right" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3"]
			<h4>RTL SUPPORT</h4>
			As well as being 100% translatable, Cardinal also supports Right-To-Left languages. So now everyone can enjoy your content.
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="fa-ellipsis-v" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" el_class="mt0 pt0" width="1/3" el_position="last"]
			<h4>ONE PAGE NAVIGATION</h4>
			Cardinal comes with 2 different types of one-page navigation. Standard with text tooltips and Counter + arrows.
			
			[/spb_icon_box] [spb_divider type="standard" text="Go to top" top_margin="20px" bottom_margin="20px" fullwidth="no" width="1/1" el_position="first last"] [spb_icon_box box_type="standard-title" icon="ss-sugarpackets" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3" el_position="first"]
			<h4>ANIMATIONS</h4>
			Get creative with 12 different animation styles. Apply them to Text-blocks, Images &amp; Icon Boxes to bring life to your content.
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="ss-lightbulb" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3"]
			<h4>AWESOME ICONS</h4>
			Gizmo webfont license included, so you can access 300+ stunning Dutch icons. As well as 439 Font Awesome icons (version 4.1.0).
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="ss-picture" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3" el_position="last"]
			<h4>76+ PORTFOLIO TYPES</h4>
			We have documented 76 variations of portfolio, but of course the possibilities are almost infinite. <a href="http://cardinal.swiftideas.com/asset-reference/">Check them out here.</a>
			
			[/spb_icon_box] [spb_divider type="standard" text="Go to top" top_margin="20px" bottom_margin="20px" fullwidth="no" width="1/1" el_position="first last"] [spb_icon_box box_type="standard-title" icon="ss-star" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" el_class="mb0 pb0" width="1/3" el_position="first"]
			<h4><span style="color: #00bca3;">NEW</span> SWIFT SLIDER</h4>
			The new &amp; improved Swift Slider is an easy-to-use responsive slider with native video playback &amp; many other features.
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="ss-highvolume" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" el_class="mb0 pb0" width="1/3"]
			<h4>PROMO BAR</h4>
			Set a site wide call to action with our promo bar. Choose from 3 different styles and lots of customisable options.
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="ss-notebook" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" el_class="mb0 pb0" width="1/3" el_position="last"]
			<h4>15+ BLOG TYPES</h4>
			We have documented 15 variations of blog, but of course the possibilities are almost infinite. <a href="http://cardinal.swiftideas.com/asset-reference/">Check them out here.</a>
			
			[/spb_icon_box] [spb_divider type="standard" text="Go to top" top_margin="20px" bottom_margin="20px" fullwidth="no" width="1/1" el_position="first last"] [spb_icon_box box_type="standard-title" icon="ss-video" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" el_class="mt0 pt0" width="1/3" el_position="first"]
			<h4>TUTORIAL VIDEOS</h4>
			As intuitive as Cardinal is, we have provided HD tutorial videos to explain how to do certain things. If you still have any queries, just ask.
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="ss-crosshair" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" el_class="mt0 pt0" width="1/3"]
			<h4>iLIGHTBOX</h4>
			iLightBox allows you to easily create the most beautiful responsive overlay windows using the jQuery JavaScript library.
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="ss-rows" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3" el_position="last"]
			<h4>MEGA MENU</h4>
			Sometimes a standard menu just won’t cut it, that’s why Cardinal allows you to roll out a super slick mega menu.
			
			[/spb_icon_box] [spb_divider type="standard" text="Go to top" top_margin="20px" bottom_margin="20px" fullwidth="no" width="1/1" el_position="first last"] [spb_icon_box box_type="standard-title" icon="ss-smartphone" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3" el_position="first"]
			<h4>FULLY RESPONSIVE</h4>
			Cardinal is mobile ready right from the start. Its responsive, fluid grid system scales up/down as the device or viewport changes.
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="ss-briefcase" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3"]
			<h4>PSD FILES INCLUDED</h4>
			Cardinal includes a bundle of PSD files. All of Page Builder elements, shortcodes, styles and various options are incorporated.
			
			[/spb_icon_box] [spb_icon_box box_type="standard-title" icon="fa-gear" target="_self" animation="none" animation_delay="0" icon_color="#cccccc" bg_color="#ffffff" width="1/3" el_position="last"]
			<h4>LOADED WITH SHORTCODES</h4>
			An incredible selection of beautifully designed and cleverly built shortcodes. All of which work seamlessly with/without our Page Builder.
			
			[/spb_icon_box] [spb_blank_spacer height="75px" responsive_vis="hidden-xs" width="1/1" el_position="first last"] [spb_blank_spacer height="25px" responsive_vis="hidden-lg_hidden-md_hidden-sm" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="image" row_bg_color="#f7f7f7" bg_image="179" bg_type="pattern" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" responsive_vis="hidden-xs_hidden-sm" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="1px" responsive_vis="hidden-xs_hidden-sm" width="1/4" el_position="first"] [spb_text_block animation="fade-in" animation_delay="0" padding_vertical="5" padding_horizontal="12" width="1/2"]
			<h3 style="text-align: center;">Last, but by no means least;
			Cardinal includes the following:</h3>
			<p style="text-align: center;">iLightbox worth $13
			WooCommerce Quickview worth $18
			Go Responsive Pricing Tables worth $17
			Revolution Slider worth $19
			Master Slider worth $25
			LayerSlider $18</p>
			
			<h4 style="text-align: center;">TOTAL: $110</h4>
			[/spb_text_block] [spb_blank_spacer height="1px" responsive_vis="hidden-xs_hidden-sm" width="1/4" el_position="last"] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="image" row_bg_color="#f7f7f7" bg_image="179" bg_type="pattern" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" responsive_vis="hidden-lg_hidden-md" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="1px" responsive_vis="hidden-lg_hidden-md" width="1/4" el_position="first"] [spb_text_block animation="fade-in" animation_delay="0" padding_vertical="5" padding_horizontal="12" width="1/2"]
			<h3 style="text-align: center;">Last, but by no means least; Cardinal includes the following:</h3>
			<p style="text-align: center;">iLightbox worth $13
			WooCommerce Quickview worth $18
			Go Responsive Pricing Tables worth $17
			Revolution Slider worth $19
			Master Slider worth $25
			LayerSlider $18</p>
			
			<h4 style="text-align: center;">TOTAL: $110</h4>
			[/spb_text_block] [spb_blank_spacer height="1px" responsive_vis="hidden-lg_hidden-md" width="1/4" el_position="last"] [/spb_row] [spb_promo_bar display_type="promo-arrow" promo_bar_text="PURCHASE NOW &amp; GET GOING" btn_text="Button Text" btn_color="accent" href="http://cardinal.swiftideas.com/buy" target="_blank" bg_color="#fe504f" text_color="#ffffff" fullwidth="yes" width="1/1" el_position="first last"]'
        );

        // agency two home
        $prebuilt_templates["agency-two-home"] = array(
            'id'   => "agency-two-home",
            'name' => 'Home (Agency Two)',
            'code' => '[spb_blank_spacer height="60px" width="1/1" el_position="first last"] [spb_icon_box title="Incredible Clean Design" box_type="left-icon-alt" icon="ss-picture" link="#" target="_self" animation="fade-from-bottom" animation_delay="0" icon_color="#af2444" width="1/3" el_position="first"]
			
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nunc dui, tristique in semper vel.
			
			[/spb_icon_box] [spb_icon_box title="Powerful Yet Simple" box_type="left-icon-alt" icon="ss-like" link="#" target="_self" animation="fade-from-bottom" animation_delay="100" icon_color="#d3375b" width="1/3"]
			
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nunc dui, tristique in semper vel.
			
			[/spb_icon_box] [spb_icon_box title="Super Flexible" box_type="left-icon-alt" icon="ss-man" link="#" target="_self" animation="fade-from-bottom" animation_delay="200" icon_color="#ff4b75" width="1/3" el_position="last"]
			
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nunc dui, tristique in semper vel.
			
			[/spb_icon_box] [spb_icon_box title="Engineered With Precision" box_type="left-icon-alt" icon="ss-bezier" link="#" target="_self" animation="fade-from-bottom" animation_delay="300" icon_color="#c7294e" width="1/3" el_position="first"]
			
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nunc dui, tristique in semper vel.
			
			[/spb_icon_box] [spb_icon_box title="Tutorial Videos" box_type="left-icon-alt" icon="ss-video" link="#" target="_self" animation="fade-from-bottom" animation_delay="400" icon_color="#df3960" width="1/3"]
			
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nunc dui, tristique in semper vel.
			
			[/spb_icon_box] [spb_icon_box title="Free Support &amp; Updates" box_type="left-icon-alt" icon="ss-chat" link="#" target="_self" animation="fade-from-bottom" animation_delay="500" icon_color="#fd6e8f" width="1/3" el_position="last"]
			
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nunc dui, tristique in semper vel.
			
			[/spb_icon_box] [spb_blank_spacer height="60px" width="1/1" el_position="first last"] [spb_portfolio display_type="gallery" multi_size_ratio="1/1" fullwidth="yes" gutters="no" columns="4" show_title="yes" show_subtitle="yes" show_excerpt="no" hover_show_excerpt="no" excerpt_length="20" item_count="8" category="All" portfolio_filter="no" pagination="no" button_enabled="no" width="1/1" el_position="first last"] [spb_promo_bar display_type="promo-button" promo_bar_text="Like What You See? We are Just Getting Started" btn_text="CHECK OUT OUR WORK" btn_color="transparent-light" href="#" target="_self" bg_color="#222222" text_color="#ffffff" fullwidth="yes" width="1/1" el_position="first last"] [spb_row wrap_type="content-width" row_bg_type="color" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="95" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" responsive_vis="hidden-xs_hidden-sm" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="5" el_class="mb0 pb0" width="1/3" el_position="first"]
			
			[list]
			[list_item icon="ss-right"]3 Stunning Styles with more on the way[/list_item]
			[list_item icon="ss-right"]30 Demos with simple content install[/list_item]
			[list_item icon="ss-right"]Easy-to-use drag &amp; drop page builder[/list_item]
			[list_item icon="ss-right"]Lots of Google Fonts to choose from[/list_item]
			[list_item icon="ss-right"]Full Shop built for WooCommerce 2.0+[/list_item]
			[list_item icon="ss-right"]Loaded with Shortcodes &amp; Goodies[/list_item]
			[list_item icon="ss-right"]Super slick Mega Menu with icons[/list_item]
			<span style="line-height: 1.5em;">[/list]</span>
			
			[/spb_text_block] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="5" el_class="mb0 pb0" width="1/3"]
			
			[list]
			[list_item icon="ss-right"]Stunning Gizmo Icon webfont license[/list_item]
			[list_item icon="ss-right"]Revolution &amp; Layer Sliders included[/list_item]
			[list_item icon="ss-right"]Swift Slider with curtain option[/list_item]
			[list_item icon="ss-right"]Next-level animation features[/list_item]
			[list_item icon="ss-right"]Font size &amp; line height control[/list_item]
			[list_item icon="ss-right"]Infinite custom colour choice[/list_item]
			[list_item icon="ss-right"]Efficient &amp; friendly support[/list_item]
			<span style="line-height: 1.5em;">[/list]</span>
			
			[/spb_text_block] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="5" el_class="mb0 pb0" width="1/3" el_position="last"]
			
			[list]
			[list_item icon="ss-right"]Video &amp; Image Parallax backgrounds[/list_item]
			[list_item icon="ss-right"]Immense Portfolio &amp; Blog options[/list_item]
			[list_item icon="ss-right"]Boxed &amp; Full-width page layouts[/list_item]
			[list_item icon="ss-right"]SEO optimised &amp; pristine code[/list_item]
			[list_item icon="ss-right"]Customisable page widths[/list_item]
			[list_item icon="ss-right"]In-depth video tutorials[/list_item]
			[list_item icon="ss-right"]Extensive Theme options[/list_item]
			<span style="line-height: 1.5em;">[/list]</span>
			
			[/spb_text_block] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="95" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" responsive_vis="hidden-lg_hidden-md" width="1/1" el_position="first last"] [spb_blank_spacer height="1px" width="1/6" el_position="first"] [spb_text_block animation="none" animation_delay="0" padding_vertical="5" padding_horizontal="20" el_class="mb0 pb0" width="2/3"]
			
			[list]
			[list_item icon="ss-right"]3 Stunning Styles with more on the way[/list_item]
			[list_item icon="ss-right"]30 Demos with simple content install[/list_item]
			[list_item icon="ss-right"]Easy-to-use drag &amp; drop page builder[/list_item]
			[list_item icon="ss-right"]Lots of Google Fonts to choose from[/list_item]
			[list_item icon="ss-right"]Full Shop built for WooCommerce 2.0+[/list_item]
			[list_item icon="ss-right"]Loaded with Shortcodes &amp; Goodies[/list_item]
			[list_item icon="ss-right"]Super slick Mega Menu with icons[/list_item]
			<span style="line-height: 1.5em;">[/list]</span>
			
			[/spb_text_block] [spb_blank_spacer height="1px" width="1/6" el_position="last"] [spb_blank_spacer height="1px" width="1/6" el_position="first"] [spb_text_block animation="none" animation_delay="0" padding_vertical="5" padding_horizontal="20" el_class="mb0 pb0" width="2/3"]
			
			[list]
			[list_item icon="ss-right"]Stunning Gizmo Icon webfont license[/list_item]
			[list_item icon="ss-right"]Revolution &amp; Layer Sliders included[/list_item]
			[list_item icon="ss-right"]Swift Slider with curtain option[/list_item]
			[list_item icon="ss-right"]Next-level animation features[/list_item]
			[list_item icon="ss-right"]Font size &amp; line height control[/list_item]
			[list_item icon="ss-right"]Infinite custom colour choice[/list_item]
			[list_item icon="ss-right"]Efficient &amp; friendly support[/list_item]
			<span style="line-height: 1.5em;">[/list]</span>
			
			[/spb_text_block] [spb_blank_spacer height="1px" width="1/6" el_position="last"] [spb_blank_spacer height="1px" width="1/6" el_position="first"] [spb_text_block animation="none" animation_delay="0" padding_vertical="5" padding_horizontal="20" el_class="mb0 pb0" width="2/3"]
			
			[list]
			[list_item icon="ss-right"]Video &amp; Image Parallax backgrounds[/list_item]
			[list_item icon="ss-right"]Immense Portfolio &amp; Blog options[/list_item]
			[list_item icon="ss-right"]Boxed &amp; Full-width page layouts[/list_item]
			[list_item icon="ss-right"]SEO optimised &amp; pristine code[/list_item]
			[list_item icon="ss-right"]Customisable page widths[/list_item]
			[list_item icon="ss-right"]In-depth video tutorials[/list_item]
			[list_item icon="ss-right"]Extensive Theme options[/list_item]
			<span style="line-height: 1.5em;">[/list]</span>
			
			[/spb_text_block] [spb_blank_spacer height="1px" width="1/6" el_position="last"] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#757090" bg_image="166" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" vertical_center="false" width="1/1" el_position="first last"] [spb_blank_spacer height="1px" width="1/6" el_position="first"] [spb_image image="11182" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" el_class="pb0 mb0" width="2/3"] [spb_blank_spacer height="1px" width="1/6" el_position="last"] [spb_boxed_content type="coloured" custom_bg_colour="#24222c" custom_text_colour="#ffffff" padding_vertical="12" padding_horizontal="12" width="1/3" el_position="first"]
			<h4><span style="color: #ffffff;">IT IS AS EASY AS A,B,C OR 1,2,3.</span></h4>
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nunc dui, tristique in semper vel, congue sed ligula. Nam dolor ligula, faucibus id sodales in, auctor fringill.
			
			[sf_button colour="transparent-light" type="standard" size="standard" link="#" target="_self" icon="" dropshadow="no" extraclass=""]LEARN MORE[/sf_button]
			
			[/spb_boxed_content] [spb_boxed_content type="coloured" custom_bg_colour="#382430" custom_text_colour="#ffffff" padding_vertical="12" padding_horizontal="12" width="1/3"]
			<h4><span style="color: #ffffff;">FREE SUPPORT &amp; UPDATES.</span></h4>
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nunc dui, tristique in semper vel, congue sed ligula. Nam dolor ligula, faucibus id sodales in, auctor fringill.
			
			[sf_button colour="transparent-light" type="standard" size="standard" link="#" target="_self" icon="" dropshadow="no" extraclass=""]LEARN MORE[/sf_button]
			
			[/spb_boxed_content] [spb_boxed_content type="coloured" custom_bg_colour="#512b38" custom_text_colour="#ffffff" padding_vertical="12" padding_horizontal="12" width="1/3" el_position="last"]
			<h4><span style="color: #ffffff;">STUNNING DESIGN, INFINITE OPTIONS.</span></h4>
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nunc dui, tristique in semper vel, congue sed ligula. Nam dolor ligula, faucibus id sodales in, auctor fringill.
			
			<span style="line-height: 1.5em;">[sf_button colour="transparent-light" type="standard" size="standard" link="#" target="_self" icon="" dropshadow="no" extraclass=""]LEARN MORE[/sf_button]</span>
			
			[/spb_boxed_content] [/spb_row] [spb_blank_spacer height="60px" width="1/1" el_position="first last"] [spb_icon_box title="Fully Responsive" box_type="standard" icon="ss-smartphone" link="#" target="_self" animation="pop-up" animation_delay="0" icon_color="#0c3e4c" width="1/4" el_position="first"]
			
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nunc dui, tristique.
			
			[/spb_icon_box] [spb_icon_box title="100% Translatable" box_type="standard" icon="ss-globe" link="#" target="_self" animation="pop-up" animation_delay="200" icon_color="#6f4f78" width="1/4"]
			
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nunc dui, tristique.
			
			[/spb_icon_box] [spb_icon_box title="Retina-Ready" box_type="standard" icon="ss-view" link="#" target="_self" animation="pop-up" animation_delay="400" icon_color="#0d516a" width="1/4"]
			
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nunc dui, tristique.
			
			[/spb_icon_box] [spb_icon_box title="Full Shop" box_type="standard" icon="ss-cart" link="#" target="_self" animation="pop-up" animation_delay="600" icon_color="#138885" width="1/4" el_position="last"]
			
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nunc dui, tristique.
			
			[/spb_icon_box] [spb_blank_spacer height="60px" width="1/1" el_position="first last"]'
        );

        $prebuilt_templates["agency-two-about"] = array(
            'id'   => "agency-two-about",
            'name' => 'About (Agency Two)',
            'code' => '[spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#ff4b75" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" vertical_center="true" width="1/1" el_position="first last"] [spb_boxed_content type="coloured" custom_bg_colour="#512b38" custom_text_colour="#ffffff" padding_vertical="10" padding_horizontal="20" width="1/1" el_position="first last"]
			<h2 style="text-align: center;"><span style="color: #ffffff;">We work together with brands to craft strategies along with a unique and innovative digital approach.</span></h2>
			[/spb_boxed_content] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#f7f7f7" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" width="1/1" el_position="first last"] [spb_blank_spacer height="30px" width="1/1" el_position="first last"] [spb_image image="11209" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/6" el_position="first"] [spb_image image="11208" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/6"] [spb_image image="11205" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/6"] [spb_image image="11204" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/6"] [spb_image image="11207" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/6"] [spb_image image="11206" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/6" el_position="last"] [/spb_row] [spb_team display_type="gallery" carousel="no" item_columns="5" item_count="5" category="All" pagination="no" fullwidth="yes" gutters="no" width="1/1" el_position="first last"] [spb_row wrap_type="content-width" row_bg_type="image" row_bg_color="#222222" bg_image="11201" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="90px" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="5" padding_horizontal="0" width="1/4" el_position="first"]
			<div style="text-align: center;">[chart percentage="90" size="170" barcolour="#ff4b75" trackcolour="#222222" content="ss-pixels" align="center"]</div>
			<h3 style="text-align: center;"><span style="color: #ffffff;">Web Development</span></h3>
			[/spb_text_block] [spb_text_block animation="none" animation_delay="0" padding_vertical="5" padding_horizontal="0" width="1/4"]
			<div style="text-align: center;">[chart percentage="85" size="170" barcolour="#ff4b75" trackcolour="#222222" content="ss-bezier" align="center"]</div>
			<h3 style="text-align: center;"><span style="color: #ffffff;">Graphic Design</span></h3>
			[/spb_text_block] [spb_text_block animation="none" animation_delay="0" padding_vertical="5" padding_horizontal="0" width="1/4"]
			<div style="text-align: center;">[chart percentage="70" size="170" barcolour="#ff4b75" trackcolour="#222222" content="ss-users" align="center"]</div>
			<h3 style="text-align: center;"><span style="color: #ffffff;">Social Media</span></h3>
			[/spb_text_block] [spb_text_block animation="none" animation_delay="0" padding_vertical="5" padding_horizontal="0" width="1/4" el_position="last"]
			<div style="text-align: center;">[chart percentage="84" size="170" barcolour="#ff4b75" trackcolour="#222222" content="ss-businessuser" align="center"]</div>
			<h3 style="text-align: center;"><span style="color: #ffffff;">Accounts</span></h3>
			[/spb_text_block] [spb_blank_spacer height="90px" width="1/1" el_position="first last"] [/spb_row] [spb_blog_grid item_count="20" twitter_username="swiftideas" instagram_id="1193751843" instagram_token="1193751843.5b9e1e6.c8c5c0a5df0e4d5687e389f24adb8643" fullwidth="yes" width="1/1" el_position="first last"] [spb_promo_bar display_type="promo-button" promo_bar_text="Ready to take the next step?" btn_text="BUY CARDINAL" btn_color="black" href="#" target="_self" bg_color="#ff4b75" text_color="#ffffff" fullwidth="yes" width="1/1" el_position="first last"]'
        );

        $prebuilt_templates["agency-three-culture"] = array(
            'id'   => "agency-three-culture",
            'name' => 'Culture (Agency Three)',
            'code' => '[spb_row wrap_type="content-width" row_bg_type="color" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="60" remove_element_spacing="no" vertical_center="true" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="20" width="1/1" el_position="first last"]
			<h1><span style="color: #ffffff;">Welcome to Cardinal!</span></h1>
			<h2><span style="color: #ffffff;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer imperdiet iaculis ipsum aliquet ultricies. Sed a tincidunt enim. Maecenas ultriceles viverra ligula, vel lobortis ante pulvinar sed. Donec erat magna, aliquam vitae semper vitae, accumsan vel nisl. Vivamus pellentesque orci.</span></h2>
			&nbsp;
			
			[/spb_text_block] [/spb_row] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="20" el_class="mb0 pb0" width="1/1" el_position="first last"]
			<h1><span style="color: #ffffff;">What We are Up To...</span></h1>
			[/spb_text_block] [spb_blank_spacer height="30px" el_class="mb0 pb0" width="1/1" el_position="first last"] [spb_blog_grid item_count="10" twitter_username="swiftideas" instagram_id="1193751843" instagram_token="1193751843.5b9e1e6.c8c5c0a5df0e4d5687e389f24adb8643" fullwidth="yes" width="1/1" el_position="first last"] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#e4e4e4" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" width="1/1" el_position="first last"] [spb_blank_spacer height="90px" el_class="mb0 pb0" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="20" el_class="mb0 pb0" width="1/1" el_position="first last"]
			<h1>Our Amazing Team</h1>
			[/spb_text_block] [spb_blank_spacer height="30px" el_class="mb0 pb0" width="1/1" el_position="first last"] [spb_team display_type="gallery" carousel="no" item_columns="5" item_count="5" category="All" pagination="no" fullwidth="yes" gutters="no" el_class="mt0 pt0 mb0 pb0" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" vertical_center="true" width="1/1" el_position="first last"] [spb_icon_box title="We Love To Eat Pizza" box_type="animated" icon="ss-cheesepizza" target="_self" animation="pop-up" animation_delay="0" icon_color="#ffffff" text_color="#ffffff" bg_color="#509eed" flip_bg_color="#ffffff" flip_text_color="#509eed" width="1/4" el_position="first"]
			
			Lorem ipsum dolor sit amet,
			
			consectetur adipiscing elit.
			
			[/spb_icon_box] [spb_icon_box title="We Are Pixel Gods" box_type="animated" icon="ss-bezier" target="_self" animation="pop-up" animation_delay="200" icon_color="#ffffff" text_color="#ffffff" bg_color="#509eed" flip_bg_color="#ffffff" flip_text_color="#509eed" width="1/4"]
			
			Lorem ipsum dolor sit amet,
			
			consectetur adipiscing elit.
			
			[/spb_icon_box] [spb_icon_box title="We are Coffee Junkies" box_type="animated" icon="ss-mug" target="_self" animation="pop-up" animation_delay="400" icon_color="#ffffff" text_color="#ffffff" bg_color="#509eed" flip_bg_color="#ffffff" flip_text_color="#509eed" width="1/4"]
			
			Lorem ipsum dolor sit amet,
			
			consectetur adipiscing elit.
			
			[/spb_icon_box] [spb_icon_box title="We are Full Of Ideas" box_type="animated" icon="ss-lightbulb" target="_self" animation="pop-up" animation_delay="600" icon_color="#ffffff" text_color="#ffffff" bg_color="#509eed" flip_bg_color="#ffffff" flip_text_color="#509eed" width="1/4" el_position="last"]
			
			Lorem ipsum dolor sit amet,
			
			consectetur adipiscing elit.
			
			[/spb_icon_box] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" vertical_center="true" width="1/1" el_position="first last"] [spb_image image="274" image_size="full" frame="noframe" intro_animation="fade-from-left" animation_delay="200" fullwidth="no" lightbox="yes" link_target="_self" caption_pos="below" el_class="mb0 pb0" width="1/2" el_position="first"] [spb_image image="268" image_size="full" frame="noframe" intro_animation="fade-from-bottom" animation_delay="200" fullwidth="no" lightbox="yes" link_target="_self" caption_pos="below" el_class="mb0 pb0" width="1/4"] [spb_image image="272" image_size="full" frame="noframe" intro_animation="fade-from-bottom" animation_delay="200" fullwidth="no" lightbox="yes" link_target="_self" caption_pos="below" el_class="mb0 pb0" width="1/4" el_position="last"] [/spb_row]'
        );

        $prebuilt_templates["agency-four-home"] = array(
            'id'   => "agency-four-home",
            'name' => 'Home (Agency Four)',
            'code' => '[spb_blank_spacer height="120px" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="0" width="1/1" el_position="first last"]
			<h1 style="text-align: center;">Welcome to Cardinal.</h1>
			[/spb_text_block] [spb_blank_spacer height="120px" width="1/1" el_position="first last"] [spb_portfolio display_type="gallery" multi_size_ratio="1/1" fullwidth="no" gutters="no" columns="3" show_title="yes" show_subtitle="yes" show_excerpt="no" hover_show_excerpt="no" excerpt_length="20" item_count="9" category="All" portfolio_filter="no" pagination="no" button_enabled="no" width="1/1" el_position="first last"] [spb_blog blog_type="masonry" gutters="no" columns="3" fullwidth="no" item_count="8" category="All" offset="0" posts_order="desc" show_title="yes" show_excerpt="yes" show_details="yes" excerpt_length="20" content_output="excerpt" show_read_more="no" social_integration="yes" twitter_username="swiftideas" instagram_id="1193751843" instagram_token="1193751843.5b9e1e6.c8c5c0a5df0e4d5687e389f24adb8643" blog_filter="no" pagination="none" width="1/1" el_position="first last"]'
        );

        $prebuilt_templates["agency-four-about"] = array(
            'id'   => "agency-four-about",
            'name' => 'About (Agency Four)',
            'code' => '[spb_blank_spacer height="60px" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="20" width="1/1" el_position="first last"]
			<h5 style="text-align: center;">Our Mission.</h5>
			<h3 style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula.</h3>
			[/spb_text_block] [spb_blank_spacer height="120px" width="1/1" el_position="first last"] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#ffffff" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" width="1/1" el_position="first last"] [spb_blank_spacer height="120px" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="20" width="1/1" el_position="first last"]
			<h5 style="text-align: center;">Our Team.</h5>
			[/spb_text_block] [spb_blank_spacer height="60px" width="1/1" el_position="first last"] [spb_team display_type="gallery" carousel="no" item_columns="3" item_count="6" category="All" pagination="no" fullwidth="no" gutters="no" width="1/1" el_position="first last"] [spb_blank_spacer height="120px" width="1/1" el_position="first last"] [/spb_row] [spb_blank_spacer height="120px" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="20" width="1/1" el_position="first last"]
			<h5 style="text-align: center;">Stay Social</h5>
			[/spb_text_block] [spb_blank_spacer height="60px" width="1/1" el_position="first last"] [spb_blog_grid item_count="15" twitter_username="swiftideas" instagram_id="1193751843" instagram_token="1193751843.5b9e1e6.c8c5c0a5df0e4d5687e389f24adb8643" fullwidth="yes" width="1/1" el_position="first last"]'
        );

        $prebuilt_templates["corporate-one-home"] = array(
            'id'   => "corporate-one-home",
            'name' => 'Home (Corporate One)',
            'code' => '[spb_blank_spacer height="40px" width="1/1" el_position="first last"] [spb_row wrap_type="content-width" row_bg_type="image" bg_image="48" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" responsive_vis="hidden-xs" row_el_class="mb0 pb0 no-shadow" width="1/1" el_position="first last"] [spb_column responsive_vis="hidden-xs" width="1/2" el_position="first"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="0" width="1/1" el_position="first last"]
			<h2>Meet your new favourite WordPress theme.</h2>
			[/spb_text_block] [spb_blank_spacer height="19px" responsive_vis="hidden-xs" width="1/1" el_position="first last"] [spb_icon_box box_type="left-icon-alt" icon="ss-picture" target="_self" animation="none" animation_delay="0" icon_color="#c6c6c6" el_class="ml0 pl0 mb0 pb0" width="1/1" el_position="first last"]
			<h6>INCREDIBLE CLEAN DESIGN</h6>
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos.
			
			[/spb_icon_box] [spb_icon_box box_type="left-icon-alt" icon="ss-bezier" target="_self" animation="none" animation_delay="0" icon_color="#c6c6c6" el_class="ml0 pl0 mt0 pt0 mb pb0" width="1/1" el_position="first last"]
			<h6>Engineered With Precision</h6>
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos.
			
			[/spb_icon_box] [spb_icon_box box_type="left-icon-alt" icon="ss-video" target="_self" animation="none" animation_delay="0" icon_color="#c6c6c6" el_class="ml0 pl0" width="1/1" el_position="first last"]
			<h6>Tutorial Videos</h6>
			[sf_fullscreenvideo type="image-button2" btntext="" imageurl="http://cardinal.swiftideas.com/corporate-demo/wp-content/uploads/sites/23/2014/05/about_typing.jpg" videourl="#" extraclass=""]
			
			[/spb_icon_box] [spb_blank_spacer height="60px" responsive_vis="hidden-xs" width="1/1" el_position="first last"] [/spb_column] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="0" width="1/2" el_position="last"]
			
			<img class="alignright size-full wp-image-51" src="http://cardinal.swiftideas.com/corporate-demo/wp-content/uploads/sites/23/2014/03/poster_quote_v2.png" alt="poster_quote_v2" width="400" height="200" />
			
			[/spb_text_block] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" bg_image="48" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" responsive_vis="hidden-lg_hidden-md" row_el_class="mb0 pb0 no-shadow" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="0" width="1/1" el_position="first last"]
			<h2>Meet your new favourite WordPress theme.</h2>
			[/spb_text_block] [spb_blank_spacer height="19px" responsive_vis="hidden-lg_hidden-md" width="1/1" el_position="first last"] [spb_icon_box box_type="left-icon-alt" icon="ss-picture" target="_self" animation="none" animation_delay="0" icon_color="#c6c6c6" el_class="ml0 pl0 mb0 pb0" width="1/1" el_position="first last"]
			<h6>INCREDIBLE CLEAN DESIGN</h6>
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos.
			
			[/spb_icon_box] [spb_icon_box box_type="left-icon-alt" icon="ss-bezier" target="_self" animation="none" animation_delay="0" icon_color="#c6c6c6" el_class="ml0 pl0 mt0 pt0 mb pb0" width="1/1" el_position="first last"]
			<h6>Engineered With Precision</h6>
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos.
			
			[/spb_icon_box] [spb_icon_box box_type="left-icon-alt" icon="ss-video" target="_self" animation="none" animation_delay="0" icon_color="#c6c6c6" el_class="ml0 pl0" width="1/1" el_position="first last"]
			<h6>Tutorial Videos</h6>
			[sf_fullscreenvideo type="image-button2" btntext="" imageurl="http://cardinal.swiftideas.com/corporate-demo/wp-content/uploads/sites/23/2014/05/about_typing.jpg" videourl="#" extraclass=""]
			
			[/spb_icon_box] [spb_blank_spacer height="60px" responsive_vis="hidden-lg_hidden-md" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="image" row_bg_color="#222222" bg_image="50" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="450px" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#f7f7f7" bg_image="48" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_el_class="mb0 pb0 no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="40px" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="0" width="1/1" el_position="first last"]
			<h2 style="text-align: center;">You will be up and running in no time.</h2>
			[/spb_text_block] [spb_icon_box box_type="standard" icon="ss-layergroup" target="_self" animation="none" animation_delay="0" el_class="ml0 pl0 mb0 pb0" width="1/3" el_position="first"]
			<h6>3 stunning styles</h6>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis.
			
			[/spb_icon_box] [spb_icon_box box_type="standard" icon="ss-desktop" target="_self" animation="none" animation_delay="0" el_class="ml0 pl0 mb0 pb0" width="1/3"]
			<h6>30 example sites!</h6>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis.
			
			[/spb_icon_box] [spb_icon_box box_type="standard" icon="ss-downloadcloud" target="_self" animation="none" animation_delay="0" el_class="ml0 pl0 mb0 pb0" width="1/3" el_position="last"]
			<h6>demo content importer</h6>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis.
			
			[/spb_icon_box] [spb_blank_spacer height="60px" width="1/1" el_position="first last"] [spb_image image="53" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" el_class="mb0 pb0" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="image" row_bg_color="#222222" bg_image="54" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="450px" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#f7f7f7" bg_image="48" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_el_class="mb0 pb0 no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="40px" width="1/1" el_position="first last"] [spb_column width="1/2" el_position="first"] [spb_blank_spacer height="19px" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="7" width="1/2" el_position="first"]
			
			[chart percentage="100" size="170" barcolour="#464646" trackcolour="#f7f7f7" content="ss-man" align="left"]
			<h6 style="text-align: center;">FIGURE 1.1</h6>
			[/spb_text_block] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="7" width="1/2" el_position="last"]
			
			[chart percentage="90" size="170" barcolour="#998675" trackcolour="#f7f7f7" content="ss-lock" align="left"]
			<h6 style="text-align: center;">FIGURE 1.2</h6>
			[/spb_text_block] [spb_blank_spacer height="19px" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="7" width="1/2" el_position="first"]
			
			[chart percentage="80" size="170" barcolour="#c7b299" trackcolour="#f7f7f7" content="ss-signpost" align="left"]
			<h6 style="text-align: center;">FIGURE 1.3</h6>
			[/spb_text_block] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="7" width="1/2" el_position="last"]
			
			[chart percentage="70" size="170" barcolour="#f1cfa7" trackcolour="#f7f7f7" content="ss-calculator" align="left"]
			<h6 style="text-align: center;">FIGURE 1.4</h6>
			[/spb_text_block] [/spb_column] [spb_column width="1/2" el_position="last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="0" width="1/1" el_position="first last"]
			<h2>Cardinal is perfect for any use, here are 3 reason why:</h2>
			[/spb_text_block] [spb_blank_spacer height="19px" width="1/1" el_position="first last"] [spb_icon_box box_type="left-icon-alt" icon="ss-picture" character="1" target="_self" animation="none" animation_delay="0" icon_color="#c6c6c6" el_class="ml0 pl0 mb0 pb0" width="1/1" el_position="first last"]
			<h6>It is Powerful yet simple</h6>
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos.
			
			[/spb_icon_box] [spb_icon_box box_type="left-icon-alt" icon="ss-bezier" character="2" target="_self" animation="none" animation_delay="0" icon_color="#c6c6c6" el_class="ml0 pl0 mt0 pt0 mb pb0" width="1/1" el_position="first last"]
			<h6>It is super flexible</h6>
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos.
			
			[/spb_icon_box] [spb_icon_box box_type="left-icon-alt" icon="ss-bezier" character="3" target="_self" animation="none" animation_delay="0" icon_color="#c6c6c6" el_class="ml0 pl0 mt0 pt0 mb pb0" width="1/1" el_position="first last"]
			<h6>It comes with free support &amp; updates</h6>
			Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos.
			
			[/spb_icon_box] [spb_blank_spacer height="60px" width="1/1" el_position="first last"] [/spb_column] [/spb_row]'
        );

        $prebuilt_templates["corporate-one-pricing"] = array(
            'id'   => "corporate-one-pricing",
            'name' => 'Pricing (Corporate One)',
            'code' => '[spb_row wrap_type="content-width" row_bg_type="color" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" width="1/1" el_position="first last"] [spb_blank_spacer height="120px" width="1/1" el_position="first last"] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/1" el_position="first last"]
			
			[go_pricing id="demo-blue_01"]
			
			[/spb_text_block] [spb_blank_spacer height="90px" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#f7f7f7" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" width="1/1" el_position="first last"] [spb_blank_spacer height="90px" width="1/1" el_position="first last"] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/1" el_position="first last"]
			<h2 style="text-align: center;">What is included.</h2>
			[/spb_text_block] [spb_blank_spacer height="30px" width="1/1" el_position="first last"] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/3" el_position="first"]
			
			[icon image="fa-group" character="" size="small" cont="no" float="left" color="#998675"]
			
			&nbsp;
			
			<span style="color: #464646;"><strong>Professional Service
			</strong>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</span>
			
			[/spb_text_block] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/3"]
			
			[icon image="fa-gift" character="" size="small" cont="no" float="left" color="#998675"]
			
			&nbsp;
			
			<span style="color: #464646;"><strong>Loaded with features
			</strong>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</span>
			
			[/spb_text_block] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/3" el_position="last"]
			
			[icon image="fa-stack-overflow" character="" size="small" cont="no" float="left" color="#998675"]
			
			&nbsp;
			
			<span style="color: #464646;"><strong>Unparalleled flexibility
			</strong>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</span>
			
			[/spb_text_block] [spb_blank_spacer height="40px" width="1/1" el_position="first last"] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/3" el_position="first"]
			
			[icon image="fa-laptop" character="" size="small" cont="no" float="left" color="#998675"]
			
			&nbsp;
			
			<span style="color: #464646;"><strong>Fully Respsonsive
			</strong>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</span>
			
			[/spb_text_block] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/3"]
			
			[icon image="fa-rocket" character="" size="small" cont="no" float="left" color="#998675"]
			
			&nbsp;
			
			<span style="color: #464646;"><strong>The most powerful theme
			</strong>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</span>
			
			[/spb_text_block] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/3" el_position="last"]
			
			[icon image="fa-comments" character="" size="small" cont="no" float="left" color="#998675"]
			
			&nbsp;
			
			<span style="color: #464646;"><strong>Free support &amp; updates
			</strong>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</span>
			
			[/spb_text_block] [spb_blank_spacer height="90px" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" width="1/1" el_position="first last"] [spb_blank_spacer height="90px" width="1/1" el_position="first last"] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/1" el_position="first last"]
			<h2 style="text-align: center;">Frequently Asked Questions.</h2>
			[/spb_text_block] [spb_blank_spacer height="30px" width="1/1" el_position="first last"] [spb_text_block padding_vertical="0" padding_horizontal="4" width="1/2" el_position="first"]
			
			<span style="color: #464646;"><strong>First question:
			</strong>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</span>
			
			<span style="color: #464646;"><strong>Second question:
			</strong>Aliquam rhoncus molestie auctor. Suspendisse ac risus eros. Vivamus faucibus auctor nibh id semper. Quisque sit amet ante ante. Phasellus vestibulum accumsan ligula quis adipiscing. Sed eget consectetur urna. Ut et auctor risus. Vivamus sed sapien in nunc rhoncus eleifend eu quis elit.</span>
			
			<span style="color: #464646;"><strong>Third question:
			</strong>Mauris ultricies, justo eu convallis placerat, felis enim ornare nisi, vitae mattis nulla ante id dui. Ut lectus purus, commodo et tincidunt vel, interdum sed lectus.</span>
			
			[/spb_text_block] [spb_text_block padding_vertical="0" padding_horizontal="4" width="1/2" el_position="last"]
			
			<span style="color: #464646;"><strong>Fourth question:
			</strong>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</span>
			
			<span style="color: #464646;"><strong>Fifth question:
			</strong>Aliquam rhoncus molestie auctor. Suspendisse ac risus eros. Vivamus faucibus auctor nibh id semper. Quisque sit amet ante ante. Phasellus vestibulum accumsan ligula quis adipiscing. Sed eget consectetur urna. Ut et auctor risus. Vivamus sed sapien in nunc rhoncus eleifend eu quis elit.</span>
			
			<span style="color: #464646;"><strong>Sixth question:
			</strong>Mauris ultricies, justo eu convallis placerat, felis enim ornare nisi, vitae mattis nulla ante id dui. Ut lectus purus, commodo et tincidunt vel, interdum sed lectus.</span>
			
			[/spb_text_block] [spb_blank_spacer height="60px" width="1/1" el_position="first last"] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/1" el_position="first last"]
			<h2 style="text-align: center;">Still Have Questions?</h2>
			[/spb_text_block] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/1" el_position="first last"]
			<p style="text-align: center;">[sf_button colour="transparent-dark" type="sf-icon-stroke" size="large" link="#" target="_self" icon="ss-chat" dropshadow="no" extraclass=""]Chat with us[/sf_button] [sf_button colour="transparent-dark" type="sf-icon-stroke" size="large" link="#" target="_self" icon="ss-mail" dropshadow="no" extraclass=""]Drop us an email[/sf_button]</p>
			[/spb_text_block] [spb_blank_spacer height="60px" width="1/1" el_position="first last"] [/spb_row]'
        );

        $prebuilt_templates["corporate-three-home"] = array(
            'id'   => "corporate-three-home",
            'name' => 'Home (Corporate Three)',
            'code' => '[spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#ffffff" bg_image="48" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_el_class="mb0 pb0 no-shadow" width="1/1" el_position="first last"] [spb_text_block padding_vertical="6" padding_horizontal="20" width="1/1" el_position="first last"]
			<h3 style="text-align: center;">SHAPING THE FUTURE OF BUSINESS.</h3>
			<h4 style="text-align: center;"></h4>
			<h4 style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae. Sed dui lorem, adipiscing in adipiscing et, interdum nec metus.</h4>
			&nbsp;
			<p style="text-align: center;">[sf_button colour="accent" type="large" size="standard" link="#" target="_self" icon="" dropshadow="no" extraclass=""]OUR CAPABILITIES[/sf_button]</p>
			[/spb_text_block] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#f2f2f2" bg_image="50" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_boxed_content type="coloured" custom_bg_colour="#2c414c" custom_text_colour="#ffffff" padding_vertical="10" padding_horizontal="20" width="1/2" el_position="first"]
			<h3 style="text-align: center;">[icon image="ss-sync" character="" size="medium" cont="no" float="none" color=""]</h3>
			<h3 style="text-align: center;"><span style="color: #ffffff;">Optimize an existing site</span></h3>
			<p style="text-align: center;">Already have a site? Perfect. We will work with you to improve its usability, speed, and effectiveness. You will also be able to edit all your content with Siteleaf, a simple and flexible content management system.</p>
			[/spb_boxed_content] [spb_boxed_content type="coloured" custom_bg_colour="#283a44" custom_text_colour="#ffffff" padding_vertical="10" padding_horizontal="20" width="1/2" el_position="last"]
			<h3 style="text-align: center;">[icon image="ss-lightbulb" character="" size="medium" cont="no" float="none" color=""]</h3>
			<h3 style="text-align: center;"><span style="color: #ffffff;">Optimize an existing site</span></h3>
			<p style="text-align: center;">Already have a site? Perfect. We will work with you to improve its usability, speed, and effectiveness. You will also be able to edit all your content with Siteleaf, a simple and flexible content management system.</p>
			[/spb_boxed_content] [spb_promo_bar display_type="promo-arrow" promo_bar_text="Request a meeting" btn_text="Button Text" btn_color="accent" href="#" target="_self" bg_color="#2da094" text_color="#ffffff" fullwidth="yes" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#f6f6ef" bg_image="50" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_text_block padding_vertical="20" padding_horizontal="0" width="1/3" el_position="first"]
			<h3 style="text-align: center;">[icon image="ss-pen" character="" size="medium" cont="no" float="none" color=""]</h3>
			<h3 style="text-align: center;">Step 1</h3>
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
			[/spb_text_block] [spb_text_block padding_vertical="20" padding_horizontal="0" width="1/3"]
			<h3 style="text-align: center;">[icon image="ss-smartphone" character="" size="medium" cont="no" float="none" color=""]</h3>
			<h3 style="text-align: center;">Step 2</h3>
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
			[/spb_text_block] [spb_text_block padding_vertical="20" padding_horizontal="0" width="1/3" el_position="last"]
			<h3 style="text-align: center;">[icon image="ss-barchart" character="" size="medium" cont="no" float="none" color=""]</h3>
			<h3 style="text-align: center;">Step 3</h3>
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
			[/spb_text_block] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#ffffff" bg_image="48" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_el_class="mb0 pb0 no-shadow" width="1/1" el_position="first last"] [spb_text_block padding_vertical="6" padding_horizontal="20" width="1/1" el_position="first last"]
			<h3 style="text-align: center;">WHO WE ARE.</h3>
			<h4 style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae. Sed dui lorem, adipiscing in adipiscing et, interdum nec metus. Mauris ultricies, justo eu convallis placerat, felis enim ornare nisi.</h4>
			[/spb_text_block] [/spb_row] [spb_gmaps size="600" type="roadmap" zoom="12" color="#24dc97" saturation="color" fullscreen="yes" el_class="mb0" width="1/1" el_position="first last"] [spb_tab title=""] [spb_map_pin address="Brooklyn" pin_image="11294" content="
			
			This is a map pin. Click the edit button to change it.
			
			" width="1/1" el_position="first last"] [/spb_tab] [/spb_gmaps]'
        );

        $prebuilt_templates["corporate-three-about"] = array(
            'id'   => "corporate-three-about",
            'name' => 'About (Corporate Three)',
            'code' => '[spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#f6f6ef" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" width="1/1" el_position="first last"] [spb_blank_spacer height="90px" width="1/1" el_position="first last"] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/1" el_position="first last"]
			<h2 style="text-align: center;">What is included.</h2>
			[/spb_text_block] [spb_blank_spacer height="30px" width="1/1" el_position="first last"] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/3" el_position="first"]
			<p style="text-align: center;">[icon image="ss-bezier" character="" size="medium" cont="no" float="none" color=""]</p>
			
			<h3 style="text-align: center;"><span style="color: #464646;">Loaded with features</span></h3>
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
			[/spb_text_block] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/3"]
			<p style="text-align: center;">[icon image="ss-unlock" character="" size="medium" cont="no" float="none" color=""]</p>
			
			<h3 style="text-align: center;"><span style="color: #464646;">Unparalleled flexibility</span></h3>
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
			[/spb_text_block] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/3" el_position="last"]
			<p style="text-align: center;">[icon image="ss-layergroup" character="" size="medium" cont="no" float="none" color=""]</p>
			
			<h3 style="text-align: center;">30 Demos!!!</h3>
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
			[/spb_text_block] [spb_blank_spacer height="40px" width="1/1" el_position="first last"] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/3" el_position="first"]
			<p style="text-align: center;">[icon image="ss-tablet" character="" size="medium" cont="no" float="none" color=""]</p>
			
			<h3 style="text-align: center;">Fully Responsive</h3>
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
			[/spb_text_block] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/3"]
			<p style="text-align: center;">[icon image="ss-like" character="" size="medium" cont="no" float="none" color=""]</p>
			
			<h3 style="text-align: center;">The Most Powerful Theme</h3>
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
			[/spb_text_block] [spb_text_block padding_vertical="0" padding_horizontal="0" width="1/3" el_position="last"]
			<p style="text-align: center;">[icon image="ss-businessuser" character="" size="medium" cont="no" float="none" color=""]</p>
			
			<h3 style="text-align: center;">Free Support &amp; Updates</h3>
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
			[/spb_text_block] [spb_blank_spacer height="90px" width="1/1" el_position="first last"] [/spb_row] [spb_blank_spacer height="120px" width="1/1" el_position="first last"] [spb_team title="Our team" display_type="standard-alt" carousel="yes" item_columns="3" item_count="6" category="All" pagination="no" fullwidth="no" gutters="yes" width="1/1" el_position="first last"] [spb_blank_spacer height="120px" width="1/1" el_position="first last"]'
        );

        $prebuilt_templates["corporate-four-home"] = array(
            'id'   => "corporate-four-home",
            'name' => 'Home (Corporate Four)',
            'code' => '[spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#111417" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_id="ourclients" row_name="Our Clients" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="45px" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="0" width="1/1" el_position="first last"]
			<h6 style="text-align: center;"><span style="color: #666d78;">OUR CLIENTS</span></h6>
			[/spb_text_block] [spb_image image="11330" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/6" el_position="first"] [spb_image image="11331" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/6"] [spb_image image="11332" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/6"] [spb_image image="11333" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/6"] [spb_image image="11334" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/6"] [spb_image image="11335" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/6" el_position="last"] [spb_blank_spacer height="30px" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#f3f5f7" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_id="theplatform" row_name="The Platform" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="6" padding_horizontal="20" width="1/1" el_position="first last"]
			<h3 style="text-align: center;">[hr_bold]</h3>
			<h2 style="text-align: center;">OUR MISSION</h2>
			<h4 style="text-align: center;"></h4>
			<h3 style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae. Sed dui lorem, adipiscing in adipiscing et, interdum nec metus.</h3>
			[/spb_text_block] [spb_blank_spacer height="30px" width="1/6" el_position="first"] [spb_image image="11364" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="2/3"] [spb_blank_spacer height="30px" width="1/6" el_position="last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="6" padding_horizontal="20" width="1/1" el_position="first last"]
			<h3 style="text-align: center;"><img class="alignnone size-full wp-image-11343" src="http://cardinal.swiftideas.com/corporate-demo-four/wp-content/uploads/sites/36/2014/03/Unknown.png" alt="Unknown" width="405" height="79" /></h3>
			&nbsp;
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae. Sed dui lorem, adipiscing in adipiscing et, interdum.</p>
			<p style="text-align: center;">[sf_button colour="accent" type="standard" size="standard" link="#" target="_self" icon="" dropshadow="no" extraclass=""]TELL ME MORE[/sf_button]</p>
			[/spb_text_block] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" responsive_vis="hidden-xs_hidden-sm" row_id="casestudies" row_name="Case Studies" width="1/1" el_position="first last"] [spb_swift_slider fullscreen="false" maxheight="700" slidecount="3" category="work" loop="true" nav="true" pagination="true" fullwidth="yes" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#111417" bg_image="11351" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" responsive_vis="hidden-lg_hidden-md" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="20" padding_horizontal="10" width="1/1" el_position="first last"]
			<p style="text-align: center;"><img class="aligncenter size-full wp-image-11349" src="http://cardinal.swiftideas.com/corporate-demo-four/wp-content/uploads/sites/36/2014/05/red_bar.png" alt="red_bar" width="60" height="5" /></p>
			
			<h1 style="text-align: center;"><span style="color: #ffffff;">BE CREATIVE</span></h1>
			<p style="text-align: center;"><span style="color: #ffffff;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae. Sed dui lorem, adipiscing in adipiscing et, interdum nec metus.</span>
			<span style="color: #ffffff;">  </span></p>
			<img class="aligncenter size-full wp-image-11350" src="http://cardinal.swiftideas.com/corporate-demo-four/wp-content/uploads/sites/36/2014/05/corp4_slider_img1.png" alt="corp4_slider_img1" width="917" height="595" />
			
			[/spb_text_block] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#f3f5f7" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_id="howitworks" row_name="How It Works" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="88px" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="20" width="1/1" el_position="first last"]
			<h3 style="text-align: center;">[hr_bold]</h3>
			<h2 style="text-align: center;">HOW IT WORKS</h2>
			<h4 style="text-align: center;"></h4>
			<h3 style="text-align: center;">Mauris ultricies, justo eu convallis placerat, felis enim ornare nisi, vitae mattis nulla ante id dui. Ut lectus purus, commodo.</h3>
			[/spb_text_block] [spb_blank_spacer height="30px" width="1/1" el_position="first last"] [spb_blank_spacer height="30px" width="1/6" el_position="first"] [spb_video link="http://vimeo.com/36176127" full_width="no" width="2/3"] [spb_blank_spacer height="30px" width="1/6" el_position="last"] [spb_blank_spacer height="120px" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#f2f2f2" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_id="features" row_name="Features" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_promo_bar display_type="promo-text" promo_bar_text="Isnt it about time you treated yourself?" btn_text="Button Text" btn_color="accent" href="#" target="_self" bg_color="#aabac1" text_color="#ffffff" fullwidth="yes" width="1/1" el_position="first last"] [spb_boxed_content type="coloured" custom_bg_colour="#2b3842" custom_text_colour="#ffffff" padding_vertical="10" padding_horizontal="20" width="1/3" el_position="first"]
			<h3 style="text-align: center;">[icon image="ss-picture" character="" size="medium" cont="no" float="none" color=""]</h3>
			<h3 style="text-align: center;"><span style="color: #ffffff;">Incredible Clean Design</span></h3>
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
			[/spb_boxed_content] [spb_boxed_content type="coloured" custom_bg_colour="#212835" custom_text_colour="#ffffff" padding_vertical="10" padding_horizontal="20" width="1/3"]
			<h3 style="text-align: center;">[icon image="ss-bezier" character="" size="medium" cont="no" float="none" color=""]</h3>
			<h3 style="text-align: center;"><span style="color: #ffffff;">Engineered With Precision</span></h3>
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
			[/spb_boxed_content] [spb_boxed_content type="coloured" custom_bg_colour="#111417" custom_text_colour="#ffffff" padding_vertical="10" padding_horizontal="20" width="1/3" el_position="last"]
			<h3 style="text-align: center;">[icon image="ss-chat" character="" size="medium" cont="no" float="none" color=""]</h3>
			<h3 style="text-align: center;"><span style="color: #ffffff;">Free Support &amp; Updates</span></h3>
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
			[/spb_boxed_content] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#f3f5f7" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_id="signup" row_name="Sign Up" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="68px" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="20" width="1/1" el_position="first last"]
			<h3 style="text-align: center;">[hr_bold]</h3>
			<h2 style="text-align: center;">SIGN UP</h2>
			<h4 style="text-align: center;"></h4>
			<h3 style="text-align: center;">Mauris ultricies, justo eu convallis placerat, felis enim ornare nisi, vitae mattis nulla ante id dui. Ut lectus purus, commodo et tincidunt vel, interdum sed lectus. Vestibulum adipiscing tempor nisi id elementu sadips ipsums dolores uns fugiats.</h3>
			[/spb_text_block] [spb_blank_spacer height="30px" width="1/6" el_position="first"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="20" width="2/3"]
			<h3 style="text-align: center;">[contact-form-7 id="11355" title="sign up"]</h3>
			[/spb_text_block] [spb_blank_spacer height="30px" width="1/6" el_position="last"] [spb_blank_spacer height="33px" width="1/1" el_position="first last"] [/spb_row] [spb_gmaps size="600" type="roadmap" zoom="15" color="#f75655" saturation="color" fullscreen="yes" el_class="mb0" width="1/1" el_position="first last"] [spb_tab title=""] [spb_map_pin address="London" pin_latitude="51.5073509" pin_longitude="-0.12775829999998223" pin_image="11361" width="1/1" el_position="first last"]
			
			This is a map pin. Click the edit button to change it.
			
			[/spb_map_pin] [/spb_tab] [/spb_gmaps] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#2b3842" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_id="contactus" row_name="Contact Us" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="6" padding_horizontal="20" width="1/1" el_position="first last"]
			<h3 style="text-align: center;">[hr_bold]</h3>
			<h2 style="text-align: center;"><span style="color: #ffffff;">CONTACT US</span></h2>
			<h4 style="text-align: center;"></h4>
			<h3 style="text-align: center;"><span style="color: #ffffff;">Drop us a line and we will be in touch.</span></h3>
			&nbsp;
			<p style="text-align: center;">[contact-form-7 id="4" title="Contact form 1"]</p>
			
			<h3 style="text-align: center;"><span style="color: #ffffff;">* Indicates a required field.</span></h3>
			[/spb_text_block] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#111417" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_id="aboutcardinal" row_name="About Cardinal" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="6" padding_horizontal="20" width="1/1" el_position="first last"]
			<p style="text-align: center;"><img class="aligncenter size-full wp-image-11357" src="http://cardinal.swiftideas.com/corporate-demo-four/wp-content/uploads/sites/36/2014/05/cardinal_corp4_logo_v3@2x.png" alt="cardinal_corp4_logo_v3@2x" width="346" height="72" /></p>
			<p style="text-align: center;"><span style="color: #ffffff;">People ask us why we get so excited about WordPress themes. It’s not about WordPress themes, it’s about helping people build better websites for them or their clients. We like to think we’re making the web a better place.</span></p>
			[/spb_text_block] [spb_team display_type="gallery" carousel="no" item_columns="3" item_count="6" category="All" pagination="no" fullwidth="no" gutters="yes" width="1/1" el_position="first last"] [spb_blank_spacer height="80px" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#040406" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="false" row_id="testimonials" row_name="Testimonials" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_blank_spacer height="30px" width="1/6" el_position="first"] [spb_testimonial_slider text_size="large" item_count="6" order="rand" category="All" autoplay="yes" width="2/3"] [spb_blank_spacer height="30px" width="1/6" el_position="last"] [/spb_row]'
        );

        $prebuilt_templates["corporate-five-home"] = array(
            'id'   => "corporate-five-home",
            'name' => 'Home (Corporate Five)',
            'code' => '[spb_blank_spacer height="10px" width="1/1" el_position="first last"] [spb_swift_slider fullscreen="false" maxheight="500" slidecount="5" category="home" loop="true" nav="true" pagination="true" fullwidth="no" width="1/1" el_position="first last"] [spb_blank_spacer height="30px" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="0" el_class="mt0 mb0" width="1/1" el_position="first last"]
			<h3 style="text-align: center;"><span style="color: #000000;">Meet your new favourite WordPress theme. Cardinal is perfect for any use, it’s simple, flexible &amp; loaded with features. It comes with 3 design styles, 30 demo sites and a super simple demo content installer. That way you can be up and running in no time. Boom!</span></h3>
			[/spb_text_block] [spb_divider type="standard" text="Go to top" top_margin="40px" bottom_margin="40px" fullwidth="no" width="1/1" el_position="first last"] [spb_column width="1/1" el_position="first last"] [spb_team display_type="standard-alt" carousel="no" item_columns="3" item_count="3" category="All" pagination="no" fullwidth="no" gutters="yes" width="1/1" el_position="first last"] [/spb_column]'
        );

        $prebuilt_templates["shop-one-home"] = array(
            'id'   => "shop-one-home",
            'name' => 'Home (Shop One)',
            'code' => '[spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#ffffff" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" vertical_center="true" responsive_vis="hidden-xs_hidden-sm" width="1/1" el_position="first last"] [spb_boxed_content type="coloured" custom_bg_colour="#222222" custom_text_colour="#ffffff" padding_vertical="20" padding_horizontal="10" width="1/4" el_position="first"]
			
			<img class="aligncenter size-full wp-image-11268" src="http://cardinal.swiftideas.com/shop-demo/wp-content/uploads/sites/15/2014/04/SALE_static.png" alt="SALE_static" width="672" height="232" />
			
			[hr_bold]
			<p style="text-align: center;">[sf_countdown year="2014" month="8" day="13" fontsize="small" displaytext=""]</p>
			[/spb_boxed_content] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="15" width="1/2"]
			<h2 style="text-align: center;">WELCOME TO A CARDINAL SHOP DEMO.</h2>
			[hr_bold]
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis dignissim.</p>
			[/spb_text_block] [spb_tweets_slider twitter_username="swiftideas" tweets_count="2" autoplay="yes" width="1/4" el_position="last"] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#ffffff" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" vertical_center="true" responsive_vis="hidden-lg_hidden-md" width="1/1" el_position="first last"] [spb_boxed_content type="coloured" custom_bg_colour="#222222" custom_text_colour="#ffffff" padding_vertical="20" padding_horizontal="10" width="1/1" el_position="first last"]
			
			<img class="aligncenter size-full wp-image-11268" src="http://cardinal.swiftideas.com/shop-demo/wp-content/uploads/sites/15/2014/04/SALE_static.png" alt="SALE_static" width="672" height="232" />
			
			[hr_bold]
			<p style="text-align: center;">[sf_countdown year="2014" month="8" day="13" fontsize="small" displaytext=""]</p>
			[/spb_boxed_content] [spb_text_block animation="none" animation_delay="0" padding_vertical="10" padding_horizontal="15" width="1/1" el_position="first last"]
			<h2 style="text-align: center;">WELCOME TO A CARDINAL SHOP DEMO.</h2>
			[hr_bold]
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis dignissim.</p>
			[/spb_text_block] [spb_tweets_slider twitter_username="swiftideas" tweets_count="2" autoplay="yes" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#222222" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" vertical_center="true" responsive_vis="hidden-xs_hidden-sm" width="1/1" el_position="first last"] [spb_image image="11272" image_size="full" frame="noframe" intro_animation="none" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/4" el_position="first"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="15" width="1/2"]
			<h2 style="text-align: center;"><span style="color: #ffffff;">S/S 14 COLLECTION NOW OUT.</span></h2>
			<p style="text-align: center;">[sf_button colour="transparent-light" type="standard" size="standard" link="/shop-demo/shop/" target="_self" icon="" dropshadow="no" extraclass=""]VIEW IT NOW[/sf_button]</p>
			[/spb_text_block] [spb_image image="11273" image_size="full" frame="noframe" intro_animation="none" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/4" el_position="last"] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#222222" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" vertical_center="true" responsive_vis="hidden-lg_hidden-md" width="1/1" el_position="first last"] [spb_image image="11272" image_size="full" frame="noframe" intro_animation="none" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="10" padding_horizontal="15" width="1/1" el_position="first last"]
			<h2 style="text-align: center;"><span style="color: #ffffff;">S/S 14 COLLECTION NOW OUT.</span></h2>
			<p style="text-align: center;">[sf_button colour="transparent-light" type="standard" size="standard" link="/shop-demo/shop/" target="_self" icon="" dropshadow="no" extraclass=""]VIEW IT NOW[/sf_button]</p>
			[/spb_text_block] [spb_image image="11273" image_size="full" frame="noframe" intro_animation="none" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#ffffff" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" vertical_center="true" responsive_vis="hidden-xs_hidden-sm" width="1/1" el_position="first last"] [spb_testimonial_slider text_size="standard" item_count="3" order="rand" category="All" autoplay="no" width="1/4" el_position="first"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="20" width="1/2"]
			<h2 style="text-align: center;">NEW STOCK COMING SOON.</h2>
			[hr_bold]
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis dignissim. Pellentesque pharetra velit eu velit elementum et convallis erat vulputate.</p>
			[/spb_text_block] [spb_boxed_content type="coloured" custom_bg_colour="#222222" custom_text_colour="#ffffff" padding_vertical="0" padding_horizontal="15" width="1/4" el_position="last"]
			<p style="text-align: center;"><span style="color: #ffffff;">Keep up to date with what we are up to</span></p>
			
			<div style="text-align: center;">[social size="large"]</div>
			[/spb_boxed_content] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#ffffff" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" vertical_center="true" responsive_vis="hidden-lg_hidden-md" width="1/1" el_position="first last"] [spb_testimonial_slider text_size="standard" item_count="3" order="rand" category="All" autoplay="no" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="10" padding_horizontal="20" width="1/1" el_position="first last"]
			<h2 style="text-align: center;">NEW STOCK COMING SOON.</h2>
			[hr_bold]
			<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis dignissim. Pellentesque pharetra velit eu velit elementum et convallis erat vulputate.</p>
			[/spb_text_block] [spb_boxed_content type="coloured" custom_bg_colour="#222222" custom_text_colour="#ffffff" padding_vertical="10" padding_horizontal="15" width="1/1" el_position="first last"]
			<p style="text-align: center;"><span style="color: #ffffff;">Keep up to date with what we are up to</span></p>
			
			<div style="text-align: center;">[social size="large"]</div>
			[/spb_boxed_content] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#f7f7f7" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" vertical_center="true" responsive_vis="hidden-xs_hidden-sm" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="20" width="1/4" el_position="first"]
			<p style="text-align: center;"><strong>ADDRESS:</strong></p>
			<p style="text-align: center;">[hr_bold]</p>
			<p style="text-align: center;">Apple Computer Inc
			1 Infinite Loop
			Cupertino CA
			95014</p>
			[/spb_text_block] [spb_gmaps size="400" type="roadmap" zoom="15" saturation="mono-dark" fullscreen="no" width="1/2"] [spb_tab title=""] [spb_map_pin address="Los Angeles" pin_latitude="34.0522342" pin_longitude="-118.2436849" pin_image="11371" width="1/1" el_position="first last"]
			
			This is a map pin. Click the edit button to change it.
			
			[/spb_map_pin] [/spb_tab] [spb_tab title=""] [/spb_tab] [spb_tab title=""] [/spb_tab] [/spb_gmaps] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="20" width="1/4" el_position="last"]
			<p style="text-align: center;"><strong>OPENING HOURS:</strong></p>
			<p style="text-align: center;">[hr_bold]</p>
			<p style="text-align: center;">Monday to Friday: 10am to 6pm
			Saturday: 10 am to 4pm
			Sunday: Closed</p>
			[/spb_text_block] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#f7f7f7" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" vertical_center="true" responsive_vis="hidden-lg_hidden-md" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="10" padding_horizontal="20" width="1/1" el_position="first last"]
			<p style="text-align: center;"><strong>ADDRESS:</strong></p>
			<p style="text-align: center;">[hr_bold]</p>
			<p style="text-align: center;">Apple Computer Inc
			1 Infinite Loop
			Cupertino CA
			95014</p>
			[/spb_text_block] [spb_gmaps size="400" type="roadmap" zoom="15" saturation="mono-dark" fullscreen="no" width="1/1" el_position="first last"] [spb_tab title=""] [spb_map_pin address="Los Angeles" pin_latitude="34.0522342" pin_longitude="-118.2436849" pin_image="11371" width="1/1" el_position="first last"]
			
			This is a map pin. Click the edit button to change it.
			
			[/spb_map_pin] [/spb_tab] [spb_tab title=""] [/spb_tab] [/spb_gmaps] [spb_text_block animation="none" animation_delay="0" padding_vertical="10" padding_horizontal="20" width="1/1" el_position="first last"]
			<p style="text-align: center;"><strong>OPENING HOURS:</strong></p>
			<p style="text-align: center;">[hr_bold]</p>
			<p style="text-align: center;">Monday to Friday: 10am to 6pm
			Saturday: 10 am to 4pm
			Sunday: Closed</p>
			[/spb_text_block] [/spb_row]'
        );

        $prebuilt_templates["shop-one-about"] = array(
            'id'   => "shop-one-about",
            'name' => 'About (Shop One)',
            'code' => '[spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#f7f7f7" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="video-height" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="30" remove_element_spacing="no" row_el_class="mb0 pb0 mt0" width="1/1" el_position="first last"] [spb_blank_spacer height="60px" width="1/1" el_position="first last"] [spb_text_block padding_vertical="0" padding_horizontal="10" el_class="mb0 pb0" width="1/2" el_position="first"]
			<h2>OUR STORY SO FAR...</h2>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis dignissim. Pellentesque pharetra velit eu velit elementum et convallis erat vulputate. Sed in nulla ut elit mollis posuere. Praesent a felis accumsan neque interdum molestie. Sed blandit convallis dignissim.
			
			[/spb_text_block] [spb_image image="11278" image_size="full" frame="noframe" intro_animation="fade-in" full_width="no" lightbox="no" link_target="_self" el_class="mb0 pb0" width="1/2" el_position="last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#fefefe" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="video-height" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="30" remove_element_spacing="no" row_el_class="mb0 pb0 mt0" width="1/1" el_position="first last"] [spb_blank_spacer height="60px" width="1/1" el_position="first last"] [spb_image image="11359" image_size="full" frame="noframe" intro_animation="fade-in" full_width="no" lightbox="no" link_target="_self" el_class="mb0 pb0" width="1/2" el_position="first"] [spb_text_block padding_vertical="5" padding_horizontal="10" width="1/2" el_position="last"]
			<h2>OUR APPROACH...</h2>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis dignissim. Pellentesque pharetra velit eu velit elementum et convallis erat vulputate. Sed in nulla ut elit mollis posuere. Praesent a felis accumsan neque interdum molestie. Sed blandit convallis dignissim.
			
			[/spb_text_block] [spb_blank_spacer height="60px" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#f7f7f7" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="video-height" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="30" remove_element_spacing="no" row_el_class="mb0 pb0 mt0" width="1/1" el_position="first last"] [spb_blank_spacer height="60px" width="1/1" el_position="first last"] [spb_text_block padding_vertical="0" padding_horizontal="10" el_class="mb0 pb0" width="1/2" el_position="first"]
			<h2>OUR PROMISE...</h2>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis dignissim. Pellentesque pharetra velit eu velit elementum et convallis erat vulputate. Sed in nulla ut elit mollis posuere. Praesent a felis accumsan neque interdum molestie. Sed blandit convallis dignissim.
			
			[/spb_text_block] [spb_image image="11316" image_size="full" frame="noframe" intro_animation="fade-in" full_width="no" lightbox="no" link_target="_self" el_class="mb0 pb0" width="1/2" el_position="last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" bg_image="11325" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="video-height" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" width="1/1" el_position="first last"] [spb_blank_spacer height="60px" width="1/1" el_position="first last"] [spb_text_block padding_vertical="0" padding_horizontal="20" width="1/1" el_position="first last"]
			<h2 style="text-align: center;">SPRING / SUMMER 14
			COLLECTION.</h2>
			[hr_bold]
			<p style="text-align: center;">[sf_button colour="transparent-dark" type="standard" size="standard" link="/shop-demo/shop/" target="_self" icon="" dropshadow="no" extraclass=""]VIEW COLLECTION[/sf_button]</p>
			[/spb_text_block] [spb_blank_spacer height="60px" width="1/1" el_position="first last"] [/spb_row]'
        );

        $prebuilt_templates["shop-two-about"] = array(
            'id'   => "shop-two-about",
            'name' => 'About (Shop Two)',
            'code' => '[spb_row wrap_type="full-width" row_bg_type="image" bg_image="11397" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="video-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="20" padding_horizontal="20" el_class="mb0 pb0" width="1/2" el_position="first last"]
			<h2>Our story so far...</h2>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis dignissim. Pellentesque pharetra velit eu velit elementum et convallis erat vulputate. Sed in nulla ut elit mollis posuere. Praesent a felis accumsan neque interdum molestie. Sed blandit convallis dignissim.
			
			Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis dignissim. Pellentesque pharetra velit eu velit elementum et convallis erat vulputate. Sed in nulla ut elit mollis posuere. Praesent a felis accumsan neque interdum molestie. Sed blandit convallis dignissim.
			
			[/spb_text_block] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#f7f7f7" bg_image="11397" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" responsive_vis="hidden-xs" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_boxed_content type="coloured" custom_bg_colour="#222222" custom_text_colour="#ffffff" padding_vertical="10" padding_horizontal="20" width="1/2" el_position="first"]
			<h2><span style="color: #ffffff;">Stay up to date</span></h2>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis dignissim.Pellentesque pharetra velit eu velit elementum et convallis erat vulputate. Sed in nulla ut elit mollis posuere.
			
			Praesent a felis accumsan neque interdum molestie. Sed blandit convallis dignissim. Praesent a felis accumsan neque interdum molestie. Sed blandit convallis dignissim.
			
			[contact-form-7 id="11403" title="Subscribe"]
			
			[/spb_boxed_content] [spb_text_block animation="none" animation_delay="0" padding_vertical="10" padding_horizontal="20" el_class="mb0 pb0" width="1/2" el_position="last"]
			<h2>Stockists</h2>
			[one_half]
			<h6>&amp;Pens Press</h6>
			Los Angeles
			<h6>Cameron Marks</h6>
			Santa Cruz
			<h6>Cord</h6>
			Portland
			<h6>Deus Ex Machina</h6>
			Los Angeles
			<h6>General Store</h6>
			Los Angeles
			
			[/one_half]
			[one_half_last]
			<h6>General Store</h6>
			San Fransisco
			<h6>Izzy Martin</h6>
			Albuquerque
			<h6>Milkmade</h6>
			Venice
			<h6>Mohawk General Store</h6>
			Los Angeles
			<h6>Ok LA</h6>
			West Hollywood
			
			[/one_half_last]
			
			[/spb_text_block] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#f7f7f7" bg_image="11397" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" responsive_vis="hidden-lg_hidden-md_hidden-sm" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_boxed_content type="coloured" custom_bg_colour="#222222" custom_text_colour="#ffffff" padding_vertical="10" padding_horizontal="20" width="1/1" el_position="first last"]
			<h2><span style="color: #ffffff;">Stay up to date</span></h2>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis dignissim.Pellentesque pharetra velit eu velit elementum et convallis erat vulputate. Sed in nulla ut elit mollis posuere.
			
			Praesent a felis accumsan neque interdum molestie. Sed blandit convallis dignissim. Praesent a felis accumsan neque interdum molestie. Sed blandit convallis dignissim.
			
			[contact-form-7 id="11403" title="Subscribe"]
			
			[/spb_boxed_content] [spb_text_block animation="none" animation_delay="0" padding_vertical="10" padding_horizontal="20" el_class="mb0 pb0" width="1/1" el_position="first last"]
			<h2>Stockists</h2>
			[one_half]
			<h6>&amp;Pens Press</h6>
			Los Angeles
			<h6>Cameron Marks</h6>
			Santa Cruz
			<h6>Cord</h6>
			Portland
			<h6>Deus Ex Machina</h6>
			Los Angeles
			<h6>General Store</h6>
			Los Angeles
			
			[/one_half]
			[one_half_last]
			<h6>General Store</h6>
			San Fransisco
			<h6>Izzy Martin</h6>
			Albuquerque
			<h6>Milkmade</h6>
			Venice
			<h6>Mohawk General Store</h6>
			Los Angeles
			<h6>Ok LA</h6>
			West Hollywood
			
			[/one_half_last]
			
			[/spb_text_block] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="image" row_bg_color="#8b61a3" bg_image="11404" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="video-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="10" padding_horizontal="0" width="1/1" el_position="first last"]
			<h2 style="text-align: center;"><span style="color: #ffffff;">Check out our new Spring Collection</span></h2>
			&nbsp;
			<p style="text-align: center;">[sf_button colour="white" type="standard" size="standard" link="/shop-demo-two/shop/" target="_self" icon="" dropshadow="no" extraclass=""]SHOP SPRING COLLECTION[/sf_button]</p>
			[/spb_text_block] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" row_bg_color="#ffd57b" bg_image="11397" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="scroll" parallax_image_speed="0.5" parallax_video_height="video-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_gmaps size="700" type="roadmap" zoom="15" saturation="mono-light" fullscreen="no" width="2/3" el_position="first"] [spb_tab title=""] [spb_map_pin address="Los Angeles" pin_latitude="34.0522342" pin_longitude="-118.2436849" pin_image="11401" width="1/1" el_position="first last"]
			
			This is a map pin. Click the edit button to change it.
			
			[/spb_map_pin] [/spb_tab] [/spb_gmaps] [spb_text_block animation="none" animation_delay="0" padding_vertical="5" padding_horizontal="20" el_class="mb0 pb0" width="1/3" el_position="last"]
			<h2 style="text-align: center;">Our store</h2>
			<p style="text-align: center;">Apple Computer Inc
			1 Infinite Loop
			Cupertino CA
			95014</p>
			
			<h6 style="text-align: center;">Opening hours</h6>
			<p style="text-align: center;">Monday to Friday: 10am to 6pm
			Saturday: 10 am to 4pm
			Sunday: Closed</p>
			[/spb_text_block] [/spb_row]'
        );

        $prebuilt_templates["shop-three-home"] = array(
            'id'   => "shop-three-home",
            'name' => 'Home (Shop Three)',
            'code' => '[spb_row wrap_type="full-width" row_bg_type="image" bg_image="11396" bg_type="cover" parallax_image_height="window-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" vertical_center="true" responsive_vis="hidden-xs_hidden-sm" row_id="row3" row_name="row3" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="5" width="1/1" el_position="first last"]
			<h3><span style="color: #000000;">NEW COLLECTION OUT</span></h3>
			<h1><span style="color: #333333;"><span style="color: #000000;">SPRING</span> <span style="color: #808080;">&amp;</span></span></h1>
			<h1><span style="color: #000000;">SUMMER 14</span></h1>
			&nbsp;
			
			[sf_button colour="black" type="standard" size="standard" link="#" target="_self" icon="" dropshadow="no" extraclass=""]See more[/sf_button]
			
			[/spb_text_block] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="image" bg_image="11405" bg_type="cover" parallax_image_height="window-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" vertical_center="true" responsive_vis="hidden-xs_hidden-sm" row_id="row1" row_name="row1" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="5" width="1/1" el_position="first last"]
			<h3><span style="color: #ffffff;">MILAN 2014</span></h3>
			<h1><span style="color: #ffffff;">NEW PRODUCTS</span></h1>
			&nbsp;
			
			[sf_button colour="white" type="standard" size="standard" link="#" target="_self" icon="" dropshadow="no" extraclass=""]See more[/sf_button]
			
			[/spb_text_block] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="image" bg_image="11404" bg_type="cover" parallax_image_height="window-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="yes" vertical_center="true" responsive_vis="hidden-xs_hidden-sm" row_id="row2" row_name="row2" row_el_class="no-shadow" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="5" width="1/1" el_position="first last"]
			<h3><span style="color: #333333;">NEW ARRIVALS</span></h3>
			<h1><span style="color: #333333;">BRAND</span></h1>
			<h1><span style="color: #333333;">NEW</span></h1>
			<h1><span style="color: #333333;">SHOES</span></h1>
			&nbsp;
			
			[sf_button colour="black" type="standard" size="standard" link="#" target="_self" icon="" dropshadow="no" extraclass=""]See more[/sf_button]
			
			[/spb_text_block] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" responsive_vis="hidden-lg_hidden-md" width="1/1" el_position="first last"] [spb_image image="11396" image_size="full" frame="noframe" intro_animation="none" animation_delay="200" fullwidth="yes" lightbox="no" link_target="_self" caption_pos="below" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="5" width="1/1" el_position="first last"]
			<h3 style="text-align: center;">NEW COLLECTION OUT</h3>
			<h2 style="text-align: center;">SPRING <span style="color: #808080;">&amp;</span> SUMMER 14</h2>
			<p style="text-align: center;">[sf_button colour="black" type="standard" size="standard" link="#" target="_self" icon="" dropshadow="no" extraclass=""]See more[/sf_button]</p>
			[/spb_text_block] [spb_image image="11405" image_size="full" frame="noframe" intro_animation="none" animation_delay="200" fullwidth="yes" lightbox="no" link_target="_self" caption_pos="below" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="5" width="1/1" el_position="first last"]
			<h3 style="text-align: center;">MILAN 2014</h3>
			<h2 style="text-align: center;">NEW PRODUCTS</h2>
			<p style="text-align: center;">[sf_button colour="black" type="standard" size="standard" link="#" target="_self" icon="" dropshadow="no" extraclass=""]See more[/sf_button]</p>
			[/spb_text_block] [spb_image image="11404" image_size="full" frame="noframe" intro_animation="none" animation_delay="200" fullwidth="yes" lightbox="no" link_target="_self" caption_pos="below" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="5" width="1/1" el_position="first last"]
			<h3 style="text-align: center;">NEW ARRIVALS</h3>
			<h2 style="text-align: center;">BRAND NEW SHOES</h2>
			<p style="text-align: center;">[sf_button colour="black" type="standard" size="standard" link="#" target="_self" icon="" dropshadow="no" extraclass=""]See more[/sf_button]</p>
			[/spb_text_block] [/spb_row] [spb_row wrap_type="full-width" row_bg_type="color" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="video-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="0" remove_element_spacing="no" vertical_center="true" row_id="row5" row_name="row5" width="1/1" el_position="first last"] [spb_blank_spacer height="60px" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="5" width="1/1" el_position="first last"]
			<h2 style="text-align: center;">Latest Arrivals.</h2>
			[hr_bold]
			
			[/spb_text_block] [/spb_row] [spb_products asset_type="latest-products" carousel="yes" fullwidth="yes" columns="5" item_count="10" button_enabled="no" width="1/1" el_position="first last"] [spb_blank_spacer height="60px" width="1/1" el_position="first last"]'
        );

        $prebuilt_templates["shop-three-about"] = array(
            'id'   => "shop-three-about",
            'name' => 'About (Shop Three)',
            'code' => '[spb_row wrap_type="full-width" row_bg_type="image" row_bg_color="#f7f7f7" bg_image="11426" bg_type="cover" parallax_image_height="window-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="video-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="30" remove_element_spacing="no" vertical_center="true" row_el_class="mb0 pb0 mt0 no-shadow" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="5" el_class="mb0 pb0" width="1/1" el_position="first last"]
			<h1><span style="color: #ffffff;">OUR STORY</span></h1>
			<h1><span style="color: #ffffff;">SO FAR...</span></h1>
			[/spb_text_block] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#fefefe" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="30" remove_element_spacing="no" vertical_center="true" responsive_vis="hidden-xs_hidden-sm" row_el_class="mb0 pb0 mt0" width="1/1" el_position="first last"] [spb_blank_spacer height="60px" responsive_vis="hidden-xs_hidden-sm" width="1/1" el_position="first last"] [spb_image image="11359" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" el_class="mb0 pb0" width="1/2" el_position="first"] [spb_text_block animation="none" animation_delay="0" padding_vertical="5" padding_horizontal="10" width="1/2" el_position="last"]
			<h2>OUR APPROACH...</h2>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis dignissim. Pellentesque pharetra velit eu velit elementum et convallis erat vulputate. Sed in nulla ut elit mollis posuere. Praesent a felis accumsan neque interdum molestie. Sed blandit convallis dignissim.
			
			[/spb_text_block] [spb_blank_spacer height="60px" responsive_vis="hidden-xs_hidden-sm" width="1/1" el_position="first last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#fefefe" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="30" remove_element_spacing="no" vertical_center="false" responsive_vis="hidden-lg_hidden-md" row_el_class="mb0 pb0 mt0" width="1/1" el_position="first last"] [spb_blank_spacer height="60px" responsive_vis="hidden-lg_hidden-md" width="1/1" el_position="first last"] [spb_image image="11359" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" el_class="mb0 pb0" width="1/2" el_position="first"] [spb_text_block animation="none" animation_delay="0" padding_vertical="5" padding_horizontal="10" width="1/2" el_position="last"]
			<h2>OUR APPROACH...</h2>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis dignissim. Pellentesque pharetra velit eu velit elementum et convallis erat vulputate. Sed in nulla ut elit mollis posuere. Praesent a felis accumsan neque interdum molestie. Sed blandit convallis dignissim.
			
			[/spb_text_block] [spb_blank_spacer height="60px" responsive_vis="hidden-lg_hidden-md" width="1/1" el_position="first last"] [/spb_row] [spb_swift_slider fullscreen="false" maxheight="1000" slidecount="3" category="about" loop="true" nav="true" pagination="true" fullwidth="yes" width="1/1" el_position="first last"] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#f7f7f7" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="30" remove_element_spacing="no" vertical_center="true" responsive_vis="hidden-xs_hidden-sm" row_el_class="mb0 pb0 mt0" width="1/1" el_position="first last"] [spb_blank_spacer height="60px" responsive_vis="hidden-xs_hidden-sm" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="10" el_class="mb0 pb0" width="1/2" el_position="first"]
			<h2>OUR PROMISE...</h2>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis dignissim. Pellentesque pharetra velit eu velit elementum et convallis erat vulputate. Sed in nulla ut elit mollis posuere. Praesent a felis accumsan neque interdum molestie. Sed blandit convallis dignissim.
			
			[/spb_text_block] [spb_image image="11316" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" el_class="mb0 pb0" width="1/2" el_position="last"] [/spb_row] [spb_row wrap_type="content-width" row_bg_type="color" row_bg_color="#f7f7f7" bg_type="cover" parallax_image_height="content-height" parallax_image_movement="fixed" parallax_image_speed="0.5" parallax_video_height="window-height" parallax_video_overlay="none" row_overlay_opacity="0" row_padding_vertical="0" row_margin_vertical="30" remove_element_spacing="no" vertical_center="false" responsive_vis="hidden-lg_hidden-md" row_el_class="mb0 pb0 mt0" width="1/1" el_position="first last"] [spb_blank_spacer height="60px" responsive_vis="hidden-lg_hidden-md" width="1/1" el_position="first last"] [spb_text_block animation="none" animation_delay="0" padding_vertical="0" padding_horizontal="10" el_class="mb0 pb0" width="1/2" el_position="first"]
			<h2>OUR PROMISE...</h2>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquam massa quis mauris sollicitudin commodo venenatis ligula commodo. Sed blandit convallis dignissim. Pellentesque pharetra velit eu velit elementum et convallis erat vulputate. Sed in nulla ut elit mollis posuere. Praesent a felis accumsan neque interdum molestie. Sed blandit convallis dignissim.
			
			[/spb_text_block] [spb_image image="11316" image_size="full" frame="noframe" intro_animation="fade-in" animation_delay="200" fullwidth="no" lightbox="no" link_target="_self" caption_pos="hover" el_class="mb0 pb0" width="1/2" el_position="last"] [/spb_row]'
        );

//		$prebuilt_templates["agency-two-home"] = array(
//			'id' => "agency-two-home",
//			'name' => 'Home (Agency Two)',
//			'code' => '[cool]'
//		);	

        // filter array
        $prebuilt_templates = apply_filters( 'spb_prebuilt_templates', $prebuilt_templates );

        // return array
        return $prebuilt_templates;

    }

?>