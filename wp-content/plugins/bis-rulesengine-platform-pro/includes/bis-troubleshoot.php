<?php
$geoLocation = new bis\repf\util\GeoPluginWrapper();
?>
<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php _e("Geolocation", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
        </div>
        <div class="panel-body">
            <?php

            $clientIP = $geoLocation->getIPAddress();
            if ($clientIP === null || '::1' === $clientIP || $clientIP === "" || $clientIP === "127.0.0.1") {
            ?>  
            <div class="alert alert-danger" role="alert"><?php _e("Unable to resolve client IP Address, contact your hosting service provider.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></div>
            <?php } else { ?>
                <div class="alert alert-success alert-dismissible" role="alert">
                        <?php _e("Resolved client IP Address", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></div>
            <?php } ?>
            <div class="row form-group">
                <label  class="control-label col-md-2" for="description">
                    <?php _e("IP Address", BIS_RULES_ENGINE_TEXT_DOMAIN); ?> :
                </label>

                <label class="col-md-10">
                    <?php echo $clientIP; ?>
                </label> 
            </div>

            <div class="row form-group">
                <label  class="control-label col-md-2" for="description">
                    <?php _e("Country", BIS_RULES_ENGINE_TEXT_DOMAIN); ?> :
                </label>
                <label class="col-md-10">
                    <?php echo $geoLocation->getCountryName(); ?>
                </label> 
            </div>
            
            <div class="row form-group">
                <label  class="control-label col-md-2" for="description">
                    <?php _e("Region", BIS_RULES_ENGINE_TEXT_DOMAIN); ?> :
                </label>
                <label class="col-md-10">
                    <?php echo $geoLocation->getRegion(); ?>
                </label> 
            </div>

            <div class="row form-group">
                <label  class="control-label col-md-2" for="description">
                    <?php _e("City", BIS_RULES_ENGINE_TEXT_DOMAIN); ?> :
                </label>
                <label class="col-md-10">
                    <?php 
                    
                    if("Other" === $geoLocation->getCity()) { ?>
                    <span style="font-size: 15px;" class="label label-warning">
                            <?php _e("Cities database is not configured", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                    </span>
                    <?php } else {
                        echo $geoLocation->getCity();
                    }
                    
                    ?>
                </label> 
            </div>
            <div class="row form-group">
                <label  class="control-label col-md-2" for="description">
                    <?php _e("Geolocation API", BIS_RULES_ENGINE_TEXT_DOMAIN); ?> :
                </label>

                <label class="col-md-10">
                    <?php 
                    if (RulesEngineUtil::get_option(BIS_GEO_LOOKUP_TYPE) == 1) {
                        echo "MaxMind local database";
                        if (RulesEngineUtil::get_option(BIS_GEO_LOOKUP_WEBSERVICE_TYPE) == 1) {
                            echo " & ";
                        }
                    } 
                    if (RulesEngineUtil::get_option(BIS_GEO_LOOKUP_WEBSERVICE_TYPE) == 1) {
                        echo "Geo plugin webservice";
                    }?>
                </label> 
            </div>
            <div class="row form-group">
                <label  class="control-label col-md-2" for="description">
                    <?php _e("Client IP Look up", BIS_RULES_ENGINE_TEXT_DOMAIN); ?> :
                </label>

                <label class="col-md-10">
                    <?php echo $geoLocation->getIPLookUp(); ?>
                </label> 
            </div>
        </div>
    </div>
</div>