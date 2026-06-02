<?
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	session_start(); // Use session variable on this page.
	date_default_timezone_set('Australia/Sydney');
	//require_once('classes/membersdb.php');
	require_once('classes/checklist.php');
	require_once('classes/db.php');
	include 'classes/dbconfig.php';
	require_once('classes/menu.php');
	require_once('classes/users.php');

	//$membersdb = new membersdb();
	$users = new users();
	$CheckList = new CheckList();
	$Menu = new Menu();

	



	if ($users->isLoggedIn == 0)
	{
		header("location:login.php"); // Re-direct to main.php
		echo "NOT LOGGGED IN: ";
		//echo $membersdb->isLoggedIn;
		echo $users->isLoggedIn;

	}
	
	$Vehicles = $CheckList->getVehicles();

	//if($membersdb->isLoggedIn == 1)
	//{$membersName = $membersdb->fullName;}
	if($users->isLoggedIn == 1)
	{$membersName = $users->fullName;}
	
	
	?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<title>DPT SES - Check Lists</title>
	<link rel="shortcut icon" href="favicon.ico">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<body>


	<? $Menu->Show(1,$membersName, $users->Permission); ?>
		 
	
	<div class="container">
		<div class="row justify-content-md-center">
			<div class="col-md-auto">
				Welcome <? echo $membersName; ?>
				<br>
				<?php
				// Get the current week number of the year (1–52)
				$currentWeek = date("W");

				// Cycle it into 1–4
				$weekCycle = (($currentWeek - 1) % 4) + 1;

				echo "Monthly Check - Week: $weekCycle";
				?>
			</div>
		</div>
		<br>

		
		<br>

		
		<div class="container">
				<?
				//print_r($Vehicles);;
				echo '<table id="tblVehicles" class="table table-bordered table-striped table-hover align-middle" >';
				foreach($Vehicles as $Vehicle) {
					$progress = $CheckList->checkProgress($Vehicle['id']);
					echo "<tr>
                            <td class='w-50'><a href=checklist.php?idVehicle=" . $Vehicle['id'] . " class='btn btn-primary btn-sm w-100'>" . $Vehicle['Name'] . "</a></td>
                            <td class='w-50'>";
					echo '<div class="progress" style="height: 25px;">';
						echo '<div class="progress-bar" role="progressbar" style="width: ' . $progress["Percent"] . '%;" aria-valuenow="' . $progress["ProgressCount"] . '" aria-valuemin="0" aria-valuemax="' . $progress["EquipmentCount"] . '">' . $progress["Percent"] . '%</div>';
					echo '</div></td></tr>';
				}
				echo '</table>';
				?>
		</div>



	</div>
	</body>