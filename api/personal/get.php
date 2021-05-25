<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$membId = $_GET['membId'];
$searchtext = $_GET['searchtext'];
$email = $_GET['email'];
$password = $_GET['password'];
$mode = $_GET['mode'];
$province = $_GET['province'];
$memberId = $api->getMember($membId, $password, "MEMBER_ID");
if ($memberId == false) {
    $return = array(
        'id' => $memberId,
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Invalid user."
    );
    echo json_encode($return);
    exit();
}

// Query back
$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
if ($province) {
    $fnd_sql = $sql->prepare("EXEC " . $mssql_db_info . ".dbo.censusSearch :searchtext, :mode, :province");
} else {
    $fnd_sql = $sql->prepare("EXEC " . $mssql_db_info . ".dbo.censusSearch :searchtext, :mode");
}
$fnd_sql->BindParam(":searchtext", $searchtext);
$fnd_sql->BindParam(":mode", $mode);
if ($province) {
    $fnd_sql->BindParam(":province", $province);
}
$fnd_sql->execute();
if ($mode == "Count") {
    $fnd = $fnd_sql->fetch(PDO::FETCH_NUM);
    $return['value'] = $fnd;
} else {
    while ($result = $fnd_sql->fetch(PDO::FETCH_ASSOC)) {
        $return['value'][] = $result;
    }
}

$return['comment'] = "";

echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($result) {
    $api->sendLogUser($membId, $api->logData('ค้นหาบุคคล', 'คำค้นหา ' . $firstname . ' ' . $lastname . ' '));
} else {
    $api->sendLogUser($membId, $api->logData('ค้นหาบุคคล', 'คำค้นหา ' . $firstname . ' ' . $lastname . ' '), 'ERR');
}
exit();
