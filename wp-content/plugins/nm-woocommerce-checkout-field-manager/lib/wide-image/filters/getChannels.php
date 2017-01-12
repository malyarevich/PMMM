<?php
	/**
	 * @package Demos
	 */
	class Demo_getChannels extends Demo
	{
		public $order = 500;
		
		function init()
		{
			$this->addField(new CheckboxSetField('channels', array('red', 'green', 'blue', 'alpha')));
		}
		
		function execute($img, $request)
		{
			return $img->getChannels($this->fields['channels']->value);
		}
	}
?>