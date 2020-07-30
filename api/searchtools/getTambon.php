<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");


$amphure = $_GET['amphure'];

// Response
$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
$back_sql = $sql->prepare("SELECT TambonThaiShort FROM trthailandinformation WHERE DistrictThaiShort = :amphure GROUP BY TambonThaiShort ORDER BY TambonThaiShort");
$back_sql->BindParam(":amphure", $amphure);
$back_sql->execute();
while($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
    $return['value'][] = $back;
}
$return['comment'] = '';
echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>