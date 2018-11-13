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
		
		//start get_customers
		$customer = new customers();
		$stmt = $customer->get_data();
		$num = $stmt->rowCount();

		if($num>0){
			$customers_arr=array();
			$customers_arr["customers"]=array();
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				extract($row);
				$customer_item=array(
					"id" => $ID,
					"name" => $NAME,
					"address" => $ADDRESS,
					"egn" => $EGN,
					"sn" => $SN,
					"service" => $SERVICE,
					"auto" => $AUTO,
					"state" => $STATE,
					"state_rf" => $STATE_RF
				);
		 
				array_push($customers_arr["customers"], $customer_item);
			}
			// set response code - 200 OK
			http_response_code(200);
		 
			// show products data in json format
			echo json_encode($customers_arr);
		}else{
		 
			// set response code - 404 Not found
			http_response_code(404);
		 
			// tell the user no products found
			echo json_encode(
				array("message" => "No products found.")
			);
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