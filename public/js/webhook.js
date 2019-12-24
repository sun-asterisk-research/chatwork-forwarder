$(document).ready(function () {
    $('#cw_rooms').prop('disabled', true);

    if($("input[name='_method']").val() == 'PUT'){
        load_room_name();
        load_room_id();
    }

    function load_room_name() {
        var room_name = document.getElementById("room_name");

        $.ajax({
            type: 'GET',
            url: '/rooms',
            data: {
                '_token': $("input[name='_token']").val(),
                'bot_id': $('#cw_bots').val()
            },
            success: function (data) {
                $('#cw_rooms').find('option').remove().end();
                $('#cw_rooms').append("<option></option>");
                $.each(data, function (index, value) {
                    if (room_name != null && value['name'] == room_name.value) {
                        $('#cw_rooms').append("<option cw-room-id='" + value['room_id'] + "' value='" + value['name'] + "' selected='selected'>" + value['name'] + "</option>");
                        $('#cw_rooms').select2('data', {id: value['name'], text: room_name.value});
                    } else {
                        $('#cw_rooms').append("<option cw-room-id='" + value['room_id'] + "' value='" + value['name'] + "'>" + value['name'] + "</option>");
                    }
                });
                $('#cw_rooms').prop('disabled', false);
            },
            error: function () {
                $('#cw_rooms').val(null).trigger('change');
                $('#cw_rooms').prop('disabled', true);
                $('#cw_room_id').val('');
            }
        });
    }

    function load_room_id() {
        var room_id = document.getElementById("room_id");

        if (room_id != null) {
          $('#cw_room_id').val(room_id.value);
        }
    }

    $('#cw_bots').change(function () {
        $('#cw_rooms').select2('data', {id: '', text: 'Choose one...'});
        load_room_name();
    });

    $('#cw_rooms').change(function () {
        room_id = $(this).find(':selected').attr('cw-room-id');
        $('#cw_room_id').val(room_id);
    });

    $('body').on('click', '.btn-enable-wh', function() {
        var webhook_id = $(this).data('id');
        var webhook_name = $(this).data('name');
        $('#enableModal .webhook-name').text(webhook_name);
        $('#enableModal input').val(webhook_id);
        $('#enableModal').modal('show');
    });

    $('body').on('click', '.btn-disable-wh', function() {
        var webhook_id = $(this).data('id');
        var webhook_name = $(this).data('name');
        $('#disableModal .webhook-name').text(webhook_name);
        $('#disableModal input').val(webhook_id);
        $('#disableModal').modal('show');
    });

    $('body').on('click', '.btn-confirm-enable', function() {
        updateWebhookStatus('#enableModal', 'enable', 'success');
    });

    $('body').on('click', '.btn-confirm-disable', function() {
        updateWebhookStatus('#disableModal', 'disable', 'warning');
    });

    function updateWebhookStatus(modal_id, status, current_btn_class) {
        let webhook_id = $(modal_id + ' input').val();
        let item = $('.item-' + webhook_id);
        let status_change = (status == 'enable') ? 'enabled' : 'disabled';
        let opposite_status = (status == 'enable') ? 'disable' : 'enable';
        let opposite_btn_class = (current_btn_class == 'success') ? 'warning' : 'success';

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'PUT',
            url: '/webhooks/change_status',
            data: {
                id: webhook_id,
                status: status_change.toUpperCase()
            },
            success: function(data) {
                $(modal_id).modal('toggle');
                var button = item.find('button.btn-enable-disable');
                $(button).css('text-transform', 'capitalize');
                $(button).text(opposite_status);
                $(button).removeClass(`btn-${current_btn_class}`);
                $(button).removeClass(`btn-${status}-wh`);
                $(button).addClass(`btn-${opposite_btn_class}`);
                $(button).addClass(`btn-${opposite_status}-wh`);

                var webhook_status= $(item).find('div.webhook-status');
                $(webhook_status).removeClass(`label-${opposite_btn_class}`);
                $(webhook_status).addClass(`label-${current_btn_class}`);
                $(webhook_status).text(status_change).css('text-transform', 'capitalize');
                toastr.success(data, 'Update Successfully', {timeOut: 4000, showEasing: 'linear'});
            },
            error: function() {
                toastr.error('Something went wrong. Please try again!', 'Update Failed', {timeOut: 4000});
            }
        })
    }

    $('.payload-content').on('click', '.form-delete', function (e) {
        e.preventDefault();
        var $form = $(this);
        $('#delete-confirm').modal({ backdrop: 'static', keyboard: false })
            .on('click', '#delete-btn', function () {
                $form.submit();
            });
    });

    $('.mapping-item').on('click', '.form-delete-mapping', function (e) {
        e.preventDefault();
        var $form = $(this);
        $('#delete-mapping-confirm').modal().on('click', '#delete-mapping-btn', function () {
            $form.submit();
        });
    });

    $('.webhook-item').on('click', '.form-delete', function (e) {
        e.preventDefault();
        var $form = $(this);
        $('#delete-confirm').modal({ backdrop: 'static', keyboard: false })
            .on('click', '#delete-btn', function () {
                $form.submit();
            });
    });

    $('.webhook-form').on('click', '.cancel-btn', function (e) {
        e.preventDefault();
        $('#cancel-confirm').modal({ backdrop: 'static', keyboard: false })
        .on('click', '#cancel-btn', function () {
            window.location.pathname = '/webhooks';
        });
    });

    $('#copyUrl').on('click', function (e) {
        e.preventDefault();
        var copyText = document.getElementById("webhookUrl");
        var btnCopy = document.getElementById('copyUrl');
        copyText.select();
        copyText.setSelectionRange(0, 99999)
        document.execCommand("copy");
        btnCopy.setAttribute('data-toggle', 'tooltip');
        btnCopy.setAttribute('data-placement', 'top');
        btnCopy.setAttribute('data-original-title', 'Copied!');
        $('[data-toggle="tooltip"], .enable-tooltip').tooltip({container: 'body', animation: false});
        $('#copyUrl').mouseover();
    });
});
