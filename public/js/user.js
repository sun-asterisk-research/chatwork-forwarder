var TablesDatatables = function () {

    return {
        init: function () {
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */
            $('#user-table').dataTable({
                lengthMenu: [[15, 20, 30, -1], [15, 20, 30, 'All']],
                pagingType: "full_numbers",
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/list/users',
                },
                columns: [
                {
                    data: 'DT_RowIndex',
                    render: function (data, type, row) {
                        return '<div class="text-center">' + data + '</div>';
                    }
                },
                {
                    data: 'avatar',
                    render: function (data, type, row) {
                        if (data != null) {
                            if (row.cover == 1) {
                                var img = data
                            } else {
                                var img = "/storage/" + data
                            }
                            return '<div class="imgList text-center"><img width= "60px;" src="' + img + '" alt=""></div>';
                        } else {
                            return '<div class="imgList text-center"><img width= "60px;" src="' + '/img/avatar2.jpg' + '" alt=""></div>';
                        }
                    }
                },
                {
                    data: 'name',
                    render: function (data, type, row) {
                        return '<div class="text-center">' + data + '</div>';
                    }
                },
                {
                    data: 'email',
                    render: function (data, type, row) {
                        return '<div class="text-center">' + data + '</div>';
                    }
                },
                {
                    data: 'role',
                    render: function (data, type, row) {
                        return '<div class="text-center">' + data + '</div>';
                    }
                },
                {
                    data: 'id',
                    render: function (data, type, row) {
                        if(current_user_id == data){
                            return '<div class="text-center"><a href="#" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-default" onclick="edit(' + data + ');"><i class="fa fa-pencil"></i> Edit</a></div>';
                        } else {
                            return '<div class="text-center"><a href="#" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-default" onclick="edit(' + data + ');"><i class="fa fa-pencil"></i> Edit</a>' +
                            '&nbsp<a href="#" data-toggle="tooltip" title="Delete" class="btn btn-sm btn-danger" onclick="delete(' + data + ');"><i class="fa fa-trash-o"></i> Delete</a></div>';
                        }
                    }
                },
                ],
            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'Search');
        }
    };
}();

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
        window.location.pathname = '/users';
    });
});
