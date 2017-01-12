<?php
	/**
	 * @package Demos
	 */
	class Demo_autocrop extends Demo
	{
		public $order = 1050;
		
		function init()
		{
			$this->addField(new IntField('margin', 0));
			$this->addField(new IntField('rgb_threshold', 0));
			$this->addField(new IntField('pixel_cutoff', 1));
			$this->addField(new IntField('base_color', null));
		}
		
		function execute($image, $request)
		{
			$margin = $this->fields['margin']->value;
			$rgb_threshold = $this->fields['rgb_threshold']->value;
			$pixel_cutoff = $this->fields['pixel_cutoff']->value;
			$base_color = $this->fields['base_color']->value;
			
			return $image->autocrop($margin, $rgb_threshold, $pixel_cutoff, $base_color);
		}
	}
?>