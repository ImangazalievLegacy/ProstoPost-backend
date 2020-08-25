<?php

if ($act == 'set_group_id')
{
  
  $result['result'] = $CODE_TRUE;
  
  $vk_group_id = isset($_POST['vk_group_id']) ? $_POST['vk_group_id'] : '';
  $token = isset($_POST['token']) ? $_POST['token'] : '';
  
  if (!check_string($vk_group_id) or
  !check_string($token)
  )
  {
    $result['result'] = $CODE_FALSE;
  }

  $params = array(':vk_group_id' => $vk_group_id,
  ':token' => $token
  );

  $SQL= "UPDATE `users` SET `vk_group_id`=:vk_group_id WHERE `token`=:token";
	
  try
  {
    $q_result = $DBH->prepare($SQL);
    $q_result->execute($params);
  }  
  catch(PDOException $e)
  {  
    $result['result'] = $CODE_FALSE;
  }

  $params = array(':vk_group_id' => $vk_group_id
  );

    $SQL = "SELECT * FROM `users` WHERE `vk_group_id`=:vk_group_id";
	
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
    
  echo json_encode($result);
  
  exit;
}

if ($act == 'add_parser') {
  
  $result['result'] = $CODE_TRUE;

  	$lang_code = isset($_GET['lang_code']) ?  $_GET['lang_code'] : '';
 		$category_code = isset($_GET['category_code']) ?  $_GET['category_code'] : '';
 			$parser_code = isset($_GET['parser_code']) ? $_GET['parser_code'] : '';
  
  $token = isset($_POST['token']) ? $_POST['token'] : '';
  
  if (
  !check_string($token) or 
  !check_string($lang_code) or 
  !check_string($category_code) or 
  !check_string($parser_code)
  )
  {
    $result['result'] = $CODE_FALSE;
  }

  $params = array(':token' => $token
  );

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
  
  if (($row['parsers'] != '') and ($row['parsers'] != 'null'))
    $parsers = json_decode($row['parsers'], true);
      $parsers['parsers'][$lang_code][$category_code][$parser_code] = true;
  
  $parsers = json_encode($parsers);
  
  $params = array(':parsers' => $parsers,
  ':token' => $token
  );

  $SQL= "UPDATE `users` SET `parsers`=:parsers WHERE `token`=:token";
	
  try
  {
    $q_result = $DBH->prepare($SQL);
    $q_result->execute($params);
  }  
  catch(PDOException $e)
  {  
    $result['result'] = $CODE_FALSE;
  }  
  
  echo json_encode($result);
  
  exit;
}

if ($act == 'delete_parser') {
  
  $result['result'] = $CODE_TRUE;

  	$lang_code = isset($_GET['lang_code']) ?  $_GET['lang_code'] : '';
 		$category_code = isset($_GET['category_code']) ?  $_GET['category_code'] : '';
 			$parser_code = isset($_GET['parser_code']) ? $_GET['parser_code'] : '';
  
  $token = isset($_POST['token']) ? $_POST['token'] : '';
  
  if (
  !check_string($token) or 
  !check_string($lang_code) or 
  !check_string($category_code) or 
  !check_string($parser_code)
  )
  {
    $result['result'] = $CODE_FALSE;
  }

  $params = array(':token' => $token
  );

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
    $parsers = json_decode($row['parsers'], true);
    
  unset($parsers['parsers'][$lang_code][$category_code][$parser_code]);
  
  $parsers = json_encode($parsers);
  
  if ($parsers == false) $parsers = '';
  
  $params = array(':parsers' => $parsers,
  ':token' => $token
  );

  $SQL= "UPDATE `users` SET `parsers`=:parsers WHERE `token`=:token";
	
  try
  {
    $q_result = $DBH->prepare($SQL);
    $q_result->execute($params);
  }  
  catch(PDOException $e)
  {  
    $result['result'] = $CODE_FALSE;
  }
  
  echo json_encode($result);
  
  exit;
}

if ($act == 'get_user_parsers_list') {
  
  $result['result'] = $TRUE_VALUE_CODE;

  	$lang_code = isset($_GET['lang_code']) ?  $_GET['lang_code'] : '';
 		$category_code = isset($_GET['category_code']) ?  $_GET['category_code'] : '';
 			$parser_code = isset($_GET['parser_code']) ? $_GET['parser_code'] : '';
  
  $token = isset($_POST['token']) ? $_POST['token'] : '';
  
  if (
  !check_string($token) or 
  !check_string($lang_code) or 
  !check_string($category_code) or 
  !check_string($parser_code)
  )
  {
    $result['result'] = $CODE_FALSE;
  }

  $params = array(':token' => $token
  );

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
  
  $user_parsers = $row['parsers'];
  
  if ($user_parsers != '' and $user_parsers != 'null') {
  
  $user_parsers = json_decode($user_parsers, true);
  $user_parsers = $user_parsers['parsers'];
  
  $p_num = 0;
  
  foreach ($user_parsers as $lang_code => $language)
  { 
  
    foreach($language as $category_code => $category)
    {
      
      foreach($category as $parser_code => $parser)
      {
      
         $lang_folder = LanguagesFolder($lang_code);
	
         if ($lang_folder == '') 
	        {
           echo 'Language Not Found';
           exit;
         }

         $category_folder = categoriesFolder($lang_folder, $category_code);
	
         if ($category_folder == '') 
         {
           echo 'Category Not Found';
           exit;
         }
        
        $parsers = file($lang_folder.'/'.$category_folder.'/'.$PARSERS_LIST_FILE);
        
        for ($i = 0; $i < count($parsers); $i++)
		       {
			        $parser = explode($DELIMITER, $parsers[$i]);
          $p_code = $parser[0];
          
          if ($p_code == $parser_code)
          {
            $parser_folder = $parser[1];
            $parser_icon = $parser[2];
            $parser_name = $parser[3];
            break;
          }
        }      
        $result['response'][$p_num]['parser_code'] = $parser_code;
        $result['response'][$p_num]['parser_name'] = $parser_name;
        $result['response'][$p_num]['parser_icon'] = $SCRIPT_DIR.'/'.$lang_code.'/'.$category_code.'/'.$parser_folder.'/'.$parser_icon;
        
        $p_num++;
      
      }
    
    }
        
  }
  }
  
  $result['parsers_num'] = count($result['response']);
  
  echo json_encode($result);
 
  exit;
}

if ($act == 'get_user_info')
{

  $result['result'] = $CODE_TRUE;

  $token = isset($_POST['token']) ? $_POST['token'] : '';
  
  if (!check_string($token))
  {
  
    $result['result'] = $CODE_FALSE;
  
  }

    $params = array(':token' => $token
  );

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
  
  $result['paid'] = $CODE_TRUE;
  
  if ($row['vk_group_id'] == '' or $row['vk_group_id'] == 'null')
  $result['group_selected'] = $CODE_FALSE;
  else
  {
    $result['group_selected'] = $CODE_TRUE;
  
    $result['group_id'] = $row['response']['vk_group_id'];
  }
 
  if ($row['parsers'] == '' or
   $row['parsers'] == 'null')
  $result['parser_selected'] = $CODE_FALSE;
  else $result['parser_selected'] = $CODE_TRUE;
  
  echo json_encode($result);
  
  exit;

}

?>