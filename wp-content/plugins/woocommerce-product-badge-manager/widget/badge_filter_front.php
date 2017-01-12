<?php
    $yestooltip = $instance['tooltip'];
    $tooltip_animation = $instance['tooltip_animation'];
?>
<h2 class="widget-title"><?php echo $instance['title']; ?></h2>
<?php echo do_shortcode('[woo_pro_badges_filter size="'.$instance["badge_size"].'" tooltip="'.$yestooltip.'" tooltip_animation="'.$tooltip_animation.'"]'); ?>