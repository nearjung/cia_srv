<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$memberId = $api->getMember($_GET['memberId'], $_GET['password'], "MEMBER_ID");
$credit = $api->getMember($_GET['memberId'], $_GET['password'], "CREDIT");
$amount = $_GET['price'];
if ($memberId == false) {
    $return = array(
        'id' => $memberId,
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Invalid user."
    );
    echo json_encode($return);
} else if($credit < $amount) {
    $return = array(
        'id' => $memberId,
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Not enough credit."
    );
    echo json_encode($return);
} else {
    $return = array(
        'id' => $memberId,
        'code' => 200,
        'status' => "Success",
        'text' => "Success."
    );
    echo json_encode($return);
}
?>