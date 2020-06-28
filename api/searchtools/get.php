<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include("../../configuration/config.php");

// Val
$gender = $_GET['gender'];
$age1 = $_GET['age1'];
$age2 = $_GET['age2'];
$province = $_GET['province'];
$car = $_GET['car'];
$yearcar = $_GET['yearcar'];
$motor = $_GET['motor'];
$yearmotor = $_GET['yearmotor'];
$ensure = $_GET['ensure'];
$email = $_GET['email'];
$telephone = $_GET['telephone'];
$top = $_GET['top'];
$memberId = $api->getMember($_GET['memberId'], $_GET['password'], "member_id");
$credit = $api->getMember($_GET['memberId'], $_GET['password'], "credit");

// GET Tier
$tier_sql = $sql->prepare("SELECT price FROM ".$mssql_db_user.".dbo.trpricetier WHERE amount_info = :top");
$tier_sql->BindParam(":top",$top);
$tier_sql->execute();
$tier = $tier_sql->fetch(PDO::FETCH_ASSOC);
$price = $tier['price'];

$i = 0;

// Response
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
    if ($gender && $car && $ensure && $email && $telephone) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($car && $ensure && $email && $telephone) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($gender && $ensure && $email && $telephone) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }
            $i++;
        }
    } else if ($gender && $car && $email && $telephone) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($gender && $car && $ensure && $telephone) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL = ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL = ''
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($gender && $car && $ensure && $email) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone = ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone = ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($ensure && $email && $telephone) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        }
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            $i++;
        }
    } else if ($car && $email && $telephone) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($car && $ensure && $telephone) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL = ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL = ''
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($car && $ensure && $email) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone = ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone = ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($gender && $email && $telephone) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            $i++;
        }
    } else if ($gender && $ensure && $telephone) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL = ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL = ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            $i++;
        }
    } else if ($gender && $ensure && $email) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone = ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }
            $i++;
        }
    } else if ($gender && $car && $telephone) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL = ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL = ''
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($gender && $car && $email) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone = ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone = ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($gender && $car && $ensure) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($gender && $car) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;
            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($gender && $ensure) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($gender && $email) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone = ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone = ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            $i++;
        }
    } else if ($gender && $telephone) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL = ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE " . $tbl["census"] . ".Gender = :gender
        AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL = ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            $i++;
        }
    } else if ($car && $ensure) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        WHERE YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
        WHERE YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($car && $email) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone = ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone = ''
        AND " . $tbl["customer"] . ".EMAIL != ''
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($car && $telephone) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL = ''
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
        WHERE YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND " . $tbl["customer"] . ".MobilePhone != ''
        AND " . $tbl["customer"] . ".EMAIL = ''
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($ensure && $email) {
        $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
    , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
    , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
    , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
    INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
    INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
    WHERE YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
    AND " . $tbl["census"] . ".Province = :province
    AND " . $tbl["customer"] . ".MobilePhone = ''
    AND " . $tbl["customer"] . ".EMAIL != ''
    GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
    , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
    , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");

        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            $i++;
        }
    } else if ($ensure && $telephone) {
        $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
    , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
    , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
    , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
    INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
    INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
    WHERE YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
    AND " . $tbl["census"] . ".Province = :province
    AND " . $tbl["customer"] . ".MobilePhone != ''
    AND " . $tbl["customer"] . ".EMAIL = ''
    GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
    , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
    , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }
            $i++;
        }
    } else if ($email && $telephone) {
        $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
    , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
    , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
    , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
    INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
    WHERE YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
    AND " . $tbl["census"] . ".Province = :province
    AND " . $tbl["customer"] . ".MobilePhone != ''
    AND " . $tbl["customer"] . ".EMAIL != ''
    GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
    , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
    , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }

            $i++;
        }
    } else if ($gender) {
        $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
    , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
    , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
    , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
    WHERE " . $tbl["census"] . ".Gender = :gender
    AND YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
    AND " . $tbl["census"] . ".Province = :province
    GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
    , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
    , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        $back_sql->BindParam(":gender", $gender);
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;
            $i++;
        }
    } else if ($car) {
        if (!$yearcar) {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        WHERE YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
        } else {
            $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
        , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
        , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
        , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
        INNER JOIN " . $tbl["carall"] . " ON " . $tbl["carall"] . ".ACQ_ID = " . $tbl["census"] . ".IDCard
        WHERE YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
        AND " . $tbl["census"] . ".Province = :province
        AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)
        GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
        , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
        , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province
        HAVING COUNT(" . $tbl["carall"] . ".ACQ_ID) > 0");
            $back_sql->BindParam(":years", $yearcar);
        }
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Car
            if (!$yearcar) {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            } else {
                $car_sql = $sql->prepare("SELECT CONCAT(" . $tbl["carall"] . ".PLATE1, " . $tbl["carall"] . ".PLATE2, ' ', " . $tbl["carall"] . ".OFF_PROV_D) as Plate
                , CONCAT(" . $tbl["carall"] . ".BRAND_D, ' ', " . $tbl["carall"] . ".MODEL) as CarModel
                , " . $tbl["carall"] . ".REG_DATE as Register FROM " . $tbl["carall"] . " WHERE ACQ_ID = :idcard
                AND YEAR(" . $tbl["carall"] . ".REG_DATE) > (YEAR(GETDATE()) - :years)");
                $car_sql->BindParam(":idcard", $back['IDCard']);
                $car_sql->BindParam(":years", $yearcar);
                $car_sql->execute();
                while ($car = $car_sql->fetch(PDO::FETCH_ASSOC)) {
                    $return['value'][$i]['Vehicle'][] = $car;
                }
            }
            $i++;
        }
    } else if ($ensure) {
        $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
    , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
    , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
    , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
    INNER JOIN " . $tbl["SSDB"] . " ON " . $tbl["SSDB"] . ".idcard = " . $tbl["census"] . ".IDCard
    WHERE YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
    AND " . $tbl["census"] . ".Province = :province
    GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
    , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
    , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;

            // Get Ensure
            $ensure_sql = $sql->prepare("SELECT " . $tbl["SSDB"] . ".company, " . $tbl["SSDB"] . ".emp_date as StartWork FROM " . $tbl["SSDB"] . " WHERE " . $tbl["SSDB"] . ".idcard = :idcard");
            $ensure_sql->BindParam(":idcard", $back['IDCard']);
            $ensure_sql->execute();
            while ($ensure = $ensure_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['Ensure'][] = $ensure;
            }
            $i++;
        }
    } else if ($email) {
        $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
    , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
    , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
    , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
    INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
    WHERE YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
    AND " . $tbl["census"] . ".Province = :province
    AND " . $tbl["customer"] . ".MobilePhone = ''
    AND " . $tbl["customer"] . ".EMAIL != ''
    GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
    , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
    , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;
            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }
            $i++;
        }
    } else if ($telephone) {
        $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
    , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
    , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
    , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
    INNER JOIN " . $tbl["customer"] . " ON " . $tbl["customer"] . ".IDCARDNO = " . $tbl["census"] . ".IDCard
    WHERE YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
    AND " . $tbl["census"] . ".Province = :province
    AND " . $tbl["customer"] . ".MobilePhone != ''
    AND " . $tbl["customer"] . ".EMAIL = ''
    GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
    , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
    , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;
            $info_sql = $sql->prepare("SELECT " . $tbl["customer"] . ".MobilePhone, " . $tbl["customer"] . ".EMAIL FROM " . $tbl["customer"] . " WHERE " . $tbl["customer"] . ".IDCARDNO = :idcard");
            $info_sql->BindParam(":idcard", $back['IDCard']);
            $info_sql->execute();
            while ($info = $info_sql->fetch(PDO::FETCH_ASSOC)) {
                $return['value'][$i]['CustomerData'][] = $info;
            }
            $i++;
        }
    } else {
        $back_sql = $sql->prepare("SELECT TOP " . $top . " " . $tbl["census"] . ".IDCard, CONCAT(" . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, ' ', " . $tbl["census"] . ".CLname) as Name, CONCAT(" . $tbl["census"] . ".BDay, '/', " . $tbl["census"] . ".BMonth, '/', " . $tbl["census"] . ".BYear) as BirthDay
    , YEAR(GETDATE()) - " . $tbl["census"] . ".BYear as Age
    , CONCAT('เลขที่ '," . $tbl["census"] . ".HouseNo, ' หมู่ ', " . $tbl["census"] . ".Moo, ' ตรอก', " . $tbl["census"] . ".Trok, ' ซอย', " . $tbl["census"] . ".Soi, ' ถนน', " . $tbl["census"] . ".Street, ' ตำบล', " . $tbl["census"] . ".Subdistrict
    , ' อำเภอ', " . $tbl["census"] . ".District, ' จังหวัด', " . $tbl["census"] . ".Province) as Address FROM " . $tbl["census"] . "
    WHERE YEAR(GETDATE()) - " . $tbl["census"] . ".BYear BETWEEN :age1 AND :age2
    AND " . $tbl["census"] . ".Province = :province
    GROUP BY " . $tbl["census"] . ".IDCard, " . $tbl["census"] . ".CTitle, " . $tbl["census"] . ".CFname, " . $tbl["census"] . ".CLname, " . $tbl["census"] . ".BDay, " . $tbl["census"] . ".BMonth, " . $tbl["census"] . ".BYear
    , " . $tbl["census"] . ".HouseNo, " . $tbl["census"] . ".Moo, " . $tbl["census"] . ".Trok, " . $tbl["census"] . ".Soi, " . $tbl["census"] . ".Street, " . $tbl["census"] . ".Subdistrict
    , " . $tbl["census"] . ".District, " . $tbl["census"] . ".Province");
        $back_sql->BindParam(":age1", $age1);
        $back_sql->BindParam(":age2", $age2);
        $back_sql->BindParam(":province", $province);
        $back_sql->execute();
        while ($back = $back_sql->fetch(PDO::FETCH_ASSOC)) {
            $return['value'][] = $back;
            $i++;
        }
    }
    
    $api->sendLogUser($memberId, $api->logData('ดูข้อมูล', 'Search tools used.'));
}

$return['comment'] = "";
echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
