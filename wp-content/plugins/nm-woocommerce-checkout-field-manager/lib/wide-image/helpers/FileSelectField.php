<?php
	/**
	 * @package Demos
	 */
	class FileSelectField extends Field
	{
		public $request;
		public $files = array();
		public $options;
		
		function __construct($name, $path, $options = array())
		{
			$this->name = $name;
			$this->path = $path;
			$this->options = $options;
			
			if (!isset($options['show']))
				$this->options['show'] = true;
			
			if (!isset($options['pattern']))
				$this->options['pattern'] = '/(.*)/';
		}
		
		function init($request)
		{
			$this->value = null;
			$di = new DirectoryIterator(DEMO_PATH . $this->path);
			foreach ($di as $file)
				if (!$file->isDot() && strpos($file->getFilename(), '.') !== 0 && preg_match($this->options['pattern'], $file->getFilename()))
				{
					$this->files[] = $file->getFilename();
					if ($this->value === null && isset($this->options['default']) && $this->options['default'] == $file->getFilename())
						$this->value = $this->options['default'];
					
					if ($this->request->get($this->name) == $file->getFilename())
						$this->value = $file->getFilename();
				}
			
			sort($this->files);
			
			if (!$this->value && count($this->files) > 0)
				$this->value = $this->files[0];
		}
		
		function render()
		{
			if ($this->options['show'])
			{
				$onch = "document.getElementById('sel_{$this->name}').src = '{$this->path}/' + this.options[this.selectedIndex].value;";
			}
			else
				$onch = '';
			
			echo '<select name="' . $this->name . '" onchange="' . $onch . '">';
			$selected_file = null;
			foreach ($this->files as $file)
			{
				if ($this->value == $file)
				{
					$sel = 'selected="selected"';
					$selected_file = $file;
				}
				else
					$sel = '';
				
				echo '<option ' . $sel . ' value="' . $file . '">' . $file . '</option>';
			}
			echo '</select>';
			
			if ($this->options['show'] && $selected_file)
				echo '<img id="sel_' . $this->name . '" width="40" src="' . $this->path . '/' . $selected_file . '" /> ';
		}
		
		function getURLValue()
		{
			return $this->name . '=' . $this->value;
		}
	}
?>