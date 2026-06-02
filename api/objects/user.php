<?php
// 'user' object
class User{
 
    // database connection and table name
    private $conn;
    private $table_name = "webusers";
 
    // object properties
    public $id;
    public $username;
    public $name;
    public $password;
    public $access;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
 
    // create new user record
    function create(){
     
        // prepare the query
        $stmt = $this->conn->prepare("INSERT INTO webusers (username, name, password, access) VALUES (?,?,?,?)");
     
        // sanitize
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->access=htmlspecialchars(strip_tags($this->access));
        // hash the password before saving to database
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        // bind the values
        $stmt->bind_param("sssi", $this->username, $this->name, $password_hash , $this->access);
     
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
     
        return false;
    }
     
    // check if given email exist in the database
function UserNameExists(){


    // sanitize
    $this->username=htmlspecialchars(strip_tags($this->username));
 
    // prepare the query
    $stmt = $this->conn->prepare("SELECT * FROM webusers WHERE username=?");
    // bind given username value
    $stmt->bind_param("s", $this->username);
 
    // execute the query
    $stmt->execute();
 
    // get number of rows
    $result = $stmt->get_result();
    $num = $result->num_rows;
    //echo "<br>records: " . $num;
    // if email exists, assign values to object properties for easy access and use for php sessions
    if($num>0){
 
        // get record details / values
        $row = $result->fetch_assoc();
 
        // assign values to object properties
        $this->id = $row['ID'];
        $this->username = $row['username'];
        $this->name = $row['name'];
        $this->password = $row['password'];
        $this->access = $row['access'];

 
        // return true because email exists in the database
        return true;
    }
 
    // return false if email does not exist in the database
    return false;
}

function details(){


    // sanitize
    $this->username=htmlspecialchars(strip_tags($this->username));
 
    // prepare the query
    $stmt = $this->conn->prepare("SELECT ID, 
            username, name, access 
            FROM webusers where username=?");
    $stmt->bind_param("s",  $this->username);

	// execute the query
    $stmt->execute();
    $result = $stmt->get_result();
    // get number of rows
    $RowCount = $result->num_rows;
    if($RowCount>0)
    {
        $data['message'] = "true";
        while($row = $result->fetch_assoc()) {
                $data['data'][] = $row;
        }
    }
    else{
        $data['message'] = "None";
    }
    return $data;
}

function list(){


 
    // prepare the query
    $stmt = $this->conn->prepare("SELECT ID, 
            username, name, access 
            FROM webusers");

	// execute the query
    $stmt->execute();
    $result = $stmt->get_result();
    // get number of rows
    $RowCount = $result->num_rows;
    if($RowCount>0)
    {
        $data['message'] = "true";
        while($row = $result->fetch_assoc()) {
                $data['data'][] = $row;
        }
    }
    else{
        $data['message'] = "None";
    }
    return $data;
}
 
    // update a user record
    public function edit(){
 
 
    $query = "UPDATE webusers
            SET
                name = ?,
                username = ?,
                access = ?
            WHERE id = ?";

    //echo "Query: " . $query;
 
    // prepare the query
    $stmt = $this->conn->prepare("UPDATE webusers
            SET
                name = ?,
                username = ?,
                access = ?
            WHERE id = ?");
 
    // sanitize
    $this->id=htmlspecialchars(strip_tags($this->id));
    $this->name=htmlspecialchars(strip_tags($this->name));
    $this->username=htmlspecialchars(strip_tags($this->username));
    $this->access=htmlspecialchars(strip_tags($this->access));
 
    // bind the values from the form
    $stmt->bind_param("ssii",  $this->name, $this->username, $this->access, $this->id);
 
 
    // execute the query
    if($stmt->execute()){
        return true;
    }
 
    return false;
}

    // reset user password
    public function resetPassword(){
        // prepare the query
        $stmt = $this->conn->prepare("UPDATE webusers SET password = ? WHERE id = ?");
     
        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));
        $this->password=htmlspecialchars(strip_tags($this->password));
        
        // hash the password before saving to database
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
    
        // bind the values
        $stmt->bind_param("si", $password_hash, $this->id);
     
        // execute the query
        if($stmt->execute()){
            return true;
        }
     
        return false;
    }
}
