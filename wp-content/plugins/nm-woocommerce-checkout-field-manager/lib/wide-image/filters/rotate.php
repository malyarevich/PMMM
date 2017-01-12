<?php
	/**
	 * @package Demos
	 */
	class Demo_rotate extends Demo
	{
		public $order = 1100;

		function init()
		{
			$this->addField(new CoordField('angle', 25));
		}
		
		function execute($image, $request)
		{
			$angle = $this->fields['angle']->value;
			
			return $image->rotate($angle);
		}
	}
?>