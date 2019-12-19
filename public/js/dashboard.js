$(document).ready(function () {
    $('#fromDate').val(moment().startOf('month').format('DD-MM-YYYY'));
    $('#toDate').val(moment().format('DD-MM-YYYY'));
});
