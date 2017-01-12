<?php
/**
 * Email Footer
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates/Emails
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include( MBWPE_TPL_PATH.'/settings.php' );

?>
                            </td>
                        </tr>
                        </table>
                    </td>
                </tr>
            </table>

            
            <div class="footer" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; width: 100%; clear: both; color: #666; margin: 0; padding: 0;">
                <table width="100%" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; margin: 0; padding: 0;">
                    <tr style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; margin: 0; padding: 0;">
                    <td class="alignleft quarter" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; vertical-align: middle; width: 25%; margin: 0; padding: 10px 0;" valign="middle" width="25%">
                    
                   <?php

                    if ( ( $img = get_option( 'woocommerce_email_mbc_logo' ) ) && $logowidth > 0 ) {

                        $logowidth = $logowidth > round($width/4) ? round($width/4) : $logowidth;
                        if ( $linkl = get_option( 'woocommerce_email_mbc_logo_link' ) )
                        echo '<a href="'.esc_url($linkl).'">';
                        echo '<img src="'.esc_url($img).'" style="max-width: 100%; height: auto; margin: 0; padding: 0;" align="top" width="'.$logowidth.'" />';
                        if ( $linkl = get_option( 'woocommerce_email_mbc_logo_link' ) )
                        echo '</a>';
                    }
                    
                    ?>
                    
                    </td>
                    
                    <td class="aligncenter half" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: 11px; color: <?php echo $text;?>; vertical-align: middle; text-align: center; width: 50%; margin: 0; padding: 10px 0; line-height: 1.2;" align="center" valign="middle" width="50%">
                    
                        <?php echo apply_filters( 'woocommerce_email_mbc_email_footer_text', wpautop( wp_kses_post( wptexturize( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ) ) ) ); ?>
              
                    </td>
                    
                    <td class="alignright quarter" style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; vertical-align: middle; text-align: right; width: 25%; margin: 0; padding: 10px 0;" align="right" valign="middle" width="25%">
                        
                        <?php if ($fb = get_option('woocommerce_email_mbc_facebook')) : ?>
                        <?php echo '<a href="'.esc_url($fb).'"><img src="'. esc_url($fblogopath) .'" border="0" /></a>&nbsp;';?>
                        <?php endif; ?>
                        
                        <?php if ($twitter = get_option('woocommerce_email_mbc_twitter')) : ?>
                        <?php echo '<a href="'.esc_url($twitter).'"><img src="' . esc_url($tlogopath) . '" border="0" /></a>&nbsp;';?>
                        <?php endif; ?>

                        <?php if ($instagram = get_option('woocommerce_email_mbc_instagram')) : ?>
                        <?php echo '<a href="'.esc_url($instagram).'"><img src="' . esc_url($ilogopath) . '" border="0" /></a>&nbsp;';?>
                        <?php endif; ?>

                        <?php if ($pinterest = get_option('woocommerce_email_mbc_pinterest')) : ?>
                        <?php echo '<a href="'.esc_url($pinterest).'"><img src="' . esc_url($plogopath) . '" border="0" /></a>&nbsp;';?>
                        <?php endif; ?>

                        <?php if ($google = get_option('woocommerce_email_mbc_google')) : ?>
                        <?php echo '<a href="'.esc_url($google).'"><img src="' . esc_url($glogopath) . '" border="0" /></a>&nbsp;';?>
                        <?php endif; ?>


                    </td>
                    </tr>
                </table>
            </div>

            </div>
        </td>
        <td style="font-family: <?php echo $bodyFont;?>; box-sizing: border-box; font-size: <?php echo $bodyfontsize;?>; vertical-align: middle; margin: 0; padding: 0;" valign="middle"></td>
    </tr>
</table>
<!--[if gte mso 9]>
</td></tr></table>
<![endif]-->
</body>
</html>