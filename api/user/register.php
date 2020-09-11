<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

$email = $_GET['email'];
$pass = md5(sha1($_GET['password']));
$titleName = $_GET['titleName'];
$fullName = $_GET['fullName'];
$idCard = $_GET['idCard'];
$telephone = $_GET['telephone'];
$token = $_GET['token'];

$name = explode(" ", $fullName);
$auth = "Memb";
$createDate = date("Y-m-d H:i:s");
// Check 
$chk_sql = $sql->prepare("SELECT email FROM ".$mssql_db_user.".dbo.trmember WHERE email = :email");
$chk_sql->BindParam(":email", $email);
$chk_sql->execute();
$chk = $chk_sql->fetch(PDO::FETCH_ASSOC);
if($chk) {
    $return = array(
        'id' => '',
        'code' => 500,
        'status' => "Error",
        'text' => "Email is already used."
    );
    echo json_encode($return);
    exit();
} else {
        // Register
    $register_sql = $sql->prepare("INSERT INTO ".$mssql_db_user.".dbo.trmember(email, password, titleName, firstName, lastName, idcard
    , telephone, authority, createDate, token) VALUES(:p1, :p2, :p3, :p4, :p5, :p6, :p7, :p8, :p9, :p10)");
    $register_sql->BindParam(":p1", $email);
    $register_sql->BindParam(":p2", $pass);
    $register_sql->BindParam(":p3", $titleName);
    $register_sql->BindParam(":p4", $name[0]);
    $register_sql->BindParam(":p5", $name[1]);
    $register_sql->BindParam(":p6", $idCard);
    $register_sql->BindParam(":p7", $telephone);
    $register_sql->BindParam(":p8", $auth);
    $register_sql->BindParam(":p9", $createDate);
    $register_sql->BindParam(":p10", $token);
    $register_sql->execute();
    if($register_sql) {
        $return = array(
            'id' => '',
            'code' => 200,
            'status' => "Success",
            'text' => "Save: Success"
        );
        echo json_encode($return);
        exit();
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
}

?>