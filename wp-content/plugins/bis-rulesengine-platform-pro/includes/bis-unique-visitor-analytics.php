<?php
$month_start = strtotime('first day of this month', time());
$month_end = strtotime('last day of this month', time());

$month_start = date('Y-m-d', $month_start);
$month_end = date('Y-m-d', $month_end);

$analyticsEngineModel = new bis\repf\model\AnalyticsEngineModel();

$rows_unique_visitors = $analyticsEngineModel->get_unique_visitors(null, $month_start, $month_end);
$unique_views = $rows_unique_visitors['data'];
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="well">
                <h4><?php _e("Unique visitors", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h4>
                <span>
                    <?php
                    _e("This chart provides the unique visitors on daily basis. Mouse over to view tooltip."
                            . " Zoom by mouse wheel event and slide by drag.", BIS_RULES_ENGINE_TEXT_DOMAIN);
                    ?>
                </span>
            </div>          
        </div>
    </div>
    <div class="panel-body">
        <div class="container-fluid search-container" >
            <div class="row">
                <?php include 'bis-analytics-search-report-type-pannel.php'; ?>  
            </div>  
            <div class="row">
                <div class="col-md-12">&nbsp;</div>
            </div>    
            <div class="row">
                <div class="col-md-12">
                    <label for="selectPages"><?php _e("Select Pages", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>:</label>

                    <select multiple="multiple" class="form-control"
                            name="bis_re_uniqueview_pages[]" id="bis_re_uniqueview_pages" size="2">
                                <?php
                                $pages = get_pages();
                                foreach ($pages as $page) {
                                    ?>
                            <option value="<?php echo $page->ID ?>"
                                    id="<?php echo $page->ID ?>"><?php echo $page->post_title ?></option>
<?php } ?>
                    </select>

                </div>
            </div>    
        </div>        
    </div>     
    <div class="row">        
        <div class="col-lg-12">
            <?php 
            $no_data = "display:block";
            if (!empty($unique_views)) { 
                $no_data = "display:none";
                ?>
                <div id="page_views"></div>
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


<script>

    jQuery(document).ready(function () {

        var dateArray = new Array();
        dateArray.push('pdate');

        var sampleArray = new Array();
        sampleArray.push('sample');

<?php
if (!empty($unique_views)) {


    foreach ($unique_views as $key => $uniqueview) {
        ?>
                dateArray.push('<?php echo $uniqueview->pdate ?>');
                sampleArray.push('<?php echo $uniqueview->sample ?>');
        <?php
    }
}
?>
        jQuery('#bis_re_uniqueview_pages').multiselect({
            includeSelectAllOption: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 300,
            selectedClass: null,
            nonSelectedText: '<?php _e("Select Pages", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>',
            buttonWidth: '300px'
        });

        var pageViewChart = c3.generate({
            bindto: '#page_views',
            point: {
                r: 3
            },
            data: {
                x: 'pdate',
                xFormat: '%Y%m%d',
                type: jQuery("#bis_report_type").val(),
                columns: [
                    dateArray,
                    sampleArray
                ],
                names:
                        {
                            pdate: '<?php _e("Date", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>', sample: '<?php _e("Unique Visitors", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
                        }
            },
            axis: {
                x: {
                    label: '<?php _e("Date", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>',
                    type: 'timeseries',
                    tick: {
                        format: "%e %b %y",
                    }
                },
                y: {
                    label: '<?php _e("Unique Visitors", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
                }
            },
            zoom: {
                enabled: true
            },
            grid: {
                x: {
                    show: true
                },
                y: {
                    show: true
                }
            }
        });


        var options = {
            beforeSubmit: bis_validate_dates,
            success: showResponse,
            url: BISAjax.ajaxurl,
            data: {
                action: 'bis_generate_report',
                bis_report_id: 4,
                bis_report_type_id: 'bis_unique_visitors',
                bis_nonce: BISAjax.bis_rules_engine_nonce
            }
        };

        jQuery('#bisAnalyticsForm').ajaxForm(options);

        function showResponse(responseText, statusText, xhr, $form) {
            if (responseText["status"] === "success") {
                
                jQuery("#page_views").show();
                jQuery("#no_data_found").hide();

                var data = responseText['data'];
                sampleArray = [];
                sampleArray.push('sample');

                dateArray = [];
                dateArray.push('pdate');

                for (i = 0; i < data.length; i++) {
                    dateArray.push(data[i].pdate);
                    sampleArray.push(data[i].sample);
                }


                pageViewChart.load({
                    type: jQuery("#bis_report_type").val(),
                    json: data,
                    keys: {
                        value: ['pdate', 'sample'],
                    }
                });

            } else if (responseText["status"] === "success_with_no_data") {

                jQuery("#page_views").hide();
                jQuery("#no_data_found").show();
                
                var data = responseText['data'];
                sampleArray = [];
                sampleArray.push('sample');

                dateArray = [];
                dateArray.push('pdate');

                pageViewChart.unload({
                    ids: ['sample', 'pdate']
                })

            }
        }

    });
</script>
