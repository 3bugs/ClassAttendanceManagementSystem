<?php
function convertDateFormat($date) {
    $datePart = explode('-', $date);
    $day = $datePart[2];
    $month = $datePart[1];
    $year = $datePart[0];
    return "$day/$month/$year";
}

function formatThaiShortDate($date) {
    $datePart = explode('-', $date);
    $day = (int)$datePart[2];
    $month = $datePart[1] - 1;
    $year = $datePart[0] + 543;

    $monthNameArray = array("ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
    $monthName = $monthNameArray[$month];
    return "$day $monthName $year";
}

function formatTime($time) {
    $timePart = explode(':', $time);
    $hour = $timePart[0];
    $minute = $timePart[1];
    return "$hour.$minute";
}
?>