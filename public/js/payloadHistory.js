$('.payload-history-item').on('click', '.form-delete', function (e) {
    e.preventDefault();
    var $form = $(this);
    $('#delete-confirm').modal({ backdrop: 'static', keyboard: false })
    .on('click', '#delete-btn', function () {
        $form.submit();
    });
});
