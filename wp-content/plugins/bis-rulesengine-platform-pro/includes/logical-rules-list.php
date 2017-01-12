	<span id="logical_rules_list_content">
		<form name="logicalRuleForm" id="logicalRuleForm" method="POST">

            <!-- Search Panel -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-2">
                                <h3 class="panel-title"><label><?php _e("Logical Rules", BIS_RULES_ENGINE_TEXT_DOMAIN);?></label></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="container-fluid search-container" >
                        <div>
                            <div class="pull-left">
                                <label><?php _e("Search by ", BIS_RULES_ENGINE_TEXT_DOMAIN);?>:</label>
                                <select name="bis_re_search_by" size="2" id="bis_re_search_by">
                                    <option value="name" selected="selected"><?php _e("Name", BIS_RULES_ENGINE_TEXT_DOMAIN);?></option>
                                    <option value="description"><?php _e("Description", BIS_RULES_ENGINE_TEXT_DOMAIN);?></option>
                                </select>
                            </div>
                            <div class="col-md-3 pull-left">
                                <input type="text" id="bis_re_search_value"
                                       name="bis_re_search_value" placeholder='<?php _e("Enter search criteria", BIS_RULES_ENGINE_TEXT_DOMAIN);?>'
                                       class="form-control">
                            </div>
                            <div class="pull-left">
                                <label><?php _e("Status", BIS_RULES_ENGINE_TEXT_DOMAIN);?>:</label>
                                <select name="bis_re_search_status" size="2" id="bis_re_search_status">
                                    <option selected="selected" value="all"><?php _e("All", BIS_RULES_ENGINE_TEXT_DOMAIN);?></option>
                                    <option value="1"><?php _e("Active", BIS_RULES_ENGINE_TEXT_DOMAIN);?></option>
                                    <option value="0"><?php _e("Deactive", BIS_RULES_ENGINE_TEXT_DOMAIN);?></option>
                                </select>
                            </div>
                            <div class="col-md-2 pull-left">
                                <button class="btn btn-primary" type="submit">
                                    <i class="glyphicon glyphicon-search bis-search-position"></i>
                                    <?php _e("Search Rules", BIS_RULES_ENGINE_TEXT_DOMAIN);?>
                                </button>
                            </div>
                            <div class="pull-right">
                                <button id="bis_addlogicalrule" type="button" class="btn btn-primary">
                                    <i class="glyphicon glyphicon-plus bis-glyphi-position"></i>
                                    <?php _e("Logical Rule", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                                </button>
                            </div>
                        </div>
                       
                    </div>

                    <!-- End of Search Panel -->
                    <!-- Start of Results Panel -->
                    <div class="bis-table-scroll">
                        <!-- Message span -->
                        <div id="bis-logical-no-result" class="bis-no-results-message"><h4></h4></div>

                        <table id="bis-logical-rules-table" style="display: none"
                               class="table table-hover table-bordered table-striped">
                            <thead>
                            <tr>
                                <th width="40%"><?php _e("Name", BIS_RULES_ENGINE_TEXT_DOMAIN);?></th>
                                <th><?php _e("Description", BIS_RULES_ENGINE_TEXT_DOMAIN);?></th>
                                <th width="10%"><?php _e("Status", BIS_RULES_ENGINE_TEXT_DOMAIN);?></th>
                            </tr>
                            </thead>

                            <tbody id="bis-logicalRulesContent"></tbody>

                        </table>
                    </div>
                    <!-- End of Results Pannel -->
                </div>

            </div>
            <div class="pull-right">
                <label class="bis-rows-count"><?php _e("Number of Rules", BIS_RULES_ENGINE_TEXT_DOMAIN);?> </label><span class="badge bis-rows-no">0</span>
            </div>    
        </form>

	</span>
	<span id="logical_rules_child_content"> </span>

<script>
    jQuery(document).ready(function () {

        bis_setLogicalSearchButtonWidth();
        bis_logicalResetFields();
        
        var options = {
            success: showResponse,
            url: BISAjax.ajaxurl,

            data: {
                action: 'bis_re_search_rule',
                bis_nonce: BISAjax.bis_rules_engine_nonce
            }
        };

        jQuery('#logicalRuleForm').ajaxForm(options);

        jQuery("#bis_addlogicalrule").click(function () {

            var jqXHR = jQuery.get(ajaxurl,
                {
                    action: "bis_re_new_rule",
                    bis_nonce: BISAjax.bis_rules_engine_nonce
                });

            jqXHR.done(function (data) {
                jQuery("#logical_rules_child_content").html(data);
                jQuery("#logical_rules_list_content").hide();
                jQuery("#logical_rules_child_content").show();
            });

        });


    });
</script>