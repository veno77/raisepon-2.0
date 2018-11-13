<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
include_once '../classes/accounts_class.php';
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

$accounts = new accounts();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->username)){
	$accounts->setUsername($data->username);
	$user_exist = $accounts->user_exist();
	// generate json web token
	// check if user exists and if password is correct
	$password = sha1( $data->password );
	if($user_exist && hash_equals($password, $accounts->getPassword())){
	 
		$token = array(
		   "iss" => $iss,
		   "aud" => $aud,
		   "iat" => $iat,
		   "nbf" => $nbf,
		   "exp" => $exp,
		   "data" => array(
			   "id" => $accounts->getAccount_id(),
			   "username" => $accounts->getUsername(),
			   "type" => $accounts->getType(),
		   )
		);
	 
		// set response code
		http_response_code(200);
	 
		// generate jwt
		$jwt = JWT::encode($token, $key);
		echo json_encode(
				array(
					"message" => "Successful login.",
					"jwt" => $jwt
				)
			);
	 // login failed 
	} else {
 
    // set response code
    http_response_code(401);
 
    // tell the user login failed
    echo json_encode(array("message" => "Login failed.  "));
	}
 

    
}else{
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Data is incomplete."));
}








?>