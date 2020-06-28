<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$membId = $_GET['membId'];
$menuId = $_GET['menuId'];

$createDate = date("Y-m-d H:i:s");

$back_sql = $sql->prepare("DELETE FROM ".$mssql_db_user.".dbo.trmenuactive WHERE menuId = :menu AND member_id = :membId");
$back_sql->BindParam(":menu", $menuId);
$back_sql->BindParam(":membId", $membId);
$back_sql->execute();
if($back_sql) {
    $return = array(
        'id' => '',
        'code' => 200,
        'status' => "Success",
        'text' => "Load. Success"
    );
    echo json_encode($return);
    exit();
} else {
    $return = array(
        'id' => '',
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Database Error."
    );
    echo json_encode($return);
    exit();
}

echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit();
?>