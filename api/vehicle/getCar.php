<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$memberId = $api->getMember($_GET['memberId'], $_GET['password'], "MEMBER_ID");
$credit = $api->getMember($_GET['memberId'], $_GET['password'], "CREDIT");
$price = $api->getMenu(4, "menuPrice");
$plate1 = $_GET['plate1'];
$plate2 = $_GET['plate2'];
$numBody = $_GET['numBody'];
$numEng = $_GET['numEng'];
$mode = $_GET['mode'];
// Query back
$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
$back_sql = $sql->prepare("SELECT PLATE1, PLATE2, OFF_PROV_D, TYPE_D, OFF_LOC_DESC
, REG_DATE, BRAND_D, MODEL, NUM_BODY, NUM_ENG, FUEL_D
, CC, WGT_CAR, COLOR1_D, MFG_YEAR, ACQ_TTL_D, ACQ_FNAME
, ACQ_LNAME, ACQ_ADDR, ACQ_TUM_D, ACQ_AMP_D, ACQ_PRV_D
, ACQ_ZIP, ACQ_BIRTH, ACQ_ID, ACQ_NAT_D, Payment
, JUT_TTL_D, JUT_FNAME, JUT_LNAME 
FROM " . $tbl["carall"] . "
WHERE PLATE1 = :plate1 AND PLATE2 = :plate2 AND NUM_BODY = :numbody AND NUM_ENG = :numeng");
$back_sql->BindParam(":plate1", $plate1);
$back_sql->BindParam(":plate2", $plate2);
$back_sql->BindParam(":numbody", $numBody);
$back_sql->BindParam(":numeng", $numEng);
$back_sql->execute();
$back = $back_sql->fetch(PDO::FETCH_ASSOC);
$return['value'] = $back;

$return['comment'] = '';
$api->sendLogUser($memberId, $api->logData('ดูข้อมูลรถ', 'ดูข้อมูลรถป้ายทะเบียน : ' . $plate1 . '' . $plate2 . ' เลขตัวถัง : ' . $numEng . ''));
if (!$mode == "Personal") {
    $api->sendLogUser($memberId, $api->logData('เครดิต', 'ถูกหักเครดิตจำนวน : ' . $price . ''));
    if ($api->creditManage("reduce", $price, $memberId) == true) {
        echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }
} else {
    echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}
