<?php
    // Change the line below to your timezone!
    date_default_timezone_set('Australia/Melbourne');
    require_once( 'classes/checklist.php');
    include 'classes/dbconfig.php';

    $CheckList = new CheckList();
    
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
// Takes raw data from the request
$json = file_get_contents('php://input');
$data = json_decode($json,true);





foreach (getallheaders() as $name => $value) {
        if ($name == "Authorization" ){
                if ($value == $APIPassword)
                {
                        //echo "You Have accesss";
                        if ($data['request'] == "getEquipmentStatusbyid")
                        { 
                                echo json_encode($CheckList->getEquipmentStatus($data['idVehicleEquipment']));
                        }

                        if ($data['request'] == "getVehicleEquipmentStatus")
                        {
                                echo json_encode($CheckList->getEquipmentStatus());
                        }
                        if ($data['request'] == "setEquipmentStatus")
                        {
                                echo json_encode($CheckList->setEquipmentStatus($data['idVehicleEquipment'], $data['Status'], $data['Date'], $data['Qty']));
                        }
                        if ($data['request'] == "addVehicle")
                        {
                                echo json_encode($CheckList->addVehicle ($data['VehicleName'], $data['VehicleCallSign']));
                        }
                        if ($data['request'] == "addEquipment")
                        {
                                echo json_encode($CheckList->addVehicleEquipment ($data['idVehicleSection'],$data['subCatID'],$data['Name'],$data['Qty']));
                        }
                        if ($data['request'] == "addVehicleSection")
                        {
                                echo json_encode($CheckList->addSection ($data['idVehicle'],$data['Name']));
                        }
                        if ($data['request'] == "addVehicleSubSection")
                        {
                                echo json_encode($CheckList->addSubSection ($data['IDSection'],$data['Name']));
                        }
                }
                else{
                       echo "[Access Denied]";
                }
        }
        
    }


?>