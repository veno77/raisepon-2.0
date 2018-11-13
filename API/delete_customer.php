<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 // required to encode json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
include_once '../classes/customers_class.php';
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt=isset($data->jwt) ? $data->jwt : "";
 
// decode jwt here
// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));		
		$customer = new customers();
		if(!empty($data->id) || !empty($data->sn)){
			// set customer property values
			if (isset($data->id))
				$customer->setCustomer_id($data->id);
			if (isset($data->sn))
				$customer->setSn($data->sn);
			
			$customer->get_data_customer();
			
			$error = $customer->delete_customer();
		 
			if (empty($error)) {
			   // set response code - 200 ok
				http_response_code(200);
		 
				// tell the user
				
				echo json_encode(array("message" => "Customer was deleted."));
			}else{
		 
				// set response code - 503 service unavailable
				http_response_code(503);
		 
				// tell the user
				echo json_encode(array("message" => "Unable to delete customer. $error"));
			}
		}else{
		 
			// set response code - 400 bad request
			http_response_code(400);
		 
			// tell the user
			echo json_encode(array("message" => "Unable to delete customer. Data is incomplete."));
		}
	}
	catch (Exception $e){
		// set response code
		http_response_code(401);
	 
		// show error message
		echo json_encode(array(
			"message" => "Access denied.",
			"error" => $e->getMessage()
		));
	}
}else{
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
}







?>