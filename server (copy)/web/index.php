<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require_once 'connect_db.php';

if (isset($_POST['submit'])) {
    $courseCode = $_POST['course_code'];
    $courseName = $_POST['course_name'];

    $insertCourseSql = "INSERT INTO course (code, name, teacher_id) VALUES ('$courseCode', '$courseName', $teacherId)";
    $insertCourseResult = $db->query($insertCourseSql);
    if ($insertCourseResult) {
        $insertCourseSuccess = TRUE;
        $insertCourseMessage = 'เพิ่มรายวิชาสำเร็จ';
    } else {
        $insertCourseSuccess = FALSE;
        $insertCourseMessage = 'เกิดข้อผิดพลาดในการเพิ่มรายวิชา';
    }
}

?>
    <!DOCTYPE html>
    <html>
    <head>
        <?php require_once 'include/header.php'; ?>

        <script>
            $(document).ready(function () {
                $('#data_table').dataTable({
                    "stateSave": false,
                    "ordering": true,
                    "language": {
                        "lengthMenu": "แสดงหน้าละ _MENU_ รายการ",
                        "search": "ค้นหา:",
                        "info": "แสดงรายการที่ _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                        "paginate": {
                            "first": "แรก",
                            "last": "สุดท้าย",
                            "next": "ถัดไป",
                            "previous": "ก่อนหน้า"
                        }
                    }
                });
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
        <h1>รายวิชาที่สอน</h1>
    </div>

    <?php
    if (isset($insertCourseSuccess)) {
        if ($insertCourseSuccess) {
            ?>
            <div class="alert alert-success alert-dismissible" role="alert" style="margin: 0 100px 20px;">
                <!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                <strong>Success:</strong> <?php echo $insertCourseMessage; ?>
            </div>
            <?php
        } else {
            ?>
            <div class="alert alert-danger alert-dismissible" role="alert" style="margin: 0 100px 20px;">
                <!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                <strong>Error:</strong> <?php echo $insertCourseMessage; ?>
            </div>
            <?php
        }
    }

    $sql = "SELECT * FROM course WHERE teacher_id = $teacherId ";
    $result = $db->query($sql);
    if ($result) {
        ?>
        <div id="div_table" style="margin: 0 100px 50px;">
            <table id="data_table" class="table table-striped table-bordered" width="100%">
                <thead>
                <tr>
                    <td align="center" class="title" width="20%"><strong>รหัสวิชา</strong></td>
                    <td align="center" class="title" width="50%"><strong>ชื่อวิชา</strong></td>
                    <td align="center" class="title" width="30%"><strong>การดำเนินการ</strong></td>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td align="center" style="vertical-align: text-top; font-family: monospace; "><?php echo $row['code']; ?></td>
                            <td align="left" style="vertical-align: text-top;"><?php echo $row['name']; ?></td>
                            <td align="center" style="vertical-align: text-top;">
                                <a class="btn btn-default" href="course.php?course_id=<?php echo $row['id']; ?>">ข้อมูลคลาสเรียน</a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td align="center" colspan="3" style="vertical-align: text-top;">ไม่มีข้อมูล</td>
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
            <h3><u>เพิ่มวิชาที่สอน</u></h3>
            <form method="post" action="<?php echo $phpSelf; ?>">
                <table width="100%" cellpadding="5px">
                    <tr>
                        <td width="30%">
                            <label for="course_code" class="title">รหัสวิชา</label><br>
                            <input class="form-control" type="text" name="course_code" placeholder="รหัสวิชา" size="20" style="font-family: monospace;" required/>
                        </td>
                        <td width="70%">
                            <label for="course_name" class="title">ชื่อวิชา</label><br>
                            <input class="form-control" type="text" name="course_name" placeholder="ชื่อวิชา" size="80" required/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input class="btn btn-primary" type="submit" name="submit" value=" เพิ่ม " style="margin-top: 10px; "/>
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