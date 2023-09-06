<?php
class VehicleSections{
  
    // database connection and table name
    private $conn;
    private $table_name = "tblVehicleEquipment";
  
    // object properties
    public $id;
    public $idVehicle;
    public $Name;


  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    
    function getAllVehicleSubSections(){

        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));
        $this->idVehicle=htmlspecialchars(strip_tags($this->idVehicle));
      
        // prepare query
        $stmt = $this->conn->prepare("SELECT * FROM `tblVehicleSections` where idVehicle=? ORDER BY `tblVehicleSections`.`Name` ASC ");
        // bind values
        $stmt->bind_param("i", $this->idVehicle);
        // execute query
        $stmt->execute();
        $result = $stmt->get_result();
		if($result->num_rows != 0) 
		{
			while($row = $result->fetch_assoc()) {
				 $Sections[] = $row;
			}
		}
		else
		{
			$Sections[] = "No Records";
		}
		$stmt->close();

        $stmt = $this->conn->prepare("SELECT * FROM `tblVehicleSubSections` where IDSection=? ORDER BY `tblVehicleSubSections`.`Name` ASC ");
        // bind values
        $stmt->bind_param("i", $this->IDSection);
        // execute query
        foreach ($Sections as $Section) {
            $this->IDSection = $Section['id'];
            $stmt->execute();
            $result = $stmt->get_result();
		    if($result->num_rows != 0) 
		    {
			    while($row = $result->fetch_assoc()) {
				     $SubSections[] = $row;
			    }
		    }
        }
    
        
		$stmt->close();
    return $SubSections;
    }

}
?>