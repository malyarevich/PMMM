<!-- Upgrade to pro link box -->
<!-- TOP BOX-->

<?php
	global $wpdb;
	$Slug_Base = get_option("EWD_UFAQ_Slug_Base");

	//start review box
	if(get_option('EWD_UFAQ_Hide_Dash_Review_Ask')){
		$hideReview = get_option('EWD_UFAQ_Hide_Dash_Review_Ask');
	}
	else {
		add_option('EWD_UFAQ_Hide_Dash_Review_Ask', 'No');
	}
	$hideReviewBox = $_POST["hide_ufaq_review_box_hidden"];
	if($hideReviewBox == 'Yes'){
		update_option('EWD_UFAQ_Hide_Dash_Review_Ask', 'Yes');
		header('Location: admin.php?page=EWD-UFAQ-Options');
	}
	//end review box
?>

<div id="fade" class="ewd-ufaq-dark_overlay"></div>

<div id="ewd-dashboard-top" class="metabox-holder">
<?php
if ($UFAQ_Full_Version != "Yes" or get_option("EWD_UFAQ_Trial_Happening") == "Yes") { ?>
<div id="side-sortables" class="metabox-holder ">
	<div id="upcp_pro" class="postbox " >
		<div class="handlediv" title="Click to toggle"></div><h3 class='hndle'><span><?php _e("Full Version", 'EWD_UFAQ') ?></span></h3>
		<div class="inside">
			<ul><li><a href="http://www.etoilewebdesign.com/plugins/ultimate-faqs/"><?php _e("Upgrade to the full version ", "EWD_UFAQ"); ?></a><?php _e("to take advantage of all the available features of Ultimate FAQs for Wordpress!", 'EWD_UFAQ'); ?></li>
				<?php if (get_option("EWD_UFAQ_Trial_Happening") == "Yes") { ?><li><strong>Your trial expires at <?php echo date("Y-m-d H:i:s", get_option("EWD_UFAQ_Trial_Expiry_Time")); ?> GMT</strong>, upgrade before then to retain any premium changes made!</li>
				<?php } elseif (!get_option("EWD_UFAQ_Trial_Happening")) { ?><li>Want to try out the features first? Use code "EWD Trial" for a 7 day trial!</li><?php } ?>
			</ul>
			<h3 class='hndle'><span><?php _e("What you get by upgrading:", 'EWD_UFAQ') ?></span></h3>
				<ul>
					<li>Ability to add a unique FAQ tab to each WooCommerce product page.</li>
					<li>Premium shortcodes to accept questions from users and insert an AJAX FAQ search.</li>
					<li>Additional FAQ style skins, dozens of styling and labeling options and much more!</li>
					<li>Access to e-mail support.</li>
				</ul>
			<div class="full-version-form-div">
				<form action="edit.php?post_type=ufaq" method="post">
					<div class="form-field form-required">
						<label for="Key"><?php _e("Product Key", 'EWD_UFAQ') ?></label>
						<input name="Key" type="text" value="" size="40" />
					</div>
					<input type="submit" name="Upgrade_To_Full" value="<?php _e('Upgrade', 'EWD_UFAQ') ?>">
				</form>
			</div>
		</div>
	</div>
	</div>
<?php } ?>

<?php
EWD_UFAQ_Get_EWD_Blog();
EWD_UFAQ_Get_Changelog();
	/*
if (get_option("EWD_UFAQ_Update_Flag") == "Yes" or get_option("EWD_UFAQ_Install_Flag") == "Yes") {?>
	<div id="side-sortables" class="metabox-holder ">
		<div id="EWD_UFAQ_pro" class="postbox " >
			<div class="handlediv" title="Click to toggle"></div>
			<h3 class='hndle'><span><?php _e("Thank You!", 'EWD_UFAQ') ?></span></h3>
		 	<div class="inside">
				<?php  if (get_option("EWD_UFAQ_Install_Flag") == "Yes") { ?><ul><li><?php _e("Thanks for installing the Ultimate FAQs plugin.", "EWD_UFAQ"); ?><br> <a href='https://www.youtube.com/channel/UCZPuaoetCJB1vZOmpnMxJNw'><?php _e("Subscribe to our YouTube channel ", "EWD_UFAQ"); ?></a> <?php _e("for tutorial videos on this and our other plugins!", "EWD_UFAQ");?> </li></ul>
				<?php } else { ?><ul><li><?php _e("Thanks for upgrading to version 1.3.1!", "EWD_UFAQ"); ?><br> <a href='https://wordpress.org/support/view/plugin-reviews/ultimate-faqs?filter=5'><?php _e("Please rate our plugin", "EWD_UFAQ"); ?></a> <?php _e("if you find Ultimate FAQs useful!", "EWD_UFAQ");?> </li></ul><?php } ?>

				<?php /* if (get_option("EWD_UFAQ_Install_Flag") == "Yes") { ?><ul><li><?php _e("Thanks for installing the Ultimate Product Catalogue Plugin.", "EWD_UFAQ"); ?><br> <a href='http://www.facebook.com/EtoileWebDesign'><?php _e("Follow us on Facebook", "EWD_UFAQ"); ?></a> <?php _e("to suggest new features or hear about upcoming ones!", "EWD_UFAQ");?> </li></ul>
				<?php } else { ?><ul><li><?php _e("Thanks for upgrading to version 2.2.9!", "EWD_UFAQ"); ?><br> <a href='http://www.facebook.com/EtoileWebDesign'><?php _e("Follow us on Facebook", "EWD_UFAQ"); ?></a> <?php _e("to suggest new features or hear about upcoming ones!", "EWD_UFAQ");?> </li></ul><?php } */ ?>

				<?php /* if (get_option("EWD_UFAQ_Install_Flag") == "Yes") { ?><ul><li><?php _e("Thanks for installing the Ultimate Product Catalogue Plugin.", "EWD_UFAQ"); ?><br> <a href='http://www.facebook.com/EtoileWebDesign'><?php _e("Follow us on Facebook", "EWD_UFAQ"); ?></a> <?php _e("to suggest new features or hear about upcoming ones!", "EWD_UFAQ");?>  </li></ul>
				<?php } else { ?><ul><li><?php _e("Thanks for upgrading to version 3.0.16!", "EWD_UFAQ"); ?><br> <a href='http://wordpress.org/support/view/plugin-reviews/ultimate-product-catalogue'><?php _e("Please rate our plugin", "EWD_UFAQ"); ?></a> <?php _e("if you find the Ultimate Product Catalogue Plugin useful!", "EWD_UFAQ");?> </li></ul><?php } */ ?>

				<?php /* if (get_option("EWD_UFAQ_Install_Flag") == "Yes") { ?><ul><li><?php _e("Thanks for installing the Ultimate Product Catalogue Plugin.", "EWD_UFAQ"); ?><br> <a href='http://www.facebook.com/EtoileWebDesign'><?php _e("Follow us on Facebook", "EWD_UFAQ"); ?></a> <?php _e("to suggest new features or hear about upcoming ones!", "EWD_UFAQ");?>  </li></ul>
				<?php } else { ?><ul><li><?php _e("Thanks for upgrading to version 3.4.8!", "EWD_UFAQ"); ?><br> <a href='http://wordpress.org/plugins/order-tracking/'><?php _e("Try out order tracking plugin ", "EWD_UFAQ"); ?></a> <?php _e("if you ship orders and find the Ultimate Product Catalogue Plugin useful!", "EWD_UFAQ");?> </li></ul><?php } */ ?>

				<?php /* if (get_option("EWD_UFAQ_Install_Flag") == "Yes") { ?><ul><li><?php _e("Thanks for installing the Ultimate Product Catalogue Plugin.", "EWD_UFAQ"); ?><br> <a href='http://www.facebook.com/EtoileWebDesign'><?php _e("Follow us on Facebook", "EWD_UFAQ"); ?></a> <?php _e("to suggest new features or hear about upcoming ones!", "EWD_UFAQ");?>  </li></ul>
				<?php } else { ?><ul><li><?php _e("Thanks for upgrading to version 2.3.9!", "EWD_UFAQ"); ?><br> <a href='http://wordpress.org/support/topic/error-hunt'><?php _e("Please let us know about any small display/functionality errors. ", "EWD_UFAQ"); ?></a> <?php _e("We've noticed a couple, and would like to eliminate as many as possible.", "EWD_UFAQ");?> </li></ul><?php } */ ?>

				<?php /* if (get_option("EWD_UFAQ_Install_Flag") == "Yes") { ?><ul><li><?php _e("Thanks for installing the Ultimate Product Catalogue Plugin.", "EWD_UFAQ"); ?><br> <a href='https://www.youtube.com/channel/UCZPuaoetCJB1vZOmpnMxJNw'><?php _e("Check out our YouTube channel ", "EWD_UFAQ"); ?></a> <?php _e("for tutorial videos on this and our other plugins!", "EWD_UFAQ");?> </li></ul>
				<?php } elseif ($Full_Version == "Yes") { ?><ul><li><?php _e("Thanks for upgrading to version 3.5.0!", "EWD_UFAQ"); ?><br> <a href='http://www.facebook.com/EtoileWebDesign'><?php _e("Follow us on Facebook", "EWD_UFAQ"); ?></a> <?php _e("to suggest new features or hear about upcoming ones!", "EWD_UFAQ");?> </li></ul>
				<?php } else { ?><ul><li><?php _e("Thanks for upgrading to version 3.4!", "EWD_UFAQ"); ?><br> <?php _e("Love the plugin but don't need the premium version? Help us speed up product support and development by donating. Thanks for using the plugin!", "EWD_UFAQ");?>
						<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="AQLMJFJ62GEFJ">
						<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
						</form>
						</li></ul>
				<?php }  ?>

			</div>
		</div>
	</div>
	<?php
	EWD_UFAQ_Get_EWD_Blog();
	EWD_UFAQ_Get_Changelog();
	update_option('EWD_UFAQ_Update_Flag', "No");
	update_option('EWD_UFAQ_Install_Flag', "No");
}*/
?>


	<div id="ewd-dashboard-box-orders" class="ewd-ufaq-dashboard-box" >
	  	<div class="ewd-dashboard-box-icon"><img src="<?php echo plugins_url(); ?>/ultimate-faqs/images/ewd-dashboard-icon-ufaq-01.png"/>
	  	</div>
		<div class="ewd-dashboard-box-value-and-field-container">
		  <div class="ewd-dashboard-box-value"><span class="displaying-num"><?php echo $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type='ufaq' AND post_status='publish'"); ?></span>
		  </div>
		  <div class="ewd-dashboard-box-field">FAQs
		  </div>
		</div>
	</div>
	<div id="ewd-dashboard-box-views" class="ewd-ufaq-dashboard-box" >
	  	<div class="ewd-dashboard-box-icon"><img src="<?php echo plugins_url(); ?>/ultimate-faqs/images/ewd-dashboard-icon-ufaq-02.png"/>
	  	</div>
		<div class="ewd-dashboard-box-value-and-field-container">
		  <div class="ewd-dashboard-box-value"><?php echo $wpdb->get_var("SELECT SUM(meta_value) FROM $wpdb->postmeta WHERE meta_key='ufaq_view_count' and post_id!='0'"); ?>
		  </div>
		  <div class="ewd-dashboard-box-field">Views
		  </div>
		</div>
	</div>
	<div id="ewd-dashboard-box-links" class="ewd-ufaq-dashboard-box" >
	  	<div class="ewd-dashboard-box-icon"><img src="<?php echo plugins_url(); ?>/ultimate-faqs/images/ewd-dashboard-icon-ufaq-03.png"/>
	  	</div>
		<div class="ewd-dashboard-box-value-and-field-container">
		  <div class="ewd-dashboard-box-value"><?php echo $wpdb->get_var("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key='ufaq_view_count' and post_id!='0' ORDER BY cast(meta_value as unsigned) DESC"); ?>
		  </div>
		  <div class="ewd-dashboard-box-field">Most FAQ Views
		  </div>
		</div>
	</div>

	<div id="ewd-dashboard-box-support" class="ewd-ufaq-dashboard-box" >
		<div class="ewd-dashboard-box-icon"><img src="<?php echo plugins_url(); ?>/ultimate-faqs/images/ufaq-buttonsicons-04.png"/>
	  	</div>
		<div class="ewd-dashboard-box-value-and-field-container">
		  	<div class="ewd-dashboard-box-support-value">
			<form id="form1" runat="server">
			<a href="javascript:void(0)" onclick="document.getElementById('light').style.display='block';document.getElementById('fade').style.display='block'">Click here for support</a>
		  		</div>
			</div>
		</div>
	<div id="light" class="ewd-ufaq-bright_content">
            <asp:Label ID="lbltext" runat="server" Text="Hey there!"></asp:Label>
            <a href="javascript:void(0)" onclick="document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">Close</a>
		</br>
		<h2>Need help?</h2>
		<p>You may find the information you need with our support tools.</p>
		<a href="https://www.youtube.com/playlist?list=PLEndQUuhlvSrNdfu5FKa1uGHsaKZxgdWt"><img src="<?php echo plugins_url(); ?>/ultimate-faqs/images/support_icons_ufaq-01.png" /></a>
		<a href="https://www.youtube.com/playlist?list=PLEndQUuhlvSrNdfu5FKa1uGHsaKZxgdWt"><h4>Youtube Tutorials</h4></a>
		<p>Our tutorials show you the basics of setting up your plugin, to the more specific utilization of our features.</p>
		<div class="ewd-ufaq-clear"></div>
		<a href="https://wordpress.org/support/plugin/ultimate-faqs"><img src="<?php echo plugins_url(); ?>/ultimate-faqs/images/support_icons_ufaq-03.png"/></a>
		<a href="https://wordpress.org/support/plugin/ultimate-faqs"><h4>WordPress Forum</h4></a>
		<p>We make sure to answer your questions within a 24hrs frame during our business days. Search within our threads to find your answers. If it has not been addressed, please create a new thread!</p>
		<div class="ewd-ufaq-clear"></div>
		<a href="http://www.etoilewebdesign.com/plugins/ultimate-faq/documentation-ultimate-faq/"><img src="<?php echo plugins_url(); ?>/ultimate-faqs/images/support_icons_ufaq-02.png"/></a>
		<a href="http://www.etoilewebdesign.com/plugins/ultimate-faq/documentation-ultimate-faq/"><h4>Documentation</h4></a>
		<p>Most information concerning the installation, the shortcodes and the features are found within our documentation page.</p>
        </div>
	</form>

<!--END TOP BOX-->
</div>



<!--Middle box-->
<div class="ewd-dashboard-middle">
<div id="col-full">
<h3 class="ewd-ufaq-dashboard-h3">FAQ Summary</h3>
<div>
	<table class='ewd-ufaq-overview-table wp-list-table widefat fixed striped posts'>
		<thead>
			<tr>
				<th><?php _e("Title", 'EWD_ABCO'); ?></th>
				<th><?php _e("Views", 'EWD_ABCO'); ?></th>
				<th><?php _e("Categories", 'EWD_ABCO'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				$args = array(
					'post_type' => 'ufaq',
					'orderby' => 'meta_value_num',
					'meta_key' => 'ufaq_view_count'
				);

				$Dashboard_FAQs_Query = new WP_Query($args);
				$Dashboard_FAQs = $Dashboard_FAQs_Query->get_posts();

				if (sizeOf($Dashboard_FAQs) == 0) {echo "<tr><td colspan='3'>" . __("No FAQs to display yet. Create an FAQ and then view it for it to be displayed here.", 'EWD_UFAQ') . "</td></tr>";}
				else {
					foreach ($Dashboard_FAQs as $Dashboard_FAQ) { ?>
						<tr>
							<td><a href='post.php?post=<?php echo $Dashboard_FAQ->ID;?>&action=edit'><?php echo $Dashboard_FAQ->post_title; ?></a></td>
							<td><?php echo get_post_meta($Dashboard_FAQ->ID, 'ufaq_view_count', true); ?></td>
							<td><?php echo EWD_UFAQ_Get_Categories($Dashboard_FAQ->ID); ?></td>
						</tr>
					<?php }
				}
			?>
		</tbody>
	</table>
</div>
<br class="clear" />
</div>
</div>

<?php if($hideReview != 'Yes'){ ?>
<div id='ewd-ufaq-dashboard-leave-review' class='ewd-ufaq-leave-review postbox upcp-postbox-collapsible'>
	<h3 class='hndle ewd-ufaq-dashboard-h3'>Leave a Review <span></span></h3>
	<div class='ewd-dashboard-content'>
		<div class="ewd-dashboard-leave-review-text">
			If you enjoy this plugin and have a minute, please consider leaving a 5-star review. Thank you!
		</div>
		<a href="https://wordpress.org/support/plugin/ultimate-faqs/reviews/" class="ewd-dashboard-leave-review-link" target="_blank">Leave a Review!</a>
		<div class="clear"></div>
	</div>
	<form action="admin.php?page=EWD-UFAQ-Options" method="post">
		<input type="hidden" name="hide_ufaq_review_box_hidden" value="Yes">
		<input type="submit" name="hide_ufaq_review_box_submit" class="ewd-dashboard-leave-review-dismiss" value="I've already left a review">
	</form>
</div>
<div class="clear"></div>
<?php } ?>

<!-- END MIDDLE BOX -->

<!-- FOOTER BOX -->
<!-- A list of the products in the catalogue -->
<div class="ewd-dashboard-footer">
<div id='ewd-dashboard-updates' class='ewd-ufaq-updates postbox upcp-postbox-collapsible'>
<h3 class='hndle ewd-ufaq-dashboard-h3' id='ewd-recent-changes'><?php _e("Recent Changes", 'UPCP'); ?> <i class="fa fa-cog" aria-hidden="true"></i></h3>
<div class='ewd-dashboard-content' ><?php echo get_option('EWD_UFAQ_Changelog_Content'); ?></div>
</div>

<div id='ewd-dashboard-blog' class='ewd-ufaq-blog postbox upcp-postbox-collapsible'>
<h3 class='hndle ewd-ufaq-dashboard-h3'>News <i class="fa fa-rss" aria-hidden="true"></i></h3>
<div class='ewd-dashboard-content'><?php echo get_option('EWD_UFAQ_Blog_Content'); ?></div>
</div>

<div id="ewd-dashboard-plugins" class='ewd-ufaq-plugins postbox upcp-postbox-collapsible' >
	<h3 class='hndle ewd-ufaq-dashboard-h3'><span><?php _e("Goes great with:", 'UPCP') ?></span><i class="fa fa-plug" aria-hidden="true"></i></h3>
	<div class="inside">
		<div class="ewd-dashboard-plugin-icons">
			<div style="width:50%">
				<a target='_blank' href='https://wordpress.org/plugins/ultimate-product-catalogue/'><img style="width:100%" src='<?php echo plugins_url(); ?>/ultimate-faqs/images/UPCP_Icons-07-300x300.png'/></a>
			</div>
			<div>
				<h3>Product Catalog</h3> <p>Enables you to display your business's products in a clean and efficient manner.</p>
			</div>

		</div>
		<div class="ewd-dashboard-plugin-icons">
			<div style="width:50%">
				<a target='_blank' href='https://wordpress.org/plugins/ultimate-slider/'><img style="width:100%" src='<?php echo plugins_url(); ?>/ultimate-faqs/images/US_Related_Sales_Icon.png'/></a>
			</div>
			<div>
				<h3>Ultimate Slider</h3><p>An easy-to-use slider plugin that lets you add a clean, modern, responsive slider to any WordPress page.</p>
			</div>

		</div>
	</div>
</div>
</div>
</div>


<?php
function EWD_UFAQ_Get_EWD_Blog() {
	$Blog_URL = EWD_UFAQ_CD_PLUGIN_PATH . 'Blog.html';
	$Blog = file_get_contents($Blog_URL);

	update_option('EWD_UFAQ_Blog_Content', $Blog);
}

function EWD_UFAQ_Get_Changelog() {
	$Readme_URL = EWD_UFAQ_CD_PLUGIN_PATH . 'readme.txt';
	$Readme = file_get_contents($Readme_URL);

	$Changes_Start = strpos($Readme, "== Changelog ==") + 15;
	$Changes_Section = substr($Readme, $Changes_Start);

	$Changes_Text = substr($Changes_Section, 0, strposX($Changes_Section, "=", 5));

	$Changes_Text = str_replace("= ", "<h3>", $Changes_Text);
	$Changes_Text = str_replace(" =", "</h3>", $Changes_Text);
	$Changes_Text = str_replace("- ", "<br />- ", $Changes_Text);

	update_option('EWD_UFAQ_Changelog_Content', $Changes_Text);
}

function strposX($haystack, $needle, $number){
    if($number == '1'){
        return strpos($haystack, $needle);
    }elseif($number > '1'){
        return strpos($haystack, $needle, strposX($haystack, $needle, $number - 1) + strlen($needle));
    }else{
        return error_log('Error: Value for parameter $number is out of range');
    }
}

?>
