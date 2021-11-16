<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$membId = $_GET['membId'];

$back_sql = $sql->prepare("SELECT  member_id, email, password,
authority, credit, emailActive, titleName, firstName, lastname, idcard, accActive,
telephone, createDate, updateDate  FROM ".$mssql_db_user.".dbo.trmember WHERE member_id = :mem");
$back_sql->BindParam(":mem", $membId);
$back_sql->execute();
$back = $back_sql->fetch(PDO::FETCH_ASSOC);
if($back) {
    $return = array(
        'id' => '',
        'code' => 200,
        'status' => "Success",
        'text' => "Load. Success",
        'value' => $back,
        'comment' => ''
    );
    echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
} else {
    $return = array(
        'id' => '',
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Database Error.",
        'comment' => ''
    );
    echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

?>