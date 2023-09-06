<?

	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	session_start(); // Use session variable on this page.
	date_default_timezone_set('Australia/Sydney');
	require_once('classes/membersdb.php');
	require_once('classes/checklist.php');
	require_once('classes/db.php');
	include 'classes/dbconfig.php';
	require_once('classes/menu.php');

	$membersdb = new membersdb();
	$CheckList = new CheckList();
	$Menu = new Menu();

	if ($membersdb->isLoggedIn == 0)
	{
		header("location:login.php"); // Re-direct to main.php
	}
	
	$Vehicles = $CheckList->getVehicles();
	
	?>

<script> 



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


	<? $Menu->Show(1,$membersdb->fullName); ?>
		 
	
	<div class="container">
		<div class="row justify-content-md-center">
			<div class="col-md-auto">
				Welcome <? echo $membersdb->fullName; ?>
			</div>
		</div>
		<br>

		
		<div class="container">
				<?
				//print_r($Vehicles);;
				echo '<table id="tblVehicles" class="table table-bordered table-striped" >';
				foreach($Vehicles as $Vehicle) {
					$progress = $CheckList->checkProgress($Vehicle['id']);
					echo "<tr><td><a href=checklist.php?idVehicle=" . $Vehicle['id'] . ">" . $Vehicle['Name'] . "</a></td><td> " . $Vehicle['CallSign'] . "</td>"  ;
					echo '<td style="min-width: 100px;"><div class="progress" style="height: 20px;">';
						echo '<div class="progress-bar" role="progressbar" style="width: ' . $progress["Percent"] . '%;" aria-valuenow="' . $progress["ProgressCount"] . '" aria-valuemin="0" aria-valuemax="' . $progress["EquipmentCount"] . '">' . $progress["Percent"] . '%</div>';
					echo '</div></td></tr>';
				}
				echo '</table>';
				?>
		</div>



	</div>
	</body>