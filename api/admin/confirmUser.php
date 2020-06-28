<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$membId = $_GET['membId'];

$num = 1;
$back_sql = $sql->prepare("UPDATE ".$mssql_db_user.".dbo.trmember SET accActive = :num WHERE member_id = :mem");
$back_sql->BindParam(":num", $num);
$back_sql->BindParam(":mem", $membId);
$back_sql->execute();
if($back_sql) {
    $return = array(
        'id' => '',
        'code' => 200,
        'status' => "Success",
        'text' => "Load Success",
        'comment' => ''
    );
    echo json_encode($return);
    exit();
} else {
    $return = array(
        'id' => '',
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Database Error.",
        'comment' => ''
    );
    echo json_encode($return);
    exit();
}

?>