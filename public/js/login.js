$('#login_form').on('submit',function(e){
    e.preventDefault();
    var formData = new FormData($(this)[0]);
    $.ajax({
        url: '/login',
        data: formData,
        type: 'POST',
        contentType: false,
        processData: false,
        success: function (data) {
            window.location.href = "/webhooks";
        },
        error: function (data) {
            $('.has-error').show();
            document.getElementById("error-password").innerHTML = "";
            document.getElementById("error-email").innerHTML = "";
            jQuery.each(data.responseJSON.errors, function(key, value){
                document.getElementById("error-" + key).innerHTML = "<span class='help-block'>" + value + "</span>";
            });
        }
    })
});
