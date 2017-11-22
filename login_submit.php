<?php
header('Content-Type: text/html; charset=utf-8');
//print "<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\">";
print "<link rel=\"stylesheet\" href=\"./css/bootstrap.min.css\">";

print "<script src=\"./jquery-3.1.1.min.js\"></script>";
print "<script src=\"./js/bootstrap.min.js\"></script>";
include ("navigation.php");
/*** begin our session ***/
session_start();
include("dbconnect.php");

$id = "";

/*** check if the users is already logged in ***/
if(isset( $_SESSION['id'] ))
{
    $message = 'User is already logged in';
}
/*** check that both the username, password have been submitted ***/
if(!isset( $_POST['username'], $_POST['password']))
{
    $message = 'Please enter a valid username and password';
}
/*** check the username is the correct length ***/
elseif (strlen( $_POST['username']) > 20 || strlen($_POST['username']) < 4)
{
    $message = 'Incorrect Length for Username';
}
/*** check the password is the correct length ***/
elseif (strlen( $_POST['password']) > 20 || strlen($_POST['password']) < 4)
{
    $message = 'Incorrect Length for Password';
}
/*** check the username has only alpha numeric characters ***/
elseif (ctype_alnum($_POST['username']) != true)
{
    /*** if there is no match ***/
    $message = "Username must be alpha numeric";
}
/*** check the password has only alpha numeric characters ***/
elseif (ctype_alnum($_POST['password']) != true)
{
        /*** if there is no match ***/
        $message = "Password must be alpha numeric";
}
else
{
    /*** if we are here the data is valid and we can insert it into database ***/
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

    /*** now we can encrypt the password ***/
    $password = sha1( $password );
    

    try
    {
        
        /*** prepare the select statement ***/
        $stmt = $db->prepare("SELECT id, username, password, type FROM ACCOUNTS 
                    WHERE username = :username AND password = :password");

        /*** bind the parameters ***/
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR, 40);

        /*** execute the prepared statement ***/
        $stmt->execute();

        /*** check for a result ***/
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$id = $row{'id'};
			$type = $row{'type'};
		}

        /*** if we have no result then fail boat ***/
        if($id == false)
        {
                $message = 'Login Failed';
        }
        /*** if we do have a result, all is well ***/
        else
        {
				/*** set the session user_id variable ***/
				$_SESSION['id'] = $id;
				$_SESSION['type'] = $type;
				header("Location: index.php");
		}


    }
    catch(Exception $e)
    {
        /*** if we are here, something has gone wrong with the database ***/
        $message = 'We are unable to process your request. Please try again later"';
    }
}
?>

<body><center>
<p><?php echo $message; ?>
</body>
</html>
