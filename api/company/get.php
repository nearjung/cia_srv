<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$membId = $_GET['membId'];
$companyName = $_GET['companyName'];
$capital1 = $_GET['capital1'];
$capital2 = $_GET['capital2'];
$objective = $_GET['objective'];
$province = $_GET['province'];
$mode = $_GET['mode'];
$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
// Query Back
$back_sql = $sql->prepare("EXEC ".$mssql_db_info.".dbo.companySearch :companyName, :capital1, :capital2, :objective, :province, :mode");
$back_sql->BindParam(":companyName", $companyName);
$back_sql->BindParam(":capital1", $capital1);
$back_sql->BindParam(":capital2", $capital2);
$back_sql->BindParam(":objective", $objective);
$back_sql->BindParam(":province", $province);
$back_sql->BindParam(":mode", $mode);
$back_sql->execute();
while($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
    $return['value'][] = $back;
}
$return['comment'] = '';

$api->sendLogUser($membId, $api->logData('ค้นหาบริษัท', 'คำค้นหา : '.$companyName.''));
echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit();

?>