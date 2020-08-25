<?php

//REGISTRATION/LOGIN

$REGISTRATION_OPENED = true;
$AUTHORIZATION_OPENED = true;

$INDEX_PAGE_WRITE_MODE = 'append';

$MAX_ID = 10000;

$SITE_NAME = 'VKCM';
$SENDER = 'VCKM-GROUP-Inc-Corporation';

$MIN_PASS_LENGTH = 6;
$MAX_PASS_LENGTH = 15;

$MIN_USERNAME_LENGTH = 4;
$MAX_USERNAME_LENGTH = 15;

//CODES

$CODE_UNKNOWN = '123';
 
$CODE_TRUE = '200';
$CODE_FALSE = '0';

$UNKNOWN_COMMAND_CODE = '400';

$FILE_NOT_FOUND_CODE = '404';
$EMPTY_CODE = '666';

$ALREADY_TAKEN_CODE = '303';

$UNKNOWN_ERROR_CODE = '111';

//

$RAND_MAX = 10000;

//files and folders

$REQUEST_PERIOD =  10;//in sec
$LAST_REQUEST_TIME_FILE = 'last_request_time.txt';
$LAST_PAGE_URL_FILE = 'last_page_url.txt';

$SAVED_DATA_FOLDER_NAME = 'saved_data';
$SAVED_DATA_FOLDER = 'data';

$SAVED_PAGES_FOLDER = 'pages';
$SAVED_INDEX_PAGE_FOLDER = 'index';

$PARSERS_FOLDER = 'parsers';

$LANGUAGES_LIST_FILE = 'languages.txt';
$CATEGORIES_LIST_FILE = 'categories.txt';
$PARSERS_LIST_FILE = 'parsers.txt';
$PAGES_LIST_FILE = 'pages.txt';

//resize

$SCALING_PERCENT = 1;//меньше 1 - уменьшение, больше 1 - увеличение
$IMAGE_QUALITY = 75;
$SAVED_IMAGES_FOLDER = 'saved_images';
$IMAGES_FOLDER = 'images';
$OPTIMIZED_IMAGES_FOLDER = 'mobile';
$MIN_IMAGE_WIDTH = 100;
$MIN_IMAGE_HEIGHT = 100;

//

$ELEMENTS_DELIMITER = '|E|';
$PARAMS_DELIMITER = '|';
$IMAGE_TAG = 'image';

?>