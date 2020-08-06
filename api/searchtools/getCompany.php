<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$companyType = $_GET['companyType'];
$registeredCapital = $_GET['registeredCapital'];
$profit = $_GET['profit'];
$employee = $_GET['employee'];
$limit = $_GET['limit'];


$memberId = $api->getMember($_GET['memberId'], $_GET['password'], "member_id");
$credit = $api->getMember($_GET['memberId'], $_GET['password'], "credit");
$price = $api->getMenu(8, "menuPrice");

if($memberId == false) {
    $return = array(
        'id' => $memberId,
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Invalid user."
    );
    echo json_encode($return);
} else if($price > $credit) {
    $return = array(
        'id' => $memberId,
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Not enough credit."
    );
    echo json_encode($return);
} else {
    
}
?>