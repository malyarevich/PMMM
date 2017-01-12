<?php
/*
 * Plugin Name: Custom Referral Spam Blocker
 * Plugin URI: http://jacobbaron.net/
 * Description: This plugin blocks referral spam bots which are screwing up your Google Analytics data.
 * Version: 1.4.5
 * Author: csmicfool
 * Author URI: http://jacobbaron.net
 * License: GPLv2+
 * Text Domain: custom-referral-spam-blocker
 * Min WP Version: 2.5.0
 * Max WP Version: 4.7
 */
 
$crsb_db_version = "1.4.5";

function custom_referral_spam_block() {
	if(isset($_SERVER['HTTP_REFERER'])) {
		$referrer = $_SERVER["HTTP_REFERER"];
		$referrer_host = parse_url($referrer,PHP_URL_HOST);
		
		$mysite = get_site_url();
		$myhost = parse_url($mysite,PHP_URL_HOST);
		
		$spammers = explode("\n",get_option( 'crsb_spammers_list' ));
		if(is_multisite()){
			$spammers = explode("\n",get_site_option( 'crsb_spammers_list' ));
		}
		if(count($spammers)<2){
			$spammers = crsb_return_file_array();
		}
		
	}
	else
	{
		return true;
	}

	if(function_exists("idn_to_ascii")){
		foreach ($spammers as $spammer) { 
			$spammer = idn_to_ascii(mb_strtolower(trim($spammer)));
			$referrer_host = idn_to_ascii(mb_strtolower(trim($referrer_host)));
			if (($spammer == $referrer_host) && ($referrer_host != $myhost)) { 
				header("Location: $referrer"); exit(); 
			} 
		}
	}
	else{
		require_once('idna-convert/idna_convert.class.php');
		$IDN = new idna_convert();
		foreach ($spammers as $spammer) { 
			$spammer = $IDN->decode(mb_strtolower(trim($spammer)));
			$referrer_host = $IDN->decode(mb_strtolower(trim($referrer_host)));
			if (($spammer == $referrer_host) && ($referrer_host != $myhost)) { 
				header("Location: $referrer"); exit(); 
			} 
		}
	}
}

add_action('init','custom_referral_spam_block');




//Admin sections

if(is_multisite()){
	add_action( 'network_admin_menu', 'crsb_add_network_admin_menu' );
}
else{
	add_action( 'admin_menu', 'crsb_add_admin_menu' );
}

add_action( 'admin_init', 'crsb_settings_init' );


function crsb_add_admin_menu(  ) { 

	add_options_page( 'Custom Referral Spam Blocker', 'Custom Referral Spam Blocker', 'manage_options', 'custom_referral_spam_blocker', 'crsb_options_page' );
	
}

function crsb_add_network_admin_menu(  ) { 

	add_menu_page( 'Custom Referral Spam Blocker', 'Custom Referral Spam Blocker', 'manage_options', 'custom_referral_spam_blocker', 'crsb_options_page_networkadmin' );
	
}



function crsb_settings_init(  ) { 

	register_setting( 'pluginPage', 'crsb_spammers_list' );
	register_setting( 'pluginPage', 'crsb_share_data' );
	register_setting( 'pluginPage', 'crsb_share_data_last' );

	add_settings_section(
		'crsb_pluginPage_section', 
		__( 'Spam Referrer List', 'custom-referral-spam-blocker' ), 
		'crsb_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'crsb_spammers_list', 
		__( 'Referrer List<br><br/><small><em>Add spam referrer domains<br/>one on each line.</em></small><br><br/><small><em>Set your custom list blank<br/>to restore default list.</em></small>', 'custom-referral-spam-blocker' ), 
		'crsb_spammers_list_render', 
		'pluginPage', 
		'crsb_pluginPage_section' 
	);
	
	add_settings_field(
		'crsb_share_data',
		__( '<span title="Enabling this feature will send anonymous data back to the plugin developer to help maintain rerferrer list. This feature is opt-in only and no personal data is collected nor stored.">Allow data sharing</span>','custom-referral-spam-blocker' ),
		'crsb_share_data_render',
		'pluginPage',
		'crsb_pluginPage_section'
	);


}

//increases timeout for curl operations known to take longer.  Optional usage
function curl_http_timeout_ex(){
	$timeout = 30;
	return $timeout;
}

function crsb_spammers_list_render(  ) { 

	$spammers = get_option('crsb_spammers_list');
	if(is_multisite()){
		$spammers = get_site_option('crsb_spammers_list');
	}
	$spammers = explode("\n",$spammers);
	$spammers_file = crsb_return_file();
	asort($spammers);
	$spammers_array = $spammers;
	$spammers = trim(implode("\n",array_unique($spammers)));
	$last_post = get_option('crsb_share_data_last');
	$share_data = get_option('crsb_share_data');
	if(is_multisite()){
		$last_post = get_site_option('crsb_share_data_last');
		$share_data = get_site_option('crsb_share_data');
	}
	$send_post = false;
	
	if($share_data&&((time()-$last_post)>(24*60*60))){
		$spammers_file_array = explode("\n",$spammers_file);
		$spammers_diff = array_diff($spammers_array,$spammers_file_array); //Improves performance for the data sharing feature to avoid hitting cURL execution limits
		$spammers_diff = trim(implode("\n", array_unique($spammers_diff)));
		$send_post=true;
	}
	if($send_post){
		add_filter('http_request_timeout','curl_http_timeout_ex'); //force longer timeout
		$response = wp_remote_post('http://spamblocker.jacobbaron.net/referrers.php', array(
			'method' => 'POST',
			'blocking' => true,
			'body' => array( 'refurl' => $spammers_diff )
			)
		);

		if ( is_wp_error( $response ) ) {
		   $error_message = $response->get_error_message();
		   echo "Something went wrong: $error_message";
		} 
		else{
			update_option('crsb_share_data_last',time());
			update_option('crsb_share_data_resp_raw', json_encode($response));
			if(is_multisite()){
				update_site_option('crsb_share_data_last',time());
				update_site_option('crsb_share_data_resp_raw', json_encode($response));
			}
		}
	}
	
	if((strlen($spammers)==0)){
		//import from flat file if blank
		$spammers = crsb_return_file();
	}
	?>
	<textarea cols='40' rows='25' name='crsb_spammers_list'><?php echo $spammers; ?></textarea>
	<?php

}


function crsb_share_data_render(  ) { 

	$share = get_option( 'crsb_share_data' );
	if(is_multisite()){
		$share = get_site_option('crsb_share_data');
	}
			
	?>
	<input type="checkbox" name="crsb_share_data" value="true" <?php if($share) echo "checked"; ?>/>
	<?php

}

add_action('network_admin_edit_csrb_network_admin_settings_post', 'save_network_settings_page');
function save_network_settings_page(){
	update_site_option(crsb_spammers_list,$_POST['crsb_spammers_list']);
	update_site_option(crsb_share_data,$_POST['crsb_share_data']);
	wp_redirect('admin.php?page=custom_referral_spam_blocker');
	exit();
}


function crsb_settings_section_callback(  ) { 

	echo __( 'Add referrers to the list to block them from being recorded in Google Analytics.', 'custom-referral-spam-blocker' );

}


function crsb_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h2>Custom Referral Spam Blocker</h2>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form>
	<?php

}


function crsb_options_page_networkadmin(  ) { 

	?>
	<form action='edit.php?action=csrb_network_admin_settings_post' method='post'>
		
		<h2>Custom Referral Spam Blocker</h2>
		<?php wp_nonce_field('csrb_nonce'); ?>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form>
	<?php

}


function crsb_return_file(  ) {
	
	$spammers = file_get_contents(plugin_dir_path(__FILE__).'spammers.txt');
	return $spammers;
	
} 

function crsb_return_file_array(  ) {
	
	$spamfile = crsb_return_file();
	$spammers = explode("\n",$spamfile);
	return $spammers;
	
}

function crsb_list_update() {
    global $crsb_db_version;
    if (get_site_option( 'crsb_db_version' ) != $crsb_db_version) {
        crsb_list_update_helper();
    }
}
add_action( 'plugins_loaded', 'crsb_list_update' );

function crsb_list_update_helper() {
    global $crsb_db_version;
	$spammers_file = crsb_return_file_array();
	$spammers = explode("\n",get_option( 'crsb_spammers_list' ));
	if(is_multisite()){
		$spammers = explode("\n",get_site_option( 'crsb_spammers_list' ));
	}
	if(count($spammers)<2){
		$spammers = $spammers_file;
	}
	
	$ts = $spammers;
	foreach($spammers_file as $pspam){
		$r=0;
		foreach($ts as $uspam){
			if(trim($pspam) == trim($uspam)){
				$r++;
			}
		}
		if($r==0){
			$spammers[]=$pspam;
		}
	}

	//ToDo = formal whitelist
	// - need to remove false positives such as t.co
	$whitelist = array('t.co');
	foreach ($whitelist as $wh) {
		foreach($spammers as $key=>$spammer){
			if(trim($spammer) == trim($wh)){
				unset($spammers[$key]);
			}
		}
	}
	
	asort($spammers);
	$spammers = implode("\n",array_unique($spammers));
	update_option('crsb_spammers_list',$spammers);
	update_option('crsb_db_version',$crsb_db_version);
	if(is_multisite()){
		update_site_option('crsb_spammers_list',$spammers);
		update_site_option('crsb_db_version', $crsb_db_version);
	}
}

function my_plugin_load_plugin_textdomain() {
    load_plugin_textdomain( 'custom-referral-spam-blocker', FALSE, basename( dirname( __FILE__ ) ) );
}
add_action( 'plugins_loaded', 'my_plugin_load_plugin_textdomain' );

?>