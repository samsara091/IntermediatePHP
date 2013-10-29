<?php

	require("connection.php");
	session_start();

// echo "<pre>";
// var_dump($_POST);
// echo "</pre>";


//see if the user wants to login
if(isset($_POST['action']) and $_POST['action'] == "login")
{
	loginAction();
}
else if(isset($_POST['action']) and $_POST['action'] == "register")
{
	registerAction();
}
else if(isset($_POST['action']) and $_POST['action'] == "message")
{
	addMessageAction();
}
else if(isset($_POST['action']) and $_POST['action'] == "comment")
{
	addCommentAction();
}
else
{
	//assume that the user wants to log off
	session_destroy();
	header("Location: index.php");
}


function loginAction()
{
	$errors = array();

	if(!(isset($_POST['email']) and filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)))
	{
		$errors[] = "email is not valid";
	}

	if(!(isset($_POST['password']) and strlen($_POST['password'])>=6))
	{
		$errors[] = "please double check your password (length must be greater than 6)";
	}

	//see if there are errors
	if(count($errors) > 0)
	{
		$_SESSION['login_errors'] = $errors;
		header('Location: index.php');
	}
	else
	{
		//check if the email and the password is valid
		$query = "SELECT * FROM users WHERE email = '" . mysql_real_escape_string($_POST['email']) 
		          . "' AND password ='".md5($_POST['password'])."'";
		$users = fetch_all($query);
		
		if(count($users)>0)
		{
			$_SESSION['logged_in'] = true;
			$_SESSION['user']['first_name'] = $users[0]['first_name'];
			$_SESSION['user']['last_name'] = $users[0]['last_name'];
			$_SESSION['user']['email'] = $users[0]['email'];
			$_SESSION['userId'] = $users[0]['id'];
			header("Location: wall.php");
		}
		else
		{
			$errors[] = "Invalid login information";
			$_SESSION['login_errors'] = $errors;
			header('Location: index.php');
		}
	}
}

function registerAction()
{
	$errors = array();
	//let's see if the first_name is a string
	if(!(isset($_POST['first_name']) and is_string($_POST['first_name']) and strlen($_POST['first_name'])>0))
	{
		$errors[] = "first name is not valid!";
	}

	//let's see if the last_name is a string
	if(!(isset($_POST['last_name']) and is_string($_POST['last_name']) and strlen($_POST['last_name'])>0))
	{
		$errors[] = "last name is not valid!";
	}

	if(!(isset($_POST['email']) and filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)))
	{
		$errors[] = "email is not valid";
	}

	if(!(isset($_POST['password']) and strlen($_POST['password'])>=6))
	{
		$errors[] = "please double check your password (length must be greater than 6)";
	}

	if(!(isset($_POST['confirm_password']) and isset($_POST['password']) and $_POST['password'] == $_POST['confirm_password']))
	{
		$errors[] = "please confirm your password";
	}

	if(count($errors)>0)
	{
		$_SESSION['register_errors'] = $errors;
		header("Location: index.php");
	}
	else
	{
		//see if the email address already is taken
		$query = "SELECT * FROM users WHERE email = '" . mysql_real_escape_string($_POST['email']) . "'";
		$users = fetch_all($query);	

		//see if someone already registered with that email address
		if(count($users)>0)
		{
			$errors[] = "someone already registered with this email address";
			$_SESSION['register_errors'] = $errors;
			header("Location: index.php");
		}
		else
		{
			$query = "INSERT INTO users (first_name, last_name, email, password, created_at) "
			    . " VALUES ('" . mysql_real_escape_string($_POST['first_name']) 
				. "', '{$_POST['last_name']}', '" . mysql_real_escape_string($_POST['email']) 
				. "', '".md5($_POST['password'])."', NOW())";
			mysql_query($query);

			$_SESSION['message'] = "User was successfully created!";
			header("Location: index.php");
		}
	}
}
	function addMessageAction()
	{
		if(isset($_POST['message']))
		{	
			$query = "insert into messages (user_id,message,created_at) values (" . mysql_real_escape_string($_SESSION['userId']) 
				. ",'" . mysql_real_escape_string($_POST['message']) . "',NOW())";
			mysql_query($query);
		}
		header("Location: wall.php");

	}

	function addCommentAction()
	{
		if(isset($_POST['comment']))
		{	
			// '". mysql_real_escape_string($email)."', 
			// things we need to know: message ID, the comment, the user id
			$query = "insert into comments (message_id, user_id, comment, created_at) "
						. "VALUES (" . mysql_real_escape_string($_POST['message_id']) . "," . mysql_real_escape_string($_SESSION['userId']) . ",'"
						. mysql_real_escape_string($_POST['comment']) . "', NOW())";

			mysql_query($query);
		}
		header("Location: wall.php");
	}
?>