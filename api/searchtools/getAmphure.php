<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");


$province = $_GET['province'];

// Response
$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
$back_sql = $sql->prepare("SELECT DistrictThaiShort FROM trthailandinformation WHERE ProvinceThai = :province GROUP BY DistrictThaiShort ORDER BY DistrictThaiShort");
$back_sql->BindParam(":province", $province);
$back_sql->execute();
while($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
    $return['value'][] = $back;
}
$return['comment'] = '';
echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>