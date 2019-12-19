$(function() {
    $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        locale: {
            format: 'DD-MM-YYYY'
        }
    }, function(start, end, label) {
        console.log("A new date selection was made: " + start.format('DD-MM-YYYY') + ' to ' + end.format('DD-MM-YYYY'));
    });
});

if ($('#date-ranger').val() == '') {
    $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        startDate: moment().startOf('month'),
        endDate:moment(),
        locale: {
            format: 'DD-MM-YYYY'
        }
    }, function(start, end, label) {
        console.log("A new date selection was made: " + start.format('DD-MM-YYYY') + ' to ' + end.format('DD-MM-YYYY'));
    });
}
