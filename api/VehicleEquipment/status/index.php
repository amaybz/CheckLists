<?php
error_reporting(E_ALL);
//ini_set('display_errors', 1);

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, set, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/core.php';
include_once '../../config/database.php';
include_once '../../objects/VehicleEquipment.php';
require_once('../../../classes/membersdb.php');
require_once( '../../../classes/checklist.php');
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
foreach (getallheaders() as $name => $value) {
    if ($name == "Authorization" ){
            $token = $value;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

//connection to members DB
$membersdb = new membersdb();

// get database connection
$database = new Database();
$db = $database->getConnection();
  
// instantiate object
$VehicleEquipment = new VehicleEquipment($db);

//checklist classes
$CheckList = new CheckList();
 

// set product property values
//BaseSignIn->idVehicleEquipment = $data->IDBaseCode;
//$BaseSignIn->Status = $data->GameID;
//print_r($data);


if($token){
    // if decode succeed, show user details
    $ValidToken = 0;
    try {
        // decode jwt
        $membersdb->MemberAuthToken = $token;
        $membersdb->LoginViaToken();
        if ($membersdb->isLoggedIn == 1)
	    {
		    $ValidToken = 1;
	    }
    }
   // if decode fails, it means jwt is invalid
    catch (Exception $e){
        $ValidToken = 0;
    }
}

 
// check if sign in Exists
if(
    $ValidToken == 1
){

    $EquipmentStatus = $CheckList->getEquipmentStatus();
    // set response code
    http_response_code(200);
 
    // display data
    echo json_encode($EquipmentStatus);
}
elseif($ValidToken == 0)
{
// set response code
http_response_code(401);
 
// tell the user access denied  & show error message
echo json_encode(array(
    "message" => "Access denied."

));

}
// message if unable to find patrol
else{
 
    // set response code
    http_response_code(400);
 
    // display message: unable to create user
    echo json_encode(array(
        "message" => "Failed to get equipment status."
        )
    
);
}


?>