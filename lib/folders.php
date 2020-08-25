<?php

$PARSERS_LIST_FILE = 'parsers.txt';
$LANGUAGES_LIST_FILE = 'languages.txt';
$CATEGORIES_LIST_FILE = 'categories.txt';
$DELIMITER = '|';

//ѕровер€ет существует ли €зык, возвращает название папки €зыка или пустую строку
function LanguagesFolder($lang_code)
{
	global $PARSERS_FOLDER, $LANGUAGES_LIST_FILE, $DELIMITER;

	$languages = file($PARSERS_FOLDER.'/'.$LANGUAGES_LIST_FILE);
	
	$lang_folder = '';
	
	for ($i = 0; $i < count($languages); $i++)
	{
		$language = explode($DELIMITER, $languages[$i]);
		
		if ($lang_code == $language[0])
		{
			$lang_folder = $language[1];
			break;
		}
	}
	
	return $lang_folder;
}

function CategoriesFolder($lang_folder, $category_code)
{
	global $PARSERS_FOLDER, $LANGUAGES_LIST_FILE, $CATEGORIES_LIST_FILE, $DELIMITER;
	
	$categories = file($PARSERS_FOLDER.'/'.$lang_folder.'/'.$CATEGORIES_LIST_FILE);
	
	$category_folder = '';
	
	for ($i = 0; $i < count($categories); $i++)
	{
		$category = explode($DELIMITER, $categories[$i]);
				
		if ($category_code == $category[0])
		{
			$category_folder = $category[1];
			break;
		}
	}
	
	return $category_folder;
}

function ParsersFolder($lang_folder, $category_folder, $parser_code)
{
	global $PARSERS_FOLDER, $LANGUAGES_LIST_FILE, $CATEGORIES_LIST_FILE, $PARSERS_LIST_FILE, $DELIMITER, $PARSER_OPENING_TAG, $PARSER_CLOSING_TAG;
	
	$parsers = file($PARSERS_FOLDER.'/'.$lang_folder.'/'.$category_folder.'/'.$PARSERS_LIST_FILE);
	
	$parser_folder = '';
	
	for ($i = 0; $i < count($parsers); $i++)
	{
		$parsers = explode($DELIMITER, $parsers[$i]);
				
		if ($parser_code == $parsers[0])
		{
			$parser_folder = $parsers[1];
			break;
		}
	}
	
	return $parser_folder;
	
}

	$lang_code = isset($_GET['lang_code']) ? $_GET['lang_code'] : '';
	$lang_folder = $lang_code == '' ? '' : LanguagesFolder($lang_code);
	
	
	$category_code = isset($_GET['category_code']) ? $_GET['category_code'] : '';
	$category_folder = $category_code == '' ? '' : CategoriesFolder($lang_folder, $category_code);
	
	$parser_code = isset($_GET['parser_code']) ? $_GET['parser_code'] : '';
	$parser_folder = $parser_code == '' ? '' : ParsersFolder($lang_folder, $category_folder, $parser_code);
	
	$SAVED_DATA_FOLDER_PATH = $SAVED_DATA_FOLDER_NAME.'/'.$lang_folder.'/'.$category_folder.'/'.$parser_folder.'/';
	$PAGES_LIST_FILE = $SAVED_DATA_FOLDER_PATH.'data/pages.txt';

?>