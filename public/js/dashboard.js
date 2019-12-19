$(document).ready(function () {
    if ($('#fromDate').val() == '') {
        $('#fromDate').val(moment().startOf('month').format('DD-MM-YYYY'));
    }
    if ($('#toDate').val() == '') {
        $('#toDate').val(moment().format('DD-MM-YYYY'));
    }
});
