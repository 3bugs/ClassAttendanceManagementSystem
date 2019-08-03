<?php
error_reporting(E_ERROR | E_PARSE);

header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

require_once '../db_config.php';
require_once 'include/utils.php';

$db = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
if ($db->connect_errno) {
    echo 'Error: เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
    exit();
}

$db->set_charset("utf8");

$classId = $_GET['class_id'];

$sql = "SELECT s.username AS username, s.display_name AS display_name, cl.class_date AS class_date, ca.created_at AS attend_date, TIMESTAMPDIFF(MINUTE, cl.class_date, ca.created_at) AS time_diff_minute "
    . " FROM class_attendance ca INNER JOIN student s INNER JOIN class cl "
    . " ON ca.student_id = s.id AND ca.class_id = cl.id "
    . " WHERE class_id = $classId ORDER BY attend_date ASC ";
if ($result = $db->query($sql)) {
    $numStudents = $result->num_rows;
    $lateStudents = 0;
    $output = <<<EOT
    <table class="table table-bordered">
        <tr>
            <th class="text-center">ลำดับ</th>
            <th class="text-center">รหัสนักศึกษา</th>
            <th class="text-center">ชื่อ-นามสกุล</th>
            <th class="text-center">วันที่เข้าเรียน</th>
            <th class="text-center">เวลาเข้าเรียน</th>
            <th class="text-center">สถานะ</th>
        </tr>
EOT;
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $count++;
        $studentId = substr($row['username'], 1);
        $displayName = $row['display_name'];
        $attendDatePart = explode(" ", $row['attend_date']);
        $attendDate = formatThaiShortDate($attendDatePart[0]);
        $attendTime = formatTime($attendDatePart[1]);
        $timeDiffMinute = $row['time_diff_minute'] - 15;

        $bgColor = 'white';
        if ($timeDiffMinute > 0 && $timeDiffMinute <= 120) {
            $bgColor = 'yellow';
        } else if ($timeDiffMinute > 120) {
            $bgColor = '#ff4500';
        }

        if ($timeDiffMinute > 0) {
            $lateStudents++;
        }
        //$rowStyle = $timeDiffMinute > 0 ? 'style="background: #FFBFC5"' : '';
        $statusText = $timeDiffMinute > 0 ? "มาสาย $timeDiffMinute นาที" : "ตรงเวลา";
        $output .= <<<EOT2
            <tr bgcolor="$bgColor">
                <td style="font-family: monospace;" class="text-center">$count</td>
                <td style="font-family: monospace;" class="text-center">$studentId</td>
                <td>$displayName</td>
                <td style="font-family: monospace;">$attendDate</td>
                <td style="font-family: monospace;">$attendTime น.</td>
                <td>$statusText</td>
            </tr>
EOT2;
    }
    $output .= '</table>';
    $result->close();
    ?>
    <h4 style="color: dodgerblue; margin-top: 25px; margin-bottom: 25px;">เข้าเรียน <?php echo $numStudents; ?> คน, ในจำนวนนี้มาสาย <?php echo $lateStudents; ?>
        คน</h4>
    <?php
    echo $output;
} else {
    echo 'Error: เกิดข้อผิดพลาดในการดึงข้อมูลจากฐานข้อมูล: ' . $db->error;
}

$db->close();
?>