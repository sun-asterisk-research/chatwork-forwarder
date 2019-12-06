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

$('.user-form').on('click', '.cancel-btn', function (e) {
    e.preventDefault();
    $('#cancel-confirm').modal({ backdrop: 'static', keyboard: false })
    .on('click', '#cancel-btn', function () {
        window.location.pathname = 'admin/users';
    });
});
