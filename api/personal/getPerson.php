<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");
$postdata = file_get_contents("php://input");


if (isset($postdata) && !empty($postdata)) {
    $post = json_decode($postdata);
    $memberId = $api->getMember($post->memberId, $post->password, "MEMBER_ID");
    $credit = $api->getMember($post->memberId, $post->password, "CREDIT");
    $obj = $post->obj;
    $price = $api->getMenu(2, "menuPrice");
    if ($memberId == false) {
        $return = array(
            'id' => $memberId,
            'code' => 500,
            'status' => "Error",
            'text' => "Error: Invalid user."
        );
        echo json_encode($return);
        exit();
    } else if ($credit < $price) {
        $return = array(
            'id' => $memberId,
            'code' => 500,
            'status' => "Credit",
            'text' => "Error: Enough credit."
        );
        echo json_encode($return);
        exit();
    } else {
        if ($obj) {
            // Search
            $result = array();
            if (sizeof($obj) > 0) {
                for ($i = 0; $i < sizeof($obj); $i++) {
                    $back_sql = $sql->prepare("EXEC " . $mssql_db_user . ".dbo.getCensusInfo :idcard");
                    $back_sql->BindParam(":idcard", $obj[$i]->IDCard);
                    $back_sql->execute();
                    $back = $back_sql->fetch(PDO::FETCH_ASSOC);
                    if ($back) {
                        array_push($result, $back);
                    } else {
                        $return = array(
                            'id' => $memberId,
                            'code' => 500,
                            'status' => "Error",
                            'text' => "Error: Data return."
                        );
                        echo json_encode($return);
                        exit();
                    }
                }
            }

            $return['id'] = $memberId;
            $return['code'] = 200;
            $return['status'] = "Success";
            $return['text'] = "Load Success.";
            $return['value'] = $result;
            echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            $return = array(
                'id' => $memberId,
                'code' => 500,
                'status' => "Error",
                'text' => "Error: Data"
            );
            echo json_encode($return);
            exit();
        }
    }
}
