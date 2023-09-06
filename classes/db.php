<?
class db
{
    public $conn;

    public function __construct()
    {
		// Change the line below to your timezone!
        date_default_timezone_set('Australia/Melbourne');
        
        include 'dbconfig.php';

        $db = new mysqli($DBHost, $DBUser, $DBPassword, $DBNme);
        $this->conn = $db;

        if ($db->connect_error) {
            echo "DB Failed";
            die('Connect Error (' . $db->connect_error . ') '
                    . $db->connect_error);

        }
    }
}

?>