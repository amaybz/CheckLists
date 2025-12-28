<?

	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	session_start(); // Use session variable on this page.
	date_default_timezone_set('Australia/Sydney');
	require_once('classes/users.php');
	require_once('classes/checklist.php');
	require_once('classes/menu.php');
	require_once('classes/db.php');
	include 'classes/dbconfig.php';

	//$membersdb = new membersdb();
	$users = new users();
	$CheckList = new CheckList();
	$Menu = new Menu();
	//$membersdb->getLoggedInMember(); 
	if ($users->isLoggedIn == 0)
	{
		header("location:login.php"); // Re-direct to main.php
	}
	if ($users->Permission < 1 )
	{
		//echo $users->Permission;
		header("location:login.php"); // Re-direct to main.php
	}
	$Vehicles = $CheckList->getVehicles();
	//echo "permission: " . $membersdb->Permission; 
?>

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

		<div class="container">
		<div class="row justify-content-md-center">
			<div class="col-md-auto">
				Welcome <? echo $membersName; ?>
			</div>
		</div>
		<br>
	<h1>Actions </h1>

	<? 

	 $//this->id=htmlspecialchars(strip_tags($this->id));
  
            // prepare query
            $stmt = $this->conn->prepare("SELECT * FROM tblflag");
            // $stmt = $this->conn->prepare("SELECT * FROM tblVehicleEquipment");
            // bind values
            //$stmt->bind_param("i", $this->id);
            // execute query
            $stmt->execute();
            $result = $stmt->get_result();
		        if($result->num_rows != 0) 
		        {
			        while($row = $result->fetch_assoc()) {
				            $data[] = $row;
			        }
			        return $data;
		        }
		        else
		        {
			        $data[] = "No Records";
			        return $data;
		        }
		        $stmt->close();