            <div class="col-md-3">
                <label><?php _e("Report Type", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>:</label>
                <select name="bis_report_type" size="2" id="bis_report_type">
                    <option value="area" selected="selected"><?php _e("XY Line Chart", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></option>
                    <option value="bar"><?php _e("Bar Chart", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></option>
                    <option value="area-spline"><?php _e("Area Spline Chart", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></option>
                    <option value="area-step"><?php _e("Area Chart", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></option>
                </select>
            </div>
            <div class="col-md-3">
                <label><?php _e("Search by ", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>:</label>
                <select name="bis_re_generate" size="2" id="bis_re_generate">
                    <option value="current_month" selected="selected"><?php _e("Current Month", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></option>
                    <option value="date_range"><?php _e("Date Range", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" id="bis_re_from_date" disabled
                       name="bis_re_from_date" placeholder='<?php _e("Select from date", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
                       class="form-control">
            </div>
            <div class="col-md-2">
                <input type="text" id="bis_re_to_date" disabled
                       name="bis_re_to_date" placeholder='<?php _e("Select to date", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
                       class="form-control">
            </div>
            <div class="col-md-2">
                <label></label>
                <button class="btn btn-primary" type="submit">
                    <i class="glyphicon glyphicon-search bis-search-position"></i>
                    <?php _e("Generate Report", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                </button>
            </div>

 <script>
    jQuery(document).ready(function () {
        // Default only date
        tpicker = false;
        dformat = 'Y-m-d';
        dpicker = true;

        jQuery('#bis_report_type').multiselect('select', 'area');       
        jQuery('#bis_report_type').multiselect('deselect', 'bar');       
        jQuery('#bis_report_type').multiselect('deselect', 'area-spline');       
        jQuery('#bis_report_type').multiselect('deselect', 'area-step');       
        jQuery('#bis_re_generate').multiselect('select', 'current_month');       
        jQuery('#bis_re_generate').multiselect('deselect', 'date_range');       
        jQuery("#bis_re_from_date").attr("disabled", true);
        jQuery("#bis_re_to_date").attr("disabled", true); 
        jQuery("#bis_re_from_date").val(getMonthFirstDay());
        jQuery("#bis_re_to_date").val(getMonthLastDay());

        
        jQuery("#bis_re_generate").change(function() {
            if(this.value === 'date_range') {
                jQuery("#bis_re_from_date").removeAttr("disabled")
                jQuery("#bis_re_to_date").removeAttr("disabled")
                jQuery("#bis_re_from_date").val("");
                jQuery("#bis_re_to_date").val("");
            } else {
                jQuery("#bis_re_from_date").attr("disabled", true);
                jQuery("#bis_re_to_date").attr("disabled", true); 
                jQuery("#bis_re_from_date").val(getMonthFirstDay());
                jQuery("#bis_re_to_date").val(getMonthLastDay());
            }
        })

        jQuery("#bis_re_from_date").datetimepicker({
            timepicker: tpicker,
            format: dformat,
            datepicker: dpicker,
            closeOnDateSelect: true,
            mask: true
        });

        jQuery("#bis_re_to_date").datetimepicker({
            timepicker: tpicker,
            format: dformat,
            datepicker: dpicker,
            closeOnDateSelect: true,
            mask: true
        });
        
        jQuery("#bis_re_from_date").val(getMonthFirstDay());
        jQuery("#bis_re_to_date").val(getMonthLastDay());
        
        function getMonthLastDay() {
            
            var date = new Date();
            var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            lastDay = lastDay.getFullYear() + '-' + (lastDay.getMonth() + 1)+ '-' +(lastDay.getDate()) ;
            
            return lastDay;
        }
        
        function getMonthFirstDay() {
            var date = new Date();
      
            var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            firstday = firstDay.getFullYear()+ '-' + (firstDay.getMonth() + 1) + '-' +(firstDay.getDate());
            
            return firstday;
        }
    });
</script>    