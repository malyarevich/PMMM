<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsAddonSliderFront {
	
	protected function enqueueScripts() {
		
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
		
	}
	
	protected function enqueuePreview() {
		
		add_action('revslider_preview_slider_head', array($this, 'enqueue_preview'));
		
	}
	
	protected function writeInitScript() {
		
		add_action('revslider_fe_javascript_output', array($this, 'write_init_script'), 10, 3);
		
	}
	
	public function enqueue_scripts() {
		
		$ops           = new RevSliderOperations();
		$globals       = $ops->getGeneralSettingsValues();
		
		$putJsToFooter = RevSliderFunctions::getVal($globals, 'js_to_footer', 'off') === 'off';
		
		$_handle       = 'rs-' . static::$_PluginTitle . '-front';
		$_base         = static::$_PluginUrl . 'public/assets/';
		
		wp_enqueue_style($_handle, $_base . 'css/' . static::$_PluginTitle . '.css', array(), static::$_Version);
		wp_enqueue_script(
		
			$_handle, 
			$_base . 'js/revolution.addon.' . static::$_PluginTitle . '.min.js', 
			array('jquery', 'revmin'), 
			static::$_Version, 
			$putJsToFooter
			
		);
		
	}
	
	public function enqueue_preview() {
		
		$_base = static::$_PluginUrl . 'public/assets/';
		
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo $_base . 'css/' . static::$_PluginTitle . '.css'; ?>" />
		<script type="text/javascript" src="<?php echo $_base . 'js/revolution.addon.' . static::$_PluginTitle . '.min.js'; ?>"></script>
		<?php
		
	}

	public function write_init_script($_slider, $_id) {
		
		$_id    = $_slider->getID();
		$_title = static::$_PluginTitle;
		
		echo                  "\n";
		echo '                Rs' . ucfirst($_title) . 'AddOn(tpj, revapi' . $_id . ');'."\n";
		
	}
	
}
?>