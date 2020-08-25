<?php

function get_page_preview()
{
	global $SAVED_DATA_FOLDER_PATH, $SAVED_INDEX_PAGE_FOLDER, $SAVED_DATA_FOLDER, $error;
	
	if (!$error)
	{
		if (isset($_GET['pid'])) $page_id = $_GET['pid'];
		else
		$error = true;
	}

	if (!$error)
	{
		
		$PREVIEWS_FOLDER_PATH = $SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_INDEX_PAGE_FOLDER.'/';
			
		$PREVIEW_FILE_PATH = $PREVIEWS_FOLDER_PATH.$page_id.'.txt';
		
		if (!file_exists($PREVIEW_FILE_PATH))
		{
			$result = false;
		}
		else
		{
			 $result = file_get_contents($PREVIEW_FILE_PATH);
 		}
	
	}
	
	if ($error)$result = false;
	
	return $result;
}

function get_page_content()
{
	global $SAVED_DATA_FOLDER_PATH, $SAVED_PAGES_FOLDER, $SAVED_DATA_FOLDER, $SCRIPT_DIR, $error;
	
	if (!$error)
	{
		if (isset($_GET['pid'])) $page_id = $_GET['pid'];
		else
		$error = true;
	}

	if (!$error)
	{
		
		$PAGE_FOLDER_PATH = $SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_PAGES_FOLDER.'/'.$page_id.'/';
			
		$PAGE_FILE_PATH = $PAGE_FOLDER_PATH.$page_id.'.txt';
		
		if (!file_exists($PAGE_FILE_PATH))
		{
			$result = false;
		}
		else
		{
			 $result = file_get_contents($PAGE_FILE_PATH);
 		}
	
	}
	
	if ($error)$result = false;
	
	return $result;
}

function prepare_wiki_page($page)
{

	$n = count($page['response']['content']);
	
	$wiki_page = '';
	
	for ($i = 0; $i < $n; $i++)
	{
		if ($page['response']['content'][$i]['type'] == 'text') 
		{
		
			if ($wiki_page == '') $wiki_page = $page['response']['content'][$i]['text'];
			else
			$wiki_page.= "\n\n".$page['response']['content'][$i]['text'];
		
		}
		
		if ($page['response']['content'][$i]['type'] == 'image') 
		{
		
			if ($wiki_page == '') $wiki_page = '[['.$page['response']['content'][$i]['url'].'|noborder|'.$page['response']['content'][$i]['href'].']]';
			else
			$wiki_page.= "\n\n".'[['.$page['response']['content'][$i]['url'].'|noborder|'.$page['response']['content'][$i]['href'].']]';
		
		}
	}
	
	return $wiki_page;

}

if ($act == 'resize')
{
	
	if (!$error)
	{
		if (isset($_GET['pid'])) $page_id = $_GET['pid'];
		else
		$error = true;
	}

	if (!$error)
	{
	
		$PAGE_FOLDER_PATH = $SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_PAGES_FOLDER.'/'.$page_id;
	
		if (!file_exists($PAGE_FOLDER_PATH.'/'.$IMAGES_FOLDER)) mkdir($PAGE_FOLDER_PATH.'/'.$IMAGES_FOLDER, 0777, true);
	  
		if (!file_exists($PAGE_FOLDER_PATH.'/'.$OPTIMIZED_IMAGES_FOLDER)) mkdir($PAGE_FOLDER_PATH.'/'.$OPTIMIZED_IMAGES_FOLDER, 0777, true);

	}
	
	if (!$error)
	{
		$images_list = scandir($PAGE_FOLDER_PATH.'/'.$IMAGES_FOLDER);
	  
		$n = count($images_list);
	  
		for ($i = 0; $i < $n; $i++)
		{
	  
			$filename = $images_list[$i];
	  
			if ($filename == '..' or $filename == '.') continue;
	  
			$filepath = $PAGE_FOLDER_PATH.'/'.$IMAGES_FOLDER.'/'.$filename;
	  
			if (!file_exists($PAGE_FOLDER_PATH.'/'.$OPTIMIZED_IMAGES_FOLDER.'/'.$filename))
			{
	  
				$ext = GetFileExt($filename);
				
				$ext = strtolower($ext);
				
				$image = null;
	  
				if ($ext == 'png')
				$image = imageCreateFromPNG($filepath);
				
				if ($ext == 'jpg' or $ext == 'jpeg')
				$image = @imagecreatefromjpeg($filepath);
	  
				if ($ext == 'gif')
				$image = imagecreatefromgif($filepath);
				
				if ($image == null) continue;
	  
				list($width, $height) = getimagesize($filepath);
	  
				if ($width > $MIN_IMAGE_WIDTH and $height > $MIN_IMAGE_HEIGHT)
				{
	  
					$new_width = $width * $SCALING_PERCENT;
					$new_height = $height * $SCALING_PERCENT;
	  
					$dst_image = imagecreatetruecolor($new_width, $new_height);
	  
					if ($SCALING_PERCENT = 1)
					{
					
						$dst_image = $image;
					
					}
					else
					{
						imagecopyresampled($dst_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	 
						imagecopyresampled($image, $dst_image, 0, 0, 0, 0, $width, $height, $new_width, $new_height);
					}
	  
				}
	  
				$filename = explode('.', $filename);
				$filename = $filename[0];
				$filename.='.jpg';
	  
				imagejpeg($dst_image, $PAGE_FOLDER_PATH.'/'.$OPTIMIZED_IMAGES_FOLDER.'/'.$filename, $IMAGE_QUALITY);

				imagedestroy($image);
				imagedestroy($dst_image);
	  
			}
	  
		}
	
	}
	
	if ($error)
	$result['result'] = $CODE_FALSE;
	else
	$result['result'] = $CODE_TRUE;
		
	echo json_encode($result);
	
	exit;
}

if ($act == 'get_page_preview')
{
	$preview =  get_page_preview();
	
	if ($preview == false)
	$result['result'] = $CODE_FALSE;
	else
	{
		$result['result'] = $CODE_TRUE;
		$result['response'] = iconv('windows-1251', 'UTF-8', $preview);
	}	
	
	$result =  json_encode($result, JSON_UNESCAPED_UNICODE);
	
	$result = iconv('UTF-8', 'windows-1251', $result);
	
	echo $result;
	
	exit;
}

if ($act == 'get_page_content')
{
	
	$result['result'] = $CODE_TRUE;
	
	$mode = isset($_GET['mode'])? $_GET['mode']:'normal';

	$content =  get_page_content();
	
	if ($content === false)
	{
		$result['result'] = $CODE_FALSE;
		
	}
	else
	{	
		
		if (strtolower($mode) == 'mobile')
		{
			$result['response'] = $content;			
		}
		else
		$result['response'] = $content;
	}
	
	echo json_encode($result);
	
	exit;
}

if ($act == 'vk_post')
{
	if (!$error)
	{
		
		$login = '89285436766';
		$pass = 'qwerty12345';
		$group_id = '73685349';
		$album_id = '198717486';
		
		$message = get_page_preview();
	
		$wiki_page = get_page_content();
		
		if ($message == false or $wiki_page == false) $error = true;
		
		if (!file_exists($PAGES_LIST_FILE)) $error = true;
		
		if (isset($_GET['pid']))
		$pid = $_GET['pid'];
		else
		$error = true;
		
		$token = GetToken($login, $pass);
		
		if ($token === false) $error = true;
	}
	
	if (!$error)
	{
		$pages = file($PAGES_LIST_FILE);
		
		for ($i = 0; $i < count($pages); $i++)
		{
			$page = explode($PARAMS_DELIMITER, $pages[$i]);
			$page_id = $page[0];
			$page_url = $page[1];
			$page_title = $page[2];
			$page_poster = $page[3];
					
			if ($pid == $page_id) break;
		}
		
		$attachments = '';
		
		$wiki_page = iconv('windows-1251', 'UTF-8', $wiki_page);
		
		$wiki_page = json_decode($wiki_page, true);
		
		$photos[0] = $wiki_page['response']['poster']['path'];
		
		$n = count($wiki_page['response']['content']);
		
		for ($i = 0; $i < $n; $i++)
		{
			
			if ($wiki_page['response']['content'][$i]['type'] == 'image')
			$photos[] = $wiki_page['response']['content'][$i]['path'];
		
		}
		
		$wiki_page = prepare_wiki_page($wiki_page, $token, $page_title);
		
		$wiki_page = iconv('UTF-8', 'windows-1251', $wiki_page);
		
		$uploaded_photos = VK_upload_photos($token, $photos, $page_title);
		
		if ($uploaded_photos === false) $error = true;
	}
	
	if (!$error)
	{
	
		$attachments = implode(', ', $uploaded_photos);
		
		$wiki_page_url = VK_create_wiki_page($token, $page_title, $wiki_page);
		
		if ($wiki_page_url === false) $error = true;
	
	}
	
	if (!$error)
	{
	
		if ($attachments == '')
		$attachments = $wiki_page_url;
		else
		$attachments.= ','.$wiki_page_url;
		
		if (!VK_post($token, $message, $attachments)) $error = true;
	}
	
	
	if ($error)
	$result['result'] = $CODE_FALSE;
	else
	$result['result'] = $CODE_TRUE;
		
	echo json_encode($result);
	
	exit;
	
}

?>