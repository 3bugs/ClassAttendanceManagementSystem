$(document).ready(function() {
    // ทำการ refresh PHP session ทุก 5 นาที เพื่อไม่ให้ session หมดเวลา
    //refreshSession(5);
});

$(document).ajaxStart(function() {
    $('#div_loading').show();
}).ajaxStop(function () {
    $('#div_loading').hide();
});

function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}

function refreshSession(timeInMinutes) {
    var refreshTime = timeInMinutes * 60000;
    window.setInterval(function() {
        $.ajax({
            cache: false,
            type: "GET",
            url: "refresh_session.php",
            success: function(data) {
                console.log(data);
            }
        });
    }, refreshTime);
}
