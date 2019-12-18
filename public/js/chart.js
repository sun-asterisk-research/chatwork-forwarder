
var payloadHistoriesChart = $('#payload-histories-chart');
var payloadSuccessCases = payloadHistory["payloadSuccessChart"];
var payloadFailedCases = payloadHistory["payloadFailedChart"];
var payloadChartDates = payloadHistory["dateChart"];
show_chart(payloadHistoriesChart, payloadSuccessCases, payloadFailedCases, payloadChartDates);

var messageHistoriesChart = $('#message-histories-chart');
var messageSuccessCases = messageHistory["messageSuccessChart"];
var messageFailedCases = messageHistory["messageFailedChart"];
var messageChartDates = messageHistory["dateChart"];
show_chart(messageHistoriesChart, messageSuccessCases, messageFailedCases, messageChartDates);

function show_chart(historyChart, successCases, failedCases, chartDates) {
    $.plot(historyChart,
        [
            {
                data: successCases,
                lines: {show: true, fill: false},
                points: {show: true, radius: 6, fillColor: '#3af900'}
            },
            {
                data: failedCases,
                lines: {show: true, fill: false},
                points: {show: true, radius: 6, fillColor: '#F63328'}
            }
        ],
        {
            colors: ['#353535', '#353535'],
            legend: {show: false},
            grid: {borderWidth: 0, hoverable: true, clickable: true},
            yaxis: {show: false},
            xaxis: {show: false, ticks: chartDates}
        }
    );

    // Creating and attaching a tooltip to the widget
    var previousPoint = null, ttlabel = null;
    historyChart.bind('plothover', function(event, pos, item) {

        if (item) {
            if (previousPoint !== item.dataIndex) {
                previousPoint = item.dataIndex;

                $('#chart-tooltip').remove();
                var x = item.datapoint[0], y = item.datapoint[1];

                // Get xaxis label
                var dateLabel = item.series.xaxis.options.ticks[item.dataIndex][1];

                if (item.seriesIndex === 1) {
                    ttlabel = '<strong>' + y + '</strong> cases In <strong>' + dateLabel + '</strong>';
                } else {
                    ttlabel = '<strong>' + y + '</strong> cases In <strong>' + dateLabel + '</strong>';
                }

                $('<div id="chart-tooltip" class="chart-tooltip">' + ttlabel + '</div>')
                    .css({top: item.pageY - 50, left: item.pageX - 50}).appendTo("body").show();
            }
        }
        else {
            $('#chart-tooltip').remove();
            previousPoint = null;
        }
    });
};
