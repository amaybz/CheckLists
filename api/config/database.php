<?php
class Database{
  
    // specify your own database credentials
    private $host = "localhost";
    private $db_name = "";
    private $username = "";
    private $password = "";
    public $conn;
  
    // get the database connection
    public function getConnection(){
  
        $this->conn = new mysqli($this->host , $this->username, $this->password, $this->db_name);
            if ( $this->conn->connect_error) {
                echo "DB Connect Failed";
                die('Connect Error (' . $this->conn->connect_error . ') '
                        . $this->conn->connect_error);
            }
            else{
                //echo "DB Connected";
            }
  
        return $this->conn;
    }
}
?>