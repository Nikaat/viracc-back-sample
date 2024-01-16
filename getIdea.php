<?php

header("Content-type: application/json; charset=utf-8");

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
$ideaId = convertPersianToEnglish($_POST["ideaId"]);
$persona = "";

$filters = array(
  array(
    "label" => "تاریخ",
    "value" => "date"
  ),
  array(
    "label" => "امتیاز",
    "value" => "rate"
  ),
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

$result = $conn->query("SELECT * FROM users WHERE `userId`='$userId'");
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $persona = $row["persona"];
  }
}

$ideas = array();

$query = "SELECT * FROM idea WHERE `userId`='$userId' ORDER BY `rate` DESC";
if ($persona == "admin"){
  $query = "SELECT * FROM idea ORDER BY `rate` DESC";
  if (isset($_POST["ideaId"])) {
    $query = "SELECT * FROM idea WHERE `id`='$ideaId'";
  }
}
else {
  if (isset($_POST["ideaId"])) {
    $query = "SELECT * FROM idea WHERE `userId`='$userId' AND `id`='$ideaId'";
  }
}

$result = $conn->query($query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {

    $boom = null;

    $rate = $row["rate"];
    $rate = round($rate / 20);

    $ideaId = $row["id"];
    $res["ideaId"] = $row["id"];
    $res["rate"] = $rate;
    $res["status"] = array(
      "key" => $row["status"],
      "title" => getTitleFromStatusKey($row["status"])
  );
    $res["items"] = array(
      array(
        "title" => "شماره تماس",
        "value" => $row["phoneNumber"],
        "key" => "phoneNumber",
      ),
      array(
        "title" => "عنوان طرح",
        "value" => $row["title"],
        "key" => "title",
      ),
      array(
        "title" => "نام تیم",
        "value" => $row["teamName"],
        "key" => "teamName",
      ),
      array(
        "title" => "آدرس وبسایت",
        "value" => $row["website"],
        "key" => "website",
      ),
      array(
        "title" => "تعداد اعضای تیم",
        "value" => $row["teamMembersQuantity"],
        "key" => "teamMembersQuantity",
      ),
      array(
        "title" => "مسئله محوری استارتاپ را توضیح دهید",
        "value" => $row["mainStartupDescription"],
        "key" => "mainStartupDescription",
      ),
      array(
        "title" => "راه حل شما برای این مسئله چیست؟",
        "value" => $row["solution"],
        "key" => "solution",
      ),
      array(
        "title" => "ارزش حدودی خدمات شما برای هر مشتری (CLV) چقدر است؟",
        "value" => $row["clv"],
        "key" => "clv",
      ),
      array(
        "title" => "اندازه بازار محصول شما چقدر است؟",
        "value" => $row["marketSize"],
        "key" => "marketSize",
      ),
      array(
        "title" => "هزینه جذب مشتری های شما (CPO) در حال حاضر چقدر است؟",
        "value" => $row["cpo"],
        "key" => "cpo",
      ),
      array(
        "title" => "تعداد مشتریان شما چقدر است؟",
        "value" => $row["customersNumber"],
        "key" => "customersNumber",
      ),
      array(
        "title" => "بیزینس مدل محصول شما چیست؟",
        "value" => $row["businessModel"],
        "key" => "businessModel",
      ),
      array(
        "title" =>  "استراتژی بازاریابی شما چیست؟",
        "value" => $row["marketingStrategy"],
        "key" => "marketingStrategy",
      ),
      array(
        "title" => "کانال های بازاریابی شما چیست؟",
        "value" => $row["marketingChannels"],
        "key" => "marketingChannels",
      ),
      array(
        "title" => "سایت شما، ماهانه چه تعداد بازدید دارد؟",
        "value" => $row["monthlySiteVisit"],
        "key" => "monthlySiteVisit",
      ),
      array(
        "title" => "تعداد فالورهای شما در شبکه های اجتماعی چقدر است؟",
        "value" => $row["socialMediaFollowers"],
        "key" => "socialMediaFollowers",
      ),
      array(
        "title" => "تعداد نصب اپلیکیشن شما چقدر است؟",
        "value" => $row["appInstallationNumber"],
        "key" => "appInstallationNumber",
      ),
      array(
        "title" => "تخصص هریک از اعضای تیم را توضیح دهید",
        "value" => $row["teamMembersExperience"],
        "key" => "teamMembersExperience",
      ),
      array(
        "title" => "کانورژن خرید مشتری ( نسبت خرید به بازدید) چقدر است؟",
        "value" => $row["conversion"],
        "key" => "conversion",
      ),
      array(
        "title" => "رقبا و مزیت رقابتی کسب و کار شما چیست؟",
        "value" => $row["competitors"],
        "key" => "competitors",
      ),
   
      array(
        "title" => "نحوه توزیع سهام بین هریک از سهامداران",
        "value" => $row["distributeShares"],
        "key" => "distributeShares",
      ),
      array(
        "title" => "نمودار درامد و هزینه سال جاری",
        "value" => $row["incomeAndExpenseChart"],
        "key" => "incomeAndExpenseChart",
      ),
      array(
        "title" => "میزان سرمایه مورد نیاز و پیشنهاد مشخص",
        "value" => $row["requiredCapital"],
        "key" => "requiredCapital",
      ),
      array(
        "title" => "نحوه استفاده از سرمایه در توسعه کسب و کار",
        "value" => $row["capitalUsage"],
        "key" => "capitalUsage",
      ),
      array(
        "title" => "سهام باقی مانده در مالکیت بنیان گذاران",
        "value" => $row["foundersRemainingShare"],
        "key" => "foundersRemainingShare",
      ),
      array(
        "title" => "پیش بینی از نقطه سر به سر هزینه درآمد",
        "value" => $row["revenueCostForecasting"],
        "key" => "revenueCostForecasting",
      ),
      array(
        "title" => "آیا مجوزی برای فعالیت دارید؟",
        "value" => $row["hasLicense"],
        "key" => "hasLicense",
      ),
      array(
        "title" => "فایل درآمد و هزینه",
        "value" => "https://panel.viracc.ir/api/user/excels/".$row["excel"],
        "key" => "incomeAndExpenseChartExcelFile",
      ),
    );
    $resultBoom = $conn->query("SELECT * FROM boom WHERE `ideaId`='$ideaId'");
    if ($resultBoom->num_rows > 0) {
      $resBoom = array();
      while ($rowBoom = $resultBoom->fetch_assoc()) {
        $resBoom["_id"] = $rowBoom["ideaId"];
        $resBoom["customerSections"] = $rowBoom["customerSections"];
        $resBoom["valueProposition"] = $rowBoom["valueProposition"];
        $resBoom["channels"] = $rowBoom["channels"];
        $resBoom["customerRelations"] = $rowBoom["customerRelations"];
        $resBoom["incomeStreams"] = $rowBoom["incomeStreams"];
        $resBoom["mainPartners"] = $rowBoom["mainPartners"];
        $resBoom["costStructures"] = $rowBoom["costStructures"];
        $resBoom["mainActivities"] = $rowBoom["mainActivities"];
      }
      $res["boom"] = $resBoom;
    }
    array_push($ideas, $res);
  }
}

$response['success'] = 'true';
$response['message'] = '200';
$response['data'] = array(
  "filters" => $filters,
  "items" => $ideas
);
$json_response = json_encode($response);
echo $json_response;
die();


function getTitleFromStatusKey($key){
  if ($key == "pending"){
      return "در حال بررسی";
  }
  if ($key == "accepted"){
      return "تایید شده";
  }
  if ($key == "denied"){
      return "رد شده";
  }
}