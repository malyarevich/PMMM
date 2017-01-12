<div id="bis_addlogicalrule">
    <form id="bis-addlogicalruleform" method="post" name="bis-addlogicalruleform">
        <?php
        require_once 'logical-rules-add-body.php';
        ?>
        <div class="row">
            <div class="col-md-10">
                <a href="javascript:void(0)" id="bis_back_rules_list" class="btn btn-primary">
                    <i class="glyphicon glyphicon-chevron-left bis-glyphi-position"></i><?php _e("Go Back", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></a>
            </div>
            <div class="col-md-2" align="right">
                <button type="submit" class="btn btn-primary">
                    <i class="glyphicon glyphicon-ok-sign bis-glyphi-position"></i><?php _e(" Save Rule", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                </button>
            </div>
        </div>
    </form>
</div>