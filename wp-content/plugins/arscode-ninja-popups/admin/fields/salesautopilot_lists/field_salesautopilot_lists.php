<?php

class SNP_NHP_Options_salesautopilot_lists extends SNP_NHP_Options
{

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since SNP_NHP_Options 1.0
	 */
	function __construct($field = array(), $value ='', $parent)
	{

		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;
		//$this->render();
	}

//function

	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since SNP_NHP_Options 1.0
	 */
	function render()
	{

		$class = (isset($this->field['class'])) ? $this->field['class'] : '';

		if (!$this->value)
		{
			$this->value = $this->field['std'];
		}
		$this->field['options']=snp_ml_get_salesautopilot_lists();
		$class = (isset($this->field['class'])) ? 'class="' . $this->field['class'] . '" ' : '';
		echo '<select id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" ' . $class . 'rows="6" >';
		if($this->field['meta']==1)
		{
		    echo '<option value="" ' . selected($this->value, '', false) . '>Use global settings</option>';
		}
		if(count((array)$this->field['options'])==0)
		{
			echo '<option value="" ' . selected($this->value, '', false) . '>--</option>';
		}
		else
		{
			foreach ($this->field['options'] as $k => $v)
			{
				echo '<option value="' . $k . '" ' . selected($this->value, $k, false) . '>' . $v['name'] . '</option>';
			}//foreach
		}
		echo '</select>';
		if(!isset($this->field['meta']))
		{
		    echo '<input type="button" rel-id="' . $this->field['id'] . '" class="button salesautopilot_lists_gl" name="" value="Grab Lists" />';
		}
		echo (isset($this->field['desc']) && !empty($this->field['desc'])) ? ' <span class="description">' . $this->field['desc'] . '</span>' : '';
	}

//function

	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since SNP_NHP_Options 1.0
	 */
	function enqueue()
	{

		wp_enqueue_script(
				'nhp-opts-field-salesautopilot_lists-js', SNP_NHP_OPTIONS_URL . 'fields/salesautopilot_lists/field_salesautopilot_lists.js', array('jquery', 'farbtastic'), time(), true
		);
	}

//function
}

//class
?>