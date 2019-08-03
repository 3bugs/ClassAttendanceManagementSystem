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

if (isset($_GET['course_id'])) {
    $courseId = $_GET['course_id'];
    $selectCourseSql = "SELECT * FROM course WHERE id = $courseId";
    $selectCourseResult = $db->query($selectCourseSql);
    if ($selectCourseResult) {
        if ($selectCourseRow = $selectCourseResult->fetch_assoc()) {
            $courseCode = $selectCourseRow['code'];
            $courseName = $selectCourseRow['name'];
        } else {
            echo "Error: ไม่มีคอร์ส ID: $courseId";
            exit();
        }
        $selectCourseResult->close();
    } else {
        echo 'Error: เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ';
        exit();
    }
} else {
    echo 'ไม่ได้ระบุวิชาเรียน';
    exit();
}

if (isset($_POST['submit'])) {
    $classNumber = $_POST['class_number'];
    $classDate = $_POST['class_date'];
    $classBeginTime = $_POST['class_begin_time'];
    $classEndTime = $_POST['class_end_time'];

    $sql = "SELECT * FROM class WHERE course_id = $courseId AND class_number = $classNumber";
    $result = $db->query($sql);
    if ($result) {
        if ($row = $result->fetch_assoc()) {
            $insertClassSuccess = FALSE;
            $insertClassMessage = 'ระบุครั้งที่เรียนไม่ถูกต้อง (ซ้ำ)';
        } else {
            $classDatePart = explode('/', $classDate);
            $day = $classDatePart[0];
            $month = $classDatePart[1];
            $year = $classDatePart[2];
            $classDate = "$year-$month-$day $classBeginTime:00";

            $insertClassSql = "INSERT INTO class (course_id, class_number, class_date, class_begin_time, class_end_time) "
                . " VALUES ($courseId, $classNumber, '$classDate', '$classBeginTime', '$classEndTime')";
            $insertClassResult = $db->query($insertClassSql);
            if ($insertClassResult) {
                $insertClassSuccess = TRUE;
                $insertClassMessage = 'เพิ่มคลาสเรียนสำเร็จ';
            } else {
                $insertClassSuccess = FALSE;
                $insertClassMessage = 'เกิดข้อผิดพลาดในการเพิ่มคลาสเรียน';
            }
        }
    } else {
        $insertClassSuccess = FALSE;
        $insertClassMessage = 'เกิดข้อผิดพลาดในการเพิ่มคลาสเรียน';
    }
}

?>
    <!DOCTYPE html>
    <html>
    <head>
        <?php require_once 'include/header.php'; ?>
        <script src="scripts/clockpicker.js"></script>
        <link rel="stylesheet" href="css/clockpicker.css">
        <script>
            $(document).ready(function () {
                $("#class_date_input").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    minDate: 0,
                    dateFormat: 'dd/mm/yy',
                    showAnim: 'slide'
                });

                $('.clockpicker').clockpicker();
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

    <?php
    if (isset($insertClassSuccess)) {
        if ($insertClassSuccess) {
            ?>
            <div class="alert alert-success alert-dismissible" role="alert" style="margin: 0 100px 20px;">
                <!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                <strong>Success:</strong> <?php echo $insertClassMessage; ?>
            </div>
            <?php
        } else {
            ?>
            <div class="alert alert-danger alert-dismissible" role="alert" style="margin: 0 100px 20px;">
                <!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                <strong>Error:</strong> <?php echo $insertClassMessage; ?>
            </div>
            <?php
        }
    }

    $sql = "SELECT id, class_number, class_date, TIME_FORMAT(class_begin_time, '%H.%i') AS class_begin_time, TIME_FORMAT(class_end_time, '%H.%i') AS class_end_time FROM class WHERE course_id = $courseId ";
    $result = $db->query($sql);
    if ($result) {
        ?>
        <div id="div_table" style="margin: 0 100px 50px;">
            <div style="text-align: center;"><h3><u>คลาสเรียน</u></h3></div>
            <table id="data_table" class="table table-striped table-bordered" width="100%">
                <thead>
                <tr>
                    <td width="" align="center" bgcolor="#90A2F6" class="title"><strong>เรียนครั้งที่</strong></td>
                    <td width="" align="center" bgcolor="#90A2F6" class="title"><strong>วันที่เรียน</strong></td>
                    <td width="" align="center" bgcolor="#90A2F6" class="title"><strong>เวลาเรียน</strong></td>
                    <td width="" align="center" bgcolor="#90A2F6" class="title"><strong>ข้อมูลคิวอาร์โค้ดและรายชื่อนักศึกษา</strong>
                    </td>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $dateTime = $row['class_date'];
                        $dateTimePart = explode(' ', $dateTime);
                        $date = formatThaiShortDate($dateTimePart[0]);
                        /*$datePart = explode('-', $date);
                        $day = $datePart[2];
                        $month = $datePart[1];
                        $year = $datePart[0];
                        $date = "$day/$month/$year";*/
                        ?>
                        <tr>
                            <td align="center" style="vertical-align: text-top; font-family: monospace; ">
                                <?php echo $row['class_number']; ?>
                            </td>
                            <td align="center"
                                style="vertical-align: text-top; font-family: monospace;"><?php echo $date; ?></td>
                            <td align="center" style="vertical-align: text-top; font-family: monospace;">
                                <?php echo $row['class_begin_time'] . ' - ' . $row['class_end_time'] . ' น.'; ?>
                            </td>
                            <td align="center" style="vertical-align: text-top;">
                                <a class="btn btn-info" href="class.php?class_id=<?php echo $row['id']; ?>">ข้อมูลการเข้าเรียน</a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td align="center" colspan="4" style="vertical-align: text-top;">ไม่มีข้อมูล</td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
        <?php
        $result->close();
        ?>

        <div style="margin: auto auto 100px; width: 800px; border: 5px solid #51c3f2; padding: 20px 30px 30px 30px; text-align: center; ">
            <h3><u>เพิ่มคลาสเรียน</u></h3>
            <form method="post">
                <table width="100%" cellpadding="5px">
                    <tr>
                        <td width="25%">
                            <label for="class_number_select" class="title">เรียนครั้งที่</label><br>
                            <select class="form-control" id="class_number_select" name="class_number" required>
                                <?php
                                $sql = "SELECT class_number, TIME_FORMAT(class_begin_time, '%H:%i') AS class_begin_time, TIME_FORMAT(class_end_time, '%H:%i') AS class_end_time FROM class WHERE course_id = $courseId ORDER BY class_number DESC LIMIT 1";
                                if ($result = $db->query($sql)) {
                                    if ($result->num_rows > 0) {
                                        $row = $result->fetch_assoc();
                                        $nextClassNumber = $row['class_number'] + 1;
                                        $beginTime = $row['class_begin_time'];
                                        $endTime = $row['class_end_time'];
                                    } else {
                                        $nextClassNumber = 1;
                                        $beginTime = '';
                                        $endTime = '';
                                    }
                                    $result->close();
                                }


                                /*$sql = "SELECT MAX(class_number) max_class_number FROM class WHERE course_id = $courseId";
                                $result = $db->query($sql);
                                if ($result) {
                                    $row = $result->fetch_assoc();
                                    $maxClassNumber = $row['max_class_number'];
                                    $result->close();
                                }*/
                                //$selected = '';
                                for ($i = 1; $i <= 15; $i++) {
                                    if ($i == $nextClassNumber) {
                                        $selected = 'selected';
                                    } else {
                                        $selected = '';
                                    }
                                    /*if (isset($maxClassNumber) && !is_null($maxClassNumber)) {
                                        if ($i == $maxClassNumber + 1) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }
                                    } else {
                                        if ($i == 1) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }
                                    }*/
                                    ?>

                                    <option values="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </td>
                        <td width="35%">
                            <label for="class_date_input" class="title">วันที่เรียน</label><br>
                            <input class="form-control" type="text" id="class_date_input" name="class_date"
                                   style="font-family: monospace;" required>
                            <!--<input class="form-control" type="text" name="class_date" size="20" required/>-->
                        </td>
                        <td width="20%">
                            <label for="class_begin_time_input" class="title">เวลาเริ่ม</label><br>
                            <!--<input class="form-control" type="time" id="class_begin_time_input" name="class_begin_time"
                                   min="6:00" max="18:00" style="font-family: monospace;" required/>-->
                            <div class="input-group clockpicker" data-placement="top" data-align="top" data-autoclose="true">
                                <input class="form-control" type="text" id="class_begin_time_input" name="class_begin_time"
                                       value="<?php echo $beginTime; ?>" style="font-family: monospace;" required>
                                <!--<span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>-->
                            </div>
                        </td>
                        <td width="20%">
                            <label for="class_end_time_input" class="title">เวลาเลิก</label><br>
                            <!--<input class="form-control" type="time" id="class_end_time_input" name="class_end_time" min="6:00" max="18:00" style="font-family: monospace;" required />-->

                            <div class="input-group clockpicker" data-placement="top" data-align="top" data-autoclose="true">
                                <input class="form-control" type="text" id="class_end_time_input" name="class_end_time"
                                       value="<?php echo $endTime; ?>" style="font-family: monospace;" required>
                                <!--<span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>-->
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center">
                            <input class="btn btn-primary" type="submit" name="submit" value=" เพิ่ม "
                                   style="margin-top: 10px; "/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <?php
    } else {
        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
    }
    ?>

    </body>
    </html>
<?php $db->close(); ?>