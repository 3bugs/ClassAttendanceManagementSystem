<?php
error_reporting(E_ERROR | E_PARSE);

header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

session_start();

if (isset($_GET['logout'])) {
    session_unset();   // remove all session variables
    session_destroy(); // destroy the session
    header('Location: login.php');
    exit();
}

if (isset($_SESSION['username'])) {
    // redirect ไปหน้าหลัก
    header('Location: index.php');
    exit();
}

require_once '../db_config.php';
$db = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
if ($db->connect_errno) {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
    exit();
}
$db->set_charset("utf8");

$phpSelf = $_SERVER['PHP_SELF'];

?>
    <!DOCTYPE html>
    <html>
    <head>
        <?php require_once 'include/header.php'; ?>

        <script>
            $(document).ready(function () {
                $('#login_form').on("submit", function (event) {
                    hideSuccessError();
                    event.preventDefault();
                    if (this.checkValidity()) {
                        doLogin();
                    } else {
                        showError('กรอกข้อมูลให้ครบถ้วน');
                    }
                });
            });

            function doLogin() {
                var usernameInput = $('#username_input');
                var passwordInput = $('#password_input');
                var username = usernameInput.val();
                var password = passwordInput.val();

                $.post(
                    "../api/ldap.php",
                    {
                        username: username,
                        password: password
                    },
                    function (result) {
                        var errorCode = result.error_code;
                        if (errorCode === 0) {
                            var username = result.user.username;
                            var displayName = result.user.display_name;
                            var successMessage = 'ยินดีต้อนรับคุณ' + displayName;
                            showSuccess(successMessage);

                            doInsertTeacher(username, displayName);
                        } else if (errorCode > 0) {
                            var errorMessage = result.error_message;
                            showError(errorMessage);
                        }
                    }
                );
            }

            function doInsertTeacher(username, displayName) {
                $.post(
                    "../api/api.php/insert_teacher",
                    {
                        username: username,
                        display_name: displayName
                    },
                    function (result) {
                        var errorCode = result.error_code;
                        if (errorCode === 0) {
                            //showSuccess('บันทึกข้อมูลเรียบร้อย');
                            window.location = 'index.php';
                        } else if (errorCode > 0) {
                            var errorMessage = result.error_message;
                            showError(errorMessage);
                        }
                    }
                );
            }

            function showSuccess(successMessage) {
                hideSuccessError();
                $('#success_span').html(successMessage);
                $('#success_div').show();
            }

            function showError(errorMessage) {
                hideSuccessError();
                $('#error_span').html(errorMessage);
                $('#error_div').show();
            }

            function hideSuccessError() {
                $('#success_div').hide();
                $('#error_div').hide();
            }
        </script>

        <style>
            body {
				background-image: url(images/bg.jpg);
                font-family: Arial, Helvetica, sans-serif;
            }

            form {
                margin: 0 30% 0 30%;
                /*border: 3px solid #f1f1f1;*/
            }

            input[type=text], input[type=password] {
                width: 100%;
                padding: 8px 16px;
                margin: 6px 0;
                display: inline-block;
                border: 1px solid #ccc;
                box-sizing: border-box;
            }

            button {
                /*background-color: #4CAF50;*/
                background-color: #51c3f2;
                color: white;
                padding: 14px 20px;
                margin: 8px 0;
                border: none;
                cursor: pointer;
                width: 100%;
            }

            button:hover {
                opacity: 0.8;
            }

            .imgcontainer {
                text-align: center;
                margin: 10px 0 10px 0;
            }

            img.logo {
                width: 25%;
            }

            .container {
                margin-top: 10px;
                padding: 0 16px 16px 16px;
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
        <h1>เข้าสู่ระบบ </h1>
    </div>

    <form id="login_form" method="post" action="<?php echo $phpSelf; ?>">
        <div class="container">
            <div class="imgcontainer">
                <img src="images/logo.png" alt="ตราสัญลักษณ์ มหาวิทยาลัยสวนดุสิต" class="logo">
            </div>

            <div id="success_div" class="alert alert-success alert-dismissible" role="alert" style="display: none; ">
                <!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                <strong>Success:</strong> <span id="success_span"></span>
            </div>
            <div id="error_div" class="alert alert-danger alert-dismissible" role="alert" style="display: none; ">
                <!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                <strong>Error:</strong> <span id="error_span"></span>
            </div>

            <label for="username"><b>ชื่อผู้ใช้ (Username)</b></label>
            <input type="text" class="form-control" placeholder="กรอกชื่อผู้ใช้" id="username_input" name="username"
                   style="font-family: monospace;" required>

            <label for="password"><b>รหัสผ่าน (Password)</b></label>
            <input type="password" class="form-control" placeholder="กรอกรหัสผ่าน" id="password_input" name="password"
                   style="font-family: monospace;" required>

            <button type="submit" id="login_button" name="login" style="margin-top: 20px;">เข้าสู่ระบบ</button>
        </div>
		
    </form>
	
    </body>
    </html>
<?php $db->close(); ?>