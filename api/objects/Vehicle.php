<?php
class Vehicle{
  
    // database connection and table name
    private $conn;
    private $table_name = "tblVehicles";
  
    // object properties
    public $id;
    public $Name;
    public $CallSign;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function Create(){
        // sanitize
        $this->Name=htmlspecialchars(strip_tags($this->Name));
        $this->CallSign=htmlspecialchars(strip_tags($this->CallSign));
  
        // prepare query
        $stmt = $this->conn->prepare("INSERT INTO tblVehicles (Name, CallSign) VALUES (?, ?)");
        // bind values
        $stmt->bind_param("ss", $this->Name, $this->CallSign);
        // execute query
        if($stmt->execute()){
            return true;
        }
  
        return false;
      
    }
  
    function Update(){
        // sanitize
        $this->Name=htmlspecialchars(strip_tags($this->Name));
        $this->CallSign=htmlspecialchars(strip_tags($this->CallSign));
        $this->id=htmlspecialchars(strip_tags($this->id));
  
        // prepare query
        $stmt = $this->conn->prepare("UPDATE tblVehicles SET Name=?, CallSign=? where id=?");
        // bind values
        $stmt->bind_param("ssi", $this->Name, $this->CallSign, $this->id);
        // execute query
        if($stmt->execute()){
            return true;
        }
  
        return false;
      
    }

    function Delete(){
        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));
  
        // prepare query
        $stmt = $this->conn->prepare("DELETE FROM tblVehicles where id=?");
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
