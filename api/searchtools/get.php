<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

// Val
$gender = $_GET['gender'];
$age1 = $_GET['age1'];
$age2 = $_GET['age2'];
$province = $_GET['province'];
$car = $_GET['car'];
$yearcar = $_GET['yearcar'];
$motor = $_GET['motor'];
$yearmotor = $_GET['yearmotor'];
$amphure = $_GET['amphure'];
$tambon = $_GET['tambon'];
$ensure = $_GET['ensure'];
$email = $_GET['email'];
$telephone = $_GET['telephone'];
$top = $_GET['top'];
$getCount = $_GET['getcount'];
$carbrand =  $_GET['carbrand'];
$carmodel = $_GET['carmodel'];
$memberId = $api->getMember($_GET['memberId'], $_GET['password'], "member_id");
$credit = $api->getMember($_GET['memberId'], $_GET['password'], "credit");

$folder = "../../filesUpload/txtDB/";
$folderName = "../../filesUpload/txtDB/searchTools";
if ($car) {
    $folderName .= "_car";
}
if ($email) {
    $folderName .= "_email";
}
if ($telephone) {
    $folderName .= "_telephone";
}
if ($ensure) {
    $folderName .= "_ensure";
}

$filesDirectory = $folderName . "_" . $memberId . ".txt";

$i = 0;

// Response
if ($memberId == false) {
    $return = array(
        'id' => $memberId,
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Invalid user."
    );
    echo json_encode($return);
    $api->sendLogUser($memberId, $api->logData('ดูข้อมูล Search Tools Personal', 'Error: Invalid user.'), 'ERR');
    return;
} else {
    $return['id'] = '';
    $return['code'] = 200;
    $return['status'] = "Success";
    $return['text'] = "Load Success.";

    $back_sql = $sql->prepare("EXEC " . $mssql_db_info . ".dbo.searchTools :top, :getcount, :gender, :age1, :age2, :province, :amphure, :tambon, :car, :yearcar, :ensure, :email, :telephone, :carbrand, :carmodel");
    $back_sql->BindParam(":top", $top);
    $back_sql->BindParam(":getcount", $getCount);
    $back_sql->BindParam(":gender", $gender);
    $back_sql->BindParam(":age1", $age1);
    $back_sql->BindParam(":age2", $age2);
    $back_sql->BindParam(":province", $province);
    $back_sql->BindParam(":amphure", $amphure);
    $back_sql->BindParam(":tambon", $tambon);
    $back_sql->BindParam(":car", $car);
    $back_sql->BindParam(":yearcar", $yearcar);
    $back_sql->BindParam(":ensure", $ensure);
    $back_sql->BindParam(":email", $email);
    $back_sql->BindParam(":telephone", $telephone);
    $back_sql->BindParam(":carbrand", $carbrand);
    $back_sql->BindParam(":carmodel", $carmodel);
    $back_sql->execute();
    while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
        // Search if already search
        $getFiles = file_get_contents($filesDirectory);
        $pattern = preg_quote($back['IDCard'], '/');
        $pattern = "/^.*$pattern.*\$/m";
        if (!preg_match_all($pattern, $getFiles, $matches)) {
            $return['value'][] = $back;
            // Log Files
            file_put_contents($filesDirectory, $back['IDCard'] . PHP_EOL, FILE_APPEND | LOCK_EX);
        }


        $i++;
    }

    // Insert Log Files name
    $date = date("d/m/Y");
    $log_sql = $sql->prepare("INSERT INTO trlogexport(filesName, account, exportDate) VALUES(:fileName, :account, :exportDate)");
    $log_sql->BindParam(":fileName", $filesDirectory);
    $log_sql->BindParam(":account", $memberId);
    $log_sql->BindParam(":exportDate", $date);
    $log_sql->execute();

    $return['comment'] = "";
}
echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$api->sendLogUser($memberId, $api->logData('ดูข้อมูล Search Tools Personal', 'Search tools used.'));
