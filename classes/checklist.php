<?php

class CheckList
{
    private $db;

    public function __construct($dbConnection = null)
    {
        if ($dbConnection) {
            $this->db = $dbConnection;
        } else {
            require_once('db.php');
            $this->db = (new db())->conn;
        }
    }

    private function fetchAll($stmt)
    {
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getEquipmentStatusbyid($idVehicleEquipment)
    {
        $stmt = $this->db->prepare("SELECT * FROM tblEquipmentCheckList WHERE `idVehicleEquipment` = ? ORDER BY `Date` DESC");
        $stmt->bind_param("i", $idVehicleEquipment);
        $result = $this->fetchAll($stmt);
        
        if (empty($result)) {
            return [['Status' => 0]];
        }
        return $result;
    }

    public function getEquipmentStatus()
    {
        $stmt = $this->db->prepare("SELECT * FROM tblEquipmentCheckList WHERE Date >= CURDATE() ORDER BY `Date` DESC");
        $result = $this->fetchAll($stmt);
        
        if (empty($result)) {
            return [['Status' => "no Records"]];
        }
        return $result;
    }

    public function getEquipmentStatusAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM tblEquipmentCheckList ORDER BY `Date` DESC");
        $result = $this->fetchAll($stmt);
        
        if (empty($result)) {
            return [['Status' => "no Records"]];
        }
        return $result;
    }

    public function getVehicles()
    {
        $stmt = $this->db->prepare("SELECT * FROM tblVehicles ORDER BY `CallSign` ASC");
        $result = $this->fetchAll($stmt);
        
        if (empty($result)) {
            return ["no Records"];
        }
        return $result;
    }

    public function getVehiclebyid($idVehicle)
    {
        $stmt = $this->db->prepare("SELECT * FROM tblVehicles where id=? ORDER BY `CallSign` ASC");
        $stmt->bind_param("i", $idVehicle);
        $result = $this->fetchAll($stmt);
        
        if (empty($result)) {
            return ["no Records"];
        }
        return $result;
    }

    public function getSectionsByVehicleID($idVehicle)
    {
        $stmt = $this->db->prepare("SELECT * FROM tblVehicleSections where idVehicle=?");
        $stmt->bind_param("i", $idVehicle);
        $result = $this->fetchAll($stmt);
        
        if (empty($result)) {
            return ["No Records"];
        }
        return $result;
    }

    public function getSubSectionsBySectionID($idSection)
    {
        $stmt = $this->db->prepare("SELECT * FROM tblVehicleSubSections where IDSection=? ORDER BY Name ASC");
        $stmt->bind_param("i", $idSection);
        $result = $this->fetchAll($stmt);
        
        if (empty($result)) {
            return ["No Records"];
        }
        return $result;
    }

    public function getEquipmentBySectionID($idSection)
    {
        $stmt = $this->db->prepare("SELECT * FROM tblVehicleEquipment where idVehicleSection=?");
        $stmt->bind_param("i", $idSection);
        $result = $this->fetchAll($stmt);
        
        if (empty($result)) {
            return ["No Records"];
        }
        return $result;
    }

    public function getEquipmentBySubSectionID($idSubSection)
    {
        $stmt = $this->db->prepare("SELECT * FROM tblVehicleEquipment where subCatID=?");
        $stmt->bind_param("i", $idSubSection);
        $result = $this->fetchAll($stmt);
        
        if (empty($result)) {
            return ["No Records"];
        }
        return $result;
    }

    public function editVehicle($id, $Name, $CallSign)
    {
        $stmt = $this->db->prepare("UPDATE tblVehicles SET Name=?, CallSign=? WHERE id=?");
        $stmt->bind_param("ssi", $Name, $CallSign, $id);
        $stmt->execute();
        return ["status" => "Updated"];
    }

    public function deleteVehicle($id)
    {
        $stmt = $this->db->prepare("DELETE FROM tblVehicles WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return ["status" => "Deleted"];
    }

    public function addVehicle($Name, $CallSign)
    {
        if (empty($Name) || empty($CallSign)) {
            return ["Invalid Data"];
        }
        $stmt = $this->db->prepare("INSERT INTO tblVehicles (Name, CallSign) VALUES (?, ?)");
        $stmt->bind_param("ss", $Name, $CallSign);
        $stmt->execute();
        $id = $this->db->insert_id;
        
        $stmt = $this->db->prepare("SELECT * FROM tblVehicles where id=?");
        $stmt->bind_param("i", $id);
        $result = $this->fetchAll($stmt);
        
        return empty($result) ? ["FAILED TO ADD"] : $result;
    }

    public function addSection($idVehicle, $Name)
    {
        if (empty($idVehicle) || empty($Name)) {
            return ["Invalid Data"];
        }
        $stmt = $this->db->prepare("INSERT INTO tblVehicleSections (idVehicle, Name) VALUES (?, ?)");
        $stmt->bind_param("is", $idVehicle, $Name);
        $stmt->execute();
        $id = $this->db->insert_id;

        $stmt = $this->db->prepare("SELECT * FROM tblVehicleSections where id=?");
        $stmt->bind_param("i", $id);
        $result = $this->fetchAll($stmt);
        
        return empty($result) ? ["FAILED TO ADD"] : $result;
    }

    public function checkProgress($idVehicle)
    {
        $VehicleSections = $this->getSectionsByVehicleID($idVehicle);
        $equipmentcount = 0;
        $ProgressCount = 0;
        
        foreach ($VehicleSections as $Sections) {
            if (isset($Sections['id'])) {
                $SubSections = $this->getSubSectionsBySectionID($Sections['id']);
                foreach ($SubSections as $SubSection) {
                    if (isset($SubSection['ID'])) {
                        $equipment = $this->getEquipmentBySubSectionID($SubSection['ID']);
                        $equipmentcount += count($equipment);
                        foreach ($equipment as $item) {
                            if (isset($item['id'])) {
                                $itemstatus = $this->getEquipmentStatusbyid($item['id']);
                                if ($itemstatus[0]["Status"] == 1 && date("Y-m-d", strtotime($itemstatus[0]["Date"])) == date("Y-m-d")) {
                                    $ProgressCount++;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return [
            "EquipmentCount" => $equipmentcount,
            "ProgressCount" => $ProgressCount,
            "Percent" => $equipmentcount > 0 ? ceil($ProgressCount / $equipmentcount * 100) : 0
        ];
    }

    public function checkProgressByIDSection($idSection)
    {
        $equipmentcount = 0;
        $ProgressCount = 0;
        $SubSections = $this->getSubSectionsBySectionID($idSection);
        
        foreach ($SubSections as $SubSection) {
            if (!isset($SubSection['ID'])) continue;
            
            $equipment = $this->getEquipmentBySubSectionID($SubSection['ID']);
            $equipmentcount += count($equipment);
            
            foreach ($equipment as $item) {
                if (isset($item['id'])) {
                    $itemstatus = $this->getEquipmentStatusbyid($item['id']);
                    if ($itemstatus[0]["Status"] == 1 && date("Y-m-d", strtotime($itemstatus[0]["Date"])) == date("Y-m-d")) {
                        $ProgressCount++;
                    }
                }
            }
        }
        
        return [
            "EquipmentCount" => $equipmentcount,
            "ProgressCount" => $ProgressCount,
            "Percent" => $equipmentcount > 0 ? ceil($ProgressCount / $equipmentcount * 100) : 0
        ];
    }

    public function addSubSection($IDSection, $Name)
    {
        if (empty($IDSection) || empty($Name)) {
            return ["Invalid Data"];
        }
        $stmt = $this->db->prepare("INSERT INTO tblVehicleSubSections (IDSection, Name) VALUES (?, ?)");
        $stmt->bind_param("is", $IDSection, $Name);
        $stmt->execute();
        $id = $this->db->insert_id;

        $stmt = $this->db->prepare("SELECT * FROM tblVehicleSubSections where id=?");
        $stmt->bind_param("i", $id);
        $result = $this->fetchAll($stmt);
        
        return empty($result) ? ["FAILED TO ADD"] : $result;
    }

    public function addVehicleEquipment($idVehicleSection, $subCatID, $Name, $Qty)
    {
        $stmt = $this->db->prepare("INSERT INTO tblVehicleEquipment (idVehicleSection, subCatID, Name, Qty) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $idVehicleSection, $subCatID, $Name, $Qty);
        $stmt->execute();
        $id = $this->db->insert_id;
        
        $stmt = $this->db->prepare("SELECT * FROM tblVehicleEquipment where id=?");
        $stmt->bind_param("i", $id);
        $result = $this->fetchAll($stmt);
        
        return empty($result) ? ["FAILED TO ADD"] : $result;
    }

    public function deleteVehicleEquipment($idequipment)
    {
        $stmt = $this->db->prepare("DELETE FROM tblVehicleEquipment WHERE id=?");
        $stmt->bind_param("i", $idequipment);
        $stmt->execute();
        
        $stmt = $this->db->prepare("SELECT * FROM tblVehicleEquipment where id=?");
        $stmt->bind_param("i", $idequipment);
        $stmt->execute();
        return $stmt->get_result()->num_rows === 0;
    }

    public function setEquipmentStatus($idVehicleEquipment, $Status, $date, $Qty)
    {
        $stmt = $this->db->prepare("UPDATE tblEquipmentCheckList SET Status=?, Qty=? where idVehicleEquipment=? and Date>=?");
        $stmt->bind_param("iiss", $Status, $Qty, $idVehicleEquipment, $date);
        $stmt->execute();
        
        if ($this->db->affected_rows > 0) {
            return ["status" => "Updated", "affectedrows" => $this->db->affected_rows];
        }

        $stmt = $this->db->prepare("SELECT * FROM tblEquipmentCheckList WHERE `idVehicleEquipment` = ? AND Date >= ?");
        $stmt->bind_param("is", $idVehicleEquipment, $date);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows === 0) {
            $stmt = $this->db->prepare("INSERT INTO tblEquipmentCheckList (idVehicleEquipment, Date, Status, Qty) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isii", $idVehicleEquipment, $date, $Status, $Qty);
            $stmt->execute();
            return ["status" => "Added"];
        }
        
        return ["status" => "NoChange"];
    }
}
