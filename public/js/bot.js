$(document).ready(function () {
    $('table[data-form="deleteForm"]').on('click', '.form-delete', function (e) {
        e.preventDefault();
        var $form = $(this);
        $('#confirm').modal({ backdrop: 'static', keyboard: false })
            .on('click', '#delete-btn', function () {
                $form.submit();
            });
    });
    
    $('.bot-form').on('click', '.cancel-btn', function (e) {
        e.preventDefault();
        $('#cancel-confirm').modal({ backdrop: 'static', keyboard: false })
            .on('click', '#cancel-btn', function () {
                window.location.pathname = '/bots';
            });
    });
});
