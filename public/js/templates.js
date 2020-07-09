var hasValueChanged = false;

function setChangeStatus(status) {
    hasValueChanged = status;
}

function checkData() {
    var flag = true;
    $(".error-field").html("");
    $(".error-value").html("");
    $(".field").each(function () {
        if ($(this).val() == "") {
            $(".error-field." + $(this).attr('id')).html("Please enter field")
            flag = false;
        }
    });
    $(".value").each(function () {
        if ($(this).val().length > 100) {
            $(".error-value").html("Value is too long (maximum is 100 characters)")
            flag = false;
        }
        if ($(this).val() == "") {
            $(".error-value").html("Please enter value")
            flag = false;
        }
    });

    return flag;
}

function clearOldErrorMessage() {
    $(".name").html("");
    $(".params").html("");
    $(".content").html("");
}

function printErrorMsg(errors) {
    $.each(errors, function (index, value) {
        if (index === 'fields') {
            $.each(value[0], function (key, message) {
                $('#' + key)
                    .after('<div class="has-error fields"><span class="help-block">' + message + '</span></div>')
            })
        } else {
            $("." + index).html(value);
        }
    })
}

$(document).ready(function () {
    $("#submit").click(function (e) {
        e.preventDefault();
        if (checkData()) {
            var _token = $('meta[name="csrf-token"]').attr('content');
            var content = $("textarea[name='content']").val();
            var params = $("textarea[name='params']").val();
            var name = $("input[name='name']").val();
            var status = $("select[name='status']").val();

            $.ajax({
                url: "/templates",
                type: "POST",
                data: {
                    _token: _token,
                    name: name,
                    content: content,
                    params: params,
                    status: status,
                },
                success: function (id) {
                    window.location.replace("/templates/" + id + "/edit");
                },
                error: function (data) {
                    toastr.error(' Something went wrong', 'Create failed', { timeOut: 4000 });
                    clearOldErrorMessage();
                    printErrorMsg(data.responseJSON.errors);
                }
            });
        }
    });

    $("#submitUpdate").click(function (e) {
        e.preventDefault();
        if (checkData()) {
            var _token = $('meta[name="csrf-token"]').attr('content');
            var url = $("input[name='url']").val();
            var content = $("textarea[name='content']").val();
            var params = $("textarea[name='params']").val();
            var name = $("input[name='name']").val();
            var template_id = $("input[name='id']").val();
            var status = $("select[name='status']").val();
            $.ajax({
                url: url,
                method: 'PUT',
                data: {
                    _token: _token,
                    name: name,
                    content: content,
                    params: params,
                    status: status,
                    id: template_id,
                },
                success: function (id) {
                    window.location.replace("/templates/" + id + "/edit");
                },
                error: function (data) {
                    toastr.error(' Something went wrong', 'Update failed', { timeOut: 4000 });
                    clearOldErrorMessage();
                    printErrorMsg(data.responseJSON.errors);
                }
            });
        }
    });

    $('.cancel-btn').on('click', function (e) {
        e.preventDefault();
        $('#cancel-confirm').modal({ backdrop: 'static', keyboard: false })
            .on('click', '#cancel-btn', function () {
                window.location.pathname = '/templates';
            });
    });

    $("textarea[name='content']").on('change', function () {
        setChangeStatus(true);
    });

    $("textarea[name='params']").on('change', function () {
        setChangeStatus(true);
    });

    $('table[data-form="deleteForm"]').on('click', '.form-delete', function (e) {
        e.preventDefault();
        var $form = $(this);
        $('#confirm').modal({ backdrop: 'static', keyboard: false })
            .on('click', '#delete-btn', function () {
                $form.submit();
            });
    });
});
