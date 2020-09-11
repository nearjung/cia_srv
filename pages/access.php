<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../configuration/config.php");

$token = $_GET['token'];

if ($token) {
    // Check if update or not
    $chk_sql = $sql->prepare("SELECT emailActive FROM trmember WHERE token = :token");
    $chk_sql->BindParam(":token", $token);
    $chk_sql->execute();
    $chk = $chk_sql->fetch(PDO::FETCH_ASSOC);
    if($chk['emailActive'] == 1) {
        $return = array(
            'id' => '',
            'code' => 500,
            'status' => "Error",
            'text' => "Error: This email already active."
        );
        echo json_encode($return);
        exit();
    }

    // Update
    $update = $sql->prepare("UPDATE trmember SET emailActive = '1' WHERE token = :token");
    $update->BindParam(":token", $token);
    $update->execute();
    if ($update) {
        $return = array(
            'id' => '',
            'code' => 200,
            'status' => "Success",
            'text' => "Save: Success"
        );
        echo json_encode($return);
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
} else {
}
