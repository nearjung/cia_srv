<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$menuId = $_GET['menuId'];

$back_sql = $sql->prepare("SELECT menuName, menuLink, menuPrice, menuId FROM ".$mssql_db_user.".dbo.trmenu WHERE menuId = :id");
$back_sql->BindParam(":id", $menuId);
$back_sql->execute();
$back = $back_sql->fetch(PDO::FETCH_ASSOC);
if($back) {
    $return = array(
        'id' => '',
        'code' => 200,
        'status' => "Success",
        'text' => "Load. Success",
        'value' => $back,
        'comment' => ''
    );
    echo json_encode($return);
    exit();
} else {
    $return = array(
        'id' => '',
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Database Error.",
        'comment' => ''
    );
    echo json_encode($return);
    exit();
}


?>