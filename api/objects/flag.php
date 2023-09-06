<?php
    class flag{
  
        // database connection and table name
        private $conn;
        private $table_name = "tblflag";
  
        // object properties
        public $id;
        public $issue;
        public $vehicleID;
        public $itemID;
        public $reportedBy;

  
        // constructor with $db as database connection
        public function __construct($db){
            $this->conn = $db;
        }

    


        function getByID(){

            // sanitize
            $this->id=htmlspecialchars(strip_tags($this->id));
  
            // prepare query
            $stmt = $this->conn->prepare("SELECT * FROM tblflag where id=?");
            // $stmt = $this->conn->prepare("SELECT * FROM tblVehicleEquipment");
            // bind values
            $stmt->bind_param("i", $this->id);
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
        }

        function AddFlag(){

            // sanitize
            $this->issue=htmlspecialchars(strip_tags($this->issue));
            $this->vehicleID=htmlspecialchars(strip_tags($this->vehicleID));
            $this->itemID=htmlspecialchars(strip_tags($this->itemID));
            $this->reportedBy=htmlspecialchars(strip_tags($this->reportedBy));

  
            // prepare query
            $stmt = $this->conn->prepare("INSERT INTO `tblflag`(
                                        `issue`,
                                        `vehicleID`,
                                        `itemID`,
                                        `reportedBy`
                                        )
                                        VALUES(
                                           ?,
                                           ?,
                                           ?,
                                           ?,
                                        )");
            // bind values
            $stmt->bind_param("siis", $this->issue, $this->vehicleID, $this->itemID, $this->reportedBy);
            // execute query
            if($stmt->execute()){
                return true;
            }
  
            return false;
        }
    }
?>