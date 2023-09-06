<?

	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);

date_default_timezone_set('Australia/Sydney');
require_once('classes/membersdb.php');

$membersdb = new membersdb();

session_start();

if($_GET['Error'])
	{
		$message="--- ACCESS DENIED---";
    }
    
	$Login=$_POST['Login'];
	
	

    if($Login)
	{ // If clicked on Login button.
		$MemberID=$_POST['username'];
		$password=$_POST['password']; 
		
		
		if($membersdb->login($MemberID, $password) == 1)
		{
				
				
		}
		if ($membersdb->isLoggedIn == 1)
		{
			$message="Logged in";
			header("location:index.php"); // Re-direct to main.php
		}
		else
		{ 
			$message="--- Incorrect Username or Password ---";
		}
			
		}

		
         
    

    ?>
    
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<title>DPT SES</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="favicon.ico">
</head>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<body>





<div class="head">
	<img src="logo.2b1db366.svg" alt="SES Logo" width="20" height="20">
	Dapto Check Lists
</div>

	<div class="menuInline" align="center">
	<? 
	//include 'menu.php';
	?>
		 
	</div>
	
<div class="content">
<form id="form1" name="form1" method="post" action="login.php">
  <div align="center">
    <table width="146" border="0.5">
      <tr>
        <td width="62">Username:</td>
        <td width="68"><input name="username" type="text" id="username" size="12" /></td>
        </tr>
      <tr>
        <td>Password:</td>
        <td><input name="password" type="password" id="password" size="12" /></td>
        </tr>
    </table>
	<? echo $message; ?>
    <p>&nbsp;</p>
    <p>
	    <div class=buttons>
	      <input class="buttons" name="Login" type="submit" value="Login" />
	    </div>
    </p>
  </div>
</form>
</div>
<div class="footer">
  <div class="clear"></div>
</div>
</body>
</html>