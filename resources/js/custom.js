$(document).ready(function () {
    var pathNames = window.location.pathname.split('/');
    var current_root_path = window.location.pathname.includes('/admin') ? pathNames[2] : pathNames[1];

    $(".sidebar-content a").each(function (index, element) {
        $(element).removeClass('active');
        if (element.href.split('/').pop() === current_root_path) {
            $(element).addClass('active');
        }
    });

    $('.alert-dismissible').fadeIn().delay(3000).fadeOut();
});
