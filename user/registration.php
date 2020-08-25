<?php

if ($act == 'get_registration_status') 
{
	if ($REGISTRATION_OPENED) 
	$result['response'] = $CODE_TRUE;
	else
	$result['response'] = $CODE_FALSE;
	
	echo json_encode($result);
	
    exit;
}

if ($act == 'register')
{

  $error = false;
  
  $parameters = array('username', 'email', 'pass', 'captcha_id', 'captcha_code');
  
  $result['username'] = $CODE_TRUE;
  $result['email'] = $CODE_TRUE;
  $result['pass'] = $CODE_TRUE;
  $result['captcha'] = $CODE_TRUE;
  
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
    $username = $_POST['username'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $captcha_code = $_POST['captcha_code'];
    $captcha_id = $_POST['captcha_id'];
  
  }
  
  if (!$error)
  {
    if (!check_string($username)
    or
    (strlen($username) > $MAX_USERNAME_LENGTH
    or
    strlen($username) < $MIN_USERNAME_LENGTH)) 
    {
      $result['username'] = $CODE_FALSE;
      $error = true;
    }

    if (!checkmail($email))
    {
      $result['email'] = $CODE_FALSE;
      $error = true;
    } 

    if (!check_string($pass)
    or
    (strlen($pass) < $MIN_PASS_LENGTH
    or
    strlen($pass) > $MAX_PASS_LENGTH))
    {
      $result['pass'] = $CODE_FALSE;
      $error = true;
    }

    $captcha_result = check_captcha($captcha_id, $captcha_code); 
	
    if (!$captcha_result[0])
    {
      $result['captcha'] = $captcha_result[1];
      $error = true;
    }
  
  }	

  if (!$error)
  {
    $user_id = mt_rand(0, $MAX_ID);
  
    $params = array(
	':id' => $user_id, 
	':username' => $username,
	':email' => $email);

    $SQL = "SELECT * FROM `users` WHERE `username`=:username OR `email`=:email OR `id`=:id";
	  
    try
    {
      $q_result = $DBH->prepare($SQL);
      $q_result->execute($params);
    }  
    catch(PDOException $e)
    {  
      $result['result'] = $UNKNOWN_ERROR_CODE;
    }
    
    while ( $row = $q_result->fetch())
    {
    
      if (strtolower($row['username']) == strtolower($username)) 
      {
        $result['username'] = $ALREADY_TAKEN_CODE;
        $error = true;
      }
      
        if (strtolower($row['email']) == strtolower($email)) 
      {
        $result['email'] = $ALREADY_TAKEN_CODE;
        $error = true;
      }
      	  
      if ($row['id'] == $user_id) $id = mt_rand(0, $MAX_ID);
      
    }
    
  }

  if (!$error)
  {
    $salt = random_string();

    $pass = md5(md5($pass).md5($salt));

    $hash = md5(mt_rand(0, 99999));
	
    $reg_date = date("d.m.y H:i:s");
	
    $params = array(
    ':id' => $user_id, 
    ':username' => $username,
    ':email' => $email,
    ':pass' => $pass,
    ':salt' => $salt,
    ':reg_date' => $reg_date,
    ':email_verified' => 'false',
    ':hash' => $hash);

    $SQL = "INSERT INTO `users` (`id`, `username`, `email`, `pass`, `salt`, `reg_date`, `email_verified`,  `hash`) VALUES (:id, :username, :email, :pass, :salt, :reg_date, :email_verified, :hash)";
	
    try
    {
      $q_result = $DBH->prepare($SQL);
      $q_result->execute($params);
    }  
    catch(PDOException $e)
    {  
  	    $result['result'] = $UNKNOWN_ERROR_CODE;
  	    $error = true;
    }

  
    $DBH = null;

  }

  if (!$error)
  {
  
    $confirm_url = $SCRIPT_URL.'?act=confirm_email&hash='.$hash;
    
    $to = $email;
    $subject = 'Подтвердите регистрацию на '.$SITE_NAME ;
    $headers = 'MIME-Version: 1.0' . "\r\n" ;
    $headers .= 'Content-type: text/html; charset=CP1251' . "\r\n" ;
    $headers .= 'From: '.$SENDER . "\r\n" ;
    
    $search = array('%SITE_NAME%', '%CONFIRM_URL%');
    $replace = array($SITE_NAME, $confirm_url);
    
    $text = file_get_contents('templates/confirm_message.tpl');
    
    $text = str_replace($search, $replace, $text);

    if (!mail($to, $subject, $text, $headers)) $error = true; 

  }
 
  if (!isset($result['result']))
  {
    if (!$error)
    $result['result'] = $CODE_TRUE;
    else 
    $result['result'] = $CODE_FALSE;
  }
  
  echo json_encode($result);

  exit;
}

if ($act == 'confirm_email')
{
  
  $hash = isset($_GET['hash']) ? $_GET['hash'] : '';
  
  if (!check_string($hash))
  {
    echo 'Error';
    exit;
  }

  $params = array(':hash' => $hash);

  $SQL= "UPDATE `users` SET `email_verified`='true' WHERE `hash`=:hash";
	
  try
  {
    $result = $DBH->prepare($SQL);
    $result->execute($params);
  }  
  catch(PDOException $e)
  {  
	    echo 'Error';
		exit;
  }

  if ($result->rowCount() > 0)
  echo 'E-mail confirmed!';
  else
  echo 'Error';
  
  exit;
}

?>