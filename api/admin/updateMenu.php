<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$menuId = $_GET['menuId'];
$menuName = $_GET['menuName'];
$menuLink = $_GET['menuLink'];
$menuPrice = $_GET['menuPrice'];

$back_sql = $sql->prepare("UPDATE ".$mssql_db_user.".dbo.trmenu SET menuName = :name, menuLink = :link, menuPrice = :price WHERE menuId = :id");
$back_sql->BindParam(":name", $menuName);
$back_sql->BindParam(":link", $menuLink);
$back_sql->BindParam(":price", $menuPrice);
$back_sql->BindParam(":id", $menuId);
$back_sql->execute();
if($back_sql) {
    $return = array(
        'id' => '',
        'code' => 200,
        'status' => "Success",
        'text' => "Load. Success",
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