<?php
class VehicleEquipment{
  
    // database connection and table name
    private $conn;
    private $table_name = "tblVehicleEquipment";
  
    // object properties
    public $id;
    public $idVehicleSection;
    public $subCatID;
    public $Name;
    public $Qty;

  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    


    function getByID(){

    // sanitize
    $this->id=htmlspecialchars(strip_tags($this->id));
  
    // prepare query
    $stmt = $this->conn->prepare("SELECT * FROM tblVehicleEquipment where id=?");
   // $stmt = $this->conn->prepare("SELECT * FROM tblVehicleEquipment");
    // bind values
    $stmt->bind_param("i", $this->id);
    // execute query
    $stmt->execute();
    $result = $stmt->get_result();
		if($result->num_rows != 0) 
		{
			while($row = $result->fetch_assoc()) {
				 $Equipment[] = $row;
			}
			return $Equipment;
		}
		else
		{
			$Equipment[] = "No Records";
			return $Equipment;
		}
		$stmt->close();
      
    }

    function UpdateEquipment(){

        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));
        $this->idVehicleSection=htmlspecialchars(strip_tags($this->idVehicleSection));
        $this->subCatID=htmlspecialchars(strip_tags($this->subCatID));
        $this->Name=htmlspecialchars(strip_tags($this->Name));
        $this->Qty=htmlspecialchars(strip_tags($this->Qty));

  
        // prepare query
        $stmt = $this->conn->prepare("UPDATE tblVehicleEquipment SET idVehicleSection=?, subCatID=?, Name=?, Qty=? where id=?");
        // bind values
        $stmt->bind_param("iisii", $this->idVehicleSection, $this->subCatID, $this->Name, $this->Qty, $this->id);
        // execute query
        if($stmt->execute()){
            return true;
        }
  
        return false;
      
    }

    function DeleteEquipment(){

        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));
  
        // prepare query
        $stmt = $this->conn->prepare("DELETE FROM tblVehicleEquipment where id=?");
        // bind values
        $stmt->bind_param("i", $this->id);
        // execute query
        if($stmt->execute()){
            return true;
        }
  
        return false;
      
    }


}
?>