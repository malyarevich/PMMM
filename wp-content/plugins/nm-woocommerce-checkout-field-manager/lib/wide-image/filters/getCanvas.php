<?php
	/**
	 * @package Demos
	 */
	class Demo_getCanvas extends Demo
	{
		public $order = 1300;
		
		function init()
		{
			$this->addField(new Field('text', 'Hello world!'));
			$this->addField(new IntField('angle', 30));
			$this->addField(new FileSelectField('font', 'fonts', array('show' => false, 'pattern' => '/(.*)\.ttf$/')));
			$this->addField(new IntField('size', 18));
		}
		
		function execute($image, $request)
		{
			$text = $this->fields['text']->value;
			$angle = $this->fields['angle']->value;
			$font = $this->fields['font']->value;
			$font_size = $this->fields['size']->value;
			
			$canvas = $image->getCanvas();
			$font_file = DEMO_PATH . 'fonts/' . $font;
			$canvas->setFont(new WideImage_Font_TTF($font_file, $font_size, $image->allocateColor(0, 0, 0)));
			$canvas->writeText(11, 51, $text, $angle);
			
			$canvas->setFont(new WideImage_Font_TTF($font_file, $font_size, $image->allocateColor(200, 220, 255)));
			$canvas->writeText(10, 50, $text, $angle);
			
			$canvas->filledRectangle(10, 10, 80, 40, $image->allocateColor(255, 127, 255));
			$canvas->line(60, 80, 30, 100, $image->allocateColor(255, 0, 0));
			
			return $image;
		}
		
		function text()
		{
			echo "This demo also executes:
<pre>
	\$canvas->filledRectangle(10, 10, 80, 40, \$img->allocateColor(255, 127, 255));
	\$canvas->line(60, 80, 30, 100, \$img->allocateColor(255, 0, 0));
</pre>";
		}
	}
?>