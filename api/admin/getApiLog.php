<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$cert = $_GET['cert'];

$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
$back_sql = $sql->prepare("SELECT * FROM ".$mssql_db_user.".dbo.trlogapi WHERE cert_code = :cert ORDER BY createDate ASC");
$back_sql->BindParam(":cert", $cert);
$back_sql->execute();
while($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
    $return['value'][] = $back;
}

echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>