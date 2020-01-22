$(document).ready(function () {
    $('.mapping-form').on('click', '.cancel-btn', function (e) {
        e.preventDefault();
        $('#cancel-confirm').modal({ backdrop: 'static', keyboard: false })
        .on('click', '#cancel-btn', function () {
            let webhook_id = window.location.pathname.match(/\d+/)[0];
            window.location.pathname = '/webhooks/' + webhook_id + '/edit';
        });
    });
    $("#submit").click(function (e) {
        e.preventDefault();
        var _token = $('meta[name="csrf-token"]').attr('content');
        var webhook_id = $("input[name='webhook_id']").val();
        var keys = getValues($("input[name^='key[]']"));
        var values = getValues($("input[name^='value[]']"));

        $.ajax({
            url: "/webhooks/" + webhook_id + "/mappings",
            type: "POST",
            data: {
                _token: _token,
                keys: keys,
                values: values,
                webhook_id: webhook_id,
            },
            success: function (response) {
                if(response.error) {
                    toastr.error(response.message, 'Failed', { timeOut: 5000 });
                } else {
                    window.location.replace("/webhooks/" + response.webhook_id + "/edit");
                }
            },
            error: function (data) {
                $.each(data.responseJSON.errors, function (index, value) {
                    toastr.error( value, 'Failed', { timeOut: 5000 });
                })
            }
        });
    });

    $("#submitUpdate").click(function (e) {
        e.preventDefault();
        var _token = $('meta[name="csrf-token"]').attr('content');
        var webhook_id = $("input[name='webhook_id']").val();
        var keys = getValues($("input[name^='key[]']"));
        var values = getValues($("input[name^='value[]']"));
        $.ajax({
            url: "/webhooks/" + webhook_id + "/mappings/update",
            type: "POST",
            data: {
                _token: _token,
                keys: keys,
                values: values,
                webhook_id: webhook_id,
            },
            success: function (response) {
                if(response.error) {
                    toastr.error(response.message, 'Failed', { timeOut: 5000 });
                } else {
                    window.location.replace("/webhooks/" + response.webhook_id + "/edit");
                }
            },
            error: function (data) {
                $.each(data.responseJSON.errors, function (index, value) {
                    toastr.error( value, 'Failed', { timeOut: 5000 });
                })
            }
        });
    });
});

function getValues(items) {
    return items.map(function () { return $(this).val(); }).get();
}

function addFields() {
    var counter = $(".mult-condition").children().length;
    var keyInput = $("<input>")
        .attr({ name: "key[]", id: "key" + counter, placeholder: "Enter key" })
        .addClass("form-control col-md-5 key")
        .attr('onchange', 'setChangeStatus(true)');
    var valueInput = $("<input>")
        .attr({ name: "value[]", id: "value" + counter, placeholder: "Enter value" })
        .addClass("form-control col-md-5 value")
        .attr('onchange', 'setChangeStatus(true)');
    var btnDelete = $("<button/>")
        .attr({ name: "action[]", id: "action" + counter })
        .addClass("btn btn--link-danger font-weight-normal action")
        .append("<i/>").addClass("fa fa-minus-circle")
        .attr('onClick', 'removeCondition(' + counter + ')');
    var conditions = $('.mult-condition');
    var row = $('<div class="row"></div>');
    var key = $('<div class="col-md-5"></div>');
    var value = $('<div class="col-md-5"></div>');
    var btn = $('<div class="col-md-2"></div>');

    $(key).append(keyInput);
    $(value).append(valueInput);
    $(btn).append(btnDelete);
    $(row).append(key, value, btn);
    $(conditions).append(row);
}

function removeCondition(row) {
    $("#key" + row).parent().parent().remove();
    setChangeStatus(true);
}

function setChangeStatus(status) {
    hasValueChanged = status;
}

$('.cancel-btn').on('click', function (e) {
    e.preventDefault();
    $('#cancel-confirm').modal({ backdrop: 'static', keyboard: false })
        .on('click', '#cancel-btn', function () {
            var webhook_id = $("input[name='webhook_id']").val();
            window.location.pathname = '/webhooks/' + webhook_id + '/edit';
        });
});
