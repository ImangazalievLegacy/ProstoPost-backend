<?php

$CAPTCHA_FOLDER = 'captcha_images';
$CAPTCHA_CASE_SENSETIVE = false;

function get_captcha()
{
	global $CAPTCHA_FOLDER, $CAPTCHA_CASE_SENSETIVE, $SCRIPT_DIR;
	include('captcha.php');
	
	$error = false;
  
	if (!$error){
	
		if (!file_exists($CAPTCHA_FOLDER))
		
		if (mkdir($CAPTCHA_FOLDER) === false) $error = true;
		
		if (!file_exists($CAPTCHA_FOLDER.'/images'))
		if (mkdir($CAPTCHA_FOLDER.'/images') === false) $error = true;
		
		if (!file_exists($CAPTCHA_FOLDER.'/codes'))
		if (mkdir($CAPTCHA_FOLDER.'/codes') === false) $error = true;

		if (isset($_POST['last_captcha_id']))
		{
		
			$last_captcha_id = $_POST['last_captcha_id'];
		
			try
			{
				if (file_exists($CAPTCHA_FOLDER.'/images/'.$last_captcha_id.'.png')) unlink($CAPTCHA_FOLDER.'/images/'.$last_captcha_id.'.png');
				if (file_exists($CAPTCHA_FOLDER.'/codes/'.$last_captcha_id.'.txt')) unlink($CAPTCHA_FOLDER.'/codes/'.$last_captcha_id.'.txt');
			} catch (Exception $e){
			
			}
		}
		
	}
		
	if (!$error){

		$captcha_code = random_string(4);
	  
		$captcha_gen = new captcha;
	 
		$im = $captcha_gen -> generate_captcha($captcha_code);

		$captcha_id = md5(mt_rand());
	  
		imagepng($im, $CAPTCHA_FOLDER.'/images/'.$captcha_id.'.png');
	  
		imagedestroy($im);
	  
		$captcha_url = $SCRIPT_DIR.'/'.$CAPTCHA_FOLDER.'/images/'.$captcha_id.'.png';
	  
		if (file_put_contents($CAPTCHA_FOLDER.'/codes/'.$captcha_id.'.txt', $captcha_code) === false) $error = true;
	}
	
	if ($error)
	return false;
	else
	return array('captcha_url' => $captcha_url, 'captcha_id' => $captcha_id);

}

function check_captcha($captcha_id, $check_code)
{

	global $FILE_NOT_FOUND_CODE, $CAPTCHA_CASE_SENSETIVE, $CODE_TRUE, $CODE_FALSE, $CAPTCHA_FOLDER;

	if (!file_exists($CAPTCHA_FOLDER.'/codes/'.$captcha_id.'.txt'))
	{
		$result[0] = false;
		$result[1] = $FILE_NOT_FOUND_CODE;
		return $result;
	}
  
	$captcha_code = file_get_contents($CAPTCHA_FOLDER.'/codes/'.$captcha_id.'.txt');
  
	if (!$CAPTCHA_CASE_SENSETIVE)
	{
		$captcha_code = strtolower($captcha_code);
		$check_code = strtolower($check_code);
	}
  
	if ($check_code == $captcha_code)
	{
		$result[0] = true;
		$result[1] = $CODE_TRUE;
	}
	else
	{
		$result[0] = false;
		$result[1] = $CODE_FALSE;
	}  
  
	try
	{
		if (file_exists($CAPTCHA_FOLDER.'/images/'.$captcha_id.'.png')) unlink($CAPTCHA_FOLDER.'/images/'.$captcha_id.'.png');
		if (file_exists($CAPTCHA_FOLDER.'/codes/'.$captcha_id.'.txt')) unlink($CAPTCHA_FOLDER.'/codes/'.$captcha_id.'.txt');
	} catch (Exception $e){
			
	}
  
	return $result;

}

?>