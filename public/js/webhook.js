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
});
