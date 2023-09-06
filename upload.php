<?php
    error_reporting(E_ALL);
	ini_set('display_errors', 1);
    date_default_timezone_set('Australia/Sydney');
//echo print_r($_FILES, true);
//echo print_r($_POST, true);

function compressImage($source, $destination, $quality) { 
    // Get image info 
    $imgInfo = getimagesize($source); 
    $mime = $imgInfo['mime']; 
     
    // Create a new image from file 
    switch($mime){ 
        case 'image/jpeg': 
            $image = imagecreatefromjpeg($source); 
            break; 
        case 'image/png': 
            $image = imagecreatefrompng($source); 
            break; 
        case 'image/gif': 
            $image = imagecreatefromgif($source); 
            break; 
        default: 
            $image = imagecreatefromjpeg($source); 
    } 
     
    // Save image 
    imagejpeg($image, $destination, $quality); 
     
    // Return compressed image 
    return $destination; 
}

$target_dir = "img/";
$target_file = $target_dir . basename($_FILES["file"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$target_file = $target_dir . $_POST["itemid"] . '.jpg';

//echo $target_dir;
//echo $target_file;
//echo $imageFileType;
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES["file"]["tmp_name"]);
  if($check !== false) {
    echo "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
  } else {
    echo "File is not an image.";
    $uploadOk = 0;
  }
}

// Check if file already exists
//if (file_exists($target_file)) {
//  echo "Sorry, file already exists.";
//  $uploadOk = 0;
//}

// Check file size
if ($_FILES["file"]["size"] > 5000000) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    // Compress size and upload image 
            $compressedImage = compressImage($_FILES["file"]["tmp_name"], $target_file, 75); 
             
            if($compressedImage){ 
                 
                $status = 'success'; 
                echo "Image Uploaded successfully."; 
            }else{ 
                echo "Image compress failed!"; 
            } 
      //if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
       // echo "The file ". htmlspecialchars(basename( $_FILES["file"]["name"])). " has been uploaded. tmp: " . $_FILES["file"]["tmp_name"];
      //}
      //else {
	    //
        //echo "Sorry, there was an error uploading your file. Target: " . $target_file . " Name: " . $_FILES["file"]["name"] . print_r($_FILES, true);
      //}
}
?>