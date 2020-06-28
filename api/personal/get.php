<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$membId = $_GET['membId'];
$searchTxt = $_GET['searchTxt'];
// Query back
$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
$fnd_sql = $sql->prepare("EXEC ".$mssql_db_info.".dbo.censusSearch :searchTxt");
$fnd_sql->BindParam(":searchTxt", $searchTxt);
$fnd_sql->execute();
while($result = $fnd_sql->fetch(PDO::FETCH_ASSOC)) {
    $return['value'][] = $result;
} 
$return['comment'] = "";

$api->sendLogUser($membId, $api->logData('ค้นหาบุคคล', 'คำค้นหา '.$searchTxt.''));

echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>