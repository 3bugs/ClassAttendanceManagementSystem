<?php
error_reporting(E_ERROR | E_PARSE);

header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

require_once '../db_config.php';
$db = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
if ($db->connect_errno) {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
    exit();
}
$db->set_charset("utf8");

$phpSelf = $_SERVER['PHP_SELF'];
$username = $_SESSION['username'];
$displayName = $_SESSION['display_name'];

$selectTeacherSql = "SELECT id FROM teacher WHERE username = '$username'";
$selectTeacherResult = $db->query($selectTeacherSql);
if ($selectTeacherResult) {
    if ($selectTeacherRow = $selectTeacherResult->fetch_assoc()) {
        $teacherId = $selectTeacherRow['id'];
    }
    $selectTeacherResult->close();
}
if (!isset($teacherId)) {
    echo 'Error: เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ';
    exit();
}
?>