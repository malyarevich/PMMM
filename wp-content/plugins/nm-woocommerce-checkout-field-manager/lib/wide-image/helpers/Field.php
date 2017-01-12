<?php
	/**
	 * @package Demos
	 */
	class Field
	{
		public $name;
		public $default;
		public $value;
		public $request;
		
		function __construct($name, $default = null)
		{
			$this->name = $name;
			$this->default = $default;
		}
		
		function init($request)
		{
			$this->value = $request->get($this->name, $this->default);
		}
		
		function render()
		{
			echo $this->name . ': ';
			echo '<input type="text" size="15" name="' . $this->name . '" value="' . $this->value . '" /> ';
		}
		
		function getUrlValue()
		{
			return urlencode($this->name) . '=' . urlencode($this->value);
		}
	}
?>