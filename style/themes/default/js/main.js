$(document).ready(function () {
    $("#up").click(function () {
        $("#hide").slideToggle("slow");
        $(this).toggleClass("active");
        return false;
    });
});
