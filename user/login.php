<?php

  if ($act == 'get_login_status') 
  {
    if ($AUTHORIZATION_OPENED) 
    $result['response'] = $CODE_TRUE;
    else
    $result['response'] = $CODE_FALSE;
	
	echo json_encode($result);
	
    exit;
  }
  
  if ($act == 'login')
  {  
	  $error = false;

	  $result['email'] = $CODE_TRUE;
	  $result['pass'] = $CODE_TRUE;
	  $result['token'] = '';
	  $result['email_verified'] = '';

	  $parameters = array('email', 'pass');
	  
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
	    $email = $_POST['email'];
	    $pass = $_POST['pass'];
	  }

	  if (!$error)
	  {

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

	  }


	  if (!$error)
	  {
		$user_id = mt_rand(0, $MAX_ID);
	  
		$params = array(
		':email' => $email
		);

		$SQL = "SELECT * FROM `users` WHERE `email`=:email";
		  
		try
		{
		  $q_result = $DBH->prepare($SQL);
		  $q_result->execute($params);
		}  
		catch(PDOException $e)
		{  
		  $result['result'] = $UNKNOWN_ERROR_CODE;
		}

		$row_count = $q_result->rowCount();
		
		if ($row_count == 0) $result['result'] = $CODE_FALSE;
		else
		{
		  $row = $q_result->fetch();
		  $salt = $row['salt'];
		  $pass = md5(md5($pass).md5($salt));
		
		  if ($pass != $row['pass'])
		  {
			$result['result'] = $CODE_FALSE;
		  }
		  else
		  {
			$id = $row['id'];
			$username = $row['username'];
			
			$email_verified = $row['email_verified'];
			$email_verified = strtolower($email_verified);
			if ($email_verified == 'true')
			$result['email_verified'] = $CODE_TRUE;
			else
			$result['email_verified'] = $CODE_FALSE;
			
			$result['user_id'] = $id;
			
			if ($row['token'] == '')
			{
			  $token = md5(mt_rand());
			  $result['token'] = $token;
			
			  $params = array(
			  ':email' => $email,
			  ':token' => $token
			  );

			  $SQL= "UPDATE `users` SET `token`=:token WHERE `email`=:email";
	  
			  try
			  {
				$q_result = $DBH->prepare($SQL);
				$q_result->execute($params);
			  }  
			  catch(PDOException $e)
			  {  
				  echo 'Error';
			  }
			}
			else
			{
			  $result['token'] = $row['token'];
			}
		  
		  }
		}
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

?>