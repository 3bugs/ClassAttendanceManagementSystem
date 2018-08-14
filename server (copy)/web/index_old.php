<?php

require_once '../db_config.php';
$db = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
if ($db->connect_errno) {
    header('Content-type: text/html; charset=utf-8');
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
    exit();
}

$db->set_charset("utf8");

?>
    <!DOCTYPE html>
    <html>
    <head>
        <?php require_once 'header.php'; ?>

        <script>
            $(document).ready(function () {
                $('#course_select').on("change", function (event) {
                    if (validateForm()) {
                        this.form.submit();
                    }
                });

                <?php
                if (isset($_POST['submit_hidden'])) {
                $courseCode = $_POST['course_select'];
                ?>
                $.get(
                    "student_list.php?course_code=<?php echo $courseCode; ?>",
                    function (data) {
                        var studentListDiv = $('#student_list_div');
                        studentListDiv.empty();
                        studentListDiv.append(data);
                    }
                );

                var intervalId = setInterval(function () {
                        $.get(
                            "student_list.php?course_code=<?php echo $courseCode; ?>",
                            function (data) {
                                var studentListDiv = $('#student_list_div');
                                studentListDiv.empty();
                                studentListDiv.append(data);
                            }
                        );
                    }, 12000
                );
                <?php
                }
                ?>
            });

            function validateForm() {
                var courseSelectValue = $('#course_select').val();
                if (courseSelectValue !== null) {
                    return true;
                } else {
                    alert('เลือกวิชา');
                    return false;
                }
            }

        </script>
    </head>

    <body>
    <div style="margin: 50px 200px 50px 200px; text-align: center; ">
        <h1>Class Attendance Management System</h1>
        <br><br>
        <form id="main_form" method="post">
            <input type="hidden" name="submit_hidden" value="true"/>
            <div class="form-group">
                <select class="form-control" id="course_select" name="course_select">
                    <option value="-1" disabled selected> -- เลือกวิชาเรียน --</option>
                    <?php
                    $sql = "SELECT * FROM course";
                    $result = $db->query($sql);
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            $courseId = $row['id'];
                            $courseCode = $row['code'];
                            $courseName = $row['name'];
                            ?>
                            <option value="<?php echo $courseCode; ?>"><?php echo "$courseCode - $courseName"; ?></option>
                            <?php
                        }
                        $result->close();
                    }
                    ?>
                </select>
            </div>
            <!--<input class="btn btn-primary" type="submit" id="submit_button" name="submit_button" value="เลือก" / >-->
        </form>

        <?php
        if (isset($_POST['submit_hidden'])) {
            ?>
            <br>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <!--<tr>
                        <th align="center">รหัสวิชา</th>
                        <th align="center">ชื่อวิชา</th>
                    </tr>-->
                    <?php
                    $courseCode = $_POST['course_select'];
                    $sql = "SELECT * FROM course WHERE code = '$courseCode'";
                    $result = $db->query($sql);
                    if ($result) {
                        $row = $result->fetch_assoc();
                        ?>
                        <tr>
                            <td><h2><?php echo $row['code'] ?></h2></td>
                            <td><h2><?php echo $row['name'] ?></h2></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table width="100%">
                                    <tr>
                                        <td width="50%">
                                            <img src="qr.php?code=<?php echo $row['code']; ?>" width="250px"
                                                 height="250px">
                                        </td>
                                        <td width="50%" valign="top" align="center">
                                            <h3><u>รายชื่อนักศึกษาเข้าเรียน</u></h3>
                                            <div id="student_list_div"></div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            <?php
        }
        ?>
    </div>
    </body>
    </html>
<?php
$db->close();
?>