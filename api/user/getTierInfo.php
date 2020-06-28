<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$amount = $_GET['price'];

// Response
$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
$back_sql = $sql->prepare("SELECT price FROM ".$mssql_db_user.".dbo.trpricetier WHERE amount_info = :amount");
$back_sql->BindParam(":amount", $amount);
$back_sql->execute();
$back = $back_sql->fetch(PDO::FETCH_ASSOC);
$return['value'] = $back['price'];
$return['comment'] = '';
echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>