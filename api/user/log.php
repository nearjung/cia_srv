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
    $dataMaster = $json->dataMaster;
    $dataSub = $json->dataSub;

    $active = 'Y';
    // Add Master
    $query = $sql->prepare("INSERT INTO TRLOGMASTER(memberId, keySearch, module, active) VALUES(:memberId, :keySearch, :module, :active)");
    $query->BindParam(":memberId", $dataMaster->memberId);
    $query->BindParam(":keySearch", $dataMaster->keySearch);
    $query->BindParam(":module", $dataMaster->module);
    $query->BindParam(":active", $active);
    $query->execute();

    // Get Id
    $get_sql = $sql->prepare("SELECT TOP 1 logMasterId FROM TRLOGMASTER WHERE memberId = :memberId ORDER BY logMasterId DESC");
    $get_sql->BindParam(":memberId", $dataMaster->memberId);
    $get_sql->execute();
    $get = $get_sql->fetch(PDO::FETCH_ASSOC);

    // Add Sub
    // $sqlQuery = array();
    // foreach ($dataSub as $row) {
    //     $sqlQuery[] = '("' . $get['memberId'] . '", ' . json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ')';
    // }
    // // mysql_query('INSERT INTO table (text, category) VALUES '.implode(',', $sql));
    // $bindValue = implode(',', $sqlQuery);
    for ($i = 0; $i < count($dataSub); $i++) {
        $sub = $dataSub[$i];
        $insert = $sql->prepare('INSERT INTO TRLOG(logMasterId, data, active) VALUES (:p1, :p2, :p3)');
        $insert->BindParam(":p1", $get['logMasterId']);
        $insert->BindParam(":p2", json_encode($sub, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $insert->BindParam(":p3", $active);
        $insert->execute();
    }


    // INSERT INTO table (text, category) VALUES '.implode(',', $sql)
}
