<?php

if ($act == 'get_languages_list')
{
	$result['result'] = $CODE_TRUE;
	
	$languages = file2array($ROOT_FOLDER.$LANGUAGES_LIST_FILE);
	
	for ($i = 0; $i < count($languages); $i++)
	{
		$language = explode($DELIMITER, $languages[$i]);
		$lang_code = $language[0];
		$lang_folder = $language[1];
		$lang_icon = $SCRIPT_DIR.'/'.$IMAGES_FOLDER.'/icons/langs/'.$language[2];
		$lang_name = $language[3];

		$result['response'][$i]['lang_code'] = $lang_code;
		$result['response'][$i]['lang_name'] = $lang_name;

		$result['response'][$i]['lang_icon'] = $lang_icon;
	}
	
	echo json_encode($result);
	
	exit;

}

if ($act == 'get_categories_list')
{
	$result['result'] = $CODE_TRUE;
	
	if ($lang_folder == '') 
	{
		$result['result'] = $CODE_FALSE;
		$result['language'] = $FILE_NOT_FOUND_CODE;
	}
	else
	{
		$categories = file2array($ROOT_FOLDER.$lang_folder.'/'.$CATEGORIES_LIST_FILE);
		
		for ($i = 0; $i < count($categories); $i++)
		{
			$category = explode($DELIMITER, $categories[$i]);
			$category_code = $category[0];
			$category_folder = $category[1];
			$category_icon = $SCRIPT_DIR.'/'.$IMAGES_FOLDER.'/icons/categories/'.$category[2];
			$category_name = $category[3];
			
			$result['response'][$i]['category_code'] = $category_code;	
			$result['response'][$i]['category_icon'] = $category_icon;	
			$result['response'][$i]['category_name'] = $category_name;	
			
	
		}
		
			echo json_encode($result);
		
	}
	
	exit;
}

if ($act == 'get_parsers_list')
{
	$result['result'] = $CODE_TRUE;

		
	//=================================
  
	$result['result'] = $CODE_TRUE;
  
	token = isset($_POST['token']) ? $_POST['token'] : '';
  
	if (
	!check_string($token) or 
	!check_string($lang_code) or 
	!check_string($category_code) or 
	!check_string($parser_code)
	)
	{
		$result['result'] = $CODE_FALSE;
	}

	$params = array(':token' => $token);

    $SQL = "SELECT * FROM `users` WHERE `token`=:token";
	
	try
	{
		$q_result = $DBH->prepare($SQL);
		$q_result->execute($params);
	}  
	catch(PDOException $e)
	{  
		$result['result'] = $CODE_FALSE;
	}

	if ($q_result->rowCount() == 0)
	{
		$result['result'] = $CODE_FALSE;
	}
  
	$row = $q_result->fetch();
  
	if ($row['parsers'] != '')
	$user_parsers = json_decode($row['parsers'], true);
    
	$parsers = file2array($lang_folder.'/'.$category_folder.'/'.$PARSERS_LIST_FILE);
		
	//=================================
		
	for ($i = 0; $i < count($parsers); $i++)
	{
		$parser = explode($DELIMITER, $parsers[$i]);
		$parser_code = $parser[0];
		$parser_folder = $parser[1];
		$parser_icon = $parser[2];
		$parser_name = $parser[3];
			
		$result['response'][$i]['parser_code'] = $parser_code;
		$result['response'][$i]['parser_name'] = $parser_name;			
		$result['response'][$i]['parser_icon'] = $SCRIPT_DIR.'/'.$lang_code.'/'.$category_code.'/'.$parser_folder.'/'.$parser_icon;  
		$result['response'][$i]['subscribed'] = isset($user_parsers['parsers'][$lang_code][$category_code][$parser_code]) ? true : false;
		
	}
   
	echo json_encode($result);

	exit;
	
}

if ($act == 'get_pages_list')
{
  
	$result['result'] = $CODE_TRUE;
	
	$mode = isset($_GET['mode']) ? $_GET['mode'] : '';
	$page_id = isset($_GET['pid']) ? $_GET['pid'] : '';
  
	if (!file_exists($PAGES_LIST_FILE)) !$error = true;
  
	if (!$error)
	{	
		$pages = file($PAGES_LIST_FILE);
		
		$host = $_SERVER['HTTP_HOST'];
		
		for ($i = 0; $i < count($pages); $i++)
		{

			$page = explode($DELIMITER, $pages[$i]);
			$page_id = $page[0];
			$page_url = $page[1];
			$page_title = $page[2];
			$page_poster = $page[3];
			
			$page_preview_filepath = $SAVED_DATA_FOLDER_PATH.$SAVED_DATA_FOLDER.'/'.$SAVED_INDEX_PAGE_FOLDER.'/'.$page_id.'.txt';
		
			$page_poster = 
			$SCRIPT_DIR.'/'.$page_poster;
			
			$page_preview = file_get_contents($page_preview_filepath);

			if ($mode == 'new' and $last_page_id == $page_id) break;
			
			$result['response'][$i]['page_id'] = $page_id;
  
			$result['response'][$i]['page_title'] = iconv('windows-1251', 'UTF-8', $page_title);
   
			$result['response'][$i]['page_poster'] = $page_poster;
			$result['response'][$i]['page_preview'] = iconv('windows-1251', 'UTF-8', $page_preview);
								
		}
		
		echo iconv('UTF-8', 'windows-1251',  json_encode($result, JSON_UNESCAPED_UNICODE));
		
	}
	
	exit;

}

?>