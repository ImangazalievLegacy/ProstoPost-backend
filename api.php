<?php

error_reporting(E_ALL);

header("Content-type: text/html;charset=cp1251");

include('config.php');
include('lib/string_functions.php');

$SCRIPT_URL = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$SCRIPT_DIR = dirname($SCRIPT_URL);

$ROOT_FOLDER = dirname(__FILE__);

$error = false;

$act = $_GET['act'];

$act = strtolower($act);

if (!check_string($act))
{

	$result['result'] = $CODE_FALSE;
	$result['act'] = $ILLEGAL_CHARACTERS;
	$result['description'] = 'ACT parameter is invalid or contains illegal characters';

}

if ($act == '')
{

	$result['result'] = $CODE_FALSE;
	$result['act'] = $EMPTY_VALUE;
	$result['description'] = 'ACT parameter is empty';

}

//CAPTCHA

if ($act == 'get_captcha')
{

	include('captcha/captcha_gen.php');
	
	$result['result'] = $CODE_TRUE;
	
	$captcha = get_captcha();
	
	$result['captcha_url'] = $captcha['captcha_url'];
	$result['captcha_id'] = $captcha['captcha_id'];
	
	echo json_encode($result);
	
	exit;
}

if ($act == 'get_registration_status') 
{
	include('user/registration.php');
}

if ($act == 'register')
{
	include('lib/dbconnect.php');
	include('captcha/captcha_gen.php');
	include('user/registration.php');
}

if ($act == 'confirm_email')
{
	include('lib/dbconnect.php');
	include('user/registration.php');
}

if ($act == 'get_login_status') 
{
	include('user/login.php');
}

if ($act == 'login')
{
	include('lib/dbconnect.php');
	include('user/login.php');
}

if ($act == 'set_group_id')
{
	include('lib/dbconnect.php');
	include('lib/folders.php');
	include('user/profile.php');
}

if ($act == 'add_parser')
{
	include('lib/dbconnect.php');
	include('lib/folders.php');
	include('user/profile.php');
}

if ($act == 'delete_parser')
{
	include('lib/dbconnect.php');
	include('lib/folders.php');
	include('user/profile.php');
}

if ($act == 'get_user_parsers_list')
{
	include('lib/dbconnect.php');
	include('lib/folders.php');
	include('user/profile.php');
}

if ($act == 'get_vk_token')
{
  $result['result'] = $CODE_TRUE;
  $result['token'] = '';
  	  	
  $parameters = array('login', 'pass');
   
  if (!$error)
  {
	   for ($i = 0; $i < count($parameters); $i++)
	   {
      $key = $parameters[$i];
		
      if (empty($_POST[$key]))
      {
        $error = true;
        $result[$key] = $EMPTY_CODE;
      }
    }
  }
  
  if (!$error)
  {
    include('lib/requests.php');
    include('vk/vk.php');
    
    $login = $_POST['login'];
    $pass = $_POST['pass'];
  	
    $temp = GetToken($login, $pass);
    if (isset($temp['access_token']))
    {
      $result['token'] = $temp['access_token'];
      $result['user_id'] = $temp['user_id'];
    }
    else
    $result['result'] = $CODE_FALSE;
  }
    
    echo json_encode($result);
  
  exit;
}

if ($act == 'get_groups_list')
{
  include('lib/dbconnect.php');
  include('lib/requests.php');
  include('vk/vk.php');
}

if ($act == 'get_languages_list')
{
  include('lib/folders.php');
  include('lists.php');
}

if ($act == 'get_categories_list')
{
  include('lib/folders.php');
  
  if ($lang_folder == '') $error = true;
  
  include('lists.php');
}

if ($act == 'get_parsers_list')
{
	include('lib/folders.php');
  
	if ($lang_folder == '') $error = true;
	if ($category_folder == '') $error = true;
	  
	include('lib/dbconnect.php');
	include('lists.php');
}

if ($act == 'get_user_info')
{
	include('lib/dbconnect.php');
	include('user/profile.php');
}

if ($act == 'is_group_admin')
{

  include('lib/dbconnect.php');
  include('lib/requests.php');
  include('vk/vk.php');
}

if ($act == 'get_pages_list')
{
	include('lib/folders.php');

	if ($lang_folder == '') $error = true;
	if ($category_folder == '') $error = true;
	if ($parser_folder == '') $error = true;

	include('lists.php');

}

if ($act == 'saveindex')
{
	include('lib/folders.php');
	
	if ($lang_folder == '') $error = true;
	if ($category_folder == '') $error = true;
	if ($parser_folder == '') $error = true;
	
	$parser_name = $parser_folder;
	
	$parser_path = $PARSERS_FOLDER.'/'.$lang_folder.'/'.$category_folder.'/'.$parser_folder.'/'.$parser_name.'.php';
	
	include('lib/requests.php');
	include('lib/patterns.php');
	if (!$error) include($parser_path);

}

if ($act == 'savepage')
{
	include('lib/folders.php');
	
	if ($lang_folder == '') $error = true;
	if ($category_folder == '') $error = true;
	if ($parser_folder == '') $error = true;
	
	$parser_path = $PARSERS_FOLDER.'/'.$lang_folder.'/'.$category_folder.'/'.$parser_folder.'/4pda.php';
	
	include('lib/requests.php');
	include('lib/patterns.php');
	if (!$error) include($parser_path);

}

if ($act == 'resize')
{
	include('lib/folders.php');
	
	if ($lang_folder == '') $error = true;
	if ($category_folder == '') $error = true;
	if ($parser_folder == '') $error = true;

	if (!$error) include('handler.php');

}

if ($act == 'get_page_preview')
{
	include('lib/folders.php');
	
	if ($lang_folder == '') $error = true;
	if ($category_folder == '') $error = true;
	if ($parser_folder == '') $error = true;
	
	if (!$error) include('handler.php');

}

if ($act == 'get_page_content')
{
	include('lib/folders.php');
	
	if ($lang_folder == '') $error = true;
	if ($category_folder == '') $error = true;
	if ($parser_folder == '') $error = true;
	
	if (!$error) include('handler.php');

}

if ($act == 'vk_post')
{
	include('lib/folders.php');
	include('lib/requests.php');
	include('vk/vk.php');
	
	if ($lang_folder == '') $error = true;
	if ($category_folder == '') $error = true;
	if ($parser_folder == '') $error = true;
	
	if (!$error) include('handler.php');

}

$result['result'] = $CODE_FALSE;
$result['act'] = $CODE_UNKNOWN;
$result['description'] = 'Unknown ACT parameter';
echo json_encode($result);

?>