<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once(__DIR__ . '/../../../classes/db.php');
require_once(__DIR__ . '/../../../classes/users.php');
require_once(__DIR__ . '/../../objects/Vehicle.php');

$database = new db();
$db = $database->conn;

$users = new users();
if ($users->isLoggedIn == 0 || $users->Permission < 1) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit;
}

$vehicle = new Vehicle($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->Name) && !empty($data->CallSign)){
    $vehicle->Name = $data->Name;
    $vehicle->CallSign = $data->CallSign;

    if($vehicle->Create()){
        http_response_code(201);
        echo json_encode(array("message" => "Vehicle was created."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create vehicle."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create vehicle. Data is incomplete."));
}
?>
