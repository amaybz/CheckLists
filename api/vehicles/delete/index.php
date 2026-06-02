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

include_once '../../config/database.php';
include_once '../../objects/Vehicle.php';
include_once('../../../classes/users.php');

$users = new users();
$token = "";
foreach (getallheaders() as $name => $value) {
    if ($name == "Authorization" ){
        $token = $value;
    }
}

$ValidToken = 0;
if($token){
    try {
        $users->MemberAuthToken = $token;
        $users->LoginViaToken();
        if ($users->isLoggedIn == 1 && $users->Permission >= 1) {
            $ValidToken = 1;
        }
    }
    catch (Exception $e){
        $ValidToken = 0;
    }
}

if ($ValidToken == 1) {
    $database = new Database();
    $db = $database->getConnection();

    $data = json_decode(file_get_contents("php://input"));

    if(!empty($data->id)){
        $vehicle = new Vehicle($db);
        $vehicle->id = $data->id;

        if($vehicle->Delete()){
            http_response_code(200);
            echo json_encode(array("message" => "Vehicle was deleted."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete vehicle."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Unable to delete vehicle. Data is incomplete."));
    }
}
else {
    http_response_code(401);
    echo json_encode(array("message" => "Access denied."));
}
?>
