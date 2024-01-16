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

$filters = array(
  array(
    "label" => "تاریخ",
    "value" => "date",
  ),
  array(
    "label" => "امتیاز",
    "value" => "rate",
  )
);

$currentTime = time();
$token = null;
$headers = apache_request_headers();

//if (isset($headers['Authorization'])) {
//  $token = $headers['Authorization'];
//} else {
 // $response['success'] = 'false';
//  $response['message'] = '403';
 // $json_response = json_encode($response);
 // echo $json_response;
 // die();
//}

// $result = $conn->query("SELECT * FROM users WHERE `userId`='$userId' AND `token`='$token'");
// if ($result->num_rows == 0) {
//   $response['success'] = 'false';
//   $response['message'] = "403";
//   $json_response = json_encode($response);
//   echo $json_response;
//   die();
// }

$query = "SELECT * FROM needs";
if (isset($_POST["userId"]) and $_POST["userId"] != null){
  $query = "SELECT * FROM needs WHERE `userId`='$userId'";
}
if (isset($_GET["needId"]) and $_GET["needId"] != null){
  $needId = $_GET["needId"];
  $query = "SELECT * FROM needs WHERE `id`='$needId'";
}
if ($_POST["status"] == "accepted"){
  $query = "SELECT * FROM needs WHERE `status`='accepted'";
}


$needs = array();
$result = $conn->query($query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {

    $status = $row["status"];
    if ($status == "accepted"){
      $status = "تایید شده";
    }
    if ($status == "pending"){
      $status = "در انتظار تایید";
    }
    if ($status == "denied"){
      $status = "رد شده";
    }

    $res["needId"] = $row["id"];
    $res["status"] = array(
      "key" => $row["status"],
      "title" => getTitleFromStatusKey($row["status"])
  );
    $res["items"] = array(
      array(
        "title" => "عنوان نیاز",
        "value" => $row["title"],
        "key" => "title",
      ),
      array(
        "title" => "توضیحات نیاز",
        "value" => $row["description"],
        "key" => "description",
      ),
      array(
        "title" => "اهداف",
        "value" => $row["targets"],
        "key" => "targets",
      ),
      array(
        "title" => "استاندارد ها",
        "value" => $row["standards"],
        "key" => "standards",
      ),
    );


    array_push($needs , $res);
  }
}

$response['success'] = 'true';
$response['message'] = '200';
$response['data'] = array(
  "filters" => $filters,
  "items" => $needs 
);
$json_response = json_encode($response);
echo $json_response;
die();


function getTitleFromStatusKey($key){
  if ($key == "pending"){
      return "در انتظار تایید";
  }
  if ($key == "accepted"){
      return "تایید شده";
  }
  if ($key == "denied"){
      return "رد شده";
  }
}