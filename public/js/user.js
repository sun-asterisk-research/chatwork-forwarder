$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});

$('#avatar_show').on('click',function(){
    $('#avatar').click();
})

$('#avatar').change(function () {
    if ($(this).val() != '') {
        var reader = new FileReader();
        reader.onload = function (e){
            $('#avatar_show').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
    }
})

$('.avatar_show_edit').on('click',function(){
    $('#avatar_edit').click();
})

$('#avatar_edit').change(function () {
    if ($(this).val() != '') {
        var reader = new FileReader();
        reader.onload = function (e){
            $('.avatar_show_edit').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
    }
})

$('.user-form').on('click', '.cancel-btn', function (e) {
    e.preventDefault();
    $('#cancel-confirm').modal({ backdrop: 'static', keyboard: false })
    .on('click', '#cancel-btn', function () {
        window.location.pathname = 'admin/users';
    });
});

$('#user_update').on('submit',function(e){
    e.preventDefault();
    var id = $('#id_user').val();
    var formData = new FormData($(this)[0]);
    formData.append('_method', 'PUT');

    $.ajax({
        url: "/admin/users/" + id, 
        data: formData,
        type: 'POST',
        contentType: false,
        processData: false,
        success: function (data) {
            if(data.error == true) {
                toastr.error(data.messageFail)
            } else {
                toastr.success(data.messageSuccess)
                $('.has-error').hide();
            }
        },
        error: function (data) {
            $('.has-error').show();
            document.getElementById("error-name", "error-email", "error-password", "error-avatar").innerHTML = ""
            jQuery.each(data.responseJSON.errors, function(key, value){
                document.getElementById("error-" + key).innerHTML = "<span class='help-block'>" + value + "</span>";
            });
        }
    })
});

$('.user-item').on('click', '.form-delete', function (e) {
    e.preventDefault();
    var $form = $(this);
    $('#delete-confirm').modal({ backdrop: 'static', keyboard: false })
    .on('click', '#delete-btn', function () {
        $form.submit();
    });
});
