<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$email = $_GET['email'];
$memberId = $_GET['memberId'];
$pass = substr(md5(sha1($_GET['password'])), 0, 19);
$titleName = $_GET['titleName'];
$fullName = $_GET['fullName'];
$idCard = $_GET['idCard'];
$telephone = $_GET['telephone'];
$accActive = $_GET['accActive'];
$token = $_GET['token'];
$credit = $_GET['credit'];

$name = explode(" ", $fullName);
$auth = "Memb";
$createDate = date("Y-m-d H:i:s");

// Update
// $register_sql = $sql->prepare("INSERT INTO ".$mssql_db_user.".dbo.trmember(email, password, titleName, firstName, lastName, idcard
// , telephone, authority, createDate, token, credit) VALUES(:p1, :p2, :p3, :p4, :p5, :p6, :p7, :p8, :p9, :p10, :p11)");
$register_sql = $sql->prepare("UPDATE ".$mssql_db_user.".dbo.trmember SET
email = :p1,
titleName = :p3,
firstName = :p4,
lastName = :p5,
idcard = :p6,
telephone = :p7,
authority = :p8,
updateDate = :p9,
token = :p10,
credit = :p11,
accActive = :p12
WHERE member_id = :p13
");

$register_sql->BindParam(":p1", $email);
$register_sql->BindParam(":p3", $titleName);
$register_sql->BindParam(":p4", $name[0]);
$register_sql->BindParam(":p5", $name[1]);
$register_sql->BindParam(":p6", $idCard);
$register_sql->BindParam(":p7", $telephone);
$register_sql->BindParam(":p8", $auth);
$register_sql->BindParam(":p9", $createDate);
$register_sql->BindParam(":p10", $token);
$register_sql->BindParam(":p11", $credit);
$register_sql->BindParam(":p12", $accActive);
$register_sql->BindParam(":p13", $memberId);
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
