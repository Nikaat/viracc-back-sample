<?php

error_reporting(0);

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

include '../utils/database.php';
include '../utils/helper.php';

showErrors();

$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
mysqli_set_charset($conn, "utf8");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$userId = convertPersianToEnglish($_POST["userId"]);
$courseId = convertPersianToEnglish($_POST["courseId"]);
$title = convertPersianToEnglish($_POST["title"]);
$description = convertPersianToEnglish($_POST["description"]);
$questions = convertPersianToEnglish($_POST["questions"]);
$questionsArray = json_decode($questions, true);
$qbid = convertPersianToEnglish($_POST["qbid"]);

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

if ($conn->query("UPDATE questionsBank SET `title`='$title', `description`='$description', `courseId`='$courseId', `userId`='$userId', `created_at`='$currentTime', `updated_at`='$currentTime' WHERE `qbid`='$qbid'")) {

  for ($i = 0; $i < sizeof($questionsArray); $i++) {
    $questionTitle = $questionsArray[$i]["questionTitle"];
    $questionDescription = $questionsArray[$i]["questionDescription"];
    $answers = $questionsArray[$i]["answers"];
    $answersArray = $answers;
    $qid = $questionsArray[$i]["qid"];
    
    if ($conn->query("UPDATE questions SET `title`='$questionTitle', `description`='$questionDescription' WHERE `qid`='$qid'")) {

      for ($j = 0; $j < sizeof($answersArray); $j++) {
        $label = $answersArray[$j]["label"];
        $value = $answersArray[$j]["value"];
        $aid = $answersArray[$j]["aid"];

        if ($conn->query("UPDATE answers SET `label`='$label', `value`='$value' WHERE `aid`='$aid'")) {
        } else {
          $response['success'] = 'false';
          $response['message'] = 'مشکل در ذخیره سازی گزینه. لطفا بعدا تلاش کنید.';
          $json_response = json_encode($response);
          echo $json_response;

          die();
        }
      }
    } else {
      $response['success'] = 'false';
      $response['message'] = 'مشکل در ذخیره سازی سوال. لطفا بعدا تلاش کنید.';
      $json_response = json_encode($response);
      echo $json_response;

      die();
    }
  }

  $response['success'] = 'true';
  $response['message'] = 'بانک سوال آپدیت شد';
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

