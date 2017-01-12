<?php

/* ######################################################################################

  Copyright (C) 2015 by Ritu.  All rights reserved.  This software
  is an unpublished work and trade secret of Ritu, and distributed only
  under restriction.  This software (or any part of it) may not be used,
  modified, reproduced, stored on a retrieval system, distributed, or
  transmitted without the express written consent of Ritu.  Violation of
  the provisions contained herein may result in severe civil and criminal
  penalties, and any violators will be prosecuted to the maximum extent
  possible under the law.  Further, by using this software you acknowledge and
  agree that if this software is modified by anyone such as you, a third party
  or Ritu, then Ritu will have no obligation to provide support or
  maintenance for this software.

  ##################################################################################### */

// Plugin version constant
define("BIS_RULES_ENGINE_VERSION_CONST", "BIS_RULES_ENGINE_VERSION");
define("BIS_RULES_ENGINE_PLATFORM_DIR", "BIS_RULES_ENGINE_PLATFORM_DIR");
define("BIS_GEO_NAME_USER", "BIS_GEO_NAME_USER");
define("BIS_RULES_ENGINE_ALLOWABLE_TAGS_CONST", "BIS_RULES_ENGINE_ALLOWABLE_TAGS");
define("BIS_RULES_ENGINE_ALLOWABLE_TAGS", "<video><audio><h1><h2><h3><h4><h5><h6><p><i><b><a><ul><li><blockquote><hr><img><span><strong><br>");
define("BIS_RULES_ENGINE_DELETE_DB", "BIS_RULES_ENGINE_DELETE_DB");
define("BIS_RULES_ENGINE_CACHE_INSTALLED", "BIS_RULES_ENGINE_CACHE_INSTALLED");
define("BIS_REDIRECT_META_TEMPLATE", "BIS_REDIRECT_META_TEMPLATE");
define("BIS_RULES_ENGINE_PLUGIN_FORCE_DELETE", "BIS_RULES_ENGINE_PLUGIN_FORCE_DELETE");
define("BIS_RULES_ENGINE_SITE", "http://rulesengine.in/");

define("BIS_RULES_CRITERIA_ROWS_COUNT", 10);
define("BIS_RULES_ARRAY", "bis_rules_array");
define("BIS_REQUEST_RULES_ARRAY", "bis_request_rules_array");
define("BIS_REQUEST_RULES", "bis_request_rules_");
define("BIS_EXCLUDE_PAGES", "bis_exclude_page");
define("BIS_APPEND_TO_PAGES", "bis_append_to_page");
define("BIS_APPEND_TO_POSTS", "bis_append_to_posts");
define("BIS_EXCLUDE_POSTS", "bis_exclude_posts");
define("BIS_EXCLUDE_WIDGETS", "bis_exclude_widgets");
define("BIS_EXCLUDE_CATEGORIES", "bis_exclude_categories");
define("BIS_REDIRECT_RULES", "bis_redirect_rules");
define("BIS_LOAD_RULE_THEME", "bis_load_rule_theme");
define("BIS_DEFAULT_THEME_DETAILS", "bis_default_theme_details");

define("BIS_REDIRECT_RULES_ARRAY", "bis_redirect_rules_array");
define("BIS_REDIRECT_RULES_CACHE_KEY", "bis_redirect_rules_cache_key");

// DB Transaction constants
define("BIS_DB_START_TRANSACTION", "start transaction");
define("BIS_DB_COMMIT", "commit");
define("BIS_DB_ROLLBACK", "rollback");

// DB Error codes.
define("BIS_DUPLICATE_ENTRY", "duplicate_entry");
define("BIS_DUPLICATE_ENTRY_SQL_MESSAGE", "Duplicate entry");
define("BIS_GENERIC_DATABASE_ERROR", "generic_database_error");

// Message constants
define("BIS_MESSAGE_LOGICAL_RULE_DELETE_FAILED", "Delete failed, Please remove child rules before removing parent rules.");
define("BIS_MESSAGE_NO_RECORD_FOUND", "Logical rules not found");
define("BIS_MESSAGE_PAGE_RECORD_DELETE", "Error occurred while deleting page rules");


// Response results constants
define("BIS_STATUS", "status");
define("BIS_DATA", "data");
define("BIS_GEOLOCATION_DATA", "geo_data");
define("BIS_GEOLOCATION_WRAPPER", "geo_plugin_wrapper");

define("BIS_POPUP_DATA", "popup_data");
define("BIS_SUCCESS", "success");
define("BIS_MESSAGE_KEY", "message_key");
define("BIS_SUCCESS_WITH_NO_DATA", "success_with_no_data");
define("BIS_ERROR", "error");
define("BIS_INVALID_DATABASE_FILE", "bis_invalid_database_file");
define("BIS_NO_RECORDS_FOUND", "no_records_found");
define("BIS_NO_METHOD_FOUND", "no_method_found");
define("BIS_NO_SHORTCODE_FOUND", "no_shortcode_found");
define("BIS_INVALID_SHORTCODE", "invalid_shortcode");


// Rule type constants
define("BIS_PAGE_TYPE_RULE", 1);
define("BIS_REDIRECT_TYPE_RULE", 2);
define("BIS_POST_TYPE_RULE", 3);
define("BIS_WIDGET_TYPE_RULE", 4);
define("BIS_THEME_TYPE_RULE", 5);
define("BIS_CATEGORY_TYPE_RULE", 6);
define("BIS_LANGUAGE_TYPE_RULE", 7);
define("BIS_WOO_PRODUCT_TYPE_RULE", 8);
define("BIS_POPUP_TYPE_RULE", 9);
define("BIS_WOO_POPUP_TYPE_RULE", 10);
define("BIS_RULE_TYPE_CONST", "bis_rule_type_const_");
define("BIS_RULE_CRITERIA_SESSION_CONST", "bis_rule_criteria_session__const_");


// Rule eval constants.
define("BIS_EVAL_SESSION_TYPE", 1);
define("BIS_EVAL_REQUEST_TYPE", 2);

// Values Type constants
define("BIS_PAGE_TYPE_VALUE", 1);
define("BIS_POST_TYPE_VALUE", 2);
define("BIS_WIDGET_TYPE_VALUE", 3);
define("BIS_SIDEBAR_TYPE_VALUE", 4);

// Dynamic expression for request rules
define("BIS_RULE_CATEGORY_EXPRESSION_APPEND", "XCG$");
define("BIS_RULE_PAGE_EXPRESSION_APPEND", "XPG$");
define("BIS_RULE_PARAM_EXPRESSION_APPEND", "XPARAM$");
define("BIS_RULE_FORM_DATA_EXPRESSION_APPEND", "XFORMDATA$");
define("BIS_RULE_STATUS_EXPRESSION_APPEND", "XSTATUS$");
define("BIS_RULE_POST_EXPRESSION_APPEND", "XPT$");
define("BIS_RULE_REFERRAL_PATH_EXPRESSION_APPEND", "XRP$");
define("BIS_RULE_REFERRED_PATH_EXPRESSION_APPEND", "XRFDP$");
define("BIS_RULE_COOKIE_EXPRESSION_APPEND", "XCOOKIE$");

define("BIS_EXCLUDE_REQUEST_RULE_PAGES", "bis_exclude_request_rule_pages");
define("BIS_EXCLUDE_REQUEST_RULE_POSTS", "bis_exclude_request_rule_posts");

// Search constants
define("BIS_SEARCH_BY", "bis_re_search_by");
define("BIS_SEARCH_STATUS", "bis_re_status");


define("BIS_PAGE_RULE_EVALUATED", "bis_page_rule_evaluated_");
define("BIS_STATUS_EVALUATED", "bis_status_evaluated_");
define("BIS_CATEGORY_RULE_EVALUATED", "bis_category_rule_evaluated_");
define("BIS_EXCLUDE_CATEGORY_IDS", "bis_exclude_category_ids");
define("BIS_PAGE_ACTION_ID", 1000);
define("BIS_PAGE_CONTENT_POSITION", 1001);
define("BIS_CONTENT_IMAGE_SIZE", 1002);

define("BIS_POST_ACTION_ID", 1003);
define("BIS_POST_CONTENT_POSITION", 1004);

// Constant for text domain
define("BIS_RULES_ENGINE_TEXT_DOMAIN", "rulesengine");
define("BIS_GEOLOCATION_VO", "bis_geolocation_vo");

define("BIS_LOGICAL_RULE_RESET", "bis_logical_rule_reset");
define("BIS_LOGICAL_RULE_INIT_TIME", "bis_logical_rule_init_time");
define("BIS_PAGE_RULE_RESET", "bis_page_rule_reset");
define("BIS_POST_RULE_RESET", "bis_post_rule_reset");
define("BIS_APPLY_LANGUAGE", "bis_apply_language");

// Constants for dashboard.

// Language
define("BIS_PLATFORM_LANGUAGE_PLUGIN_ID", "bis_language_plugin");
define("BIS_PLATFORM_LANGUAGE_API_KEY", "cbxdwidktyirelyz7yoigxotsg7dmqqw");
define("BIS_PLATFORM_LANGUAGE_PLUGIN_DISPLAY_NAME", "Language Rules");
define("BIS_PLATFORM_LANGUAGE_CSS_CLASS", "language-rule-icon");
define("BIS_PLATFORM_LANGUAGE_PLUGIN_PATH", "admin.php?page=languagerules");
define("BIS_PLATFORM_LANGUAGE_PLUGIN_ABSPATH", "bis-language-switcher-addon/language-rules-index.php");
define("BIS_PLATFORM_LANGUAGE_PLUGIN_DESCRIPTION", "Define rules for switching languages.");

// Pages
define("BIS_PLATFORM_PAGE_PLUGIN_ID", "bis_page_plugin");
define("BIS_PLATFORM_PAGE_API_KEY", "0cjbnb0tud6yqrd99oe7vp58dbgb0hj9");
define("BIS_PLATFORM_PAGE_PLUGIN_DISPLAY_NAME", "Page Rules");
define("BIS_PLATFORM_PAGE_CSS_CLASS", "page-rule-icon");
define("BIS_PLATFORM_PAGE_PLUGIN_PATH", "admin.php?page=pagerules");
define("BIS_PLATFORM_PAGE_PLUGIN_ABSPATH", "bis-language-switcher-addon/language-rules-index.php");
define("BIS_PLATFORM_PAGE_PLUGIN_DESCRIPTION", "Define rules for hide, show, append, replace pages.");

// Posts
define("BIS_PLATFORM_POST_PLUGIN_ID", "bis_posts_plugin");
define("BIS_PLATFORM_POST_API_KEY", "cs5nm273hbj6v9uyk2j1i2pd32el4tbr");
define("BIS_PLATFORM_POST_PLUGIN_DISPLAY_NAME", "Posts Rules");
define("BIS_PLATFORM_POST_CSS_CLASS", "post-rule-icon");
define("BIS_PLATFORM_POST_PLUGIN_PATH", "admin.php?page=postrules");
define("BIS_PLATFORM_POST_PLUGIN_ABSPATH", "bis-language-switcher-addon/language-rules-index.php");
define("BIS_PLATFORM_POST_PLUGIN_DESCRIPTION", "Define rules for hide, show, append, replace posts.");

// Categories
define("BIS_PLATFORM_CATEGORY_PLUGIN_ID", "bis_category_plugin");
define("BIS_PLATFORM_CATEGORY_API_KEY", "u7bg58n38p4jz4qlq674f9wfkx4p9fb5");
define("BIS_PLATFORM_CATEGORY_PLUGIN_DISPLAY_NAME", "Category Rules");
define("BIS_PLATFORM_CATEGORY_CSS_CLASS", "category-rule-icon");
define("BIS_PLATFORM_CATEGORY_PLUGIN_PATH", "admin.php?page=categoryrules");
define("BIS_PLATFORM_CATEGORY_PLUGIN_ABSPATH", "bis-language-switcher-addon/language-rules-index.php");
define("BIS_PLATFORM_CATEGORY_PLUGIN_DESCRIPTION", "Define rules for show or hide categories.");

// Widgets
define("BIS_PLATFORM_WIDGET_PLUGIN_ID", "bis_widget_plugin");
define("BIS_PLATFORM_WIDGET_API_KEY", "4vh9fgil1sfoz58zizzdtx72gq24ftk9");
define("BIS_PLATFORM_WIDGET_PLUGIN_DISPLAY_NAME", "Widget Rules");
define("BIS_PLATFORM_WIDGET_CSS_CLASS", "widget-rule-icon");
define("BIS_PLATFORM_WIDGET_PLUGIN_PATH", "admin.php?page=widgetrules");
define("BIS_PLATFORM_WIDGET_PLUGIN_ABSPATH", "bis-language-switcher-addon/language-rules-index.php");
define("BIS_PLATFORM_WIDGET_PLUGIN_DESCRIPTION", "Define rules for show or hide widgets.");

// Themes
define("BIS_PLATFORM_THEME_PLUGIN_ID", "bis_theme_plugin");
define("BIS_PLATFORM_THEME_API_KEY", "o5uk6m5ahwxwz4o11bt0d2z7zhe1gq48");
define("BIS_PLATFORM_THEME_PLUGIN_DISPLAY_NAME", "Theme Rules");
define("BIS_PLATFORM_THEME_CSS_CLASS", "theme-rule-icon");
define("BIS_PLATFORM_THEME_PLUGIN_PATH", "admin.php?page=themerules");
define("BIS_PLATFORM_THEME_PLUGIN_ABSPATH", "bis-language-switcher-addon/language-rules-index.php");
define("BIS_PLATFORM_THEME_PLUGIN_DESCRIPTION", "Define rules for switching themes.");

// Redirect
define("BIS_PLATFORM_REDIRECT_PLUGIN_ID", "bis_redirect_plugin");
define("BIS_PLATFORM_REDIRECT_API_KEY", "g1kugxr2385sf9z5bjsblhc8z9gcfqej");
define("BIS_PLATFORM_REDIRECT_PLUGIN_DISPLAY_NAME", "Redirect Rules");
define("BIS_PLATFORM_REDIRECT_CSS_CLASS", "redirect-rule-icon");
define("BIS_PLATFORM_REDIRECT_PLUGIN_PATH", "admin.php?page=redirectrules");
define("BIS_PLATFORM_REDIRECT_PLUGIN_ABSPATH", "bis-language-switcher-addon/language-rules-index.php");
define("BIS_PLATFORM_REDIRECT_PLUGIN_DESCRIPTION", "Define rules for url redirection.");

define("BIS_RULES_ENGINE", "http://rulesengine.in/");

// Const for no redirect condition
define("BIS_NO_REDIRECT", "bis_nrd");
define("BIS_AUDIT_INFO", "bis_audit_vo");

define("RULES_ENGINE_MAIL", "rules4wp@gmail.com");

define("BIS_PUR_CODE", "BIS_PUR_CODE_");
define("BIS_SESSION_INITIATED", "initiated");
define("BIS_SESSION_RULEVO", "bis_session_rulevo");
define("BIS_CHILD_FILE_PATH", "bis_redirect_child_rule_path");
define("BIS_CHILD_RULE_ID", "bis_child_rule_id");

define("BIS_REPORT_CURRENT_MONTH", "current_month");
define("BIS_CAPTURE_ANALYTICS_DATA", "BIS_CAPTURE_ANALYTICS_DATA");
define("BIS_COUNTRY_SELECT", "bis_country");
define("BIS_COOKIE_EXPIRE_TIME", 2592000); // 30 DAYS
define("BIS_COOKIE_ONE_DAY_EXPIRE_TIME", 86400); // 1 DAYS
define("BIS_GEO_DETAILS", "BIS_GEO_DETAILS"); 
define("BIS_RESET_RULE_PARAM", "bis_reset_rule");
define("TRANSLATOR_DROPDOWN_COOKIE_KEY", "translator-dropdown-translator-dropdown-jquery-to");


define("BIS_LOGICAL_RULE_ID", "bis_logical_rule_id");

//1 = MaxMind DB 2= Geolocation plugin
define("BIS_GEO_LOOKUP_TYPE", "BIS_GEO_LOOKUP_TYPE");
define("BIS_GEO_LOOKUP_WEBSERVICE_TYPE", "BIS_GEO_LOOKUP_WEBSERVICE_TYPE");
define("BIS_GEO_MAXMIND_DB_FILE", "BIS_GEO_MAXMIND_DB_FILE");
define("BIS_GEO_MAXMIND_DB_FILE_NAME", "GeoLite2-Country.mmdb");
define("BIS_SOFT_EXCLUDE_CATEGORY_IDS", "bis_soft_exclude_category_ids");
define("BIS_USER_COUNTRY_DROPDOWN", "bis_user_country_dropdown");

define("BIS_REDIRECT_COOKIE", "bis_red_cookie");
define("BIS_REDIRECT_COOKIE_REDIRECT", "redirect");
define("BIS_REDIRECT_COOKIE_CANCEL", "cancel");
define("BIS_UPLOAD_DIRECTORY", "bis_rulesengine_uploads");
