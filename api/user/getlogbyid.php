<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$memberId = $_GET['memberId'];
$logMasterId = $_GET['logMasterId'];
$auth = $_GET['auth'];

// Response
$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
if ($auth == 'Admin') {
    $back_sql = $sql->prepare("SELECT TRLOG.* FROM TRLOG INNER JOIN TRLOGMASTER ON TRLOGMASTER.logMasterId = TRLOG.logMasterId WHERE TRLOGMASTER.logMasterId = :logMasterId");
} else {
    $back_sql = $sql->prepare("SELECT TRLOG.* FROM TRLOG INNER JOIN TRLOGMASTER ON TRLOGMASTER.logMasterId = TRLOG.logMasterId WHERE TRLOGMASTER.logMasterId = :logMasterId AND TRLOGMASTER.memberId = :memberId");
}
$back_sql->BindParam(":logMasterId", $logMasterId);
if ($auth != 'Admin') {
    $back_sql->BindParam(":memberId", $memberId);
}
$back_sql->execute();
while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
    $return['value'][] = $back;
}

echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
