<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title:','cfb_login_lang'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('badge_size'); ?>"><?php echo __('Badge Size:','cfb_login_lang'); ?></label>
<input id="<?php echo $this->get_field_id( 'badge_size' ); ?>" name="<?php echo $this->get_field_name( 'badge_size' ); ?>" type="text" value="<?php echo esc_attr($badge_size); ?>" /><span>px</span>
</p>

<p>
<label for="<?php echo $this->get_field_id('tooltip'); ?>"><?php echo __('Show Tooltip:','cfb_login_lang'); ?></label>
<input class="checkbox" <?php checked($instance['tooltip'], 'yes'); ?> id="<?php echo $this->get_field_id( 'tooltip' ); ?>" name="<?php echo $this->get_field_name( 'tooltip' ); ?>" type="checkbox" value="yes" />
</p>

<p>
<label for="<?php echo $this->get_field_id('tooltip_animation'); ?>"><?php echo __('Tooltip Animation:','cfb_login_lang'); ?></label>
<select class="widefat" name="<?php echo $this->get_field_name( 'tooltip_animation' ); ?>" id="<?php echo $this->get_field_id( 'tooltip_animation' ); ?>">
    <option <?php if ('fade' == $instance['tooltip_animation']) echo 'selected="selected"'; ?>>fade</option>
    <option <?php if ('grow' == $instance['tooltip_animation']) echo 'selected="selected"'; ?>>grow</option>
    <option <?php if ('slide' == $instance['tooltip_animation']) echo 'selected="selected"'; ?>>slide</option>
    <option <?php if ('swing' == $instance['tooltip_animation']) echo 'selected="selected"'; ?>>swing</option>
</select>
</p>