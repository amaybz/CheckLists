<?
class CheckList
{
	public function __construct()
    {
		// Change the line below to your timezone!
		date_default_timezone_set('Australia/Melbourne');
	}

	public function getEquipmentStatusbyid($idVehicleEquipment)
    {
		require_once('db.php');
   		$db = new db();
		$stmt = $db->conn->prepare("SELECT * FROM tblEquipmentCheckList WHERE `idVehicleEquipment` = ? ORDER BY `Date` DESC");
		$stmt->bind_param("i", $idVehicleEquipment);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows != 0) 
		{
			while($row = $result->fetch_assoc()) {
				 $EquipmentStatus[] = $row;
			}
			return $EquipmentStatus;
		}
		else
		{
			$EquipmentStatus[] = "no Records";
			return $EquipmentStatus;
		}
		$stmt->close();
		
	}

	public function getEquipmentStatus()
    {
		require_once('db.php');
		$db = new db();
		$stmt = $db->conn->prepare("SELECT * FROM tblEquipmentCheckList ORDER BY `Date` DESC");
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows != 0) 
		{
			while($row = $result->fetch_assoc()) {
				 $EquipmentStatus[] = $row;
			}
			return $EquipmentStatus;
		}
		else
		{
			$EquipmentStatus[] = "no Records";
			return $EquipmentStatus;
		}
		$stmt->close();
		
	}

	public function getVehicles()
    {
    require_once('db.php');
    $db = new db();
		$stmt = $db->conn->prepare("SELECT * FROM tblVehicles ORDER BY `CallSign` ASC");
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows != 0) 
		{
			while($row = $result->fetch_assoc()) {
				 $Vehicles[] = $row;
			}
			return $Vehicles;
		}
		else
		{
			$Vehicles[] = "no Records";
			return $Vehicles;
		}
		$stmt->close();
	}

	public function getVehiclebyid($idVehicle)
    {
    require_once('db.php');
    $db = new db();
		$stmt = $db->conn->prepare("SELECT * FROM tblVehicles where id=? ORDER BY `CallSign` ASC");
		$stmt->bind_param("i", $idVehicle);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows != 0) 
		{
			while($row = $result->fetch_assoc()) {
				 $Vehicles[] = $row;
			}
			return $Vehicles;
		}
		else
		{
			$Vehicles[] = "no Records";
			return $Vehicles;
		}
		$stmt->close();
	}

	public function getSectionsByVehicleID($idVehicle)
    {
    require_once('db.php');
    $db = new db();
		$stmt = $db->conn->prepare("SELECT * FROM tblVehicleSections where idVehicle=?");
		$stmt->bind_param("i", $idVehicle);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows != 0) 
		{
			while($row = $result->fetch_assoc()) {
				 $VehicleSections[] = $row;
			}
			return $VehicleSections;
		}
		else
		{
			$VehicleSections[] = "No Records";
			return $VehicleSections;
		}
		$stmt->close();
	}

	public function getSubSectionsBySectionID($idSection)
    {
    require_once('db.php');
    $db = new db();
		$stmt = $db->conn->prepare("SELECT * FROM tblVehicleSubSections where IDSection=? ORDER BY Name ASC");
		$stmt->bind_param("i", $idSection);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows != 0) 
		{
			while($row = $result->fetch_assoc()) {
				 $VehicleSubSections[] = $row;
			}
			return $VehicleSubSections;
		}
		else
		{
			$VehicleSubSections[] = "No Records";
			return $VehicleSubSections;
		}
		$stmt->close();
	}

	public function getEquipmentBySectionID($idSection)
    {
    require_once('db.php');
    $db = new db();
		$stmt = $db->conn->prepare("SELECT * FROM tblVehicleEquipment where idVehicleSection=?");
		$stmt->bind_param("i", $idSection);
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

	public function getEquipmentBySubSectionID($idSubSection)
    {
    require_once('db.php');
    $db = new db();
		$stmt = $db->conn->prepare("SELECT * FROM tblVehicleEquipment where subCatID=?");
		$stmt->bind_param("i", $idSubSection);
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

	public function addVehicle ($Name, $CallSign)
	{
		require_once('db.php');
		$db = new db();
		//echo "Name: " . $Name;
		//echo " CallSign: " .$CallSign;
		if($Name != "" AND $CallSign != "")
		{
			$stmt = $db->conn->prepare("INSERT INTO tblVehicles (Name, CallSign)  VALUES (?, ?)");
			$stmt->bind_param("ss", $Name, $CallSign);
			$stmt->execute();
			$id = $db->conn->insert_id;
			$stmt = $db->conn->prepare("SELECT * FROM tblVehicles where id=?");
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$result = $stmt->get_result();
			if($result->num_rows != 0) 
			{
				while($row = $result->fetch_assoc()) {
					$Vehicle[] = $row;
				}
				return $Vehicle;
			}
			else
			{
				$Vehicle[] = "FAILED TO ADD";
				return $Vehicle;
			}
		}
		else
		{
			$Vehicle[] = "Invalid Data";
			return $Vehicle;
		}
		
	}

	public function addSection ($idVehicle, $Name)
	{
		require_once('db.php');
		$db = new db();
		if($idVehicle != "" AND $Name != "")
		{
			$stmt = $db->conn->prepare("INSERT INTO tblVehicleSections (idVehicle, Name)  VALUES (?, ?)");
			$stmt->bind_param("is", $idVehicle, $Name);
			$stmt->execute();
			$id = $db->conn->insert_id;
			$stmt = $db->conn->prepare("SELECT * FROM tblVehicleSections where id=?");
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$result = $stmt->get_result();
			if($result->num_rows != 0) 
			{
				while($row = $result->fetch_assoc()) {
					$VehicleSection[] = $row;
				}
				return $VehicleSection;
			}
			else
			{
				$VehicleSection[] = "FAILED TO ADD";
				return $VehicleSection;
			}
		}
		else
		{
			$VehicleSection[] = "Invalid Data";
			return $VehicleSection;
		}
	}

	public function checkProgress ($idVehicle)
	{
		$vehicle = $this->getVehiclebyid($idVehicle);
		$VehicleSections = $this->getSectionsByVehicleID($idVehicle);
		$equipmentcount = 0;
		$ProgressCount = 0;
		foreach($VehicleSections as $Sections) {
            $SubSections = $this->getSubSectionsBySectionID($Sections['id']);
            foreach($SubSections as $SubSection) {
                $equipment = $this->getEquipmentBySubSectionID($SubSection['ID']);
				$equipmentcount = $equipmentcount + count($equipment);
                foreach($equipment as $item) {
                    $itemstatus = $this->getEquipmentStatusbyid($item['id']);

					if($itemstatus[0][Status] == 1 and date("Y-m-d", strtotime($itemstatus[0][Date]) ) == date("Y-m-d"))
					{
						
						$ProgressCount++;
					}
                }
            }
        }
		$Progress["EquipmentCount"] = $equipmentcount;
		$Progress["ProgressCount"] = $ProgressCount;
		$Progress["Percent"] = ceil($Progress["ProgressCount"]/$Progress["EquipmentCount"] * 100);


		return $Progress;
	}

	public function checkProgressByIDSection ($idSection)
	{
		$equipmentcount = 0;
		$ProgressCount = 0;
            $SubSections = $this->getSubSectionsBySectionID($idSection);
            foreach($SubSections as $SubSection) {
                $equipment = $this->getEquipmentBySubSectionID($SubSection['ID']);
				$equipmentcount = $equipmentcount + count($equipment);
                foreach($equipment as $item) {
                    $itemstatus = $this->getEquipmentStatusbyid($item['id']);

					if($itemstatus[0][Status] == 1 and date("Y-m-d", strtotime($itemstatus[0][Date]) ) == date("Y-m-d"))
					{
						
						$ProgressCount++;
					}
                }
            }
        
		$Progress["EquipmentCount"] = $equipmentcount;
		$Progress["ProgressCount"] = $ProgressCount;
		$Progress["Percent"] = ceil($Progress["ProgressCount"]/$Progress["EquipmentCount"] * 100);


		return $Progress;
	}

	public function addSubSection ($IDSection, $Name)
	{
		require_once('db.php');
		$db = new db();
		if($IDSection != "" AND $Name != "")
		{
			$stmt = $db->conn->prepare("INSERT INTO tblVehicleSubSections (IDSection, Name)  VALUES (?, ?)");
			$stmt->bind_param("is", $IDSection, $Name);
			$stmt->execute();
			$id = $db->conn->insert_id;
			$stmt = $db->conn->prepare("SELECT * FROM tblVehicleSubSections where id=?");
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$result = $stmt->get_result();
			if($result->num_rows != 0) 
			{
				while($row = $result->fetch_assoc()) {
					$VehicleSubSection[] = $row;
				}
				return $VehicleSubSection;
			}
			else
			{
				$VehicleSubSection[] = "FAILED TO ADD";
				return $VehicleSubSection;
			}
		}
		else
		{
			$VehicleSubSection[] = "Invalid Data";
			return $VehicleSubSection;
		}
	}

	public function addVehicleEquipment ($idVehicleSection, $subCatID, $Name, $Qty)
	{
		require_once('db.php');
		$db = new db();
		$stmt = $db->conn->prepare("INSERT INTO tblVehicleEquipment (idVehicleSection, subCatID, Name, Qty)  VALUES (?, ?, ?, ?)");
		$stmt->bind_param("iisi", $idVehicleSection, $subCatID, $Name, $Qty);
		$stmt->execute();
		$id = $db->conn->insert_id;
		$stmt = $db->conn->prepare("SELECT * FROM tblVehicleEquipment where id=?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows != 0) 
		{
			while($row = $result->fetch_assoc()) {
				$Vehicle[] = $row;
			}
			return $Vehicle;
		}
		else
		{
			$Vehicle[] = "FAILED TO ADD";
			return $Vehicle;
		}

	}

	public function deleteVehicleEquipment ($idequipment)
	{
		require_once('db.php');
		$db = new db();
		$stmt = $db->conn->prepare("DELETE FROM tblVehicleEquipment WHERE id=?");
		$stmt->bind_param("i", $idequipment);
		$stmt->execute();
		$stmt = $db->conn->prepare("SELECT * FROM tblVehicleEquipment where id=?");
		$stmt->bind_param("i", $idequipment);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows != 0) 
		{
			return false;
		}
		else
		{
			return true;
		}

	}
	

	public function setEquipmentStatus($idVehicleEquipment, $Status, $date, $Qty)
    {
		require_once('db.php');
		$db = new db();
		//echo "UPDATE tblEquipmentCheckList SET Status=$Status where idVehicleEquipment=$idVehicleEquipment and Date>='$date'";
		$stmt = $db->conn->prepare("UPDATE tblEquipmentCheckList SET Status=?, Qty=? where idVehicleEquipment=? and Date>=?");
		$stmt->bind_param("iiss", $Status, $Qty, $idVehicleEquipment, $date);
		$stmt->execute();
		if($stmt->affected_rows > 0)
		{
			$insert['status'] = "Updated";
			$insert['affectedrows'] = $stmt->affected_rows;
			return $insert;

		}
		else
		{
			$stmts = $db->conn->prepare("SELECT * FROM tblEquipmentCheckList WHERE `idVehicleEquipment` = ? AND Date >= ?");
							$stmts->bind_param("is", $idVehicleEquipment, $date);
							$stmts->execute();
							$results = $stmts->get_result();
							if($results->num_rows == 0) 
							{
								//Insert Row
								//echo "add required";
								// prepare and bind
								$stmt = $db->conn->prepare("INSERT INTO tblEquipmentCheckList (idVehicleEquipment, Date, Status, Qty)  VALUES (?, ?, ?, ?)");
								$stmt->bind_param("isii", $idVehicleEquipment, $date, $Status, $Qty);
								$stmt->execute();	
								$insert['status'] = "Added";
								return $insert;
							}	
							else
							{
								$insert['status'] = "NoChange";
								return $insert;
							}
			

		}
		
	}

}

?>