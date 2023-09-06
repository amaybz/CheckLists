<?

	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	session_start(); // Use session variable on this page.
	date_default_timezone_set('Australia/Sydney');
	require_once('classes/membersdb.php');
	require_once('classes/checklist.php');
	require_once('classes/menu.php');
	require_once('classes/db.php');
	include 'classes/dbconfig.php';

	$membersdb = new membersdb();
	$CheckList = new CheckList();
	$Menu = new Menu();
	$membersdb->getLoggedInMember(); 
	if ($membersdb->isLoggedIn == 0)
	{
		header("location:login.php"); // Re-direct to main.php
	}
	if ($membersdb->Permission != "EDIT_UNIT")
	{
		header("location:login.php"); // Re-direct to main.php
	}
	$Vehicles = $CheckList->getVehicles();
	//echo "permission: " . $membersdb->Permission; 
?>
<html>
	<script> 

		function addVehicle() {

			var VehicleName = document.getElementById("VehicleName").value;
			var VehicleCallSign = document.getElementById("VehicleCallSign").value;

			//document.write(today);
			var settings = {
			"url": "https://maybzcomputers.com/dptses/check_list/api.php",
			"method": "POST",
			"timeout": 0,
			"headers": {
				"Authorization": "<? echo $APIPassword; ?>",
				"Content-Type": "application/json"
			},
			"data": JSON.stringify({"request":"addVehicle","VehicleName":VehicleName,"VehicleCallSign":VehicleCallSign}),
			};

			$.ajax(settings).done(function (response) {
				//console.log(response);
				data = JSON.parse(response);
				console.log(data[0]);
				if(data[0] !="Invalid Data")
				{
					var table = document.getElementById("tblVehicles");
					var row = table.insertRow(-1);
					var cell1 = row.insertCell(0);
					var cell2 = row.insertCell(1);
					cell1.innerHTML = data[0]["Name"];
					cell2.innerHTML = data[0]["CallSign"];
				}
	
				});
		}
	</script>

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<title>DPT SES - Check Lists</title>
		<link rel="shortcut icon" href="favicon.ico">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	</head>

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<body>


		<? $Menu->Show(0,$membersdb->fullName); ?>

		 
	
		<div class="container">
			<div class="row justify-content-center">
				<p class="text-center">Welcome <? echo $membersdb->fullName; ?></p>
			</div>
				<?
					//print_r($Vehicles);;
					echo '<table id="tblVehicles" class="table table-responsive table-bordered table-striped" >';
					foreach($Vehicles as $Vehicle) {
						$progress = $CheckList->checkProgress($Vehicle['id']);
						echo "<tr><td><a href=adminVehicles.php?idVehicle=" . $Vehicle['id'] . ">" . $Vehicle['Name'] . "</a></td><td> " . $Vehicle['CallSign'] . "</td>"  ;
						echo '<td style="min-width: 100px;"><div class="progress" style="height: 20px;">';
							echo '<div class="progress-bar" role="progressbar" style="width: ' . $progress["Percent"] . '%;" aria-valuenow="' . $progress["ProgressCount"] . '" aria-valuemin="0" aria-valuemax="' . $progress["EquipmentCount"] . '">' . $progress["Percent"] . '%</div>';
						echo '</div></td></tr>';
					}
					echo '</table>';
				?>	
			</div>
			<div name="addVehicle">
				<div class="container">
					<form id="frmVehicles" name="frmVehicles"> 
						<label for="VehicleName">Vehicle Name:</label>
							<input name="VehicleName" id="VehicleName" type="text" class="form-control" />
						<label for="VehicleCallSign"> CallSign:</label>
							<input name="VehicleCallSign" id="VehicleCallSign" type="text" maxlength="7" class="form-control" />
						<input type="button" value="Add" onclick="addVehicle();">
					</form>
				</div>
			</div>
		</div>
	</body>
</html>