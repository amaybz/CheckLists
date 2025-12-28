<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// required to decode jwt
// files needed to connect to database
//require '../../../../composer/vendor/autoload.php';
include_once '../../config/core.php';
include_once '../../config/database.php';
include_once '../../objects/user.php';
include_once '../../../../../composer/vendor/firebase/php-jwt/src/BeforeValidException.php';
include_once '../../../../../composer/vendor/firebase/php-jwt/src/ExpiredException.php';
include_once '../../../../../composer/vendor/firebase/php-jwt/src/SignatureInvalidException.php';
include_once '../../../../../composer/vendor/firebase/php-jwt/src/JWT.php';
include_once '../../../../../composer/vendor/firebase/php-jwt/src/JWK.php';
use \Firebase\JWT\JWT;
//use \Firebase\JWT\Key;
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
//echo "<br> Headers<br>";
foreach (getallheaders() as $name => $value) {
    
    //echo $name . "<br>";
    if ($name == "Authorization" ){
            $jwt = $value;
    }
}



// get jwt
//$jwt=isset($data->jwt) ? $data->jwt : "";
 
// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        //$decoded = JWT::decode($jwt, new Key($key, 'HS256'));
 
        // set response code
        http_response_code(200);
 
        // show user details
        echo json_encode(array(
            "message" => "Access granted.",
            "data" => $decoded->data
        ));
 
    }
 
   // if decode fails, it means jwt is invalid
catch (Exception $e){
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied  & show error message
    echo json_encode(array(
        "message" => "Access denied.",
        "error" => $e->getMessage()
    ));
}
}
 
// show error message if jwt is empty
else{
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
}
?>