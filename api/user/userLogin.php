<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$email = $_GET['email'];
$password = substr(md5(sha1($_GET['password'])), 0, 19);

$active = 'Y';
$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
$back_sql = $sql->prepare("SELECT member_id, email, password,
authority, credit, emailActive, titleName, firstName, lastname, idcard, accActive,
telephone, createDate, updateDate, token, tokenexpire FROM trmember WHERE email = :email AND password = :pass AND active = :active");
$back_sql->BindParam(":email", $email);
$back_sql->BindParam(":pass", $password);
$back_sql->BindParam(":active", $active);
$back_sql->execute();
$back = $back_sql->fetch(PDO::FETCH_ASSOC);
if($back) {
    $return['value'] = $back;
    $return['comment'] = '';
    header("Authorization: Basic ".base64_encode($back['email'].":".$back['token']));
    echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $api->sendLogUser($back['member_id'], $api->logData("เข้าสู่ระบบ", "คุณได้เข้าสู่ระบบด้วยไอพี : ".$api->get_client_ip()));
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