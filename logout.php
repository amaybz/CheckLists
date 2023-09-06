<?

	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);

	date_default_timezone_set('Australia/Sydney');
	require_once('classes/membersdb.php');

	$membersdb = new membersdb();
	
	$membersdb->logout();

	session_start();
	session_destroy();
	setcookie("token", "",time()-3600);
	header("location:login.php");
?>