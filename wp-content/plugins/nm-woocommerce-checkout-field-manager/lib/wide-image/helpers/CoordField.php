<?php
	/**
	 * @package Demos
	 */
	class CoordField extends Field
	{
		function init($request)
		{
			$this->value = $request->getCoord($this->name, $this->default);
			if ($this->value > 1000)
				$this->value = 1000;
		}
	}
?>