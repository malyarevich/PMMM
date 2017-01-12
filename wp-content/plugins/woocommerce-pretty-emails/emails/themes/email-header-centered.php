<?php

/**
 * Email Header
 *
 * @version  1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include( MBWPE_TPL_PATH.'/settings.php' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo get_bloginfo( 'name' ); ?></title>
<style type="text/css">
img {
max-width: 100%; height: auto;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6;
}
body {
background-color: <?php echo $bg;?>;
}
@media only screen and (max-width: 640px) {
 
  h1 {
    font-size: <?php echo $h1s;?> !important;
  }
  h2 {
    font-size: <?php echo $h2s;?> !important;
  }
  h3 {
    font-size: <?php echo $h3s;?> !important;
  }
  .content {
    padding: 10px !important;
  }
  h1.content {
    padding: 0px !important;
  }
  .content-wrapper {
    padding: 10px !important;
  }
}
</style>
</head>

<body style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6; background: <?php echo $bg;?>; margin: 0; padding: 0;">
<!--[if gte mso 9]>
<table width="<?php echo $width;?>" align="center"><tr>
<td width="<?php echo $width;?>">
<![endif]-->
<table class="body-wrap" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; width: 100%; background: <?php echo $bg;?>; margin: 0; padding: 0;">
		<tr style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; margin: 0; padding: 0;">
		<td style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; vertical-align: middle; margin: 0; padding: 0;" valign="middle"></td>
		<td class="container dark" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; vertical-align: middle; display: block !important; max-width: <?php echo $width.'px';?> !important; clear: both !important; margin: 0 auto; padding: 0;" valign="middle"> 
			

			
			<div class="content" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; max-width: <?php echo $width.'px';?>; display: block; margin: 0 auto; padding: 20px; padding-bottom:10px;">
				<table width="100%" cellpadding="0" cellspacing="0" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; margin: 0; padding: 0;">
				<tr style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; margin: 0; padding: 0;">
					<td align="center" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; vertical-align: middle; width: 100%; margin: 0; padding: 0;text-align: center;" valign="middle">
					
					<?php

					if ( $img = get_option( 'woocommerce_email_mbc_logo' ) ) {

						if ( $linkl = get_option( 'woocommerce_email_mbc_logo_link' ) )
	             		echo '<a href="'.esc_url($linkl).'">';
						echo '<img src="'.esc_url($img).'" style="max-width: 100%; height: auto; margin: 0; padding: 0;" align="top"  />';
	              		if ( $linkl = get_option( 'woocommerce_email_mbc_logo_link' ) )
	             		echo '</a>';
	              	}

	                ?>
					
					</td>
				</tr>
				<tr style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; margin: 0; padding: 0;">
			
					<td class="headlinks" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; vertical-align: bottom; text-align: center; width: 100%; margin: 0; padding: 0;" align="right" valign="bottom">

						<?php if( $onelink = get_option('woocommerce_email_mbc_extra_link_one') ) : ?>
						<a href="<?php echo get_permalink($onelink);?>" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; color: <?php echo $elcolor;?>; text-decoration: underline; margin: 0; padding: 0;"><?php echo get_the_title($onelink); ?></a>
						<?php endif; ?>
						
						<?php if( $onelink = get_option('woocommerce_email_mbc_extra_link_one') &&  $secondlink = get_option('woocommerce_email_mbc_extra_link_two') ) : ?>
						<span style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: 10px; margin: 0; padding: 0;">|</span>	
						<?php endif; ?>

						<?php if( $secondlink = get_option('woocommerce_email_mbc_extra_link_two') ) : ?>
						<a href="<?php echo get_permalink($secondlink);?>" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; color: <?php echo $elcolor;?>; text-decoration: underline; margin: 0; padding: 0;"><?php echo get_the_title($secondlink); ?></a>
						<?php endif; ?>
						
	
						<?php if( $secondlink = get_option('woocommerce_email_mbc_extra_link_two') &&  $thirdlink = get_option('woocommerce_email_mbc_extra_link_three') ) : ?>
						<span style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: 10px; margin: 0; padding: 0;">|</span>	
						<?php endif; ?>
					

						<?php if( $thirdlink = get_option('woocommerce_email_mbc_extra_link_three') ) : ?>
						<a href="<?php echo get_permalink($thirdlink);?>" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; color: <?php echo $elcolor;?>; text-decoration: underline; margin: 0; padding: 0;"><?php echo get_the_title($thirdlink); ?></a>
						<?php endif; ?>



					</td>
				
				</tr>
				</table>
			</div>
	
		</td>
		<td style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; vertical-align: middle; margin: 0; padding: 0;" valign="middle"></td>
		</tr>

		<tr style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; margin: 0; padding: 0;">
		<td style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; vertical-align: middle; margin: 0; padding: 0;" valign="middle"></td>
		<td class="container" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; vertical-align: middle; display: block !important; max-width: <?php echo $width.'px';?> !important; clear: both !important; margin: 0 auto; padding: 0;" valign="middle">
			<div class="content" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; max-width: <?php echo $width.'px';?>; display: block; margin: 0 auto; padding: 20px; padding-top:10px;">
			<table class="main" width="100%" cellpadding="0" cellspacing="0" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; background: <?php echo $bodybg; ?> ; margin: 0; padding: 0; border: 1px solid <?php echo $mbordercolor; ?>;">
			<?php
	              
	              if ( $img = get_option( 'woocommerce_email_header_image' ) ) {
	             					echo '<tr>';
	             					echo '<td colspan="2" valign="top">';
	             					if ( $linkb = get_option( 'woocommerce_email_mbc_banner_link' ) )
	             					echo '<a href="'.esc_url($linkb).'">';
	                				echo '<img src="' . esc_url( $img ) . '" alt="' . get_bloginfo( 'name' ) . '"  align="top" style="width:100%; height:auto;" />';
	              					if ( $linkb = get_option( 'woocommerce_email_mbc_banner_link' ) )
	             					echo '</a>';
	              					echo '</td>';
	              					echo '</tr>';
	              }

	            ?>
			<tr style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; margin: 0; padding: 0;">
					<td style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; vertical-align: middle; margin: 0; padding: 20px; background: <?php echo $base;?>;" valign="middle">
						<h1 class="content" style="font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif; box-sizing: border-box; font-size: <?php echo $h1;?>;  max-width: <?php echo $width.'px';?>; display: block; color: <?php echo $h1c; ?>; line-height: 1.2; font-weight: 500; background: <?php echo $base;?>; margin: 0 auto; padding: 0px;"><?php echo $email_heading; ?></h1>
					</td>	
			</tr>
			<tr style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; margin: 0; padding: 0;">
					<td class="content-wrap" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; vertical-align: middle; margin: 0; padding: 20px;" valign="middle">
						<table width="100%" cellpadding="0" cellspacing="0" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; margin: 0; padding: 0;">

						<tr style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; margin: 0; padding: 0;">
							<td style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; vertical-align: middle; margin: 0; padding: 0;" valign="middle">