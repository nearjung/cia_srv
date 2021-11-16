<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$memberId = $_GET['memberId'];
$createDate = date("Y-m-d H:i:s");

$active = 'N';
// Update
// $register_sql = $sql->prepare("INSERT INTO ".$mssql_db_user.".dbo.trmember(email, password, titleName, firstName, lastName, idcard
// , telephone, authority, createDate, token, credit) VALUES(:p1, :p2, :p3, :p4, :p5, :p6, :p7, :p8, :p9, :p10, :p11)");
$register_sql = $sql->prepare("UPDATE ".$mssql_db_user.".dbo.trmember SET
active = :p1,
updateDate = :p2
WHERE member_id = :p3");

$register_sql->BindParam(":p1", $active);
$register_sql->BindParam(":p2", $updateDate);
$register_sql->BindParam(":p3", $memberId);
$register_sql->execute();
if($register_sql) {
    $return = array(
        'id' => '',
        'code' => 200,
        'status' => "Success",
        'text' => "Save: Success"
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
