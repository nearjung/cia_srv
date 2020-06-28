<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$membId = $_GET['membId'];
$menuId = $_GET['menuId'];
$by = $_GET['by'];

$createDate = date("Y-m-d H:i:s");

$back_sql = $sql->prepare("INSERT INTO ".$mssql_db_user.".dbo.trmenuactive(member_id, menuId, createDate, createBy) VALUES(:mem, :menuId, :create, :by)");
$back_sql->BindParam(":mem", $membId);
$back_sql->BindParam(":menuId", $menuId);
$back_sql->BindParam(":create", $createDate);
$back_sql->BindParam(":by", $by);
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

//echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit();
?>