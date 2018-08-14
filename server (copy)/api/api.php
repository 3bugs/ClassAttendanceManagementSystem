<?php
require_once 'global.php';

error_reporting(E_ERROR | E_PARSE);
header('Content-type: application/json; charset=utf-8');

header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

$response = array();

$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$action = strtolower(array_shift($request));
$id = array_shift($request);

require_once '../db_config.php';
$db = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

if ($db->connect_errno) {
    $response[KEY_ERROR_CODE] = ERROR_CODE_CONNECT_DB_FAILED;
    $response[KEY_ERROR_MESSAGE] = 'การเชื่อมต่อฐานข้อมูลล้มเหลว';
    $response[KEY_ERROR_MESSAGE_MORE] = $db->connect_error;
    echo json_encode($response);
    exit();
}
$db->set_charset("utf8");

switch ($action) {
    case 'insert_student':
        doInsertStudent($_POST['username'], $_POST['display_name']);
        break;
    case 'insert_teacher':
        doInsertTeacher($_POST['username'], $_POST['display_name']);
        break;
    case 'insert_class_attendance':
        doInsertClassAttendance($_POST['class_id'], $_POST['student_id']);
        break;
    default:
        $response[KEY_ERROR_CODE] = ERROR_CODE_INVALID_ACTION;
        $response[KEY_ERROR_MESSAGE] = 'No action specified or invalid action.';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        break;
}

$db->close();
echo json_encode($response);
exit();

function doInsertStudent($username, $displayName, $table = 'student')
{
    global $db, $response;

    $insertSql = "INSERT INTO $table (username, display_name) VALUES ('$username', '$displayName')";
    $insertResult = $db->query($insertSql);

    $selectSql = "SELECT id, username, display_name from $table WHERE username = '$username'";
    $selectResult = $db->query($selectSql);

    if ($selectResult) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = '';
        $response[KEY_ERROR_MESSAGE_MORE] = '';

        if ($row = $selectResult->fetch_assoc()) {
            $student = array();
            $student['id'] = $row['id'];
            $student['username'] = $row['username'];
            $student['display_name'] = $row['display_name'];
            $response['user'] = $student;
        }  else {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูลผู้ใช้ลงฐานข้อมูล';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
        }
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $selectSql";
    }
}

function doInsertTeacher($username, $displayName)
{
    doInsertStudent($username, $displayName, 'teacher');
}

function doInsertClassAttendance($classId, $studentId)
{
    global $db, $response;

    $sql = "SELECT COUNT(*) AS count FROM class_attendance WHERE class_id = $classId AND student_id = $studentId";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    $count = $row['count'];
    if ($count > 0) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'ได้บันทึกข้อมูลการเข้าเรียนไปแล้ว';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        return;
    }
    $result->close();

    $insertSql = "INSERT INTO class_attendance (class_id, student_id) VALUES ($classId, $studentId)";
    $insertResult = $db->query($insertSql);

    if ($insertResult) {
        $selectSql = "SELECT * FROM course c INNER JOIN class cl INNER JOIN class_attendance ca "
            . " ON c.id = cl.course_id AND cl.id = ca.class_id "
            . " WHERE ca.class_id = $classId AND ca.student_id = $studentId ";
        $selectResult = $db->query($selectSql);

        $row = $selectResult->fetch_assoc();
        $courseCode = $row['code'];
        $courseName = $row['name'];
        $classNumber = $row['class_number'];
        $classDate = $row['class_date'];
        $attendDate = $row['created_at'];

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = '';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $response['course_code'] = $courseCode;
        $response['course_name'] = $courseName;
        $response['class_number'] = (int)$classNumber;
        $response['class_date'] = $classDate;
        $response['attend_date'] = $attendDate;

        $selectResult->close();
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $insertSql";
    }
}

?>