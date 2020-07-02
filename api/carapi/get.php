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

$log = array(
    'PLATE1' => $plate1,
    'PLATE2' => $plate2,
    'PROVINCE' => $province,
    'createDate' => $datetimes
);
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
        $api->sendLog($certification, json_encode($log, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'ERR');
        exit();
    }
    
    $back_sql = $sql->prepare("EXEC ".$mssql_db_info.".dbo.ChubbSearch :plate1, :plate2, :province");
    $back_sql->BindParam(":plate1", $plate1);
    $back_sql->BindParam(":plate2", $plate2);
    $back_sql->BindParam(":province",$province);
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
        
        $api->sendLog($certification, json_encode($log, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        exit();
    } else {
        $return = array(
            'id' => '',
            'code' => 500,
            'status' => "Error",
            'value' => $back,
            'text' => "Error: Invalid data."
        );
        echo json_encode($return);
        $api->sendLog($certification, json_encode($log, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'ERR');
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
    $api->sendLog($certification, json_encode($log, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'ERR');
    exit();
}


?>