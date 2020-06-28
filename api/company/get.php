<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$membId = $_GET['membId'];
$searchTxt = $_GET['searchTxt'];

$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
// Query Back
$back_sql = $sql->prepare("EXEC ".$mssql_db_info.".dbo.companySearch :searchTxt");
$back_sql->BindParam(":searchTxt", $searchTxt);
$back_sql->execute();
while($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
    $return['value'][] = $back;
}
$return['comment'] = '';

$api->sendLogUser($membId, $api->logData('ค้นหาบริษัท', 'คำค้นหา : '.$searchTxt.''));
echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit();

?>