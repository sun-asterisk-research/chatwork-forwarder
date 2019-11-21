/*
 *  Document   : tablesDatatables.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Tables Datatables page
 */

var TablesDatatables = function () {

    return {
        init: function () {
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */
            $('#example-datatable').dataTable({
                columnDefs: [{ orderable: false, targets: [1, 5] }],
                pageLength: 10,
                lengthMenu: [[10, 20, 30, -1], [10, 20, 30, 'All']]
            });

            $('#bot-datatable').dataTable({
                columnDefs: [{orderable: false, targets: [2]}],
                pageLength: 15,
                lengthMenu: [[15, 50, 100, -1], [15, 50, 100, 'All']]
            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'Search');
        }
    };
}();