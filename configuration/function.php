<?php
class API {
    public function sendLog($cert_id, $data) {
        global $sql, $datetimes, $mssql_db_user;
        $log_sql = $sql->prepare("INSERT INTO ".$mssql_db_user.".dbo.trlogapi(cert_code, data, createDate) VALUES(:cer, :data, :crDate)");
        $log_sql->BindParam(":cer", $cert_id);
        $log_sql->BindParam(":data", $data);
        $log_sql->BindParam(":crDate", $datetimes);
        $log_sql->execute();
    }

    public function updateCount($cert_id) {
        global $sql, $mssql_db_user;
        $chk_sql = $sql->prepare("SELECT [limit], [count] FROM ".$mssql_db_user.".dbo.trcertification WHERE cert_key = :cer");
        $chk_sql->BindParam(":cer", $cert_id);
        $chk_sql->execute();
        $chk = $chk_sql->fetch(PDO::FETCH_ASSOC);
        if($chk['limit'] <= $chk['count']) {
            return false;
        } else {
            $cnt_sql = $sql->prepare("UPDATE ".$mssql_db_user.".dbo.trcertification SET count = count+1 WHERE cert_key = :cer");
            $cnt_sql->BindParam(":cer", $cert_id);
            $cnt_sql->execute();
            return true;
        }
    }

    public function getCertification($cert_id) {
        global $sql;
        $chk_sql = $sql->prepare("SELECT cert_key FROM trcertification WHERE cert_key = :cer");
        $chk_sql->BindParam(":cer", $cert_id);
        $chk_sql->execute();
        $chk = $chk_sql->fetch(PDO::FETCH_ASSOC);
        if(!$chk) {
            return false;
        } else {
            return true;
        }
    }

    public function cl($txt) {
        $text = str_replace("'", "", $txt);
        return $text;
    }

    public function getMember($memberId, $password, $field) {
        global $sql, $mssql_db_user;
        $mem_sql = $sql->prepare("SELECT * FROM ".$mssql_db_user.".dbo.trmember WHERE member_id = :memId AND password = :pass");
        $mem_sql->BindParam(":memId", $memberId);
        $mem_sql->BindParam(":pass", $password);
        $mem_sql->execute();
        $mem = $mem_sql->fetch(PDO::FETCH_ASSOC);
        if($mem) {
            return $mem[$field];
        } else {
            return false;
        }
    }

    public function getMenu($menuId, $field) {
        global $sql, $mssql_db_user;
        $menu_sql = $sql->prepare("SELECT ".$field." FROM ".$mssql_db_user.".dbo.trmenu WHERE menuId = :id");
        $menu_sql->BindParam(":id", $menuId);
        $menu_sql->execute();
        $menu = $menu_sql->fetch(PDO::FETCH_ASSOC);
        if($menu) {
            return $menu[$field];
        } else {
            return false;
        }
    }

    public function sendLogUser($memberId, $data) {
        global $sql, $mssql_db_user, $datetimes;
        $log_sql = $sql->prepare("INSERT INTO ".$mssql_db_user.".dbo.trlogevent(member_id, data, createDate) VALUES(:memId, :data, :createDate)");
        $log_sql->BindParam(":memId", $memberId);
        $log_sql->BindParam(":data", $data);
        $log_sql->BindParam(":createDate", $datetimes);
        $log_sql->execute();
        if($log_sql) {
            return true;
        } else {
            return false;
        }
    }

    public function logData($title, $text) {
        global $datetimes;
        $data = array(
            'title' => $title,
            'text' => $text,
            'createDate' => $datetimes
        );
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function creditManage($mode, $amount, $memberId) {
        global $sql, $mssql_db_user;
        if($mode == "add") {
            $credit_sql = $sql->prepare("UPDATE ".$mssql_db_user.".dbo.trmember SET credit = credit + :amount WHERE member_id = :membId");
            $credit_sql->BindParam(":amount", $amount);
            $credit_sql->BindParam(":membId", $memberId);
            $credit_sql->execute();
            if(!$credit_sql) {
                return false;
            } else {
                return true;
            }
        } else if($mode == "reduce") {
            $credit_sql = $sql->prepare("UPDATE ".$mssql_db_user.".dbo.trmember SET credit = credit - :amount WHERE member_id = :membId");
            $credit_sql->BindParam(":amount", $amount);
            $credit_sql->BindParam(":membId", $memberId);
            $credit_sql->execute();
            if(!$credit_sql) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

}
?>