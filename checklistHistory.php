<?
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
    session_start(); // Use session variable on this page.
    date_default_timezone_set('Australia/Sydney');
    require_once('classes/users.php');
    require_once('classes/db.php');
    require_once('classes/checklist.php');
    include 'classes/dbconfig.php';
    require_once('classes/menu.php');

    $users = new users();
    $db = new db();
    $CheckList = new CheckList();
    $Menu = new Menu();

    if ($users->isLoggedIn == 0)
    {
        header("location:login.php"); // Re-direct to login.php
    }


    $idSection = isset($_GET["idSection"]) ? $_GET["idSection"] : 0;

    if(isset($_GET["idVehicle"])){
        if(isset($_SESSION['idVehicle']) && $_SESSION['idVehicle'] != $_GET["idVehicle"])
        {
            $idSection = 0;
        }
		$idVehicle = $_GET["idVehicle"];
		$_SESSION['idVehicle'] = $_GET["idVehicle"];
        
	}
	else
	{
		$idVehicle = isset($_SESSION["idVehicle"]) ? $_SESSION["idVehicle"] : 0;
	}
    

    

			

?>

<script> 

function getVehicleEquipmentStatus() {
    filterDate = document.getElementById('datepicker').value
    filterDate = filterDate + ' 00:00:00'
    console.log(filterDate);
    //Clear Check Boxs 
    var inputs = document.getElementsByName("itemchk");
                for(var i = 0; i < inputs.length; i++) {
                    inputs[i].checked = false;
                }
    var inputs = document.getElementsByName("itemqty");
                for(var i = 0; i < inputs.length; i++) {
                    inputs[i].value = "";
                }

    //document.write(today);
    var settings = {
    "url": "api.php",
    "method": "POST",
    "timeout": 0,
    "headers": {
        "Authorization": "<?php echo $APIPassword; ?>",
        "Content-Type": "application/json"
    },
    "data": JSON.stringify({"request":"getVehicleEquipmentStatusAll"}),
    };

    $.ajax(settings).done(function (response) {
        //console.log(response);
        data = JSON.parse(response);
        var i;
        for (i = 0; i < data.length; i++) {
            checkbox = document.getElementById(data[i]['idVehicleEquipment'])
            numValue = document.getElementById("Qty_" + data[i]['idVehicleEquipment'])
            if (checkbox != null){
                if(data[i]['Status'] == 1 && data[i]['Date'] == filterDate)
                {
                    checkbox.checked = true;
                    numValue.value = data[i]['Qty']
                }
                else if (data[i]['Status'] == 0 && data[i]['Date'] == filterDate)
                {
                    checkbox.checked = false;
                    numValue.value = data[i]['Qty']
                 }
            }
        
        }
        });
}



</script>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>DPT SES - Check Lists</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="favicon.ico">
</head>
<script>
    var today = new Date();
  $( function() {
    $( "#datepicker" ).datepicker({
        dateFormat: "yy-mm-dd"
    });
  } );
  // Setter
  
  </script>
<body onload="onload()">

    <?php $Menu->Show(3, $users->fullName); ?>
	
<div class="container">
     <div class="row justify-content-md-center">
        <div class="col-md-auto">
            Welcome <?php echo $users->fullName; ?>
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
            <div class="col-md-auto">
                    Date: 
             </div>
              <div class="col-md-auto">
                <input type="text" id="datepicker" class="form-control" onchange="getVehicleEquipmentStatus()">
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
                            echo '<td><input name="itemqty" disabled id="Qty_' . $equipment['id'] . '" type="number" min="0" max="20"  />' . "</td>";
                            echo '<td name="tdcheck"><input type="checkbox" disabled name="itemchk" id="' . $equipment['id'] . '" /></td>';
                            echo '</tr>';     
                        }
                 }
                echo '</table></div>';
            }

         ?>
            
        

</div>



</body>

<script> 


//TD Click
function onload() {

            
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    datepickervalue = document.getElementById("datepicker");
    datepickervalue.value = yyyy + "-" + mm + "-" + dd;
    getVehicleEquipmentStatus()


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
                // Simplified to prevent recursion stack overflow
                if(td.nodeName === "INPUT") {
                    // Logic for input
                }
            }
            function inputOnclick(input) {
                input.checked = !input.checked;
				
                return false;
            }


</script>


