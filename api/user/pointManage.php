<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$memberId = $api->getMember($_GET['memberId'], $_GET['password'], "MEMBER_ID");
$mode = $_GET['mode'];
$amount = $_GET['amount'];
if ($memberId == false) {
    $return = array(
        'id' => $memberId,
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Invalid user."
    );
    echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} else {
    if($api->creditManage($mode, $amount, $memberId) === false) {
        $return = array(
            'id' => $memberId,
            'code' => 500,
            'status' => "Error",
            'text' => "Error: Can't manage credit"
        );
        echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        $return = array(
            'id' => $memberId,
            'code' => 200,
            'status' => "Success",
            'text' => "Success."
        );
        echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
?>