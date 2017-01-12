<?php

namespace bis\repf\util;

class BISAnalyticsUtil {

    private function __construct() {
        
    }

    public static function get_current_month_first_day() {
        $month_start = strtotime('first day of this month', time());
        return date('Y-m-d', $month_start);
    }

    public static function get_current_month_last_day() {
        $month_end = strtotime('last day of this month', time());
        return date('Y-m-d', $month_end);
    }

}
