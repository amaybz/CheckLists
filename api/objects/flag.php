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
        public $subject;

  
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
            $result = $stmt->get_result();  ``
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
            $this->subject=htmlspecialchars(strip_tags($this->subject));
  
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
                                           ?
                                        )");
            // bind values
            $stmt->bind_param("ssss", $this->issue, $this->vehicleID, $this->itemID, $this->reportedBy);
            // execute query
            if($stmt->execute()){

                //send email

                    $txt = '<html>Hi Team<br>
                    <br>
                    an Item has been flagged: ' . $this->subject . '<br>' . 
                    $this->issue . 
                    '<br>Reported By ' . $this->reportedBy . 
                    '<br>
                    <br>
                    Regards<br>
                    Dapto SES Management <br></html>';

                    $to = "aiden.mayberry@member.ses.nsw.gov.au, dpt.logs@ses.nsw.gov.au";
                    //$to = "aiden.mayberry@member.ses.nsw.gov.au, amayberry87@gmail.com";
                    //$to = "amayberry87@gmail.com";
                    $subject = "Dapto SES check lists: " . $this->subject ;
                    $headers = "From: daptochecklists@ajcomputers.com.au" . "\r\n";
                    //$headers .= "CC: aidenmayberry@hotmail.com" . "\r\n";
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

                    
                    
                    
                    //"CC: aidenmayberry@hotmail.com" . "\r\n";
                    


                    mail($to,$subject,$txt,$headers);

                
			        $data[] = "addedflag";
                    $data[] = $to;
                    $data[] = $subject;
                    $data[] = $headers;
                    $data[] = $txt;
			        return $data;
            }
                $data[] = "Failed";
			    return $data;
        }
    }
?>