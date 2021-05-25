<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$member = $_GET['member_id'];

// Response
$return['id'] = '';
$return['code'] = 200;
$return['status'] = "Success";
$return['text'] = "Load Success.";
// GET AUTH
$auth_sql = $sql->prepare("SELECT authority FROM ".$mssql_db_user.".dbo.trmember WHERE member_id = :memid");
$auth_sql->BindParam(":memid", $member);
$auth_sql->execute();
$auth = $auth_sql->fetch(PDO::FETCH_ASSOC);
if($auth) {
    // GET Menu
    if($auth['authority'] == "Admin") {
        $menu_sql = $sql->prepare("SELECT menuName, menuLink, menuPrice, menuId FROM trmenu");
        $menu_sql->execute();
        while($menu = $menu_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $menu;
        }
        $return['comment'] = '';
        echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    } else {
        $menu_sql = $sql->prepare("SELECT trmenu.menuName, trmenu.menuLink, trmenu.menuPrice, trmenu.menuId FROM trmenu INNER JOIN trmenuactive ON trmenu.menuId = trmenuactive.menuId AND trmenuactive.member_id = :memid AND trmenu.menuRole != 'Admin' ORDER BY menuId");
        $menu_sql->BindParam(":memid", $member);
        $menu_sql->execute();
        while($menu = $menu_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $menu;
        }
        $return['comment'] = '';
        echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }
} else {
    $return = array(
        'id' => '',
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Database Error."
    );
    echo json_encode($return);
    exit();
}
?>