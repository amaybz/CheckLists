<?

	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
    session_start(); // Use session variable on this page.
    date_default_timezone_set('Australia/Sydney');
    require_once('classes/membersdb.php');
    require_once('classes/db.php');
    require_once('classes/checklist.php');
    require_once('classes/menu.php');
    include 'classes/dbconfig.php';

    $membersdb = new membersdb();
    $db = new db();
    $CheckList = new CheckList();
    $Menu = new Menu();

    if ($membersdb->isLoggedIn == 0)
    {
        header("location:login.php"); // Re-direct to login.php
    }


    if($_GET["idSection"]){
        $idSection = $_GET["idSection"];
	}

    if($_GET["idVehicle"]){
        if($_SESSION['idVehicle'] != $_GET["idVehicle"])
        {
            $idSection = 0;
        }
		$idVehicle = $_GET["idVehicle"];
		$_SESSION['idVehicle'] = $_GET["idVehicle"];
        
	}
	else
	{
		$_SESSION['idVehicle'] = $_GET["idVehicle"];
		$idVehicle = $_SESSION["idVehicle"];
	}
    

    

			

?>

<script> 

function getVehicleEquipmentStatus() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    today = yyyy  + '-' + mm  + '-' + dd;

    //document.write(today);
    var settings = {
    "url": "https://ajcomputers.com.au/dptses/check_list/api.php",
    "method": "POST",
    "timeout": 0,
    "headers": {
        "Authorization": "<? echo $APIPassword; ?>",
        "Content-Type": "application/json"
    },
    "data": JSON.stringify({"request":"getVehicleEquipmentStatus"}),
    };

    $.ajax(settings).done(function (response) {
        //console.log(response);
        data = JSON.parse(response);
        var i;
        for (i = 0; i < data.length; i++) {
            checkbox = document.getElementById(data[i]['idVehicleEquipment'])
            numValue = document.getElementById("Qty_" + data[i]['idVehicleEquipment'])
            if (checkbox != null){
                if(data[i]['Status'] == 1 && data[i]['Date'] > today)
                {
                    checkbox.checked = true;
                    numValue.value = data[i]['Qty']
                }
                else if (data[i]['Status'] == 0 && data[i]['Date'] > today)
                {
                    checkbox.checked = false;
                    numValue.value = data[i]['Qty']
                 }
            }
        
        }
        });
}



function SetVehicleEquipmentStatus(idVehicleEquipment, Status, Qty) {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    today = yyyy  + '-' + mm  + '-' + dd;
    if(Qty == null || Qty == "")
    {
        Qty = 0;
    }
    console.log(idVehicleEquipment);
    console.log(Status);
    console.log(today);
    console.log(Qty);
    var settings = {
      "url": "https://ajcomputers.com.au/dptses/check_list/api.php",
      "method": "POST",
      "timeout": 0,
      "headers": {
        "Authorization": "<? echo $APIPassword; ?>",
        "Content-Type": "application/json"
      },
      "data": JSON.stringify({"request":"setEquipmentStatus","idVehicleEquipment":idVehicleEquipment,"Status":Status,"Date":today,"Qty":Qty}),
    };

    $.ajax(settings).done(function (response) {
      console.log(response);
    });
}

function updateQty(idVehicleEquipment, Qty){
    if(Qty > 0)
    {
        SetVehicleEquipmentStatus(idVehicleEquipment, 1, Qty);
    }
    else
    {
        SetVehicleEquipmentStatus(idVehicleEquipment, 0, Qty);
    }
    setTimeout(function(){ getVehicleEquipmentStatus(); }, 400);
    
    
}

</script>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>DPT SES - Check Lists</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="favicon.ico">
</head>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<body onload="onload()">

    <? $Menu->Show(2,$membersdb->fullName); ?>
	
<div class="container">
     <div class="row justify-content-md-center">
        <div class="col-md-auto">
            <form id="frmCheckListSelect"> 
          </div>
     </div>
     <div class="row justify-content-md-center">
        <div class="col-md-auto">
			Vehicle:
            
        </div>
        <div class="col-md-auto">
            <select name='idVehicle' id='idVehicle' class="form-select" onchange='document.getElementById("frmCheckListSelect").submit();'>
				<?
					echo '<option value="0">Select Vehicle</option>';
					$sql = "SELECT * FROM tblVehicles order by Name";
					$result = $db->conn->query($sql);
					$RowCountStaff = $result->num_rows;
					
					if ($RowCountStaff > 0)
					{
						while($row = $result->fetch_assoc()){
							//select		
                            if ($row["id"] == $idVehicle
                    ){
                                echo '<option selected value="' . $row["id"] . '">' . $row["CallSign"] . '</option>';
							}
							else{
                                echo '<option value="' . $row["id"] . '">' . $row["CallSign"] . '</option>';
							}

						}
					}
					else
					{
						echo '<option value="0">No Vehicle in DB</option>';
					}
					echo '</select>';
					
				
                ?>
                </div>
            <div class="col-md-auto">
                Section:
            </div>
            <div class="col-md-auto">
            <select name='idSection' id='idSection' class="form-select"  onchange='document.getElementById("frmCheckListSelect").submit();'>
				<?
                    $class="";
                    echo '<option value="0">Select Section</option>';
                    $stmt = $db->conn->prepare("SELECT * FROM tblVehicleSections WHERE `idVehicle` = ?");
                    $stmt->bind_param("i", $idVehicle);
                    $stmt->execute();
                    $result = $stmt->get_result();
					
					if($result->num_rows != 0) 
					{
						while($row = $result->fetch_assoc()){
                            $Progress = $CheckList->checkProgressByIDSection ($row["id"]);
                            if($Progress["Percent"] < 100)
                            {
                                $class="bg-warning";
                            }
                            if($Progress["Percent"] == 100)
                            {
                                $class="bg-success";
                            }
                            if($Progress["Percent"] == 0)
                            {
                                $class="bg-danger";
                            }
							//select		
                            if ($row["id"] == $idSection){
                                echo '<option class="' . $class . '" selected value="' . $row["id"] . '">' . $row["Name"] . '</option>';
							}
							else{
                                echo '<option class="' . $class . '" value="' . $row["id"] . '">' . $row["Name"] . '</option>';
							}

						}
					}
					else
					{
						echo '<option value="0">No Sections in DB</option>';
					}
					echo '</select>';
					
				
                ?>
			
			</form>
            </div>
     </div>
		
        <?php
            $VehicleSections = $CheckList->getSectionsByVehicleID($idVehicle);
            //Get all Sub Sections
            $SubSections = $CheckList->getSubSectionsBySectionID($idSection);

            if ($SubSections[0] == "No Records") {
	             echo "Select Vehicle Section";
            }
            else {
	            echo '<div id="checklist" class="checklist">';
                echo '<table class="table table-striped">';
                echo '<tr><td>Item</td><td>Expected <br> QTY </td><td>Actual <br> QTY </td><td>Checked</td><tr>';
                foreach ($SubSections as $SubSection) 
                {
                        echo '<tr><th colspan="4" class="table-dark"><b>' . $SubSection['Name'] . '</b></th></tr>';
                        $equipmentforSubSection = $CheckList->getEquipmentBySubSectionID($SubSection['ID']);
                        //List gear for Sub Sections
                        foreach ($equipmentforSubSection as $equipment) {
                            echo "<tr>";
                            echo "<td>" . $equipment['Name'] . "</td>";
                            echo "<td>" . $equipment['Qty'] . "</td>";
                            echo '<td><input id="Qty_' . $equipment['id'] . '" type="number" min="0" max="20" onClick="this.focus();this.select();" onblur="updateQty(' .  $equipment['id'] . ', this.value);"/>' . "</td>";
                            echo '<td name="tdcheck"><input type="checkbox" name="itemchk" id="' . $equipment['id'] . '" /></td>';
                            echo '</tr>';     
                        }
                 }
                echo '</table></div>';
            }

         ?>
            
        

</div>


</body>

<script> 

getVehicleEquipmentStatus();
setInterval(function(){getVehicleEquipmentStatus(); }, 15000);



//TD Click
function onload() {
                var tds = document.getElementsByName("tdcheck");
                for(var i = 0; i < tds.length; i++) {
                    tds[i].onclick = 
                                    function(td) { 
                                        return function() { 
                                            tdOnclick(td); 
                                        }; 
                                    }(tds[i]); 
                }
                var inputs = document.getElementsByTagName("input");
                for(var i = 0; i < inputs.length; i++) {
                    inputs[i].onclick = 
                                    function(input){ 
                                        return function() { 
                                            inputOnclick(input); 
                                        };
                                    }(inputs[i]); 
                }
            }
            function tdOnclick(td) {
                for(var i = 0; i < td.childNodes.length; i++) {
                    if(td.childNodes[i].nodeType == 1) {
                        if(td.childNodes[i].nodeName == "INPUT") {
                            var Qty = document.getElementById("Qty_" + td.childNodes[i].id).value;
                            if(td.childNodes[i].checked) {
                                td.childNodes[i].checked = false;
                                //td.style.backgroundColor = "red";
								//td.className = "Red";
                                
                                console.log(Qty);
                                SetVehicleEquipmentStatus(td.childNodes[i].id, 0, Qty)
                                setTimeout(function(){ getVehicleEquipmentStatus(); }, 1000);
                                //getVehicleEquipmentStatus();
								//UpdateAvailability(td.childNodes[i]);
                            } else {
                                td.childNodes[i].checked = true;
                                //td.style.backgroundColor = "green";
								//td.className = "green";
								//UpdateAvailability(td.childNodes[i]);
                                SetVehicleEquipmentStatus(td.childNodes[i].id, 1, Qty)
                                setTimeout(function(){ getVehicleEquipmentStatus(); }, 1000);
                                //getVehicleEquipmentStatus();
                            }
                        } else {
                            tdOnclick(td.childNodes[i]);
                        }
                    }
                }
            }
            function inputOnclick(input) {
                input.checked = !input.checked;
				
                return false;
            }


</script>


