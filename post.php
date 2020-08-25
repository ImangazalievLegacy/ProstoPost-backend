<?php

$login = '89285436766';
$pass = 'qwerty1234';
$group_id = '73685349';
$album_id = '198717486';

function GET($url)
{
  $useragent='Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3';

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  $result=curl_exec($ch);
curl_close($ch);
  
  return $result;
}

function POST($url, $parameters)
{
  $useragent='Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3';

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
  $result=curl_exec($ch);
curl_close($ch);
  
  return $result;
}

function API_Request($method, $parameters)
{
//method - метод API
//parameters - ассоциативный массив или строка с параметрами

  $url = 'https://api.vkontakte.ru/method/'.$method;

  $response = POST($url, $parameters);

  return $response;

}

function GetToken()
{
	global 	$login, $pass;
	$url = 'https://oauth.vk.com/token?grant_type=password&client_id=2274003&client_secret=hHbZxrka2uZ6jB1inYsH&username='.$login.'&password='.$pass.'&captcha_key=&captcha_sid=';

	$response = GET($url);

	$temp = json_decode($response, true);
	
	return $temp['access_token'];
}

 $parameters = array(
'access_token'=>$token,
'group_id'=>$group_id,
'album_id'=>'198717486',
);

function VK_upload_photo($token, $photos, $caption)
{
	global $group_id, $album_id;
	
	$parameters = array(
'access_token'=>$token,
'group_id'=>$group_id,
'album_id'=>'198717486',
);

	$response = API_Request('photos.getUploadServer', $parameters);
	
	$temp = json_decode($response, true);
  
	$upload_url = $temp['response']['upload_url'];
	$album_id = $temp['response']['aid'];
	$user_id = $temp['response']['mid'];
	
	$files_num = count($photos);
	
	for ($i = 1; $i <= $files_num; $i++)
	{
		$files['file'.$i] = '@'.$photos[$i];
	}
	
	$ch = curl_init($upload_url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $files); 
	$response = curl_exec($ch);

	$temp = json_decode($response, true);
	
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
	
	$files_num = count($temp['response']);
	
	for ($i = 0; $i < $files_num; $i++)
	{
		$uploaded_photos[] = 'photo'.$temp['response'][$i]['owner_id'].'_'.$temp['response'][$i]['pid'];
		
	}
	
	$uploaded_photos = implode(', ', $uploaded_photos);
	
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
	  
	echo $response;
}

function VK_create_wiki_page($token, $title, $text)
{
	global $group_id;
	
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

	$page_id = $temp['response'];
	
	return  "http://vk.com/page-".$group_id."_".$page_id;
}


?>