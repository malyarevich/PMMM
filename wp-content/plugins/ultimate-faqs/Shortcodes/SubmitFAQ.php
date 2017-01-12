<?php
/* The function that creates the HTML on the front-end, based on the parameters
* supplied in the customer-order shortcode */
function Insert_Question_Form($atts) {
	global $user_message;
	global $wpdb;
		
	$Custom_CSS = get_option('EWD_UFAQ_Custom_CSS');
	$Allow_Proposed_Answer = get_option("EWD_UFAQ_Allow_Proposed_Answer");
	
	$ReturnString = "";
		
	// Get the attributes passed by the shortcode, and store them in new variables for processing
	extract( shortcode_atts( array(
		 		'success_message' => __('Thank you for submitting an FAQ.', 'EWD_UFAQ'),
		 		'submit_faq_form_title' => __('Submit a Question', 'EWD_UFAQ'),
				'submit_faq_instructions' => __('Please fill out the form below to submit a question.', 'EWD_UFAQ'),
				'submit_text' => __('Send Question', 'EWD_UFAQ')),
		$atts
		)
	);
	if (get_option("EWD_UFAQ_Thank_You_Submit_Label") != "") {$success_message = get_option("EWD_UFAQ_Thank_You_Submit_Label");}
	if (get_option("EWD_UFAQ_Submit_Question_Label") != "") {$submit_faq_form_title = get_option("EWD_UFAQ_Submit_Question_Label");}
	if (get_option("EWD_UFAQ_Please_Fill_Form_Below_Label") != "") {$submit_faq_instructions = get_option("EWD_UFAQ_Please_Fill_Form_Below_Label");}
	if (get_option("EWD_UFAQ_Send_Question_Label") != "") {$submit_text = get_option("EWD_UFAQ_Send_Question_Label");}
	$Question_Title_Label = get_option("EWD_UFAQ_Question_Title_Label");
	if ($Question_Title_Label == "") {$Question_Title_Label = __("Question Title", 'EWD_UFAQ');}
	$What_Question_Being_Answered_Label = get_option("EWD_UFAQ_What_Question_Being_Answered_Label");
	if ($What_Question_Being_Answered_Label == "") {$What_Question_Being_Answered_Label = __("What question is being answered?", 'EWD_UFAQ');}
	$Proposed_Answer_Label = get_option("EWD_UFAQ_Proposed_Answer_Label");
	if ($Proposed_Answer_Label == "") {$Proposed_Answer_Label = __("Proposed Answer", 'EWD_UFAQ');}
	$Review_Author_Label = get_option("EWD_UFAQ_Review_Author_Label");
	if ($Review_Author_Label == "") {$Review_Author_Label = __("FAQ Author", 'EWD_UFAQ');}
	$What_Name_With_Review_Label = get_option("EWD_UFAQ_What_Name_With_Review_Label");
	if ($What_Name_With_Review_Label == "") {$What_Name_With_Review_Label = __("What name should be displayed with your FAQ?", 'EWD_UFAQ');}

	if (isset($_POST['Submit_Question'])) {$user_update = EWD_UFAQ_Submit_Question($success_message);}

	$ReturnString .= "<style type='text/css'>";
	$ReturnString .= $Custom_CSS;
	$ReturnString .= "</style>";
	$ReturnString .= EWD_UFAQ_Add_Modified_Styles();

	$ReturnString .= "<div class='ewd-ufaq-question-form'>";

	if (isset($_POST['Submit_Question'])) {
		$ReturnString .= "<div class='ewd-ufaq-question-update'>";
		$ReturnString .= $user_update;
		$ReturnString .= "</div>";
	}

	$ReturnString .= "<form id='question_form' method='post' action='#'>";
	$ReturnString .= wp_nonce_field();
	$ReturnString .= wp_referer_field();

	$ReturnString .= "<div class='form-field'>";
	$ReturnString .= "<div id='ewd-ufaq-review-title' class='ewd-ufaq-review-label'>";
	$ReturnString .= $Question_Title_Label . ": ";
	$ReturnString .= "</div>";
	$ReturnString .= "<div id='ewd-ufaq-review-author-input' class='ewd-ufaq-review-input'>";
	$ReturnString .= "<input type='text' name='Post_Title' id='Post_Title' />";
	$ReturnString .= "</div>";
	$ReturnString .= "<div id='ewd-ufaq-title-explanation' class='ewd-ufaq-review-explanation'>";
	$ReturnString .= "<p>" . $What_Question_Being_Answered_Label  . "</p>";
	$ReturnString .= "</div>";
	$ReturnString .= "</div>";

	if ($Allow_Proposed_Answer == "Yes") {
		$ReturnString .= "<div class='ewd-ufaq-meta-field'>";
		$ReturnString .= "<label for='Post_Body'>";
		$ReturnString .= $Proposed_Answer_Label . ": ";
		$ReturnString .= "</label>";
		$ReturnString .= "<textarea name='Post_Body'></textarea>";
		$ReturnString .= "</div>";
	}

	$ReturnString .= "<div class='form-field'>";
	$ReturnString .= "<div id='ewd-faq-review-author' class='ewd-faq-review-label'>";
	$ReturnString .= $Review_Author_Label . ": ";
	$ReturnString .= "</div>";
	$ReturnString .= "<div id='ewd-faq-review-author-input' class='ewd-faq-review-input'>";
	$ReturnString .= "<input type='text' name='Post_Author' id='Post_Author' />";
	$ReturnString .= "</div>";
	$ReturnString .= "<div id='ewd-faq-author-explanation' class='ewd-faq-review-explanation'>";
	$ReturnString .= "<p>" . $What_Name_With_Review_Label . "</p>";
	$ReturnString .= "</div>";
	$ReturnString .= "</div>";


	$ReturnString .= "<p class='submit'><input type='submit' name='Submit_Question' id='submit' class='button-primary' value='" . $submit_text . "'  /></p></form>";
	$ReturnString .= "</div>";

	return $ReturnString;
}
if ($UFAQ_Full_Version == "Yes") {add_shortcode("submit-question", "Insert_Question_Form");}

?>