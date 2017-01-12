<?php
function  EWD_UFAQ_Submit_Question($success_message) {
	$Admin_Question_Notification = get_option("EWD_UFAQ_Admin_Question_Notification");

	$Post_Title = sanitize_text_field($_POST['Post_Title']);
	$Post_Body = sanitize_text_field($_POST['Post_Body']);
	$Post_Author = sanitize_text_field($_POST['Post_Author']);

	$post = array(
		'post_content' => $Post_Body,
		'post_title' => $Post_Title,
		'post_type' => 'ufaq',
		'post_status' => 'draft' //Can create an option for admin approval of reviews here
	);
	$post_id = wp_insert_post($post);
	if ($post_id == 0) {$user_message = __("FAQ was not created succesfully.", 'EWD_UFAQ'); return $user_message;}

	update_post_meta($post_id, "EWD_UFAQ_Post_Author", $Post_Author);

	if ($Admin_Question_Notification == "Yes") {
		EWD_UFAQ_Send_Admin_Notification_Email($post_id, $Post_Title, $Post_Body);
	}

	return $success_message;
}

function EWD_UFAQ_Send_Admin_Notification_Email($post_id, $Post_Title, $Post_Body) {
	$Admin_Email = get_option( 'admin_email' );

	$ReviewLink = site_url() . "/wp-admin/post.php?post=" . $post_id . "&action=edit";

	$Subject_Line = __("New Question Received", 'EWD_UFAQ');

	$Message_Body = __("Hello Admin,", 'EWD_UFAQ') . "<br/><br/>";
	$Message_Body .= __("You've received a new question titled", 'EWD_UFAQ') . " '" . $Post_Title . "'.<br/><br/>";
	if ($Post_Body != "") {
		$Message_Body .= __("The answer reads:<br>", 'EWD_UFAQ');
		$Message_Body .= $Post_Body . "<br><br><br>";
	}
	$Message_Body .= __("You can view the question in the admin area by going to the following link:<br>", 'EWD_UFAQ');
	$Message_Body .= "<a href='" . $ReviewLink . "' />" . __("See the review", 'EWD_UFAQ') . "</a><br/><br/>";
	$Message_Body .= __("Have a great day,", 'EWD_UFAQ') . "<br/><br/>";
	$Message_Body .= __("Ultimate FAQs Team");

	$headers = array('Content-Type: text/html; charset=UTF-8');
	$Mail_Success = wp_mail($Admin_Email, $Subject_Line, $Message_Body, $headers);
}

?>