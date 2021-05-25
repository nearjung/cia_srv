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
            // Get Vehicle
            $vehicle_sql = $sql->prepare("SELECT ACQ_ID, BRAND_D, MODEL, PLATE1, PLATE2, OFF_PROV_D, NUM_BODY, NUM_ENG FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
            $vehicle_sql->BindParam(":idcard", $idcard);
            $vehicle_sql->execute();
            while ($vehicle = $vehicle_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value']['vehicle'][] = $vehicle;
            }

            // Get Customer Data
            $customer_sql = $sql->prepare("SELECT MobilePhone, HomePhone, EMAIL, EducationLevelThaiDesc FROM " . $tbl["customer"] . " WHERE IDCARDNO = :idcard");
            $customer_sql->BindParam(":idcard", $idcard);
            $customer_sql->execute();
            $customer = $customer_sql->fetch(PDO::FETCH_ASSOC);
            $return['value']['customer'] = $customer;

            // Working Data
            $ssdb_sql = $sql->prepare("SELECT emp_date, company, address1, address2, address3, addressprovince, addresspostal FROM " . $tbl["SSDB"] . " WHERE idcard = :idcard");
            $ssdb_sql->BindParam(":idcard", $idcard);
            $ssdb_sql->execute();
            $ssdb = $ssdb_sql->fetch(PDO::FETCH_ASSOC);
            $return['value']['working'] = $ssdb;
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
