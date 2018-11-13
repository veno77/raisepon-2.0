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
		if ($type >= "3") {	
			
			$customer = new customers();
			if (isset($data->id)) { 
				$id = $data->id;
				$customer->setCustomer_id($id); 
			}else if (isset($data->sn)) { 
				$sn = $data->sn;
				$customer->setSn($sn); 
			}

			$customer->get_data_customer();

			if($customer->getName()!=null){
				// create array
				$customer_item=array(
					"id" => $customer->getCustomers_id(),
					"name" => $customer->getName(),
					"address" => $customer->getAddress(),
					"egn" => $customer->getEgn(),
					"sn" => $customer->getSn(),
					"service" => $customer->getService(),
					"auto" => $customer->getAuto(),
					"state" => $customer->getState(),
					"state_rf" => $customer->getState_rf()
				); 
				// set response code - 200 OK
				http_response_code(200);
			 
				// show customers data in json format
				echo json_encode($customer_item);
			}else{
			 
				// set response code - 404 Not found
				http_response_code(404);
			 
				// tell the user no customers found
				$boza = $customer->getName();
				echo json_encode(
					array("message $boza" => "Customer does not exist.")
				);
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