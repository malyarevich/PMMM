<?php
	/**
	 * @package Demos
	 */
	class SelectField extends Field
	{
		public $options;
		
		function __construct($name, $options, $default = null)
		{
			$this->name = $name;
			$this->options = $options;
			if ($default === null)
				$this->default = $options[0];
			else
				$this->default = $default;
		}
		
		function init($request)
		{
			$this->value = $this->default;
			$v = str_replace('+', ' ', $request->get($this->name));
			if (in_array($v, $this->options))
				$this->value = $v;
		}
		
		function render()
		{
			echo $this->name . ': ';
			echo '<select name="' . $this->name . '">';
			foreach ($this->options as $option)
			{
				if ($this->value == $option)
					$sel = 'selected="selected"';
				else
					$sel = '';
				
				echo '<option ' . $sel . ' value="' . $option . '">' . $option . '</option>';
			}
			echo '</select>';
		}
	}
?>