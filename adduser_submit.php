<?php
/*** begin our session ***/
include ("common.php");
include ("dbconnect.php");

if ($user_class < "9")
	exit();

$account_id = "";
/*** first check that both the username, password and form token have been sent ***/
if(!isset( $_POST['username'], $_POST['password'], $_POST['type'], $_POST['form_token']))
{
    $message = 'Please enter a valid username, password and user-type';
}
/*** check the form token is valid ***/
elseif( $_POST['form_token'] != $_SESSION['form_token'] && $_POST["SUBMIT"] !== "DELETE")
{
    $message = 'Invalid form submission';
}
/*** check the username is the correct length ***/
elseif (strlen( $_POST['username']) > 20 || strlen($_POST['username']) < 4)
{
    $message = 'Incorrect Length for Username';
}

/*** check the password is the correct length ***/
elseif (strlen( $_POST['password']) > 20 || strlen($_POST['password']) < 4 && $_POST["SUBMIT"] !== "DELETE")
{
    $message = 'Incorrect Length for Password';
}

elseif (!preg_match('/^[0-9]*$/', $_POST['type'])) {
	$message = 'Incorrect User-type';
}

/*** check the username has only alpha numeric characters ***/
elseif (ctype_alnum($_POST['username']) != true)
{
    /*** if there is no match ***/
    $message = "Username must be alpha numeric";
}
/*** check the password has only alpha numeric characters ***/
elseif (ctype_alnum($_POST['password']) != true && $_POST["SUBMIT"] !== "DELETE")
{
        /*** if there is no match ***/
        $message = "Password must be alpha numeric";
}
elseif (!preg_match('/^[0-9]*$/', $_POST['account_id'])) {
	$message = 'That sux';
}
else
{
    /*** if we are here the data is valid and we can insert it into database ***/
	$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
	$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
	$type = filter_var($_POST['type'], FILTER_SANITIZE_STRING);
	$account_id = filter_var($_POST['account_id'], FILTER_SANITIZE_STRING);

    /*** now we can encrypt the password ***/
    $password = sha1( $password );
    
  
	if ($_POST["SUBMIT"] == "ADD") {
		try
		{
		  
			/*** prepare the insert ***/
			$stmt = $db->prepare("INSERT INTO ACCOUNTS (username, password, type) VALUES (:username, :password, :type )");

			/*** bind the parameters ***/
			$stmt->bindParam(':username', $username, PDO::PARAM_STR);
			$stmt->bindParam(':password', $password, PDO::PARAM_STR, 40);
			$stmt->bindParam(':type', $type, PDO::PARAM_STR);
			
			/*** execute the prepared statement ***/
			$stmt->execute();

			/*** unset the form token session variable ***/
			unset( $_SESSION['form_token'] );

			/*** if all is done, say thanks ***/
			$message = 'New user added';
		}
		catch(Exception $e)
		{
			/*** check if the username already exists ***/
			if( $e->getCode() == 23000)
			{
				$message = 'Username already exists';
			}
			else
			{
				/*** if we are here, something has gone wrong with the database ***/
				$message = 'We are unable to process your request. Please try again later"';
			}
		}
	}
	
	if ($_POST["SUBMIT"] == "EDIT") {
		try
		{
		  
			/*** prepare the insert ***/
			$stmt = $db->prepare("UPDATE ACCOUNTS SET username=:username, password=:password, type=:type where ID=:account_id");

			/*** bind the parameters ***/
			$stmt->bindParam(':username', $username, PDO::PARAM_STR);
			$stmt->bindParam(':password', $password, PDO::PARAM_STR, 40);
			$stmt->bindParam(':type', $type, PDO::PARAM_STR);
			$stmt->bindParam(':account_id', $account_id, PDO::PARAM_STR);

			/*** execute the prepared statement ***/
			$stmt->execute();

			/*** unset the form token session variable ***/
			unset( $_SESSION['form_token'] );

			/*** if all is done, say thanks ***/
			$message = 'User Edited Successfully';
		}
		catch(Exception $e)
		{
			/*** if we are here, something has gone wrong with the database ***/
			$message = 'We are unable to process your request. Please try again later"';
		}
	}
	if ($_POST["SUBMIT"] == "DELETE") {
		try
		{
		  
			/*** prepare the insert ***/
			$stmt = $db->prepare("DELETE FROM ACCOUNTS where ID=:account_id");

			/*** bind the parameters ***/
			
			$stmt->bindParam(':account_id', $account_id, PDO::PARAM_STR);

			/*** execute the prepared statement ***/
			$stmt->execute();

			/*** unset the form token session variable ***/
			unset( $_SESSION['form_token'] );

			/*** if all is done, say thanks ***/
			$message = 'User Deleted Successfully';
		}
		catch(Exception $e)
		{
			/*** if we are here, something has gone wrong with the database ***/
			$message = 'We are unable to process your request. Please try again later"';
		}
	}
}
?>


<body>
<p><?php echo $message; ?>
</body>
</html>
