<div class = "panel panel-warning">
    <div class = "panel-heading">
        <h3 class = "panel-title"><?php _e("Verify Purchase Code", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
    </div>
    <div class = "panel-body">
        <?php
        $ac_plugin = "admin.php?page=bis_pg_settings&stab=true";

        _e("Plugin purchase code is not verified.", BIS_RULES_ENGINE_TEXT_DOMAIN);

        if(is_multisite()) { 
            $ac_plugin = network_site_url()."wp-admin/network/admin.php?page=bis_pg_settings&stab=true";
        } ?>
        <a href = "<?php echo $ac_plugin ?>"><?php _e("Click here to verify purchase code", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></a>

    </div>
</div>