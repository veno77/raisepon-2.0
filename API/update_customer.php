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
		$decoded = (array)JWT::decode($jwt, $key, array('HS256'));
		$type = $decoded["data"]->type;
		if ($type >= "6") {	
			$customer = new customers();
			if(!empty($data->id) || !empty($data->sn)){
				// set customer property values
				if (isset($data->id))
					$customer->setCustomer_id($data->id);
				if (isset($data->sn))
					$customer->setSn($data->sn);
				
				$customer->get_data_customer();
				
				if (!empty($data->name))
					$customer->setName($data->name);
				if (!empty($data->address)) 
					$customer->setAddress($data->address);
				if (!empty($data->egn)) 
					$customer->setEgn($data->egn);
				if (!empty($data->sn)) 
					$customer->setSn($data->sn);
				if (!empty($data->service)) 
					$customer->setService($data->service);
				if (!empty($data->auto)) 
					$customer->setAuto($data->auto);
				if (!empty($data->state)) 
					$customer->setState($data->state);
				if (!empty($data->state_rf)) 
					$customer->setState_rf($data->state_rf);

				$error = $customer->edit_customer();

				if (empty($error)) {
					// set response code - 201 created
					http_response_code(201);
			 
					// tell the user
					
					echo json_encode(array("message" => "Customer was updated."));
				}else{
			 
					// set response code - 503 service unavailable
					http_response_code(503);
			 
					// tell the user
					echo json_encode(array("message" => "Unable to edit customer. $error"));
				}
			}else{
			 
				// set response code - 400 bad request
				http_response_code(400);
			 
				// tell the user
				echo json_encode(array("message" => "Unable to edit customer. Data is incomplete."));
			}
		}else{
			// set response code
			http_response_code(401);
	 
			// show error message
			echo json_encode(array(
				"message" => "Access denied. Not enough privilege.",
			));
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