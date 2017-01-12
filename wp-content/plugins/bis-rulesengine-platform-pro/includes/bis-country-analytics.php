<?php

use bis\repf\util\BISAnalyticsUtil;

$analyticsEngineModel = new bis\repf\model\AnalyticsEngineModel();
$ctry_req_info = $analyticsEngineModel->get_requests_by_country
        (BISAnalyticsUtil::get_current_month_first_day(), BISAnalyticsUtil::get_current_month_last_day());

$countries_requests = $ctry_req_info['data'];

$json_data = json_encode($countries_requests);
?>
<div class="container-fluid">
    
    <div class="panel-body">
        <div class="container-fluid search-container" >
            <div class="row">
                <?php include 'bis-analytics-search-pannel.php'; ?>  
            </div>
        </div>    
          
        <div class="row">
                <div class="col-lg-4">
                    <div class="jumbotron">
                        <h4><?php _e("Requests by Country Chart", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h4>
                        <span>
                            <?php _e("This chart provides the requests made to the site from each country.
                    Mouseover on the chart get the percentage of requests from selected country.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                        </span>
                    </div>          
                </div>
            <div class="col-md-8">
            <?php 
                $no_data = "display:block";
                if (!empty($countries_requests)) {
                    $no_data = "display:none";
            ?>
                    <div id="donut_chart_country"></div>
            <?php
                }
            ?>
            <span id="no_data_found" style="<?php echo $no_data; ?>">
                <div class="text-center alert alert-warning" role="alert">
                    <h3><?php echo _e("No data found."); ?></h3>
                </div>                    
            </span>
            </div>
            
        </div>
    </div>
     
</div>    

<script>
    jQuery(document).ready(function () {

        
        var jsonData = <?php echo $json_data ?>;

        var countryArray = new Array();
        var count = 0;
        var countries = new Array();

        jsonData.forEach(function (e) {
            countryArray[count] = [e.country, e.requests]; 
            countries[count] = e.country;
            count++;
        });

        var countryAnalytics = c3.generate({
            bindto: '#donut_chart_country',
            data: {
                columns: countryArray,
                type: jQuery("#bis_report_type").val(),
            },
            size: {
                height: 640,
                width: 640
            },
            donut: {
                label: {
                    format: function (value, ratio) {
                        return d3.format('')(value);
                    }
                },
                title: "<?php _e("Requests by Country", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>",
                width: 150
            },
            pie: {
                label: {
                    format: function (value, ratio) {
                        return d3.format('')(value);
                    }
                },
                title: "<?php _e("Requests by Country", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>",
                width: 150
            },        
            zoom: {
                enabled: true
            }       
        });

        var options = {
            beforeSubmit: bis_validate_dates,
            success: showResponse,
            url: BISAjax.ajaxurl,
            data: {
                action: 'bis_generate_report',
                bis_report_id: 1,
                bis_report_type_id: 'bis_country_requests',
                bis_nonce: BISAjax.bis_rules_engine_nonce
            }
        };

        jQuery('#bisAnalyticsForm').ajaxForm(options);

        function showResponse(responseText, statusText, xhr, $form) {
            if (responseText["status"] === "success") {
                jQuery("#donut_chart_country").show();
                jQuery("#no_data_found").hide();
                var jsonData = responseText['data'];
                var countryArray = new Array();
                var count = 0;
                
                countryAnalytics.unload({
                    ids: countries
                });
                
                countries = new Array();
                
                jsonData.forEach(function (e) {
                    countryArray[count] = [e.country, e.requests];
                    countries[count] = e.country;
                    count++;
                });
                
                countryAnalytics.load({
                   columns: countryArray,
                   type: jQuery("#bis_report_type").val()
                });
                
            } else if (responseText["status"] === "success_with_no_data") {
                
                jQuery("#no_data_found").show();
                countryAnalytics.unload({
                    ids: countries
                });
                jQuery("#donut_chart_country").hide();

            }
        }
    });
</script>
