<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$memberId = $api->getMember($_GET['memberId'], $_GET['password'], "MEMBER_ID");
$credit = $api->getMember($_GET['memberId'], $_GET['password'], "CREDIT");
$idcard = $_GET['idCard'];
$price = $api->getMenu(2, "menuPrice");
if ($memberId == false) {
    $return = array(
        'id' => $memberId,
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Invalid user."
    );
    echo json_encode($return);
    exit();
} else if ($credit < $price) {
    $return = array(
        'id' => $memberId,
        'code' => 500,
        'status' => "Credit",
        'text' => "Error: Enough credit."
    );
    echo json_encode($return);
    exit();
} else {
    if ($idcard) {
        // Search
        $back_sql = $sql->prepare("EXEC " . $mssql_db_user . ".dbo.getCensusInfo :idcard");
        $back_sql->BindParam(":idcard", $idcard);
        $back_sql->execute();
        $back = $back_sql->fetch(PDO::FETCH_ASSOC);
        if ($back) {
            $return['id'] = $memberId;
            $return['code'] = 200;
            $return['status'] = "Success";
            $return['text'] = "Load Success.";
            $return['value'] = $back;
            $api->sendLogUser($memberId, $api->logData('ดูข้อมูลบุคคล', 'ดูข้อมูลบุคคล : ' . $idcard . ''));
            echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            $return = array(
                'id' => $memberId,
                'code' => 500,
                'status' => "Error",
                'text' => "Error: Data return."
            );
            echo json_encode($return);
            exit();
        }
    } else {
        $return = array(
            'id' => $memberId,
            'code' => 500,
            'status' => "Error",
            'text' => "Error: Data"
        );
        echo json_encode($return);
        exit();
    }
}
