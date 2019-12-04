$(document).ready(function () {
    $('#cw_rooms').prop('disabled', true);
    $('#cw_bots').change(function () {
        $.ajax({
            type: 'GET',
            url: '/rooms',
            data: {
                '_token': $("input[name='_token']").val(),
                'bot_id': $('#cw_bots').val()
            },
            success: function (data) {
                $.each(data, function (index, value) {
                    $('#cw_rooms').append("<option cw-room-id='" + value['room_id'] + "' value='" + value['name'] + "'>" + value['name'] + "</option>");
                });
                $('#cw_rooms').prop('disabled', false);
            },
            error: function () {
                $('#cw_rooms').val(null).trigger('change');
                $('#cw_rooms').prop('disabled', true);
                $('#cw_room_id').val('');
            }
        });
    });

    $('#cw_rooms').change(function () {
        room_id = $(this).find(':selected').attr('cw-room-id');
        $('#cw_room_id').val(room_id);
    });

    $('body').on('click', '.btn-enabled-wh', function() {
        var webhook_id = $(this).data('id');
        var webhook_name = $(this).data('name');
        $('#enableModal .webhook-name').text(webhook_name);
        $('#enableModal input').val(webhook_id);
        $('#enableModal').modal('show');
    });

    $('body').on('click', '.btn-disabled-wh', function() {
        var webhook_id = $(this).data('id');
        var webhook_name = $(this).data('name');
        $('#disableModal .webhook-name').text(webhook_name);
        $('#disableModal input').val(webhook_id);
        $('#disableModal').modal('show');
    });

    $('body').on('click', '.btn-confirm-enable', function() {
        updateWebhookStatus('#enableModal', 'enabled', 'success');
    });

    $('body').on('click', '.btn-confirm-disable', function() {
        updateWebhookStatus('#disableModal', 'disabled', 'danger');
    });

    function updateWebhookStatus(modal_id, status, current_btn_class) {
        let webhook_id = $(modal_id + ' input').val();
        let item = $('.item-' + webhook_id);
        let opposite_status = (status == 'enabled') ? 'disabled' : 'enabled';
        let opposite_btn_class = (current_btn_class == 'success') ? 'danger' : 'success';

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'PUT',
            url: '/webhooks/change_status',
            data: {
                id: webhook_id,
                status: status.toUpperCase()
            },
            success: function(data) {
                $(modal_id).modal('toggle');
                var button = item.find('button');
                $(button).css('text-transform', 'capitalize');
                $(button).text(opposite_status);
                $(button).removeClass(`btn-${current_btn_class}`);
                $(button).removeClass(`btn-${status}-wh`);
                $(button).addClass(`btn-${opposite_btn_class}`);
                $(button).addClass(`btn-${opposite_status}-wh`);
                $(item).find('td.webhook-status').text(status).css('text-transform', 'capitalize');
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

    $('.webhook-item').on('click', '.form-delete', function (e) {
        e.preventDefault();
        var $form = $(this);
        $('#delete-confirm').modal({ backdrop: 'static', keyboard: false })
            .on('click', '#delete-btn', function () {
                $form.submit();
            });
    });
});
