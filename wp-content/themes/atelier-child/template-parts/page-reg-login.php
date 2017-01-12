<div class="container">
    <div class="row">
        <?php if ( post_password_required() ){?>
            <div class="col-md-12">
                <div class="page-password">
                    <?php echo get_the_password_form();?>
                </div>

            </div>
        <?php }else{ ?>
            <div class="col-md-6">
            <div class="gf_registration">
                <?php
                    echo do_shortcode('[spb_gravityforms grav_form="11" show_title="true" show_desc="false" ajax="false" width="1/1" el_position="first last"]');
                ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="gf_login">
                <?php
                    echo do_shortcode('[spb_gravityforms grav_form="12" show_title="true" show_desc="false" ajax="false" width="1/1" el_position="first last"]'); ?>
                </div>
            </div>
        <?php }?>
    </div>
</div>

