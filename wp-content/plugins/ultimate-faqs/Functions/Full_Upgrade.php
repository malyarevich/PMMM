<?php 
function EWD_UFAQ_Upgrade_To_Full() {
	global $ewd_ufaq_message, $EWD_UFAQ_Full_Version;
	
	$Key = $_POST['Key'];
	
	if ($Key == "EWD Trial" and !get_option("EWD_UFAQ_Trial_Happening")) {
		$ewd_urp_message = array("Message_Type" => "Update", "Message" => __("Trial successfully started!", 'EWD_UFAQ'));

		update_option("EWD_UFAQ_Trial_Expiry_Time", time() + (7*24*60*60));
		update_option("EWD_UFAQ_Trial_Happening", "Yes");
		update_option("EWD_UFAQ_Full_Version", "Yes");
		$EWD_UFAQ_Full_Version = get_option("EWD_UFAQ_Full_Version");
	}
	elseif ($Key != "EWD Trial") {
		$opts = array('http'=>array('method'=>"GET"));
		$context = stream_context_create($opts);
		$Response = unserialize(file_get_contents("http://www.etoilewebdesign.com/UPCP-Key-Check/EWD_UFAQ_KeyCheck.php?Key=" . $Key . "&Site=" . get_bloginfo('wpurl'), false, $context));
		//echo "http://www.etoilewebdesign.com/UPCP-Key-Check/EWD_OTP_KeyCheck.php?Key=" . $Key . "&Site=" . get_bloginfo('wpurl');
		//$Response = file_get_contents("http://www.etoilewebdesign.com/UPCP-Key-Check/KeyCheck.php?Key=" . $Key);
		
		if ($Response['Message_Type'] == "Error") {
			  $ewd_ufaq_message = array("Message_Type" => "Error", "Message" => $Response['Message']);
		}
		else {
				$ewd_ufaq_message = array("Message_Type" => "Update", "Message" => $Response['Message']);
				update_option("EWD_UFAQ_Trial_Happening", "No");
				delete_option("EWD_UFAQ_Trial_Expiry_Time");
				update_option("EWD_UFAQ_Full_Version", "Yes");
				$EWD_UFAQ_Full_Version = get_option("EWD_UFAQ_Full_Version");
		}
	}
}

 ?>
