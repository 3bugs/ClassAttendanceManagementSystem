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

$classId = $_GET['class_id'];

$sql = "SELECT * FROM class_attendance ca INNER JOIN student s ON ca.student_id = s.id "
    . " WHERE class_id = $classId ORDER BY created_at ASC ";
$result = $db->query($sql);

$studentList = "";
while ($row = $result->fetch_assoc()) {
    $studentList .= $row['display_name'] . "<br>\n";
}
echo $studentList;

$result->close();
$db->close();
?>