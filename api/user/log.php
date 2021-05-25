<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$postdata = file_get_contents("php://input");

// Response
$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $json = json_decode($postdata);

    $active = 'Y';
    $query = $sql->prepare("INSERT INTO TRLOGDATA(memberId, jsonDataMain, module, active) VALUES(:p1, :p2, :p3, :p4)");
    $query->BindParam(":p1", $json->memberId);
    $query->BindParam(":p2", json_encode($json->data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    $query->BindParam(":p3", $json->module);
    $query->BindParam(":p4", $active);
    $query->execute();
}
