<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$memberId = $api->getMember($_GET['memberId'], $_GET['password'], "member_id");
$credit = $api->getMember($_GET['memberId'], $_GET['password'], "credit");
$comp_id = $_GET['compId'];
$price = $api->getMenu(3, "menuPrice");

if ($memberId == false) {
    $return = array(
        'id' => $memberId,
        'code' => 500,
        'status' => "Error",
        'text' => "Error: Invalid user."
    );
    echo json_encode($return);
} else if ($credit < $price) {
    $return = array(
        'id' => $memberId,
        'code' => 500,
        'status' => "Credit",
        'text' => "Error: Enough credit."
    );
    echo json_encode($return);
} else {
    $return['id'] = '';
    $return['code'] = 200;
    $return['status'] = "Success";
    $return['text'] = "Load Success.";
    // Query Back
    $back_sql = $sql->prepare("SELECT comp_name, comp_id, type_comp, regist_date, object_detail, status_comp, board_action_1th, capital
, comp_address, comp_address1, comp_address2, comp_address3, comp_address4, post_number, send_money, t_tel1, t_tel2, t_fax1, email
, web, employee, m_money_year, m_assets, m_debt, m_revenue, m_expenditure, m_profit, board1, board2, board3, board4, board5, board6
, board7, board8, board9, board10, board11, board12, updatetime FROM " . $tbl['company'] . " WHERE comp_id = :compId");
    $back_sql->BindParam(":compId", $comp_id);
    $back_sql->execute();
    $back = $back_sql->fetch(PDO::FETCH_ASSOC);
    $return['value'] = $back;
    $return['comment'] = '';

    $api->sendLogUser($memberId, $api->logData('ดูข้อมูลบริษัท', 'ดูข้อมูลบริษัท : ' . $comp_id . ''));
    $api->sendLogUser($memberId, $api->logData('เครดิต', 'ถูกหักเครดิตจำนวน : ' . $price . ''));
    if($api->creditManage("reduce", $price, $memberId) == true) {
        echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    exit();
}
