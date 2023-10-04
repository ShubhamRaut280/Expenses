$(document).ready(function() {
    var dom = document.getElementById("transactions");
    var myChart = echarts.init(dom);
    var app = {};
    option = null;
    app.title = 'Hellaplus';

    option = {
        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b}: {c} ({d}%)"
        },
        color: ["#57BE65", "#ff1a1a", "#3EA4F1"],
        series: [
            {
                name:'Transactions',
                type:'pie',
                radius: ['50%', '70%'],
                avoidLabelOverlap: false,
                label: {
                    normal: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        show: true,
                        textStyle: {
                            fontSize: '16',
                            fontWeight: 'bold'
                        }
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    }
                },
                data:[
                    {value:totalIncome, name:'Income'},
                    {value:totalExpenses, name:'Expenses'},
                    {value:totalSavings, name:'Savings'}
                ]
            }
        ]
    };
    ;
    if (option && typeof option === "object") {
        myChart.setOption(option, true);
    }


// grapgh


    initMonthlyGraph();

});
function initMonthlyGraph(){
    var dom = document.getElementById("monthly");
    var myChart = echarts.init(dom);
    var app = {};
    option = null;
    var xAxisData = [];
    var data1 = [];
    var data2 = [];
    for (var i = 1; i < 31; i++) {
        xAxisData.push(i + ' Jan');
        data2.push(Math.random());
        data1.push(Math.random() * -1);
    }
    labels = $.map(labels, function (val, key) {
        var split_month_label   = val.split(' ');
        var full_month      = split_month_label[1];
        return split_month_label[0] + ' ' + eval('monthsShort' + full_month);
    });

    option = {
        legend: {
            data: [expense_title+' ('+currency+')', income_title+' ('+currency+')'],
            align: 'left'
        },
        tooltip: {},
        xAxis: {
            data: labels,
            silent: false,
            splitLine: {
                show: false
            }
        },
        yAxis: {
        },
        series: [{
            name: expense_title+' ('+currency+')',
            type: 'bar',
            stack: 'transactions',
            itemStyle: {
                normal: {
                    barBorderRadius: 50,
                    color: "#ff1a1a"
                }
            },
            data: expenses,
            animationDelay: function (idx) {
                return idx * 10;
            }
        }, {
            name: income_title+' ('+currency+')',
            type: 'bar',
            stack: 'transactions',
            barMaxWidth: 10,
            itemStyle: {
                normal: {
                    barBorderRadius: 50,
                    color: "#13A54E"
                }
            },
            data: income,
            animationDelay: function (idx) {
                return idx * 10 + 100;
            }
        }],
        animationEasing: 'elasticOut',
        animationDelayUpdate: function (idx) {
            return idx * 5;
        }
    };
    if (option && typeof option === "object") {
        myChart.setOption(option, true);
    }
}

$(function () {

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        var start_month = start.format('MMMM');
        var end_month   = end.format('MMMM');
        start_month     = eval('months' + start_month) + start.format(' D, YYYY');
        end_month       = eval('months' + end_month) + end.format(' D, YYYY');
        $('.reportrange span').html(start_month + ' - ' + end_month);
    }

    $('.reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        locale: {
            "applyLabel": apply,
            "cancelLabel": cancel,
            "daysOfWeek": [
                daysMinSunday,
                daysMinMonday,
                daysMinTuesday,
                daysMinWednesday,
                daysMinThursday,
                daysMinFriday,
                daysMinSaturday
            ],
            "monthNames": [
                monthsShortJanuary,
                monthsShortFebruary,
                monthsShortMarch,
                monthsShortApril,
                monthsShortMay,
                monthsShortJune,
                monthsShortJuly,
                monthsShortAugust,
                monthsShortSeptember,
                monthsShortOctober,
                monthsShortNovember,
                monthsShortDecember
            ],
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);
    $('*[data-range-key="Today"]').html(today);
    $('*[data-range-key="Yesterday"]').html(yesterday);
    $('*[data-range-key="Last 7 Days"]').html(last_7_days);
    $('*[data-range-key="Last 30 Days"]').html(last_30_days);
    $('*[data-range-key="This Month"]').html(this_month);
    $('*[data-range-key="Last Month"]').html(last_month);
    $('*[data-range-key="Custom Range"]').html(custom_range);

});


$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
    $(".reports-title").text(eval(picker.chosenLabel.toLowerCase().split(' ').join('_')));
    server({
        url: reportsUrl,
        data: {
            "from": picker.startDate.format('YYYY-MM-DD'),
            "to": picker.endDate.format('YYYY-MM-DD'),
            "csrf-token": Cookies.get("CSRF-TOKEN")
        },
        loader: true
    });
});


function reports(reports) {
    log(reports);
    $(".reports-income").text(reports.income.total);
    $(".income-count").text(reports.income.count+" Trns.");
    $(".reports-expenses").text(reports.expenses.total);
    $(".expenses-count").text(reports.expenses.count+" Trns.");

    $(".top-expenses").empty();
    if (Object.keys(reports.expenses.top).length) {
        for (let expense of reports.expenses.top) {
            $(".top-expenses").append('<tr><td>'+expense.title+'</td><td class="text-right">'+expense.amount+'</td></tr>');
        }
    }else{
        $(".top-expenses").html("<tr><td class='text-center'>It's empty here!</td> </tr>");
    }
    labels = reports.chart.label;
    income = reports.chart.income;
    expenses = reports.chart.expenses;
    initMonthlyGraph();

}