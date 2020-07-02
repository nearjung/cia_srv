<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$idcard = $_GET['idcard'];
$certification = $_GET['certification'];
// Query back

$cer_sql = $sql->prepare("SELECT cert_key FROM trcertification WHERE cert_key = :cer");
$cer_sql->BindParam(":cer", $certification);
$cer_sql->execute();
$cer = $cer_sql->fetch(PDO::FETCH_ASSOC);
if ($cer) {
    if ($api->updateCount($certification) === false) {
        $return = array(
            'id' => $certification,
            'code' => 500,
            'status' => "Error",
            'text' => "ไม่สามารถใช้งานบริการได้เนื่องจากเกินจำนวนสิทธิ์"
        );
        echo json_encode($return);
        $api->sendLog($certification, json_encode($log, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'ERR');
        exit();
    }

    $return['id'] = $certification;
    $return['code'] = 200;
    $return['status'] = "Success";
    $return['text'] = "Load Success.";
    $customer_sql = $sql->prepare("SELECT FIRSTTHAINAME as FirstName, LASTTHAINAME as LastName, EducationLevelThaiDesc as EducationLevel FROM " . $tbl["customer"] . "
    WHERE IDCARDNO = :idcard OR PASSPORTNO = :passport");
    $customer_sql->BindParam(":idcard", $idcard);
    $customer_sql->BindParam(":passport", $idcard);
    $customer_sql->execute();
    $customer = $customer_sql->fetch(PDO::FETCH_ASSOC);
    $return['value'] = $customer;
    // Car
    $car_sql = $sql->prepare("SELECT  Mobile1 as MobileNo, TYPE_D as Type, BRAND_D as BrandCar, MODEL as ModelCar, REG_DATE as CarYear, Payment as PurchaseType FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
    $car_sql->BindParam(":idcard", $idcard);
    $car_sql->execute();
    while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
        if ($car['Type'] == "รถจักรยานยนต์") {
            $return['value']['Motorcycle'][] = $car;
        } else {
            $return['value']['car'][] = $car;
        }
    }


    // Employee
    $emp_sql = $sql->prepare("SELECT emp_date as EmpDate, company as Company FROM " . $tbl["SSDB"] . " WHERE idcard = :idcard");
    $emp_sql->BindParam(":idcard", $idcard);
    $emp_sql->execute();
    while ($emp = $emp_sql->fetch(PDO::FETCH_ASSOC)) {
        $return['value']['Work'][] = $emp;
    }
    $return['comment'] = "";

    $log = array(
        'idcard' => $idcard,
        'createDate' => $datetimes
    );

    if (!$customer && !$car && !$bike && !$emp) {
        $error_back = array(
            'id' => '',
            'code' => 500,
            'status' => "Error",
            'text' => "Error: No data found."
        );
        echo json_encode($error_back);
        $api->sendLog($certification, json_encode($log, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'ERR');
        exit();
    } else {
        echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $api->sendLog($certification, json_encode($log, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
} else {
    $return = array(
        'id' => '',
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Invalid certification."
    );
    echo json_encode($return);
    $api->sendLog($certification, json_encode($log, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'ERR');
    exit();
}
