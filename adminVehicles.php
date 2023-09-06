<?

	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
    session_start(); // Use session variable on this page.
    date_default_timezone_set('Australia/Sydney');
    require_once('classes/membersdb.php');
    require_once('classes/checklist.php');
    require_once('classes/db.php');
    include_once 'api/objects/VehicleSections.php';
    include_once 'api/config/database.php';

    include 'classes/dbconfig.php';

    $membersdb = new membersdb();
    $CheckList = new CheckList();

    // get database connection
    $database = new Database();
    $apidb = $database->getConnection();

    
    

    if ($membersdb->isLoggedIn == 0)
    {
        header("location:login.php"); // Re-direct to main.php
    }

    if($_GET["idVehicle"]){
        $idVehicle = $_GET["idVehicle"];
	}
    $vehicle = $CheckList->getVehiclebyid($idVehicle);

    $VehicleSections = $CheckList->getSectionsByVehicleID($idVehicle);
    // instantiate object
    $APIVehicleSections = new VehicleSections($apidb);
    // set product property values
    $APIVehicleSections->idVehicle = $idVehicle;
    $AllVehicleSubSections = $APIVehicleSections->getAllVehicleSubSections();
    ?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
    <meta http-equiv="Content-Type" content="text/html, charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>DPT SES - CL - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="favicon.ico">
</head>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script> 

function addEquipment(idSection, IdSubSection) {
    var evt = event.srcElement.id;
    var btn_clicked = document.getElementById(evt);
    var tr_referred = btn_clicked.parentNode.parentNode;
    console.log('EVT: ' + evt);
    var SubCatDropdown = document.getElementById("subCatid_"+ IdSubSection);
    var itemName = document.getElementById("itemName_" + IdSubSection).value;
    var itemQty = document.getElementById("itemQty_"+ IdSubSection).value;
    var itemSubCat = document.getElementById("subCatid_"+ IdSubSection).value;
    var idVehicle = <? echo $idVehicle; ?>

    //document.write(today);
    var settings = {
    "url": "https://ajcomputers.com.au/dptses/check_list/api.php",
    "method": "POST",
    "timeout": 0,
    "headers": {
        "Authorization": "<? echo $APIPassword; ?>",
        "Content-Type": "application/json"
    },
    "data": JSON.stringify({"request":"addEquipment","idVehicleSection":idSection,"subCatID":itemSubCat,"Name":itemName,"Qty":itemQty }),
    };

    $.ajax(settings).done(function (response) {
        //console.log(response);
        data = JSON.parse(response);
        console.log(data[0]);
            if(data[0] !="Invalid Data")
            {
                var td1 = document.createElement('td');
                td1.innerHTML = data[0]["Name"];
                var td2 = document.createElement('td');
                td2.innerHTML = data[0]["Qty"];
                var tr = document.createElement('tr');
                tr.appendChild(td1);
                tr.appendChild(td2);
                tr_referred.parentNode.insertBefore(tr, tr_referred );
                return tr;
            }
    });
}

function addSection() {

    var secName = document.getElementById("Section-name").value;
    var idVehicle = <? echo $idVehicle; ?>

    //document.write(today);
    var settings = {
    "url": "https://ajcomputers.com.au/dptses/check_list/api.php",
    "method": "POST",
    "timeout": 0,
    "headers": {
        "Authorization": "<? echo $APIPassword; ?>",
        "Content-Type": "application/json"
    },
    "data": JSON.stringify({"request":"addVehicleSection","idVehicle":idVehicle,"Name":secName }),
    };

    $.ajax(settings).done(function (response) {
        //console.log(response);
        data = JSON.parse(response);
        console.log(data[0]);
            if(data[0] !="Invalid Data")
            {
                return data[0];
                
            }
    }); 
    location.reload();
}

function addSubSection() {

    var secName = document.getElementById("SubSection-name").value;
    var e = document.getElementById("SelectSection");
    var IDSection = e.value;

    //document.write(today);
    var settings = {
    "url": "https://ajcomputers.com.au/dptses/check_list/api.php",
    "method": "POST",
    "timeout": 0,
    "headers": {
        "Authorization": "<? echo $APIPassword; ?>",
        "Content-Type": "application/json"
    },
    "data": JSON.stringify({"request":"addVehicleSubSection","IDSection":IDSection,"Name":secName }),
    };

    $.ajax(settings).done(function (response) {
        //console.log(response);
        data = JSON.parse(response);
        console.log(data[0]);
            if(data[0] !="Invalid Data")
            {
                return data[0];
                
            }
    });
    location.reload();
}

function getEquipment(id) {

    //document.write(today);
    var settings = {
    "url": "<? echo $APIAddress; ?>VehicleEquipment/get/index.php",
    "method": "POST",
    "timeout": 0,
    "headers": {
        "Authorization": "Bearer <? echo $membersdb->MemberAuthToken; ?>",
        "Content-Type": "application/json"
    },
    "data": JSON.stringify({"id":id}),
    };

    $.ajax(settings).done(function (response) {
        //console.log(response);
        //data = JSON.parse(response);
        data = response;
        console.log(data[0]);
            if(data[0] !="Invalid Data")
            {
                var ModelItemName = document.getElementById('ItemName')
                var ModelItemQty = document.getElementById('ItemQty')
                var ModelEditSection = document.getElementById('EditSubSection')
                
                ModelItemName.value = data[0]['Name']
                ModelItemQty.value = data[0]['Qty']
                ModelEditSection.value = data[0]['subCatID']
                return data[0];
                
            }
    });
}

function DeleteEquipment() {

    //document.write(today);
    var ItemID = document.getElementById('itemid')
    var settings = {
    "url": "<? echo $APIAddress; ?>VehicleEquipment/delete/index.php",
    "method": "POST",
    "timeout": 0,
    "headers": {
        "Authorization": "Bearer <? echo $membersdb->MemberAuthToken; ?>",
        "Content-Type": "application/json"
    },
    "data": JSON.stringify({"id":ItemID}),
    };

    $.ajax(settings).done(function (response) {
        //console.log(response);
        //data = JSON.parse(response);
        data = response;
        console.log(data[0]);
            if(data[0] !="Invalid Data")
            {

               location.reload();
                
            }
    });
}

function EditEquipment() {

    var ModelItemID = document.getElementById('itemid')
    var ModelItemName = document.getElementById('ItemName')
    var ModelItemQty = document.getElementById('ItemQty')
    var ModelSubSection = document.getElementById("EditSubSection");
    //var ModelIDsection = $(#EditSubSection).find(':selected').data('idsection');
    var ModelIDSection = ModelSubSection.querySelector(':checked').getAttribute('data-idsection');
    console.log(ModelIDSection);

    //document.write(today);
    var settings = {
    "url": "<? echo $APIAddress; ?>VehicleEquipment/edit/index.php",
    "method": "POST",
    "timeout": 0,
    "headers": {
        "Authorization": "Bearer <? echo $membersdb->MemberAuthToken; ?>",
        "Content-Type": "application/json"
    },
    "data": JSON.stringify({"id":ModelItemID.value,"Name":ModelItemName.value,"Qty": ModelItemQty.value, "subCatID": ModelSubSection.value, "idVehicleSection": ModelIDSection}),
    };

    $.ajax(settings).done(function (response) {
        //console.log(response);
        //data = JSON.parse(response);
        data = response;
        console.log(data);
        location.reload();
    });
}

</script>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		  <div class="container-fluid">
					<a class="navbar-brand" href="index.php">
			<img src="logo.2b1db366.svg" alt="SES Logo" width="30" height="24" class="d-inline-block align-text-top">
			Dapto Check Lists
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
			  <span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNavAltMarkup">
			  <div class="navbar-nav">
				<a class="nav-link"  href="index.php">Home</a>
				<a class="nav-link active" aria-current="page" href="admin.php">Admin</a>
			  </div>
			</div>
		  </div>
	</nav>
	
    <div class="container">

    

        <h3><? echo $vehicle[0]["Name"]; ?></h3>
        <br>
        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addsectionModal" data-bs-record="0">Add Section</button>
        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addsubsectionModal" data-bs-record="0">Add Sub-Section</button>
        

        <br>
        <br>
  
       <form id="frmequipment" name="frmequipment">
        <?
        //print_r($Vehicles);;
        echo '<table id="tblVehicleEquipment" class="table table-striped">';
        foreach($VehicleSections as $Sections) {
            echo "<tr class='table-info'><td colspan=4><b>" . $Sections['Name'] . "</b></td></tr>";
            
            $SubSections = $CheckList->getSubSectionsBySectionID($Sections['id']);
            foreach($SubSections as $SubSection) {
                echo "<tr class='table-dark'><td colspan=4><b>" . $SubSection['Name'] . "</b></td></tr>";
                $equipment = $CheckList->getEquipmentBySubSectionID($SubSection['ID']);
                foreach($equipment as $item) {
                     
                    echo "<tr><td>" . $item['Name'] . "</td><td>" . $item['Qty'] . "</td>"; 
                    echo '<td><button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#EditItemModel" data-bs-record="'. $item["id"] . '">Edit</button></td>';
                    echo '<td><button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModel" data-bs-record="'. $item["id"] . '">DELETE</button></td></tr>';
                }
                echo '<tr><td><input name="itemName" id="itemName_' . $SubSection['ID'] . '" type="text" /></td>';
                echo '<td><input name="qty" id="itemQty_' . $SubSection['ID'] . '" type="number" min="0" max="20" /></td>';
                echo '<td>';
                echo '<input type="hidden" name="subCatid" id="subCatid_' . $SubSection['ID'] . '" value=' . $SubSection['ID'] . '>';
                echo '</td>';
                echo '<td> <input id="btn' . $SubSection['ID'] . '" type="button" class="btn btn-dark" value="Add" onclick="addEquipment(' . $Sections['id'] . ',' . $SubSection['ID'] . ');"></td></tr>';

            }
            
            
        }
        echo '</table>';
        ?>
        </form>

    </div>

    <script>

    function SavePhoto()  {
                var uploadbutton = document.getElementById('uploadbutton')
                uploadbutton.innerHTML = "Uploading."

                var fd = new FormData();    
                var allfiles = document.getElementById("itemImage").files;
                var lastfile = allfiles[allfiles.length - 1];
                uploadbutton.innerHTML = "Uploading.."

                console.log(lastfile);
                fd.append( 'file', lastfile);
                fd.append( 'itemid', document.getElementById("itemid").value);
                uploadbutton.innerHTML = "Uploading..."
                $.ajax({
                  url: 'https://ajcomputers.com.au/dptses/check_list/upload.php',
                  data: fd,
                  processData: false,
                  contentType: false,
                  type: 'POST',
                  success: function(data){
                    alert(data);
                    var formfile = document.getElementById("itemImage");
                    try {
                        formfile.value = null;
                      } catch(ex) { }
                      if (formfile.value) {
                        formfile.parentNode.replaceChild(formfile.cloneNode(true), formfile);
                      }
                      
                      
                      var ModelItemID = document.getElementById('itemid')
                      var Modeldisplayimage = document.getElementById('displayimage')
                      Modeldisplayimage.src = "img/" + ModelItemID.value  + ".jpg?n=1"
                      var uploadbutton = document.getElementById('uploadbutton')
                      uploadbutton.innerHTML = "Upload"
                  }
                });
     }
  </script>

  


<div class="modal fade" data-bs-backdrop="static" id="EditItemModel" tabindex="-1" aria-labelledby="EditItemModelLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="EditItemTitle">Edit Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="imageupload" name="imageupload" enctype="multipart/form-data" onsubmit="event.preventDefault();" role="form">
      <div class="modal-body">
      
          <div class="mb-3">
          
            <div class="form-group">
            <div class="text-center">
                  <img id="displayimage" src="img/8.jpg" style="width:128px;" class="figure-img img-fluid img-thumbnail rounded mx-auto d-block" alt="...">
          </div>
          <br>
                <input type="hidden" name="itemid" id="itemid" value="">
                <label for="itemImage">Image of Item:</label>
                <input type="file" name="itemImage" id="itemImage" accept="image/*">
                <button id="uploadbutton" class="btn btn-primary" onclick="SavePhoto()"> Upload </button>
                <br>
                <label for="ItemName">Name:</label>
                <input type="text" class="form-control" name="ItemName" id="ItemName" >
                <label for="ItemQty">Expected Qty:</label>
                <input type="num" class="form-control" name="ItemQty" id="ItemQty" >
                </div>
          </div>
          <div class="mb-3">
            <label for="SelectSubSection" class="col-form-label">Sub Section:</label>
            <select class="form-select" id="EditSubSection"> 
            <? 
                foreach($AllVehicleSubSections as $SubSection) {
                    echo '<option value="' . $SubSection["ID"] . '" data-idsection="' . $SubSection["IDSection"] . '">' . $SubSection["Name"] . '</option>';
                }
            ?>
            </select>
          </div>
          
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" id="Save-button" class="btn btn-primary" onclick="EditEquipment()"> Save </input>
      </div>
      </form>
    </div>
  </div>
</div>
    

<div class="modal fade" data-bs-backdrop="static" id="addsectionModal" tabindex="-1" aria-labelledby="addsectionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="AddSectionTitle">Add Section</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="Section-name" class="col-form-label">Section Name:</label>
            <input type="text" class="form-control" id="Section-name">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" onclick="addSection();" class="btn btn-primary">Add</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteModel" tabindex="-1" aria-labelledby="deleteModelLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="DeleteModelTitle">Delete Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <p>Comfirm Delete?
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" name="btnDelete" class="btn btn-danger">DELETE</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" data-bs-backdrop="static" id="addsubsectionModal" tabindex="-1" aria-labelledby="addsubsectionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="AddSubSectionTitle">Add Sub-Section</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="SelectSection" class="col-form-label">Section:</label>
            <select class="form-select" id="SelectSection"> 
            <? 
                foreach($VehicleSections as $Sections) {
                    echo '<option value="' . $Sections["id"] . '">' . $Sections["Name"] . '</option>';
                }
            ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="SubSection-name" class="col-form-label">Sub-Section Name:</label>
            <input type="text" class="form-control" id="SubSection-name">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" onClick="addSubSection()" class="btn btn-primary">Add</button>
      </div>
    </div>
  </div>
</div>
<script>

var EditItemModel = document.getElementById('EditItemModel')
        EditItemModel.addEventListener('show.bs.modal', function (event) {
          // Button that triggered the modal
          var button = event.relatedTarget 
          // Extract info from data-bs-* attributes
          var recordid = button.getAttribute('data-bs-record')
          var ModelItemID = document.getElementById('itemid')
          var Modeldisplayimage = document.getElementById('displayimage')
          ModelItemID.value = recordid
          Modeldisplayimage.src = "img/" + recordid + ".jpg"
           
          getEquipment(recordid)
          
          // If necessary, you could initiate an AJAX request here
          // and then do the updating in a callback.
          //
          // Update the modal's content.
          //var modalTitle = exampleModal.querySelector('.modal-title')
          //var modalBodyInput = exampleModal.querySelector('.modal-body input')

          //modalTitle.textContent = 'New message to ' + recipient
          //modalBodyInput.value = recipient
        })

        var btnDelete = document.getElementsByName('btnDelete')
        
        btnDelete.addEventListener('click', function(event){
                var delbutton = event.relatedTarget 
                var recordid = button.getAttribute('data-bs-record')
                console.log('Button Clicked' & recordid);
         });


$(document).ready(function(){	
	$("#imageupload").submit(function(event){
		return false;
	});
});
</script>