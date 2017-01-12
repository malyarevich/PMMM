<?php
/*
	function of_get_registered_sidebars()
	{
		global $wp_registered_sidebars;
		print_r($wp_registered_sidebars);
		return $wp_registered_sidebars;
	}

	$of_sidebars = array();
	$of_get_registered_sidebars = of_get_registered_sidebars();
	foreach ((array) $of_get_registered_sidebars as $sidebar)
	{
		$of_sidebars[$sidebar['id']] = $sidebar['name'];
	}
	
 *  */
class SNP_NHP_Options_sidebar_select extends SNP_NHP_Options{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since SNP_NHP_Options 1.0.1
	*/
	function __construct($field = array(), $value ='', $parent){
		
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;
		//$this->render();
		
	}//function
	
	
	
	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since SNP_NHP_Options 1.0.1
	*/
	function render(){
		global $wp_registered_sidebars;
		$class = (isset($this->field['class']))?'class="'.$this->field['class'].'" ':'';
		
		echo '<select id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" '.$class.'rows="6" >';
		
		$args = wp_parse_args($this->field['args'], array());
		
		if($this->field['defaultforsite'])
		{
			echo '<option value=""'.selected($this->value, '', false).'>-- default for site --</option>';
		}
		foreach ((array) $wp_registered_sidebars as $sidebar)
		{
			echo '<option value="'.$sidebar['id'].'"'.selected($this->value, $sidebar['id'], false).'>'.$sidebar['name'].'</option>';
		}

		echo '</select>';

		echo (isset($this->field['desc']) && !empty($this->field['desc']))?' <span class="description">'.$this->field['desc'].'</span>':'';
		
	}//function
	
}//class
?>