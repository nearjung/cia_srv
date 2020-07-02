<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$membId = $_GET['membId'];
$firstname = $_GET['firstname'];
$lastname = $_GET['lastname'];
// Query back
$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
$fnd_sql = $sql->prepare("EXEC ".$mssql_db_info.".dbo.censusSearch2 :firstname, :lastname");
$fnd_sql->BindParam(":firstname", $firstname);
$fnd_sql->BindParam(":lastname", $lastname);
$fnd_sql->execute();
while ($result = $fnd_sql->fetch(PDO::FETCH_ASSOC)) {
    $return['value'][] = $result;
}
$return['comment'] = "";

echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($result) {
    $api->sendLogUser($membId, $api->logData('ค้นหาบุคคล', 'คำค้นหา ' . $firstname . ' ' . $lastname . ' '));
} else {
    $api->sendLogUser($membId, $api->logData('ค้นหาบุคคล', 'คำค้นหา ' . $firstname . ' ' . $lastname . ' '), 'ERR');
}
exit();
?>