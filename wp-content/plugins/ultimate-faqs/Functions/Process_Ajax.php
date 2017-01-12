<?php
/* Processes the ajax requests being put out in the admin area and the front-end
*  of the UPCP plugin */

// Returns the FAQs that are found for a specific search
function UFAQ_Search() {
    $Path = ABSPATH . 'wp-load.php';
    include_once($Path);

    $response = do_shortcode("[ultimate-faqs search_string='" . strtolower($_POST['Q']) . "' include_category='" . $_POST['include_category'] . "' exclude_category='" . $_POST['exclude_category'] . "' orderby='" . $_POST['orderby'] . "' order='" . $_POST['order'] . "' post_count='" . $_POST['post_count'] . "' ajax='Yes']");

    $ReturnArray['request_count'] = $_POST['request_count'];
    $ReturnArray['message'] = $response;
    echo json_encode($ReturnArray);
}
add_action('wp_ajax_ufaq_search', 'UFAQ_Search');
add_action( 'wp_ajax_nopriv_ufaq_search', 'UFAQ_Search');

// Change the up and down votes for a particular FAQ
function EWD_UFAQ_Update_Rating() {
    /*$Path = ABSPATH . 'wp-load.php';
    include_once($Path);*/

    $FAQ_ID = $_POST['FAQ_ID'];
    $Vote_Type = $_POST['Vote_Type'];

    if ($Vote_Type == "Up") {
        $Up_Votes = get_post_meta($FAQ_ID, "FAQ_Up_Votes", true);
        update_post_meta($FAQ_ID, "FAQ_Up_Votes", $Up_Votes + 1);
        $Total_Score = get_post_meta($FAQ_ID, "FAQ_Total_Score", true);
        update_post_meta($FAQ_ID, "FAQ_Total_Score", $Total_Score + 1);
    }
    if ($Vote_Type == "Down") {
        $Down_Votes = get_post_meta($FAQ_ID, "FAQ_Down_Votes", true);
        update_post_meta($FAQ_ID, "FAQ_Down_Votes", $Down_Votes + 1);
        $Total_Score = get_post_meta($FAQ_ID, "FAQ_Total_Score", true);
        update_post_meta($FAQ_ID, "FAQ_Total_Score", $Total_Score - 1);
    }

    die();
}
add_action('wp_ajax_ufaq_update_rating', 'EWD_UFAQ_Update_Rating');
add_action( 'wp_ajax_nopriv_ufaq_update_rating', 'EWD_UFAQ_Update_Rating');

// Records the number of time an FAQ post is opened
function UFAQ_Record_View() {
    $Path = ABSPATH . 'wp-load.php';
    include_once($Path);

    global $wpdb;
    $wpdb->show_errors();
    $post_id = substr($_POST['post_id'], 4, strrpos($_POST['post_id'], "-") - 4);
    $Meta_ID = $wpdb->get_var($wpdb->prepare("SELECT meta_id FROM $wpdb->postmeta WHERE post_id=%d AND meta_key='ufaq_view_count'", $post_id));
    if ($Meta_ID != "" and $Meta_ID != 0) {$wpdb->query($wpdb->prepare("UPDATE $wpdb->postmeta SET meta_value=meta_value+1 WHERE post_id=%d AND meta_key='ufaq_view_count'", $post_id));}
    else {$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->postmeta (post_id,meta_key,meta_value) VALUES (%d,'ufaq_view_count','1')", $post_id));}

}
add_action('wp_ajax_ufaq_record_view', 'UFAQ_Record_View');
add_action( 'wp_ajax_nopriv_ufaq_record_view', 'UFAQ_Record_View');

function EWD_UFAQ_Save_Order(){   
    foreach ($_POST['ewd-ufaq-item'] as $Key=>$ID) {
        update_post_meta($ID, 'ufaq_order', $Key);
    }
    
}
add_action('wp_ajax_UFAQ_update_order','EWD_UFAQ_Save_Order');
