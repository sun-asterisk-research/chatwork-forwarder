var hasValueChanged = false;

function setChangeStatus(status) {
    hasValueChanged = status;
}
function adminChangeStatus(status, templateId)
{
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'PUT',
            url: `/admin/templates/${templateId}/change_status`,
            data: {
                id: templateId,
                status: status
            },
            success: function(data) {
                toastr.success(data, 'Update Successfully', {timeOut: 3000, showEasing: 'linear'});
                setTimeout(function() {
                    window.location.reload();
                }, 3000);
            },
            error: function(error) {
                error = error.responseJSON;
                setTimeout(function() {
                    window.location.reload();
                }, 3000);
                toastr.error(error['message'], error['status'], {timeOut: 3000});
            }
        })
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

    return flag;
}

function getValues(items) {
    return items.map(function () { return $(this).val(); }).get();
}

function clearOldErrorMessage() {
    $(".name").html("");
    $(".params").html("");
    $(".content").html("");
    $(".fields").remove();
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
            var contentType = $("input[name='content_type']:checked").val();
            var content = $("textarea[name='content']").val();
            var params = $("textarea[name='params']").val();
            var name = $("input[name='name']").val();
            var status = $("select[name='status']").val();
            var fields = getValues($("input[name^='field[]']"));
            var operators = getValues($("select[name^='operator[]']"));
            var values = getValues($("input[name^='value[]']"));

            $.ajax({
                url: "/templates",
                type: "POST",
                data: {
                    _token: _token,
                    name: name,
                    content: content,
                    params: params,
                    status: status,
                    operators: operators,
                    fields: fields,
                    values: values,
                    content_type: contentType,
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
        if (checkData() && checkDataCondition()) {
            var _token = $('meta[name="csrf-token"]').attr('content');
            var url = $("input[name='url']").val();
            var contentType = $("input[name='content_type']:checked").val();
            var content = $("textarea[name='content']").val();
            var params = $("textarea[name='params']").val();
            var name = $("input[name='name']").val();
            var template_id = $("input[name='id']").val();
            var status = $("select[name='status']").val();
            var fields = getValues($("input[name^='field[]']"));
            var operators = getValues($("select[name^='operator[]']"));
            var values = getValues($("input[name^='value[]']"));
            var ids = $("input[name^='field[]']").map(function () { return $(this).attr("data-id"); }).get();
            var conditions = [];
            for (i = 0; i < fields.length; i++) {
                conditions.push({
                    id: ids[i] ? ids[i] : "",
                    field: fields[i],
                    operator: operators[i],
                    value: values[i],
                });
            }
            $.ajax({
                url: url,
                method: 'PUT',
                data: {
                    _token: _token,
                    name: name,
                    content: content,
                    params: params,
                    id: template_id,
                    conditions: conditions,
                    ids: ids,
                    content_type: contentType,
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
    $('body').on('click', '.btn-submit-wh', function() {
        var template_id = $(this).data('id');
        var template_name = $(this).data('name');
        $('#publicModal .template-name').text(template_name);
        $('#publicModal input').val(template_id);
        $('#publicModal').modal('show');
    });

    $('body').on('click', '.btn-unsubmit-wh', function() {
        var template_id = $(this).data('id');
        var template_name = $(this).data('name');
        $('#unpublicModal .webhook-name').text(template_name);
        $('#unpublicModal input').val(template_id);
        $('#unpublicModal').modal('show');
    });

    $('body').on('click', '.btn-confirm-public', function() {
        updateTemplateStatus('#publicModal', 'submit', 'success', 'warning');
    });

    $('body').on('click', '.btn-confirm-unpublic', function() {
        updateTemplateStatus('#unpublicModal', 'unsubmit', 'warning', 'default');
    });

    function updateTemplateStatus(modal_id, status, current_btn, current_label) {
        let template_id = $(modal_id + ' input').val();
        let item = $('.item-' + template_id);
        let status_change = (status == 'submit') ? 'reviewing' : 'private';
        let opposite_status = (status == 'submit') ? 'unsubmit' : 'submit';
        let opposite_btn_class = (status == 'submit') ? 'warning' : 'success';
        let opposite_label_class = (status == 'submit') ? 'default' : 'warning';
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'PUT',
            url: `/templates/${template_id}/change_status`,
            data: {
                status: status == 'submit' ? 1 : 0
            },
            success: function(data) {
                $(modal_id).modal('toggle');
                var button = item.find('button.btn-public-unpublic');
                $(button).css('text-transform', 'capitalize');
                $(button).text(opposite_status);
                $(button).removeClass(`btn-${current_btn}`);
                $(button).removeClass(`btn-${status}-wh`);
                $(button).addClass(`btn-${opposite_btn_class}`);
                $(button).addClass(`btn-${opposite_status}-wh`);

                var template_status= $(item).find('div.template-status');
                $(template_status).removeClass(`label-${current_label}`);
                $(template_status).addClass(`label-${opposite_label_class}`);
                $(template_status).text(status_change).css('text-transform', 'capitalize');
                toastr.success(data, 'Update Successfully', {timeOut: 4000, showEasing: 'linear'});
            },
            error: function(error) {
                error = error.responseJSON;
                toastr.error(error['message'], error['status'], {timeOut: 4000});
            }
        })
    }
});
function removeCondition(row) {
    $("#field" + row).parent().parent().remove();
    setChangeStatus(true);
    rerenderConditions();
}

function rerenderConditions() {
    var counter = $(".mult-condition").children().length;
    fields = $('.field-condition').toArray();
    operators = $('.operator').toArray();
    values = $('.value').toArray();
    actions = $('.action').toArray();

    for (i = 0; i < counter; i++) {
        fields[i].id = 'field' + i;
        operators[i].id = 'operator' + i;
        values[i].id = 'value' + i;
        actions[i].id = 'action' + i;
        actions[i].setAttribute('onclick', 'removeCondition(' + i + ')');
    }
}
function addFields() {
    if (checkDataCondition()) {
        var counter = $(".mult-condition").children().length;
        var operators = ["==", "!=", ">", ">=", "<", "<=", "Match"]
        var fieldInput = $("<input>")
            .attr({ name: "field[]", id: "field" + counter, placeholder: "Contidion field" })
            .addClass("form-control col-md-4 field-condition")
            .attr('onchange', 'setChangeStatus(true)');
        var valueInput = $("<input>")
            .attr({ name: "value[]", id: "value" + counter, placeholder: "Contidion value" })
            .addClass("form-control col-md-4 value")
            .attr('onchange', 'setChangeStatus(true)');
        var operatorsDropdown = $("<select/>")
            .attr({ name: "operator[]", id: "operator" + counter })
            .addClass("form-control col-md-2 operator")
            .attr('onchange', 'setChangeStatus(true)');
        var btnDelete = $("<button/>")
            .attr({ name: "action[]", id: "action" + counter })
            .addClass("btn btn--link-danger font-weight-normal action")
            .append("<i/>").addClass("fa fa-minus-circle")
            .attr('onClick', 'removeCondition(' + counter + ')');
        var conditions = $('.mult-condition');
        var row = $('<div class="row"></div>');
        var field = $('<div class="col-md-2"></div>');
        var operator = $('<div class="col-md-1"></div>');
        var value = $('<div class="col-md-2"></div>');
        var btn = $('<div class="col-md-1"></div>');

        $.each(operators, function (index, value) {
            operatorsDropdown.append($("<option/>").val(value).html(value))
        })

        $(field).append(fieldInput);
        $(operator).append(operatorsDropdown);
        $(value).append(valueInput);
        $(btn).append(btnDelete);
        $(row).append(field, operator, value, btn);
        $(conditions).append(row);
    }
}
function checkDataCondition() {
    var flag = true;
    $(".error-field-condition").html("");
    $(".error-value").html("");
    $(".field-condition").each(function () {
        if ($(this).val() == "") {
            $(".error-field-condition").html("Please enter field")
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
