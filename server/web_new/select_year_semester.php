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

if (isset($_POST['submit'])) {
    if (!isset($_POST['semester']) || !isset($_POST['year'])) {
        echo 'Error: Required parameter(s) missing!';
    } else {
        $_SESSION['semester'] = $_POST['semester'];
        $_SESSION['year'] = $_POST['year'];

        header('Location: index.php');
    }
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once 'include/header.php'; ?>

    <style>
        body {
            background-color: #fFF;
            font-family: Arial, Helvetica, sans-serif;
        }

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
    <h1>Class Attendance Management System</h1>
</div>

<div style="margin: auto auto 100px; width: 800px; border: 5px solid #51c3f2; padding: 20px 30px 30px 30px; text-align: center; ">
    <h3><u>เลือกภาคและปีการศึกษา</u></h3>
    <form method="post">
        <table width="100%" cellpadding="5px">
            <tr>
                <td width="50%">
                    <label for="semester_select" class="title">ภาคการศึกษา</label><br>
                    <select class="form-control" id="semester_select" name="semester" required>
                        <option value="1">1 (เทอมต้น)</option>
                        <option value="2">2 (เทอมปลาย)</option>
                    </select>
                </td>
                <td width="50%">
                    <label for="year_select" class="title">ปีการศึกษา</label><br>
                    <select class="form-control" id="year_select" name="year" required>
                        <?php
                        for ($i = 2561; $i <= 2570; $i++) {
                            ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input class="btn btn-primary" type="submit" name="submit" value=" ตกลง "
                           style="margin-top: 10px; "/>
                </td>
            </tr>
        </table>
    </form>
</div>
</body>
</html>