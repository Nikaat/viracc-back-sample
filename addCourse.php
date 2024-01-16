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
$categoryId = convertPersianToEnglish($_POST["categoryId"]);
$title = convertPersianToEnglish($_POST["title"]);
$description = convertPersianToEnglish($_POST["description"]);
$about = convertPersianToEnglish($_POST["about"]);
$duration = convertPersianToEnglish($_POST["courseDuration"]);
$preview = convertPersianToEnglish($_POST["preview"]);
$featuredImage = convertPersianToEnglish($_POST["featuredImage"]);
$chapters = convertPersianToEnglish($_POST["chapters"]);
$chaptersArray = json_decode($chapters, true);



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



if ($conn->query("INSERT INTO courses(`courseId` , `categoryId` , `title` , `description` , `about` , `duration` , `preview` , `featuredImage` , `created_at` , `updated_at`) VALUES('$courseId' , '$categoryId' , '$title' , '$description' , '$about' , '$duration' , '$preview' , '$featuredImage' , '$currentTime' , '$currentTime')")) {

  for ($i = 0; $i < sizeof($chaptersArray); $i++) {
    $chapterTitle = $chaptersArray[$i]["chapterTitle"];
    
    $chapterDescription = $chaptersArray[$i]["chapterDescription"];
    $chapterDuration = $chaptersArray[$i]["chapterDuration"];
    $videos = $chaptersArray[$i]["videos"];
    // print_r($videos);
    // die();
    $videosArray = $videos;
    $chapterId = uniqid("ch");
    
    

    if ($conn->query("INSERT INTO chapters(`courseId` , `chapterId` , `title`) VALUES('$courseId' , '$chapterId' , '$chapterTitle')")) {

      for ($j = 0; $j < sizeof($videosArray); $j++) {
        $videoTitle = $videosArray[$j]["videoTitle"];
        $videoDescription = $videosArray[$j]["videoDescription"];
        $videoDuration = $videosArray[$j]["videoDuration"];
        $videoLink = $videosArray[$j]["videoLink"];
        $videoId = uniqid("vi");

        if ($conn->query("INSERT INTO videos( `videoId` , `chapterId` , `title` , `description` , `link`) VALUES('$videoId' , '$chapterId' , '$videoTitle' , '$videoDescription' , '$videoLink')")) {
        } else {
          $response['success'] = 'false';
          $response['message'] = 'مشکل در ذخیره سازی ویدئو. لطفا بعدا تلاش کنید.';
          $json_response = json_encode($response);
          echo $json_response;

          die();
        }
      }
    } else {
      $response['success'] = 'false';
      $response['message'] = 'مشکل در ذخیره سازی فصل. لطفا بعدا تلاش کنید.';
      $json_response = json_encode($response);
      echo $json_response;

      die();
    }
  }

  $response['success'] = 'true';
  $response['message'] = 'دوره اضافه شد';
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

