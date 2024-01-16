<?php

error_reporting(0);

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

include '../utils/database.php';
include '../utils/helper.php';


$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
mysqli_set_charset($conn, "utf8");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$userId = convertPersianToEnglish($_POST["userId"]);
$title = convertPersianToEnglish($_POST["title"]);
$description = convertPersianToEnglish($_POST["description"]);
$targets = convertPersianToEnglish($_POST["targets"]);
$standards = convertPersianToEnglish($_POST["standards"]);

$currentTime = time();
$token = null;
$headers = apache_request_headers();

//if (isset($headers['Authorization'])) {
 // $token = $headers['Authorization'];
//} else {
 // $response['success'] = 'false';
 // $response['message'] = '403';
//  $json_response = json_encode($response);
//  echo $json_response;
//  die();
//}

// $result = $conn->query("SELECT * FROM users WHERE `userId`='$userId' AND `token`='$token'");
// if ($result->num_rows == 0) {
//   $response['success'] = 'false';
//   $response['message'] = "403";
//   $json_response = json_encode($response);
//   echo $json_response;
//   die();
// }

if ($conn->query("INSERT INTO needs(`userId` , `title` , `description` , `targets` , `standards` , `created_at` , `updated_at`) VALUES('$userId' , '$title' , '$description' , '$targets' , '$standards' , '$currentTime' , '$currentTime')")) {

  $response['success'] = 'true';
  $response['message'] = 'نیاز شما ثبت شد و پس از بررسی نتیجه به شما اطلاع داده می شود';
  $json_response = json_encode($response);
  echo $json_response;
  die();
  
} else {
  $response['success'] = 'false';
  $response['message'] = 'مشکل در ذخیره سازی اطلاعات. لطفا بعدا تلاش کنید.';
  $json_response = json_encode($response);
  echo $json_response;

  die();
}
