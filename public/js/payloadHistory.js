$('.payload-history-item').on('click', '.form-delete', function (e) {
    e.preventDefault();
    var $form = $(this);
    $('#delete-confirm').modal({ backdrop: 'static', keyboard: false })
    .on('click', '#delete-btn', function () {
        $form.submit();
    });
});
$(document).ready(function () {
    $("#recheck").click(function (e) {
        var id = $('#history_id').val();
        var _token = $('meta[name="csrf-token"]').attr('content');
        e.preventDefault();
            $.ajax({
                url: "/history/recheck",
                type: "POST",
                data: {
                    _token: _token,
                    id: id,
                },
                success: function (response) {
                    if (response.error) {
                        jQuery.each(response.data, function(key, value){
                            toastr.error("content mismatched params", "Not found " + value, { timeOut: 6000 });
                        });
                    } else {
                        toastr.success('Success', { timeOut: 6000 });
                    }
                },
                error: function (data) {
                    toastr.error(' Something went wrong', 'Check failed', { timeOut: 4000 });
                }
            });
    });
});
