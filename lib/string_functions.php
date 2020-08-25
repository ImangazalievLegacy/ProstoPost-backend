<?php

function check_string($str)
{
	return preg_match("/^([a-zA-Z0-9_\-\.]*)$/", $str);
}

function file2array($filename)
{
  
  $data = file_get_contents($filename); 
  $lines = explode(PHP_EOL, $data);
  return $lines;
  
}

function check_array($array)
{

	foreach ($array as $value)
	{
		if (!preg_match("/^([a-zA-Z0-9_\-\.]*)$/", $value))
		{
			return false;
			break;
		}
	}
}

function checkmail($str)
{
	return filter_var($str, FILTER_VALIDATE_EMAIL) && preg_match("/^([a-zA-Z0-9@_\-\.]*)$/", $str);
}

function random_char()
{

  $EN = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $en = 'abcdefghijklmnopqrstuvwxyz';
  $num = '123456789';

  $letters = $EN.$num;

  $r_char = $letters[rand(0, strlen($letters)-1)];
 
  return $r_char;

}

function random_string($length = 32)
{

  $r_str = '';
  
  for ($i = 0; $i < $length; $i++)
  {
  
    $r_str.= random_char();
  
  }
 
  return $r_str;

}

function GetFileExt($filename)
{
	$fn = explode(".", $filename);
	return end($fn);
}

?>