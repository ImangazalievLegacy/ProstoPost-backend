<?php

class captcha {
  
	var $CAPTCHA_WIDTH = 200;
	var $CAPTCHA_HEIGHT = 70;

	var $CAPTCHA_FONT = 'fonts/8080.ttf';

	var $CAPTCHA_BACKGROUND_COLOR = array(0, 0, 0);

	var $MIN_LETTER_ANGLE = -45;
	var $MAX_LETTER_ANGLE = 45;

	var $RANDOM_FONT_COLOR = true;

	var $FONT_COLOR = array(255, 255, 255);

	var $MIN_FONT_SIZE = 25;
	var $MAX_FONT_SIZE = 50;

	var $RANDOM_FONT_SIZE = true;

	var $FONT_SIZE = 16;

	var $TRANSPARENT_BACKGROUND = true;

	var $RANDOM_LINES = false;

	var $LINES_NUM = 5;
  
	function __construct()
	{
		$this -> r = $this -> CAPTCHA_BACKGROUND_COLOR[0];
		$this -> g = $this -> CAPTCHA_BACKGROUND_COLOR[1];
		$this -> b = $this -> CAPTCHA_BACKGROUND_COLOR[2];
	}
  
	function set_bg_color(int $r, int $g, int $b)
	{
		$this -> r = $r;
		$this -> g = $g;
		$this -> b = $b;
  
	}
	
	function set_bg_transparent(boolean $transparent)
	{
		$this -> TRANSPARENT_BACKGROUND = $transparent;
	}

	function generate_captcha($captcha_text)
	{

		$captcha_image = imagecreatetruecolor ($this -> CAPTCHA_WIDTH, $this -> CAPTCHA_HEIGHT);
	 
		$r = $this -> r;
		$g = $this -> g;
		$b = $this -> b;
	  
		if ($this -> TRANSPARENT_BACKGROUND)
		{
	  
			imagealphablending( $captcha_image, false );
			imagesavealpha($captcha_image, true);

			$background_color = imagecolorallocatealpha($captcha_image, $r, $g, $b, 127);
			imagefill($captcha_image, 0, 0, $background_color);
		
			imagealphablending( $captcha_image, true );
	  
		}
		else
		{
			$background_color = imagecolorallocate($captcha_image, $r, $g, $b);
			imagefill($captcha_image, 0, 0, $background_color);  
		}
	  
		if ($this -> RANDOM_LINES)
		{
			for ($i=0; $i< $this -> LINES_NUM; $i++)
			{
				$line_color = imagecolorallocate($captcha_image, rand(0, 255), rand(0, 255), rand(0, 255)); // Случайный цвет c изображения 
			
				imageline($captcha_image,
				rand(0, $this -> CAPTCHA_WIDTH),
				rand(0, $this -> CAPTCHA_HEIGHT),
				rand(0, $this -> CAPTCHA_HEIGHT),
				rand(0, $this -> CAPTCHA_WIDTH), 
				$line_color);
			}
		}
	   
		$x = 20;

		$captcha_length = strlen($captcha_text);

		for ($i = 0; $i < $captcha_length; $i++)
		{
	  
			$letter = $captcha_text[$i];

			if ($this -> RANDOM_FONT_SIZE)  
			$font_size = mt_rand($this -> MIN_FONT_SIZE, $this -> MAX_FONT_SIZE);
			else
			$font_size = $this -> FONT_SIZE;
	  
			if ($this -> RANDOM_FONT_COLOR)
			{
				$r = mt_rand(0, 255);
				$g = mt_rand(0, 255);
				$b = mt_rand(0, 255);
				
				$captcha_font_color = imagecolorallocate ($captcha_image, $r, $g, $b);
			}
			else
			{
				$r = $this -> FONT_COLOR[0];
				$g = $this -> FONT_COLOR[1];
				$b = $this -> FONT_COLOR[2];
		
				$captcha_font_color = imagecolorallocate ($captcha_image, $r, $g, $b);
			}
	  
			$y = $font_size;
		  
			$letter_angle = mt_rand($this -> MIN_LETTER_ANGLE, $this -> MAX_LETTER_ANGLE); 

			imagettftext ($captcha_image, $font_size, $letter_angle, $x, $y, $captcha_font_color, $this -> CAPTCHA_FONT, $letter);
	  
			$x = $x + $font_size;
		}

		return $captcha_image;

		}
	}

?>