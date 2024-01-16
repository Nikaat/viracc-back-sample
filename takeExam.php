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
$examId = convertPersianToEnglish($_POST["examId"]);
$qaList = convertPersianToEnglish($_POST["qaList"]);
$qaList = json_decode($qaList, true);

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

$result = $conn->query("SELECT * FROM exam WHERE `examId`='$examId'");
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $teacherId = $row["userId"];
  }

}

$allAnswers = sizeof($qaList);
$correctAnswers = 0;
$falseAnswers = 0;
$rate = 0;


$takedExamId = uniqid("teid");

for ($i = 0; $i < sizeof($qaList); $i++) {
  $qid = $qaList[$i]["qid"];
  $aid = $qaList[$i]["aid"];
  $resultAnswers = $conn->query("SELECT * FROM answers WHERE `qid`='$qid' AND `aid`='$aid'");
  if ($resultAnswers->num_rows > 0) {
    while ($rowAnswers = $resultAnswers->fetch_assoc()) {
      $value = $rowAnswers["value"];
      if ($value == "1"){
        $correctAnswers++;
      }
      else {
        $falseAnswers++;
      }
    }
  }
  $conn->query("INSERT INTO examAnswers(`studentId` , `takedExamId` , `examId` , `qid` , `aid` , `created_at`) VALUES('$userId' , '$takedExamId' , '$examId' , '$qid' , '$aid' , '$currentTime')");
}
$rate = ($correctAnswers/$allAnswers) * 100;


$conn->query("INSERT INTO takedExam(`studentId` , `teacherId` , `examId` , `created_at` , `updated_at` , `rate` , `allAnswers` , `correctAnswers` , `falseAnswers`) VALUES('$userId' , '$teacherId' , '$examId' , '$currentTime' , '$currentTime' , '$rate' , '$allAnswers' , '$correctAnswers' , '$falseAnswers')");
$conn->query("UPDATE exam SET `status`='finished' WHERE `examId`='$examId'");


$response['success'] = 'true';
$response['message'] = 'آزمون ثبت شد';
$json_response = json_encode($response);
echo $json_response;
die();
