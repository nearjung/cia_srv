<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");


// Get Param
$brand = $_GET['brand'];
$yearmin = $_GET['yearmin'];
$yearmax = $_GET['yearmax'];
$province = $_GET['province'];
$limit = $_GET['limit'];

// User
$memberId = $api->getMember($_GET['memberId'], $_GET['password'], "member_id");
$credit = $api->getMember($_GET['memberId'], $_GET['password'], "credit");

$i = 0;

if($memberId == false) {
    $return = array(
        'id' => $memberId,
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Invalid user."
    );
    echo json_encode($return);
    $api->sendLogUser($memberId, $api->logData('ดูข้อมูล Search Tools vehicle', 'Error: Invalid user.'), 'ERR');
    return;
} else {
    $return['id'] = '';
    $return['code'] = 200;
    $return['status'] = "Success";
    $return['text'] = "Load Success.";  
    $back_sql = $sql->prepare("EXEC ".$mssql_db_info.".dbo.batchSearchVehicle :brand, :yearmin, :yearmax, :province, :limit");
    $back_sql->BindParam(":brand", $brand);
    $back_sql->BindParam(":yearmin", $yearmin);
    $back_sql->BindParam(":yearmax", $yearmax);
    $back_sql->BindParam(":province", $province);
    $back_sql->BindParam(":limit", $limit);
    $back_sql->execute();
    while($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
        $return['value'][] = $back;
        $i++;
    }
    $return['comment'] = "";
}
echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$api->sendLogUser($memberId, $api->logData('ดูข้อมูล Search Tools vehicle', 'Search tools used.'));
?>