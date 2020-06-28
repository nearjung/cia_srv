<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

// Query back
$acc = 0;
$auth = 'Memb';
$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
$back_sql = $sql->prepare("SELECT member_id, idcard, CONCAT(titleName, firstName) AS firstName, lastName 
FROM ".$mssql_db_user.".dbo.trmember WHERE accActive = :acc AND authority = :auth");
$back_sql->BindParam(":acc", $acc);
$back_sql->BindParam(":auth", $auth);
$back_sql->execute();
while($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
    $return['value'][] = $back;
}
$return['comment'] = '';

echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>