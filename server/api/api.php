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
//require_once '../web_new/include/utils.php';
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
    case 'select_course_by_student':
        doSelectCourseByStudent($_POST['student_id']);
        break;
    case 'select_class_attendance':
        doSelectClassAttendance($_POST['course_id'], $_POST['student_id']);
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

    $sql = "SELECT username FROM $table WHERE username = '$username'";
    if ($result = $db->query($sql)) {
        if ($result->num_rows == 0) {
            $insertSql = "INSERT INTO $table (username, display_name) VALUES ('$username', '$displayName')";
            $insertResult = $db->query($insertSql);
        }
        $result->close();
    }

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
        $selectResult->close();
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

function doSelectCourseByStudent($studentId)
{
    global $db, $response;

    $sql = "SELECT c.id AS course_id, c.code AS course_code, c.name AS course_name FROM course c INNER JOIN class cl INNER JOIN class_attendance ca "
        . " ON c.id = cl.course_id AND cl.id = ca.class_id "
        . " WHERE ca.student_id = $studentId GROUP BY course_id ORDER BY course_id ";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = '';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $response['course_list'] = array();

        while ($row = $result->fetch_assoc()) {
            $course = array();
            $course['id'] = (int)$row['course_id'];
            $course['code'] = $row['course_code'];
            $course['name'] = $row['course_name'];
            array_push($response['course_list'], $course);
        }
        $result->close();
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doSelectClassAttendance($courseId, $studentId)
{
    global $db, $response;

    $sql = "SELECT cl.class_number AS class_number, cl.class_date AS class_date, ca.created_at AS attend_date "
        . " FROM class cl LEFT JOIN "
        . " (SELECT * FROM class_attendance WHERE student_id = $studentId) AS ca "
        . " ON cl.id = ca.class_id "
        . " WHERE cl.course_id = $courseId "
        . " ORDER BY class_number";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = '';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $response['class_attendance_list'] = array();

        while ($row = $result->fetch_assoc()) {
            $ca = array();
            $ca['class_number'] = (int)$row['class_number'];
            $ca['class_date'] = $row['class_date'];
            $ca['attend_date'] = $row['attend_date'];
            if (!is_null($row['attend_date'])) {
                $attendDate = new DateTime($row['attend_date']);
                $classDate = new DateTime($row['class_date']);

                $attendDatePart = explode(" ", $row['attend_date']);
                $ca['attend_date_format'] = formatThaiShortDate($attendDatePart[0]);
                $ca['attend_time_format'] = formatTime($attendDatePart[1]);

                $dateDiff = $classDate->diff($attendDate);
                $minutes = $dateDiff->days * 24 * 60;
                $minutes += $dateDiff->h * 60;
                $minutes += $dateDiff->i;

                $ca['date_diff_minutes'] = $minutes;
            } else {
                $ca['attend_date_format'] = NULL;
                $ca['attend_time_format'] = NULL;
                $ca['date_diff_minutes'] = NULL;
            }
            array_push($response['class_attendance_list'], $ca);
        }
        $result->close();
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function formatThaiShortDate($date) {
    $datePart = explode('-', $date);
    $day = (int)$datePart[2];
    $month = $datePart[1] - 1;
    $year = $datePart[0] + 543;

    $monthNameArray = array("ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
    $monthName = $monthNameArray[$month];
    return "$day $monthName $year";
}

function formatTime($time) {
    $timePart = explode(':', $time);
    $hour = $timePart[0];
    $minute = $timePart[1];
    return "$hour.$minute";
}

?>