<?php
	/**
	 * @package Demos
	 */
	class IntField extends Field
	{
		function init($request)
		{
			$this->value = $request->getInt($this->name, $this->default);
		}
	}
?>