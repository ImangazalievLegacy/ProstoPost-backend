<?php

	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	if ($act == 'saveindex')
	{
	
		if (!$error)
		{
		
			if (!file_exists($SAVED_DATA_FOLDER_PATH))
			if (!mkdir(dirname($SAVED_DATA_FOLDER_PATH), 0777, true)) $error = true;
			
			if (!file_exists($SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_INDEX_PAGE_FOLDER)) 
			if (!mkdir($SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_INDEX_PAGE_FOLDER, 0777, true)) $error = true;
			
			if (!file_exists($SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_INDEX_PAGE_FOLDER.'/'.$IMAGES_FOLDER))
			if (!mkdir($SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_INDEX_PAGE_FOLDER.'/'.$IMAGES_FOLDER, 0777, true)) $error = true;
			
			if (!file_exists($SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_PAGES_FOLDER))
			if (!mkdir($SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_PAGES_FOLDER, 0777, true)) $error = true;
		
		}
		
		if (!$error)
		{
		
			$current_time = microtime_float();
		
			if (file_exists($SAVED_DATA_FOLDER_PATH.$LAST_REQUEST_TIME_FILE))
			$last_request_time = (int) file_get_contents($SAVED_DATA_FOLDER_PATH.$LAST_REQUEST_TIME_FILE);
			else
			{
				$last_request_time = 0;
				if (file_put_contents($SAVED_DATA_FOLDER_PATH.$LAST_REQUEST_TIME_FILE, $current_time) === false) $error = true;
			}
			
			echo 'last_request_time '.$last_request_time.'<br>LAST_REQUEST_TIME_FILE '.$LAST_REQUEST_TIME_FILE.'<br>current_time'.$current_time.'<br>';

			if (($current_time - $last_request_time) < $REQUEST_PERIOD)
			{
				echo 'not';
				exit;
			}
			else
			{
				echo 'refresh';
				if (file_put_contents($SAVED_DATA_FOLDER_PATH.$LAST_REQUEST_TIME_FILE, $current_time) === false) $error = true;
			}
			
		}
		
		if (!$error)
		{
			$mode = isset($_GET['mode'])? $_GET['mode']:$INDEX_PAGE_WRITE_MODE;

			$page_url = 'http://4pda.ru';

			$page = GET($page_url);

			if(!preg_match_all($title_pattern, $page, $title_matches)) $error = true;
			if(!preg_match_all($poster_pattern, $page, $poster_matches)) $error = true;
			if(!preg_match_all($description_pattern, $page, $description_matches)) $error = true;

		}
		
		if (!$error)
		{
		
			if (!file_exists($PAGES_LIST_FILE)) touch($PAGES_LIST_FILE);
			$pages_list = file_get_contents($PAGES_LIST_FILE);
			
			if ($pages_list === false) $error = true;
		
			if ($mode == 'append')
			{
				if (!file_exists($SAVED_DATA_FOLDER_PATH.$LAST_PAGE_URL_FILE)) touch($SAVED_DATA_FOLDER_PATH.$LAST_PAGE_URL_FILE);
				
				$last_link = file_get_contents($SAVED_DATA_FOLDER_PATH.$LAST_PAGE_URL_FILE);
				$last_link = trim($last_link);
				
				if (($last_link === false)) $error = true;
			}
		
		}
		
		if (!$error)
		{
		
			$new_pages_list = '';
			
			$n = count($title_matches[0]);
			
			$result['pages_num'] = $n;
			
			for($i = 0; $i < $n; $i++)
			{
		
				$link = $page_url.$title_matches[1][$i];
				
				if ($mode == 'append')
				{
					if ($link == $last_link)
					{
						break;
					}
				}
				
				$title = $title_matches[2][$i];
				$title = htmlspecialchars_decode($title);
				
				$image_url = $poster_matches[1][$i];
				$image_path = $SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_INDEX_PAGE_FOLDER.'/'.$IMAGES_FOLDER.'/'.basename($image_url);
				$image = file_get_contents($image_url);
				
				if (($image === false)) $error = true;
				
				if (!file_exists($image_path)) 
				{
					if (file_put_contents($image_path, $image) === false) $error = true;
				}
				
				$description = $description_matches[1][$i];
				$description = strip_tags($description);
				$description = trim($description);

				$page_id = date('Ymd').mt_rand();
				
				if (file_put_contents($SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_INDEX_PAGE_FOLDER.'/'.$page_id.'.txt', $description)===FALSE) $error = true;
				
				$new_pages_list = $new_pages_list.$page_id.'|'.$link.'|'.$title.'|'.$image_path.'|EMPTY'."\n";
				
				if ($i == 0)
				{
					if (file_put_contents($SAVED_DATA_FOLDER_PATH.$LAST_PAGE_URL_FILE, $link)===FALSE) $error = true;
				}
				
				
			}
			
			unset($title_matches);
			unset($poster_matches);
			unset($description_matches);
			
			if ($new_pages_list != '') $pages_list = $new_pages_list.$pages_list;
			
			print_r($new_pages_list);
			
			if (file_put_contents($PAGES_LIST_FILE, $pages_list)===FALSE) $error = true;
		}
	
		if ($error)
		$result['result'] = $CODE_FALSE;
		else
		$result['result'] = $CODE_TRUE;
		
		echo json_encode($result);

		exit;
	
	}


	if ($act == 'savepage')
	{
		
		if (!$error)
		{
			if ( isset($_GET['pid']) ) $id = $_GET['pid'];
			else
			$error = true;

			$pages = file($PAGES_LIST_FILE);
			
			if ($pages === FALSE) $error = true;
			
			$page_url = '';
			
			$n = count($pages);
			
		}
		
		if (!$error)
		{
			for ($i = 0; $i<$n; $i++)
			{
				$info = explode('|', $pages[$i]);
				$page_id = $info[0];

				if ($page_id == $id)
				{
					$page_url = $info[1];
					$page_title = $info[2];
					$page_poster = $info[3];
					$page_saved = $info[4];
					unset($info);
					
					$result['response']['poster']['path'] = $page_poster;
					$result['response']['poster']['url'] = $SCRIPT_DIR.'/'.$page_poster;
					
					if (strtoupper($page_saved) == 'SAVED')
					{
						echo 'SAVED<br>Text:<br>';
						echo file_get_contents($SAVED_DATA_FOLDER_PATH.'/'.$SAVED_DATA_FOLDER.'/'.$SAVED_PAGES_FOLDER.'/'.$page_id.'/'.$page_id.'.txt');
						exit;
					}
					else
					{
						break;
					}
				} 
			}
		
		}
		
		if (!$error)
		{
		
			if ($page_url == '')
			{
				$error = true;
			}
			else
			{
			
				$pages[$i] = $page_id.'|'.$page_url.'|'.$page_title.'|'.$page_poster.'|SAVED'."\n";
				
				//Сохраняем массив в файл
				
				if (file_put_contents($PAGES_LIST_FILE, $pages)===FALSE) $error = true;
			}
		}
		
		if (!$error)
		{
		
			$page = GET($page_url);
			
			$title_pattern = '/<h1 itemprop="name">(.*?)<\/h1>/is';
			$content_pattern = '/\"\/>.*?<p style="text-align: justify;".*?>(.*?)<\/div><\/div><\/div>/is';

			if(!preg_match($title_pattern, $page, $title_matches)) $error = true;
			if(!preg_match($content_pattern, $page, $content_matches)) $error = true;

		}	
			
		if (!$error)
		{		
			if (!file_exists($SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_PAGES_FOLDER.'/'.$page_id))
			{
				if (!mkdir($SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_PAGES_FOLDER.'/'.$page_id, 0777, true)) $error = true;
			}	
			
			if (!file_exists($SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_PAGES_FOLDER.'/'.$page_id.'/'.$IMAGES_FOLDER))
			{
				if (!mkdir($SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_PAGES_FOLDER.'/'.$page_id.'/'.$IMAGES_FOLDER, 0777, true)) $error = true;
			}
			
		}
		
		if (!$error)
		{
		
			$title= $title_matches[1];
			$content= $content_matches[1];
			
			//парсим URL картинок
			
			$images_pattern = '/(<a.*?href="(.*?)".*?>[\s]*?)?<img.*?src="(.*?)".*?>([\s]*?<\/a>)?/is';
			
			if (preg_match_all($images_pattern, $content, $images_matches) == false) $error = true;
			
			//сохраняем картинки, заменяем URL и теги
			
			$n = count($images_matches[1]);
				
			for ($i = 0; $i < $n; $i++)
			{
				$image_url = trim($images_matches[2][$i]);
				$image_path = $SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_PAGES_FOLDER.'/'.$page_id.'/images/'.basename($image_url);
				
				if ($image_url == '') continue;
				
				$image = file_get_contents($image_url);
				
				if ($image === false) continue;
				
				if (file_put_contents($image_path, $image) == false) continue;
				list($width, $height) = getimagesize($image_path);
				
				$href = $images_matches[3][$i];;
				$images_matches[1][$i] = $ELEMENTS_DELIMITER.$IMAGE_TAG.$PARAMS_DELIMITER.$image_path.$PARAMS_DELIMITER.$href.$PARAMS_DELIMITER.$width.'x'.$height.$ELEMENTS_DELIMITER;

			}
			
			$content = str_replace($images_matches[0], $images_matches[1], $content);
			unset($images_matches);
			unset($image);
			
			$LIST_MARKER = "&#8226;";
	
			$content = str_replace('<li>', '<br/>'.$LIST_MARKER.' ', $content);
			
			$content = nl2br($content);
			$content = str_replace(array('<br>', '<br />'), '<br/>', $content);
			
			$content = strip_tags($content, '<br/><b><i><a><li>');
			
			//$content = htmlspecialchars_decode($content);
			
			echo '>>>>>>>', $content;
				
			$result['response']['title'] = iconv('windows-1251', 'UTF-8', $title);
			
			$content = explode($ELEMENTS_DELIMITER, $content);
			
			$n = count($content);
			
			for ($i = 0; $i < $n; $i++)
			{

				//$element = trim($content[$i]);	
				$element = $content[$i];	
				
				if (strpos($element, $PARAMS_DELIMITER))
				{
					$params = explode($PARAMS_DELIMITER, $element);
					
					if ($params[0] == $IMAGE_TAG)
					{
						$result['response']['content'][$i]['type'] = 'image';
						$result['response']['content'][$i]['path'] = $params[1];
						$result['response']['content'][$i]['url'] = $SCRIPT_DIR.'/'.$params[1];
						$result['response']['content'][$i]['href'] = $params[2];
						$size = explode('x', $params[3]);
						$result['response']['content'][$i]['width'] = $size[0];
						$result['response']['content'][$i]['height'] = $size[1];
						
					}
				}
				else
				{
					$result['response']['content'][$i]['type'] = 'text';
					$result['response']['content'][$i]['text'] = iconv('windows-1251', 'UTF-8', $element);
				}
				
				echo $element;
			}	
			unset($params);
			unset($content);
			unset($element);
				
		}

		if ($error)
		$result['result'] = $CODE_FALSE;
		else
		$result['result'] = $CODE_TRUE;
		
		$result = json_encode($result, JSON_UNESCAPED_UNICODE);
		
		$result = iconv('UTF-8', 'windows-1251', $result);
		
		if (!$error)
		{
			if (file_put_contents($SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_PAGES_FOLDER.'/'.$page_id.'/'.$page_id.'.txt', $result)===FALSE) $error = true;
		}
		if (json_last_error() <> JSON_ERROR_NONE) 
		{
			//Обработка ошибки
		}
		
		//echo $result;
		
		exit;
		
	}

?>