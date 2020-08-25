<?php

function API_Request($method, $parameters)
{
//method - метод API
//parameters - ассоциативный массив или строка с параметрами

  $url = 'https://api.vkontakte.ru/method/'.$method;

  $response = POST($url, $parameters);

  return $response;

}

function GetToken($login, $pass)
{
	$url = 'https://oauth.vk.com/token?grant_type=password&client_id=2274003&client_secret=hHbZxrka2uZ6jB1inYsH&username='.$login.'&password='.$pass.'&captcha_key=&captcha_sid=';
	
	$response = GET($url);

	$temp = json_decode($response, true);
	
	if (isset($temp['access_token']))
	return $temp['access_token'];
	else
	return false;
}

function VK_upload_photos($token, $photos, $caption)
{
	global $group_id, $album_id;
	
	$error = false;
	
	if (!$error)
	{
		if (count($photos) > 0)
		{
			$parameters = array(
			'access_token'=>$token,
			'group_id'=>$group_id,
			'album_id'=>'198717486',
			);

			$response = API_Request('photos.getUploadServer', $parameters);
			
			$temp = json_decode($response, true);
			
			if (!isset($temp['response'])) $error = true;
		
		}
	}
	
	if (!$error)
	{
		$upload_url = $temp['response']['upload_url'];
		$album_id = $temp['response']['aid'];
		$user_id = $temp['response']['mid'];
		
		$files_num = count($photos);
		
		for ($i = 0; $i < $files_num; $i++)
		{
			$files['file'.($i+1)] = '@'.$photos[$i];
		}
		
		$useragent = 'Mozilla/5.0 (compatible; Windows NT 5.1; DE; RV: 1.8.1.8) Gecko/20071008 SeaMonkey/1.0';
		
		$ch = curl_init($upload_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $files); 
		$response = curl_exec($ch);

		$temp = json_decode($response, true);
		
		if (!isset($temp['photos_list'])) $error = true;
		
	}
	
	if (!$error)
	{
		
		$caption = iconv('WINDOWS-1251', 'UTF-8', $caption);

		$parameters = array(
		'access_token'=>$token,
		'album_id'=>$album_id,
		'group_id'=>$group_id,
		'hash'=>$temp['hash'],
		'server'=>$temp['server'],
		'caption'=>$caption,
		'photos_list'=>$temp['photos_list']
		);

		$response = API_Request('photos.save', $parameters);
	  
		$temp = json_decode($response, true);
		
		if (!isset($temp['response'])) $error = true;
	}
	
	if (!$error)
	{
		$files_num = count($temp['response']);
		
		for ($i = 0; $i < $files_num; $i++)
		{
			$uploaded_photos[] = 'photo'.$temp['response'][$i]['owner_id'].'_'.$temp['response'][$i]['pid'];
			
		}
	
	}
	
	if ($error)
	return false;
	else
	return $uploaded_photos;
}

function VK_post($token, $message, $attachments)
{
	global $group_id;

	$message = iconv('WINDOWS-1251', 'UTF-8', $message);

	$parameters = array(
	'access_token'=>$token,
	'message'=>$message,
	'owner_id'=>'-'.$group_id,
	'from_group'=>'1',
	'attachments'=>$attachments,
	);

	$response = API_Request('wall.post', $parameters);
	
	$response = json_decode($response, true);

	$result = isset($response['response']['post_id']) ? true : false;
	
	return $result;
}

function VK_create_wiki_page($token, $title, $text)
{
	global $group_id;
	
	$error = false;
	
	if (!$error)
	{
	
		$title = iconv('WINDOWS-1251', 'UTF-8', $title);
	  
		$text = iconv('WINDOWS-1251', 'UTF-8', $text);

		$parameters = array(
		'access_token'=>$token,
		'gid'=>$group_id,
		'title'=>$title,
		'Text'=>$text
		);

		$response = API_Request('pages.save', $parameters);

		$temp = json_decode($response, true);

		if (!isset($temp['response'])) $error = true;
	}
	if (!$error)
	{
		$page_id = $temp['response'];
	}
	
	if ($error)
	return false;
	else
	return  "http://vk.com/page-".$group_id."_".$page_id;
}

if ($act == 'get_groups_list')
{

	$result['result'] = $CODE_TRUE;

	$token = $_POST['token'];
	$vk_token = $_POST['vk_token'];
	$vk_user_id = $_POST['vk_user_id'];
  
	if (
	!check_string($token) or
	!check_string($vk_token) or
	!check_string($vk_user_id)
	)
	{
		$error = true;
	}
  
  if (!$error)
  {
	$parameters = array(
	'access_token'=>$vk_token,
	'user_id'=>$vk_user_id,
	'filter'=>'admin'
	);
    
    $response = API_Request('groups.get', $parameters);
	
    $temp = json_decode($response, true);
	
    if (isset($temp['response']))
    $group_ids = implode(',', $temp['response']);
    else
    $error = true;
   
  }  
  
  if (!$error)
  {
	$parameters = array(
	'group_ids'=> $group_ids
	);
    
    $response = API_Request('groups.getById', $parameters);
   
    $temp = json_decode($response, true);
    
    if (isset($temp['response']))
    $result['response'] = $temp['response'];
    else
    $error = true;
	}  
  
	if ($error) $result['result'] = $CODE_FALSE;
  
	echo json_encode($result);
  
	exit;

}

if ($act == 'is_group_admin')
{
  
	$result['result'] = $CODE_TRUE;
	$error = false;

	$token = $_POST['token'];
	$vk_token = $_POST['vk_token'];
	$vk_user_id = $_POST['vk_user_id'];
	$group_id= $_POST['group_id'];
  
	if (
	!check_string($token) or
	!check_string($vk_token) or
	!check_string($vk_user_id or
	!check_string($group_id))
	)
	{
		$error = true;
	}
  
	if (!$error)
	{
		$parameters = array(
		'access_token'=>$vk_token,
		'user_id'=>$vk_user_id,
		'filter'=>'admin'
		);
			
		$response = API_Request('groups.get', $parameters);
			
		$temp = json_decode($response, true);
		if (!isset($temp['response']))
		$error = true;
   
	}  
  
	if (!$error)
	{
  
		if (!in_array($group_id, $temp['response'])) $error = true;
  
	}
  
	if ($error) $result['result'] = $CODE_FALSE;
  
	echo json_encode($result);
  
	exit;

}

?>