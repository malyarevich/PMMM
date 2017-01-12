<?php


	if ( ! defined( 'ABSPATH' ) ) exit;

	class prdctfltr extends WP_Widget {

		public static $settings;

		function __construct() {
			$widget_ops = array(
				'classname' => 'prdctfltr-widget',
				'description' => __( 'Product Filter widget version.', 'prdctfltr' )
			);
			parent::__construct(
				'prdctfltr',
				'+ Product Filter',
				$widget_ops
			);
		}

		function pf_title( $args ) {

			$args['before'] = self::$settings['before'] . $args['before'];
			$args['after'] = $args['after'] . self::$settings['after'];

			return $args;

		}

		function widget( $args, $instance ) {
			extract( $args, EXTR_SKIP );

			self::$settings = array(
				'before' => $before_title,
				'after' => $after_title
			);

			add_filter( 'prdctfltr_filter_title_args', array( &$this, 'pf_title' ) );


			global $prdctfltr_global;

			$prdctfltr_global['widget_search'] = true;
			WC_Prdctfltr::$settings['widget'] = array(
				'style' => ( isset( $instance['preset'] ) ? $instance['preset'] : '' ),
				'preset' => ( isset( $instance['template'] ) ? $instance['template'] : '' ),
				'disable_overrides' => ( isset( $instance['disable_overrides'] ) && $instance['disable_overrides'] == 'yes' ? 'yes' : 'no' ),
				'action' => ( isset( $instance['widget_action'] ) ? $instance['widget_action'] : '' )
			);

			$prdctfltr_global['widget_options'] = array(
				'style' => ( isset( $instance['preset'] ) ? $instance['preset'] : '' ),
				'preset' => ( isset( $instance['template'] ) ? $instance['template'] : '' ),
				'disable_overrides' => ( isset( $instance['disable_overrides'] ) && $instance['disable_overrides'] == 'yes' ? 'yes' : 'no' ),
				'action' => ( isset( $instance['widget_action'] ) ? $instance['widget_action'] : '' )
			);

			$prdctfltr_global['preset'] = $prdctfltr_global['widget_options']['preset'];
			$prdctfltr_global['disable_overrides'] = $prdctfltr_global['widget_options']['disable_overrides'];
			$prdctfltr_global['action'] = $prdctfltr_global['widget_options']['action'];

			echo $before_widget;

			include( WC_Prdctfltr::$dir . 'woocommerce/loop/product-filter.php' );

			echo $after_widget;

			WC_Prdctfltr::$settings['widget'] = null;
			$prdctfltr_global['widget_search'] = null;
			$prdctfltr_global['widget_options'] = array();

		}

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$instance['preset'] = $new_instance['preset'];
			$instance['template'] = $new_instance['template'];
			$instance['disable_overrides'] = ( isset( $new_instance['disable_overrides'] ) ? 'yes' : 'no' );
			$instance['widget_action'] = esc_url( $new_instance['widget_action'] );

			return $instance;
		}

		function form( $instance ) {
			$vars = array( 'preset' => 'pf_default', 'template' => '', 'disable_overrides' => 'no', 'widget_action' => '' );
			$instance = wp_parse_args( (array) $instance, $vars );

			$preset = strip_tags($instance['preset']);
			$template = strip_tags($instance['template']);
			$disable_overrides = strip_tags($instance['disable_overrides']);
			$widget_action = strip_tags($instance['widget_action']);

	?>
			<div>
				<p class="prdctfltr-box">
					<label for="<?php echo $this->get_field_id('preset'); ?>" class="prdctfltr-label"><?php _e('Style', 'prdctfltr'); ?> :</label>
					<select name="<?php echo $this->get_field_name('preset'); ?>" id="<?php echo $this->get_field_id('preset'); ?>" class="widefat">
						<option value="pf_default_inline"<?php echo ( $preset == 'pf_default_inline' ? ' selected="selected"' : '' ); ?>><?php _e('Flat Inline', 'prdctfltr'); ?></option>
						<option value="pf_default"<?php echo ( $preset == 'pf_default' ? ' selected="selected"' : '' ); ?>><?php _e('Flat Block', 'prdctfltr'); ?></option>
						<option value="pf_default_select"<?php echo ( $preset == 'pf_default_select' ? ' selected="selected"' : '' ); ?>><?php _e('Flat Select', 'prdctfltr'); ?></option>
					</select>
				</p>
				<p class="prdctfltr-box"> 
					<label for="<?php echo $this->get_field_id('template'); ?>" class="prdctfltr-label"><?php _e('Preset', 'prdctfltr'); ?> :</label>
					<select name="<?php echo $this->get_field_name('template'); ?>" id="<?php echo $this->get_field_id('template'); ?>" class="widefat">
						<option value="default"<?php echo ( $template == 'default' ? ' selected="selected"' : '' ); ?>><?php _e('Default', 'prdctfltr'); ?></option>
					<?php
						$curr_templates = get_option( 'prdctfltr_templates', array() );
						foreach ( $curr_templates as $k => $v ) {
					?>
						<option value="<?php echo $k; ?>"<?php echo ( $template == $k ? ' selected="selected"' : '' ); ?>><?php echo $k; ?></option>
					<?php
						}
					?>
					</select>
				</p>
				<p class="prdctfltr-box">
					<label for="<?php echo $this->get_field_id('disable_overrides'); ?>" class="prdctfltr-label"><?php _e('Disable Overrides', 'prdctfltr'); ?> :</label>
					<input type="checkbox" name="<?php echo $this->get_field_name('disable_overrides'); ?>" id="<?php echo $this->get_field_id('disable_overrides'); ?>" value="yes" <?php echo ( $disable_overrides == 'yes' ? ' checked' : '' ); ?> />
				</p>
				<p class="prdctfltr-box">
					<label for="<?php echo $this->get_field_id('widget_action'); ?>" class="prdctfltr-label"><?php _e('Widget Action URL', 'prdctfltr'); ?> :</label>
					<input type="text" name="<?php echo $this->get_field_name('widget_action'); ?>" id="<?php echo $this->get_field_id('widget_action'); ?>" value="<?php echo $widget_action; ?>" class="widefat" /><br/>
					<small><?php _e( 'Custom action is used if the widget is not used in shop, product archives or pages with Product Filter shortcodes. This way you can redirect filtering to your shop page or a custom page. Enter URL to redirect. For example your shop page URL', 'prdctfltr' ); ?> <?php echo get_permalink( self::prdctfltr_wpml_get_id( woocommerce_get_page_id( 'shop' ) ) ); ?></small>
				</p>

			</div>

	<?php
		}

		public static function prdctfltr_wpml_get_id( $id ) {
			if( function_exists( 'icl_object_id' ) ) {
				return icl_object_id( $id, 'page', true );
			}
			else {
				return $id;
			}
		}

	}

	function prdctfltr_register_widgets() {
		register_widget( 'prdctfltr' );
	}
	add_action( 'widgets_init', 'prdctfltr_register_widgets' );

?>