<?php global $post, $product, $woocommerce;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

do_action($this->slug.'-before-images');

$prod_imgs = $this->get_product_images( $product ); ?>

<div id="<?php echo $this->slug.'_images_wrap'; ?>" <?php if( is_rtl() ) { echo 'dir="ltr"'; } ?>>
    <?php if(!empty($prod_imgs)): ?>

        <?php $prod_imgsCount = count($prod_imgs); ?>

        <div id="<?php echo $this->slug.'_images'; ?>" class="jckqv_slider">

        	<?php $i = 0; foreach($prod_imgs as $img_id => $imgData): ?>

        		<img src="<?php echo $imgData['img_src']; ?>" data-<?php echo $this->slug; ?>="<?php echo implode(' ', $imgData['slideId']); ?>" class="<?php echo $this->slug; ?>_image" width="<?php echo $imgData['img_width']; ?>" height="<?php echo $imgData['img_height']; ?>">

        	<?php $i++; endforeach; ?>

        </div>

        <?php if( $prod_imgsCount > 1 && $this->settings['popup_imagery_thumbnails'] == 'thumbnails' ): ?>

            <div id="<?php echo $this->slug.'_thumbs'; ?>" class="jckqv_slider">

                <?php $i = 0; foreach($prod_imgs as $img_id => $imgData): ?>

            		<?php
                    $classes = array();
                    if($i==0) $classes[] = "slick-main-active";
                    ?>

            		<div data-index="<?php echo $i; ?>" class="<?php echo implode(' ', $classes); ?>"><img src="<?php echo $imgData['img_thumb_src']; ?>" data-<?php echo $this->slug; ?>="<?php echo implode(' ', $imgData['slideId']); ?>" class="<?php echo $this->slug; ?>_thumb"></div>

            	<?php $i++; endforeach; ?>

            </div>

        <?php endif; ?>

    <?php endif; ?>
</div>

<?php do_action($this->slug.'-after-images'); ?>