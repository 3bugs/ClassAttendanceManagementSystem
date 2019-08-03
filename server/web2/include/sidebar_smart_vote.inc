<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header" style="color: yellow; text-align: center">จัดการเนื้อหา (CMS)</li>
            <li>
                <a href="election.php">
                    <i class="fa fa-calendar"></i> <span>หน้าแรก</span>
                </a>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-file-text-o"></i> <span>สาระน่ารู้การเลือกตั้ง</span>
                    <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="#"><i class="fa fa-circle-o"></i> ความรู้เบื้องต้น</a></li>
                    <li><a href="info.php?category=2"><i class="fa fa-circle-o"></i> กระบวนการเลือกตั้ง ส.ส.</a></li>
                    <li><a href="info.php?category=3"><i class="fa fa-circle-o"></i> กรณีไม่อาจไปใช้สิทธิ</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> กฎหมายการเลือกตั้ง</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-archive"></i> <span>ลงคะแนนก่อนวันเลือกตั้ง</span>
                    <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="info.php?category=5"><i class="fa fa-circle-o"></i> ในเขตเลือกตั้ง</a></li>
                    <li><a href="info.php?category=6"><i class="fa fa-circle-o"></i> นอกเขตเลือกตั้ง</a></li>
                    <li><a href="info.php?category=7"><i class="fa fa-circle-o"></i> นอกราชอาณาจักร</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> คนพิการ/ทุพพลภาพ/สูงอายุ</a></li>
                </ul>
            </li>
            <li>
                <a href="party.php">
                    <i class="fa fa-folder-open"></i> <span>ข้อมูลพรรคการเมือง</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-link"></i> <span>WEB LINKS</span>
                </a>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
<script>
    $(document).ready(function () {
        // Sets active link in Bootstrap menu
        // Add this code in a central place used\shared by all pages
        // like your _Layout.cshtml in ASP.NET MVC for example
        const url = (location.pathname+location.search).substr(1);
        $('a[href="' + url + '"]').parents('li,ul').addClass('active');
        //alert(url);
    });
</script>