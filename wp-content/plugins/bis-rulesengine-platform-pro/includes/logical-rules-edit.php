<?php

$logical_rule_engine_modal = new bis\repf\model\LogicalRulesEngineModel();

$ruleId = $_GET ['ruleId'];

// Get the rule details.
$bis_rule = $logical_rule_engine_modal->get_rule($ruleId);

// Ge the rule details
$rule = $bis_rule["rule"];

// Get the criteria array from map
$rule_criteria_array = $bis_rule["rule_criteria"];

?>

<div id="bis_editlogicalrule">
    <form id="bis-editlogicalruleform" method="post" name="bis-editlogicalruleform">
        <?php 
            require_once 'logical-rules-edit-body.php';
        ?>
        
        <div class="row">
            <div class="col-md-10">
                <button type="button" id="bis_re_show_logical_rules" class="btn btn-primary">
                    <i class="glyphicon glyphicon-chevron-left bis-glyphi-position"></i><?php _e("Go Back", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                </button>
            </div>
            <div class="col-md-2" align="right">
                <button type="submit" class="btn btn-primary">
                    <i class="glyphicon glyphicon-ok-sign bis-glyphi-position"></i><?php _e("Update Rule", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                </button>   
            </div>
        </div>
    </form>
</div>