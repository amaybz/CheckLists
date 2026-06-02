<?
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    session_start();
    date_default_timezone_set('Australia/Sydney');
    require_once('classes/users.php');
    require_once('classes/checklist.php');
    require_once('classes/db.php');
    require_once('classes/menu.php');
    include 'classes/dbconfig.php';

    $users = new users();
    $Menu = new Menu();
    $CheckList = new CheckList();

    if ($users->isLoggedIn == 0 || $users->Permission < 1)
    {
        header("location:login.php");
        exit;
    }

    $Vehicles = $CheckList->getVehicles();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DPT SES - Manage Vehicles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php $Menu->Show(0,$users->fullName, $users->Permission); ?>
    
    <div class="container mt-4">
        <h3>Manage Vehicles</h3>
        <div class="card mb-4">
            <div class="card-body">
                <h5>Add New Vehicle</h5>
                <div class="row">
                    <div class="col-md-5"><input type="text" class="form-control" id="new-name" placeholder="Name"></div>
                    <div class="col-md-5"><input type="text" class="form-control" id="new-callsign" placeholder="CallSign"></div>
                    <div class="col-md-2"><button type="button" class="btn btn-success" onclick="addVehicle()">Add</button></div>
                </div>
            </div>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>CallSign</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <? foreach($Vehicles as $Vehicle) { ?>
                <tr>
                    <td><input type="text" class="form-control" id="edit-name-<?= $Vehicle['id'] ?>" value="<?= htmlspecialchars($Vehicle['Name']) ?>"></td>
                    <td><input type="text" class="form-control" id="edit-callsign-<?= $Vehicle['id'] ?>" value="<?= htmlspecialchars($Vehicle['CallSign']) ?>"></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary" onclick="editVehicle(<?= $Vehicle['id'] ?>)">Save</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteVehicle(<?= $Vehicle['id'] ?>)">Delete</button>
                    </td>
                </tr>
                <? } ?>
            </tbody>
        </table>
    </div>

    <script>
    function addVehicle() {
        var name = document.getElementById("new-name").value;
        var callsign = document.getElementById("new-callsign").value;

        if(!name || !callsign) { alert("Please enter both Name and CallSign"); return; }

        var settings = {
            "url": "api/vehicles/add/index.php",
            "method": "POST",
            "headers": {
                "Authorization": "<? echo $users->MemberAuthToken; ?>",
                "Content-Type": "application/json"
            },
            "data": JSON.stringify({"Name":name,"CallSign":callsign}),
        };

        $.ajax(settings).done(function (response) {
            alert("Added successfully");
            location.reload();
        }).fail(function(xhr, status, error) {
            console.error(xhr.responseText);
            alert("Error: " + status + " " + error + "\n" + xhr.responseText);
        });
    }

    function editVehicle(id) {
        var name = document.getElementById("edit-name-" + id).value;
        var callsign = document.getElementById("edit-callsign-" + id).value;

        var settings = {
            "url": "api/vehicles/edit/index.php",
            "method": "POST",
            "headers": {
                "Authorization": "<? echo $users->MemberAuthToken; ?>",
                "Content-Type": "application/json"
            },
            "data": JSON.stringify({"id":id,"Name":name,"CallSign":callsign}),
        };

        $.ajax(settings).done(function (response) {
            alert("Updated successfully");
            location.reload();
        }).fail(function(xhr, status, error) {
            console.error(xhr.responseText);
            alert("Error: " + error);
        });
    }

    function deleteVehicle(id) {
        if(!confirm("Are you sure you want to delete this vehicle?")) return;

        var settings = {
            "url": "api/vehicles/delete/index.php",
            "method": "POST",
            "headers": {
                "Authorization": "<? echo $users->MemberAuthToken; ?>",
                "Content-Type": "application/json"
            },
            "data": JSON.stringify({"id":id}),
        };

        $.ajax(settings).done(function (response) {
            location.reload();
        }).fail(function(xhr, status, error) {
            console.error(xhr.responseText);
            alert("Error: " + error);
        });
    }
    </script>
</body>
</html>
