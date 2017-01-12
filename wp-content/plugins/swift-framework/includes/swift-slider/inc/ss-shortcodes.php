<?php
    /*
    *
    *	Swift Slider Caption Shortcodes
    *	------------------------------------------------
    *	Swift Slider
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    //Adding Swift Slider shortcodes button
    function add_ss_shortcodes_button() {

        // display button matching new UI
        echo '<a href="#TB_inline?width=480&inlineId=ss-shortcodes" class="thickbox button shortcode_btn_link" id="ss_add_button" title="' . __( "Add Button", 'swift-framework-plugin' ) . '">' . __( "Add Button", 'swift-framework-plugin' ) . '</a>';
    }

    add_action( 'media_buttons', 'add_ss_shortcodes_button', 20 );

    //Action target that displays the popup to insert a form to a post/page
    function add_ss_mce_popup() {
        ?>
        <script>
            function ss_add_button() {

                var button_size = jQuery( "#button-size" ).val();
                var button_type = jQuery( "#button-type" ).val();
                var button_colour = jQuery( "#button-colour" ).val();
                var button_text = jQuery( "#button-text" ).val();
                var button_url = jQuery( "#button-url" ).val();
                var button_target = jQuery( "#button-target" ).is( ":checked" ) ? "_blank" : "_self";
                var button_dropshadow = jQuery( "#button-dropshadow" ).is( ":checked" ) ? "yes" : "no";

                window.send_to_editor( '[sf_button colour="' + button_colour + '" type="' + button_type + '" size="' + button_size + '" link="' + button_url + '" target="' + button_target + '" dropshadow="' + button_dropshadow + '" extraclass=""]' + button_text + '[/sf_button]' );
            }
        </script>

        <div id="ss-shortcodes" style="display:none;">
            <div class="wrap">
                <div>
                    <div style="padding:0 15px;">
                        <h2 style="margin-bottom: 10px;"><?php _e( "Insert Button", 'swift-framework-plugin' ); ?></h2>
                        <span>
                            <?php _e( "Select the various button options below, then press add button to add a button to the caption", 'swift-framework-plugin' ); ?>
                        </span>
                    </div>
                    <div class="option" style="padding: 15px 15px 0;">
                        <label for="button-size"
                               style="width: 160px;display: inline-block;font-weight: bold;font-size: 14px;"><?php _e( 'Button size', 'swift-framework-plugin' ); ?></label>
                        <select id="button-size" name="button-size" style="width: 200px;">
                            <option value="standard"><?php _e( 'Standard', 'swift-framework-plugin' ); ?></option>
                            <option value="large"><?php _e( 'Large', 'swift-framework-plugin' ); ?></option>
                        </select>
                    </div>
                    <div class="option" style="padding: 15px 15px 0;">
                        <label for="button-colour"
                               style="width: 160px;display: inline-block;font-weight: bold;font-size: 14px;"><?php _e( 'Button colour', 'swift-framework-plugin' ); ?></label>
                        <select id="button-colour" name="button-colour" style="width: 200px;">
                            <option value="accent"><?php _e( 'Accent', 'swift-framework-plugin' ); ?></option>
                            <option value="black"><?php _e( 'Black', 'swift-framework-plugin' ); ?></option>
                            <option value="white"><?php _e( 'White', 'swift-framework-plugin' ); ?></option>
                            <option value="blue"><?php _e( 'Blue', 'swift-framework-plugin' ); ?></option>
                            <option value="grey"><?php _e( 'Grey', 'swift-framework-plugin' ); ?></option>
                            <option value="lightgrey"><?php _e( 'Light Grey', 'swift-framework-plugin' ); ?></option>
                            <option value="orange"><?php _e( 'Orange', 'swift-framework-plugin' ); ?></option>
                            <option value="turquoise"><?php _e( 'Turquoise', 'swift-framework-plugin' ); ?></option>
                            <option value="green"><?php _e( 'Green', 'swift-framework-plugin' ); ?></option>
                            <option value="pink"><?php _e( 'Pink', 'swift-framework-plugin' ); ?></option>
                            <option value="gold"><?php _e( 'Gold', 'swift-framework-plugin' ); ?></option>
                            <option
                                value="transparent-light"><?php _e( 'Transparent - Light', 'swift-framework-plugin' ); ?></option>
                            <option
                                value="transparent-dark"><?php _e( 'Transparent - Dark', 'swift-framework-plugin' ); ?></option>
                        </select>
                    </div>
                    <div class="option" style="padding: 15px 15px 0;">
                        <label for="button-type"
                               style="width: 160px;display: inline-block;font-weight: bold;font-size: 14px;"><?php _e( 'Button type', 'swift-framework-plugin' ); ?></label>
                        <select id="button-type" name="button-type">
                            <option value="standard"><?php _e( 'Standard', 'swift-framework-plugin' ); ?></option>
                            <option
                                value="stroke-to-fill"><?php _e( 'Stroke To Fill', 'swift-framework-plugin' ); ?></option>
                        </select>
                    </div>
                    <div class="option" style="padding: 15px 15px 0;">
                        <label for="button-dropshadow" class="for-checkbox"
                               style="width: 160px;display: inline-block;font-weight: bold;font-size: 14px;"><?php _e( 'Button drop shadow', 'swift-framework-plugin' ); ?></label>
                        <input id="button-dropshadow" class="checkbox" name="button-dropshadow" type="checkbox"
                               style="margin-top:2px;"/>
                    </div>
                    <div class="option" style="padding: 15px 15px 0;">
                        <label for="button-text"
                               style="width: 160px;display: inline-block;font-weight: bold;font-size: 14px;"><?php _e( 'Button text', 'swift-framework-plugin' ); ?></label>
                        <input id="button-text" name="button-text" type="text"
                               value="<?php _e( 'Button text', 'swift-framework-plugin' ); ?>"/>
                    </div>
                    <div class="option" style="padding: 15px 15px 0;">
                        <label for="button-url"
                               style="width: 160px;display: inline-block;font-weight: bold;font-size: 14px;"><?php _e( 'Button URL', 'swift-framework-plugin' ); ?></label>
                        <input id="button-url" name="button-url" type="text" value="http://"/>
                    </div>
                    <div class="option" style="padding: 15px 15px 0;">
                        <label for="button-target" class="for-checkbox"
                               style="width: 210px;display: inline-block;font-weight: bold;font-size: 14px;"><?php _e( 'Open link in a new window?', 'swift-framework-plugin' ); ?></label>
                        <input id="button-target" class="checkbox" name="button-target" type="checkbox"
                               style="margin-top:2px;"/>
                    </div>
                    <div style="padding:15px;">
                        <input type="button" class="button-primary"
                               value="<?php _e( "Add Button", 'swift-framework-plugin' ); ?>"
                               onclick="ss_add_button();"/>&nbsp;&nbsp;&nbsp;
                        <a class="button" style="color:#bbb;" href="#"
                           onclick="tb_remove(); return false;"><?php _e( "Cancel", 'swift-framework-plugin' ); ?></a>
                    </div>
                </div>
            </div>
        </div>

    <?php
    }

    if ( in_array( basename( $_SERVER['PHP_SELF'] ), array( 'post.php', 'post-new.php' ) ) ) {
        add_action( 'admin_footer', 'add_ss_mce_popup' );
    }
?>