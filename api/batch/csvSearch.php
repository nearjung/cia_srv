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
    $obj = explode(",", $json->obj);
    $result = array();
    for ($i = 0; $i < sizeof($obj); $i++) {
        $querySql = $sql->prepare("EXEC " . $mssql_db_info . ".dbo.csvSearch :mode, :data");
        $querySql->BindParam(":mode", $json->mode);
        $querySql->BindParam(":data", $obj[$i]);
        $querySql->execute();
        while ($data = $querySql->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $data);
        }
    }

    $return['value'] = $result;

    echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
