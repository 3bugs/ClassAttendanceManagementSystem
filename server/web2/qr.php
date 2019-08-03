<?php
include('phpqrcode/qrlib.php');

$courseCode = $_GET['code'];
QRCode::png($courseCode, false, QR_ECLEVEL_L, 4);
?>