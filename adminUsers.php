<?

	error_reporting(E_ALL);
	ini_set('display_errors', 1);
    session_start(); // Use session variable on this page.
    date_default_timezone_set('Australia/Sydney');
    require_once('classes/users.php');
    require_once('classes/checklist.php');
    require_once('classes/menu.php');
    require_once('classes/db.php');
    include_once 'api/objects/user.php';
    include_once 'api/config/database.php';

    include 'classes/dbconfig.php';

	$users = new users();
    $CheckList = new CheckList();

    // get database connection
    $database = new Database();
    $apidb = $database->getConnection();

    
    

    if ($users->isLoggedIn == 0)
	{
		header("location:login.php"); // Re-direct to main.php
	}
	if ($users->Permission < 1 )
	{
		//echo $users->Permission;
		header("location:login.php"); // Re-direct to main.php
	}




    // instantiate object
    $APIUser = new User($apidb);
    // set product property values
    $allUsers = $APIUser->list();
    ?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
    <meta http-equiv="Content-Type" content="text/html, charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>DPT SES - CL - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="favicon.ico">
</head>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script> 

function addUser() {
    var name = document.getElementById("add-name").value;
    var username = document.getElementById("add-username").value;
    var password = document.getElementById("add-password").value;
    var access = document.getElementById("add-access").value;

    var settings = {
    "url": "<? echo $APIAddress; ?>users/add/index.php",
    "method": "POST",
    "timeout": 0,
    "headers": {
        "Authorization": "<? echo $users->MemberAuthToken; ?>",
        "Content-Type": "application/json"
    },
    "data": JSON.stringify({"username":username,"name":name,"password":password, "access":access }),
    };

    $.ajax(settings).done(function (response) {
        location.reload();
    });
}

function editUser(id) {
    var name = document.getElementById("edit-name-" + id).value;
    var username = document.getElementById("edit-username-" + id).value;
    var access = document.getElementById("edit-access-" + id).value;

    var settings = {
    "url": "<? echo $APIAddress; ?>users/edit/index.php",
    "method": "POST",
    "timeout": 0,
    "headers": {
        "Authorization": "<? echo $users->MemberAuthToken; ?>",
        "Content-Type": "application/json"
    },
    "data": JSON.stringify({"id":id,"username":username,"name":name,"access":access }),
    };

    $.ajax(settings).done(function (response) {
        location.reload();
    });
}

function deleteUser(id) {
    if(!confirm("Are you sure you want to delete this user?")) return;

    var settings = {
    "url": "<? echo $APIAddress; ?>users/delete/index.php",
    "method": "POST",
    "timeout": 0,
    "headers": {
        "Authorization": "<? echo $users->MemberAuthToken; ?>",
        "Content-Type": "application/json"
    },
    "data": JSON.stringify({"id":id}),
    };

    $.ajax(settings).done(function (response) {
        location.reload();
    });
}

function resetPassword(userID) {
    var newPassword = prompt("Enter new password:");
    if (newPassword == null || newPassword == "") {
        return;
    }

    var settings = {
    "url": "<? echo $APIAddress; ?>users/resetPassword/index.php",
    "method": "POST",
    "timeout": 0,
    "headers": {
        "Authorization": "<? echo $users->MemberAuthToken; ?>",
        "Content-Type": "application/json"
    },
    "data": JSON.stringify({"id":userID,"password":newPassword}),
    };

    $.ajax(settings).done(function (response) {
        alert("Password updated successfully.");
    });
}
</script>
<body>

    <?
    $Menu = new Menu();
    $Menu->Show(0,$users->fullName, $users->Permission);
    ?>
	
    <div class="container">
        <br>
        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
        

        <br>
        <br>
  
        <?
        echo '<table class="table table-striped">';
        echo '<thead><tr><th>Name</th><th>Username</th><th>Access</th><th>Actions</th></tr></thead>';
        if($allUsers['data'] != "No Records"){
                foreach($allUsers['data'] as $user) {
                    echo "<tr>";
                    echo "<td><input type='text' class='form-control' id='edit-name-" . $user['ID'] . "' value='" . $user['name'] . "'></td>";
                    echo "<td><input type='text' class='form-control' id='edit-username-" . $user['ID'] . "' value='" . $user['username'] . "'></td>";
                    echo "<td><input type='number' class='form-control' id='edit-access-" . $user['ID'] . "' value='" . $user['access'] . "'></td>";
                    echo "<td>";
                    echo "<button type='button' class='btn btn-sm btn-primary' onclick='editUser(" . $user['ID'] . ")'>Save</button> ";
                    echo "<button type='button' class='btn btn-sm btn-warning' onclick='resetPassword(" . $user['ID'] . ")'>Reset Pwd</button> ";
                    echo "<button type='button' class='btn btn-sm btn-danger' onclick='deleteUser(" . $user['ID'] . ")'>Delete</button>";
                    echo "</td>";
                    echo "</tr>";
            }
        }
        echo '</table>';
        ?>

    </div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="AddUserTitle">Add User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="add-name" class="col-form-label">Name:</label>
            <input type="text" class="form-control" id="add-name">
          </div>
          <div class="mb-3">
            <label for="add-username" class="col-form-label">Username:</label>
            <input type="text" class="form-control" id="add-username">
          </div>
          <div class="mb-3">
            <label for="add-password" class="col-form-label">Password:</label>
            <input type="password" class="form-control" id="add-password" autocomplete="new-password">
          </div>
          <div class="mb-3">
            <label for="add-access" class="col-form-label">Access Level:</label>
            <input type="number" class="form-control" id="add-access">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" onclick="addUser();" class="btn btn-primary">Add</button>
      </div>
    </div>
  </div>
</div>
</body>
