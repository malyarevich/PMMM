<?php

class SNP_NHP_Options_select_show_fieldsgroup extends SNP_NHP_Options
{

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since SNP_NHP_Options 1.0.1
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
	 * @since SNP_NHP_Options 1.0.1
	 */
	function render()
	{

		$class = (isset($this->field['class'])) ? $this->field['class'] : '';

		echo '<select id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" class="' . $class . ' nhp-opts-select-show-fieldsgroup" >';

		foreach ($this->field['options'] as $k => $v)
		{

			echo '<option value="' . $k . '" ' . selected($this->value, $k, false) . ' data-fieldsgroup="' . (isset($v['fieldsgroup']) ? $v['fieldsgroup'] : '') . '">' . $v['name'] . '</option>';
		}//foreach

		echo '</select>';

		echo (isset($this->field['desc']) && !empty($this->field['desc'])) ? ' <span class="description">' . $this->field['desc'] . '</span>' : '';
	}

//function

	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since SNP_NHP_Options 1.0.1
	 */
	function enqueue()
	{

		wp_enqueue_script(
				'nhp-opts-select-show-fieldsgroup-js', SNP_NHP_OPTIONS_URL . 'fields/select_show_fieldsgroup/field_select_show_fieldsgroup.js', array('jquery'), time(), true
		);
	}

//function
}

//class
?>