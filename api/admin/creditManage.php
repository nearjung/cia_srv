<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$mode = $_GET['mode'];
$email = $_GET['email'];
$amount = $_GET['amount'];

if($mode == "add") {
    $back_sql = $sql->prepare("UPDATE ".$mssql_db_user.".dbo.trmember SET credit = credit + :credit WHERE email = :mem");
} else if($mode == "reduce") {
    $back_sql = $sql->prepare("UPDATE ".$mssql_db_user.".dbo.trmember SET credit = credit - :credit WHERE email = :mem");
}
$back_sql->BindParam(":credit", $amount);
$back_sql->BindParam(":mem", $email);
$back_sql->execute();
if($back_sql) {
    $return = array(
        'id' => '',
        'code' => 200,
        'status' => "Success",
        'text' => "Load. Success",
        'comment' => ''
    );
    echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
} else {
    $return = array(
        'id' => '',
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Database Error.",
        'comment' => ''
    );
    echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}


?>