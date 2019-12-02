function addFields() {
    var flag = true;
    $(".field").each(function () {
        if ($(this).val() == "") {
            $(".error-field").html("Please enter field")
            flag = false;
            return false;
        }
    });
    $(".value").each(function () {
        if ($(this).val().length > 100) {
            $(".error-value").html("Value is too long (maximum is 100 characters)")
            flag = false;
            return false;
        }
        if ($(this).val() == "") {
            $(".error-value").html("Please enter value")
            flag = false;
            return false;
        }
    });
    if (flag == true) {
        $(".error-field").html("");
        $(".error-value").html("");
        counter = $(".mult-condition").children().length;

        var operators = ["==", "!=", ">", ">=", "<", "<="]
        var fieldInput = $("<input>")
            .attr({ name: "field[]", id: "field" + counter })
            .addClass("form-control col-md-4 field");
        var valueInput = $("<input>")
            .attr({ name: "value[]", id: "value" + counter })
            .addClass("form-control col-md-4 value");
        var operatorsDropdown = $("<select/>")
            .attr({ name: "operator[]", id: "operator" + counter })
            .addClass("form-control col-md-2 operator");
        var btnDelete = $("<button/>")
            .attr({ name: "action[]", id: "action" + counter })
            .addClass("btn btn--link-danger font-weight-normal")
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

function removeCondition(row) {
    $(".error-field").html("");
    $(".error-value").html("");
    $("#field" + row).remove();
    $("#operator" + row).remove();
    $("#value" + row).remove();
    $("#action" + row).remove();
}

function clearOldErrorMessage() {
    $(".webhook_id").html("");
    $(".content").html("");
}

function printErrorMsg(errors) {
    $.each(errors, function (index, value) {
        $("." + index).html(value);
    })
}

function getValues(items){
    return items.map(function(){ return $(this).val(); }).get();
}

$(document).ready(function () {
    $("#submit").click(function (e) {
        e.preventDefault();

        var _token = $('meta[name="csrf-token"]').attr('content');
        var content = $("textarea[name='content']").val();
        var webhook_id = $("input[name='webhook_id']").val();
        var fields = getValues($("input[name^='field[]']"));
        var operators = getValues($("select[name^='operator[]']"));
        var values = getValues($("input[name^='value[]']"));

        $.ajax({
            url: "/webhooks/" + webhook_id + "/payloads",
            type: "POST",
            data: {
                _token: _token,
                content: content,
                fields: fields,
                operators: operators,
                values: values,
            },
            success: function (id) {
                window.location.replace("/webhooks/" + webhook_id + "/payloads/" + id + "/edit");
            },
            error: function (data) {
                toastr.error(' Something went wrong', 'Create failed', { timeOut: 4000 });
                clearOldErrorMessage();
                printErrorMsg(data.responseJSON.errors);
            }
        });
    });
});
