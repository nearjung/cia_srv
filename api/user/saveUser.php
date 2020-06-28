<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$memberId = $_GET['memberId'];
$idcard = $_GET['idcard'];
$titleName = $_GET['titleName'];
$fullName = $_GET['fullName'];
$telephone = $_GET['telephone'];

$name = explode(" ", $fullName);
$updateDate = date("Y-m-d H:i:s");
// Update User
$update_sql = $sql->prepare("UPDATE ".$mssql_db_user.".dbo.trmember SET titleName = :title, firstName = :fname, lastName = :lname
, idcard = :idcard, telephone = :tel, updateDate = :update WHERE member_id = :memid");
$update_sql->BindParam(":title", $titleName);
$update_sql->BindParam(":fname", $name[0]);
$update_sql->BindParam(":lname", $name[1]);
$update_sql->BindParam(":idcard", $idcard);
$update_sql->BindParam(":tel", $telephone);
$update_sql->BindParam(":update", $updateDate);
$update_sql->BindParam(":memid", $memberId);
$update_sql->execute();
if($update_sql) {
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
?>