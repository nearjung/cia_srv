<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$memberId = $api->getMember($_GET['memberId'], $_GET['password'], "MEMBER_ID");
$credit = $api->getMember($_GET['memberId'], $_GET['password'], "CREDIT");
$comp_id = $_GET['compId'];
$price = $api->getMenu(3, "menuPrice");

if ($memberId == false) {
    $return = array(
        'id' => $memberId,
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Invalid user."
    );
    echo json_encode($return);
} else if ($credit < $price) {
    $return = array(
        'id' => $memberId,
        'code' => 500,
        'status' => "Credit",
        'text' => "Error: Enough credit."
    );
    echo json_encode($return);
} else {
    $return['id'] = '';
    $return['code'] = 200;
    $return['status'] = "Success";
    $return['text'] = "Load Success.";
    // Query Back
    $back_sql = $sql->prepare("SELECT * FROM " . $tbl['company'] . " WHERE companyCode = :compId");
    $back_sql->BindParam(":compId", $comp_id);
    $back_sql->execute();
    $back = $back_sql->fetch(PDO::FETCH_ASSOC);
    $return['value'] = $back;
    $return['comment'] = '';

    $api->sendLogUser($memberId, $api->logData('ดูข้อมูลบริษัท', 'ดูข้อมูลบริษัท : ' . $comp_id . ''));
    $api->sendLogUser($memberId, $api->logData('เครดิต', 'ถูกหักเครดิตจำนวน : ' . $price . ''));
    if($api->creditManage("reduce", $price, $memberId) == true) {
        echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    exit();
}
