<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('memory_limit', '-1');
error_reporting(E_ALL);
// SQL Server
$mssql_host	=	"171.99.203.126"; // ชื่อโฮส SQL
$mssql_user	=	"sa"; // ชื่อยูสเซอร์ SQL
$mssql_pass	=	"P@ssw0rd"; // รหัสผ่าน SQL
$mssql_db_user	=	"cia_db"; // ชื่อฐานข้อมูลที่ใช้เก็บ User
$mssql_db_info	=	"cia"; // ชื่อฐานข้อมูลที่ใช้เก็บข้อมูลอื่นๆ

// TABLE
$tbl["census"] = '[cia].[dbo].[CENSUS]';
$tbl["carall"] = "[cia].[dbo].[CarWhole]";
$tbl["customer"] = "[cia].[dbo].[CustomerData]";
$tbl["SSDB"] = "[cia].[dbo].[SSDB]";
$tbl['company'] = '[cia].[dbo].[Company]';
$tbl['carclean'] = '[cia].[dbo].[CarWhole_clean]';
$tbl['bikeall'] = '[CIABike].[dbo].[BikeAll]';

///////////////////// ห้ามแก้ไข //////////////////
#คำสั่งเชื่อมต่อฐานข้อมูล
date_default_timezone_set('Asia/Bangkok');
$sql = new PDO("sqlsrv:server=".$mssql_host.";Database=".$mssql_db_user,$mssql_user,$mssql_pass);
include_once("function.php");
$api = new API(true);
$datetimes = date("Y-m-d H:i:s");


?>