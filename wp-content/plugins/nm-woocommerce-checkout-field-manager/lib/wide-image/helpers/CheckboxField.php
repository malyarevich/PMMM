<?php
	/**
	 * @package Demos
	 */
	class CheckboxField extends Field
	{
		function __construct($name, $default, $caption = null)
		{
			$this->name = $name;
			$this->default = $default;
			
			if ($caption == null)
				$caption = $name;
			
			$this->caption = $caption;
		}
		
		function init($request)
		{
			$this->value = $request->get($this->name, null) === '1';
		}
		
		function render()
		{
			if ($this->value)
				$chk = 'checked="checked"';
			else
				$chk = '';
			
			echo '<input type="checkbox" ' . $chk . ' name="' . $this->name . '" id="' . $this->name . '" value="1" />';
			echo '<label for="' . $this->name . '">' . $this->caption . '</label>';
		}
	}
?>