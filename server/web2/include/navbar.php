<nav id="main_navbar" class="navbar navbar-default navbar-fixed-top">
    <div id="container_fluid" class="container-fluid" style="padding-bottom: 0;">
        <div class="navbar-header">
            <a class="navbar-brand" href="index.php" style="display: flex; align-items: center; ">
                <span>
                    Class Attendance Management System
                </span>
            </a>
            <?php
            if (isset($_SESSION['year']) && isset($_SESSION['semester'])) {
                ?>
                <a class="navbar-brand" href="select_year_semester.php" style="display: flex; align-items: center; ">
                <span style="background-color: yellow; padding: 2px">
                    <?php
                    if (isset($_SESSION['year']) && isset($_SESSION['semester'])) {
                        $semester = $_SESSION['semester'];
                        $year = $_SESSION['year'];
                        echo "&nbsp;$semester / $year&nbsp;";
                    }
                    ?>
                </span>
                </a>
                <?php
            }
            ?>
        </div>

        <?php
        $phpSelf = $_SERVER['PHP_SELF'];

        if (strpos($phpSelf, 'login.php') === FALSE && strpos($phpSelf, 'select_year_semester.php') === FALSE) {
            ?>
            <ul class="nav navbar-nav">
                <ul class="breadcrumb list-inline" style="display: flex; align-items: center; ">
                    <?php
                    if (strpos($phpSelf, 'index.php') !== FALSE) {
                        ?>
                        <li class="active">Home</li>
                        <?php
                    } else if (strpos($phpSelf, 'course.php') !== FALSE) {
                        ?>
                        <li><a href="index.php">Home</a></li>
                        <li class="active"><?php echo "วิชา $courseCode"; ?></li>
                        <?php
                    } else if (strpos($phpSelf, 'class.php') !== FALSE) {
                        ?>
                        <li><a href="index.php">Home</a></li>
                        <li>
                            <a href="course.php?course_id=<?php echo $courseId; ?>"><?php echo "วิชา $courseCode"; ?></a>
                        </li>
                        <li class="active"><?php echo "เรียนครั้งที่ $classNumber"; ?></li>
                        <?php
                    }
                    ?>
                </ul>
            </ul>
            <?php
        }
        ?>

        <!--<ul class="breadcrumb">
            <li><a href="#">Home</a> <span class="divider">/</span></li>
            <li><a href="#">Library</a> <span class="divider">/</span></li>
            <li class="active">Data</li>
        </ul>-->

        <!--<ul id="navigation_bar" class="nav navbar-nav">
            <li id="data_menu" class="active">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <span class="glyphicon glyphicon-tasks"></span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="index.php">Menu Item 1</a></li>
                    <li><a href="index.php">Menu Item 2</a></li>
                    <li><a href="index.php">Menu Item 3</a></li>
                </ul>
            </li>
            <li id="user_menu" class="active">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <span class="glyphicon glyphicon-user"></span> Menu 2
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="index.php">Menu Item 1</a></li>
                </ul>
            </li>
        </ul>-->
        <?php
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $displayName = $_SESSION['display_name'];
            ?>
            <ul class="nav navbar-nav navbar-right">
                <li id="profile_menu" class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"
                       style="display: flex; align-items: center;">
                        <img src="images/avatar.png" width="30px"
                             height="30px"/>&nbsp;&nbsp;<?php echo "$displayName ($username)"; ?>&nbsp;&nbsp;<span
                                class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li id="logout_menu_item"><a href="#"><span class="glyphicon glyphicon-log-out"></span>
                                ออกจากระบบ</a></li>
                    </ul>
                </li>
            </ul>
            <?php
        }
        ?>
    </div>
</nav>

<script>
    $("#logout_menu_item").on("click", function () {
        var dialogResult = confirm("ยืนยันออกจากระบบ?");
        if (dialogResult === true) {
            window.location = "login.php?logout=yes";
        }
    });
</script>
