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

// files needed to connect to database
include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/user.php';
include_once '../../../../composer/vendor/firebase/php-jwt/src/BeforeValidException.php';
include_once '../../../../composer/vendor/firebase/php-jwt/src/ExpiredException.php';
include_once '../../../../composer/vendor/firebase/php-jwt/src/SignatureInvalidException.php';
include_once '../../../../composer/vendor/firebase/php-jwt/src/JWT.php';


use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// get posted data
$data = json_decode(file_get_contents("php://input"));

//echo "<br>user: " . $data->username;
//echo "<br>Pass: " .$data->password;
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate user object
$user = new User($db);
 


// set product property values
$user->username = $data->username;
$user->name = $data->name;
$user->access = $data->access;
$user->password = $data->password;
$username_exists = $user->UserNameExists();


// check if email exists and if password is correct
if($user->create()){


    //echo "<br>password correct<br>";
    $token = array(
       "iat" => $issued_at,
       "exp" => $expiration_time,
       "iss" => $issuer,
       "data" => array(
           "id" => $user->id,
           "username" => $user->username,
           "name" => $user->name,
           "access" => $user->access,
       )
    );
 
    // set response code
    http_response_code(200);
 
    // generate jwt
    $jwt = JWT::encode($token, $key, 'HS256');
    echo json_encode(
            array(
                "message" => "Successful login.",
                "jwt" => $jwt,
                "username" => $user->username,
                "name" => $user->name,
                "access" => $user->access,
            )
        );
 
}
 
// login failed
else{

    // set response code
    http_response_code(401);
 
    // tell the user login failed
    echo json_encode(array("message" => "Login failed."));
}
?>