<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$email = $_GET['email'];
$idcard = $_GET['idcard'];


$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
$back_sql = $sql->prepare("SELECT member_id, email, password,
authority, credit, emailActive, titleName, firstName, lastname, idcard,
telephone, createDate, updateDate FROM trmember WHERE email = :email AND idcard = :idcard");
$back_sql->BindParam(":email", $email);
$back_sql->BindParam(":idcard", $idcard);
$back_sql->execute();
$back = $back_sql->fetch(PDO::FETCH_ASSOC);
if($back) {
    $return['value'] = $back;
    $return['comment'] = '';
    echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $api->sendLogUser($back['member_id'], $api->logData("ลืมรหัสผ่าน", "อีเมล : ".$email." ได้ขอรหัสผ่านใหม่ "));
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