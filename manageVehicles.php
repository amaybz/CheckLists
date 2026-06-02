<?
    session_start();
    date_default_timezone_set('Australia/Sydney');
    require_once('classes/users.php');
    require_once('classes/checklist.php');
    require_once('classes/db.php');
    include 'classes/dbconfig.php';

    $users = new users();
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Dapto Check Lists</a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link active" href="admin.php">Admin</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h3>Manage Vehicles</h3>
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
