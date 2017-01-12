<span id="redirect_rule_tab"> 
    <span id="redirect_rules_list_content">
        <form name="redirectRulesListForm" id="redirectRulesListForm" method="POST">
            <!-- Search Panel -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-2">
                                <h3 class="panel-title"><label><?php _e("Redirect Rules", "redirectrules"); ?></label></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="container-fluid search-container">
                        <div>
                            <div class="pull-left">
                                <label><?php _e("Search by ", "redirectrules"); ?>:</label>
                                <select name="bis_re_redirect_search_by" size="2" id="bis_re_redirect_search_by"
                                        class="bis-multiselect">
                                    <option value="name" selected="selected"><?php _e("Name", "redirectrules"); ?></option>
                                    <option value="description"><?php _e("Description", "redirectrules"); ?></option>
                                </select>
                            </div>
                            <div class="col-md-3 pull-left">
                                <input type="text" id="bis_re_redirect_search_value"
                                       name="bis_re_redirect_search_value" placeholder='<?php _e("Enter search criteria", "redirectrules"); ?>'
                                       class="form-control">
                            </div>
                            <div class="pull-left">
                                <label><?php _e("Status", "redirectrules"); ?> : </label>
                                <select name="bis_re_redirect_search_status" size="2" id="bis_re_redirect_search_status"
                                        class="bis-multiselect">
                                    <option selected="selected" value="all"><?php _e("All", "redirectrules"); ?></option>
                                    <option value="1"><?php _e("Active", "redirectrules"); ?></option>
                                    <option value="0"><?php _e("Deactive", "redirectrules"); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2 pull-left">
                                <button class="form-control btn btn-primary" type="submit">
                                    <i class="glyphicon glyphicon-search bis-search-position"></i><?php _e("Search Rules", "redirectrules"); ?>
                                </button>
                            </div>
                            <div class="pull-right">
                                <button id="bis_re_show_add_redirect_rule" type="button" class="btn btn-primary">
                                    <i class="glyphicon glyphicon-plus bis-glyphi-position"></i><?php _e("Redirect Rule", "redirectrules"); ?>
                                </button>
                            </div> 
                        </div>
                    </div>

                    <div class="bis-table-scroll">
                        <!-- Message span -->
                        <div id="bis-redirect-no-result" class="bis-no-results-message"><h4></h4></div>

                        <table id="bis-redirect-rules-table" style="display: none"
                               class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="25%"><?php _e("Name", "redirectrules"); ?></th>
                                    <th width="25%"><?php _e("Redirect Path", "redirectrules"); ?></th>
                                    <th width="10%"><?php _e("Logical Rule Name", "redirectrules"); ?></th>
                                    <th width="20%"><?php _e("Description", "redirectrules"); ?></th>
                                    <th width="10%"><?php _e("Status", "redirectrules"); ?></th>
                                </tr>
                            </thead>
                            <tbody id="bis-redirectRulesListContent"></tbody>

                        </table>
                    </div>
                </div>
            </div>

            <div class="pull-right">
                <label class="bis-rows-count"><?php _e("Number of Rules", "redirectrules"); ?></label><span class="badge bis-rows-no">0</span>
            </div>
        </form>
    </span> 
    <input type="hidden" id="bis_child_list_callback" name="bis_child_list_callback" value="bis_showRedirectRulesList" />
    <span id="redirect_rules_child_content"> </span>	
    <span id="redirect_rules_parent_content"> </span>	
</span>
<script>

    // wait for the DOM to be loaded
    jQuery(document).ready(function () {

        bis_setRedirectSearchButtonWidth();
        bis_redirectFieldsReset();

        var options = {
            success: bis_showRedirectResponse,
            url: BISAjax.ajaxurl,
            data: {
                action: 'bis_re_redirect_search_rule',
                bis_nonce: BISAjax.bis_rules_engine_nonce
            }
        };

        jQuery('#redirectRulesListForm').ajaxForm(options);

        jQuery("#bis_re_show_add_redirect_rule").click(function () {
            jQuery.post(
                    BISAjax.ajaxurl,
                    {
                        action: 'bis_re_show_add_redirect_rule',
                        bis_nonce: BISAjax.bis_rules_engine_nonce
                    },
                    function (response) {
                        jQuery("#redirect_rules_child_content").html(response);
                        jQuery("#redirect_rules_list_content").hide();
                        jQuery("#redirect_rules_child_content").show();
                        bis_setRedirectSearchButtonWidth();
                        bis_redirectFieldsReset();
                    });

        });

    });
</script>
