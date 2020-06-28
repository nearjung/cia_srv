<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$membId = $_GET['memberid'];
$type = $_GET['type'];
$field1 = $_GET['searchTxt'];
$field2 = $_GET['searchTxt2'];
// Query back
$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
$back_sql = $sql->prepare("EXEC ".$mssql_db_info.".dbo.searchVehicle :type, :search1, :search2");
$back_sql->BindParam(":type", $type);
$back_sql->BindParam(":search1", $field1);
$back_sql->BindParam(":search2", $field2);
$back_sql->execute();
while($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
    $return['value'][] = $back;
}
$return['comment'] = '';
$api->sendLogUser($membId, $api->logData('ค้นหาพาหนะ', 'คำค้นหา : '.$field1.', '.$field2.''));
echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit();
?>