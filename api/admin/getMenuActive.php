<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$membId = $_GET['membId'];

$role = "Memb";
// Response
$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
$back_sql = $sql->prepare("SELECT trmenu.menuName, trmenu.menuId, trmenuactive.member_id  FROM trmenu INNER JOIN trmenuactive ON trmenu.menuId = trmenuactive.menuId AND trmenuactive.member_id = :mem AND trmenu.menuRole = :rol");
$back_sql->BindParam(":mem", $membId);
$back_sql->BindParam(":rol", $role);
$back_sql->execute();
while($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
    $return['value'][] = $back;
}
$return['comment'] = '';

echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit();
?>