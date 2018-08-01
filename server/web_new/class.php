<?php
header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_SESSION['year']) || !isset($_SESSION['semester'])) {
    header('Location: select_year_semester.php');
    exit();
}

require_once 'connect_db.php';
require_once 'include/utils.php';

if (isset($_GET['class_id'])) {
    $classId = $_GET['class_id'];
    $selectClassSql = "SELECT cl.class_number AS class_number, cl.class_date AS class_date, "
        . " TIME_FORMAT(cl.class_begin_time, '%H.%i') AS class_begin_time, TIME_FORMAT(cl.class_end_time, '%H.%i') AS class_end_time, "
        . " c.id AS course_id, c.code AS course_code, c.name AS course_name "
        . " FROM class cl INNER JOIN course c ON cl.course_id = c.id "
        . " WHERE cl.id = $classId";
    $selectClassResult = $db->query($selectClassSql);
    if ($selectClassResult) {
        if ($selectClassRow = $selectClassResult->fetch_assoc()) {
            $classNumber = $selectClassRow['class_number'];
            $classDateTime = $selectClassRow['class_date'];
            $courseId = $selectClassRow['course_id'];
            $courseCode = $selectClassRow['course_code'];
            $courseName = $selectClassRow['course_name'];

            $classDateTimePart = explode(' ', $classDateTime);
            $classDate = formatThaiShortDate($classDateTimePart[0]);

            $classBeginTime = $selectClassRow['class_begin_time'];
            $classEndTime = $selectClassRow['class_end_time'];
        } else {
            echo "Error: ไม่มีคลาส ID: $classId";
            exit();
        }
        $selectClassResult->close();
    } else {
        echo 'Error: เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ';
        exit();
    }
} else {
    echo 'ไม่ได้ระบุวิชาเรียน';
    exit();
}

?>
    <!DOCTYPE html>
    <html>
    <head>
        <?php require_once 'include/header.php'; ?>

        <script>
            $(document).ready(function () {
                $.get(
                    "student_list.php?class_id=<?php echo $classId; ?>",
                    function (data) {
                        var studentListDiv = $('#student_list_div');
                        studentListDiv.empty();
                        studentListDiv.append(data);
                    }
                );

                var intervalId = setInterval(function () {
                        $.get(
                            "student_list.php?class_id=<?php echo $classId; ?>",
                            function (data) {
                                var studentListDiv = $('#student_list_div');
                                studentListDiv.empty();
                                studentListDiv.append(data);
                            }
                        );
                    }, 10000
                );
            });
        </script>

        <style>
            .title {
                font-family: "Lucida Console", Monaco, monospace;
                font-size: large;
            }
        </style>
    </head>

    <body>
    <?php
    require_once 'include/navbar.php';
    ?>
    <script>
        $("#navigation_bar li").removeClass("active");
    </script>

    <div class="center-div" id="div_loading" style="display: none;">
        <img src="images/ic_loading.gif" width="32px" height="32px">
        <br/>
    </div>

    <div class="page-header" style="text-align: center; padding-top: 50px;">
        <h1>หลักสูตรบริหารธุรกิจบัณฑิต สาขาวิชาคอมพิวเตอร์ธุรกิจ</h1>
        <h2>วิชา <?php echo "$courseCode $courseName"; ?></h2>
    </div>

    <div id="div_table" class="table-responsive" style="margin: 0 100px 50px;">
        <table class="table table-bordered">
            <tr>
                <td width="30%" align="center">
                    <h2><?php echo "เรียนครั้งที่ $classNumber"; ?></h2>
                </td>
                <td width="70%" align="center">
                    <h2 style="font-family: monospace;">
                        <?php echo $classDate; ?>, <?php echo $classBeginTime ?>-<?php echo $classEndTime; ?> น.
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table width="100%">
                        <tr>
                            <td width="30%" align="center">
                                <img src="qr.php?code=<?php echo $classId; ?>" width="250px" height="250px">
                            </td>
                            <td width="70%" valign="top" align="center">
                                <h3><u>รายชื่อนักศึกษาเข้าเรียน</u></h3>
                                <div id="student_list_div"></div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    </body>
    </html>
<?php $db->close(); ?>