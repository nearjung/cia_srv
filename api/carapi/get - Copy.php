<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$plate1 = $_GET['plate1'];
$plate2 = $_GET['plate2'];
$province = $_GET['province'];
$certification = $_GET['certification'];
$return = "";
$log = "";
// Query back

$cer_sql = $sql->prepare("SELECT cert_key FROM trcertification WHERE cert_key = :cer");
$cer_sql->BindParam(":cer", $certification);
$cer_sql->execute();
$cer = $cer_sql->fetch(PDO::FETCH_ASSOC);
if($cer) {
    if($api->updateCount($certification) === false) {
        $return = array(
            'id' => $certification,
            'code' => 500,
            'status' => "Error",
            'text' => "ไม่สามารถใช้งานบริการได้เนื่องจากเกินจำนวนสิทธิ์"
        );
        echo json_encode($return);
        exit();
    }
    $type1 = 12;
    $type2 = 17;
    
    $back_sql = $sql->prepare("SELECT [TYPE_D], [REG_DATE], [EXP_DATE], [PLATE1], [PLATE2], 
    [OFF_PROV_D], [BRAND_D], [MODEL], [MKModel], [ACQ_TTL_D], [ACQ_FNAME], [ACQ_LNAME], 
    [ACQ_ADDR], [ACQ_TUM_D], [ACQ_AMP_D], [OFF_PROV_D], [ACQ_ZIP], [OCC_DATE], [RANK_OWNER], 
    [JUT_TTL_D], [JUT_FNAME], [JUT_LNAME], [JUT_ADDR], [JUT_TUM_D], [JUT_AMP_D], [JUT_PRV_D], 
    [JUT_ZIP], [NUM_BODY], [NUM_ENG], [JUT_BIRTH], [JUT_ID], [ACQ_BIRTH], [ACQ_ID], [ACQ_NAT_D], 
    [COLOR1_D], [CC], [FUEL], [WGT_CAR], [MFG_YEAR], [STAT_CODE], [Ownertype], [Payment] 
    FROM ".$tbl['carall']." WHERE PLATE1 = :plate1 AND PLATE2 = :plate2 AND OFF_PROV_D = :province AND TYPE = :type1 OR TYPE = :type2");
    $back_sql->BindParam(":plate1", $plate1);
    $back_sql->BindParam(":plate2", $plate2);
    $back_sql->BindParam(":province",$province);
    $back_sql->BindParam(":type1", $type1);
    $back_sql->BindParam(":type2", $type2);
    $back_sql->execute();
    $back = $back_sql->fetch(PDO::FETCH_ASSOC);
    if($back) {
        $return = array(
            'id' => $certification,
            'code' => 200,
            'status' => "Success",
            'value' => $back,
            'text' => "Load Success.",
            'comment' => ""
        );
        echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        $log = array(
            'PLATE1' => $plate1,
            'PLATE2' => $plate2,
            'PROVINCE' => $province
        );
        $api->sendLog($certification, json_encode($log, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        exit();
    } else {
        $return = array(
            'id' => '',
            'code' => 500,
            'status' => "Error",
            'text' => "Error: Invalid data."
        );
        echo json_encode($return);
        exit();
    }
} else {
    $return = array(
        'id' => '',
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Invalid certification."
    );
    echo json_encode($return);
    exit();
}


?>