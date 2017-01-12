<?php
use bis\repf\util\BISAnalyticsUtil;

$analyticsEngineModel = new bis\repf\model\AnalyticsEngineModel();
$dev_req_info = $analyticsEngineModel->get_requests_by_device
        (BISAnalyticsUtil::get_current_month_first_day(), BISAnalyticsUtil::get_current_month_last_day());

$devices_requests = $dev_req_info['data'];

$json_data = json_encode($devices_requests);
?>
<div class="container-fluid">
    <div class="container-fluid search-container" >
        <div class="row">
            <?php include 'bis-analytics-search-pannel.php'; ?>  
        </div>
    </div>  
    <div class="row">
        <div class="col-lg-4">
            <div class="jumbotron">
                <h4><?php _e("Requests by Device Chart", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h4>
                <span>
                    <?php _e("This chart provides the requests made to the site from each device. Mouseover on the chart get the percentage of requests from selected device.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                    </span>
            </div>          
        </div>
        <div class="col-lg-8">
            <?php 
            $no_data = "display:block";
            if (!empty($devices_requests)) {
                $no_data =  "display:none";
                ?>
                <div id="donut_chart_device"></div>
            <?php }?>     
            
            <span id="no_data_found" style="<?php echo $no_data; ?>">
                <div class="text-center alert alert-warning" role="alert">
                    <h3><?php echo  _e("No data found."); ?></h3>
                </div>                    
            </span>
        </div>
    </div>
</div>  


<script>
 
    jQuery(document).ready(function () {

        var jsonData = <?php echo $json_data ?>;
        
        var deviceArray = new Array();
        var count = 0;
        var devices = new Array();

        jsonData.forEach(function (e) {
            deviceArray[count] = [e.device, e.count]; 
            devices[count] = e.device;
            count++;
        });

        var deviceAnalytics = c3.generate({
            bindto: '#donut_chart_device',
            data: {
                columns: deviceArray,
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
                title: "<?php _e("Requests by Device", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>",
                width: 150
            },
            pie: {
                label: {
                    format: function (value, ratio) {
                        return d3.format('')(value);
                    }
                },
                title: "<?php _e("Requests by Device", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>",
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
                    bis_report_type_id: 'bis_device_requests',
                    bis_nonce: BISAjax.bis_rules_engine_nonce
                }
            };

            jQuery('#bisAnalyticsForm').ajaxForm(options);

            function showResponse(responseText, statusText, xhr, $form) {
                if (responseText["status"] === "success") {
                    jQuery("#donut_chart_device").show();
                    jQuery("#no_data_found").hide();
                    var jsonData = responseText['data'];
                    var deviceArray = new Array();
                    var count = 0;

                    deviceAnalytics.unload({
                        ids: devices
                    });

                    devices = new Array();

                    jsonData.forEach(function (e) {
                        deviceArray[count] = [e.device, e.count]; 
                        devices[count] = e.device;
                        count++;
                    });

                    deviceAnalytics.load({
                        columns: deviceArray,
                        type: jQuery("#bis_report_type").val()
                    });

                } else if (responseText["status"] === "success_with_no_data") {

                    jQuery("#no_data_found").show();
                    deviceAnalytics.unload({
                        ids: devices
                    });
                    jQuery("#donut_chart_device").hide();

                }
            }
        });

</script>
